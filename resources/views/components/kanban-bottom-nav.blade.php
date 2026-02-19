@props(['activeType', 'view' => 'kanban'])

<div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[100] w-auto">
    <div class="kanban-bottom-nav bg-white/95 dark:bg-gray-900/90 backdrop-blur-md px-2 py-2 rounded-full shadow-2xl border border-gray-200 dark:border-white/10 flex items-center gap-1">
        
        <!-- Produção -->
        <a href="{{ route('kanban.index', ['type' => 'production']) }}" 
           class="kanban-bottom-link flex items-center gap-2 px-4 py-2 rounded-full transition-all duration-200 {{ $activeType === 'production' ? 'is-active bg-indigo-600 text-white shadow-lg' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="text-sm font-bold tracking-tight {{ $activeType === 'production' ? '!text-white' : '' }}">Produção</span>
        </a>

        <!-- Separator -->
        <div class="kanban-bottom-separator w-px h-6 bg-gray-300 dark:bg-white/10 mx-1"></div>

        <!-- Personalizados -->
        <a href="{{ route('kanban.index', ['type' => 'personalized']) }}" 
           class="kanban-bottom-link flex items-center gap-2 px-4 py-2 rounded-full transition-all duration-200 {{ $activeType === 'personalized' ? 'is-active bg-pink-600 text-white shadow-lg' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-sm font-bold tracking-tight {{ $activeType === 'personalized' ? '!text-white' : '' }}">Personalizados</span>
        </a>

    </div>
</div>

<style>
    /* Keep the kanban bottom nav in light mode even if a nested .dark class appears */
    html:not(.dark) .kanban-bottom-nav {
        background-color: rgba(255, 255, 255, 0.95) !important;
        border-color: #e5e7eb !important;
    }
    html:not(.dark) .kanban-bottom-nav .kanban-bottom-separator {
        background-color: #d1d5db !important;
    }
    html:not(.dark) .kanban-bottom-nav .kanban-bottom-link,
    html:not(.dark) .kanban-bottom-nav .kanban-bottom-control {
        color: #4b5563 !important;
    }
    html:not(.dark) .kanban-bottom-nav .kanban-bottom-link:hover,
    html:not(.dark) .kanban-bottom-nav .kanban-bottom-control:hover {
        color: #111827 !important;
        background-color: #f3f4f6 !important;
    }
    html:not(.dark) .kanban-bottom-nav .kanban-bottom-link.is-active {
        color: #ffffff !important;
    }
</style>
