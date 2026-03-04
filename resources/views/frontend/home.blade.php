@extends('layouts.app')

@section('title', 'Trang Chủ')
@section('meta_description', 'E-News AGU — Trang tin điện tử Trường Đại học An Giang. Cập nhật tin tức, sự kiện, nghiên cứu khoa học và hoạt động sinh viên.')

@section('content')

{{-- ═══ HERO + SIDEBAR ROW ═══ --}}
<div class="hero-area">

  {{-- HERO SLIDER — dữ liệu thật từ DB --}}
  <div class="hero-slider-wrap">
    @if($heroPosts->isNotEmpty())
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        @foreach($heroPosts as $i => $hero)
        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
          <div class="hero-slide">
            <a href="{{ route('post.show', $hero->slug) }}">
              <img src="{{ $hero->thumbnail_url }}" alt="{{ $hero->title }}">
            </a>
            <div class="hero-badge-label">{{ $hero->category->name ?? 'Tin nổi bật' }}</div>
            <div class="hero-caption">
              <a href="{{ route('post.show', $hero->slug) }}" class="hero-caption-text" style="text-decoration:none;color:#fff;">
                {{ $hero->title }}
              </a>
              <div style="font-size:.72rem; color:rgba(255,255,255,.7); margin-top:6px; display:flex; gap:12px;">
                <span><i class="bi bi-person"></i> {{ $hero->author->name ?? 'Ban Biên tập' }}</span>
                <span><i class="bi bi-calendar3"></i> {{ $hero->published_at?->format('d/m/Y') }}</span>
                <span><i class="bi bi-eye"></i> {{ number_format($hero->view_count) }}</span>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      {{-- Controls --}}
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"
              style="width:30px; background:rgba(0,0,0,.3); border-radius:0 3px 3px 0; left:0; height:30px; top:40%;">
        <span class="carousel-control-prev-icon" style="width:16px;height:16px;"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"
              style="width:30px; background:rgba(0,0,0,.3); border-radius:3px 0 0 3px; right:0; height:30px; top:40%;">
        <span class="carousel-control-next-icon" style="width:16px;height:16px;"></span>
      </button>
    </div>

    {{-- Numbered dots --}}
    <div class="hero-dots" id="heroDots">
      @foreach($heroPosts as $i => $hero)
      <span class="{{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}">{{ $i + 1 }}</span>
      @endforeach
    </div>
    @else
    <div style="height:360px; background:#f0f4f0; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#aaa;">
      <p>Chưa có bài viết nào.</p>
    </div>
    @endif
  </div>

  {{-- SIDEBAR: Mới nhất — dữ liệu thật --}}
  <div class="sidebar-latest">
    <div class="widget-title"><i class="bi bi-lightning-fill"></i> Mới nhất</div>
    <div class="latest-list">
      @forelse($sidebarLatest as $item)
      <a href="{{ route('post.show', $item->slug) }}" class="latest-item">
        {{ $item->title }}
      </a>
      @empty
      <p style="padding:10px; font-size:.8rem; color:#aaa;">Chưa có bài viết.</p>
      @endforelse
    </div>
  </div>

</div>{{-- /hero-area --}}

<hr class="agu">

{{-- ═══ MACRO: hiển thị 1 section-widget từ $sections[$slug] ═══ --}}
@php
/**
 * Helper: render một section widget
 * $slug       — slug của category
 * $label      — Tên hiển thị
 * $posts      — Collection bài viết (limit 4)
 */
function renderSection(string $slug, string $label, \Illuminate\Database\Eloquent\Collection $posts): string { return ''; }
@endphp

