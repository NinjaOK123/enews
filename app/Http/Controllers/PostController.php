<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    /**
     * Hiển thị trang chi tiết bài viết.
     * Dùng Route Model Binding theo slug.
     */
    public function show(Post $post): \Illuminate\View\View
    {
        // Chỉ hiện bài đã publish
        abort_if($post->status !== 'published', 404);

        // Tăng lượt xem mỗi lần vào trang
        $post->increment('view_count');

        // Load quan hệ đầy đủ tránh N+1 query
        $post->load([
            'author:id,name,avatar',
            'category:id,name,slug',
            'comments' => fn($q) => $q->approved()->with('user:id,name,avatar')->latest(),
        ]);

        // Lấy 4 bài viết liên quan: cùng chuyên mục, trừ bài hiện tại, mới nhất
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest()
            ->limit(4)
            ->get();

        // Sidebar: 6 bài mới nhất (toàn site)
        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->with(['category:id,name,slug'])
            ->latest()
            ->limit(6)
            ->get();

        // Sidebar: tất cả chuyên mục với số bài
        $allCategories = \App\Models\Category::where('is_active', true)
            ->withCount(['posts' => fn($q) => $q->where('status','published')])
            ->orderByDesc('posts_count')
            ->get();

        return view('posts.show', compact('post', 'relatedPosts', 'recentPosts', 'allCategories'));
    }

    /**
     * Lưu bình luận mới.
     */
    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        // Chỉ user đăng nhập mới comment
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để bình luận.');
        }

        // Validate
        $request->validate([
            'content' => 'required|min:5|max:1000',
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.min'      => 'Bình luận phải có ít nhất 5 ký tự.',
            'content.max'      => 'Bình luận không được vượt quá 1000 ký tự.',
        ]);

        // Lưu comment
        Comment::create([
            'post_id'     => $post->id,
            'user_id'     => auth()->id(),
            'content'     => $request->content,
            'is_approved' => true,
        ]);

        return redirect()
            ->route('post.show', $post->slug)
            ->with('success', 'Bình luận của bạn đã được gửi thành công!')
            ->withFragment('comments');
    }
}
