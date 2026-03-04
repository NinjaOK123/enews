@extends('layouts.app')

@section('title', 'Trang Chủ')

@section('content')

{{-- ═══ HERO + SIDEBAR ROW ═══ --}}
<div class="hero-area">

  {{-- HERO SLIDER --}}
  <div class="hero-slider-wrap">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">

        <div class="carousel-item active">
          <div class="hero-slide">
            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=700&q=80"
                 alt="Sinh viên ngành Triết học và Giáo dục chính trị nghiên cứu thực tế tại Đà Lạt">
            <div class="hero-badge-label">Tin nổi bật</div>
            <div class="hero-caption">
              <div class="hero-caption-text">Sinh viên ngành Triết học và Giáo dục chính trị nghiên cứu thực tế tại Đà Lạt</div>
            </div>
          </div>
        </div>

        <div class="carousel-item">
          <div class="hero-slide">
            <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=700&q=80"
                 alt="Hội thảo Khoa học Quốc tế về Nông nghiệp Bền vững tại ĐBSCL 2024">
            <div class="hero-badge-label">Tin nổi bật</div>
            <div class="hero-caption">
              <div class="hero-caption-text">Hội thảo Khoa học Quốc tế về Nông nghiệp Bền vững tại ĐBSCL 2024</div>
            </div>
          </div>
        </div>

        <div class="carousel-item">
          <div class="hero-slide">
            <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=700&q=80"
                 alt="Sinh viên AGU giành giải Nhất cuộc thi Khởi nghiệp Sáng tạo Quốc gia">
            <div class="hero-badge-label">Tin nổi bật</div>
            <div class="hero-caption">
              <div class="hero-caption-text">Sinh viên AGU giành giải Nhất cuộc thi Khởi nghiệp Sáng tạo Quốc gia năm 2024</div>
            </div>
          </div>
        </div>

        <div class="carousel-item">
          <div class="hero-slide">
            <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=700&q=80"
                 alt="Đoàn Trường Đại học An Giang triển khai các hoạt động cao điểm Tháng Thanh niên năm 2026">
            <div class="hero-badge-label">Tin nổi bật</div>
            <div class="hero-caption">
              <div class="hero-caption-text">Đoàn Trường Đại học An Giang triển khai các hoạt động cao điểm Tháng Thanh niên năm 2026</div>
            </div>
          </div>
        </div>

        <div class="carousel-item">
          <div class="hero-slide">
            <img src="https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=700&q=80"
                 alt="Nghiên cứu phát triển giống lúa thích ứng biến đổi khí hậu tại An Giang">
            <div class="hero-badge-label">Tin nổi bật</div>
            <div class="hero-caption">
              <div class="hero-caption-text">Nghiên cứu phát triển giống lúa thích ứng biến đổi khí hậu tại An Giang</div>
            </div>
          </div>
        </div>

      </div>

      {{-- Prev/Next Controls --}}
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"
              style="width:30px; background:rgba(0,0,0,.3); border-radius:0 3px 3px 0; left:0; top:auto; bottom:10px; height:30px; top:40%;">
        <span class="carousel-control-prev-icon" style="width:16px;height:16px;"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"
              style="width:30px; background:rgba(0,0,0,.3); border-radius:3px 0 0 3px; right:0; top:auto; height:30px; top:40%;">
        <span class="carousel-control-next-icon" style="width:16px;height:16px;"></span>
      </button>
    </div>

    {{-- Numbered dots (1 2 3 4 5) --}}
    <div class="hero-dots" id="heroDots">
      <span class="active" data-slide="0">1</span>
      <span data-slide="1">2</span>
      <span data-slide="2">3</span>
      <span data-slide="3">4</span>
      <span data-slide="4">5</span>
    </div>
  </div>

  {{-- SIDEBAR: Mới nhất --}}
  <div class="sidebar-latest">
    <div class="widget-title">Mới nhất</div>
    <div class="latest-list">
      @php
      $latestItems = [
        'Một mùi hương ký ức',
        'Tuyên dương gương người tốt việc tốt',
        'Tuyên dương gương người tốt việc tốt',
        'Tết và Me',
        'Đoàn Trường Đại học An Giang triển khai các hoạt động cao điểm Tháng Thanh niên năm 2026',
        'Sinh viên AGU tham gia hiến máu tình nguyện đợt 1 năm 2026',
        'Hội nghị khoa học sinh viên lần thứ 18 — AGU',
        'Câu chuyện về những người "giữ lửa" văn hóa dân tộc',
        'Ký ức mùa thi không thể quên của sinh viên AGU',
      ];
      @endphp
      @foreach($latestItems as $item)
        <a href="#" class="latest-item">{{ $item }}</a>
      @endforeach
    </div>
  </div>

