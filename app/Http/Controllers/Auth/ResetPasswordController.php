<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    // Hiển thị form đặt lại mật khẩu
    public function showForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // Xử lý đặt lại mật khẩu
    public function reset(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.required'    => 'Vui lòng nhập mật khẩu mới.',
            'password.min'         => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed'   => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Tìm record trong bảng password_reset_tokens
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Kiểm tra tồn tại
        if (!$record) {
            return back()->withErrors([
                'email' => 'Không có bản ghi về yêu cầu đặt lại đó. Hãy khởi tạo yêu cầu đặt lại mật khẩu mới.',
            ]);
        }

        // Kiểm tra token hợp lệ
        if (!hash_equals($record->token, hash('sha256', $request->token))) {
            return back()->withErrors(['email' => 'Link đặt lại mật khẩu không hợp lệ.']);
        }

        // Kiểm tra hết hạn (30 phút)
        if (Carbon::parse($record->created_at)->addMinutes(30)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors([
                'email' => 'Link đặt lại mật khẩu đã hết hạn (30 phút). Hãy khởi tạo yêu cầu đặt lại mật khẩu mới.',
            ]);
        }

        // Cập nhật mật khẩu
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Xóa token đã dùng
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', '✅ Mật khẩu đã được cập nhật thành công! Vui lòng đăng nhập.');
    }
}
