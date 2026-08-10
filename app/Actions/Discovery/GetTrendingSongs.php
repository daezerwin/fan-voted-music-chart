<?php

namespace App\Actions\Discovery;

use App\Models\Song;
use App\Models\Vote;
use Illuminate\Support\Collection;

class GetTrendingSongs
{
    /**
     * Songs with the most votes cast *today*, independent of the official
     * chart (which reflects a completed prior day). This is the live,
     * unofficial "what's hot right now" signal.
     *
     * @return Collection<int, Song>
     */
    public function __invoke(?int $genreId = null, int $limit = 5): Collection
    {
        return Vote::query()
            ->select('song_id')
            ->selectRaw('COUNT(*) as votes_today')
            ->where('vote_date', now()->toDateString())
            ->whereHas('song', function ($songs) use ($genreId) {
                $songs->where('is_active', true)
                    ->whereHas('artist', fn ($artists) => $artists->where('is_active', true));

                if ($genreId !== null) {
                    $songs->where('genre_id', $genreId);
                }
            })
            ->groupBy('song_id')
            ->orderByDesc('votes_today')
            ->limit($limit)
            ->with('song.artist')
            ->get()
            ->map(function ($row) {
                $song = $row->song;
                $song->votes_today = (int) $row->votes_today;

                return $song;
            });
    }
}
