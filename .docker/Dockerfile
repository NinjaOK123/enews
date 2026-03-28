FROM php:8.3-fpm

# Cài đặt các thư viện hệ thống cần thiết cho Laravel và Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Cài đặt các extension PHP cần thiết
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Cài đặt Node.js mới nhất (NPM đi kèm apt thường khá cũ, script này kéo bản 20.x mới nhất)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thư mục làm việc mặc định trong container
WORKDIR /var/www

# Copy toàn bộ mã nguồn vào trong container (trừ những file liệt kê trong .dockerignore)
COPY . /var/www

# Gán quyền thư mục cho user www-data (nginx/php-fpm mặc định dùng user này)
RUN chown -R www-data:www-data /var/www

# Copy kịch bản nổ máy (entrypoint) vào và cấp quyền chạy
COPY .docker/entrypoint.sh /usr/local/bin/
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Cổng mặc định của PHP-FPM
EXPOSE 9000

# Chạy script tự động cài đặt thay vì chạy web ngay lập tức
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
