<x-layouts.admin title="Votes">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-ink">Voting Activity</h1>

        <form action="{{ route('admin.votes.index') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="rounded-lg border border-white/10 bg-surface px-3 py-1.5 text-sm text-ink">
        </form>
    </div>

    <h2 class="mt-8 text-lg font-semibold text-ink">Top Voters</h2>
    <p class="text-sm text-muted">
        Visibility only — no automated fraud scoring exists yet. Use this to spot unusual vote velocity.
    </p>

    <div class="mt-4 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($topVoters as $row)
            <div class="flex items-center justify-between gap-4 p-4">
                <span class="truncate text-ink">{{ $row->user->name ?? 'Unknown user' }}</span>
                <span class="shrink-0 text-sm text-muted">{{ $row->votes_cast }} votes</span>
            </div>
        @empty
            <div class="p-4 text-sm text-muted">No votes on this date.</div>
        @endforelse
    </div>

    <h2 class="mt-8 text-lg font-semibold text-ink">Shared IP Addresses</h2>
    <p class="text-sm text-muted">
        IPs used by more than one account today. Shared IPs are common (households, NAT, mobile
        carriers, campus/office networks) and are not evidence of abuse by themselves — treat this as a
        prompt for moderation review, not an automatic signal.
    </p>

    <div class="mt-4 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($sharedIps as $row)
            <div class="flex items-center justify-between gap-4 p-4 text-sm">
                <span class="truncate text-ink">{{ $row->ip_address }}</span>
                <span class="shrink-0 text-muted">{{ $row->account_count }} accounts</span>
            </div>
        @empty
            <div class="p-4 text-sm text-muted">No shared IPs on this date.</div>
        @endforelse
    </div>

    <h2 class="mt-8 text-lg font-semibold text-ink">Recent Votes</h2>

    <div class="mt-4 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($recentVotes as $vote)
            <div class="flex items-center justify-between gap-4 p-4 text-sm">
                <span class="truncate text-ink">{{ $vote->user->name ?? 'Unknown user' }}</span>
                <span class="min-w-0 flex-1 truncate text-muted">{{ $vote->song->title ?? 'Unknown song' }}</span>
                <span class="shrink-0 text-muted">{{ $vote->ip_address }}</span>
                <span class="shrink-0 text-muted">{{ $vote->created_at->format('H:i:s') }}</span>
            </div>
        @empty
            <div class="p-4 text-sm text-muted">No votes on this date.</div>
        @endforelse
    </div>
</x-layouts.admin>
