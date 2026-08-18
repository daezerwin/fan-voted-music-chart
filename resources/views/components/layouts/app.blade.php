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
<body class="h-full bg-background text-ink antialiased">
    <div class="flex min-h-full flex-col">
        <header class="border-b border-black/10 bg-background/80 backdrop-blur">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Primary">
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-ink">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6 shrink-0 text-primary" aria-hidden="true">
                        <rect x="3" y="10" width="4.5" height="11" rx="1.5" fill="currentColor" />
                        <rect x="9.75" y="4" width="4.5" height="17" rx="1.5" fill="currentColor" />
                        <rect x="16.5" y="13" width="4.5" height="8" rx="1.5" fill="currentColor" />
                    </svg>
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
                        class="w-full rounded-full border border-black/10 bg-surface px-4 py-1.5 text-sm text-ink placeholder-muted focus:border-primary focus:outline-none"
                    >
                </form>

                <div class="flex items-center gap-6 text-sm font-medium text-muted">
                    <a href="{{ route('charts.daily') }}" class="hover:text-ink">Charts</a>
                    <a href="{{ route('artists.index') }}" class="hover:text-ink">Artists</a>
                    <a href="{{ route('genres.index') }}" class="hover:text-ink">Genres</a>
                    <a href="{{ route('shuffle.all') }}" class="hover:text-ink">Shuffle</a>

                    @auth
                        <span class="text-muted">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="hover:text-ink">Sign Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-ink">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-primary px-3 py-1.5 text-ink hover:bg-primary/90">Sign Up</a>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-black/10">
            <div class="mx-auto max-w-6xl px-4 py-8 text-sm text-muted sm:px-6 lg:px-8">
                <p>{{ config('app.name') }} is an independent, community-driven music chart. Not affiliated with Billboard.</p>
            </div>
        </footer>
    </div>
</body>
</html>
