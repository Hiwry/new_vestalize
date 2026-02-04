@extends('layouts.admin')
@push('styles')
<style>
    /* Dark mode styles para o Kanban */
    .dark .kanban-filter-container {
        background-color: #1f2937 !important; /* gray-800 */
        border-color: #374151 !important; /* gray-700 */
    }
    .dark .kanban-column-wrapper {
        background-color: #1f2937 !important; /* gray-800 */
        border-color: #374151 !important; /* gray-700 */
    }
    .dark .kanban-column {
        background-color: #1f2937 !important; /* gray-800 */
    }
    .dark .kanban-card {
        background-color: #374151 !important; /* gray-700 */
        border-color: #4b5563 !important; /* gray-600 */
    }
    .dark .kanban-card h3 {
        color: #fff !important;
    }
    .dark .kanban-card .text-gray-600,
    .dark .kanban-card .text-gray-500 {
        color: #d1d5db !important; /* gray-300 */
    }
    .dark .kanban-card .text-gray-900 {
        color: #fff !important;
    }
    .dark .kanban-card .border-t {
        border-color: #4b5563 !important; /* gray-600 */
    }
    .dark .kanban-filter-container label {
        color: #d1d5db !important; /* gray-300 */
    }
    .dark .kanban-filter-container input,
    .dark .kanban-filter-container select {
        background-color: #374151 !important; /* gray-700 */
        border-color: #4b5563 !important; /* gray-600 */
        color: #fff !important;
    }
    .dark .kanban-filter-container input::placeholder {
        color: #9ca3af !important; /* gray-400 */
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kanbanBoardProd', (ordersData, startDate) => ({
            view: 'kanban', // 'kanban' | 'calendar'
            showFilters: true,
            currentDate: startDate ? new Date(startDate + 'T12:00:00') : new Date(),
            events: ordersData,
            
            get currentMonthName() {
                return this.currentDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' });
            },

            get calendarDays() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();
                
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                
                const startDayOfWeek = firstDay.getDay(); 
                
                const days = [];
                
                for (let i = startDayOfWeek; i > 0; i--) {
                    const d = new Date(year, month, 1 - i);
                    days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
                }
                
                for (let i = 1; i <= lastDay.getDate(); i++) {
                    const d = new Date(year, month, i);
                    days.push({ date: d, isCurrentMonth: true, isToday: this.isToday(d) });
                }
                
                const remainingCells = 42 - days.length;
                for (let i = 1; i <= remainingCells; i++) {
                    const d = new Date(year, month + 1, i);
                    days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
                }
                
                return days;
            },

            isToday(date) {
                const today = new Date();
                return date.getDate() === today.getDate() &&
                       date.getMonth() === today.getMonth() &&
                       date.getFullYear() === today.getFullYear();
            },

            getEventsForDay(date) {
                const dateString = date.toISOString().split('T')[0];
                return this.events.filter(event => event.date === dateString);
            },

            prevMonth() {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
            },

            nextMonth() {
                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
            },
            
            goToToday() {
                this.currentDate = new Date();
            },

            init() {
                const savedView = localStorage.getItem('kanban_view_personalized');
                if (savedView) this.view = savedView;
                this.$watch('view', value => localStorage.setItem('kanban_view_personalized', value));
            }
        }));
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/kanban-sortable.js') }}"></script>
@endpush

