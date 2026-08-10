<?php

namespace App\Http\Controllers;

use App\Enums\ChartType;
use App\Models\Chart;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ChartController extends Controller
{
    public function daily(?string $date = null): View
    {
        $query = Chart::query()->where('chart_type', ChartType::Daily);

        $chart = $date !== null
            ? $query->where('chart_date', $date)->first()
            : $query->latest('chart_date')->first();

        abort_if($chart === null, 404);

        $chart->load([
            'entries' => fn ($entries) => $entries->orderBy('rank'),
            'entries.song.artist',
        ]);

        return view('charts.daily', [
            'chart' => $chart,
            'votedSongIds' => $this->votedSongIds($chart->entries->pluck('song_id')),
        ]);
    }

    /**
     * @param  Collection<int, int>  $songIds
     * @return Collection<int, int>
     */
    private function votedSongIds(Collection $songIds): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        return Vote::query()
            ->where('user_id', Auth::id())
            ->where('vote_date', now()->toDateString())
            ->whereIn('song_id', $songIds)
            ->pluck('song_id');
    }
}
