@extends('layouts.admin')
@section('title', 'Lịch sử chỉnh sửa bài viết')
@section('content')

<div class="max-w-4xl mx-auto p-4 md:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.posts.index') }}" class="w-10 h-10 flex flex-col items-center justify-center rounded-full bg-white dark:bg-zinc-900 shadow-sm border border-gray-200 dark:border-white/10 text-gray-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-all">
            <i class="bi bi-arrow-left text-lg leading-none"></i>
        </a>
        <div>
            <h3 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Lịch sử chỉnh sửa</h3>
            <p class="text-[14px] text-zinc-500 dark:text-zinc-400 font-medium mt-1">Bài viết: <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $post->title }}</span></p>
        </div>
    </div>

    <!-- Timeline Card -->
    <div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md rounded-2xl shadow-sm border border-zinc-200 dark:border-white/10 overflow-hidden p-6 sm:p-8 transition-colors">
        @if($revisions->count() > 0)
        <div class="relative border-l border-zinc-200 dark:border-white/10 ml-4 space-y-8">
            @foreach($revisions as $index => $revision)
            <div class="relative pl-8">
                <!-- Avatar Dot -->
                <div class="absolute -left-[20px] top-0 w-10 h-10 rounded-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 shadow-sm z-10 flex items-center justify-center overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-500/20 dark:to-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold flex items-center justify-center text-[13px]">
                        {{ strtoupper(substr($revision->user->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
                
                <!-- Revision Content Card -->
                <div class="bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 rounded-xl p-5 hover:border-emerald-200 dark:hover:border-emerald-500/30 transition-colors group">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="text-[14px] font-bold text-zinc-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $revision->user->name ?? 'Người dùng không xác định' }}</p>
                            <p class="text-[12px] text-zinc-500 dark:text-zinc-400 font-medium mt-0.5">{{ $revision->user->email ?? '' }}</p>
                        </div>
                        <div class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 px-3 py-1.5 rounded-lg whitespace-nowrap shadow-sm">
                            <i class="bi bi-clock me-1 text-zinc-400 dark:text-zinc-500"></i> {{ $revision->created_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900/50 rounded-lg border border-zinc-100 dark:border-white/5 p-4 mt-2">
                        <p class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-200 mb-1">Bản lưu Rev #{{ $revision->revision_number ?? ($revisions->count() - $index) }}</p>
                        <p class="text-[13px] text-zinc-600 dark:text-zinc-400">Nội dung đã được cập nhật và lưu vào cơ sở dữ liệu.</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-zinc-50 dark:bg-white/5 flex items-center justify-center mb-4 border border-zinc-100 dark:border-white/5 shadow-sm">
                <i class="bi bi-clock-history text-3xl text-zinc-400 dark:text-zinc-500"></i>
            </div>
            <p class="font-semibold text-[15px] text-zinc-700 dark:text-zinc-300">Chưa có bản ghi lịch sử nào.</p>
            <p class="text-[13px] text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm">Hệ thống chưa ghi nhận lần chỉnh sửa nào cho bài viết này.</p>
        </div>
        @endif
    </div>
</div>
@endsection
