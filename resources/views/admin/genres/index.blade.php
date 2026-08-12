<x-layouts.admin title="Genres">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-white">Genres</h1>
        <a href="{{ route('admin.genres.create') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
            New Genre
        </a>
    </div>

    <div class="mt-6 divide-y divide-white/10 rounded-lg border border-white/10">
        @forelse ($genres as $genre)
            <div class="flex items-center justify-between gap-4 p-4">
                <div class="min-w-0">
                    <p class="truncate font-medium text-white">{{ $genre->name }}</p>
                    <p class="text-sm text-neutral-500">{{ $genre->songs_count }} {{ Str::plural('song', $genre->songs_count) }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-4">
                    <a href="{{ route('admin.genres.edit', $genre) }}" class="text-sm text-neutral-400 hover:text-white">Edit</a>
                    <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST" onsubmit="return confirm('Delete this genre?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-4 text-sm text-neutral-500">No genres yet.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $genres->links() }}</div>
</x-layouts.admin>
