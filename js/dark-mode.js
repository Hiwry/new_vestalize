/**
 * Global dark mode handler.
 * Uses localStorage key "dark" with values "true" / "false".
 */
(function () {
    const ICON_PAIRS = [
        { moon: 'moon-icon', sun: 'sun-icon' },
        { moon: 'moon-icon-mkt', sun: 'sun-icon-mkt' },
    ];

    function getSavedDarkMode() {
        return localStorage.getItem('dark') === 'true';
    }

    function syncIcons(isDark) {
        ICON_PAIRS.forEach(({ moon, sun }) => {
            const moonIcon = document.getElementById(moon);
            const sunIcon = document.getElementById(sun);
            if (!moonIcon || !sunIcon) {
                return;
            }

            if (isDark) {
                moonIcon.classList.add('hidden');
                sunIcon.classList.remove('hidden');
            } else {
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            }
        });
    }

    function dispatchThemeEvents(isDark) {
        const detail = { dark: isDark };
        ['theme-changed', 'dark-mode-toggled'].forEach((eventName) => {
            document.dispatchEvent(new CustomEvent(eventName, { detail }));
            window.dispatchEvent(new CustomEvent(eventName, { detail }));
        });
    }

    function applyTheme(isDark) {
        const html = document.documentElement;
        const body = document.body;

        html.classList.toggle('dark', isDark);
        html.style.colorScheme = isDark ? 'dark' : 'light';

        if (body) {
            body.classList.toggle('dark', isDark);
            body.style.colorScheme = isDark ? 'dark' : 'light';
        }

        syncIcons(isDark);
        return isDark;
    }

    window.applySavedTheme = function () {
        return applyTheme(getSavedDarkMode());
    };

    window.toggleDarkMode = function () {
        const nextDark = !document.documentElement.classList.contains('dark');
        localStorage.setItem('dark', nextDark ? 'true' : 'false');
        applyTheme(nextDark);
        dispatchThemeEvents(nextDark);
    };

    // Apply immediately to avoid flash.
    applyTheme(getSavedDarkMode());

    // Re-sync after the full DOM is available.
    document.addEventListener('DOMContentLoaded', function () {
        applyTheme(getSavedDarkMode());
    });

    // Keep tabs/windows in sync.
    window.addEventListener('storage', function (event) {
        if (event.key === 'dark') {
            const isDark = getSavedDarkMode();
            applyTheme(isDark);
            dispatchThemeEvents(isDark);
        }
    });
})();
