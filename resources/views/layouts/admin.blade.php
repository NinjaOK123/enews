<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'AGU E-News') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --agu-primary: #198754; /* xanh lá AGU */
            --agu-accent: #FF6600; /* cam accent */
            --bg-light: #f4f6f9;
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-light);
            color: #333;
            overflow-x: hidden;
        }
        
        /* ── Sidebar ── */
        .admin-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 1040;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--agu-primary);
            text-decoration: none;
        }
        .sidebar-menu {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(25, 135, 84, 0.08);
            color: var(--agu-primary);
            border-right: 4px solid var(--agu-primary);
        }
        .sidebar-menu a i {
            font-size: 1.15rem;
        }

        /* ── Main Content ── */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        /* ── Top Navbar ── */
        .admin-navbar {
            background: #fff;
            height: 70px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            z-index: 1030;
        }

        /* ── Toggle Button with animated hamburger ── */
        .toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 38px;
            height: 38px;
            transition: background 0.2s;
        }
        .toggle-btn:hover { background: rgba(0,0,0,0.06); }
        .toggle-btn span {
            display: block;
            height: 2px;
            background: #555;
            border-radius: 2px;
            transition: transform 0.3s ease, opacity 0.3s ease, width 0.3s ease;
            transform-origin: center;
        }
        .toggle-btn span:nth-child(1) { width: 22px; }
        .toggle-btn span:nth-child(2) { width: 18px; }
        .toggle-btn span:nth-child(3) { width: 22px; }
        /* Opened state — X icon */
        .sidebar-open .toggle-btn span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
            width: 22px;
        }
        .sidebar-open .toggle-btn span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }
        .sidebar-open .toggle-btn span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
            width: 22px;
        }

        .admin-content-wrapper {
            padding: 24px;
            flex: 1;
        }

        /* Desktop: sidebar collapsible */
        body.sidebar-collapsed .admin-sidebar {
            transform: translateX(-100%);
        }
        body.sidebar-collapsed .admin-main {
            margin-left: 0;
        }

        /* Responsive — mobile */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            body.sidebar-open .admin-sidebar {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0;
                width: 100vw; height: 100vh;
                background: rgba(0,0,0,0.45);
                z-index: 1035;
                transition: opacity 0.3s;
            }
            body.sidebar-open .sidebar-overlay {
                display: block;
            }
        }

        @yield('styles')
    </style>
</head>
<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--agu-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="bi bi-newspaper"></i>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">E-News Admin</a>
        </div>
        <div class="sidebar-menu">
            @php 
                $role = auth()->user()->role ?? 'reader'; 
                $dashboardRoute = match($role) {
                    'admin' => route('admin.dashboard'),
                    'editor' => route('editor.dashboard'),
                    'contributor' => route('contributor.dashboard'),
                    default => route('home')
                };
            @endphp

            @if(in_array($role, ['admin', 'editor', 'contributor']))
            <a href="{{ $dashboardRoute }}" class="{{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            @endif

            @if(in_array($role, ['admin', 'editor']))
            <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Bài viết
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Chuyên mục
            </a>
            @endif

            @if($role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Người dùng
            </a>
            @endif

            @if(in_array($role, ['admin', 'editor']))
            <a href="{{ route('admin.media.index') }}" class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                <i class="bi bi-images"></i> Media (Thư viện)
            </a>
            @endif

            @if($role === 'admin')
            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Báo cáo & Thống kê
            </a>
            @endif

            @if($role === 'admin')
            <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Thông báo
            </a>
            @endif

            @if($role === 'admin')
            @php $pendingComments = \App\Models\Comment::where('is_approved', false)->count(); @endphp
            <a href="{{ route('admin.comments.index') }}" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> Bình luận
                @if($pendingComments > 0)
                    <span class="badge bg-danger ms-auto rounded-pill" style="font-size:.65rem;">{{ $pendingComments }}</span>
                @endif
            </a>
            @endif

            @if($role === 'admin')
            @php $pendingCTV = \App\Models\ContributorRequest::where('status', 'pending')->count(); @endphp
            <a href="{{ route('admin.contributor.index') }}" class="{{ request()->routeIs('admin.contributor.*') ? 'active' : '' }}">
                <i class="bi bi-person-check"></i> Cộng tác viên
                @if($pendingCTV > 0)
                    <span class="badge bg-warning text-dark ms-auto rounded-pill" style="font-size:.65rem;">{{ $pendingCTV }}</span>
                @endif
            </a>
            @endif
        </div>
    </aside>

    <!-- Main wrapper -->
    <main class="admin-main">
        <!-- Topbar -->
        <nav class="admin-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="toggle-btn" id="sidebarToggle" title="Ẩn/hiện menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="fw-bold fs-5 d-none d-sm-block">@yield('title')</div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-success rounded-pill fw-semibold shadow-sm px-3 hover-lift">
                    <i class="bi bi-house-door"></i> <span class="d-none d-sm-inline">Trang chủ</span>
                </a>
                
                <div class="dropdown">
                    <button class="btn btn-light rounded-pill dropdown-toggle d-flex align-items-center gap-2 border-0 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--agu-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="d-none d-sm-inline fw-semibold text-dark">{{ auth()->user()->name ?? 'Admin' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; min-width: 210px;">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('home') }}">
                                <i class="bi bi-house me-2" style="color:#555;"></i>Trang chủ
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile') }}">
                                <i class="bi bi-person-circle me-2" style="color:#555;"></i>Thông tin cá nhân
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ $dashboardRoute }}">
                                <i class="bi bi-speedometer2 me-2" style="color:#555;"></i>Dashboard
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger fw-semibold">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="admin-content-wrapper">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const overlay   = document.getElementById('sidebarOverlay');
            const body      = document.body;
            const isMobile  = () => window.innerWidth < 992;

            // Restore desktop state from localStorage
            if (!isMobile() && localStorage.getItem('sidebarCollapsed') === '1') {
                body.classList.add('sidebar-collapsed');
            }

            function toggleSidebar() {
                if (isMobile()) {
                    body.classList.toggle('sidebar-open');
                } else {
                    body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed',
                        body.classList.contains('sidebar-collapsed') ? '1' : '0');
                }
            }

            toggleBtn.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', function() {
                body.classList.remove('sidebar-open');
            });

            window.addEventListener('resize', function() {
                if (!isMobile()) {
                    body.classList.remove('sidebar-open');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
