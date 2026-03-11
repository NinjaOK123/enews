<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="@yield('meta_description', 'Trang Tin Điện Tử - Trang báo Sinh viên Đại học An Giang')">
  <title>@yield('title', 'Trang chủ') — Trang báo Sinh viên Đại học An Giang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
  @stack('styles')
  <style>
    /* Notification Bell Ring Animation */
    @keyframes ring {
      0% { transform: rotate(0); }
      5% { transform: rotate(30deg); }
      10% { transform: rotate(-28deg); }
      15% { transform: rotate(34deg); }
      20% { transform: rotate(-32deg); }
      25% { transform: rotate(30deg); }
      30% { transform: rotate(-28deg); }
      35% { transform: rotate(26deg); }
      40% { transform: rotate(-24deg); }
      45% { transform: rotate(22deg); }
      50% { transform: rotate(-20deg); }
      55% { transform: rotate(18deg); }
      60% { transform: rotate(-16deg); }
      65% { transform: rotate(14deg); }
      70% { transform: rotate(-12deg); }
      75% { transform: rotate(10deg); }
      80% { transform: rotate(-8deg); }
      85% { transform: rotate(6deg); }
      90% { transform: rotate(-4deg); }
      95% { transform: rotate(2deg); }
      100% { transform: rotate(0); }
    }
    .action-icon.ringing .bi-bell {
      display: inline-block;
      animation: ring 2s ease infinite;
      transform-origin: top center;
    }
    
    /* Red dot badge */
    .notif-badge {
      position: absolute;
      top: 0;
      right: 0;
      width: 8px;
      height: 8px;
      background-color: #dc3545;
      border-radius: 50%;
      border: 1.5px solid #fff;
    }
  </style>
