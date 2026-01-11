/**
 * Side Panel / Drawer - Painel lateral deslizante para edições rápidas
 * Substitui modais tradicionais por painéis laterais mais modernos
 */

class SidePanel {
    constructor(options = {}) {
        this.options = {
            position: options.position || 'right', // 'right' ou 'left'
            width: options.width || '500px',
            maxWidth: options.maxWidth || '90vw',
            overlay: options.overlay !== false,
            closeOnOverlay: options.closeOnOverlay !== false,
            closeOnEscape: options.closeOnEscape !== false,
            onOpen: options.onOpen || null,
            onClose: options.onClose || null,
        };

        this.isOpen = false;
        this.panel = null;
        this.overlay = null;
        this.contentContainer = null;

        this.init();
    }

    init() {
        this.createPanel();
        this.createOverlay();
        this.bindEvents();
    }

    createPanel() {
        this.panel = document.createElement('div');
        this.panel.className = `side-panel side-panel-${this.options.position}`;
        this.panel.style.cssText = `
            position: fixed;
            top: 0;
            ${this.options.position}: 0;
            height: 100vh;
            width: ${this.options.width};
            max-width: ${this.options.maxWidth};
            background: white;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transform: translateX(${this.options.position === 'right' ? '100%' : '-100%'});
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        `;

        // Header
        const header = document.createElement('div');
        header.className = 'side-panel-header';
        header.innerHTML = `
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <h2 class="side-panel-title text-lg font-semibold text-gray-900 dark:text-gray-100">Título</h2>
                <button class="side-panel-close p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

        // Content container
        this.contentContainer = document.createElement('div');
        this.contentContainer.className = 'side-panel-content flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900';

        // Footer (opcional, será preenchido dinamicamente)
        const footer = document.createElement('div');
        footer.className = 'side-panel-footer hidden border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800';

        this.panel.appendChild(header);
        this.panel.appendChild(this.contentContainer);
        this.panel.appendChild(footer);

        document.body.appendChild(this.panel);

        // Aplicar dark mode
        this.applyDarkMode();
    }

    createOverlay() {
        if (!this.options.overlay) return;

        this.overlay = document.createElement('div');
        this.overlay.className = 'side-panel-overlay';
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        `;

        document.body.appendChild(this.overlay);
    }

    applyDarkMode() {
        const isDark = document.documentElement.classList.contains('dark');
        if (isDark) {
            this.panel.style.background = 'rgb(31, 41, 55)'; // gray-800
            this.panel.style.boxShadow = '-4px 0 20px rgba(0, 0, 0, 0.4)';
        }
    }

    bindEvents() {
        // Botão de fechar
        const closeBtn = this.panel.querySelector('.side-panel-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }

        // Fechar no overlay
        if (this.overlay && this.options.closeOnOverlay) {
            this.overlay.addEventListener('click', () => this.close());
        }

        // Fechar com ESC
        if (this.options.closeOnEscape) {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            });
        }

        // Observar mudanças de dark mode
        const observer = new MutationObserver(() => this.applyDarkMode());
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    }

    open(options = {}) {
        const { title, content, footer, onOpen } = options;

        // Atualizar título
        if (title) {
            const titleEl = this.panel.querySelector('.side-panel-title');
            if (titleEl) titleEl.textContent = title;
        }

        // Atualizar conteúdo
        if (content) {
            if (typeof content === 'string') {
                this.contentContainer.innerHTML = content;
            } else if (content instanceof HTMLElement) {
                this.contentContainer.innerHTML = '';
                this.contentContainer.appendChild(content);
            }
        }

        // Atualizar footer
        const footerEl = this.panel.querySelector('.side-panel-footer');
        if (footer && footerEl) {
            if (typeof footer === 'string') {
                footerEl.innerHTML = footer;
            } else if (footer instanceof HTMLElement) {
                footerEl.innerHTML = '';
                footerEl.appendChild(footer);
            }
            footerEl.classList.remove('hidden');
        } else if (footerEl) {
            footerEl.classList.add('hidden');
        }

        // Abrir painel
        this.isOpen = true;
        this.panel.style.transform = 'translateX(0)';

        if (this.overlay) {
            this.overlay.style.opacity = '1';
            this.overlay.style.visibility = 'visible';
        }

        document.body.style.overflow = 'hidden';

        // Callbacks
        if (onOpen) onOpen(this);
        if (this.options.onOpen) this.options.onOpen(this);
    }

    close() {
        this.isOpen = false;
        this.panel.style.transform = `translateX(${this.options.position === 'right' ? '100%' : '-100%'})`;

        if (this.overlay) {
            this.overlay.style.opacity = '0';
            this.overlay.style.visibility = 'hidden';
        }

        document.body.style.overflow = '';

        if (this.options.onClose) this.options.onClose(this);
    }

    setContent(content) {
        if (typeof content === 'string') {
            this.contentContainer.innerHTML = content;
        } else if (content instanceof HTMLElement) {
            this.contentContainer.innerHTML = '';
            this.contentContainer.appendChild(content);
        }
    }

    setTitle(title) {
        const titleEl = this.panel.querySelector('.side-panel-title');
        if (titleEl) titleEl.textContent = title;
    }

    setLoading(loading = true) {
        if (loading) {
            this.contentContainer.innerHTML = `
                <div class="flex items-center justify-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                </div>
            `;
        }
    }

    destroy() {
        if (this.panel) this.panel.remove();
        if (this.overlay) this.overlay.remove();
    }
}

