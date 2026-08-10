<x-layouts.admin title="Artists">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-white">Artists</h1>
        <a href="{{ route('admin.artists.create') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-neutral-950 hover:bg-neutral-200">
            New Artist
        </a>
    </div>

    <div class="mt-6 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($artists as $artist)
            <div class="flex items-center justify-between gap-4 p-4">
                <div class="min-w-0">
                    <p class="truncate font-medium text-white">{{ $artist->name }}</p>
                    <p class="text-sm text-neutral-500">
                        {{ $artist->songs_count }} {{ Str::plural('song', $artist->songs_count) }}
                        @unless ($artist->is_active)
                            &middot; <span class="text-red-400">Inactive</span>
                        @endunless
                        @if ($artist->is_featured)
                            &middot; <span class="text-sky-400">Featured</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.artists.edit', $artist) }}" class="shrink-0 text-sm text-neutral-400 hover:text-white">Edit</a>
            </div>
        @empty
            <div class="p-4 text-sm text-neutral-500">No artists yet.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $artists->links() }}</div>
</x-layouts.admin>
