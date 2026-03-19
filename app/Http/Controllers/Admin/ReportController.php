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
}

