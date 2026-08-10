<x-layouts.admin title="Songs">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-white">Songs</h1>
        <a href="{{ route('admin.songs.create') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-neutral-950 hover:bg-neutral-200">
            New Song
        </a>
    </div>

    <div class="mt-6 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($songs as $song)
            <div class="flex items-center justify-between gap-4 p-4">
                <div class="min-w-0">
                    <p class="truncate font-medium text-white">{{ $song->title }}</p>
                    <p class="text-sm text-neutral-500">
                        {{ $song->artist->name }} &middot; {{ $song->genre->name }}
                        @unless ($song->is_active)
                            &middot; <span class="text-red-400">Inactive</span>
                        @endunless
                        @unless ($song->voting_enabled)
                            &middot; <span class="text-amber-400">Voting Closed</span>
                        @endunless
                        @if ($song->is_featured)
                            &middot; <span class="text-sky-400">Featured</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.songs.edit', $song) }}" class="shrink-0 text-sm text-neutral-400 hover:text-white">Edit</a>
            </div>
        @empty
            <div class="p-4 text-sm text-neutral-500">No songs yet.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $songs->links() }}</div>
</x-layouts.admin>
