<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Banner;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Cache toàn bộ data trang chủ 5 phút (300s)
        // Khi có bài mới, cache tự hết hạn sau 5 phút
        $homeData = Cache::remember('home.page.data', 300, function () {
            // ── Hero: 5 bài TIÊU BIỂU (Bật Slider) ─────────────────────────
            $heroPosts = Post::published()
                ->where('is_featured', true)
                ->with(['category:id,name,slug', 'author:id,name'])
                ->orderBy('featured_order', 'asc')
                ->orderByDesc('published_at')
                ->limit(5)
                ->get();

            // Nếu không có bài tiêu biểu nào, fallback lấy 5 bài mới nhất
            if ($heroPosts->isEmpty()) {
                $heroPosts = Post::published()
                    ->with(['category:id,name,slug', 'author:id,name'])
                    ->latest()
                    ->limit(5)
                    ->get();
            }

            // ── "Mới nhất" sidebar ────────────────────────────────────────
            $sidebarLatest = Post::published()
                ->with('category:id,name,slug')
                ->latest()
                ->limit(9)
                ->get();

            // ── Sections: tối ưu — load TẤT CẢ categories 1 query ────────
            $sections = $this->loadSections([
                'ban-tin-agu',
                'guong-mat-agu',
                'enews-va-ban-doc',
                'cau-chuyen-agu',
                'khoa-hoc-voi-agu',
                'goc-nhin',
                'tan-man',
                'luot-web-cung-sv',
                'phong-su-anh',
                'clb-van-tho',
                'clb-am-nhac',
                'clb-tin-hoc',
                'clb-tam-tinh-tre',
                'clb-ngoai-ngu',
                'clb-sach-ban-doc',
                'clb-nghe-thuat',
                'clb-su-hoc',
                'clb-moi-truong',
                'clb-du-lich',
            ]);

            // ── Banners cuộc thi (marquee) ────────────────────────────────
            $banners = Banner::active()->get();

            return compact('heroPosts', 'sidebarLatest', 'sections', 'banners');
        });

        return view('frontend.home', $homeData);
    }

    /**
     * Load posts for each section — tối ưu: 1 query categories + 1 query posts tất cả
     * Thay vì 20 queries riêng lẻ → chỉ 2 queries tổng
     */
    private function loadSections(array $slugs): array
    {
        // 1 query: lấy tất cả categories cần thiết
        $categories = Category::whereIn('slug', $slugs)
            ->where('is_active', true)
            ->pluck('id', 'slug');

        $catIds = $categories->values()->toArray();

        if (empty($catIds)) {
            return array_fill_keys($slugs, collect());
        }

        // 1 query: lấy TẤT CẢ posts của tất cả sections cùng lúc
        $allPosts = Post::published()
            ->whereIn('category_id', $catIds)
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest()
            ->get()
            ->groupBy('category_id');

        // Map về đúng slug
        $sections = [];
        foreach ($slugs as $slug) {
            $catId = $categories[$slug] ?? null;
            $sections[$slug] = $catId
                ? ($allPosts[$catId] ?? collect())->take(4)
                : collect();
        }

        return $sections;
    }
}
