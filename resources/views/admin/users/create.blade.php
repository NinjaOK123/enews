@extends('layouts.admin')
@section('title', 'Thêm người dùng')
@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Thêm người dùng mới</h3>
            <p class="text-sm text-gray-500 mt-1">Khởi tạo tài khoản và phân quyền cho hệ thống</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i> Trở về
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="p-6 md:p-8 space-y-6">
                <!-- Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Họ tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nhập họ tên đầy đủ..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all placeholder:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Tên đăng nhập <span class="text-red-500">*</span></label>
                        <input type="text" name="username" required value="{{ old('username') }}" placeholder="Ví dụ: nva.admin" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all placeholder:text-gray-400">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email') }}" placeholder="name@domain.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all placeholder:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Mật khẩu <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="Nhập mật khẩu an toàn..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all placeholder:text-gray-400">
                    </div>
                </div>

                <div class="w-full h-px bg-gray-100 my-4"></div>

                <!-- Row 3 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Vai trò hệ thống</label>
                        <div class="relative group">
                            <select name="role" required class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all appearance-none cursor-pointer font-medium text-gray-700 hover:border-emerald-300">
                                <option value="admin">Quản trị viên (Admin)</option>
                                <option value="editor">Biên tập viên (Editor)</option>
                                <option value="contributor">Cộng tác viên (Contributor)</option>
                                <option value="reader">Người đọc (Reader)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400 group-hover:text-emerald-500 transition-colors"><i class="bi bi-chevron-down text-[10px]"></i></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Trạng thái tài khoản</label>
                        <div class="relative group">
                            <select name="status" required class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all appearance-none cursor-pointer font-medium text-gray-700 hover:border-emerald-300">
                                <option value="active">Active (Hoạt động)</option>
                                <option value="inactive">Inactive (Bị khóa)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400 group-hover:text-emerald-500 transition-colors"><i class="bi bi-chevron-down text-[10px]"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 rounded-xl transition-colors shadow-sm">
                    Hủy bỏ
                </a>
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-xl shadow-sm transition-all shadow-emerald-200 focus:ring-2 focus:ring-emerald-200 focus:outline-none">
                    <i class="bi bi-person-check-fill"></i> Lưu người dùng
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