</head>
<body>
<div class="page-wrapper">

  {{-- ═══ TOP UTILITY BAR (dark green like VNExpress dark bar) ═══ --}}
  <div class="top-bar">
    <div class="top-bar-left">
      <i class="bi bi-building"></i>
      <span>Trường Đại học An Giang — VNU-HCM</span>
    </div>
    <div class="top-bar-right">
      {{-- Chọn ngôn ngữ (đặt trước ngày tháng) --}}
      <div class="top-bar-lang">
        <div id="google_translate_element"></div>
      </div>
      <i class="bi bi-calendar3"></i>
      @php
        $topBarDate = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->locale('vi');
        $topBarDateStr = ucwords($topBarDate->isoFormat('dddd')) . ', ' . $topBarDate->isoFormat('D [tháng] M, YYYY');
      @endphp
      <span>{{ $topBarDateStr }}</span>
    </div>
  </div>

  {{-- ═══ MAIN HEADER (white, logo + search + nav + login) ═══ --}}
  <header class="main-header">

    {{-- Real AGU Logo --}}
    <a href="{{ route('home') }}" class="header-brand">
      <img src="{{ asset('images/logo.png') }}"
           alt="Logo Trường Đại học An Giang"
           style="width:58px;height:58px;object-fit:contain;border-radius:50%;flex-shrink:0;">
      <div class="brand-name-block">
        <span class="uni-name">ĐẠI HỌC AN GIANG</span>
        <span class="enews-tag">E-News</span>
      </div>
    </a>

    {{-- Main navigation --}}
    <nav class="header-nav d-none d-lg-flex">
      <a href="{{ route('home') }}" class="header-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="bi bi-house"></i>
        <span>Trang chủ</span>
      </a>
      <a href="{{ route('about') }}" class="header-nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
        <i class="bi bi-info-circle"></i>
        <span>Giới thiệu</span>
      </a>
      <a href="{{ route('rules') }}" class="header-nav-item {{ request()->routeIs('rules') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i>
        <span>Quy định</span>
      </a>
      <a href="{{ route('contact') }}" class="header-nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
        <i class="bi bi-envelope"></i>
        <span>Liên hệ</span>
      </a>
      <a href="{{ route('doc-suy-ngam') }}" class="header-nav-item {{ request()->routeIs('doc-suy-ngam') ? 'active' : '' }}">
        <i class="bi bi-book"></i>
        <span>eNews - Đọc &amp; Suy ngẫm</span>
      </a>
    </nav>

    {{-- Auth section: Login button hoặc User Dropdown --}}
    <div class="header-actions">
      @auth
      {{-- ── User dropdown khi đã đăng nhập (Bootstrap 5) ── --}}
      <ul class="navbar-nav mb-0">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #333; padding-right: 0;">
            @if(auth()->user()->avatar)
              <img src="{{ filter_var(auth()->user()->avatar, FILTER_VALIDATE_URL) ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
            @else
              <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
            @endif
            <span class="fw-semibold">{{ auth()->user()->name ?? 'Người dùng' }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="position: absolute; border-radius: 8px;">
            <li><a class="dropdown-item py-2 {{ request()->routeIs('home') ? 'active' : '' }}" href="/"><i class="bi bi-house me-2" style="color: #555;"></i>Trang chủ</a></li>
            <li><a class="dropdown-item py-2 {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}"><i class="bi bi-person me-2" style="color: #555;"></i>Thông tin cá nhân</a></li>
            @if(auth()->user()->role !== 'reader')
              @php
                $dashUrl = match(auth()->user()->role) {
                  'admin'       => route('admin.dashboard'),
                  'editor'      => route('editor.dashboard'),
                  'contributor' => route('contributor.dashboard'),
                  default       => route('home'),
                };
              @endphp
              <li><a class="dropdown-item py-2" href="{{ $dashUrl }}"><i class="bi bi-speedometer2 me-2" style="color: #2a7a27;"></i>Dashboard</a></li>
            @endif
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" id="logout-form" class="m-0 p-0">
                @csrf
                <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                  <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
      @else
      {{-- ── Nút đăng nhập cho guest ── --}}
      <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" style="background-color: var(--primary, #2a7a27); border-color: var(--primary, #2a7a27);">
        <i class="bi bi-person-circle me-1"></i> Đăng nhập
      </a>
      @endauth

      {{-- ── Notification Bell (Animated) ── --}}
      <div class="nav-item dropdown ms-3">
        <a href="#" class="action-icon ringing position-relative" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo" style="display:flex; align-items:center; justify-content:center; width:36px; height:36px; color:#555; text-decoration:none; font-size:1.15rem;">
          <i class="bi bi-bell"></i>
          <span class="notif-badge"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="notifDropdown" style="width: 320px; border-radius: 12px; padding: 0; overflow: hidden; border: 1px solid rgba(0,0,0,0.08);">
          <li style="padding: 12px 16px; background: #fafafa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <span class="fw-bold" style="font-size: 0.95rem; color: #333;">Thông báo</span>
            <span style="font-size: 0.75rem; color: var(--primary, #2a7a27); font-weight: 600; cursor: pointer;">Đánh dấu đã đọc</span>
          </li>
          <li>
            <div style="padding: 40px 20px; text-align: center;">
              <i class="bi bi-bell-slash" style="font-size: 2.5rem; color: #ccc; margin-bottom: 12px; display: block;"></i>
              <div style="font-weight: 600; color: #555; font-size: 0.9rem;">Không có thông báo mới</div>
              <div style="font-size: 0.8rem; color: #888; margin-top: 4px;">Hiện tại bạn chưa có thông báo nào cần xem.</div>
            </div>
          </li>
          <li style="border-top: 1px solid #eee; background: #fff; text-align: center;">
            <a href="#" style="display: block; padding: 10px; font-size: 0.85rem; color: var(--primary, #2a7a27); text-decoration: none; font-weight: 600;">Xem tất cả</a>
          </li>
        </ul>
      </div>
    </div>

  </header>

  {{-- ═══ CATEGORY NAV (AGU green sticky bar) ═══ --}}
  <nav class="cat-nav" id="cat-nav">
    <ul>
      @php $navCategories = $navCategories ?? \App\Models\Category::active()->roots()->get(); @endphp
      @foreach($navCategories as $cat)
      <li class="{{ request()->is('chuyen-muc/'.$cat->slug.'*') ? 'active' : '' }}">
        <a href="{{ route('category', $cat->slug) }}">{{ $cat->name }}</a>
      </li>
      @endforeach
      {{-- Icon tìm kiếm nằm sau mục cuối (Lướt web cùng SV) --}}
      <li class="cat-nav-search-li" style="margin-left: auto; display: flex; align-items: center;">
        <button type="button" onclick="toggleAdvSearch()" title="Tìm kiếm" class="cat-nav-search-btn d-flex align-items-center justify-content-center" style="background: transparent; border: none; outline: none; color: rgba(255,255,255,0.9); padding: 9px 14px; cursor: pointer;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 17px; height: 17px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </button>
      </li>
    </ul>
  </nav>

  {{-- ═══ ADVANCED SEARCH PANEL (dropdown from search icon) ═══ --}}
  <div id="advSearchPanel" style="display:none; background:#fff; border-bottom:3px solid var(--primary,#2a7a27); box-shadow:0 4px 16px rgba(0,0,0,.12); padding:16px 20px; position:relative; z-index:999;">
    <form action="{{ route('search') }}" method="GET">
      <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
        <div style="flex:2; min-width:180px;">
          <label style="font-size:.75rem;font-weight:700;color:#555;display:block;margin-bottom:4px;">Từ khóa</label>
          <input type="text" name="keyword" placeholder="Nội dung, tiêu đề..." value="{{ request('keyword') }}"
                 style="width:100%;border:1.5px solid #d0d0d0;border-radius:6px;padding:7px 10px;font-size:.82rem;outline:none;">
        </div>
        <div style="flex:1; min-width:140px;">
          <label style="font-size:.75rem;font-weight:700;color:#555;display:block;margin-bottom:4px;">Tác giả</label>
          <input type="text" name="author" placeholder="Tên tác giả..." value="{{ request('author') }}"
                 style="width:100%;border:1.5px solid #d0d0d0;border-radius:6px;padding:7px 10px;font-size:.82rem;outline:none;">
        </div>
        <div style="flex:1.2; min-width:140px;">
          <label style="font-size:.75rem;font-weight:700;color:#555;display:block;margin-bottom:4px;">Chuyên mục</label>
          <select name="category_id" style="width:100%;border:1.5px solid #d0d0d0;border-radius:6px;padding:7px 10px;font-size:.82rem;outline:none;background:#fff;">
            <option value="">-- Tất cả --</option>
            @foreach($navCategories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div style="flex:1; min-width:130px;">
          <label style="font-size:.75rem;font-weight:700;color:#555;display:block;margin-bottom:4px;">Từ ngày</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}"
                 style="width:100%;border:1.5px solid #d0d0d0;border-radius:6px;padding:7px 10px;font-size:.82rem;outline:none;">
        </div>
        <div style="flex:1; min-width:130px;">
          <label style="font-size:.75rem;font-weight:700;color:#555;display:block;margin-bottom:4px;">Đến ngày</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}"
                 style="width:100%;border:1.5px solid #d0d0d0;border-radius:6px;padding:7px 10px;font-size:.82rem;outline:none;">
        </div>
        <div>
          <button type="submit" style="background:var(--primary,#2a7a27);color:#fff;border:none;border-radius:6px;padding:8px 20px;font-size:.82rem;font-weight:700;cursor:pointer;">
            <i class="bi bi-search me-1"></i> Tìm
          </button>
          <a href="{{ route('search') }}" style="margin-left:6px;font-size:.75rem;color:#888;text-decoration:none;">Xóa bộ lọc</a>
        </div>
      </div>
    </form>
  </div>


  {{-- ═══ PAGE CONTENT ═══ --}}
  <div class="main-content-area">
    @yield('content')
  </div>

  {{-- ═══ FOOTER ═══════════════════════════════════════════════ --}}
  <footer id="footer">

    {{-- ── Main footer body ─────────────────────────────────── --}}
    <div style="background: linear-gradient(135deg, #0d3b10 0%, #1b5e20 50%, #0d3b10 100%);
                padding: 42px 0 28px; border-top: 4px solid #f5d400;">
      <div style="max-width:1140px; margin:0 auto; padding:0 20px;
                  display:grid; grid-template-columns:2fr 1fr 1fr; gap:36px;">

        {{-- Col 1: Logo + Giới thiệu --}}
        <div>
          <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
            <img src="{{ asset('images/logo.png') }}" alt="AGU Logo"
                 style="width:52px; height:52px; border-radius:50%; object-fit:cover;
                        border:2px solid rgba(245,212,0,.5);"
                 onerror="this.style.display='none'">
            <div>
              <div style="font-size:1.1rem; font-weight:900; color:#f5d400; line-height:1.2;
                          font-style:italic; letter-spacing:.5px;">e-News</div>
              <div style="font-size:.72rem; color:rgba(255,255,255,.75); font-weight:600; letter-spacing:.3px;">
                TRANG BÁO SINH VIÊN ĐẠI HỌC AN GIANG
              </div>
            </div>
          </div>

          <p style="font-size:.80rem; color:rgba(255,255,255,.70); line-height:1.75; margin-bottom:18px;">
            e-News là nơi phản ánh hoạt động học tập, phong trào sinh viên và
            giảng viên Trường Đại học An Giang — ra mắt từ tháng 10 năm 2004.
          </p>

          {{-- Thông tin liên hệ --}}
          <div style="display:flex; flex-direction:column; gap:8px;">
            <div style="display:flex; align-items:flex-start; gap:8px; font-size:.78rem; color:rgba(255,255,255,.80);">
              <i class="bi bi-geo-alt-fill" style="color:#f5d400; margin-top:2px; flex-shrink:0;"></i>
              <span>Số 18, đường Ung Văn Khiêm, phường Long Xuyên, tỉnh An Giang</span>
            </div>
            <div style="display:flex; align-items:center; gap:8px; font-size:.78rem; color:rgba(255,255,255,.80);">
              <i class="bi bi-telephone-fill" style="color:#f5d400; flex-shrink:0;"></i>
              <span>+84 296 625 6565 nhánh 1602</span>
            </div>
            <div style="display:flex; align-items:center; gap:8px; font-size:.78rem; color:rgba(255,255,255,.80);">
              <i class="bi bi-envelope-fill" style="color:#f5d400; flex-shrink:0;"></i>
              <a href="mailto:enews@agu.edu.vn"
                 style="color:rgba(255,255,255,.80); text-decoration:none; transition:color .18s;"
                 onmouseover="this.style.color='#f5d400'" onmouseout="this.style.color='rgba(255,255,255,.80)'">
                enews@agu.edu.vn
              </a>
            </div>
            <div style="display:flex; align-items:center; gap:8px; font-size:.78rem; color:rgba(255,255,255,.80);">
              <i class="bi bi-globe2" style="color:#f5d400; flex-shrink:0;"></i>
              <a href="http://enews.agu.edu.vn" target="_blank"
                 style="color:rgba(255,255,255,.80); text-decoration:none; transition:color .18s;"
                 onmouseover="this.style.color='#f5d400'" onmouseout="this.style.color='rgba(255,255,255,.80)'">
                enews.agu.edu.vn
              </a>
            </div>
          </div>
        </div>

        {{-- Col 2: Chuyên mục --}}
        <div>
          <h3 style="font-size:.80rem; font-weight:800; color:#f5d400; text-transform:uppercase;
                     letter-spacing:.8px; margin-bottom:14px; padding-bottom:8px;
                     border-bottom:1px solid rgba(245,212,0,.2);">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Chuyên mục
          </h3>
          @php
            $footerCats = \App\Models\Category::active()->roots()->limit(8)->get();
          @endphp
          <ul style="list-style:none; padding:0; margin:0;">
            @foreach($footerCats as $cat)
            <li style="margin-bottom:1px;">
              <a href="{{ route('category', $cat->slug) }}"
                 style="display:flex; align-items:center; gap:7px; padding:5px 0;
                        font-size:.78rem; color:rgba(255,255,255,.72); text-decoration:none;
                        border-bottom:1px solid rgba(255,255,255,.06); transition:color .15s;"
                 onmouseover="this.style.color='#f5d400'" onmouseout="this.style.color='rgba(255,255,255,.72)'">
                <i class="bi bi-chevron-right" style="font-size:.60rem; color:#f5d400;">
                </i>{{ $cat->name }}
              </a>
            </li>
            @endforeach
          </ul>
        </div>

        {{-- Col 3: Liên kết & Ban biên tập --}}
        <div>
          <h3 style="font-size:.80rem; font-weight:800; color:#f5d400; text-transform:uppercase;
                     letter-spacing:.8px; margin-bottom:14px; padding-bottom:8px;
                     border-bottom:1px solid rgba(245,212,0,.2);">
            <i class="bi bi-link-45deg me-2"></i>Thông tin
          </h3>
          <ul style="list-style:none; padding:0; margin:0 0 20px;">
            @foreach([
              ['url'=>route('about'),  'label'=>'Giới thiệu'],
              ['url'=>route('rules'),  'label'=>'Quy định đăng bài'],
              ['url'=>route('contact'),'label'=>'Liên hệ tòa soạn'],
              ['url'=>route('search'), 'label'=>'Tìm kiếm bài viết'],
              ['url'=>route('login'),  'label'=>'Đăng nhập hệ thống'],
            ] as $link)
            <li style="margin-bottom:1px;">
              <a href="{{ $link['url'] }}"
                 style="display:flex; align-items:center; gap:7px; padding:5px 0;
                        font-size:.78rem; color:rgba(255,255,255,.72); text-decoration:none;
                        border-bottom:1px solid rgba(255,255,255,.06); transition:color .15s;"
                 onmouseover="this.style.color='#f5d400'" onmouseout="this.style.color='rgba(255,255,255,.72)'">
                <i class="bi bi-chevron-right" style="font-size:.60rem; color:#f5d400;"></i>
                {{ $link['label'] }}
              </a>
            </li>
            @endforeach
          </ul>

          {{-- Phụ trách --}}
          <div style="background:rgba(255,255,255,.07); border-radius:8px; padding:14px;
                      border:1px solid rgba(245,212,0,.15);">
            <div style="font-size:.70rem; font-weight:800; color:#f5d400; text-transform:uppercase;
                        letter-spacing:.5px; margin-bottom:10px;">
              <i class="bi bi-person-badge-fill me-1"></i> Phụ trách
            </div>
            <div style="display:flex; align-items:center; gap:9px; margin-bottom:8px;">
              <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#2a7a27,#388e3c);
                          color:#fff; display:flex; align-items:center; justify-content:center;
                          font-size:.75rem; font-weight:800; flex-shrink:0;">NK</div>
              <div>
                <div style="font-size:.72rem; font-weight:700; color:#fff;">ThS. Ngô Thị Kim Duyên</div>
                <div style="font-size:.66rem; color:rgba(255,255,255,.55);">Phụ trách chung e-News</div>
              </div>
            </div>
          </div>

          {{-- Social icons --}}
          <div style="display:flex; gap:8px; margin-top:16px;">
            <a href="#" title="Facebook"
               style="width:34px; height:34px; border-radius:8px; background:rgba(255,255,255,.1);
                      color:rgba(255,255,255,.8); display:flex; align-items:center; justify-content:center;
                      font-size:.9rem; text-decoration:none; transition:all .18s; border:1px solid rgba(255,255,255,.1);"
               onmouseover="this.style.background='#1877F2'; this.style.color='#fff'; this.style.border='1px solid #1877F2';"
               onmouseout="this.style.background='rgba(255,255,255,.1)'; this.style.color='rgba(255,255,255,.8)'; this.style.border='1px solid rgba(255,255,255,.1)';">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#" title="YouTube"
               style="width:34px; height:34px; border-radius:8px; background:rgba(255,255,255,.1);
                      color:rgba(255,255,255,.8); display:flex; align-items:center; justify-content:center;
                      font-size:.9rem; text-decoration:none; transition:all .18s; border:1px solid rgba(255,255,255,.1);"
               onmouseover="this.style.background='#FF0000'; this.style.color='#fff'; this.style.border='1px solid #FF0000';"
               onmouseout="this.style.background='rgba(255,255,255,.1)'; this.style.color='rgba(255,255,255,.8)'; this.style.border='1px solid rgba(255,255,255,.1)';">
              <i class="bi bi-youtube"></i>
            </a>
            <a href="mailto:enews@agu.edu.vn" title="Email"
               style="width:34px; height:34px; border-radius:8px; background:rgba(255,255,255,.1);
                      color:rgba(255,255,255,.8); display:flex; align-items:center; justify-content:center;
                      font-size:.9rem; text-decoration:none; transition:all .18s; border:1px solid rgba(255,255,255,.1);"
               onmouseover="this.style.background='#f5d400'; this.style.color='#1b5e20'; this.style.border='1px solid #f5d400';"
               onmouseout="this.style.background='rgba(255,255,255,.1)'; this.style.color='rgba(255,255,255,.8)'; this.style.border='1px solid rgba(255,255,255,.1)';">
              <i class="bi bi-envelope-fill"></i>
            </a>
          </div>

        </div>
      </div>
    </div>

    {{-- ── Copyright bar ─────────────────────────────────────── --}}
    <div style="background:#071d08; padding:12px 20px;
                display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:10px;">
      <div style="font-size:.73rem; color:rgba(255,255,255,.50);">
        &copy; {{ date('Y') }} <strong style="color:rgba(255,255,255,.70);">e-News</strong>
        &mdash; Trang báo Sinh viên Trường Đại học An Giang &mdash; VNU-HCM.
        Bảo lưu mọi quyền.
      </div>
      {{-- Back to top --}}
      <button onclick="window.scrollTo({top:0,behavior:'smooth'})"
              style="display:flex; align-items:center; gap:6px; background:rgba(245,212,0,.12);
                     border:1px solid rgba(245,212,0,.3); border-radius:6px;
                     color:#f5d400; font-size:.72rem; font-weight:700; padding:5px 14px;
                     cursor:pointer; transition:all .18s; letter-spacing:.3px;"
              onmouseover="this.style.background='rgba(245,212,0,.22)'"
              onmouseout="this.style.background='rgba(245,212,0,.12)'">
        <i class="bi bi-arrow-up-circle-fill"></i> Trở lên trên
      </button>
    </div>

  </footer>


</div>{{-- /page-wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleAdvSearch() {
  const panel = document.getElementById('advSearchPanel');
  if (panel) {
    const isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';
    if (!isVisible) panel.querySelector('input[name="keyword"]')?.focus();
  }
}
@if(request()->anyFilled(['keyword','author','date_from','date_to','category_id']))
document.addEventListener('DOMContentLoaded', () => { toggleAdvSearch(); });
@endif

// Cấu hình JS (nếu có)
</script>

{{-- Script Google Translate --}}
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'vi',
    // Không dùng includedLanguages để hiển thị Full ngôn ngữ 
    // Không khai báo layout để hiển thị thẻ Dropdown Select (kéo xuống)
    autoDisplay: false
  }, 'google_translate_element');
}

