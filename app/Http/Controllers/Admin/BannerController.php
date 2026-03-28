<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners.
     */
    public function index()
    {
        // Lấy danh sách banner sắp xếp theo thứ tự order
        $banners = Banner::orderBy('order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Store a newly created banner in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required_without:image_url|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'required_without:image|nullable|url|max:1000',
            'link'  => 'nullable|url|max:255',
        ], [
            'image.required_without' => 'Vui lòng chọn ảnh, dán ảnh hoặc nhập URL ảnh.',
            'image_url.required_without' => 'Vui lòng chọn ảnh, dán ảnh hoặc nhập URL ảnh.',
            'image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
            'image_url.url' => 'URL Hình ảnh không hợp lệ.',
            'link.url' => 'Link liên kết không hợp lệ.',
        ]);

        $item = new Banner();
        $item->title = $request->input('title');
        $item->link = $request->input('link');
        $item->is_active = $request->has('is_active');
        
        // Tự động gán thứ tự order lớn nhất
        $maxOrder = Banner::max('order') ?? 0;
        $item->order = $maxOrder + 1;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $item->image = $path;
        } elseif ($request->filled('image_url')) {
            $item->image = $this->downloadOrKeepUrl($request->input('image_url'));
        }

        $item->save();

        return redirect()->route('admin.banners.index')->with('success', 'Thêm Banner thành công.');
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'nullable|url|max:1000',
            'link'  => 'nullable|url|max:255',
        ], [
            'image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
            'image_url.url' => 'URL Hình ảnh không hợp lệ.',
            'link.url' => 'Link liên kết không hợp lệ.',
        ]);

        $banner->title = $request->input('title');
        $banner->link = $request->input('link');
        $banner->is_active = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Xoá ảnh cũ (nếu có và không bắt đầu bằng http - đề phòng dữ liệu ảo)
            if ($banner->image && !str_starts_with($banner->image, 'http')) {
                Storage::disk('public')->delete($banner->image);
            }
            // Lưu ảnh mới
            $path = $request->file('image')->store('banners', 'public');
            $banner->image = $path;
        } elseif ($request->filled('image_url')) {
            // Xoá ảnh cũ
            if ($banner->image && !str_starts_with($banner->image, 'http')) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->image = $this->downloadOrKeepUrl($request->input('image_url'));
        }

        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật Banner thành công.');
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image && !str_starts_with($banner->image, 'http')) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Đã xoá Banner cuộc thi.');
    }

    /**
     * Bật/Tắt trạng thái hiển thị
     */
    public function toggleActive(Request $request, Banner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'success' => true,
            'is_active' => $banner->is_active
        ]);
    }

    /**
     * Cập nhật Thứ tự (Ajax từ SortableJS)
     */
    public function updateOrder(Request $request)
    {
        $orderedIds = $request->input('order'); // Mảng ID được gửi lên
        if (!$orderedIds || !is_array($orderedIds)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        }

        foreach ($orderedIds as $index => $id) {
            Banner::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Đã cập nhật thứ tự Banner.']);
    }

    /**
     * Download URL image to local storage, or keep URL if failed
     */
    private function downloadOrKeepUrl($url)
    {
        // Detect Google Drive links
        if (preg_match('/drive\.google\.com\/file\/d\/(.*?)\//', $url, $matches) || preg_match('/drive\.google\.com\/open\?id=(.*?)$/', $url, $matches)) {
            $url = 'https://drive.google.com/uc?export=download&id=' . $matches[1];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
            if ($response->successful()) {
                $ext = 'jpg';
                $contentType = $response->header('Content-Type');
                if (str_contains($contentType, 'png')) $ext = 'png';
                elseif (str_contains($contentType, 'webp')) $ext = 'webp';
                elseif (str_contains($contentType, 'gif')) $ext = 'gif';
                else {
                    $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
                    if (isset($pathInfo['extension'])) {
                        $ext = strtolower($pathInfo['extension']);
                    }
                }

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'])) {
                    $filename = 'banners/' . uniqid() . '.' . $ext;
                    Storage::disk('public')->put($filename, $response->body());
                    return $filename;
                }
            }
        } catch (\Exception $e) {
            // Ignore downloading errors
        }

        return $url;
    }
}