</div>{{-- /hero-area --}}

<hr class="agu">

{{-- ═══ SECTION ROW 1: Bản tin AGU + SV với Câu lạc bộ ═══ --}}
<div class="sections-row">

  {{-- BẢN TIN AGU --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Bản tin AGU
      <a href="{{ route('category', 'ban-tin-agu') }}" class="more-link">» Xem thêm</a>
    </div>
    <div class="section-widget-body">
      {{-- Featured top article --}}
      <div class="featured-article">
        <a href="#">
          <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=500&q=80"
               alt="Lễ tốt nghiệp đại học chính quy năm 2024" loading="lazy">
        </a>
        <div class="featured-article-title">
          <a href="#">Trường Đại học An Giang tổ chức Lễ tốt nghiệp Đại học chính quy năm 2024</a>
        </div>
        <div class="featured-article-meta">04/03/2024 &nbsp;·&nbsp; Phòng Truyền thông</div>
      </div>
      {{-- List articles --}}
      @php $bantinItems = [
        ['img'=>'https://images.unsplash.com/photo-1560439514-4e9645039924?w=200&q=70','title'=>'Lễ ký kết hợp tác giữa AGU và Công ty FPT Software','date'=>'03/03/2024'],
        ['img'=>'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=200&q=70','title'=>'Thông báo lịch thi kết thúc học phần HK II năm học 2023–2024','date'=>'01/03/2024'],
        ['img'=>'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=200&q=70','title'=>'Kết quả xét chọn học bổng khuyến khích học tập HK I 2023–2024','date'=>'28/02/2024'],
      ]; @endphp
      @foreach($bantinItems as $item)
      <div class="news-list-item">
        <a href="#"><img src="{{ $item['img'] }}" class="news-list-thumb" alt="{{ $item['title'] }}" loading="lazy"></a>
        <div>
          <div class="news-list-title"><a href="#">{{ $item['title'] }}</a></div>
          <div class="news-list-meta">{{ $item['date'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- SV VỚI CÂU LẠC BỘ --}}
  <div class="section-widget">
    <div class="section-widget-title">
      SV với Câu lạc bộ
      <a href="{{ route('category', 'sv-clb') }}" class="more-link">» Xem thêm</a>
    </div>
    <div class="section-widget-body">
      <div class="featured-article">
        <a href="#">
          <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=500&q=80"
               alt="CLB Võ thuật AGU đoạt huy chương vàng" loading="lazy">
        </a>
        <div class="featured-article-title">
          <a href="#">CLB Võ thuật AGU đoạt Huy chương Vàng tại giải vô địch tỉnh An Giang năm 2024</a>
        </div>
        <div class="featured-article-meta">03/03/2024 &nbsp;·&nbsp; Đoàn Thanh niên</div>
      </div>
      @php $svclbItems = [
        ['img'=>'https://images.unsplash.com/photo-1609220136736-443140cfeaa8?w=200&q=70','title'=>'CLB Tình nguyện AGU tổ chức hiến máu nhân đạo đợt 1 năm 2024','date'=>'02/03/2024'],
        ['img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&q=70','title'=>'CLB Tiếng Anh AGU English câu lạc bộ kỷ niệm 5 năm thành lập','date'=>'28/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=200&q=70','title'=>'Sinh viên AGU tham gia Festival Cờ đỏ Sao vàng tỉnh An Giang','date'=>'25/02/2024'],
      ]; @endphp
      @foreach($svclbItems as $item)
      <div class="news-list-item">
        <a href="#"><img src="{{ $item['img'] }}" class="news-list-thumb" alt="{{ $item['title'] }}" loading="lazy"></a>
        <div>
          <div class="news-list-title"><a href="#">{{ $item['title'] }}</a></div>
          <div class="news-list-meta">{{ $item['date'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>{{-- /sections-row --}}

<hr class="agu">

{{-- ═══ SECTION ROW 2: Gương mặt AGU + eNews và Bạn đọc ═══ --}}
<div class="sections-row">

  {{-- GƯƠNG MẶT AGU --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Gương mặt AGU
      <a href="{{ route('category', 'guong-mat-agu') }}" class="more-link">» Xem thêm</a>
    </div>
    <div class="section-widget-body">
      <div class="featured-article">
        <a href="#">
          <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=500&q=80"
               alt="Gương mặt sinh viên tiêu biểu" loading="lazy">
        </a>
        <div class="featured-article-title">
          <a href="#">Nguyễn Thị Thanh Thảo — Nữ sinh viên xuất sắc vượt khó vươn lên trong học tập</a>
        </div>
        <div class="featured-article-meta">01/03/2024 &nbsp;·&nbsp; Ban Biên tập</div>
      </div>
      @php $guongmatItems = [
        ['img'=>'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=200&q=70','title'=>'Thầy Lê Minh Tú — 20 năm gắn bó với bục giảng Đại học An Giang','date'=>'27/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1531545514256-b1400bc00f31?w=200&q=70','title'=>'Chàng sinh viên ngành CNTT khởi nghiệp với ứng dụng quản lý nông nghiệp','date'=>'22/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=200&q=70','title'=>'Cô gái dân tộc Khmer nỗ lực trở thành kỹ sư nông nghiệp','date'=>'18/02/2024'],
      ]; @endphp
      @foreach($guongmatItems as $item)
      <div class="news-list-item">
        <a href="#"><img src="{{ $item['img'] }}" class="news-list-thumb" alt="{{ $item['title'] }}" loading="lazy"></a>
        <div>
          <div class="news-list-title"><a href="#">{{ $item['title'] }}</a></div>
          <div class="news-list-meta">{{ $item['date'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ENEWS VÀ BẠN ĐỌC --}}
  <div class="section-widget">
    <div class="section-widget-title">
      eNews và Bạn đọc
      <a href="{{ route('category', 'enews-ban-doc') }}" class="more-link">» Xem thêm</a>
    </div>
    <div class="section-widget-body">
      <div class="featured-article">
        <a href="#">
          <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=500&q=80"
               alt="Thư bạn đọc - Ký ức mùa thi" loading="lazy">
        </a>
        <div class="featured-article-title">
          <a href="#">"Ký ức mùa thi" — Cảm xúc của một sinh viên năm cuối nhìn lại hành trình</a>
        </div>
        <div class="featured-article-meta">02/03/2024 &nbsp;·&nbsp; Bạn đọc gửi</div>
      </div>
      @php $enewsItems = [
        ['img'=>'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=200&q=70','title'=>'Một mùi hương ký ức — Tản văn của sinh viên Khoa Văn hóa học','date'=>'28/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=200&q=70','title'=>'Tết và Mẹ — Câu chuyện xúc động từ sinh viên Khoa Sư phạm','date'=>'24/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&q=70','title'=>'Ngày về thăm thầy — Kỷ niệm không quên của lớp K20 Quản trị kinh doanh','date'=>'20/02/2024'],
      ]; @endphp
      @foreach($enewsItems as $item)
      <div class="news-list-item">
        <a href="#"><img src="{{ $item['img'] }}" class="news-list-thumb" alt="{{ $item['title'] }}" loading="lazy"></a>
        <div>
          <div class="news-list-title"><a href="#">{{ $item['title'] }}</a></div>
          <div class="news-list-meta">{{ $item['date'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>{{-- /sections-row --}}

<hr class="agu">

{{-- ═══ PHÓNG SỰ ẢNH (Photo strip) ═══ --}}
<div class="section-widget" style="margin-bottom:10px;">
  <div class="section-widget-title">
    Phóng sự Ảnh
    <a href="{{ route('category', 'phong-su-anh') }}" class="more-link">» Xem thêm</a>
  </div>
  <div class="section-widget-body">
    <div class="photo-strip">
      @php $photos = [
        ['src'=>'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=300&q=75','caption'=>'Lễ tốt nghiệp 2024'],
        ['src'=>'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=300&q=75','caption'=>'Hội nghị KH sinh viên'],
        ['src'=>'https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=300&q=75','caption'=>'Thực tế đồng ruộng'],
        ['src'=>'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=300&q=75','caption'=>'Ngày hội nghề nghiệp'],
        ['src'=>'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=300&q=75','caption'=>'Học bổng khuyến học'],
        ['src'=>'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=300&q=75','caption'=>'Thư viện AGU'],
        ['src'=>'https://images.unsplash.com/photo-1609220136736-443140cfeaa8?w=300&q=75','caption'=>'Hiến máu tình nguyện'],
        ['src'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&q=75','caption'=>'Tư vấn tuyển sinh 2024'],
      ]; @endphp
      @foreach($photos as $photo)
      <a href="#" class="photo-strip-item">
        <img src="{{ $photo['src'] }}" alt="{{ $photo['caption'] }}" loading="lazy">
        <div class="photo-strip-caption">{{ $photo['caption'] }}</div>
      </a>
      @endforeach
    </div>
  </div>
</div>

<hr class="agu">

{{-- ═══ SECTION ROW 3: Câu chuyện AGU + Khoa học với AGU ═══ --}}
<div class="sections-row">

  {{-- CÂU CHUYỆN AGU --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Câu chuyện AGU
      <a href="{{ route('category', 'cau-chuyen-agu') }}" class="more-link">» Xem thêm</a>
    </div>
    <div class="section-widget-body">
      @php $cauchuyenItems = [
        ['img'=>'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=200&q=70','title'=>'Câu chuyện về những người "giữ lửa" văn hóa dân tộc Khmer tại An Giang','date'=>'03/03/2024'],
        ['img'=>'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=200&q=70','title'=>'Hành trình khởi nghiệp từ trong trường đại học của cựu sinh viên AGU','date'=>'28/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=200&q=70','title'=>'Thầy giáo vùng cao — Hành trình mang chữ đến vùng biên giới','date'=>'22/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=200&q=70','title'=>'Câu chuyện bảo tồn giống cây ăn quả đặc sản tại ĐBSCL của nhóm SV','date'=>'18/02/2024'],
      ]; @endphp
      @foreach($cauchuyenItems as $item)
      <div class="news-list-item">
        <a href="#"><img src="{{ $item['img'] }}" class="news-list-thumb" alt="{{ $item['title'] }}" loading="lazy"></a>
        <div>
          <div class="news-list-title"><a href="#">{{ $item['title'] }}</a></div>
          <div class="news-list-meta">{{ $item['date'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- KHOA HỌC VỚI AGU --}}
  <div class="section-widget">
    <div class="section-widget-title">
      Khoa học với AGU
      <a href="{{ route('category', 'khoa-hoc-voi-agu') }}" class="more-link">» Xem thêm</a>
    </div>
    <div class="section-widget-body">
      @php $khoahocItems = [
        ['img'=>'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=200&q=70','title'=>'Ứng dụng AI trong dự báo lũ lụt tại ĐBSCL — Nghiên cứu của nhóm SV Khoa CNTT','date'=>'04/03/2024'],
        ['img'=>'https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=200&q=70','title'=>'Nghiên cứu giống lúa chịu mặn ứng phó biến đổi khí hậu vùng ven biển ĐBSCL','date'=>'01/03/2024'],
        ['img'=>'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?w=200&q=70','title'=>'Hệ thống quan trắc môi trường nước tự động tại các kênh rạch tỉnh An Giang','date'=>'25/02/2024'],
        ['img'=>'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=200&q=70','title'=>'Điều tra đa dạng sinh học vùng Tứ giác Long Xuyên: 340 loài ghi nhận mới','date'=>'20/02/2024'],
      ]; @endphp
      @foreach($khoahocItems as $item)
      <div class="news-list-item">
        <a href="#"><img src="{{ $item['img'] }}" class="news-list-thumb" alt="{{ $item['title'] }}" loading="lazy"></a>
        <div>
          <div class="news-list-title"><a href="#">{{ $item['title'] }}</a></div>
          <div class="news-list-meta">{{ $item['date'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>{{-- /sections-row --}}

<hr class="agu">

{{-- ═══ GÓC NHÌN + TẢN MẠN + LƯỚT WEB ═══ --}}
<div class="sections-row triple">

  {{-- GÓC NHÌN --}}
  <div class="section-widget">
    <div class="section-widget-title">Góc nhìn <a href="{{ route('category', 'goc-nhin') }}" class="more-link">» Thêm</a></div>
    <div class="section-widget-body">
      @php $gocnhinItems = [
        'Giáo dục đại học trong kỷ nguyên trí tuệ nhân tạo — cơ hội và thách thức',
        'Văn hóa ứng xử của sinh viên thời đại 4.0',
        'Môi trường học thuật lành mạnh — nền tảng cho sự phát triển bền vững',
        'Học đại học để làm gì? — Góc nhìn từ một sinh viên năm 4',
      ]; @endphp
      @foreach($gocnhinItems as $item)
      <div class="news-list-item" style="display:block; padding:6px 0; border-bottom:1px dashed var(--border);">
        <a href="#" style="font-size:.80rem; font-weight:600; color:var(--text); line-height:1.4; display:block;">
          » {{ $item }}
        </a>
      </div>
      @endforeach
    </div>
  </div>

  {{-- TẢN MẠN --}}
  <div class="section-widget">
    <div class="section-widget-title">Tản mạn <a href="{{ route('category', 'tan-man') }}" class="more-link">» Thêm</a></div>
    <div class="section-widget-body">
      @php $tanmanItems = [
        'Nhớ về ngôi trường cũ — Tản văn của cựu sinh viên AGU',
        'Chiều tà bên bờ sông Hậu — Những suy tư của người con đất An Giang',
        'Tết ở ký túc xá — Ký ức không thể quên của sinh viên xa nhà',
        'Mùa sen nở trên cánh đồng Tứ giác Long Xuyên',
      ]; @endphp
      @foreach($tanmanItems as $item)
      <div class="news-list-item" style="display:block; padding:6px 0; border-bottom:1px dashed var(--border);">
        <a href="#" style="font-size:.80rem; font-weight:600; color:var(--text); line-height:1.4; display:block;">
          » {{ $item }}
        </a>
      </div>
      @endforeach
    </div>
  </div>

  {{-- LƯỚT WEB CÙNG SV --}}
  <div class="section-widget">
    <div class="section-widget-title">Lướt web cùng SV <a href="{{ route('category', 'luot-web-cung-sv') }}" class="more-link">» Thêm</a></div>
    <div class="section-widget-body">
      @php $luotwebItems = [
        '10 kỹ năng mềm sinh viên cần chuẩn bị trước khi ra trường',
        'Ứng dụng ChatGPT hỗ trợ học tập: Góc nhìn từ sinh viên AGU',
        'Review sách: "Đắc Nhân Tâm" — Kim chỉ nam cho thế hệ trẻ',
        'Podcast học tiếng Anh tốt nhất cho sinh viên Việt Nam',
      ]; @endphp
      @foreach($luotwebItems as $item)
      <div class="news-list-item" style="display:block; padding:6px 0; border-bottom:1px dashed var(--border);">
        <a href="#" style="font-size:.80rem; font-weight:600; color:var(--text); line-height:1.4; display:block;">
          » {{ $item }}
        </a>
      </div>
      @endforeach
    </div>
  </div>

</div>{{-- /sections-row triple --}}

@endsection

@push('scripts')
<script>
// Numbered dot sync with Bootstrap carousel
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

  // Auto play
  new bootstrap.Carousel(carousel, { interval: 5000, ride: 'carousel' });
}
</script>
@endpush
