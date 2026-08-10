<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenreRequest;
use App\Models\Genre;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class GenreController extends Controller
{
    public function index(): View
    {
        $genres = Genre::query()
            ->withCount('songs')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.genres.index', ['genres' => $genres]);
    }

    public function create(): View
    {
        return view('admin.genres.form', ['genre' => new Genre]);
    }

    public function store(GenreRequest $request): RedirectResponse
    {
        Genre::query()->create($request->validated());

        return redirect()->route('admin.genres.index')->with('status', 'Genre created.');
    }

    public function edit(Genre $genre): View
    {
        return view('admin.genres.form', ['genre' => $genre]);
    }

    public function update(GenreRequest $request, Genre $genre): RedirectResponse
    {
        $genre->update($request->validated());

        return redirect()->route('admin.genres.index')->with('status', 'Genre updated.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        try {
            $genre->delete();
        } catch (QueryException) {
            return redirect()->route('admin.genres.index')
                ->with('error', 'This genre still has songs assigned to it and cannot be deleted.');
        }

        return redirect()->route('admin.genres.index')->with('status', 'Genre deleted.');
    }
}
