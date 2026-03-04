@extends('layouts.app')

@section('title', $category->name)
@section('meta_description', $category->description ?? 'Chuyên mục ' . $category->name . ' - E-News AGU')

@section('content')
<div style="max-width:1140px; margin:0 auto; padding:20px 16px;">

  {{-- ─── Breadcrumb ─────────────────────────────────────── --}}
  <nav style="font-size:.78rem; color:#888; margin-bottom:14px;">
    <a href="{{ route('home') }}" style="color:var(--primary,#2a7a27);text-decoration:none;">Trang chủ</a>
    <span style="margin:0 6px;">›</span>
    <span style="color:#555; font-weight:600;">{{ $category->name }}</span>
  </nav>

  {{-- ─── Title ──────────────────────────────────────────── --}}
  <h1 style="font-size:1.5rem; font-weight:900; color:var(--primary,#2a7a27); border-left:4px solid var(--primary,#2a7a27); padding-left:12px; margin-bottom:20px;">
    {{ $category->name }}
  </h1>

  @if($posts->isEmpty())
    {{-- Empty state --}}
    <div style="text-align:center; padding:60px 0; color:#aaa;">
      <i class="bi bi-newspaper" style="font-size:3rem; display:block; margin-bottom:12px;"></i>
      <p style="font-size:1rem;">Chưa có bài viết nào trong chuyên mục này.</p>
      <a href="{{ route('home') }}" style="display:inline-block; margin-top:12px; padding:8px 20px; background:var(--primary,#2a7a27); color:#fff; border-radius:6px; text-decoration:none; font-size:.85rem; font-weight:700;">
        ← Về trang chủ
      </a>
    </div>

  @else
    {{-- ─── Posts grid ─────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px; margin-bottom:28px;">
      @foreach($posts as $post)
      <article style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 1px 8px rgba(0,0,0,.07); transition:box-shadow .2s;"
               onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.14)'" onmouseout="this.style.boxShadow='0 1px 8px rgba(0,0,0,.07)'">
        <a href="{{ route('post.show', $post->slug) }}">
          <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}"
               style="width:100%; height:170px; object-fit:cover; display:block;" loading="lazy">
        </a>
        <div style="padding:12px 14px 14px;">
          <div style="font-size:.72rem; color:#888; margin-bottom:6px;">
            <i class="bi bi-person-fill" style="margin-right:3px;"></i>{{ $post->author->name ?? 'Ban Biên tập' }}
            &nbsp;·&nbsp;
            <i class="bi bi-calendar3" style="margin-right:3px;"></i>{{ $post->published_at?->format('d/m/Y') }}
          </div>
          <a href="{{ route('article', $post->slug) }}"
             style="font-size:.88rem; font-weight:700; color:#1a1a1a; line-height:1.45; display:block; text-decoration:none;
                    display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
            {{ $post->title }}
          </a>
          @if($post->excerpt_short)
          <p style="font-size:.76rem; color:#777; margin-top:6px; line-height:1.5;
                     display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
            {{ $post->excerpt_short }}
          </p>
          @endif
        </div>
      </article>
      @endforeach
    </div>

    {{-- ─── Pagination ──────────────────────────────────── --}}
    @if($posts->hasPages())
    <div style="display:flex; justify-content:center; gap:6px; flex-wrap:wrap;">
      {{-- Previous --}}
      @if($posts->onFirstPage())
        <span style="padding:5px 12px; border:1px solid #e0e0e0; border-radius:4px; color:#ccc; font-size:.82rem;">‹</span>
      @else
        <a href="{{ $posts->previousPageUrl() }}" style="padding:5px 12px; border:1px solid #d0d0d0; border-radius:4px; color:#555; font-size:.82rem; text-decoration:none;">‹</a>
      @endif

      @foreach($posts->links()->elements[0] ?? [] as $page => $url)
        @if($page == $posts->currentPage())
          <span style="padding:5px 12px; border:1px solid var(--primary,#2a7a27); border-radius:4px; background:var(--primary,#2a7a27); color:#fff; font-size:.82rem; font-weight:700;">{{ $page }}</span>
        @else
          <a href="{{ $url }}" style="padding:5px 12px; border:1px solid #d0d0d0; border-radius:4px; color:#555; font-size:.82rem; text-decoration:none;">{{ $page }}</a>
        @endif
      @endforeach

      {{-- Next --}}
      @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}" style="padding:5px 12px; border:1px solid #d0d0d0; border-radius:4px; color:#555; font-size:.82rem; text-decoration:none;">›</a>
      @else
        <span style="padding:5px 12px; border:1px solid #e0e0e0; border-radius:4px; color:#ccc; font-size:.82rem;">›</span>
      @endif
    </div>
    @endif

  @endif

</div>
@endsection
