<x-layouts.admin title="Artists">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-ink">Artists</h1>
        <a href="{{ route('admin.artists.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-ink hover:bg-primary/90">
            New Artist
        </a>
    </div>

    <div class="mt-6 divide-y divide-black/10 rounded-lg border border-black/10">
        @forelse ($artists as $artist)
            <div class="flex items-center justify-between gap-4 p-4">
                <div class="min-w-0">
                    <p class="truncate font-medium text-ink">{{ $artist->name }}</p>
                    <p class="text-sm text-muted">
                        {{ $artist->songs_count }} {{ Str::plural('song', $artist->songs_count) }}
                        @unless ($artist->is_active)
                            &middot; <span class="text-red-400">Inactive</span>
                        @endunless
                        @if ($artist->is_featured)
                            &middot; <span class="text-accent">Featured</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.artists.edit', $artist) }}" class="shrink-0 text-sm text-muted hover:text-ink">Edit</a>
            </div>
        @empty
            <div class="p-4 text-sm text-muted">No artists yet.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $artists->links() }}</div>
</x-layouts.admin>
