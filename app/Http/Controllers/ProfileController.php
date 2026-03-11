<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin cá nhân.
     */
    public function show($id = null)
    {
        $user = $id ? \App\Models\User::findOrFail($id) : auth()->user();
        
        // Bảo mật: Nếu xem profile người khác mà không phải Admin thì chặn
        if ($user->id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền xem thông tin của người dùng này.');
        }

        // Cấp quyền sửa (isEditable)
        $isEditable = ($user->id === auth()->id() || auth()->user()->role === 'admin');

        // Render giao diện theo role (vai trò)
        if ($user->role === 'reader') {
            return view('profile.roles.reader', compact('user', 'isEditable'));
        } elseif ($user->role === 'contributor') {
            $articlesCount = $user->posts()->count();
            $totalViews = $user->posts()->sum('view_count');
            $posts = $user->posts()->latest()->take(10)->get();
            return view('profile.roles.contributor', compact('user', 'isEditable', 'articlesCount', 'totalViews', 'posts'));
        } elseif ($user->role === 'editor' || $user->role === 'admin') {
            // Lấy thống kê duyệt bài
            $toReviewCount = \App\Models\Post::where('status', 'pending')->count();
            $approvedCount = \App\Models\Post::where('status', 'published')->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            // Lịch sử duyệt bài
            $recentActivities = \App\Models\Post::with(['category:id,name,slug', 'author:id,name'])
                ->whereNotNull('updated_at')
                ->latest('updated_at')
                ->take(5)
                ->get();
            return view('profile.roles.editor', compact('user', 'isEditable', 'toReviewCount', 'approvedCount', 'recentActivities'));
        }

        return view('profile.index', compact('user', 'isEditable')); // fallback
    }

    /**
     * Cập nhật avatar của user
     */
    public function updateAvatar(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền chỉnh sửa ảnh đại diện của người dùng này.');
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // Xử lý ảnh: Crop vuông 200x200
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $image->coverDown(400, 400); // cover center 400x400

            $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
            $path = 'avatars/' . $filename;

            // Xoá ảnh cũ (nếu có và không phải ảnh mặc định từ ngoài)
            if ($user->avatar && str_starts_with($user->avatar, 'avatars/') && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới
            Storage::disk('public')->put($path, (string) $image->toJpeg(80));

            // Cập nhật database
            $user->avatar = $path;
            $user->save();

            return redirect()->back()->with('success', 'Khoác áo mới thành công! (Cập nhật Avatar)');
        }

        return redirect()->back()->with('error', 'Có lỗi khi tải ảnh lên.');
    }
}
