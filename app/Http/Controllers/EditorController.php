<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class EditorController extends Controller
{
    /**
     * Editor Dashboard — quản lý bài viết chờ duyệt.
     */
    public function dashboard(): View
    {
        // Bài chờ duyệt
        $pendingPosts = Post::with(['author:id,name', 'category:id,name,slug'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        // Bài đã duyệt gần nhất
        $recentPublished = Post::with(['author:id,name', 'category:id,name,slug'])
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(5)
            ->get();

        $stats = [
            'pending'   => Post::where('status', 'pending')->count(),
            'published' => Post::where('status', 'published')->count(),
            'draft'     => Post::where('status', 'draft')->count(),
        ];

        return view('dashboard.editor', compact('pendingPosts', 'recentPublished', 'stats'));
    }
}