{{-- ═══ SECTION ROW 1: Bản tin AGU + SV với CLB ═══ --}}
<div class="sections-row">

  {{-- BẢN TIN AGU --}}
  @include('partials.home-section', [
    'slug'  => 'ban-tin-agu',
    'label' => 'Bản tin AGU',
    'posts' => $sections['ban-tin-agu'],
  ])

  {{-- SV VỚI CLB --}}
  @include('partials.home-section', [
    'slug'  => 'sv-clb',
    'label' => 'SV với Câu lạc bộ',
    'posts' => $sections['sv-clb'],
  ])

</div>

<hr class="agu">

{{-- ═══ SECTION ROW 2: Gương mặt AGU + eNews và Bạn đọc ═══ --}}
<div class="sections-row">

  @include('partials.home-section', [
    'slug'  => 'guong-mat-agu',
    'label' => 'Gương mặt AGU',
    'posts' => $sections['guong-mat-agu'],
  ])

  @include('partials.home-section', [
    'slug'  => 'enews-ban-doc',
    'label' => 'eNews và Bạn đọc',
    'posts' => $sections['enews-ban-doc'],
  ])

</div>

<hr class="agu">

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
      {{-- Fill remaining slots với placeholder nếu ít hơn 4 bài --}}
      @for($i = $photosPosts->count(); $i < 4; $i++)
      <div class="photo-strip-item" style="background:#f0f0f0; display:flex; align-items:center; justify-content:center; min-height:96px; border-radius:8px;">
        <i class="bi bi-image text-muted" style="font-size:1.5rem;"></i>
      </div>
      @endfor
    </div>
    @else
    <p style="color:#aaa; text-align:center; padding:20px; font-size:.85rem;">Chưa có ảnh phóng sự.</p>
    @endif
  </div>
</div>

<hr class="agu">

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

<hr class="agu">

{{-- ═══ GÓC NHÌN + TẢN MẠN + LƯỚT WEB ═══ --}}
<div class="sections-row triple">

  {{-- GÓC NHÌN --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Góc nhìn <a href="{{ route('category', 'goc-nhin') }}" class="more-link">» Thêm</a>
    </div>
    <div class="section-widget-body">
      @forelse($sections['goc-nhin'] as $p)
      <div class="news-list-item" style="display:block; padding:6px 0; border-bottom:1px dashed var(--border);">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.80rem; font-weight:600; color:var(--text); line-height:1.4; display:block;">
          » {{ $p->title }}
        </a>
      </div>
      @empty
      <p style="font-size:.8rem; color:#aaa; padding:8px 0;">Chưa có bài viết.</p>
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
      <div class="news-list-item" style="display:block; padding:6px 0; border-bottom:1px dashed var(--border);">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.80rem; font-weight:600; color:var(--text); line-height:1.4; display:block;">
          » {{ $p->title }}
        </a>
      </div>
      @empty
      <p style="font-size:.8rem; color:#aaa; padding:8px 0;">Chưa có bài viết.</p>
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
      <div class="news-list-item" style="display:block; padding:6px 0; border-bottom:1px dashed var(--border);">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.80rem; font-weight:600; color:var(--text); line-height:1.4; display:block;">
          » {{ $p->title }}
        </a>
      </div>
      @empty
      <p style="font-size:.8rem; color:#aaa; padding:8px 0;">Chưa có bài viết.</p>
      @endforelse
    </div>
  </div>

</div>{{-- /sections-row triple --}}

@endsection

@push('scripts')
<script>
const carousel = document.getElementById('heroCarousel');
const dots = document.querySelectorAll('#heroDots span');

if (carousel && dots.length) {
  carousel.addEventListener('slide.bs.carousel', function (e) {
    dots.forEach(d => d.classList.remove('active'));
    if (dots[e.to]) dots[e.to].classList.add('active');
  });
  dots.forEach(function(dot) {
    dot.addEventListener('click', function() {
      const bs = bootstrap.Carousel.getInstance(carousel);
      if (bs) bs.to(parseInt(this.dataset.slide));
    });
  });
  new bootstrap.Carousel(carousel, { interval: 5000, ride: 'carousel' });
}
</script>
@endpush
