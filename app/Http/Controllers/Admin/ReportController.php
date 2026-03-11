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
     * Xuất dữ liệu thống kê ra file CSV / Excel.
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
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

        // Lấy danh sách bài viết theo khoảng thời gian
        $posts = $applyDateFilter(Post::with(['category', 'author']))->orderBy('created_at', 'desc')->get();

        $filename = "bao_cao_he_thong_" . date('Ymd_Hi') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Dùng closure stream để trả file csv
        $callback = function() use($posts) {
            $file = fopen('php://output', 'w');
            // Thêm BOM UTF-8 để Excel đọc tiếng Việt không bị lỗi font
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header file
            $columns = ['ID', 'Tiêu đề', 'Chuyên mục', 'Người đăng', 'Lượt xem', 'Trạng thái', 'Ngày đăng'];
            fputcsv($file, $columns);

            $statusMap = [
                'published' => 'Đã duyệt',
                'pending'   => 'Chờ duyệt',
                'draft'     => 'Nháp',
                'rejected'  => 'Từ chối',
            ];

            foreach ($posts as $post) {
                $statusName = $statusMap[$post->status] ?? $post->status;
                $row = [
                    $post->id,
                    $post->title,
                    $post->category ? $post->category->name : 'N/A',
                    $post->author ? $post->author->name : 'N/A',
                    $post->view_count,
                    $statusName,
                    $post->created_at->format('d/m/Y H:i')
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
