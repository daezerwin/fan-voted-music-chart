@props(['song', 'hasVoted' => false])

@auth
    @if (! $song->voting_enabled)
        <button type="button" disabled {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg bg-white/10 px-5 py-2.5 font-medium text-muted cursor-not-allowed']) }}>
            Voting Closed
        </button>
    @elseif ($hasVoted)
        <button type="button" disabled {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg bg-success/20 px-5 py-2.5 font-medium text-success cursor-default']) }}>
            Voted Today
        </button>
    @else
        <form action="{{ route('votes.store', $song) }}" method="POST">
            @csrf
            <button type="submit" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 font-medium text-ink hover:bg-primary/90']) }}>
                Vote
            </button>
        </form>
    @endif
@else
    <a href="{{ route('login') }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 font-medium text-ink hover:bg-primary/90']) }}>
        Sign In to Vote
    </a>
@endauth
