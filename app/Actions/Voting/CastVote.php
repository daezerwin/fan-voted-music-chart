<?php

namespace App\Actions\Voting;

use App\Enums\VoteResult;
use App\Models\Song;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class CastVote
{
    /**
     * Cast a vote for a song, using the app's configured business timezone to
     * determine "today". Duplicate-vote prevention is enforced by the
     * database's unique constraint, not a check-then-insert, so concurrent
     * requests can never produce two valid votes for the same user/song/day.
     */
    public function __invoke(User $user, Song $song): VoteResult
    {
        if (! $song->is_active || ! $song->voting_enabled) {
            return VoteResult::Ineligible;
        }

        $voteDate = now()->toDateString();

        try {
            DB::transaction(function () use ($user, $song, $voteDate) {
                Vote::query()->create([
                    'user_id' => $user->id,
                    'song_id' => $song->id,
                    'vote_date' => $voteDate,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            return VoteResult::AlreadyVoted;
        }

        return VoteResult::Cast;
    }
}
