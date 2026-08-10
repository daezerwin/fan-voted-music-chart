<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * A basic window into recent voting activity. This is visibility only —
     * no fraud scoring or automated flagging exists yet (that's real Phase 11
     * anti-abuse work); an admin scanning this list is the starting point.
     */
    public function index(Request $request): View
    {
        $date = $request->string('date')->toString() ?: now()->toDateString();

        $topVoters = Vote::query()
            ->selectRaw('user_id, COUNT(*) as votes_cast')
            ->where('vote_date', $date)
            ->groupBy('user_id')
            ->orderByDesc('votes_cast')
            ->with('user')
            ->limit(20)
            ->get();

        $recentVotes = Vote::query()
            ->where('vote_date', $date)
            ->with(['user', 'song'])
            ->latest('created_at')
            ->limit(50)
            ->get();

        return view('admin.votes.index', [
            'date' => $date,
            'topVoters' => $topVoters,
            'recentVotes' => $recentVotes,
        ]);
    }
}
