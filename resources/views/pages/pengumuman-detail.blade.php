@extends('layouts.app')

@section('title', $announcement['title'] ?? 'Detail Pengumuman')

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

<div class="mb-5" data-reveal>
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 rounded-lg border-3 border-ink bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide shadow-brutal-sm transition-all hover:-translate-y-0.5">
        <x-icon name="arrow-left" class="h-3.5 w-3.5" /> Kembali
    </a>
</div>

<x-card class="mb-4" data-reveal>
    <p class="font-mono text-xs font-medium text-ink/60">{{ $announcement['date'] ? \Carbon\Carbon::parse($announcement['date'])->translatedFormat('d F Y') : '' }}</p>
    <h1 class="mt-2 text-xl font-extrabold leading-tight">{{ $announcement['title'] ?? 'Pengumuman' }}</h1>
    <p class="mt-1 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>


    <div class="mt-4">
        @if(!empty($announcement['content_html']))
            <div class="prose prose-sm max-w-none font-medium leading-relaxed text-ink [&_br]:my-2">
                {!! nl2br(e($announcement['content'])) !!}
                {{-- Fallback raw html safely escaped, show as text with line breaks --}}
            </div>
        @elseif(!empty($announcement['content']))
            <p class="text-sm font-medium leading-relaxed">{{ $announcement['content'] }}</p>
        @else
            <x-empty-state icon="info" title="Konten tidak tersedia" description="Coba refresh atau buka lagi nanti." />
        @endif

        @if(!empty($announcement['attachment']))
            <div class="mt-6">
                <a href="{{ url('https://lms.iwima.ac.id/' . ltrim($announcement['attachment'], './')) }}" target="_blank" rel="noopener"
                class="inline-flex items-center gap-2 rounded-lg border-3 border-ink bg-primary px-4 py-2 text-xs font-bold uppercase tracking-wide shadow-brutal transition-all hover:-translate-y-0.5">
                    <x-icon name="download" class="h-4 w-4" /> Unduh Lampiran
                </a>
                <p class="mt-2 font-mono text-[10px] text-ink/60 break-all">{{ $announcement['attachment'] }}</p>
            </div>
        @endif
    </div>
</x-card>

    </div>
</div>
@endsection
