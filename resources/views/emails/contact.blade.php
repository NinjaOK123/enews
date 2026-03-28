<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liên hệ từ Ban Đọc E-News</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #2a7a27; color: #fff; padding: 15px 20px; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; background: #fafafa; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; text-align: center; }
        h2 { margin-top: 0; }
        .field { margin-bottom: 12px; }
        .label { font-weight: bold; color: #555; }
        .message-box { background: #fff; padding: 15px; border-left: 4px solid #f5d400; border-radius: 4px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Bạn có thư mới từ Độc giả E-News! 📬</h2>
        </div>
        
        <div class="content">
            <p>Hệ thống vừa ghi nhận một yêu cầu liên hệ từ người xem e-News:</p>

            <div class="field">
                <span class="label">Người gửi:</span> {{ $data['name'] }}
            </div>
            
            <div class="field">
                <span class="label">Email liên hệ:</span> <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a>
            </div>
            
            <div class="field">
                <span class="label">Tiêu đề:</span> 
                @if(!empty($data['subject']))
                    <strong>{{ $data['subject'] }}</strong>
                @else
                    <i>(Không có tiêu đề)</i>
                @endif
            </div>

            <p style="margin-top: 25px;"><span class="label">Nội dung:</span></p>
            <div class="message-box">
                {!! nl2br(e($data['message'])) !!}
            </div>
        </div>

        <div class="footer">
            <p>Thư này được gửi tự động từ Form Liên Hệ trang e-News AGU. Vui lòng bấm "Trả lời" (Reply) để phản hồi lại email người gửi ({{ $data['email'] }}).</p>
        </div>
    </div>
</body>
</html>
