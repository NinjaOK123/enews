<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="@yield('meta_description', 'Trang Tin Điện Tử - Trang báo Sinh viên Đại học An Giang')">
  <title>@yield('title', 'Trang chủ') — Trang báo Sinh viên Đại học An Giang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  @stack('styles')
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
      <i class="bi bi-calendar3"></i>
      <span>{{ \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->isoFormat('dddd, D [tháng] M, YYYY') }}</span>
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

    {{-- Search bar --}}
    <div class="header-search">
      <form action="{{ route('search') }}" method="GET">
        <div class="search-wrap">
          <input type="text" name="q" placeholder="Tìm kiếm tin tức..." value="{{ request('q') }}" autocomplete="off">
          <button class="search-btn" type="submit"><i class="bi bi-search"></i></button>
        </div>
      </form>
    </div>

    {{-- Quick desktop nav --}}
    <nav class="header-nav d-none d-lg-flex">
      <a href="{{ route('home') }}" class="header-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i>Trang chủ
      </a>
      <a href="#cat-nav" class="header-nav-item">
        <i class="bi bi-grid-3x3-gap"></i>Chuyên mục
      </a>
      <a href="{{ route('search') }}" class="header-nav-item {{ request()->routeIs('search') ? 'active' : '' }}">
        <i class="bi bi-newspaper"></i>Tin mới nhất
      </a>
      <a href="#events" class="header-nav-item">
        <i class="bi bi-calendar-event"></i>Sự kiện
      </a>
      <a href="#footer" class="header-nav-item">
        <i class="bi bi-envelope"></i>Liên hệ
      </a>
    </nav>

    {{-- Login + Bell --}}
    <div class="header-actions">
      <a href="{{ route('login') }}" class="btn-login">
        <i class="bi bi-person-circle"></i> Đăng nhập
      </a>
      <a href="#" class="action-icon" title="Thông báo">
        <i class="bi bi-bell"></i>
      </a>
    </div>

  </header>

  {{-- ═══ CATEGORY NAV (AGU green sticky bar) ═══ --}}
  <nav class="cat-nav" id="cat-nav">
    <ul>
      <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
        <a href="{{ route('home') }}">Bản tin AGU</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/phong-su-anh*') ? 'active' : '' }}">
        <a href="{{ route('category', 'phong-su-anh') }}">Phóng sự Ảnh</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/khoa-hoc*') ? 'active' : '' }}">
        <a href="{{ route('category', 'khoa-hoc-voi-agu') }}">Khoa học với AGU</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/cau-chuyen*') ? 'active' : '' }}">
        <a href="{{ route('category', 'cau-chuyen-agu') }}">Câu chuyện AGU</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/goc-nhin*') ? 'active' : '' }}">
        <a href="{{ route('category', 'goc-nhin') }}">Góc nhìn</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/tan-man*') ? 'active' : '' }}">
        <a href="{{ route('category', 'tan-man') }}">Tản mạn</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/guong-mat*') ? 'active' : '' }}">
        <a href="{{ route('category', 'guong-mat-agu') }}">Gương mặt AGU</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/sv-clb*') ? 'active' : '' }}">
        <a href="{{ route('category', 'sv-clb') }}">SV với Câu lạc bộ</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/enews-ban-doc*') ? 'active' : '' }}">
        <a href="{{ route('category', 'enews-ban-doc') }}">eNews và Bạn đọc</a>
      </li>
      <li class="{{ request()->is('chuyen-muc/luot-web*') ? 'active' : '' }}">
        <a href="{{ route('category', 'luot-web-cung-sv') }}">Lướt web cùng SV</a>
      </li>
    </ul>
  </nav>

  {{-- ═══ PAGE CONTENT ═══ --}}
  <div class="main-content-area">
    @yield('content')
  </div>

  {{-- ═══ FOOTER ═══ --}}
  <footer class="site-footer" id="footer">
    <div>
      <strong style="color:#fff;">Trang báo Sinh viên — Đại học An Giang</strong><br>
      <span>18 Ung Văn Khiêm, P. Đông Xuyên, TP. Long Xuyên, An Giang</span><br>
      <span>ĐT: (0296) 3 841 424 &nbsp;|&nbsp; Email: enews@agu.edu.vn</span><br>
      <span style="font-size:.72rem;color:rgba(255,255,255,.45);">&copy; {{ date('Y') }} Trường Đại học An Giang — VNU-HCM. Bảo lưu mọi quyền.</span>
    </div>
    <div class="footer-socials">
      <a href="#" class="footer-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
      <a href="#" class="footer-social-btn" title="YouTube"><i class="bi bi-youtube"></i></a>
      <a href="{{ route('login') }}" class="footer-social-btn" title="Đăng nhập CMS"><i class="bi bi-person-fill-lock"></i></a>
    </div>
  </footer>

</div>{{-- /page-wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