// Criar instância global padrão do Side Panel
window.sidePanel = new SidePanel({ position: 'right', width: '500px' });

// CSS adicional para o Side Panel
const sidePanelStyles = document.createElement('style');
sidePanelStyles.textContent = `
    /* Formulários dentro do Side Panel */
    .side-panel-content form {
        background: white;
        border-radius: 0.5rem;
        padding: 1rem;
    }

    .dark .side-panel-content form {
        background: rgb(55, 65, 81);
    }

    .side-panel-content .form-group {
        margin-bottom: 1rem;
    }

    .side-panel-content .form-group:last-child {
        margin-bottom: 0;
    }

    .side-panel-content label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(55, 65, 81);
        margin-bottom: 0.5rem;
    }

    .dark .side-panel-content label {
        color: rgb(209, 213, 219);
    }

    .side-panel-content input[type="text"],
    .side-panel-content input[type="email"],
    .side-panel-content input[type="number"],
    .side-panel-content input[type="tel"],
    .side-panel-content input[type="date"],
    .side-panel-content select,
    .side-panel-content textarea {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid rgb(209, 213, 219);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .dark .side-panel-content input[type="text"],
    .dark .side-panel-content input[type="email"],
    .dark .side-panel-content input[type="number"],
    .dark .side-panel-content input[type="tel"],
    .dark .side-panel-content input[type="date"],
    .dark .side-panel-content select,
    .dark .side-panel-content textarea {
        background: rgb(55, 65, 81);
        border-color: rgb(75, 85, 99);
        color: rgb(243, 244, 246);
    }

    .side-panel-content input:focus,
    .side-panel-content select:focus,
    .side-panel-content textarea:focus {
        outline: none;
        border-color: rgb(99, 102, 241);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Botões do footer */
    .side-panel-footer {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .side-panel-footer .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        cursor: pointer;
    }

    .side-panel-footer .btn-primary {
        background: rgb(99, 102, 241);
        color: white;
        border: none;
    }

    .side-panel-footer .btn-primary:hover {
        background: rgb(79, 70, 229);
    }

    .side-panel-footer .btn-secondary {
        background: rgb(243, 244, 246);
        color: rgb(55, 65, 81);
        border: 1px solid rgb(209, 213, 219);
    }

    .dark .side-panel-footer .btn-secondary {
        background: rgb(55, 65, 81);
        color: rgb(209, 213, 219);
        border-color: rgb(75, 85, 99);
    }

    .side-panel-footer .btn-secondary:hover {
        background: rgb(229, 231, 235);
    }

    .dark .side-panel-footer .btn-secondary:hover {
        background: rgb(75, 85, 99);
    }

    /* Animação de entrada */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(0);
        }
    }

    @keyframes slideInLeft {
        from {
            transform: translateX(-100%);
        }
        to {
            transform: translateX(0);
        }
    }

    /* Responsivo */
    @media (max-width: 640px) {
        .side-panel {
            width: 100% !important;
            max-width: 100% !important;
        }
    }

    /* Toast de sucesso no side panel */
    .side-panel-toast {
        position: absolute;
        bottom: 80px;
        left: 1rem;
        right: 1rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .side-panel-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .side-panel-toast.success {
        background: rgb(209, 250, 229);
        color: rgb(6, 95, 70);
        border: 1px solid rgb(167, 243, 208);
    }

    .side-panel-toast.error {
        background: rgb(254, 226, 226);
        color: rgb(153, 27, 27);
        border: 1px solid rgb(252, 165, 165);
    }

    .dark .side-panel-toast.success {
        background: rgba(6, 95, 70, 0.2);
        color: rgb(167, 243, 208);
        border-color: rgba(167, 243, 208, 0.3);
    }

    .dark .side-panel-toast.error {
        background: rgba(153, 27, 27, 0.2);
        color: rgb(252, 165, 165);
        border-color: rgba(252, 165, 165, 0.3);
    }
`;
document.head.appendChild(sidePanelStyles);

// Função utilitária para abrir side panel com URL (carregar conteúdo via AJAX)
async function openSidePanelFromUrl(url, title = 'Editar') {
    window.sidePanel.setLoading(true);
    window.sidePanel.open({ title });

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error('Erro ao carregar conteúdo');

        const html = await response.text();
        window.sidePanel.setContent(html);
    } catch (error) {
        console.error('Erro:', error);
        window.sidePanel.setContent(`
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Erro ao carregar conteúdo</p>
            </div>
        `);
    }
}

// Expor globalmente
window.SidePanel = SidePanel;
window.openSidePanelFromUrl = openSidePanelFromUrl;

// Função para mostrar toast no side panel
function showSidePanelToast(message, type = 'success') {
    const panel = document.querySelector('.side-panel');
    if (!panel) return;

    // Remover toast existente
    const existingToast = panel.querySelector('.side-panel-toast');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = `side-panel-toast ${type}`;
    toast.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${type === 'success'
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
        }
        </svg>
        <span>${message}</span>
    `;

    panel.appendChild(toast);

    // Animar entrada
    setTimeout(() => toast.classList.add('show'), 10);

    // Remover após 3 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

window.showSidePanelToast = showSidePanelToast;
