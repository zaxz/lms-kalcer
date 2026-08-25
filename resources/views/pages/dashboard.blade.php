@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div data-lms-load data-cached="{{ !empty($isCached) ? '1' : '0' }}">
        {{-- Skeleton loading awal --}}
        <div data-skeleton class="{{ !empty($isCached) ? 'hidden ' : '' }}space-y-5" aria-hidden="true">
            <div class="skeleton h-6 w-2/3 rounded-lg"></div>
            <div class="skeleton h-40 w-full rounded-lg border-3 border-ink/10"></div>
            <div class="grid grid-cols-2 gap-3">
                <div class="skeleton h-24 rounded-lg border-3 border-ink/10"></div>
                <div class="skeleton h-24 rounded-lg border-3 border-ink/10"></div>
                <div class="skeleton h-24 rounded-lg border-3 border-ink/10"></div>
                <div class="skeleton h-24 rounded-lg border-3 border-ink/10"></div>
            </div>
            <div class="skeleton h-32 w-full rounded-lg border-3 border-ink/10"></div>
        </div>

        {{-- Konten dashboard --}}
        <div data-content class="{{ !empty($isCached) ? '' : 'hidden' }}">
            {{-- 1. Header mahasiswa --}}
            <div class="mb-6 flex items-start justify-between gap-4" data-reveal>
                <div class="min-w-0">
                    <p class="text-xs font-bold tracking-widest text-ink/60">Haloo 👋</p>
                    <h1 class="text-2xl font-extrabold leading-tight md:text-4xl">{{ $student['full_name'] }}</h1>
                    <p class="mt-1 font-mono text-xs font-medium text-ink/70">{{ $student['program'] }} · Kelas
                        {{ $student['class'] }} · Semester {{ $summary['semester'] }}</p>
                    <p class="mt-1.5 font-mono text-[10px] font-medium text-ink/60" data-updated-at>{{ $updatedAt }}</p>
                </div>
                <div class="shrink-0 -rotate-2 rounded-lg border-3 border-ink bg-white p-1 shadow-brutal">
                    <img src="{{ route('photo') }}" alt="Foto {{ $student['full_name'] }}"
                        class="h-14 w-14 rounded-md border-2 border-ink object-cover object-top"
                        onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($student['full_name'] ?? 'M') }}&background=FFEB3B&color=000&size=128&bold=true'">
                </div>
            </div>

            {{-- 2. Kuliah hari ini --}}
            <section class="mb-7" data-reveal>
                <x-section-title>Kuliah hari ini ({{ $todayName }})</x-section-title>

                @if (count($todaySchedule))
                    <ol class="relative space-y-3 border-l-3 border-dashed border-ink/40 pl-4">
                        @foreach ($todaySchedule as $item)
                            <li class="relative" data-reveal>
                                <span
                                    class="absolute -left-[26.5px] top-4 h-5 w-5 -rotate-2 rounded border-2 border-ink bg-primary shadow-brutal-sm"></span>
                                <x-card :hover="true" class="flex gap-3">
                                    <div
                                        class="shrink-0 rounded-lg border-2 border-ink bg-cream px-2 py-1 text-center font-mono">
                                        <p class="text-sm font-bold leading-tight">{{ $item['start_time'] }}</p>
                                        <p class="text-[10px] font-medium leading-tight text-ink/60">
                                            {{ $item['end_time'] }}</p>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <h3 class="text-sm font-bold leading-snug">{{ $item['course_name'] }}</h3>
                                            <x-badge :color="$item['type'] === 'Praktikum' ? 'tertiary' : 'cream'">{{ $item['type'] }}</x-badge>
                                        </div>
                                        <p
                                            class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs font-medium text-ink/70">
                                            <span class="inline-flex items-center gap-1"><x-icon name="map-pin"
                                                    class="h-3.5 w-3.5" /> {{ $item['room'] }}</span>
                                            <span class="truncate">{{ $item['lecturer'] }}</span>
                                        </p>
                                    </div>
                                </x-card>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <x-empty-state title="Tidak ada kuliah hari ini"
                        description="Nikmati harimu — atau cek jadwal minggu ini." icon="moon">
                        <a href="{{ url('/jadwal') }}"
                            class="mt-4 inline-flex items-center gap-1.5 rounded-lg border-3 border-ink bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide shadow-brutal-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-brutal active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                            Lihat jadwal <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>
                    </x-empty-state>
                @endif
            </section>

            {{-- 3. Jadwal ujian terdekat --}}
            @if (count($upcomingExams))
                <section class="mb-7" data-reveal>
                    <x-section-title data-reveal>
                        Ujian terdekat
                        <x-slot:action>
                            <a href="{{ url('/ujian') }}"
                                class="text-xs font-bold underline decoration-2 underline-offset-2 hover:bg-primary">Semua</a>
                        </x-slot:action>
                    </x-section-title>

                    <div class="space-y-3">
                        @foreach ($upcomingExams as $exam)
                            <x-card color="bg-primary" :hover="true" class="flex items-center gap-3" data-reveal>
                                <div
                                    class="shrink-0 rounded-lg border-2 border-ink bg-white px-2.5 py-1.5 text-center font-mono">
                                    <p class="text-lg font-bold leading-none">
                                        {{ \Carbon\Carbon::parse($exam['date'])->format('d') }}</p>
                                    <p class="text-[10px] font-medium uppercase">
                                        {{ \Carbon\Carbon::parse($exam['date'])->translatedFormat('M') }}</p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <x-badge color="secondary">{{ $exam['exam_type'] }}</x-badge>
                                        <h3 class="text-sm font-bold leading-snug">{{ $exam['course_name'] }}</h3>
                                    </div>
                                    <p class="mt-1 text-xs font-medium text-ink/70">
                                        {{ $exam['day'] }} · {{ $exam['start_time'] }}–{{ $exam['end_time'] }} ·
                                        {{ $exam['room'] }} · Kursi {{ $exam['seat_number'] }}
                                    </p>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 4. Ringkasan akademik --}}
            <section class="mb-7" data-reveal>
                <x-section-title data-reveal>Ringkasan akademik</x-section-title>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    <x-card color="bg-primary" class="text-center" data-reveal>
                        <p class="text-[10px] font-bold uppercase tracking-widest">IPK</p>
                        <p class="mt-1 font-mono text-3xl font-bold">{{ $summary['ipk'] }}</p>
                        <p class="mt-1 text-[10px] font-medium text-ink/70">Kumulatif</p>
                    </x-card>
                    <x-card class="text-center" data-reveal>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-ink/60">Total SKS</p>
                        <p class="mt-1 font-mono text-3xl font-bold">{{ $summary['total_sks'] }}</p>
                        <p class="mt-1 text-[10px] font-medium text-ink/70">SKS lulus</p>
                    </x-card>
                    <x-card color="bg-tertiary" class="text-center text-white" data-reveal>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Semester</p>
                        <p class="mt-1 font-mono text-xl font-bold">{{ $summary['semester'] }}</p>
                        <p class="mt-1 text-[10px] font-medium">{{ $summary['tahun_akademik'] }}</p>
                    </x-card>
                    <x-card :color="$attendanceStatus === 'safe' ? 'bg-white' : 'bg-secondary'" class="text-center {{ $attendanceStatus === 'safe' ? '' : 'text-white' }}"
                        data-reveal>
                        <p
                            class="text-[10px] font-bold uppercase tracking-widest {{ $attendanceStatus === 'safe' ? 'text-ink/60' : '' }}">
                            Kehadiran</p>
                        <p class="mt-1 font-mono text-3xl font-bold">{{ $summary['attendance_avg'] }}<span
                                class="text-base">%</span></p>
                        <p class="mt-1 text-[10px] font-medium {{ $attendanceStatus === 'safe' ? 'text-ink/70' : '' }}">
                            Rata-rata</p>
                    </x-card>
                </div>
            </section>

            {{-- 5. Shortcut fitur --}}
            <section class="mb-7" data-reveal>
                <x-section-title data-reveal>Akses cepat</x-section-title>
                <div class="grid grid-cols-3 gap-3">
                    @foreach ([['/jadwal', 'calendar', 'Jadwal Kuliah', 'bg-white'], ['/ujian', 'file-text', 'Jadwal Ujian', 'bg-white'], ['/nilai', 'chart', 'Nilai', 'bg-white'], ['/kehadiran', 'clipboard-check', 'Kehadiran', 'bg-white'], ['/khs', 'book', 'KHS', 'bg-white'], ['/profil', 'user', 'Profil', 'bg-white']] as $i => [$href, $icon, $label, $color])
                        <a href="{{ url($href) }}" data-reveal
                            class="group flex flex-col items-center gap-2 rounded-lg border-3 border-ink {{ $color }} p-3 text-center shadow-brutal transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary hover:shadow-brutal-lg active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                            <x-icon :name="$icon" class="h-6 w-6" />
                            <span
                                class="text-[11px] font-bold uppercase tracking-wide leading-tight">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- 6. Pengumuman terbaru --}}
            <section data-reveal>
                <x-section-title>
                    Pengumuman
                    <x-slot:action>
                        <a href="{{ url('/pengumuman') }}"
                            class="text-xs font-bold underline decoration-2 underline-offset-2 hover:bg-primary">Semua</a>
                    </x-slot:action>
                </x-section-title>

                <div class="space-y-3">
                    @foreach (array_slice($announcements, 0, 3) as $announcement)
                        @php $annId = $announcement['id'] ?? null; @endphp
                        <a @if ($annId) href="{{ route('pengumuman.detail', $annId) }}" @else href="{{ route('pengumuman') }}" @endif
                            class="block rounded-lg border-3 border-ink bg-white p-4 shadow-brutal transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary hover:shadow-brutal-lg active:translate-x-[2px] active:translate-y-[2px] active:shadow-none group"
                            data-reveal>
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 grid h-9 w-9 shrink-0 rotate-2 place-items-center rounded-lg border-2 border-ink bg-primary shadow-brutal-sm group-hover:rotate-0 transition-transform">
                                    <x-icon name="megaphone" class="h-4.5 w-4.5" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-mono text-[10px] font-medium text-ink/60">
                                        {{ \Carbon\Carbon::parse($announcement['date'])->translatedFormat('d F Y') }}</p>
                                    <h3
                                        class="mt-0.5 text-xs font-bold leading-snug group-hover:underline decoration-2 underline-offset-2">
                                        {{ $announcement['title'] }}</h3>
                                    @if ($annId)
                                        <p
                                            class="mt-1 font-mono text-[10px] font-bold text-ink/40 group-hover:text-ink/70">
                                            Tap untuk baca detail →</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
