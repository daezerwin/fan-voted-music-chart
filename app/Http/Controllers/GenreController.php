<?php

namespace App\Http\Controllers;

use App\Actions\Discovery\GetBiggestGainers;
use App\Actions\Discovery\GetNewEntries;
use App\Actions\Discovery\GetTrendingSongs;
use App\Models\Genre;
use Illuminate\Contracts\View\View;

class GenreController extends Controller
{
    public function index(): View
    {
        $genres = Genre::query()->orderBy('name')->get();

        return view('genres.index', ['genres' => $genres]);
    }

    public function show(
        Genre $genre,
        GetTrendingSongs $trendingSongs,
        GetBiggestGainers $biggestGainers,
        GetNewEntries $newEntries,
    ): View {
        $songs = $genre->songs()
            ->where('is_active', true)
            ->whereHas('artist', fn ($artists) => $artists->where('is_active', true))
            ->with('artist')
            ->orderByDesc('release_date')
            ->paginate(24);

        return view('genres.show', [
            'genre' => $genre,
            'songs' => $songs,
            'trendingSongs' => $trendingSongs($genre->id),
            'biggestGainers' => $biggestGainers($genre->id),
            'newEntries' => $newEntries($genre->id),
        ]);
    }
}
