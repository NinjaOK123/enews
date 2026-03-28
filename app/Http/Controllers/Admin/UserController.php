<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = User::query();
        
        // Lọc theo từ khóa (tên, email, mssv)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }
        
        // Lọc theo vai trò
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:50',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'editor', 'contributor', 'reader'])],
            'status' => ['required', Rule::in(['active', 'inactive'])]
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required','string','max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required','string','email','max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in(['admin', 'editor', 'contributor', 'reader'])],
            'status' => ['required', Rule::in(['active', 'inactive'])]
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Bulk action: Nâng quyền, hạ quyền, khoá...
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('bulk_action');
        $userIds = $request->input('user_ids', []);

        if (!$action || empty($userIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất 1 người dùng và thao tác cần thực hiện.');
        }

        $users = User::whereIn('id', $userIds)->get();
        $successCount = 0;

        foreach ($users as $user) {
            if ($user->id === auth()->id()) continue; // Không tự xử lý account của chính mình

            if ($action === 'upgrade_contributor') {
                $user->update(['role' => 'contributor']);
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Cập nhật tài khoản',
                    'message' => '🎉 Bạn đã được Ban biên tập NÂNG CẤP thành Cộng tác viên! Hãy bắt đầu gửi bài viết ngay nào.',
                    'type' => 'success',
                    'link' => route('contributor.dashboard')
                ]);
                $successCount++;
            } elseif ($action === 'downgrade_reader') {
                $user->update(['role' => 'reader']);
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Cập nhật tài khoản',
                    'message' => '⚠️ Quyền thay đổi: Tài khoản của bạn hiện đang là Người đọc thông thường.',
                    'type' => 'error',
                ]);
                $successCount++;
            } elseif ($action === 'lock_account') {
                $user->update(['status' => 'inactive']);
                $successCount++;
            } elseif ($action === 'unlock_account') {
                $user->update(['status' => 'active']);
                $successCount++;
            } elseif ($action === 'delete') {
                $user->delete();
                $successCount++;
            }
        }

        if ($successCount === 0) {
            return back()->with('error', 'Không có người dùng nào được áp dụng (Bạn không thể tự thao tác trên chính tài khoản của bạn).');
        }

        return back()->with('success', "Đã áp dụng thành công thao tác trên {$successCount} tài khoản.");
    }
}
