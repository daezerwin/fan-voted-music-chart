@if ($trendingSongs->isNotEmpty() || $biggestGainers->isNotEmpty() || $newEntries->isNotEmpty())
    <div class="grid gap-8 sm:grid-cols-3">
        <div>
            <h2 class="text-lg font-semibold text-white">Trending Now</h2>
            <p class="text-xs text-neutral-500">Most votes cast today</p>

            <div class="mt-4 space-y-3">
                @forelse ($trendingSongs as $song)
                    <a href="{{ route('songs.show', $song) }}" class="flex items-center justify-between gap-2 hover:underline">
                        <span class="min-w-0 truncate text-sm text-white">{{ $song->title }}</span>
                        <span class="shrink-0 text-xs text-neutral-500">{{ $song->votes_today }} votes</span>
                    </a>
                @empty
                    <p class="text-sm text-neutral-500">No votes yet today.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-white">Biggest Gainers</h2>
            <p class="text-xs text-neutral-500">Largest chart movement</p>

            <div class="mt-4 space-y-3">
                @forelse ($biggestGainers as $entry)
                    <a href="{{ route('songs.show', $entry->song) }}" class="flex items-center justify-between gap-2 hover:underline">
                        <span class="min-w-0 truncate text-sm text-white">{{ $entry->song->title }}</span>
                        <x-movement-indicator :entry="$entry" />
                    </a>
                @empty
                    <p class="text-sm text-neutral-500">No movers yet.</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-white">New Entries</h2>
            <p class="text-xs text-neutral-500">New to the chart</p>

            <div class="mt-4 space-y-3">
                @forelse ($newEntries as $entry)
                    <a href="{{ route('songs.show', $entry->song) }}" class="flex items-center justify-between gap-2 hover:underline">
                        <span class="min-w-0 truncate text-sm text-white">{{ $entry->song->title }}</span>
                        <span class="shrink-0 text-xs text-neutral-500">#{{ $entry->rank }}</span>
                    </a>
                @empty
                    <p class="text-sm text-neutral-500">No new entries yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endif
