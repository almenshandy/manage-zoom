<script>
    (() => {
        let preference;
        try {
            preference = localStorage.getItem('managezoom_theme');
        } catch {
            // The system theme is available without browser storage.
        }

        const root = document.documentElement;
        root.dataset.themePreference = ['light', 'dark'].includes(preference) ? preference : 'system';
        root.dataset.theme = root.dataset.themePreference === 'system'
            ? (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
            : root.dataset.themePreference;
    })();
</script>
