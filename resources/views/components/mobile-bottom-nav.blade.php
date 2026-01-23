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

<div id="mobile-bottom-nav" class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-black/80 backdrop-blur-xl border-t border-white/5 shadow-2xl safe-area-bottom">
    <nav class="flex items-center justify-around h-16 max-w-lg mx-auto px-2">
        
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" 
           data-no-ajax
           data-nav-item="dashboard"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-all duration-300">
            <i class="fa-solid fa-house text-lg mb-1"></i>
            <span class="text-[9px] font-black uppercase tracking-widest">Início</span>
        </a>
        
        {{-- Pedidos Lista --}}
        <a href="{{ route('orders.index') }}" 
           data-nav-item="orders"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-all duration-300">
            <i class="fa-solid fa-receipt text-lg mb-1"></i>
            <span class="text-[9px] font-black uppercase tracking-widest">Pedidos</span>
        </a>
        
        {{-- Novo Pedido (FAB central) --}}
        @if($canCreateOrder)
        <a href="{{ route('orders.wizard.start') }}" 
           data-nav-item="new"
           class="flex flex-col items-center justify-center w-16 h-full relative">
            <div class="w-14 h-14 bg-gradient-to-br from-primary to-purple-600 rounded-2xl flex items-center justify-center shadow-xl shadow-primary/30 -mt-10 border-4 border-black active:scale-90 transition-transform bg-white">
                <i class="fa-solid fa-plus text-white text-xl"></i>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest mt-1 text-muted">Vender</span>
        </a>
        @endif
        
        {{-- Kanban ou Clientes --}}
        @if($canAccessKanban)
        <a href="{{ route('kanban.index') }}" 
           data-nav-item="kanban"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-all duration-300">
            <i class="fa-solid fa-layer-group text-lg mb-1"></i>
            <span class="text-[9px] font-black uppercase tracking-widest">Fluxo</span>
        </a>
        @else
        <a href="{{ route('clients.index') }}" 
           data-nav-item="clients"
           class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-all duration-300">
            <i class="fa-solid fa-users text-lg mb-1"></i>
            <span class="text-[9px] font-black uppercase tracking-widest">Clientes</span>
        </a>
        @endif
        
        {{-- Menu (abre sidebar) --}}
        <button type="button"
                x-data
                @click="$dispatch('toggle-sidebar')"
                class="mobile-nav-link flex flex-col items-center justify-center w-16 h-full transition-all duration-300">
            <i class="fa-solid fa-bars text-lg mb-1"></i>
            <span class="text-[9px] font-black uppercase tracking-widest">Menu</span>
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
        color: #a1a1aa; /* muted */
    }
    
    .mobile-nav-link.active {
        color: #7c3aed; /* primary */
        transform: translateY(-2px);
    }
    
    .mobile-nav-link.active i {
        filter: drop-shadow(0 0 8px rgba(124, 58, 237, 0.4));
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
