<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ config('app.name', 'AGU E-News') }}</title>

    {{-- Icons + Fonts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* slate-50 */
            color: #111827; /* gray-900 */
        }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar cho Sidebar */
        .adm-sidebar::-webkit-scrollbar { width: 4px; }
        .adm-sidebar::-webkit-scrollbar-track { background: transparent; }
        .adm-sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .adm-sidebar::-webkit-scrollbar-thumb:hover { background: #475569; }

        @yield('styles')
    </style>
</head>

<body x-data="adminLayout()" x-init="init()" class="antialiased">

    {{-- ── Mobile Overlay ── --}}
    <div x-show="mobileOpen" x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden"
         style="display:none;"></div>

    {{-- ───────────── SIDEBAR (SaaS STYLE) ───────────── --}}
    <aside class="fixed top-0 bottom-0 left-0 bg-slate-950 z-40 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800"
           :style="sidebarStyle()">

        {{-- Logo --}}
        <div class="flex items-center gap-3 h-[64px] border-b border-slate-800 shrink-0 relative w-full overflow-hidden transition-all duration-300" :class="collapsed ? 'justify-center px-0' : 'justify-start px-5'">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 shrink-0 transition-all duration-300">
                <i class="bi bi-newspaper"></i>
            </div>
            <span class="text-[16px] font-bold text-white tracking-tight truncate transition-opacity duration-300" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'">E-News Admin</span>
        </div>

        {{-- Nav links --}}
        <nav class="adm-sidebar flex-1 overflow-y-auto py-4 space-y-1 w-full flex flex-col px-3" style="color: #cbd5e1;">
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
                    $navItems[] = ['route'=>$dashRoute,'is'=>'*.dashboard','icon'=>'bi-house-door','label'=>'Overview'];
                
                // Section Title (Khối Quản Lý)
                $navItems[] = ['type' => 'header', 'label' => 'QUẢN LÝ NỘI DUNG'];

                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.posts.index'),'is'=>'admin.posts.*','icon'=>'bi-file-text','label'=>'Bài viết'];

                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.categories.index'),'is'=>'admin.categories.*','icon'=>'bi-tags','label'=>'Chuyên mục'];
                
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.banners.index'),'is'=>'admin.banners.*','icon'=>'bi-image','label'=>'Banners'];
                
                if(in_array($role,['admin','editor','contributor']))
                    $navItems[] = ['route'=>route('contributor.posts.create'),'is'=>'contributor.posts.*','icon'=>'bi-pencil-square','label'=>'Soạn bài mới'];

                // Section Title (Hệ Thống)
                if(in_array($role,['admin']))
                    $navItems[] = ['type' => 'header', 'label' => 'HỆ THỐNG'];

                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.users.index'),'is'=>'admin.users.*','icon'=>'bi-people','label'=>'Người dùng'];
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.media.index'),'is'=>'admin.media.*','icon'=>'bi-folder','label'=>'Media'];
                
                // Section Title (Thống kê)
                if(in_array($role,['admin']))
                    $navItems[] = ['type' => 'header', 'label' => 'THỐNG KÊ & BÁO CÁO'];

                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.reports.index'),'is'=>'admin.reports.index','icon'=>'bi-bar-chart','label'=>'Báo cáo chung'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.reports.royalty.index'),'is'=>'admin.reports.royalty.*','icon'=>'bi-wallet2','label'=>'Nhuận bút'];

                // Section Title (Xét Duyệt)
                if(in_array($role,['admin']))
                    $navItems[] = ['type' => 'header', 'label' => 'XÉT DUYỆT'];

                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.notifications.index'),'is'=>'admin.notifications.*','icon'=>'bi-bell','label'=>'Thông báo'];
                
                if($role==='admin') {
                    $pendingC = \App\Models\Comment::where('is_approved', false)->count();
                    $navItems[] = ['route'=>route('admin.comments.index'),'is'=>'admin.comments.*','icon'=>'bi-chat-dots','label'=>'Bình luận','badge'=>$pendingC,'badgeColor'=>'bg-red-500'];
                }
                if($role==='admin') {
                    $pendingCTV = \App\Models\ContributorRequest::where('status','pending')->count();
                    $navItems[] = ['route'=>route('admin.contributor.index'),'is'=>'admin.contributor.*','icon'=>'bi-person-check','label'=>'Cộng tác viên','badge'=>$pendingCTV,'badgeColor'=>'bg-emerald-500'];
                }
            @endphp

            @foreach($navItems as $item)
                @if(isset($item['type']) && $item['type'] === 'header')
                    <div class="px-3 pt-6 pb-2 text-[11px] font-bold tracking-wider uppercase truncate" style="color: #94a3b8;" x-show="!collapsed">
                        {{ $item['label'] }}
                    </div>
                @else
                    @php $isActive = request()->routeIs($item['is']); @endphp
                    <a href="{{ $item['route'] }}" wire:navigate
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14px] transition-all duration-200 w-full outline-none group relative overflow-hidden"
                       style="color: {{ $isActive ? '#ffffff' : '#cbd5e1' }}; {{ $isActive ? 'background-color: rgba(255,255,255,0.1); font-weight: 600;' : 'font-weight: 500;' }}"
                       title="{{ $item['label'] }}"
                       onmouseover="this.style.color='#ffffff'; this.style.backgroundColor='rgba(255,255,255,0.1)';"
                       onmouseout="this.style.color='{{ $isActive ? '#ffffff' : '#cbd5e1' }}'; this.style.backgroundColor='{{ $isActive ? "rgba(255,255,255,0.1)" : "transparent" }}';">
                        
                        {{-- Indicator dọc khi active (SaaS style) --}}
                        @if($isActive)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-emerald-500 rounded-r-md shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        @endif

                        <i class="bi {{ $item['icon'] }} text-[18px] shrink-0 w-6 text-center transition-colors duration-200 drop-shadow-sm"></i>
                        <span class="whitespace-nowrap truncate tracking-tight transition-opacity" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'">
                            {{ $item['label'] }}
                        </span>

                        @if(!empty($item['badge']) && $item['badge'] > 0)
                        <span class="ml-auto text-[10px] font-bold min-w-[20px] h-[20px] flex items-center justify-center px-1.5 rounded-full {{ $item['badgeColor'] }} transition-opacity shadow-sm" style="color: #ffffff;" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'">
                            {{ $item['badge'] }}
                        </span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- User info bottom --}}
        <div class="p-3 border-t border-slate-800 shrink-0 relative w-full" x-data="{ dropupOpen: false }" @click.outside="dropupOpen = false">
            {{-- Dropup Menu --}}
            <div x-show="dropupOpen" x-transition x-cloak
                 class="absolute bottom-[calc(100%+8px)] left-3 bg-white rounded-xl py-1.5 z-50 overflow-hidden shadow-lg border border-gray-200" 
                 :class="collapsed ? 'min-w-[180px]' : 'w-[calc(100%-24px)]'"
                 style="display:none;">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-[13px] font-medium hover:bg-gray-50 text-gray-700 no-underline transition-colors whitespace-nowrap">
                    <i class="bi bi-box-arrow-up-right text-gray-400"></i>
                    <span>Tới trang chủ</span>
                </a>
                <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-3 px-4 py-2 text-[13px] font-medium hover:bg-gray-50 text-gray-700 no-underline transition-colors whitespace-nowrap">
                    <i class="bi bi-person-gear text-gray-400"></i> 
                    <span>Tài khoản</span>
                </a>
                <div class="my-1 border-t border-gray-100"></div>
                <button @click.prevent="$dispatch('open-logout'); dropupOpen = false" type="button" class="w-full flex items-center gap-3 px-4 py-2 text-[13px] font-medium hover:bg-red-50 text-red-600 outline-none text-left transition-colors whitespace-nowrap">
                    <i class="bi bi-power"></i> 
                    <span>Đăng xuất</span>
                </button>
            </div>

            {{-- Trigger Button --}}
            <button @click="dropupOpen = !dropupOpen" 
                    class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-slate-800/50 transition cursor-pointer border-none outline-none focus:outline-none text-left" :class="collapsed ? 'justify-center' : ''">
                <div class="w-8 h-8 rounded-md overflow-hidden shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'A') }}&background=0f172a&color=10b981&rounded=false" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0 flex-1 transition-opacity" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'">
                    <p class="text-[13px] font-semibold text-slate-200 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-[11px] text-slate-400 font-medium truncate uppercase tracking-wider">{{ auth()->user()->role ?? 'Admin' }}</p>
                </div>
                <i class="bi bi-chevron-expand text-slate-400 shrink-0 transition-opacity" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'"></i>
            </button>
        </div>
    </aside>

    {{-- ───────────── MAIN CONTENT ───────────── --}}
    <div class="flex flex-col min-h-screen transition-all duration-300 ease-in-out"
         :style="mainStyle()">

        {{-- Topbar (SaaS STYLE) --}}
        <header class="h-[64px] bg-white border-b border-gray-200 sticky top-0 z-30 px-6 flex items-center justify-between shadow-sm">
            
            <div class="flex items-center gap-4">
                {{-- Hamburger --}}
                <button @click="toggle()"
                        class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-500 transition cursor-pointer outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Breadcrumbs / Welcome --}}
                <div class="hidden md:flex flex-col">
                    <span class="text-[14px] font-semibold text-gray-900">Workspace</span>
                    <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wider">Tin Tức AGU Admin</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                
                {{-- Nút truy cập Web --}}
                <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 text-[13px] font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors shadow-sm">
                    <i class="bi bi-box-arrow-up-right"></i> Web
                </a>

                <div class="h-5 w-px bg-gray-200 mx-1"></div>

                {{-- User Dropdown Topbar --}}
                <div class="relative" x-data="{open:false}" @click.outside="open=false">
                    <button @click="open=!open" class="flex items-center gap-2 hover:bg-gray-50 p-1 rounded-md transition outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=059669&color=fff&rounded=true" alt="Avatar" class="w-7 h-7 rounded-full shadow-sm">
                        <i class="bi bi-chevron-down text-[10px] text-gray-400 ml-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition.origin.top.right
                         class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-100 z-50">
                        <div class="px-4 py-2 border-b border-gray-100 mb-1">
                            <p class="text-[13px] font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-[13px] text-gray-700 hover:bg-gray-50 no-underline">
                           <i class="bi bi-person text-gray-400"></i> Hồ sơ
                        </a>
                        <div class="my-1 border-t border-gray-100"></div>
                        <button @click.prevent="$dispatch('open-logout'); open = false" class="w-full flex items-center gap-2 px-4 py-2 text-[13px] text-red-600 hover:bg-red-50 outline-none text-left">
                            <i class="bi bi-power"></i> Đăng xuất
                        </button>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content wrapper --}}
        <main class="flex-1 min-w-0 p-[24px]">
            @yield('content')
        </main>
    </div>

    <script>
    function adminLayout() {
        // Linear/Vercel standard sidebar width is usually 240px-260px.
        const SIDEBAR_W = 260; 
        const SIDEBAR_COLLAPSED_W = 72;

        return {
            collapsed: localStorage.getItem('adm_saas_collapsed') === '1',
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
                    localStorage.setItem('adm_saas_collapsed', this.collapsed ? '1' : '0');
                }
            },

            sidebarStyle() {
                if (this.isMobile) {
                    return `width:${SIDEBAR_W}px; transform:translateX(${this.mobileOpen ? '0' : '-100%'})`;
                }
                return `width:${this.collapsed ? SIDEBAR_COLLAPSED_W : SIDEBAR_W}px`;
            },

            mainStyle() {
                if (this.isMobile) return 'margin-left:0';
                return `margin-left:${this.collapsed ? SIDEBAR_COLLAPSED_W : SIDEBAR_W}px`;
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
                title: 'Xác nhận!',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669', // emerald-600
                cancelButtonColor: '#e5e7eb', // gray-200
                confirmButtonText: 'Đồng ý',
                cancelButtonText: '<span style="color:#374151">Hủy bỏ</span>',
                background: '#ffffff',
                customClass: {
                    title: 'text-lg font-bold font-sans text-gray-900',
                    popup: 'rounded-xl shadow-xl border border-gray-100',
                    confirmButton: 'px-6 py-2 mx-2 rounded-lg text-sm font-medium shadow-sm text-white',
                    cancelButton: 'px-6 py-2 mx-2 rounded-lg text-sm font-medium border border-gray-200 bg-white'
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
                confirmButtonColor: '#dc2626', // red-600
                cancelButtonColor: '#f1f5f9', // slate-100
                confirmButtonText: 'Xác nhận xóa',
                cancelButtonText: '<span style="color:#475569">Hủy bỏ</span>',
                background: '#ffffff',
                customClass: {
                    title: 'text-lg font-bold font-sans text-red-600',
                    popup: 'rounded-xl shadow-xl border border-gray-100',
                    confirmButton: 'px-6 py-2 mx-2 rounded-lg text-sm font-medium shadow-sm text-white',
                    cancelButton: 'px-6 py-2 mx-2 rounded-lg text-sm font-medium border border-gray-200 bg-slate-50'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
        };
    </script>

    {{-- ============================================== --}}
    {{-- GLOBAL LOGOUT MODAL TẠI CẤP LAYOUT ADMIN TỔNG --}}
    {{-- ============================================== --}}
    <div x-data="{ showLogoutModal: false }"
         @open-logout.window="showLogoutModal = true"
         x-show="showLogoutModal"
         x-cloak
         class="fixed inset-0 z-[999999] overflow-y-auto"
         style="display: none;">
         
        {{-- Overlay che nền --}}
        <div x-show="showLogoutModal"
             x-transition.opacity.duration.300ms
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px]"
             @click="showLogoutModal = false">
        </div>

        {{-- Căn giữa modal --}}
        <div class="relative min-h-screen flex items-center justify-center p-4">
            
            {{-- Panel Modal Nội dung --}}
            <div x-show="showLogoutModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative bg-white rounded-[16px] w-full border border-gray-200 shadow-xl"
                style="max-width:440px;"
                @click.stop>

                {{-- Nút X --}}
                <button @click="showLogoutModal = false" type="button" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>

                {{-- Body --}}
                <div class="p-8 text-center flex flex-col items-center">
                    <div class="w-16 h-16 mb-5 rounded-2xl bg-red-50 flex items-center justify-center border border-red-100">
                        <i class="bi bi-box-arrow-right text-3xl text-red-500"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        Đăng xuất hệ thống
                    </h3>
                    
                    <p class="text-sm text-gray-500 leading-relaxed mb-6">
                        Bạn có chắc chắn muốn đăng xuất? Phiên làm việc của bạn sẽ kết thúc.
                    </p>

                    {{-- Footer Buttons --}}
                    <div class="w-full flex items-center justify-center gap-3">
                        <button @click="showLogoutModal = false" type="button" class="flex-1 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                            Hủy
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 flex-1">
                            @csrf
                            <button type="submit" class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
