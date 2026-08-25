<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#FFEB3B">
    <title>@yield('title', 'Portal Mahasiswa') — IWIMA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-cream font-sans text-ink antialiased">
    <div class="flex min-h-[100dvh] flex-col">
        <header class="sticky top-0 z-[100] border-b-3 border-ink bg-primary">
            <div class="mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 py-2.5">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-iwima.svg') }}" alt="Logo IWIMA"
                        class="h-9 w-9 -rotate-2 rounded-lg border-3 border-ink bg-white p-1 shadow-brutal-sm object-contain">
                    <span class="text-sm font-extrabold uppercase tracking-widest">LMS Kalcer</span>
                </a>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
                    @foreach ([['/', 'Beranda'], ['/jadwal', 'Jadwal'], ['/ujian', 'Ujian'], ['/nilai', 'Nilai'], ['/kehadiran', 'Kehadiran'], ['/profil', 'Profil']] as [$href, $label])
                        <a href="{{ url($href) }}"
                            class="rounded-lg border-2 px-3 py-1 text-xs font-bold uppercase tracking-wide transition-all duration-200 {{ request()->is(ltrim($href, '/') ?: '/') ? 'border-ink bg-white shadow-brutal-sm' : 'border-transparent hover:border-ink hover:bg-white/60' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                <button type="button" data-refresh aria-label="Refresh data"
                    class="grid h-9 w-9 place-items-center rounded-lg border-3 border-ink bg-white shadow-brutal-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-brutal active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                    <x-icon name="refresh" class="h-4 w-4" />
                </button>
            </div>
        </header>

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 pb-28 pt-5 md:pb-10 md:pt-8">
            @yield('content')
            <p class="mt-4 text-center text-[11px] font-medium leading-relaxed text-ink/60" data-reveal>
                nemu bug? dm bae bang <a href="https://instagram.com/zaxz.dev" class="font-bold text-ink underline">@zaxz.dev</a>
            </p>
        </main>
        @include('partials.bottom-nav')
    </div>
</body>

</html>
