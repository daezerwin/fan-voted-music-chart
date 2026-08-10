<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Contracts\View\View;

class ArtistController extends Controller
{
    public function index(): View
    {
        $artists = Artist::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(24);

        return view('artists.index', ['artists' => $artists]);
    }

    public function show(Artist $artist): View
    {
        abort_unless($artist->is_active, 404);

        $songs = $artist->songs()
            ->where('is_active', true)
            ->with('artist')
            ->orderByDesc('release_date')
            ->paginate(24);

        return view('artists.show', ['artist' => $artist, 'songs' => $songs]);
    }
}
