<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    /**
     * Dữ liệu trang chủ (Gộp chung để Next.js gọi 1 lần SSR là đủ hết)
     */
    public function home(): JsonResponse
    {
        // Có thể dùng cache giống HomeController
        $homeData = Cache::remember('api.home.data', 300, function () {
            $heroPosts = Post::published()
                ->where('is_featured', true)
                ->with(['category:id,name,slug', 'author:id,name'])
                ->orderBy('featured_order', 'asc')
                ->latest()
                ->limit(5)
                ->get();

            if ($heroPosts->isEmpty()) {
                $heroPosts = Post::published()
                    ->with(['category:id,name,slug', 'author:id,name'])
                    ->latest()
                    ->limit(5)
                    ->get();
            }

            $sidebarLatest = Post::published()
                ->with('category:id,name,slug')
                ->latest()
                ->limit(9)
                ->get();

            $sections = $this->loadSections([
                'ban-tin-agu', 'guong-mat-agu', 'enews-va-ban-doc', 'cau-chuyen-agu',
                'khoa-hoc-voi-agu', 'goc-nhin', 'tan-man', 'luot-web-cung-sv', 'phong-su-anh',
            ]);

            $banners = Banner::active()->get();

            return [
                'heroPosts' => $heroPosts,
                'sidebarLatest' => $sidebarLatest,
                'sections' => $sections,
                'banners' => $banners,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $homeData
        ]);
    }

    /**
     * Tối ưu lấy bài theo nhiều chuyên mục
     */
    private function loadSections(array $slugs): array
    {
        $categories = Category::whereIn('slug', $slugs)
            ->where('is_active', true)
            ->pluck('id', 'slug');

        $catIds = $categories->values()->toArray();

        if (empty($catIds)) {
            return array_fill_keys($slugs, []);
        }

        $allPosts = Post::published()
            ->whereIn('category_id', $catIds)
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest()
            ->get()
            ->groupBy('category_id');

        $sections = [];
        foreach ($slugs as $slug) {
            $catId = $categories[$slug] ?? null;
            $sections[$slug] = $catId ? ($allPosts[$catId] ?? collect())->take(4) : [];
        }

        return $sections;
    }

    /**
     * Chi tiết 1 bài viết và tin liên quan
     */
    public function showPost($slug): JsonResponse
    {
        $post = Post::published()
            ->with(['category:id,name,slug', 'author:id,name,avatar', 'tags:id,name,slug'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Bài liên quan (cùng chuyên mục)
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->with('category:id,name,slug')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'post' => $post,
                'related' => $relatedPosts
            ]
        ]);
    }

    /**
     * Lấy danh sách bài viết theo Category (có phân trang)
     */
    public function categoryPosts($slug, Request $request): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest()
            ->paginate((int) $request->input('limit', 12));

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'posts' => $posts
            ]
        ]);
    }
}
