#!/bin/bash
set -e

# Đợi DB khởi động ổn định ở cổng 3306 (nếu DB được cấu hình chạy chung trong docker-compose)
echo "Kiểm tra kết nối Database..."

# Tự động sao chép .env nếu hệ thống host (máy người dùng) chưa có
if [ ! -f ".env" ]; then
    echo "FILE .env KHÔNG TỒN TẠI! Đang tự động tạo từ .env.example..."
    cp .env.example .env

    # Cập nhật kết nối database với Docker (Container db) (Laravel 11 mặc định hay dùng sqlite hoặc đã comment các biến host mysql)
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
    sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=db/g' .env
    sed -i 's/# DB_PORT=3306/DB_PORT=3306/g' .env
    sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=enews/g' .env
    sed -i 's/# DB_USERNAME=root/DB_USERNAME=root/g' .env
    sed -i 's/# DB_PASSWORD=/DB_PASSWORD=root/g' .env
    
    # Kể cả nếu uncommented cũ
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/g' .env
    sed -i 's/DB_DATABASE=laravel/DB_DATABASE=enews/g' .env
    sed -i 's/^DB_USERNAME=root$/DB_USERNAME=root/g' .env
    sed -i 's/^DB_PASSWORD=$/DB_PASSWORD=root/g' .env
fi

# Cấp quyền ghi cần thiết cho Nginx/PHP-FPM vào thư mục ghi dữ liệu của Laravel
echo "Cấp quyền cho storage và bootstrap/cache..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Cài đặt PHP Packages thông qua Composer
echo "Đang nạp thư viện PHP (Composer Install)..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Khởi tạo App Key nếu chưa có (khi .env vừa tạo mới)
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "Đang cấp phát khóa bảo mật (App Key)..."
    php artisan key:generate
fi

# Cài đặt thư viện Nodejs (Vite/Tailwind) và tự động đóng gói file assets!
if [ ! -d "node_modules" ]; then
    echo "Đang nạp thư viện Node.js (NPM Install)..."
    npm install
fi
echo "Đang biên dịch Giao diện (Vite Build)..."
npm run build

# Chạy Migration để cấu trúc lại Cơ sở dữ liệu E-News
# Dùng --force để ép chạy trong mọi mode (kể cả app.env=production)
echo "Tiến hành ghép Cơ sở dữ liệu vào máy (Migration)..."
# Vòng lặp chờ database thật sự Up trước khi migrate (MySQL container thường mất 10-20s lần đầu khởi tạo)
for i in {1..30}; do
  if php artisan migrate --force; then
      php artisan db:seed --force || true
      echo "✅ Database đã sẵn sàng và được Migrate thành công!"
      break
  fi
  echo "⏳ Chờ CSDL MySQL khởi động vòng $i/30..."
  sleep 3
done

# Quét sạch cache rác cho nhẹ máy
php artisan optimize:clear

echo "================================================="
echo "   HỆ THỐNG ĐÃ SẴN SÀNG! ĐANG KHỞI ĐỘNG MÁY CHỦ  "
echo "================================================="

# Start PHP-FPM tiến trình chạy nền
exec php-fpm
