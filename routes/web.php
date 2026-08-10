<?php

use App\Http\Controllers\Admin\ArtistController as AdminArtistController;
use App\Http\Controllers\Admin\ChartController as AdminChartController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenreController as AdminGenreController;
use App\Http\Controllers\Admin\SongController as AdminSongController;
use App\Http\Controllers\Admin\VoteController as AdminVoteController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\TopTenPlayerController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::redirect('/charts', '/charts/daily');
Route::get('/charts/daily', [ChartController::class, 'daily'])->name('charts.daily');
Route::get('/charts/{date}', [ChartController::class, 'daily'])
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('charts.date');

Route::get('/play', [TopTenPlayerController::class, 'show'])->name('play');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artists/{artist:slug}', [ArtistController::class, 'show'])->name('artists.show');

Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
Route::get('/genres/{genre:slug}', [GenreController::class, 'show'])->name('genres.show');

Route::get('/songs/{song:slug}', [SongController::class, 'show'])->name('songs.show');

Route::post('/songs/{song:slug}/votes', [VoteController::class, 'store'])
    ->middleware(['auth', 'throttle:votes'])
    ->name('votes.store');

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

    Route::resource('artists', AdminArtistController::class)
        ->except(['show', 'destroy'])
        ->parameters(['artists' => 'artist']);

    Route::resource('songs', AdminSongController::class)
        ->except(['show', 'destroy'])
        ->parameters(['songs' => 'song']);

    Route::resource('genres', AdminGenreController::class)
        ->except(['show'])
        ->parameters(['genres' => 'genre']);

    Route::get('votes', [AdminVoteController::class, 'index'])->name('votes.index');

    Route::get('charts', [AdminChartController::class, 'index'])->name('charts.index');
    Route::post('charts/regenerate', [AdminChartController::class, 'regenerate'])->name('charts.regenerate');
});
