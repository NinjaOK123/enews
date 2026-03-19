<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle Like cho bài viết (AJAX).
     */
    public function toggle(Post $post)
    {
        $user = auth()->user();

        $existing = PostLike::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            PostLike::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $liked = true;
        }

        $count = PostLike::where('post_id', $post->id)->count();

        return response()->json([
            'liked' => $liked,
            'count' => $count,
        ]);
    }
}
