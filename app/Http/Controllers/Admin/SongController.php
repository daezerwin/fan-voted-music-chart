<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SongRequest;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SongController extends Controller
{
    public function index(): View
    {
        $songs = Song::query()
            ->with(['artist', 'genre'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.songs.index', ['songs' => $songs]);
    }

    public function create(): View
    {
        return view('admin.songs.form', [
            'song' => new Song,
            'artists' => Artist::query()->orderBy('name')->get(),
            'genres' => Genre::query()->orderBy('name')->get(),
        ]);
    }

    public function store(SongRequest $request): RedirectResponse
    {
        $song = Song::query()->create($request->validated());

        return redirect()->route('admin.songs.edit', $song)->with('status', 'Song created.');
    }

    public function edit(Song $song): View
    {
        return view('admin.songs.form', [
            'song' => $song,
            'artists' => Artist::query()->orderBy('name')->get(),
            'genres' => Genre::query()->orderBy('name')->get(),
        ]);
    }

    public function update(SongRequest $request, Song $song): RedirectResponse
    {
        $song->update($request->validated());

        return redirect()->route('admin.songs.edit', $song)->with('status', 'Song updated.');
    }
}
