<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route yang hanya bisa diakses setelah login
Route::prefix('post')->name('post.')->middleware('auth')->group(function () {

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{project}/gallery', [ProjectController::class, 'storeGallery'])->name('projects.gallery.store');
    Route::get('/skills/{skill}/edit', [SkillController::class, 'edit'])->name('skills.edit');
    Route::put('/skills/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::put('/gallery/{gallery}/caption', [GalleryController::class, 'updateCaption'])->name('gallery.caption.update');
    Route::delete('/projects/{project}/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    Route::get('/profile-saya', function () {
        return view('post.profile-saya');
    })->name('profile-saya');

    Route::get('/admin-test', function () {
        return 'Selamat datang, Admin!';
    })->middleware('admin')->name('admin-test');
});

Route::get('/projects', [PortfolioController::class, 'projects'])->name('projects');
Route::get('/projects/{project}', [PortfolioController::class, 'show'])->name('projects.show');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
