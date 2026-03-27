<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="@yield('meta_description', 'Trang Tin Điện Tử - Trang báo Sinh viên Đại học An Giang')">
  <title>@yield('title', 'Trang chủ') — Trang báo Sinh viên Đại học An Giang</title>

  {{-- Google Font: Be Vietnam Pro (đẹp hơn cho tiếng Việt) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  {{-- Bootstrap Icons (icon-only, no Bootstrap CSS) --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  {{-- Vite: Tailwind v4 + Alpine.js --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Livewire --}}
  @livewireStyles

  {{-- Google Translate hidden banner fix --}}
  <style>
    .goog-te-banner-frame.skiptranslate,
    iframe.goog-te-banner-frame { display: none !important; }
    .VIpgJd-ZVi9od-ORHb-OEVmcd { display: none !important; }
    body { top: 0 !important; position: static !important; font-family: 'Inter', sans-serif; }
    html { margin-top: 0 !important; height: auto !important; }
    #goog-gt-tt, .goog-te-balloon-frame { display: none !important; }
    .goog-text-highlight { background: transparent !important; box-shadow: none !important; }
    .goog-te-gadget { font-size: 0 !important; color: transparent !important; }
    .goog-te-gadget span { display: none !important; }
    /* Bell ring animation */
    @keyframes ring {
      0%,100% { transform: rotate(0); }
      10% { transform: rotate(28deg); }
      20% { transform: rotate(-26deg); }
      30% { transform: rotate(22deg); }
      40% { transform: rotate(-18deg); }
      50% { transform: rotate(14deg); }
    }
    .bell-ringing { display:inline-block; animation: ring 2s ease infinite; transform-origin: top center; }
  </style>

  @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

{{-- ═══ PAGE WRAPPER ═══ --}}
<div class="flex flex-col min-h-screen">

  {{-- ═══ TOP UTILITY BAR ═══ --}}
  <div class="bg-[#1b5e20] text-white text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3 py-1.5">

      {{-- Tên trường --}}
      <div class="hidden sm:flex items-center gap-1.5 text-white/80 truncate">
        <i class="bi bi-building text-[#f5d400]"></i>
        <span>Trường Đại học An Giang — VNU-HCM</span>
      </div>

      {{-- Right: Social + Translate + Date --}}
      <div class="flex items-center gap-3 ml-auto">

        {{-- Social icons --}}
        <div class="flex items-center gap-1.5">
          <a href="https://www.facebook.com/trangbaosinhvientruongdaihocangiang"
             target="_blank" rel="noopener" title="Facebook eNews AGU"
             class="flex items-center justify-center w-6 h-6 rounded bg-white/15 hover:bg-[#1877F2] transition-colors duration-200">
            <i class="bi bi-facebook text-xs"></i>
          </a>
          <a href="https://www.youtube.com/@thuvienaihocangiang7831"
             target="_blank" rel="noopener" title="YouTube Thư viện AGU"
             class="flex items-center justify-center w-6 h-6 rounded bg-white/15 hover:bg-red-600 transition-colors duration-200">
            <i class="bi bi-youtube text-xs"></i>
          </a>
          <a href="mailto:enews@agu.edu.vn" title="Email: enews@agu.edu.vn"
             class="flex items-center justify-center w-6 h-6 rounded bg-white/15 hover:bg-white/30 transition-colors duration-200">
            <i class="bi bi-envelope-fill text-xs"></i>
          </a>
          <span class="w-px h-3.5 bg-white/25 mx-1"></span>
        </div>

        {{-- Google Translate --}}
        <div class="top-bar-lang flex items-center [&_.goog-te-combo]:rounded [&_.goog-te-combo]:border [&_.goog-te-combo]:border-white/35 [&_.goog-te-combo]:px-2 [&_.goog-te-combo]:py-0.5 [&_.goog-te-combo]:text-xs [&_.goog-te-combo]:text-white [&_.goog-te-combo]:bg-white/15 [&_.goog-te-combo]:cursor-pointer [&_.goog-te-combo]:max-w-[120px] [&_.goog-te-combo_option]:bg-[#1b5e20] [&_.goog-te-combo_option]:text-white">
          <div id="google_translate_element"></div>
        </div>

        {{-- Ngày tháng --}}
        <div class="hidden md:flex items-center gap-1.5 text-white/75">
          <i class="bi bi-calendar3 text-[#f5d400]"></i>
          @php
            $topBarDate = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->locale('vi');
            $topBarDateStr = ucwords($topBarDate->isoFormat('dddd')) . ', ' . $topBarDate->isoFormat('D [tháng] M, YYYY');
          @endphp
          <span>{{ $topBarDateStr }}</span>
        </div>

      </div>
    </div>
  </div>

  {{-- ═══ MAIN HEADER (logo + nav + user) ═══ --}}
  <header class="relative z-50 bg-white shadow-md border-b-2 border-[#2a7a27]/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between gap-4 py-4">

        {{-- Logo + Brand --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0 group">
          <img src="{{ asset('images/logo.png') }}"
               alt="Logo Trường Đại học An Giang"
               class="w-12 h-12 sm:w-14 sm:h-14 object-contain rounded-full flex-shrink-0">
          <div class="hidden sm:flex flex-col leading-tight">
            <span class="text-[#2a7a27] font-black text-sm tracking-wide uppercase">Đại học An Giang</span>
            <span class="text-[#FF6600] font-extrabold text-lg italic leading-none">E-News</span>
          </div>
        </a>

        {{-- Desktop Nav (hidden on mobile) --}}
        <nav class="hidden lg:flex items-center gap-2">
          @php
            $navLinks = [
              ['route' => 'home',        'icon' => 'bi-house',             'label' => 'Trang chủ'],
              ['route' => 'about',       'icon' => 'bi-info-circle',       'label' => 'Giới thiệu'],
              ['route' => 'rules',       'icon' => 'bi-file-earmark-text', 'label' => 'Quy định'],
              ['route' => 'contact',     'icon' => 'bi-envelope',          'label' => 'Liên hệ'],
              ['route' => 'doc-suy-ngam','icon' => 'bi-book',             'label' => 'Đọc & Suy ngẫm'],
            ];
          @endphp
          @foreach($navLinks as $nav)
          <a href="{{ route($nav['route']) }}"
             class="relative flex items-center gap-1.5 px-3 py-2.5 text-sm font-semibold transition-all duration-200 group
                    {{ request()->routeIs($nav['route']) ? 'text-[#2a7a27]' : 'text-gray-600 hover:text-[#2a7a27]' }}">
            <i class="bi {{ $nav['icon'] }} text-base"></i>
            <span>{{ $nav['label'] }}</span>
            {{-- Underline slide-in khi hover/active --}}
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#FF6600] rounded-full transition-transform duration-200 origin-left
                         {{ request()->routeIs($nav['route']) ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
          </a>
          @endforeach
        </nav>

        {{-- Right: Auth + Notif + Mobile burger --}}
        <div class="flex items-center gap-2">

          {{-- Auth: Login / User Dropdown via Alpine --}}
          @auth
          <div x-data="{ open: false, showLogout: false }" class="relative">
            <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors duration-200 focus:outline-none">
              @if(auth()->user()->avatar)
                <img src="{{ filter_var(auth()->user()->avatar, FILTER_VALIDATE_URL) ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar) }}"
                     alt="Avatar" class="w-8 h-8 rounded-full object-cover">
              @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2a7a27&color=fff"
                     alt="Avatar" class="w-8 h-8 rounded-full">
              @endif
              <span class="hidden sm:block text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Người dùng' }}</span>
              <i class="bi bi-chevron-down text-xs text-gray-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl py-1 z-50 overflow-hidden border border-gray-100"
                 style="display:none;">

              {{-- Trang chủ --}}
              <a href="{{ route('home') }}"
                 class="flex items-center gap-3 px-4 py-3 text-[15px] font-medium text-gray-700 hover:bg-gray-50 hover:text-[#2a7a27] transition-colors no-underline">
                <i class="bi bi-house text-lg text-[#2a7a27]"></i>
                <span>Trang chủ</span>
              </a>

              {{-- Thông tin cá nhân --}}
              <a href="{{ route('profile') }}"
                 class="flex items-center gap-3 px-4 py-3 text-[15px] font-medium text-gray-700 hover:bg-gray-50 hover:text-[#2a7a27] transition-colors no-underline">
                <i class="bi bi-person-circle text-lg text-[#2a7a27]"></i>
                <span>Thông tin cá nhân</span>
              </a>

              @if(auth()->user()->role !== 'reader')
                @php
                  $dashUrl = match(auth()->user()->role) {
                    'admin'       => route('admin.dashboard'),
                    'editor'      => route('editor.dashboard'),
                    'contributor' => route('contributor.dashboard'),
                    default       => route('home'),
                  };
                @endphp
                <a href="{{ $dashUrl }}"
                   class="flex items-center gap-3 px-4 py-3 text-[15px] font-medium text-gray-700 hover:bg-gray-50 hover:text-[#2a7a27] transition-colors no-underline">
                  <i class="bi bi-speedometer2 text-lg text-[#2a7a27]"></i>
                  <span>Dashboard</span>
                </a>
              @endif

              {{-- Divider --}}
              <div class="border-t border-gray-100 my-1"></div>

              {{-- Đăng xuất --}}
              <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                @csrf
                <button type="button" @click="open = false; showLogout = true"
                        class="w-full flex items-center gap-3 px-4 py-3 text-[15px] font-medium text-red-500 hover:bg-red-50 transition-colors">
                  <i class="bi bi-box-arrow-right text-lg"></i>
                  <span>Đăng xuất</span>
                </button>
              </form>

            </div>

            {{-- Modal xác nhận đăng xuất kiểu TikTok --}}
            <div x-show="showLogout"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                 style="display:none;">
              {{-- Backdrop --}}
              <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showLogout = false"></div>
              {{-- Panel --}}
              <div class="relative w-full max-w-sm bg-white rounded-3xl overflow-hidden shadow-2xl"
                   x-transition:enter="transition ease-out duration-250"
                   x-transition:enter-start="opacity-0 scale-90"
                   x-transition:enter-end="opacity-100 scale-100"
                   @click.stop>
                {{-- Body --}}
                <div class="px-8 pt-10 pb-8 text-center">
                  {{-- Icon --}}
                  <div class="mx-auto mb-4 w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                    <i class="bi bi-box-arrow-left text-3xl text-red-500"></i>
                  </div>
                  <h3 class="text-gray-900 text-xl font-bold mb-2">Đăng xuất</h3>
                  <p class="text-gray-500 text-sm">Bạn có chắc chắn muốn đăng xuất khỏi tài khoản?</p>
                </div>
                {{-- Buttons --}}
                <div class="flex gap-3 px-8 pb-8">
                  <button @click="showLogout = false"
                          class="flex-1 py-3 text-[15px] font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-2xl transition-colors">
                    Hủy
                  </button>
                  <form method="POST" action="{{ route('logout') }}" class="flex-1 m-0">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 text-[15px] font-bold text-white bg-red-500 hover:bg-red-600 rounded-2xl shadow-sm transition-colors">
                      Đăng xuất
                    </button>
                  </form>
                </div>
              </div>
            </div>

          </div>{{-- end x-data --}}
          @else
          <a href="{{ route('login') }}"
             class="flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold text-white
                    bg-[#FF6600] shadow-md hover:bg-[#e55a00] hover:shadow-lg hover:scale-105
                    transition-all duration-200 active:scale-95">
            <i class="bi bi-person-circle"></i>
            <span class="hidden sm:inline">Đăng nhập</span>
          </a>
          @endauth

          {{-- Notification Bell --}}
          @auth
          <div x-data="{ open: false }" class="relative">
            <button @click="open = !open; $event.stopPropagation(); if(open) loadNotifications()"
                    @click.outside="open = false"
                    class="relative flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors duration-200">
              <i class="bi bi-bell text-lg" id="bellIcon"></i>
              <span id="notifBadge"
                    class="hidden absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none border-2 border-white"></span>
            </button>

            {{-- Notification panel --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="absolute right-0 mt-2 w-80 sm:w-[340px] bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50"
                 style="display:none;">
              {{-- Header --}}
              <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-[#1a5c38] to-[#2d9e60]">
                <span class="flex items-center gap-2 text-sm font-bold text-white">
                  <i class="bi bi-bell-fill"></i> Thông báo
                </span>
                <button onclick="markAllRead(event)"
                        class="px-3 py-1 rounded bg-white/20 text-white text-xs font-semibold hover:bg-white/30 transition-colors">
                  ✓ Đọc tất cả
                </button>
              </div>
              {{-- List --}}
              <div id="notifList" class="max-h-[380px] overflow-y-auto">
                <div id="notifLoading" class="flex flex-col items-center justify-center py-10 text-gray-400 gap-2">
                  <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                  <span class="text-xs">Đang tải...</span>
                </div>
              </div>
              {{-- Footer --}}
              <div class="border-t border-gray-100 bg-gray-50 text-center">
                <a href="{{ route('notifications.user.index') }}" class="block py-3 text-xs font-semibold text-[#2a7a27] hover:text-[#1b5e20] transition-colors">
                  Xem tất cả thông báo
                </a>
              </div>
            </div>
          </div>

          <script>
          var _notifLoaded = false;
          var _csrfToken  = '{{ csrf_token() }}';
          var _notifUrl   = '{{ route('notifications.user.index') }}';
          var _markReadUrl  = '/thong-bao/__ID__/doc';
          var _markAllUrl   = '{{ route('notifications.user.markAllRead') }}';

          function loadNotifications(force) {
            if (_notifLoaded && !force) return;
            _notifLoaded = true;
            fetch(_notifUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
              .then(r => r.json())
              .then(data => renderNotifications(data.notifications, data.unread_count))
              .catch(() => {
                document.getElementById('notifList').innerHTML =
                  '<div class="py-8 text-center text-xs text-gray-400">Không thể tải thông báo.</div>';
              });
          }

          function renderNotifications(items, unreadCount) {
            const list  = document.getElementById('notifList');
            const badge = document.getElementById('notifBadge');
            const bell  = document.getElementById('bellIcon');

            if (unreadCount > 0) {
              badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
              badge.classList.remove('hidden');
              bell.classList.add('bell-ringing');
            } else {
              badge.classList.add('hidden');
              bell.classList.remove('bell-ringing');
            }

            if (!items || items.length === 0) {
              list.innerHTML = `<div class="py-10 text-center">
                <i class="bi bi-bell-slash text-3xl text-gray-300 block mb-2"></i>
                <p class="text-sm font-semibold text-gray-500">Không có thông báo mới</p>
              </div>`;
              return;
            }

            list.innerHTML = items.map(n => `
              <a href="/thong-bao/${n.id}"
                 class="flex gap-3 px-4 py-3 border-b border-gray-50 cursor-pointer transition-colors no-underline ${n.is_read ? 'bg-white hover:bg-gray-50' : 'bg-green-50 hover:bg-green-100'}"
                 data-id="${n.id}">
                <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 ${n.is_read ? 'bg-transparent' : 'bg-green-600'}"></div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs ${n.is_read ? 'font-medium' : 'font-bold'} text-gray-800 truncate">${n.title}</p>
                  <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">${n.content}</p>
                  <p class="text-[10px] text-gray-400 mt-1">${n.sent_at}</p>
                </div>
              </a>`).join('');
          }

          function markOneRead(id, el) {
            fetch(_markReadUrl.replace('__ID__', id), {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': _csrfToken, 'Content-Type': 'application/json' }
            });
            el.classList.remove('bg-green-50', 'hover:bg-green-100');
            el.classList.add('bg-white');
            const dot = el.querySelector('.rounded-full');
            if (dot) dot.classList.replace('bg-green-600', 'bg-transparent');
            _notifLoaded = false;
            setTimeout(() => loadNotifications(true), 300);
          }

          function markAllRead(e) {
            e.preventDefault(); e.stopPropagation();
            fetch(_markAllUrl, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': _csrfToken, 'Content-Type': 'application/json' }
            }).then(() => { _notifLoaded = false; loadNotifications(true); });
          }

          document.addEventListener('DOMContentLoaded', () => loadNotifications(true));
          </script>
          @endauth

          {{-- Mobile hamburger for header nav --}}
          <div x-data="{ mobileOpen: false }" class="lg:hidden">
            <button @click="mobileOpen = !mobileOpen"
                    class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
              <i class="bi" :class="mobileOpen ? 'bi-x-lg' : 'bi-list'" class="text-lg"></i>
            </button>

            {{-- Mobile nav menu --}}
            <div x-show="mobileOpen" x-transition
                 class="absolute left-0 right-0 top-full bg-white shadow-lg border-t border-gray-100 z-40 lg:hidden"
                 style="display:none;">
              <nav class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('home') ? 'bg-[#e8f5e2] text-[#2a7a27]' : 'text-gray-700 hover:bg-gray-50' }}">
                  <i class="bi bi-house"></i> Trang chủ
                </a>
                <a href="{{ route('about') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('about') ? 'bg-[#e8f5e2] text-[#2a7a27]' : 'text-gray-700 hover:bg-gray-50' }}">
                  <i class="bi bi-info-circle"></i> Giới thiệu
                </a>
                <a href="{{ route('rules') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('rules') ? 'bg-[#e8f5e2] text-[#2a7a27]' : 'text-gray-700 hover:bg-gray-50' }}">
                  <i class="bi bi-file-earmark-text"></i> Quy định
                </a>
                <a href="{{ route('contact') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('contact') ? 'bg-[#e8f5e2] text-[#2a7a27]' : 'text-gray-700 hover:bg-gray-50' }}">
                  <i class="bi bi-envelope"></i> Liên hệ
                </a>
                <a href="{{ route('doc-suy-ngam') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('doc-suy-ngam') ? 'bg-[#e8f5e2] text-[#2a7a27]' : 'text-gray-700 hover:bg-gray-50' }}">
                  <i class="bi bi-book"></i> Đọc &amp; Suy ngẫm
                </a>
                @auth
                <div class="border-t border-gray-100 my-1"></div>
                <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('profile') ? 'bg-[#e8f5e2] text-[#2a7a27]' : 'text-gray-700 hover:bg-gray-50' }}">
                  <i class="bi bi-person-circle"></i> Xem hồ sơ
                </a>
                @if(auth()->user()->role !== 'reader')
                @php
                  $mobileDashUrl = match(auth()->user()->role) {
                    'admin'       => route('admin.dashboard'),
                    'editor'      => route('editor.dashboard'),
                    'contributor' => route('contributor.dashboard'),
                    default       => route('home'),
                  };
                @endphp
                <a href="{{ $mobileDashUrl }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                  <i class="bi bi-speedometer2 text-[#2a7a27]"></i> Dashboard
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold text-red-500 hover:bg-red-50">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                  </button>
                </form>
                @endauth
                @guest
                <a href="{{ route('login') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold text-[#2a7a27] hover:bg-[#e8f5e2]">
                  <i class="bi bi-person-circle"></i> Đăng nhập
                </a>
                @endguest
              </nav>
            </div>
          </div>

        </div>
      </div>
    </div>
  </header>

  {{-- ═══ CATEGORY NAV (green sticky bar) ═══ --}}
  @php
    $allNavCategories = $navCategories ?? \App\Models\Category::active()->roots()->get();
    // Lấy các danh mục được đánh dấu show_in_menu, sắp xếp theo order
    $filteredNavCats = \App\Models\Category::active()
                            ->where('show_in_menu', true)
                            ->orderBy('order', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();
                            
    $visibleHorizontalCount = 10;
    $horizontalCats = $filteredNavCats->take($visibleHorizontalCount);
    $dropdownCats = $filteredNavCats->skip($visibleHorizontalCount);
  @endphp
  <nav class="cat-nav sticky top-0 z-30 bg-[#2a7a27]" id="cat-nav">
    <div class="cat-nav-inner flex items-stretch min-h-[42px]">

      {{-- Category links wrapper (hidden on mobile) --}}
      <div class="cat-nav-links hidden md:flex flex-1 overflow-hidden items-stretch min-w-0">
        @foreach($horizontalCats as $cat)
        <a href="{{ route('category', $cat->slug) }}"
           class="cat-nav-item flex-1 flex items-center justify-center text-center px-2.5 text-[.79rem] font-semibold text-white/92 no-underline border-r border-white/14 transition-colors duration-200 whitespace-nowrap
                  hover:bg-white/15 hover:text-white
                  {{ request()->is('chuyen-muc/'.$cat->slug.'*') ? 'bg-white !text-[#2a7a27]' : '' }}">
          {{ $cat->name }}
        </a>
        @endforeach
      </div>

      {{-- ≡ Hamburger: remaining categories dropdown (only show if any) --}}
      @if($dropdownCats->isNotEmpty())
      <button id="catMenuToggle" onclick="toggleCatMenu()" title="Xem thêm chuyên mục"
              aria-expanded="false" aria-controls="catMenuDropdown"
              class="flex-shrink-0 self-stretch flex flex-col items-center justify-center gap-1 px-3.5 w-11 bg-transparent border-none text-white cursor-pointer transition-colors duration-200 hover:bg-white/15">
        <span class="block w-4 h-0.5 bg-white rounded"></span>
        <span class="block w-4 h-0.5 bg-white rounded"></span>
        <span class="block w-4 h-0.5 bg-white rounded"></span>
      </button>
      @endif

      {{-- 🔍 Search --}}
      <button type="button" onclick="toggleAdvSearch()" title="Tìm kiếm"
              class="flex-shrink-0 self-stretch flex items-center justify-center px-3.5 w-11 bg-transparent border-none text-white/90 cursor-pointer transition-colors duration-200 hover:bg-white/15">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
        </svg>
      </button>

    </div>
  </nav>

  {{-- ═══ CATEGORY DROPDOWN (khi bấm ≡) ═══ --}}
  @if($dropdownCats->isNotEmpty())
  <div id="catMenuDropdown"
       class="hidden relative z-[29] bg-white border-b-4 border-[#2a7a27] shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1">
        @foreach($dropdownCats as $cat)
        <a href="{{ route('category', $cat->slug) }}"
           onclick="closeCatMenu()"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-gray-800 no-underline transition-colors duration-200
                  hover:bg-[#e8f5e2] hover:text-[#2a7a27]
                  {{ request()->is('chuyen-muc/'.$cat->slug.'*') ? 'bg-[#e8f5e2] text-[#2a7a27]' : '' }}">
          <span class="w-1.5 h-1.5 rounded-full bg-[#2a7a27] flex-shrink-0"></span>
          {{ $cat->name }}
        </a>
        @endforeach
      </div>
    </div>
  </div>
  <div id="catMenuBackdrop" onclick="closeCatMenu()" class="hidden fixed inset-0 z-[28]"></div>
  @endif

  {{-- ═══ ADVANCED SEARCH PANEL ═══ --}}
  <div id="advSearchPanel"
       class="hidden relative z-[27] bg-white border-b-4 border-[#2a7a27] shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <form action="{{ route('search') }}" method="GET">
        <div class="flex flex-wrap gap-3 items-end">

          <div class="flex-[2] min-w-[180px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Từ khóa</label>
            <input type="text" name="keyword" placeholder="Nội dung, tiêu đề..." value="{{ request('keyword') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a7a27] focus:ring-1 focus:ring-[#2a7a27] transition">
          </div>

          <div class="flex-1 min-w-[140px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Tác giả</label>
            <input type="text" name="author" placeholder="Tên tác giả..." value="{{ request('author') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a7a27] focus:ring-1 focus:ring-[#2a7a27] transition">
          </div>

          <div class="flex-[1.2] min-w-[140px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Chuyên mục</label>
            <select name="category_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a7a27] bg-white transition">
              <option value="">-- Tất cả --</option>
              @foreach($allNavCategories ?? [] as $cat)
              <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="flex-1 min-w-[130px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Từ ngày</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a7a27] transition">
          </div>

          <div class="flex-1 min-w-[130px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Đến ngày</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#2a7a27] transition">
          </div>

          <div class="flex items-center gap-2">
            <button type="submit"
                    class="flex items-center gap-1.5 px-5 py-2 bg-[#2a7a27] hover:bg-[#1b5e20] text-white text-sm font-bold rounded-lg transition-colors">
              <i class="bi bi-search"></i> Tìm
            </button>
            <a href="{{ route('search') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Xóa lọc</a>
          </div>

        </div>
      </form>
    </div>
  </div>

  {{-- ═══ PAGE CONTENT ═══ --}}
  <main class="flex-1 bg-white pt-4">
    @yield('content')
  </main>

  {{-- ═══ FOOTER ═══ --}}
  <footer id="footer" class="mt-auto">

    {{-- Main footer body --}}
    <div class="bg-gradient-to-br from-[#0d3b10] via-[#1b5e20] to-[#0d3b10] border-t-4 border-[#f5d400] pt-10 pb-7 w-full">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">

          {{-- Col 1: Logo + About --}}
          <div>
            <div class="flex items-center gap-3.5 mb-4">
              <img src="{{ asset('images/logo.png') }}" alt="AGU Logo"
                   class="w-12 h-12 rounded-full object-cover border-2 border-[#f5d400]/50"
                   onerror="this.style.display='none'">
              <div>
                <div class="text-lg font-black text-[#f5d400] italic tracking-wide leading-none">e-News</div>
                <div class="text-[.68rem] text-white/70 font-semibold tracking-wider mt-0.5">TRANG BÁO SINH VIÊN ĐẠI HỌC AN GIANG</div>
              </div>
            </div>

            <p class="text-[.78rem] text-white/65 leading-relaxed mb-5">
              e-News là nơi phản ánh hoạt động học tập, phong trào sinh viên và giảng viên Trường Đại học An Giang — ra mắt từ tháng 10 năm 2004.
            </p>

            <div class="flex flex-col gap-2">
              <div class="flex items-start gap-2 text-[.75rem] text-white/75">
                <i class="bi bi-geo-alt-fill text-[#f5d400] mt-0.5 flex-shrink-0"></i>
                <span>Số 18, đường Ung Văn Khiêm, phường Long Xuyên, tỉnh An Giang</span>
              </div>
              <div class="flex items-center gap-2 text-[.75rem] text-white/75">
                <i class="bi bi-telephone-fill text-[#f5d400] flex-shrink-0"></i>
                <span>+84 296 625 6565 nhánh 1602</span>
              </div>
              <div class="flex items-center gap-2 text-[.75rem] text-white/75">
                <i class="bi bi-envelope-fill text-[#f5d400] flex-shrink-0"></i>
                <a href="mailto:enews@agu.edu.vn" class="text-white/75 hover:text-[#f5d400] transition-colors no-underline">enews@agu.edu.vn</a>
              </div>
              <div class="flex items-center gap-2 text-[.75rem] text-white/75">
                <i class="bi bi-globe2 text-[#f5d400] flex-shrink-0"></i>
                <a href="http://enews.agu.edu.vn" target="_blank" class="text-white/75 hover:text-[#f5d400] transition-colors no-underline">enews.agu.edu.vn</a>
              </div>
            </div>
          </div>

          {{-- Col 2: Chuyên mục --}}
          <div>
            <h3 class="text-[.75rem] font-extrabold text-[#f5d400] uppercase tracking-widest mb-3.5 pb-2 border-b border-[#f5d400]/20 flex items-center gap-2">
              <i class="bi bi-grid-3x3-gap-fill"></i> Chuyên mục
            </h3>
            @php $footerCats = \App\Models\Category::active()->roots()->limit(8)->get(); @endphp
            <ul class="space-y-0.5">
              @foreach($footerCats as $cat)
              <li>
                <a href="{{ route('category', $cat->slug) }}"
                   class="flex items-center gap-2 py-1.5 text-[.75rem] text-white/68 hover:text-[#f5d400] border-b border-white/6 transition-colors no-underline">
                  <i class="bi bi-chevron-right text-[.55rem] text-[#f5d400]"></i>
                  {{ $cat->name }}
                </a>
              </li>
              @endforeach
            </ul>
          </div>

          {{-- Col 3: Liên kết + Phụ trách --}}
          <div>
            <h3 class="text-[.75rem] font-extrabold text-[#f5d400] uppercase tracking-widest mb-3.5 pb-2 border-b border-[#f5d400]/20 flex items-center gap-2">
              <i class="bi bi-link-45deg"></i> Thông tin
            </h3>

            <ul class="space-y-0.5 mb-5">
              @foreach([
                ['url'=>route('about'),   'label'=>'Giới thiệu'],
                ['url'=>route('rules'),   'label'=>'Quy định đăng bài'],
                ['url'=>route('contact'), 'label'=>'Liên hệ tòa soạn'],
                ['url'=>route('search'),  'label'=>'Tìm kiếm bài viết'],
                ['url'=>route('login'),   'label'=>'Đăng nhập hệ thống'],
              ] as $link)
              <li>
                <a href="{{ $link['url'] }}"
                   class="flex items-center gap-2 py-1.5 text-[.75rem] text-white/68 hover:text-[#f5d400] border-b border-white/6 transition-colors no-underline">
                  <i class="bi bi-chevron-right text-[.55rem] text-[#f5d400]"></i>
                  {{ $link['label'] }}
                </a>
              </li>
              @endforeach
            </ul>

            {{-- Phụ trách --}}
            <div class="bg-white/7 rounded-lg p-3.5 border border-[#f5d400]/15">
              <div class="text-[.65rem] font-extrabold text-[#f5d400] uppercase tracking-wider mb-2.5">
                <i class="bi bi-person-badge-fill mr-1"></i> Phụ trách
              </div>
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#2a7a27] to-[#388e3c] flex items-center justify-center text-white text-[.7rem] font-extrabold flex-shrink-0">NK</div>
                <div>
                  <div class="text-[.7rem] font-bold text-white">ThS. Ngô Thị Kim Duyên</div>
                  <div class="text-[.62rem] text-white/50">Phụ trách chung e-News</div>
                </div>
              </div>
            </div>

            {{-- Social --}}
            <div class="flex gap-2 mt-4">
              <a href="https://www.facebook.com/trangbaosinhvientruongdaihocangiang" target="_blank" title="Facebook"
                 class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 flex items-center justify-center text-white/75 text-sm hover:bg-[#1877F2] hover:border-[#1877F2] hover:text-white transition-all no-underline">
                <i class="bi bi-facebook"></i>
              </a>
              <a href="https://www.youtube.com/@thuvienaihocangiang7831" target="_blank" title="YouTube"
                 class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 flex items-center justify-center text-white/75 text-sm hover:bg-red-600 hover:border-red-600 hover:text-white transition-all no-underline">
                <i class="bi bi-youtube"></i>
              </a>
              <a href="mailto:enews@agu.edu.vn" title="Email"
                 class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 flex items-center justify-center text-white/75 text-sm hover:bg-[#f5d400] hover:border-[#f5d400] hover:text-[#1b5e20] transition-all no-underline">
                <i class="bi bi-envelope-fill"></i>
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- Copyright bar --}}
    <div class="bg-[#071d08] px-4 py-3">
      <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-3">
        <div class="text-[.7rem] text-white/45">
          &copy; {{ date('Y') }} <strong class="text-white/65">e-News</strong>
          &mdash; Trang báo Sinh viên Trường Đại học An Giang &mdash; VNU-HCM. Bảo lưu mọi quyền.
        </div>
        <button onclick="window.scrollTo({top:0,behavior:'smooth'})"
                class="flex items-center gap-1.5 px-3.5 py-1.5 bg-[#f5d400]/12 border border-[#f5d400]/30 rounded-lg text-[#f5d400] text-[.7rem] font-bold hover:bg-[#f5d400]/22 transition-colors cursor-pointer">
          <i class="bi bi-arrow-up-circle-fill"></i> Trở lên trên
        </button>
      </div>
    </div>

  </footer>

</div>{{-- /page-wrapper --}}

{{-- Google Translate --}}
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'vi',
    autoDisplay: false
  }, 'google_translate_element');
}
window.addEventListener('pageshow', function(e) {
  if (e.persisted) window.location.reload();
});
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

@stack('scripts')

{{-- Hamburger category menu --}}
<script>
function toggleCatMenu() {
  var dropdown = document.getElementById('catMenuDropdown');
  var backdrop = document.getElementById('catMenuBackdrop');
  var btn      = document.getElementById('catMenuToggle');
  var isOpen   = !dropdown.classList.contains('hidden');
  if (isOpen) { closeCatMenu(); }
  else {
    dropdown.classList.remove('hidden');
    backdrop.classList.remove('hidden');
    btn.setAttribute('aria-expanded', 'true');
  }
}
function closeCatMenu() {
  var dropdown = document.getElementById('catMenuDropdown');
  var backdrop = document.getElementById('catMenuBackdrop');
  var btn      = document.getElementById('catMenuToggle');
  if (dropdown) dropdown.classList.add('hidden');
  if (backdrop) backdrop.classList.add('hidden');
  if (btn) btn.setAttribute('aria-expanded', 'false');
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeCatMenu();
});

function toggleAdvSearch() {
  var panel = document.getElementById('advSearchPanel');
  if (panel) {
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) panel.querySelector('input[name="keyword"]')?.focus();
  }
}
@if(request()->anyFilled(['keyword','author','date_from','date_to','category_id']))
document.addEventListener('DOMContentLoaded', () => { toggleAdvSearch(); });
@endif
</script>

{{-- Livewire --}}
@livewireScripts

{{-- Reading Progress Bar --}}
<div id="reading-progress"></div>

<script>
// ── Global image fallback (ảnh lỗi → placeholder) ──
(function() {
  const PLACEHOLDER = 'https://placehold.co/600x350/e8f5e2/2a7a27?text=eNews+AGU';
  document.addEventListener('error', function(e) {
    const img = e.target;
    if (img.tagName === 'IMG' && img.src !== PLACEHOLDER) {
      img.src = PLACEHOLDER;
      img.style.objectFit = 'contain';
      img.style.padding = '8px';
    }
  }, true); // capture phase để bắt được trước khi alt text hiển thị
})();

// ── Scroll Reveal ──
(function() {
  const els = document.querySelectorAll('.section-widget, .news-list-item, .featured-article, .clb-card, .photo-strip-item');
  if (!els.length) return;
  els.forEach(el => el.classList.add('reveal'));
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('visible'), i * 60);
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.08 });
  els.forEach(el => io.observe(el));
})();

// ── Reading Progress Bar ──
(function() {
  const bar = document.getElementById('reading-progress');
  if (!bar) return;
  window.addEventListener('scroll', () => {
    const docH  = document.documentElement.scrollHeight - window.innerHeight;
    const pct   = docH > 0 ? (window.scrollY / docH) * 100 : 0;
    bar.style.width = Math.min(pct, 100) + '%';
  }, { passive: true });
})();
</script>

</body>
</html>
