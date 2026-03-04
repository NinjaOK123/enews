<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Frontend ────────────────────────────────────────────────
Route::get('/', function () {
    return view('frontend.home');
})->name('home');

Route::get('/tim-kiem', function () {
    return view('frontend.search');
})->name('search');

Route::get('/chuyen-muc/{slug}', function ($slug) {
    return view('frontend.category', compact('slug'));
})->name('category');

Route::get('/bai-viet/{slug}', function ($slug) {
    return view('frontend.article', compact('slug'));
})->name('article');

// ─── Auth: đăng nhập / đăng xuất ────────────────────────────
Route::get('/dang-nhap',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'loginWithPassword'])->name('login.post');
Route::get('/dang-xuat',  [AuthController::class, 'logout'])->name('logout');

// ─── Auth: Google OAuth2 ─────────────────────────────────────
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
