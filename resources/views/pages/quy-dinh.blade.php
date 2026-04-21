@extends('layouts.app')

@section('title', 'Quy định đăng bài — E-News AGU')
@section('meta_description', 'Quy định cộng tác, quy định bài viết và nhuận bút của Trang báo sinh viên điện tử e-News Trường Đại học An Giang.')

@push('styles')
<style>
/* ═══ QUY ĐỊNH PAGE ════════════════════════════════════════ */
.rules-page {
  max-width: 1100px;
  margin: 28px auto;
  padding: 0 16px;
  display: flex;
  gap: 26px;
  align-items: flex-start;
}
.rules-main  { flex: 1; min-width: 0; }
.rules-side  { width: 268px; flex-shrink: 0; position: sticky; top: 70px; }

/* ── Page hero ─────────────────────────────────────────────── */
.rules-hero {
  background: linear-gradient(135deg, #1b5e20 0%, #2a7a27 60%, #33691e 100%);
  border-radius: 12px;
  padding: 32px 36px;
  margin-bottom: 26px;
  display: flex;
  align-items: center;
  gap: 22px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 6px 28px rgba(27,94,32,.25);
}
.rules-hero::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse at 85% 30%, rgba(245,212,0,.10) 0%, transparent 55%);
  pointer-events: none;
}
.hero-icon-wrap {
  width: 70px; height: 70px; border-radius: 16px; flex-shrink: 0;
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(245,212,0,.4);
  display: flex; align-items: center; justify-content: center;
  font-size: 2rem;
}
.rules-hero h1 { font-size:1.55rem; font-weight:900; color:#fff; margin:0 0 6px; }
.rules-hero p  { font-size:.84rem; color:rgba(255,255,255,.78); margin:0; line-height:1.6; }
.hero-badge {
  display:inline-flex; align-items:center; gap:5px;
  background:rgba(245,212,0,.18); border:1px solid rgba(245,212,0,.35);
  color:#f5d400; font-size:.68rem; font-weight:700; padding:2px 12px;
  border-radius:20px; letter-spacing:.5px; text-transform:uppercase; margin-bottom:8px;
}

/* ── Section blocks ──────────────────────────────────────── */
.rule-section {
  margin-bottom: 24px;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #e4e4e4;
  background: #fff;
  box-shadow: 0 2px 10px rgba(0,0,0,.05);
}
.rule-section-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  background: linear-gradient(135deg, #1b5e20 0%, #2a7a27 100%);
  cursor: pointer;
  user-select: none;
}
.rule-section-num {
  width: 32px; height: 32px; border-radius: 8px;
  background: rgba(245,212,0,.2); border: 1px solid rgba(245,212,0,.4);
  color: #f5d400; font-size: .88rem; font-weight: 900;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.rule-section-title {
  flex: 1; font-size: .92rem; font-weight: 800; color: #fff; letter-spacing: .2px;
}
.rule-section-icon { color: rgba(255,255,255,.6); font-size: .85rem; transition: transform .25s; }
.rule-section-body { padding: 20px 22px; }

/* ── Rule items ──────────────────────────────────────────── */
.rule-item {
  display: flex;
  gap: 13px;
  align-items: flex-start;
  padding: 13px 0;
  border-bottom: 1px dashed #f0f0f0;
}
.rule-item:last-child { border-bottom: none; padding-bottom: 0; }
.rule-item-icon {
  width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: .95rem; margin-top: 1px;
}
.rule-item-body { flex: 1; min-width: 0; }
.rule-item-text {
  font-size: .875rem; color: #333; line-height: 1.72;
}
.rule-item-text strong { color: #1b5e20; }

/* Highlight box (important notes) */
.rule-highlight {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 14px 16px; border-radius: 8px; margin: 14px 0;
}
.rule-highlight.warn   { background: #fff8e1; border-left: 4px solid #f57f17; }
.rule-highlight.danger { background: #fff5f5; border-left: 4px solid #e53935; }
.rule-highlight.info   { background: #f3fbf2; border-left: 4px solid #2a7a27; }
.rule-highlight.blue   { background: #e8f0fe; border-left: 4px solid #1565c0; }
.rule-highlight i { flex-shrink: 0; margin-top: 2px; font-size: 1rem; }
.rule-highlight p { font-size: .83rem; color: #444; margin: 0; line-height: 1.7; }

/* Tag chips */
.tag-row { display:flex; flex-wrap:wrap; gap:7px; margin-top:12px; }
.tag-chip {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 12px; border-radius:20px; font-size:.72rem; font-weight:700;
}
.tag-green  { background:#e8f5e9; color:#2a7a27; }
.tag-red    { background:#ffebee; color:#c62828; }
.tag-orange { background:#fff8e1; color:#e65100; }
.tag-blue   { background:#e3f2fd; color:#1565c0; }

/* ── Sidebar ─────────────────────────────────────────────── */
.sw { border-radius:10px; overflow:hidden; margin-bottom:16px; }
.sw-head {
  background:linear-gradient(135deg,#1b5e20,#2a7a27); color:#fff;
  font-size:.78rem; font-weight:800; padding:10px 14px;
  display:flex; align-items:center; gap:7px;
  text-transform:uppercase; letter-spacing:.4px;
}
.sw-head i { color:#f5d400; }
.sw-body   { border:1px solid #ddd; border-top:none; border-radius:0 0 10px 10px; background:#fff; }

/* TOC sidebar */
.toc-item {
  display:flex; align-items:center; gap:10px;
  padding:10px 14px; border-bottom:1px solid #f5f5f5;
  font-size:.79rem; color:#444; text-decoration:none; transition:background .15s;
}
.toc-item:last-child { border-bottom:none; }
.toc-item:hover { background:#f3fbf2; color:#2a7a27; }
.toc-num {
  width:22px; height:22px; border-radius:6px; flex-shrink:0;
  background:#2a7a27; color:#fff;
  display:flex; align-items:center; justify-content:center;
  font-size:.65rem; font-weight:800;
}

@media (max-width:860px) {
  .rules-page { flex-direction:column; }
  .rules-side { width:100%; position:static; }
}

/* ── Dark Mode ────────────────────────────────────────── */
html.dark .rule-section { background: #18181b; border-color: #27272a; }
html.dark .rule-item { border-bottom-color: #27272a; }
html.dark .rule-item-text { color: #e4e4e7; }
html.dark .rule-item-text strong { color: #6ee7b7; }
html.dark .rule-highlight.info { background: rgba(42,122,39,0.1); border-left-color: #34d399; }
html.dark .rule-highlight.blue { background: rgba(21,101,192,0.1); border-left-color: #60a5fa; }
html.dark .rule-highlight.danger { background: rgba(229,57,53,0.1); border-left-color: #f87171; }
html.dark .rule-highlight.warn { background: rgba(245,127,23,0.1); border-left-color: #fbbf24; }
html.dark .rule-highlight p { color: #d1d5db; }
html.dark .tag-green { background: rgba(42,122,39,0.15); color: #6ee7b7; }
html.dark .tag-red { background: rgba(229,57,53,0.15); color: #f87171; }
html.dark .sw-body { background: #18181b; border-color: #27272a; }
html.dark .toc-item { color: #a1a1aa; border-bottom-color: #27272a; }
html.dark .toc-item:hover { background: #27272a; color: #a7f3d0; }
html.dark .sw-body div[style*="border-bottom"] { border-bottom-color: #27272a !important; color: #a1a1aa !important; }
html.dark .sw-body a:not(.toc-item) { border-bottom-color: #27272a !important; color: #d1d5db !important; }
html.dark .sw-body a:hover:not(.toc-item) { background: #27272a !important; color: #a7f3d0 !important; }
</style>
@endpush

@section('content')

<div class="rules-page">

  {{-- ════════════════════════════════════
       MAIN
  ════════════════════════════════════════ --}}
  <main class="rules-main">

    {{-- Hero --}}
    <div class="rules-hero">
      <div class="hero-icon-wrap">📋</div>
      <div>
        <div class="hero-badge"><i class="bi bi-shield-check-fill"></i> Chính sách tòa soạn</div>
        <h1>Quy định cộng tác</h1>
        <p>
          Các quy định về hình thức cộng tác, tiêu chuẩn bài viết và chế độ nhuận bút
          của Trang báo Sinh viên điện tử <strong style="color:#f5d400;">e-News AGU</strong>.
        </p>
      </div>
    </div>

    {{-- ══ SECTION 1: Hình thức cộng tác ════════════════════════ --}}
    <div class="rule-section" id="sec1">
      <div class="rule-section-header">
        <div class="rule-section-num">1</div>
        <span class="rule-section-title">
          <i class="bi bi-people-fill me-2"></i>Hình thức cộng tác
        </span>
        <i class="bi bi-chevron-down rule-section-icon"></i>
      </div>
      <div class="rule-section-body">

        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e8f5e9; color:#2a7a27;">
            <i class="bi bi-pencil-square"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              Cộng tác viên có thể đưa tin, gửi bài viết phản ánh những thông tin về
              hoạt động học tập, phong trào của sinh viên cũng như các hoạt động giảng dạy
              của giảng viên Trường Đại học An Giang.
              Chi tiết, vui lòng xem mục <a href="{{ route('about') }}" style="color:#2a7a27; font-weight:600;">Giới thiệu</a>.
            </p>
          </div>
        </div>

        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e3f2fd; color:#1565c0;">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              Bài viết đạt yêu cầu sẽ được <strong>Ban Biên tập đăng lên trang web</strong>
              <a href="mailto:enews@agu.edu.vn" style="color:#2a7a27;">enews@agu.edu.vn</a>.
              Mỗi bài viết được đăng sẽ được nhận <strong>nhuận bút</strong> theo quy định chung của Trường.
            </p>
            <div class="rule-highlight info" style="margin-top:10px;">
              <i class="bi bi-star-fill" style="color:#2a7a27;"></i>
              <p>
                Ngoài ra, cộng tác viên có bài viết được đăng trong học kỳ hoặc có đóng góp tích cực
                sẽ được <strong>cộng điểm rèn luyện</strong> theo Khung đánh giá kết quả điểm rèn luyện
                sinh viên hình thức đào tạo chính quy tại Trường Đại học An Giang.
              </p>
            </div>
          </div>
        </div>

        <div class="rule-item">
          <div class="rule-item-icon" style="background:#fff8e1; color:#f57f17;">
            <i class="bi bi-reply-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              Các bài viết gửi đến nếu <strong>không được đăng hoặc cần chỉnh sửa</strong>,
              Ban Biên tập sẽ phản hồi đến các tác giả. Nếu có ý kiến thắc mắc về bài viết đã gửi,
              bạn đọc xin vui lòng liên hệ qua email:
              <a href="mailto:enews@agu.edu.vn" style="color:#2a7a27; font-weight:600;">enews@agu.edu.vn</a>.
            </p>
          </div>
        </div>

      </div>
    </div>

    {{-- ══ SECTION 2: Quy định bài viết ══════════════════════════ --}}
    <div class="rule-section" id="sec2">
      <div class="rule-section-header">
        <div class="rule-section-num">2</div>
        <span class="rule-section-title">
          <i class="bi bi-file-earmark-text-fill me-2"></i>Quy định bài viết
        </span>
        <i class="bi bi-chevron-down rule-section-icon"></i>
      </div>
      <div class="rule-section-body">

        {{-- Định dạng kỹ thuật --}}
        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e8f5e9; color:#2a7a27;">
            <i class="bi bi-fonts"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              <strong>Định dạng kỹ thuật:</strong> Bài viết cần dùng bảng mã <strong>Unicode</strong>,
              dùng tiếng Việt có dấu (Unikey hoặc Vietkey).
              Không sử dụng Drop Cap, Word Art, chèn bảng, đóng khung vào bài viết.
            </p>
            <div class="tag-row">
              <span class="tag-chip tag-green"><i class="bi bi-check2"></i> Unicode UTF-8</span>
              <span class="tag-chip tag-green"><i class="bi bi-check2"></i> Tiếng Việt có dấu</span>
              <span class="tag-chip tag-red"><i class="bi bi-x-lg"></i> Drop Cap</span>
              <span class="tag-chip tag-red"><i class="bi bi-x-lg"></i> Word Art</span>
              <span class="tag-chip tag-red"><i class="bi bi-x-lg"></i> Chèn bảng</span>
            </div>
          </div>
        </div>

        {{-- Cách gửi bài --}}
        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e3f2fd; color:#1565c0;">
            <i class="bi bi-envelope-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              <strong>Cách gửi bài:</strong> Soạn bằng Microsoft Word hoặc Notepad thành một tập tin,
              sau đó gửi email <strong>dưới dạng tệp đính kèm</strong> đến Ban Biên tập tại:
            </p>
            <div class="rule-highlight blue" style="margin-top:10px;">
              <i class="bi bi-envelope-fill" style="color:#1565c0;"></i>
              <p>
                <strong>enews@agu.edu.vn</strong>
              </p>
            </div>
          </div>
        </div>

        {{-- Nội dung không được đăng --}}
        <div class="rule-item">
          <div class="rule-item-icon" style="background:#ffebee; color:#c62828;">
            <i class="bi bi-slash-circle-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text"><strong>e-News KHÔNG đăng các bài:</strong></p>
            <div class="rule-highlight danger" style="margin-top:8px;">
              <i class="bi bi-exclamation-triangle-fill" style="color:#e53935;"></i>
              <div>
                <div class="tag-row" style="gap:6px;">
                  <span class="tag-chip tag-red"><i class="bi bi-x-circle-fill"></i> Bài sưu tầm</span>
                  <span class="tag-chip tag-red"><i class="bi bi-x-circle-fill"></i> Sao chép của tác giả khác</span>
                  <span class="tag-chip tag-red"><i class="bi bi-x-circle-fill"></i> Viết bởi AI (ChatGPT, v.v.)</span>
                  <span class="tag-chip tag-red"><i class="bi bi-x-circle-fill"></i> Đã đăng tải trên báo/trang khác</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Trích dẫn --}}
        <div class="rule-item">
          <div class="rule-item-icon" style="background:#f3e5f5; color:#7b1fa2;">
            <i class="bi bi-quote"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              <strong>Trích dẫn & bản quyền:</strong> Nếu sử dụng nhiều tài liệu tham khảo
              (cả tiếng nước ngoài và tiếng Việt) cần <strong>trích dẫn rõ nguồn</strong> theo chuẩn khoa học.
              Tác giả phải chịu trách nhiệm về tính chính xác, trung thực và
              <strong>bản quyền nội dung</strong> của bài viết và hình ảnh kèm theo.
            </p>
          </div>
        </div>

        {{-- Hình ảnh --}}
        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e1f5fe; color:#0277bd;">
            <i class="bi bi-image-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text"><strong>Hình ảnh minh họa:</strong></p>
            <ul style="margin:8px 0 0 0; padding-left:0; list-style:none; display:flex; flex-direction:column; gap:6px;">
              @foreach([
                'Không chèn hình trực tiếp vào bài viết',
                'Phải có dẫn giải, chú thích ảnh, nguồn ảnh, tác giả ảnh',
                'Đính kèm file hình qua email hoặc gửi qua USB',
              ] as $img)
              <li style="display:flex; gap:7px; align-items:flex-start; font-size:.84rem; color:#444;">
                <i class="bi bi-camera-fill" style="color:#0277bd; flex-shrink:0; margin-top:3px;"></i>
                {{ $img }}
              </li>
              @endforeach
            </ul>
          </div>
        </div>

        {{-- Thông tin tác giả --}}
        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e8f5e9; color:#2a7a27;">
            <i class="bi bi-person-badge-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              <strong>Thông tin tác giả (cuối bài):</strong> Ghi rõ họ tên
              (có thể dùng bút danh nhưng phải kèm họ tên thật), đơn vị công tác,
              số điện thoại và email để Ban Biên tập liên lạc khi cần.
            </p>
          </div>
        </div>

        {{-- BBT biên tập --}}
        <div class="rule-item">
          <div class="rule-item-icon" style="background:#fff8e1; color:#f57f17;">
            <i class="bi bi-pencil-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              <strong>Quyền biên tập:</strong> Ban Biên tập e-News có thể biên tập bài viết
              để phù hợp với tiêu chí hoạt động của Trang báo.
              Các bài đăng trên <a href="http://enews.agu.edu.vn" target="_blank" style="color:#2a7a27;">enews.agu.edu.vn</a>
              đều <strong>thuộc quyền sở hữu của trang báo</strong> và Ban Biên tập được toàn quyền quản lý, sử dụng.
            </p>
          </div>
        </div>

      </div>
    </div>

    {{-- ══ SECTION 3: Quy định nhuận bút ════════════════════════ --}}
    <div class="rule-section" id="sec3">
      <div class="rule-section-header">
        <div class="rule-section-num">3</div>
        <span class="rule-section-title">
          <i class="bi bi-cash-coin me-2"></i>Quy định về nhuận bút
        </span>
        <i class="bi bi-chevron-down rule-section-icon"></i>
      </div>
      <div class="rule-section-body">

        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e8f5e9; color:#2a7a27;">
            <i class="bi bi-cash-stack"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              Các bài viết được đăng trên e-News sẽ được tính nhuận bút theo
              <strong>Quy chế chi tiêu nội bộ của Trường</strong> và được Ban Biên tập
              chi trả khi có thông báo mời nhận nhuận bút chính thức trên chuyên mục
              <em>e-News và bạn đọc</em> trên Trang báo.
            </p>
          </div>
        </div>

        <div class="rule-item">
          <div class="rule-item-icon" style="background:#e3f2fd; color:#1565c0;">
            <i class="bi bi-bell-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              Tác giả vui lòng thông báo với Ban Biên tập nếu không đến nhận được
              hoặc có thể <strong>nhờ người đến nhận thay</strong>.
            </p>
          </div>
        </div>

        <div class="rule-item">
          <div class="rule-item-icon" style="background:#fff8e1; color:#f57f17;">
            <i class="bi bi-clock-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              <strong>Thời hạn nhận nhuận bút:</strong>
            </p>
            <div class="rule-highlight warn" style="margin-top:8px;">
              <i class="bi bi-exclamation-circle-fill" style="color:#f57f17;"></i>
              <p>
                Quá <strong>3 tháng</strong> sau khi có thông báo, nếu tác giả không có phản hồi
                thì số tiền nhuận bút sẽ được e-News sử dụng cho các <strong>mục đích thiện nguyện</strong>.
              </p>
            </div>
          </div>
        </div>

        <div class="rule-item">
          <div class="rule-item-icon" style="background:#f3fbf2; color:#2a7a27;">
            <i class="bi bi-heart-fill"></i>
          </div>
          <div class="rule-item-body">
            <p class="rule-item-text">
              Trường hợp cộng tác viên <strong>chỉ muốn đăng bài, không muốn nhận nhuận bút</strong>,
              vui lòng ghi rõ trong email cộng tác gửi đến Ban Biên tập.
            </p>
          </div>
        </div>

      </div>
    </div>

    {{-- CTA gửi bài --}}
    <div style="margin-top:6px; padding:22px 24px; background:linear-gradient(135deg,#1b5e20,#2a7a27);
                border-radius:12px; display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:16px;">
      <div>
        <p style="font-size:.95rem; color:#fff; font-weight:700; margin:0 0 4px;">
          <i class="bi bi-pencil-fill" style="color:#f5d400;"></i>
          Sẵn sàng tham gia cộng tác?
        </p>
        <p style="font-size:.80rem; color:rgba(255,255,255,.75); margin:0;">
          Gửi bài hoặc liên hệ qua email tòa soạn để được hỗ trợ.
        </p>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="mailto:enews@agu.edu.vn"
           style="display:inline-flex; align-items:center; gap:7px; padding:10px 22px;
                  background:#f5d400; color:#1b5e20; border-radius:8px;
                  font-size:.84rem; font-weight:800; text-decoration:none; transition:opacity .18s;"
           onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
          <i class="bi bi-envelope-fill"></i> Gửi bài ngay
        </a>
        <a href="{{ route('login') }}"
           style="display:inline-flex; align-items:center; gap:7px; padding:10px 22px;
                  background:rgba(255,255,255,.15); color:#fff;
                  border:1.5px solid rgba(255,255,255,.35); border-radius:8px;
                  font-size:.84rem; font-weight:700; text-decoration:none; transition:background .18s;"
           onmouseover="this.style.background='rgba(255,255,255,.25)'"
           onmouseout="this.style.background='rgba(255,255,255,.15)'">
          <i class="bi bi-box-arrow-in-right"></i> Đăng nhập hệ thống
        </a>
      </div>
    </div>

  </main>

  {{-- ════════════════════════════════════
       SIDEBAR
  ════════════════════════════════════════ --}}
  <aside class="rules-side d-none d-lg-block">

    {{-- Mục lục --}}
    <div class="sw">
      <div class="sw-head"><i class="bi bi-list-ul"></i> Mục lục</div>
      <div class="sw-body">
        @foreach([
          ['num'=>'1','label'=>'Hình thức cộng tác','icon'=>'bi-people-fill','sec'=>'sec1'],
          ['num'=>'2','label'=>'Quy định bài viết','icon'=>'bi-file-earmark-text-fill','sec'=>'sec2'],
          ['num'=>'3','label'=>'Quy định nhuận bút','icon'=>'bi-cash-coin','sec'=>'sec3'],
        ] as $toc)
        <a href="#{{ $toc['sec'] }}" class="toc-item">
          <div class="toc-num">{{ $toc['num'] }}</div>
          <div style="flex:1;">
            <i class="bi {{ $toc['icon'] }}" style="color:#2a7a27; margin-right:5px;"></i>
            {{ $toc['label'] }}
          </div>
        </a>
        @endforeach
      </div>
    </div>

    {{-- Được đăng --}}
    <div class="sw">
      <div class="sw-head" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <i class="bi bi-check-circle-fill"></i> Được đăng
      </div>
      <div class="sw-body" style="padding:12px 14px;">
        @foreach([
          'Tin tức hoạt động sinh viên AGU',
          'Bài viết nghiên cứu khoa học',
          'Tường thuật sự kiện, phong trào',
          'Truyện ngắn, tản văn, thơ',
          'Góc nhìn cá nhân, bình luận',
          'Gương mặt sinh viên tiêu biểu',
        ] as $ok)
        <div style="display:flex; gap:7px; align-items:flex-start; font-size:.78rem; color:#333; padding:5px 0; border-bottom:1px solid #f5f5f5;">
          <i class="bi bi-check-circle-fill" style="color:#2a7a27; flex-shrink:0; margin-top:2px;"></i>
          {{ $ok }}
        </div>
        @endforeach
      </div>
    </div>

    {{-- Không được đăng --}}
    <div class="sw">
      <div class="sw-head" style="background:linear-gradient(135deg,#b71c1c,#e53935);">
        <i class="bi bi-x-circle-fill"></i> Không được đăng
      </div>
      <div class="sw-body" style="padding:12px 14px;">
        @foreach([
          'Bài sưu tầm, copy từ báo khác',
          'Viết bởi AI (ChatGPT, Gemini...)',
          'Đã đăng trên phương tiện khác',
          'Nội dung chưa được kiểm chứng',
        ] as $no)
        <div style="display:flex; gap:7px; align-items:flex-start; font-size:.78rem; color:#333; padding:5px 0; border-bottom:1px solid #f5f5f5;">
          <i class="bi bi-x-circle-fill" style="color:#e53935; flex-shrink:0; margin-top:2px;"></i>
          {{ $no }}
        </div>
        @endforeach
      </div>
    </div>

    {{-- Liên hệ nhanh --}}
    <div class="sw">
      <div class="sw-head"><i class="bi bi-envelope-fill"></i> Liên hệ toà soạn</div>
      <div class="sw-body" style="padding:14px;">
        <div style="font-size:.75rem; color:#666; margin-bottom:8px;">Gửi bài hoặc thắc mắc:</div>
        <a href="mailto:enews@agu.edu.vn"
           style="display:flex; align-items:center; gap:8px; padding:10px 14px;
                  background:#f3fbf2; border:1.5px solid #c8e6c9; border-radius:8px;
                  color:#2a7a27; font-size:.82rem; font-weight:700; text-decoration:none; transition:background .18s;"
           onmouseover="this.style.background='#e8f5e2'" onmouseout="this.style.background='#f3fbf2'">
          <i class="bi bi-envelope-fill" style="font-size:1rem;"></i>
          enews@agu.edu.vn
        </a>
        <div style="font-size:.72rem; color:#aaa; margin-top:10px; text-align:center;">
          Ban Biên tập sẽ phản hồi trong 3–5 ngày làm việc
        </div>
      </div>
    </div>

    {{-- Link nhanh --}}
    <div class="sw">
      <div class="sw-head"><i class="bi bi-arrow-right-circle-fill"></i> Xem thêm</div>
      <div class="sw-body" style="padding:6px 0;">
        @foreach([
          ['url'=>route('about'),  'icon'=>'bi-info-circle-fill','label'=>'Giới thiệu e-News'],
          ['url'=>route('contact'),'icon'=>'bi-geo-alt-fill',    'label'=>'Liên hệ BTV'],
          ['url'=>route('home'),   'icon'=>'bi-house-fill',      'label'=>'Trang chủ'],
        ] as $l)
        <a href="{{ $l['url'] }}"
           style="display:flex; align-items:center; gap:9px; padding:9px 14px;
                  font-size:.79rem; color:#333; text-decoration:none;
                  border-bottom:1px solid #f5f5f5; transition:background .15s;"
           onmouseover="this.style.background='#f3fbf2'; this.style.color='#2a7a27';"
           onmouseout="this.style.background=''; this.style.color='#333';">
          <i class="bi {{ $l['icon'] }}" style="color:#2a7a27;"></i>
          {{ $l['label'] }}
        </a>
        @endforeach
      </div>
    </div>

  </aside>

</div>
@endsection
