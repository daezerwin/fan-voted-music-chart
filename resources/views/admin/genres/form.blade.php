<x-layouts.admin :title="$genre->exists ? 'Edit Genre' : 'New Genre'">
    <h1 class="text-2xl font-semibold text-ink">{{ $genre->exists ? 'Edit Genre' : 'New Genre' }}</h1>

    <form
        action="{{ $genre->exists ? route('admin.genres.update', $genre) : route('admin.genres.store') }}"
        method="POST"
        class="mt-6 max-w-xl space-y-5"
    >
        @csrf
        @if ($genre->exists) @method('PUT') @endif

        <div>
            <label for="name" class="block text-sm font-medium text-muted">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $genre->name) }}" required
                class="mt-1 w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-ink">
            @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-muted">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $genre->slug) }}" placeholder="Auto-generated from name if left blank"
                class="mt-1 w-full rounded-lg border border-white/10 bg-surface px-3 py-2 text-ink">
            @error('slug') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 font-medium text-ink hover:bg-primary/90">
            {{ $genre->exists ? 'Save Changes' : 'Create Genre' }}
        </button>
    </form>
</x-layouts.admin>
