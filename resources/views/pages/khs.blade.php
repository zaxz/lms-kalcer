@extends('layouts.app')

@section('title', 'Kartu Hasil Studi')

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
        <h1 class="text-2xl font-extrabold uppercase tracking-wide">KHS</h1>
        <p class="mt-0.5 font-mono text-xs text-ink/70">Kartu Hasil Studi per semester</p>
    </div>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

<div data-tabs data-reveal>
    <div class="mb-5 flex flex-wrap gap-2" role="tablist">
        @foreach ($khs as $index => $semester)
            <button type="button" role="tab" data-tab-target="{{ Str::slug($semester['semester']) }}"
                    data-tab-on="bg-primary" data-tab-off="bg-white hover:bg-primary/40"
                    class="rounded-lg border-3 border-ink px-3 py-1.5 text-xs font-bold uppercase tracking-wide shadow-brutal-sm transition-all duration-200 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none {{ $loop->last ? 'bg-primary' : 'bg-white hover:bg-primary/40' }}">
                {{ $semester['semester'] }}
            </button>
        @endforeach
    </div>

    @foreach ($khs as $semester)
        <section data-tab-panel="{{ Str::slug($semester['semester']) }}" class="{{ $loop->last ? '' : 'hidden' }}" role="tabpanel">
            <x-card color="bg-primary" class="mb-4" data-reveal>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest">{{ $semester['semester'] }}</p>
                        <p class="mt-1 font-mono text-3xl font-bold">IPS {{ format_number((float) $semester['ips']) }}</p>
                    </div>
                    <p class="text-right font-mono text-xs font-medium text-ink/70">{{ $semester['total_sks'] }} SKS<br>N×K {{ $semester['total_nxk'] }}</p>
                </div>
            </x-card>

            <div class="space-y-3">
                @foreach ($semester['items'] as $grade)
                    <x-card :hover="true" class="flex items-center gap-3" data-reveal>
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border-3 border-ink font-mono text-sm font-bold shadow-brutal-sm {{ in_array($grade['grade'], ['A', 'A-']) ? 'bg-tertiary text-white' : ($grade['grade'] === 'B' ? 'bg-white' : ($grade['grade'] === 'C' ? 'bg-primary' : 'bg-secondary text-white')) }}">{{ $grade['grade'] }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold md:text-sm">{{ $grade['course_name'] }}</p>
                            <p class="font-mono text-[10px] font-medium text-ink/50">{{ $grade['course_code'] }} · Bobot {{ format_number((float) $grade['weight'], 1) }}</p>
                        </div>
                        <div class="shrink-0 text-right font-mono">
                            <p class="text-sm font-bold">{{ $grade['credits'] }} <span class="text-[10px] font-medium text-ink/50">SKS</span></p>
                            <p class="text-[10px] font-medium text-ink/50">N×K {{ $grade['weighted_credits'] }}</p>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
    </div>
</div>
@endsection
