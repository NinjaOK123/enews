{{--
    partials/home-section.blade.php
    Reusable section widget: 1 featured + list of articles from a category.
    Variables: $slug, $label, $posts (Collection)
--}}
<div class="section-widget" data-aos="fade-up" data-aos-duration="600">
  <div class="section-widget-title">
    {{ $label }}
    <a href="{{ route('category', $slug) }}" class="more-link" wire:navigate.hover>» Xem thêm</a>
  </div>
  <div class="section-widget-body">

    @if($posts->isNotEmpty())

      {{-- Featured article (first post) --}}
      @php $featured = $posts->first(); @endphp
      <div class="featured-article" data-aos="fade-up" data-aos-delay="100">
        <a href="{{ route('post.show', $featured->slug) }}" wire:navigate.hover>
          <div class="featured-article-img-wrap">
            <img src="{{ $featured->thumbnail_url }}"
                 alt="{{ $featured->title }}" loading="lazy">
          </div>
        </a>
        <div class="featured-article-title">
          <a href="{{ route('post.show', $featured->slug) }}" wire:navigate.hover>{{ $featured->title }}</a>
        </div>
        <div class="featured-article-meta">
          {{ $featured->published_at?->format('d/m/Y') }}
          &nbsp;·&nbsp;
          {{ $featured->author->name ?? 'Ban Biên tập' }}
        </div>
      </div>

      {{-- List articles (remaining posts) --}}
      @foreach($posts->skip(1) as $index => $post)
      <div class="news-list-item" data-aos="fade-up" data-aos-delay="{{ 150 + ($index * 50) }}">
        <a href="{{ route('post.show', $post->slug) }}" wire:navigate.hover>
          <img src="{{ $post->thumbnail_url }}"
               class="news-list-thumb" alt="{{ $post->title }}" loading="lazy">
        </a>
        <div>
          <div class="news-list-title">
            <a href="{{ route('post.show', $post->slug) }}" wire:navigate.hover>{{ $post->title }}</a>
          </div>
          <div class="news-list-meta">
            <span><i class="bi bi-calendar3"></i> {{ $post->published_at?->format('d/m/Y') }}</span>
            <span><i class="bi bi-eye"></i> {{ number_format($post->view_count) }}</span>
          </div>
        </div>
      </div>
      @endforeach

    @else
      <p style="color:#bbb; text-align:center; padding:20px 0; font-size:.82rem;">
        <i class="bi bi-newspaper" style="display:block; font-size:1.5rem; margin-bottom:6px;"></i>
        Chưa có bài viết trong chuyên mục này.
      </p>
    @endif

  </div>
</div>
