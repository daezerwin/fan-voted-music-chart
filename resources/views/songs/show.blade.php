<x-layouts.app :title="$song->title.' — '.$song->artist->name" :description="Str::limit(strip_tags((string) $song->description), 155)">
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
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

            <div class="min-w-0 flex-1">
                <h1 class="text-3xl font-semibold text-white">{{ $song->title }}</h1>
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

                <div class="mt-6">
                    <x-vote-button :song="$song" :has-voted="$hasVotedToday" />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
