<nav class="fixed inset-x-0 bottom-0 z-[100] border-t-3 border-ink bg-white pb-[env(safe-area-inset-bottom)] md:hidden"
    aria-label="Navigasi bawah">
    <div class="mx-auto grid max-w-md grid-cols-5">
        @php
            $items = [
                ['/', 'Beranda', 'home', request()->is('/')],
                ['/jadwal', 'Jadwal', 'calendar', request()->is('jadwal*', 'ujian*')],
                ['/nilai', 'Nilai', 'chart', request()->is('nilai*', 'khs*')],
                ['/kehadiran', 'Hadir', 'clipboard-check', request()->is('kehadiran*')],
                ['/profil', 'Profil', 'user', request()->is('profil*', 'pengumuman*')],
            ];
        @endphp

        @foreach ($items as [$href, $label, $icon, $active])
            <a href="{{ url($href) }}"
                class="group flex flex-col items-center gap-1 py-2 text-[10px] font-bold uppercase tracking-wide {{ $active ? 'text-ink' : 'text-ink/60' }}">
                <span
                    class="grid h-8 w-14 place-items-center rounded-lg border-2 transition-all duration-200 {{ $active ? 'border-ink bg-primary shadow-brutal-sm' : 'border-transparent group-hover:border-ink group-hover:bg-cream' }}">
                    <x-icon :name="$icon" class="h-5 w-5" />
                </span>
                {{ $label }}
            </a>
        @endforeach
    </div>
</nav>
