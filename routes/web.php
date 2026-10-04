<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SetupController;
use App\Http\Controllers\PublicPortfolioController;
use App\Http\Middleware\TrackPortfolioAnalytics;
use App\Services\PortfolioAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware(TrackPortfolioAnalytics::class)->group(function (): void {
    Route::get('/', [PublicPortfolioController::class, 'home'])->name('home');
    Route::get('/projects', [PublicPortfolioController::class, 'projects'])->name('projects.index');
    Route::get('/projects/{slug}', [PublicPortfolioController::class, 'project'])->name('projects.show');
});
Route::post('/contact', [PublicPortfolioController::class, 'contact'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/analytics/events', function (Request $request, PortfolioAnalytics $analytics) {
    $data = $request->validate(['event' => ['required', 'string', 'in:'.implode(',', PortfolioAnalytics::EVENTS)], 'path' => ['required', 'string', 'max:512'], 'project_id' => ['nullable', 'integer'], 'target' => ['nullable', 'string', 'max:20'], 'duration' => ['nullable', 'integer', 'min:0', 'max:86400']]);

    return response()->json(['recorded' => $analytics->recordEvent($request, $data)]);
})->middleware('throttle:60,1')->name('analytics.events');
Route::post('/analytics/activity', function (Request $request) {
    $sessionKey = $request->cookie('analytics_session');
    if (is_string($sessionKey) && Str::isUuid($sessionKey) && $request->cookie('analytics_opt_out') !== '1') {
        $updated = DB::table('analytics_sessions')->where('session_key', $sessionKey)->where('last_activity_at', '>=', now()->subMinutes(30))->update(['last_activity_at' => now(), 'ended_at' => null, 'updated_at' => now()]);
        if ($updated) {
            return response()->noContent()->withCookie(cookie('analytics_session', $sessionKey, 30, '/', null, $request->isSecure(), true, false, 'Lax'));
        }
    }

    return response()->noContent();
})->middleware('throttle:30,1')->name('analytics.activity');
Route::post('/analytics/opt-out', fn () => response()->json(['opted_out' => true])->withCookie(cookie('analytics_opt_out', '1', 60 * 24 * 365 * 13, '/', null, request()->isSecure(), false, false, 'Lax')))->name('analytics.opt-out');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/setup', [SetupController::class, 'create'])->middleware('throttle:10,1')->name('setup');
    Route::post('/setup', [SetupController::class, 'store'])->middleware('throttle:5,1')->name('setup.store');
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::prefix('analytics')->name('analytics.')->controller(AnalyticsController::class)->group(function (): void {
            Route::get('/', 'overview')->name('overview');
            Route::get('/visitors', 'visitors')->name('visitors');
            Route::get('/visitors/{visitor}', 'visitor')->whereNumber('visitor')->name('visitor');
            Route::get('/sources', 'sources')->name('sources');
            Route::get('/pages', 'pages')->name('pages');
            Route::get('/page', 'page')->name('page');
            Route::get('/events', 'events')->name('events');
            Route::get('/geography', 'geography')->name('geography');
            Route::get('/realtime', 'realtime')->name('realtime');
            Route::get('/live', 'live')->name('live');
            Route::get('/export', 'export')->name('export');
        });
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
