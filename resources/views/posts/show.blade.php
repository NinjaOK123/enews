{{-- posts/show.blade.php — AGU E-News Article Detail (VnExpress-inspired, Stitch-enhanced) --}}
@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->excerpt_short)

@push('styles')
{{-- Open Graph --}}
<meta property="og:title"       content="{{ $post->title }}">
<meta property="og:description" content="{{ $post->excerpt_short }}">
<meta property="og:image"       content="{{ $post->thumbnail_url }}">
<meta property="og:url"         content="{{ route('post.show', $post->slug) }}">
<meta property="og:type"        content="article">
<style>
/* ═══ ARTICLE PAGE LAYOUT ═══════════════════════════════════ */
.article-page {
  display: flex;
  gap: 28px;
  max-width: 1140px;
  margin: 22px auto;
  padding: 0 16px;
  align-items: flex-start;
}

/* ── Main column ─────────────────────────────────────────── */
.article-main { flex: 1; min-width: 0; }

/* ── Breadcrumb ──────────────────────────────────────────── */
.article-breadcrumb {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: .74rem;
  color: #999;
  margin-bottom: 12px;
  flex-wrap: wrap;
}
.article-breadcrumb a { color: var(--green,#2a7a27); text-decoration:none; }
.article-breadcrumb a:hover { text-decoration: underline; }
.article-breadcrumb .sep { color: #ccc; font-size: .65rem; }

/* ── Category pill ───────────────────────────────────────── */
.article-cat-pill {
  display: inline-block;
  background: var(--green,#2a7a27);
  color: #fff;
  font-size: .65rem;
  font-weight: 800;
  padding: 3px 14px;
  border-radius: 20px;
  letter-spacing: .7px;
  text-transform: uppercase;
  text-decoration: none;
  margin-bottom: 12px;
}

/* ── H1 ──────────────────────────────────────────────────── */
.article-title {
  font-size: 1.72rem;
  font-weight: 900;
  line-height: 1.35;
  color: #111;
  margin-bottom: 16px;
  letter-spacing: -.3px;
}

/* ── Lead / excerpt ──────────────────────────────────────── */
.article-lead {
  font-size: 1.02rem;
  font-style: italic;
  color: #444;
  border-left: 4px solid var(--green,#2a7a27);
  padding: 10px 16px;
  background: #f3fbf2;
  border-radius: 0 8px 8px 0;
  margin-bottom: 16px;
  line-height: 1.75;
}

/* ── Meta row ────────────────────────────────────────────── */
.article-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0;
  align-items: stretch;
  font-size: .78rem;
  color: #666;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
  margin-bottom: 20px;
  padding: 8px 0;
}
.article-meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 0 14px 0 0;
  margin-right: 14px;
  border-right: 1px solid #e5e5e5;
}
.article-meta-item:last-child { border-right: none; margin-right: 0; }
.article-meta-item i { color: var(--green,#2a7a27); font-size: .85rem; }
.article-meta-item strong { color: #333; }

/* ── Hero image ──────────────────────────────────────────── */
.article-hero {
  margin-bottom: 24px;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,.12);
}
.article-hero img {
  width: 100%;
  max-height: 460px;
  object-fit: cover;
  display: block;
}
.article-hero figcaption {
  background: #f8f8f8;
  padding: 8px 14px;
  font-size: .74rem;
  color: #888;
  font-style: italic;
  border-top: 1px solid #eee;
}

/* ── Article prose ───────────────────────────────────────── */
.article-body {
  font-size: 1.02rem;
  line-height: 1.9;
  color: #1c1c1c;
}
.article-body p { margin-bottom: 1.2rem; }
.article-body h2 {
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--green,#2a7a27);
  margin: 2rem 0 .9rem;
  padding-bottom: 6px;
  border-bottom: 2px solid var(--green-light,#e8f5e2);
}
.article-body h3 {
  font-size: 1.05rem;
  font-weight: 700;
  margin: 1.5rem 0 .7rem;
  color: #222;
}
.article-body img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 1.2rem auto;
  display: block;
  box-shadow: 0 2px 12px rgba(0,0,0,.10);
}
.article-body blockquote {
  border-left: 4px solid var(--orange,#FF6600);
  padding: 12px 20px;
  background: #fff8f4;
  border-radius: 0 8px 8px 0;
  margin: 1.4rem 0;
  font-style: italic;
  color: #555;
  font-size: .98rem;
}
.article-body ul, .article-body ol {
  padding-left: 1.4rem;
  margin-bottom: 1.2rem;
}
.article-body li { margin-bottom: .4rem; }

/* ── Tag row ─────────────────────────────────────────────── */
.article-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 7px;
  margin: 20px 0 0;
  padding-top: 14px;
  border-top: 1px solid #f0f0f0;
}
.article-tag {
  display: inline-block;
  padding: 3px 12px;
  border: 1px solid #d0e8cf;
  border-radius: 20px;
  font-size: .72rem;
  color: var(--green,#2a7a27);
  text-decoration: none;
  transition: all .18s;
}
.article-tag:hover {
  background: var(--green,#2a7a27);
  color: #fff;
  border-color: var(--green,#2a7a27);
}

/* ── Share bar ───────────────────────────────────────────── */
.share-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin: 22px 0;
  padding: 14px 18px;
  background: linear-gradient(135deg, #f3fbf2 0%, #fff 100%);
  border: 1px solid #d4edd2;
  border-radius: 10px;
}
.share-label {
  font-size: .75rem;
  font-weight: 800;
  color: #888;
  text-transform: uppercase;
  letter-spacing: .5px;
  margin-right: 4px;
}
.share-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  border-radius: 6px;
  font-size: .78rem;
  font-weight: 700;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: opacity .18s, transform .12s;
}
.share-btn:hover { opacity: .88; transform: translateY(-1px); color: #fff; }
.share-fb   { background: #1877F2; color: #fff; }
.share-tw   { background: #000;    color: #fff; }
.share-zalo { background: #0068FF; color: #fff; }
.share-copy { background: #6c757d; color: #fff; }

/* ═══ DIVIDER SECTION TITLE ═══════════════════════════════ */
.section-title-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(135deg, var(--green-dark,#1b5e20) 0%, var(--green,#2a7a27) 100%);
  color: #fff;
  font-size: .84rem;
  font-weight: 800;
  padding: 10px 16px;
  border-radius: 6px 6px 0 0;
  letter-spacing: .3px;
}
.section-title-bar i { color: #f5d400; }

/* ═══ RELATED ARTICLES ═════════════════════════════════════ */
.related-wrap {
  border: 1px solid #e4e4e4;
  border-top: none;
  border-radius: 0 0 8px 8px;
  padding: 16px;
  margin-bottom: 28px;
  background: #fff;
}
.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 14px;
}
.related-card {
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
  border: 1px solid #ebebeb;
  transition: box-shadow .2s, transform .2s;
  display: flex;
  flex-direction: column;
}
.related-card:hover {
  box-shadow: 0 6px 20px rgba(0,0,0,.12);
  transform: translateY(-2px);
}
.related-card-img {
  width: 100%;
  height: 128px;
  object-fit: cover;
  display: block;
}
.related-card-body { padding: 10px 12px 12px; flex: 1; }
.related-card-cat {
  font-size: .65rem;
  font-weight: 700;
  color: var(--green,#2a7a27);
  text-transform: uppercase;
  letter-spacing: .4px;
  margin-bottom: 4px;
}
.related-card-title {
  font-size: .80rem;
  font-weight: 700;
  color: #1a1a1a;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-decoration: none;
}
.related-card-title:hover { color: var(--green,#2a7a27); }
.related-card-date { font-size: .68rem; color: #aaa; margin-top: 6px; }

/* ═══ COMMENTS ══════════════════════════════════════════════ */
.comments-wrap {
  border: 1px solid #e4e4e4;
  border-top: none;
  border-radius: 0 0 8px 8px;
  padding: 18px;
  background: #fff;
}
.comment-item {
  display: flex;
  gap: 12px;
  padding: 14px 0;
  border-bottom: 1px solid #f5f5f5;
}
.comment-item:last-of-type { border-bottom: none; }
.comment-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: .9rem; color: #fff;
  flex-shrink: 0;
  background: var(--green,#2a7a27);
}
.comment-bubble {
  background: #f7f8fa;
  border-radius: 0 10px 10px 10px;
  padding: 10px 14px;
  flex: 1;
}
.comment-bubble-name { font-weight: 700; font-size: .82rem; color: #222; }
.comment-bubble-text { font-size: .88rem; color: #333; margin-top: 4px; line-height: 1.6; }
.comment-bubble-time { font-size: .70rem; color: #bbb; margin-top: 5px; }
.comment-form-wrap   { margin-top: 20px; padding-top: 18px; border-top: 1px solid #f0f0f0; }
.comment-input-row   { display: flex; gap: 10px; align-items: flex-start; }
.comment-textarea {
  flex: 1;
  border: 1.5px solid #ddd;
  border-radius: 8px;
  padding: 12px 14px;
  font-size: .88rem;
  font-family: inherit;
  resize: vertical;
  min-height: 100px;
  outline: none;
  transition: border-color .18s;
}
.comment-textarea:focus { border-color: var(--green,#2a7a27); }
.btn-submit-comment {
  background: var(--green,#2a7a27);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: .82rem;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  transition: background .18s;
  display: flex; align-items: center; gap: 6px;
}
.btn-submit-comment:hover { background: #1b5e20; }

/* ═══ SIDEBAR ═══════════════════════════════════════════════ */
.article-sidebar {
  width: 270px;
  flex-shrink: 0;
  position: sticky;
  top: 65px;
  align-self: flex-start;
}
.sidebar-widget { margin-bottom: 18px; }
.sidebar-widget-title {
  background: linear-gradient(135deg, #1b5e20 0%, #2a7a27 100%);
  color: #fff;
  font-size: .78rem;
  font-weight: 800;
  padding: 9px 14px;
  border-radius: 6px 6px 0 0;
  display: flex;
  align-items: center;
  gap: 6px;
  text-transform: uppercase;
  letter-spacing: .4px;
}
.sidebar-widget-title i { color: #f5d400; font-size: .9rem; }
.sidebar-widget-body {
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 6px 6px;
  background: #fff;
  overflow: hidden;
}

/* Info box */
.info-box { padding: 14px 16px; background: #f3fbf2; }
.info-row {
  display: flex;
  align-items: flex-start;
  gap: 9px;
  font-size: .78rem;
  color: #555;
  padding: 6px 0;
  border-bottom: 1px solid #e8f5e2;
}
.info-row:last-child { border-bottom: none; }
.info-row i { color: var(--green,#2a7a27); margin-top: 2px; font-size: .85rem; flex-shrink: 0; }
.info-row strong { color: #333; }

/* Recent list */
.recent-item {
  display: flex;
  gap: 10px;
  padding: 10px 12px;
  border-bottom: 1px solid #f5f5f5;
  text-decoration: none;
  color: inherit;
  transition: background .15s;
}
.recent-item:last-child { border-bottom: none; }
.recent-item:hover { background: #f7fbf7; }
.recent-item-img {
  width: 60px; height: 44px;
  object-fit: cover;
  border-radius: 5px;
  flex-shrink: 0;
}
.recent-item-title {
  font-size: .76rem;
  font-weight: 600;
  color: #222;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.recent-item:hover .recent-item-title { color: var(--green,#2a7a27); }
.recent-item-date { font-size: .67rem; color: #bbb; margin-top: 4px; }

/* Category list */
.cat-list-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 14px;
  font-size: .78rem;
  color: #444;
  text-decoration: none;
  border-bottom: 1px solid #f5f5f5;
  transition: background .15s;
}
.cat-list-item:last-child { border-bottom: none; }
.cat-list-item:hover { background: #f3fbf2; color: var(--green,#2a7a27); }
.cat-list-count {
  background: var(--green-light,#e8f5e2);
  color: var(--green,#2a7a27);
  font-size: .65rem;
  font-weight: 700;
  padding: 1px 8px;
  border-radius: 20px;
}

/* ── Progress reading bar ─────────────────────────────────── */
#readProgress {
  position: fixed;
  top: 0; left: 0;
  height: 3px;
  width: 0%;
  background: linear-gradient(to right, var(--green,#2a7a27), var(--orange,#FF6600));
  z-index: 9999;
  transition: width .1s;
}

@media (max-width: 900px) {
  .article-page    { flex-direction: column; }
  .article-sidebar { width: 100%; position: static; }
  .article-title   { font-size: 1.35rem; }
}
</style>
@endpush

@section('content')

{{-- Reading progress bar --}}
<div id="readProgress"></div>

<div class="article-page">

  {{-- ════════════════════════════════════════════════
       MAIN CONTENT COLUMN
  ════════════════════════════════════════════════════ --}}
  <main class="article-main">

    {{-- Breadcrumb --}}
    <nav class="article-breadcrumb">
      <a href="{{ route('home') }}"><i class="bi bi-house-fill"></i> Trang chủ</a>
      @if($post->category)
        <i class="bi bi-chevron-right sep"></i>
        <a href="{{ route('category', $post->category->slug) }}">{{ $post->category->name }}</a>
      @endif
      <i class="bi bi-chevron-right sep"></i>
      <span>{{ Str::limit($post->title, 55) }}</span>
    </nav>

    {{-- Category pill --}}
    @if($post->category)
    <a href="{{ route('category', $post->category->slug) }}" class="article-cat-pill">
      {{ $post->category->name }}
    </a>
    @endif

    {{-- H1 Title --}}
    <h1 class="article-title">{{ $post->title }}</h1>

    {{-- Lead / excerpt --}}
    @if($post->excerpt)
    <p class="article-lead">{{ $post->excerpt }}</p>
    @endif

    {{-- Meta row --}}
    <div class="article-meta">
      <div class="article-meta-item">
        <i class="bi bi-person-fill"></i>
        <strong>{{ $post->author->name ?? 'Ban Biên tập' }}</strong>
      </div>
      <div class="article-meta-item">
        <i class="bi bi-calendar3"></i>
        {{ $post->published_at?->locale('vi')->isoFormat('dddd, D/M/YYYY') }}
      </div>
      <div class="article-meta-item">
        <i class="bi bi-eye-fill"></i>
        {{ number_format($post->view_count) }} lượt xem
      </div>
      <div class="article-meta-item">
        <i class="bi bi-chat-dots-fill"></i>
        {{ $post->comments->count() }} bình luận
      </div>
    </div>

    {{-- Hero image --}}
    <figure class="article-hero">
      <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" loading="eager">
      <figcaption>
        <i class="bi bi-camera"></i>
        Ảnh: {{ $post->author->name ?? 'Ban Biên tập' }} — {{ $post->published_at?->format('d/m/Y') }}
      </figcaption>
    </figure>

    {{-- Article body --}}
    <article class="article-body" id="articleBody">
      {!! $post->content !!}
    </article>

    {{-- Tags --}}
    @if($post->category)
    <div class="article-tags">
      <span style="font-size:.72rem; font-weight:700; color:#999; text-transform:uppercase; letter-spacing:.4px; margin-right:4px;">
        <i class="bi bi-tag-fill"></i> Chuyên mục:
      </span>
      <a href="{{ route('category', $post->category->slug) }}" class="article-tag">
        {{ $post->category->name }}
      </a>
    </div>
    @endif

    {{-- Share bar --}}
    @php $shareUrl = urlencode(route('post.show', $post->slug)); @endphp
    <div class="share-bar">
      <span class="share-label"><i class="bi bi-share-fill me-1"></i> Chia sẻ:</span>
      <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
         target="_blank" rel="noopener" class="share-btn share-fb">
        <i class="bi bi-facebook"></i> Facebook
      </a>
      <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ $shareUrl }}"
         target="_blank" rel="noopener" class="share-btn share-tw">
        <i class="bi bi-twitter-x"></i> Twitter
      </a>
      <a href="https://zalo.me/share/url?url={{ $shareUrl }}&title={{ urlencode($post->title) }}"
         target="_blank" rel="noopener" class="share-btn share-zalo">
        <i class="bi bi-chat-fill"></i> Zalo
      </a>
      <button onclick="copyLink()" id="copyBtn" class="share-btn share-copy">
        <i class="bi bi-link-45deg"></i> Sao chép
      </button>
      <span id="copyFeedback" style="display:none; font-size:.75rem; color:var(--green,#2a7a27); font-weight:700;">
        <i class="bi bi-check2-circle"></i> Đã sao chép!
      </span>
    </div>

    {{-- ══ Related Articles ══════════════════════════════════ --}}
    @if($relatedPosts->isNotEmpty())
    <div style="margin-bottom: 28px;">
      <div class="section-title-bar">
        <i class="bi bi-grid-3x3-gap-fill"></i>
        BÀI VIẾT LIÊN QUAN
      </div>
      <div class="related-wrap" style="padding-bottom:6px;">
        <div class="related-grid">
          @foreach($relatedPosts as $rel)
          <div class="related-card">
            <a href="{{ route('post.show', $rel->slug) }}">
              <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}" class="related-card-img" loading="lazy">
            </a>
            <div class="related-card-body">
              @if($rel->category)
              <div class="related-card-cat">{{ $rel->category->name }}</div>
              @endif
              <a href="{{ route('post.show', $rel->slug) }}" class="related-card-title">
                {{ $rel->title }}
              </a>
              <div class="related-card-date">
                <i class="bi bi-calendar3"></i>
                {{ $rel->published_at?->format('d/m/Y') }}
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    {{-- ══ Comments ══════════════════════════════════════════ --}}
    <div id="comments">
      <div class="section-title-bar">
        <i class="bi bi-chat-dots-fill"></i>
        BÌNH LUẬN ({{ $post->comments->count() }})
      </div>
      <div class="comments-wrap">

        {{-- Flash success --}}
        @if(session('success'))
        <div style="background:#e8f5e9; border-left:4px solid #2a7a27; padding:10px 14px; border-radius:0 6px 6px 0;
                    font-size:.82rem; color:#2a7a27; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        @endif

        {{-- Comment list --}}
        @forelse($post->comments as $comment)
        <div class="comment-item">
          <div class="comment-avatar"
               style="background: hsl({{ (ord($comment->user->name[0] ?? 'U') * 13) % 360 }}, 60%, 40%);">
            {{ strtoupper(mb_substr($comment->user->name ?? 'U', 0, 1)) }}
          </div>
          <div style="flex:1; min-width:0;">
            <div class="comment-bubble">
              <span class="comment-bubble-name">{{ $comment->user->name ?? 'Ẩn danh' }}</span>
              <p class="comment-bubble-text">{{ $comment->content }}</p>
            </div>
            <div class="comment-bubble-time">
              <i class="bi bi-clock"></i>
              {{ $comment->created_at->locale('vi')->diffForHumans() }}
              &nbsp;·&nbsp; {{ $comment->created_at->format('H:i d/m/Y') }}
            </div>
          </div>
        </div>
        @empty
        <div style="text-align:center; padding:28px 0; color:#ccc;">
          <i class="bi bi-chat-square" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
          <p style="font-size:.88rem;">Chưa có bình luận. Hãy là người đầu tiên!</p>
        </div>
        @endforelse

        {{-- Comment form --}}
        <div class="comment-form-wrap">
          @auth
          <p style="font-size:.78rem; font-weight:700; color:#555; margin-bottom:12px;">
            <i class="bi bi-pencil-fill" style="color:var(--green,#2a7a27);"></i>
            Bình luận với tư cách <strong style="color:var(--green,#2a7a27);">{{ auth()->user()->name }}</strong>
          </p>
          <form action="{{ route('post.comment', $post->slug) }}" method="POST">
            @csrf
            <div class="comment-input-row">
              <div class="comment-avatar" style="margin-top:2px; background: hsl({{ (ord(auth()->user()->name[0]) * 13) % 360 }}, 60%, 40%);">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
              </div>
              <div style="flex:1;">
                <textarea name="content" class="comment-textarea"
                          placeholder="Nhập bình luận của bạn (tối thiểu 5 ký tự)..."
                          maxlength="1000" required>{{ old('content') }}</textarea>
                @error('content')
                <p style="color:#e53935; font-size:.75rem; margin-top:4px;">
                  <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </p>
                @enderror
                <div style="display:flex; justify-content:flex-end; margin-top:8px;">
                  <button type="submit" class="btn-submit-comment">
                    <i class="bi bi-send-fill"></i> Gửi bình luận
                  </button>
                </div>
              </div>
            </div>
          </form>
          @else
          <div style="text-align:center; padding:18px; background:#f8f9fa; border-radius:8px;">
            <p style="font-size:.85rem; color:#666; margin-bottom:12px;">
              <i class="bi bi-lock-fill" style="color:#f5d400;"></i>
              Vui lòng đăng nhập để tham gia bình luận.
            </p>
            <a href="{{ route('login') }}"
               style="display:inline-flex; align-items:center; gap:6px; padding:9px 26px;
                      background:var(--green,#2a7a27); color:#fff; border-radius:8px;
                      font-size:.82rem; font-weight:700; text-decoration:none; transition:background .18s;"
               onmouseover="this.style.background='#1b5e20'" onmouseout="this.style.background='var(--green,#2a7a27)'">
              <i class="bi bi-person-fill"></i> Đăng nhập
            </a>
          </div>
          @endauth
        </div>

      </div>{{-- /comments-wrap --}}
    </div>{{-- /comments --}}

  </main>{{-- /article-main --}}

  {{-- ════════════════════════════════════════════════
       SIDEBAR
  ════════════════════════════════════════════════════ --}}
  <aside class="article-sidebar d-none d-xl-block">

    {{-- Widget 1: Thông tin bài viết --}}
    <div class="sidebar-widget">
      <div class="sidebar-widget-title">
        <i class="bi bi-info-circle-fill"></i> Thông tin bài viết
      </div>
      <div class="sidebar-widget-body">
        <div class="info-box">
          <div class="info-row">
            <i class="bi bi-person-fill"></i>
            <div>
              <strong>Tác giả</strong><br>
              <span>{{ $post->author->name ?? 'Ban Biên tập' }}</span>
            </div>
          </div>
          <div class="info-row">
            <i class="bi bi-folder-fill"></i>
            <div>
              <strong>Chuyên mục</strong><br>
              @if($post->category)
              <a href="{{ route('category', $post->category->slug) }}"
                 style="color:var(--green,#2a7a27); text-decoration:none; font-size:.8rem;">
                {{ $post->category->name }}
              </a>
              @endif
            </div>
          </div>
          <div class="info-row">
            <i class="bi bi-calendar-event-fill"></i>
            <div>
              <strong>Ngày đăng</strong><br>
              <span>{{ $post->published_at?->format('H:i · d/m/Y') }}</span>
            </div>
          </div>
          <div class="info-row">
            <i class="bi bi-eye-fill"></i>
            <div>
              <strong>Lượt xem</strong>
              <span style="display:block; font-size:1rem; font-weight:800; color:var(--green,#2a7a27);">
                {{ number_format($post->view_count) }}
              </span>
            </div>
          </div>
          <div class="info-row">
            <i class="bi bi-chat-dots-fill"></i>
            <div>
              <strong>Bình luận</strong>
              <span style="display:block; font-size:1rem; font-weight:800; color:var(--green,#2a7a27);">
                {{ $post->comments->count() }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Widget 2: Bài viết mới nhất --}}
    <div class="sidebar-widget">
      <div class="sidebar-widget-title">
        <i class="bi bi-lightning-fill"></i> Mới nhất
      </div>
      <div class="sidebar-widget-body">
        @forelse($recentPosts as $rp)
        <a href="{{ route('post.show', $rp->slug) }}" class="recent-item">
          <img src="{{ $rp->thumbnail_url }}" alt="{{ $rp->title }}" class="recent-item-img" loading="lazy">
          <div>
            <div class="recent-item-title">{{ $rp->title }}</div>
            <div class="recent-item-date">
              <i class="bi bi-calendar3"></i>
              {{ $rp->published_at?->format('d/m/Y') }}
            </div>
          </div>
        </a>
        @empty
        <p style="padding:12px; font-size:.8rem; color:#aaa; text-align:center;">Chưa có bài viết.</p>
        @endforelse
      </div>
    </div>

    {{-- Widget 3: Chuyên mục --}}
    <div class="sidebar-widget">
      <div class="sidebar-widget-title">
        <i class="bi bi-grid-3x3-gap-fill"></i> Chuyên mục
      </div>
      <div class="sidebar-widget-body">
        @foreach($allCategories as $cat)
        <a href="{{ route('category', $cat->slug) }}" class="cat-list-item">
          <span>{{ $cat->name }}</span>
          <span class="cat-list-count">{{ $cat->posts_count }}</span>
        </a>
        @endforeach
      </div>
    </div>

    {{-- Back to category --}}
    @if($post->category)
    <a href="{{ route('category', $post->category->slug) }}"
       style="display:flex; align-items:center; justify-content:center; gap:6px; padding:10px;
              border:1.5px solid var(--green,#2a7a27); border-radius:8px;
              color:var(--green,#2a7a27); font-size:.78rem; font-weight:700; text-decoration:none;
              transition:all .18s;"
       onmouseover="this.style.background='var(--green,#2a7a27)'; this.style.color='#fff';"
       onmouseout="this.style.background=''; this.style.color='var(--green,#2a7a27)';">
      <i class="bi bi-arrow-left"></i> Về {{ $post->category->name }}
    </a>
    @endif

  </aside>

</div>{{-- /article-page --}}

@endsection

@push('scripts')
<script>
// ── Reading progress bar ───────────────────────────────────
(function () {
  const bar  = document.getElementById('readProgress');
  const body = document.getElementById('articleBody');
  if (!bar || !body) return;
  window.addEventListener('scroll', function () {
    const rect  = body.getBoundingClientRect();
    const total = body.offsetHeight - window.innerHeight + rect.top + window.scrollY;
    const pct   = Math.min(100, Math.max(0, (window.scrollY - rect.top - window.scrollY + body.offsetTop) / total * 100));
    const scrolled = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    bar.style.width = (Math.min(scrolled / docHeight, 1) * 100) + '%';
  });
})();

// ── Copy link ─────────────────────────────────────────────
function copyLink() {
  const url = '{{ route('post.show', $post->slug) }}';
  const btn = document.getElementById('copyBtn');
  const fb  = document.getElementById('copyFeedback');
  navigator.clipboard.writeText(url).then(() => {
    btn.style.display = 'none';
    fb.style.display  = 'inline-flex';
    setTimeout(() => { btn.style.display = ''; fb.style.display = 'none'; }, 2500);
  }).catch(() => {
    const el = document.createElement('textarea');
    el.value = url;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
  });
}
</script>
@endpush
