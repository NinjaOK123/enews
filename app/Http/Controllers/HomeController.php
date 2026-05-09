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
                    ->latest('published_at')
                    ->limit(5)
                    ->get();
            }

            // ── "Mới nhất" sidebar ────────────────────────────────────────
            $sidebarLatest = Post::published()
                ->with('category:id,name,slug')
                ->latest('published_at')
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

    private function loadSections(array $slugs): array
    {
        // 1 query: lấy tất cả categories cần thiết
        $categories = Category::whereIn('slug', $slugs)
            ->where('is_active', true)
            ->pluck('id', 'slug');

        $sections = [];
        
        // Tuy dùng vòng lặp N query nhưng mỗi query đều có LIMIT 4, 
        // tốc độ cực nhanh so với việc load hàng vạn bài viết rồi groupBy trên RAM
        foreach ($slugs as $slug) {
            $catId = $categories[$slug] ?? null;
            if ($catId) {
                $sections[$slug] = Post::published()
                    ->where('category_id', $catId)
                    ->with(['author:id,name', 'category:id,name,slug'])
                    ->latest('published_at')
                    ->limit(4)
                    ->get();
            } else {
                $sections[$slug] = collect();
            }
        }

        return $sections;
    }
}
