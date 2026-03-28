@echo off
echo ==============================================================
echo [E-NEWS AGU] CACHING & OPTIMIZATION FOR PRODUCTION
echo ==============================================================
echo.

echo 1. Xoa toan bo Cache cu...
call php artisan optimize:clear

echo.
echo 2. Dong goi Caching config, routes, views...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache
call php artisan event:cache

echo.
echo ==============================================================
echo XONG! Du an E-News cua ban dang chay o trang thai MAX SPEED!
echo P/S: Khi dang viet Code (Dev), huy cache bang [php artisan optimize:clear].
echo ==============================================================
pause
