@extends('layouts.guest')
@section('title', 'Quên mật khẩu — E-News Đại học An Giang')

@section('content')
<!-- Thẻ Glassmorphism -->
<div class="relative bg-white/10 backdrop-blur-xl border border-white/30 shadow-[0_8px_32px_rgba(0,0,0,0.3)] rounded-[1.5rem] p-8 sm:p-10 overflow-hidden">
    
    <!-- Ánh sáng viền trên cùng (tạo khối 3D) -->
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent"></div>

    {{-- Phần nhận diện thương hiệu / Header --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
            <i class="bi bi-key-fill text-3xl text-emerald-400 drop-shadow-md"></i>
        </div>
        <h1 class="text-[1.8rem] font-extrabold text-white tracking-wide mb-1 flex items-center justify-center" style="font-family:'Manrope',sans-serif; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
            Quên mật khẩu?
        </h1>
        <p class="text-[0.9rem] font-medium text-white/80 tracking-wide drop-shadow-sm mt-2">
            Nhập email của bạn để nhận liên kết đặt lại mật khẩu.
        </p>
    </div>

    {{-- Hiển thị thông báo (nếu có) --}}
    @if(session('status'))
    <div class="bg-emerald-500/20 backdrop-blur-md border border-emerald-500/50 text-emerald-100 px-4 py-3 rounded-xl shadow-lg mb-6 text-sm flex items-start animate-fade-in-up">
        <i class="bi bi-check-circle-fill mr-2 mt-0.5 text-emerald-400"></i>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/20 backdrop-blur-md border border-red-500/50 text-red-100 px-4 py-3 rounded-xl shadow-lg mb-6 text-sm flex items-start animate-fade-in-up">
        <i class="bi bi-exclamation-triangle-fill mr-2 mt-0.5 text-red-400"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('password.email') }}" class="space-y-6" novalidate>
        @csrf

        {{-- Email --}}
        <div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-[18px] flex items-center pointer-events-none">
                    <i class="bi bi-envelope-fill text-white/60 group-focus-within:text-emerald-400 font-semibold text-lg transition-colors duration-300"></i>
                </div>
                <!-- Input glass: bg trong suốt nhẹ, viền trắng mờ -->
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="w-full bg-black/20 border border-white/20 text-white placeholder-white/50 rounded-xl pl-12 pr-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-black/30 transition-all duration-300 shadow-inner block"
                       placeholder="Địa chỉ Email">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-400 animate-fade-in-up font-medium drop-shadow-sm"><i class="bi bi-info-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button (Gradient) --}}
        <button type="submit" class="relative w-full overflow-hidden group rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.6)] transition-all duration-300">
            <!-- Nền gradient chạy từ Xanh lục sang Xanh ngọc -->
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-green-500 opacity-90 group-hover:opacity-100 transition-opacity duration-300"></div>
            <!-- Lớp phủ sáng tạo hiệu ứng bóng bẩy -->
            <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent"></div>
            
            <div class="relative flex items-center justify-center px-4 py-[14px] text-[1.05rem] font-bold text-white tracking-wide">
                <span>Gửi link khôi phục</span>
                <i class="bi bi-send-fill mt-[2px] ml-2 text-lg group-hover:scale-110 transition-transform duration-300"></i>
            </div>
        </button>
    </form>

    {{-- Dấu phân cách --}}
    <div class="mt-8 mb-4 border-t border-white/20"></div>

    {{-- Quay lại đăng nhập --}}
    <div class="text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-semibold text-emerald-300 hover:text-white transition-colors duration-200" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
            <i class="bi bi-arrow-left-short text-xl leading-none mr-1"></i>
            Quay lại trang Đăng nhập
        </a>
    </div>
</div>
@endsection
