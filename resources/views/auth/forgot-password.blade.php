<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quên mật khẩu — eNews AGU</title>
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
              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
          </svg>
        </div>
        <h1 class="text-white text-2xl font-bold">Quên mật khẩu?</h1>
        <p class="text-white/75 text-sm mt-1">Nhập email để nhận link đặt lại mật khẩu</p>
      </div>

      {{-- Body --}}
      <div class="px-8 py-8">

        {{-- Success --}}
        @if(session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 flex items-start gap-3 text-sm">
          <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span>{{ session('status') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
          {{ session('error') }}
        </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('password.email') }}" method="POST">
          @csrf
          <div class="mb-5">
            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ Email</label>
            <input type="email" id="email" name="email"
              value="{{ old('email') }}" required autofocus
              placeholder="example@agu.edu.vn"
              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base
                     focus:outline-none focus:ring-2 focus:ring-[#2a7a27] focus:border-transparent transition
                     @error('email') border-red-400 bg-red-50 @enderror">
            @error('email')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit"
            class="w-full bg-[#2a7a27] hover:bg-[#1e5c1c] text-white font-bold
                   py-3 px-4 rounded-xl transition text-base">
            📧 Gửi link đặt lại mật khẩu
          </button>
        </form>

        {{-- Back --}}
        <div class="text-center mt-6">
          <a href="{{ route('login') }}"
             class="text-sm text-[#2a7a27] hover:underline font-semibold">
            ← Quay lại đăng nhập
          </a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
