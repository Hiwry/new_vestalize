/**
 * Keyboard Shortcuts - Atalhos de Teclado Globais
 * Melhora produtividade do usuário com atalhos rápidos
 */

class KeyboardShortcuts {
    constructor() {
        this.shortcuts = new Map();
        this.isModalOpen = false;
        this.helpModal = null;

        this.registerDefaultShortcuts();
        this.bindEvents();
        this.createHelpModal();
    }

    registerDefaultShortcuts() {
        // Navegação rápida
        this.register('ctrl+n', 'Novo Pedido', () => {
            window.location.href = '/pedidos/wizard/inicio';
        });

        this.register('ctrl+k', 'Busca Rápida Global', () => {
            this.openQuickSearch();
        });

        this.register('/', 'Focus na Busca', (e) => {
            const searchInput = document.querySelector('input[name="search"], input[type="search"], #search');
            if (searchInput && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });

        this.register('escape', 'Fechar Modal/Painel', () => {
            // Fechar side panel se aberto
            if (window.sidePanel?.isOpen) {
                window.sidePanel.close();
                return;
            }
            // Fechar busca rápida se aberta
            const quickSearch = document.getElementById('quick-search-modal');
            if (quickSearch && !quickSearch.classList.contains('hidden')) {
                this.closeQuickSearch();
                return;
            }
        });

        this.register('ctrl+/', 'Mostrar Atalhos', (e) => {
            e.preventDefault();
            this.toggleHelpModal();
        });

        this.register('?', 'Mostrar Atalhos', (e) => {
            if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                this.toggleHelpModal();
            }
        });

        // Navegação por páginas
        this.register('g h', 'Ir para Dashboard', () => {
            window.location.href = '/dashboard';
        });

        this.register('g p', 'Ir para Pedidos', () => {
            window.location.href = '/pedidos';
        });

        this.register('g c', 'Ir para Clientes', () => {
            window.location.href = '/clientes';
        });

        this.register('g k', 'Ir para Kanban', () => {
            window.location.href = '/kanban';
        });
    }

    register(shortcut, description, callback) {
        this.shortcuts.set(shortcut.toLowerCase(), { description, callback });
    }

