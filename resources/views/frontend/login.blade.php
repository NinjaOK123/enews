<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập — Trang báo Sinh viên Đại học An Giang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', Arial, sans-serif;
      min-height: 100vh;
      background: url('{{ asset("images/campus-bg.png") }}') center/cover no-repeat fixed;
      display: flex;
      align-items: stretch;
      justify-content: flex-end;
    }

    /* Subtle dark overlay on the whole page */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: rgba(0, 30, 10, 0.35);
      z-index: 0;
    }

    /* Full height left area (just shows campus) */
    .login-left {
      flex: 1;
      position: relative;
      z-index: 1;
      display: flex;
      align-items: flex-end;
      padding: 40px;
    }
    .login-left-text {
      color: rgba(255,255,255,.85);
      font-size: .82rem;
      text-shadow: 0 1px 4px rgba(0,0,0,.5);
    }

    /* Right panel: white card */
    .login-panel {
      width: 420px;
      min-height: 100vh;
      background: rgba(255,255,255,.96);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 40px;
      box-shadow: -8px 0 40px rgba(0,0,0,.18);
    }

    /* Logo + Title */
    .login-logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 14px;
    }

    .login-title-enews {
      font-size: 2.4rem;
      font-weight: 900;
      letter-spacing: 1px;
      background: linear-gradient(135deg, #1b5e20 0%, #2a7a27 50%, #4caf50 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1;
      margin-bottom: 4px;
    }

    .login-title-sub {
      font-size: .76rem;
      font-weight: 600;
      color: #FF6600;
      letter-spacing: .8px;
      text-transform: uppercase;
      text-align: center;
      margin-bottom: 2px;
    }

    .login-title-uni {
      font-size: .84rem;
      font-weight: 800;
      color: #c0392b;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: .3px;
      margin-bottom: 28px;
    }

    /* Divider */
    .login-divider {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 16px 0;
      color: #aaa;
      font-size: .78rem;
      font-weight: 500;
    }
    .login-divider::before,
    .login-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e0e0e0;
    }

    /* Gmail button */
    .btn-gmail {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 12px 20px;
      background: #1565c0;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: .9rem;
      font-weight: 700;
      cursor: pointer;
      transition: all .2s;
      text-decoration: none;
      letter-spacing: .3px;
    }
    .btn-gmail:hover {
      background: #0d47a1;
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 4px 16px rgba(21,101,192,.35);
    }
    .gmail-icon {
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Input fields */
    .login-label {
      font-size: .8rem;
      font-weight: 700;
      color: #555;
      margin-bottom: 12px;
      display: block;
      width: 100%;
    }

    .login-input {
      width: 100%;
      border: 1.5px solid #d0d0d0;
      border-radius: 8px;
      padding: 12px 14px;
      font-size: .88rem;
      font-family: 'Inter', sans-serif;
      color: #333;
      background: #f8f9fa;
      transition: all .18s;
      margin-bottom: 12px;
      outline: none;
    }
    .login-input:focus {
      border-color: #2a7a27;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(42,122,39,.12);
    }
    .login-input::placeholder { color: #aaa; }

    /* Submit button */
    .btn-submit {
      width: 100%;
      padding: 12px;
      background: #2a7a27;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: .9rem;
      font-weight: 700;
      cursor: pointer;
      transition: all .2s;
      margin-top: 4px;
      letter-spacing: .3px;
    }
    .btn-submit:hover {
      background: #1b5e20;
      transform: translateY(-1px);
      box-shadow: 0 4px 16px rgba(42,122,39,.35);
    }

    /* Forgot + Back links */
    .login-links {
      margin-top: 14px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      font-size: .78rem;
    }
    .login-links a { color: #1565c0; text-decoration: none; }
    .login-links a:hover { text-decoration: underline; }

    /* Error message */
    .login-error {
      width: 100%;
      background: #fdecea;
      border: 1px solid #f5c6c6;
      color: #c0392b;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: .82rem;
      margin-bottom: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Footer of panel */
    .login-panel-footer {
      margin-top: 30px;
      font-size: .72rem;
      color: #aaa;
      text-align: center;
    }

    /* Responsive: stack on mobile */
    @media(max-width: 768px) {
      body { justify-content: center; }
      .login-left { display: none; }
      .login-panel {
        width: 100%;
        min-height: 100vh;
        padding: 40px 24px;
        box-shadow: none;
      }
    }
  </style>
</head>
<body>

  {{-- Left: campus photo shows through --}}
  <div class="login-left">
    <div class="login-left-text">
      <i class="bi bi-geo-alt-fill"></i>
      18 Ung Văn Khiêm, P. Đông Xuyên, TP. Long Xuyên, An Giang
    </div>
  </div>

  {{-- Right: login card --}}
  <div class="login-panel">

    {{-- Logo --}}
    <img src="{{ asset('images/logo.png') }}" class="login-logo" alt="Logo AGU">

    {{-- Title --}}
    <div class="login-title-enews">E-NEWS</div>
    <div class="login-title-sub">Hệ thống tin tức sinh viên</div>
    <div class="login-title-uni">Trường Đại học An Giang</div>

    {{-- Error flash --}}
    @if(session('error'))
    <div class="login-error">
      <i class="bi bi-exclamation-circle-fill"></i>
      {{ session('error') }}
    </div>
    @endif

    {{-- Gmail AGU SSO --}}
    <div style="width:100%;">
      <div class="login-label">Đăng nhập qua:</div>
      <a href="{{ route('auth.google') }}" class="btn-gmail">
        {{-- Gmail colorful icon --}}
        <svg class="gmail-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6z" fill="white" opacity=".15"/>
          <path d="M4 8l8 5 8-5" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round"/>
          <rect x="2" y="4" width="20" height="16" rx="2" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1"/>
          {{-- Colorful M --}}
          <path d="M2 6l10 7 10-7" fill="none" stroke="white" stroke-width="1.5" opacity=".8"/>
        </svg>
        Gmail AGU
      </a>
    </div>

    {{-- Divider --}}
    <div class="login-divider">HOẶC tài khoản</div>

    {{-- Login form --}}
    <form action="{{ route('login') }}" method="POST" style="width:100%;">
      @csrf

      {{-- Hiển thị lỗi --}}
      @if($errors->any())
      <div class="login-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        {{ $errors->first() }}
      </div>
      @endif

      <input
        type="text"
        name="login"
        class="login-input"
        placeholder="Email AGU hoặc tên tài khoản"
        value="{{ old('login') }}"
        required
        autofocus
        autocomplete="username"
      >
      <div style="position:relative;">
        <input
          type="password"
          name="password"
          class="login-input"
          placeholder="Mật khẩu"
          id="passwordInput"
          required
          autocomplete="current-password"
          style="padding-right:42px;"
        >
        <button type="button"
                onclick="togglePwd()"
                style="position:absolute;right:12px;top:50%;transform:translateY(-60%);background:none;border:none;color:#aaa;cursor:pointer;font-size:1rem;padding:0;">
          <i class="bi bi-eye" id="pwdEye"></i>
        </button>
      </div>

      <button type="submit" class="btn-submit">
        <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
      </button>
    </form>

    {{-- Links --}}
    <div class="login-links">
      <a href="#">Quên mật khẩu?</a>
      <a href="{{ route('home') }}">
        <i class="bi bi-arrow-left"></i> Về trang chủ
      </a>
    </div>

    <div class="login-panel-footer">
      &copy; {{ date('Y') }} Trường Đại học An Giang — VNU-HCM
    </div>

  </div>

<script>
function togglePwd() {
  const inp = document.getElementById('passwordInput');
  const eye = document.getElementById('pwdEye');
  if (inp.type === 'password') {
    inp.type = 'text';
    eye.className = 'bi bi-eye-slash';
  } else {
    inp.type = 'password';
    eye.className = 'bi bi-eye';
  }
}
</script>
</body>
</html>
