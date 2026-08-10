<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->string('q'));

        $artists = collect();
        $songs = collect();

        if ($query !== '') {
            $like = '%'.addcslashes($query, '%_\\').'%';

            $artists = Artist::query()
                ->where('is_active', true)
                ->where('name', 'like', $like)
                ->orderBy('name')
                ->limit(12)
                ->get();

            $songs = Song::query()
                ->where('is_active', true)
                ->where('title', 'like', $like)
                ->whereHas('artist', fn ($artists) => $artists->where('is_active', true))
                ->with('artist')
                ->orderBy('title')
                ->limit(12)
                ->get();
        }

        return view('search.index', [
            'query' => $query,
            'artists' => $artists,
            'songs' => $songs,
        ]);
    }
}
