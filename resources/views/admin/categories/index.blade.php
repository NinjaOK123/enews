@extends('layouts.admin')
@section('title', 'Quản lý chuyên mục')
@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            @if(isset($parentCat))
                <div class="flex items-center gap-3 mb-1">
                    <a href="{{ route('admin.categories.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors shadow-sm" title="Quay lại danh mục chính">
                        <i class="bi bi-arrow-left text-lg"></i>
                    </a>
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Chi nhánh con: {{ $parentCat->name }}</h3>
                </div>
            @else
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight"><i class="bi bi-folder-fill text-emerald-500 me-2"></i>Quản lý chuyên mục</h3>
            @endif
            <p class="text-sm text-gray-500 mt-1">Quản lý và sắp xếp các chuyên mục bài viết</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-emerald-500/20">
            <i class="bi bi-plus-circle text-lg"></i> Thêm chuyên mục
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-exclamation-circle-fill text-xl text-red-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row gap-3">
            @if(request('parent_id'))
                <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
            @endif
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc slug chuyên mục..." class="w-full !pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all">
            </div>
            <div class="w-full sm:w-48 relative">
                <select name="status" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">-- Mọi trạng thái --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hiện</option>
                    <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400"><i class="bi bi-chevron-down text-xs"></i></div>
            </div>
            <button type="submit" class="px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 w-full sm:w-auto">
                <i class="bi bi-funnel"></i> Lọc
            </button>
            @if(request()->anyFilled(['search', 'status', 'parent_id']))
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all text-center w-full sm:w-auto">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold text-[11px] tracking-wider uppercase">
                    <tr>
                        <th class="px-5 py-4">Tên chuyên mục</th>
                        <th class="px-4 py-4">Slug</th>
                        <th class="px-4 py-4 text-center whitespace-nowrap">Bài viết</th>
                        <th class="px-4 py-4 text-center whitespace-nowrap">Trạng thái</th>
                        <th class="px-5 py-4 text-right whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-5 py-3">
                            <div class="flex flex-col">
                                <strong class="text-gray-900 font-bold group-hover:text-emerald-600 transition-colors">{{ $category->name }}</strong>
                                @if($category->parent)
                                    <span class="inline-flex items-center gap-1 mt-1 w-max px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-500">
                                        <i class="bi bi-arrow-return-right"></i> {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 mt-1 w-max px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide uppercase bg-emerald-50 text-emerald-600">
                                        Mục gốc
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">
                            {{ $category->slug }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[11px] font-bold">{{ $category->posts_count }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/60 shadow-sm"><i class="bi bi-check-circle-fill"></i> Đang hiện</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-[11px] font-bold border border-gray-200/60 shadow-sm"><i class="bi bi-eye-slash-fill"></i> Đang ẩn</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                @if(!$category->parent_id)
                                <a href="{{ route('admin.categories.index', ['parent_id' => $category->id]) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors shadow-sm" title="Xem các chuyên mục con trực thuộc">
                                    <i class="bi bi-list-nested"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.categories.edit', $category) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors shadow-sm" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="m-0" onsubmit="return confirm('Chắc chắn xoá chuyên mục?');">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm" title="Xóa">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="bi bi-folder-x text-4xl text-gray-300"></i>
                                <p>Chưa có chuyên mục nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
