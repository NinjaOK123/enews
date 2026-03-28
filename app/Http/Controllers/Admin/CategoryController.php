<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()->with(['parent'])->withCount('posts');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $parentCat = null;
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
            $parentCat = Category::find($request->parent_id);
        }

        $categories = $query->latest()->paginate(15)->appends($request->all());
        return view('admin.categories.index', compact('categories', 'parentCat'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'show_in_menu' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Chuyên mục đã được tạo.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'show_in_menu' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Chuyên mục đã được cập nhật.');
    }

    public function destroy(Category $category)
    {
        if ($category->posts()->count() > 0) {
            return back()->with('error', 'Không thể xoá chuyên mục đang có bài viết.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã xoá chuyên mục.');
    }

    public function toggleActive(Request $request, Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái hoạt động.',
            'is_active' => $category->is_active
        ]);
    }

    public function toggleMenu(Request $request, Category $category)
    {
        $validated = $request->validate([
            'show_in_menu' => 'required|boolean'
        ]);

        $category->update([
            'show_in_menu' => $validated['show_in_menu']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái menu ngang.',
            'show_in_menu' => $category->show_in_menu
        ]);
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:show_menu,hide_menu',
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id'
        ]);

        if ($validated['action'] === 'show_menu') {
            Category::whereIn('id', $validated['ids'])->update(['show_in_menu' => true]);
            $msg = 'Đã hiển thị các chuyên mục đã chọn lên menu.';
        } elseif ($validated['action'] === 'hide_menu') {
            Category::whereIn('id', $validated['ids'])->update(['show_in_menu' => false]);
            $msg = 'Đã ẩn các chuyên mục đã chọn khỏi menu.';
        }

        return redirect()->route('admin.categories.index')->with('success', $msg);
    }

    public function updateOrder(Request $request, Category $category)
    {
        $validated = $request->validate([
            'order' => 'required|integer|min:1'
        ]);
        
        $newOrder = $validated['order'];
        $oldOrder = $category->order;
        
        if ($newOrder != $oldOrder) {
            if ($newOrder < $oldOrder) {
                Category::where('id', '!=', $category->id)
                    ->whereBetween('order', [$newOrder, $oldOrder - 1])
                    ->increment('order');
            } else {
                Category::where('id', '!=', $category->id)
                    ->whereBetween('order', [$oldOrder + 1, $newOrder])
                    ->decrement('order');
            }
            
            $category->update(['order' => $newOrder]);
            $this->normalizeOrders();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thứ tự.'
        ]);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'exists:categories,id'
        ]);

        $ids = $validated['orders'];
        if (empty($ids)) return response()->json(['success' => true]);

        $minOrder = Category::whereIn('id', $ids)->min('order') ?? 1;

        foreach ($ids as $index => $id) {
            Category::where('id', $id)->update(['order' => $minOrder + $index]);
        }

        $this->normalizeOrders();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật vị trí.'
        ]);
    }

    private function normalizeOrders()
    {
        $categories = Category::orderBy('order', 'asc')->orderBy('updated_at', 'desc')->get();
        $currentOrder = 1;
        
        foreach ($categories as $cat) {
            if ($cat->order != $currentOrder) {
                $cat->timestamps = false; // Ngăn không tự động cập nhật updated_at
                $cat->update(['order' => $currentOrder]);
                $cat->timestamps = true;
            }
            $currentOrder++;
        }
    }
}
