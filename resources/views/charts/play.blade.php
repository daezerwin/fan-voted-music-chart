<x-layouts.app :title="'Play Top 10 — '.config('app.name')" description="Listen to today's Top 10 fan-voted songs.">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8" x-data="topTenPlayer({{ $queueJson }})">
        <h1 class="text-2xl font-semibold text-white">Play Top 10</h1>

        @if ($entries->isEmpty())
            <x-empty-state message="No chart is available to play yet." class="mt-6" />
        @else
            <div class="mt-6 aspect-video w-full overflow-hidden rounded-lg bg-black">
                <div id="youtube-player" class="h-full w-full"></div>
            </div>

            <div class="mt-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm text-neutral-500">
                        Now Playing (<span x-text="index + 1"></span> of <span x-text="queue.length"></span>)
                    </p>
                    <p class="truncate font-medium text-white" x-text="current?.title"></p>
                    <p class="truncate text-sm text-neutral-400" x-text="current?.artist"></p>
                </div>

                <div class="flex shrink-0 gap-2">
                    <button
                        type="button"
                        @click="playPrevious()"
                        :disabled="index === 0"
                        class="rounded-lg bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/20 disabled:opacity-40"
                    >
                        Previous
                    </button>
                    <button
                        type="button"
                        @click="playNext()"
                        :disabled="index === queue.length - 1"
                        class="rounded-lg bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/20 disabled:opacity-40"
                    >
                        Next
                    </button>
                </div>
            </div>

            <ol class="mt-8 space-y-1">
                @foreach ($entries as $i => $entry)
                    <li>
                        <button
                            type="button"
                            @click="playAt({{ $i }})"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left hover:bg-white/5"
                            :class="index === {{ $i }} ? 'bg-white/10' : ''"
                        >
                            <span class="w-6 text-sm font-semibold text-neutral-500">{{ $entry->rank }}</span>
                            <span class="min-w-0 flex-1 truncate text-sm text-white">{{ $entry->song->title }}</span>
                            <span class="shrink-0 truncate text-sm text-neutral-500">{{ $entry->song->artist->name }}</span>
                        </button>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</x-layouts.app>
