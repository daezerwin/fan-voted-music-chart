@props(['artist'])

<a href="{{ route('artists.show', $artist) }}" class="group block rounded-lg border border-white/10 bg-white/5 p-4 transition hover:border-white/20 hover:bg-white/10">
    <div class="aspect-square w-full overflow-hidden rounded-md bg-neutral-800">
        @if ($artist->image)
            <img src="{{ $artist->image }}" alt="{{ $artist->name }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center text-2xl font-semibold text-neutral-600">
                {{ Str::of($artist->name)->substr(0, 1) }}
            </div>
        @endif
    </div>

    <p class="mt-3 truncate font-medium text-white group-hover:underline">{{ $artist->name }}</p>

    @if ($artist->country)
        <p class="text-sm text-neutral-500">{{ $artist->country }}</p>
    @endif
</a>
