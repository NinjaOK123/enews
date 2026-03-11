@extends('layouts.admin')
@section('title', 'Sửa người dùng')
@section('content')
<div class="container mt-4" style="max-width: 600px;">
    <h3>Sửa người dùng: {{ $user->name }}</h3>
    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label>Họ tên</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                </div>
                <div class="mb-3">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" required value="{{ old('username', $user->username) }}">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
                </div>
                <div class="mb-3">
                    <label>Tiểu sử/Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                        <option value="editor" {{ $user->role === 'editor' ? 'selected' : '' }}>Biên tập viên</option>
                        <option value="contributor" {{ $user->role === 'contributor' ? 'selected' : '' }}>Cộng tác viên</option>
                        <option value="reader" {{ $user->role === 'reader' ? 'selected' : '' }}>Người đọc</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Trạng thái</label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Mật khẩu mới (bỏ trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Trở về</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

