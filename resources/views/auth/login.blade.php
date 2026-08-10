<x-layouts.app :title="'Sign In — '.config('app.name')">
    <div class="mx-auto max-w-md px-4 py-24 text-center sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-white">Sign In</h1>
        <p class="mt-2 text-neutral-400">Sign in to vote for your favorite songs.</p>

        @if (session('error'))
            <p class="mt-6 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </p>
        @endif

        <a
            href="{{ route('auth.redirect', 'facebook') }}"
            class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1877F2] px-4 py-3 font-medium text-white hover:bg-[#1665d8]"
        >
            Continue with Facebook
        </a>
    </div>
</x-layouts.app>
