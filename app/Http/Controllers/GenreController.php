<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Contracts\View\View;

class GenreController extends Controller
{
    public function index(): View
    {
        $genres = Genre::query()->orderBy('name')->get();

        return view('genres.index', ['genres' => $genres]);
    }

    public function show(Genre $genre): View
    {
        $songs = $genre->songs()
            ->where('is_active', true)
            ->whereHas('artist', fn ($artists) => $artists->where('is_active', true))
            ->with('artist')
            ->orderByDesc('release_date')
            ->paginate(24);

        return view('genres.show', ['genre' => $genre, 'songs' => $songs]);
    }
}
