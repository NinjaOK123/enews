<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $user = auth()->user();

        // Fix BUG-01: Race condition
        // Unique constraint on (user_id, post_id) should exist in DB.
        // We find the record, if it exists we delete it to unlike.
        // If not, we try to create it. We can do it atomically-ish:
        
        $deleted = PostLike::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->delete();

        if ($deleted) {
            $liked = false;
        } else {
            // firstOrCreate handles race conditions fairly well if DB index exists
            PostLike::firstOrCreate([
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
