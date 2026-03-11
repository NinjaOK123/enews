@extends('layouts.admin')
@section('title', 'Quản lý Thông báo')

@section('content')
<div class="container-fluid p-0">

    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-1"><i class="bi bi-bell text-success me-2"></i>Quản lý Thông báo</h2>
            <p class="text-muted mb-0">Tạo và gửi thông báo đến người dùng hệ thống</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-success fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Tạo thông báo mới
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung ngắn</th>
                            <th>Đối tượng</th>
                            <th>Đã gửi</th>
                            <th>Ngày tạo</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                        <tr>
                            <td class="text-muted">{{ $notification->id }}</td>
                            <td class="fw-semibold">{{ Str::limit($notification->title, 40) }}</td>
                            <td class="text-muted">{{ Str::limit(strip_tags($notification->content), 60) }}</td>
                            <td>
                                @foreach($notification->recipients as $r)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary me-1">{{ $r }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($notification->sent_at)
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="bi bi-check-circle me-1"></i>Đã gửi
                                    </span>
                                    <small class="d-block text-muted mt-1">{{ $notification->sent_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-clock me-1"></i>Chưa gửi
                                    </span>
                                @endif
                            </td>
                            <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    {{-- Edit (chỉ cho phép nếu chưa gửi) --}}
                                    @unless($notification->sent_at)
                                        <a href="{{ route('admin.notifications.edit', $notification) }}"
                                           class="btn btn-sm btn-light border text-primary" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endunless

                                    {{-- Gửi --}}
                                    @unless($notification->sent_at)
                                        <form action="{{ route('admin.notifications.send', $notification) }}"
                                              method="POST"
                                              onsubmit="return confirm('Gửi thông báo «{{ addslashes($notification->title) }}» ngay bây giờ?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Gửi ngay">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </form>
                                    @endunless

                                    {{-- Xóa --}}
                                    <form action="{{ route('admin.notifications.destroy', $notification) }}"
                                          method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn xoá thông báo này không?')">
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
                                <i class="bi bi-bell-slash fs-1 d-block mb-3 opacity-25"></i>
                                Chưa có thông báo nào. <a href="{{ route('admin.notifications.create') }}">Tạo ngay</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
