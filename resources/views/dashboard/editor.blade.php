@extends('layouts.admin')
@section('title', 'Editor Dashboard')
@section('content')

@php $user = auth()->user(); @endphp

<div style="max-width:1180px; margin:32px auto; padding:0 24px;">

  <!-- Welcome Header Area -->
  <div class="row mb-4 align-items-center">
      <div class="col-md-7 mb-3 mb-md-0">
          <h2 class="fw-bold mb-1" style="font-weight: 900 !important; color:#111;">Cổng thông tin Biên Tập Viên</h2>
          <p class="text-muted mb-0" style="font-size: 0.9rem;">
              Xin chào, <strong class="text-dark">{{ $user->name ?? 'Biên Tập Viên' }}</strong> • 
              <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Biên Tập Viên</span> • 
              {{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}
          </p>
      </div>
      <div class="col-md-5 text-md-end d-flex justify-content-md-end gap-2">
          <a href="{{ route('home') }}" class="btn btn-outline-success rounded-pill fw-semibold shadow-sm px-4 hover-lift">
              <i class="bi bi-house-door-fill me-1"></i> Trang chủ
          </a>
          <a href="{{ route('contributor.posts.create') }}" class="btn btn-success rounded-pill fw-semibold shadow-sm px-4 hover-lift">
              <i class="bi bi-pencil-square me-1"></i> Viết bài mới
          </a>
      </div>
  </div>

  {{-- ── Stat cards ───────────────────────────────────────── --}}
  <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; margin-bottom:32px;">
    @foreach([['label'=>'Chờ duyệt','value'=>$stats['pending'],'icon'=>'bi-hourglass-split','cx'=>'#FF6600'],
              ['label'=>'Đã đăng','value'=>$stats['published'],'icon'=>'bi-check-circle-fill','cx'=>'#2a7a27'],
              ['label'=>'Bản nháp','value'=>$stats['draft'],'icon'=>'bi-file-earmark','cx'=>'#888']] as $c)
    <div style="background:#fff; border:1px solid rgba(0,0,0,.04); border-radius:16px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,.03); position:relative; overflow:hidden;">
      <div style="position:absolute; top:0; right:0; width:60px; height:60px; background:{{ $c['cx'] }}; opacity:0.04; border-bottom-left-radius:60px;"></div>
      
      <div style="width:40px; height:40px; border-radius:10px; background:{{ $c['cx'] }}15; color:{{ $c['cx'] }}; display:flex; align-items:center; justify-content:center; font-size:1.2rem; margin-bottom:16px;">
        <i class="bi {{ $c['icon'] }}"></i>
      </div>
      <div style="font-size:1.8rem; font-weight:800; color:#111; line-height:1;">{{ $c['value'] }}</div>
      <div style="font-size:.78rem; color:#777; font-weight:600; margin-top:8px;">{{ $c['label'] }}</div>
    </div>
    @endforeach
  </div>

  {{-- ── Main Content Grid ──────────────── --}}
  <div style="display:grid; grid-template-columns:2fr 1fr; gap:24px; margin-bottom:32px;">

    {{-- Left: Pending Posts ── --}}
    <div style="background:#fff; border:1px solid rgba(0,0,0,.05); border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.02); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="font-size:1.15rem; font-weight:800; color:#111; margin:0;"><i class="bi bi-hourglass-split" style="color:#FF6600; margin-right:8px;"></i>Bài viết chờ duyệt ({{ $stats['pending'] }})</h3>
      </div>

      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
          <thead>
            <tr style="border-bottom:2px solid #f1f3f5;">
              <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Bài viết / Tác giả</th>
              <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Chuyên mục</th>
              <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700; text-align:right;">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendingPosts as $p)
            <tr style="border-bottom:1px solid #f8f9fa;">
              <td style="padding:16px 8px;">
                <a href="{{ route('post.show', $p->slug) }}" style="font-size:.9rem; font-weight:600; color:#222; text-decoration:none; line-height:1.4; display:block; margin-bottom:4px;">{{ $p->title }}</a>
                <div style="font-size:.75rem; color:#888; display:flex; gap:12px;">
                  <span><i class="bi bi-person"></i> {{ $p->author->name ?? '-' }}</span>
                  <span><i class="bi bi-clock"></i> {{ $p->created_at->locale('vi')->diffForHumans() }}</span>
                </div>
              </td>
              <td style="padding:16px 8px; font-size:.85rem; color:#555;">{{ $p->category->name ?? '-' }}</td>
              <td style="padding:16px 8px; text-align:right;">
                <div style="display:flex; justify-content:flex-end; gap:6px;">
                  <a href="{{ route('post.show', $p->slug) }}" target="_blank" style="padding:6px 12px; background:rgba(42,122,39,.1); color:#2a7a27; border-radius:6px; font-size:.75rem; font-weight:700; text-decoration:none;"><i class="bi bi-eye"></i> Xem</a>
                  
                  <form action="{{ route('editor.posts.approve', $p) }}" method="POST" onsubmit="return confirm('Bạn muốn duyệt bài viết này?');">
                    @csrf
                    <button type="submit" style="padding:6px 12px; background:#2a7a27; color:#fff; border:none; border-radius:6px; font-size:.75rem; font-weight:700; cursor:pointer;"><i class="bi bi-check-lg"></i> Duyệt</button>
                  </form>
                  
                  <form action="{{ route('editor.posts.reject', $p) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn từ chối bài?');">
                    @csrf
                    <button type="submit" style="padding:6px 12px; background:#fff; color:#c62828; border:1px solid #ffcdd2; border-radius:6px; font-size:.75rem; font-weight:700; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" style="padding:40px 20px; text-align:center;">
                <div style="width:48px; height:48px; border-radius:50%; background:rgba(42,122,39,.1); color:#2a7a27; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 12px;"><i class="bi bi-check2-all"></i></div>
                <div style="font-size:.9rem; font-weight:600; color:#444;">Không có bài chờ duyệt!</div>
                <div style="font-size:.75rem; color:#999; margin-top:4px;">Bạn đã xử lý xong tất cả các bài viết.</div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div style="margin-top:20px;">
        {{ $pendingPosts->links() }}
      </div>
    </div>

    {{-- Right: Recently published ── --}}
    <div style="background:#fff; border:1px solid rgba(0,0,0,.05); border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.02); padding:24px;">
      <h3 style="font-size:1.15rem; font-weight:800; color:#111; margin:0 0 20px 0;"><i class="bi bi-check-circle-fill" style="color:#2a7a27; margin-right:8px;"></i>Vừa được đăng</h3>
      
      <div style="display:flex; flex-direction:column; gap:16px;">
        @forelse($recentPublished as $p)
        <div style="border:1px solid #f1f3f5; border-radius:12px; padding:16px;">
          <a href="{{ route('post.show', $p->slug) }}" style="font-size:.9rem; font-weight:700; color:#222; margin-bottom:8px; line-height:1.4; display:block; text-decoration:none;">{{ Str::limit($p->title, 55) }}</a>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:.75rem; color:#888;">{{ $p->author->name ?? 'User' }}</div>
            <div style="font-size:.70rem; background:rgba(42,122,39,.1); color:#2a7a27; font-weight:800; padding:2px 8px; border-radius:12px;">{{ $p->published_at?->format('d/m/Y') }}</div>
          </div>
        </div>
        @empty
        <div style="padding:20px; text-align:center; color:#999; font-size:.85rem;">Trống.</div>
        @endforelse
      </div>
    </div>

  </div>

</div>
@endsection

