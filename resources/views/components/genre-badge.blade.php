@props(['genre'])

<a
    href="{{ route('genres.show', $genre) }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full bg-black/10 px-3 py-1 text-xs font-medium text-muted hover:bg-black/20 hover:text-ink']) }}
>
    {{ $genre->name }}
</a>
