@extends('layouts.app')

@section('title', 'Profil')

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
    <h1 class="text-2xl font-extrabold uppercase tracking-wide">Profil</h1>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

{{-- Kartu identitas --}}
<x-card color="bg-primary" class="mb-5" data-reveal>
    <div class="flex items-center gap-4">
        <div class="shrink-0 -rotate-2 rounded-lg border-3 border-ink bg-white p-1 shadow-brutal">
            <img src="{{ route('photo') }}" alt="Foto {{ $student['full_name'] }}" class="h-16 w-16 rounded-md border-2 border-ink object-cover object-top"
                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($student['full_name'] ?? 'M') }}&background=FFEB3B&color=000&size=128&bold=true'">
        </div>
        <div class="min-w-0">
            <h2 class="truncate text-lg font-extrabold leading-tight">{{ $student['full_name'] }}</h2>
            <p class="font-mono text-xs font-medium text-ink/70">{{ $student['student_id'] }}</p>
            <p class="mt-1 text-xs font-bold">{{ $student['program'] }} · {{ $student['class'] }}</p>
        </div>
    </div>
</x-card>

{{-- Detail profil --}}
<x-section-title data-reveal>Data mahasiswa</x-section-title>
<x-card class="mb-6 p-0" data-reveal>
    <dl class="divide-y-2 divide-ink/10">
        @foreach ([
            ['NIM', $student['student_id']],
            ['Program Studi', $student['program']],
            ['Konsentrasi', $student['concentration']],
            ['Kelas', $student['class']],
            ['Angkatan', $student['cohort_year']],
            ['Dosen Pembimbing Akademik', $student['academic_advisor']],
        ] as [$label, $value])
            <div class="flex items-baseline justify-between gap-4 px-4 py-2.5">
                <dt class="shrink-0 text-xs font-medium text-ink/60">{{ $label }}</dt>
                <dd class="text-right text-xs font-bold md:text-sm">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>
</x-card>
{{-- Menu --}}
<x-section-title data-reveal>Menu</x-section-title>
<div class="space-y-3" data-reveal>
    @foreach ([
        ['/kmk', 'kmk', 'Kartu Mata Kuliah (KMK)', 'bg-white'],
        ['/point-book', 'book', 'Point Book', 'bg-white'],
        ['/pengumuman', 'megaphone', 'Pengumuman', 'bg-white'],
        ['/ujian', 'file-text', 'Jadwal Ujian', 'bg-white'],
        ['/khs', 'book', 'Kartu Hasil Studi', 'bg-white'],
        ['/nilai', 'chart', 'Nilai Kumulatif', 'bg-white'],
    ] as [$href, $icon, $label, $color])
        <a href="{{ url($href) }}"
           class="flex items-center justify-between gap-3 rounded-lg border-3 border-ink {{ $color }} p-4 text-sm font-bold shadow-brutal transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary hover:shadow-brutal-lg active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
            <span class="inline-flex items-center gap-3"><x-icon :name="$icon" class="h-5 w-5" /> {{ $label }}</span>
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    @endforeach

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="flex w-full items-center justify-between gap-3 rounded-lg border-3 border-ink bg-secondary p-4 text-sm font-bold text-white shadow-brutal transition-all duration-200 hover:-translate-y-0.5 hover:shadow-brutal-lg active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
            <span class="inline-flex items-center gap-3"><x-icon name="logout" class="h-5 w-5" /> Logout</span>
            <x-icon name="arrow-right" class="h-4 w-4" />
        </button>
    </form>

</div>
    </div>
</div>
@endsection
