<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    public function show(Song $song): View
    {
        $song->load(['artist', 'genre']);

        abort_unless($song->is_active && $song->artist->is_active, 404);

        $todayVoteCount = $song->votes()->where('vote_date', now()->toDateString())->count();

        $hasVotedToday = Auth::check()
            && $song->votes()->where('user_id', Auth::id())->where('vote_date', now()->toDateString())->exists();

        return view('songs.show', [
            'song' => $song,
            'todayVoteCount' => $todayVoteCount,
            'hasVotedToday' => $hasVotedToday,
        ]);
    }
}
