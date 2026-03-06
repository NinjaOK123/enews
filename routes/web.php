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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

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
         
         // Posts management
         Route::get('/posts/create', [App\Http\Controllers\Contributor\PostController::class, 'create'])->name('posts.create');
         Route::post('/posts', [App\Http\Controllers\Contributor\PostController::class, 'store'])->name('posts.store');
         Route::get('/posts/{post}/edit', [App\Http\Controllers\Contributor\PostController::class, 'edit'])->name('posts.edit');
         Route::post('/posts/{post}/update', [App\Http\Controllers\Contributor\PostController::class, 'update'])->name('posts.update');
         Route::post('/posts/{post}/submit', [App\Http\Controllers\Contributor\PostController::class, 'submit'])->name('posts.submit');
         Route::post('/posts/{post}/autosave', [App\Http\Controllers\Contributor\PostController::class, 'autosave'])->name('posts.autosave');
         Route::post('/posts/import-word', [App\Http\Controllers\Contributor\PostController::class, 'importWord'])->name('posts.import-word');
         
         // Media management
         Route::post('/media/upload', [App\Http\Controllers\Contributor\PostController::class, 'uploadMedia'])->name('media.upload');
         Route::get('/media/personal', [App\Http\Controllers\Contributor\MediaController::class, 'getPersonalMedia'])->name('media.personal');
         Route::get('/media/shared', [App\Http\Controllers\Contributor\MediaController::class, 'getSharedMedia'])->name('media.shared');
         Route::get('/media/{media}/view', [App\Http\Controllers\Contributor\MediaController::class, 'show'])->name('media.view');
     });

// AI endpoints
Route::middleware(['auth'])->group(function () {
    Route::post('/ai/generate-post', [App\Http\Controllers\AIController::class, 'generatePost'])->name('ai.generate-post');
});

// ─── Google OAuth ───────────────────────────────────────────────
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');


