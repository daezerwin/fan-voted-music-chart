<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'Users' => User::query()->count(),
                'Artists' => Artist::query()->where('is_active', true)->count(),
                'Songs' => Song::query()->where('is_active', true)->count(),
                'Genres' => Genre::query()->count(),
                'Votes today' => Vote::query()->where('vote_date', now()->toDateString())->count(),
            ],
        ]);
    }
}
