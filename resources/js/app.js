/**
 * Portal Mahasiswa IWIMA — interaksi frontend.
 */

function swapClasses(el, list, add) {
    if (!list) return;
    list.split(/\s+/)
        .filter(Boolean)
        .forEach((cls) => el.classList.toggle(cls, add));
}

// Tabs / segmented control (jadwal, ujian, KHS)
document.querySelectorAll('[data-tabs]').forEach((container) => {
    const buttons = container.querySelectorAll('[data-tab-target]');
    const panels = container.querySelectorAll('[data-tab-panel]');

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.tabPanel !== button.dataset.tabTarget);
            });
            buttons.forEach((other) => {
                const isActive = other === button;
                swapClasses(other, other.dataset.tabOn, isActive);
                swapClasses(other, other.dataset.tabOff, !isActive);
                other.setAttribute('aria-selected', String(isActive));
            });
        });
    });
});

// Skeleton loading -> konten (simulasi fetch data LMS)
document.querySelectorAll('[data-lms-load]').forEach((wrapper) => {
    const skeleton = wrapper.querySelector('[data-skeleton]');
    const content = wrapper.querySelector('[data-content]');
    if (!skeleton || !content) return;

    if (wrapper.dataset.cached === '1') {
        skeleton.classList.add('hidden');
        content.classList.remove('hidden');
        return;
    }

    setTimeout(() => {
        skeleton.classList.add('hidden');
        content.classList.remove('hidden');
    }, 150);
});

// Tombol refresh: putar ikon + reload dengan ?refresh=1 agar cache LMS di-bypass dan timestamp persist
document.querySelectorAll('[data-refresh]').forEach((button) => {
    button.addEventListener('click', () => {
        if (button.classList.contains('is-spinning')) return;
        button.classList.add('is-spinning');
        // Update timestamp secara optimistik
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        document.querySelectorAll('[data-updated-at]').forEach((el) => {
            el.textContent = `Diperbarui pukul ${hh}:${mm} WIB`;
        });
        setTimeout(() => {
            const url = new URL(window.location.href);
            url.searchParams.set('refresh', '1');
            window.location.href = url.toString();
        }, 700);
    });
});

// Bersihkan ?refresh=1 dari URL setelah reload agar reload berikutnya tidak selalu refresh (timestamp jadi persist)
(function () {
    try {
        const url = new URL(window.location.href);
        if (url.searchParams.has('refresh')) {
            url.searchParams.delete('refresh');
            window.history.replaceState({}, '', url.pathname + (url.search ? url.search : '') + url.hash);
        }
    } catch (e) {}
})();

// Entry animation: fade + translate-Y, staggered 80ms
document.querySelectorAll('[data-reveal]').forEach((el, index) => {
    el.style.animationDelay = `${Math.min(index * 80, 480)}ms`;
});

// Login: validasi client-side sebelum form POST diteruskan ke backend
const loginForm = document.querySelector('[data-login]');
if (loginForm) {
    loginForm.addEventListener('submit', (event) => {
        const usid = loginForm.querySelector('#usid');
        const pwd = loginForm.querySelector('#pwd');
        const error = loginForm.querySelector('[data-login-error]');
        const errorText = loginForm.querySelector('[data-login-error-text]');

        if (!usid.value.trim() || !pwd.value.trim()) {
            event.preventDefault();
            errorText.textContent = !usid.value.trim() && !pwd.value.trim()
                ? 'Isi ID pengguna dan password dulu ya.'
                : (!usid.value.trim() ? 'ID pengguna belum diisi.' : 'Password belum diisi.');
            error.classList.remove('hidden');
            error.classList.add('flex');
            return;
        }

        error.classList.add('hidden');
        error.classList.remove('flex');
    });
}
