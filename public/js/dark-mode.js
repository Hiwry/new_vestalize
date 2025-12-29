// Check and apply theme immediately
(function () {
    const isDarkMode = localStorage.getItem('dark') === 'true';
    const html = document.documentElement;

    if (isDarkMode) {
        html.classList.add('dark');
        html.style.colorScheme = 'dark';
    } else {
        html.classList.remove('dark');
        html.style.colorScheme = 'light';
    }

    // Update UI if DOM is already ready (rare for inline, but good practice)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateThemeUI);
    } else {
        updateThemeUI();
    }
})();

function updateThemeUI() {
    const isDark = document.documentElement.classList.contains('dark');
    const moonIcon = document.getElementById('moon-icon');
    const sunIcon = document.getElementById('sun-icon');

    if (moonIcon && sunIcon) {
        if (isDark) {
            moonIcon.classList.add('hidden');
            sunIcon.classList.remove('hidden');
        } else {
            moonIcon.classList.remove('hidden');
            sunIcon.classList.add('hidden');
        }
    }
}

// Toggle function to be called from buttons
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        html.style.colorScheme = 'light';
        localStorage.setItem('dark', 'false');
    } else {
        html.classList.add('dark');
        html.style.colorScheme = 'dark';
        localStorage.setItem('dark', 'true');
    }

    updateThemeUI();

    // Dispatch event for other components (like Alpine)
    window.dispatchEvent(new CustomEvent('dark-mode-toggled', {
        detail: { dark: !isDark }
    }));
}

// Listen for custom event if triggered elsewhere
window.addEventListener('dark-mode-toggled', function () {
    updateThemeUI();
});
