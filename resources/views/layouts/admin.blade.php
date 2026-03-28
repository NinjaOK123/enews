<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ config('app.name', 'AGU E-News') }}</title>

    {{-- Icons + Fonts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS (compat cho các trang admin cũ còn dùng Bootstrap classes) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Tailwind + Alpine via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- SweetAlert2 for beautiful popups --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

    <style>
        html, body { height: 100%; font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        /* sidebar active indicator - 3D pill */
        .adm-link-active { 
            background: linear-gradient(145deg, #10b981 0%, #059669 100%); 
            color: #ffffff !important; 
            font-weight: 600; 
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3), inset 0 2px 0 rgba(255, 255, 255, 0.2), inset 0 -2px 0 rgba(0, 0, 0, 0.1); 
            transform: translateY(-1px);
            border: 1px solid #059669;
        }
        .adm-link-active i { color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
        .adm-link-base { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .adm-link-base:hover:not(.adm-link-active) {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            background-color: #f8fafc;
            color: #059669;
            border-bottom: 2px solid #e2e8f0;
        }
        .adm-link-base:hover:not(.adm-link-active) i { color: #10b981; }
        /* custom scrollbar sidebar */
        .adm-sidebar::-webkit-scrollbar { width: 5px; }
        .adm-sidebar::-webkit-scrollbar-track { background: transparent; }
        .adm-sidebar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
        @yield('styles')
    </style>
</head>

<body class="bg-gray-50 overflow-x-hidden" x-data="adminLayout()" x-init="init()">

    {{-- ── Mobile Overlay ── --}}
    <div x-show="mobileOpen" x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"
         style="display:none;"></div>

    {{-- ───────────── SIDEBAR ───────────── --}}
    <aside class="fixed top-0 left-0 h-full bg-white shadow-[4px_0_24px_rgba(0,0,0,0.04)] z-40 flex flex-col transition-all duration-300 ease-in-out border-r border-gray-50"
           :style="sidebarStyle()">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-50 shrink-0 bg-white relative z-10 shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-500 to-green-700 shadow-[inset_0_2px_4px_rgba(255,255,255,0.3),0_2px_4px_rgba(0,0,0,0.1)] flex items-center justify-center text-white shrink-0 text-sm">
                <i class="bi bi-newspaper"></i>
            </div>
            <span class="text-base font-bold text-green-700 whitespace-nowrap" style="text-shadow: 0 1px 1px rgba(0,0,0,0.05);">E-News Admin</span>
        </div>

        {{-- Nav links --}}
        <nav class="adm-sidebar flex-1 overflow-y-auto px-3 py-4 space-y-1 bg-[#fcfdfd]">
            @php
                $role = auth()->user()->role ?? 'reader';
                $dashRoute = match($role) {
                    'admin'       => route('admin.dashboard'),
                    'editor'      => route('editor.dashboard'),
                    'contributor' => route('contributor.dashboard'),
                    default       => route('home'),
                };
            @endphp

            @php
                $navItems = [];
                if(in_array($role,['admin','editor','contributor']))
                    $navItems[] = ['route'=>$dashRoute,'is'=>'*.dashboard','icon'=>'bi-speedometer2','label'=>'Dashboard'];
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.posts.index'),'is'=>'admin.posts.*','icon'=>'bi-file-earmark-text','label'=>'Bài viết'];

                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.categories.index'),'is'=>'admin.categories.*','icon'=>'bi-tags','label'=>'Chuyên mục'];
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.banners.index'),'is'=>'admin.banners.*','icon'=>'bi-images','label'=>'Banners Cuộc thi'];
                if(in_array($role,['admin','editor','contributor']))
                    $navItems[] = ['route'=>route('contributor.posts.create'),'is'=>'contributor.posts.*','icon'=>'bi-pencil-square','label'=>'Viết bài mới'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.users.index'),'is'=>'admin.users.*','icon'=>'bi-people','label'=>'Người dùng'];
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.media.index'),'is'=>'admin.media.*','icon'=>'bi-images','label'=>'Media Library'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.reports.index'),'is'=>'admin.reports.index','icon'=>'bi-bar-chart-line','label'=>'Báo cáo Tổng quát'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.reports.royalty.index'),'is'=>'admin.reports.royalty.*','icon'=>'bi-wallet2','label'=>'Báo cáo Nhuận bút'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.notifications.index'),'is'=>'admin.notifications.*','icon'=>'bi-bell','label'=>'Thông báo'];
                if($role==='admin') {
                    $pendingC = \App\Models\Comment::where('is_approved', false)->count();
                    $navItems[] = ['route'=>route('admin.comments.index'),'is'=>'admin.comments.*','icon'=>'bi-chat-dots','label'=>'Bình luận','badge'=>$pendingC,'badgeColor'=>'bg-red-500 text-white'];
                }
                if($role==='admin') {
                    $pendingCTV = \App\Models\ContributorRequest::where('status','pending')->count();
                    $navItems[] = ['route'=>route('admin.contributor.index'),'is'=>'admin.contributor.*','icon'=>'bi-person-check','label'=>'Cộng tác viên','badge'=>$pendingCTV,'badgeColor'=>'bg-amber-400 text-amber-900'];
                }
            @endphp

            @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['is']); @endphp
            <a href="{{ $item['route'] }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'adm-link-active' : 'adm-link-base text-gray-600' }}"
               title="{{ $item['label'] }}">
                <i class="bi {{ $item['icon'] }} text-[1.1rem] shrink-0 {{ $isActive ? '' : 'text-gray-400 drop-shadow-sm' }}"></i>
                <span class="whitespace-nowrap truncate">{{ $item['label'] }}</span>
                @if(!empty($item['badge']) && $item['badge'] > 0)
                <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $item['badgeColor'] }} shadow-[inset_0_1px_rgba(255,255,255,0.2),0_1px_2px_rgba(0,0,0,0.1)]">{{ $item['badge'] }}</span>
                @endif
            </a>
            @endforeach
        </nav>

        {{-- User info bottom --}}
        <div class="px-4 py-4 border-t border-gray-50 shrink-0 relative bg-white z-10 shadow-[0_-4px_12px_rgba(0,0,0,0.02)]" x-data="{ dropupOpen: false }" @click.outside="dropupOpen = false">
            {{-- Dropup Menu --}}
            <div x-show="dropupOpen" x-transition.opacity.duration.200ms x-cloak
                 class="absolute bottom-full left-4 right-4 mb-2 bg-white rounded-2xl shadow-[0_8px_30px_rgba(0,0,0,0.12)] border border-gray-100 py-2 z-50 overflow-hidden" 
                 style="display:none;">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="bi bi-person text-lg text-gray-400"></i> Hồ sơ cá nhân
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block w-full m-0 p-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition text-left">
                        <i class="bi bi-box-arrow-right text-lg"></i> Đăng xuất
                    </button>
                </form>
            </div>

            {{-- 	Trigger Button --}}
            <button @click="dropupOpen = !dropupOpen" 
                    class="w-full flex items-center gap-3 p-2 -mx-2 rounded-xl hover:bg-gray-50 hover:shadow-[reset_0_2px_8px_rgba(0,0,0,0.04)] transition-all duration-200 text-left group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-700 text-white flex items-center justify-center font-bold text-sm shadow-[inset_0_2px_4px_rgba(255,255,255,0.3),0_2px_5px_rgba(0,0,0,0.15)] shrink-0 group-hover:scale-105 transition-transform">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-800 truncate" style="text-shadow: 0 1px 1px rgba(0,0,0,0.03);">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-gray-500 font-medium truncate">{{ auth()->user()->email }}</p>
                </div>
                <i class="bi bi-chevron-expand text-gray-400 group-hover:text-gray-600 transition"></i>
            </button>
        </div>
    </aside>

    {{-- ───────────── MAIN ───────────── --}}
    <div class="flex flex-col min-h-screen transition-all duration-300"
         :style="mainStyle()">

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-5 sticky top-0 z-20 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Hamburger --}}
                <button @click="toggle()"
                        class="w-9 h-9 flex flex-col items-center justify-center gap-[5px] rounded-xl hover:bg-gray-100 transition cursor-pointer shrink-0">
                    <span class="block w-[22px] h-[2px] bg-gray-600 rounded-full"></span>
                    <span class="block w-[16px] h-[2px] bg-gray-600 rounded-full"></span>
                    <span class="block w-[22px] h-[2px] bg-gray-600 rounded-full"></span>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-green-700 border border-green-200 bg-green-50 hover:bg-green-100 rounded-xl transition whitespace-nowrap">
                    <i class="bi bi-house-door"></i> Trang chủ
                </a>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{open:false}" @click.outside="open=false">
                    <button @click="open=!open"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 transition cursor-pointer">
                        <div class="w-7 h-7 rounded-full bg-green-700 text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-semibold text-gray-700 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down text-xs text-gray-400" :class="open ? 'rotate-180' : ''" style="transition:transform .2s;"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                         style="display:none;">
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">
                            <i class="bi bi-house text-gray-400"></i> Trang chủ
                        </a>
                        <a href="{{ route('profile') }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">
                            <i class="bi bi-person-circle text-gray-400"></i> Thông tin cá nhân
                        </a>
                        <a href="{{ $dashRoute }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50">
                            <i class="bi bi-speedometer2 text-gray-400"></i> Dashboard
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 font-semibold hover:bg-red-50">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 bg-gray-100 min-h-[calc(100vh-64px)]" style="padding: 32px 24px !important; margin-top: 1px;">
            @yield('content')
        </main>
    </div>

    <script>
    function adminLayout() {
        const SIDEBAR_W = 256; // w-64 = 256px

        return {
            collapsed: localStorage.getItem('adm_collapsed') === '1',
            mobileOpen: false,
            isMobile: window.innerWidth < 1024,

            init() {
                window.addEventListener('resize', () => {
                    this.isMobile = window.innerWidth < 1024;
                    if (!this.isMobile) this.mobileOpen = false;
                });
            },

            toggle() {
                if (this.isMobile) {
                    this.mobileOpen = !this.mobileOpen;
                } else {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('adm_collapsed', this.collapsed ? '1' : '0');
                }
            },

            // Sidebar: luôn full-width (256px), ẩn/hiện bằng translateX
            sidebarStyle() {
                const hidden = this.isMobile ? !this.mobileOpen : this.collapsed;
                return `width:${SIDEBAR_W}px; transform:translateX(${hidden ? '-100%' : '0'})`;
            },

            // Main: push sang phải khi sidebar hiện (chỉ desktop)
            mainStyle() {
                if (this.isMobile) return 'margin-left:0';
                return `margin-left:${this.collapsed ? '0' : SIDEBAR_W + 'px'}`;
            }
        };
    }
    </script>


    @yield('scripts')
    @stack('scripts')

    {{-- Global SweetAlert2 Form Helpers --}}
    <script>
        window.confirmSubmit = function(event, btnElement, actionValue, message) {
            event.preventDefault();
            const form = btnElement.closest('form');
            if (!form) return;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Xác nhận',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy bỏ',
                customClass: {
                    title: 'text-xl font-bold text-gray-800 font-sans',
                    popup: 'rounded-2xl shadow-2xl border border-gray-100',
                    confirmButton: 'px-5 mx-2 rounded-xl font-semibold shadow text-white',
                    cancelButton: 'px-5 mx-2 rounded-xl font-semibold shadow text-white'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (actionValue) {
                        const oldInput = form.querySelector('input[name="action"]');
                        if (oldInput) oldInput.remove();
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'action';
                        input.value = actionValue;
                        form.appendChild(input);
                    }
                    form.submit();
                }
            });
        };

        window.confirmAction = function(event, formElement, message) {
            event.preventDefault();
            Swal.fire({
                title: 'Xác nhận',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy bỏ',
                customClass: {
                    title: 'text-xl font-bold font-sans',
                    popup: 'rounded-2xl shadow-2xl',
                    confirmButton: 'px-5 mx-2 rounded-xl font-semibold shadow text-white',
                    cancelButton: 'px-5 mx-2 rounded-xl font-semibold shadow text-white'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
        };
    </script>
</body>
</html>
