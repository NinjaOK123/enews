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
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Tin tức AGU — Cổng Quản Trị
        </h2>
        <p class="text-[15px] text-slate-500 mt-1.5 font-medium">
            Hôm nay là {{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}.
        </p>
    </div>
    
    <a href="{{ route('contributor.posts.create') }}"
       class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow hover:-translate-y-0.5 transition-all duration-200 shrink-0 outline-none">
        <i class="bi bi-plus-lg"></i>
        <span>Viết bài mới</span>
    </a>
</div>

{{-- 2. STATS CARDS (6 CARDS) --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-10">
    
    {{-- Card 1: Tổng bài viết --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 group cursor-default">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tổng bài viết</span>
            <div class="w-10 h-10 rounded-full bg-emerald-50/50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100/50 transition-colors">
                <i class="bi bi-file-text text-lg"></i>
            </div>
        </div>
        <div class="text-[28px] font-black text-slate-800 leading-none">{{ number_format($s_posts) }}</div>
    </div>

    {{-- Card 2: Chờ duyệt --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 group cursor-default">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Chờ duyệt</span>
            <div class="w-10 h-10 rounded-full bg-amber-50/50 text-amber-500 flex items-center justify-center group-hover:bg-amber-100/50 transition-colors">
                <i class="bi bi-hourglass-split text-lg"></i>
            </div>
        </div>
        <div class="text-[28px] font-black text-slate-800 leading-none">{{ number_format($s_pending) }}</div>
    </div>

    {{-- Card 3: Người dùng --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 group cursor-default">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Người dùng</span>
            <div class="w-10 h-10 rounded-full bg-blue-50/50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100/50 transition-colors">
                <i class="bi bi-people text-lg"></i>
            </div>
        </div>
        <div class="text-[28px] font-black text-slate-800 leading-none">{{ number_format($s_users) }}</div>
    </div>

    {{-- Card 4: Bình luận --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 group cursor-default">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bình luận</span>
            <div class="w-10 h-10 rounded-full bg-indigo-50/50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-100/50 transition-colors">
                <i class="bi bi-chat-dots text-lg"></i>
            </div>
        </div>
        <div class="text-[28px] font-black text-slate-800 leading-none">{{ number_format($s_comments) }}</div>
    </div>

    {{-- Card 5: Lượt xem --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 group cursor-default">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Lượt xem</span>
            <div class="w-10 h-10 rounded-full bg-red-50/50 text-red-500 flex items-center justify-center group-hover:bg-red-100/50 transition-colors">
                <i class="bi bi-eye text-lg"></i>
            </div>
        </div>
        <div class="text-[28px] font-black text-slate-800 leading-none">{{ number_format($s_views) }}</div>
    </div>

    {{-- Card 6: Chuyên mục --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 group cursor-default">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Chuyên mục</span>
            <div class="w-10 h-10 rounded-full bg-cyan-50/50 text-cyan-600 flex items-center justify-center group-hover:bg-cyan-100/50 transition-colors">
                <i class="bi bi-tags text-lg"></i>
            </div>
        </div>
        <div class="text-[28px] font-black text-slate-800 leading-none">{{ number_format($s_cats) }}</div>
    </div>

</div>

{{-- 3. DATA VISUALIZATION (CHARTS) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
    
    {{-- Chart 1: Line Chart (Lượt xem) --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.04)] p-8 xl:col-span-2 hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-[17px] font-bold text-slate-800">Lượt xem & Tương tác 7 ngày qua</h3>
        </div>
        <div class="relative h-[280px] w-full">
            <canvas id="viewsChart"></canvas>
        </div>
    </div>

    {{-- Chart 2: Donut Chart (Trạng thái bài) --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.04)] p-8 flex flex-col hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-[17px] font-bold text-slate-800">Trạng thái bài viết</h3>
        </div>
        <div class="relative flex-1 w-full min-h-[250px] flex items-center justify-center">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

</div>

{{-- 4 & 5. TABLE & QUICK ACCESS --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- Bảng: Bài viết gần đây --}}
    <div class="xl:col-span-9 bg-white border border-slate-100 rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-300">
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-white">
            <h3 class="text-[17px] font-bold text-slate-800">Bài viết gần đây</h3>
            <a href="{{ route('admin.posts.index') }}" class="text-[13px] font-semibold text-emerald-600 hover:text-emerald-700 transition-colors flex items-center gap-1">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest sticky top-0">Tiêu đề</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest sticky top-0">Chuyên mục</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest sticky top-0">Người đăng</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest sticky top-0">Ngày tạo</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest sticky top-0">Trạng thái</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest sticky top-0 text-right">#</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($latestPosts as $post)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-8 py-5 max-w-[250px] font-semibold text-[14px] text-slate-800 group-hover:text-emerald-700 transition-colors">
                            <div class="truncate" title="{{ $post->title }}">{{ $post->title }}</div>
                        </td>
                        <td class="px-8 py-5 text-[14px] text-slate-500 font-medium">
                            {{ $post->category->name ?? 'Không phân loại' }}
                        </td>
                        <td class="px-8 py-5 text-[14px] text-slate-500">
                            {{ $post->author->name ?? 'Ẩn danh' }}
                        </td>
                        <td class="px-8 py-5 text-[14px] text-slate-500">
                            {{ $post->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-8 py-5">
                            @if($post->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700">Đã đăng</span>
                            @elseif($post->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700">Chờ duyệt</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">Bản nháp</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-slate-400 hover:text-emerald-600 transition-colors">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-10 text-center text-slate-500 font-medium">
                            Chưa có bài viết nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Access Cards --}}
    <div class="xl:col-span-3 flex flex-col gap-4">
        
        <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-4 p-5 bg-white border border-slate-100 rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md hover:border-emerald-200 transition-all duration-200 no-underline group outline-none">
            <div class="w-12 h-12 rounded-xl bg-emerald-50/80 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors shrink-0">
                <i class="bi bi-file-text text-xl"></i>
            </div>
            <div>
                <h4 class="text-[15px] font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">Quản lý bài viết</h4>
                <p class="text-[13px] text-slate-500 mt-0.5">Duyệt, đăng, xóa bài</p>
            </div>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-4 p-5 bg-white border border-slate-100 rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md hover:border-cyan-200 transition-all duration-200 no-underline group outline-none">
            <div class="w-12 h-12 rounded-xl bg-cyan-50/80 flex items-center justify-center text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition-colors shrink-0">
                <i class="bi bi-tags text-xl"></i>
            </div>
            <div>
                <h4 class="text-[15px] font-bold text-slate-900 group-hover:text-cyan-700 transition-colors">Chuyên mục</h4>
                <p class="text-[13px] text-slate-500 mt-0.5">Danh mục bài viết</p>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-4 p-5 bg-white border border-slate-100 rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md hover:border-blue-200 transition-all duration-200 no-underline group outline-none">
            <div class="w-12 h-12 rounded-xl bg-blue-50/80 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors shrink-0">
                <i class="bi bi-people text-xl"></i>
            </div>
            <div>
                <h4 class="text-[15px] font-bold text-slate-900 group-hover:text-blue-700 transition-colors">Người dùng</h4>
                <p class="text-[13px] text-slate-500 mt-0.5">Phân quyền, tài khoản</p>
            </div>
        </a>

        <a href="{{ route('admin.media.index') }}" class="flex items-center gap-4 p-5 bg-white border border-slate-100 rounded-2xl shadow-[0_1px_2px_rgba(0,0,0,0.04)] hover:-translate-y-1 hover:shadow-md hover:border-pink-200 transition-all duration-200 no-underline group outline-none">
            <div class="w-12 h-12 rounded-xl bg-pink-50/80 flex items-center justify-center text-pink-600 group-hover:bg-pink-600 group-hover:text-white transition-colors shrink-0">
                <i class="bi bi-images text-xl"></i>
            </div>
            <div>
                <h4 class="text-[15px] font-bold text-slate-900 group-hover:text-pink-700 transition-colors">Thư viện Media</h4>
                <p class="text-[13px] text-slate-500 mt-0.5">Quản lý hình ảnh</p>
            </div>
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Config mặc định Chart.js cho Clean UI chuẩn Linear
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8'; // slate-400
    Chart.defaults.scale.grid.color = '#f8fafc'; // slate-50
    Chart.defaults.plugins.tooltip.backgroundColor = '#0f172a'; // slate-950
    Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: 'bold' };
    Chart.defaults.plugins.tooltip.bodyFont = { size: 13 };
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.displayColors = false;
    
    // 1. Line Chart: Lượt xem 7 ngày qua (Dữ liệu giả lập demo)
    const ctxViews = document.getElementById('viewsChart');
    if (ctxViews) {
        const days = [];
        for(let i=6; i>=0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            days.push(d.toLocaleDateString('vi-VN', {day:'2-digit', month:'2-digit'}));
        }

        const gradient = ctxViews.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)'); // emerald-500 lighter
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctxViews, {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Luợt xem',
                    data: [1100, 1500, 1300, 2100, 1800, 2400, 2250], 
                    borderColor: '#10b981', // emerald-500
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 0, // Ẩn point khi bình thường (Chuẩn Vercel)
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Làm đường line cong mượt
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: '#f1f5f9', // Rất mờ
                            drawTicks: false
                        },
                        ticks: { padding: 12, maxTicksLimit: 5 }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false }, // Ẩn luôn grid dọc
                        ticks: { padding: 12 }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    }

    // 2. Donut Chart: Trạng thái bài viết
    const ctxStatus = document.getElementById('statusChart');
    if (ctxStatus) {
        const pub = {{ $s_published }};
        const pen = {{ $s_pending }};
        const dra = {{ $s_draft }};
        
        const total = pub + pen + dra;
        const dataStatus = total > 0 ? [pub, pen, dra] : [1];
        const bgStatus = total > 0 ? ['#10b981', '#f59e0b', '#cbd5e1'] : ['#f8fafc'];
        const labelStatus = total > 0 ? ['Đã đăng', 'Chờ duyệt', 'Nháp'] : ['Chưa có bài viết'];

        new Chart(ctxStatus, {
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
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10 // Để chart không bị sát mép canvas
                },
                cutout: '80%', // Mỏng đi nhiều tạo cảm giác tinh tế
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 24,
                            boxWidth: 8,
                            font: { size: 13, weight: '500' },
                            color: '#475569'
                        }
                    }
                }
            }
        });
    }

});
</script>
@endsection
