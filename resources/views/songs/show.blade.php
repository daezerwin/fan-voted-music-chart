<x-layouts.app :title="$song->title.' — '.$song->artist->name" :description="Str::limit(strip_tags((string) $song->description), 155)">
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
            <div class="h-40 w-40 shrink-0 overflow-hidden rounded-lg bg-neutral-800">
                @if ($song->cover_image)
                    <img src="{{ $song->cover_image }}" alt="{{ $song->title }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-4xl font-semibold text-neutral-600">
                        {{ Str::of($song->title)->substr(0, 1) }}
                    </div>
                @endif
            </div>

            <div class="min-w-0">
                <h1 class="text-3xl font-semibold text-white">{{ $song->title }}</h1>
                <a href="{{ route('artists.show', $song->artist) }}" class="mt-1 inline-block text-neutral-400 hover:text-white hover:underline">
                    {{ $song->artist->name }}
                </a>

                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-neutral-500">
                    <x-genre-badge :genre="$song->genre" />

                    @if ($song->release_date)
                        <span>{{ $song->release_date->format('F j, Y') }}</span>
                    @endif
                </div>

                @if ($song->description)
                    <p class="mt-4 max-w-2xl text-neutral-300">{{ $song->description }}</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
