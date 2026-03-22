@extends('layouts.app')

@section('title', 'Trang Chủ')
@section('meta_description', 'E-News AGU — Trang tin điện tử Trường Đại học An Giang. Cập nhật tin tức, sự kiện, nghiên cứu khoa học và hoạt động sinh viên.')

@section('content')

{{-- ═══ HERO + SIDEBAR ROW ═══ --}}
<div class="hero-area">

  {{-- HERO SLIDER — Alpine.js auto-slide mỗi 5 giây --}}
  <div class="hero-slider-wrap">
    @if($heroPosts->isNotEmpty())
    <div x-data="{
           current: 0,
           total: {{ $heroPosts->count() }},
           timer: null,
           init() { this.timer = setInterval(() => this.next(), 5000); },
           next() { this.current = (this.current + 1) % this.total; },
           prev() { this.current = (this.current - 1 + this.total) % this.total; },
           go(n)  { this.current = n; clearInterval(this.timer); this.timer = setInterval(() => this.next(), 5000); }
         }"
         style="position:relative;">

      {{-- Slides --}}
      @foreach($heroPosts as $i => $hero)
      <div x-show="current === {{ $i }}"
           x-transition:enter="transition-opacity duration-500"
           x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity duration-300"
           x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
           class="hero-slide" {{ $i > 0 ? 'style=display:none' : '' }}>
        <a href="{{ route('post.show', $hero->slug) }}" style="display:block;">
          <img src="{{ $hero->thumbnail_url }}" alt="{{ $hero->title }}"
               loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
        </a>
        <div class="hero-content">
          <div class="hero-cat-badge">{{ $hero->category->name ?? 'Tin nổi bật' }}</div>
          <a href="{{ route('post.show', $hero->slug) }}" class="hero-title" style="text-decoration:none;display:block;">
            {{ $hero->title }}
          </a>
          <div class="hero-meta">
            <span><i class="bi bi-person"></i> {{ $hero->author->name ?? 'Ban Biên tập' }}</span>
            <span><i class="bi bi-calendar3"></i> {{ $hero->published_at?->format('d/m/Y') }}</span>
            <span><i class="bi bi-eye"></i> {{ number_format($hero->view_count) }}</span>
          </div>
        </div>
      </div>
      @endforeach

      {{-- Glass nav buttons --}}
      <button @click="prev()" type="button" class="hero-nav-btn prev" aria-label="Trước">
        <i class="bi bi-chevron-left" style="font-size:.85rem;"></i>
      </button>
      <button @click="next()" type="button" class="hero-nav-btn next" aria-label="Sau">
        <i class="bi bi-chevron-right" style="font-size:.85rem;"></i>
      </button>

      {{-- Progress dots --}}
      <div class="hero-dots">
        @foreach($heroPosts as $i => $hero)
        <span @click="go({{ $i }})" :class="current === {{ $i }} ? 'active' : ''"
              :key="current"></span>
        @endforeach
      </div>

    </div>
    @else
    <div style="height:360px;background:#f0f4f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#aaa;">
      <p>Chưa có bài viết nào.</p>
    </div>
    @endif
  </div>

  {{-- SIDEBAR: Mới nhất --}}
  <div class="sidebar-latest">
    <div class="widget-title"><i class="bi bi-lightning-fill"></i> Mới nhất</div>
    <div class="latest-list">
      @forelse($sidebarLatest as $item)
      <a href="{{ route('post.show', $item->slug) }}" class="latest-item">
        {{ $item->title }}
      </a>
      @empty
      <p style="padding:10px;font-size:.8rem;color:#aaa;">Chưa có bài viết.</p>
      @endforelse
    </div>
  </div>

</div>{{-- /hero-area --}}

{{-- ═══ BANNER CUỘC THI — Marquee chạy vòng ═══ --}}
@if($banners->isNotEmpty())
<div class="banner-marquee-wrap">
  <div class="banner-marquee-track">
    {{-- Render 2 lần để tạo hiệu ứng vòng liên tục --}}
    @foreach([1, 2] as $_)
    <div class="banner-marquee-row">
      @foreach($banners as $banner)
      @if($banner->link)
      <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="banner-item">
        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" loading="lazy">
      </a>
      @else
      <div class="banner-item">
        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" loading="lazy">
      </div>
      @endif
      @endforeach
    </div>
    @endforeach
  </div>
</div>
@endif

<hr class="fusion">

{{-- ═══ SECTION ROW 1: Bản tin AGU + Gương mặt AGU ═══ --}}
<div class="sections-row">

  @include('partials.home-section', [
    'slug'  => 'ban-tin-agu',
    'label' => 'Bản tin AGU',
    'posts' => $sections['ban-tin-agu'],
  ])

  @include('partials.home-section', [
    'slug'  => 'guong-mat-agu',
    'label' => 'Gương mặt AGU',
    'posts' => $sections['guong-mat-agu'],
  ])

</div>

<hr class="fusion">

