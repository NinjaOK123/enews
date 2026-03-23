<?php

namespace App\Http\Controllers;

use App\Models\ContributorRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContributorRequestController extends Controller
{
    /**
     * Lưu yêu cầu trở thành cộng tác viên
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Đã là contributor rồi thì không cần
        if (in_array($user->role, ['contributor', 'editor', 'admin'])) {
            return response()->json(['error' => 'Bạn đã là cộng tác viên hoặc có vai trò cao hơn.'], 422);
        }

        // Đang chờ duyệt rồi
        if (ContributorRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return response()->json(['error' => 'Bạn đã có yêu cầu đang chờ duyệt. Vui lòng đợi admin xét duyệt.'], 422);
        }

        $request->validate([
            'full_name'      => 'required|string|max:100',
            'bank_name'      => 'required|string|max:100',
            'bank_account'   => 'required|string|max:30|regex:/^[0-9]{6,20}$/',
            'account_holder' => 'required|string|max:100',
            'note'           => 'nullable|string|max:500',
        ], [
            'full_name.required'      => 'Vui lòng nhập họ tên.',
            'bank_name.required'      => 'Vui lòng chọn ngân hàng.',
            'bank_account.required'   => 'Vui lòng nhập số tài khoản.',
            'bank_account.regex'      => 'Số tài khoản chỉ gồm chữ số (6–20 ký tự).',
            'account_holder.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ]);

        ContributorRequest::create([
            'user_id'        => $user->id,
            'full_name'      => $request->full_name,
            'bank_name'      => $request->bank_name,
            'bank_account'   => $request->bank_account,
            'account_holder' => strtoupper($request->account_holder),
            'note'           => $request->note,
            'status'         => 'pending',
        ]);

        return response()->json(['success' => true]);
    }

    // ─── Admin ───────────────────────────────────────────────────────────────

    /**
     * Admin: danh sách yêu cầu
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = ContributorRequest::with('user')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => ContributorRequest::where('status', 'pending')->count(),
            'approved' => ContributorRequest::where('status', 'approved')->count(),
            'rejected' => ContributorRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.contributor-requests.index', compact('requests', 'counts', 'status'));
    }

    /**
     * Admin: duyệt yêu cầu
     */
    public function approve(ContributorRequest $contributorRequest)
    {
        $contributorRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => Carbon::now(),
        ]);

        // Nâng role user thành contributor
        $contributorRequest->user->update(['role' => 'contributor']);

        // Gửi thông báo vào chuông của user
        $this->sendNotificationToUser(
            $contributorRequest->user_id,
            '🎉 Chúc mừng! Yêu cầu Cộng tác viên được duyệt',
            "<p>Chào <strong>{$contributorRequest->full_name}</strong>,</p>
            <p>Yêu cầu trở thành <strong>Cộng tác viên</strong> của bạn đã được Admin <strong>phê duyệt</strong>.</p>
            <p>Bạn đã được cấp quyền Cộng tác viên — có thể viết và đăng bài ngay bây giờ!</p>
            <p>Thông tin ngân hàng được lưu: <strong>{$contributorRequest->bank_name}</strong> - TK: <strong>{$contributorRequest->bank_account}</strong></p>"
        );

        return redirect()->back()->with('success', "✅ Đã duyệt và cấp quyền Cộng tác viên cho {$contributorRequest->full_name}.");
    }

    /**
     * Admin: từ chối yêu cầu
     */
    public function reject(Request $request, ContributorRequest $contributorRequest)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:300',
        ]);

        $adminNote = $request->admin_note ?? 'Không đáp ứng điều kiện.';

        $contributorRequest->update([
            'status'      => 'rejected',
            'admin_note'  => $adminNote,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => Carbon::now(),
        ]);

        // Gửi thông báo vào chuông của user
        $this->sendNotificationToUser(
            $contributorRequest->user_id,
            '❌ Yêu cầu Cộng tác viên không được duyệt',
            "<p>Chào <strong>{$contributorRequest->full_name}</strong>,</p>
            <p>Rất tiếc, yêu cầu trở thành <strong>Cộng tác viên</strong> của bạn đã bị <strong>từ chối</strong>.</p>
            <p><strong>Lý do:</strong> {$adminNote}</p>
            <p>Bạn có thể gửi lại yêu cầu sau khi bổ sung đầy đủ thông tin. Nếu cần hỗ trợ, vui lòng liên hệ quản trị viên.</p>"
        );

        return redirect()->back()->with('success', "❌ Đã từ chối yêu cầu của {$contributorRequest->full_name}.");
    }

    /**
     * Helper: tạo notification gửi vào chuông của 1 user cụ thể
     */
    private function sendNotificationToUser(int $userId, string $title, string $content): void
    {
        Notification::create([
            'title'      => $title,
            'content'    => $content,
            'recipients' => [$userId], // Gửi cho đúng user ID này
            'sent_at'    => Carbon::now(),
        ]);
    }
}
