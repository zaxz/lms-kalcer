@extends('layouts.app')

@section('title', 'Kartu Mata Kuliah')

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
        <h1 class="text-2xl font-extrabold uppercase tracking-wide">KMK</h1>
        <p class="mt-0.5 font-mono text-xs text-ink/70">Kartu Mata Kuliah — {{ $kmk['academic_year'] ?? '' }} · {{ $kmk['semester'] ?? '' }}</p>
    </div>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

{{-- Header info --}}
<x-card color="bg-primary" class="mb-6" data-reveal>
    <div class="flex items-center justify-between gap-4">
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-widest">{{ $kmk['program'] ?? '' }} · {{ $kmk['study_level'] ?? '' }}</p>
            <p class="mt-1 text-sm font-extrabold leading-tight truncate">{{ $kmk['name'] ?? '' }}</p>
            <p class="font-mono text-xs font-medium text-ink/70">{{ $kmk['nim'] ?? '' }}</p>
        </div>
        <div class="shrink-0 rounded-lg border-3 border-ink bg-white px-3 py-2 text-center shadow-brutal">
            <p class="font-mono text-2xl font-bold leading-none">{{ $kmk['total_credits'] }}</p>
            <p class="text-[10px] font-bold uppercase tracking-widest">SKS</p>
        </div>
    </div>
</x-card>

<x-section-title data-reveal>Daftar mata kuliah</x-section-title>

{{-- Tabel desktop --}}
<div class="hidden overflow-hidden rounded-lg border-3 border-ink bg-white shadow-brutal md:block" data-reveal>
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b-3 border-ink bg-cream text-[10px] font-bold uppercase tracking-widest">
                <th class="px-4 py-2.5">Kode</th>
                <th class="px-4 py-2.5">Mata Kuliah</th>
                <th class="px-4 py-2.5 text-center">SKS</th>
                <th class="px-4 py-2.5 text-center">Kelas</th>
                <th class="px-4 py-2.5">Jenis</th>
            </tr>
        </thead>
        <tbody class="divide-y-2 divide-ink/10">
            @foreach ($kmk['courses'] as $course)
                <tr>
                    <td class="whitespace-nowrap px-4 py-2.5 font-mono text-xs font-bold">{{ $course['course_code'] }}</td>
                    <td class="px-4 py-2.5 text-xs font-bold">{{ $course['course_name'] }}</td>
                    <td class="px-4 py-2.5 text-center font-mono text-sm font-bold">{{ $course['credits'] }}</td>
                    <td class="px-4 py-2.5 text-center font-mono text-xs">{{ $course['class_code'] }}</td>
                    <td class="px-4 py-2.5 text-xs"><x-badge color="cream">{{ $course['type'] }}</x-badge></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-3 border-ink bg-primary font-mono text-sm font-bold">
                <td class="px-4 py-2.5" colspan="2">JUMLAH SKS</td>
                <td class="px-4 py-2.5 text-center">{{ $kmk['total_credits'] }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- Kartu mobile --}}
<div class="space-y-3 md:hidden">
    @forelse ($kmk['courses'] as $course)
        <x-card :hover="true" data-reveal>
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold leading-snug">{{ $course['course_name'] }}</h3>
                    <p class="mt-1 font-mono text-[10px] font-medium text-ink/50">{{ $course['course_code'] }} · Kelas {{ $course['class_code'] }}</p>
                    <x-badge color="cream" class="mt-2">{{ $course['type'] }}</x-badge>
                </div>
                <div class="shrink-0 rounded-lg border-3 border-ink bg-primary px-3 py-1.5 text-center shadow-brutal-sm">
                    <p class="font-mono text-lg font-bold leading-none">{{ $course['credits'] }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-widest">SKS</p>
                </div>
            </div>
        </x-card>
    @empty
        <x-empty-state icon="book" title="Belum ada KMK" description="Kartu mata kuliah belum tersedia untuk semester ini." />
    @endforelse

    <x-card color="bg-primary" class="flex items-center justify-between" data-reveal>
        <span class="text-sm font-extrabold uppercase tracking-wide">Total SKS</span>
        <span class="font-mono text-xl font-bold">{{ $kmk['total_credits'] }}</span>
    </x-card>
</div>
    </div>
</div>
@endsection
