@extends('layouts.admin')
@section('title', 'Quản lý Bình luận')

@section('content')
<div class="container-fluid p-0">

    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-1">
                <i class="bi bi-chat-dots text-success me-2"></i>Quản lý Bình luận
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-2" style="font-size:0.65rem; vertical-align: middle;">
                        {{ $pendingCount }} chờ duyệt
                    </span>
                @endif
            </h2>
            <p class="text-muted mb-0">Duyệt, ẩn và xoá bình luận từ người dùng</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="card border-0 shadow-sm mb-0" style="border-radius: 16px;">
        <div class="card-body p-0">
            {{-- Tab Nav --}}
            <div class="border-bottom px-4 pt-3 d-flex gap-1">
                @foreach([
                    'pending'  => ['label' => 'Chờ duyệt',  'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                    'approved' => ['label' => 'Đã duyệt',   'icon' => 'bi-check-circle',    'color' => 'success'],
                    'rejected' => ['label' => 'Từ chối',    'icon' => 'bi-x-circle',        'color' => 'danger'],
                    'all'      => ['label' => 'Tất cả',     'icon' => 'bi-list-ul',         'color' => 'secondary'],
                ] as $key => $tab)
                <a href="{{ route('admin.comments.index', ['status' => $key]) }}"
                   class="btn btn-sm fw-semibold px-3 py-2 rounded-top-3 border-0 mb-0 {{ $status === $key ? 'bg-'.$tab['color'].' text-white' : 'text-muted bg-light' }}"
                   style="border-bottom: 3px solid {{ $status === $key ? 'var(--bs-'.$tab['color'].')' : 'transparent' }}; border-radius: 8px 8px 0 0 !important;">
                    <i class="bi {{ $tab['icon'] }} me-1"></i>
                    {{ $tab['label'] }}
                    @if($key === 'pending' && $pendingCount > 0)
                        <span class="badge bg-danger ms-1 rounded-pill" style="font-size:.65rem;">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endforeach
            </div>

            {{-- Table --}}
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px">#</th>
                                <th style="width:180px">Người bình luận</th>
                                <th>Nội dung</th>
                                <th style="width:200px">Bài viết</th>
                                <th style="width:110px">Trạng thái</th>
                                <th style="width:110px">Ngày đăng</th>
                                <th style="width:130px" class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $comment)
                            <tr class="{{ $comment->is_approved === null ? 'table-warning bg-opacity-25' : ($comment->is_approved === false ? 'table-danger bg-opacity-10' : '') }}">
                                <td class="text-muted small">{{ $comment->id }}</td>

                                {{-- User --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px; height:32px; border-radius:50%; background:var(--agu-primary, #198754); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.8rem; flex-shrink:0;">
                                            {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold small">{{ $comment->user->name ?? 'Ẩn danh' }}</div>
                                            <div class="text-muted" style="font-size:.75rem;">{{ $comment->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nội dung --}}
                                <td>
                                    <p class="mb-0" style="font-size:.9rem; line-height:1.5;">
                                        {{ Str::limit($comment->content, 120) }}
                                    </p>
                                </td>

                                {{-- Bài viết --}}
                                <td>
                                    @if($comment->post)
                                        <a href="{{ route('post.show', $comment->post) }}"
                                           target="_blank"
                                           class="text-decoration-none text-success small fw-semibold"
                                           title="{{ $comment->post->title }}">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>
                                            {{ Str::limit($comment->post->title, 40) }}
                                        </a>
                                    @else
                                        <span class="text-muted small">Bài đã xoá</span>
                                    @endif
                                </td>

                                {{-- Trạng thái --}}
                                <td>
                                    @if($comment->is_approved === true)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">
                                            <i class="bi bi-check-circle me-1"></i>Đã duyệt
                                        </span>
                                    @elseif($comment->is_approved === false)
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold">
                                            <i class="bi bi-x-circle me-1"></i>Từ chối
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-20 text-warning fw-semibold">
                                            <i class="bi bi-hourglass-split me-1"></i>Chờ duyệt
                                        </span>
                                    @endif
                                </td>

                                {{-- Ngày --}}
                                <td class="text-muted small">{{ $comment->created_at->format('d/m/Y') }}<br>{{ $comment->created_at->format('H:i') }}</td>

                                {{-- Actions --}}
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        {{-- Nút Duyệt (chỉ hiện nếu chưa duyệt hoặc đã từ chối) --}}
                                        @if($comment->is_approved !== true)
                                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Duyệt">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Nút Từ chối (chỉ hiện nếu chưa từ chối) --}}
                                        @if($comment->is_approved !== false)
                                            <form action="{{ route('admin.comments.reject', $comment) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Từ chối/Ẩn">
                                                    <i class="bi bi-x-lg text-dark"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Xoá --}}
                                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                                              onsubmit="return confirm('Xoá vĩnh viễn bình luận này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Xoá">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-slash fs-1 d-block mb-3 opacity-25"></i>
                                    @if($status === 'pending')
                                        Không có bình luận nào đang chờ duyệt. 🎉
                                    @elseif($status === 'approved')
                                        Chưa có bình luận nào được duyệt.
                                    @elseif($status === 'rejected')
                                        Tuyệt vời, không có bình luận nào bị từ chối!
                                    @else
                                        Chưa có bình luận nào trong hệ thống.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($comments->hasPages())
                    <div class="mt-4">
                        {{ $comments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
