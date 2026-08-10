<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Contracts\View\View;

class SongController extends Controller
{
    public function show(Song $song): View
    {
        $song->load(['artist', 'genre']);

        abort_unless($song->is_active && $song->artist->is_active, 404);

        return view('songs.show', ['song' => $song]);
    }
}
