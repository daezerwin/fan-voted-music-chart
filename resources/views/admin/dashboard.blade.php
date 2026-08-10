<x-layouts.admin title="Dashboard">
    <h1 class="text-2xl font-semibold text-white">Dashboard</h1>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($stats as $label => $value)
            <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                <p class="text-2xl font-semibold text-white">{{ number_format($value) }}</p>
                <p class="text-sm text-neutral-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>
</x-layouts.admin>
