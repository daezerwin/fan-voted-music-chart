<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $title ?? config('app.name');
        $pageDescription = $description ?? 'An independent, community-driven music chart.';
        $pageUrl = $canonical ?? url()->current();
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $pageUrl }}">

    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    @isset($image)
        <meta property="og:image" content="{{ $image }}">
    @endisset

    <meta name="twitter:card" content="{{ isset($image) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-neutral-950 text-neutral-100 antialiased">
    <div class="flex min-h-full flex-col">
        <header class="border-b border-white/10 bg-neutral-950/80 backdrop-blur">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Primary">
                <a href="{{ url('/') }}" class="text-lg font-semibold tracking-tight text-white">
                    {{ config('app.name') }}
                </a>

                <form action="{{ route('search') }}" method="GET" class="hidden flex-1 max-w-sm px-6 sm:block">
                    <label for="nav-search" class="sr-only">Search artists and songs</label>
                    <input
                        id="nav-search"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search artists and songs"
                        class="w-full rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-sm text-white placeholder-neutral-500 focus:border-white/30 focus:outline-none"
                    >
                </form>

                <div class="flex items-center gap-6 text-sm font-medium text-neutral-300">
                    <a href="{{ route('charts.daily') }}" class="hover:text-white">Charts</a>
                    <a href="{{ route('artists.index') }}" class="hover:text-white">Artists</a>
                    <a href="{{ route('genres.index') }}" class="hover:text-white">Genres</a>

                    @auth
                        <span class="text-neutral-400">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="hover:text-white">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-white">Sign In</a>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-white/10">
            <div class="mx-auto max-w-6xl px-4 py-8 text-sm text-neutral-500 sm:px-6 lg:px-8">
                <p>{{ config('app.name') }} is an independent, community-driven music chart. Not affiliated with Billboard.</p>
            </div>
        </footer>
    </div>
</body>
</html>
