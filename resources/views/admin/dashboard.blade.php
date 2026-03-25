@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
/* ── 3D Card Base ── */
.card-3d {
    transform-style: preserve-3d;
    transition: transform 0.35s cubic-bezier(.23,1,.32,1), box-shadow 0.35s ease;
}
.card-3d:hover {
    transform: translateY(-6px) rotateX(4deg);
    box-shadow: 0 20px 40px rgba(0,0,0,0.14);
}

/* ── Stat Card Gradient ── */
.stat-card {
    border-radius: 18px;
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: transform 0.35s cubic-bezier(.23,1,.32,1), box-shadow 0.35s ease;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: -20px; right: -20px;
    width: 100px; height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
}
.stat-card::after {
    content: '';
    position: absolute;
    bottom: -30px; right: 20px;
    width: 70px; height: 70px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.02) rotateX(3deg);
    box-shadow: 0 24px 48px rgba(0,0,0,0.18);
}
.stat-card .stat-icon {
    font-size: 2.2rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}
.stat-card .stat-num {
    font-size: 2rem;
    font-weight: 900;
    line-height: 1;
    position: relative;
    z-index: 1;
    letter-spacing: -0.02em;
}
.stat-card .stat-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    opacity: 0.85;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

/* ── Quick Access Card ── */
.qa-card {
    background: #fff;
    border-radius: 16px;
    border: 1.5px solid transparent;
    padding: 24px 16px;
    text-align: center;
    text-decoration: none;
    color: #1e293b;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.04);
    transition: all 0.3s cubic-bezier(.23,1,.32,1);
    transform-style: preserve-3d;
    position: relative;
}
.qa-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(255,255,255,0.6) 0%, rgba(255,255,255,0) 60%);
    pointer-events: none;
}
.qa-card:hover {
    transform: translateY(-8px) rotateX(5deg) rotateY(-2deg);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    border-color: rgba(0,0,0,0.06);
    color: #1e293b;
    text-decoration: none;
}
.qa-card .qa-icon {
    font-size: 2rem;
    transition: transform 0.3s ease;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
}
.qa-card:hover .qa-icon {
    transform: scale(1.2) translateZ(10px);
}
.qa-card .qa-title {
    font-weight: 700;
    font-size: 0.9rem;
}
.qa-card .qa-desc {
    font-size: 0.73rem;
    color: #94a3b8;
}
</style>
@endsection

@section('content')
@php
    $user = auth()->user();
    $s_posts    = $stats['total_posts']      ?? 0;
    $s_pending  = $stats['pending_posts']    ?? 0;
    $s_users    = $stats['total_users']      ?? 0;
    $s_comments = $stats['total_comments']   ?? 0;
    $s_views    = $stats['total_views']      ?? 0;
    $s_cats     = $stats['total_categories'] ?? 0;
@endphp

