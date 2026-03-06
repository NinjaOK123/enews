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
        // Chỉ cấp quyền xem nếu media là của user hiện tại hoặc admin đã share
        if ($media->user_id === auth()->id() || $media->is_shared) {
            $path = storage_path('app/public/' . $media->file_path);
            
            if (!file_exists($path)) {
                abort(404);
            }
            
            // Trả về file resource
            return response()->file($path);
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
        $media = Media::where('is_shared', true)->latest()->get()->map(function($m) {
            $m->url = route('contributor.media.view', $m->id);
            return $m;
        });
        return response()->json($media);
    }
}
