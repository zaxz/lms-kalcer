@props(['icon' => 'moon', 'title' => 'Tidak ada data', 'description' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg border-3 border-dashed border-ink/50 bg-cream p-6 text-center']) }}>
    <div class="mx-auto mb-3 grid h-12 w-12 rotate-2 place-items-center rounded-lg border-3 border-ink bg-primary shadow-brutal-sm">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>
    <p class="text-sm font-bold">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 text-xs text-ink/70">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
