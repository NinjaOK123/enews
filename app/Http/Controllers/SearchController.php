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
        $sort = $request->input('sort', 'desc');

        $hasFilters = collect($filters)->filter()->isNotEmpty();

        $posts = collect();
        $total = 0;

        if ($hasFilters || $sort !== 'desc') {
            $keyword = $filters['keyword'] ?? '';
            
            // Nếu có từ khóa, dùng Scout (Full-text/Meilisearch engine)
            if (!empty($keyword)) {
                $scoutBuilder = Post::search($keyword)->query(function ($query) use ($filters) {
                    $query->published()
                          ->filterAdvanced($filters)
                          ->with(['author:id,name,avatar', 'category:id,name,slug']);
                });
                
                // Mặc định Scout sắp xếp theo relevance, ta có thể ghi đè nếu muốn:
                // Để Scout tự do tìm kiếm tốt nhất thì ko bắt buộc, nhưng nếu user ép "Mới nhất/Cũ nhất":
                try {
                    $scoutBuilder->orderBy('published_at', $sort);
                } catch (\Exception $e) {
                    // Ignore if driver doesn't support
                }

                $posts = $scoutBuilder->paginate(10)->withQueryString();
            } else {
                // Nếu không có từ khóa (chỉ dùng các bộ lọc khác), truy vấn thẳng DB
                $query = Post::published()
                    ->filterAdvanced($filters)
                    ->with(['author:id,name,avatar', 'category:id,name,slug']);
                    
                if ($sort === 'asc') {
                    $query->orderBy('published_at', 'asc');
                } else {
                    $query->orderBy('published_at', 'desc');
                }
                    
                $posts = $query->paginate(10)->withQueryString();
            }

            $total = $posts->total();
            
            // Nếu không có filter gì ngoài sort thì cũng bật flag để hiện kết quả
            $hasFilters = true; 
        }

        $categories = Category::active()->roots()->get();

        return view('frontend.search', compact('posts', 'filters', 'categories', 'total', 'hasFilters'));
    }
}
