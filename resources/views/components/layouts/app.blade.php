<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-neutral-950 text-neutral-100 antialiased">
    <div class="flex min-h-full flex-col">
        <header class="border-b border-white/10 bg-neutral-950/80 backdrop-blur">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Primary">
                <a href="{{ url('/') }}" class="text-lg font-semibold tracking-tight text-white">
                    {{ config('app.name') }}
                </a>

                <div class="flex items-center gap-6 text-sm font-medium text-neutral-300">
                    <a href="{{ url('/') }}" class="hover:text-white">Charts</a>

                    @auth
                        <span class="text-neutral-400">{{ auth()->user()->name }}</span>
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
