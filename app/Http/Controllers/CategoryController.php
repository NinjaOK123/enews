<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['author:id,name,avatar', 'category:id,name,slug'])
            ->latest()
            ->paginate(12);

        // All active categories for sidebar
        $categories = Category::active()->roots()->get();

        return view('frontend.category', compact('category', 'posts', 'categories'));
    }
}
