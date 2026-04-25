@extends('layouts.app')

@section('title', 'Ngoại tuyến - Mất nối mạng')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-16 text-center"
     data-aos="fade-up" data-aos-duration="600">
    
    <div class="inline-flex items-center justify-center w-24 h-24 sm:w-32 sm:h-32 mb-6 rounded-full bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400">
        <i class="bi bi-wifi-off text-5xl sm:text-7xl"></i>
    </div>

    <h1 class="text-3xl sm:text-4xl font-black text-gray-800 dark:text-zinc-100 mb-4 tracking-tight">
        Bạn Đang Ngoại Tuyến!
    </h1>
    
    <p class="text-base sm:text-lg text-gray-500 dark:text-zinc-400 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
        Có vẻ như thiết bị của bạn đã mất kết nối Internet. Đừng lo lắng định tuyến, bạn vẫn có thể sử dụng nút bên dưới để quay lại đọc những tin tức, bài báo đã được lưu tạm trong bộ nhớ đệm (Cache) của trình duyệt.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button onclick="window.location.reload()"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3.5 bg-[#2a7a27] hover:bg-[#1a4a18] dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
            <i class="bi bi-arrow-clockwise"></i> Thử kết nối lại
        </button>
        
        <a href="{{ route('home') }}"
           class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-bold rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition-all duration-300">
            <i class="bi bi-journal-text border-zinc-700"></i> Xem tin đã lưu (Cache)
        </a>
    </div>

</div>

<script>
    // Tự động tải lại trang nếu phát hiện có mạng trở lại
    window.addEventListener('online', function() {
        window.location.reload();
    });
</script>
@endsection
