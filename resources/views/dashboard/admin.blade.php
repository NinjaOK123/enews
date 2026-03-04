@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')

@php $user = auth()->user(); @endphp

<div style="max-width:1100px; margin:24px auto; padding:0 16px;">

  {{-- ── Welcome bar ─────────────────────────────────────── --}}
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
      <h1 style="font-size:1.45rem; font-weight:900; color:#111; margin:0;">
        <i class="bi bi-speedometer2" style="color:#2a7a27;"></i>
        Admin Dashboard
      </h1>
      <p style="font-size:.82rem; color:#888; margin:4px 0 0;">
        Xin chào, <strong style="color:#2a7a27;">{{ $user->name }}</strong> —
        <span style="background:#e8f5e2; color:#2a7a27; border-radius:20px; padding:1px 10px; font-size:.72rem; font-weight:700;">
          {{ $user->roleLabel() }}
        </span>
        &nbsp;·&nbsp; {{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY HH:mm') }}
      </p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
              style="display:flex; align-items:center; gap:6px; padding:8px 18px; background:#fff; border:1.5px solid #ddd;
                     border-radius:8px; font-size:.80rem; font-weight:700; color:#555; cursor:pointer; transition:all .18s;">
        <i class="bi bi-box-arrow-right"></i> Đăng xuất
      </button>
    </form>
  </div>

  {{-- ── Stat cards ───────────────────────────────────────── --}}
  <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:14px; margin-bottom:28px;">

    @php
    $cards = [
      ['icon'=>'bi-file-earmark-text-fill','label'=>'Tổng bài viết','value'=>number_format($stats['total_posts']),'color'=>'#2a7a27','bg'=>'#f3fbf2'],
      ['icon'=>'bi-hourglass-split','label'=>'Chờ duyệt','value'=>number_format($stats['pending_posts']),'color'=>'#FF6600','bg'=>'#fff8f4'],
      ['icon'=>'bi-people-fill','label'=>'Người dùng','value'=>number_format($stats['total_users']),'color'=>'#1565c0','bg'=>'#e8f0fe'],
      ['icon'=>'bi-chat-dots-fill','label'=>'Bình luận','value'=>number_format($stats['total_comments']),'color'=>'#7b1fa2','bg'=>'#f9f0ff'],
      ['icon'=>'bi-eye-fill','label'=>'Tổng lượt xem','value'=>number_format($stats['total_views']),'color'=>'#c62828','bg'=>'#fff5f5'],
      ['icon'=>'bi-grid-fill','label'=>'Chuyên mục','value'=>number_format($stats['total_categories']),'color'=>'#00695c','bg'=>'#e0f2f1'],
    ];
    @endphp

    @foreach($cards as $card)
    <div style="background:{{ $card['bg'] }}; border:1px solid {{ $card['color'] }}22; border-radius:10px; padding:18px 16px;
                display:flex; align-items:center; gap:14px; box-shadow:0 1px 6px rgba(0,0,0,.05);">
      <div style="width:42px; height:42px; border-radius:10px; background:{{ $card['color'] }}20; color:{{ $card['color'] }};
                  display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
        <i class="bi {{ $card['icon'] }}"></i>
      </div>
      <div>
        <div style="font-size:1.5rem; font-weight:900; color:#111;">{{ $card['value'] }}</div>
        <div style="font-size:.72rem; color:#777; font-weight:600;">{{ $card['label'] }}</div>
      </div>
    </div>
    @endforeach

  </div>

  {{-- ── Two column: Latest posts + Pending ──────────────── --}}
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:24px;">

    {{-- Latest posts --}}
    <div style="border:1px solid #e4e4e4; border-radius:10px; overflow:hidden; background:#fff;">
      <div style="background:linear-gradient(135deg,#1b5e20,#2a7a27); color:#fff; padding:12px 16px; font-size:.82rem; font-weight:800;
                  display:flex; align-items:center; gap:8px;">
        <i class="bi bi-clock-fill" style="color:#f5d400;"></i> BÀI VIẾT MỚI NHẤT
      </div>
      <div>
        @forelse($latestPosts as $p)
        <div style="display:flex; align-items:center; gap:10px; padding:10px 14px; border-bottom:1px solid #f5f5f5;">
          <a href="{{ route('post.show', $p->slug) }}" style="flex:1; font-size:.80rem; font-weight:600; color:#222; text-decoration:none; line-height:1.4;">
            {{ Str::limit($p->title, 52) }}
          </a>
          <span style="font-size:.68rem; color:#aaa; white-space:nowrap;">{{ $p->created_at->format('d/m') }}</span>
          @php $sc = ['published'=>['bg'=>'#e8f5e9','c'=>'#2a7a27'],'pending'=>['bg'=>'#fff8e1','c'=>'#f57f17'],'draft'=>['bg'=>'#f5f5f5','c'=>'#777']]; $s=$sc[$p->status]??$sc['draft']; @endphp
          <span style="background:{{ $s['bg'] }}; color:{{ $s['c'] }}; font-size:.62rem; font-weight:800; padding:2px 8px; border-radius:20px; white-space:nowrap;">
            {{ $p->status }}
          </span>
        </div>
        @empty
        <p style="padding:20px; color:#ccc; text-align:center; font-size:.85rem;">Chưa có bài viết.</p>
        @endforelse
      </div>
    </div>

    {{-- Pending posts --}}
    <div style="border:1px solid #e4e4e4; border-radius:10px; overflow:hidden; background:#fff;">
      <div style="background:linear-gradient(135deg,#e65100,#FF6600); color:#fff; padding:12px 16px; font-size:.82rem; font-weight:800;
                  display:flex; align-items:center; gap:8px;">
        <i class="bi bi-hourglass-split" style="color:#f5d400;"></i> CHỜ DUYỆT ({{ $stats['pending_posts'] }})
      </div>
      <div>
        @forelse($pendingPosts as $p)
        <div style="padding:10px 14px; border-bottom:1px solid #f5f5f5;">
          <div style="font-size:.80rem; font-weight:600; color:#222; margin-bottom:3px;">{{ Str::limit($p->title, 48) }}</div>
          <div style="font-size:.70rem; color:#aaa; display:flex; gap:12px;">
            <span><i class="bi bi-person"></i> {{ $p->author->name ?? '-' }}</span>
            <span><i class="bi bi-calendar3"></i> {{ $p->created_at->format('d/m/Y') }}</span>
          </div>
        </div>
        @empty
        <p style="padding:20px; color:#ccc; text-align:center; font-size:.85rem;">
          <i class="bi bi-check2-all" style="color:#2a7a27; font-size:1.5rem; display:block;"></i>
          Không có bài chờ duyệt!
        </p>
        @endforelse
      </div>
    </div>

  </div>

  {{-- ── Latest users ─────────────────────────────────────── --}}
  <div style="border:1px solid #e4e4e4; border-radius:10px; overflow:hidden; background:#fff;">
    <div style="background:linear-gradient(135deg,#1565c0,#1976d2); color:#fff; padding:12px 16px; font-size:.82rem; font-weight:800;
                display:flex; align-items:center; gap:8px;">
      <i class="bi bi-people-fill" style="color:#f5d400;"></i> NGƯỜI DÙNG MỚI
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:.80rem;">
      <thead>
        <tr style="background:#f8f9fa; color:#555; font-weight:700; font-size:.72rem; text-transform:uppercase;">
          <th style="padding:9px 14px; text-align:left;">Tên</th>
          <th style="padding:9px 14px; text-align:left;">Email</th>
          <th style="padding:9px 14px; text-align:left;">Vai trò</th>
          <th style="padding:9px 14px; text-align:left;">Ngày tạo</th>
        </tr>
      </thead>
      <tbody>
        @forelse($latestUsers as $u)
        <tr style="border-top:1px solid #f5f5f5;">
          <td style="padding:9px 14px; font-weight:600;">
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:30px; height:30px; border-radius:50%; background:hsl({{ ord($u->name[0])*13%360 }},60%,42%);
                          color:#fff; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:800; flex-shrink:0;">
                {{ strtoupper(mb_substr($u->name,0,1)) }}
              </div>
              {{ $u->name }}
            </div>
          </td>
          <td style="padding:9px 14px; color:#666;">{{ $u->email }}</td>
          <td style="padding:9px 14px;">
            @php $rc=['admin'=>'#2a7a27','editor'=>'#1565c0','contributor'=>'#7b1fa2','reader'=>'#555']; $rc2=$rc[$u->role]??'#555'; @endphp
            <span style="background:{{ $rc2 }}18; color:{{ $rc2 }}; font-size:.68rem; font-weight:800; padding:2px 10px; border-radius:20px;">
              {{ $u->roleLabel() }}
            </span>
          </td>
          <td style="padding:9px 14px; color:#aaa; font-size:.72rem;">{{ $u->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="padding:20px; text-align:center; color:#ccc; font-size:.85rem;">Chưa có người dùng.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
