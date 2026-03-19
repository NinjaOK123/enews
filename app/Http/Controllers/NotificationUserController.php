<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationUserController extends Controller
{
    /**
     * Lấy danh sách thông báo dành cho người dùng hiện tại (AJAX).
     */
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $role   = $user->role;

        // Lấy thông báo đã gửi (sent_at không null) và dành cho user này
        $notifications = Notification::whereNotNull('sent_at')
            ->where(function ($q) use ($userId, $role) {
                // recipients = null hoặc 'all' → gửi tất cả
                $q->whereNull('recipients')
                  ->orWhereRaw("JSON_CONTAINS(recipients, ?)", ['"all"'])
                  // Gửi theo role
                  ->orWhereRaw("JSON_CONTAINS(recipients, ?)", ['"' . $role . '"'])
                  // Gửi theo user ID cụ thể
                  ->orWhereRaw("JSON_CONTAINS(recipients, ?)", [(string) $userId]);
            })
            ->orderByDesc('sent_at')
            ->take(20)
            ->get(['id', 'title', 'content', 'sent_at']);

        // Danh sách ID đã đọc của user này
        $readIds = DB::table('user_notification_reads')
            ->where('user_id', $userId)
            ->pluck('notification_id')
            ->toArray();

        $items = $notifications->map(function ($n) use ($readIds) {
            return [
                'id'       => $n->id,
                'title'    => $n->title,
                'content'  => \Str::limit(strip_tags($n->content), 100),
                'sent_at'  => $n->sent_at->diffForHumans(),
                'is_read'  => in_array($n->id, $readIds),
            ];
        });

        $unreadCount = $items->where('is_read', false)->count();

        return response()->json([
            'notifications' => $items,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * Đánh dấu một thông báo đã đọc.
     */
    public function markRead($id)
    {
        $userId = auth()->id();
        DB::table('user_notification_reads')->updateOrInsert(
            ['user_id' => $userId, 'notification_id' => $id],
            ['read_at' => now()]
        );
        return response()->json(['ok' => true]);
    }

    /**
     * Đánh dấu tất cả đã đọc.
     */
    public function markAllRead()
    {
        $user   = auth()->user();
        $userId = $user->id;
        $role   = $user->role;

        $ids = Notification::whereNotNull('sent_at')
            ->where(function ($q) use ($userId, $role) {
                $q->whereNull('recipients')
                  ->orWhereRaw("JSON_CONTAINS(recipients, ?)", ['"all"'])
                  ->orWhereRaw("JSON_CONTAINS(recipients, ?)", ['"' . $role . '"'])
                  ->orWhereRaw("JSON_CONTAINS(recipients, ?)", [(string) $userId]);
            })
            ->pluck('id');

        foreach ($ids as $nid) {
            DB::table('user_notification_reads')->updateOrInsert(
                ['user_id' => $userId, 'notification_id' => $nid],
                ['read_at' => now()]
            );
        }

        return response()->json(['ok' => true]);
    }
}
