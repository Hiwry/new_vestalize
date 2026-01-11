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

<div class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg safe-area-bottom">
    <nav class="flex items-center justify-around h-16 px-1">
        
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full transition-colors duration-200
                  {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-indigo-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="text-[10px] mt-1 font-medium">Início</span>
        </a>
        
        {{-- Pedidos / Novo Pedido --}}
        @if($canCreateOrder)
        <a href="{{ route('orders.wizard.start') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full transition-colors duration-200
                  {{ request()->routeIs('orders.wizard.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-indigo-500' }}">
            <div class="relative">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg -mt-5 border-4 border-white dark:border-gray-900">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
            <span class="text-[10px] mt-1 font-medium">Novo</span>
        </a>
        @endif
        
        {{-- Kanban (apenas se tiver acesso) --}}
        @if($canAccessKanban)
        <a href="{{ route('kanban.index') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full transition-colors duration-200
                  {{ request()->routeIs('kanban.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-indigo-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
            </svg>
            <span class="text-[10px] mt-1 font-medium">Kanban</span>
        </a>
        @else
        {{-- Clientes (alternativa ao Kanban para quem não tem acesso) --}}
        <a href="{{ route('clients.index') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full transition-colors duration-200
                  {{ request()->routeIs('clients.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-indigo-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="text-[10px] mt-1 font-medium">Clientes</span>
        </a>
        @endif
        
        {{-- Pedidos Lista --}}
        <a href="{{ route('orders.index') }}" 
           class="flex flex-col items-center justify-center flex-1 h-full transition-colors duration-200
                  {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400 hover:text-indigo-500' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span class="text-[10px] mt-1 font-medium">Pedidos</span>
        </a>
        
        {{-- Menu (abre sidebar completa) --}}
        <button @click="$dispatch('toggle-sidebar')" 
                class="flex flex-col items-center justify-center flex-1 h-full transition-colors duration-200 text-gray-500 dark:text-gray-400 hover:text-indigo-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <span class="text-[10px] mt-1 font-medium">Menu</span>
        </button>
    </nav>
</div>

{{-- Safe area padding para iPhone com notch --}}
<style>
    .safe-area-bottom {
        padding-bottom: env(safe-area-inset-bottom, 0);
    }
    
    /* Adicionar padding ao conteúdo principal para não ficar atrás da bottom nav */
    @media (max-width: 767px) {
        main, .main-content {
            padding-bottom: 5rem !important;
        }
    }
</style>
