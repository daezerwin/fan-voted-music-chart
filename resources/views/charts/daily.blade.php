<x-layouts.app :title="'Daily Chart — '.$chart->chart_date->format('F j, Y')" description="The fan-voted daily Top chart.">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-white">Daily Chart</h1>
            <span class="text-sm text-neutral-500">{{ $chart->chart_date->format('F j, Y') }}</span>
        </div>

        <a href="{{ route('play') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-neutral-950 hover:bg-neutral-200">
            ▶ Play Top 10
        </a>

        @if ($chart->entries->isEmpty())
            <x-empty-state message="No votes were cast on this date." class="mt-6" />
        @else
            <div class="mt-6 space-y-2">
                @foreach ($chart->entries as $entry)
                    <x-chart-row :entry="$entry" :has-voted="$votedSongIds->contains($entry->song_id)" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
