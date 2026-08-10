<?php

namespace App\Http\Controllers;

use App\Actions\Charts\BuildTopTenPlaylist;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Js;

class TopTenPlayerController extends Controller
{
    public function show(BuildTopTenPlaylist $playlist): View
    {
        $entries = $playlist();

        $queue = $entries->map(fn ($entry) => [
            'videoId' => $entry->song->youtube_video_id,
            'title' => $entry->song->title,
            'artist' => $entry->song->artist->name,
        ])->values();

        return view('charts.play', [
            'entries' => $entries,
            'queueJson' => Js::from($queue),
        ]);
    }
}
