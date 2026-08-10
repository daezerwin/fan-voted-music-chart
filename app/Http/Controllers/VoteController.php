<?php

namespace App\Http\Controllers;

use App\Actions\Voting\CastVote;
use App\Enums\VoteResult;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function store(Request $request, Song $song, CastVote $castVote): RedirectResponse
    {
        abort_unless($song->is_active && $song->artist->is_active, 404);

        $result = $castVote(Auth::user(), $song, $request->ip());

        return match ($result) {
            VoteResult::Cast => back()->with('status', 'Your vote has been recorded.'),
            VoteResult::AlreadyVoted => back()->with('status', 'You have already voted for this song today.'),
            VoteResult::Ineligible => back()->with('error', 'Voting is currently closed for this song.'),
        };
    }
}
