<?php

namespace App\Http\Controllers;

use App\Actions\Songs\GetShuffleQueue;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    /**
     * How many upcoming songs the "Up Next" queue holds.
     */
    private const QUEUE_SIZE = 10;

    public function show(Request $request, Song $song, GetShuffleQueue $getShuffleQueue): View
    {
        $song->load(['artist', 'genre']);

        abort_unless($song->is_active && $song->artist->is_active, 404);

        $todayVoteCount = $song->votes()->where('vote_date', now()->toDateString())->count();

        $hasVotedToday = Auth::check()
            && $song->votes()->where('user_id', Auth::id())->where('vote_date', now()->toDateString())->exists();

        $queue = $this->resolveQueue(
            $request->query('shuffle'),
            $request->query('scope'),
            $song,
            $getShuffleQueue,
        );

        return view('songs.show', [
            'song' => $song,
            'todayVoteCount' => $todayVoteCount,
            'hasVotedToday' => $hasVotedToday,
            'queue' => $queue,
            'nextSong' => $queue->first()['song'] ?? null,
            'nextSongUrl' => $queue->first()['url'] ?? null,
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
     * @return Collection<int, array{song: Song, url: string}>
     */
    private function resolveQueue(?string $mode, ?string $scope, Song $current, GetShuffleQueue $getShuffleQueue): Collection
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

        $queue = $getShuffleQueue(artistId: $artistId, genreId: $genreId, excludeId: $current->id, limit: self::QUEUE_SIZE);

        // The scope had nothing else to offer (e.g. an artist with only one
        // song) — fall back to any other active songs rather than showing
        // no "Up Next" at all.
        if ($queue->isEmpty() && ($artistId !== null || $genreId !== null)) {
            $queue = $getShuffleQueue(excludeId: $current->id, limit: self::QUEUE_SIZE);
            $effectiveMode = 'all';
            $effectiveScope = null;
        }

        return $queue->map(function (Song $song) use ($effectiveMode, $effectiveScope) {
            $routeParams = [$song, 'shuffle' => $effectiveMode];

            if ($effectiveScope !== null) {
                $routeParams['scope'] = $effectiveScope;
            }

            return ['song' => $song, 'url' => route('songs.show', $routeParams)];
        });
    }
}
