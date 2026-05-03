@extends('layouts.admin')
@section('title', 'Báo cáo Cộng tác viên')

@section('content')
<div class="p-6 max-w-7xl mx-auto" id="report-container">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100 tracking-tight flex items-center gap-3 transition-colors">
                <span class="w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl shadow-sm">
                    <i class="bi bi-people-fill text-lg"></i>
                </span>
                Báo cáo Cộng tác viên
            </h2>
            <p class="text-[14px] text-gray-500 dark:text-zinc-400 mt-1.5 ml-13 transition-colors">Thống kê sự phát triển và hiệu suất bài viết của đội ngũ Cộng tác viên.</p>
        </div>

        <!-- Filter & Actions -->
        <div class="flex flex-col md:flex-row flex-wrap items-end md:items-center gap-3 header-actions z-20 relative mt-4 md:mt-0 xl:flex-nowrap">
            
            <!-- Quick Periods -->
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-zinc-800/80 rounded-xl p-1 border border-gray-200 dark:border-zinc-800">
                <a href="{{ route('admin.reports.contributors.index', ['period' => 'week', 'type' => request('type')]) }}" class="px-3 py-1.5 text-[12px] font-bold rounded-lg {{ request('period') == 'week' ? 'bg-white dark:bg-zinc-700 text-blue-600 dark:text-blue-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-200/50 dark:hover:bg-zinc-700/50' }} transition-all">Tuần này</a>
                <a href="{{ route('admin.reports.contributors.index', ['period' => 'month', 'type' => request('type')]) }}" class="px-3 py-1.5 text-[12px] font-bold rounded-lg {{ request('period', 'month') == 'month' ? 'bg-white dark:bg-zinc-700 text-blue-600 dark:text-blue-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-200/50 dark:hover:bg-zinc-700/50' }} transition-all">Tháng này</a>
                <a href="{{ route('admin.reports.contributors.index', ['period' => 'year', 'type' => request('type')]) }}" class="px-3 py-1.5 text-[12px] font-bold rounded-lg {{ request('period') == 'year' ? 'bg-white dark:bg-zinc-700 text-blue-600 dark:text-blue-400 shadow-sm ring-1 ring-black/5 dark:ring-white/10' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300 hover:bg-gray-200/50 dark:hover:bg-zinc-700/50' }} transition-all">Năm nay</a>
            </div>

            <form action="{{ route('admin.reports.contributors.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="period" value="{{ request('period', 'month') }}">
                <input type="hidden" name="type" value="{{ request('type') }}">
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

            @if(request('start_date') || request('end_date') || request('period') || request('type'))
            <a href="{{ route('admin.reports.contributors.index') }}" class="px-4 py-2.5 text-[13px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <i class="bi bi-x-circle-fill"></i> Xóa lọc
            </a>
            @endif

            <!-- Export -->
            <div class="relative shrink-0 ml-auto md:ml-1 md:border-l md:border-gray-200 dark:md:border-zinc-800 md:pl-4">
                <a href="{{ route('admin.reports.contributors.export', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'period' => request('period'), 'type' => request('type')]) }}" class="flex items-center gap-2 px-5 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-[13px] font-bold rounded-xl shadow-sm transition-all focus:outline-none no-underline">
                    <i class="bi bi-file-earmark-excel-fill"></i> Tải Excel
                </a>
            </div>
        </div>
    </div>

    <div class="mb-6 animate-fade-in-up flex flex-wrap gap-2 items-center">
        @if(request('start_date') && request('end_date'))
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 text-blue-700 dark:text-blue-400 rounded-full text-[13px] font-semibold shadow-sm transition-colors">
                <i class="bi bi-calendar-check-fill text-blue-500"></i> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </span>
        @endif

        @if(request('type') == 'new')
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-teal-50 dark:bg-teal-500/10 border border-teal-200 dark:border-teal-500/20 text-teal-700 dark:text-teal-400 rounded-full text-[13px] font-semibold shadow-sm transition-colors">
                <i class="bi bi-person-plus-fill text-teal-500"></i> Chỉ xem CTV mới gia nhập
                <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="ml-1 hover:text-teal-900 dark:hover:text-teal-200"><i class="bi bi-x-circle"></i></a>
            </span>
        @endif
    </div>

    <!-- 2 Primary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
        <!-- Card 1: Total Contributors -->
        <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="rounded-2xl {{ request('type') != 'new' ? 'bg-blue-500 dark:bg-blue-600 ring-4 ring-blue-500/20 shadow-lg' : 'bg-gray-400 dark:bg-zinc-700 opacity-80' }} p-5 text-white relative overflow-hidden transition-all duration-300 group min-w-0 flex flex-col no-underline hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-people-fill text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="text-blue-50 text-[13px] font-bold uppercase tracking-wider mb-2 flex items-center gap-2"><i class="bi bi-person-lines-fill"></i> TỔNG CỘNG TÁC VIÊN</div>
                <div class="text-3xl xl:text-[34px] font-black mb-1 truncate">{{ number_format($totalContributors) }}</div>
                <div class="text-[12px] font-medium mb-3 flex items-center gap-1.5 opacity-90">
                    <span>{{ request('type') != 'new' ? 'Đang xem tất cả' : 'Xem tất cả' }}</span>
                </div>
            </div>
        </a>

        <!-- Card 2: New Contributors -->
        <a href="{{ request()->fullUrlWithQuery(['type' => 'new']) }}" class="rounded-2xl {{ request('type') == 'new' ? 'bg-teal-500 dark:bg-teal-600 ring-4 ring-teal-500/20 shadow-lg' : 'bg-gray-400 dark:bg-zinc-700 opacity-80' }} p-5 text-white relative overflow-hidden transition-all duration-300 group min-w-0 flex flex-col no-underline hover:scale-[1.02]">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-person-plus-fill text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="text-teal-50 text-[13px] font-bold uppercase tracking-wider mb-2 flex items-center gap-2"><i class="bi bi-star-fill"></i> CTV MỚI THAM GIA</div>
                <div class="text-3xl xl:text-[34px] font-black mb-1 truncate">{{ number_format($newContributors) }}</div>
                <div class="text-[12px] font-medium mb-3 flex items-center gap-1.5 opacity-90">
                    <span>{{ request('type') == 'new' ? 'Đang xem CTV mới' : 'Lọc theo CTV mới' }}</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden flex flex-col transition-colors duration-200 mb-8">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-zinc-800/80 bg-gray-50/50 dark:bg-zinc-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-zinc-100 tracking-tight flex items-center gap-2">
                    <i class="bi bi-list-ul text-gray-400 dark:text-zinc-500"></i> Danh sách Cộng tác viên & Bài viết
                </h3>
                <p class="text-[13px] text-gray-500 dark:text-zinc-400 mt-1">Dữ liệu được lọc theo khoảng thời gian đã cấu hình.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-100 dark:border-zinc-800/80">
                        <th class="px-6 py-3.5 text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest w-12 text-center">STT</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Cộng tác viên</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest text-center">Ngày tham gia</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest text-center text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-500/5">Tổng bài gửi</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest text-center text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-500/5">Được duyệt</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-widest text-center text-amber-600 dark:text-amber-400 bg-amber-50/50 dark:bg-amber-500/5">Chờ/Từ chối</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($contributors as $index => $contributor)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-6 py-4 text-[13px] font-medium text-gray-500 dark:text-zinc-400 text-center">
                            {{ $contributors->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($contributor->avatar)
                                        <img src="{{ asset('storage/' . $contributor->avatar) }}" alt="{{ $contributor->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="bi bi-person-fill text-gray-400"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-[14px] font-semibold text-gray-900 dark:text-zinc-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $contributor->name }}</div>
                                    <div class="text-[12px] text-gray-500 dark:text-zinc-400 mt-0.5">{{ $contributor->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[13px] text-gray-500 dark:text-zinc-400 text-center font-medium">
                            {{ $contributor->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center bg-blue-50/20 dark:bg-blue-500/5">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 font-bold text-[13px]">
                                {{ $contributor->total_posts_in_period }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center bg-emerald-50/20 dark:bg-emerald-500/5">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-bold text-[13px]">
                                {{ $contributor->published_posts_in_period }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center bg-amber-50/20 dark:bg-amber-500/5">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 font-bold text-[13px]">
                                {{ $contributor->pending_posts_in_period }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-zinc-400">
                                <i class="bi bi-inbox text-4xl mb-3 opacity-50"></i>
                                <p class="text-[14px] font-medium">Không tìm thấy cộng tác viên nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($contributors->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800/80 bg-gray-50/30 dark:bg-zinc-900/30">
            {{ $contributors->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
