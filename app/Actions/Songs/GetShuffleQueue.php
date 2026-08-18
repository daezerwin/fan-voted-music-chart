<?php

namespace App\Actions\Songs;

use App\Models\Song;
use Illuminate\Database\Eloquent\Collection;

class GetShuffleQueue
{
    /**
     * @return Collection<int, Song>
     */
    public function __invoke(?int $artistId = null, ?int $genreId = null, ?int $excludeId = null, int $limit = 10): Collection
    {
        return Song::query()
            ->where('is_active', true)
            ->whereHas('artist', fn ($artists) => $artists->where('is_active', true))
            ->when($artistId !== null, fn ($songs) => $songs->where('artist_id', $artistId))
            ->when($genreId !== null, fn ($songs) => $songs->where('genre_id', $genreId))
            ->when($excludeId !== null, fn ($songs) => $songs->where('id', '!=', $excludeId))
            ->inRandomOrder()
            ->limit($limit)
            ->with('artist')
            ->get();
    }
}