{{-- ═══ SV VỚI CÂU LẠC BỘ — GRID CÁC CLB CON ═══ --}}
<div class="section-widget" style="margin-bottom:10px;">
  <div class="section-widget-title">
    SV với Câu lạc bộ
    <a href="{{ route('category', 'sv-voi-cau-lac-bo') }}" class="more-link">» Xem thêm</a>
  </div>
  <div class="clb-grid">

    @php
    $clbSections = [
      'clb-van-tho'      => 'CLB Văn thơ',
      'clb-am-nhac'      => 'CLB Âm nhạc',
      'clb-tin-hoc'      => 'CLB Tin học',
      'clb-tam-tinh-tre' => 'CLB Tâm tình trẻ',
      'clb-ngoai-ngu'    => 'CLB Ngoại ngữ',
      'clb-sach-ban-doc' => 'CLB Sách & Bạn đọc',
      'clb-nghe-thuat'   => 'CLB Nghệ thuật',
      'clb-su-hoc'       => 'CLB Sử học',
      'clb-moi-truong'   => 'CLB Môi trường',
      'clb-du-lich'      => 'CLB Du lịch',
    ];
    @endphp

    @foreach($clbSections as $clbSlug => $clbName)
    @php $clbPosts = $sections[$clbSlug] ?? collect(); @endphp
    <div class="clb-card">
      <div class="clb-card-header">
        <a href="{{ route('category', $clbSlug) }}" class="clb-card-title">{{ $clbName }}</a>
        <a href="{{ route('category', $clbSlug) }}" class="clb-card-more">» Thêm</a>
      </div>
      <div class="clb-card-body">
        @forelse($clbPosts->take(4) as $p)
        <div class="clb-list-item">
          <a href="{{ route('post.show', $p->slug) }}">» {{ Str::limit($p->title, 55) }}</a>
        </div>
        @empty
        <p class="clb-empty">Chưa có bài viết.</p>
        @endforelse
      </div>
    </div>
    @endforeach

  </div>{{-- /clb-grid --}}
</div>

<hr class="fusion">

{{-- ═══ SECTION ROW 2: eNews và Bạn đọc ═══ --}}
<div class="sections-row">

  @include('partials.home-section', [
    'slug'  => 'enews-va-ban-doc',
    'label' => 'eNews và Bạn đọc',
    'posts' => $sections['enews-va-ban-doc'],
  ])

</div>

<hr class="fusion">

{{-- ═══ PHÓNG SỰ ẢNH ═══ --}}
@php $photosPosts = $sections['phong-su-anh']; @endphp
<div class="section-widget" style="margin-bottom:10px;">
  <div class="section-widget-title">
    Phóng sự Ảnh
    <a href="{{ route('category', 'phong-su-anh') }}" class="more-link">» Xem thêm</a>
  </div>
  <div class="section-widget-body">
    @if($photosPosts->isNotEmpty())
    <div class="photo-strip">
      @foreach($photosPosts as $p)
      <a href="{{ route('post.show', $p->slug) }}" class="photo-strip-item">
        <img src="{{ $p->thumbnail_url }}" alt="{{ $p->title }}" loading="lazy">
        <div class="photo-strip-caption">{{ Str::limit($p->title, 40) }}</div>
      </a>
      @endforeach
      @for($i = $photosPosts->count(); $i < 4; $i++)
      <div class="photo-strip-item" style="background:#f0f0f0;display:flex;align-items:center;justify-content:center;min-height:96px;border-radius:8px;">
        <i class="bi bi-image text-muted" style="font-size:1.5rem;"></i>
      </div>
      @endfor
    </div>
    @else
    <p style="color:#aaa;text-align:center;padding:20px;font-size:.85rem;">Chưa có ảnh phóng sự.</p>
    @endif
  </div>
</div>

<hr class="fusion">

{{-- ═══ SECTION ROW 3: Câu chuyện AGU + Khoa học với AGU ═══ --}}
<div class="sections-row">

  @include('partials.home-section', [
    'slug'  => 'cau-chuyen-agu',
    'label' => 'Câu chuyện AGU',
    'posts' => $sections['cau-chuyen-agu'],
  ])

  @include('partials.home-section', [
    'slug'  => 'khoa-hoc-voi-agu',
    'label' => 'Khoa học với AGU',
    'posts' => $sections['khoa-hoc-voi-agu'],
  ])

</div>

<hr class="fusion">

{{-- ═══ GÓC NHÌN + TẢN MẠN + LƯỚT WEB ═══ --}}
<div class="sections-row triple">

  {{-- GÓC NHÌN --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Góc nhìn <a href="{{ route('category', 'goc-nhin') }}" class="more-link">» Thêm</a>
    </div>
    <div class="section-widget-body">
      @forelse($sections['goc-nhin'] as $p)
      <div class="news-list-item" style="display:block;padding:6px 0;border-bottom:1px dashed var(--border);">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.80rem;font-weight:600;color:var(--text);line-height:1.4;display:block;">
          » {{ $p->title }}
        </a>
      </div>
      @empty
      <p style="font-size:.8rem;color:#aaa;padding:8px 0;">Chưa có bài viết.</p>
      @endforelse
    </div>
  </div>

  {{-- TẢN MẠN --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Tản mạn <a href="{{ route('category', 'tan-man') }}" class="more-link">» Thêm</a>
    </div>
    <div class="section-widget-body">
      @forelse($sections['tan-man'] as $p)
      <div class="news-list-item" style="display:block;padding:6px 0;border-bottom:1px dashed var(--border);">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.80rem;font-weight:600;color:var(--text);line-height:1.4;display:block;">
          » {{ $p->title }}
        </a>
      </div>
      @empty
      <p style="font-size:.8rem;color:#aaa;padding:8px 0;">Chưa có bài viết.</p>
      @endforelse
    </div>
  </div>

  {{-- LƯỚT WEB CÙNG SV --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Lướt web cùng SV <a href="{{ route('category', 'luot-web-cung-sv') }}" class="more-link">» Thêm</a>
    </div>
    <div class="section-widget-body">
      @forelse($sections['luot-web-cung-sv'] as $p)
      <div class="news-list-item" style="display:block;padding:6px 0;border-bottom:1px dashed var(--border);">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.80rem;font-weight:600;color:var(--text);line-height:1.4;display:block;">
          » {{ $p->title }}
        </a>
      </div>
      @empty
      <p style="font-size:.8rem;color:#aaa;padding:8px 0;">Chưa có bài viết.</p>
      @endforelse
    </div>
  </div>

</div>{{-- /sections-row triple --}}

@endsection
