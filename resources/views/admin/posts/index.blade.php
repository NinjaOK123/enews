@extends('layouts.admin')
@section('title', 'Quản lý bài viết toàn hệ thống')
@section('content')
<div class="mt-4 p-6 md:p-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Quản lý bài viết</h3>
        <div class="flex items-center gap-3">
            <!-- Sync Enews AGU Button -->
            <form action="{{ route('admin.posts.sync-agu') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn kết nối và đồng bộ tin mới nhất từ enews.agu.edu.vn?')">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-[13px] font-medium rounded-lg shadow-sm transition-colors shrink-0 outline-none">
                    <i class="bi bi-arrow-repeat"></i> Đồng bộ Enews AGU
                </button>
            </form>

            <!-- Admin can create posts by redirecting to contributor create -->
            <a href="{{ route('contributor.posts.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 !text-white dark:!text-zinc-900 text-[13px] font-medium rounded-lg shadow-sm transition-colors shrink-0 outline-none">
                <i class="bi bi-plus-lg"></i> Viết bài mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 mb-5 transition-colors">
        <form action="{{ route('admin.posts.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <!-- Từ khóa -->
                <div>
                    <label for="q" class="block text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5 pt:mt-0 transition-colors">Tìm kiếm</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 transition-colors"></i>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Tiêu đề, tác giả..." 
                               class="w-full !pl-9 pr-3 py-1.5 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 text-gray-700 dark:text-zinc-200 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 dark:focus:ring-emerald-500/20 focus:border-green-500 focus:bg-white dark:focus:bg-zinc-950 outline-none shadow-sm transition-all placeholder:text-zinc-400 dark:placeholder:text-zinc-600">
                    </div>
                </div>

                <!-- Chuyên mục -->
                <div>
                    <label for="category_id" class="block text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5 transition-colors">Chuyên mục</label>
                    <div class="relative">
                        <i class="bi bi-folder2-open absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 transition-colors"></i>
                        <select name="category_id" id="category_id" class="w-full !pl-9 pr-7 py-1.5 appearance-none bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 text-gray-700 dark:text-zinc-200 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 dark:focus:ring-emerald-500/20 focus:border-green-500 focus:bg-white dark:focus:bg-zinc-950 outline-none shadow-sm transition-all">
                            <option value="">Tất cả chuyên mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-500 dark:text-zinc-500 pointer-events-none transition-colors"></i>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div>
                    <label for="status" class="block text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5 transition-colors">Trạng thái</label>
                    <div class="relative">
                        <i class="bi bi-funnel absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 transition-colors"></i>
                        <select name="status" id="status" class="w-full !pl-9 pr-7 py-1.5 appearance-none bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 text-gray-700 dark:text-zinc-200 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 dark:focus:ring-emerald-500/20 focus:border-green-500 focus:bg-white dark:focus:bg-zinc-950 outline-none shadow-sm transition-all">
                            <option value="">Tất cả trạng thái</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản (Đăng)</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối / Đã xoá</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-500 dark:text-zinc-500 pointer-events-none transition-colors"></i>
                    </div>
                </div>

                <!-- Ngày đăng -->
                <div>
                    <label for="date" class="block text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide mb-1.5 transition-colors">Ngày đăng</label>
                    <div class="relative">
                        <i class="bi bi-calendar3 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 transition-colors"></i>
                        <input type="date" name="date" id="date" value="{{ request('date') }}" 
                               class="w-full !pl-9 pr-3 py-1.5 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 text-gray-700 dark:text-zinc-200 text-[13px] rounded-lg focus:ring-2 focus:ring-green-500/20 dark:focus:ring-emerald-500/20 focus:border-green-500 focus:bg-white dark:focus:bg-zinc-950 outline-none shadow-sm transition-all [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:w-full">
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-zinc-800/80 flex items-center justify-end gap-2.5 transition-colors">
                @if(request()->anyFilled(['q', 'category_id', 'status', 'date', 'sort']))
                <a href="{{ route('admin.posts.index') }}" class="px-4 py-1.5 text-[13px] font-semibold text-gray-500 dark:text-zinc-400 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 hover:text-gray-700 dark:hover:text-zinc-200 rounded-lg transition-colors">
                    Xóa lọc
                </a>
                @endif
                
                <select name="sort" class="px-3 py-1.5 text-[13px] font-semibold bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500/20 dark:focus:ring-emerald-500/20 focus:border-green-500 transition-all cursor-pointer shadow-sm">
                    <option value="desc" {{ request('sort') != 'asc' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Cũ nhất</option>
                </select>

                <button type="submit" class="px-5 py-1.5 bg-gray-800 dark:bg-zinc-100 hover:bg-black dark:hover:bg-white text-white dark:text-zinc-900 text-[13px] font-semibold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 dark:focus:ring-zinc-100 flex items-center gap-1.5">
                    <i class="bi bi-funnel-fill text-xs"></i> Lọc kết quả
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-800 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-zinc-950/40 border-b border-gray-100 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 font-semibold text-[11px] tracking-wider uppercase transition-colors">
                    <tr>
                        <th class="px-6 py-5 text-center">Tiêu đề</th>
                        <th class="px-6 py-5 text-center">Chuyên mục</th>
                        <th class="px-6 py-5 text-center">Tác giả</th>
                        <th class="px-6 py-5 text-center">Người đăng</th>
                        <th class="px-6 py-5 text-center">Trạng thái</th>
                        <th class="px-6 py-5 text-center">Hiện Slider</th>
                        <th class="px-6 py-5 text-center">Ngày đăng</th>
                        <th class="px-6 py-5 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/80 text-gray-700 dark:text-zinc-300">
                    @forelse($posts as $post)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-6 py-5 align-top whitespace-normal min-w-[280px]">
                            <p class="font-bold text-gray-900 dark:text-zinc-100 leading-snug group-hover:text-green-700 dark:group-hover:text-emerald-400 transition-colors">{{ \Illuminate\Support\Str::limit($post->title, 50) }}</p>
                        </td>
                        <td class="px-6 py-5 align-top">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 text-[11px] font-bold tracking-wide transition-colors">
                                <i class="bi bi-tag-fill text-gray-400 dark:text-zinc-500"></i> {{ $post->category->name ?? 'Không có' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 align-top text-center">
                            @if($post->source_author)
                                <span class="font-medium text-gray-800 dark:text-zinc-200 text-[13px]">{{ $post->source_author }}</span>
                            @else
                                <span class="text-gray-400 dark:text-zinc-500 text-[12px] italic">Không có</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 align-top">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-100 to-emerald-200 dark:from-emerald-900/30 dark:to-emerald-800/30 text-emerald-800 dark:text-emerald-400 font-bold flex items-center justify-center text-xs shadow-sm">
                                    {{ strtoupper(substr($post->author->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800 dark:text-zinc-200 text-[13px]">{{ $post->author->name ?? 'Ẩn danh' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 align-top text-center">
                            @if($post->status === 'published')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-bold border border-emerald-200/60 dark:border-emerald-500/20 shadow-sm transition-colors"><i class="bi bi-check2-circle text-[10px]"></i> Đã xuất bản</span>
                            @elseif($post->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-bold border border-amber-200/60 dark:border-amber-500/20 shadow-sm transition-colors"><i class="bi bi-hourglass-split text-[10px]"></i> Chờ duyệt</span>
                            @elseif($post->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-xs font-bold border border-red-200/60 dark:border-red-500/20 shadow-sm transition-colors"><i class="bi bi-x-circle text-[10px]"></i> Từ chối</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-50 dark:bg-zinc-800/80 text-gray-600 dark:text-zinc-400 text-xs font-bold border border-gray-200/60 dark:border-zinc-700 shadow-sm transition-colors"><i class="bi bi-file-earmark-text text-[10px]"></i> Bản nháp</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 align-top text-center">
                            <div x-data="{
                                isOn: {{ $post->is_featured ? 'true' : 'false' }},
                                toggle() {
                                    let oldState = this.isOn;
                                    this.isOn = !this.isOn; // Optimistic update immediately
                                    
                                    axios.post('{{ route('admin.posts.toggle-slider', $post->id) }}')
                                    .then(res => {
                                        if(res.data.success) {
                                            this.isOn = !!res.data.is_featured;
                                        } else {
                                            this.isOn = oldState; // Revert if logically failed
                                        }
                                    })
                                    .catch(err => {
                                        this.isOn = oldState; // Revert on network error
                                        if (err.response && err.response.data && err.response.data.message) {
                                            alert(err.response.data.message);
                                        } else {
                                            alert('Lỗi cập nhật Slide!');
                                        }
                                    });
                                }
                            }" class="flex flex-col items-center justify-center gap-1.5 w-full">
                                <button type="button" @click="toggle()" :class="isOn ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-200 dark:bg-zinc-700'" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer !rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-zinc-900">
                                    <span class="sr-only">Toggle Slider</span>
                                    <span aria-hidden="true" :class="isOn ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block h-4 w-4 transform !rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span x-text="isOn ? 'Hiện Slide' : 'Đang ẩn'" class="text-[11px] font-bold transition-colors" :class="isOn ? 'text-blue-700 dark:text-blue-400' : 'text-gray-400 dark:text-zinc-500'"></span>
                            </div>
                        </td>
                        <td class="px-6 py-5 align-top text-center text-gray-500 dark:text-zinc-400 text-[13px] font-medium transition-colors">
                            <i class="bi bi-clock me-1 text-gray-400 dark:text-zinc-500"></i> {{ optional($post->published_at)->format('d/m/Y') ?? 'Chưa đăng' }}<br>
                            {{ optional($post->published_at)->format('H:i') }}
                        </td>
                        <td class="px-6 py-5 align-top text-right" x-data="{ actOpen: false }" @click.outside="actOpen = false">
                            <div class="relative inline-flex items-center justify-end text-left">
                                <button @click="actOpen = !actOpen" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors shadow-sm">
                                    Thao tác <i class="bi bi-chevron-down text-xs transition-transform" :class="actOpen ? 'rotate-180' : ''"></i>
                                </button>
                                
                                <div x-show="actOpen" x-transition.opacity.duration.200ms x-cloak
                                     class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-zinc-900 rounded-xl shadow-[0_8px_30px_rgba(0,0,0,0.12)] border border-gray-100 dark:border-zinc-800 py-1.5 z-[60] overflow-hidden transition-colors" 
                                     style="display:none;">
                                     
                                    @if($post->status === 'published')
                                        <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-700 dark:hover:text-blue-400 transition-colors">
                                            <i class="bi bi-eye text-blue-500"></i> Xem bài đã duyệt
                                        </a>
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:text-orange-700 dark:hover:text-orange-400 transition-colors">
                                            <i class="bi bi-pencil-square text-orange-500"></i> Sửa bài đã duyệt
                                        </a>
                                    @else
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-700 dark:hover:text-indigo-400 transition-colors font-medium">
                                            <i class="bi bi-pencil-square text-indigo-500"></i> Xem & Duyệt bài
                                        </a>
                                    @endif
                                    
                                    <div class="border-t border-gray-50 dark:border-zinc-800 my-1"></div>
                                    <a href="{{ route('admin.posts.revisions', $post) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-purple-50 dark:hover:bg-purple-500/10 hover:text-purple-700 dark:hover:text-purple-400 transition-colors">
                                        <i class="bi bi-clock-history text-purple-500"></i> Lịch sử chỉnh sửa
                                    </a>

                                    <div class="border-t border-gray-50 dark:border-zinc-800 my-1"></div>
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="m-0" onsubmit="window.confirmFormSubmit(event, 'Bạn có chắc chắn muốn xoá bài viết này không?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-700 dark:hover:text-red-300 transition-colors font-semibold text-left">
                                            <i class="bi bi-trash3 text-red-500"></i> Xóa bài viết
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-500 dark:text-zinc-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-zinc-800 flex items-center justify-center">
                                    <i class="bi bi-journal-x text-3xl text-gray-400 dark:text-zinc-600"></i>
                                </div>
                                <p class="font-medium text-[15px] dark:text-zinc-300">Chưa có bài viết nào trong hệ thống.</p>
                                <a href="{{ route('contributor.posts.create') }}" class="mt-2 text-sm text-green-600 dark:text-emerald-400 hover:text-green-700 dark:hover:text-emerald-300 font-semibold underline underline-offset-4">Bắt đầu viết bài đầu tiên</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800/80 bg-gray-50/50 dark:bg-zinc-950/40 transition-colors">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

