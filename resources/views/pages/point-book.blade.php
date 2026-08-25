@extends('layouts.app')

@section('title', 'Point Book')

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
        <h1 class="text-2xl font-extrabold uppercase tracking-wide">Point Book</h1>
        <p class="mt-0.5 font-mono text-xs text-ink/70">Catatan poin kegiatan kemahasiswaan</p>
    </div>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

{{-- Total poin --}}
<x-card color="bg-tertiary" class="mb-6 text-white" data-reveal>
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest">Total Poin</p>
            <p class="mt-1 font-mono text-5xl font-bold leading-none">{{ $pointBook['total_points'] }}</p>
        </div>
        <div class="grid h-16 w-16 rotate-2 place-items-center rounded-lg border-3 border-ink bg-white text-ink shadow-brutal">
            <x-icon name="book" class="h-8 w-8" />
        </div>
    </div>
</x-card>

<x-section-title data-reveal>Riwayat kegiatan</x-section-title>

{{-- Tabel desktop --}}
<div class="hidden overflow-hidden rounded-lg border-3 border-ink bg-white shadow-brutal md:block" data-reveal>
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b-3 border-ink bg-cream text-[10px] font-bold uppercase tracking-widest">
                <th class="px-4 py-2.5">Tanggal</th>
                <th class="px-4 py-2.5">Nama Kegiatan</th>
                <th class="px-4 py-2.5 text-center">Poin</th>
                <th class="px-4 py-2.5">Keterangan</th>
            </tr>
        </thead>
        <tbody class="divide-y-2 divide-ink/10">
            @foreach ($pointBook['entries'] as $entry)
                <tr>
                    <td class="whitespace-nowrap px-4 py-2.5 font-mono text-xs">{{ \Carbon\Carbon::parse($entry['date'])->format('d-m-Y') }}</td>
                    <td class="px-4 py-2.5 text-xs font-bold">{{ $entry['activity'] }}</td>
                    <td class="px-4 py-2.5 text-center font-mono text-sm font-bold">{{ $entry['points'] }}</td>
                    <td class="whitespace-nowrap px-4 py-2.5 text-xs font-medium text-ink/70">{{ $entry['note'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-3 border-ink bg-primary font-mono text-sm font-bold">
                <td class="px-4 py-2.5" colspan="2">Total Poin</td>
                <td class="px-4 py-2.5 text-center">{{ $pointBook['total_points'] }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- Kartu mobile --}}
<div class="space-y-3 md:hidden">
    @foreach ($pointBook['entries'] as $entry)
        <x-card :hover="true" data-reveal>
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="font-mono text-[10px] font-medium text-ink/60">{{ \Carbon\Carbon::parse($entry['date'])->format('d-m-Y') }}</p>
                    <h3 class="mt-0.5 text-sm font-bold leading-snug">{{ $entry['activity'] }}</h3>
                    <x-badge color="cream" class="mt-2">{{ $entry['note'] }}</x-badge>
                </div>
                <div class="shrink-0 rounded-lg border-2 border-ink bg-primary px-2.5 py-1 font-mono text-sm font-bold shadow-brutal-sm">
                    +{{ $entry['points'] }}
                </div>
            </div>
        </x-card>
    @endforeach

    <x-card color="bg-primary" class="flex items-center justify-between" data-reveal>
        <span class="text-sm font-extrabold uppercase tracking-wide">Total Poin</span>
        <span class="font-mono text-xl font-bold">{{ $pointBook['total_points'] }}</span>
    </x-card>
</div>
    </div>
</div>
@endsection
