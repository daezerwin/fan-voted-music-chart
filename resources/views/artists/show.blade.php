<x-layouts.app
    :title="$artist->name.' — '.config('app.name')"
    :description="Str::limit(strip_tags((string) $artist->bio), 155)"
    :image="$artist->image"
    og-type="profile"
>
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
            <div class="h-32 w-32 shrink-0 overflow-hidden rounded-lg bg-surface">
                @if ($artist->image)
                    <img src="{{ $artist->image }}" alt="{{ $artist->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-4xl font-semibold text-muted">
                        {{ Str::of($artist->name)->substr(0, 1) }}
                    </div>
                @endif
            </div>

            <div class="min-w-0">
                <h1 class="text-3xl font-semibold text-ink">{{ $artist->name }}</h1>

                @if ($artist->country)
                    <p class="mt-1 text-muted">{{ $artist->country }}</p>
                @endif

                @if ($artist->bio)
                    <p class="mt-4 max-w-2xl text-muted">{{ $artist->bio }}</p>
                @endif

                @if ($artist->website)
                    <a href="{{ $artist->website }}" rel="noopener noreferrer" target="_blank" class="mt-4 inline-block text-sm text-muted underline hover:text-ink">
                        Official website
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-12 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-ink">Songs</h2>

            @if ($songs->isNotEmpty())
                <a href="{{ route('artists.shuffle', $artist) }}" class="text-sm font-medium text-muted hover:text-ink">
                    Shuffle
                </a>
            @endif
        </div>

        @if ($songs->isEmpty())
            <x-empty-state message="No songs yet." class="mt-4" />
        @else
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
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
