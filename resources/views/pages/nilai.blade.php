@extends('layouts.app')

@section('title', 'Nilai')

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
        <h1 class="text-2xl font-extrabold uppercase tracking-wide">Nilai</h1>
        <p class="mt-0.5 font-mono text-xs text-ink/70">@if($currentSemester)Kumulatif sampai semester {{ $currentSemester }}@else Kumulatif @endif</p>
    </div>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

{{-- Ringkasan IPK --}}
<x-card color="bg-primary" class="mb-6" data-reveal>
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest">IPK Kumulatif</p>
            <p class="mt-1 font-mono text-5xl font-bold leading-none">{{ $ipkFormatted }}</p>
            <p class="mt-2 text-xs font-medium text-ink/70">Total {{ $gradesSummary['total_sks'] }} SKS · N×K {{ format_number((int) $gradesSummary['total_nxk'], 0) }}</p>
        </div>
        <div class="grid h-16 w-16 rotate-2 place-items-center rounded-lg border-3 border-ink bg-white shadow-brutal">
            <x-icon name="graduation" class="h-8 w-8" />
        </div>
    </div>
    <a href="{{ url('/khs') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg border-3 border-ink bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide shadow-brutal-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-brutal active:translate-x-0.5 active:translate-y-[2px] active:shadow-none">
        Buka KHS per semester <x-icon name="arrow-right" class="h-3.5 w-3.5" />
    </a>
</x-card>

{{-- Daftar nilai kumulatif --}}
<x-section-title data-reveal>Nilai kumulatif</x-section-title>
<div class="overflow-hidden rounded-lg border-3 border-ink bg-white shadow-brutal" data-reveal>
    <div class="hidden grid-cols-[2rem_1fr_3rem_3rem_4rem] gap-2 border-b-3 border-ink bg-cream px-3 py-2 text-[10px] font-bold uppercase tracking-widest md:grid-cols-[3rem_1fr_4rem_3.5rem_5rem] md:px-4">
        <span>No</span>
        <span>Mata kuliah</span>
        <span class="text-center">Grade</span>
        <span class="text-center">SKS</span>
        <span class="text-center">N×K</span>
    </div>
    <ol class="divide-y-2 divide-ink/10">
        @foreach ($grades as $i => $grade)
            <li class="grid grid-cols-[auto_1fr_auto] items-center gap-2 px-3 py-2.5 md:grid-cols-[3rem_1fr_4rem_3.5rem_5rem] md:gap-2 md:px-4" data-reveal>
                <span class="font-mono text-xs text-ink/50">{{ $loop->iteration }}.</span>
                <div class="min-w-0">
                    <p class="truncate text-xs font-bold md:text-sm">{{ $grade['course_name'] }}</p>
                    <p class="font-mono text-[10px] font-medium text-ink/50">{{ $grade['course_code'] }}</p>
                </div>
                <div class="flex items-center gap-2 md:contents">
                    <span class="grid h-7 w-7 place-items-center rounded-lg border-2 border-ink font-mono text-xs font-bold md:mx-auto {{ in_array($grade['grade'], ['A', 'A-']) ? 'bg-tertiary text-white' : ($grade['grade'] === 'B' ? 'bg-white' : ($grade['grade'] === 'C' ? 'bg-primary' : 'bg-secondary text-white')) }}">{{ $grade['grade'] }}</span>
                    <span class="font-mono text-xs md:text-center">{{ $grade['credits'] }} sks</span>
                    <span class="hidden font-mono text-xs md:block md:text-center">{{ $grade['weighted_credits'] }}</span>
                </div>
            </li>
        @endforeach
    </ol>
    <div class="flex items-center justify-between gap-2 border-t-3 border-ink bg-primary px-3 py-2.5 font-mono text-xs font-bold md:px-4">
        <span>TOTAL</span>
        <span>{{ $gradesSummary['total_sks'] }} SKS · N×K {{ $gradesSummary['total_nxk'] }} · IPK {{ $ipkFormatted }}</span>
    </div>
</div>
    </div>
</div>
@endsection
