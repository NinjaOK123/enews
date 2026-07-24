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
            $query->whereDate('published_at', $request->date);
        }


        // Sắp xếp
        $sort = $request->input('sort', 'desc');
        if ($sort === 'asc') {
            $query->orderBy('published_at', 'asc');
        } else {
            $query->latest('published_at');
        }

        $posts = $query->paginate(15)->withQueryString();
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
        $royaltyRates = \App\Models\RoyaltyRate::orderBy('group_name')->orderBy('name')->get();
        $suggestedAuthors = \App\Models\User::whereIn('role', ['contributor', 'editor', 'admin'])->pluck('name');
        // Uses the same view as contributor, or a dedicated admin view.
        return view('contributor.posts.create', compact('post', 'categories', 'royaltyRates', 'suggestedAuthors'));
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

    public function toggleSlider(Post $post)
    {
        // Fix BUG-03: Race condition with DB::transaction and lockForUpdate
        return \Illuminate\Support\Facades\DB::transaction(function () use ($post) {
            // Lock the posts table for this read
            $currentCount = Post::where('is_featured', true)->lockForUpdate()->count();
            
            // Nếu bài viết đang không hiện trên Slider và được yêu cầu bật
            if (!$post->is_featured) {
                if ($currentCount >= 5) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn chỉ được phép bật tối đa 5 bài trên Slider. Vui lòng tắt một bài bất kỳ trước khi bật bài này!'
                    ], 400);
                }
            }

            $post->is_featured = !$post->is_featured;
            $post->save();

            return response()->json([
                'success' => true,
                'is_featured' => $post->is_featured,
                'message' => $post->is_featured ? 'Đã bật bài viết trên Slider.' : 'Đã tắt bài viết trên Slider.'
            ]);
        });
    }

    public function syncAguNews(Request $request)
    {
        $crawler = new \App\Services\AguNewsCrawlerService();
        $limit = (int) $request->input('limit', 15);
        $result = $crawler->crawlLatestNews($limit);

        if ($result['status'] === 'error') {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
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
