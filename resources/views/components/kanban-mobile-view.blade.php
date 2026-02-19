{{--
    Kanban Mobile View
    Visualização mobile do Kanban com cards compactos e tabs para status
--}}

@props(['statuses', 'ordersByStatus'])

<div x-data="{ 
    activeStatus: {{ $statuses->first()?->id ?? 0 }},
    touchStartX: 0,
    touchEndX: 0,
    
    handleSwipeStart(e) {
        this.touchStartX = e.touches[0].clientX;
    },
    
    handleSwipeEnd(e) {
        this.touchEndX = e.changedTouches[0].clientX;
        this.handleSwipe();
    },
    
    handleSwipe() {
        const diff = this.touchStartX - this.touchEndX;
        const threshold = 50;
        const statusIds = {{ Js::from($statuses->pluck('id')) }};
        const currentIndex = statusIds.indexOf(this.activeStatus);
        
        if (diff > threshold && currentIndex < statusIds.length - 1) {
            // Swipe left - próximo status
            this.activeStatus = statusIds[currentIndex + 1];
        } else if (diff < -threshold && currentIndex > 0) {
            // Swipe right - status anterior
            this.activeStatus = statusIds[currentIndex - 1];
        }
    }
}" class="md:hidden">
    
    {{-- Status Tabs (scrollable) --}}
    <div class="sticky top-16 z-20 bg-gray-50 dark:bg-gray-900 pb-2 -mx-4 px-4">
        <div class="flex overflow-x-auto gap-2 py-2 scrollbar-hide">
            @foreach($statuses as $status)
                <button @click="activeStatus = {{ $status->id }}"
                        :class="activeStatus === {{ $status->id }} 
                            ? 'ring-2 ring-offset-2 ring-offset-gray-50 dark:ring-offset-gray-900 shadow-lg scale-105' 
                            : 'opacity-70 hover:opacity-100'"
                        class="flex-shrink-0 px-4 py-2 rounded-full text-white text-sm font-semibold transition-all duration-200"
                        style="background-color: {{ $status->color }};">
                    {{ $status->name }}
                    <span class="ml-1 px-1.5 py-0.5 bg-white/20 rounded-full text-xs">
                        {{ ($ordersByStatus[$status->id] ?? collect())->count() }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>
    
    {{-- Cards Container --}}
    <div @touchstart="handleSwipeStart" @touchend="handleSwipeEnd" class="min-h-[50vh]">
        @foreach($statuses as $status)
            <div x-show="activeStatus === {{ $status->id }}"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform translate-x-4"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 class="space-y-2">
                
                @forelse(($ordersByStatus[$status->id] ?? collect()) as $order)
                    <x-kanban-card-compact :order="$order" />
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm">Nenhum pedido neste status</p>
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
    
    {{-- Swipe Hint --}}
    <div class="fixed bottom-20 inset-x-0 flex justify-center pointer-events-none" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <div class="bg-gray-900/80 text-white text-xs px-3 py-1.5 rounded-full flex items-center gap-2">
            <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
            </svg>
            Deslize para mudar status
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
