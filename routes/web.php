<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 投稿関連
// 一覧画面
Route::get('/post', [PostController::class, 'index'])->name('post.index');
// 個別表示画面
Route::get('post/show/{post}', [PostController::class, 'show'])->name('post.show');
// フォーム画面
Route::get('/post/create', [PostController::class, 'create']);
// 投稿
Route::post('/post', [PostController::class, 'store'])->name('post.store');
// 編集画面
Route::get('/post/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
// 編集
Route::patch('/post/{post}', [PostController::class, 'update'])->name('post.update');
// 削除
Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');

require __DIR__ . '/auth.php';
