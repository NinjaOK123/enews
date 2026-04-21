@extends('layouts.app')

@section('title', 'Giới thiệu — E-News AGU')
@section('meta_description', 'Trang báo sinh viên điện tử Trường Đại học An Giang (e-News) — Nơi phản ánh hoạt động học tập, phong trào sinh viên và giảng viên AGU từ năm 2004.')

@push('styles')
<style>
/* ═══ ABOUT PAGE ═══════════════════════════════════════════ */
.about-page {
  max-width: 1100px;
  margin: 28px auto;
  padding: 0 16px;
}

/* Hero banner */
.about-hero {
  border-radius: 14px;
  overflow: hidden;
  background: linear-gradient(135deg, #1b5e20 0%, #2a7a27 45%, #33691e 100%);
  padding: 48px 52px;
  display: flex;
  align-items: center;
  gap: 40px;
  margin-bottom: 32px;
  position: relative;
  box-shadow: 0 8px 40px rgba(27, 94, 32, .3);
}
.about-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse at 80% 20%, rgba(245,212,0,.12) 0%, transparent 50%),
    radial-gradient(ellipse at 10% 80%, rgba(255,102,0,.10) 0%, transparent 50%);
  pointer-events: none;
}
.hero-logo-wrap {
  flex-shrink: 0;
  width: 110px; height: 110px;
  border-radius: 50%;
  background: rgba(255,255,255,.12);
  border: 3px solid rgba(245,212,0,.5);
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 24px rgba(0,0,0,.2);
  backdrop-filter: blur(4px);
}
.hero-logo-wrap img { width: 86px; height: 86px; object-fit: contain; border-radius: 50%; }
.hero-text { flex: 1; min-width: 0; }
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(245,212,0,.18);
  border: 1px solid rgba(245,212,0,.4);
  color: #f5d400;
  font-size: .72rem;
  font-weight: 700;
  padding: 3px 14px;
  border-radius: 20px;
  letter-spacing: .6px;
  text-transform: uppercase;
  margin-bottom: 12px;
}
.hero-title {
  font-size: 2.1rem;
  font-weight: 900;
  color: #fff;
  line-height: 1.2;
  margin-bottom: 10px;
  letter-spacing: -.5px;
}
.hero-title span { color: #f5d400; }
.hero-desc {
  font-size: .95rem;
  color: rgba(255,255,255,.82);
  line-height: 1.7;
  margin-bottom: 18px;
}
.hero-url {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #f5d400;
  border: none;
  color: #1b5e20;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: .82rem;
  font-weight: 700;
  text-decoration: none;
  transition: background .18s, transform .15s;
  box-shadow: 0 2px 10px rgba(0,0,0,.15);
}
.hero-url:hover { background: #fff; color: #1b5e20; transform: translateY(-1px); }
.hero-stats {
  display: flex;
  gap: 28px;
  flex-shrink: 0;
}
.hero-stat {
  text-align: center;
  color: #fff;
}
.hero-stat-val {
  font-size: 2rem;
  font-weight: 900;
  color: #f5d400;
  line-height: 1;
  margin-bottom: 4px;
}
.hero-stat-label { font-size: .72rem; color: rgba(255,255,255,.7); }

/* Main layout */
.about-layout {
  display: flex;
  gap: 26px;
  align-items: flex-start;
}
.about-main { flex: 1; min-width: 0; }
.about-sidebar {
  width: 280px;
  flex-shrink: 0;
  position: sticky;
  top: 70px;
}

/* Section titles */
.section-heading {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1.08rem;
  font-weight: 900;
  color: #111;
  margin: 28px 0 16px;
  padding-bottom: 10px;
  border-bottom: 2px solid #e8f5e2;
}
.section-heading::before {
  content: '';
  width: 4px;
  height: 20px;
  background: linear-gradient(to bottom, #2a7a27, #f5d400);
  border-radius: 2px;
  flex-shrink: 0;
}

/* Intro paragraphs */
.intro-card {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  padding: 16px 18px;
  background: #fff;
  border: 1px solid #e8f5e2;
  border-radius: 10px;
  margin-bottom: 12px;
  transition: box-shadow .2s;
}
.intro-card:hover { box-shadow: 0 4px 16px rgba(42,122,39,.08); }
.intro-icon {
  width: 38px; height: 38px;
  border-radius: 10px;
  background: linear-gradient(135deg, #2a7a27, #388e3c);
  color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
  margin-top: 1px;
}
.intro-text {
  font-size: .92rem;
  color: #333;
  line-height: 1.75;
}

/* Topic cards */
.topic-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.topic-card {
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 20px 18px;
  transition: box-shadow .2s, transform .2s, border-color .2s;
}
.topic-card:hover {
  box-shadow: 0 6px 24px rgba(42,122,39,.12);
  transform: translateY(-2px);
  border-color: #c8e6c9;
}
.topic-card-icon {
  width: 48px; height: 48px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.35rem;
  margin-bottom: 12px;
}
.topic-card-title {
  font-size: .90rem;
  font-weight: 800;
  color: #1a1a1a;
  margin-bottom: 8px;
}
.topic-card-items {
  list-style: none;
  padding: 0; margin: 0;
}
.topic-card-items li {
  font-size: .79rem;
  color: #555;
  line-height: 1.55;
  padding: 4px 0;
  border-bottom: 1px dashed #f0f0f0;
  display: flex;
  gap: 6px;
  align-items: flex-start;
}
.topic-card-items li:last-child { border-bottom: none; }
.topic-card-items li::before {
  content: '›';
  color: #2a7a27;
  font-weight: 700;
  flex-shrink: 0;
  margin-top: 1px;
}

/* Sidebar widgets */
.sw { border-radius: 10px; overflow: hidden; margin-bottom: 16px; }
.sw-title {
  background: linear-gradient(135deg, #1b5e20, #2a7a27);
  color: #fff;
  font-size: .78rem;
  font-weight: 800;
  padding: 10px 14px;
  display: flex; align-items: center; gap: 7px;
  text-transform: uppercase; letter-spacing: .4px;
}
.sw-title i { color: #f5d400; }
.sw-body {
  border: 1px solid #ddd;
  border-top: none;
  background: #fff;
  padding: 16px;
}
.sw-stat-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 8px 0; border-bottom: 1px dashed #f0f0f0; font-size: .80rem;
}
.sw-stat-row:last-child { border-bottom: none; padding-bottom: 0; }
.sw-stat-val { font-weight: 800; color: #2a7a27; font-size: .95rem; }
.timeline-item {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 10px 0; border-bottom: 1px solid #f5f5f5;
}
.timeline-item:last-child { border-bottom: none; padding-bottom: 0; }
.timeline-dot {
  width: 28px; height: 28px; border-radius: 50%;
  background: #2a7a27; color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: .65rem; font-weight: 800; flex-shrink: 0;
}
.timeline-text { font-size: .78rem; color: #444; line-height: 1.5; }
.timeline-year { font-weight: 800; color: #2a7a27; display: block; font-size: .80rem; }

/* Contact info in sidebar */
.contact-row {
  display: flex; gap: 10px; align-items: flex-start;
  font-size: .78rem; color: #555; padding: 7px 0;
  border-bottom: 1px solid #f5f5f5;
}
.contact-row:last-child { border-bottom: none; }
.contact-row i { color: #2a7a27; margin-top: 2px; }
.about-page a { color: #2a7a27; }

@media (max-width: 900px) {
  .about-layout  { flex-direction: column; }
  .about-sidebar { width: 100%; position: static; }
  .topic-grid    { grid-template-columns: 1fr; }
  .about-hero    { flex-direction: column; gap: 24px; padding: 32px 24px; }
  .hero-stats    { justify-content: center; }
  .hero-title    { font-size: 1.55rem; }
}

/* ── Dark Mode ────────────────────────────────────────── */
html.dark .section-heading { color: #f4f4f5; border-bottom-color: #27272a; }
html.dark .intro-card { background: #18181b; border-color: #27272a; }
html.dark .intro-text { color: #e4e4e7; }
html.dark .topic-card { background: #18181b; border-color: #27272a; }
html.dark .topic-card-title { color: #f4f4f5; }
html.dark .topic-card-items li { color: #a1a1aa; border-bottom-color: #27272a; }
html.dark .sw-body { background: #18181b; border-color: #27272a; }
html.dark .sw-stat-row { border-bottom-color: #27272a; color: #a1a1aa; }
html.dark .timeline-item { border-bottom-color: #27272a; }
html.dark .timeline-text { color: #a1a1aa; }
html.dark .contact-row { color: #a1a1aa; border-bottom-color: #27272a; }
html.dark .sw-body a, html.dark .about-page a.hero-url { color: #e4e4e7 !important; }
html.dark .sw-body a:hover { background: #27272a !important; color: #a7f3d0 !important; }
</style>
@endpush

@section('content')

<div class="about-page">

  {{-- ══ HERO BANNER ══════════════════════════════════════════════ --}}
  <div class="about-hero">
    <div class="hero-logo-wrap">
      <img src="{{ asset('images/logo.png') }}" alt="Logo AGU"
           onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'font-size:2.2rem;\'>🎓</span>'">
    </div>

    <div class="hero-text">
      <div class="hero-badge">
        <i class="bi bi-broadcast-pin"></i>
        Trang tin điện tử sinh viên
      </div>
      <h1 class="hero-title">
        <span>e-News</span> AGU<br>
        <span style="font-size:1.1rem; color:rgba(255,255,255,.85); font-weight:600;">
          Trang báo Sinh viên Đại học An Giang
        </span>
      </h1>
      <p class="hero-desc">
        Trang báo sinh viên điện tử Trường Đại học An Giang (e-News) là một bộ phận của
        <strong style="color:#f5d400;">Thư viện Trường Đại học An Giang</strong>,
        ra mắt độc giả từ tháng 10 năm 2004 đến nay.
      </p>
      <a href="http://enews.agu.edu.vn" target="_blank" rel="noopener" class="hero-url">
        <i class="bi bi-globe2"></i> enews.agu.edu.vn
      </a>
    </div>

    <div class="hero-stats d-none d-md-flex">
      <div class="hero-stat">
        <div class="hero-stat-val">20+</div>
        <div class="hero-stat-label">Năm hoạt động</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-val">2004</div>
        <div class="hero-stat-label">Năm ra mắt</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-val">∞</div>
        <div class="hero-stat-label">Kỷ niệm SV</div>
      </div>
    </div>
  </div>

  {{-- ══ LAYOUT: Main + Sidebar ═══════════════════════════════════ --}}
  <div class="about-layout">

    {{-- MAIN ──────────────────────────────────────────────────── --}}
    <div class="about-main">

      {{-- Giới thiệu chung --}}
      <h2 class="section-heading">
        <i class="bi bi-info-circle-fill" style="color:#2a7a27;"></i>
        Về e-News AGU
      </h2>

      <div class="intro-card">
        <div class="intro-icon"><i class="bi bi-newspaper"></i></div>
        <div class="intro-text">
          <strong>e-News</strong> là nơi phản ánh những thông tin về các hoạt động học tập, phong trào
          của sinh viên cũng như hoạt động giảng dạy, nghiên cứu khoa học của giảng viên
          Trường Đại học An Giang.
        </div>
      </div>

      <div class="intro-card">
        <div class="intro-icon"><i class="bi bi-stars"></i></div>
        <div class="intro-text">
          <strong>e-News</strong> là sân chơi độc đáo dành riêng cho sinh viên Trường Đại học An Giang,
          nơi ghi dấu những kỷ niệm của bạn trong suốt thời gian học tập tại Trường.
          Đây là không gian để mỗi sinh viên được <em>lắng nghe, được viết, được sáng tạo</em>.
        </div>
      </div>

      <div class="intro-card" style="background:linear-gradient(135deg,#f3fbf2,#fff); border-color:#c8e6c9;">
        <div class="intro-icon" style="background:linear-gradient(135deg,#FF6600,#ff8c42);">
          <i class="bi bi-quote"></i>
        </div>
        <div class="intro-text">
          <em style="color:#555;">"Tri thức — Sáng tạo — Phát triển"</em>
          <br>
          <span style="font-size:.82rem; color:#888;">
            Đây là tinh thần mà e-News AGU hướng đến qua hơn 20 năm đồng hành
            cùng các thế hệ sinh viên Đại học An Giang.
          </span>
        </div>
      </div>

      {{-- Các chủ đề khai thác --}}
      <h2 class="section-heading">
        <i class="bi bi-grid-3x3-gap-fill" style="color:#2a7a27;"></i>
        Các chủ đề sinh viên có thể khai thác
      </h2>

      <div class="topic-grid">

        {{-- 1. Hoạt động, sự kiện --}}
        <div class="topic-card">
          <div class="topic-card-icon" style="background:#e8f5e9; color:#2a7a27;">
            <i class="bi bi-calendar-event-fill"></i>
          </div>
          <div class="topic-card-title">🎯 Hoạt động & Sự kiện</div>
          <ul class="topic-card-items">
            <li>Sự kiện, phong trào của lớp, chi đoàn, chi hội</li>
            <li>Đoàn khoa, Liên chi hội AGU</li>
            <li>Hoạt động cấp Ngành, cấp Khoa, cấp Trường</li>
            <li>CLB/Đội/Nhóm trực thuộc Hội Sinh viên</li>
          </ul>
        </div>

        {{-- 2. Học tập & NCKH --}}
        <div class="topic-card">
          <div class="topic-card-icon" style="background:#e3f2fd; color:#1565c0;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div class="topic-card-title">📚 Học tập & Nghiên cứu KH</div>
          <ul class="topic-card-items">
            <li>Kinh nghiệm học tốt, quản lý thời gian</li>
            <li>Câu chuyện vượt khó, chinh phục tri thức</li>
            <li>Kiến thức gắn thực tế và đời sống</li>
            <li>Gương mặt sinh viên tiêu biểu</li>
            <li>Thành tích học tập, khởi nghiệp nổi bật</li>
          </ul>
        </div>

        {{-- 3. Đời sống sinh viên --}}
        <div class="topic-card">
          <div class="topic-card-icon" style="background:#fff8e1; color:#f57f17;">
            <i class="bi bi-house-heart-fill"></i>
          </div>
          <div class="topic-card-title">🌱 Đời sống Sinh viên</div>
          <ul class="topic-card-items">
            <li>Nhịp sống sinh viên, thích nghi môi trường mới</li>
            <li>Trưởng thành qua từng giai đoạn học tập</li>
            <li>Góc nhìn về xã hội, văn hóa, giáo dục, công nghệ</li>
            <li>Ngoại khóa, thực tập, giao lưu quốc tế</li>
          </ul>
        </div>

        {{-- 4. Tuổi trẻ & Tình cảm đẹp --}}
        <div class="topic-card">
          <div class="topic-card-icon" style="background:#fce4ec; color:#c62828;">
            <i class="bi bi-heart-fill"></i>
          </div>
          <div class="topic-card-title">💛 Tuổi trẻ & Tình cảm đẹp</div>
          <ul class="topic-card-items">
            <li>Tình gia đình, tình thầy trò, tình bạn</li>
            <li>Kỷ niệm với quê hương, mái trường AGU</li>
            <li>Cảm xúc trẻ trung trước cái đẹp, nhân văn</li>
            <li>Sáng tác: truyện ngắn, tản văn, thơ</li>
          </ul>
        </div>

        {{-- 5. Giảng viên & Cộng đồng -- colspan 2 --}}
        <div class="topic-card" style="grid-column: 1 / -1; border-color:#c8e6c9; background:linear-gradient(135deg,#f3fbf2,#fff);">
          <div style="display:flex; gap:14px; align-items:flex-start;">
            <div class="topic-card-icon" style="background:linear-gradient(135deg,#2a7a27,#388e3c); color:#fff; flex-shrink:0;">
              <i class="bi bi-people-fill"></i>
            </div>
            <div style="flex:1;">
              <div class="topic-card-title" style="color:#1b5e20;">
                🎓 Hoạt động của Giảng viên & Cộng đồng AGU
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:6px; margin-top:6px;">
                @foreach([
                  'Đề tài nghiên cứu khoa học, đổi mới phương pháp giảng dạy',
                  'Chia sẻ từ thầy cô truyền cảm hứng học tập và sống đẹp',
                  'Kết nối giảng viên, cựu sinh viên trong học thuật & khởi nghiệp',
                ] as $item)
                <div style="display:flex; gap:6px; font-size:.78rem; color:#555; align-items:flex-start;">
                  <i class="bi bi-check-circle-fill" style="color:#2a7a27; flex-shrink:0; margin-top:2px;"></i>
                  {{ $item }}
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

      </div>{{-- /topic-grid --}}

      {{-- Call to action --}}
      <div style="margin-top:28px; padding:22px 24px; background:linear-gradient(135deg,#1b5e20,#2a7a27); border-radius:12px; text-align:center; color:#fff;">
        <p style="font-size:.95rem; margin-bottom:14px; color:rgba(255,255,255,.9);">
          <i class="bi bi-pencil-square" style="color:#f5d400;"></i>
          Bạn muốn đóng góp bài viết cho <strong style="color:#f5d400;">e-News AGU</strong>?
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
          <a href="{{ route('login') }}"
             style="display:inline-flex;align-items:center;gap:7px;padding:10px 24px;
                    background:#f5d400;color:#1b5e20;border-radius:8px;font-size:.84rem;
                    font-weight:800;text-decoration:none;transition:opacity .18s;"
             onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <i class="bi bi-pencil-fill"></i> Đăng nhập để viết bài
          </a>
          <a href="{{ route('contact') }}"
             style="display:inline-flex;align-items:center;gap:7px;padding:10px 24px;
                    background:rgba(255,255,255,.15);color:#fff;border:1.5px solid rgba(255,255,255,.35);
                    border-radius:8px;font-size:.84rem;font-weight:700;text-decoration:none;transition:background .18s;"
             onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
            <i class="bi bi-envelope-fill"></i> Liên hệ BTV
          </a>
        </div>
      </div>

    </div>{{-- /about-main --}}

    {{-- SIDEBAR ───────────────────────────────────────────────── --}}
    <aside class="about-sidebar d-none d-lg-block">

      {{-- Thống kê nhanh --}}
      <div class="sw">
        <div class="sw-title"><i class="bi bi-bar-chart-fill"></i> Thông tin nhanh</div>
        <div class="sw-body">
          @foreach([
            ['label' => 'Năm thành lập', 'val' => '2004'],
            ['label' => 'Trực thuộc', 'val' => 'Thư viện AGU'],
            ['label' => 'Mô hình', 'val' => 'Báo SV điện tử'],
            ['label' => 'Cập nhật', 'val' => 'Liên tục'],
          ] as $s)
          <div class="sw-stat-row">
            <span style="color:#555;">{{ $s['label'] }}</span>
            <span class="sw-stat-val">{{ $s['val'] }}</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Mốc lịch sử --}}
      <div class="sw">
        <div class="sw-title"><i class="bi bi-clock-history"></i> Dấu mốc lịch sử</div>
        <div class="sw-body" style="padding:12px 16px;">
          @foreach([
            ['year'=>'10/2004','text'=>'e-News ra mắt độc giả lần đầu tiên'],
            ['year'=>'2010','text'=>'Nâng cấp giao diện và bổ sung chuyên mục mới'],
            ['year'=>'2015','text'=>'Mở rộng cộng tác viên từ toàn trường'],
            ['year'=>'2024','text'=>'Kỷ niệm 20 năm đồng hành cùng AGU'],
            ['year'=>'2026','text'=>'Ra mắt portal mới — hiện đại và năng động'],
          ] as $t)
          <div class="timeline-item">
            <div class="timeline-dot">{{ mb_substr($t['year'],0,2) }}</div>
            <div class="timeline-text">
              <span class="timeline-year">{{ $t['year'] }}</span>
              {{ $t['text'] }}
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Liên hệ --}}
      <div class="sw">
        <div class="sw-title"><i class="bi bi-geo-alt-fill"></i> Liên hệ tòa soạn</div>
        <div class="sw-body">
          @foreach([
            ['icon'=>'bi-building','text'=>'Thư viện Trường ĐH An Giang'],
            ['icon'=>'bi-geo-alt-fill','text'=>'18 Ung Văn Khiêm, Long Xuyên, An Giang'],
            ['icon'=>'bi-globe2','text'=>'enews.agu.edu.vn'],
            ['icon'=>'bi-envelope-fill','text'=>'bientap@agu.edu.vn'],
          ] as $c)
          <div class="contact-row">
            <i class="bi {{ $c['icon'] }}"></i>
            <span>{{ $c['text'] }}</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Link nhanh --}}
      <div class="sw">
        <div class="sw-title"><i class="bi bi-link-45deg"></i> Xem thêm</div>
        <div class="sw-body" style="padding:8px 0;">
          @foreach([
            ['url'=>route('home'),'icon'=>'bi-house-fill','label'=>'Trang chủ'],
            ['url'=>route('rules'),'icon'=>'bi-file-text-fill','label'=>'Quy định đăng bài'],
            ['url'=>route('contact'),'icon'=>'bi-envelope-fill','label'=>'Liên hệ BTV'],
            ['url'=>route('category','ban-tin-agu'),'icon'=>'bi-newspaper','label'=>'Bản tin AGU'],
          ] as $l)
          <a href="{{ $l['url'] }}"
             style="display:flex;align-items:center;gap:9px;padding:9px 16px;font-size:.80rem;
                    color:#333;text-decoration:none;border-bottom:1px solid #f5f5f5;transition:background .15s;"
             onmouseover="this.style.background='#f3fbf2'; this.style.color='#2a7a27';"
             onmouseout="this.style.background=''; this.style.color='#333';">
            <i class="bi {{ $l['icon'] }}" style="color:#2a7a27;"></i>
            {{ $l['label'] }}
          </a>
          @endforeach
        </div>
      </div>

    </aside>

  </div>{{-- /about-layout --}}

</div>{{-- /about-page --}}

@endsection
