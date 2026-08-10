<x-layouts.admin :title="$song->exists ? 'Edit Song' : 'New Song'">
    <h1 class="text-2xl font-semibold text-white">{{ $song->exists ? 'Edit Song' : 'New Song' }}</h1>

    <form
        action="{{ $song->exists ? route('admin.songs.update', $song) : route('admin.songs.store') }}"
        method="POST"
        class="mt-6 max-w-xl space-y-5"
    >
        @csrf
        @if ($song->exists) @method('PUT') @endif

        <div>
            <label for="title" class="block text-sm font-medium text-neutral-300">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $song->title) }}" required
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('title') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-neutral-300">Slug</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $song->slug) }}" placeholder="Auto-generated from title if left blank"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('slug') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="artist_id" class="block text-sm font-medium text-neutral-300">Artist</label>
                <select id="artist_id" name="artist_id" required
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
                    <option value="">Select an artist</option>
                    @foreach ($artists as $artist)
                        <option value="{{ $artist->id }}" @selected((int) old('artist_id', $song->artist_id) === $artist->id)>{{ $artist->name }}</option>
                    @endforeach
                </select>
                @error('artist_id') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="genre_id" class="block text-sm font-medium text-neutral-300">Genre</label>
                <select id="genre_id" name="genre_id" required
                    class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
                    <option value="">Select a genre</option>
                    @foreach ($genres as $genre)
                        <option value="{{ $genre->id }}" @selected((int) old('genre_id', $song->genre_id) === $genre->id)>{{ $genre->name }}</option>
                    @endforeach
                </select>
                @error('genre_id') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="youtube_url" class="block text-sm font-medium text-neutral-300">YouTube URL or video ID</label>
            <input id="youtube_url" name="youtube_url" type="text" value="{{ old('youtube_url', $song->youtube_video_id) }}" required
                placeholder="https://www.youtube.com/watch?v=... or a bare video ID"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('youtube_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="release_date" class="block text-sm font-medium text-neutral-300">Release Date</label>
            <input id="release_date" name="release_date" type="date" value="{{ old('release_date', optional($song->release_date)->format('Y-m-d')) }}"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('release_date') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="cover_image" class="block text-sm font-medium text-neutral-300">Cover Image URL</label>
            <input id="cover_image" name="cover_image" type="url" value="{{ old('cover_image', $song->cover_image) }}"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">
            @error('cover_image') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-neutral-300">Description</label>
            <textarea id="description" name="description" rows="4"
                class="mt-1 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white">{{ old('description', $song->description) }}</textarea>
            @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm text-neutral-300">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $song->exists ? $song->is_active : true)) class="rounded border-white/20 bg-white/5">
                Active
            </label>

            <label class="flex items-center gap-2 text-sm text-neutral-300">
                <input type="checkbox" name="voting_enabled" value="1" @checked(old('voting_enabled', $song->exists ? $song->voting_enabled : true)) class="rounded border-white/20 bg-white/5">
                Voting Enabled
            </label>

            <label class="flex items-center gap-2 text-sm text-neutral-300">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $song->is_featured)) class="rounded border-white/20 bg-white/5">
                Featured
            </label>
        </div>

        <button type="submit" class="rounded-lg bg-white px-5 py-2.5 font-medium text-neutral-950 hover:bg-neutral-200">
            {{ $song->exists ? 'Save Changes' : 'Create Song' }}
        </button>
    </form>
</x-layouts.admin>
