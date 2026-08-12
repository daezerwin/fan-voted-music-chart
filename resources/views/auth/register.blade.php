<x-layouts.app :title="'Sign Up — '.config('app.name')">
    <div class="mx-auto max-w-md px-4 py-24 sm:px-6 lg:px-8">
        <h1 class="text-center text-2xl font-semibold text-white">Create an Account</h1>
        <p class="mt-2 text-center text-neutral-400">Sign up to vote for your favorite songs.</p>

        <a
            href="{{ route('auth.redirect', 'facebook') }}"
            class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1877F2] px-4 py-3 font-medium text-white hover:bg-[#1665d8]"
        >
            Continue with Facebook
        </a>

        <div class="my-6 flex items-center gap-3 text-xs text-neutral-500">
            <span class="h-px flex-1 bg-white/10"></span>
            or
            <span class="h-px flex-1 bg-white/10"></span>
        </div>

        <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-neutral-300">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-neutral-300">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-300">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                @error('password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-neutral-300">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full rounded-lg bg-blue-700 px-4 py-2.5 font-medium text-white hover:bg-blue-600">
                Create Account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-neutral-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 hover:underline">Sign in</a>
        </p>
    </div>
</x-layouts.app>
