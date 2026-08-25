@extends('layouts.app')

@section('title', 'Jadwal Ujian')

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
        <h1 class="text-2xl font-extrabold uppercase tracking-wide">Jadwal Ujian</h1>
        <p class="mt-0.5 font-mono text-xs text-ink/70">{{ $summary['tahun_akademik'] }}</p>
    </div>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

<div data-tabs data-reveal>
    {{-- Segmented control UTS / UAS --}}
    <div class="mb-5 grid max-w-xs grid-cols-2 rounded-lg border-3 border-ink bg-white p-1 shadow-brutal" role="tablist">
        @foreach (['UTS', 'UAS'] as $type)
            <button type="button" role="tab" data-tab-target="{{ $type }}"
                    data-tab-on="bg-primary border-2 border-ink shadow-brutal-sm -rotate-1 text-ink" data-tab-off="text-ink/60 hover:text-ink"
                    class="rounded-lg px-3 py-2 text-sm font-extrabold uppercase tracking-widest transition-colors duration-200 {{ $type === 'UTS' ? 'bg-primary border-2 border-ink shadow-brutal-sm -rotate-1' : 'text-ink/60 hover:text-ink' }}">
                {{ $type }}
            </button>
        @endforeach
    </div>

    @foreach ($exams as $type => $items)
        <section data-tab-panel="{{ $type }}" class="{{ $type === 'UTS' ? '' : 'hidden' }}" role="tabpanel">
            @if (count($items))
                @php
                    $nearest = collect($items)->where('date', '>=', date('Y-m-d'))->sortBy('date')->first();
                @endphp
                <div class="space-y-3">
                    @foreach ($items as $exam)
                        @php
                            $isPast = $exam['date'] < date('Y-m-d');
                            $isNext = $nearest && $exam['date'] === $nearest['date'] && $exam['course_code'] === $nearest['course_code'];
                        @endphp
                        <x-card :color="$isPast ? 'bg-cream' : 'bg-white'" :hover="! $isPast" class="{{ $isPast ? 'opacity-60' : '' }}" data-reveal>
                            <div class="flex gap-3">
                                <div class="w-14 shrink-0 rounded-lg border-2 {{ $isNext ? 'border-ink bg-primary shadow-brutal-sm' : 'border-ink bg-cream' }} px-1 py-1.5 text-center font-mono">
                                    <p class="text-xl font-bold leading-none">{{ \Carbon\Carbon::parse($exam['date'])->format('d') }}</p>
                                    <p class="text-[10px] font-medium uppercase">{{ \Carbon\Carbon::parse($exam['date'])->translatedFormat('M') }}</p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h3 class="text-sm font-bold leading-snug">{{ $exam['course_name'] }}</h3>
                                        @if ($isNext)
                                            <x-badge color="primary">Terdekat</x-badge>
                                        @endif
                                        @if ($isPast)
                                            <x-badge color="ink">Selesai</x-badge>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 font-mono text-[11px] font-medium text-ink/60">{{ $exam['course_code'] }} · Kelas {{ $exam['class_code'] }}</p>
                                    <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-medium text-ink/70">
                                        <span class="inline-flex items-center gap-1"><x-icon name="clock" class="h-3.5 w-3.5" /> {{ $exam['day'] }}, {{ $exam['start_time'] }}–{{ $exam['end_time'] }}</span>
                                        <span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="h-3.5 w-3.5" /> {{ $exam['room'] }}</span>
                                    </p>
                                    <p class="mt-1 text-xs font-medium text-ink/70">{{ $exam['lecturer'] }}</p>
                                    <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                        <x-badge color="white">Kursi {{ $exam['seat_number'] }}</x-badge>
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @else
                <x-empty-state icon="file-text" title="Belum ada jadwal {{ $type }}" description="Jadwal biasanya diumumkan 2 minggu sebelum masa ujian." />
            @endif
        </section>
    @endforeach
</div>
    </div>
</div>
@endsection
