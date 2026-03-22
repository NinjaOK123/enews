<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đặt lại mật khẩu — eNews AGU</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  body { background: linear-gradient(135deg, #1a5c38 0%, #2d9e60 100%); min-height: 100vh; }
</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

      {{-- Header --}}
      <div class="bg-gradient-to-r from-[#1a5c38] to-[#2d9e60] px-8 py-8 text-center">
        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h1 class="text-white text-2xl font-bold">Đặt lại mật khẩu</h1>
        <p class="text-white/75 text-sm mt-1">Nhập mật khẩu mới cho tài khoản của bạn</p>
      </div>

      {{-- Body --}}
      <div class="px-8 py-8">

        {{-- Errors --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
          @foreach($errors->all() as $error)
          <p>{{ $error }}</p>
          @endforeach
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
          {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          {{-- Email --}}
          <div class="mb-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ Email</label>
            <input type="email" name="email" value="{{ $email }}" required readonly
              class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-base text-gray-500 cursor-not-allowed">
          </div>

          {{-- New password --}}
          <div class="mb-5" x-data="{ show: false }">
            <label class="block text-sm font-bold text-gray-700 mb-2">
              Mật khẩu mới <span class="text-gray-400 font-normal text-xs">(tối thiểu 8 ký tự)</span>
            </label>
            <div class="relative">
              <input :type="show ? 'text' : 'password'" name="password" required
                placeholder="••••••••"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base pr-12
                       focus:outline-none focus:ring-2 focus:ring-[#2a7a27] focus:border-transparent transition
                       @error('password') border-red-400 bg-red-50 @enderror">
              <button type="button" @click="show = !show"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
            @error('password')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
          </div>

          {{-- Confirm --}}
          <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
            <input type="password" name="password_confirmation" required
              placeholder="••••••••"
              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base
                     focus:outline-none focus:ring-2 focus:ring-[#2a7a27] focus:border-transparent transition">
          </div>

          <button type="submit"
            class="w-full bg-[#2a7a27] hover:bg-[#1e5c1c] text-white font-bold
                   py-3 px-4 rounded-xl transition text-base">
            🔑 Cập nhật mật khẩu
          </button>
        </form>

        <div class="text-center mt-6">
          <a href="{{ route('login') }}" class="text-sm text-[#2a7a27] hover:underline font-semibold">
            ← Quay lại đăng nhập
          </a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
