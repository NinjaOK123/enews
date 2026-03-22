<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Hiển thị form nhập email
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    // Xử lý gửi link đặt lại mật khẩu
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email'    => 'Email không hợp lệ.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Luôn trả thông báo thành công (bảo mật — không lộ email tồn tại)
        if (!$user) {
            return back()->with('status',
                'Nếu email tồn tại trong hệ thống, chúng tôi đã gửi link đặt lại mật khẩu. Vui lòng kiểm tra hộp thư.'
            );
        }

        // Xóa token cũ (nếu có)
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Tạo token mới
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => hash('sha256', $token), // Lưu hash
            'created_at' => Carbon::now(),
        ]);

        // Gửi email
        Mail::to($user->email)->send(new ResetPasswordMail($user, $token));

        return back()->with('status',
            'Chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn. Link có hiệu lực trong 30 phút.'
        );
    }
}
