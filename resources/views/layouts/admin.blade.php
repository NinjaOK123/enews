<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ config('app.name', 'AGU E-News') }}</title>

    {{-- Icons + Fonts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS (dùng chung cho các layout con cũ) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Tailwind + Alpine via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

    <style>
        html, body { 
            min-height: 100vh; 
            height: auto !important; 
            overflow-x: hidden !important; 
            overflow-y: auto !important; 
            font-family: 'Poppins', sans-serif; 
        }
        [x-cloak] { display: none !important; }
        
        /* Glassmorphism Navigation Links */
        .adm-link-active { 
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff !important; 
            font-weight: 600; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.2); 
            transform: translateY(-1px);
            border-left: 4px solid #34d399; /* Xanh lục ngọc sáng */
        }
        .adm-link-active i { color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        
        .adm-link-base { 
            transition: all 0.3s ease; 
            border-left: 4px solid transparent;
        }
        .adm-link-base:hover:not(.adm-link-active) {
            transform: translateX(4px);
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
            border-left: 4px solid rgba(255,255,255,0.4);
        }
        .adm-link-base:hover:not(.adm-link-active) i { color: #ffffff !important; }

        /* Custom Scrollbar cho Sidebar Kính */
        .adm-sidebar::-webkit-scrollbar { width: 4px; }
        .adm-sidebar::-webkit-scrollbar-track { background: transparent; }
        .adm-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .adm-sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }

        /* Kính mờ UI Helpers */
        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }
        .glass-header {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Ẩn / Chỉnh lại Bootstrap Card trong Admin (nếu form cũ còn) để hợp tông */
        @media (min-width: 1024px) {
            .card { background: rgba(255,255,255,0.95) !important; border-radius: 1rem !important; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;}
        }

        @yield('styles')
    </style>
</head>

<body class="text-white" x-data="adminLayout()" x-init="init()" 
      style="background: linear-gradient(135deg, #064e3b 0%, #047857 40%, #10b981 100%); background-attachment: fixed;">

    {{-- ── Mobile Overlay ── --}}
    <div x-show="mobileOpen" x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"
         style="display:none;"></div>

    {{-- ───────────── SIDEBAR (GLASSMORPHISM) ───────────── --}}
    <aside class="fixed top-4 bottom-4 left-4 glass-panel rounded-[24px] z-40 flex flex-col transition-all duration-400 ease-out overflow-hidden"
           :style="sidebarStyle()">

        {{-- Logo --}}
        <div class="flex items-center justify-center gap-3 h-[80px] border-b border-white/10 shrink-0 bg-transparent relative z-10 w-full">
            <div class="w-10 h-10 rounded-[12px] bg-white/20 border border-white/30 backdrop-blur-md shadow-inner flex items-center justify-center text-white shrink-0 text-lg">
                <i class="bi bi-newspaper drop-shadow-md"></i>
            </div>
            <span class="text-[18px] font-extrabold text-white tracking-wide" style="font-family:'Manrope',sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">E-News Admin</span>
        </div>

        {{-- Nav links --}}
        <nav class="adm-sidebar flex-1 overflow-y-auto px-4 py-6 space-y-2 bg-transparent text-white w-full">
            @php
                $role = auth()->user()->role ?? 'reader';
                $dashRoute = match($role) {
                    'admin'       => route('admin.dashboard'),
                    'editor'      => route('editor.dashboard'),
                    'contributor' => route('contributor.dashboard'),
                    default       => route('home'),
                };

                $navItems = [];
                if(in_array($role,['admin','editor','contributor']))
                    $navItems[] = ['route'=>$dashRoute,'is'=>'*.dashboard','icon'=>'bi-house-fill','label'=>'Home Dashboard'];
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.posts.index'),'is'=>'admin.posts.*','icon'=>'bi-file-earmark-richtext-fill','label'=>'Quản lý Bài viết'];

                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.categories.index'),'is'=>'admin.categories.*','icon'=>'bi-tags-fill','label'=>'Danh mục tin học'];
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.banners.index'),'is'=>'admin.banners.*','icon'=>'bi-images','label'=>'Banners - Cuộc thi'];
                if(in_array($role,['admin','editor','contributor']))
                    $navItems[] = ['route'=>route('contributor.posts.create'),'is'=>'contributor.posts.*','icon'=>'bi-pencil-square','label'=>'Soạn bài mới'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.users.index'),'is'=>'admin.users.*','icon'=>'bi-people-fill','label'=>'Tài khoản'];
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.media.index'),'is'=>'admin.media.*','icon'=>'bi-image-fill','label'=>'Thư viện Media'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.reports.index'),'is'=>'admin.reports.index','icon'=>'bi-bar-chart-line-fill','label'=>'Báo cáo Tổng quát'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.reports.royalty.index'),'is'=>'admin.reports.royalty.*','icon'=>'bi-wallet-fill','label'=>'Báo cáo Nhuận bút'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.notifications.index'),'is'=>'admin.notifications.*','icon'=>'bi-bell-fill','label'=>'Hộp Thông báo'];
                if($role==='admin') {
                    $pendingC = \App\Models\Comment::where('is_approved', false)->count();
                    $navItems[] = ['route'=>route('admin.comments.index'),'is'=>'admin.comments.*','icon'=>'bi-chat-dots-fill','label'=>'Quản lý Bình luận','badge'=>$pendingC,'badgeColor'=>'bg-red-500 text-white shadow-md text-[11px]'];
                }
                if($role==='admin') {
                    $pendingCTV = \App\Models\ContributorRequest::where('status','pending')->count();
                    $navItems[] = ['route'=>route('admin.contributor.index'),'is'=>'admin.contributor.*','icon'=>'bi-person-check-fill','label'=>'Duyệt Cộng tác viên','badge'=>$pendingCTV,'badgeColor'=>'bg-amber-400 text-amber-900 shadow-md text-[11px]'];
                }
            @endphp

            @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['is']); @endphp
            <a href="{{ $item['route'] }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-medium transition-all duration-300 {{ $isActive ? 'adm-link-active' : 'adm-link-base text-white/80' }}"
               title="{{ $item['label'] }}">
                <i class="bi {{ $item['icon'] }} text-[20px] shrink-0 w-6 text-center {{ $isActive ? '' : 'text-white/60 drop-shadow-sm' }}"></i>
                <span class="whitespace-nowrap truncate font-medium tracking-wide">{{ $item['label'] }}</span>
                @if(!empty($item['badge']) && $item['badge'] > 0)
                <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full {{ $item['badgeColor'] }}">{{ $item['badge'] }}</span>
                @endif
            </a>
            @endforeach
        </nav>

        {{-- User info bottom --}}
        <div class="p-4 border-t border-white/10 shrink-0 relative bg-white/5 z-10 w-full" x-data="{ dropupOpen: false }" @click.outside="dropupOpen = false">
            {{-- Dropup Menu Glass --}}
            <div x-show="dropupOpen" x-transition.opacity.duration.200ms x-cloak
                 class="absolute bottom-[calc(100%+8px)] left-4 right-4 mb-2 bg-white rounded-[16px] py-2 z-50 overflow-hidden shadow-xl border border-gray-100" 
                 style="display:none;">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 text-[14px] font-medium hover:bg-gray-50 transition-colors no-underline" style="color: #333;">
                    <i class="bi bi-house text-[18px]" style="color: #666;"></i>
                    <span>Trang chủ</span>
                </a>
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 text-[14px] font-medium hover:bg-gray-50 transition-colors no-underline" style="color: #333;">
                    <i class="bi bi-person-fill text-[18px]" style="color: #666;"></i> 
                    <span>Cài đặt tài khoản</span>
                </a>
                <div class="my-1" style="border-top: 1px solid #eaeaea;"></div>
                <form method="POST" action="{{ route('logout') }}" class="block w-full m-0 p-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-[14px] font-bold hover:bg-gray-50 transition-colors text-left" style="color: #e11d48;">
                        <i class="bi bi-power text-[18px]"></i> 
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>

            {{-- 	Trigger Button --}}
            <button @click="dropupOpen = !dropupOpen" 
                    class="w-full flex items-center gap-3 p-2.5 rounded-2xl hover:bg-white/20 transition cursor-pointer border-none outline-none focus:outline-none text-left group">
                <div class="w-10 h-10 rounded-full shadow-md overflow-hidden flex shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8BD9&color=fff&rounded=true" alt="User" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[14px] font-semibold text-white truncate" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">{{ auth()->user()->name }}</p>
                    <p class="text-[12px] text-white/70 font-medium truncate tracking-wide">{{ auth()->user()->role ?? 'Manager' }}</p>
                </div>
                <i class="bi bi-chevron-expand text-white/50 group-hover:text-white transition"></i>
            </button>
        </div>
    </aside>

    {{-- ───────────── MAIN CONTENT ───────────── --}}
    <div class="flex flex-col min-h-screen transition-all duration-400 ease-out"
         :style="mainStyle()">

        {{-- Topbar Glass --}}
        <div class="px-[20px] md:px-[30px] pt-4 w-full">
            <header class="glass-header rounded-[24px] h-[70px] flex items-center justify-between px-5 md:px-6 sticky top-4 z-50 shadow-sm border border-white/10 relative">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    {{-- Hamburger --}}
                    <button @click="toggle()"
                            class="w-10 h-10 flex flex-col items-center justify-center gap-[5px] rounded-xl hover:bg-white/20 transition cursor-pointer shrink-0 group">
                        <span class="block w-[22px] h-[2px] bg-white rounded-full group-hover:scale-105 transition-transform"></span>
                        <span class="block w-[16px] h-[2px] bg-white rounded-full group-hover:scale-105 transition-transform"></span>
                        <span class="block w-[22px] h-[2px] bg-white rounded-full group-hover:scale-105 transition-transform"></span>
                    </button>
                    <div class="hidden sm:block text-[15px] font-medium text-white/90 truncate ml-2">
                        Chào mừng bạn trở lại! 👋
                    </div>

                    {{-- Nút Xem Website được chuyển sang bên trái, sử dụng inline style thay vì tailwind class mới do lỗi cache build CSS --}}
                    <a href="{{ route('home') }}"
                       class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 text-[14px] font-medium text-white/90 bg-white/5 hover:bg-white/15 rounded-[12px] transition whitespace-nowrap" style="margin-left: 1.5rem;">
                        <i class="bi bi-globe2"></i> Xem Website
                    </a>
                </div>

                <div class="flex items-center gap-6 md:gap-10 shrink-0">
                    {{-- User Dropdown Topbar --}}
                    <div class="relative shrink-0" x-data="{open:false}" @click.outside="open=false">
                        <button @click="open=!open"
                                class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-white/20 transition cursor-pointer border-none outline-none focus:outline-none">
                            <div class="w-8 h-8 rounded-full shadow-md shrink-0 overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8BD9&color=fff&rounded=true" alt="Profile" class="w-full h-full object-cover">
                            </div>
                            <span class="hidden md:block text-[14px] font-semibold text-white max-w-[120px] truncate" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">{{ auth()->user()->name }}</span>
                            <i class="bi bi-caret-down-fill text-[10px] text-white/70" :class="open ? 'rotate-180' : ''" style="transition:transform .2s;"></i>
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 top-[calc(100%+10px)] w-56 bg-white rounded-[16px] shadow-xl py-2 z-[99] border border-gray-100"
                             style="display:none;">
                            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 text-[14px] font-medium hover:bg-gray-50 transition-colors no-underline" style="color: #333;">
                                <i class="bi bi-house text-[18px]" style="color: #666;"></i>
                                <span>Trang chủ</span>
                            </a>
                            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 text-[14px] font-medium hover:bg-gray-50 transition-colors no-underline" style="color: #333;">
                                <i class="bi bi-person-fill text-[18px]" style="color: #666;"></i> 
                                <span>Cài đặt tài khoản</span>
                            </a>
                            <div class="my-1" style="border-top: 1px solid #eaeaea;"></div>
                            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 block w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-[14px] font-bold hover:bg-gray-50 transition-colors text-left" style="color: #e11d48;">
                                    <i class="bi bi-power text-[18px]"></i> 
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
        </div>

        {{-- Page Content wrapper --}}
        <main class="flex-1 min-w-0 p-[20px] md:p-[30px] pt-8">
            @yield('content')
        </main>
    </div>

    <script>
    function adminLayout() {
        const SIDEBAR_W = 280; // Độ rộng Glass Sidebar = 280px
        const SIDEBAR_MARGIN = 16; // left-4 = 16px

        return {
            collapsed: localStorage.getItem('adm_glass_collapsed') === '1',
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
                    localStorage.setItem('adm_glass_collapsed', this.collapsed ? '1' : '0');
                }
            },

            // Sidebar: Floating & translate ra ngoài lề
            sidebarStyle() {
                const hidden = this.isMobile ? !this.mobileOpen : this.collapsed;
                return `width:${SIDEBAR_W}px; transform:translateX(${hidden ? '-120%' : '0'})`;
            },

            // Main: thụt lề bù cho Sidebar
            mainStyle() {
                if (this.isMobile) return 'margin-left:0';
                const totalOffset = SIDEBAR_W + SIDEBAR_MARGIN * 2; // Khoảng cách đẩy main content vào
                return `margin-left:${this.collapsed ? '0' : totalOffset + 'px'}`;
            }
        };
    }
    </script>
    
    @yield('scripts')
    @stack('scripts')

    {{-- Global SweetAlert2 Form Helpers (Đã tối ưu màu cho Glass UI) --}}
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
                title: 'Xác nhận!',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#1e293b', // Chỉnh sang màu tối thay vì đỏ để hiện đại
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy bỏ',
                background: 'rgba(255,255,255,0.95)',
                customClass: {
                    title: 'text-xl font-bold font-sans text-emerald-700',
                    popup: 'rounded-2xl shadow-2xl backdrop-blur-md',
                    confirmButton: 'px-6 py-2 mx-2 rounded-xl font-semibold shadow-lg text-white border-0',
                    cancelButton: 'px-6 py-2 mx-2 rounded-xl font-semibold shadow-lg text-white border-0'
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
                title: 'Cẩn trọng!',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', 
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Xác nhận xóa',
                cancelButtonText: 'Hủy bỏ',
                background: 'rgba(255,255,255,0.95)',
                customClass: {
                    title: 'text-xl font-bold font-sans text-red-600',
                    popup: 'rounded-2xl shadow-2xl backdrop-blur-md',
                    confirmButton: 'px-6 py-2 mx-2 rounded-xl font-semibold shadow-lg text-white border-0',
                    cancelButton: 'px-6 py-2 mx-2 rounded-xl font-semibold shadow-lg text-white border-0'
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
