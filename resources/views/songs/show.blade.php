<x-layouts.app
    :title="$song->title.' — '.$song->artist->name"
    :description="Str::limit(strip_tags((string) $song->description), 155)"
    :image="$song->cover_image"
    og-type="music.song"
>
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        @if (session('status'))
            <p class="mb-6 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </p>
        @endif

        @if (session('error'))
            <p class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </p>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div
                    class="group relative aspect-video w-full overflow-hidden rounded-lg bg-black"
                    x-data="shufflePlayer(@js($song->youtube_video_id), @js($nextSongUrl ?? route('shuffle.all')))"
                    x-init="init()"
                >
                    <div id="shuffle-youtube-player" class="h-full w-full"></div>

                    <x-youtube-player-controls />
                </div>

                <h1 class="mt-6 text-3xl font-semibold text-white">{{ $song->title }}</h1>
                <a href="{{ route('artists.show', $song->artist) }}" class="mt-1 inline-block text-neutral-400 hover:text-white hover:underline">
                    {{ $song->artist->name }}
                </a>

                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-neutral-500">
                    <x-genre-badge :genre="$song->genre" />

                    @if ($song->release_date)
                        <span>{{ $song->release_date->format('F j, Y') }}</span>
                    @endif

                    <span>{{ trans_choice(':count vote today|:count votes today', $todayVoteCount, ['count' => $todayVoteCount]) }}</span>
                </div>

                @if ($song->description)
                    <p class="mt-4 max-w-2xl text-neutral-300">{{ $song->description }}</p>
                @endif

                <div class="mt-6 flex items-center gap-3">
                    <x-vote-button :song="$song" :has-voted="$hasVotedToday" />

                    <a
                        href="{{ $nextSongUrl ?? route('shuffle.all') }}"
                        class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-neutral-300 hover:border-white/20 hover:text-white"
                    >
                        Next
                    </a>
                </div>
            </div>

            <div class="lg:col-span-1">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Up Next</h2>

                @if ($queue->isNotEmpty())
                    <div class="mt-3 space-y-1">
                        @foreach ($queue as $item)
                            <a href="{{ $item['url'] }}" class="flex items-center gap-3 rounded-lg p-2 hover:bg-white/5">
                                <img
                                    src="https://img.youtube.com/vi/{{ $item['song']->youtube_video_id }}/hqdefault.jpg"
                                    alt=""
                                    class="{{ $loop->first ? 'h-20 w-36' : 'h-12 w-20' }} shrink-0 rounded object-cover bg-neutral-800"
                                >
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-white">{{ $item['song']->title }}</p>
                                    <p class="truncate text-sm text-neutral-400">{{ $item['song']->artist->name }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-neutral-500">No other songs to play next yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
