@extends('layouts.admin')
@section('title', 'Báo cáo & Thống kê')

@section('content')
<div class="container-fluid p-0">

    {{-- ── Header + Toolbar ─────────────────────────────────────── --}}
    <div style="background:#fff; border-radius:16px; padding:20px 24px; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:24px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            {{-- Title --}}
            <div>
                <h2 class="fw-bold mb-1 d-flex align-items-center gap-2" style="font-size:1.35rem;">
                    <span style="background:linear-gradient(135deg,#198754,#2d9e60);border-radius:10px;width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="bi bi-bar-chart-line-fill text-white" style="font-size:.95rem;"></i>
                    </span>
                    Báo cáo Hệ thống
                </h2>
                <p class="text-muted mb-0" style="font-size:.82rem; padding-left:48px;">Thống kê chi tiết bài viết, lượt xem và người dùng</p>
            </div>

            {{-- Actions --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Date filter --}}
                <form action="{{ route('admin.reports.index') }}" method="GET"
                      class="d-flex align-items-center gap-2"
                      style="background:#f8f9fa; border:1.5px solid #e9ecef; border-radius:10px; padding:6px 12px;">
                    <i class="bi bi-calendar3 text-muted" style="font-size:.85rem;"></i>
                    <input type="date" name="start_date"
                           class="border-0 bg-transparent shadow-none"
                           style="font-size:.82rem; outline:none; width:120px; color:#444;"
                           value="{{ request('start_date') }}" title="Từ ngày">
                    <span class="text-muted" style="font-size:.8rem;">→</span>
                    <input type="date" name="end_date"
                           class="border-0 bg-transparent shadow-none"
                           style="font-size:.82rem; outline:none; width:120px; color:#444;"
                           value="{{ request('end_date') }}" title="Đến ngày">
                    <button type="submit"
                            style="background:#198754;color:#fff;border:none;border-radius:7px;padding:4px 14px;font-size:.8rem;font-weight:600;cursor:pointer;white-space:nowrap;">
                        <i class="bi bi-funnel-fill me-1"></i>Lọc
                    </button>
                </form>

                {{-- Clear filter --}}
                @if(request('start_date') || request('end_date'))
                <a href="{{ route('admin.reports.index') }}"
                   style="display:inline-flex;align-items:center;gap:5px;background:#fee2e2;color:#dc3545;border:none;border-radius:8px;padding:6px 12px;font-size:.8rem;font-weight:600;text-decoration:none;">
                    <i class="bi bi-x-circle-fill"></i> Xóa lọc
                </a>
                @endif

                {{-- Export dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-success fw-semibold dropdown-toggle d-flex align-items-center gap-2"
                            type="button" data-bs-toggle="dropdown"
                            style="border-radius:10px; padding:7px 16px; font-size:.85rem; background:linear-gradient(135deg,#198754,#146c43); border:none; box-shadow:0 2px 8px rgba(25,135,84,.3);">
                        <i class="bi bi-download"></i> Trích xuất
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px; overflow:hidden; min-width:190px;">
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="window.print()">
                                <span style="width:28px;height:28px;background:#f3f4f6;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-printer text-secondary"></i></span>
                                In báo cáo
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="exportPDF()">
                                <span style="width:28px;height:28px;background:#fff1f0;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-file-earmark-pdf text-danger"></i></span>
                                Xuất file PDF
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                               href="{{ route('admin.reports.export-csv', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                                <span style="width:28px;height:28px;background:#f0fdf4;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-file-earmark-excel text-success"></i></span>
                                Xuất file Excel (.xlsx)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Date range label (nếu đang lọc) --}}
        @if(request('start_date') && request('end_date'))
        <div class="mt-3" style="padding-left:4px;">
            <span style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:20px;padding:4px 14px;font-size:.78rem;color:#15803d;font-weight:600;">
                <i class="bi bi-funnel-fill me-1"></i>
                Đang lọc: {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                → {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
            </span>
        </div>
        @endif
    </div>


    <!-- 4 Main Primary Stats -->
    <div class="row g-3 mb-4">
        <!-- Card 1 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 14px; background: linear-gradient(135deg, #198754, #146c43);">
                <div class="card-body p-3 text-white position-relative overflow-hidden">
                    <i class="bi bi-file-earmark-text position-absolute end-0 top-0 mt-2 me-2" style="font-size: 4rem; opacity: 0.15;"></i>
                    <div class="fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.72rem; opacity: 0.85;">Tổng Bài Viết</div>
                    <div class="fw-black mb-1" style="font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 900; line-height: 1.1;">{{ number_format($totalPosts) }}</div>
                    <div style="font-size: 0.78rem; opacity: 0.9; margin-top: 8px; display:flex; gap:6px; flex-wrap:wrap;">
                        <span class="badge bg-white text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>{{ number_format($publishedPosts) }} Đã đăng</span>
                        <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-hourglass-split me-1"></i>{{ number_format($pendingPosts) }} Chờ</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 14px; background: linear-gradient(135deg, #0d6efd, #0a58ca);">
                <div class="card-body p-3 text-white position-relative overflow-hidden">
                    <i class="bi bi-people position-absolute end-0 top-0 mt-2 me-2" style="font-size: 4rem; opacity: 0.15;"></i>
                    <div class="fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.72rem; opacity: 0.85;">Tổng Người Dùng</div>
                    <div class="fw-black mb-1" style="font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 900; line-height: 1.1;">{{ number_format($usersCount) }}</div>
                    <div style="font-size: 0.78rem; opacity: 0.85; margin-top: 8px;">
                        {{ $usersByRole['admin'] ?? 0 }} admin · {{ $usersByRole['editor'] ?? 0 }} biên tập · {{ $usersByRole['contributor'] ?? 0 }} CTV
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 14px; background: linear-gradient(135deg, #FF6600, #e65c00);">
                <div class="card-body p-3 text-white position-relative overflow-hidden">
                    <i class="bi bi-eye position-absolute end-0 top-0 mt-2 me-2" style="font-size: 4rem; opacity: 0.15;"></i>
                    <div class="fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.72rem; opacity: 0.85;">Tổng Lượt Xem</div>
                    <div class="fw-black mb-1" style="font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 900; line-height: 1.1;">{{ number_format($totalViews) }}</div>
                    <div style="font-size: 0.78rem; opacity: 0.85; margin-top: 8px;">Lượt xem tích luỹ từ mọi bài viết</div>
                </div>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm hover-lift h-100" style="border-radius: 14px; background: linear-gradient(135deg, #6f42c1, #59339d);">
                <div class="card-body p-3 text-white position-relative overflow-hidden">
                    <i class="bi bi-chat-dots position-absolute end-0 top-0 mt-2 me-2" style="font-size: 4rem; opacity: 0.15;"></i>
                    <div class="fw-bold text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 0.72rem; opacity: 0.85;">Tương Tác (Bình Luận)</div>
                    <div class="fw-black mb-1" style="font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 900; line-height: 1.1;">{{ number_format($commentsCount) }}</div>
                    <div style="font-size: 0.78rem; opacity: 0.85; margin-top: 8px;">{{ number_format($categoriesCount) }} chuyên mục đang hoạt động</div>
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

        const roleNames = {
            'admin':       'Quản trị viên',
            'editor':      'Biên tập viên',
            'contributor': 'Cộng tác viên',
            'viewer':      'Người xem',
            'reader':      'Đọc giả',
        };
        const labels = Object.keys(usersByRole).map(role => roleNames[role] || role);
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
