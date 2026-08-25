@props(['color' => 'white' ])

@php
    $colors = [
        'white' => 'bg-white text-ink',
        'primary' => 'bg-primary text-ink',
        'secondary' => 'bg-secondary text-white',
        'tertiary' => 'bg-tertiary text-white',
        'cream' => 'bg-cream text-ink',
        'ink' => 'bg-ink text-white',
    ];
    $style = $colors[$color] ?? $colors['white'];
@endphp

<span {{ $attributes->merge(['class' => $style . ' inline-flex items-center gap-1 rounded-lg border-2 border-ink px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide']) }}>{{ $slot }}</span>
