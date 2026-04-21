# 📰 Hệ Thống Báo Điện Tử E-News (TALL Stack)

Chào mừng bạn đến với dự án **E-News** – Hệ thống Toà soạn Báo điện tử trực tuyến dành cho sinh viên, được phát triển trên kiến trúc nền tảng **TALL Stack** cực kỳ tối ưu và hiện đại:
- **T**: Tailwind CSS v4 (Thiết kế giao diện siêu việt, mượt mà)
- **A**: Alpine.js (Tương tác UI Javascript tối giản)
- **L**: Laravel 11/12 (Framework Backend PHP mạnh mẽ nhất)
- **L**: Livewire 3 (Render giao diện Server-side Real-time không cần Reload)
- Đi kèm với **Vite** siêu tốc và tích hợp tính năng **Trợ Lý AI Tự Viết Bài (Google Gemini / Groq / DeepSeek)**

---

## 🛠️ Hướng dẫn Cài đặt & Vận hành trên Laragon / XAMPP
1. Mở Terminal (CMD, Powershell) tải mã nguồn:
   ```bash
   git clone https://github.com/NinjaOK123/enews.git
   cd enews
   ```
2. Sửa file `.env.example` thành `.env`, khai báo thông tin Database `DB_DATABASE=enews`, `DB_USERNAME=root`, `DB_PASSWORD=`.
3. Cài các gói thư viện backend: `composer install`
4. Cài giao diện frontend: `npm install && npm run build`
5. Sinh mã bảo mật: `php artisan key:generate`
6. Khởi tạo Cơ Sở Dữ Liệu và Dữ liệu mẫu ban đầu: `php artisan migrate --seed`
7. Bật Web Server (nếu không chạy sẵn qua tên miền ảo của Laragon): `php artisan serve`

🎉 **Chúc bạn có những trải nghiệm thật mượt mà trên hệ thống E-News!**

🎉 **Chúc bạn có những trải nghiệm thật mượt mà trên hệ thống E-News!**
