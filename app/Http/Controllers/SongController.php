<?php

namespace App\Http\Controllers;

use App\Actions\Songs\GetRandomSong;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    public function show(Request $request, Song $song, GetRandomSong $getRandomSong): View
    {
        $song->load(['artist', 'genre']);

        abort_unless($song->is_active && $song->artist->is_active, 404);

        $todayVoteCount = $song->votes()->where('vote_date', now()->toDateString())->count();

        $hasVotedToday = Auth::check()
            && $song->votes()->where('user_id', Auth::id())->where('vote_date', now()->toDateString())->exists();

        [$nextSong, $nextSongUrl] = $this->resolveNext(
            $request->query('shuffle'),
            $request->query('scope'),
            $song,
            $getRandomSong,
        );

        return view('songs.show', [
            'song' => $song,
            'todayVoteCount' => $todayVoteCount,
            'hasVotedToday' => $hasVotedToday,
            'nextSong' => $nextSong,
            'nextSongUrl' => $nextSongUrl,
        ]);
    }

    /**
     * Every song page previews and autoplays into another song when the
     * current one ends, defaulting to a site-wide random pick. Arriving
     * with ?shuffle=artist|genre&scope={slug} (set by the shuffle
     * redirects, or carried forward from the previous song's own "Up
     * Next") keeps that pick scoped to the artist/genre instead — the
     * scope is re-resolved against real records, rather than trusted as
     * given, so it can't be used to leak an inactive artist/genre's songs.
     *
     * @return array{0: ?Song, 1: ?string}
     */
    private function resolveNext(?string $mode, ?string $scope, Song $current, GetRandomSong $getRandomSong): array
    {
        $artistId = null;
        $genreId = null;
        $effectiveMode = 'all';
        $effectiveScope = null;

        if ($mode === 'artist' && $scope !== null) {
            $artist = Artist::query()->where('slug', $scope)->where('is_active', true)->first();

            if ($artist) {
                $artistId = $artist->id;
                $effectiveMode = 'artist';
                $effectiveScope = $artist->slug;
            }
        } elseif ($mode === 'genre' && $scope !== null) {
            $genre = Genre::query()->where('slug', $scope)->first();

            if ($genre) {
                $genreId = $genre->id;
                $effectiveMode = 'genre';
                $effectiveScope = $genre->slug;
            }
        }

        $nextSong = $getRandomSong(artistId: $artistId, genreId: $genreId, excludeId: $current->id);

        // The scope had nothing else to offer (e.g. an artist with only one
        // song) — fall back to any other active song rather than showing
        // no "Up Next" at all.
        if ($nextSong === null && ($artistId !== null || $genreId !== null)) {
            $nextSong = $getRandomSong(excludeId: $current->id);
            $effectiveMode = 'all';
            $effectiveScope = null;
        }

        if ($nextSong === null) {
            return [null, null];
        }

        $nextSong->load('artist');

        $routeParams = [$nextSong, 'shuffle' => $effectiveMode];

        if ($effectiveScope !== null) {
            $routeParams['scope'] = $effectiveScope;
        }

        return [$nextSong, route('songs.show', $routeParams)];
    }
}
