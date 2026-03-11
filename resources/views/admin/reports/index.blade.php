@extends('layouts.admin')
@section('title', 'Báo cáo & Thống kê')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-success me-2"></i>Báo cáo Hệ thống</h2>
            <p class="text-muted mb-0">Thống kê chi tiết bài viết, lượt xem và người dùng</p>
        </div>
        <div class="col-md-7 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2 header-actions">
            <!-- Bộ lọc thời gian: Từ ngày - Đến ngày -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="d-flex gap-2 align-items-center bg-light p-1 rounded-3 border shadow-sm" style="max-width: 400px;">
                <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-transparent shadow-none w-auto" value="{{ request('start_date') }}" required title="Từ ngày">
                <span class="text-muted small px-1"><i class="bi bi-arrow-right"></i></span>
                <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-transparent shadow-none w-auto" value="{{ request('end_date') }}" required title="Đến ngày">
                <button type="submit" class="btn btn-sm btn-primary rounded px-3"><i class="bi bi-funnel"></i> Lọc</button>
            </form>
            @if(request('start_date') || request('end_date'))
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center shadow-sm" title="Xóa lọc">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif

            <!-- Dropdown chức năng xuất file / in -->
            <div class="dropdown">
                <button class="btn btn-success fw-semibold shadow-sm hover-lift dropdown-toggle d-flex align-items-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.375rem 0.75rem;">
                    <i class="bi bi-box-arrow-down me-1"></i> Trích xuất
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="exportDropdown">
                    <li>
                        <a class="dropdown-item" href="#" onclick="window.print()">
                            <i class="bi bi-printer text-secondary me-2"></i> In báo cáo
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="exportPDF()">
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Xuất file PDF
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.reports.export-csv', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i> Xuất file Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 4 Main Primary Stats -->
    <div class="row g-3 mb-4">
        <!-- Card 1 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px; background: linear-gradient(135deg, #198754, #146c43);">
                <div class="card-body p-4 text-white position-relative overflow-hidden">
                    <i class="bi bi-file-earmark-text position-absolute end-0 top-0 mt-3 me-3" style="font-size: 5rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.8rem; opacity: 0.9;">Tổng Bài Viết</h6>
                    <h2 class="fw-black mb-0 display-5" style="font-weight: 900;">{{ number_format($totalPosts) }}</h2>
                    <div class="mt-3" style="font-size: 0.85rem;">
                        <span class="badge bg-white text-success rounded-pill me-1"><i class="bi bi-check-circle-fill"></i> {{ number_format($publishedPosts) }} Đã đăng</span>
                        <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-hourglass-split"></i> {{ number_format($pendingPosts) }} Chờ</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px; background: linear-gradient(135deg, #0d6efd, #0a58ca);">
                <div class="card-body p-4 text-white position-relative overflow-hidden">
                    <i class="bi bi-people position-absolute end-0 top-0 mt-3 me-3" style="font-size: 5rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.8rem; opacity: 0.9;">Tổng Người Dùng</h6>
                    <h2 class="fw-black mb-0 display-5" style="font-weight: 900;">{{ number_format($usersCount) }}</h2>
                    <div class="mt-3" style="font-size: 0.85rem; opacity: 0.9;">
                        Gồm {{ $usersByRole['admin'] ?? 0 }} admin, {{ $usersByRole['editor'] ?? 0 }} biên tập...
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px; background: linear-gradient(135deg, #FF6600, #e65c00);">
                <div class="card-body p-4 text-white position-relative overflow-hidden">
                    <i class="bi bi-eye position-absolute end-0 top-0 mt-3 me-3" style="font-size: 5rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.8rem; opacity: 0.9;">Tổng Lượt Xem</h6>
                    <h2 class="fw-black mb-0 display-5" style="font-weight: 900;">{{ number_format($totalViews) }}</h2>
                    <div class="mt-3" style="font-size: 0.85rem; opacity: 0.9;">
                        Lượt xem tích luỹ từ mọi bài viết
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 12px; background: linear-gradient(135deg, #6f42c1, #59339d);">
                <div class="card-body p-4 text-white position-relative overflow-hidden">
                    <i class="bi bi-chat-dots position-absolute end-0 top-0 mt-3 me-3" style="font-size: 5rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.8rem; opacity: 0.9;">Tương tác (Bình luận)</h6>
                    <h2 class="fw-black mb-0 display-5" style="font-weight: 900;">{{ number_format($commentsCount) }}</h2>
                    <div class="mt-3" style="font-size: 0.85rem; opacity: 0.9;">
                        Cùng {{ number_format($categoriesCount) }} Chuyên mục đang active
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Row -->
    <div class="row g-4 mb-4">
        <!-- Line Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0">Biểu đồ Đăng tin theo tháng ({{ date('Y') }})</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="postsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0">Cơ cấu Người dùng</h5>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <canvas id="usersChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jsPDF & html2pdf CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function exportPDF() {
        const element = document.querySelector('.container-fluid');
        // Hide buttons temporarily so they don't appear in PDF
        const headerActions = document.querySelector('.header-actions');
        if (headerActions) headerActions.style.display = 'none';
        
        var opt = {
            margin:       10,
            filename:     'bao_cao_he_thong_' + new Date().toISOString().slice(0,10) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            if (headerActions) headerActions.style.display = ''; // Restore
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Line Chart: Bài viết theo tháng
        const ctxPosts = document.getElementById('postsChart').getContext('2d');
        const postsChart = new Chart(ctxPosts, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Số bài viết mới',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#198754',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#198754',
                    fill: true,
                    tension: 0.4 // Làm cong đường
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Doughnut Chart: Người dùng
        const ctxUsers = document.getElementById('usersChart').getContext('2d');
        const usersByRole = {!! json_encode($usersByRole) !!};
        
        const labels = Object.keys(usersByRole).map(role => role.toUpperCase());
        const data = Object.values(usersByRole);
        
        // Màu sắc
        const bgColors = ['#198754', '#0d6efd', '#FF6600', '#6c757d', '#6f42c1'];

        const usersChart = new Chart(ctxUsers, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bgColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
