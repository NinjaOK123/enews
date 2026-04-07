@extends('layouts.admin')
@section('title', 'Báo cáo & Thống kê')

@section('content')
<div class="p-6 max-w-7xl mx-auto" id="report-container">

    <!-- Header + Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row justify-between md:items-center gap-6 header-actions">
        <!-- Title -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
                <span class="w-10 h-10 flex items-center justify-center bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl shadow-sm">
                    <i class="bi bi-bar-chart-line-fill text-lg"></i>
                </span>
                Báo cáo Hệ thống
            </h2>
            <p class="text-sm text-gray-500 mt-1.5 ml-13">Thống kê chi tiết bài viết, lượt xem và tương tác người dùng</p>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Form -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl">
                <i class="bi bi-calendar3 text-gray-400"></i>
                <input type="date" name="start_date" value="{{ request('start_date') }}" title="Từ ngày" class="bg-transparent border-none text-sm text-gray-700 focus:ring-0 w-[120px] p-0 font-medium">
                <span class="text-gray-400 text-sm">→</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" title="Đến ngày" class="bg-transparent border-none text-sm text-gray-700 focus:ring-0 w-[120px] p-0 font-medium">
                <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-colors ml-1">Lọc</button>
            </form>

            @if(request('start_date') || request('end_date'))
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                <i class="bi bi-x-circle-fill"></i> Xóa lọc
            </a>
            @endif

            <!-- Export Dropdown (Alpine) -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-bold rounded-xl shadow-sm transition-all">
                    <i class="bi bi-download"></i> Tải báo cáo <i class="bi bi-chevron-down text-xs ml-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden" style="display: none;">
                    <ul class="py-2 text-sm text-gray-700">
                        <li>
                            <button onclick="window.print()" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center"><i class="bi bi-printer-fill"></i></span> In văn bản
                            </button>
                        </li>
                        <li>
                            <button onclick="exportPDF()" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center"><i class="bi bi-file-earmark-pdf-fill"></i></span> PDF
                            </button>
                        </li>
                        <div class="h-px w-full bg-gray-100 my-1"></div>
                        <li>
                            <a href="{{ route('admin.reports.export-csv', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 hover:bg-gray-50 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="bi bi-file-earmark-excel-fill"></i></span> Excel (.xlsx)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(request('start_date') && request('end_date'))
        <div class="mb-6 animate-fade-in-up">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-sm font-semibold shadow-sm">
                <i class="bi bi-funnel-fill text-emerald-500"></i> Đang lọc: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} → {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
            </span>
        </div>
    @endif

    <!-- 4 Main Primary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-file-earmark-text text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-2">Tổng Bài Viết</div>
                <div class="text-4xl font-black mb-3">{{ number_format($totalPosts) }}</div>
                <div class="flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="px-2 py-1 bg-white/20 rounded backdrop-blur-sm"><i class="bi bi-check-circle-fill mr-1"></i>{{ number_format($publishedPosts) }} Đã đăng</span>
                    <span class="px-2 py-1 bg-black/20 rounded backdrop-blur-sm"><i class="bi bi-hourglass-split mr-1"></i>{{ number_format($pendingPosts) }} Chờ duyệt</span>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 p-6 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-people-fill text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-blue-100 text-xs font-bold uppercase tracking-wider mb-2">Tổng Người Dùng</div>
                <div class="text-4xl font-black mb-3">{{ number_format($usersCount) }}</div>
                <div class="text-xs text-blue-100 font-medium">
                    {{ $usersByRole['admin'] ?? 0 }} admin • {{ $usersByRole['editor'] ?? 0 }} biên tập • {{ $usersByRole['contributor'] ?? 0 }} CTV
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-eye-fill text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-amber-100 text-xs font-bold uppercase tracking-wider mb-2">Tổng Lượt Xem</div>
                <div class="text-4xl font-black mb-3">{{ number_format($totalViews) }}</div>
                <div class="text-xs text-amber-100 font-medium">Lượt xem tích luỹ toàn hệ thống</div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="rounded-2xl bg-gradient-to-br from-purple-500 to-fuchsia-600 p-6 text-white relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-chat-heart-fill text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-purple-100 text-xs font-bold uppercase tracking-wider mb-2">Lượng Bình Luận</div>
                <div class="text-4xl font-black mb-3">{{ number_format($commentsCount) }}</div>
                <div class="text-xs text-purple-100 font-medium">{{ number_format($categoriesCount) }} chuyên mục đang hoạt động</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Line Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h5 class="text-lg font-bold text-gray-900 mb-6">Biểu đồ Đăng tin theo tháng ({{ date('Y') }})</h5>
            <div class="w-full">
                <canvas id="postsChart" height="120"></canvas>
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h5 class="text-lg font-bold text-gray-900 mb-6">Cơ cấu Người dùng</h5>
            <div class="w-full flex items-center justify-center">
                <canvas id="usersChart" height="240"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    function exportPDF() {
        const element = document.getElementById('report-container');
        const headerActions = document.querySelector('.header-actions');
        if (headerActions) headerActions.style.display = 'none'; // Hide UI buttons during print
        
        var opt = {
            margin:       10,
            filename:     'bao_cao_he_thong_' + new Date().toISOString().slice(0,10) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            if (headerActions) headerActions.style.display = ''; // Restore UI buttons
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: { family: "'Inter', sans-serif" }
                    }
                },
                tooltip: {
                    titleFont: { family: "'Inter', sans-serif" },
                    bodyFont: { family: "'Inter', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                }
            }
        };

        // Line Chart: Bài viết theo tháng
        const ctxPosts = document.getElementById('postsChart').getContext('2d');
        new Chart(ctxPosts, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Số bài viết mới',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#10b981', // emerald-500
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#10b981',
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
                    legend: { position: 'top' }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: { precision: 0 }
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
                    borderWidth: 0,
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
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