// Khắc phục lỗi mất nút Translate khi người dùng ấn Back (lỗi bfcache)
window.addEventListener('pageshow', function (event) {
  if (event.persisted) {
    window.location.reload();
  }
});
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<style>
/* Ẩn thanh Google Translate banner phía trên cùng làm đẩy trang xuống */
.goog-te-banner-frame.skiptranslate, 
iframe.goog-te-banner-frame { display: none !important; }
.VIpgJd-ZVi9od-ORHb-OEVmcd { display: none !important; }
body { top: 0px !important; position: static !important; }
html { margin-top: 0px !important; height: auto !important; }

/* Ẩn popup tooltip khi hover vào text đã dịch */
#goog-gt-tt, .goog-te-balloon-frame { display: none !important; }
.goog-text-highlight { background-color: transparent !important; box-shadow: none !important; }

/* Ẩn chữ "Powered by Google Translate" */
.goog-te-gadget { font-size: 0px !important; color: transparent !important; }
.goog-te-gadget span { display: none !important; }

/* Thanh xanh trên: dropdown ngôn ngữ — nền tối, chữ sáng */
.top-bar .top-bar-lang { display: inline-flex; align-items: center; margin-right: 14px; }
.top-bar .goog-te-combo {
  border-radius: 6px;
  border: 1px solid rgba(255,255,255,.35);
  padding: 4px 10px;
  outline: none;
  font-size: 0.8rem;
  color: #fff;
  background-color: rgba(255,255,255,.15);
  cursor: pointer;
  max-width: 140px;
}
.top-bar .goog-te-combo option { background: #1b5e20; color: #fff; }

/* Dropdown ngôn ngữ ở header trắng (dự phòng) */
.goog-te-combo {
  border-radius: 6px;
  border: 1px solid #c2e1c2;
  padding: 4px 8px;
  outline: none;
  font-size: 0.85rem;
  color: #1b5e20;
  font-weight: 500;
  background-color: #f6fdf6;
  cursor: pointer;
}
</style>

@stack('scripts')
</body>
</html>

