@props(['title' => 'Admin'])

<x-layouts.app :title="$title.' — Admin — '.config('app.name')">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="mb-8 flex flex-wrap gap-4 border-b border-white/10 pb-4 text-sm font-medium text-neutral-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Dashboard</a>
            <a href="{{ route('admin.artists.index') }}" class="hover:text-white">Artists</a>
            <a href="{{ route('admin.songs.index') }}" class="hover:text-white">Songs</a>
            <a href="{{ route('admin.genres.index') }}" class="hover:text-white">Genres</a>
            <a href="{{ route('admin.votes.index') }}" class="hover:text-white">Votes</a>
            <a href="{{ route('admin.charts.index') }}" class="hover:text-white">Charts</a>
        </nav>

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

        {{ $slot }}
    </div>
</x-layouts.app>
