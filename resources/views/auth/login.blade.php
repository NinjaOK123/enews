<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập — E-News AGU</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { 
      font-family: 'Inter', sans-serif; 
      min-height: 100vh; 
      display: flex; 
      justify-content: center;
      align-items: center;
      background: url('{{ asset("images/campus-bg.png") }}') center center / cover no-repeat fixed;
    }
    
    .login-container {
      width: 100%;
      max-width: 420px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      padding: 35px 30px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(4px);
      margin: 20px;
    }

    .brand-title {
      font-size: 2.2rem;
      font-weight: 900;
      color: #0b5ed7;
      text-align: center;
      margin-bottom: 2px;
      letter-spacing: 1px;
    }
    
    .brand-subtitle-1 {
      font-size: 0.85rem;
      font-weight: 600;
      color: #dc3545;
      text-align: center;
      margin-bottom: 2px;
      text-transform: uppercase;
    }

    .brand-subtitle-2 {
      font-size: 1.05rem;
      font-weight: 800;
      color: #dc3545;
      text-align: center;
      margin-bottom: 24px;
      text-transform: uppercase;
    }

    .section-label {
      font-size: 0.9rem;
      font-weight: 700;
      color: #212529;
      margin-bottom: 8px;
    }

    .btn-google {
      background-color: #0b5ed7;
      color: #fff;
      border: none;
      width: 100%;
      padding: 10px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.95rem;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      transition: background-color 0.2s;
      text-decoration: none;
      margin-bottom: 20px;
    }
    .btn-google:hover {
      background-color: #0a58ca;
      color: #fff;
    }

    .form-control {
      border-radius: 4px;
      padding: 10px 14px;
      font-size: 0.9rem;
      border: 1px solid #ced4da;
      margin-bottom: 12px;
    }
    .form-control:focus {
      border-color: #0b5ed7;
      box-shadow: 0 0 0 0.25rem rgba(11, 94, 215, 0.25);
    }
    
    .btn-login {
      background-color: transparent;
      color: #0b5ed7;
      border: 1px solid #0b5ed7;
      width: 100%;
      padding: 10px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.2s;
      margin-bottom: 12px;
    }
    .btn-login:hover {
      background-color: #0b5ed7;
      color: #fff;
    }

    .forgot-pwd {
      font-size: 0.85rem;
      color: #0b5ed7;
      text-decoration: none;
    }
    .forgot-pwd:hover {
      text-decoration: underline;
    }

    .alert-danger-custom {
      background: #f8d7da;
      border: 1px solid #f5c2c7;
      color: #842029;
      border-radius: 4px;
      padding: 10px 12px;
      font-size: 0.85rem;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

  <div class="login-container">
    {{-- Branding --}}
    <div class="brand-title">E-NEWS</div>
    <div class="brand-subtitle-1">Hệ thống quản lý tin tức</div>
    <div class="brand-subtitle-2">Trường Đại học An Giang</div>

    {{-- Flash messages --}}
    @if(session('error'))
    <div class="alert-danger-custom">
      <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert-danger-custom">
      <i class="bi bi-exclamation-circle-fill me-1"></i> Lỗi đăng nhập, vui lòng kiểm tra lại.
    </div>
    @endif

    {{-- Google Login Section --}}
    <div class="section-label">Đăng nhập qua:</div>
    <a href="{{ route('auth.google') }}" class="btn-google">
      <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="G" style="width: 18px; margin-right: 5px;"> Gmail AGU
    </a>

    {{-- Credentials Form Section --}}
    <div class="section-label" style="text-transform: uppercase; font-size: 0.85rem; margin-top: 24px;">HOẶC tài khoản:</div>
    <form method="POST" action="{{ route('login') }}" novalidate>
      @csrf

      <input type="text"
             name="login"
             class="form-control"
             placeholder="Tên tài khoản:"
             value="{{ old('login') }}"
             autocomplete="username"
             required
             autofocus>

      <input type="password"
             name="password"
             class="form-control"
             placeholder="Mật khẩu"
             autocomplete="current-password"
             required>

      <button type="submit" class="btn-login mt-2">
        Đăng nhập
      </button>

      <div class="text-start mt-2">
        <a href="#" class="forgot-pwd">Quên mật khẩu?</a>
      </div>
    </form>
  </div>

</body>
</html>
