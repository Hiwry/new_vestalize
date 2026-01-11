/**
 * Dashboard Widgets - Sistema de Widgets Arrastáveis
 * Permite reorganizar, minimizar/expandir e salvar layout do dashboard
 */
class DashboardWidgets {
    constructor(containerSelector = '.widget-container') {
        this.containerSelector = containerSelector;
        this.storageKey = 'vestalize_dashboard_layout';
        this.sortableInstances = [];
        this.init();
    }

    init() {
        // Aguardar DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        // Carregar layout salvo
        this.loadLayout();

        // Inicializar SortableJS se disponível
        if (typeof Sortable !== 'undefined') {
            this.initSortable();
        } else {
            // Carregar SortableJS dinamicamente
            this.loadSortableJS().then(() => this.initSortable());
        }

        // Adicionar controles aos widgets
        this.addWidgetControls();

        // Listener para salvar ao fechar a página
        window.addEventListener('beforeunload', () => this.saveLayout());
    }

    loadSortableJS() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    initSortable() {
        const containers = document.querySelectorAll(this.containerSelector);

        containers.forEach(container => {
            const sortable = new Sortable(container, {
                animation: 200,
                handle: '.widget-drag-handle',
                ghostClass: 'widget-ghost',
                chosenClass: 'widget-chosen',
                dragClass: 'widget-drag',
                filter: '.widget-no-drag',
                onEnd: (evt) => {
                    this.saveLayout();
                    this.showSaveToast();
                }
            });
            this.sortableInstances.push(sortable);
        });
    }

    addWidgetControls() {
        const widgets = document.querySelectorAll('.dashboard-widget');

        widgets.forEach((widget, index) => {
            // Adicionar ID único ao widget se não tiver
            if (!widget.id) {
                widget.id = `widget-${index}`;
            }

            // Adicionar header de controle se não existir
            const existingHeader = widget.querySelector('.widget-header');
            if (!existingHeader) {
                const header = widget.querySelector('h2, .widget-title');
                if (header) {
                    this.wrapHeader(widget, header);
                }
            }
        });
    }

    wrapHeader(widget, titleElement) {
        const headerWrapper = document.createElement('div');
        headerWrapper.className = 'widget-header flex items-center justify-between mb-4';

        // Criar container do título com handle de arrastar
        const titleContainer = document.createElement('div');
        titleContainer.className = 'flex items-center gap-2 widget-drag-handle cursor-grab';

        // Ícone de arrastar
        const dragIcon = document.createElement('span');
        dragIcon.className = 'text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 transition-colors';
        dragIcon.innerHTML = `
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 2a2 2 0 00-2 2v1a2 2 0 004 0V4a2 2 0 00-2-2zm6 0a2 2 0 00-2 2v1a2 2 0 004 0V4a2 2 0 00-2-2zM7 8a2 2 0 00-2 2v1a2 2 0 004 0v-1a2 2 0 00-2-2zm6 0a2 2 0 00-2 2v1a2 2 0 004 0v-1a2 2 0 00-2-2zm-6 6a2 2 0 00-2 2v1a2 2 0 004 0v-1a2 2 0 00-2-2zm6 0a2 2 0 00-2 2v1a2 2 0 004 0v-1a2 2 0 00-2-2z"/>
            </svg>
        `;

        titleContainer.appendChild(dragIcon);

        // Clonar título
        const titleClone = titleElement.cloneNode(true);
        titleClone.className = titleElement.className + ' m-0';
        titleContainer.appendChild(titleClone);

        // Botões de controle
        const controls = document.createElement('div');
        controls.className = 'flex items-center gap-1 widget-controls';

        // Botão minimizar
        const minimizeBtn = document.createElement('button');
        minimizeBtn.className = 'p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all';
        minimizeBtn.innerHTML = `
            <svg class="w-4 h-4 minimize-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg class="w-4 h-4 maximize-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        `;
        minimizeBtn.onclick = () => this.toggleWidget(widget, minimizeBtn);

        controls.appendChild(minimizeBtn);

        headerWrapper.appendChild(titleContainer);
        headerWrapper.appendChild(controls);

        // Substituir título original pelo wrapper
        titleElement.parentNode.insertBefore(headerWrapper, titleElement);
        titleElement.remove();

        // Adicionar classe para identificar conteúdo do widget
        const widgetContent = widget.querySelector('.widget-content');
        if (!widgetContent) {
            const children = Array.from(widget.children).slice(1); // Pular o header
            const contentWrapper = document.createElement('div');
            contentWrapper.className = 'widget-content transition-all duration-300 overflow-hidden';
            children.forEach(child => contentWrapper.appendChild(child));
            widget.appendChild(contentWrapper);
        }
    }

