<?php

namespace App\Http\Controllers;

use App\Models\ContributorRequest;
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
            return redirect()->back()->with('error', 'Bạn đã là cộng tác viên hoặc có vai trò cao hơn.');
        }

        // Đang chờ duyệt rồi
        if (ContributorRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return redirect()->back()->with('error', 'Bạn đã có yêu cầu đang chờ duyệt. Vui lòng đợi admin xét duyệt.');
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

        return redirect()->back()->with('success', '✅ Yêu cầu đăng ký cộng tác viên đã được gửi! Admin sẽ xét duyệt sớm.');
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

        $contributorRequest->update([
            'status'      => 'rejected',
            'admin_note'  => $request->admin_note ?? 'Không đáp ứng điều kiện.',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', "❌ Đã từ chối yêu cầu của {$contributorRequest->full_name}.");
    }
}
