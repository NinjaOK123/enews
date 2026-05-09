@extends('layouts.app')

@section('title', 'Tìm kiếm' . (request('keyword') ? ' — "' . request('keyword') . '"' : ''))

@section('content')
<div style="max-width:1140px; margin:0 auto; padding:20px 16px;">

  {{-- ─── Page title ─────────────────────────────────────── --}}
  <h1 style="font-size:1.4rem; font-weight:900; color:var(--primary,#2a7a27); border-left:4px solid var(--primary,#2a7a27); padding-left:12px; margin-bottom:18px;">
    <i class="bi bi-search me-2"></i>
    @if(request('keyword'))
      Kết quả tìm kiếm: <em style="font-style:italic;">"{{ request('keyword') }}"</em>
    @else
      Tìm kiếm nâng cao
    @endif
  </h1>

  {{-- ─── Full search form ───────────────────────────────── --}}
  <div style="background:#f8f9fa; border-radius:10px; padding:18px 20px; margin-bottom:24px; border:1px solid #e8e8e8;">
    <form action="{{ route('search') }}" method="GET">
      <div style="display:flex; flex-wrap:wrap; gap:14px;">

        <div style="flex:2; min-width:200px;">
          <label style="font-size:.75rem; font-weight:700; color:#555; display:block; margin-bottom:5px;">
            <i class="bi bi-search me-1"></i>Từ khóa
          </label>
          <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tiêu đề, nội dung..."
                 style="width:100%; border:1.5px solid #d0d0d0; border-radius:7px; padding:9px 12px; font-size:.85rem; outline:none; font-family:inherit;"
                 onfocus="this.style.borderColor='#2a7a27'" onblur="this.style.borderColor='#d0d0d0'">
        </div>

        <div style="flex:1; min-width:160px;">
          <label style="font-size:.75rem; font-weight:700; color:#555; display:block; margin-bottom:5px;">
            <i class="bi bi-person me-1"></i>Tác giả
          </label>
          <input type="text" name="author" value="{{ request('author') }}" placeholder="Tên tác giả..."
                 style="width:100%; border:1.5px solid #d0d0d0; border-radius:7px; padding:9px 12px; font-size:.85rem; outline:none; font-family:inherit;"
                 onfocus="this.style.borderColor='#2a7a27'" onblur="this.style.borderColor='#d0d0d0'">
        </div>

        <div style="flex:1.2; min-width:160px;">
          <label style="font-size:.75rem; font-weight:700; color:#555; display:block; margin-bottom:5px;">
            <i class="bi bi-grid me-1"></i>Chuyên mục
          </label>
          <select name="category_id"
                  style="width:100%; border:1.5px solid #d0d0d0; border-radius:7px; padding:9px 12px; font-size:.85rem; outline:none; background:#fff; font-family:inherit;">
            <option value="">-- Tất cả chuyên mục --</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div style="flex:1; min-width:140px;">
          <label style="font-size:.75rem; font-weight:700; color:#555; display:block; margin-bottom:5px;">
            <i class="bi bi-calendar3 me-1"></i>Từ ngày
          </label>
          <input type="date" name="date_from" value="{{ request('date_from') }}"
                 style="width:100%; border:1.5px solid #d0d0d0; border-radius:7px; padding:9px 12px; font-size:.85rem; outline:none;"
                 onfocus="this.style.borderColor='#2a7a27'" onblur="this.style.borderColor='#d0d0d0'">
        </div>

        <div style="flex:1; min-width:140px;">
          <label style="font-size:.75rem; font-weight:700; color:#555; display:block; margin-bottom:5px;">
            <i class="bi bi-calendar3-range me-1"></i>Đến ngày
          </label>
          <input type="date" name="date_to" value="{{ request('date_to') }}"
                 style="width:100%; border:1.5px solid #d0d0d0; border-radius:7px; padding:9px 12px; font-size:.85rem; outline:none;"
                 onfocus="this.style.borderColor='#2a7a27'" onblur="this.style.borderColor='#d0d0d0'">
        </div>

      </div>

      <div style="margin-top:14px; display:flex; gap:10px; align-items:center;">
        <button type="submit"
                style="background:var(--primary,#2a7a27); color:#fff; border:none; border-radius:7px; padding:9px 24px; font-size:.88rem; font-weight:700; cursor:pointer; transition:background .2s;"
                onmouseover="this.style.background='#1b5e20'" onmouseout="this.style.background='var(--primary,#2a7a27)'">
          <i class="bi bi-search me-2"></i>Tìm kiếm
        </button>
        
        <select name="sort" style="border:1.5px solid #d0d0d0; border-radius:7px; padding:8px 12px; font-size:.85rem; outline:none; background:#fff; font-family:inherit; font-weight:600; color:#555; cursor:pointer;" onchange="this.form.submit()">
          <option value="desc" {{ request('sort') != 'asc' ? 'selected' : '' }}>Mới nhất</option>
          <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Cũ nhất</option>
        </select>

        @if($hasFilters)
        <a href="{{ route('search') }}"
           style="color:#888; font-size:.80rem; text-decoration:none; padding:9px 14px; border:1px solid #ddd; border-radius:7px;">
          <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
        </a>
        @endif
      </div>
    </form>
  </div>

  {{-- ─── Results ─────────────────────────────────────────── --}}
  @if(!$hasFilters)
    {{-- No search yet --}}
    <div style="text-align:center; padding:50px 0; color:#aaa;">
      <i class="bi bi-search" style="font-size:3rem; display:block; margin-bottom:10px; opacity:.4;"></i>
      <p>Nhập từ khóa hoặc chọn bộ lọc để tìm kiếm bài viết.</p>
    </div>

  @elseif($posts->isEmpty())
    {{-- No results --}}
    <div style="text-align:center; padding:50px 0; color:#aaa;">
      <i class="bi bi-file-earmark-x" style="font-size:3rem; display:block; margin-bottom:10px; opacity:.4;"></i>
      <p>Không tìm thấy bài viết nào phù hợp với bộ lọc của bạn.</p>
      <a href="{{ route('search') }}" style="display:inline-block; margin-top:10px; padding:8px 20px; background:var(--primary,#2a7a27); color:#fff; border-radius:6px; text-decoration:none; font-size:.84rem; font-weight:700;">
        Thử lại
      </a>
    </div>

  @else
    {{-- Results header --}}
    <div style="font-size:.82rem; color:#777; margin-bottom:16px;">
      Tìm thấy <strong style="color:var(--primary,#2a7a27);">{{ number_format($total) }}</strong> bài viết
      @if(request('keyword')) cho "<em>{{ request('keyword') }}</em>" @endif
    </div>

    {{-- Results list --}}
    <div style="display:flex; flex-direction:column; gap:16px;">
      @foreach($posts as $post)
      <article style="display:flex; gap:14px; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 1px 6px rgba(0,0,0,.07); padding:0; transition:box-shadow .2s;"
               onmouseover="this.style.boxShadow='0 3px 14px rgba(0,0,0,.12)'" onmouseout="this.style.boxShadow='0 1px 6px rgba(0,0,0,.07)'">
        {{-- Thumbnail --}}
        <a href="{{ route('post.show', $post->slug) }}" style="flex-shrink:0;">
          <img src="{{ $post->thumbnail_url }}"
               alt="{{ $post->title }}"
               style="width:160px; height:110px; object-fit:cover; display:block;" loading="lazy">
        </a>
        {{-- Content --}}
        <div style="padding:12px 14px 12px 0; flex:1; min-width:0;">
          {{-- Category badge --}}
          <a href="{{ route('category', $post->category->slug ?? '') }}"
             style="font-size:.68rem; font-weight:700; color:var(--primary,#2a7a27); text-transform:uppercase; letter-spacing:.5px; text-decoration:none; background:#e8f5e9; padding:2px 7px; border-radius:3px; margin-bottom:5px; display:inline-block;">
            {{ $post->category->name ?? '' }}
          </a>
          {{-- Title --}}
          <a href="{{ route('post.show', $post->slug) }}"
             style="display:block; font-size:.95rem; font-weight:700; color:#1a1a1a; line-height:1.45; text-decoration:none; margin-bottom:5px;
                    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
            {{ $post->title }}
          </a>
          {{-- Excerpt --}}
          @if($post->excerpt_short)
          <p style="font-size:.77rem; color:#777; line-height:1.55; margin:0 0 8px;
                     display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
            {{ $post->excerpt_short }}
          </p>
          @endif
          {{-- Meta --}}
          <div style="font-size:.72rem; color:#999; display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
            <span><i class="bi bi-person-fill" style="margin-right:3px; color:var(--primary,#2a7a27);"></i>{{ $post->author->name ?? 'Ban Biên tập' }}</span>
            <span><i class="bi bi-calendar3" style="margin-right:3px; color:var(--primary,#2a7a27);"></i>{{ $post->published_at?->format('d/m/Y') }}</span>
            <span><i class="bi bi-eye" style="margin-right:3px; color:var(--primary,#2a7a27);"></i>{{ number_format($post->view_count) }} lượt xem</span>
          </div>
        </div>
      </article>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($posts->hasPages())
    <div style="display:flex; justify-content:center; gap:6px; margin-top:28px; flex-wrap:wrap;">
      {{ $posts->links() }}
    </div>
    @endif

  @endif

</div>
@endsection
