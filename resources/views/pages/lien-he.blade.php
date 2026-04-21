@extends('layouts.app')

@section('title', 'Liên hệ — E-News AGU')
@section('meta_description', 'Thông tin liên hệ Ban biên tập Trang báo sinh viên điện tử e-News Trường Đại học An Giang. Địa chỉ, điện thoại, email và danh sách Ban điều hành.')

@push('styles')
<style>
/* ═══ CONTACT PAGE ══════════════════════════════════════════ */
.contact-page {
  max-width: 1100px;
  margin: 28px auto;
  padding: 0 16px;
}

/* ── Hero ────────────────────────────────────────────────── */
.contact-hero {
  background: linear-gradient(135deg, #1b5e20 0%, #2a7a27 55%, #33691e 100%);
  border-radius: 14px;
  padding: 34px 40px;
  display: flex;
  align-items: center;
  gap: 28px;
  margin-bottom: 30px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 6px 28px rgba(27,94,32,.28);
}
.contact-hero::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse at 85% 20%, rgba(245,212,0,.10) 0%, transparent 55%);
  pointer-events: none;
}
.hero-icon-lg {
  width: 72px; height: 72px; flex-shrink: 0;
  border-radius: 18px; background: rgba(255,255,255,.13);
  border: 2px solid rgba(245,212,0,.4);
  display: flex; align-items: center; justify-content: center; font-size: 2.2rem;
}
.contact-hero h1  { font-size:1.55rem; font-weight:900; color:#fff; margin:0 0 6px; }
.contact-hero p   { font-size:.84rem; color:rgba(255,255,255,.78); margin:0; line-height:1.6; }

/* ── Info strip ─────────────────────────────────────────── */
.info-strip {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 14px;
  margin-bottom: 32px;
}
.info-card {
  background: #fff;
  border: 1px solid #e4e4e4;
  border-radius: 12px;
  padding: 18px 18px;
  display: flex;
  gap: 14px;
  align-items: flex-start;
  box-shadow: 0 2px 10px rgba(0,0,0,.05);
  transition: box-shadow .2s, transform .2s;
}
.info-card:hover { box-shadow: 0 6px 20px rgba(42,122,39,.10); transform: translateY(-2px); }
.info-card-icon {
  width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
.info-card-label { font-size: .68rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.info-card-val   { font-size: .84rem; color: #222; line-height: 1.62; }
.info-card-val a { color: #2a7a27; text-decoration: none; font-weight: 600; transition: color .15s; }
.info-card-val a:hover { color: #1b5e20; text-decoration: underline; }

/* ── Section heading ─────────────────────────────────────── */
.team-heading {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 1.08rem;
  font-weight: 900;
  color: #111;
  margin: 36px 0 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #e8f5e2;
}
.team-heading::before {
  content: '';
  width: 4px; height: 22px;
  background: linear-gradient(to bottom, #2a7a27, #f5d400);
  border-radius: 2px; flex-shrink: 0;
}

/* ── Person card ─────────────────────────────────────────── */
.team-grid {
  display: grid;
  gap: 16px;
}
.team-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
.team-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
.team-grid.cols-5 { grid-template-columns: repeat(5, 1fr); }
.team-grid.cols-1 { grid-template-columns: repeat(3, 1fr); } /* centered */

.person-card {
  background: #fff;
  border: 1px solid #ebebeb;
  border-radius: 14px;
  padding: 22px 18px 18px;
  text-align: center;
  transition: box-shadow .2s, transform .2s, border-color .2s;
  position: relative;
  overflow: hidden;
}
.person-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(to right, #2a7a27, #f5d400);
  border-radius: 14px 14px 0 0;
  opacity: 0; transition: opacity .2s;
}
.person-card:hover { box-shadow: 0 8px 28px rgba(42,122,39,.12); transform: translateY(-3px); border-color: #c8e6c9; }
.person-card:hover::before { opacity: 1; }

/* Avatar */
.person-avatar {
  width: 80px; height: 80px;
  border-radius: 50%;
  margin: 0 auto 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.6rem; font-weight: 900; color: #fff;
  font-style: normal;
  position: relative;
  overflow: hidden;
  border: 3px solid #e8f5e2;
  box-shadow: 0 3px 12px rgba(0,0,0,.12);
  transition: border-color .2s;
}
.person-card:hover .person-avatar { border-color: #2a7a27; }
.person-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

.person-role {
  display: inline-block;
  font-size: .65rem; font-weight: 800;
  padding: 2px 12px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: .5px;
  margin-bottom: 8px;
}
.role-truongban   { background: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
.role-photruongban{ background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
.role-bientap     { background: #e8f5e9; color: #2a7a27; border: 1px solid #a5d6a7; }
.role-kythuat     { background: #f3e5f5; color: #7b1fa2; border: 1px solid #ce93d8; }

.person-name  { font-size: .88rem; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; line-height: 1.3; }
.person-email { font-size: .75rem; }
.person-email a { color: #2a7a27; text-decoration: none; transition: color .15s; }
.person-email a:hover { color: #1b5e20; text-decoration: underline; }
.person-phone { font-size: .74rem; color: #666; margin-top: 4px; }

/* ── Contact form ─────────────────────────────────────────── */
.contact-form-wrap {
  background: #fff;
  border: 1px solid #e4e4e4;
  border-radius: 14px;
  overflow: hidden;
  margin-top: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,.05);
}
.cf-header {
  background: linear-gradient(135deg, #1b5e20, #2a7a27);
  padding: 16px 22px;
  display: flex; align-items: center; gap: 10px;
  color: #fff; font-size: .88rem; font-weight: 800;
}
.cf-header i { color: #f5d400; }
.cf-body { padding: 24px; }
.cf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.cf-label  { font-size: .75rem; font-weight: 700; color: #555; display: block; margin-bottom: 5px; }
.cf-input, .cf-textarea, .cf-select {
  width: 100%; border: 1.5px solid #d0e8cf; border-radius: 8px;
  padding: 10px 12px; font-size: .84rem; font-family: inherit; outline: none;
  transition: border-color .18s, box-shadow .18s; background: #fff;
}
.cf-input:focus, .cf-textarea:focus, .cf-select:focus {
  border-color: #2a7a27;
  box-shadow: 0 0 0 3px rgba(42,122,39,.10);
}
.cf-textarea { resize: vertical; min-height: 110px; }
.cf-submit {
  background: linear-gradient(135deg, #2a7a27, #388e3c);
  color: #fff; border: none; border-radius: 8px;
  padding: 11px 28px; font-size: .88rem; font-weight: 800;
  cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
  transition: opacity .18s, transform .12s;
}
.cf-submit:hover { opacity: .9; transform: translateY(-1px); }

/* Map placeholder */
.map-wrap {
  border-radius: 12px; overflow: hidden;
  border: 1px solid #e4e4e4;
  margin-top: 28px;
  box-shadow: 0 2px 10px rgba(0,0,0,.06);
}

@media (max-width:860px) {
  .team-grid.cols-2,
  .team-grid.cols-3,
  .team-grid.cols-5,
  .team-grid.cols-1 { grid-template-columns: repeat(2, 1fr); }
  .cf-grid { grid-template-columns: 1fr; }
  .contact-hero { flex-direction: column; gap: 16px; padding: 24px 20px; }
}
@media (max-width:520px) {
  .team-grid.cols-2,
  .team-grid.cols-3,
  .team-grid.cols-5,
  .team-grid.cols-1 { grid-template-columns: 1fr; }
}

/* ── Dark Mode ────────────────────────────────────────── */
html.dark .info-card { background: #18181b; border-color: #27272a; }
html.dark .info-card-val { color: #e4e4e7; }
html.dark .info-card-val a { color: #6ee7b7; }
html.dark .info-card-icon { background: rgba(255,255,255,0.05) !important; color: #a1a1aa !important; }
html.dark .team-heading { color: #f4f4f5; border-bottom-color: #27272a; }
html.dark .person-card { background: #18181b; border-color: #27272a; }
html.dark .person-name { color: #f4f4f5; }
html.dark .person-email a { color: #6ee7b7; }
html.dark .person-phone { color: #a1a1aa; }
html.dark .contact-form-wrap { background: #18181b; border-color: #27272a; }
html.dark .cf-label { color: #a1a1aa; }
html.dark .cf-input, html.dark .cf-textarea, html.dark .cf-select { background: #27272a; border-color: #3f3f46; color: #f4f4f5; }
html.dark .cf-input:focus, html.dark .cf-textarea:focus, html.dark .cf-select:focus { border-color: #34d399; }
html.dark .map-wrap { border-color: #27272a; }
html.dark .contact-page div[style*="background:linear-gradient(135deg,#f3fbf2,#fff)"] { background: #18181b !important; border-color: #27272a !important; }
html.dark .contact-page p[style*="color:#1b5e20"] { color: #a7f3d0 !important; }
html.dark .contact-page p[style*="color:#666"] { color: #a1a1aa !important; }
html.dark .contact-page a[style*="background:#fff"] { background: #27272a !important; color: #6ee7b7 !important; border-color: #3f3f46 !important; }
</style>
@endpush

@section('content')

<div class="contact-page">

  {{-- ── Hero ─────────────────────────────────────────────────── --}}
  <div class="contact-hero">
    <div class="hero-icon-lg">📬</div>
    <div>
      <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(245,212,0,.18);
                  border:1px solid rgba(245,212,0,.35);color:#f5d400;font-size:.68rem;font-weight:700;
                  padding:2px 12px;border-radius:20px;letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px;">
        <i class="bi bi-envelope-fill"></i> Tòa soạn
      </div>
      <h1>Liên hệ Ban Biên tập</h1>
      <p>
        Gửi bài viết, góp ý hoặc liên hệ trực tiếp Ban Biên tập
        <strong style="color:#f5d400;">e-News</strong> — Trường Đại học An Giang.
      </p>
    </div>
  </div>

  {{-- ── Info cards ───────────────────────────────────────────── --}}
  <div class="info-strip">

    <div class="info-card">
      <div class="info-card-icon" style="background:#e8f5e9; color:#2a7a27;">
        <i class="bi bi-geo-alt-fill"></i>
      </div>
      <div>
        <div class="info-card-label">Địa chỉ</div>
        <div class="info-card-val">
          Ban biên tập e-News<br>
          <strong>Tầng 4 – Tòa nhà Thư viện và các Trung tâm</strong><br>
          Trường ĐH An Giang<br>
          Số 18, Ung Văn Khiêm, Long Xuyên, An Giang
        </div>
      </div>
    </div>

    <div class="info-card">
      <div class="info-card-icon" style="background:#e3f2fd; color:#1565c0;">
        <i class="bi bi-telephone-fill"></i>
      </div>
      <div>
        <div class="info-card-label">Điện thoại</div>
        <div class="info-card-val">
          <strong>Bàn:</strong>
          <a href="tel:+842966256565">+84 296 625 6565</a> – 1606<br><br>
          <strong>Di động:</strong><br>
          <a href="tel:0919324291">0919 324 291</a> <span style="color:#888;">(cô Anh Thư)</span><br>
          <a href="tel:0918000525">0918 000 525</a> <span style="color:#888;">(thầy Thiện Mỹ)</span>
        </div>
      </div>
    </div>

    <div class="info-card">
      <div class="info-card-icon" style="background:#fff8e1; color:#f57f17;">
        <i class="bi bi-envelope-fill"></i>
      </div>
      <div>
        <div class="info-card-label">Email</div>
        <div class="info-card-val">
          <a href="mailto:enews@agu.edu.vn" style="font-size:.90rem;">enews@agu.edu.vn</a><br><br>
          <a href="http://enews.agu.edu.vn" target="_blank" style="font-size:.82rem;">
            <i class="bi bi-globe2"></i> enews.agu.edu.vn
          </a>
        </div>
      </div>
    </div>

    <div class="info-card">
      <div class="info-card-icon" style="background:#f3e5f5; color:#7b1fa2;">
        <i class="bi bi-clock-fill"></i>
      </div>
      <div>
        <div class="info-card-label">Giờ làm việc</div>
        <div class="info-card-val">
          <strong>Thứ 2 – Thứ 6</strong><br>
          07:00 – 11:30 &nbsp;·&nbsp; 13:30 – 17:00<br><br>
          <span style="color:#aaa; font-size:.78rem;">Nghỉ thứ 7, Chủ nhật &amp; lễ</span>
        </div>
      </div>
    </div>

  </div>

  {{-- ── Ban điều hành ────────────────────────────────────────── --}}
  <h2 class="team-heading">
    <i class="bi bi-star-fill" style="color:#f5d400;"></i> Ban điều hành
  </h2>
  <div class="team-grid cols-2" style="max-width:560px;">

    <div class="person-card">
      <div class="person-avatar" style="background: linear-gradient(135deg, #2a7a27, #388e3c);">NK</div>
      <div class="person-role role-truongban">Trưởng ban</div>
      <div class="person-name">ThS. Ngô Thị Kim Duyên</div>
      <div class="person-email"><a href="mailto:ntkduyen@agu.edu.vn">ntkduyen@agu.edu.vn</a></div>
    </div>

    <div class="person-card">
      <div class="person-avatar" style="background: linear-gradient(135deg, #1565c0, #1976d2);">NL</div>
      <div class="person-role role-photruongban">Phó trưởng ban</div>
      <div class="person-name">ThS. Nguyễn Thị Hồng Loan</div>
      <div class="person-email"><a href="mailto:nthloan@agu.edu.vn">nthloan@agu.edu.vn</a></div>
    </div>

  </div>

  {{-- ── Ban biên tập ─────────────────────────────────────────── --}}
  <h2 class="team-heading">
    <i class="bi bi-pencil-fill" style="color:#2a7a27;"></i> Ban biên tập
  </h2>
  <div class="team-grid cols-3">

    <div class="person-card">
      <div class="person-avatar" style="background: linear-gradient(135deg, #00695c, #00897b);">NT</div>
      <div class="person-role role-bientap">Biên tập viên</div>
      <div class="person-name">ThS. Nguyễn N Anh Thư</div>
      <div class="person-email"><a href="mailto:nnathu@agu.edu.vn">nnathu@agu.edu.vn</a></div>
      <div class="person-phone"><i class="bi bi-telephone" style="font-size:.7rem;"></i> 0919 324 291</div>
    </div>

    <div class="person-card">
      <div class="person-avatar" style="background: linear-gradient(135deg, #ad1457, #d81b60);">HC</div>
      <div class="person-role role-bientap">Biên tập viên</div>
      <div class="person-name">ThS. Huỳnh Thị Cam</div>
      <div class="person-email"><a href="mailto:htcam@agu.edu.vn">htcam@agu.edu.vn</a></div>
    </div>

    <div class="person-card">
      <div class="person-avatar" style="background: linear-gradient(135deg, #4527a0, #5e35b1);">LM</div>
      <div class="person-role role-bientap">Biên tập viên</div>
      <div class="person-name">CN. Lê Thiện Mỹ</div>
      <div class="person-email"><a href="mailto:ltmy@agu.edu.vn">ltmy@agu.edu.vn</a></div>
      <div class="person-phone"><i class="bi bi-telephone" style="font-size:.7rem;"></i> 0918 000 525</div>
    </div>

    <div class="person-card">
      <div class="person-avatar" style="background: linear-gradient(135deg, #e65100, #ef6c00);">TC</div>
      <div class="person-role role-bientap">Biên tập viên</div>
      <div class="person-name">ThS. Trần Tùng Chinh</div>
      <div class="person-email"><a href="mailto:ttchinh@agu.edu.vn">ttchinh@agu.edu.vn</a></div>
    </div>

    <div class="person-card">
      <div class="person-avatar" style="background: linear-gradient(135deg, #1565c0, #0277bd);">HP</div>
      <div class="person-role role-bientap">Biên tập viên</div>
      <div class="person-name">TS. Huỳnh Phước Hải</div>
      <div class="person-email"><a href="mailto:hphai@agu.edu.vn">hphai@agu.edu.vn</a></div>
    </div>

  </div>

  {{-- ── Đội ngũ kỹ thuật ────────────────────────────────────── --}}
  <h2 class="team-heading">
    <i class="bi bi-gear-fill" style="color:#7b1fa2;"></i> Đội ngũ kỹ thuật
  </h2>
  <div class="team-grid cols-1">

    <div class="person-card" style="max-width:240px;">
      <div class="person-avatar" style="background: linear-gradient(135deg, #7b1fa2, #9c27b0);">TT</div>
      <div class="person-role role-kythuat">Kỹ thuật</div>
      <div class="person-name">Trịnh Thanh Thảo</div>
      <div class="person-email"><a href="mailto:ttthao@agu.edu.vn">ttthao@agu.edu.vn</a></div>
    </div>

  </div>

  {{-- ── Form liên hệ + Bản đồ ───────────────────────────────── --}}
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:22px; margin-top:20px;">

    {{-- Form --}}
    <div class="contact-form-wrap">
      <div class="cf-header">
        <i class="bi bi-chat-dots-fill"></i> Gửi tin nhắn cho chúng tôi
      </div>
      <div class="cf-body">
        
        @if(session('success'))
          <div style="background:#e8f5e9; color:#2a7a27; border:1px solid #c8e6c9; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-size:0.85rem; font-weight:600;">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
          </div>
        @endif

        <form action="{{ route('contact.send') }}" method="POST">
          @csrf
        <div class="cf-grid">
          <div>
            <label class="cf-label">Họ và tên <span style="color:#e53935;">*</span></label>
            <input type="text" name="name" class="cf-input" placeholder="Nguyễn Văn A" required>
            @error('name') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
          </div>
          <div>
            <label class="cf-label">Email <span style="color:#e53935;">*</span></label>
            <input type="email" name="email" class="cf-input" placeholder="email@agu.edu.vn" required>
            @error('email') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
          </div>
        </div>
        <div style="margin-bottom:14px;">
          <label class="cf-label">Tiêu đề</label>
          <input type="text" name="subject" class="cf-input" placeholder="Chủ đề liên hệ...">
          @error('subject') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
        </div>
        <div style="margin-bottom:18px;">
          <label class="cf-label">Nội dung <span style="color:#e53935;">*</span></label>
          <textarea name="message" class="cf-textarea" placeholder="Nhập nội dung liên hệ, góp ý hoặc gửi bài..." required></textarea>
          @error('message') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
        </div>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
          <p style="font-size:.74rem; color:#aaa; margin:0;">
            <i class="bi bi-info-circle"></i>
            Để gửi bài viết, hãy gửi file qua email
            <a href="mailto:enews@agu.edu.vn" style="color:#2a7a27;">enews@agu.edu.vn</a>
          </p>
          <button type="submit" class="cf-submit">
            <i class="bi bi-send-fill"></i> Gửi tin nhắn
          </button>
        </div>
      </form>
      </div>
    </div>

    {{-- Google Map — Thư viện Trường Đại học An Giang --}}
    <div class="map-wrap" style="position:relative;">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1962.1!2d105.4325691!3d10.3694947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310a7318db1f27c5%3A0xac5cc8ab6416d0f3!2zVGjGsCB2aeG7h24gdHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBBbiBHaWFuZw!5e0!3m2!1svi!2svn!4v1711040000000"
        width="100%" height="100%" style="border:0; min-height:360px; display:block;"
        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
        title="Thư viện Trường Đại học An Giang"></iframe>

      {{-- Nút chỉ đường --}}
      <a href="https://www.google.com/maps/dir/10.3463332,105.3716798/Th%C6%B0+vi%E1%BB%87n+tr%C6%B0%E1%BB%9Dng+%C4%90%E1%BA%A1i+h%E1%BB%8Dc+An+Giang,+9C9M%2BP2V,+P.+M%E1%BB%B9+Ph%C6%B0%E1%BB%9Bc,+Th%C3%A0nh+ph%E1%BB%91+Long+Xuy%C3%AAn,+An+Giang,+Vi%E1%BB%87t+Nam/@10.3625199,105.3827697,5971m/data=!3m2!1e3!4b1!4m10!4m9!1m1!4e1!1m5!1m1!1s0x310a7318db1f27c5:0xac5cc8ab6416d0f3!2m2!1d105.4325691!2d10.3694947!3e0?entry=ttu&g_ep=EgoyMDI2MDMxOC4xIKXMDSoASAFQAw%3D%3D"
         target="_blank" rel="noopener"
         style="position:absolute; bottom:16px; left:16px; z-index:10;
                display:inline-flex; align-items:center; gap:8px;
                background:#2a7a27; color:#fff; font-size:.84rem; font-weight:700;
                padding:10px 20px; border-radius:10px; text-decoration:none;
                box-shadow:0 4px 16px rgba(0,0,0,.25); transition:all .2s;"
         onmouseover="this.style.background='#1b5e20';this.style.transform='translateY(-2px)'"
         onmouseout="this.style.background='#2a7a27';this.style.transform='translateY(0)'">
        <i class="bi bi-sign-turn-right-fill" style="font-size:1.1rem;"></i>
        Chỉ đường đến đây
      </a>

      {{-- Label địa chỉ --}}
      <div style="position:absolute; top:12px; left:12px; z-index:10;
                  background:rgba(255,255,255,.95); backdrop-filter:blur(6px);
                  padding:8px 14px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.12);
                  font-size:.78rem; color:#333; max-width:280px; line-height:1.5;">
        <strong style="color:#2a7a27;">📍 Thư viện ĐH An Giang</strong><br>
        <span style="color:#777;">18 Ung Văn Khiêm, P. Mỹ Phước, TP. Long Xuyên</span>
      </div>
    </div>

  </div>

  {{-- ── Banner liên kết ──────────────────────────────────────── --}}
  <div style="margin-top:28px; padding:20px 24px; background:linear-gradient(135deg,#f3fbf2,#fff);
              border:1px solid #c8e6c9; border-radius:12px; display:flex;
              align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;">
    <div>
      <p style="font-size:.90rem; font-weight:700; color:#1b5e20; margin:0 0 3px;">
        <i class="bi bi-info-circle-fill"></i> Muốn tìm hiểu thêm?
      </p>
      <p style="font-size:.78rem; color:#666; margin:0;">
        Xem thêm quy định cộng tác hoặc tìm hiểu về trang báo e-News AGU.
      </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="{{ route('rules') }}"
         style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;
                background:#2a7a27;color:#fff;border-radius:8px;font-size:.82rem;
                font-weight:700;text-decoration:none;transition:background .18s;"
         onmouseover="this.style.background='#1b5e20'" onmouseout="this.style.background='#2a7a27'">
        <i class="bi bi-file-earmark-text-fill"></i> Quy định đăng bài
      </a>
      <a href="{{ route('about') }}"
         style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;
                background:#fff;border:1.5px solid #c8e6c9;color:#2a7a27;border-radius:8px;
                font-size:.82rem;font-weight:700;text-decoration:none;transition:all .18s;"
         onmouseover="this.style.background='#f3fbf2'" onmouseout="this.style.background='#fff'">
        <i class="bi bi-info-circle-fill"></i> Giới thiệu
      </a>
    </div>
  </div>

</div>
@endsection
