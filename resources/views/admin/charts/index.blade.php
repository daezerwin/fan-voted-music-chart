<x-layouts.admin title="Charts">
    <h1 class="text-2xl font-semibold text-white">Chart Tools</h1>

    <div class="mt-6 max-w-md rounded-lg border border-white/10 bg-white/5 p-6">
        <h2 class="font-medium text-white">Regenerate Daily Chart</h2>
        <p class="mt-1 text-sm text-neutral-500">
            Recalculates and replaces the chart snapshot for the given vote date. Safe to rerun.
        </p>

        <form action="{{ route('admin.charts.regenerate') }}" method="POST" class="mt-4 flex items-end gap-3">
            @csrf

            <div class="flex-1">
                <label for="date" class="block text-sm font-medium text-neutral-300">Vote date</label>
                <input id="date" name="date" type="date" required value="{{ old('date', now()->subDay()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
                @error('date') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-neutral-950 hover:bg-neutral-200">
                Regenerate
            </button>
        </form>
    </div>
</x-layouts.admin>
