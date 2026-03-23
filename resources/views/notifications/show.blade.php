@extends('layouts.app')
@section('title', 'Chi tiết thông báo')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Back button --}}
    <a href="javascript:history.back()"
       class="inline-flex items-center gap-2 text-sm font-semibold text-[#2a7a27] hover:underline mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Quay lại
    </a>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#1a5c38] to-[#2d9e60] px-8 py-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 text-2xl">
                    🔔
                </div>
                <div>
                    <h1 class="text-white font-bold text-xl leading-snug">{{ $notification->title }}</h1>
                    <p class="text-white/70 text-sm mt-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $notification->sent_at->format('H:i — d/m/Y') }}
                        <span class="opacity-50">•</span>
                        {{ $notification->sent_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Nội dung --}}
        <div class="px-8 py-8">
            <div class="prose prose-green max-w-none text-gray-700 leading-relaxed text-base"
                 style="line-height:1.8;">
                {!! $notification->content !!}
            </div>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 px-8 py-4 bg-gray-50 flex items-center justify-between">
            <span class="text-xs text-gray-400">✅ Đã đọc</span>
            <a href="{{ route('home') }}"
               class="text-sm font-semibold text-[#2a7a27] hover:underline">
                Về trang chủ →
            </a>
        </div>
    </div>

</div>
@endsection
