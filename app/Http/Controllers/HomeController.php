<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $recentSongs = Song::query()
            ->where('is_active', true)
            ->whereHas('artist', fn ($artists) => $artists->where('is_active', true))
            ->with('artist')
            ->latest('release_date')
            ->limit(8)
            ->get();

        $featuredArtists = Artist::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(12)
            ->get();

        return view('welcome', [
            'recentSongs' => $recentSongs,
            'featuredArtists' => $featuredArtists,
        ]);
    }
}
