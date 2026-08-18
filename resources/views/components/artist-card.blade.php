@props(['artist'])

<a href="{{ route('artists.show', $artist) }}" class="group block rounded-lg border border-black/10 bg-surface p-4 transition hover:border-black/20 hover:bg-black/10">
    <div class="aspect-square w-full overflow-hidden rounded-md bg-black/10">
        @if ($artist->image)
            <img src="{{ $artist->image }}" alt="{{ $artist->name }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center text-2xl font-semibold text-muted">
                {{ Str::of($artist->name)->substr(0, 1) }}
            </div>
        @endif
    </div>

    <p class="mt-3 truncate font-medium text-ink group-hover:underline">{{ $artist->name }}</p>

    @if ($artist->country)
        <p class="text-sm text-muted">{{ $artist->country }}</p>
    @endif
</a>
