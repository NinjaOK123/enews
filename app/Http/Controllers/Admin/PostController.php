<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display all posts with filter options for Admin
     */
    public function index(Request $request): View
    {
        $query = Post::with(['author', 'category']);
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $posts = $query->latest()->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Note: Creating and updating posts are best handled by reusing 
     * Contributor\PostController logic or custom form. Here we keep it basic.
     */
    public function edit(Post $post): View
    {
        $categories = \App\Models\Category::all();
        // Uses the same view as contributor, or a dedicated admin view.
        return view('contributor.posts.create', compact('post', 'categories'));
    }

    /**
     * Admin deleting any post
     */
    public function destroy(Post $post)
    {
        // soft delete or hard delete depending on Post model setup.
        $post->delete();
        return back()->with('success', 'Đã xoá bài viết.');
    }
}
