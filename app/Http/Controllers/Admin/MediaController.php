<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaController extends Controller
{
    /**
     * Display a listing of the media files.
     */
    public function index(Request $request)
    {
        $query = Media::with(['user', 'sharedUser']);

        if ($request->input('owner') === 'me') {
            $query->where('user_id', auth()->id());
        } elseif (!auth()->user()->isAdmin()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere(function ($sub) {
                      $sub->where('is_shared', true)
                          ->where(function ($sq) {
                              $sq->whereNull('shared_role')->whereNull('shared_user_id')
                                 ->orWhere('shared_role', auth()->user()->role)
                                 ->orWhere('shared_user_id', auth()->id());
                          });
                  });
            });
        }

        // Query Search file name
        if ($search = $request->input('search')) {
            $query->where('file_name', 'like', "%{$search}%");
        }

        if ($type = $request->input('type')) {
            $query->where('file_type', 'like', "{$type}%");
        }

        $roles = [
            'editor' => 'Biên tập viên',
            'contributor' => 'Cộng tác viên',
            'viewer' => 'Người xem nội bộ',
            'reader' => 'Người dùng AGU',
        ];
        $users = \App\Models\User::select('id', 'name', 'email')->orderBy('name')->get();

        $media = $query->latest()->paginate(10)->withQueryString();
        $totalMedia = $query->count();

        return view('admin.media.index', compact('media', 'totalMedia', 'roles', 'users'));
    }

    /**
     * Store a newly uploaded media file using Dropzone.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,mp4|max:20480', // 20MB limit
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        $extension = $file->getClientOriginalExtension();
        $safeName = md5(time() . $originalName) . '.' . $extension;
        $path = 'media/shared/' . $safeName;

        // Ensure directory exists
        if (!Storage::disk('public')->exists('media/shared')) {
            Storage::disk('public')->makeDirectory('media/shared');
        }

        // Image Processing with Intervention
        if (str_starts_with($mimeType, 'image/')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // Resize only if width > 1200
            if ($image->width() > 1200) {
                $image->scaleDown(width: 1200);
            }

            // Encode with 80% quality and save
            $encoded = $image->toJpeg(80); // Convert to JPEG or keep original based on requirements, simplifying to JPEG or WEBP to save space
            Storage::disk('public')->put($path, $encoded);
            
            // Re-calculate size after compression
            $size = Storage::disk('public')->size($path);
        } else {
            // Video or other file - store directly
            Storage::disk('public')->putFileAs('media/shared', $file, $safeName);
        }

        $shareType = $request->input('share_type', 'public');
        $isShared = true;
        $sharedRole = null;
        $sharedUserId = null;

        if ($shareType === 'private') {
            $isShared = false;
        } elseif ($shareType === 'role') {
            $sharedRole = $request->input('shared_role');
        } elseif ($shareType === 'user') {
            $sharedUserId = $request->input('shared_user_id');
        }

        // Save to Database
        $media = Media::create([
            'user_id' => auth()->id(),
            'file_name' => $originalName,
            // lưu dạng relative để dùng thống nhất với contributor flow
            'file_path' => $path,
            'file_type' => $mimeType, // Đã fix: lưu MIME type gốc thay vì 'image' hay 'video' để Fix lỗi nhận diện trên views.
            'file_size' => $size,
            'is_shared' => $isShared,
            'shared_role' => $sharedRole,
            'shared_user_id' => $sharedUserId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Upload thành công!',
            'media' => $media
        ]);
    }

    /**
     * Preview media API/Direct return.
     */
    public function show(Media $media)
    {
        abort_if(!$media->is_shared && $media->user_id !== auth()->id(), 403);
        return response()->file($media->absolutePath());
    }

    /**
     * Update sharing rules for a media file.
     */
    public function updateShare(Request $request, Media $media)
    {
        // Only owner or admin can update share settings
        if (!auth()->user()->isAdmin() && $media->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa file này.');
        }

        $shareType = $request->input('share_type', 'public');
        $isShared = true;
        $sharedRole = null;
        $sharedUserId = null;

        if ($shareType === 'private') {
            $isShared = false;
        } elseif ($shareType === 'role') {
            $sharedRole = $request->input('shared_role');
        } elseif ($shareType === 'user') {
            $sharedUserId = $request->input('shared_user_id');
        }

        $media->update([
            'is_shared' => $isShared,
            'shared_role' => $sharedRole,
            'shared_user_id' => $sharedUserId,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật quyền chia sẻ thành công!');
    }

    /**
     * Delete a media file.
     */
    public function destroy(Media $media)
    {
        // Must be auth user or admin, but this route is protected by role:admin anyway
        $storagePath = (string) $media->file_path;
        if (str_starts_with($storagePath, '/storage/')) {
            $storagePath = str_replace('/storage/', '', $storagePath);
        }
        
        // Delete actual file
        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }

        // Delete DB record
        $media->delete();

        return redirect()->route('admin.media.index')->with('success', 'Đã xoá file thành công.');
    }
}
