/**
 * Dashboard Widgets - Sistema Premium de Widgets Arrastáveis
 * Com animações sofisticadas, responsividade mobile e salvamento de layout
 */
class DashboardWidgets {
    constructor(containerSelector = '.widget-container') {
        this.containerSelector = containerSelector;
        this.storageKey = 'vestalize_dashboard_layout';
        this.sortableInstances = [];
        this.animationObserver = null;
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        // Injetar estilos premium
        this.injectStyles();

        // Carregar layout salvo
        this.loadLayout();

        // Inicializar SortableJS
        if (typeof Sortable !== 'undefined') {
            this.initSortable();
        } else {
            this.loadSortableJS().then(() => this.initSortable());
        }

        // Adicionar controles aos widgets
        this.addWidgetControls();

        // Inicializar animações de scroll
        this.initScrollAnimations();

        // Listener para salvar ao fechar a página
        window.addEventListener('beforeunload', () => this.saveLayout());

        // Adicionar suporte a touch para arrastar no mobile
        this.initTouchSupport();
    }

    injectStyles() {
        const styles = document.createElement('style');
        styles.id = 'dashboard-widgets-styles';

        // Se já existe, não adicionar novamente
        if (document.getElementById('dashboard-widgets-styles')) return;

        styles.textContent = `
            /* Animações de Ghost e Drag */
            .widget-ghost {
                opacity: 0.3;
                transform: scale(0.98);
                filter: blur(2px);
            }
            
            .widget-chosen {
                transform: scale(1.02) rotate(1deg);
                box-shadow: 0 25px 60px -15px rgba(124, 58, 237, 0.35),
                            0 0 0 3px rgba(124, 58, 237, 0.2) !important;
                z-index: 1000;
            }
            
            .widget-drag {
                opacity: 0.95;
                cursor: grabbing !important;
            }
            
            .widget-drag-handle:hover {
                cursor: grab;
            }
            
            .widget-drag-handle:active {
                cursor: grabbing;
            }
            
            /* Estado Minimizado */
            .widget-minimized {
                min-height: auto !important;
            }
            
            .widget-minimized .widget-content {
                max-height: 0;
                overflow: hidden;
                opacity: 0;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
            
            /* Transições Suaves */
            .dashboard-widget {
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            
            .dashboard-widget:hover {
                box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12);
            }
            
            .dark .dashboard-widget:hover {
                box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.4);
            }
            
            /* Controles do Widget */
            .widget-controls {
                opacity: 0;
                transform: translateX(10px);
                transition: all 0.3s ease;
            }
            
            .dashboard-widget:hover .widget-controls {
                opacity: 1;
                transform: translateX(0);
            }
            
            /* Botões de Controle */
            .widget-control-btn {
                width: 32px;
                height: 32px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: rgb(156, 163, 175);
                transition: all 0.2s ease;
                background: transparent;
                border: none;
                cursor: pointer;
            }
            
            .widget-control-btn:hover {
                color: #7c3aed;
                background: rgba(124, 58, 237, 0.1);
            }
            
            .dark .widget-control-btn:hover {
                color: #a78bfa;
                background: rgba(124, 58, 237, 0.2);
            }
            
            /* Animação de Entrada */
            .widget-enter {
                animation: widgetEnter 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            }
            
            @keyframes widgetEnter {
                from {
                    opacity: 0;
                    transform: translateY(30px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            
            /* Animação de Toggle */
            .widget-content {
                transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                            opacity 0.3s ease,
                            padding 0.3s ease;
            }
            
            /* Toast de Salvamento */
            #widget-toast {
                font-size: 13px;
                font-weight: 600;
                letter-spacing: 0.025em;
            }
            
            /* Indicador de Arrastar no Mobile */
            .mobile-drag-indicator {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 40px;
                height: 5px;
                background: rgba(124, 58, 237, 0.3);
                border-radius: 3px;
                opacity: 0;
                transition: opacity 0.2s;
            }
            
            .widget-drag-handle:active .mobile-drag-indicator {
                opacity: 1;
            }
            
            /* Responsividade Mobile */
            @media (max-width: 640px) {
                .widget-controls {
                    opacity: 1;
                    transform: translateX(0);
                }
                
                .widget-control-btn {
                    width: 28px;
                    height: 28px;
                    border-radius: 8px;
                }
                
                .widget-chosen {
                    transform: scale(1.01);
                }
            }
            
            /* Efeito Ripple ao Clicar */
            .widget-ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(124, 58, 237, 0.3);
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            }
            
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;

        document.head.appendChild(styles);
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
        const isMobile = window.innerWidth < 640;

        containers.forEach(container => {
            const sortable = new Sortable(container, {
                animation: 300,
                easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                handle: '.widget-drag-handle',
                ghostClass: 'widget-ghost',
                chosenClass: 'widget-chosen',
                dragClass: 'widget-drag',
                filter: '.widget-no-drag',
                delay: isMobile ? 150 : 0,
                delayOnTouchOnly: true,
                touchStartThreshold: 5,
                forceFallback: isMobile,
                fallbackClass: 'widget-fallback',
                fallbackTolerance: 5,
                onStart: (evt) => {
                    document.body.style.cursor = 'grabbing';
                    evt.item.style.zIndex = '1000';

                    // Haptic feedback no mobile (se disponível)
                    if (navigator.vibrate) {
                        navigator.vibrate(50);
                    }
                },
                onEnd: (evt) => {
                    document.body.style.cursor = '';
                    evt.item.style.zIndex = '';
                    this.saveLayout();
                    this.showSaveToast();

                    // Haptic feedback
                    if (navigator.vibrate) {
                        navigator.vibrate([30, 20, 30]);
                    }
                }
            });
            this.sortableInstances.push(sortable);
        });
    }

    initTouchSupport() {
        const handles = document.querySelectorAll('.widget-drag-handle');

        handles.forEach(handle => {
            let longPressTimer;

            handle.addEventListener('touchstart', (e) => {
                longPressTimer = setTimeout(() => {
                    handle.classList.add('touch-active');
                    if (navigator.vibrate) navigator.vibrate(30);
                }, 200);
            }, { passive: true });

            handle.addEventListener('touchend', () => {
                clearTimeout(longPressTimer);
                handle.classList.remove('touch-active');
            }, { passive: true });

            handle.addEventListener('touchmove', () => {
                clearTimeout(longPressTimer);
            }, { passive: true });
        });
    }

    initScrollAnimations() {
        if ('IntersectionObserver' in window) {
            this.animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('widget-enter');
                        this.animationObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('.dashboard-widget').forEach(widget => {
                // Só animar se não tiver animação de delay
                if (!widget.classList.contains('animate-fade-in-up')) {
                    this.animationObserver.observe(widget);
                }
            });
        }
    }

    addWidgetControls() {
        const widgets = document.querySelectorAll('.dashboard-widget');

        widgets.forEach((widget, index) => {
            // Adicionar ID único ao widget se não tiver
            if (!widget.id) {
                widget.id = `widget-${index}`;
            }

            // Verificar se já tem controles
            if (widget.querySelector('.widget-controls')) return;

            // Encontrar o header do widget
            const headerArea = widget.querySelector('.widget-drag-handle')?.parentElement;
            if (!headerArea) return;

            // Criar botões de controle
            const controls = document.createElement('div');
            controls.className = 'widget-controls flex items-center gap-1';

            // Botão minimizar/expandir
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'widget-control-btn';
            toggleBtn.title = 'Minimizar/Expandir';
            toggleBtn.innerHTML = `
                <svg class="minimize-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <svg class="maximize-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            `;
            toggleBtn.onclick = (e) => {
                e.stopPropagation();
                this.createRipple(e, toggleBtn);
                this.toggleWidget(widget, toggleBtn);
            };

            // Botão de refresh (opcional)
            const refreshBtn = document.createElement('button');
            refreshBtn.className = 'widget-control-btn';
            refreshBtn.title = 'Atualizar';
            refreshBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            `;
            refreshBtn.onclick = (e) => {
                e.stopPropagation();
                this.createRipple(e, refreshBtn);
                this.refreshWidget(widget, refreshBtn);
            };

            controls.appendChild(toggleBtn);
            controls.appendChild(refreshBtn);

            headerArea.appendChild(controls);
        });
    }

    createRipple(event, button) {
        const ripple = document.createElement('span');
        ripple.className = 'widget-ripple';

        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (event.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (event.clientY - rect.top - size / 2) + 'px';

        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    }

    toggleWidget(widget, btn) {
        const content = widget.querySelector('.widget-content');
        const minimizeIcon = btn.querySelector('.minimize-icon');
        const maximizeIcon = btn.querySelector('.maximize-icon');

        if (content) {
            const isMinimized = widget.classList.contains('widget-minimized');

            if (isMinimized) {
                // Expandir
                widget.classList.remove('widget-minimized');
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                minimizeIcon?.classList.remove('hidden');
                maximizeIcon?.classList.add('hidden');

                setTimeout(() => {
                    content.style.maxHeight = '';
                }, 400);
            } else {
                // Minimizar
                content.style.maxHeight = content.scrollHeight + 'px';
                content.offsetHeight; // Force reflow
                content.style.maxHeight = '0';
                content.style.opacity = '0';

                setTimeout(() => {
                    widget.classList.add('widget-minimized');
                }, 400);

                minimizeIcon?.classList.add('hidden');
                maximizeIcon?.classList.remove('hidden');
            }

            this.saveLayout();
        }
    }

    refreshWidget(widget, btn) {
        const content = widget.querySelector('.widget-content');
        const icon = btn.querySelector('svg');

        // Animar ícone
        icon.style.animation = 'spin 1s ease-in-out';

        // Simular refresh (em produção, fazer fetch real)
        if (content) {
            content.style.opacity = '0.5';

            setTimeout(() => {
                content.style.opacity = '1';
                icon.style.animation = '';
                this.showSaveToast('Widget atualizado!', 'info');
            }, 800);
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

                // Restaurar estados
                const states = savedContainer.states || {};
                Object.entries(states).forEach(([widgetId, state]) => {
                    const widget = document.getElementById(widgetId);
                    if (widget && state.minimized) {
                        widget.classList.add('widget-minimized');
                        const content = widget.querySelector('.widget-content');
                        if (content) {
                            content.style.maxHeight = '0';
                            content.style.opacity = '0';
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

    showSaveToast(message = 'Layout salvo!', type = 'success') {
        let toast = document.getElementById('widget-toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'widget-toast';
            toast.className = 'fixed bottom-4 right-4 px-5 py-3 rounded-2xl shadow-2xl transform translate-y-full opacity-0 transition-all duration-500 z-[9999] flex items-center gap-3 backdrop-blur-xl border';
            document.body.appendChild(toast);
        }

        // Configurar aparência baseada no tipo
        const configs = {
            success: {
                bg: 'bg-gradient-to-r from-green-500 to-emerald-600',
                border: 'border-green-400/30',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
            },
            info: {
                bg: 'bg-gradient-to-r from-blue-500 to-indigo-600',
                border: 'border-blue-400/30',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            }
        };

        const config = configs[type] || configs.success;

        toast.className = `fixed bottom-4 right-4 ${config.bg} ${config.border} text-white px-5 py-3 rounded-2xl shadow-2xl transform translate-y-full opacity-0 transition-all duration-500 z-[9999] flex items-center gap-3 backdrop-blur-xl border`;

        toast.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${config.icon}
            </svg>
            <span class="font-semibold">${message}</span>
        `;

        // Mostrar
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-full', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        // Esconder
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 2500);
    }

    resetLayout() {
        localStorage.removeItem(this.storageKey);
        this.showSaveToast('Layout resetado!', 'info');
        setTimeout(() => window.location.reload(), 500);
    }

    destroy() {
        this.sortableInstances.forEach(instance => instance.destroy());
        this.sortableInstances = [];

        if (this.animationObserver) {
            this.animationObserver.disconnect();
        }

        const styles = document.getElementById('dashboard-widgets-styles');
        if (styles) styles.remove();
    }
}

// Adicionar estilo de spin para o refresh
const spinStyle = document.createElement('style');
spinStyle.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(spinStyle);

// Inicializar automaticamente
function initDashboardWidgets() {
    if (document.querySelector('.dashboard-widget') || document.querySelector('[data-dashboard-widgets]')) {
        // Se já existir uma instância, destruí-la antes de criar uma nova
        if (window.dashboardWidgets && typeof window.dashboardWidgets.destroy === 'function') {
            window.dashboardWidgets.destroy();
        }
        window.dashboardWidgets = new DashboardWidgets('.widget-container');
    }
}

// Inicializar na carga inicial
initDashboardWidgets();

// Capturar navegação AJAX
document.addEventListener('ajax-content-loaded', function () {
    console.log('DashboardWidgets: Reinicializando para conteúdo AJAX...');
    initDashboardWidgets();
});

// Expor classe globalmente
window.DashboardWidgets = DashboardWidgets;
