@extends('layouts.admin')
@section('title', 'Lịch sử chỉnh sửa bài viết')
@section('content')

<div class="max-w-4xl mx-auto p-4 md:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.posts.index') }}" class="w-10 h-10 flex flex-col items-center justify-center rounded-full bg-white dark:bg-zinc-800 shadow-sm border border-gray-100 dark:border-zinc-700 text-gray-500 dark:text-zinc-400 hover:text-green-600 dark:hover:text-emerald-400 hover:bg-green-50 dark:hover:bg-emerald-500/10 transition-colors">
            <i class="bi bi-arrow-left text-lg leading-none"></i>
        </a>
        <div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-zinc-100 tracking-tight transition-colors">Lịch sử chỉnh sửa</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium mt-1 transition-colors">Bài viết: <span class="font-bold text-gray-800 dark:text-zinc-200">{{ $post->title }}</span></p>
        </div>
    </div>

    <!-- Timeline Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 overflow-hidden p-6 sm:p-8 transition-colors">
        @if($revisions->count() > 0)
        <div class="relative border-l-2 border-green-100 dark:border-emerald-500/20 ml-3 md:ml-4 space-y-8 transition-colors">
            @foreach($revisions as $index => $revision)
            <div class="relative pl-8 md:pl-10">
                <!-- Dot -->
                <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-zinc-900 border-4 border-green-500 dark:border-emerald-500 shadow-sm z-10 transition-colors"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40 text-emerald-800 dark:text-emerald-400 font-bold flex items-center justify-center text-xs shadow-sm shrink-0 transition-colors">
                            {{ strtoupper(substr($revision->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-zinc-200 transition-colors">{{ $revision->user->name ?? 'Người dùng không xác định' }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-zinc-500 font-medium transition-colors">{{ $revision->user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-zinc-400 bg-gray-50 dark:bg-zinc-950/50 border border-transparent dark:border-zinc-800/80 px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors">
                        <i class="bi bi-clock me-1"></i> {{ $revision->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>

                <div class="mt-3 p-4 bg-gray-50/80 dark:bg-zinc-950/30 rounded-xl border border-gray-100 dark:border-zinc-800/50 text-sm text-gray-600 dark:text-zinc-400 transition-colors">
                    <p class="font-medium text-gray-700 dark:text-zinc-300 mb-1 transition-colors">Bản lưu Rev #{{ $revision->revision_number ?? ($revisions->count() - $index) }}</p>
                    <p class="text-[13px] opacity-80">Nội dung đã được ghi nhận vào cơ sở dữ liệu.</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-12 text-center transition-colors">
            <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-zinc-800 flex items-center justify-center mb-4 transition-colors">
                <i class="bi bi-clock-history text-3xl text-gray-400 dark:text-zinc-500 transition-colors"></i>
            </div>
            <p class="font-medium text-[15px] text-gray-600 dark:text-zinc-400 transition-colors">Chưa có bản ghi lịch sử nào.</p>
            <p class="text-sm text-gray-400 dark:text-zinc-500 mt-1 transition-colors">Hệ thống chưa ghi nhận lần chỉnh sửa nào cho bài viết này.</p>
        </div>
        @endif
    </div>
</div>
@endsection
