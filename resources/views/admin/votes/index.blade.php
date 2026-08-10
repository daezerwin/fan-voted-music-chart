<x-layouts.admin title="Votes">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-white">Voting Activity</h1>

        <form action="{{ route('admin.votes.index') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-white">
        </form>
    </div>

    <h2 class="mt-8 text-lg font-semibold text-white">Top Voters</h2>
    <p class="text-sm text-neutral-500">
        Visibility only — no automated fraud scoring exists yet. Use this to spot unusual vote velocity.
    </p>

    <div class="mt-4 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($topVoters as $row)
            <div class="flex items-center justify-between gap-4 p-4">
                <span class="truncate text-white">{{ $row->user->name ?? 'Unknown user' }}</span>
                <span class="shrink-0 text-sm text-neutral-500">{{ $row->votes_cast }} votes</span>
            </div>
        @empty
            <div class="p-4 text-sm text-neutral-500">No votes on this date.</div>
        @endforelse
    </div>

    <h2 class="mt-8 text-lg font-semibold text-white">Shared IP Addresses</h2>
    <p class="text-sm text-neutral-500">
        IPs used by more than one account today. Shared IPs are common (households, NAT, mobile
        carriers, campus/office networks) and are not evidence of abuse by themselves — treat this as a
        prompt for moderation review, not an automatic signal.
    </p>

    <div class="mt-4 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($sharedIps as $row)
            <div class="flex items-center justify-between gap-4 p-4 text-sm">
                <span class="truncate text-white">{{ $row->ip_address }}</span>
                <span class="shrink-0 text-neutral-500">{{ $row->account_count }} accounts</span>
            </div>
        @empty
            <div class="p-4 text-sm text-neutral-500">No shared IPs on this date.</div>
        @endforelse
    </div>

    <h2 class="mt-8 text-lg font-semibold text-white">Recent Votes</h2>

    <div class="mt-4 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($recentVotes as $vote)
            <div class="flex items-center justify-between gap-4 p-4 text-sm">
                <span class="truncate text-white">{{ $vote->user->name ?? 'Unknown user' }}</span>
                <span class="min-w-0 flex-1 truncate text-neutral-400">{{ $vote->song->title ?? 'Unknown song' }}</span>
                <span class="shrink-0 text-neutral-500">{{ $vote->ip_address }}</span>
                <span class="shrink-0 text-neutral-500">{{ $vote->created_at->format('H:i:s') }}</span>
            </div>
        @empty
            <div class="p-4 text-sm text-neutral-500">No votes on this date.</div>
        @endforelse
    </div>
</x-layouts.admin>
