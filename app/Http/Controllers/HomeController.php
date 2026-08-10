<?php

namespace App\Http\Controllers;

use App\Enums\ChartType;
use App\Models\Artist;
use App\Models\Chart;
use App\Models\Song;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $topTen = Chart::query()
            ->where('chart_type', ChartType::Daily)
            ->latest('chart_date')
            ->first();

        $topTen?->load([
            'entries' => fn ($entries) => $entries->orderBy('rank')->limit(10),
            'entries.song.artist',
        ]);

        $votedSongIds = $topTen !== null && Auth::check()
            ? Vote::query()
                ->where('user_id', Auth::id())
                ->where('vote_date', now()->toDateString())
                ->whereIn('song_id', $topTen->entries->pluck('song_id'))
                ->pluck('song_id')
            : collect();

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
            'topTen' => $topTen,
            'votedSongIds' => $votedSongIds,
            'recentSongs' => $recentSongs,
            'featuredArtists' => $featuredArtists,
        ]);
    }
}
