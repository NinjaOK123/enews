@extends('layouts.app')
@section('title', 'Editor Dashboard')
@section('content')

@php $user = auth()->user(); @endphp

<div style="max-width:1000px; margin:24px auto; padding:0 16px;">

  {{-- Welcome --}}
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:10px;">
    <div>
      <h1 style="font-size:1.35rem; font-weight:900; color:#111; margin:0;">
        <i class="bi bi-pencil-square" style="color:#2a7a27;"></i>
        Biên tập viên Dashboard
      </h1>
      <p style="font-size:.80rem; color:#888; margin:4px 0 0;">
        Chào, <strong style="color:#2a7a27;">{{ $user->name }}</strong> &nbsp;·&nbsp;
        {{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}
      </p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" style="display:flex;align-items:center;gap:6px;padding:8px 16px;background:#fff;border:1.5px solid #ddd;border-radius:8px;font-size:.78rem;font-weight:700;color:#555;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i> Đăng xuất
      </button>
    </form>
  </div>

  {{-- Stats --}}
  <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:24px;">
    @foreach([['label'=>'Chờ duyệt','value'=>$stats['pending'],'icon'=>'bi-hourglass-split','color'=>'#FF6600','bg'=>'#fff8f4'],
              ['label'=>'Đã đăng','value'=>$stats['published'],'icon'=>'bi-check-circle-fill','color'=>'#2a7a27','bg'=>'#f3fbf2'],
              ['label'=>'Bản nháp','value'=>$stats['draft'],'icon'=>'bi-file-earmark','color'=>'#888','bg'=>'#f8f8f8']] as $c)
    <div style="background:{{ $c['bg'] }};border:1px solid {{ $c['color'] }}22;border-radius:10px;padding:16px;display:flex;align-items:center;gap:12px;">
      <i class="bi {{ $c['icon'] }}" style="font-size:1.6rem;color:{{ $c['color'] }};"></i>
      <div>
        <div style="font-size:1.6rem;font-weight:900;color:#111;">{{ $c['value'] }}</div>
        <div style="font-size:.72rem;color:#777;font-weight:600;">{{ $c['label'] }}</div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Pending posts --}}
  <div style="border:1px solid #e4e4e4;border-radius:10px;overflow:hidden;background:#fff;margin-bottom:20px;">
    <div style="background:linear-gradient(135deg,#e65100,#FF6600);color:#fff;padding:12px 16px;font-size:.82rem;font-weight:800;display:flex;align-items:center;gap:8px;">
      <i class="bi bi-hourglass-split" style="color:#f5d400;"></i>
      BÀI VIẾT CHỜ DUYỆT ({{ $stats['pending'] }})
    </div>
    @forelse($pendingPosts as $p)
    <div style="padding:12px 16px;border-bottom:1px solid #f5f5f5;display:flex;align-items:flex-start;gap:12px;">
      <div style="flex:1;min-width:0;">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.85rem;font-weight:700;color:#222;text-decoration:none;line-height:1.4;display:block;">
          {{ $p->title }}
        </a>
        <div style="font-size:.72rem;color:#aaa;margin-top:4px;display:flex;gap:12px;">
          <span><i class="bi bi-person"></i> {{ $p->author->name ?? '-' }}</span>
          <span><i class="bi bi-folder"></i> {{ $p->category->name ?? '-' }}</span>
          <span><i class="bi bi-clock"></i> {{ $p->created_at->locale('vi')->diffForHumans() }}</span>
        </div>
      </div>
      <div style="display:flex;gap:6px;flex-shrink:0;">
        <a href="{{ route('post.show', $p->slug) }}" style="padding:5px 12px;background:#e8f5e9;color:#2a7a27;border-radius:6px;font-size:.72rem;font-weight:700;text-decoration:none;">
          <i class="bi bi-eye"></i> Xem
        </a>
      </div>
    </div>
    @empty
    <div style="padding:32px;text-align:center;color:#bbb;">
      <i class="bi bi-check2-all" style="font-size:2rem;color:#2a7a27;display:block;margin-bottom:8px;"></i>
      <p style="font-size:.88rem;">Không có bài viết nào đang chờ duyệt!</p>
    </div>
    @endforelse
    {{ $pendingPosts->links() }}
  </div>

  {{-- Recently published --}}
  <div style="border:1px solid #e4e4e4;border-radius:10px;overflow:hidden;background:#fff;">
    <div style="background:linear-gradient(135deg,#1b5e20,#2a7a27);color:#fff;padding:12px 16px;font-size:.82rem;font-weight:800;display:flex;align-items:center;gap:8px;">
      <i class="bi bi-check-circle-fill" style="color:#f5d400;"></i>
      VỪA ĐƯỢC ĐĂNG
    </div>
    @foreach($recentPublished as $p)
    <div style="padding:10px 16px;border-bottom:1px solid #f5f5f5;display:flex;justify-content:space-between;align-items:center;gap:10px;">
      <a href="{{ route('post.show', $p->slug) }}" style="font-size:.80rem;font-weight:600;color:#222;text-decoration:none;">{{ Str::limit($p->title,60) }}</a>
      <span style="font-size:.68rem;color:#aaa;white-space:nowrap;">{{ $p->published_at?->format('d/m/Y') }}</span>
    </div>
    @endforeach
  </div>

</div>
@endsection
