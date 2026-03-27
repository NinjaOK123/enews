<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $notifications = Notification::latest()->paginate(10);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        $groupedUsers = User::orderBy('name')->get()->groupBy('role');
        return view('admin.notifications.create', compact('groupedUsers'));
    }

    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients' => 'required|array',
        ], [
            'title.required' => 'Vui lòng nhập Tiêu đề thông báo.',
            'content.required' => 'Vui lòng nhập Nội dung thông báo (Nếu ô nhập bị mờ, hãy thử gõ vào).',
            'recipients.required' => 'Bạn chưa chọn Đối tượng nhận thông báo nào.',
            'recipients.array' => 'Định dạng đối tượng nhận không hợp lệ.'
        ]);

        $notification = Notification::create([
            'title' => $request->title,
            'content' => $request->content,
            'recipients' => $request->recipients,
            'sent_at' => null // Draft initially until sent
        ]);

        if ($request->input('action') === 'send') {
            return $this->send($notification);
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Thông báo lưu nháp thành công.');
    }

    /**
     * Show the form for editing the notification.
     */
    public function edit(Notification $notification)
    {
        $groupedUsers = User::orderBy('name')->get()->groupBy('role');
        return view('admin.notifications.edit', compact('notification', 'groupedUsers'));
    }

    /**
     * Update the notification in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients' => 'required|array',
        ], [
            'title.required' => 'Vui lòng nhập Tiêu đề thông báo.',
            'content.required' => 'Vui lòng nhập Nội dung thông báo (Nếu ô nhập bị mờ, hãy thử gõ vào).',
            'recipients.required' => 'Bạn chưa chọn Đối tượng nhận thông báo nào.',
            'recipients.array' => 'Định dạng đối tượng nhận không hợp lệ.'
        ]);

        $notification->update([
            'title' => $request->title,
            'content' => $request->content,
            'recipients' => $request->recipients,
        ]);

        if ($request->input('action') === 'send') {
            return $this->send($notification);
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Thông báo đã lưu thay đổi.');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Đã xoá thông báo.');
    }

    public function send(Notification $notification)
    {
        $recipients = $notification->recipients ?? [];
        $usersToNotify = collect();

        // Interpret recipients logic
        if (in_array('all', $recipients)) {
            $usersToNotify = User::all();
        } else {
            $roles = [];
            $userIds = [];
            foreach ($recipients as $item) {
                if (str_starts_with($item, 'user_')) {
                    $userIds[] = (int)str_replace('user_', '', $item);
                } else {
                    $roles[] = $item;
                }
            }

            $query = User::query();
            if (count($roles) > 0) {
                $query->whereIn('role', $roles);
            }
            if (count($userIds) > 0) {
                if (count($roles) > 0) {
                    $query->orWhereIn('id', $userIds);
                } else {
                    $query->whereIn('id', $userIds);
                }
            }
            
            if (count($roles) > 0 || count($userIds) > 0) {
                $usersToNotify = $query->get();
            }
        }

        // Send Email via BCC chunks to avoid SMTP limits and protect privacy
        if ($usersToNotify->count() > 0) {
            foreach ($usersToNotify->chunk(50) as $chunk) {
                Mail::bcc($chunk)->send(new \App\Mail\SystemNotificationMail($notification));
            }
        }

        // Mark as sent
        $notification->update(['sent_at' => now()]);

        return redirect()->route('admin.notifications.index')
            ->with('success', "Thông báo đã được gửi qua Email đến " . $usersToNotify->count() . " người nhận.");
    }
}
