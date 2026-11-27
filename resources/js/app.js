const themeToggle = document.getElementById('themeToggle');

if (themeToggle) {
    const root = document.documentElement;
    const themePreference = document.getElementById('themePreference');
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

    function applyTheme(preference) {
        const dark = preference === 'dark' || (preference === 'system' && systemTheme.matches);
        root.dataset.themePreference = preference;
        root.dataset.theme = dark ? 'dark' : 'light';
        themeToggle.setAttribute('aria-checked', String(dark));
        themeToggle.title = dark ? 'Beralih ke mode terang' : 'Beralih ke mode gelap';
        themePreference.value = preference;
        document.dispatchEvent(new Event('managezoom:themechange'));
    }

    function saveTheme(preference) {
        try {
            localStorage.setItem('managezoom_theme', preference);
        } catch {
            // The preference lasts for this page when storage is unavailable.
        }
        applyTheme(preference);
    }

    themeToggle.addEventListener('click', () => saveTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));
    themePreference.addEventListener('change', () => saveTheme(themePreference.value));
    systemTheme.addEventListener('change', () => applyTheme(root.dataset.themePreference));
    applyTheme(root.dataset.themePreference);
}

const sidebar = document.getElementById('mainSidebar');

if (sidebar) {
    const toggle = document.getElementById('sidebarToggleBtn');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeButton = document.getElementById('sidebarCloseBtn');
    const desktop = window.matchMedia('(min-width: 768px)');
    const main = document.querySelector('.app-main');
    const mobileMenu = document.getElementById('mobileMenuBtn');
    const focusMenuTrigger = () => (desktop.matches ? toggle : mobileMenu).focus();

    try {
        document.body.classList.toggle('sidebar-mini', localStorage.getItem('managezoom_sidebar_mini') === '1');
    } catch {
        // Navigation remains usable when browser storage is unavailable.
    }

    function syncNavigation() {
        const open = !desktop.matches && document.body.classList.contains('mobile-nav-open');
        sidebar.inert = !desktop.matches && !open;
        sidebar.setAttribute('aria-hidden', String(sidebar.inert));
        main.inert = open;
        backdrop.classList.toggle('hidden', !open);
        const expanded = desktop.matches ? !document.body.classList.contains('sidebar-mini') : open;
        toggle.setAttribute('aria-expanded', String(expanded));
        mobileMenu.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', desktop.matches ? 'Perkecil / Perbesar menu' : (open ? 'Tutup menu' : 'Buka menu'));
        toggle.title = toggle.getAttribute('aria-label');
    }

    window.closeSidebar = function () {
        document.body.classList.remove('mobile-nav-open');
        syncNavigation();
        focusMenuTrigger();
    };

    window.toggleSidebar = function () {
        if (desktop.matches) {
            const mini = document.body.classList.toggle('sidebar-mini');
            try {
                localStorage.setItem('managezoom_sidebar_mini', mini ? '1' : '0');
            } catch {
                // The preference lasts for this page when storage is unavailable.
            }
        } else {
            const open = document.body.classList.toggle('mobile-nav-open');
            syncNavigation();
            if (open) closeButton.focus();
            return;
        }
        syncNavigation();
    };

    desktop.addEventListener('change', () => {
        const wasOpen = document.body.classList.contains('mobile-nav-open');
        document.body.classList.remove('mobile-nav-open');
        syncNavigation();
        if (wasOpen || (!desktop.matches && (sidebar.contains(document.activeElement) || document.activeElement === toggle)) || (desktop.matches && document.activeElement === mobileMenu)) focusMenuTrigger();
    });

    sidebar.addEventListener('keydown', event => {
        if (desktop.matches || !document.body.classList.contains('mobile-nav-open')) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            window.closeSidebar();
        } else if (event.key === 'Tab') {
            const controls = Array.from(sidebar.querySelectorAll('a[href], button:not([disabled])'))
                .filter(element => element.getClientRects().length);
            const first = controls[0];
            const last = controls[controls.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    syncNavigation();
}
