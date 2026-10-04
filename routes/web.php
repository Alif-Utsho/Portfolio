<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SetupController;
use App\Http\Controllers\PublicPortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPortfolioController::class, 'home'])->name('home');
Route::get('/projects', [PublicPortfolioController::class, 'projects'])->name('projects.index');
Route::get('/projects/{slug}', [PublicPortfolioController::class, 'project'])->name('projects.show');
Route::post('/contact', [PublicPortfolioController::class, 'contact'])->middleware('throttle:5,1')->name('contact.store');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/setup', [SetupController::class, 'create'])->middleware('throttle:10,1')->name('setup');
    Route::post('/setup', [SetupController::class, 'store'])->middleware('throttle:5,1')->name('setup.store');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/content/{type}', [ContentController::class, 'index'])->where('type', 'project|experience|skill|personal|social|navigation')->name('content.index');
        Route::get('/content/{type}/create', [ContentController::class, 'create'])->where('type', 'project|experience|skill|personal|social|navigation')->name('content.create');
        Route::post('/content/{type}', [ContentController::class, 'store'])->where('type', 'project|experience|skill|personal|social|navigation')->name('content.store');
        Route::get('/content/items/{item}/edit', [ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/items/{item}', [ContentController::class, 'update'])->name('content.update');
        Route::delete('/content/items/{item}', [ContentController::class, 'destroy'])->name('content.destroy');

        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::post('/media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::patch('/messages/{message}/status', [ContactMessageController::class, 'updateStatus'])->name('messages.status');
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
    });
});
