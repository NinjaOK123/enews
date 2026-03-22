@extends('layouts.admin')
@section('title', 'Yêu cầu Cộng tác viên')

@section('content')
<div class="max-w-full">

    {{-- ── Top Stats Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; background:linear-gradient(135deg,#fef3c7,#fde68a);">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div style="font-size:2rem;">⏳</div>
                    <div>
                        <div style="font-size:2rem;font-weight:800;color:#92400e;line-height:1;">{{ $counts['pending'] }}</div>
                        <div style="font-size:.8rem;color:#b45309;font-weight:600;">Chờ duyệt</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; background:linear-gradient(135deg,#d1fae5,#a7f3d0);">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div style="font-size:2rem;">✅</div>
                    <div>
                        <div style="font-size:2rem;font-weight:800;color:#065f46;line-height:1;">{{ $counts['approved'] }}</div>
                        <div style="font-size:.8rem;color:#047857;font-weight:600;">Đã duyệt</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; background:linear-gradient(135deg,#fee2e2,#fecaca);">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div style="font-size:2rem;">❌</div>
                    <div>
                        <div style="font-size:2rem;font-weight:800;color:#991b1b;line-height:1;">{{ $counts['rejected'] }}</div>
                        <div style="font-size:.8rem;color:#dc2626;font-weight:600;">Từ chối</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px; background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div style="font-size:2rem;">📋</div>
                    <div>
                        <div style="font-size:2rem;font-weight:800;color:#4c1d95;line-height:1;">{{ $counts['pending'] + $counts['approved'] + $counts['rejected'] }}</div>
                        <div style="font-size:.8rem;color:#6d28d9;font-weight:600;">Tổng cộng</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Buttons ── --}}
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <span class="fw-bold text-secondary me-1">Lọc:</span>
        @foreach([
            'pending'  => ['label' => 'Chờ duyệt', 'icon' => '⏳', 'cls' => 'btn-warning'],
            'approved' => ['label' => 'Đã duyệt',  'icon' => '✅', 'cls' => 'btn-success'],
            'rejected' => ['label' => 'Từ chối',   'icon' => '❌', 'cls' => 'btn-danger'],
            'all'      => ['label' => 'Tất cả',    'icon' => '📋', 'cls' => 'btn-secondary'],
        ] as $s => $opt)
        <a href="{{ route('admin.contributor.index', ['status' => $s]) }}"
           class="btn {{ $status === $s ? $opt['cls'] : 'btn-outline-secondary' }} rounded-pill px-4 fw-semibold"
           style="font-size:.9rem;">
            {{ $opt['icon'] }} {{ $opt['label'] }}
            @if($s !== 'all' && $counts[$s] > 0)
                <span class="badge {{ $status === $s ? 'bg-white text-dark' : 'bg-dark text-white' }} ms-1 rounded-pill" style="font-size:.7rem;">{{ $counts[$s] }}</span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm py-3 px-4 mb-4" style="font-size:.95rem;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- ── Table ── --}}
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.9rem;">
                <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                    <tr>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Người dùng</th>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Họ tên</th>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Ngân hàng</th>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Số TK</th>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Chủ TK</th>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Ghi chú</th>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Ngày gửi</th>
                        <th class="px-4 py-3 fw-bold text-secondary" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Trạng thái</th>
                        <th class="px-4 py-3 fw-bold text-secondary text-center" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-semibold text-dark">{{ $req->user->name }}</div>
                            <div class="text-muted" style="font-size:.8rem;">{{ $req->user->email }}</div>
                        </td>
                        <td class="px-4 py-3 fw-medium">{{ $req->full_name }}</td>
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-dark border" style="font-size:.82rem;">{{ $req->bank_name }}</span>
                        </td>
                        <td class="px-4 py-3 font-monospace fw-semibold">{{ $req->bank_account }}</td>
                        <td class="px-4 py-3">{{ $req->account_holder }}</td>
                        <td class="px-4 py-3 text-muted" style="max-width:160px;">
                            <div class="text-truncate">{{ $req->note ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-muted" style="white-space:nowrap;">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            @if($req->status === 'pending')
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2">⏳ Chờ duyệt</span>
                            @elseif($req->status === 'approved')
                                <span class="badge rounded-pill bg-success px-3 py-2">✅ Đã duyệt</span>
                            @else
                                <span class="badge rounded-pill bg-danger px-3 py-2">❌ Từ chối</span>
                                @if($req->admin_note)
                                <div class="text-danger mt-1" style="font-size:.78rem;">{{ $req->admin_note }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($req->status === 'pending')
                            <div class="d-flex gap-2 justify-content-center" x-data="{ rejectOpen:false }">
                                {{-- Approve --}}
                                <form action="{{ route('admin.contributor.approve', $req) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Duyệt và cấp quyền Cộng tác viên cho {{ $req->full_name }}?')"
                                            class="btn btn-success btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                                        <i class="bi bi-check-lg me-1"></i>Duyệt
                                    </button>
                                </form>
                                {{-- Reject --}}
                                <button @click="rejectOpen=true" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                                    <i class="bi bi-x-lg me-1"></i>Từ chối
                                </button>
                                {{-- Reject Modal --}}
                                <div x-show="rejectOpen" x-cloak
                                     class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                     style="background:rgba(0,0,0,.5);z-index:9999;">
                                    <div class="bg-white rounded-4 shadow-lg p-4" style="width:100%;max-width:400px;margin:1rem;" @click.stop>
                                        <h6 class="fw-bold mb-3">Lý do từ chối</h6>
                                        <form action="{{ route('admin.contributor.reject', $req) }}" method="POST">
                                            @csrf
                                            <textarea name="admin_note" rows="3"
                                                      placeholder="VD: Thông tin ngân hàng không hợp lệ..."
                                                      class="form-control rounded-3 mb-3" style="resize:none;"></textarea>
                                            <div class="d-flex gap-2">
                                                <button type="button" @click="rejectOpen=false" class="btn btn-light flex-fill rounded-pill">Hủy</button>
                                                <button type="submit" class="btn btn-danger flex-fill rounded-pill fw-bold">Xác nhận từ chối</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @else
                                <span class="text-muted" style="font-size:.82rem;">
                                    {{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y') : '—' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <div style="font-size:2.5rem;">📭</div>
                            <div class="mt-2">Không có yêu cầu nào.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
