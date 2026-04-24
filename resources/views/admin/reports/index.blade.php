@extends('layouts.admin')
@section('title', 'Báo cáo & Thống kê')

@section('content')
<div class="p-6 max-w-7xl mx-auto" id="report-container">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100 tracking-tight flex items-center gap-3 transition-colors">
                <span class="w-10 h-10 flex items-center justify-center bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl shadow-sm">
                    <i class="bi bi-bar-chart-line-fill text-lg"></i>
                </span>
                Báo cáo Hệ thống
            </h2>
            <p class="text-[14px] text-gray-500 dark:text-zinc-400 mt-1.5 ml-13 transition-colors">Thống kê chi tiết bài viết, lượt xem và tương tác người dùng</p>
        </div>

        <!-- Filter & Actions -->
        <div class="flex flex-col md:flex-row flex-wrap items-end md:items-center gap-3 header-actions z-20 relative mt-4 md:mt-0 xl:flex-nowrap">
            
            <!-- Quick Periods -->
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-zinc-800/80 rounded-xl p-1 border border-gray-200 dark:border-zinc-800">
                <a href="{{ route('admin.reports.index', ['period' => 'week']) }}" class="px-3 py-1.5 text-[12px] font-bold rounded-lg {{ request('period') == 'week' ? 'bg-white dark:bg-zinc-700 text-emerald-600 dark:text-emerald-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-200/50 dark:hover:bg-zinc-700/50' }} transition-all">Tuần này</a>
                <a href="{{ route('admin.reports.index', ['period' => 'month']) }}" class="px-3 py-1.5 text-[12px] font-bold rounded-lg {{ request('period') == 'month' ? 'bg-white dark:bg-zinc-700 text-emerald-600 dark:text-emerald-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-200/50 dark:hover:bg-zinc-700/50' }} transition-all">Tháng này</a>
                <a href="{{ route('admin.reports.index', ['period' => 'year']) }}" class="px-3 py-1.5 text-[12px] font-bold rounded-lg {{ request('period') == 'year' ? 'bg-white dark:bg-zinc-700 text-emerald-600 dark:text-emerald-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-200/50 dark:hover:bg-zinc-700/50' }} transition-all">Năm nay</a>
            </div>

            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl shadow-sm transition-colors overflow-hidden p-1">
                    <input type="date" name="start_date" value="{{ $startDate }}" title="Từ ngày" class="bg-transparent border-none text-[13px] text-gray-700 dark:text-zinc-300 focus:ring-0 w-[115px] sm:w-[130px] py-1.5 px-3 font-medium [&::-webkit-calendar-picker-indicator]:dark:invert">
                    
                    <div class="w-px h-5 bg-gray-200 dark:bg-zinc-800 mx-1"></div>
                    
                    <span class="pl-2 pr-1 text-gray-400 dark:text-zinc-500 text-[11px] uppercase font-bold tracking-wider">Đến</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" title="Đến ngày" class="bg-transparent border-none text-[13px] text-gray-700 dark:text-zinc-300 focus:ring-0 w-[115px] sm:w-[130px] py-1.5 px-3 font-medium [&::-webkit-calendar-picker-indicator]:dark:invert">
                    
                    <button type="submit" class="px-4 py-1.5 bg-gray-900 dark:bg-zinc-100 hover:bg-gray-800 dark:hover:bg-white text-white dark:text-zinc-900 text-[13px] font-bold rounded-lg transition-colors shadow-sm focus:outline-none flex items-center gap-1.5 ml-1">
                        <i class="bi bi-funnel-fill text-[11px]"></i> Lọc
                    </button>
                </div>
            </form>

            @if(request('start_date') || request('end_date') || request('period'))
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2.5 text-[13px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="bi bi-x-circle-fill"></i>
            </a>
            @endif

            <!-- Export Dropdown -->
            <div x-data="{ open: false }" class="relative shrink-0 ml-auto md:ml-1 md:border-l md:border-gray-200 dark:md:border-zinc-800 md:pl-4">
                <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center gap-2 px-5 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-[13px] font-bold rounded-xl shadow-sm transition-all focus:outline-none">
                    <i class="bi bi-download"></i> Tải báo cáo <i class="bi bi-chevron-down text-[10px] ml-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-zinc-900 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] dark:shadow-none border border-gray-100 dark:border-zinc-800 z-50 overflow-hidden text-gray-700 dark:text-zinc-300" style="display: none;">
                    <ul class="py-2 text-[13px] font-medium">
                        <li>
                            <button onclick="window.print()" type="button" class="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-zinc-800 flex items-center gap-3 transition-colors outline-none cursor-pointer border-none">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 flex items-center justify-center shrink-0"><i class="bi bi-printer-fill"></i></span> In văn bản
                            </button>
                        </li>
                        <div class="h-px w-full bg-gray-100 dark:bg-zinc-800 my-1 transition-colors"></div>
                        <li>
                            <a href="{{ route('admin.reports.export-csv', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 hover:bg-gray-50 dark:hover:bg-zinc-800 flex items-center gap-3 transition-colors no-underline">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0"><i class="bi bi-file-earmark-excel-fill"></i></span> Xuất thẻ Excel (.xlsx)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(request('start_date') && request('end_date'))
        <div class="mb-6 animate-fade-in-up">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-full text-[13px] font-semibold shadow-sm transition-colors">
                <i class="bi bi-funnel-fill text-emerald-500"></i> Đang lọc: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} → {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
            </span>
        </div>
    @endif

    <!-- 4 Main Primary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1 -->
        <div class="rounded-2xl bg-emerald-500 dark:bg-emerald-600 p-5 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group min-w-0 flex flex-col">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-file-earmark-text text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="text-emerald-50 text-[13px] font-bold uppercase tracking-wider mb-2 flex items-center gap-2"><i class="bi bi-card-text"></i> TỔNG BÀI VIẾT</div>
                <div class="text-3xl xl:text-[34px] font-black mb-1 truncate">{{ number_format($totalPosts) }}</div>
                <div class="text-[12px] font-medium mb-3 flex items-center gap-1.5 opacity-90">
                    <span class="px-1.5 py-0.5 rounded {{ $growths['posts'] >= 0 ? 'bg-white/20' : 'bg-red-500/40 text-red-50' }} font-bold text-[11px]">
                        @if($growths['posts'] >= 0) <i class="bi bi-arrow-up-short"></i> @else <i class="bi bi-arrow-down-short"></i> @endif {{ abs($growths['posts']) }}%
                    </span>
                    <span class="truncate">{{ $growths['label'] }}</span>
                </div>
                <div class="flex flex-wrap gap-2 text-[11px] font-semibold mt-auto">
                    <span class="px-2 py-1 bg-white/20 rounded truncate"><i class="bi bi-check-circle-fill mr-1"></i>{{ number_format($publishedPosts) }} Đã đăng</span>
                    <span class="px-2 py-1 bg-black/20 rounded truncate"><i class="bi bi-hourglass-split mr-1"></i>{{ number_format($pendingPosts) }} Chờ duyệt</span>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="rounded-2xl bg-blue-500 dark:bg-blue-600 p-5 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group min-w-0 flex flex-col">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-people-fill text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="text-blue-50 text-[13px] font-bold uppercase tracking-wider mb-2 flex items-center gap-2"><i class="bi bi-person-fill"></i> TỔNG NGƯỜI DÙNG</div>
                <div class="text-3xl xl:text-[34px] font-black mb-1 truncate">{{ number_format($usersCount) }}</div>
                <div class="text-[12px] font-medium mb-3 flex items-center gap-1.5 opacity-90">
                    <span class="px-1.5 py-0.5 rounded {{ $growths['users'] >= 0 ? 'bg-white/20' : 'bg-red-500/40 text-red-50' }} font-bold text-[11px]">
                        @if($growths['users'] >= 0) <i class="bi bi-arrow-up-short"></i> @else <i class="bi bi-arrow-down-short"></i> @endif {{ abs($growths['users']) }}%
                    </span>
                    <span class="truncate">{{ $growths['label'] }}</span>
                </div>
                <div class="text-[12px] text-blue-100 font-medium truncate mt-auto">
                    {{ $usersByRole['admin'] ?? 0 }} admin • {{ $usersByRole['editor'] ?? 0 }} biên tập • {{ $usersByRole['contributor'] ?? 0 }} CTV
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="rounded-2xl bg-orange-500 dark:bg-orange-600 p-5 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group min-w-0 flex flex-col">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-eye-fill text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="text-orange-50 text-[13px] font-bold uppercase tracking-wider mb-2 flex items-center gap-2"><i class="bi bi-eye"></i> TỔNG LƯỢT XEM</div>
                <div class="text-3xl xl:text-[34px] font-black mb-1 truncate" title="{{ number_format($totalViews) }}">{{ number_format($totalViews) }}</div>
                <div class="text-[12px] font-medium mb-3 flex items-center gap-1.5 opacity-90">
                    <span class="px-1.5 py-0.5 rounded {{ $growths['views'] >= 0 ? 'bg-white/20' : 'bg-red-500/40 text-red-50' }} font-bold text-[11px]">
                        @if($growths['views'] >= 0) <i class="bi bi-arrow-up-short"></i> @else <i class="bi bi-arrow-down-short"></i> @endif {{ abs($growths['views']) }}%
                    </span>
                    <span class="truncate">{{ $growths['label'] }}</span>
                </div>
                <div class="text-[12px] text-orange-100 font-medium truncate mt-auto">Lượt xem tích luỹ toàn hệ thống</div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="rounded-2xl bg-purple-500 dark:bg-purple-600 p-5 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group min-w-0 flex flex-col">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-chat-heart-fill text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="text-purple-50 text-[13px] font-bold uppercase tracking-wider mb-2 flex items-center gap-2"><i class="bi bi-chat-dots-fill"></i> LƯỢNG BÌNH LUẬN</div>
                <div class="text-3xl xl:text-[34px] font-black mb-1 truncate" title="{{ number_format($commentsCount) }}">{{ number_format($commentsCount) }}</div>
                <div class="text-[12px] font-medium mb-3 flex items-center gap-1.5 opacity-90">
                    <span class="px-1.5 py-0.5 rounded {{ $growths['comments'] >= 0 ? 'bg-white/20' : 'bg-red-500/40 text-red-50' }} font-bold text-[11px]">
                        @if($growths['comments'] >= 0) <i class="bi bi-arrow-up-short"></i> @else <i class="bi bi-arrow-down-short"></i> @endif {{ abs($growths['comments']) }}%
                    </span>
                    <span class="truncate">{{ $growths['label'] }}</span>
                </div>
                <div class="text-[12px] text-purple-100 font-medium truncate mt-auto">{{ number_format($categoriesCount) }} chuyên mục đang hoạt động</div>
            </div>
        </div>
    </div>

    <!-- Insights & Actionables Row -->
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900 dark:text-zinc-100 tracking-tight"><i class="bi bi-lightbulb-fill text-amber-500 mr-2"></i> Phân tích & Hành động</h3>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-10">
        <!-- Hot Posts -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 p-6 flex flex-col h-full">
            <h5 class="text-[13px] uppercase tracking-wider font-bold text-gray-900 dark:text-zinc-100 mb-5 flex items-center gap-2"><i class="bi bi-fire text-orange-500 text-lg"></i> BÀI VIẾT QUAN TÂM NHẤT</h5>
            
            <div class="space-y-3 flex-1 flex flex-col justify-between">
                @forelse($topPosts as $index => $post)
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="flex items-start gap-3 group p-2 -mx-2 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 text-[13px] font-bold flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                            #{{ $index + 1 }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[13px] font-bold text-gray-800 dark:text-zinc-200 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $post->title }}">{{ $post->title }}</div>
                            <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-zinc-400 mt-1 font-medium pr-1">
                                <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400"><i class="bi bi-eye-fill"></i> {{ number_format($post->view_count) }} lượt xem</span>
                                <span class="truncate pl-2 text-right">{{ $post->category?->name ?? 'Chưa phân loại' }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-6 text-[13px] text-gray-400 font-medium">Chưa có bài viết nào được quan tâm.</div>
                @endforelse
            </div>
            @if(count($topPosts) > 0)
            <div class="pt-3 mt-2 border-t border-gray-100 dark:border-zinc-800">
                <a href="{{ route('admin.posts.index') }}" class="text-[12px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 transition-colors flex items-center justify-center gap-1">Tra cứu thư viện <i class="bi bi-arrow-right-short text-lg"></i></a>
            </div>
            @endif
        </div>

        <!-- Hot Categories -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 p-6 flex flex-col h-full">
            <h5 class="text-[13px] uppercase tracking-wider font-bold text-gray-900 dark:text-zinc-100 mb-5 flex items-center gap-2"><i class="bi bi-tags-fill text-emerald-500 text-lg"></i> CHUYÊN MỤC SẢN XUẤT TOP</h5>
            
            <div class="space-y-5 flex-1 mt-1">
                @forelse($topCategories as $index => $cat)
                    <div>
                        <div class="flex items-center justify-between mb-1.5 min-w-0">
                            <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-[13px] font-bold text-gray-800 dark:text-zinc-200 truncate hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ $cat->name }}">
                                {{ $cat->name }}
                            </a>
                            <span class="text-[11px] font-bold text-gray-700 dark:text-zinc-300 shrink-0 ml-2">{{ number_format($cat->posts_count) }} bài</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-zinc-800 h-2.5 rounded-full overflow-hidden shadow-inner">
                            @php
                                $maxPosts = $topCategories[0]->posts_count > 0 ? $topCategories[0]->posts_count : 1;
                                $width = ($cat->posts_count / $maxPosts) * 100;
                            @endphp
                            <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2.5 rounded-full transition-all duration-1000" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-[13px] text-gray-400 font-medium">Chưa có dữ liệu chuyên mục.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        <!-- Line Chart -->
        <div class="xl:col-span-2 bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 p-6 transition-colors">
            <h5 class="text-[14px] font-bold text-gray-900 dark:text-zinc-100 mb-6 transition-colors uppercase tracking-wider"><i class="bi bi-bar-chart-steps text-blue-500 text-lg mr-1"></i> Biểu đồ Đăng tin theo tháng ({{ date('Y') }})</h5>
            <div class="w-full">
                <canvas id="postsChart" height="120"></canvas>
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 p-6 transition-colors flex flex-col">
            <h5 class="text-[14px] font-bold text-gray-900 dark:text-zinc-100 mb-6 transition-colors uppercase tracking-wider"><i class="bi bi-pie-chart-fill text-purple-500 text-lg mr-1"></i> Cơ cấu Người dùng</h5>
            <div class="w-full flex-1 flex items-center justify-center">
                <canvas id="usersChart" height="240"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Checking dark mode configuration
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#e4e4e7' : '#374151'; // zinc-200 : gray-700
        const gridColor = isDarkMode ? '#27272a' : '#f3f4f6'; // zinc-800 : gray-100

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            color: textColor,
            plugins: {
                legend: {
                    labels: {
                        color: textColor,
                        font: { family: "'Inter', sans-serif" }
                    }
                },
                tooltip: {
                    titleFont: { family: "'Inter', sans-serif" },
                    bodyFont: { family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    backgroundColor: isDarkMode ? 'rgba(39, 39, 42, 0.9)' : 'rgba(17, 24, 39, 0.9)', // zinc-800 : gray-900
                    titleColor: '#fff',
                    bodyColor: '#fff',
                }
            }
        };

        // Line Chart: Bài viết theo tháng
        const ctxPosts = document.getElementById('postsChart').getContext('2d');
        
        let gradientFill = ctxPosts.createLinearGradient(0, 0, 0, 300);
        gradientFill.addColorStop(0, isDarkMode ? 'rgba(59, 130, 246, 0.4)' : 'rgba(59, 130, 246, 0.3)'); // blue-500 fading out
        gradientFill.addColorStop(1, isDarkMode ? 'rgba(59, 130, 246, 0.0)' : 'rgba(59, 130, 246, 0.0)');

        new Chart(ctxPosts, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Số bài viết mới',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#3b82f6', // blue-500
                    backgroundColor: gradientFill,
                    borderWidth: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: isDarkMode ? '#18181b' : '#fff', // zinc-900 : white
                    pointHoverBackgroundColor: isDarkMode ? '#18181b' : '#fff',
                    pointHoverBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'top', labels: { color: textColor } }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        },
                        ticks: { precision: 0, color: textColor }
                    }
                }
            }
        });

        // Doughnut Chart: Người dùng
        const ctxUsers = document.getElementById('usersChart').getContext('2d');
        const usersByRole = {!! json_encode($usersByRole) !!};
        const roleNames = {
            'admin':       'Quản trị viên',
            'editor':      'Biên tập viên',
            'contributor': 'Cộng tác viên',
            'viewer':      'Người xem',
            'reader':      'Độc giả',
        };
        const labels = Object.keys(usersByRole).map(role => roleNames[role] || role);
        const data = Object.values(usersByRole);
        const bgColors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#64748b'];

        new Chart(ctxUsers, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: isDarkMode ? '#18181b' : '#ffffff', // zinc-900 : white
                    hoverOffset: 6
                }]
            },
            options: {
                ...commonOptions,
                cutout: '75%',
                plugins: {
                    ...commonOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: textColor
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
