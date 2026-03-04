<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\ContributorController;
use App\Http\Controllers\PageController;

// ─── Frontend (Public) ──────────────────────────────────────────
Route::get('/',  [HomeController::class, 'index'])->name('home');
Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');
Route::get('/chuyen-muc/{slug}', [CategoryController::class, 'show'])->name('category');
Route::get('/bai-viet/{post:slug}', [PostController::class, 'show'])->name('post.show');

// ─── Trang tĩnh ─────────────────────────────────────────────────
Route::get('/gioi-thieu',         [PageController::class, 'gioiThieu'])->name('about');
Route::get('/quy-dinh',           [PageController::class, 'quyDinh'])->name('rules');
Route::get('/lien-he',            [PageController::class, 'lienHe'])->name('contact');
Route::get('/enews-doc-va-suy-ngam', [PageController::class, 'docVaSuyNgam'])->name('doc-suy-ngam');

// ─── Bình luận (yêu cầu đăng nhập) ────────────────────────────
Route::post('/bai-viet/{post:slug}/binh-luan', [PostController::class, 'storeComment'])
     ->name('post.comment')
     ->middleware('auth');

// ─── Auth: Đăng nhập / Đăng xuất ───────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Role-based Dashboards ──────────────────────────────────────

// Admin
Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'role:admin'])
     ->group(function () {
         Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
     });

// Editor
Route::prefix('editor')
     ->name('editor.')
     ->middleware(['auth', 'role:editor'])
     ->group(function () {
         Route::get('/dashboard', [EditorController::class, 'dashboard'])->name('dashboard');
     });

// Contributor
Route::prefix('contributor')
     ->name('contributor.')
     ->middleware(['auth', 'role:contributor'])
     ->group(function () {
         Route::get('/dashboard', [ContributorController::class, 'dashboard'])->name('dashboard');
     });

// ─── Google OAuth ───────────────────────────────────────────────
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');


