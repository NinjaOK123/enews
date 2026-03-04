{{--
    posts/show.blade.php
    Chi tiết bài viết — AGU E-News
--}}
@extends('layouts.app')

@section('title', $post->title)

@section('meta_description', $post->excerpt_short)

@push('styles')
<style>
/* ── Article body prose ──────────────────────────── */
.article-body {
  font-size: 1rem;
  line-height: 1.85;
  color: #1a1a1a;
}
.article-body h2, .article-body h3 {
  font-weight: 800;
  color: var(--green, #2a7a27);
  margin: 1.6rem 0 .8rem;
}
.article-body p  { margin-bottom: 1.1rem; }
.article-body img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 1rem auto;
  display: block;
}
.article-body blockquote {
  border-left: 4px solid var(--green, #2a7a27);
  padding: 10px 18px;
  background: #f4fbf4;
  border-radius: 0 8px 8px 0;
  color: #444;
  font-style: italic;
  margin: 1.2rem 0;
}

/* ── Share buttons ───────────────────────────────── */
.share-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  border-radius: 6px;
  font-size: .80rem;
  font-weight: 700;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: opacity .18s;
}
.share-btn:hover { opacity: .85; color: #fff; }
.share-fb   { background: #1877F2; color: #fff; }
.share-tw   { background: #000;    color: #fff; }
.share-zalo { background: #0068FF; color: #fff; }
.share-copy { background: #6c757d; color: #fff; }

/* ── Related posts ───────────────────────────────── */
.related-card {
  border-radius: 8px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 1px 6px rgba(0,0,0,.07);
  transition: box-shadow .2s;
  display: flex;
  flex-direction: column;
}
.related-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.13); }
.related-card img   { width:100%; height:130px; object-fit:cover; }
.related-card-body  { padding: 10px 12px 12px; flex:1; }
.related-card-title {
  font-size: .82rem;
  font-weight: 700;
  color: #1a1a1a;
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.related-card-title:hover { color: var(--green,#2a7a27); }

/* ── Comments ────────────────────────────────────── */
.comment-item {
  padding: 14px 0;
  border-bottom: 1px solid #f0f0f0;
}
.comment-item:last-child { border-bottom: none; }
.comment-avatar {
  width: 38px; height: 38px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}
.comment-avatar-placeholder {
  width: 38px; height: 38px;
  border-radius: 50%;
  background: var(--green,#2a7a27);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: .88rem;
  flex-shrink: 0;
}
.comment-bubble {
  background: #f8f9fa;
  border-radius: 0 10px 10px 10px;
  padding: 10px 14px;
  flex: 1;
  font-size: .85rem;
  line-height: 1.6;
  color: #333;
}
.comment-meta {
  font-size: .72rem;
  color: #aaa;
  margin-top: 4px;
}

/* ── Comment form ────────────────────────────────── */
.comment-textarea {
  width: 100%;
  border: 1.5px solid #d8d8d8;
  border-radius: 8px;
  padding: 12px 14px;
  font-size: .88rem;
  font-family: inherit;
  resize: vertical;
  min-height: 110px;
  outline: none;
  transition: border-color .18s;
}
.comment-textarea:focus { border-color: var(--green,#2a7a27); }
</style>
@endpush

@section('content')

{{-- ═══ Open Graph (SEO meta) ═══ --}}
@push('styles')
<meta property="og:title"       content="{{ $post->title }}">
<meta property="og:description" content="{{ $post->excerpt_short }}">
<meta property="og:image"       content="{{ $post->thumbnail_url }}">
<meta property="og:url"         content="{{ route('post.show', $post->slug) }}">
<meta property="og:type"        content="article">
@endpush

<div style="max-width:1080px; margin:0 auto; padding:20px 16px; display:flex; gap:28px; align-items:flex-start;">

  {{-- ═══════════════════════════════════════════════════
       MAIN COLUMN — Article content
  ═══════════════════════════════════════════════════ --}}
  <main style="flex:1; min-width:0;">

    {{-- ── Breadcrumb ──────────────────────────────── --}}
    <nav style="font-size:.76rem; color:#999; margin-bottom:14px; display:flex; align-items:center; gap:5px; flex-wrap:wrap;">
      <a href="{{ route('home') }}" style="color:var(--green,#2a7a27); text-decoration:none;">Trang chủ</a>
      <i class="bi bi-chevron-right" style="font-size:.65rem; color:#ccc;"></i>
      @if($post->category)
      <a href="{{ route('category', $post->category->slug) }}"
         style="color:var(--green,#2a7a27); text-decoration:none;">{{ $post->category->name }}</a>
      <i class="bi bi-chevron-right" style="font-size:.65rem; color:#ccc;"></i>
      @endif
      <span style="color:#888; max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
        {{ Str::limit($post->title, 60) }}
      </span>
    </nav>

    {{-- ── Category badge ──────────────────────────── --}}
    @if($post->category)
    <a href="{{ route('category', $post->category->slug) }}"
       style="display:inline-block; background:var(--green,#2a7a27); color:#fff; font-size:.68rem; font-weight:800;
              padding:3px 12px; border-radius:20px; letter-spacing:.6px; text-transform:uppercase; text-decoration:none; margin-bottom:10px;">
      {{ $post->category->name }}
    </a>
    @endif

    {{-- ── H1: Title ────────────────────────────────── --}}
    <h1 style="font-size:1.65rem; font-weight:900; line-height:1.35; color:#111; margin-bottom:14px;">
      {{ $post->title }}
    </h1>

    {{-- ── Excerpt / lead ──────────────────────────── --}}
    @if($post->excerpt)
    <p style="font-size:1rem; font-style:italic; color:#555; border-left:3px solid var(--green,#2a7a27);
              padding:8px 14px; background:#f4fbf4; border-radius:0 6px 6px 0; margin-bottom:14px; line-height:1.7;">
      {{ $post->excerpt }}
    </p>
    @endif

    {{-- ── Post meta ────────────────────────────────── --}}
    <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; font-size:.78rem; color:#888; margin-bottom:18px;
                padding: 10px 0; border-top:1px solid #f0f0f0; border-bottom:1px solid #f0f0f0;">
      {{-- Author --}}
      <span style="display:flex; align-items:center; gap:5px;">
        <i class="bi bi-person-fill" style="color:var(--green,#2a7a27);"></i>
        <strong style="color:#444;">{{ $post->author->name ?? 'Ban Biên tập' }}</strong>
      </span>
      {{-- Date --}}
      <span style="display:flex; align-items:center; gap:5px;">
        <i class="bi bi-calendar3" style="color:var(--green,#2a7a27);"></i>
        {{ $post->published_at?->locale('vi')->isoFormat('dddd, D/M/YYYY') ?? '-' }}
      </span>
      {{-- Views --}}
      <span style="display:flex; align-items:center; gap:5px;">
        <i class="bi bi-eye" style="color:var(--green,#2a7a27);"></i>
        {{ number_format($post->view_count) }} lượt xem
      </span>
      {{-- Comment count --}}
      <span style="display:flex; align-items:center; gap:5px;">
        <i class="bi bi-chat-dots" style="color:var(--green,#2a7a27);"></i>
        {{ $post->comments->count() }} bình luận
      </span>
    </div>

    {{-- ── Thumbnail ────────────────────────────────── --}}
    <figure style="margin:0 0 20px; border-radius:10px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.10);">
      <img src="{{ $post->thumbnail_url }}"
           alt="{{ $post->title }}"
           style="width:100%; max-height:430px; object-fit:cover; display:block;">
    </figure>

    {{-- ── Article body ─────────────────────────────── --}}
    <div class="article-body">
      {!! $post->content !!}
    </div>

    {{-- ══════════════════════════════════════════════
         SHARE BUTTONS
    ══════════════════════════════════════════════ --}}
    @php $shareUrl = urlencode(route('post.show', $post->slug)); @endphp
    <div style="margin:28px 0; padding:16px; background:#f8f9fa; border-radius:10px; border:1px solid #eee;">
      <p style="font-size:.78rem; font-weight:700; color:#777; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">
        <i class="bi bi-share me-1"></i> Chia sẻ bài viết
      </p>
      <div style="display:flex; flex-wrap:wrap; gap:8px;">
        {{-- Facebook --}}
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
           target="_blank" rel="noopener"
           class="share-btn share-fb">
          <i class="bi bi-facebook"></i> Facebook
        </a>
        {{-- Twitter/X --}}
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ $shareUrl }}"
           target="_blank" rel="noopener"
           class="share-btn share-tw">
          <i class="bi bi-twitter-x"></i> Twitter / X
        </a>
        {{-- Zalo --}}
        <a href="https://zalo.me/share/url?url={{ $shareUrl }}&title={{ urlencode($post->title) }}"
           target="_blank" rel="noopener"
           class="share-btn share-zalo">
          <i class="bi bi-chat-fill"></i> Zalo
        </a>
        {{-- Copy link --}}
        <button onclick="copyLink()" class="share-btn share-copy" id="copyBtn">
          <i class="bi bi-link-45deg"></i> Sao chép link
        </button>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════
         BÀI VIẾT LIÊN QUAN
    ══════════════════════════════════════════════ --}}
    @if($relatedPosts->isNotEmpty())
    <div style="margin:28px 0;">
      <h2 style="font-size:1rem; font-weight:800; color:#fff; background:var(--green,#2a7a27);
                 padding:9px 14px; border-radius:6px 6px 0 0; margin-bottom:0; display:flex; align-items:center; gap:7px;">
        <i class="bi bi-grid-3x3-gap-fill" style="color:#f5d400;"></i>
        Bài viết liên quan
      </h2>
      <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(210px,1fr)); gap:14px;
                  border:1px solid #e8e8e8; border-top:none; border-radius:0 0 6px 6px; padding:14px; background:#fff;">
        @foreach($relatedPosts as $rel)
        <div class="related-card">
          <a href="{{ route('post.show', $rel->slug) }}">
            <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}" loading="lazy">
          </a>
          <div class="related-card-body">
            <a href="{{ route('post.show', $rel->slug) }}" class="related-card-title" style="text-decoration:none;">
              {{ $rel->title }}
            </a>
            <p style="font-size:.70rem; color:#aaa; margin-top:6px;">
              {{ $rel->published_at?->format('d/m/Y') }}
            </p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════
         BÌNH LUẬN
    ══════════════════════════════════════════════ --}}
    <div id="comments" style="margin-top:28px;">
      <h2 style="font-size:1rem; font-weight:800; color:#fff; background:var(--green,#2a7a27);
                 padding:9px 14px; border-radius:6px 6px 0 0; display:flex; align-items:center; gap:7px; margin-bottom:0;">
        <i class="bi bi-chat-dots-fill" style="color:#f5d400;"></i>
        Bình luận ({{ $post->comments->count() }})
      </h2>

      <div style="border:1px solid #e8e8e8; border-top:none; border-radius:0 0 6px 6px; padding:16px; background:#fff;">

        {{-- Flash messages --}}
        @if(session('success'))
        <div style="background:#e8f5e9; border-left:4px solid #2a7a27; padding:10px 14px; border-radius:0 6px 6px 0;
                    font-size:.82rem; color:#2a7a27; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        @endif

        {{-- ── Comment list ───────────────────────── --}}
        @forelse($post->comments as $comment)
        <div class="comment-item">
          <div style="display:flex; gap:10px; align-items:flex-start;">
            {{-- Avatar --}}
            @if(!empty($comment->user->avatar))
              <img src="{{ asset('storage/' . $comment->user->avatar) }}"
                   alt="{{ $comment->user->name }}" class="comment-avatar">
            @else
              <div class="comment-avatar-placeholder">
                {{ strtoupper(mb_substr($comment->user->name ?? 'U', 0, 1)) }}
              </div>
            @endif

            {{-- Bubble --}}
            <div style="flex:1; min-width:0;">
              <div class="comment-bubble">
                <span style="font-weight:700; color:#333; font-size:.82rem;">
                  {{ $comment->user->name ?? 'Người dùng ẩn danh' }}
                </span>
                <p style="margin:4px 0 0;">{{ $comment->content }}</p>
              </div>
              <div class="comment-meta">
                <i class="bi bi-clock"></i>
                {{ $comment->created_at->locale('vi')->diffForHumans() }}
                &nbsp;·&nbsp;
                {{ $comment->created_at->format('d/m/Y H:i') }}
              </div>
            </div>
          </div>
        </div>
        @empty
        <p style="text-align:center; color:#bbb; padding:20px 0; font-size:.88rem;">
          <i class="bi bi-chat-square" style="font-size:1.5rem; display:block; margin-bottom:6px;"></i>
          Chưa có bình luận nào. Hãy là người đầu tiên!
        </p>
        @endforelse

        {{-- ── Comment form ──────────────────────── --}}
        <div style="margin-top:20px; padding-top:18px; border-top:1px solid #f0f0f0;">
          @auth
          <form action="{{ route('post.comment', $post->slug) }}" method="POST">
            @csrf
            <div style="display:flex; gap:10px; align-items:flex-start;">
              {{-- Avatar of current user --}}
              <div class="comment-avatar-placeholder" style="margin-top:2px;">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
              </div>
              <div style="flex:1;">
                <textarea name="content"
                          class="comment-textarea"
                          placeholder="Nhập bình luận của bạn... (tối thiểu 5 ký tự)"
                          maxlength="1000"
                          required>{{ old('content') }}</textarea>
                {{-- Validation error --}}
                @error('content')
                <p style="color:#e53935; font-size:.75rem; margin-top:4px;">
                  <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </p>
                @enderror
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                  <span style="font-size:.72rem; color:#aaa;">
                    Đăng nhập với tư cách <strong>{{ auth()->user()->name }}</strong>
                  </span>
                  <button type="submit"
                          style="background:var(--green,#2a7a27); color:#fff; border:none; border-radius:6px;
                                 padding:8px 20px; font-size:.82rem; font-weight:700; cursor:pointer; transition:background .18s;"
                          onmouseover="this.style.background='#1b5e20'"
                          onmouseout="this.style.background='var(--green,#2a7a27)'">
                    <i class="bi bi-send-fill me-1"></i> Gửi bình luận
                  </button>
                </div>
              </div>
            </div>
          </form>
          @else
          <div style="text-align:center; padding:16px; background:#f8f9fa; border-radius:8px;">
            <p style="font-size:.85rem; color:#666; margin-bottom:10px;">
              <i class="bi bi-lock-fill text-warning me-1"></i>
              Bạn cần đăng nhập để bình luận.
            </p>
            <a href="{{ route('login') }}"
               style="display:inline-block; padding:8px 24px; background:var(--green,#2a7a27); color:#fff;
                      border-radius:6px; font-size:.82rem; font-weight:700; text-decoration:none;">
              <i class="bi bi-person-fill me-1"></i> Đăng nhập
            </a>
          </div>
          @endauth
        </div>

      </div>{{-- /border box --}}
    </div>{{-- /comments --}}

  </main>{{-- /main column --}}

  {{-- ═══════════════════════════════════════════════════
       SIDEBAR — Sticky
  ═══════════════════════════════════════════════════ --}}
  <aside class="d-none d-xl-block" style="width:250px; flex-shrink:0; position:sticky; top:60px; align-self:flex-start;">

    {{-- Quick Info box --}}
    <div style="background:#f4fbf4; border:1px solid #c8e6c9; border-radius:8px; padding:14px; margin-bottom:16px;">
      <h3 style="font-size:.78rem; font-weight:800; color:var(--green,#2a7a27); text-transform:uppercase;
                 letter-spacing:.5px; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
        <i class="bi bi-info-circle-fill"></i> Thông tin bài viết
      </h3>
      <ul style="list-style:none; padding:0; margin:0; font-size:.78rem; color:#555; display:flex; flex-direction:column; gap:8px;">
        <li style="display:flex; align-items:flex-start; gap:8px;">
          <i class="bi bi-person-fill" style="color:var(--green,#2a7a27); margin-top:1px;"></i>
          <span><strong>Tác giả:</strong><br>{{ $post->author->name ?? 'Ban Biên tập' }}</span>
        </li>
        <li style="display:flex; align-items:flex-start; gap:8px;">
          <i class="bi bi-folder-fill" style="color:var(--green,#2a7a27); margin-top:1px;"></i>
          <span><strong>Chuyên mục:</strong><br>
            @if($post->category)
            <a href="{{ route('category', $post->category->slug) }}"
               style="color:var(--green,#2a7a27); text-decoration:none;">{{ $post->category->name }}</a>
            @endif
          </span>
        </li>
        <li style="display:flex; align-items:flex-start; gap:8px;">
          <i class="bi bi-calendar-event-fill" style="color:var(--green,#2a7a27); margin-top:1px;"></i>
          <span><strong>Ngày đăng:</strong><br>{{ $post->published_at?->format('d/m/Y H:i') }}</span>
        </li>
        <li style="display:flex; align-items:flex-start; gap:8px;">
          <i class="bi bi-eye-fill" style="color:var(--green,#2a7a27); margin-top:1px;"></i>
          <span><strong>Lượt xem:</strong> {{ number_format($post->view_count) }}</span>
        </li>
      </ul>
    </div>

    {{-- Back to category --}}
    @if($post->category)
    <a href="{{ route('category', $post->category->slug) }}"
       style="display:block; text-align:center; padding:9px; border:1.5px solid var(--green,#2a7a27); border-radius:6px;
              color:var(--green,#2a7a27); font-size:.78rem; font-weight:700; text-decoration:none; transition:all .18s;"
       onmouseover="this.style.background='var(--green,#2a7a27)'; this.style.color='#fff';"
       onmouseout="this.style.background=''; this.style.color='var(--green,#2a7a27)';">
      <i class="bi bi-arrow-left me-1"></i> Về {{ $post->category->name }}
    </a>
    @endif

  </aside>

</div>{{-- /layout wrapper --}}

@endsection

@push('scripts')
<script>
// Copy link to clipboard
function copyLink() {
  const url = '{{ route('post.show', $post->slug) }}';
  navigator.clipboard.writeText(url).then(() => {
    const btn = document.getElementById('copyBtn');
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check2"></i> Đã sao chép!';
    btn.style.background = '#2a7a27';
    setTimeout(() => {
      btn.innerHTML = original;
      btn.style.background = '#6c757d';
    }, 2500);
  }).catch(() => {
    // Fallback for older browsers
    const el = document.createElement('textarea');
    el.value = url;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    alert('Đã sao chép link!');
  });
}
</script>
@endpush
