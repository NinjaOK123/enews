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
        return view('admin.notifications.create');
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
        ]);

        Notification::create([
            'title' => $request->title,
            'content' => $request->content,
            'recipients' => $request->recipients,
            'sent_at' => null // Draft initially until sent
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Thông báo đã được tạo thành công.');
    }

    /**
     * Show the form for editing the notification.
     */
    public function edit(Notification $notification)
    {
        return view('admin.notifications.edit', compact('notification'));
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
        ]);

        $notification->update([
            'title' => $request->title,
            'content' => $request->content,
            'recipients' => $request->recipients,
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Thông báo đã được cập nhật thành công.');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Đã xoá thông báo.');
    }

    /**
     * Simulate sending the notification.
     */
    public function send(Notification $notification)
    {
        if ($notification->sent_at) {
            return back()->with('error', 'Thông báo này đã được gửi trước đó.');
        }

        $recipients = $notification->recipients;
        $usersToNotify = collect();

        // Interpret recipients logic
        if (in_array('all', $recipients)) {
            $usersToNotify = User::all();
        } else {
            // Get users with matching roles
            $usersToNotify = User::whereIn('role', $recipients)->get();
        }

        // Mock sending process
        // In a real scenario, use Laravel Notification system:
        // NotificationFacade::send($usersToNotify, new \App\Notifications\GeneralNotification($notification));

        // Mark as sent
        $notification->update(['sent_at' => now()]);

        return redirect()->route('admin.notifications.index')
            ->with('success', "Thông báo đã được gửi đến " . $usersToNotify->count() . " người/nhóm.");
    }
}
