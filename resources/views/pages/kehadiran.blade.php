@extends('layouts.app')

@section('title', 'Kehadiran')

@section('content')
<div data-lms-load data-cached="{{ !empty($isCached) ? '1' : '0' }}">
    <div data-skeleton class="{{ !empty($isCached) ? 'hidden ' : '' }}space-y-4" aria-hidden="true">
        <div class="flex justify-between">
            <div class="skeleton h-6 w-1/3 rounded-lg"></div>
            <div class="skeleton h-3 w-20 rounded-lg"></div>
        </div>
        <div class="skeleton h-20 w-full rounded-lg border-3 border-ink/10"></div>
        <div class="space-y-3">
            <div class="skeleton h-24 w-full rounded-lg border-3 border-ink/10"></div>
            <div class="skeleton h-24 w-full rounded-lg border-3 border-ink/10"></div>
            <div class="skeleton h-24 w-full rounded-lg border-3 border-ink/10"></div>
        </div>
    </div>
    <div data-content class="{{ !empty($isCached) ? '' : 'hidden' }}">

<div class="mb-5 flex items-end justify-between gap-3" data-reveal>
    <div>
        <h1 class="text-2xl font-extrabold uppercase tracking-wide">Kehadiran</h1>
        <p class="mt-0.5 font-mono text-xs text-ink/70">@if($currentSemester)Semester {{ $currentSemester }} · @endif{{ $summary['tahun_akademik'] }}@if(!empty($summary['semester']))@endif</p>
    </div>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

@php
    $statusMeta = [
        'safe' => ['label' => 'Aman', 'color' => 'tertiary', 'bar' => 'bg-tertiary'],
        'warning' => ['label' => 'Perhatian', 'color' => 'primary', 'bar' => 'bg-primary'],
        'critical' => ['label' => 'Kritis', 'color' => 'secondary', 'bar' => 'bg-secondary'],
    ];
@endphp

{{-- Rata-rata kehadiran --}}
<x-card :color="$attendanceSummary['critical'] > 0 ? 'bg-secondary text-white' : 'bg-primary'" class="mb-3" data-reveal>
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest {{ $attendanceSummary['critical'] > 0 ? '' : '' }}">Rata-rata kehadiran</p>
            <p class="mt-1 font-mono text-5xl font-bold leading-none">{{ format_number((float) $attendanceSummary['avg'], 1) }}<span class="text-xl">%</span></p>
            <p class="mt-2 text-xs font-medium {{ $attendanceSummary['critical'] > 0 ? 'text-white/80' : 'text-ink/70' }}">
                {{ $attendanceSummary['total_courses'] }} mata kuliah
                @if ($attendanceSummary['critical'] > 0)
                    · {{ $attendanceSummary['critical'] }} berstatus kritis
                @endif
            </p>
        </div>
        <div class="grid h-16 w-16 rotate-2 place-items-center rounded-lg border-3 border-ink bg-white text-ink shadow-brutal">
            <x-icon name="clipboard-check" class="h-8 w-8" />
        </div>
    </div>
</x-card>

{{-- Legenda status --}}
<div class="mb-6 flex flex-wrap gap-2" data-reveal>
    <x-badge color="tertiary">Aman ≥ 85%</x-badge>
    <x-badge color="primary">Perhatian 75–84%</x-badge>
    <x-badge color="secondary">NDABLEG &lt; 75%</x-badge>
</div>

<x-section-title data-reveal>Per mata kuliah</x-section-title>
<div class="space-y-3">
    @foreach ($attendance as $item)
        @php $meta = $statusMeta[$item['status']]; @endphp
        <x-card :hover="true" data-reveal>
            <div class="mb-2 flex flex-wrap items-start justify-between gap-2">
                <div class="min-w-0">
                    <h3 class="text-sm font-bold leading-snug">{{ $item['course_name'] }}</h3>
                    <p class="font-mono text-[10px] font-medium text-ink/50">{{ $item['course_code'] }} · Kelas {{ $item['class_code'] }}</p>
                </div>
                <x-badge :color="$meta['color']">{{ $meta['label'] }}</x-badge>
            </div>

            <div class="mb-2.5 flex items-center gap-3">
                <div class="h-4 flex-1 overflow-hidden rounded-lg border-2 border-ink bg-cream">
                    <div class="{{ $meta['bar'] }} h-full border-r-2 border-ink" style="width: {{ max(4, $item['percentage']) }}%"></div>
                </div>
                <span class="font-mono text-sm font-bold">{{ format_number((float) $item['percentage'], 1) }}%</span>
            </div>

            <dl class="grid grid-cols-5 gap-1.5 text-center">
                <div class="rounded-lg border-2 border-ink bg-white py-1">
                    <dt class="text-[9px] font-bold uppercase tracking-wider text-ink/50">Hadir</dt>
                    <dd class="font-mono text-sm font-bold">{{ $item['present'] }}</dd>
                </div>
                <div class="rounded-lg border-2 border-ink bg-white py-1">
                    <dt class="text-[9px] font-bold uppercase tracking-wider text-ink/50">Sakit</dt>
                    <dd class="font-mono text-sm font-bold">{{ $item['sick'] }}</dd>
                </div>
                <div class="rounded-lg border-2 border-ink bg-white py-1">
                    <dt class="text-[9px] font-bold uppercase tracking-wider text-ink/50">Izin</dt>
                    <dd class="font-mono text-sm font-bold">{{ $item['permission'] }}</dd>
                </div>
                <div class="rounded-lg border-2 {{ $item['absent'] > 0 ? 'border-ink bg-secondary text-white' : 'border-ink bg-white' }} py-1">
                    <dt class="text-[9px] font-bold uppercase tracking-wider {{ $item['absent'] > 0 ? 'text-white/80' : 'text-ink/50' }}">Alpa</dt>
                    <dd class="font-mono text-sm font-bold">{{ $item['absent'] }}</dd>
                </div>
                <div class="rounded-lg border-2 border-ink bg-white py-1">
                    <dt class="text-[9px] font-bold uppercase tracking-wider text-ink/50">Temu</dt>
                    <dd class="font-mono text-sm font-bold">{{ $item['lecturer_total_meetings'] }}</dd>
                </div>
            </dl>
        </x-card>
    @endforeach
</div>
    </div>
</div>
@endsection