    bindEvents() {
        let keySequence = '';
        let keySequenceTimer = null;

        document.addEventListener('keydown', (e) => {
            // Ignorar se estiver digitando em input/textarea (exceto para Escape e Ctrl+)
            const isTyping = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName);
            const isContentEditable = document.activeElement.contentEditable === 'true';

            if ((isTyping || isContentEditable) && !e.ctrlKey && !e.metaKey && e.key !== 'Escape') {
                return;
            }

            // Construir tecla
            let key = '';
            if (e.ctrlKey || e.metaKey) key += 'ctrl+';
            if (e.altKey) key += 'alt+';
            if (e.shiftKey && e.key.length > 1) key += 'shift+';
            key += e.key.toLowerCase();

            // Verificar atalho direto
            if (this.shortcuts.has(key)) {
                const shortcut = this.shortcuts.get(key);
                shortcut.callback(e);
                keySequence = '';
                return;
            }

            // Verificar sequência de teclas (g + letra)
            if (e.key.length === 1 && !e.ctrlKey && !e.altKey) {
                keySequence += e.key.toLowerCase();

                // Limpar sequência após 1 segundo
                clearTimeout(keySequenceTimer);
                keySequenceTimer = setTimeout(() => {
                    keySequence = '';
                }, 1000);

                // Verificar sequência
                if (this.shortcuts.has(keySequence)) {
                    const shortcut = this.shortcuts.get(keySequence);
                    e.preventDefault();
                    shortcut.callback(e);
                    keySequence = '';
                }
            }
        });
    }

    createHelpModal() {
        this.helpModal = document.createElement('div');
        this.helpModal.id = 'keyboard-shortcuts-modal';
        this.helpModal.className = 'fixed inset-0 z-[9999] hidden';
        this.helpModal.innerHTML = `
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="keyboardShortcuts.toggleHelpModal()"></div>
            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">⌨️ Atalhos de Teclado</h3>
                    <button onclick="keyboardShortcuts.toggleHelpModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Ações Rápidas</h4>
                            <div class="space-y-2">
                                ${this.renderShortcutItem('Ctrl + N', 'Novo Pedido')}
                                ${this.renderShortcutItem('Ctrl + K', 'Busca Rápida Global')}
                                ${this.renderShortcutItem('/', 'Focus na Busca')}
                                ${this.renderShortcutItem('Esc', 'Fechar Modal/Painel')}
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Navegação (g + letra)</h4>
                            <div class="space-y-2">
                                ${this.renderShortcutItem('g → h', 'Dashboard (Home)')}
                                ${this.renderShortcutItem('g → p', 'Pedidos')}
                                ${this.renderShortcutItem('g → c', 'Clientes')}
                                ${this.renderShortcutItem('g → k', 'Kanban')}
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Ajuda</h4>
                            <div class="space-y-2">
                                ${this.renderShortcutItem('?', 'Mostrar este modal')}
                                ${this.renderShortcutItem('Ctrl + /', 'Mostrar este modal')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(this.helpModal);
    }

    renderShortcutItem(key, description) {
        return `
            <div class="flex items-center justify-between py-2">
                <span class="text-gray-700 dark:text-gray-300">${description}</span>
                <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-sm font-mono text-gray-700 dark:text-gray-300">${key}</kbd>
            </div>
        `;
    }

    toggleHelpModal() {
        if (this.helpModal.classList.contains('hidden')) {
            this.helpModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            this.helpModal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    openQuickSearch() {
        // Se já existe, apenas abrir
        let modal = document.getElementById('quick-search-modal');
        if (!modal) {
            modal = this.createQuickSearchModal();
        }
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        const input = modal.querySelector('input');
        if (input) {
            input.focus();
            input.value = '';
        }
    }

    closeQuickSearch() {
        const modal = document.getElementById('quick-search-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    createQuickSearchModal() {
        const modal = document.createElement('div');
        modal.id = 'quick-search-modal';
        modal.className = 'fixed inset-0 z-[9999]';
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="keyboardShortcuts.closeQuickSearch()"></div>
            <div class="fixed top-[20%] left-1/2 -translate-x-1/2 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden">
                <div class="p-4">
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input 
                            type="text" 
                            placeholder="Buscar pedidos, clientes, ações..."
                            class="w-full pl-12 pr-4 py-3 text-lg border-0 bg-gray-50 dark:bg-gray-700 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            onkeydown="keyboardShortcuts.handleQuickSearchKeydown(event)"
                            oninput="keyboardShortcuts.handleQuickSearchInput(event)"
                        >
                        <kbd class="absolute right-4 top-1/2 -translate-y-1/2 px-2 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs rounded">ESC</kbd>
                    </div>
                </div>
                <div id="quick-search-results" class="max-h-80 overflow-y-auto border-t border-gray-200 dark:border-gray-700">
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                        Digite para buscar...
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 rounded">↑↓</kbd> Navegar</span>
                        <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 rounded">Enter</kbd> Selecionar</span>
                    </div>
                    <span>Ctrl+K para abrir</span>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        return modal;
    }

    handleQuickSearchKeydown(event) {
        if (event.key === 'Escape') {
            this.closeQuickSearch();
        } else if (event.key === 'Enter') {
            const firstResult = document.querySelector('#quick-search-results a');
            if (firstResult) {
                window.location.href = firstResult.href;
            }
        }
    }

    handleQuickSearchInput(event) {
        const query = event.target.value.trim();
        const resultsContainer = document.getElementById('quick-search-results');

        if (!query) {
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Digite para buscar...
                </div>
            `;
            return;
        }

        // Mostrar loading
        resultsContainer.innerHTML = `
            <div class="p-4 text-center">
                <div class="inline-block w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        `;

        // Buscar resultados (debounced)
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performQuickSearch(query);
        }, 300);
    }

    async performQuickSearch(query) {
        const resultsContainer = document.getElementById('quick-search-results');

        try {
            // Buscar pedidos e clientes via API
            const [ordersResponse, clientsResponse] = await Promise.all([
                fetch(`/api/search/orders?q=${encodeURIComponent(query)}`).catch(() => ({ ok: false })),
                fetch(`/api/search/clients?q=${encodeURIComponent(query)}`).catch(() => ({ ok: false }))
            ]);

            let html = '';

            // Ações rápidas baseadas na query
            const actions = this.getQuickActions(query);
            if (actions.length > 0) {
                html += `
                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ações</span>
                    </div>
                `;
                actions.forEach(action => {
                    html += `
                        <a href="${action.url}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-xl">${action.icon}</span>
                            <span class="text-gray-900 dark:text-gray-100">${action.label}</span>
                        </a>
                    `;
                });
            }

            // Se não houver resultados
            if (!html) {
                html = `
                    <div class="p-6 text-center">
                        <p class="text-gray-500 dark:text-gray-400">Nenhum resultado para "${query}"</p>
                        <a href="/pedidos/wizard/inicio" class="mt-3 inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Criar novo pedido
                        </a>
                    </div>
                `;
            }

            resultsContainer.innerHTML = html;

        } catch (error) {
            console.error('Erro na busca:', error);
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-red-500">
                    Erro ao buscar. Tente novamente.
                </div>
            `;
        }
    }

    getQuickActions(query) {
        const actions = [];
        const q = query.toLowerCase();

        if (q.includes('novo') || q.includes('criar') || q.includes('pedido')) {
            actions.push({ icon: '📦', label: 'Criar Novo Pedido', url: '/pedidos/wizard/inicio' });
        }
        if (q.includes('cliente') || q.includes('cadastr')) {
            actions.push({ icon: '👤', label: 'Cadastrar Cliente', url: '/clientes/create' });
        }
        if (q.includes('kanban') || q.includes('producao') || q.includes('produção')) {
            actions.push({ icon: '📋', label: 'Ir para Kanban', url: '/kanban' });
        }
        if (q.includes('config') || q.includes('ajust')) {
            actions.push({ icon: '⚙️', label: 'Configurações', url: '/settings' });
        }
        if (q.includes('relat') || q.includes('dashboard')) {
            actions.push({ icon: '📊', label: 'Dashboard', url: '/dashboard' });
        }

        return actions;
    }
}

// Inicializar quando DOM estiver pronto
let keyboardShortcuts;

function initKeyboardShortcuts() {
    if (!keyboardShortcuts) {
        keyboardShortcuts = new KeyboardShortcuts();
        window.keyboardShortcuts = keyboardShortcuts;
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKeyboardShortcuts);
} else {
    initKeyboardShortcuts();
}

// Keyboard shortcuts initialized.
