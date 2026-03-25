@extends('layouts.admin')
@section('title', 'Lịch sử chỉnh sửa bài viết')
@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.posts.index') }}" class="w-10 h-10 flex flex-col items-center justify-center rounded-full bg-white shadow-sm border border-gray-100 text-gray-500 hover:text-green-600 hover:bg-green-50 transition-colors">
            <i class="bi bi-arrow-left text-lg leading-none"></i>
        </a>
        <div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Lịch sử chỉnh sửa</h3>
            <p class="text-sm text-gray-500 mt-1">Bài viết: <span class="font-medium text-gray-700">{{ $post->title }}</span></p>
        </div>
    </div>

    <!-- Timeline Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 sm:p-8">
        @if($revisions->count() > 0)
        <div class="relative border-l-2 border-green-100 ml-3 md:ml-4 space-y-8">
            @foreach($revisions as $index => $revision)
            <div class="relative pl-8 md:pl-10">
                <!-- Dot -->
                <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white border-4 border-green-500 shadow-sm z-10"></div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-100 to-emerald-200 text-emerald-800 font-bold flex items-center justify-center text-xs shadow-sm shrink-0">
                            {{ strtoupper(substr($revision->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $revision->user->name ?? 'Người dùng không xác định' }}</p>
                            <p class="text-[11px] text-gray-500 font-medium">{{ $revision->user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="text-xs font-semibold text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg whitespace-nowrap">
                        <i class="bi bi-clock me-1"></i> {{ $revision->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>

                <div class="mt-3 p-4 bg-gray-50/80 rounded-xl border border-gray-100 text-sm text-gray-600">
                    <p class="font-medium text-gray-700 mb-1">Bản lưu Rev #{{ $revision->revision_number ?? ($revisions->count() - $index) }}</p>
                    <p class="text-[13px] opacity-80">Nội dung đã được ghi nhận vào cơ sở dữ liệu.</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                <i class="bi bi-clock-history text-3xl text-gray-400"></i>
            </div>
            <p class="font-medium text-[15px] text-gray-600">Chưa có bản ghi lịch sử nào.</p>
            <p class="text-sm text-gray-400 mt-1">Hệ thống chưa ghi nhận lần chỉnh sửa nào cho bài viết này.</p>
        </div>
        @endif
    </div>
</div>
@endsection
