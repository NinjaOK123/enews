<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('frontend.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        // 1. Validate form
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // 2. Xác định field là email hay username
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Nếu là email, kiểm tra xem có đuôi @agu.edu.vn không (cơ bản)
        // Admin có ngoại lệ không? Đề bài: "từ chối đăng nhập (trừ khi là admin)". 
        // Tuy nhiên hàm Auth::attempt chưa biết user role.
        // Giải pháp: đăng nhập trước, check domain sau (trừ khi user là admin).

        $credentials = [
            $fieldType => $login,
            'password' => $password,
        ];

        // Thêm remember me (mặc định false)
        $remember = $request->has('remember');

        // 4. Thử xác thực
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // 5. Kiểm tra tài khoản bị khóa
            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withInput($request->only('login'))->withErrors([
                    'login' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.',
                ]);
            }

            // 6. Kiểm tra domain AGU nếu không phải admin
            if ($fieldType === 'email' && !$user->isAdmin()) {
                if (!str_ends_with(strtolower($user->email), '@agu.edu.vn') && !str_ends_with(strtolower($user->email), '@student.agu.edu.vn')) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->withInput($request->only('login'))->withErrors([
                        'login' => 'Chỉ email thuộc miền @agu.edu.vn mới được phép đăng nhập.',
                    ]);
                }
            }

            // 7. Login thành công -> Regenerate session để an toàn
            $request->session()->regenerate();

            // 8. Redirect theo role
            return $this->redirectBasedOnRole($user);
        }

        // Đăng nhập thất bại
        return back()->withInput($request->only('login'))->withErrors([
            'login' => 'Email/Username hoặc mật khẩu không đúng.',
        ]);
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Hàm điều hướng user dựa trên role (chỉ dùng nội bộ)
     */
    protected function redirectBasedOnRole($user)
    {
        return match ($user->role) {
            'admin'       => redirect()->to('/admin/dashboard'),
            'editor'      => redirect()->to('/editor/dashboard'),
            'contributor' => redirect()->to('/contributor/dashboard'),
            default       => redirect()->to('/'), // view, reader
        };
    }
}
