<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route yang hanya bisa diakses setelah login
Route::prefix('post')->name('post.')->middleware('auth')->group(function () {

    Route::get('/projects', function () {
        return 'Selamat datang di halaman Project!';
    })->name('projects.index');

    Route::get('/profile-saya', function () {
        return view('post.profile-saya');
    })->name('profile-saya');

    Route::get('/admin-test', function () {
        return 'Selamat datang, Admin!';
    })->middleware('admin')->name('admin-test');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
