<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'author', 'date_from', 'date_to', 'category_id']);

        $hasFilters = collect($filters)->filter()->isNotEmpty();

        $posts = collect();
        $total = 0;

        if ($hasFilters) {
            $keyword = $filters['keyword'] ?? '';
            
            // Nếu có từ khóa, dùng Scout (Full-text/Meilisearch engine)
            if (!empty($keyword)) {
                $scoutBuilder = Post::search($keyword)->query(function ($query) use ($filters) {
                    $query->published()
                          ->filterAdvanced($filters)
                          ->with(['author:id,name,avatar', 'category:id,name,slug']);
                });
                $posts = $scoutBuilder->paginate(10)->withQueryString();
            } else {
                // Nếu không có từ khóa (chỉ dùng các bộ lọc khác), truy vấn thẳng DB
                $query = Post::published()
                    ->filterAdvanced($filters)
                    ->with(['author:id,name,avatar', 'category:id,name,slug'])
                    ->latest();
                    
                $posts = $query->paginate(10)->withQueryString();
            }

            $total = $posts->total();
        }

        $categories = Category::active()->roots()->get();

        return view('frontend.search', compact('posts', 'filters', 'categories', 'total', 'hasFilters'));
    }
}
