@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <main class="flex min-h-[100dvh] items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm">
            <div class="mb-6 text-center" data-reveal>
                <img src="{{ asset('images/logo-iwima.svg') }}" alt="Logo IWIMA"
                    class="mx-auto mb-4 h-14 w-14 -rotate-3 rounded-lg border-3 border-ink bg-white p-1 shadow-brutal object-contain">
                <h1 class="text-2xl font-extrabold uppercase tracking-wide">LMS IWIMA KALCER</h1>
                <p class="mt-1 text-sm font-medium text-ink/70">Masuk dengan akun LMS</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" data-login data-reveal
                class="rounded-lg border-3 border-ink bg-white p-5 shadow-brutal">
                @csrf
                <input type="hidden" name="role" value="s">

                @if ($errors->any())
                    <div data-login-error
                        class="mb-4 flex items-start gap-2 rounded-lg border-3 border-ink bg-secondary p-3 text-xs font-bold text-white">
                        <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                        <span
                            data-login-error-text>{{ $errors->first('usid', 'ID atau password salah. Coba lagi ya.') }}</span>
                    </div>
                @else
                    <div data-login-error
                        class="mb-4 hidden items-start gap-2 rounded-lg border-3 border-ink bg-secondary p-3 text-xs font-bold text-white">
                        <x-icon name="alert" class="mt-0.5 h-4 w-4 shrink-0" />
                        <span data-login-error-text>ID atau password salah. Coba lagi ya.</span>
                    </div>
                @endif

                <label for="usid" class="mb-1.5 block text-xs font-bold uppercase tracking-widest">ID Pengguna</label>
                <input id="usid" name="usid" type="text" inputmode="numeric" autocomplete="username"
                    value="{{ old('usid') }}"
                    class="w-full rounded-lg border-3 border-ink bg-cream px-3 py-2.5 font-mono text-sm outline-none transition-shadow duration-200 focus:shadow-brutal"
                    placeholder="NIM / ID LMS">

                <label for="pwd" class="mb-1.5 mt-4 block text-xs font-bold uppercase tracking-widest">Password</label>
                <input id="pwd" name="pwd" type="password" autocomplete="current-password"
                    class="w-full rounded-lg border-3 border-ink bg-cream px-3 py-2.5 font-mono text-sm outline-none transition-shadow duration-200 focus:shadow-brutal"
                    placeholder="••••••••">

                <button type="submit"
                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-lg border-3 border-ink bg-primary px-4 py-3 text-sm font-extrabold uppercase tracking-widest shadow-brutal transition-all duration-200 hover:-translate-y-0.5 hover:shadow-brutal-lg active:translate-x-[2px] active:translate-y-[2px] active:shadow-none">
                    Masuk
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </button>

                <p class="mt-4 text-center text-[11px] font-medium leading-relaxed text-ink/60">
                    Aman bae ora kesimpen ke database. Login diteruskan langsung ke lms.iwima.ac.id.
                </p>
            </form>
            <p class="mt-4 text-center text-[11px] font-medium leading-relaxed text-ink/60" data-reveal>
                nemu bug? dm bae bang <a href="https://instagram.com/zaxz.dev"
                    class="font-bold text-ink underline">@zaxz.dev</a>
            </p>
        </div>
    </main>
@endsection
