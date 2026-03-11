@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    /* Card Hover Lift */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    
    /* Icon Circle */
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    /* Quick Access Card */
    .quick-access-card {
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.05);
        background: #fff;
        padding: 24px 16px;
        text-align: center;
        text-decoration: none;
        color: #333;
        display: block;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .quick-access-card .qa-icon {
        font-size: 2rem;
        margin-bottom: 12px;
        color: var(--agu-primary);
        transition: transform 0.2s;
    }
    .quick-access-card:hover .qa-icon {
        transform: scale(1.15);
    }
    .quick-access-title {
        font-weight: 700;
        font-size: 0.95rem;
    }
    .quick-access-desc {
        font-size: 0.75rem;
        color: #888;
        margin-top: 6px;
    }
    
    /* Stat Card Custom Colors */
    .bg-green-soft { background-color: #e8f5e9; color: #198754; }
    .bg-orange-soft { background-color: #fff3e0; color: #FF6600; }
    .bg-blue-soft { background-color: #e3f2fd; color: #0d6efd; }
    .bg-purple-soft { background-color: #f3e5f5; color: #6f42c1; }
    .bg-red-soft { background-color: #ffebee; color: #dc3545; }
    .bg-teal-soft { background-color: #e0f2f1; color: #20c997; }
</style>
@endsection

@section('content')

@php 
    $user = auth()->user(); 
    // Fallback data if $stats not passed (as requested "hardcode số liệu như ảnh")
    $s_posts = $stats['total_posts'] ?? 13;
    $s_pending = $stats['pending_posts'] ?? 0;
    $s_users = $stats['total_users'] ?? 8;
    $s_comments = $stats['total_comments'] ?? 0;
    $s_views = $stats['total_views'] ?? 29300;
    $s_cats = $stats['total_categories'] ?? 10;
@endphp

<!-- Welcome Header Area -->
<div class="row mb-4 align-items-center">
    <div class="col-md-7 mb-3 mb-md-0">
        <h2 class="fw-bold mb-1" style="font-weight: 900 !important; color:#111;">Tin tức AGU - Cổng Quản Trị</h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            Xin chào, <strong class="text-dark">{{ $user->name ?? 'Quản Trị Viên' }}</strong> • 
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">{{ $user ? $user->roleLabel() : 'Quản trị viên' }}</span> • 
            {{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}
        </p>
    </div>
    <div class="col-md-5 text-md-end d-flex justify-content-md-end gap-2">
        <a href="{{ route('home') }}" class="btn btn-outline-success rounded-pill fw-semibold shadow-sm px-4 hover-lift">
            <i class="bi bi-house-door-fill me-1"></i> Trang chủ
        </a>
        <a href="{{ route('contributor.posts.create') }}" class="btn btn-success rounded-pill fw-semibold shadow-sm px-4 hover-lift">
            <i class="bi bi-pencil-square me-1"></i> Viết bài mới
        </a>
    </div>
</div>

<!-- 6 Stat Cards Area -->
<div class="row g-3 mb-4">
    <!-- Card 1 -->
    <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-circle bg-green-soft">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-black text-dark lh-1 mb-1" style="font-weight:900;">{{ number_format($s_posts) }}</div>
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tổng bài viết</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-circle bg-orange-soft">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="fs-4 fw-black text-dark lh-1 mb-1" style="font-weight:900;">{{ number_format($s_pending) }}</div>
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Chờ duyệt</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-circle bg-blue-soft">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-black text-dark lh-1 mb-1" style="font-weight:900;">{{ number_format($s_users) }}</div>
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Người dùng</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-circle bg-purple-soft">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-black text-dark lh-1 mb-1" style="font-weight:900;">{{ number_format($s_comments) }}</div>
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Bình luận</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 5 -->
    <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-circle bg-red-soft">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-black text-dark lh-1 mb-1" style="font-weight:900;">{{ number_format($s_views) }}</div>
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tổng lượt xem</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 6 -->
    <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-circle bg-teal-soft">
                    <i class="bi bi-grid-fill"></i>
                </div>
                <div>
                    <div class="fs-4 fw-black text-dark lh-1 mb-1" style="font-weight:900;">{{ number_format($s_cats) }}</div>
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Chuyên mục</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Launchpad: Quick Access Grid -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4 p-md-5">
        <h5 class="fw-bold mb-4 d-flex align-items-center"><i class="bi bi-grid-1x2-fill text-success border-end border-2 border-success pe-2 me-2"></i> Truy cập nhanh (Launchpad)</h5>
        
        <div class="row g-3">
            <!-- Box 1 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.posts.index') }}" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #198754;"><i class="bi bi-file-text"></i></div>
                    <div class="quick-access-title">Quản lý Bài viết</div>
                    <div class="quick-access-desc">Duyệt, sửa, xóa tin tức</div>
                </a>
            </div>
            <!-- Box 2 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.categories.index') }}" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #0dcaf0;"><i class="bi bi-tags"></i></div>
                    <div class="quick-access-title">Chuyên mục</div>
                    <div class="quick-access-desc">Tổ chức cây danh mục</div>
                </a>
            </div>
            <!-- Box 3 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.users.index') }}" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #0d6efd;"><i class="bi bi-people"></i></div>
                    <div class="quick-access-title">Người dùng</div>
                    <div class="quick-access-desc">Phân quyền, tài khoản</div>
                </a>
            </div>
            <!-- Box 4 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.media.index') }}" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #d63384;"><i class="bi bi-images"></i></div>
                    <div class="quick-access-title">Thư viện Media</div>
                    <div class="quick-access-desc">Quản lý hình ảnh, tệp</div>
                </a>
            </div>
            <!-- Box 5 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.comments.index') }}" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #fd7e14;"><i class="bi bi-chat-left-text"></i></div>
                    <div class="quick-access-title">Bình luận</div>
                    <div class="quick-access-desc">Phê duyệt phản hồi</div>
                </a>
            </div>
            <!-- Box 6 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.reports.index') }}" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #6f42c1;"><i class="bi bi-bar-chart-line"></i></div>
                    <div class="quick-access-title">Báo cáo & Thống kê</div>
                    <div class="quick-access-desc">Lượt xem, tương tác</div>
                </a>
            </div>
            <!-- Box 7 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('admin.notifications.index') }}" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #ffc107;"><i class="bi bi-bell"></i></div>
                    <div class="quick-access-title">Thông báo</div>
                    <div class="quick-access-desc">Gửi thông báo hệ thống</div>
                </a>
            </div>
            <!-- Box 8 -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="#" class="quick-access-card hover-lift h-100">
                    <div class="qa-icon" style="color: #6c757d;"><i class="bi bi-gear"></i></div>
                    <div class="quick-access-title">Cài đặt</div>
                    <div class="quick-access-desc">Cấu hình chung website</div>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