    toggleWidget(widget, btn) {
        const content = widget.querySelector('.widget-content') || widget.querySelector(':scope > *:not(.widget-header)');
        const minimizeIcon = btn.querySelector('.minimize-icon');
        const maximizeIcon = btn.querySelector('.maximize-icon');

        if (content) {
            const isHidden = content.classList.contains('hidden');

            if (isHidden) {
                content.classList.remove('hidden');
                content.style.maxHeight = content.scrollHeight + 'px';
                minimizeIcon?.classList.remove('hidden');
                maximizeIcon?.classList.add('hidden');
            } else {
                content.style.maxHeight = '0';
                setTimeout(() => content.classList.add('hidden'), 300);
                minimizeIcon?.classList.add('hidden');
                maximizeIcon?.classList.remove('hidden');
            }

            widget.classList.toggle('widget-minimized', !isHidden);
            this.saveLayout();
        }
    }

    saveLayout() {
        const layout = {};
        const containers = document.querySelectorAll(this.containerSelector);

        containers.forEach((container, containerIndex) => {
            const widgets = container.querySelectorAll('.dashboard-widget');
            layout[containerIndex] = {
                order: [],
                states: {}
            };

            widgets.forEach((widget, widgetIndex) => {
                const id = widget.id || `widget-${containerIndex}-${widgetIndex}`;
                layout[containerIndex].order.push(id);
                layout[containerIndex].states[id] = {
                    minimized: widget.classList.contains('widget-minimized')
                };
            });
        });

        try {
            localStorage.setItem(this.storageKey, JSON.stringify(layout));
        } catch (e) {
            console.warn('Não foi possível salvar layout do dashboard:', e);
        }
    }

    loadLayout() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            if (!saved) return;

            const layout = JSON.parse(saved);
            const containers = document.querySelectorAll(this.containerSelector);

            containers.forEach((container, containerIndex) => {
                const savedContainer = layout[containerIndex];
                if (!savedContainer) return;

                // Reordenar widgets
                const order = savedContainer.order || [];
                order.forEach(widgetId => {
                    const widget = document.getElementById(widgetId);
                    if (widget && widget.parentElement === container) {
                        container.appendChild(widget);
                    }
                });

                // Restaurar estados (minimizado/expandido)
                const states = savedContainer.states || {};
                Object.entries(states).forEach(([widgetId, state]) => {
                    const widget = document.getElementById(widgetId);
                    if (widget && state.minimized) {
                        widget.classList.add('widget-minimized');
                        const content = widget.querySelector('.widget-content');
                        if (content) {
                            content.classList.add('hidden');
                            content.style.maxHeight = '0';
                        }
                        const minimizeBtn = widget.querySelector('.widget-controls button');
                        if (minimizeBtn) {
                            minimizeBtn.querySelector('.minimize-icon')?.classList.add('hidden');
                            minimizeBtn.querySelector('.maximize-icon')?.classList.remove('hidden');
                        }
                    }
                });
            });
        } catch (e) {
            console.warn('Não foi possível carregar layout do dashboard:', e);
        }
    }

    showSaveToast() {
        // Verificar se já existe um toast
        let toast = document.getElementById('widget-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'widget-toast';
            toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 z-50 flex items-center gap-2';
            toast.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Layout salvo!
            `;
            document.body.appendChild(toast);
        }

        // Mostrar toast
        setTimeout(() => {
            toast.classList.remove('translate-y-full', 'opacity-0');
        }, 10);

        // Esconder após 2 segundos
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
        }, 2000);
    }

    resetLayout() {
        localStorage.removeItem(this.storageKey);
        window.location.reload();
    }
}

// CSS para widgets
const widgetStyles = document.createElement('style');
widgetStyles.textContent = `
    .widget-ghost {
        opacity: 0.4;
        background: rgb(99, 102, 241) !important;
    }
    
    .widget-chosen {
        box-shadow: 0 10px 40px -10px rgba(99, 102, 241, 0.4) !important;
    }
    
    .widget-drag {
        opacity: 0.9;
    }
    
    .widget-drag-handle:active {
        cursor: grabbing;
    }
    
    .widget-minimized {
        min-height: auto !important;
    }
    
    .widget-minimized .widget-content {
        max-height: 0;
        overflow: hidden;
    }
    
    .dashboard-widget {
        transition: all 0.3s ease;
    }
    
    .dashboard-widget:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .dark .dashboard-widget:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
`;
document.head.appendChild(widgetStyles);

// Inicializar automaticamente se estiver na página de dashboard
if (document.querySelector('.dashboard-widget') || document.querySelector('[data-dashboard-widgets]')) {
    window.dashboardWidgets = new DashboardWidgets('.widget-container');
}
