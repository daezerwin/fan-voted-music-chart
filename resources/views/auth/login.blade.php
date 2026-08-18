<x-layouts.app :title="'Sign In — '.config('app.name')">
    <div class="mx-auto max-w-md px-4 py-24 sm:px-6 lg:px-8">
        <h1 class="text-center text-2xl font-semibold text-ink">Sign In</h1>
        <p class="mt-2 text-center text-muted">Sign in to vote for your favorite songs.</p>

        @if (session('error'))
            <p class="mt-6 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </p>
        @endif

        <a
            href="{{ route('auth.redirect', 'facebook') }}"
            class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1877F2] px-4 py-3 font-medium text-ink hover:bg-[#1665d8]"
        >
            Continue with Facebook
        </a>

        <div class="my-6 flex items-center gap-3 text-xs text-muted">
            <span class="h-px flex-1 bg-white/10"></span>
            or
            <span class="h-px flex-1 bg-white/10"></span>
        </div>

        <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-muted">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none">
                @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-muted">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none">
            </div>

            <label class="flex items-center gap-2 text-sm text-muted">
                <input type="checkbox" name="remember" value="1" class="rounded border-white/20 bg-surface">
                Remember me
            </label>

            <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 font-medium text-ink hover:bg-primary/90">
                Sign In
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-muted">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary hover:text-primary/80 hover:underline">Sign up</a>
        </p>
    </div>
</x-layouts.app>
