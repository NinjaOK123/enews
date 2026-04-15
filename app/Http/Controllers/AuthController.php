<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('frontend.login');
    }

    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $email = $request->username;
        if (!str_contains($email, '@')) {
            $email .= '@agu.edu.vn';
        }

        if (Auth::attempt(['email' => $email, 'password' => $request->password])) {
            $request->session()->regenerate();
            Session::forget('user'); // Xoá session custom cũ nếu có

            return redirect()->route('home')
                ->with('success', 'Chào mừng ' . Auth::user()->name . '!');
        }

        return back()
            ->withInput(['username' => $request->username])
            ->with('error', 'Tên tài khoản hoặc mật khẩu không đúng. Vui lòng thử lại.');
    }

    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        $state = Str::random(40);
        Session::put('google_oauth_state', $state);

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'state' => $state,
            'hd' => 'student.agu.edu.vn',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->state !== Session::get('google_oauth_state')) {
            return redirect()->route('login')
                ->with('error', 'Phien dang nhap khong hop le. Vui long thu lai.');
        }
        Session::forget('google_oauth_state');

        if ($request->has('error')) {
            return redirect()->route('login')
                ->with('error', 'Dang nhap Google that bai: ' . $request->error);
        }

        $code = $request->code;
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');

        try {
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Token exchange failed');
            }

            $tokens = $tokenResponse->json();
            $accessToken = $tokens['access_token'];

            $userResponse = Http::withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (!$userResponse->successful()) {
                throw new \Exception('Userinfo request failed');
            }

            $googleUser = $userResponse->json();
            $email = strtolower((string) ($googleUser['email'] ?? ''));
            $emailVerified = (bool) ($googleUser['email_verified'] ?? false);
            $domain = Str::after($email, '@');
            $allowedDomains = array_filter(array_map(
                'trim',
                explode(',', (string) config('services.google.allowed_domains', 'student.agu.edu.vn,agu.edu.vn'))
            ));

            if (!$emailVerified || !$email || !in_array($domain, $allowedDomains, true)) {
                return redirect()->route('login')
                    ->with('error', 'Chi chap nhan tai khoan Google AGU da xac thuc.');
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => (string) ($googleUser['name'] ?? 'Nguoi dung AGU'),
                    'email' => $email,
                    'password' => Hash::make(Str::random(40)),
                    'role' => 'reader',
                    'google_id' => (string) ($googleUser['sub'] ?? ''),
                    'avatar' => $googleUser['picture'] ?? null,
                ]);
            } else {
                $user->name = (string) ($googleUser['name'] ?? $user->name);
                $user->google_id = (string) ($googleUser['sub'] ?? $user->google_id);
                $user->avatar = $googleUser['picture'] ?? $user->avatar;
                $user->save();
            }

            // BUG-04: Prevent login if account is inactive/banned
            if ($user->status !== 'active') {
                return redirect()->route('login')
                    ->with('error', 'Tai khoan cua ban da bi khoa.');
            }

            Auth::login($user);
            $request->session()->regenerate();
            Session::forget('user');

            return redirect()->route('home')
                ->with('success', 'Chao mung ' . ($googleUser['name'] ?? '') . '!');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Da xay ra loi khi dang nhap Google. Vui long thu lai.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::forget('user');

        return redirect()->route('home')
            ->with('success', 'Ban da dang xuat thanh cong.');
    }
}
