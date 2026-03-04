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

    {{-- Main navigation --}}
    <nav class="header-nav d-none d-lg-flex">
      <a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i>
        <span>Giới thiệu</span>
      </a>
      <a href="{{ url('/quy-dinh') }}" class="nav-item {{ request()->is('quy-dinh') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i>
        <span>Quy định</span>
      </a>
      <a href="{{ url('/lien-he') }}" class="nav-item {{ request()->is('lien-he') ? 'active' : '' }}">
        <i class="bi bi-envelope"></i>
        <span>Liên hệ</span>
      </a>
      <a href="{{ url('/enews-doc-va-suy-ngam') }}" class="nav-item {{ request()->is('enews-doc-va-suy-ngam') ? 'active' : '' }}">
        <i class="bi bi-book"></i>
        <span>eNews - Đọc & Suy ngẫm</span>
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
      @php $navCategories = $navCategories ?? \App\Models\Category::active()->roots()->get(); @endphp
      @foreach($navCategories as $cat)
      <li class="{{ request()->is('chuyen-muc/'.$cat->slug.'*') ? 'active' : '' }}">
        <a href="{{ route('category', $cat->slug) }}">{{ $cat->name }}</a>
      </li>
      @endforeach
    </ul>
    {{-- Search icon at end of nav --}}
    <div class="cat-nav-search" id="catNavSearch">
      <button onclick="toggleAdvSearch()" title="Tìm kiếm nâng cao" style="background:none;border:none;color:#fff;cursor:pointer;padding:0 12px;font-size:1rem;line-height:1;">
        <i class="bi bi-search"></i>
      </button>
    </div>
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
<script>
function toggleAdvSearch() {
  const panel = document.getElementById('advSearchPanel');
  if (panel) {
    const isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';
    if (!isVisible) panel.querySelector('input[name="keyword"]')?.focus();
  }
}
// Auto-open if there are active search filters
@if(request()->anyFilled(['keyword','author','date_from','date_to','category_id']))
document.addEventListener('DOMContentLoaded', () => { toggleAdvSearch(); });
@endif
</script>
@stack('scripts')
</body>
</html>