{{-- ── Welcome Header ── --}}
<div class="flex items-center justify-between mb-7">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-10 bg-gradient-to-b from-green-500 to-green-700 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-gray-900 leading-tight tracking-tight">
                Tin tức AGU — Cổng Quản Trị
            </h2>
            <p class="text-sm text-gray-400 mt-0.5">
                Xin chào, <span class="font-semibold text-gray-600">{{ $user->name ?? 'Admin' }}</span>
                &nbsp;·&nbsp;
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    {{ $user?->roleLabel() ?? 'Quản trị viên' }}
                </span>
                &nbsp;·&nbsp;
                <span>{{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}</span>
            </p>
        </div>
    </div>
    <a href="{{ route('contributor.posts.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white text-sm font-bold rounded-2xl shadow-lg shadow-green-200 transition-all duration-200 shrink-0">
        <i class="bi bi-pencil-square"></i>
        <span class="hidden sm:inline">Viết bài mới</span>
    </a>
</div>

{{-- ── 6 Stat Cards (gradient + 3D) ── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8" style="perspective:1000px;">

    {{-- Tổng bài viết --}}
    <div class="stat-card card-3d" style="background: linear-gradient(135deg,#16a34a,#15803d);">
        <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
        <div class="stat-num mt-2">{{ number_format($s_posts) }}</div>
        <div class="stat-label mt-1">Tổng bài viết</div>
    </div>

    {{-- Chờ duyệt --}}
    <div class="stat-card card-3d" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
        <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-num mt-2">{{ number_format($s_pending) }}</div>
        <div class="stat-label mt-1">Chờ duyệt</div>
    </div>

    {{-- Người dùng --}}
    <div class="stat-card card-3d" style="background: linear-gradient(135deg,#3b82f6,#2563eb);">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div class="stat-num mt-2">{{ number_format($s_users) }}</div>
        <div class="stat-label mt-1">Người dùng</div>
    </div>

    {{-- Bình luận --}}
    <div class="stat-card card-3d" style="background: linear-gradient(135deg,#8b5cf6,#7c3aed);">
        <div class="stat-icon"><i class="bi bi-chat-dots-fill"></i></div>
        <div class="stat-num mt-2">{{ number_format($s_comments) }}</div>
        <div class="stat-label mt-1">Bình luận</div>
    </div>

    {{-- Tổng lượt xem --}}
    <div class="stat-card card-3d" style="background: linear-gradient(135deg,#ef4444,#dc2626);">
        <div class="stat-icon"><i class="bi bi-eye-fill"></i></div>
        <div class="stat-num mt-2 text-lg">{{ number_format($s_views) }}</div>
        <div class="stat-label mt-1">Tổng lượt xem</div>
    </div>

    {{-- Chuyên mục --}}
    <div class="stat-card card-3d" style="background: linear-gradient(135deg,#06b6d4,#0891b2);">
        <div class="stat-icon"><i class="bi bi-grid-fill"></i></div>
        <div class="stat-num mt-2">{{ number_format($s_cats) }}</div>
        <div class="stat-label mt-1">Chuyên mục</div>
    </div>

</div>

{{-- ── Launchpad ── --}}
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
    <div class="flex items-center gap-2 mb-5">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center text-white">
            <i class="bi bi-grid-1x2-fill text-sm"></i>
        </div>
        <h3 class="text-base font-bold text-gray-800">Truy cập nhanh (Launchpad)</h3>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4" style="perspective:1200px;">

        <a href="{{ route('admin.posts.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#16a34a;"><i class="bi bi-file-text"></i></div>
            <div class="qa-title">Quản lý Bài viết</div>
            <div class="qa-desc">Duyệt, sửa, xóa tin tức</div>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#0891b2;"><i class="bi bi-tags"></i></div>
            <div class="qa-title">Chuyên mục</div>
            <div class="qa-desc">Tổ chức cây danh mục</div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#2563eb;"><i class="bi bi-people"></i></div>
            <div class="qa-title">Người dùng</div>
            <div class="qa-desc">Phân quyền, tài khoản</div>
        </a>

        <a href="{{ route('admin.media.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#db2777;"><i class="bi bi-images"></i></div>
            <div class="qa-title">Thư viện Media</div>
            <div class="qa-desc">Quản lý hình ảnh, tệp</div>
        </a>

        <a href="{{ route('admin.comments.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#ea580c;"><i class="bi bi-chat-left-text"></i></div>
            <div class="qa-title">Bình luận</div>
            <div class="qa-desc">Phê duyệt phản hồi</div>
        </a>

        <a href="{{ route('admin.reports.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#7c3aed;"><i class="bi bi-bar-chart-line"></i></div>
            <div class="qa-title">Báo cáo & Thống kê</div>
            <div class="qa-desc">Lượt xem, tương tác</div>
        </a>

        <a href="{{ route('admin.notifications.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#d97706;"><i class="bi bi-bell"></i></div>
            <div class="qa-title">Thông báo</div>
            <div class="qa-desc">Gửi thông báo hệ thống</div>
        </a>

        <a href="{{ route('admin.contributor.index') }}" class="qa-card">
            <div class="qa-icon" style="color:#059669;"><i class="bi bi-person-check"></i></div>
            <div class="qa-title">Cộng tác viên</div>
            <div class="qa-desc">Xét duyệt đăng ký</div>
        </a>

    </div>
</div>

@endsection
