<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class ContributorController extends Controller
{
    /**
     * Contributor Dashboard — quản lý bài viết của bản thân.
     */
    public function dashboard(): View
    {
        $userId = auth()->id();

        // Tất cả bài của contributor này
        $myPosts = Post::with(['category:id,name,slug'])
            ->where('author_id', $userId)
            ->latest()
            ->paginate(15);

        $stats = [
            'total'     => Post::where('author_id', $userId)->count(),
            'published' => Post::where('author_id', $userId)->where('status', 'published')->count(),
            'pending'   => Post::where('author_id', $userId)->where('status', 'pending')->count(),
            'draft'     => Post::where('author_id', $userId)->where('status', 'draft')->count(),
            'total_views' => Post::where('author_id', $userId)->sum('view_count'),
        ];

        return view('dashboard.contributor', compact('myPosts', 'stats'));
    }
}
