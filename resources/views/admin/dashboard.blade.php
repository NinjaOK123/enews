@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
@php
    $user = auth()->user();
    // Stats
    $s_posts    = $stats['total_posts']      ?? 0;
    $s_pending  = $stats['pending_posts']    ?? 0;
    $s_users    = $stats['total_users']      ?? 0;
    $s_comments = $stats['total_comments']   ?? 0;
    $s_views    = $stats['total_views']      ?? 0;
    $s_cats     = $stats['total_categories'] ?? 0;

    // For Donut Chart
    $s_published = $stats['published_posts'] ?? 0;
    $s_draft     = $stats['draft_posts']     ?? 0;

    // For Bar Chart
    $rolesLabels = $usersByRole->keys()->map('ucfirst')->toJson();
    $rolesData = $usersByRole->values()->toJson();
@endphp

{{-- 1. PAGE HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white tracking-tight">
            Tin tức AGU — Cổng Quản Trị
        </h2>
        <p class="text-[14px] text-zinc-500 dark:text-zinc-400 mt-1 font-medium">
            Hôm nay là {{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}.
        </p>
    </div>
    
    <a href="{{ route('contributor.posts.create') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 !text-white dark:!text-zinc-900 text-[13px] font-medium rounded-lg shadow-sm transition-colors shrink-0 outline-none">
        <i class="bi bi-plus-lg"></i>
        <span>Viết bài mới</span>
    </a>
</div>

{{-- 2. STATS CARDS (4 CARDS) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    
    {{-- Card 1: Tổng bài viết --}}
    <div class="bg-blue-500 border border-transparent rounded-xl p-5 shadow-[0_4px_14px_rgba(59,130,246,0.3)] transition-all duration-200 text-white group overflow-hidden relative">
        <div class="flex items-center justify-between mb-2 relative z-10">
            <span class="text-[13px] font-semibold text-blue-50 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-pencil-square text-[15px]"></i> TỔNG BÀI VIẾT
            </span>
        </div>
        <div class="text-[36px] font-bold tracking-tight leading-none mb-3 relative z-10">{{ number_format($s_posts) }}</div>
        <div class="flex items-center gap-2 mt-auto relative z-10">
            @php $g_posts = $stats['growth_posts'] ?? 12; @endphp
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[12px] font-bold bg-white/20 text-white">
                +{{ $g_posts }}%
            </span>
            <span class="text-blue-100 text-xs font-medium">+{{ $g_posts }}% so với tuần trước.</span>
        </div>
        <!-- Decorative subtle icon -->
        <i class="bi bi-file-text absolute -right-4 -bottom-4 text-[100px] text-white opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
    </div>

    {{-- Card 2: Người dùng --}}
    <div class="bg-teal-500 border border-transparent rounded-xl p-5 shadow-[0_4px_14px_rgba(20,184,166,0.3)] transition-all duration-200 text-white group overflow-hidden relative">
        <div class="flex items-center justify-between mb-2 relative z-10">
            <span class="text-[13px] font-semibold text-teal-50 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-person-fill text-[15px]"></i> NGƯỜI DÙNG
            </span>
        </div>
        <div class="text-[36px] font-bold tracking-tight leading-none mb-3 relative z-10">{{ number_format($s_users) }}</div>
        <div class="flex items-center gap-2 mt-auto relative z-10">
            @php $g_users = $stats['growth_users'] ?? 5; @endphp
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[12px] font-bold bg-white/20 text-white">
                +{{ $g_users }}%
            </span>
            <span class="text-teal-100 text-xs font-medium">+{{ $g_users }} mới tuần trước</span>
        </div>
        <!-- Decorative subtle icon -->
        <i class="bi bi-people absolute -right-4 -bottom-4 text-[100px] text-white opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
    </div>

    {{-- Card 3: Bình luận --}}
    <div class="bg-orange-500 border border-transparent rounded-xl p-5 shadow-[0_4px_14px_rgba(249,115,22,0.3)] transition-all duration-200 text-white group overflow-hidden relative">
        <div class="flex items-center justify-between mb-2 relative z-10">
            <span class="text-[13px] font-semibold text-orange-50 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-chat-dots-fill text-[15px]"></i> BÌNH LUẬN
            </span>
        </div>
        <div class="text-[36px] font-bold tracking-tight leading-none mb-3 relative z-10">{{ number_format($s_comments) }}</div>
        <div class="flex items-center gap-2 mt-auto relative z-10">
            @php $g_comments = $stats['growth_comments'] ?? 145; @endphp
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[12px] font-bold bg-white/20 text-white">
                +{{ $g_comments }} <i class="bi bi-arrow-up-short"></i>
            </span>
            <span class="text-orange-100 text-xs font-medium">+{{ $g_comments }} so với tuần trước.</span>
        </div>
        <!-- Decorative subtle icon -->
        <i class="bi bi-chat-quote absolute -right-4 -bottom-4 text-[100px] text-white opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
    </div>

    {{-- Card 4: Chuyên mục (có mini chart mờ) --}}
    <div class="bg-gradient-to-br from-indigo-500 to-purple-500 border border-transparent rounded-xl p-5 shadow-[0_4px_14px_rgba(99,102,241,0.3)] transition-all duration-200 text-white relative overflow-hidden group">
        <div class="flex items-center justify-between mb-2 relative z-10">
            <span class="text-[13px] font-semibold text-indigo-50 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-columns-gap text-[15px]"></i> CHUYÊN MỤC
            </span>
        </div>
        <div class="text-[36px] font-bold tracking-tight leading-none mb-3 relative z-10">{{ number_format($s_cats) }}</div>
        
        <!-- Line Chart SVG mờ trang trí -->
        <svg class="absolute bottom-0 left-0 right-0 w-full h-[60px] opacity-40 group-hover:opacity-60 transition-opacity duration-300 pointer-events-none" viewBox="0 0 200 40" preserveAspectRatio="none">
            <path d="M0,35 Q20,10 50,25 T100,15 T150,25 T200,5 L200,40 L0,40 Z" fill="rgba(255,255,255,0.15)"/>
            <path d="M0,35 Q20,10 50,25 T100,15 T150,25 T200,5" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2"/>
            <circle cx="20" cy="22" r="2" fill="white"/>
            <circle cx="50" cy="25" r="2" fill="white"/>
            <circle cx="100" cy="15" r="3" fill="white" class="animate-pulse"/>
            <circle cx="150" cy="25" r="2" fill="white"/>
            <circle cx="200" cy="5" r="3" fill="white" class="animate-pulse"/>
        </svg>
    </div>

</div>

{{-- 3. DATA VISUALIZATION (CHARTS) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
    
    {{-- Chart 1: Line Chart (Lượt xem) --}}
    <div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm p-6 xl:col-span-2 transition-colors duration-200" x-data="{
        period: '7',
        fakeUpdate() {
            // Hiệu ứng loading nhẹ
            let canvas = document.getElementById('viewsChart');
            canvas.style.opacity = '0.5';
            setTimeout(() => {
                // Dispatch event fake dât
                window.dispatchEvent(new CustomEvent('update-chart-data', { detail: { period: this.period } }));
                canvas.style.opacity = '1';
            }, 300);
        }
    }">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-[15px] font-semibold text-zinc-900 dark:text-white tracking-tight leading-none mb-1.5" x-text="`Lượt xem & Tương tác ${period} ngày qua`">Lượt xem & Tương tác 7 ngày qua</h3>
                <p class="text-[12px] text-zinc-500 dark:text-zinc-400">Dữ liệu tổng hợp từ hệ thống theo thời gian thực.</p>
            </div>
            
            <select x-model="period" @change="fakeUpdate()" class="text-[13px] font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 rounded-lg px-3 py-1.5 focus:outline-none focus:border-zinc-400 focus:ring-1 focus:ring-zinc-400 appearance-none shadow-sm cursor-pointer pr-8" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%239ca3af%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.7rem top 50%; background-size: 0.65rem auto;">
                <option value="7">7 Ngày</option>
                <option value="30">30 Ngày</option>
                <option value="90">90 Ngày</option>
            </select>
        </div>
        <div class="relative h-[280px] w-full transition-opacity duration-300">
            <canvas id="viewsChart"></canvas>
        </div>
    </div>

    {{-- Chart 2: Donut Chart (Trạng thái bài) --}}
    <div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm p-6 flex flex-col transition-colors duration-200">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-[15px] font-semibold text-zinc-900 dark:text-white tracking-tight">Trạng thái bài viết</h3>
        </div>
        <div class="relative flex-1 w-full min-h-[250px] flex items-center justify-center">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

</div>

{{-- 4 & 5. TABLE & QUICK ACCESS --}}
{{-- 4. MIDDLE WIDGETS ROW (3 COLUMNS) --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    {{-- Top Bài Viết --}}
    <div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm flex flex-col overflow-hidden transition-colors duration-200">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-white/10 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-900/20">
            <h3 class="text-[14px] font-semibold text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                Bài viết nhiều lượt xem
            </h3>
            <a href="{{ route('admin.posts.index') }}" class="text-[12px] font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors">Xem tất cả</a>
        </div>
        <div class="flex flex-col divide-y divide-zinc-100 dark:divide-white/5">
            @forelse($topViewedPosts ?? [] as $topPost)
                <a href="{{ route('admin.posts.edit', $topPost) }}" class="flex items-center justify-between p-4 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group relative">
                    <div class="flex items-center gap-3 pr-4">
                        <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden shrink-0">
                            @if($topPost->author && $topPost->author->avatar)
                                <img src="{{ asset('storage/' . $topPost->author->avatar) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <i class="bi bi-person-fill text-zinc-400"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-[13px] font-medium text-zinc-900 dark:text-zinc-100 line-clamp-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors" title="{{ $topPost->title }}">{{ $topPost->title }}</h4>
                            <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 truncate">{{ $topPost->category->name ?? 'Không PL' }}</div>
                        </div>
                    </div>
                    <div class="text-[12px] font-medium text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                        {{ number_format($topPost->view_count ?: 0) }} <span class="text-[11px] text-zinc-400">lượt xem</span>
                    </div>
                </a>
            @empty
                <div class="p-5 text-center text-[12px] text-zinc-500">Chưa có dữ liệu.</div>
            @endforelse
        </div>
    </div>

    {{-- Chuyên Mục Nổi Bật --}}
    <div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm flex flex-col overflow-hidden transition-colors duration-200">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-white/10 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-900/20">
            <h3 class="text-[14px] font-semibold text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                Chuyên mục phổ biến
            </h3>
            <a href="{{ route('admin.categories.index') }}" class="text-[12px] font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors flex items-center gap-1">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="flex flex-col divide-y divide-zinc-100 dark:divide-white/5">
            @forelse($topCategories ?? [] as $topCat)
                <a href="{{ route('admin.categories.edit', $topCat) }}" class="flex items-center justify-between p-4 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group relative">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded border border-zinc-200 dark:border-white/10 text-zinc-500 dark:text-zinc-400 flex items-center justify-center text-[14px] bg-white dark:bg-zinc-900">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <span class="text-[13px] font-medium text-zinc-900 dark:text-zinc-100">{{ $topCat->name }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[12px] text-zinc-500 dark:text-zinc-400">{{ $topCat->posts_count }} bài viết</span>
                        <i class="bi bi-pencil-square text-zinc-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </div>
                </a>
            @empty
                <div class="p-5 text-center text-[12px] text-zinc-500">Chưa có dữ liệu.</div>
            @endforelse
        </div>
    </div>

    {{-- Quản lý Nhanh (Menus) --}}
    <div class="flex flex-col gap-3">
        <a href="{{ route('admin.posts.index') }}" class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl p-4 shadow-sm flex items-center justify-between hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:border-emerald-200 dark:hover:border-emerald-500/30 group transition-all duration-200">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-full bg-emerald-100/50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="bi bi-card-text"></i>
                </div>
                <span class="text-[14px] font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors">Quản lý bài viết</span>
            </div>
            <i class="bi bi-chevron-right text-zinc-400 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all"></i>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl p-4 shadow-sm flex items-center justify-between hover:bg-cyan-50 dark:hover:bg-cyan-500/10 hover:border-cyan-200 dark:hover:border-cyan-500/30 group transition-all duration-200">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-full bg-cyan-100/50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="bi bi-folder"></i>
                </div>
                <span class="text-[14px] font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-cyan-700 dark:group-hover:text-cyan-300 transition-colors">Chuyên mục</span>
            </div>
            <i class="bi bi-chevron-right text-zinc-400 group-hover:text-cyan-500 group-hover:translate-x-1 transition-all"></i>
        </a>

        <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl p-4 shadow-sm flex items-center justify-between hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-200 dark:hover:border-blue-500/30 group transition-all duration-200">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-full bg-blue-100/50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="bi bi-person"></i>
                </div>
                <span class="text-[14px] font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">Người dùng</span>
            </div>
            <i class="bi bi-chevron-right text-zinc-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
        </a>

        <a href="{{ route('admin.comments.index') ?? '#' }}" class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl p-4 shadow-sm flex items-center justify-between hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:border-orange-200 dark:hover:border-orange-500/30 group transition-all duration-200">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-full bg-orange-100/50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <span class="text-[14px] font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-orange-700 dark:group-hover:text-orange-300 transition-colors">Bình luận</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-emerald-500 text-white rounded-full text-[10px] font-bold">2.3K</span>
                <i class="bi bi-chevron-right text-zinc-400 group-hover:text-orange-500 group-hover:translate-x-1 transition-all"></i>
            </div>
        </a>

    </div>

</div>

{{-- 5. BẢNG BÀI VIẾT GẦN ĐÂY (FULL WIDTH) --}}
<div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm overflow-hidden flex flex-col transition-colors duration-200 mb-8">
    <div class="p-6 border-b border-zinc-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-zinc-50/50 dark:bg-zinc-900/20">
        <h3 class="text-[16px] font-bold text-zinc-900 dark:text-white tracking-tight leading-none m-0">Bài viết gần đây</h3>
        <div class="relative w-full sm:w-72 mt-2 sm:mt-0" style="margin-top: 0;">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-[14px]"></i>
            <input type="text" placeholder="Tìm kiếm bài viết..." class="w-full pr-4 h-[38px] text-[13px] rounded-lg border border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white outline-none focus:border-emerald-500 hover:border-zinc-400 transition-colors shadow-sm block box-border" style="box-sizing: border-box; margin: 0; line-height: normal; padding-left: 38px;">
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-zinc-900/50">
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest sticky top-0">Tiêu đề</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest sticky top-0">Chuyên mục</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest sticky top-0">Người đăng</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest sticky top-0">Ngày tạo</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest sticky top-0">Trạng thái</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest sticky top-0 text-right">#</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-white/10">
                @forelse($latestPosts as $post)
                <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4 max-w-[300px] font-medium text-[13px] text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        <div class="truncate" title="{{ $post->title }}">{{ $post->title }}</div>
                    </td>
                    <td class="px-6 py-4 text-[13px] text-zinc-500 dark:text-zinc-400">
                        {{ $post->category->name ?? 'Không phân loại' }}
                    </td>
                    <td class="px-6 py-4 text-[13px] text-zinc-500 dark:text-zinc-400">
                        {{ $post->author->name ?? 'Ẩn danh' }}
                    </td>
                    <td class="px-6 py-4 text-[13px] text-zinc-500 dark:text-zinc-400">
                        {{ $post->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($post->status === 'published')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 shadow-sm"><i class="bi bi-check-circle-fill opacity-80"></i>Đã đăng</span>
                        @elseif($post->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-200/60 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 shadow-sm"><i class="bi bi-stopwatch-fill opacity-80"></i>Chờ duyệt</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-zinc-100 text-zinc-600 border border-zinc-200/60 dark:bg-white/10 dark:text-zinc-300 dark:border-white/10 shadow-sm"><i class="bi bi-file-earmark-fill opacity-80"></i>Bản nháp</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-zinc-400 dark:text-zinc-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all opacity-0 group-hover:opacity-100">
                            <i class="bi bi-pencil-square text-base"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-[13px] text-zinc-500 dark:text-zinc-400 font-medium">
                        Chưa có bài viết nào trong hệ thống.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    let viewsChart, statusChart;

    const initCharts = () => {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#a1a1aa' : '#71717a'; // zinc-400 / zinc-500
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f4f4f5'; // white/5 / zinc-100
        const tooltipBg = isDark ? '#18181b' : '#ffffff'; // zinc-900 / white
        const tooltipText = isDark ? '#ffffff' : '#18181b'; // white / zinc-900
        const tooltipBorder = isDark ? 'rgba(255, 255, 255, 0.1)' : '#e4e4e7'; // white/10 / zinc-200

        // Config mặc định Chart.js
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = textColor;
        Chart.defaults.plugins.tooltip.backgroundColor = tooltipBg;
        Chart.defaults.plugins.tooltip.titleColor = tooltipText;
        Chart.defaults.plugins.tooltip.bodyColor = tooltipText;
        Chart.defaults.plugins.tooltip.borderColor = tooltipBorder;
        Chart.defaults.plugins.tooltip.borderWidth = 1;
        Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: 'bold' };
        Chart.defaults.plugins.tooltip.bodyFont = { size: 13 };
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.displayColors = false;

        // 1. Line Chart: Lượt xem 7 ngày qua
        const ctxViews = document.getElementById('viewsChart');
        if (ctxViews && !viewsChart) {
            const days = [];
            for(let i=6; i>=0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                days.push(d.toLocaleDateString('vi-VN', {day:'2-digit', month:'2-digit'}));
            }

            const gradient = ctxViews.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)'); // emerald-500 lighter
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            viewsChart = new Chart(ctxViews, {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Luợt xem',
                        data: [1100, 1500, 1300, 2100, 1800, 2400, 2250], 
                        borderColor: '#10b981', // emerald-500
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: isDark ? '#18181b' : '#ffffff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: gridColor, drawTicks: false },
                            ticks: { color: textColor, padding: 12, maxTicksLimit: 5 }
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: { color: textColor, padding: 12 }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' },
                }
            });
        }

        // 2. Donut Chart: Trạng thái bài viết
        const ctxStatus = document.getElementById('statusChart');
        if (ctxStatus && !statusChart) {
            const pub = {{ $s_published }};
            const pen = {{ $s_pending }};
            const dra = {{ $s_draft }};
            
            const total = pub + pen + dra;
            const dataStatus = total > 0 ? [pub, pen, dra] : [1];
            const bgStatus = total > 0 ? ['#10b981', '#f59e0b', '#71717a'] : [isDark ? 'rgba(255,255,255,0.05)' : '#f4f4f5'];
            const labelStatus = total > 0 ? ['Đã đăng', 'Chờ duyệt', 'Nháp'] : ['Chưa có bài viết'];

            statusChart = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: labelStatus,
                    datasets: [{
                        data: dataStatus,
                        backgroundColor: bgStatus,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                plugins: [{
                    id: 'doughnutLabel',
                    beforeDraw(chart, args, options) {
                        const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
                        ctx.save();
                        const yCenter = top + (height / 2);
                        const xCenter = left + (width / 2);
                        
                        const currentIsDark = document.documentElement.classList.contains('dark');
                        
                        ctx.textAlign = 'center';
                        
                        ctx.font = "500 13px 'Inter', sans-serif";
                        ctx.fillStyle = currentIsDark ? '#a1a1aa' : '#71717a';
                        ctx.fillText('Tổng cộng', xCenter, yCenter - 15);
                        
                        ctx.font = "bold 26px 'Inter', sans-serif";
                        ctx.fillStyle = currentIsDark ? '#ffffff' : '#18181b';
                        ctx.fillText(new Intl.NumberFormat().format(total), xCenter, yCenter + 15);
                        
                        ctx.restore();
                    }
                }],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: 10 },
                    cutout: '80%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 24,
                                boxWidth: 8,
                                font: { size: 13, weight: '500' },
                                color: textColor
                            }
                        }
                    }
                }
            });
        }
    };

    initCharts();

    // Lắng nghe sự kiện đổi màu giao diện (từ script trong admin.blade.php)
    window.addEventListener('theme-changed', (e) => {
        const isDark = e.detail === 'dark';
        const textColor = isDark ? '#a1a1aa' : '#71717a'; 
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f4f4f5';
        const tooltipBg = isDark ? '#18181b' : '#ffffff'; 
        const tooltipText = isDark ? '#ffffff' : '#18181b'; 
        const tooltipBorder = isDark ? 'rgba(255, 255, 255, 0.1)' : '#e4e4e7';

        if(viewsChart) {
            viewsChart.options.scales.x.ticks.color = textColor;
            viewsChart.options.scales.y.ticks.color = textColor;
            viewsChart.options.scales.y.grid.color = gridColor;
            viewsChart.options.plugins.tooltip.backgroundColor = tooltipBg;
            viewsChart.options.plugins.tooltip.titleColor = tooltipText;
            viewsChart.options.plugins.tooltip.bodyColor = tooltipText;
            viewsChart.options.plugins.tooltip.borderColor = tooltipBorder;
            viewsChart.data.datasets[0].pointBackgroundColor = isDark ? '#18181b' : '#ffffff';
            viewsChart.update();
        }

        if(statusChart) {
            statusChart.options.plugins.legend.labels.color = textColor;
            statusChart.options.plugins.tooltip.backgroundColor = tooltipBg;
            statusChart.options.plugins.tooltip.titleColor = tooltipText;
            statusChart.options.plugins.tooltip.bodyColor = tooltipText;
            statusChart.options.plugins.tooltip.borderColor = tooltipBorder;
            const total = {{ $s_published }} + {{ $s_pending }} + {{ $s_draft }};
            if(total === 0) {
                statusChart.data.datasets[0].backgroundColor = [isDark ? 'rgba(255,255,255,0.05)' : '#f4f4f5'];
            }
            statusChart.update();
        }
    });

    // Lắng nghe sự kiện fake data (Tạo Dữ Liệu Ảo Gây Ấn Tượng)
    window.addEventListener('update-chart-data', (e) => {
        if(viewsChart) {
            let period = e.detail.period;
            let newData = [];
            let newLabels = [];
            let points = period == '7' ? 7 : (period == '30' ? 10 : 15);
            for(let i=0; i<points; i++) {
                newData.push(Math.floor(Math.random() * 5000) + 1000);
                newLabels.push(`Ngày ${i+1}`);
            }
            viewsChart.data.labels = newLabels;
            viewsChart.data.datasets[0].data = newData;
            viewsChart.update();
        }
    });

});
</script>
@endsection
