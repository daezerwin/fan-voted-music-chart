@props(['genre'])

<a
    href="{{ route('genres.show', $genre) }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-neutral-300 hover:bg-white/20 hover:text-white']) }}
>
    {{ $genre->name }}
</a>
