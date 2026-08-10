<x-layouts.app :title="'Artists — '.config('app.name')" description="Browse artists on the chart.">
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-white">Artists</h1>

        @if ($artists->isEmpty())
            <x-empty-state message="No artists yet." class="mt-6" />
        @else
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($artists as $artist)
                    <x-artist-card :artist="$artist" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $artists->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
