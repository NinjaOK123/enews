<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Check if user is authenticated and has one of the required roles.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Kiểm tra đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Bạn cần đăng nhập để truy cập trang này.');
        }

        $user = Auth::user();

        // 2. Kiểm tra tài khoản có bị khóa không
        if ($user->status !== 'active') {
             Auth::logout();
             return redirect('/login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        // 3. Nếu không truyền role nào thì bỏ qua (chỉ check Auth)
        if (empty($roles)) {
            return $next($request);
        }

        // 4. Kiểm tra role hiện tại có nằm trong ds mảng $roles truyền vào ko
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 5. Nếu không khớp -> Báo lỗi 403 Forbidden
        abort(403, 'Bạn không có quyền truy cập vào khu vực này.');
    }
}
