<x-layouts.app :title="'Sign Up — '.config('app.name')">
    <div class="mx-auto max-w-md px-4 py-24 sm:px-6 lg:px-8">
        <h1 class="text-center text-2xl font-semibold text-ink">Create an Account</h1>
        <p class="mt-2 text-center text-muted">Sign up to vote for your favorite songs.</p>

        <a
            href="{{ route('auth.redirect', 'facebook') }}"
            class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1877F2] px-4 py-3 font-medium text-ink hover:bg-[#1665d8]"
        >
            Continue with Facebook
        </a>

        <div class="my-6 flex items-center gap-3 text-xs text-muted">
            <span class="h-px flex-1 bg-black/10"></span>
            or
            <span class="h-px flex-1 bg-black/10"></span>
        </div>

        <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-muted">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    class="mt-1 w-full rounded-lg border border-black/10 bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none">
                @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-muted">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="mt-1 w-full rounded-lg border border-black/10 bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none">
                @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-muted">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 w-full rounded-lg border border-black/10 bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none">
                @error('password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-muted">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 w-full rounded-lg border border-black/10 bg-surface px-3 py-2 text-ink focus:border-primary focus:outline-none">
            </div>

            <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 font-medium text-ink hover:bg-primary/90">
                Create Account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-muted">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 hover:underline">Sign in</a>
        </p>
    </div>
</x-layouts.app>
