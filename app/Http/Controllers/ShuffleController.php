<?php

namespace App\Http\Controllers;

use App\Actions\Songs\GetRandomSong;
use App\Models\Artist;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;

class ShuffleController extends Controller
{
    public function all(GetRandomSong $getRandomSong): RedirectResponse
    {
        $song = $getRandomSong();

        abort_if($song === null, 404);

        return redirect()->route('songs.show', [$song, 'shuffle' => 'all']);
    }

    public function forArtist(Artist $artist, GetRandomSong $getRandomSong): RedirectResponse
    {
        abort_unless($artist->is_active, 404);

        $song = $getRandomSong(artistId: $artist->id);

        abort_if($song === null, 404);

        return redirect()->route('songs.show', [$song, 'shuffle' => 'artist', 'scope' => $artist->slug]);
    }

    public function forGenre(Genre $genre, GetRandomSong $getRandomSong): RedirectResponse
    {
        $song = $getRandomSong(genreId: $genre->id);

        abort_if($song === null, 404);

        return redirect()->route('songs.show', [$song, 'shuffle' => 'genre', 'scope' => $genre->slug]);
    }
}
