<?php

namespace App\Actions\Songs;

use App\Models\Song;

class GetRandomSong
{
    public function __invoke(?int $artistId = null, ?int $genreId = null): ?Song
    {
        return Song::query()
            ->where('is_active', true)
            ->whereHas('artist', fn ($artists) => $artists->where('is_active', true))
            ->when($artistId !== null, fn ($songs) => $songs->where('artist_id', $artistId))
            ->when($genreId !== null, fn ($songs) => $songs->where('genre_id', $genreId))
            ->inRandomOrder()
            ->first();
    }
}
