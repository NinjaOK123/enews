# E-News - Dự án Trình duyệt Tin Tức Sinh Viên 🚀

Đây là phiên bản được cấu trúc bằng kiến trúc TALL Stack siêu mượt (Tailwind CSS 4, Alpine.js, Laravel 11/12, Livewire).

## 🐳 Hướng dẫn Chạy Nhanh Dự Án (Bằng Docker)

Dự án đã được tích hợp bộ công cụ tự động biên dịch, vì vậy bất kỳ nhân sự nào trong team cũng có thể tải về và chạy với 2 dòng lệnh duy nhất (Mà **KHÔNG** cần phải cài đặt PHP, Composer, hay NPM vào máy tính).

### Yêu cầu duy nhất: 
Máy tính phải cài sẵn phần mềm **[Docker Desktop](https://www.docker.com/products/docker-desktop/)** (Mở ứng dụng lên để chắc chắn nó đang chạy nền).

### Thực hiện:
1. Mở Terminal (Powershell / CMD / Bash) tại thư mục chứa source code vừa giải nén.
2. Gõ thần chú:
```bash
docker-compose up -d --build
```
3. Uống một tách trà, hệ thống sẽ tự động khởi tạo trong khoảng 1-2 phút (Và chỉ mất khoảng 5 giây cho các lần khởi động ngày hôm sau). Cấu trúc máy ảo Mạng Nhện sẽ tự động sao chép mã khoá Laravel, tải Plugin Composer, cày thư viện Node.js, rặn Build giao diện Vite, tự nặn ra cơ sở dữ liệu `enews` và bơm dữ liệu đệm vào DB.
4. Xong! Cứ thế dạo bước vào: 👉 [http://localhost:8000](http://localhost:8000)

*(Tips: Lần đầu tiên chạy có thể máy tính hay Docker chặn Tường lửa Firewalls, bạn cứ Allow là được)*

---

## 🛠️ Trợ Giúp Vận Hành (Dành cho Devs không dùng Docker)

Nếu bạn thiết lập môi trường bằng Laragon hay XAMPP, vui lòng làm theo các bước truyền thống:
1. Copy `.env.example` sang `.env`, chỉnh Database trùng với DB bạn đã thiết kế ở localhost.
2. Cài Composer Pkgs: `composer install`
3. Generate Key: `php artisan key:generate`
4. Sinh DB: `php artisan migrate --seed`
5. Khởi chạy Vite (Chờ chừng 2s rồi mở 1 terminal mới): `npm install && npm run build`
6. Nếu chạy serve ảo thì: `php artisan serve` (Chạy ở localhost:8000)
