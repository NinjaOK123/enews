<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập — E-News AGU</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; }

    /* ── LEFT PANEL ──────────────────────────────────────── */
    .login-left {
      width: 58%;
      background:
        linear-gradient(135deg, rgba(15, 50, 15, .88) 0%, rgba(42, 122, 39, .70) 60%, rgba(255, 102, 0, .30) 100%),
        url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&q=85') center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      padding: 60px 56px;
      position: relative;
      overflow: hidden;
    }
    .login-left::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 30% 70%, rgba(255,102,0,.15) 0%, transparent 60%);
      pointer-events: none;
    }
    .left-logo {
      width: 72px; height: 72px;
      border-radius: 50%;
      border: 3px solid rgba(245, 212, 0, .7);
      object-fit: cover;
      margin-bottom: 22px;
      box-shadow: 0 4px 20px rgba(0,0,0,.3);
    }
    .left-uni { font-size: 1.05rem; font-weight: 800; color: #fff; letter-spacing: .5px; margin-bottom: 3px; }
    .left-portal { font-size: 1.45rem; font-weight: 900; color: #f5d400; letter-spacing: 1px; margin-bottom: 6px; }
    .left-vnu { font-size: .78rem; color: rgba(255,255,255,.65); margin-bottom: 28px; }
    .left-divider { width: 52px; height: 3px; background: linear-gradient(to right, #f5d400, #FF6600); border-radius: 2px; margin-bottom: 28px; }
    .left-quote {
      font-size: 1.12rem;
      font-style: italic;
      color: rgba(255,255,255,.9);
      margin-bottom: 36px;
      line-height: 1.7;
      border-left: 3px solid #f5d400;
      padding-left: 16px;
    }
    .left-features { display: flex; flex-direction: column; gap: 14px; }
    .left-feature {
      display: flex;
      align-items: center;
      gap: 13px;
      color: rgba(255,255,255,.88);
      font-size: .88rem;
    }
    .feat-icon {
      width: 36px; height: 36px;
      border-radius: 8px;
      background: rgba(255,255,255,.12);
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem;
      flex-shrink: 0;
      backdrop-filter: blur(4px);
    }
    .left-bottom {
      position: absolute;
      bottom: 24px; left: 56px;
      font-size: .72rem;
      color: rgba(255,255,255,.45);
    }

    /* ── RIGHT PANEL ─────────────────────────────────────── */
    .login-right {
      flex: 1;
      background: #fafcfa;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 48px 40px;
      position: relative;
    }
    .login-box {
      width: 100%;
      max-width: 420px;
    }
    .login-logo-sm {
      width: 52px; height: 52px;
      border-radius: 50%;
      object-fit: cover;
      margin: 0 auto 16px;
      display: block;
      border: 2px solid #d4edd2;
    }
    .login-heading {
      font-size: 1.55rem;
      font-weight: 900;
      color: #111;
      text-align: center;
      margin-bottom: 4px;
    }
    .login-sub {
      font-size: .82rem;
      color: #2a7a27;
      text-align: center;
      font-weight: 600;
      margin-bottom: 20px;
    }
    .login-divider {
      height: 2px;
      background: linear-gradient(to right, transparent, #2a7a27, transparent);
      border-radius: 2px;
      margin-bottom: 24px;
    }

    /* Form elements */
    .form-label { font-size: .78rem; font-weight: 700; color: #444; margin-bottom: 6px; }
    .input-group-text {
      background: #f3fbf2;
      border-color: #d0e8cf;
      color: #2a7a27;
    }
    .form-control {
      border-color: #d0e8cf;
      font-size: .88rem;
      padding: 10px 14px;
      transition: border-color .18s, box-shadow .18s;
    }
    .form-control:focus {
      border-color: #2a7a27;
      box-shadow: 0 0 0 3px rgba(42, 122, 39, .12);
    }
    .btn-login-submit {
      background: linear-gradient(135deg, #2a7a27 0%, #388e3c 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px;
      font-size: .92rem;
      font-weight: 800;
      width: 100%;
      letter-spacing: .5px;
      cursor: pointer;
      transition: opacity .18s, transform .12s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .btn-login-submit:hover { opacity: .92; transform: translateY(-1px); }
    .btn-login-submit:active { transform: translateY(0); }

    .info-notice {
      background: #f3fbf2;
      border: 1px solid #c8e6c9;
      border-left: 4px solid #2a7a27;
      border-radius: 0 8px 8px 0;
      padding: 12px 14px;
      font-size: .78rem;
      color: #444;
      display: flex;
      align-items: flex-start;
      gap: 9px;
      margin-top: 20px;
    }
    .info-notice i { color: #2a7a27; font-size: 1rem; margin-top: 1px; flex-shrink: 0; }

    .alert-danger-custom {
      background: #fff5f5;
      border: 1px solid #fecaca;
      border-left: 4px solid #ef4444;
      border-radius: 0 8px 8px 0;
      padding: 10px 14px;
      font-size: .82rem;
      color: #dc2626;
      display: flex;
      align-items: flex-start;
      gap: 8px;
      margin-bottom: 16px;
    }

    .right-footer {
      position: absolute;
      bottom: 16px;
      font-size: .70rem;
      color: #ccc;
      text-align: center;
    }

    @media (max-width: 768px) {
      .login-left { display: none; }
      .login-right { padding: 32px 20px; background: #fff; }
    }
  </style>
</head>
<body>

  {{-- LEFT PANEL: University Branding --}}
  <div class="login-left">
    <img src="{{ asset('images/logo.png') }}" alt="AGU Logo" class="left-logo"
         onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/AGU_logo.png/200px-AGU_logo.png'">
    <div class="left-uni">TRƯỜNG ĐẠI HỌC AN GIANG</div>
    <div class="left-portal">E-NEWS PORTAL</div>
    <div class="left-vnu">Vietnam National University — Ho Chi Minh City</div>
    <div class="left-divider"></div>
    <p class="left-quote">
      "Tri thức — Sáng tạo — Phát triển"<br>
      <span style="font-size:.8rem; font-style:normal; color:rgba(255,255,255,.6);">
        Nền tảng truyền thông số của cộng đồng AGU
      </span>
    </p>
    <div class="left-features">
      <div class="left-feature">
        <div class="feat-icon"><i class="bi bi-newspaper"></i></div>
        <span>Cập nhật tin tức mới nhất từ AGU</span>
      </div>
      <div class="left-feature">
        <div class="feat-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <span>Dành riêng cho cộng đồng Đại học An Giang</span>
      </div>
      <div class="left-feature">
        <div class="feat-icon"><i class="bi bi-pencil-square"></i></div>
        <span>Nền tảng viết lách và sáng tạo nội dung</span>
      </div>
      <div class="left-feature">
        <div class="feat-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <span>Hệ thống bảo mật và phân quyền rõ ràng</span>
      </div>
    </div>
    <div class="left-bottom">© 2026 Đại học An Giang — VNU-HCM. Bảo lưu mọi quyền.</div>
  </div>

  {{-- RIGHT PANEL: Login Form --}}
  <div class="login-right">
    <div class="login-box">

      {{-- Logo & Heading --}}
      <img src="{{ asset('images/logo.png') }}" alt="AGU Logo" class="login-logo-sm"
           onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/AGU_logo.png/200px-AGU_logo.png'">
      <h1 class="login-heading">Đăng nhập hệ thống</h1>
      <p class="login-sub"><i class="bi bi-broadcast-pin me-1"></i> E-News — Trang tin điện tử AGU</p>
      <div class="login-divider"></div>

      {{-- Flash messages --}}
      @if(session('error'))
      <div class="alert-danger-custom">
        <i class="bi bi-exclamation-triangle-fill"></i>
        {{ session('error') }}
      </div>
      @endif

      {{-- Validation errors --}}
      @if($errors->any())
      <div class="alert-danger-custom">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>
          @foreach($errors->all() as $err)
          <div>{{ $err }}</div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Login Form --}}
      <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        {{-- Email / Username --}}
        <div class="mb-3">
          <label for="login" class="form-label">Email hoặc Tên đăng nhập</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
            <input type="text"
                   id="login"
                   name="login"
                   class="form-control @error('login') is-invalid @enderror"
                   placeholder="email@agu.edu.vn hoặc username"
                   value="{{ old('login') }}"
                   autocomplete="username"
                   required
                   autofocus>
          </div>
          @error('login')
          <div style="font-size:.74rem; color:#dc2626; margin-top:4px;">
            <i class="bi bi-exclamation-circle"></i> {{ $message }}
          </div>
          @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
          <label for="password" class="form-label">Mật khẩu</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Nhập mật khẩu"
                   autocomplete="current-password"
                   required>
            <button type="button" class="input-group-text" style="cursor:pointer;" onclick="togglePwd()">
              <i class="bi bi-eye-fill" id="eyeIcon"></i>
            </button>
          </div>
          @error('password')
          <div style="font-size:.74rem; color:#dc2626; margin-top:4px;">
            <i class="bi bi-exclamation-circle"></i> {{ $message }}
          </div>
          @enderror
        </div>

        {{-- Remember me --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember"
                   style="border-color:#2a7a27; cursor:pointer;"
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember"
                   style="font-size:.80rem; color:#555; cursor:pointer;">
              Ghi nhớ đăng nhập
            </label>
          </div>
          <a href="#" style="font-size:.78rem; color:#aaa; text-decoration:none;">Quên mật khẩu?</a>
        </div>

        {{-- Submit button --}}
        <button type="submit" class="btn-login-submit">
          <i class="bi bi-box-arrow-in-right"></i>
          ĐĂNG NHẬP
        </button>

      </form>

      {{-- Info notice --}}
      <div class="info-notice">
        <i class="bi bi-info-circle-fill"></i>
        <span>
          Hệ thống <strong>không có chức năng tự đăng ký</strong>.
          Nếu bạn cần tài khoản, vui lòng liên hệ
          <strong>Quản trị viên</strong> để được cấp quyền truy cập.
        </span>
      </div>

    </div>{{-- /login-box --}}

    <div class="right-footer">
      © 2026 Trường Đại học An Giang — VNU-HCM
    </div>
  </div>

  <script>
  function togglePwd() {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
      inp.type = 'text';
      icon.className = 'bi bi-eye-slash-fill';
    } else {
      inp.type = 'password';
      icon.className = 'bi bi-eye-fill';
    }
  }
  </script>
</body>
</html>
