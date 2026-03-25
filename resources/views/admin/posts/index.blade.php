@extends('layouts.admin')
@section('title', 'Quản lý bài viết toàn hệ thống')
@section('content')
<div class="mt-4 p-6 md:p-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Quản lý bài viết</h3>
        <!-- Admin can create posts by redirecting to contributor create -->
        <a href="{{ route('contributor.posts.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-emerald-500/20">
            <i class="bi bi-pencil-square"></i> Viết bài mới
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-100 mb-5">
        <form action="{{ route('admin.posts.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <!-- Từ khóa -->
                <div>
                    <label for="q" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1.5">Tìm kiếm</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Tiêu đề, tác giả..." 
                               class="w-full !pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 focus:bg-white outline-none shadow-sm transition-all">
                    </div>
                </div>

                <!-- Chuyên mục -->
                <div>
                    <label for="category_id" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1.5">Chuyên mục</label>
                    <div class="relative">
                        <i class="bi bi-folder2-open absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="category_id" id="category_id" class="w-full !pl-9 pr-7 py-1.5 appearance-none bg-gray-50 border border-gray-200 text-gray-700 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 focus:bg-white outline-none shadow-sm transition-all">
                            <option value="">Tất cả chuyên mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-500 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div>
                    <label for="status" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1.5">Trạng thái</label>
                    <div class="relative">
                        <i class="bi bi-funnel absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="status" id="status" class="w-full !pl-9 pr-7 py-1.5 appearance-none bg-gray-50 border border-gray-200 text-gray-700 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 focus:bg-white outline-none shadow-sm transition-all">
                            <option value="">Tất cả trạng thái</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản (Đăng)</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối / Đã xoá</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-500 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Ngày đăng -->
                <div>
                    <label for="date" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1.5">Ngày đăng</label>
                    <div class="relative">
                        <i class="bi bi-calendar3 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="date" name="date" id="date" value="{{ request('date') }}" 
                               class="w-full !pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 focus:bg-white outline-none shadow-sm transition-all [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:w-full">
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                @if(request()->anyFilled(['q', 'category_id', 'status', 'date']))
                <a href="{{ route('admin.posts.index') }}" class="px-4 py-1.5 text-[13px] font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 hover:text-gray-700 rounded-lg transition-colors">
                    Xóa lọc
                </a>
                @endif
                <button type="submit" class="px-5 py-1.5 bg-gray-800 hover:bg-black text-white text-[13px] font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 flex items-center gap-1.5">
                    <i class="bi bi-funnel-fill text-xs"></i> Lọc kết quả
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-semibold tracking-wide text-xs uppercase">
                    <tr>
                        <th class="px-6 py-5">Tiêu đề</th>
                        <th class="px-6 py-5">Chuyên mục</th>
                        <th class="px-6 py-5">Người đăng</th>
                        <th class="px-6 py-5 text-center">Trạng thái</th>
                        <th class="px-6 py-5">Ngày tạo</th>
                        <th class="px-6 py-5 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($posts as $post)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-5 align-top whitespace-normal min-w-[280px]">
                            <p class="font-bold text-gray-900 leading-snug group-hover:text-green-700 transition-colors">{{ \Illuminate\Support\Str::limit($post->title, 50) }}</p>
                        </td>
                        <td class="px-6 py-5 align-top">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-[11px] font-bold tracking-wide">
                                <i class="bi bi-tag-fill text-gray-400"></i> {{ $post->category->name ?? 'Không có' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 align-top">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-100 to-emerald-200 text-emerald-800 font-bold flex items-center justify-center text-xs shadow-sm">
                                    {{ strtoupper(substr($post->author->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800 text-[13px]">{{ $post->author->name ?? 'Ẩn danh' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 align-top text-center">
                            @if($post->status === 'published')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200/60 shadow-sm"><i class="bi bi-check2-circle text-[10px]"></i> Đã xuất bản</span>
                            @elseif($post->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200/60 shadow-sm"><i class="bi bi-hourglass-split text-[10px]"></i> Chờ duyệt</span>
                            @elseif($post->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 text-red-700 text-xs font-bold border border-red-200/60 shadow-sm"><i class="bi bi-x-circle text-[10px]"></i> Từ chối</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-50 text-gray-600 text-xs font-bold border border-gray-200/60 shadow-sm"><i class="bi bi-file-earmark-text text-[10px]"></i> Bản nháp</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 align-top text-gray-500 text-[13px] font-medium">
                            <i class="bi bi-clock me-1 text-gray-400"></i> {{ $post->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-5 align-top text-right relative" x-data="{ actOpen: false }" @click.outside="actOpen = false">
                            <div class="flex items-center justify-end">
                                <button @click="actOpen = !actOpen" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-100 transition shadow-sm">
                                    Thao tác <i class="bi bi-chevron-down text-xs transition-transform" :class="actOpen ? 'rotate-180' : ''"></i>
                                </button>
                            </div>
                            
                            <div x-show="actOpen" x-transition.opacity.duration.200ms x-cloak
                                 class="absolute right-6 top-14 w-56 bg-white rounded-xl shadow-[0_8px_30px_rgba(0,0,0,0.12)] border border-gray-100 py-1.5 z-[60] text-left overflow-hidden" 
                                 style="display:none;">
                                 
                                @if($post->status === 'published')
                                    <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition">
                                        <i class="bi bi-eye text-blue-500"></i> Xem bài đã duyệt
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition">
                                        <i class="bi bi-pencil-square text-orange-500"></i> Sửa bài đã duyệt
                                    </a>
                                @else
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition font-medium">
                                        <i class="bi bi-pencil-square text-indigo-500"></i> Xem & Duyệt bài
                                    </a>
                                @endif
                                
                                <div class="border-t border-gray-50 my-1"></div>
                                <a href="{{ route('admin.posts.revisions', $post) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition">
                                    <i class="bi bi-clock-history text-purple-500"></i> Lịch sử chỉnh sửa
                                </a>

                                <div class="border-t border-gray-50 my-1"></div>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xoá bài viết này không?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition font-semibold text-left">
                                        <i class="bi bi-trash3 text-red-500"></i> Xóa bài viết
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center">
                                    <i class="bi bi-journal-x text-3xl text-gray-400"></i>
                                </div>
                                <p class="font-medium text-[15px]">Chưa có bài viết nào trong hệ thống.</p>
                                <a href="{{ route('contributor.posts.create') }}" class="mt-2 text-sm text-green-600 hover:text-green-700 font-semibold underline underline-offset-4">Bắt đầu viết bài đầu tiên</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

