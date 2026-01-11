{{--
    Kanban Card Compact Component
    Card compacto para mobile com swipe-to-action
    
    Uso:
    <x-kanban-card-compact :order="$order" />
--}}

@props(['order'])

@php
    $firstItem = $order->items->first();
    $coverImage = $order->cover_image_url ?: $firstItem?->cover_image_url;
    $artName = $firstItem?->art_name;
    $displayName = $artName ?? ($order->client?->name ?? 'Sem cliente');
    $storeName = $order->store?->name ?? 'Loja Principal';
    $totalItems = $order->items->sum('quantity');
    $isOverdue = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isPast();
    $daysUntilDelivery = $order->delivery_date ? now()->diffInDays($order->delivery_date, false) : null;
@endphp

<div x-data="{ 
    swipeX: 0, 
    startX: 0, 
    swiping: false,
    threshold: 80,
    showActions: false,
    
    handleTouchStart(e) {
        this.startX = e.touches[0].clientX;
        this.swiping = true;
    },
    
    handleTouchMove(e) {
        if (!this.swiping) return;
        const diff = e.touches[0].clientX - this.startX;
        // Só permite swipe para esquerda (ações)
        this.swipeX = Math.min(0, Math.max(-this.threshold * 2, diff));
    },
    
    handleTouchEnd() {
        this.swiping = false;
        if (this.swipeX < -this.threshold) {
            this.swipeX = -this.threshold * 1.5;
            this.showActions = true;
        } else {
            this.swipeX = 0;
            this.showActions = false;
        }
    },
    
    closeActions() {
        this.swipeX = 0;
        this.showActions = false;
    }
}"
     @click.outside="closeActions()"
     class="relative overflow-hidden rounded-lg mb-2 shadow-sm">
    
    {{-- Swipe Actions (aparecem ao deslizar) --}}
    <div class="absolute inset-y-0 right-0 flex items-stretch" style="width: 120px;">
        {{-- Visualizar --}}
        <a href="{{ route('orders.show', $order->id) }}" 
           class="flex-1 flex items-center justify-center bg-indigo-500 text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </a>
        
        {{-- Editar --}}
        @if(Auth::user()->isAdmin())
        <a href="{{ route('orders.edit.start', $order->id) }}" 
           class="flex-1 flex items-center justify-center bg-amber-500 text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
        @endif
    </div>
    
    {{-- Card Content --}}
    <div class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg transition-transform duration-200 ease-out"
         :style="'transform: translateX(' + swipeX + 'px)'"
         @touchstart="handleTouchStart"
         @touchmove="handleTouchMove"
         @touchend="handleTouchEnd"
         @click="!showActions && (typeof openOrderModal === 'function' ? openOrderModal({{ $order->id }}) : window.location.href = '{{ route('orders.show', $order->id) }}')">
        
        <div class="flex items-center p-3 gap-3">
            {{-- Thumbnail/Avatar --}}
            <div class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                @if($coverImage)
                    <img src="{{ $coverImage }}" alt="" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            
            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded">
                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                    
                    @if($order->is_event)
                        <span class="text-[10px] px-1.5 py-0.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded font-medium">
                            EVENTO
                        </span>
                    @endif
                    
                    @if($order->priority === 'alta')
                        <span class="text-[10px] px-1.5 py-0.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded font-medium">
                            URGENTE
                        </span>
                    @endif
                </div>
                
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                    {{ $displayName }}
                </p>
                
                <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ number_format($order->total ?? 0, 2, ',', '.') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        {{ $totalItems }} itens
                    </span>
                </div>
            </div>
            
            {{-- Delivery Badge --}}
            <div class="flex-shrink-0 text-right">
                @if($order->delivery_date)
                    <div class="text-xs font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($daysUntilDelivery <= 2 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400') }}">
                        @if($isOverdue)
                            <span class="inline-flex items-center gap-1 bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Atrasado
                            </span>
                        @elseif($daysUntilDelivery == 0)
                            <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/30 px-2 py-1 rounded">
                                Hoje
                            </span>
                        @elseif($daysUntilDelivery == 1)
                            <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/30 px-2 py-1 rounded">
                                Amanhã
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">
                                {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m') }}
                            </span>
                        @endif
                    </div>
                @endif
                
                {{-- Swipe Indicator --}}
                <div class="text-gray-300 dark:text-gray-600 mt-1" x-show="!showActions">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </div>
        
        {{-- Status Bar Bottom --}}
        <div class="h-1 w-full" style="background-color: {{ $order->status?->color ?? '#6366f1' }}"></div>
    </div>
</div>
