<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artists/{artist:slug}', [ArtistController::class, 'show'])->name('artists.show');

Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
Route::get('/genres/{genre:slug}', [GenreController::class, 'show'])->name('genres.show');

Route::get('/songs/{song:slug}', [SongController::class, 'show'])->name('songs.show');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', [SessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->whereIn('provider', SocialiteController::SUPPORTED_PROVIDERS)
    ->name('auth.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->whereIn('provider', SocialiteController::SUPPORTED_PROVIDERS)
    ->name('auth.callback');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
});