@section('content')
    <div class="max-w-[1800px] mx-auto pb-24">

        @php
            $calendarEvents = $ordersByStatus->flatten()->map(function($order) {
                $firstItem = $order->items->first();
                $title = $firstItem ? $firstItem->name : $order->client->name;

                return [
                    'id' => $order->id,
                    'title' => $title,
                    'client' => $order->client->name,
                    'date' => $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : null,
                    'delivery_formatted' => $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m') : '', // Formato curto para o card
                    'total' => $order->total,
                    'items_count' => $order->items->sum('quantity'),
                    'status_color' => $order->status->color ?? '#ccc',
                    'status_name' => $order->status->name ?? 'Indefinido'
                ];
            })->values();
        @endphp

        <div x-data="kanbanBoardProd({{ Js::from($calendarEvents) }}, '{{ date('Y-m-d') }}')" x-cloak>
            <!-- Header & Controls -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Personalizados</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Fluxo de pedidos personalizados (Balcão).
                    </p>
                </div>

                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-1.5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <button @click="view = 'kanban'" 
                            :class="{ 'bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300 shadow-sm': view === 'kanban', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': view !== 'kanban' }"
                            class="px-4 py-2 rounded-md font-medium text-sm transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Kanban
                    </button>
                    <button @click="view = 'calendar'" 
                            :class="{ 'bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300 shadow-sm': view === 'calendar', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': view !== 'calendar' }"
                            class="px-4 py-2 rounded-md font-medium text-sm transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Calendário
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                     <div class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 shadow-sm">
                        Total: <strong class="text-gray-900 dark:text-white">{{ $ordersByStatus->flatten()->count() }}</strong>
                    </div>
                    <!-- Link to Personalized List (TODO: Update route when created) -->
                    <a href="{{ route('production.index', ['type' => 'personalized']) }}" 
                       class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Lista
                    </a>
                </div>
            </div>

            <!-- Filtros Collapsible -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden transition-all duration-300"
                 :class="{'max-h-[1000px] opacity-100': showFilters, 'max-h-0 opacity-0 overflow-hidden': !showFilters}">
                <div class="p-5">
                    <form method="GET" action="{{ route('production.kanban') }}" class="space-y-4">
                        <input type="hidden" name="type" value="personalized">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            <!-- Buscar -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Buscar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por cliente, produto..." class="w-full pl-10 px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all text-sm">
                                </div>
                            </div>

                            <!-- Data Entrega -->
                             <div>
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Data Entrega</label>
                                <input type="date" name="delivery_date" value="{{ $deliveryDateFilter }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                            </div>
                        </div>
        
                        <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                             <a href="{{ route('production.pdf', ['type' => 'personalized']) }}" target="_blank" class="text-sm text-pink-600 hover:text-pink-800 dark:text-pink-400 dark:hover:text-pink-300 font-medium flex items-center gap-1 mr-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Exportar PDF
                            </a>
                            <a href="{{ route('production.kanban', ['type' => 'personalized']) }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Limpar
                            </a>
                            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 shadow-lg shadow-pink-500/30 transition-all transform hover:scale-[1.02]">
                                Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Kanban View -->
            <div x-show="view === 'kanban'" class="grid grid-cols-1 md:grid-cols-5 gap-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            @foreach($statuses as $status)
                <div class="kanban-column-wrapper bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 font-semibold flex justify-between items-center" 
                         style="background: {{ $status->color }}; color: #fff">
                        <span>{{ $status->name }}</span>
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded-full text-xs">
                            {{ ($ordersByStatus[$status->id] ?? collect())->count() }}
                        </span>
                    </div>
                    <div class="kanban-column p-3 space-y-3 min-h-[400px] bg-gray-50 dark:bg-gray-800" data-status-id="{{ $status->id }}">
                        @foreach(($ordersByStatus[$status->id] ?? collect()) as $order)
                            @php
                                $firstItem = $order->items->first();
                                $coverImage = $order->cover_image_url ?: $firstItem?->cover_image_url;
                                $displayName = $firstItem ? $firstItem->name : $order->client->name;
                            @endphp
                            <div class="kanban-card bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden cursor-pointer hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-600" 
                                 data-order-id="{{ $order->id }}"
                                 onclick="event.stopPropagation(); openOrderModal({{ $order->id }})">
                                
                                @if($coverImage)
                                <div class="h-32 bg-gray-200 overflow-hidden">
                                    <img src="{{ $coverImage }}" class="w-full h-full object-cover">
                                </div>
                                @endif

                                <div class="p-4">
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-bold text-pink-600 bg-pink-50 px-2 py-1 rounded">
                                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $order->items->sum('quantity') }} pçs
                                                </span>
                                                <span class="kanban-drag-handle flex items-center justify-center w-6 h-6 rounded-md border border-transparent text-gray-400 hover:text-purple-400 hover:border-purple-400/40 cursor-grab active:cursor-grabbing" title="Arrastar" onclick="event.stopPropagation();">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="8" cy="6" r="1.5"></circle>
                                                        <circle cx="16" cy="6" r="1.5"></circle>
                                                        <circle cx="8" cy="12" r="1.5"></circle>
                                                        <circle cx="16" cy="12" r="1.5"></circle>
                                                        <circle cx="8" cy="18" r="1.5"></circle>
                                                        <circle cx="16" cy="18" r="1.5"></circle>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm" title="{{ $displayName }}">
                                            {{ $displayName }}
                                        </h3>
                                         <p class="text-xs text-gray-500 mt-1">{{ $order->client->name }}</p>
                                    </div>
                                    
                                     <!-- Datas -->
                                    <div class="border-t dark:border-gray-600 pt-3 space-y-1 text-xs text-gray-600 dark:text-gray-300">
                                        @if($order->delivery_date)
                                        <div class="flex items-center text-orange-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span><strong>Entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Total -->
                                    <div class="mt-3 pt-3 border-t dark:border-gray-600">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600 dark:text-gray-300">Total:</span>
                                            <span class="text-sm font-bold text-green-600">
                                                R$ {{ number_format($order->total, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            </div>

            <!-- Calendar View (Simplified for Personalizados) -->
            <div x-show="view === 'calendar'" class="calendar-container" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-[calc(100vh-250px)] min-h-[600px]">
                     <!-- Reuse same calendar logic but with personalized events -->
                     @include('components.calendar-view', ['days' => 'calendarDays', 'events' => 'getEventsForDay']) 
                     <!-- Wait, I need to inline the calendar HTML if I don't extract it. I'll just use the one from kanban.blade.php but simplified -->
                      <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-t-xl">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white capitalize" x-text="currentMonthName"></h2>
                             <!-- Controls reuse from main kanban logic in JS -->
                              <div class="flex items-center bg-white dark:bg-gray-700 rounded-md shadow-sm border border-gray-200 dark:border-gray-600">
                                <button @click="prevMonth()" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-l-md text-gray-500 dark:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click="goToToday()" class="px-3 py-1 text-sm font-medium border-l border-r border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200">
                                    Hoje
                                </button>
                                <button @click="nextMonth()" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-r-md text-gray-500 dark:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                     <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide py-2">
                        <div>Dom</div><div>Seg</div><div>Ter</div><div>Qua</div><div>Qui</div><div>Sex</div><div>Sáb</div>
                    </div>
                     <div class="grid grid-cols-7 flex-1 auto-rows-fr overflow-y-auto bg-gray-200 dark:bg-gray-700 gap-px border-b border-l border-gray-200 dark:border-gray-700">
                        <template x-for="day in calendarDays" :key="day.date.toISOString()">
                             <div class="bg-white dark:bg-gray-800 min-h-[120px] p-2 flex flex-col transition-colors hover:bg-gray-50 dark:hover:bg-gray-750 relative group"
                                  :class="{'bg-pink-50/30 dark:bg-pink-900/10': day.isToday, 'bg-gray-50/50 dark:bg-gray-800/50 text-gray-400': !day.isCurrentMonth}">
                                <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full mb-1"
                                      :class="{'bg-pink-600 text-white shadow-md': day.isToday, 'text-gray-700 dark:text-gray-300': !day.isToday}" 
                                      x-text="day.date.getDate()"></span>
                                <div class="flex-1 space-y-1 overflow-y-auto custom-scrollbar">
                                    <template x-for="event in getEventsForDay(day.date)" :key="event.id">
                                        <div @click.stop="openOrderModal(event.id)" 
                                             class="px-2 py-1.5 rounded text-xs border-l-4 shadow-sm cursor-pointer hover:opacity-90 transition-opacity flex flex-col gap-0.5"
                                             :style="`background-color: ${event.status_color}20; border-left-color: ${event.status_color};`">
                                            <div class="font-bold truncate text-gray-900 dark:text-white" :style="`color: ${event.status_color === '#ffffff' ? '#333' : event.status_color}`" x-text="event.title"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                     </div>
                </div>
            </div>
            
        </div>
        
    </div>

    <!-- Modal de Detalhes do Pedido -->
    <div id="order-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <div class="flex justify-between items-center mb-4 pb-4 border-b dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modal-title">Detalhes do Pedido</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="modal-content" class="space-y-6">
                <!-- Será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Drag and Drop functionality
        let draggedElement = null;
        let isDragging = false;
        
        // Re-inicializar eventos após atualizações do DOM (Alpine)
        document.addEventListener('alpine:init', () => {
             // Alpine handles its own re-rendering, but our vanilla drag/drop needs to bind to elements
             // Helper to bind events
        });
        
        // Setup Drag & Drop generic deleation or direct bind
        // Since we use vanilla JS for drag/drop on elements created by PHP loop (server-side rendered list), direct bind works.
        
        function setupDragAndDrop() {
            document.querySelectorAll('.kanban-card').forEach(card => {
                card.draggable = true;
                
                card.addEventListener('dragstart', function(e) {
                    isDragging = true;
                    draggedElement = this;
                    this.style.opacity = '0.5';
                    this.classList.add('scale-95');
                    e.dataTransfer.effectAllowed = 'move';
                });

                card.addEventListener('dragend', function(e) {
                    isDragging = false;
                    this.style.opacity = '1';
                    this.classList.remove('scale-95');
                    draggedElement = null;
                });
                
                // Prevent click when dragging
                card.addEventListener('click', function(e) {
                    if (isDragging) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                });
            });

            document.querySelectorAll('.kanban-column').forEach(column => {
                column.addEventListener('dragover', function(e) {
                    e.preventDefault(); // allow drop
                    this.classList.add('bg-blue-50', 'dark:bg-gray-700', 'border-2', 'border-dashed', 'border-blue-400');
                    e.dataTransfer.dropEffect = 'move';
                });

                column.addEventListener('dragleave', function(e) {
                    this.classList.remove('bg-blue-50', 'dark:bg-gray-700', 'border-2', 'border-dashed', 'border-blue-400');
                });
                
                column.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('bg-blue-50', 'dark:bg-gray-700', 'border-2', 'border-dashed', 'border-blue-400');
                    
                    if (draggedElement) {
                        const orderId = draggedElement.dataset.orderId;
                        const newStatusId = this.dataset.statusId;
                        
                        // Mover visualmente
                        this.appendChild(draggedElement);
                        
                        // Atualizar contador (visual simples, ideal seria reload ou state reactivity)
                        // TODO: Update counters logic if needed
                        
                        updateOrderStatus(orderId, newStatusId);
                    }
                });
            });
        }
        
        // Init Drag and Drop (fallback if Sortable.js is not available)
        window.addEventListener('load', () => {
            if (typeof Sortable === 'undefined') {
                setupDragAndDrop();
            }
        });

        function updateOrderStatus(orderId, statusId) {
            fetch(`/kanban/update-status`, { // Ensure route matches or create personalized specific one? No, existing one works if permissions allowed
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status_id: statusId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Status atualizado!', 'success');
                } else {
                    showNotification('Erro ao atualizar status', 'error');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro na requisição', 'error');
            });
        }

        // Modal Functions
        function openOrderModal(orderId) {
            if (isDragging) return;
            
            fetch(`/kanban/order/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    displayPersonalizedOrderDetails(data);
                    document.getElementById('order-modal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao carregar detalhes', 'error');
                });
        }

        function closeOrderModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        // Close on click outside
        document.getElementById('order-modal').addEventListener('click', function(e) {
            if (e.target === this) closeOrderModal();
        });

        function displayPersonalizedOrderDetails(order) {
            const payment = order.payment;
            
            let html = `
                <!-- Info Cliente -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 border border-gray-100 dark:border-gray-600">
                    <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        ${order.client.name}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">
                        <div><span class="font-semibold">Telefone:</span> ${order.client.phone_primary || '-'}</div>
                        <div><span class="font-semibold">Email:</span> ${order.client.email || '-'}</div>
                        ${order.notes ? `<div class="col-span-2 mt-2 bg-yellow-50 dark:bg-yellow-900/30 p-2 rounded border border-yellow-100 dark:border-yellow-900"><span class="font-semibold text-yellow-800 dark:text-yellow-400">Observações do Pedido:</span> <p class="text-gray-800 dark:text-gray-200">${order.notes}</p></div>` : ''}
                    </div>
                </div>

                <!-- Itens -->
                <div class="space-y-4">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Itens (${order.items.length})
                    </h4>
                    
                    ${order.items.map((item, idx) => `
                        <div class="border dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-700 shadow-sm relative overflow-hidden">
                             <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-bold text-base text-gray-800 dark:text-white">${item.name}</h5>
                                    ${item.customization_notes ? `
                                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 p-2 rounded">
                                            <strong>Nota de Personalização:</strong><br>
                                            ${item.customization_notes}
                                        </div>
                                    ` : ''}
                                    
                                     ${item.files && item.files.length > 0 ? `
                                        <div class="mt-2">
                                            <span class="text-xs font-semibold text-gray-500 uppercase">Arquivos:</span>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                ${item.files.map(f => `<a href="/storage/${f.path}" target="_blank" class="text-blue-600 hover:underline text-xs flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg> Ver Arquivo</a>`).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="text-right">
                                    <span class="block text-xl font-bold text-pink-600">${item.quantity} un.</span>
                                    <span class="text-xs text-gray-500">R$ ${parseFloat(item.unit_price).toFixed(2)} un.</span>
                                </div>
                             </div>
                        </div>
                    `).join('')}
                </div>
                
                <!-- Totais -->
                <div class="mt-6 border-t dark:border-gray-600 pt-4 flex justify-end">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">Total: R$ ${parseFloat(order.total).toFixed(2).replace('.', ',')}</div>
                        ${payment ? `
                            <div class="text-sm text-gray-500 mt-1">
                                Pago: <span class="text-green-600 font-semibold">R$ ${parseFloat(payment.entry_amount).toFixed(2).replace('.', ',')}</span> 
                                (${new Date(payment.entry_date).toLocaleDateString()})
                            </div>
                        ` : '<div class="text-sm text-red-500 mt-1">Pagamento Pendente</div>'}
                    </div>
                </div>
            `;
            
            document.getElementById('modal-title').textContent = `Pedido #${String(order.id).padStart(6, '0')}`;
            document.getElementById('modal-content').innerHTML = html;
        }

        function showNotification(message, type = 'success') {
            // Check if toast container exists
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-5 right-5 z-[100] space-y-2';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            toast.className = `px-4 py-3 rounded shadow-lg text-white text-sm font-medium transition-all transform translate-x-full ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            toast.textContent = message;
            
            container.appendChild(toast);
            
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
            });
            
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
    </div>
    <x-kanban-bottom-nav active-type="personalized" />
@endsection
