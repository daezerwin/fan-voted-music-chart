<x-layouts.app :title="'Search — '.config('app.name')">
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <form action="{{ route('search') }}" method="GET">
            <label for="q" class="sr-only">Search artists and songs</label>
            <input
                id="q"
                type="search"
                name="q"
                value="{{ $query }}"
                placeholder="Search artists and songs"
                autofocus
                class="w-full rounded-lg border border-black/10 bg-surface px-4 py-3 text-ink placeholder-muted focus:border-primary focus:outline-none"
            >
        </form>

        @if ($query === '')
            <p class="mt-8 text-muted">Start typing to search artists and songs.</p>
        @else
            <h2 class="mt-8 text-lg font-semibold text-ink">Artists</h2>
            @if ($artists->isEmpty())
                <x-empty-state message="No matching artists." class="mt-4" />
            @else
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                    @foreach ($artists as $artist)
                        <x-artist-card :artist="$artist" />
                    @endforeach
                </div>
            @endif

            <h2 class="mt-10 text-lg font-semibold text-ink">Songs</h2>
            @if ($songs->isEmpty())
                <x-empty-state message="No matching songs." class="mt-4" />
            @else
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($songs as $song)
                        <x-song-card :song="$song" />
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
