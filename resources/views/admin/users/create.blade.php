@extends('layouts.admin')
@section('title', 'Thêm người dùng')
@section('content')
<div class="container mt-4" style="max-width: 600px;">
    <h3>Thêm người dùng</h3>
    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Họ tên</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="mb-3">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Vai trò</label>
                    <select name="role" class="form-select" required>
                        <option value="admin">Quản trị viên (Admin)</option>
                        <option value="editor">Biên tập viên (Editor)</option>
                        <option value="contributor">Cộng tác viên (Contributor)</option>
                        <option value="reader">Người đọc (Reader)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Trạng thái</label>
                    <select name="status" class="form-select" required>
                        <option value="active">Active (Hoạt động)</option>
                        <option value="inactive">Inactive (Khóa)</option>
                    </select>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Lưu người dùng</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

