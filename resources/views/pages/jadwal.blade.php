@extends('layouts.app')

@section('title', 'Jadwal Kuliah')

@section('content')
<div data-lms-load data-cached="{{ !empty($isCached) ? '1' : '0' }}">
    <div data-skeleton class="{{ !empty($isCached) ? 'hidden ' : '' }}space-y-4" aria-hidden="true">
        <div class="flex justify-between">
            <div class="skeleton h-6 w-1/3 rounded-lg"></div>
            <div class="skeleton h-3 w-20 rounded-lg"></div>
        </div>
        <div class="flex gap-2 overflow-hidden">
            <div class="skeleton h-9 w-20 rounded-lg"></div>
            <div class="skeleton h-9 w-20 rounded-lg"></div>
            <div class="skeleton h-9 w-20 rounded-lg"></div>
            <div class="skeleton h-9 w-20 rounded-lg"></div>
        </div>
        <div class="space-y-3">
            <div class="skeleton h-24 w-full rounded-lg border-3 border-ink/10"></div>
            <div class="skeleton h-24 w-full rounded-lg border-3 border-ink/10"></div>
            <div class="skeleton h-24 w-full rounded-lg border-3 border-ink/10"></div>
        </div>
    </div>
    <div data-content class="{{ !empty($isCached) ? '' : 'hidden' }}">
<div class="mb-5 flex items-end justify-between gap-3" data-reveal>
    <div>
        <h1 class="text-2xl font-extrabold uppercase tracking-wide">Jadwal Kuliah</h1>
        <p class="mt-0.5 font-mono text-xs text-ink/70">{{ $summary['tahun_akademik'] }}</p>
    </div>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

<div data-tabs data-reveal>
    {{-- Tab hari --}}
    <div class="sticky top-[60px] z-40 -mx-4 mb-4 overflow-x-auto bg-cream px-4 py-2" role="tablist">
        <div class="flex w-max gap-2">
            @foreach ($schedule as $day => $items)
                <button type="button" role="tab" data-tab-target="{{ $day }}"
                        data-tab-on="bg-primary" data-tab-off="bg-white hover:bg-primary/40"
                        class="rounded-lg border-3 border-ink px-3 py-1.5 text-xs font-bold uppercase tracking-wide shadow-brutal-sm transition-all duration-200 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none {{ $day === $activeDay ? 'bg-primary' : 'bg-white hover:bg-primary/40' }} {{ $day === $todayName ? 'ring-2 ring-secondary ring-offset-2 ring-offset-cream' : '' }}">
                    {{ $day }}
                    <span class="ml-1 font-mono text-[10px] font-medium">{{ count($items) }}</span>
                    @if ($day === $todayName)
                        <span class="sr-only">(hari ini)</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    @foreach ($schedule as $day => $items)
        <section data-tab-panel="{{ $day }}" class="{{ $day === $activeDay ? '' : 'hidden' }}" role="tabpanel">
            @if (count($items))
                <div class="space-y-3">
                    @foreach ($items as $item)
                        <x-card :hover="true" data-reveal>
                            <div class="flex gap-3">
                                <div class="w-16 shrink-0 rounded-lg border-2 border-ink bg-primary px-1 py-2 text-center font-mono">
                                    <p class="text-sm font-bold leading-tight">{{ $item['start_time'] }}</p>
                                    <div class="mx-auto my-1 h-px w-6 bg-ink/40"></div>
                                    <p class="text-[10px] font-medium leading-tight">{{ $item['end_time'] }}</p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h3 class="text-sm font-bold leading-snug">{{ $item['course_name'] }}</h3>
                                        <x-badge :color="$item['type'] === 'Praktikum' ? 'tertiary' : 'cream'">{{ $item['type'] }}</x-badge>
                                    </div>
                                    <p class="mt-0.5 font-mono text-[11px] font-medium text-ink/60">{{ $item['course_code'] }} · Kelas {{ $item['class_code'] }}</p>
                                    <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-medium text-ink/70">
                                        <span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="h-3.5 w-3.5" /> {{ $item['room'] }}</span>
                                        <span class="inline-flex items-center gap-1"><x-icon name="user" class="h-3.5 w-3.5" /> {{ $item['lecturer'] }}</span>
                                    </p>
                                    @if ($item['google_classroom_id'])
                                        <p class="mt-1.5 font-mono text-[10px] font-medium text-ink/50">Classroom: {{ $item['google_classroom_id'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @else
                <x-empty-state title="Tidak ada kuliah hari {{ $day }}" description="Hari ini bebas, bisa dipakai nugas." icon="moon" />
            @endif
        </section>
    @endforeach
</div>
    </div>
</div>
@endsection
