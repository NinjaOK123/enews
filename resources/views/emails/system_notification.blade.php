<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $notification->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #10b981;
            padding: 24px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
            font-size: 15px;
            color: #4b5563;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #f3f4f6;
        }
        .btn {
            display: inline-block;
            background-color: #10b981;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thư thông báo từ Hệ thống E-News</h1>
        </div>
        
        <div class="content">
            <h2 style="color: #111827; margin-top: 0; font-size: 20px;">{{ $notification->title }}</h2>
            
            <div style="margin-top: 20px;">
                {!! $notification->content !!}
            </div>
            
            <p>Trân trọng,<br><strong>Ban quản trị AGU E-News</strong></p>
        </div>
        
        <div class="footer">
            <p>Đây là email tự động từ hệ thống. Xin vui lòng không trả lời thư này.</p>
            <p>&copy; {{ date('Y') }} AGU E-News. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
