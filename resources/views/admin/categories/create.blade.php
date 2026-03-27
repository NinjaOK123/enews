@extends('layouts.admin')
@section('title', 'Thêm chuyên mục')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Thêm chuyên mục mới</h3>
            <p class="text-sm text-gray-500 mt-1">Tạo một chuyên mục mới để phân loại bài viết</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 sm:p-8" x-data="{ isSubCategory: false }">
            @csrf
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Tên chuyên mục <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all placeholder-gray-400" 
                       placeholder="VD: Tin thế giới, Công nghệ...">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Cấp bậc chuyên mục -->
            <div class="mb-6 border border-gray-100 rounded-xl p-5 bg-gray-50/50">
                <label class="block text-sm font-semibold text-gray-800 mb-3">Cấp bậc chuyên mục</label>
                <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="_is_sub" value="0" x-model="isSubCategory" @click="isSubCategory = false" class="w-4 h-4 text-emerald-600 bg-white border-gray-300 focus:ring-emerald-500">
                        <span class="text-sm text-gray-700">Đây là mục cha (Gốc)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="_is_sub" value="1" x-model="isSubCategory" @click="isSubCategory = true" class="w-4 h-4 text-emerald-600 bg-white border-gray-300 focus:ring-emerald-500">
                        <span class="text-sm text-gray-700">Đây là mục con (Trực thuộc)</span>
                    </label>
                </div>

                <div x-show="isSubCategory" x-collapse x-cloak>
                    <label for="parent_id" class="block text-sm font-semibold text-gray-700 mb-2 mt-2">Chọn mục cha trực thuộc:</label>
                    <select name="parent_id" id="parent_id" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all cursor-pointer">
                        <option value="">-- Vui lòng chọn mục cha --</option>
                        @foreach($parents ?? [] as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="flex items-center gap-3 cursor-pointer h-full">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 cursor-pointer">
                        <div>
                            <span class="block text-sm font-semibold text-gray-700">Trạng thái công khai</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Bật để cho phép chuyên mục hiển thị</span>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer h-full">
                        <input type="hidden" name="show_in_menu" value="0">
                        <input type="checkbox" name="show_in_menu" value="1" {{ old('show_in_menu', 0) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                        <div>
                            <span class="block text-sm font-semibold text-gray-700">Hiển thị lên Menu Ngang</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Xuất hiện trên thanh điều hướng đầu trang</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-8 w-full sm:w-1/2">
                <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">Thứ tự ưu tiên <span class="text-xs text-gray-500 font-normal">(Số nhỏ xếp trước)</span></label>
                <input type="number" name="order" id="order" value="{{ old('order', 0) }}" 
                       class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all">
                @error('order') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">Hủy</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-emerald-500/20 transition-all">
                    Lưu chuyên mục
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
