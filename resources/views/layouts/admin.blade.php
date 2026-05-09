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
        [x-cloak] { display: none !important; }

        /* TypeUI Custom Scrollbar */
        .adm-sidebar::-webkit-scrollbar { width: 4px; }
        .adm-sidebar::-webkit-scrollbar-track { background: transparent; }
        .adm-sidebar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }
        .adm-sidebar::-webkit-scrollbar-thumb:hover { background: #52525b; }

        @yield('styles')
    </style>

    {{-- Dark Mode Init Script & Livewire Navigator Fix --}}
    <script>
        function applyAdmTheme() {
            try {
                const theme = localStorage.getItem('adm_theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (_) {}
        }
        applyAdmTheme();
        document.addEventListener('livewire:navigated', applyAdmTheme);
    </script>
    
    @livewireStyles
</head>

<body x-data="adminLayout()" x-init="init()" class="antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen transition-colors duration-200">

    {{-- ── Mobile Overlay ── --}}
    <div x-show="mobileOpen" x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden"
         style="display:none;"></div>

    {{-- ───────────── SIDEBAR (SaaS STYLE) ───────────── --}}
    <aside class="fixed top-0 bottom-0 left-0 bg-white dark:bg-zinc-950 z-40 flex flex-col transition-all duration-300 ease-in-out border-r border-zinc-200 dark:border-white/10"
           :style="sidebarStyle()">

        {{-- Logo --}}
        <div @click="toggle()" class="cursor-pointer flex items-center gap-3 h-[64px] border-b border-zinc-200 dark:border-white/10 shrink-0 relative w-full overflow-hidden transition-all duration-300 hover:bg-zinc-50 dark:hover:bg-white/5" :class="collapsed ? 'justify-center px-0' : 'justify-start px-5'">
            <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-white/10 flex items-center justify-center text-zinc-900 dark:text-zinc-100 shrink-0 transition-all duration-300">
                <i class="bi bi-newspaper"></i>
            </div>
            <span class="text-[16px] font-bold text-zinc-900 dark:text-white tracking-tight truncate transition-opacity duration-300" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'">E-News Admin</span>
        </div>

        {{-- Nav links --}}
        <nav class="adm-sidebar flex-1 overflow-y-auto py-4 space-y-1 w-full flex flex-col px-3">
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
                    $navItems[] = ['route'=>route('admin.reports.contributors.index'),'is'=>'admin.reports.contributors.*','icon'=>'bi-person-badge','label'=>'Báo cáo CTV'];
                if($role==='admin')
                    $navItems[] = ['route'=>route('admin.reports.royalty.index'),'is'=>'admin.reports.royalty.*','icon'=>'bi-wallet2','label'=>'Nhuận bút'];

                // Section Title (Xét Duyệt)
                if(in_array($role,['admin','editor']))
                    $navItems[] = ['type' => 'header', 'label' => 'XÉT DUYỆT'];

                if(in_array($role,['admin','editor']))
                    $navItems[] = ['route'=>route('admin.notifications.index'),'is'=>'admin.notifications.*','icon'=>'bi-bell','label'=>'Thông báo'];
                
                if($role==='admin') {
                    $pendingC = \App\Models\Comment::where('is_approved', false)->count();
                    $navItems[] = ['route'=>route('admin.comments.index'),'is'=>'admin.comments.*','icon'=>'bi-chat-dots','label'=>'Bình luận','badge'=>$pendingC,'badgeColor'=>'bg-red-500 text-white dark:bg-red-500/20 dark:text-red-400'];
                }
                if($role==='admin') {
                    $pendingCTV = \App\Models\ContributorRequest::where('status','pending')->count();
                    $navItems[] = ['route'=>route('admin.contributor.index'),'is'=>'admin.contributor.*','icon'=>'bi-person-check','label'=>'Cộng tác viên','badge'=>$pendingCTV,'badgeColor'=>'bg-emerald-500 text-white dark:bg-emerald-500/20 dark:text-emerald-400'];
                }
            @endphp

            @foreach($navItems as $item)
                @if(isset($item['type']) && $item['type'] === 'header')
                    <div class="px-3 pt-6 pb-2 text-[11px] font-bold tracking-wider uppercase truncate text-zinc-500 dark:text-zinc-500" x-show="!collapsed">
                        {{ $item['label'] }}
                    </div>
                    <div class="px-4 pt-6 pb-2 flex justify-center" x-show="collapsed" x-cloak>
                        <div class="h-px w-6 bg-zinc-200 dark:bg-white/10 rounded-full"></div>
                    </div>
                @else
                    @php $isActive = request()->routeIs($item['is']); @endphp
                    <a href="{{ $item['route'] }}" wire:navigate.hover
                       class="flex items-center gap-3 py-2.5 rounded-xl border border-transparent text-[14px] font-medium transition-all duration-200 w-full outline-none group relative overflow-hidden {{ $isActive ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold shadow-sm ring-1 ring-emerald-500/20' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5 hover:text-zinc-900 dark:hover:text-white' }}"
                       :class="collapsed ? 'justify-center px-0' : 'px-3 justify-start'"
                       title="{{ $item['label'] }}">
                        
                        {{-- Indicator dọc khi active (SaaS style) --}}
                        @if($isActive)
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-r-md shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        @endif

                        <i class="bi {{ $item['icon'] }} text-[18px] shrink-0 w-6 text-center transition-colors duration-200 {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'group-hover:text-emerald-600 dark:group-hover:text-emerald-400' }}"></i>
                        <span class="whitespace-nowrap truncate tracking-tight transition-opacity" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'">
                            {{ $item['label'] }}
                        </span>

                        @if(!empty($item['badge']) && $item['badge'] > 0)
                        {{-- Expanded badge --}}
                        <span class="ml-auto text-[10px] font-bold min-w-[20px] h-[20px] flex items-center justify-center px-1.5 rounded-full {{ $item['badgeColor'] }} transition-opacity shadow-sm" x-show="!collapsed">
                            {{ $item['badge'] }}
                        </span>
                        {{-- Collapsed dot --}}
                        <span class="absolute top-2.5 right-2 w-2 h-2 rounded-full shadow-sm {{ $item['badgeColor'] }}" x-show="collapsed" x-cloak></span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- User info bottom --}}
        <div class="p-3 border-t border-zinc-200 dark:border-white/10 shrink-0 relative w-full transition-colors duration-200" x-data="{ dropupOpen: false }" @click.outside="dropupOpen = false">
            {{-- Dropup Menu --}}
            <div x-show="dropupOpen" x-transition x-cloak
                 class="absolute bottom-[calc(100%+8px)] left-3 bg-white dark:bg-zinc-900 rounded-xl py-1.5 z-50 overflow-hidden shadow-lg border border-zinc-200 dark:border-white/10" 
                 :class="collapsed ? 'min-w-[180px]' : 'w-[calc(100%-24px)]'"
                 style="display:none;">
                <a href="{{ route('home') }}" wire:navigate.hover class="flex items-center gap-3 px-4 py-2 text-[13px] font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 no-underline transition-colors whitespace-nowrap">
                    <i class="bi bi-box-arrow-up-right text-zinc-400 dark:text-zinc-500"></i>
                    <span>Tới trang chủ</span>
                </a>
                <a href="{{ route('profile') }}" wire:navigate.hover class="flex items-center gap-3 px-4 py-2 text-[13px] font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 no-underline transition-colors whitespace-nowrap">
                    <i class="bi bi-person-gear text-zinc-400 dark:text-zinc-500"></i> 
                    <span>Tài khoản</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-[13px] font-medium hover:bg-red-50 dark:hover:bg-red-500/10 text-red-600 dark:text-red-400 outline-none transition-colors whitespace-nowrap">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>

            {{-- Trigger Button --}}
            <button @click="dropupOpen = !dropupOpen"
                    class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-white/5 transition cursor-pointer border-none outline-none focus:outline-none text-left" :class="collapsed ? 'justify-center' : ''">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'A') }}&background=059669&color=fff&rounded=true" 
                     alt="Avatar" class="w-8 h-8 rounded-full shadow-sm ring-1 ring-zinc-200 dark:ring-white/10 shrink-0">
                <div class="flex-1 min-w-0 transition-opacity" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'">
                    <p class="text-[13px] font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium truncate uppercase tracking-wider">{{ auth()->user()->role ?? 'Admin' }}</p>
                </div>
                <i class="bi bi-chevron-expand text-zinc-400 dark:text-zinc-500 shrink-0 transition-opacity" :class="collapsed ? 'opacity-0 hidden' : 'opacity-100'"></i>
            </button>
        </div>
    </aside>

    {{-- ───────────── MAIN CONTENT ───────────── --}}
    <div class="flex flex-col min-h-screen transition-all duration-300 ease-in-out"
         :style="mainStyle()">

        {{-- Topbar (SaaS STYLE) --}}
        <header class="h-[64px] bg-white dark:bg-zinc-950 border-b border-zinc-200 dark:border-white/10 sticky top-0 z-30 px-6 flex items-center justify-between shadow-sm transition-colors duration-200">
            
            <div class="flex items-center gap-4">
                {{-- Breadcrumbs / Welcome --}}
                <div class="hidden md:flex flex-col">
                    <span class="text-[14px] font-medium tracking-tight text-zinc-900 dark:text-zinc-100">Workspace</span>
                    <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium uppercase tracking-wider">Tin Tức AGU Admin</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                
                {{-- Nút truy cập Web --}}
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 text-[13px] font-medium text-zinc-600 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white rounded-md transition-colors shadow-sm">
                    <i class="bi bi-box-arrow-up-right"></i> Web
                </a>

                <div class="h-5 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

                {{-- Theme Switcher --}}
                <div class="relative" x-data="{themeOpen: false}" @click.outside="themeOpen = false">
                    <button @click="themeOpen = !themeOpen" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition cursor-pointer outline-none">
                        <i class="bi text-lg" :class="currentTheme === 'dark' ? 'bi-moon-stars' : (currentTheme === 'system' ? 'bi-display' : 'bi-sun')"></i>
                    </button>
                    <div x-show="themeOpen" x-cloak x-transition.origin.top.right
                         class="absolute right-0 top-full mt-1 w-36 bg-white dark:bg-zinc-900 rounded-lg shadow-lg py-1 border border-zinc-200 dark:border-white/10 z-50">
                        <button @click="setTheme('light'); themeOpen = false" class="w-full text-left px-4 py-2 text-[13px] font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors" :class="currentTheme==='light' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-600 dark:text-zinc-300'">
                            <i class="bi bi-sun"></i> Sáng
                        </button>
                        <button @click="setTheme('dark'); themeOpen = false" class="w-full text-left px-4 py-2 text-[13px] font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors" :class="currentTheme==='dark' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-600 dark:text-zinc-300'">
                            <i class="bi bi-moon-stars"></i> Tối
                        </button>
                        <button @click="setTheme('system'); themeOpen = false" class="w-full text-left px-4 py-2 text-[13px] font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors" :class="currentTheme==='system' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-600 dark:text-zinc-300'">
                            <i class="bi bi-display"></i> Hệ thống
                        </button>
                    </div>
                </div>

                {{-- User Dropdown Topbar --}}
                <div class="relative ml-2" x-data="{open:false}" @click.outside="open=false">
                    <button @click="open=!open" class="flex items-center gap-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 p-1 rounded-md transition outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=059669&color=fff&rounded=true" alt="Avatar" class="w-7 h-7 rounded-full shadow-sm ring-1 ring-zinc-200 dark:ring-white/10">
                        <i class="bi bi-chevron-down text-[10px] text-zinc-400 dark:text-zinc-500 ml-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition.origin.top.right
                         class="absolute right-0 top-full mt-1 w-48 bg-white dark:bg-zinc-900 rounded-lg shadow-lg py-1 border border-zinc-200 dark:border-white/10 z-50">
                        <div class="px-4 py-2 border-b border-zinc-100 dark:border-white/10 mb-1">
                            <p class="text-[13px] font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-[13px] text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 font-medium no-underline transition-colors">
                           <i class="bi bi-person text-zinc-400 dark:text-zinc-500"></i> Hồ sơ
                        </a>
                        <div class="my-1 border-t border-zinc-100 dark:border-white/10"></div>
                        <button @click.prevent="$dispatch('open-logout'); open = false" class="w-full flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 outline-none text-left transition-colors">
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
        const SIDEBAR_W = 260; 
        const SIDEBAR_COLLAPSED_W = 72;

        return {
            collapsed: localStorage.getItem('adm_saas_collapsed') === '1',
            mobileOpen: false,
            isMobile: window.innerWidth < 1024,
            currentTheme: localStorage.getItem('adm_theme') || 'system',

            init() {
                window.addEventListener('resize', () => {
                    this.isMobile = window.innerWidth < 1024;
                    if (!this.isMobile) this.mobileOpen = false;
                });
                this.applyTheme(this.currentTheme);
            },

            setTheme(val) {
                this.currentTheme = val;
                if (val === 'system') {
                    localStorage.removeItem('adm_theme');
                } else {
                    localStorage.setItem('adm_theme', val);
                }
                this.applyTheme(val);
                // Dispatch event so charts can re-render if needed
                window.dispatchEvent(new CustomEvent('theme-changed', {
                    detail: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }));
            },

            applyTheme(val) {
                const root = document.documentElement;
                if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
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

    {{-- Back to top button --}}
    <div x-data="{ showScrollTop: false }"
         @scroll.window="showScrollTop = (window.pageYOffset > 300) ? true : false"
         class="fixed bottom-8 right-8 z-[90]">
        <button x-show="showScrollTop" x-cloak
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                @click="window.scrollTo({top: 0, behavior: 'smooth'})"
                class="flex items-center gap-2.5 px-4 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] transition-all duration-300 font-bold text-[13px] group border border-zinc-700 dark:border-white/20 hover:-translate-y-1"
                title="Trở lên trên">
            <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0 text-white shadow-sm transition-transform duration-300 group-hover:scale-110">
                <i class="bi bi-arrow-up text-[14px] group-hover:-translate-y-0.5 transition-transform duration-300"></i>
            </div>
            Trở lên trên
        </button>
    </div>

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
        <script>
            window.confirmFormSubmit = function(event, message) {
                event.preventDefault();
                const form = event.target;
                Swal.fire({
                    title: 'Xác nhận',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy bỏ',
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            };

            window.confirmCustomAction = function(message, callback) {
                Swal.fire({
                    title: 'Xác nhận',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy bỏ',
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a'
                }).then((result) => {
                    if (result.isConfirmed) {
                        callback();
                    }
                });
            };
        </script>
    </div>
    
    @livewireScripts
</body>
</html>
