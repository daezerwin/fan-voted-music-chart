@props(['song'])

<a href="{{ route('songs.show', $song) }}" class="group flex items-center gap-4 rounded-lg border border-white/10 bg-white/5 p-3 transition hover:border-white/20 hover:bg-white/10">
    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-md bg-neutral-800">
        @if ($song->cover_image)
            <img src="{{ $song->cover_image }}" alt="{{ $song->title }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center text-lg font-semibold text-neutral-600">
                {{ Str::of($song->title)->substr(0, 1) }}
            </div>
        @endif
    </div>

    <div class="min-w-0">
        <p class="truncate font-medium text-white group-hover:underline">{{ $song->title }}</p>
        <p class="truncate text-sm text-neutral-500">{{ $song->artist->name }}</p>
    </div>
</a>
