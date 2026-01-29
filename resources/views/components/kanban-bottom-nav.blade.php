@props(['activeType', 'view' => 'kanban'])

<div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[100] w-auto">
    <div class="bg-gray-900/90 dark:bg-black/80 backdrop-blur-md px-2 py-2 rounded-full shadow-2xl border border-white/10 flex items-center gap-1">
        
        <!-- Produção -->
        <a href="{{ route('kanban.index', ['type' => 'production']) }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-full transition-all duration-200 {{ $activeType === 'production' ? 'bg-indigo-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="text-sm font-bold tracking-tight">Produção</span>
        </a>

        <!-- Separator -->
        <div class="w-px h-6 bg-white/10 mx-1"></div>

        <!-- Personalizados -->
        <a href="{{ route('kanban.index', ['type' => 'personalized']) }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-full transition-all duration-200 {{ $activeType === 'personalized' ? 'bg-pink-600 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-sm font-bold tracking-tight">Personalizados</span>
        </a>

        <!-- Separator -->
        <div class="w-px h-6 bg-white/10 mx-1"></div>

        <!-- View Switcher (Board/Calendar Contextual) -->
        <button @click="view = (view === 'kanban' ? 'calendar' : 'kanban')" 
                class="flex items-center gap-2 px-4 py-2 rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200">
            <template x-if="view === 'kanban'">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-sm font-bold tracking-tight">Calendário</span>
                </div>
            </template>
            <template x-if="view === 'calendar'">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                    </svg>
                    <span class="text-sm font-bold tracking-tight">Quadro</span>
                </div>
            </template>
        </button>

        <!-- Separator -->
        <div class="w-px h-6 bg-white/10 mx-1"></div>

        <!-- List View -->
        <a href="{{ route('production.index', ['type' => $activeType]) }}" 
           class="flex items-center justify-center p-2 rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200"
           title="Visualizar em Lista">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
        </a>

    </div>
</div>
