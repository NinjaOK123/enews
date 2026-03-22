<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đặt lại mật khẩu - eNews AGU</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; background: #f4f4f4; color: #333; }
    .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .header { background: #2a7a27; padding: 28px 32px; text-align: center; }
    .header h1 { color: #fff; font-size: 22px; font-weight: bold; letter-spacing: .5px; }
    .header p { color: rgba(255,255,255,.85); font-size: 13px; margin-top: 4px; }
    .body { padding: 32px 36px; }
    .greeting { font-size: 16px; margin-bottom: 16px; color: #222; }
    .message { font-size: 14px; line-height: 1.7; color: #555; margin-bottom: 24px; }
    .btn-wrap { text-align: center; margin: 28px 0; }
    .btn { display: inline-block; background: #2a7a27; color: #fff !important;
           text-decoration: none; padding: 14px 36px; border-radius: 6px;
           font-size: 15px; font-weight: bold; letter-spacing: .3px; }
    .btn:hover { background: #1e5c1c; }
    .url-box { background: #f8f8f8; border: 1px solid #e0e0e0; border-radius: 4px;
               padding: 12px 16px; font-size: 12px; color: #666;
               word-break: break-all; margin-bottom: 20px; }
    .note { font-size: 13px; color: #888; line-height: 1.6; }
    .note strong { color: #e55; }
    .divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }
    .footer { background: #f9f9f9; border-top: 1px solid #eee; padding: 20px 36px;
              font-size: 12px; color: #aaa; text-align: center; line-height: 1.8; }
    .footer a { color: #2a7a27; text-decoration: none; }
  </style>
</head>
<body>
  <div class="wrapper">

    {{-- Header --}}
    <div class="header">
      <h1>📰 eNews AGU</h1>
      <p>Trang báo Sinh viên Đại học An Giang</p>
    </div>

    {{-- Body --}}
    <div class="body">
      <p class="greeting">Chào <strong>{{ $user->name }}</strong>,</p>

      <p class="message">
        Một yêu cầu đặt lại mật khẩu được tiến hành cho tài khoản của bạn
        <strong>{{ $user->email }}</strong> tại <strong>eNews AGU</strong>.
      </p>

      <p class="message">
        Để xác nhận yêu cầu này và đặt mật khẩu mới cho tài khoản của mình,
        hãy nhấn vào nút bên dưới:
      </p>

      <div class="btn-wrap">
        <a href="{{ $resetUrl }}" class="btn">🔑 Đặt lại mật khẩu</a>
      </div>

      <p class="note">
        Hoặc copy và dán đường dẫn này vào trình duyệt:
      </p>
      <div class="url-box">{{ $resetUrl }}</div>

      <p class="note">
        <strong>⚠️ Lưu ý:</strong> Liên kết này <strong>hợp lệ trong 30 phút</strong>
        kể từ lúc yêu cầu được tiến hành. Sau thời gian đó, bạn cần tạo yêu cầu đặt lại mật khẩu mới.
      </p>

      <hr class="divider">

      <p class="note">
        Nếu yêu cầu đặt lại mật khẩu này <strong>không phải của bạn</strong>,
        không cần làm gì cả. Tài khoản của bạn vẫn an toàn.
      </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
      <p>Ban Biên tập eNews AGU · Đại học An Giang</p>
      <p>18 Ung Văn Khiêm, P. Mỹ Long, TP. Long Xuyên, An Giang</p>
      <p><a href="{{ url('/') }}">enews.agu.edu.vn</a></p>
    </div>

  </div>
</body>
</html>
