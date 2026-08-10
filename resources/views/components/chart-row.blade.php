@props(['entry', 'hasVoted' => false])

<div class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/5 p-3 sm:gap-4">
    <div class="w-8 shrink-0 text-center text-lg font-bold text-white">
        {{ $entry->rank }}
    </div>

    <div class="w-14 shrink-0">
        <x-movement-indicator :entry="$entry" />
    </div>

    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-md bg-neutral-800">
        @if ($entry->song->cover_image)
            <img src="{{ $entry->song->cover_image }}" alt="{{ $entry->song->title }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center text-lg font-semibold text-neutral-600">
                {{ Str::of($entry->song->title)->substr(0, 1) }}
            </div>
        @endif
    </div>

    <div class="min-w-0 flex-1">
        <a href="{{ route('songs.show', $entry->song) }}" class="block truncate font-medium text-white hover:underline">
            {{ $entry->song->title }}
        </a>
        <a href="{{ route('artists.show', $entry->song->artist) }}" class="block truncate text-sm text-neutral-400 hover:text-white hover:underline">
            {{ $entry->song->artist->name }}
        </a>
    </div>

    <div class="hidden shrink-0 text-sm text-neutral-500 sm:block">
        {{ number_format($entry->vote_count) }} votes
    </div>

    <div class="shrink-0">
        <x-vote-button :song="$entry->song" :has-voted="$hasVoted" class="!px-3 !py-1.5 !text-sm" />
    </div>
</div>
