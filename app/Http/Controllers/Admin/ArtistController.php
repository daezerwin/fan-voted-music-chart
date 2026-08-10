<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArtistRequest;
use App\Models\Artist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ArtistController extends Controller
{
    public function index(): View
    {
        $artists = Artist::query()
            ->withCount('songs')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.artists.index', ['artists' => $artists]);
    }

    public function create(): View
    {
        return view('admin.artists.form', ['artist' => new Artist]);
    }

    public function store(ArtistRequest $request): RedirectResponse
    {
        $artist = Artist::query()->create($request->validated());

        return redirect()->route('admin.artists.edit', $artist)->with('status', 'Artist created.');
    }

    public function edit(Artist $artist): View
    {
        return view('admin.artists.form', ['artist' => $artist]);
    }

    public function update(ArtistRequest $request, Artist $artist): RedirectResponse
    {
        $artist->update($request->validated());

        return redirect()->route('admin.artists.edit', $artist)->with('status', 'Artist updated.');
    }
}
