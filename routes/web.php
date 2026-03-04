<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PostController;

// ─── Frontend ────────────────────────────────────────────────
Route::get('/', function () {
    return view('frontend.home');
})->name('home');

Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');

Route::get('/chuyen-muc/{slug}', [CategoryController::class, 'show'])->name('category');


// Trang chi tiết bài viết — dùng Route Model Binding theo slug
Route::get('/bai-viet/{post:slug}', [PostController::class, 'show'])->name('post.show');

// Gửi bình luận (yêu cầu đăng nhập)
Route::post('/bai-viet/{post:slug}/binh-luan', [PostController::class, 'storeComment'])
     ->name('post.comment')
     ->middleware('auth');

// ─── Auth: đăng nhập / đăng xuất ────────────────────────────
use App\Http\Controllers\Auth\LoginController;

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Role-based Dashboards ──────────────────────────────────

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard - Thống kê, Quản lý bài viết, User, v.v.';
    })->name('dashboard');
});

Route::middleware(['auth', 'role:editor'])->prefix('editor')->name('editor.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Editor Dashboard - Duyệt bài, chỉnh sửa, v.v.';
    })->name('dashboard');
});

Route::middleware(['auth', 'role:contributor'])->prefix('contributor')->name('contributor.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Contributor Dashboard - Viết bài, gửi duyệt, v.v.';
    })->name('dashboard');
});

// ─── Auth: Google OAuth2 ─────────────────────────────────────
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
