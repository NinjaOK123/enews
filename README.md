# 📰 Hệ Thống Báo Điện Tử E-News (TALL Stack)

Chào mừng bạn đến với dự án **E-News** – Hệ thống Toà soạn Báo điện tử trực tuyến dành cho sinh viên, được phát triển trên kiến trúc nền tảng **TALL Stack** cực kỳ tối ưu và hiện đại:
- **T**: Tailwind CSS v4 (Thiết kế giao diện siêu việt, mượt mà)
- **A**: Alpine.js (Tương tác UI Javascript tối giản)
- **L**: Laravel 11/12 (Framework Backend PHP mạnh mẽ nhất)
- **L**: Livewire 3 (Render giao diện Server-side Real-time không cần Reload)
- Đi kèm với **Vite** siêu tốc và tích hợp tính năng **Trợ Lý AI Tự Viết Bài (Google Gemini / Groq / DeepSeek)**

---

## 🐳 Hướng dẫn Cài đặt & Vận hành Môi trường Tự Động bằng Docker (Từ A - Z)

Để thống nhất môi trường cho tất cả các lập trình viên trên Windows, MacOS và Linux, dự án này đã đóng gói toàn bộ Môi trường Server vào **Docker**.
Bạn **KHÔNG CẦN CÀI ĐẶT** PHP, Node.js, NPM, Composer hay MySQL trên máy tính gốc của bạn. Hệ thống Docker sẽ tự động lo liệu 100%.

### Bước 1: Yêu cầu chuẩn bị (Prerequisites)
1. **Docker Desktop**: Máy tính của bạn bắt buộc phải có phần mềm này.
   - 🔗 Tải về tại: [Download Docker Desktop](https://www.docker.com/products/docker-desktop/)
   - Dùng Windows thì cần kiểm tra xem đã bật [WSL 2](https://learn.microsoft.com/en-us/windows/wsl/install) chưa (Docker sẽ hướng dẫn lúc cài).
   - *Lưu ý: Mở ứng dụng Docker Desktop lên và đảm bảo icon có màu xanh (Engine is Running).*
2. **Git**: Để sao chép (Clone) bộ mã nguồn.

### Bước 2: Bắt đầu tải Mã nguồn
Mở **Terminal** (CMD, Powershell, hoặc Git Bash) và chạy lệnh:
```bash
git clone https://github.com/NinjaOK123/enews.git
cd enews
```

### Bước 3: Nổ máy Khởi động (Chỉ 1 lệnh thần thánh)
Tại đúng thư mục `enews` vừa truy cập, bạn gõ lệnh sau:
```bash
docker-compose up -d --build
```

**🍵 Chuyện gì đang xảy ra sau khi gõ lệnh? Hãy đi uống trà!**
Trong lần đầu tiên (có thể kéo dài 2-5 phút), Docker sẽ tiến hành:
- Tải hệ điều hành ảo chứa máy chủ Web `Nginx`, máy chủ ứng dụng PHP `php-fpm 8.3`, MySQL Database `8.0`.
- Tự động lấy file cấu hình `.env.example` tạo thành `.env`.
- Tự động nạp kết nối vào Database `db` (bỏ qua `localhost`).
- Tự động chạy `composer install` để cài mọi thư viện Backend Laravel.
- Tự động chạy `npm install` && `npm run build` để dịch file TailwindCSS cho Giao diện.
- Tự động chạy `php artisan migrate --seed` để khởi tạo cấu trúc Cổng thông tin và bơm Dữ liệu mẫu ban đầu vào.
*(Các lần chạy sau này, hệ thống sẽ mở lên lập tức trong khoảng 5 giây).*

> **Mẹo nhỏ:** Nếu máy tính (Windows Defender) hoặc Docker Desktop hỏi tường lửa (Firewalls Prompt), bạn cứ bấm "Allow" nhé.

### Bước 4: Tận hưởng kết quả
Hệ thống đã sẵn sàng. Bạn mở trình duyệt lên và truy cập:
👉 **[http://localhost:8000](http://localhost:8000)**

*(Tài khoản quản trị viên Admin để test hệ thống đang được cấu hình mặc định là `admin@example.com` - Mật khẩu `password` - Xin xác nhận lại Data Seeder để biết chính xác).*

---

## 🎮 Các thao tác Quản Lý Nâng Cao (Cheatsheet)

Dưới đây là một số lệnh quan trọng dùng để quản lý hệ thống khi bạn muốn can thiệp sâu:

**1. Tắt hệ thống hoàn toàn (Stop)**
Nếu không code nữa và muốn giải phóng RAM:
```bash
docker-compose down
```

**2. Gõ các lệnh của Laravel (Artisan) và Composer**
Vì bạn không cài PHP trên máy, mọi lệnh `php artisan ...` phải được gọi xuyên qua Docker vào Container (tên là `enews_app`):
```bash
# Lệnh tổng quát
docker exec -it enews_app php artisan [lệnh]

# Ví dụ: Xóa Cache của hệ thống
docker exec -it enews_app php artisan optimize:clear

# Ví dụ: Sinh ra một Model mới
docker exec -it enews_app php artisan make:model Category -m
```

**3. Xem lỗi máy chủ (Log Error)**
Nếu Website bỗng nhiên sập hoặc bị trắng trang, hãy kiểm tra Log:
```bash
docker-compose logs -f app
```

**4. Khởi động lại khi code PHP bị thay đổi lớn**
```bash
docker-compose restart app
```

*(Lưu ý: Bạn sửa code HTML, Blade, CSS thì trang web ngoài localhost sẽ nhận ngay lập tức, không cần khởi động lại do chúng ta đã mount Volume thẳng từ ổ cứng vào máy ảo).*

---

## 🚑 Xử Lý Sự Cố (Troubleshooting)

| Vấn Đề | Nguyên Nhân & Cách Khắc Phục |
| :--- | :--- |
| **Báo lỗi cổng `8000` đang bận (Port is already allocated)** | Bạn đang có một phần mềm khác (như xampp, laragon, phần mềm diệt virus) chiếm cổng 8000. Hãy mở file `docker-compose.yml`, tìm chỗ `"8000:80"` đổi thành `"8080:80"`. Sau đó chạy lại. Web chạy ở `localhost:8080`. |
| **Web bị vỡ giao diện (Mất CSS)** | Container chưa chạy xong `npm run build`. Bạn có thể tự kích hoạt tay bằng lệnh: `docker exec -it enews_app npm run build`. |
| **Không thể tải file `.env` lên DB** | Kiểm tra file `.env` chắc chắn rằng `DB_CONNECTION=mysql` và `DB_HOST=db`. Lệnh khởi động (Entrypoint) đã cố sức sửa lỗi này tự động cho bạn. |
| **Lỗi Permisson (Quyền truy cập thư mục) trên Linux** | Gõ trên terminal host: `sudo chmod -R 775 storage bootstrap/cache` |

---

## 🛠️ Trợ Giúp Vận Hành Truyền Thống (KHÔNG dùng Docker)
Dành cho bạn nào thích xài hàng nguyên gốc với Laragon / XAMPP:
1. Sửa `.env.example` -> `.env`, khai báo localhost thông thường.
2. Cài Composer: `composer install`
3. Cài NPM: `npm install && npm run build`
4. Cài Đặt Khóa bảo mật: `php artisan key:generate`
5. Tạo Cơ Sở Dữ Liệu: `php artisan migrate --seed`
6. Bật Server: `php artisan serve`

🎉 **Chúc bạn có những trải nghiệm thật mượt mà trên hệ thống E-News!**
