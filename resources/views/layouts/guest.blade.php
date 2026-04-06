<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AGU E-News'))</title>

    <!-- Fonts và Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS & JS qua Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Setup màn hình chờ đăng nhập toàn màn hình */
        body {
            font-family: 'Inter', sans-serif;
            background: url('{{ asset("images/campus-bg.png") }}') center center / cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
            /* Lớp filter làm tối nhẹ ảnh nền để Form nổi bật hơn (nếu cần) */
            background-color: #0f172a;
            background-blend-mode: overlay;
        }
    </style>
</head>
<body class="antialiased text-gray-900">
    <div class="w-full max-w-md relative z-10 w-full animate-fade-in-up">
        @yield('content')
    </div>

    <!-- Đoạn CSS bổ sung animation đơn giản nếu web chưa có Alpine -->
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</body>
</html>
