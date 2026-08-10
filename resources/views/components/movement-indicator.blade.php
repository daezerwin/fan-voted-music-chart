@props(['entry'])

@if ($entry->isNewEntry())
    <span class="text-xs font-semibold text-sky-400">NEW</span>
@elseif ($entry->isReentry())
    <span class="text-xs font-semibold text-purple-400">RE-ENTRY</span>
@elseif ($entry->movement > 0)
    <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-400">
        <span aria-hidden="true">▲</span>{{ $entry->movement }}
    </span>
@elseif ($entry->movement < 0)
    <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-red-400">
        <span aria-hidden="true">▼</span>{{ abs($entry->movement) }}
    </span>
@else
    <span class="text-xs font-semibold text-neutral-500">
        <span aria-hidden="true">—</span>
        <span class="sr-only">No change</span>
    </span>
@endif
