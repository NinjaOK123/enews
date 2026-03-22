@extends('layouts.admin')
@section('title', 'Yêu cầu Cộng tác viên')

@push('styles')
<style>
.stat-card {
    border-radius: 16px;
    padding: 20px 24px;
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.10); }
.stat-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem; flex-shrink: 0;
}
.filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
.filter-tab {
    padding: 8px 20px; border-radius: 50px; font-size: .85rem;
    font-weight: 600; text-decoration: none; border: 2px solid transparent;
    transition: all .2s;
}
.filter-tab.active-pending   { background: #fff8e1; color: #b45309; border-color: #fcd34d; }
.filter-tab.active-approved  { background: #f0fdf4; color: #15803d; border-color: #86efac; }
.filter-tab.active-rejected  { background: #fff1f2; color: #be123c; border-color: #fda4af; }
.filter-tab.active-all       { background: #eff6ff; color: #1d4ed8; border-color: #93c5fd; }
.filter-tab:not([class*="active"]) {
    background: #f8fafc; color: #64748b; border-color: #e2e8f0;
}
.filter-tab:not([class*="active"]):hover {
    background: #f1f5f9; color: #334155;
}
.table-card { border-radius: 16px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; }
.avatar-circle {
    width: 38px; height: 38px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 700; flex-shrink: 0;
    background: linear-gradient(135deg, #198754, #2d9e60);
    color: #fff;
}
.status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 50px; font-size: .75rem; font-weight: 700;
}
.pill-pending  { background: #fff8e1; color: #92400e; }
.pill-approved { background: #f0fdf4; color: #15803d; }
.pill-rejected { background: #fff1f2; color: #be123c; }
.action-btn {
    border: none; border-radius: 8px; padding: 6px 14px;
    font-size: .78rem; font-weight: 700; cursor: pointer;
    transition: all .15s; display: inline-flex; align-items: center; gap: 5px;
}
.btn-approve { background: #dcfce7; color: #15803d; }
.btn-approve:hover { background: #16a34a; color: #fff; }
.btn-reject  { background: #fee2e2; color: #be123c; }
.btn-reject:hover  { background: #dc2626; color: #fff; }
.empty-state {
    padding: 64px 24px; text-align: center;
}
.empty-state .empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: #f1f5f9; display: flex; align-items: center;
    justify-content: center; font-size: 2.2rem;
    margin: 0 auto 16px;
}
.modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.55);
    z-index: 2000; display: none;
    align-items: center; justify-content: center; padding: 16px;
}
.modal-overlay.show { display: flex; }
.modal-card {
    background: #fff; border-radius: 20px; width: 100%; max-width: 420px;
    box-shadow: 0 25px 60px rgba(0,0,0,.2); overflow: hidden;
}
</style>
@endpush

@section('content')
<div class="px-1">

  {{-- Page header --}}
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
      <h4 class="fw-bold mb-0" style="color:#1e293b;">Yêu cầu Cộng tác viên</h4>
      <p class="text-muted small mb-0 mt-1">Duyệt hoặc từ chối yêu cầu trở thành cộng tác viên</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
      ← Quay về Dashboard
    </a>
  </div>

  {{-- Flash --}}
  @if(session('success'))
  <div class="alert border-0 rounded-3 mb-4" style="background:#f0fdf4;color:#15803d;">
    ✅ {{ session('success') }}
  </div>
  @endif

  {{-- Stat cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card bg-white d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#fff8e1;color:#b45309;">⏳</div>
        <div>
          <div class="fw-bold fs-4 lh-1" style="color:#1e293b;">{{ $counts['pending'] }}</div>
          <div class="small text-muted">Chờ duyệt</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card bg-white d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#f0fdf4;color:#15803d;">✅</div>
        <div>
          <div class="fw-bold fs-4 lh-1" style="color:#1e293b;">{{ $counts['approved'] }}</div>
          <div class="small text-muted">Đã duyệt</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card bg-white d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#fff1f2;color:#be123c;">❌</div>
        <div>
          <div class="fw-bold fs-4 lh-1" style="color:#1e293b;">{{ $counts['rejected'] }}</div>
          <div class="small text-muted">Từ chối</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card bg-white d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#eff6ff;color:#1d4ed8;">👥</div>
        <div>
          <div class="fw-bold fs-4 lh-1" style="color:#1e293b;">{{ array_sum($counts) }}</div>
          <div class="small text-muted">Tổng cộng</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter tabs --}}
  <div class="filter-tabs mb-4">
    @php
      $tabs = [
        'pending'  => ['label' => '⏳ Chờ duyệt',  'class' => 'active-pending'],
        'approved' => ['label' => '✅ Đã duyệt',   'class' => 'active-approved'],
        'rejected' => ['label' => '❌ Từ chối',    'class' => 'active-rejected'],
        'all'      => ['label' => '🗂 Tất cả',     'class' => 'active-all'],
      ];
    @endphp
    @foreach($tabs as $s => $tab)
    <a href="{{ route('admin.contributor.index', ['status' => $s]) }}"
       class="filter-tab {{ $status === $s ? $tab['class'] : '' }}">
      {{ $tab['label'] }}
      @if($s === 'pending' && $counts['pending'] > 0)
        <span style="background:#f59e0b;color:#fff;border-radius:50px;padding:1px 7px;font-size:.7rem;margin-left:4px;">{{ $counts['pending'] }}</span>
      @endif
    </a>
    @endforeach
  </div>

  {{-- Table --}}
  <div class="table-card bg-white">
    @if($requests->count())
    <div class="table-responsive">
      <table class="table align-middle mb-0" style="font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:2px solid #f1f5f9;">
            <th class="px-4 py-3 fw-semibold text-muted border-0" style="white-space:nowrap;">Người dùng</th>
            <th class="py-3 fw-semibold text-muted border-0">Họ tên</th>
            <th class="py-3 fw-semibold text-muted border-0">Ngân hàng</th>
            <th class="py-3 fw-semibold text-muted border-0" style="white-space:nowrap;">Số TK</th>
            <th class="py-3 fw-semibold text-muted border-0" style="white-space:nowrap;">Chủ TK</th>
            <th class="py-3 fw-semibold text-muted border-0">Ngày gửi</th>
            <th class="py-3 fw-semibold text-muted border-0">Trạng thái</th>
            <th class="px-4 py-3 fw-semibold text-muted border-0 text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @foreach($requests as $req)
          <tr style="border-bottom:1px solid #f8fafc;" class="hover-row">
            {{-- User --}}
            <td class="px-4 py-3">
              <div class="d-flex align-items-center gap-2">
                <div class="avatar-circle">{{ strtoupper(substr($req->user->name, 0, 1)) }}</div>
                <div>
                  <div class="fw-semibold" style="color:#1e293b;">{{ $req->user->name }}</div>
                  <div class="text-muted" style="font-size:.75rem;">{{ $req->user->email }}</div>
                </div>
              </div>
            </td>
            <td class="py-3 fw-medium" style="color:#374151;">{{ $req->full_name }}</td>
            <td class="py-3">
              <span class="badge rounded-pill" style="background:#eff6ff;color:#1d4ed8;font-weight:600;font-size:.75rem;padding:5px 10px;">
                {{ $req->bank_name }}
              </span>
            </td>
            <td class="py-3 font-monospace fw-semibold" style="color:#1e293b;letter-spacing:.5px;">{{ $req->bank_account }}</td>
            <td class="py-3" style="color:#374151;">{{ $req->account_holder }}</td>
            <td class="py-3 text-muted" style="white-space:nowrap;">{{ $req->created_at->format('d/m/Y') }}<br><span style="font-size:.72rem;">{{ $req->created_at->format('H:i') }}</span></td>
            <td class="py-3">
              @if($req->status === 'pending')
                <span class="status-pill pill-pending">⏳ Chờ</span>
              @elseif($req->status === 'approved')
                <span class="status-pill pill-approved">✅ Duyệt</span>
              @else
                <span class="status-pill pill-rejected">❌ Từ chối</span>
                @if($req->admin_note)
                  <div class="text-danger mt-1" style="font-size:.72rem;">{{ Str::limit($req->admin_note, 40) }}</div>
                @endif
              @endif
            </td>
            {{-- Actions --}}
            <td class="px-4 py-3 text-center">
              @if($req->status === 'pending')
              <div class="d-flex align-items-center justify-content-center gap-2">
                <form action="{{ route('admin.contributor.approve', $req) }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="action-btn btn-approve"
                          onclick="return confirm('Duyệt và cấp quyền Cộng tác viên cho {{ $req->full_name }}?')">
                    ✅ Duyệt
                  </button>
                </form>
                <button type="button" class="action-btn btn-reject"
                        onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->full_name) }}')">
                  ❌ Từ chối
                </button>
              </div>
              @else
              <span class="text-muted" style="font-size:.78rem;">
                {{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y') : '—' }}
              </span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
    <div class="px-4 py-3 border-top" style="border-color:#f1f5f9 !important;">
      {{ $requests->links() }}
    </div>
    @endif

    @else
    {{-- Empty state --}}
    <div class="empty-state">
      <div class="empty-icon">🤝</div>
      <h6 class="fw-bold text-muted mb-1">Không có yêu cầu nào</h6>
      <p class="text-muted small mb-0">
        @if($status === 'pending') Tất cả yêu cầu đã được xử lý!
        @elseif($status === 'approved') Chưa có yêu cầu nào được duyệt.
        @elseif($status === 'rejected') Chưa có yêu cầu nào bị từ chối.
        @else Chưa có yêu cầu đăng ký nào.
        @endif
      </p>
    </div>
    @endif
  </div>

</div>

{{-- Reject Modal --}}
<div class="modal-overlay" id="rejectModalOverlay">
  <div class="modal-card">
    <div class="p-4" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <h6 class="text-white fw-bold mb-0">❌ Từ chối yêu cầu</h6>
          <p class="text-white mb-0 mt-1 small opacity-75" id="rejectName"></p>
        </div>
        <button onclick="closeRejectModal()" class="btn-close btn-close-white"></button>
      </div>
    </div>
    <form id="rejectForm" method="POST">
      @csrf
      <div class="p-4">
        <label class="form-label fw-semibold small text-muted">Lý do từ chối (tùy chọn)</label>
        <textarea name="admin_note" rows="3" class="form-control rounded-3 border-0 shadow-sm"
                  style="background:#f8fafc;resize:none;"
                  placeholder="Ví dụ: Thông tin ngân hàng không hợp lệ..."></textarea>
      </div>
      <div class="px-4 pb-4 d-flex gap-2">
        <button type="button" onclick="closeRejectModal()"
                class="btn btn-light fw-semibold rounded-3 flex-fill">Hủy</button>
        <button type="submit"
                class="btn btn-danger fw-bold rounded-3 flex-fill">Xác nhận từ chối</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openRejectModal(id, name) {
  document.getElementById('rejectName').textContent = name;
  const base = '{{ url("admin/cong-tac-vien") }}';
  document.getElementById('rejectForm').action = base + '/' + id + '/tu-choi';
  document.getElementById('rejectModalOverlay').classList.add('show');
}
function closeRejectModal() {
  document.getElementById('rejectModalOverlay').classList.remove('show');
}
document.getElementById('rejectModalOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeRejectModal();
});
</script>
@endpush
