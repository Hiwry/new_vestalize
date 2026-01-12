{{-- 
    Bottom Navigation para Mobile
    Substitui o menu lateral em dispositivos móveis com ícones de navegação rápida
    como apps modernos (Instagram, WhatsApp, etc.)
--}}

@php
    $user = Auth::user();
    $canAccessKanban = $user?->isAdmin() || ($user?->tenant?->canAccess('kanban') ?? false);
    $canCreateOrder = $user?->isAdmin() || $user?->isVendedor() || $user?->isAdminLoja();
@endphp

<div id="mobile-bottom-nav" class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] safe-area-bottom">
    <nav class="flex items-center justify-around h-16 max-w-lg mx-auto">
        
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" 
           data-nav-item="dashboard"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[10px] mt-0.5 font-medium">Início</span>
        </a>
        
        {{-- Pedidos Lista --}}
        <a href="{{ route('orders.index') }}" 
           data-nav-item="orders"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-[10px] mt-0.5 font-medium">Pedidos</span>
        </a>
        
        {{-- Novo Pedido (FAB central) --}}
        @if($canCreateOrder)
        <a href="{{ route('orders.wizard.start') }}" 
           data-nav-item="new"
           class="flex flex-col items-center justify-center w-16 h-full relative">
            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/40 -mt-6 transform hover:scale-105 active:scale-95 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
        </a>
        @endif
        
        {{-- Kanban ou Clientes --}}
        @if($canAccessKanban)
        <a href="{{ route('kanban.index') }}" 
           data-nav-item="kanban"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
            </svg>
            <span class="text-[10px] mt-0.5 font-medium">Kanban</span>
        </a>
        @else
        <a href="{{ route('clients.index') }}" 
           data-nav-item="clients"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="text-[10px] mt-0.5 font-medium">Clientes</span>
        </a>
        @endif
        
        {{-- Menu (abre sidebar) --}}
        <button type="button"
                x-data
                @click="$dispatch('toggle-sidebar')"
                class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-colors duration-200 text-gray-500 dark:text-gray-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <span class="text-[10px] mt-0.5 font-medium">Menu</span>
        </button>
    </nav>
</div>

<style>
    /* Safe area para iPhones com notch */
    .safe-area-bottom {
        padding-bottom: env(safe-area-inset-bottom, 0);
    }
    
    /* Estados da navegação */
    .mobile-nav-link {
        color: rgb(107 114 128); /* gray-500 */
    }
    
    .dark .mobile-nav-link {
        color: rgb(156 163 175); /* gray-400 */
    }
    
    .mobile-nav-link.active {
        color: rgb(99 102 241); /* indigo-500 */
    }
    
    .dark .mobile-nav-link.active {
        color: rgb(129 140 248); /* indigo-400 */
    }
</style>

<script>
(function() {
    'use strict';
    
    // Função para atualizar navegação ativa
    function updateBottomNav() {
        const path = window.location.pathname;
        const links = document.querySelectorAll('#mobile-bottom-nav .mobile-nav-link');
        
        links.forEach(link => {
            const navItem = link.dataset.navItem;
            let isActive = false;
            
            switch(navItem) {
                case 'dashboard':
                    isActive = path === '/dashboard' || path === '/';
                    break;
                case 'orders':
                    isActive = path.startsWith('/pedidos') && !path.includes('novo');
                    break;
                case 'kanban':
                    isActive = path.startsWith('/kanban');
                    break;
                case 'clients':
                    isActive = path.startsWith('/clientes');
                    break;
            }
            
            if (isActive) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
    
    // Executar na carga inicial
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateBottomNav);
    } else {
        updateBottomNav();
    }
    
    // Escutar mudanças de URL (para navegação AJAX)
    const originalPushState = history.pushState;
    history.pushState = function() {
        originalPushState.apply(this, arguments);
        setTimeout(updateBottomNav, 100);
    };
    
    const originalReplaceState = history.replaceState;
    history.replaceState = function() {
        originalReplaceState.apply(this, arguments);
        setTimeout(updateBottomNav, 100);
    };
    
    window.addEventListener('popstate', updateBottomNav);
    
    // Expor função globalmente para uso do ajax-navigation.js
    window.updateBottomNav = updateBottomNav;
})();
</script>
