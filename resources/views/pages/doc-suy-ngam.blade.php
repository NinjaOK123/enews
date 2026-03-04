@extends('layouts.app')

@section('title', 'eNews — Đọc và Suy ngẫm')
@section('meta_description', 'Chương trình "Cùng đọc báo mỗi ngày với eNews" giúp sinh viên rèn luyện kỹ năng, phản biện và tích lũy điểm rèn luyện tại Đại học An Giang.')

@push('styles')
<style>
/* ═══ DOC & SUY NGAM PAGE ══════════════════════════════════ */
.dsn-page {
  max-width: 960px;
  margin: 32px auto;
  padding: 0 16px;
}

/* ── Hero ────────────────────────────────────────────────── */
.dsn-hero {
  background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
  border-radius: 14px;
  padding: 36px 42px;
  display: flex;
  align-items: center;
  gap: 32px;
  margin-bottom: 32px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(230,81,0,.25);
  color: #fff;
}
.dsn-hero::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse at 85% 20%, rgba(255,255,255,.2) 0%, transparent 55%);
  pointer-events: none;
}
.hero-icon-dsn {
  width: 76px; height: 76px; flex-shrink: 0;
  border-radius: 50%; background: rgba(255,255,255,.2);
  border: 3px solid rgba(255,255,255,.4);
  display: flex; align-items: center; justify-content: center; font-size: 2.2rem;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
.dsn-hero h1 { font-size:1.8rem; font-weight:900; margin:0 0 10px; letter-spacing:-.5px; }
.dsn-hero p { font-size:.92rem; color:rgba(255,255,255,.9); margin:0; line-height:1.6; }

/* ── Intro Box ───────────────────────────────────────────── */
.dsn-intro {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-left: 5px solid #2a7a27;
  border-radius: 10px;
  padding: 24px 28px;
  margin-bottom: 32px;
  font-size: .95rem;
  line-height: 1.7;
  color: #333;
  box-shadow: 0 4px 14px rgba(0,0,0,.04);
}
.dsn-intro strong { color: #2a7a27; }

/* ── Activities Grid ─────────────────────────────────────── */
.activity-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 36px;
}
.act-card {
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 24px 22px;
  position: relative;
  transition: transform .2s, box-shadow .2s, border-color .2s;
  overflow: hidden;
}
.act-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0,0,0,.08);
}
.act-card::before {
  content: ''; position: absolute; top:0; left:0; right:0; height:4px;
}
.act-card.c-blue::before   { background: #1976d2; }
.act-card.c-blue:hover     { border-color: #90caf9; }
.act-card.c-green::before  { background: #388e3c; }
.act-card.c-green:hover    { border-color: #a5d6a7; }
.act-card.c-purple::before { background: #7b1fa2; }
.act-card.c-purple:hover   { border-color: #ce93d8; }

.act-icon {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; margin-bottom: 16px;
}
.act-card.c-blue .act-icon   { background: #e3f2fd; color: #1976d2; }
.act-card.c-green .act-icon  { background: #e8f5e9; color: #388e3c; }
.act-card.c-purple .act-icon { background: #f3e5f5; color: #7b1fa2; }

.act-title { font-size: 1.1rem; font-weight: 800; color: #222; margin-bottom: 12px; }
.act-desc { font-size: .88rem; color: #555; line-height: 1.6; margin-bottom: 16px; }

.act-reward {
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff8e1; border: 1px solid #ffe082;
  color: #f57f17; font-size: .82rem; font-weight: 700;
  padding: 6px 14px; border-radius: 6px; width: 100%;
}
.act-reward i { font-size: 1rem; }

/* ── Notes Section ───────────────────────────────────────── */
.dsn-note {
  background: linear-gradient(135deg, #f3fbf2, #e8f5e9);
  border: 1px solid #c8e6c9;
  border-radius: 12px;
  padding: 24px 30px;
  margin-bottom: 32px;
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
.note-icon {
  width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
  background: #2a7a27; color: #fff;
  display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
}
.note-body h3 { font-size: 1rem; font-weight: 800; color: #1b5e20; margin: 0 0 8px; }
.note-body p { font-size: .90rem; color: #444; line-height: 1.65; margin: 0; }

/* ── CTA / Outro ─────────────────────────────────────────── */
.dsn-outro {
  text-align: center;
  padding: 30px 20px;
  background: #fff;
  border-radius: 12px;
  border: 1px dashed #ccc;
}
.dsn-outro p { font-size: 1rem; color: #333; margin-bottom: 16px; font-weight: 500; }
.dsn-outro .signature { font-family: 'Georgia', serif; font-size: 1.4rem; font-weight: bold; color: #2a7a27; font-style: italic; }

@media (max-width: 768px) {
  .dsn-hero { flex-direction: column; text-align: center; padding: 28px 20px; gap: 20px; }
  .dsn-note { flex-direction: column; align-items: center; text-align: center; padding: 20px; }
}
</style>
@endpush

@section('content')
<div class="dsn-page">

  {{-- Hero Banner --}}
  <div class="dsn-hero">
    <div class="hero-icon-dsn"><i class="bi bi-book-half"></i></div>
    <div>
      <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.2);
                  border:1px solid rgba(255,255,255,.4);color:#fff;font-size:.68rem;font-weight:700;
                  padding:2px 12px;border-radius:20px;letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px;">
        <i class="bi bi-stars"></i> Trải nghiệm làm Biên tập viên
      </div>
      <h1>Cùng đọc báo mỗi ngày với eNews</h1>
      <p>
        Hoạt động giúp bạn nâng cao kỹ năng đọc viết, kỹ năng xã hội, 
        và tích lũy điểm rèn luyện trong suốt năm học tại Đại học An Giang.
      </p>
    </div>
  </div>

  {{-- Intro --}}
  <div class="dsn-intro">
    Bạn muốn có cơ hội trải nghiệm làm một <strong>“biên tập viên”</strong>, trau dồi thêm những kỹ năng phản biện đánh giá, hãy tham gia chương trình <strong>“Cùng đọc báo mỗi ngày với eNews”</strong>. Những hoạt động này không những giúp cho bạn nâng cao kỹ năng đọc viết, kỹ năng hoạt động công tác xã hội mà còn giúp cho bạn tích lũy điểm rèn luyện trong năm học.
  </div>

  {{-- Activities Grid --}}
  <div class="activity-grid">
    
    {{-- 1. Gửi bình luận --}}
    <div class="act-card c-blue">
      <div class="act-icon"><i class="bi bi-chat-quote-fill"></i></div>
      <h3 class="act-title">1. Gửi bình luận</h3>
      <div class="act-desc">
        <ul style="padding-left:18px; margin:0;">
          <li style="margin-bottom:6px;">Nêu lên cảm nghĩ và quan điểm cá nhân (viết lời bình) cho các bài đã đăng.</li>
          <li>Đặc biệt: Bình luận xuất sắc được nâng cấp thành bài viết sẽ nhận <strong>nhuận bút</strong> theo chế độ hiện hành.</li>
        </ul>
      </div>
      <div class="act-reward">
        <i class="bi bi-clock-history"></i>
        <span>Mỗi bình luận (≥ 5 dòng) = <strong>4 giờ CTXH</strong></span>
      </div>
    </div>

    {{-- 2. Cùng biên tập --}}
    <div class="act-card c-green">
      <div class="act-icon"><i class="bi bi-vector-pen"></i></div>
      <h3 class="act-title">2. Cùng biên tập với eNews</h3>
      <div class="act-desc">
        <p style="margin:0 0 8px;">Góp ý về: <strong>lỗi chính tả, sai thông tin, bổ sung thông tin…</strong> trong các bài viết đã xuất bản.</p>
        <p style="margin:0; font-size:.82rem;">
          📬 Gửi qua email: <a href="mailto:enews@agu.edu.vn" style="color:#2a7a27; font-weight:600;">enews@agu.edu.vn</a>
        </p>
      </div>
      <div class="act-reward" style="color:#2e7d32; background:#e8f5e9; border-color:#c8e6c9;">
        <i class="bi bi-clock-history"></i>
        <span>Mỗi ý kiến được chọn = <strong>2 giờ CTXH</strong></span>
      </div>
    </div>

    {{-- 3. Đề xuất chủ đề --}}
    <div class="act-card c-purple">
      <div class="act-icon"><i class="bi bi-lightbulb-fill"></i></div>
      <h3 class="act-title">3. Đề xuất &amp; Cải tiến</h3>
      <div class="act-desc">
        <ul style="padding-left:18px; margin:0;">
          <li style="margin-bottom:6px;">Giới thiệu hoàn cảnh khó khăn, sinh viên có thành tích đặc biệt...</li>
          <li>Cung cấp thông tin hoạt động phong trào đột xuất.</li>
          <li>Đề xuất chủ đề sáng tác, góp ý cải tiến trang báo.</li>
        </ul>
      </div>
      <div class="act-reward" style="color:#6a1b9a; background:#f3e5f5; border-color:#e1bee7;">
        <i class="bi bi-clock-history"></i>
        <span>Mỗi đề xuất được chọn = <strong>2 giờ CTXH</strong></span>
      </div>
    </div>

  </div>

  {{-- Note --}}
  <div class="dsn-note">
    <div class="note-icon"><i class="bi bi-info-lg"></i></div>
    <div class="note-body">
      <h3>Lưu ý về điểm rèn luyện</h3>
      <p>
        Với <strong>40 giờ/học kỳ</strong>, bạn sẽ được cộng <strong>4 điểm rèn luyện</strong> trong hoạt động công tác xã hội theo Quyết định của Hiệu trưởng số 2570/QĐ - ĐHAG, ban hành ngày 30/12/2015 về việc Quy định đánh giá kết quả rèn luyện của người học hình thức giáo dục chính quy.
      </p>
    </div>
  </div>

  {{-- Outro --}}
  <div class="dsn-outro">
    <p>Chỉ cần gửi bình luận và cung cấp thông tin, bạn đã góp phần xây dựng và quảng bá hình ảnh trang báo điện tử của chính các bạn.</p>
    <p style="color:#2a7a27; font-weight:700;">Rất mong bạn đọc eNews sẽ hưởng ứng tích cực hoạt động này để trang eNews ngày càng phong phú và hấp dẫn hơn!</p>
    <div class="signature mt-3">e-News</div>
  </div>

</div>
@endsection
