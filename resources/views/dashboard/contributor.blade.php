@extends('layouts.admin')
@section('title', 'Cộng tác viên Dashboard')
@section('content')

@php $user = auth()->user(); @endphp

<div style="max-width:1180px; margin:32px auto; padding:0 24px;">

  <!-- Welcome Header Area -->
  <div class="row mb-4 align-items-center">
      <div class="col-md-7 mb-3 mb-md-0">
          <h2 class="fw-bold mb-1" style="font-weight: 900 !important; color:#111;">Cổng thông tin Cộng Tác Viên</h2>
          <p class="text-muted mb-0" style="font-size: 0.9rem;">
              Xin chào, <strong class="text-dark">{{ $user->name ?? 'Cộng Tác Viên' }}</strong> • 
              <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Cộng Tác Viên</span> • 
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
  <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:32px;">
    @foreach([
      ['label'=>'Tổng đã viết','value'=>$stats['total'],'icon'=>'bi-file-earmark-text','cx'=>'#2a7a27'],
      ['label'=>'Đã đăng','value'=>$stats['published'],'icon'=>'bi-check-circle-fill','cx'=>'#1565c0'],
      ['label'=>'Chờ duyệt','value'=>$stats['pending'],'icon'=>'bi-hourglass-split','cx'=>'#FF6600'],
      ['label'=>'Bản nháp','value'=>$stats['draft'],'icon'=>'bi-file-earmark','cx'=>'#888'],
      ['label'=>'Tổng lượt xem','value'=>number_format($stats['total_views']),'icon'=>'bi-eye-fill','cx'=>'#c62828'],
    ] as $c)
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

  {{-- ── My Posts Grid ──────────────── --}}
  <div style="background:#fff; border:1px solid rgba(0,0,0,.05); border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.02); padding:24px; margin-bottom:32px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 style="font-size:1.15rem; font-weight:800; color:#111; margin:0;"><i class="bi bi-file-earmark-text-fill" style="color:#2a7a27; margin-right:8px;"></i>Bài viết của tôi ({{ $stats['total'] }})</h3>
    </div>

    <div style="overflow-x:auto;">
      <table style="width:100%; border-collapse:collapse; text-align:left;">
        <thead>
          <tr style="border-bottom:2px solid #f1f3f5;">
            <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Tiêu đề</th>
            <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Thống kê</th>
            <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Ngày cập nhật</th>
            <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700;">Trạng thái</th>
            <th style="padding:12px 8px; font-size:.75rem; color:#888; text-transform:uppercase; font-weight:700; text-align:right;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse($myPosts as $p)
          <tr style="border-bottom:1px solid #f8f9fa;">
            <td style="padding:16px 8px; width:40%;">
              <a href="{{ route('post.show', $p->slug) }}" style="font-size:.9rem; font-weight:600; color:#222; text-decoration:none; line-height:1.4; display:block; margin-bottom:4px;">{{ Str::limit($p->title, 70) }}</a>
              <div style="font-size:.75rem; color:#888;"><i class="bi bi-folder"></i> {{ $p->category->name ?? 'Chưa phân loại' }}</div>
            </td>
            <td style="padding:16px 8px; font-size:.85rem; color:#555;">
              <span style="display:inline-flex; align-items:center; gap:4px; background:#f8f9fa; padding:2px 8px; border-radius:12px; font-size:.75rem;"><i class="bi bi-eye"></i> {{ number_format($p->view_count) }}</span>
            </td>
            <td style="padding:16px 8px; font-size:.85rem; color:#555;">{{ $p->updated_at->format('d/m/Y H:i') }}</td>
            <td style="padding:16px 8px;">
              @php
                $statusMap = [
                  'published' => ['bg'=>'rgba(42,122,39,.1)', 'color'=>'#2a7a27', 'label'=>'Đã đăng'],
                  'pending'   => ['bg'=>'rgba(255,102,0,.1)', 'color'=>'#FF6600', 'label'=>'Chờ duyệt'],
                  'draft'     => ['bg'=>'#f1f3f5', 'color'=>'#6c757d', 'label'=>'Nháp'],
                  'rejected'  => ['bg'=>'rgba(198,40,40,.1)', 'color'=>'#c62828', 'label'=>'Từ chối'],
                ];
                $st = $statusMap[$p->status] ?? $statusMap['draft'];
              @endphp
              <span style="background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size:.75rem; font-weight:700; padding:4px 12px; border-radius:20px; white-space:nowrap;">{{ $st['label'] }}</span>
            </td>
            <td style="padding:16px 8px; text-align:right;">
              <div style="display:flex; justify-content:flex-end; gap:8px;">
                @if(in_array($p->status, ['draft', 'rejected']))
                  <a href="{{ route('contributor.posts.edit', $p) }}" style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; color:#444; border:1px solid #e9ecef; text-decoration:none; transition:bg .2s;" title="Sửa"><i class="bi bi-pencil"></i></a>
                  <form action="{{ route('contributor.posts.destroy', $p) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                    @csrf @method('DELETE')
                    <button type="submit" style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#fff; color:#c62828; border:1px solid #ffcdd2; cursor:pointer;" title="Xóa"><i class="bi bi-trash"></i></button>
                  </form>
                @else
                  <a href="{{ route('post.show', $p->slug) }}" target="_blank" style="padding:6px 12px; border-radius:8px; background:#f8f9fa; color:#444; border:1px solid #e9ecef; font-size:.75rem; font-weight:600; text-decoration:none;"><i class="bi bi-eye"></i> Xem</a>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" style="padding:40px 20px; text-align:center;">
              <div style="width:48px; height:48px; border-radius:50%; background:#f8f9fa; color:#999; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 12px;"><i class="bi bi-pencil-square"></i></div>
              <div style="font-size:.9rem; font-weight:600; color:#444;">Bạn chưa có bài viết nào</div>
              <div style="font-size:.75rem; color:#999; margin-top:4px;">Hãy bắt đầu viết bài đầu tiên của bạn!</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <div style="margin-top:20px;">
      {{ $myPosts->links() }}
    </div>
  </div>

</div>
@endsection

