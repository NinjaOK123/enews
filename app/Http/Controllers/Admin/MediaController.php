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
        $query = Media::with('user')->where('is_shared', true);

        // Scope for Admin only media
        if ($search = $request->input('search')) {
            $query->where('file_name', 'like', "%{$search}%");
        }

        if ($type = $request->input('type')) {
            $query->where('file_type', 'like', "{$type}%");
        }

        $media = $query->latest()->paginate(10)->withQueryString();
        $totalMedia = Media::where('is_shared', true)->count();

        return view('admin.media.index', compact('media', 'totalMedia'));
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

        $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

        // Save to Database
        $media = Media::create([
            'user_id' => auth()->id(),
            'file_name' => $originalName,
            // lưu dạng relative để dùng thống nhất với contributor flow
            'file_path' => $path,
            'file_type' => $type,
            'file_size' => $size,
            'is_shared' => true, // Admin uploads are shared by default in this flow
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
