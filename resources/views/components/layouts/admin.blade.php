@props(['title' => 'Admin'])

<x-layouts.app :title="$title.' — Admin — '.config('app.name')">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="mb-8 flex flex-wrap gap-4 border-b border-black/10 pb-4 text-sm font-medium text-muted">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-ink">Dashboard</a>
            <a href="{{ route('admin.artists.index') }}" class="hover:text-ink">Artists</a>
            <a href="{{ route('admin.songs.index') }}" class="hover:text-ink">Songs</a>
            <a href="{{ route('admin.genres.index') }}" class="hover:text-ink">Genres</a>
            <a href="{{ route('admin.votes.index') }}" class="hover:text-ink">Votes</a>
            <a href="{{ route('admin.charts.index') }}" class="hover:text-ink">Charts</a>
        </nav>

        @if (session('status'))
            <p class="mb-6 rounded-lg border border-success/30 bg-success/10 px-4 py-3 text-sm text-ink">
                {{ session('status') }}
            </p>
        @endif

        @if (session('error'))
            <p class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </p>
        @endif

        {{ $slot }}
    </div>
</x-layouts.app>
