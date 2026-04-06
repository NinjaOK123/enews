@extends('layouts.guest')
@section('title', 'Đăng nhập — E-News Đại học An Giang')

@section('content')
<style>
.glass-card {
  width: 100%;
  max-width: 420px;
  padding: 40px;
  border-radius: 20px;
  backdrop-filter: blur(15px);
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
  animation: fadeIn 1s ease-in-out;
  margin: 0 auto;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(.9);
    }
    to{
        opacity: 1;
        transform: scale(1);
    }
}

.glass-card h2 {
    text-align: center;
    font-size: 26px;
    color: #fff;
    margin-bottom: 25px;
    letter-spacing: 1px;
    font-weight: 700;
}

/* Phần brand info E-news được custom theo form nhưng giữ tông màu gốc */
.brand-subtitle-1 {
    font-size: 0.85rem;
    font-weight: 700;
    color: #34d399; /* Text color green */
    text-align: center;
    margin-bottom: 2px;
    text-transform: uppercase;
}

.brand-subtitle-2 {
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    text-align: center;
    margin-bottom: 25px;
    text-transform: uppercase;
}

/* Auth Google Button được làm theo style btn của user nhưng nền trắng */
.btn-google {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 30px;
    background: #ffffff;
    color: #333;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    letter-spacing: 1px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 25px;
    text-decoration: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.btn-google:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
}

.divider {
    text-align: center;
    color: #ddd;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 25px;
}

.input-box {
    position: relative;
    margin-bottom: 25px;
}

.input-box input {
    width: 100%;
    padding: 14px 45px;
    border: none; 
    border-radius: 30px;
    background: rgba(255,255,255, 0.15);
    color: #fff;
    font-size: 16px;
    outline: none;
    transition: background .3s ease;
}
.input-box input::placeholder {
    color: #ddd;
}

.input-box input:focus {
    background: rgba(255, 255,255, 0.25);
}

.input-box i {
    position: absolute;
    top: 50%;
    left: 17px;
    transform: translateY(-50%);
    color: #ddd;
    font-size: 17px;
}

.btn-login {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 30px;
    background: linear-gradient(135deg, #3b82f6, #9333ea);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: 1px;
    margin-top: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-login:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(147, 51, 234, 0.4);
}

.bottom-text {
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #ddd;
    font-size: 14px;
    padding: 0 5px;
}

.bottom-text a {
    color: #fff;
    text-decoration: underline;
}

.error-msg {
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.5);
    color: #fff;
    padding: 12px 16px;
    border-radius: 15px;
    margin-bottom: 25px;
    font-size: 14px;
}
</style>

<div class="glass-card">
    
    <h2>E-NEWS</h2>
    <div class="brand-subtitle-1">Hệ thống quản lý tin tức</div>
    <div class="brand-subtitle-2">Trường Đại học An Giang</div>

    @if(session('error'))
    <div class="error-msg">
        {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="error-msg">
        Lỗi đăng nhập, vui lòng kiểm tra lại.
    </div>
    @endif

    {{-- Nút Đăng nhập Google --}}
    <a href="{{ route('auth.google') }}" class="btn-google">
        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" style="width: 18px; margin-right: 8px;">
        Đăng nhập với Gmail AGU
    </a>

    <div class="divider">Hoặc bằng tài khoản</div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="input-box">
            <i class="bi bi-envelope-fill"></i>
            <input type="text" name="login" required autocomplete="username" placeholder="Tên tài khoản" value="{{ old('login', request()->cookie('last_login_username')) }}" autofocus>
        </div>

        <div class="input-box">
            <i class="bi bi-shield-lock-fill"></i>
            <input type="password" name="password" required autocomplete="current-password" placeholder="Mật khẩu">
        </div>

        <button type="submit" class="btn-login">Đăng nhập</button>
        
        <div class="bottom-text">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="remember" checked style="margin-right:8px; width:15px; height:15px; accent-color:#9333ea; cursor:pointer;">
                Ghi nhớ
            </label>
            <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
        </div>
    </form>
</div>
@endsection
