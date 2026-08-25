@props(['action' => null])

<div {{ $attributes->merge(['class' => 'mb-3 flex items-center justify-between gap-3']) }}>
    <h2 class="-rotate-1 rounded-lg border-3 border-ink bg-primary px-2.5 py-0.5 text-sm font-extrabold uppercase tracking-widest shadow-brutal-sm">{{ $slot }}</h2>
    @if ($action)
        <div>{{ $action }}</div>
    @endif
</div>
