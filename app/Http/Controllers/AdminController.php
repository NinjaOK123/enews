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
            
            // Fake Growth Rates (%)
            'growth_posts'     => 12.5,
            'growth_pending'   => -5.0,
            'growth_users'     => 8.4,
            'growth_comments'  => 15.2,
            'growth_views'     => 24.8,
            'growth_categories'=> 2.1,
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

        // Top 5 bài viết nhiều view nhất
        $topViewedPosts = Post::with(['category:id,name,slug'])
            ->where('status', 'published')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        // Top 5 chuyên mục phổ biến (nhiều bài đăng)
        $topCategories = Category::withCount(['posts' => function($q) {
                $q->where('status', 'published');
            }])
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'usersByRole', 'latestPosts', 'pendingPosts', 'latestUsers', 'topViewedPosts', 'topCategories'
        ));
    }
}
