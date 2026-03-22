<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Banner;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // ── Hero: 5 bài mới nhất (bất kỳ chuyên mục) ──────────────────────
        $heroPosts = Post::published()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        // ── "Mới nhất" sidebar ─────────────────────────────────────────────
        $sidebarLatest = Post::published()
            ->with('category:id,name,slug')
            ->latest()
            ->limit(9)
            ->get();

        // ── Sections: lấy bài theo từng chuyên mục ─────────────────────────
        $sections = $this->loadSections([
            // ── Main sections ───────────────────────────────
            'ban-tin-agu',
            'guong-mat-agu',
            'enews-va-ban-doc',
            'cau-chuyen-agu',
            'khoa-hoc-voi-agu',
            'goc-nhin',
            'tan-man',
            'luot-web-cung-sv',
            'phong-su-anh',
            // ── CLB sub-categories ───────────────────────────
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

        // ── Banners cuộc thi (chạy marquee)
        $banners = Banner::active()->get();

        return view('frontend.home', compact('heroPosts', 'sidebarLatest', 'sections', 'banners'));
    }

    /**
     * Load posts for each section category.
     * Returns: [ 'ban-tin-agu' => Collection, ... ]
     */
    private function loadSections(array $slugs): array
    {
        $categories = Category::whereIn('slug', $slugs)
            ->where('is_active', true)
            ->pluck('id', 'slug');

        $sections = [];

        foreach ($slugs as $slug) {
            $catId = $categories[$slug] ?? null;
            if (!$catId) {
                $sections[$slug] = collect();
                continue;
            }

            // First post = featured, rest = list items (limit 4 total)
            $sections[$slug] = Post::published()
                ->where('category_id', $catId)
                ->with(['author:id,name', 'category:id,name,slug'])
                ->latest()
                ->limit(4)
                ->get();
        }

        return $sections;
    }
}
