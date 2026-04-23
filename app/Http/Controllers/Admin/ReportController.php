<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports and statistics.
     */
    public function index(Request $request)
    {
        // Handling Date Filter (Từ ngày - Đến ngày)
        $period = $request->input('period');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateRange = null;

        if ($period) {
            $now = Carbon::now();
            if ($period === 'week') {
                $startDate = $now->copy()->startOfWeek()->format('Y-m-d');
                $endDate = $now->copy()->endOfWeek()->format('Y-m-d');
            } elseif ($period === 'month') {
                $startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                $endDate = $now->copy()->endOfMonth()->format('Y-m-d');
            } elseif ($period === 'year') {
                $startDate = $now->copy()->startOfYear()->format('Y-m-d');
                $endDate = $now->copy()->endOfYear()->format('Y-m-d');
            }
        }

        if ($startDate && $endDate) {
            $dateRange = [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()];
        }

        // Apply global date filter closure
        $applyDateFilter = function ($query) use ($dateRange) {
            if ($dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            }
            return $query;
        };

        // 1. Post Stats
        $totalPosts = $applyDateFilter(Post::query())->count();
        $publishedPosts = $applyDateFilter(Post::where('status', 'published'))->count();
        $pendingPosts = $applyDateFilter(Post::where('status', 'pending'))->count();
        $draftPosts = $applyDateFilter(Post::where('status', 'draft'))->count();

        // 2. User Stats
        $totalUsers = clone User::query();
        if ($dateRange) { 
            $totalUsers = $totalUsers->whereBetween('created_at', $dateRange);
        }
        $usersCount = $totalUsers->count();
        
        $usersByRole = clone User::query();
        if ($dateRange) { 
            $usersByRole = $usersByRole->whereBetween('created_at', $dateRange);
        }
        $usersByRole = $usersByRole->selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role');

        // 3. Views Stats
        $totalViews = $applyDateFilter(Post::query())->sum('view_count');

        // 4. Comment & Category Stats
        $totalComments = clone Comment::query();
        if ($dateRange) {
            $totalComments = $totalComments->whereBetween('created_at', $dateRange);
        }
        $commentsCount = $totalComments->count();

        $totalCategories = clone Category::query();
        if ($dateRange) {
            $totalCategories = $totalCategories->whereBetween('created_at', $dateRange);
        }
        $categoriesCount = $totalCategories->count();

        // --- HỘI ĐỒNG: BỔ SUNG NGỮ CẢNH SO SÁNH (COMPARE PREVIOUS PERIOD) ---
        $currentStart = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30)->startOfDay();
        $currentEnd   = $dateRange ? $dateRange[1] : Carbon::now()->endOfDay();
        
        $diffDays = (int) round($currentStart->diffInDays($currentEnd)) ?: 1;
        
        $prevStart = clone $currentStart;
        $prevStart->subDays($diffDays + 1);
        $prevEnd = clone $currentEnd;
        $prevEnd->subDays($diffDays + 1);

        $pulseLabel = $dateRange ? "so với " . ($diffDays) . " ngày trước" : "so với 30 ngày trước";

        $getPulse = function($baseQuery) use ($currentStart, $currentEnd, $prevStart, $prevEnd) {
            $cur = (clone $baseQuery)->whereBetween('created_at', [$currentStart, $currentEnd]);
            $prev = (clone $baseQuery)->whereBetween('created_at', [$prevStart, $prevEnd]);
            return ['cur' => $cur, 'prev' => $prev];
        };

        // Growths
        $postP = $getPulse(Post::query());
        $curPosts = $postP['cur']->count(); $prevPosts = $postP['prev']->count();
        $growthPosts = $prevPosts > 0 ? round((($curPosts - $prevPosts) / $prevPosts) * 100, 1) : ($curPosts > 0 ? 100 : 0);

        $userP = $getPulse(User::query());
        $curUsers = $userP['cur']->count(); $prevUsers = $userP['prev']->count();
        $growthUsers = $prevUsers > 0 ? round((($curUsers - $prevUsers) / $prevUsers) * 100, 1) : ($curUsers > 0 ? 100 : 0);

        $viewP = $getPulse(Post::query());
        $curViews = $viewP['cur']->sum('view_count'); $prevViews = $viewP['prev']->sum('view_count');
        $growthViews = $prevViews > 0 ? round((($curViews - $prevViews) / $prevViews) * 100, 1) : ($curViews > 0 ? 100 : 0);

        $commentP = $getPulse(Comment::query());
        $curComments = $commentP['cur']->count(); $prevComments = $commentP['prev']->count();
        $growthComments = $prevComments > 0 ? round((($curComments - $prevComments) / $prevComments) * 100, 1) : ($curComments > 0 ? 100 : 0);

        $growths = [
            'label' => $pulseLabel,
            'posts' => $growthPosts,
            'users' => $growthUsers,
            'views' => $growthViews,
            'comments' => $growthComments,
            'curPosts' => $curPosts, // To show context "Co them +X bai"
            'curViews' => $curViews,
        ];

        // --- HỘI ĐỒNG: BỔ SUNG ACTIONABLE INSIGHTS (THIẾU HÀNH ĐỘNG / DRILL DOWN) ---
        // Top 5 bài viết có lượt xem cao nhất (có thể lọc theo date_range nếu có)
        $topPosts = $applyDateFilter(Post::with('category'))->orderBy('view_count', 'desc')->take(5)->get();
        
        // Quá hạn: Bài viết pending > 3 ngày
        $stalePendingPosts = Post::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->count();
            
        // Top Chuyên mục: Danh sách category có lượng View tổng cao nhất trong chu kỳ
        $topCategories = Category::withCount(['posts' => function($q) use ($applyDateFilter) {
            $applyDateFilter($q);
        }])->orderBy('posts_count', 'desc')->take(5)->get();


        // Chart Data: Posts per month (for the current year)
        $chartData = Post::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = [];
        $chartValues = [];

        // Formatting chart data -> array of 12 months
        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = "Tháng $i";
            $monthData = $chartData->firstWhere('month', $i);
            $chartValues[] = $monthData ? $monthData->count : 0;
        }

        return view('admin.reports.index', compact(
            'totalPosts', 'publishedPosts', 'pendingPosts', 'draftPosts',
            'usersCount', 'usersByRole', 'totalViews', 'commentsCount', 'categoriesCount',
            'chartLabels', 'chartValues', 'startDate', 'endDate',
            'growths', 'topPosts', 'stalePendingPosts', 'topCategories'
        ));
    }

    /**
     * Xuất dữ liệu thống kê ra file Excel (.xlsx dạng SpreadsheetML).
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $dateRange = null;

        if ($startDate && $endDate) {
            $dateRange = [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()];
        }

        $applyDateFilter = function ($query) use ($dateRange) {
            if ($dateRange) {
                return $query->whereBetween('created_at', $dateRange);
            }
            return $query;
        };

        $posts = $applyDateFilter(Post::with(['category', 'author']))->orderBy('created_at', 'desc')->get();

        $statusMap = [
            'published' => 'Đã duyệt',
            'pending'   => 'Chờ duyệt',
            'draft'     => 'Nháp',
            'rejected'  => 'Từ chối',
        ];

        // ── Xuất SpreadsheetML XML (Excel mở được, không cần ext-zip) ────
        $filename = 'bao_cao_he_thong_' . date('Ymd_Hi') . '.xlsx';

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
              . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'
              . ' xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";

        // Style
        $xml .= '<Styles>'
              . '<Style ss:ID="header"><Alignment ss:WrapText="1" ss:Horizontal="Center"/>'
              . '<Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/>'
              . '<Interior ss:Color="#198754" ss:Pattern="Solid"/>'
              . '<Borders><Border ss:Position="Bottom" ss:Weight="2" ss:Color="#146c43"/></Borders>'
              . '</Style>'
              . '<Style ss:ID="row_even"><Interior ss:Color="#f8f9fa" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="row_odd"><Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/></Style>'
              . '<Style ss:ID="num"><NumberFormat ss:Format="#,##0"/></Style>'
              . '</Styles>' . "\n";

        $xml .= '<Worksheet ss:Name="Báo cáo bài viết">' . "\n";
        $xml .= '<Table>' . "\n";

        // Độ rộng cột
        $xml .= '<Column ss:Width="40"/>'    // ID
              . '<Column ss:Width="280"/>'   // Tiêu đề
              . '<Column ss:Width="130"/>'   // Chuyên mục
              . '<Column ss:Width="130"/>'   // Người đăng
              . '<Column ss:Width="80"/>'    // Lượt xem
              . '<Column ss:Width="90"/>'    // Trạng thái
              . '<Column ss:Width="120"/>'; // Ngày đăng

        // Header row
        $headers = ['ID', 'Tiêu đề', 'Chuyên mục', 'Người đăng', 'Lượt xem', 'Trạng thái', 'Ngày đăng'];
        $xml .= '<Row ss:Height="24">';
        foreach ($headers as $h) {
            $xml .= '<Cell ss:StyleID="header"><Data ss:Type="String">' . htmlspecialchars($h, ENT_XML1) . '</Data></Cell>';
        }
        $xml .= '</Row>' . "\n";

        // Data rows
        foreach ($posts as $i => $post) {
            $style = ($i % 2 === 0) ? 'row_even' : 'row_odd';
            $statusName = $statusMap[$post->status] ?? $post->status;
            $xml .= '<Row ss:Height="18">';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="Number">' . $post->id . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . htmlspecialchars($post->title, ENT_XML1) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . htmlspecialchars($post->category ? $post->category->name : 'N/A', ENT_XML1) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . htmlspecialchars($post->author ? $post->author->name : 'N/A', ENT_XML1) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="num"><Data ss:Type="Number">' . $post->view_count . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . htmlspecialchars($statusName, ENT_XML1) . '</Data></Cell>';
            $xml .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $post->created_at->format('d/m/Y H:i') . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table>' . "\n";
        $xml .= '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel"><FreezePanes/><FrozenNoSplit/><SplitHorizontal>1</SplitHorizontal><TopRowBottomPane>1</TopRowBottomPane></WorksheetOptions>' . "\n";
        $xml .= '</Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Giao diện báo cáo nhuận bút độc lập
     */
    public function royaltyIndex(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        // Get posts in the specified month with royalty_rate_id set
        $postsQuery = Post::with(['author', 'royaltyRate', 'category'])
            ->whereYear('published_at', $year)
            ->where('status', 'published')
            ->whereNotNull('royalty_rate_id');

        if ($month !== 'all') {
            $postsQuery->whereMonth('published_at', $month);
        }

        $unitFilter = $request->input('unit_name');
        if ($unitFilter) {
            $postsQuery->whereHas('author', function($q) use ($unitFilter) {
                $q->where('unit_name', $unitFilter);
            });
        }
        
        $posts = $postsQuery->orderBy('published_at', 'desc')->get();

        // Lọc danh sách Đơn vị từ user có bài viết
        $units = User::whereNotNull('unit_name')->select('unit_name')->distinct()->pluck('unit_name');

        $totalRoyalty = $posts->sum('royalty_total');

        return view('admin.reports.royalty', compact('posts', 'month', 'year', 'units', 'unitFilter', 'totalRoyalty'));
    }

    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|mimes:xlsx,xls|max:5120',
        ]);
        
        $file = $request->file('template');
        $file->storeAs('templates', 'royalty_template.xlsx');

        return redirect()->back()->with('success', 'Đã tải lên File Mẫu Báo cáo Nhuận bút thành công.');
    }

    /**
     * Xuất Excel Nhuận bút (Multi-sheet bằng PhpSpreadsheet)
     */
    public function exportRoyaltyExcel(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        $editedDataText = $request->input('edited_data');
        if ($editedDataText) {
            $postsList = json_decode($editedDataText, true);
            $posts = collect($postsList);
        } else {
            // Fallback nếu không truyền data từ form
            $posts = Post::with(['author', 'royaltyRate'])
                ->whereYear('published_at', $year)
                ->where('status', 'published')
                ->whereNotNull('royalty_rate_id');
                
            if ($month !== 'all') {
                $posts->whereMonth('published_at', $month);
            }
                
            $posts = $posts->get()->map(function($p) {
                return [
                    'title' => $p->title,
                    'author_name' => $p->source_author ?: ($p->author->name ?? 'N/A'),
                    'unit_name' => $p->author->unit_name ?? 'N/A',
                    'rate_name' => $p->royaltyRate->name ?? 'N/A',
                    'rate_group' => $p->royaltyRate->group_name ?? 'Khác',
                    'image_count' => $p->image_count,
                    'total' => $p->royalty_total
                ];
            });
        }

        $groupedByRate = $posts->groupBy('rate_group');
        $templatePath = storage_path('app/templates/royalty_template.xlsx');

        if (file_exists($templatePath)) {
            return $this->exportFromTemplate($month, $year, $groupedByRate, $templatePath);
        }

        // FALLBACK CŨ (KHI CHƯA UPLOAD TEMPLATE)
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet0 = $spreadsheet->getActiveSheet();
        $sheet0->setTitle('Tổng hợp');

        $sheetIndex = 1;
        $tongHopData = [];
        $totalAll = 0;

        foreach ($groupedByRate as $groupName => $groupPosts) {
            $safeName = substr(str_replace(['/','\\','?','*','[',']'], '_', $groupName), 0, 25);
            $sheet1 = $spreadsheet->createSheet($sheetIndex++);
            $sheet1->setTitle($safeName);
            
            $thangNamStr = $month == 'all' ? "Năm {$year}" : "Tháng {$month}/{$year}";
            $this->buildFallbackDetailSheet($sheet1, "{$thangNamStr} - {$groupName}", $groupPosts);
            
            $sum = $groupPosts->reduce(function($carry, $p) {
                return $carry + floatval(str_replace(',', '', $p['total'] ?? 0));
            }, 0);
            
            if ($sum > 0) {
                $tongHopData[] = [
                    'noidung' => 'Nhuận bút ' . $groupName,
                    'sotien' => $sum
                ];
                $totalAll += $sum;
            }
        }

        // TỔNG HỢP SHEET (FALLBACK)
        // Set column widths
        $sheet0->getColumnDimension('A')->setWidth(8);
        $sheet0->getColumnDimension('B')->setWidth(50);
        $sheet0->getColumnDimension('C')->setWidth(20);

        $sheet0->setCellValue('A2', 'BẢNG KÊ ĐỀ NGHỊ THANH TOÁN TIỀN NHUẬN BÚT');
        $sheet0->setCellValue('A3', 'TRANG TIN SINH VIÊN');
        $sheet0->setCellValue('A4', $month == 'all' ? "Năm {$year}" : "Tháng {$month} Năm {$year}");
        
        $sheet0->getStyle('A2:A3')->getFont()->setBold(true)->setSize(14);
        $sheet0->getStyle('A4')->getFont()->setItalic(true)->setSize(12);
        $sheet0->getStyle('A2:A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet0->mergeCells('A2:C2');
        $sheet0->mergeCells('A3:C3');
        $sheet0->mergeCells('A4:C4');
        
        $sheet0->setCellValue('A7', 'STT');
        $sheet0->setCellValue('B7', 'Nội dung');
        $sheet0->setCellValue('C7', 'Số tiền (VNĐ)');
        
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ]
        ];
        $sheet0->getStyle('A7:C7')->applyFromArray($headerStyle);
        $sheet0->getStyle('A7:C7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');

        $rowNum = 8;
        foreach ($tongHopData as $idx => $row) {
            $sheet0->setCellValue("A{$rowNum}", $idx + 1);
            $sheet0->setCellValue("B{$rowNum}", $row['noidung']);
            $sheet0->setCellValue("C{$rowNum}", $row['sotien']);
            
            $sheet0->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet0->getStyle("C{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
            $rowNum++;
        }
        
        $sheet0->setCellValue("B{$rowNum}", "Tổng cộng:");
        $sheet0->setCellValue("C{$rowNum}", $totalAll);
        $sheet0->getStyle("B{$rowNum}:C{$rowNum}")->getFont()->setBold(true);
        $sheet0->getStyle("C{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
        
        $sheet0->getStyle("A8:C{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Chữ ký
        $rowNum += 3;
        $sheet0->setCellValue("A{$rowNum}", "Người lập bảng");
        $sheet0->setCellValue("C{$rowNum}", "Thủ trưởng đơn vị");
        $sheet0->getStyle("A{$rowNum}:C{$rowNum}")->getFont()->setBold(true);
        $sheet0->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet0->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return $this->downloadSpreadsheet($spreadsheet, $month, $year);
    }

    private function buildFallbackDetailSheet($sheet, $title, $posts)
    {
        // Set column widths to match analysis
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        // Titles
        $sheet->setCellValue('A1', 'BẢNG THANH TOÁN TIỀN NHUẬN BÚT TRANG TIN SINH VIÊN');
        $sheet->setCellValue('A2', $title);
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Headers
        $headers = ['STT', 'Tác giả', 'Đơn vị', 'Nội dung tin/bài', 'Đơn giá', 'Chiết tính', 'Thành tiền', 'Ký nhận'];
        $sheet->fromArray($headers, NULL, 'A4');
        
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A4:H4')->applyFromArray($headerStyle);
        $sheet->getStyle('A4:H4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F0F0');

        $rowNum = 5;
        $idx = 1;
        $total = 0;
        foreach ($posts as $post) {
            $lineTotal = floatval(str_replace(',', '', $post['total'] ?? 0));
            $sheet->setCellValue("A{$rowNum}", $idx++);
            $sheet->setCellValue("B{$rowNum}", $post['author_name']);
            $sheet->setCellValue("C{$rowNum}", $post['unit_name']);
            $sheet->setCellValue("D{$rowNum}", $post['title']);
            $sheet->setCellValue("E{$rowNum}", $lineTotal);
            $sheet->setCellValue("F{$rowNum}", 1);
            $sheet->setCellValue("G{$rowNum}", $lineTotal);
            $sheet->setCellValue("H{$rowNum}", '');
            
            // Text wrap for long titles
            $sheet->getStyle("D{$rowNum}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Format numbers
            $sheet->getStyle("E{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
            
            $total += $lineTotal;
            $rowNum++;
        }
        
        // Sum row
        $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", 'Tổng cộng:');
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue("G{$rowNum}", $total);
        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
        
        $sheet->getStyle("A5:H{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Signatures
        $rowNum += 3;
        $sheet->setCellValue("A{$rowNum}", "Người đề nghị thanh toán");
        $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
        
        $sheet->setCellValue("C{$rowNum}", "Thư viện");
        
        $sheet->setCellValue("E{$rowNum}", "Kế toán trưởng");
        $sheet->mergeCells("E{$rowNum}:F{$rowNum}");
        
        $sheet->setCellValue("G{$rowNum}", "Thủ trưởng đơn vị");
        $sheet->mergeCells("G{$rowNum}:H{$rowNum}");
        
        $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    private function exportFromTemplate($month, $year, $groupedByRate, $templatePath)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet0 = $spreadsheet->getSheet(0); // Tổng hợp
        $masterSheet = $spreadsheet->getSheet(1); // Khuôn Đúc

        $totalAll = 0;
        $tongHopData = [];
        $thangNamStr = $month == 'all' ? "Năm {$year}" : "Tháng {$month}/{$year}";

        foreach ($groupedByRate as $groupName => $groupPosts) {
            $safeName = substr(str_replace(['/','\\','?','*','[',']'], '_', $groupName), 0, 25);
            $clonedSheet = clone $masterSheet;
            $clonedSheet->setTitle($safeName);
            $spreadsheet->addSheet($clonedSheet);
            
            // Xử lý Thay thế text tiêu đề
            $this->replacePlaceholders($clonedSheet, [
                '[THANG_NAM]' => $thangNamStr,
                '[TEN_NHOM]' => $groupName
            ]);

            // Tìm marker [STT] ở cột A (mặc định)
            $dataRowIndex = 5;
            $foundMarker = false;
            foreach ($clonedSheet->getRowIterator() as $row) {
                $cell = $clonedSheet->getCell("A" . $row->getRowIndex());
                if (trim($cell->getValue() ?? '') === '[STT]') {
                    $dataRowIndex = $row->getRowIndex();
                    $foundMarker = true;
                    break;
                }
            }

            $idx = 1;
            $groupTotal = 0;
            
            if ($foundMarker) {
                // Xoá marker hiện tại
                $clonedSheet->setCellValue("A{$dataRowIndex}", '');
                
                foreach ($groupPosts as $post) {
                    $lineTotal = floatval(str_replace(',', '', $post['total'] ?? 0));
                    
                    // Copy style của row mẫu xuống
                    $clonedSheet->insertNewRowBefore($dataRowIndex + 1, 1);
                    
                    $clonedSheet->setCellValue("A{$dataRowIndex}", $idx++);
                    $clonedSheet->setCellValue("B{$dataRowIndex}", $post['author_name']);
                    $clonedSheet->setCellValue("C{$dataRowIndex}", $post['unit_name']);
                    $clonedSheet->setCellValue("D{$dataRowIndex}", $post['title']);
                    $clonedSheet->setCellValue("E{$dataRowIndex}", ''); 
                    $clonedSheet->setCellValue("F{$dataRowIndex}", ''); 
                    $clonedSheet->setCellValue("G{$dataRowIndex}", $lineTotal);
                    
                    $groupTotal += $lineTotal;
                    $dataRowIndex++;
                }
                
                // Điền tổng vào row [TONG] (Nếu có)
                foreach ($clonedSheet->getRowIterator($dataRowIndex) as $row) {
                    $cell = $clonedSheet->getCell("A" . $row->getRowIndex());
                    if (strpos($cell->getValue() ?? '', '[TONG]') !== false) {
                        $clonedSheet->setCellValue("A" . $row->getRowIndex(), 'Tổng cộng:');
                        $clonedSheet->setCellValue("G" . $row->getRowIndex(), $groupTotal);
                    }
                    if (strpos($clonedSheet->getCell("G" . $row->getRowIndex())->getValue() ?? '', '[TONG]') !== false) {
                        $clonedSheet->setCellValue("G" . $row->getRowIndex(), $groupTotal);
                    }
                }
            }

            if ($groupTotal > 0) {
                $tongHopData[] = [
                    'noidung' => 'Nhuận bút ' . $groupName,
                    'sotien' => $groupTotal
                ];
                $totalAll += $groupTotal;
            }
        }

        // Xoá Khuôn (Sheet index 1)
        $spreadsheet->removeSheetByIndex(1);

        // Xử lý Tổng hợp (Sheet 0)
        $this->replacePlaceholders($sheet0, [
            '[THANG_NAM]' => $thangNamStr,
            '[TONG_NAM]' => "Tháng $month Năm $year",
        ]);

        $thRowIndex = 8;
        $foundTH = false;
        foreach ($sheet0->getRowIterator() as $row) {
            $cell = $sheet0->getCell("A" . $row->getRowIndex());
            if (trim($cell->getValue() ?? '') === '[STT]') {
                $thRowIndex = $row->getRowIndex();
                $foundTH = true;
                break;
            }
        }

        if ($foundTH) {
            $sheet0->setCellValue("A{$thRowIndex}", '');
            $idx = 1;
            foreach ($tongHopData as $row) {
                $sheet0->insertNewRowBefore($thRowIndex + 1, 1);
                
                $sheet0->setCellValue("A{$thRowIndex}", $idx++);
                $sheet0->setCellValue("B{$thRowIndex}", $row['noidung']);
                $sheet0->setCellValue("C{$thRowIndex}", $row['sotien']);
                $thRowIndex++;
            }
            // Điền tổng chung
            foreach ($sheet0->getRowIterator($thRowIndex) as $row) {
                $colC = "C" . $row->getRowIndex();
                if (strpos($sheet0->getCell($colC)->getValue() ?? '', '[TONG_CHUNG]') !== false) {
                    $sheet0->setCellValue($colC, $totalAll);
                }
            }
        }

        return $this->downloadSpreadsheet($spreadsheet, $month, $year);
    }

    private function replacePlaceholders($sheet, $replacements)
    {
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $val = $cell->getValue();
                if (is_string($val)) {
                    $newVal = strtr($val, $replacements);
                    if ($val !== $newVal) {
                        $cell->setValue($newVal);
                    }
                }
            }
        }
    }

    private function downloadSpreadsheet($spreadsheet, $month, $year)
    {
        if (request()->input('preview_mode') == 'true') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
            $writer->writeAllSheets();
            $html = $writer->generateHTMLAll();
            // Add some basic styling wrapper to make it center and look nice
            $html = '<div style="background: #f3f4f6; min-height: 100vh; padding: 20px; font-family: sans-serif;"><div style="background: white; max-width: 1200px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); overflow: auto;">' . $html . '</div></div>';
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        }

        $filename = "NhuanBut_{$month}.{$year}_[" . date('Y-m-d_H.i.s') . "].xlsx";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0'
        ]);
    }
}

