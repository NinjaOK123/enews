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
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateRange = null;

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
        // Currently view_count is a total aggregate. If dateRange exists we just count it for posts created in that period as a simple mockup, 
        // ideally view_count should be tracked in a separate `post_views` table with timestamps.
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
            'chartLabels', 'chartValues', 'startDate', 'endDate'
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
            ->whereMonth('published_at', $month)
            ->where('status', 'published')
            ->whereNotNull('royalty_rate_id');

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

    /**
     * Xuất Excel Nhuận bút (Multi-sheet bằng PhpSpreadsheet)
     */
    public function exportRoyaltyExcel(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        $posts = Post::with(['author', 'royaltyRate'])
            ->whereYear('published_at', $year)
            ->whereMonth('published_at', $month)
            ->where('status', 'published')
            ->whereNotNull('royalty_rate_id')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet0 = $spreadsheet->getActiveSheet();
        $sheet0->setTitle('Tổng hợp');

        $groupedByRate = $posts->groupBy(function($post) {
            return $post->royaltyRate->group_name ?? 'Khác';
        });

        $sheetIndex = 1;
        $tongHopData = [];
        $totalAll = 0;

        foreach ($groupedByRate as $groupName => $groupPosts) {
            // Rút gọn tên sheet (max 31 chars)
            $safeName = substr(str_replace(['/','\\','?','*','[',']'], '_', $groupName), 0, 25);

            // BÀI VIẾT (Sheet 1)
            $sheet1 = $spreadsheet->createSheet($sheetIndex++);
            $sheet1->setTitle($safeName . ' (1)');
            $this->buildDetailSheet($sheet1, "Tháng {$month}/{$year} - {$groupName}", $groupPosts, 'article');
            
            $sum1 = $groupPosts->reduce(function($carry, $p) {
                return $carry + (($p->royaltyRate->amount ?? 0) * ($p->royalty_multiplier ?? 1));
            }, 0);
            
            if ($sum1 > 0) {
                $tongHopData[] = [
                    'noidung' => 'Nhuận bút ' . $groupName . ' (Bài viết)',
                    'sotien' => $sum1
                ];
                $totalAll += $sum1;
            }

            // HÌNH ẢNH (Sheet 2)
            $postsWithImages = $groupPosts->filter(function($p) { return $p->image_count > 0; });
            if ($postsWithImages->count() > 0) {
                $sum2 = $postsWithImages->sum('image_count') * 10000;
                $sheet2 = $spreadsheet->createSheet($sheetIndex++);
                $sheet2->setTitle($safeName . ' (2)');
                $this->buildDetailSheet($sheet2, "Tháng {$month}/{$year} - {$groupName}", $postsWithImages, 'image');
                
                $tongHopData[] = [
                    'noidung' => 'Nhuận bút ' . $groupName . ' (Ảnh)',
                    'sotien' => $sum2
                ];
                $totalAll += $sum2;
            }
        }

        // BULD TỔNG HỢP SHEET
        $sheet0->setCellValue('A1', 'Đơn vị: Trường Đại học An Giang - Thư viện');
        $sheet0->setCellValue('A3', 'BẢNG KÊ ĐỀ NGHỊ THANH TOÁN TIỀN NHUẬN BÚT');
        $sheet0->setCellValue('A5', "Tháng {$month} Năm {$year}");
        $sheet0->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        
        $sheet0->setCellValue('A8', 'STT');
        $sheet0->setCellValue('B8', 'Nội dung');
        $sheet0->setCellValue('C8', 'Số tiền');
        $sheet0->setCellValue('D8', 'Ghi chú');
        $sheet0->getStyle('A8:D8')->getFont()->setBold(true);
        $sheet0->getColumnDimension('B')->setWidth(40);
        $sheet0->getColumnDimension('C')->setWidth(15);

        $rowNum = 9;
        foreach ($tongHopData as $idx => $row) {
            $sheet0->setCellValue("A{$rowNum}", $idx + 1);
            $sheet0->setCellValue("B{$rowNum}", $row['noidung']);
            $sheet0->setCellValue("C{$rowNum}", $row['sotien']);
            $rowNum++;
        }
        
        // Tổng
        $sheet0->setCellValue("B{$rowNum}", "Tổng cộng:");
        $sheet0->setCellValue("C{$rowNum}", $totalAll);
        $sheet0->getStyle("B{$rowNum}:C{$rowNum}")->getFont()->setBold(true);

        // Xuất file
        $filename = "NhuanBut_{$month}.{$year}_[" . date('Y-m-d_H.i.s') . "].xlsx";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Fix BUG-09: Use StreamedResponse instead of exit
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0'
        ]);
    }

    private function buildDetailSheet($sheet, $title, $posts, $type)
    {
        $sheet->setCellValue('A1', 'BẢNG THANH TOÁN TIỀN NHUẬN BÚT');
        $sheet->setCellValue('A2', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $headers = ['STT', 'Tác giả', 'Đơn vị', 'Nội dung tin/bài', 'Đơn giá', 'Chiết tính', 'Thành tiền'];
        $sheet->fromArray($headers, NULL, 'A4');
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(40);

        $rowNum = 5;
        $total = 0;
        $totalCount = 0;

        $idx = 1;
        foreach ($posts as $post) {
            $rateValue = ($type == 'image') ? 10000 : ($post->royaltyRate->amount ?? 0);
            $multiplier = ($type == 'image') ? $post->image_count : ($post->royalty_multiplier ?? 1);
            $lineTotal = $rateValue * $multiplier;
            
            // Lấy tên Tác giả (Người viết bài) hoặc Người chụp ảnh
            $authorName = '';
            if ($type == 'image') {
                $authorName = $post->photographer ?: ($post->source_author ?: ($post->author->name ?? 'N/A'));
            } else {
                $authorName = $post->source_author ?: ($post->author->name ?? 'N/A');
            }
            
            $sheet->setCellValue("A{$rowNum}", $idx++);
            $sheet->setCellValue("B{$rowNum}", $authorName);
            $sheet->setCellValue("C{$rowNum}", $post->author->unit_name ?? 'N/A');
            $sheet->setCellValue("D{$rowNum}", $post->title);
            $sheet->setCellValue("E{$rowNum}", $rateValue);
            $sheet->setCellValue("F{$rowNum}", $multiplier);
            $sheet->setCellValue("G{$rowNum}", $lineTotal);
            
            $total += $lineTotal;
            $totalCount += $multiplier;
            $rowNum++;
        }

        $sheet->setCellValue("D{$rowNum}", "Tổng cộng:");
        $sheet->setCellValue("F{$rowNum}", $totalCount);
        $sheet->setCellValue("G{$rowNum}", $total);
        $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getFont()->setBold(true);
    }
}

