<!-- Alpine.js -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Sistema de Pedidos</h1>
                <div class="hidden md:flex gap-6">
                    <a href="/" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('/') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('orders.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('pedidos*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                        Pedidos
                    </a>
                    <a href="{{ route('clients.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('clientes*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                        Clientes
                    </a>
                    <a href="{{ route('budget.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('orcamento*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                        Orçamento
                    </a>
                    <a href="{{ route('kanban.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('kanban') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                        Kanban
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('production.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('producao*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                        Produção
                    </a>
                    @endif
                    @auth
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('cash.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('cash*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                            Caixa
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 {{ request()->is('admin*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                            Painel Administrativo
                        </a>
                        @endif
                    @endauth
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button onclick="toggleDarkMode()" 
                        class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out">
                    <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
                
                @auth
                    <!-- Notifications Bell -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open; if(open) markAllAsRead()" 
                                class="relative p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span id="notification-badge" class="hidden absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white stay-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2"></span>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                             style="display: none;">
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notificações</h3>
                            </div>
                            <div id="notifications-list" class="max-h-96 overflow-y-auto">
                                <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Carregando...
                                </div>
                            </div>
                        </div>
                    </div>

                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                            Sair
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
// Sistema de Notificações
(function() {
    'use strict';
    
    let notificationsInterval;

    async function fetchNotifications() {
        try {
            const response = await fetch('/notifications', {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const contentType = response.headers.get('content-type') || '';
            if (!response.ok || !contentType.includes('application/json')) {
                return;
            }
            const text = await response.text();
            let data = null;
            try {
                data = JSON.parse(text);
            } catch (_) {
                return;
            }
            updateNotificationBadge(data.unread_count);
            renderNotifications(data.notifications);
        } catch (error) {
            console.error('Erro ao buscar notifica??es:', error);
            // Silenciosamente falha para n?o interromper a navega??o
        }
    }

    function updateNotificationBadge(count) {
        const badge = document.getElementById('notification-badge');
        if (!badge) return;
        
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function renderNotifications(notifications) {
        const list = document.getElementById('notifications-list');
        if (!list) return;
        
        if (!notifications || notifications.length === 0) {
            list.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma notificação</div>';
            return;
        }

        list.innerHTML = notifications.map(notification => {
            const isUnread = !notification.read;
            const timeAgo = getTimeAgo(notification.created_at);
            
            return `
                <a href="${notification.link || '#'}" 
                   onclick="window.markNotificationAsRead(${notification.id}); return true;"
                   class="block p-3 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition ${isUnread ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">${notification.title}</h4>
                                ${isUnread ? '<div class="w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full"></div>' : ''}
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${notification.message}</p>
                            <span class="text-xs text-gray-500 dark:text-gray-500 mt-1 block">${timeAgo}</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
    }

    function markAsRead(notificationId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token não encontrado');
            return;
        }

        fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(error => console.error('Erro ao marcar notificação como lida:', error));
    }

    function markAllAsReadFunc() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token não encontrado');
            return;
        }

        fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(() => {
            fetchNotifications();
        })
        .catch(error => console.error('Erro ao marcar todas como lidas:', error));
    }

    function getTimeAgo(datetime) {
        const now = new Date();
        const date = new Date(datetime);
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Agora mesmo';
        if (diff < 3600) return `${Math.floor(diff / 60)} min atrás`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h atrás`;
        if (diff < 604800) return `${Math.floor(diff / 86400)}d atrás`;
        
        return date.toLocaleDateString('pt-BR');
    }

    // Expor funções globalmente para uso no Alpine.js e onclick
    window.markNotificationAsRead = markAsRead;
    window.markAllAsRead = markAllAsReadFunc;
    window.fetchNotifications = fetchNotifications;

    // Buscar notificações ao carregar a página
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        initNotifications();
    }

    function initNotifications() {
        // Limpar intervalo anterior se existir
        if (notificationsInterval) {
            clearInterval(notificationsInterval);
        }
        
        // Buscar notificações imediatamente
        fetchNotifications();
        
        // NOTA: O polling automático foi removido daqui pois o componente
        // notifications-bell.blade.php já faz polling a cada 30 segundos.
        // Isso evita requisições duplicadas e melhora a performance.
        // notificationsInterval = setInterval(fetchNotifications, 30000);
    }
})();
</script>
