@extends('layouts.admin')
@section('title', 'Thêm chuyên mục')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-zinc-100 tracking-tight transition-colors">Thêm chuyên mục mới</h3>
            <p class="text-sm text-slate-500 dark:text-zinc-400 font-medium mt-1 transition-colors">Tạo một chuyên mục mới để phân loại bài viết</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl text-sm font-semibold transition-colors">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 overflow-hidden transition-colors">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 sm:p-8" x-data="{ isSubCategory: false }">
            @csrf
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2 transition-colors">Tên chuyên mục <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-sm text-gray-900 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-900 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-all placeholder-gray-400 dark:placeholder-zinc-600" 
                       placeholder="VD: Tin thế giới, Công nghệ...">
                @error('name') <p class="text-red-500 dark:text-red-400 text-sm mt-1 transition-colors">{{ $message }}</p> @enderror
            </div>

            <!-- Cấp bậc chuyên mục -->
            <div class="mb-6 border border-gray-100 dark:border-zinc-800/80 rounded-xl p-5 bg-gray-50/50 dark:bg-zinc-950/30 transition-colors">
                <label class="block text-sm font-semibold text-gray-800 dark:text-zinc-200 mb-3 transition-colors">Cấp bậc chuyên mục</label>
                <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="_is_sub" value="0" x-model="isSubCategory" @click="isSubCategory = false" class="w-4 h-4 text-emerald-600 bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-700 focus:ring-emerald-500 dark:focus:ring-emerald-500/50 dark:checked:bg-emerald-500 transition-colors">
                        <span class="text-sm text-gray-700 dark:text-zinc-300 transition-colors">Đây là mục cha (Gốc)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="_is_sub" value="1" x-model="isSubCategory" @click="isSubCategory = true" class="w-4 h-4 text-emerald-600 bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-700 focus:ring-emerald-500 dark:focus:ring-emerald-500/50 dark:checked:bg-emerald-500 transition-colors">
                        <span class="text-sm text-gray-700 dark:text-zinc-300 transition-colors">Đây là mục con (Trực thuộc)</span>
                    </label>
                </div>

                <div x-show="isSubCategory" x-collapse x-cloak>
                    <label for="parent_id" class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2 mt-2 transition-colors">Chọn mục cha trực thuộc:</label>
                    <select name="parent_id" id="parent_id" class="w-full px-4 py-2.5 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-sm text-gray-900 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-900 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-all cursor-pointer">
                        <option value="">-- Vui lòng chọn mục cha --</option>
                        @foreach($parents ?? [] as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-red-500 dark:text-red-400 text-sm mt-1 transition-colors">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="flex items-center gap-3 cursor-pointer h-full">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 bg-gray-100 dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 dark:checked:bg-emerald-500 cursor-pointer transition-colors">
                        <div>
                            <span class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 transition-colors">Trạng thái công khai</span>
                            <span class="block text-xs text-gray-500 dark:text-zinc-500 mt-0.5 transition-colors">Bật để cho phép chuyên mục hiển thị</span>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer h-full">
                        <input type="hidden" name="show_in_menu" value="0">
                        <input type="checkbox" name="show_in_menu" value="1" {{ old('show_in_menu', 0) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 dark:text-blue-500 bg-gray-100 dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 rounded focus:ring-blue-500 dark:focus:ring-blue-500/50 dark:checked:bg-blue-500 cursor-pointer transition-colors">
                        <div>
                            <span class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 transition-colors">Hiển thị lên Menu Ngang</span>
                            <span class="block text-xs text-gray-500 dark:text-zinc-500 mt-0.5 transition-colors">Xuất hiện trên thanh điều hướng đầu trang</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-8 w-full sm:w-1/2">
                <label for="order" class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2 transition-colors">Thứ tự ưu tiên <span class="text-xs text-gray-500 dark:text-zinc-500 font-normal transition-colors">(Số nhỏ xếp trước)</span></label>
                <input type="number" name="order" id="order" value="{{ old('order', 0) }}" 
                       class="w-full px-4 py-2.5 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-sm text-gray-900 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-900 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-all">
                @error('order') <p class="text-red-500 dark:text-red-400 text-sm mt-1 transition-colors">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-zinc-800 transition-colors">
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-semibold rounded-xl transition-colors">Hủy</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-emerald-500/20 transition-all">
                    Lưu chuyên mục
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
