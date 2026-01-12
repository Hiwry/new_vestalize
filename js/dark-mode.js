/**
 * Dark Mode Toggle Script
 * Sistema de Gestão de Pedidos
 * 
 * NOTA: Usar chave 'dark' consistentemente (não 'darkMode')
 */

// ⚡ Aplicar dark mode IMEDIATAMENTE (antes do DOMContentLoaded)
// Isso previne o "flash" de conteúdo light mode
(function() {
    const isDarkMode = localStorage.getItem('dark') === 'true';
    if (isDarkMode) {
        document.documentElement.classList.add('dark');
    }
})();

// Função global para toggle do dark mode
window.toggleDarkMode = function() {
    const html = document.documentElement;
    const moonIcon = document.getElementById('moon-icon');
    const sunIcon = document.getElementById('sun-icon');
    
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('dark', isDark ? 'true' : 'false');
    html.style.colorScheme = isDark ? 'dark' : 'light';
    
    // Atualizar ícones
    if (moonIcon && sunIcon) {
        if (isDark) {
            moonIcon.classList.add('hidden');
            sunIcon.classList.remove('hidden');
        } else {
            moonIcon.classList.remove('hidden');
            sunIcon.classList.add('hidden');
        }
    }
    
    // Disparar evento customizado
    document.dispatchEvent(new CustomEvent('theme-changed', { 
        detail: { dark: isDark } 
    }));
};

// Atualizar ícones quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = localStorage.getItem('dark') === 'true';
    const html = document.documentElement;
    const moonIcon = document.getElementById('moon-icon');
    const sunIcon = document.getElementById('sun-icon');
    
    // Garantir que os ícones estão sincronizados
    if (isDarkMode) {
        html.classList.add('dark');
        if (moonIcon) moonIcon.classList.add('hidden');
        if (sunIcon) sunIcon.classList.remove('hidden');
    } else {
        html.classList.remove('dark');
        if (moonIcon) moonIcon.classList.remove('hidden');
        if (sunIcon) sunIcon.classList.add('hidden');
    }
});

