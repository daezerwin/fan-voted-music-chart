<x-layouts.admin :title="$artist->exists ? 'Edit Artist' : 'New Artist'">
    <h1 class="text-2xl font-semibold text-white">{{ $artist->exists ? 'Edit Artist' : 'New Artist' }}</h1>

    <form
        action="{{ $artist->exists ? route('admin.artists.update', $artist) : route('admin.artists.store') }}"
        method="POST"
        class="mt-6 max-w-xl space-y-5"
    >
        @csrf
        @if ($artist->exists) @method('PUT') @endif

        <div>
            <label for="name" class="block text-sm font-medium text-neutral-300">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $artist->name) }}" required
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-neutral-300">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $artist->slug) }}" placeholder="Auto-generated from name if left blank"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('slug') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="bio" class="block text-sm font-medium text-neutral-300">Bio</label>
            <textarea id="bio" name="bio" rows="4"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">{{ old('bio', $artist->bio) }}</textarea>
            @error('bio') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-neutral-300">Image URL</label>
            <input id="image" name="image" type="url" value="{{ old('image', $artist->image) }}"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('image') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="country" class="block text-sm font-medium text-neutral-300">Country</label>
                <input id="country" name="country" type="text" maxlength="2" value="{{ old('country', $artist->country) }}"
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
                @error('country') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="website" class="block text-sm font-medium text-neutral-300">Website</label>
                <input id="website" name="website" type="url" value="{{ old('website', $artist->website) }}"
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
                @error('website') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm text-neutral-300">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $artist->exists ? $artist->is_active : true)) class="rounded border-white/20 bg-white/5">
                Active
            </label>

            <label class="flex items-center gap-2 text-sm text-neutral-300">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $artist->is_featured)) class="rounded border-white/20 bg-white/5">
                Featured
            </label>
        </div>

        <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 font-medium text-white hover:bg-blue-600">
            {{ $artist->exists ? 'Save Changes' : 'Create Artist' }}
        </button>
    </form>
</x-layouts.admin>
