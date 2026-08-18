<x-layouts.app :title="$genre->name.' — '.config('app.name')" :description="'Songs in the '.$genre->name.' genre.'">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-ink">{{ $genre->name }}</h1>

        <div class="mt-8">
            @include('partials.discovery-sections')
        </div>

        <div class="mt-12 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-ink">All Songs</h2>

            @if ($songs->isNotEmpty())
                <a href="{{ route('genres.shuffle', $genre) }}" class="text-sm font-medium text-muted hover:text-ink">
                    Shuffle
                </a>
            @endif
        </div>

        @if ($songs->isEmpty())
            <x-empty-state message="No songs in this genre yet." class="mt-6" />
        @else
            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($songs as $song)
                    <x-song-card :song="$song" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $songs->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
