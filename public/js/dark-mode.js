// Check and apply theme immediately
(function () {
    const isDarkMode = localStorage.getItem('dark') === 'true';
    const html = document.documentElement;

    if (isDarkMode) {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }

    // Ensure syncThemeState is available early
    window.syncThemeState = function() {
        const isDark = document.documentElement.classList.contains('dark');
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        
        if (document.body) {
            document.body.style.colorScheme = isDark ? 'dark' : 'light';
        }
        
        applyAdminThemeOverrides(isDark);
        applyThemeBackgrounds();
        updateThemeUI();
    };

    // Update UI when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.syncThemeState();
        });
    } else {
        window.syncThemeState();
    }
})();

function updateThemeUI() {
    const isDark = document.documentElement.classList.contains('dark');
    const moonIcon = document.getElementById('moon-icon');
    const sunIcon = document.getElementById('sun-icon');

    // Update icons if they exist (Top bar or Sidebar)
    if (moonIcon && sunIcon) {
        if (isDark) {
            moonIcon.classList.add('hidden');
            sunIcon.classList.remove('hidden');
        } else {
            moonIcon.classList.remove('hidden');
            sunIcon.classList.add('hidden');
        }
    }
    
    // Update Alpine.js components if they are listening to 'isDark'
    // This is done via the 'dark-mode-toggled' event or by direct sync if needed
}

function getThemeBackground(isDark) {
    const root = document.documentElement;

    if (root.classList.contains('avento-theme')) {
        return isDark ? '#000000' : '#ffffff';
    }

    const background = getComputedStyle(root).getPropertyValue('--background').trim();
    if (background) {
        return background;
    }

    return isDark ? '#0a0a0a' : '#ffffff';
}

function applyThemeBackgrounds() {
    const root = document.documentElement;
    const isDark = root.classList.contains('dark');
    const background = getThemeBackground(isDark);

    root.style.backgroundColor = background;
    if (document.body) {
        document.body.style.backgroundColor = background;
        document.body.style.background = background;
    }

    const mainContent = document.getElementById('main-content');
    if (mainContent) {
        mainContent.style.backgroundColor = background;
        mainContent.style.background = background;
    }
}

function applyAdminThemeOverrides(isDark) {
    const root = document.documentElement;
    if (!root.classList.contains('avento-theme')) {
        return;
    }

    // Force variables for heavy overrides
    root.style.setProperty('--background', isDark ? '#000000' : '#ffffff');
    root.style.setProperty('--glow-opacity', isDark ? '0.12' : '0.05');
}

// Toggle function to be called from buttons
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');
    const newState = !isDark;

    if (newState) {
        html.classList.add('dark');
        localStorage.setItem('dark', 'true');
    } else {
        html.classList.remove('dark');
        localStorage.setItem('dark', 'false');
    }

    if (typeof window.syncThemeState === 'function') {
        window.syncThemeState();
    }

    // Dispatch event for other components (like Alpine)
    window.dispatchEvent(new CustomEvent('dark-mode-toggled', {
        detail: { dark: newState }
    }));
}

window.toggleDarkMode = toggleDarkMode;

// Listen for custom event if triggered elsewhere
window.addEventListener('dark-mode-toggled', function () {
    if (typeof window.syncThemeState === 'function') {
        window.syncThemeState();
    }
});

// Sync after content changes
document.addEventListener('content-loaded', function () {
    if (typeof window.syncThemeState === 'function') {
        window.syncThemeState();
    }
});

document.addEventListener('ajax-content-loaded', function () {
    if (typeof window.syncThemeState === 'function') {
        window.syncThemeState();
    }
});

window.addEventListener('pageshow', function () {
    if (typeof window.syncThemeState === 'function') {
        window.syncThemeState();
    }
});
