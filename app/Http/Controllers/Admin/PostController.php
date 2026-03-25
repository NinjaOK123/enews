<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
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
        
        // Tìm kiếm theo tên bài hoặc tên tác giả
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                        ->orWhereHas('author', function($qA) use ($q) {
                            $qA->where('name', 'like', "%{$q}%");
                        });
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo ngày đăng
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $posts = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.posts.index', compact('posts', 'categories'));
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

    /**
     * Nhanh chóng Duyệt bài
     */
    public function approve(Post $post)
    {
        $post->update(['status' => 'published', 'published_at' => now()]);
        return back()->with('success', 'Đã duyệt và xuất bản bài viết thành công.');
    }

    /**
     * Nhanh chóng Từ chối/Gỡ bài
     */
    public function reject(Post $post)
    {
        $post->update(['status' => 'rejected']);
        return back()->with('success', 'Đã từ chối/gỡ bài viết thành công.');
    }

    /**
     * View post revision history
     */
    public function revisions(Post $post): View
    {
        $revisions = \App\Models\PostRevision::where('post_id', $post->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.posts.revisions', compact('post', 'revisions'));
    }
}
