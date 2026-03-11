@extends('layouts.admin')
@section('title', 'Quản lý bài viết toàn hệ thống')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Quản lý bài viết toàn hệ thống</h3>
        <!-- Admin can create posts by redirecting to contributor create -->
        <a href="{{ route('contributor.posts.create') }}" class="btn btn-success"><i class="bi bi-pencil-square"></i> Viết bài mới</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Chuyên mục</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td>
                            <strong>{{ \Illuminate\Support\Str::limit($post->title, 50) }}</strong>
                        </td>
                        <td>{{ $post->category->name ?? 'Không có' }}</td>
                        <td>{{ $post->author->name ?? 'Ẩn danh' }}</td>
                        <td>
                            @if($post->status === 'published')
                                <span class="badge bg-success">Đã xuất bản</span>
                            @elseif($post->status === 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @elseif($post->status === 'rejected')
                                <span class="badge bg-danger">Bị từ chối</span>
                            @else
                                <span class="badge bg-secondary">Bản nháp</span>
                            @endif
                        </td>
                        <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('post.show', $post->slug) }}" class="btn btn-sm btn-outline-info" target="_blank">Xem</a>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Xoá bài viết này?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">Chưa có bài viết nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $posts->links() }}
    </div>
</div>
@endsection

