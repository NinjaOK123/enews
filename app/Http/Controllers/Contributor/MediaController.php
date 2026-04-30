<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Hiển thị media image hoặc video cho editor (kiểm tra quyền truy cập)
     */
    public function show(Media $media)
    {
        $canAccess = $media->user_id === auth()->id() || 
            ($media->is_shared && (
                (is_null($media->shared_role) && is_null($media->shared_user_id)) ||
                $media->shared_role === auth()->user()->role ||
                $media->shared_user_id === auth()->id()
            ));

        // Chỉ cấp quyền xem nếu media là của user hiện tại hoặc admin đã share
        if ($canAccess) {
            $path = $media->absolutePath();

            if (!file_exists($path)) {
                abort(404);
            }
            
            // Trả về file resource với header rõ ràng
            return response()->file($path, [
                'Content-Type' => $media->file_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
            ]);
        }

        // Nếu file thuộc người khác và không shared, trả về 403
        abort(403, 'Bạn không có quyền truy cập file này.');
    }

    public function getPersonalMedia()
    {
        $media = Media::where('user_id', auth()->id())->latest()->get()->map(function($m) {
            $m->url = route('contributor.media.view', $m->id);
            return $m;
        });
        return response()->json($media);
    }

    public function getSharedMedia()
    {
        $media = Media::where('is_shared', true)
            ->where(function ($q) {
                $q->whereNull('shared_role')->whereNull('shared_user_id')
                  ->orWhere('shared_role', auth()->user()->role)
                  ->orWhere('shared_user_id', auth()->id());
            })
            ->latest()->get()->map(function($m) {
            $m->url = route('contributor.media.view', $m->id);
            return $m;
        });
        return response()->json($media);
    }

    public function destroy(Media $media)
    {
        if ($media->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xoá file này.'], 403);
        }

        $storagePath = (string) $media->file_path;
        if (str_starts_with($storagePath, '/storage/')) {
            $storagePath = str_replace('/storage/', '', $storagePath);
        }
        
        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }

        $media->delete();

        return response()->json(['success' => true, 'message' => 'Đã xoá file thành công.']);
    }
}
