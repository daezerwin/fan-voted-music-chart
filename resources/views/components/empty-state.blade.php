@props(['message' => 'Nothing to show yet.'])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-black/10 px-6 py-12 text-center text-muted']) }}>
    {{ $message }}
</div>
