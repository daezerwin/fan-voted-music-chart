<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    public function show(Request $request, Song $song): View
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
            'shuffleNextUrl' => $this->resolveShuffleNextUrl($request->query('shuffle'), $request->query('scope')),
        ]);
    }

    /**
     * Reached with ?shuffle=all|artist|genre (and &scope={slug} for the
     * latter two) after a shuffle redirect. Re-resolving through the real
     * shuffle routes — rather than trusting a client-supplied URL — means
     * every "Next" click still enforces the same active/eligible checks.
     */
    private function resolveShuffleNextUrl(?string $mode, ?string $scope): ?string
    {
        if ($mode === 'all') {
            return route('shuffle.all');
        }

        if ($mode === 'artist' && $scope !== null) {
            $artist = Artist::query()->where('slug', $scope)->where('is_active', true)->first();

            return $artist ? route('artists.shuffle', $artist) : null;
        }

        if ($mode === 'genre' && $scope !== null) {
            $genre = Genre::query()->where('slug', $scope)->first();

            return $genre ? route('genres.shuffle', $genre) : null;
        }

        return null;
    }
}
