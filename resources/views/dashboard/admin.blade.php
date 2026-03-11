@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')

@php $user = auth()->user(); @endphp

<div style="max-width:1180px; margin:32px auto; padding:0 24px;">

  {{-- ── Header & Quick Actions ─────────────────────────────────────── --}}
  <div style="background:#fff; border-radius:16px; padding:24px 32px; box-shadow:0 8px 32px rgba(42,122,39,.04); margin-bottom:32px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">
    
    <div style="display:flex; align-items:center; gap:20px;">
      <div style="width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg,#2a7a27,#1b5e20); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:800; shadow:0 4px 12px rgba(42,122,39,.2);">
        {{ strtoupper(mb_substr($user->name,0,1)) }}
      </div>
      <div>
        <h1 style="font-size:1.5rem; font-weight:800; color:#111; margin:0 0 4px 0;">Xin chào, {{ $user->name }}</h1>
        <div style="display:flex; align-items:center; gap:12px; font-size:.85rem; color:#666;">
          <span style="background:rgba(42,122,39,.1); color:#2a7a27; padding:4px 12px; border-radius:20px; font-weight:700;">{{ $user->roleLabel() }}</span>
          <span>{{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}</span>
        </div>
      </div>
    </div>

    <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
      <a href="{{ route('home') }}" class="btn" style="background:#e8f0fe; color:#1565c0; border:none; border-radius:30px; padding:8px 20px; font-weight:700; font-size:.85rem;"><i class="bi bi-house-door-fill"></i> Trang chủ</a>
      <a href="{{ route('contributor.posts.create') }}" class="btn" style="background:#2a7a27; color:#fff; border-radius:30px; padding:8px 20px; font-weight:600; font-size:.85rem; box-shadow:0 4px 12px rgba(42,122,39,.2);"><i class="bi bi-pencil-square"></i> Viết bài mới</a>
      <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit" class="btn" style="background:#fff; color:#c62828; border:1px solid #ffcdd2; border-radius:30px; padding:8px 20px; font-weight:600; font-size:.85rem;"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
      </form>
    </div>
  </div>

  {{-- ── Stat cards ───────────────────────────────────────── --}}
  <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:20px; margin-bottom:32px;">

    @php
    $cards = [
      ['icon'=>'bi-file-earmark-text-fill','label'=>'Tổng bài viết','value'=>number_format($stats['total_posts']),'cx'=>'#2a7a27','url'=>route('admin.posts.index')],
      ['icon'=>'bi-hourglass-split','label'=>'Chờ duyệt','value'=>number_format($stats['pending_posts']),'cx'=>'#FF6600','url'=>route('admin.posts.index', ['status'=>'pending'])],
      ['icon'=>'bi-people-fill','label'=>'Người dùng','value'=>number_format($stats['total_users']),'cx'=>'#1565c0','url'=>route('admin.users.index')],
      ['icon'=>'bi-chat-dots-fill','label'=>'Bình luận','value'=>number_format($stats['total_comments']),'cx'=>'#7b1fa2','url'=>'#'],
      ['icon'=>'bi-eye-fill','label'=>'Tổng lượt xem','value'=>number_format($stats['total_views']),'cx'=>'#c62828','url'=>'#'],
      ['icon'=>'bi-grid-fill','label'=>'Chuyên mục','value'=>number_format($stats['total_categories']),'cx'=>'#00695c','url'=>route('admin.categories.index')],
    ];
    @endphp

    @foreach($cards as $card)
    <a href="{{ $card['url'] }}" style="text-decoration:none; display:block;">
      <div style="background:#fff; border:1px solid rgba(0,0,0,.04); border-radius:16px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,.03); transition:transform .2s, box-shadow .2s; cursor:pointer; position:relative; overflow:hidden;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px rgba(42,122,39,.08)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 20px rgba(0,0,0,.03)';">
        <div style="position:absolute; top:0; right:0; width:60px; height:60px; background:{{ $card['cx'] }}; opacity:0.04; border-bottom-left-radius:60px;"></div>
        
        <div style="width:40px; height:40px; border-radius:10px; background:{{ $card['cx'] }}15; color:{{ $card['cx'] }}; display:flex; align-items:center; justify-content:center; font-size:1.2rem; margin-bottom:16px;">
          <i class="bi {{ $card['icon'] }}"></i>
        </div>
        <div style="font-size:1.8rem; font-weight:800; color:#111; line-height:1;">{{ $card['value'] }}</div>
        <div style="font-size:.78rem; color:#777; font-weight:600; margin-top:8px;">{{ $card['label'] }}</div>
      </div>
    </a>
    @endforeach

  </div>

  {{-- ── Main Content Grid ──────────────── --}}
  <div style="display:grid; grid-template-columns:2fr 1fr; gap:24px; margin-bottom:32px;">

    {{-- Left: Latest Posts ── --}}
    <div style="background:#fff; border:1px solid rgba(0,0,0,.05); border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.02); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="font-size:1.15rem; font-weight:800; color:#111; margin:0;"><i class="bi bi-clock-history" style="color:#2a7a27; margin-right:8px;"></i>Bài viết mới nhất</h3>
        <a href="{{ route('admin.posts.index') }}" style="font-size:.8rem; font-weight:600; color:#2a7a27; text-decoration:none;">Xem tất cả</a>
      </div>

      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
          <thead>
            <tr style="border-bottom:2px solid #f1f3f5;">
              <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Tiêu đề</th>
              <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Ngày</th>
              <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Trạng thái</th>
              <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700; text-align:right;"></th>
            </tr>
          </thead>
          <tbody>
            @forelse($latestPosts as $p)
            <tr style="border-bottom:1px solid #f8f9fa;">
              <td style="padding:16px 8px;">
                <a href="{{ route('post.show', $p->slug) }}" style="font-size:.9rem; font-weight:600; color:#222; text-decoration:none; line-height:1.4; display:block; margin-bottom:4px;">{{ Str::limit($p->title, 60) }}</a>
                <div style="font-size:.75rem; color:#888;"><i class="bi bi-person"></i> {{ $p->author->name ?? 'Admin' }}</div>
              </td>
              <td style="padding:16px 8px; font-size:.85rem; color:#555;">{{ $p->created_at->format('d/m/Y') }}</td>
              <td style="padding:16px 8px;">
                @php $sc = ['published'=>['bg'=>'rgba(42,122,39,.1)','c'=>'#2a7a27','l'=>'Published'],'pending'=>['bg'=>'rgba(255,102,0,.1)','c'=>'#FF6600','l'=>'Pending'],'draft'=>['bg'=>'#f1f3f5','c'=>'#6c757d','l'=>'Draft']]; $s=$sc[$p->status]??$sc['draft']; @endphp
                <span style="background:{{ $s['bg'] }}; color:{{ $s['c'] }}; font-size:.75rem; font-weight:700; padding:4px 12px; border-radius:20px;">{{ $s['l'] }}</span>
              </td>
              <td style="padding:16px 8px; text-align:right;">
                <div style="display:flex; justify-content:flex-end; gap:8px;">
                  <a href="{{ route('contributor.posts.edit', $p) }}" style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; color:#444; text-decoration:none; transition:bg .2s;"><i class="bi bi-pencil"></i></a>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:32px; text-align:center; color:#999;">Không có bài viết nào.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Right: Pending Approvals ── --}}
    <div style="background:#fff; border:1px solid rgba(0,0,0,.05); border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.02); padding:24px;">
      <h3 style="font-size:1.15rem; font-weight:800; color:#111; margin:0 0 20px 0;"><i class="bi bi-hourglass-split" style="color:#FF6600; margin-right:8px;"></i>Chờ duyệt ({{ $stats['pending_posts'] }})</h3>
      
      <div style="display:flex; flex-direction:column; gap:16px;">
        @forelse($pendingPosts as $p)
        <div style="border:1px solid #f1f3f5; border-radius:12px; padding:16px; transition:border-color .2s;" onmouseover="this.style.borderColor='rgba(255,102,0,.3)';" onmouseout="this.style.borderColor='#f1f3f5';">
          <div style="font-size:.9rem; font-weight:700; color:#222; margin-bottom:8px; line-height:1.4;">{{ Str::limit($p->title, 55) }}</div>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:.75rem; color:#888;">{{ $p->author->name ?? 'User' }} · {{ $p->created_at->diffForHumans() }}</div>
            <div style="display:flex; gap:6px;">
              <form action="{{ route('editor.posts.approve', $p) }}" method="POST">
                 @csrf <button style="width:28px; height:28px; border-radius:6px; background:rgba(42,122,39,.1); color:#2a7a27; border:none; cursor:pointer;" title="Duyệt"><i class="bi bi-check-lg"></i></button>
              </form>
              <form action="{{ route('editor.posts.reject', $p) }}" method="POST" onsubmit="return confirm('Từ chối?');">
                 @csrf <button style="width:28px; height:28px; border-radius:6px; background:#fff; color:#c62828; border:1px solid #ffcdd2; cursor:pointer;" title="Từ chối"><i class="bi bi-x-lg"></i></button>
              </form>
            </div>
          </div>
        </div>
        @empty
        <div style="padding:40px 20px; text-align:center;">
          <div style="width:48px; height:48px; border-radius:50%; background:rgba(42,122,39,.1); color:#2a7a27; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 12px;">
            <i class="bi bi-check2-all"></i>
          </div>
          <div style="font-size:.9rem; font-weight:600; color:#444;">Đã duyệt hết bài viết!</div>
          <div style="font-size:.75rem; color:#999; margin-top:4px;">Chưa có bài nào mới chờ bạn.</div>
        </div>
        @endforelse
      </div>
    </div>

  </div>
</div>
@endsection

