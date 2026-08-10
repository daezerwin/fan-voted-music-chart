<x-layouts.app :title="'Genres — '.config('app.name')" description="Browse songs by genre.">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-white">Genres</h1>

        @if ($genres->isEmpty())
            <x-empty-state message="No genres yet." class="mt-6" />
        @else
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach ($genres as $genre)
                    <x-genre-badge :genre="$genre" class="text-sm" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
