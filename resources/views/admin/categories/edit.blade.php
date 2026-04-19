@extends('layouts.admin')
@section('title', 'Sửa chuyên mục')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-zinc-100 tracking-tight transition-colors">Sửa chuyên mục</h3>
            <p class="text-sm text-slate-500 dark:text-zinc-400 font-medium mt-1 transition-colors">Cập nhật thông tin chuyên mục: <strong class="text-emerald-600 dark:text-emerald-400 border-b border-emerald-500/30 pb-0.5">{{ $category->name }}</strong></p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 rounded-xl text-sm font-semibold transition-colors shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 overflow-hidden transition-colors">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6 sm:p-8" x-data="{ isSubCategory: {{ $category->parent_id ? 'true' : 'false' }} }">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2 transition-colors">Tên chuyên mục <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required 
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
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-red-500 dark:text-red-400 text-sm mt-1 transition-colors">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-8">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 bg-gray-100 dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 dark:checked:bg-emerald-500 cursor-pointer transition-colors">
                    <div>
                        <span class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 transition-colors">Trạng thái công khai</span>
                        <span class="block text-xs text-gray-500 dark:text-zinc-500 mt-0.5 transition-colors">Bật để cho phép chuyên mục hiển thị lên trang chủ</span>
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-zinc-800 transition-colors">
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-semibold rounded-xl transition-colors">Hủy</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-emerald-500/20 transition-all">
                    Lưu cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
