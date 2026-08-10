<?php

namespace App\Http\Controllers;

use App\Actions\Charts\GetLatestDailyChart;
use App\Actions\Discovery\GetBiggestGainers;
use App\Actions\Discovery\GetNewEntries;
use App\Actions\Discovery\GetTrendingSongs;
use App\Models\Artist;
use App\Models\Song;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke(
        GetLatestDailyChart $latestDailyChart,
        GetTrendingSongs $trendingSongs,
        GetBiggestGainers $biggestGainers,
        GetNewEntries $newEntries,
    ): View {
        $topTen = $latestDailyChart();

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
            ->orderByDesc('is_featured')
            ->inRandomOrder()
            ->limit(12)
            ->get();

        return view('welcome', [
            'topTen' => $topTen,
            'votedSongIds' => $votedSongIds,
            'recentSongs' => $recentSongs,
            'featuredArtists' => $featuredArtists,
            'trendingSongs' => $trendingSongs(),
            'biggestGainers' => $biggestGainers(),
            'newEntries' => $newEntries(),
        ]);
    }
}
