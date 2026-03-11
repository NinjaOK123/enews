@extends('layouts.admin')
@section('title', 'Quản lý chuyên mục')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Quản lý chuyên mục</h3>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm chuyên mục</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên chuyên mục</th>
                        <th>Slug</th>
                        <th>Bài viết</th>
                        <th>Trạng thái hiển thị</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->slug }}</td>
                        <td><span class="badge bg-secondary">{{ $category->posts_count }}</span></td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Đang hiện</span>
                            @else
                                <span class="badge bg-secondary">Đang ẩn</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Chắc chắn xoá chuyên mục?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">Chưa có chuyên mục nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $categories->links() }}
    </div>
</div>
@endsection

