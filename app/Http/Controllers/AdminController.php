<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Admin Dashboard — thống kê toàn hệ thống.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_posts'      => Post::count(),
            'published_posts'  => Post::where('status', 'published')->count(),
            'pending_posts'    => Post::where('status', 'pending')->count(),
            'draft_posts'      => Post::where('status', 'draft')->count(),
            'total_users'      => User::count(),
            'total_comments'   => Comment::count(),
            'total_categories' => Category::count(),
            'total_views'      => Post::sum('view_count'),
        ];

        // Users per role
        $usersByRole = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        // 5 bài viết mới nhất
        $latestPosts = Post::with(['author:id,name', 'category:id,name,slug'])
            ->latest()
            ->limit(5)
            ->get();

        // 5 bài chờ duyệt
        $pendingPosts = Post::with(['author:id,name', 'category:id,name,slug'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // 5 user mới đăng ký
        $latestUsers = User::latest()->limit(5)->get();

        return view('dashboard.admin', compact(
            'stats', 'usersByRole', 'latestPosts', 'pendingPosts', 'latestUsers'
        ));
    }
}
