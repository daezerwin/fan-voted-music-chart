<x-layouts.app>
    <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
            {{ config('app.name') }}
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-neutral-400">
            A fan-voted music chart built by the community, for the community. Discover trending songs,
            vote for your favorites, and watch the daily chart move.
        </p>
    </div>

    @if ($topTen && $topTen->entries->isNotEmpty())
        <div class="mx-auto max-w-3xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Current Top 10</h2>
                <div class="flex items-center gap-4">
                    <a href="{{ route('play') }}" class="text-sm text-neutral-400 hover:text-white">▶ Play Top 10</a>
                    <a href="{{ route('charts.daily') }}" class="text-sm text-neutral-400 hover:text-white">Full chart →</a>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                @foreach ($topTen->entries as $entry)
                    <x-chart-row :entry="$entry" :has-voted="$votedSongIds->contains($entry->song_id)" />
                @endforeach
            </div>
        </div>
    @endif

    <div class="mx-auto max-w-6xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Recently Added</h2>
            <a href="{{ route('genres.index') }}" class="text-sm text-neutral-400 hover:text-white">Browse genres →</a>
        </div>

        @if ($recentSongs->isEmpty())
            <x-empty-state message="No songs yet." class="mt-4" />
        @else
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($recentSongs as $song)
                    <x-song-card :song="$song" />
                @endforeach
            </div>
        @endif
    </div>

    <div class="mx-auto max-w-6xl px-4 pb-24 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Artists</h2>
            <a href="{{ route('artists.index') }}" class="text-sm text-neutral-400 hover:text-white">Browse all artists →</a>
        </div>

        @if ($featuredArtists->isEmpty())
            <x-empty-state message="No artists yet." class="mt-4" />
        @else
            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($featuredArtists as $artist)
                    <x-artist-card :artist="$artist" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
