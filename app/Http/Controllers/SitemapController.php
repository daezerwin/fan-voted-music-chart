<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            $urls = collect([
                ['loc' => route('home'), 'priority' => '1.0'],
                ['loc' => route('charts.daily'), 'priority' => '0.9'],
                ['loc' => route('artists.index'), 'priority' => '0.7'],
                ['loc' => route('genres.index'), 'priority' => '0.7'],
            ]);

            Artist::query()->where('is_active', true)->select('slug', 'updated_at')->each(function ($artist) use ($urls) {
                $urls->push(['loc' => route('artists.show', $artist), 'lastmod' => $artist->updated_at, 'priority' => '0.6']);
            });

            Genre::query()->select('slug')->each(function ($genre) use ($urls) {
                $urls->push(['loc' => route('genres.show', $genre), 'priority' => '0.5']);
            });

            Song::query()
                ->where('is_active', true)
                ->whereHas('artist', fn ($artists) => $artists->where('is_active', true))
                ->select('slug', 'updated_at')
                ->each(function ($song) use ($urls) {
                    $urls->push(['loc' => route('songs.show', $song), 'lastmod' => $song->updated_at, 'priority' => '0.6']);
                });

            return view('sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
