<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaveController extends Controller
{
    /**
     * Lưu / bỏ lưu bài viết vào bộ sưu tập mặc định.
     */
    public function save(Post $post)
    {
        $user = auth()->user();

        // Lấy hoặc tạo bộ sưu tập mặc định "Đã lưu"
        $collection = Collection::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Đã lưu'],
            ['is_public' => false]
        );

        $exists = DB::table('post_saves')
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->where('collection_id', $collection->id)
            ->exists();

        if ($exists) {
            DB::table('post_saves')
                ->where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->where('collection_id', $collection->id)
                ->delete();
            $saved = false;
        } else {
            DB::table('post_saves')->insert([
                'user_id'       => $user->id,
                'post_id'       => $post->id,
                'collection_id' => $collection->id,
                'created_at'    => now(),
            ]);
            $saved = true;
        }

        return response()->json(['saved' => $saved]);
    }

    /**
     * Lưu bài viết vào 1 bộ sưu tập cụ thể.
     */
    public function saveToCollection(Request $request, Post $post)
    {
        $user = auth()->user();
        $request->validate(['collection_id' => 'required|exists:collections,id']);

        $collection = Collection::where('id', $request->collection_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $exists = DB::table('post_saves')
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->where('collection_id', $collection->id)
            ->exists();

        if (!$exists) {
            DB::table('post_saves')->insert([
                'user_id'       => $user->id,
                'post_id'       => $post->id,
                'collection_id' => $collection->id,
                'created_at'    => now(),
            ]);
        }

        return response()->json(['saved' => true, 'collection' => $collection->name]);
    }

    /**
     * Danh sách bộ sưu tập của người dùng.
     */
    public function collections()
    {
        $user = auth()->user();
        $collections = $user->collections()->withCount('posts')->get();
        return response()->json($collections);
    }

    /**
     * Tạo bộ sưu tập mới.
     */
    public function createCollection(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'is_public' => 'boolean',
        ]);

        $collection = auth()->user()->collections()->create([
            'name'      => $request->name,
            'is_public' => $request->boolean('is_public', false),
        ]);

        return response()->json(['collection' => $collection]);
    }

    /**
     * Xóa bộ sưu tập.
     */
    public function deleteCollection(Collection $collection)
    {
        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }
        DB::table('post_saves')->where('collection_id', $collection->id)->delete();
        $collection->delete();
        return response()->json(['deleted' => true]);
    }
}
