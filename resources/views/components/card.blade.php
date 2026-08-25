@props(['color' => 'bg-white', 'hover' => false])

<div {{ $attributes->merge(['class' => $color . ' rounded-lg border-3 border-ink p-4 shadow-brutal transition-all duration-200' . ($hover ? ' hover:-translate-y-0.5 hover:bg-primary hover:shadow-brutal-lg active:translate-x-[2px] active:translate-y-[2px] active:shadow-none cursor-pointer' : '')]) }}>
    {{ $slot }}
</div>
