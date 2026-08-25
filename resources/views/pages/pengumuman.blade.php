@extends('layouts.app')

@section('title', 'Pengumuman')

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
    <h1 class="text-2xl font-extrabold uppercase tracking-wide">Pengumuman</h1>
    <p class="shrink-0 font-mono text-[10px] text-ink/60" data-updated-at>{{ $updatedAt }}</p>
</div>

<div class="space-y-3">
    @forelse ($announcements as $announcement)
        @php $annId = $announcement['id'] ?? null; @endphp
        <a @if($annId) href="{{ route('pengumuman.detail', $annId) }}" @else href="{{ url('https://lms.iwima.ac.id/' . ltrim($announcement['href'] ?? '', './')) }}" target="_blank" rel="noopener" @endif
           class="block rounded-lg border-3 border-ink bg-white p-4 shadow-brutal transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary hover:shadow-brutal-lg active:translate-x-[2px] active:translate-y-[2px] active:shadow-none group" data-reveal>
            <div class="flex items-start gap-3">
                <div class="mt-0.5 grid h-11 w-11 shrink-0 rotate-2 place-items-center rounded-lg border-2 border-ink bg-primary shadow-brutal-sm group-hover:rotate-0 transition-transform">
                    <x-icon name="megaphone" class="h-5 w-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-mono text-[10px] font-medium text-ink/60">{{ $announcement['date'] ? \Carbon\Carbon::parse($announcement['date'])->translatedFormat('d F Y') : ($announcement['date_raw'] ?? '-') }}</p>
                    <h2 class="mt-1.5 text-sm font-bold leading-snug group-hover:underline decoration-2 underline-offset-2">{{ $announcement['title'] }}</h2>
                    @if($annId)
                        <p class="mt-1.5 inline-flex items-center gap-1 font-mono text-[10px] font-bold text-ink/50 group-hover:text-ink/70">Tap untuk baca detail <x-icon name="arrow-right" class="h-3 w-3" /></p>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <x-empty-state icon="info" title="Belum ada pengumuman" description="Pengumuman terbaru akan tampil di sini." />
    @endforelse
</div>
    </div>
</div>
@endsection
