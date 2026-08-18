@if ($trendingSongs->isNotEmpty() || $biggestGainers->isNotEmpty() || $newEntries->isNotEmpty())
    <div class="grid gap-8 sm:grid-cols-3">
        <div>
            <h2 class="text-lg font-semibold text-ink">Trending Now</h2>
            <p class="text-xs text-muted">Most votes cast today</p>

            <div class="mt-4 space-y-3">
                @forelse ($trendingSongs as $song)
                    <a href="{{ route('songs.show', $song) }}" class="flex items-center justify-between gap-2 hover:underline">
                        <span class="min-w-0 truncate text-sm text-ink">{{ $song->title }}</span>
                        <span class="shrink-0 text-xs text-muted">{{ $song->votes_today }} votes</span>
                    </a>
                @empty
                    <p class="text-sm text-muted">No votes yet today.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-ink">Biggest Gainers</h2>
            <p class="text-xs text-muted">Largest chart movement</p>

            <div class="mt-4 space-y-3">
                @forelse ($biggestGainers as $entry)
                    <a href="{{ route('songs.show', $entry->song) }}" class="flex items-center justify-between gap-2 hover:underline">
                        <span class="min-w-0 truncate text-sm text-ink">{{ $entry->song->title }}</span>
                        <x-movement-indicator :entry="$entry" />
                    </a>
                @empty
                    <p class="text-sm text-muted">No movers yet.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-ink">New Entries</h2>
            <p class="text-xs text-muted">New to the chart</p>

            <div class="mt-4 space-y-3">
                @forelse ($newEntries as $entry)
                    <a href="{{ route('songs.show', $entry->song) }}" class="flex items-center justify-between gap-2 hover:underline">
                        <span class="min-w-0 truncate text-sm text-ink">{{ $entry->song->title }}</span>
                        <span class="shrink-0 text-xs text-muted">#{{ $entry->rank }}</span>
                    </a>
                @empty
                    <p class="text-sm text-muted">No new entries yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endif
