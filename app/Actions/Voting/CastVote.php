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
     *
     * The IP address is recorded for audit visibility only (see
     * Admin\VoteController) — it never blocks or throttles a vote by itself.
     */
    public function __invoke(User $user, Song $song, ?string $ipAddress = null): VoteResult
    {
        if (! $song->is_active || ! $song->voting_enabled) {
            return VoteResult::Ineligible;
        }

        $voteDate = now()->toDateString();

        try {
            DB::transaction(function () use ($user, $song, $voteDate, $ipAddress) {
                Vote::query()->create([
                    'user_id' => $user->id,
                    'song_id' => $song->id,
                    'vote_date' => $voteDate,
                    'ip_address' => $ipAddress,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            return VoteResult::AlreadyVoted;
        }

        return VoteResult::Cast;
    }
}
