<!-- Notificações Flutuantes -->
<div x-data="notificationBell()" 
     x-init="init()"
     class="hidden md:block fixed bottom-6 right-6 z-50">
    
    <!-- Botão do Sininho -->
    <button @click="togglePanel()" 
            class="relative w-14 h-14 bg-purple-600 dark:bg-purple-600 hover:bg-purple-500 dark:hover:bg-purple-500 text-white rounded-full shadow-[0_0_25px_rgba(139,92,246,0.6)] hover:shadow-[0_0_35px_rgba(139,92,246,0.9)] transition-all duration-300 flex items-center justify-center group overflow-visible">
        <!-- Ícone do Sino -->
        <svg class="w-6 h-6 transition-transform group-hover:scale-110" 
             :class="{ 'animate-bounce': hasNew }"
             fill="none" 
             stroke="#ffffff" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Badge de Contagem -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse" style="color: white !important;">
        </span>

        <!-- Pulse Animation -->
        <span x-show="hasNew" 
              class="absolute inset-0 rounded-full bg-purple-600 dark:bg-purple-600 animate-ping opacity-75">
        </span>
    </button>

    <!-- Painel de Notificações -->
    <div x-show="showPanel" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="showPanel = false"
         class="absolute bottom-16 right-0 w-96 max-h-[600px] bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Notificações
                <span x-show="unreadCount > 0" 
                      x-text="'(' + unreadCount + ')'"
                      class="ml-1 text-sm text-indigo-600 dark:text-indigo-400">
                </span>
            </h3>
            <button @click="markAllAsRead()" 
                    x-show="unreadCount > 0"
                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors">
                Marcar todas como lidas
            </button>
        </div>

        <!-- Lista de Notificações -->
        <div class="flex-1 overflow-y-auto">
            <template x-if="loading">
                <div class="flex items-center justify-center py-12">
                    <svg class="animate-spin h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nenhuma notificação</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Você está em dia!</p>
                </div>
            </template>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="notification in notifications" :key="notification.id">
                    <div @click="handleNotificationClick(notification)"
                         :class="{ 'bg-indigo-50 dark:bg-indigo-900/10': !notification.read }"
                         class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0 pr-2">
                                <div class="flex items-center mb-1">
                                    <!-- Ícone por Tipo -->
                                    <div x-html="getNotificationIcon(notification.type)" class="flex-shrink-0 mr-2"></div>
                                    
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate" 
                                       x-text="notification.title"></p>
                                    
                                    <div x-show="!notification.read" 
                                         class="ml-2 w-2 h-2 bg-indigo-600 dark:bg-indigo-400 rounded-full flex-shrink-0"></div>
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-1" 
                                   x-text="notification.message"></p>
                                
                                <div class="flex items-center justify-between">
                                    <p class="text-xs text-gray-500 dark:text-gray-500" 
                                       x-text="formatDate(notification.created_at)"></p>
                                </div>
                            </div>

                            <!-- Botão Delete -->
                            <button @click.stop="deleteNotification(notification.id)"
                                    class="opacity-0 group-hover:opacity-100 flex-shrink-0 ml-2 p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer -->
        <div x-show="notifications.length > 0" 
             class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600 text-center">
            <button @click="clearAll()" 
                    class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 font-medium transition-colors">
                Limpar todas as notificações
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationBell() {
    return {
        notifications: [],
        unreadCount: 0,
        showPanel: false,
        loading: false,
        hasNew: false,
        lastNotificationId: null,

        init() {
            this.fetchNotifications();
            // Verificar novas notificações a cada 60 segundos (otimizado)
            setInterval(() => this.fetchNotifications(), 60000);
        },

        async fetchNotifications() {
            try {
                const response = await fetch('/notifications', {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
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
                
                // Verificar se há novas notificações
                if (this.notifications.length > 0 && data.notifications.length > 0) {
                    if (data.notifications[0].id !== this.lastNotificationId) {
                        this.hasNew = true;
                        this.playNotificationSound();
                        setTimeout(() => { this.hasNew = false; }, 3000);
                    }
                }
                
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
                
                if (data.notifications.length > 0) {
                    this.lastNotificationId = data.notifications[0].id;
                }
            } catch (error) {
                console.error('Erro ao buscar notificações:', error);
            }
        },

        togglePanel() {
            this.showPanel = !this.showPanel;
            if (this.showPanel && this.notifications.length === 0) {
                this.loading = true;
                this.fetchNotifications().then(() => {
                    this.loading = false;
                });
            }
        },

        async handleNotificationClick(notification) {
            // Marcar como lida
            if (!notification.read) {
                await this.markAsRead(notification.id);
            }
            
            // Redirecionar para o link
            if (notification.link) {
                window.location.href = notification.link;
            }
        },

        async markAsRead(id) {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });
                
                // Atualizar localmente
                const notification = this.notifications.find(n => n.id === id);
                if (notification) {
                    notification.read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Erro ao marcar como lida:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });
                
                // Atualizar localmente
                this.notifications.forEach(n => n.read = true);
                this.unreadCount = 0;
            } catch (error) {
                console.error('Erro ao marcar todas como lidas:', error);
            }
        },

        async deleteNotification(id) {
            try {
                await fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });
                
                // Remover localmente
                const notification = this.notifications.find(n => n.id === id);
                if (notification && !notification.read) {
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
                this.notifications = this.notifications.filter(n => n.id !== id);
            } catch (error) {
                console.error('Erro ao deletar notificação:', error);
            }
        },

        async clearAll() {
            if (!confirm('Tem certeza que deseja limpar todas as notificações?')) {
                return;
            }
            
            try {
                const promises = this.notifications.map(n => this.deleteNotification(n.id));
                await Promise.all(promises);
            } catch (error) {
                console.error('Erro ao limpar notificações:', error);
            }
        },

        playNotificationSound() {
            // Som de notificação (opcional)
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZSA0PVK3m8bNiHQU2jdXy0H8rBSh+zPLaizsIGGS57OihUBELTKXh8rplIAU1idHz0YA+BSOBzvLdjjYHGGi48OqmVBQKTKPh8rpnIgU2jdTzz3wsBSh+y/PajToHF2W48OuqWBYMUKPh8rtpIwU3jdXyz4ArBSh+y/PbjToHGGe58OqqWBYMUKPh8r1qJAU4jdXyz4AsBS6Dy/PdizcHGWe68OysWBcMUKPh8r1rJQU4jdXyzIAtBTCCy/PeizcHGGi68OysVxcMTqLg8r5sJgU5jdXyyoEuBTGEy/PeizcHGGi68OytWBcMTqLg8r5tJgU5jdXyyYEvBTGEy/PfizYHGWi68OutWRYMTqLg8r9uJgU6jdXyyoEvBTKEy/PfizYHGWi68OutWBYMTqLg8r9vJwU6jdXyyYEwBTKEy/PgizYHGWi68OutVxYMTqLg8sBwKAU7jdXyyYEwBTOEy/PhizYHGWm68OutVxYMTqLg8sBxKAU7jdXyyIExBTOEy/PhizUHGWm68OutWBYMTqLg8sFyKQU8jdXyyIExBTSEy/PiizUHGWm68OutWBYMTqLg8sFzKgU8jdXyyYEyBTSEy/PiizUHGWm68OutVxYMTqLg8sJzKgU9jdXyyYEzBTWEy/PjizUHGWm68OutVxYMTqLg8sN0KwU9jdXxyYE0BTWF');
                audio.volume = 0.3;
                audio.play().catch(() => {});
            } catch (e) {}
        },

        formatDate(date) {
            const now = new Date();
            const notificationDate = new Date(date);
            const diffInSeconds = Math.floor((now - notificationDate) / 1000);
            
            if (diffInSeconds < 60) {
                return 'Agora mesmo';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `Há ${minutes} min`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `Há ${hours}h`;
            } else if (diffInSeconds < 604800) {
                const days = Math.floor(diffInSeconds / 86400);
                return `Há ${days}d`;
            } else {
                return notificationDate.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            }
        },

        getNotificationIcon(type) {
            const icons = {
                'edit_request': '<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
                'edit_approved': '<svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'edit_rejected': '<svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'cancellation_request': '<svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                'cancellation_approved': '<svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>',
                'cancellation_rejected': '<svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                'budget_approved': '<svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'delivery_request': '<svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'delivery_approved': '<svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                'delivery_rejected': '<svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                'order_moved': '<svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>',
            };
            
            return icons[type] || '<svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }
    };
}
</script>
@endpush

