@extends('layouts.app')
@section('title', 'Cộng tác viên Dashboard')
@section('content')

@php $user = auth()->user(); @endphp

<div style="max-width:900px; margin:24px auto; padding:0 16px;">

  {{-- Welcome --}}
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:10px;">
    <div>
      <h1 style="font-size:1.35rem; font-weight:900; color:#111; margin:0;">
        <i class="bi bi-person-workspace" style="color:#2a7a27;"></i>
        Cộng tác viên Dashboard
      </h1>
      <p style="font-size:.80rem; color:#888; margin:4px 0 0;">
        Xin chào, <strong style="color:#2a7a27;">{{ $user->name }}</strong> &nbsp;·&nbsp;
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
  <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; margin-bottom:24px;">
    @foreach([
      ['label'=>'Tổng bài đã viết','value'=>$stats['total'],'icon'=>'bi-file-earmark-text','color'=>'#2a7a27','bg'=>'#f3fbf2'],
      ['label'=>'Đã đăng','value'=>$stats['published'],'icon'=>'bi-check-circle-fill','color'=>'#1565c0','bg'=>'#e8f0fe'],
      ['label'=>'Chờ duyệt','value'=>$stats['pending'],'icon'=>'bi-hourglass-split','color'=>'#FF6600','bg'=>'#fff8f4'],
      ['label'=>'Bản nháp','value'=>$stats['draft'],'icon'=>'bi-file-earmark','color'=>'#888','bg'=>'#f8f8f8'],
      ['label'=>'Tổng lượt xem','value'=>number_format($stats['total_views']),'icon'=>'bi-eye-fill','color'=>'#c62828','bg'=>'#fff5f5'],
    ] as $c)
    <div style="background:{{ $c['bg'] }};border:1px solid {{ $c['color'] }}22;border-radius:10px;padding:14px 12px;text-align:center;">
      <i class="bi {{ $c['icon'] }}" style="font-size:1.5rem;color:{{ $c['color'] }};display:block;margin-bottom:6px;"></i>
      <div style="font-size:1.35rem;font-weight:900;color:#111;">{{ $c['value'] }}</div>
      <div style="font-size:.70rem;color:#777;font-weight:600;">{{ $c['label'] }}</div>
    </div>
    @endforeach
  </div>

  {{-- My posts --}}
  <div style="border:1px solid #e4e4e4;border-radius:10px;overflow:hidden;background:#fff;">
    <div style="background:linear-gradient(135deg,#1b5e20,#2a7a27);color:#fff;padding:12px 16px;font-size:.82rem;font-weight:800;display:flex;align-items:center;justify-content:space-between;">
      <span><i class="bi bi-file-earmark-text-fill" style="color:#f5d400;"></i> BÀI VIẾT CỦA TÔI ({{ $stats['total'] }})</span>
    </div>

    @forelse($myPosts as $p)
    <div style="padding:12px 16px;border-bottom:1px solid #f5f5f5;display:flex;align-items:flex-start;gap:12px;">
      <div style="flex:1;min-width:0;">
        <a href="{{ route('post.show', $p->slug) }}" style="font-size:.85rem;font-weight:700;color:#222;text-decoration:none;line-height:1.4;display:block;">
          {{ $p->title }}
        </a>
        <div style="font-size:.72rem;color:#aaa;margin-top:4px;display:flex;gap:12px;flex-wrap:wrap;">
          <span><i class="bi bi-folder"></i> {{ $p->category->name ?? 'Chưa phân loại' }}</span>
          <span><i class="bi bi-eye"></i> {{ number_format($p->view_count) }} lượt xem</span>
          <span><i class="bi bi-clock"></i> {{ $p->created_at->locale('vi')->diffForHumans() }}</span>
        </div>
      </div>
      @php
        $statusMap = [
          'published' => ['bg'=>'#e8f5e9','color'=>'#2a7a27','label'=>'Đã đăng'],
          'pending'   => ['bg'=>'#fff8e1','color'=>'#f57f17','label'=>'Chờ duyệt'],
          'draft'     => ['bg'=>'#f5f5f5','color'=>'#777',   'label'=>'Nháp'],
        ];
        $st = $statusMap[$p->status] ?? $statusMap['draft'];
      @endphp
      <span style="background:{{ $st['bg'] }};color:{{ $st['color'] }};font-size:.68rem;font-weight:800;padding:3px 10px;border-radius:20px;white-space:nowrap;align-self:center;">
        {{ $st['label'] }}
      </span>
    </div>
    @empty
    <div style="padding:40px;text-align:center;color:#bbb;">
      <i class="bi bi-pencil-square" style="font-size:2.5rem;display:block;margin-bottom:10px;color:#ddd;"></i>
      <p>Bạn chưa có bài viết nào.<br>Hãy bắt đầu viết bài đầu tiên!</p>
    </div>
    @endforelse

    <div style="padding:10px 16px;">{{ $myPosts->links() }}</div>
  </div>

</div>
@endsection
