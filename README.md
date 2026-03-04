# E-News AGU

Website tin tuc sinh vien cho Truong Dai hoc An Giang (AGU), xay dung bang Laravel 12.

## Tong quan

- Framework: Laravel 12 (PHP 8.2+)
- Frontend: Blade + Bootstrap 5 + CSS tuy bien
- Build tool: Vite
- Co ho tro dang nhap Google OAuth (tai khoan AGU)

## Yeu cau moi truong

- PHP 8.2 tro len
- Composer
- Node.js + npm
- MySQL (hoac SQLite de test nhanh)

## Cai dat du an

1. Clone source:

```bash
git clone https://github.com/NinjaOK123/enews.git
cd enews
```

2. Cai dependency:

```bash
composer install
npm install
```

3. Tao file moi truong:

```bash
cp .env.example .env
php artisan key:generate
```

4. Chinh `.env` cho database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enews
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Chay migration:

```bash
php artisan migrate
```

## Cau hinh dang nhap Google AGU

Trong `.env`, cap nhat:

```env
APP_URL=http://enews.test

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://enews.test/auth/google/callback
GOOGLE_ALLOWED_DOMAINS=student.agu.edu.vn,agu.edu.vn
```

Luu y quan trong:

- Redirect URI trong Google Cloud Console phai trung khop 100% voi `GOOGLE_REDIRECT_URI`.
- Neu doi `.env`, nho clear cache:

```bash
php artisan optimize:clear
```

## Chay du an

Chay backend:

```bash
php artisan serve
```

Chay frontend dev:

```bash
npm run dev
```

Neu muon chay dong thoi cac service:

```bash
composer run dev
```

## Cac lenh hay dung

```bash
# Chay test
php artisan test

# Build frontend
npm run build

# Clear cache he thong
php artisan optimize:clear
```

## Cau truc thu muc chinh

- `app/Http/Controllers`: Controller xu ly request
- `resources/views`: Giao dien Blade
- `routes/web.php`: Route web
- `config/services.php`: Cau hinh dich vu ngoai (Google OAuth)
- `database/migrations`: Cac file migration DB

## Ghi chu

- File `.env` khong commit len git.
- Neu gap loi Google `400 malformed`, thuong do sai `client_id` hoac sai redirect URI.

