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
                
                // Começar no Domingo (0) ou Segunda (1)? Vamos usar Domingo como começo da view do calendário
                const startDayOfWeek = firstDay.getDay(); 
                
                const days = [];
                
                // Dias do mês anterior para preencher
                for (let i = startDayOfWeek; i > 0; i--) {
                    const d = new Date(year, month, 1 - i);
                    days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
                }
                
                // Dias do mês atual
                for (let i = 1; i <= lastDay.getDate(); i++) {
                    const d = new Date(year, month, i);
                    days.push({ date: d, isCurrentMonth: true, isToday: this.isToday(d) });
                }
                
                // Dias do próximo mês para completar 35 ou 42 células (6 semanas)
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
                // Restaurar view salva se existir (opcional)
                const savedView = localStorage.getItem('kanban_view');
                if (savedView) this.view = savedView;

                // Watch para salvar preferência
                this.$watch('view', value => localStorage.setItem('kanban_view', value));
            }
        }));
    });
</script>
@endpush

@section('content')
    <div class="max-w-[1800px] mx-auto">


        <!-- Preparar dados para o calendário -->
        @php
            $calendarEvents = $ordersByStatus->flatten()->map(function($order) {
                $firstItem = $order->items->first();
                $artName = null;
                if ($firstItem && $firstItem->sublimations) {
                    $firstSublimation = $firstItem->sublimations->first();
                    if ($firstSublimation) $artName = $firstSublimation->art_name;
                }
                $title = $artName ?? $order->client->name;

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

        <div x-data="kanbanBoardProd({{ Js::from($calendarEvents) }}, '{{ $startDate }}')" x-cloak>
            <!-- Header & Controls -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Produção</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Gerencie o fluxo de pedidos e acompanhe prazos.
                    </p>
                </div>

                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-1.5 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <button @click="view = 'kanban'" 
                            :class="{ 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 shadow-sm': view === 'kanban', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': view !== 'kanban' }"
                            class="px-4 py-2 rounded-md font-medium text-sm transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Kanban
                    </button>
                    <button @click="view = 'calendar'" 
                            :class="{ 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 shadow-sm': view === 'calendar', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': view !== 'calendar' }"
                            class="px-4 py-2 rounded-md font-medium text-sm transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Calendário
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                     <div class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 shadow-sm">
                        Total: <strong class="text-gray-900 dark:text-white">{{ $ordersByStatus->flatten()->count() }}</strong>
                    </div>
                    <a href="{{ route('production.index') }}" 
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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filtros Avançados
                        </h3>
                        <button @click="showFilters = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form method="GET" action="{{ route('production.kanban') }}" class="space-y-4">
                        <!-- Mantendo os inputs originais mas com novo estilo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                            <!-- Período -->
                            <div class="group">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Período</label>
                                <div class="relative">
                                    <select name="period" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none">
                                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Todo o Período</option>
                                        <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Hoje</option>
                                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Esta Semana</option>
                                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Este Mês</option>
                                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personalizado</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Datas Customizadas -->
                            <div id="start-date-field" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">De</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
        
                            <div id="end-date-field" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Até</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
        
                            <!-- Tipo -->
                            <div class="group">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Tipo</label>
                                <div class="relative">
                                    <select name="personalization_type" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none">
                                        <option value="">Todos os Tipos</option>
                                        @foreach($personalizationTypes as $key => $label)
                                            <option value="{{ $key }}" {{ $personalizationType == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Busca -->
                            <div class="md:col-span-2 lg:col-span-1">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Buscar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar pedido..." class="w-full pl-10 px-3 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                                </div>
                            </div>
                        </div>
        
                        <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                             <a href="{{ route('production.pdf') }}?{{ http_build_query(request()->all()) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium flex items-center gap-1 mr-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Exportar PDF
                            </a>
                            <a href="{{ route('production.kanban') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Limpar
                            </a>
                            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-500/30 transition-all transform hover:scale-[1.02]">
                                Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Botão Filtros Toggle (Quando fechado) -->
            <div x-show="!showFilters" class="mb-6 flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-gray-900 dark:text-white">Filtros ativos:</span>
                    @if($period !== 'all') <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded text-xs border border-indigo-100 dark:border-indigo-800">Período: {{ $period }}</span> @endif
                    @if($search) <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded text-xs border border-indigo-100 dark:border-indigo-800">Busca: {{ $search }}</span> @endif
                    @if($personalizationType) <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded text-xs border border-indigo-100 dark:border-indigo-800">Tipo: {{ $personalizationTypes[$personalizationType] ?? $personalizationType }}</span> @endif
                    @if($period === 'all' && !$search && !$personalizationType) <span class="italic text-gray-400">Nenhum</span> @endif
                </div>
                <button @click="showFilters = true" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Mostrar Filtros
                </button>
            </div>

            <!-- Views -->
            
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
                                // Buscar o nome da arte da primeira personalização
                                $artName = null;
                                if ($firstItem && $firstItem->sublimations) {
                                    $firstSublimation = $firstItem->sublimations->first();
                                    if ($firstSublimation && $firstSublimation->art_name) {
                                        $artName = $firstSublimation->art_name;
                                    }
                                }
                                $displayName = $artName ?? $order->client->name;
                            @endphp
                            <div class="kanban-card bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden cursor-move hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-600" 
                                 draggable="true" 
                                 data-order-id="{{ $order->id }}"
                                 onclick="event.stopPropagation(); openOrderModal({{ $order->id }})">
                                
                                <!-- Imagem de Capa -->
                                @if($coverImage)
                                <div class="h-32 bg-gray-200 overflow-hidden">
                                    <img src="{{ $coverImage }}" 
                                         alt="Capa do Pedido" 
                                         class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="h-32 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @endif

                                <!-- Conteúdo do Card -->
                                <div class="p-4">
                                    <!-- Número do Pedido e Cliente -->
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('orders.show', $order->id) }}" 
                                                   class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100 hover:text-indigo-800 transition-colors">
                                                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                                </a>
                                                @if($order->edit_status === 'requested')
                                                <span class="text-xs font-medium bg-orange-100 text-orange-800 px-2 py-1 rounded-full">
                                                    Aguardando Aprovação
                                                </span>
                                                @elseif($order->edit_status === 'approved')
                                                <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                                    Aprovado
                                                </span>
                                                @elseif($order->edit_status === 'rejected')
                                                <span class="text-xs font-medium bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                                    Rejeitado
                                                </span>
                                                @elseif($order->is_modified)
                                                <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                    Modificado
                                                </span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $order->items->sum('quantity') }} pçs
                                            </span>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate" title="{{ $displayName }}">
                                            {{ $displayName }}
                                        </h3>
                                    </div>

                                    <!-- Vendedor -->
                                    @if($order->seller)
                                    <div class="mb-3">
                                        <div class="flex items-center text-xs text-gray-600 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $order->seller }}">
                                                <strong>Vendedor:</strong> {{ $order->seller }}
                                            </span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Informações do Produto -->
                                    @if($firstItem)
                                    <div class="space-y-2 mb-3 text-xs">
                                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $firstItem->fabric }}">
                                                <strong>Tecido:</strong> {{ $firstItem->fabric }}
                                            </span>
                                        </div>

                                        @if($firstItem->model)
                                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $firstItem->model }}">
                                                <strong>Corte:</strong> {{ $firstItem->model }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($firstItem->print_type)
                                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $firstItem->print_type }}">
                                                <strong>Personalização:</strong> {{ $firstItem->print_type }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Datas -->
                                    <div class="border-t dark:border-gray-600 pt-3 space-y-1 text-xs text-gray-600 dark:text-gray-300">
                                        @if($order->created_at)
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span><strong>Pedido:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</span>
                                        </div>
                                        @endif

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
            <!-- End Kanban View -->
            </div>
            
            <!-- Calendar View -->
            <div x-show="view === 'calendar'" class="calendar-container" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-[calc(100vh-250px)] min-h-[600px]">
                    
                    <!-- Calendar Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-t-xl">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white capitalize" x-text="currentMonthName"></h2>
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
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden md:inline">Visualizando entregas por data</span>
                        </div>
                    </div>

                    <!-- Calendar Grid Header (Days of week) -->
                    <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide py-2">
                        <div>Dom</div>
                        <div>Seg</div>
                        <div>Ter</div>
                        <div>Qua</div>
                        <div>Qui</div>
                        <div>Sex</div>
                        <div>Sáb</div>
                    </div>

                    <!-- Calendar Grid Body -->
                    <div class="grid grid-cols-7 flex-1 auto-rows-fr overflow-y-auto bg-gray-200 dark:bg-gray-700 gap-px border-b border-l border-gray-200 dark:border-gray-700">
                        <template x-for="day in calendarDays" :key="day.date.toISOString()">
                            <div class="bg-white dark:bg-gray-800 min-h-[120px] p-2 flex flex-col transition-colors hover:bg-gray-50 dark:hover:bg-gray-750 relative group"
                                 :class="{'bg-blue-50/30 dark:bg-blue-900/10': day.isToday, 'bg-gray-50/50 dark:bg-gray-800/50 text-gray-400': !day.isCurrentMonth}">
                                
                                <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full mb-1"
                                      :class="{'bg-indigo-600 text-white shadow-md': day.isToday, 'text-gray-700 dark:text-gray-300': !day.isToday}" 
                                      x-text="day.date.getDate()"></span>
                                
                                <!-- Events List -->
                                <div class="flex-1 space-y-1 overflow-y-auto custom-scrollbar">
                                    <template x-for="event in getEventsForDay(day.date)" :key="event.id">
                                        <div @click.stop="openOrderModal(event.id)" 
                                             class="px-2 py-1.5 rounded text-xs border-l-4 shadow-sm cursor-pointer hover:opacity-90 transition-opacity flex flex-col gap-0.5"
                                             :style="`background-color: ${event.status_color}20; border-left-color: ${event.status_color};`">
                                            
                                            <div class="font-bold truncate text-gray-900 dark:text-white" :style="`color: ${event.status_color === '#ffffff' ? '#333' : event.status_color}`" x-text="event.title"></div>
                                            
                                            <div class="flex justify-between items-center text-[10px] text-gray-600 dark:text-gray-400">
                                                <span x-text="'#' + String(event.id).padStart(6, '0')"></span>
                                                <span x-text="event.items_count + ' pçs'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Add Button (Optional, visible on hover) -->
                                <!-- <button class="absolute bottom-2 right-2 p-1 rounded-full bg-indigo-50 text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-indigo-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button> -->
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div> <!-- End x-data -->

    <!-- Modal de Detalhes do Pedido -->
    <div id="order-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-6xl shadow-lg rounded-md bg-white mb-10">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
                <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Detalhes do Pedido</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
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

    <!-- Modal de Cancelamento -->
    <div id="cancellation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[60]">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Cancelar Pedido</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Tem certeza que deseja solicitar o cancelamento deste pedido?
                    </p>
                    <textarea id="cancellation-reason" 
                              class="mt-3 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                              rows="3" 
                              placeholder="Motivo do cancelamento (obrigatório)"></textarea>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirm-cancellation-btn"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Confirmar Cancelamento
                    </button>
                    <button onclick="closeCancellationModal()"
                            class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Voltar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Drag and Drop functionality
        let draggedElement = null;
        let isDragging = false;

        document.querySelectorAll('.kanban-card').forEach(card => {
            card.addEventListener('dragstart', function(e) {
                isDragging = true;
                draggedElement = this;
                this.style.opacity = '0.5';
                this.classList.add('scale-95');
            });

            card.addEventListener('dragend', function(e) {
                setTimeout(() => { isDragging = false; }, 100);
                this.style.opacity = '1';
                this.classList.remove('scale-95');
            });

            // Prevenir click quando estiver arrastando
            card.addEventListener('click', function(e) {
                if (isDragging) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.kanban-column').forEach(column => {
            column.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
            });

            column.addEventListener('dragleave', function(e) {
                this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
            });

            column.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
                
                if (draggedElement) {
                    this.appendChild(draggedElement);
                    
                    const orderId = draggedElement.dataset.orderId;
                    const newStatusId = this.dataset.statusId;
                    
                    // Atualizar status via AJAX
                    updateOrderStatus(orderId, newStatusId);
                }
            });
        });

        function updateOrderStatus(orderId, statusId) {
            fetch(`/kanban/update-status`, {
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
                    showNotification('Status atualizado com sucesso!', 'success');
                } else {
                    showNotification('Erro ao atualizar status', 'error');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao atualizar status', 'error');
                location.reload();
            });
        }

        function openOrderModal(orderId) {
            if (isDragging) return;
            
            fetch(`/kanban/order/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    displayOrderDetails(data);
                    document.getElementById('order-modal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao carregar detalhes do pedido', 'error');
                });
        }

        function closeOrderModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        function displayOrderDetails(order) {
            const payment = order.payment;
            const totalFiles = order.items.reduce((sum, item) => sum + (item.files ? item.files.length : 0), 0);
            
            let html = `
                <!-- Ações -->
                <div class="flex justify-end space-x-3">
                    <button onclick="openCancellationModal(${order.id})" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar Pedido
                    </button>
                </div>

                <!-- Botões de Download -->
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                    <h4 class="font-semibold mb-3 text-indigo-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Downloads
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <a href="/kanban/download-costura/${order.id}" target="_blank"
                           class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Costura (A4)
                        </a>
                        <a href="/kanban/download-personalizacao/${order.id}" target="_blank"
                           class="flex items-center justify-center px-4 py-3 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Personalização (A4)
                        </a>
                        ${totalFiles > 0 ? `
                        <button onclick="downloadAllFiles(${order.id})"
                                class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Arquivos da Arte (${totalFiles})
                        </button>
                        ` : ''}
                    </div>
                </div>

                <!-- Informações do Cliente -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Cliente
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><strong>Nome:</strong> ${order.client.name}</div>
                        <div><strong>Telefone:</strong> ${order.client.phone_primary || '-'}</div>
                        ${order.client.email ? `<div><strong>Email:</strong> ${order.client.email}</div>` : ''}
                        ${order.client.cpf_cnpj ? `<div><strong>CPF/CNPJ:</strong> ${order.client.cpf_cnpj}</div>` : ''}
                    </div>
                </div>

                <!-- Vendedor -->
                ${order.seller ? `
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Vendedor
                    </h4>
                    <div class="text-sm">
                        <div><strong>Nome:</strong> ${order.seller}</div>
                    </div>
                </div>
                ` : ''}

                <!-- Itens do Pedido -->
                <div class="space-y-6">
                    <h4 class="font-bold text-lg text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Itens do Pedido (${order.items.length})
                    </h4>
                    
                    ${order.items.map((item, index) => `
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-2 border-indigo-200 p-6">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-indigo-300">
                            <h5 class="text-xl font-bold text-indigo-900">Item ${item.item_number || index + 1}</h5>
                            <span class="text-sm bg-indigo-600 text-white px-3 py-1 rounded-full font-semibold">${item.quantity} peças</span>
                        </div>

                        <!-- Imagem de Capa -->
                        ${item.cover_image ? `
                        <div class="bg-white rounded-lg p-3 mb-4">
                            <h6 class="font-semibold mb-2 text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Imagem de Capa
                            </h6>
                            <img src="/storage/${item.cover_image}" alt="Capa" class="max-w-sm mx-auto rounded-lg border">
                        </div>
                        ` : ''}

                        <!-- Detalhes da Costura -->
                        <div class="bg-white rounded-lg p-4 mb-4">
                            <h6 class="font-semibold mb-3 text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                Costura
                            </h6>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                                <div><strong>Tecido:</strong> ${item.fabric}</div>
                                <div><strong>Cor:</strong> ${item.color}</div>
                                ${item.collar ? `<div><strong>Gola:</strong> ${item.collar}</div>` : ''}
                                ${item.detail ? `<div><strong>Detalhe:</strong> ${item.detail}</div>` : ''}
                                ${item.model ? `<div><strong>Tipo de Corte:</strong> ${item.model}</div>` : ''}
                                <div><strong>Personalização:</strong> ${item.print_type}</div>
                            </div>
                            
                            <div class="mt-4">
                                <strong class="block mb-2">Tamanhos:</strong>
                                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                    ${Object.entries(item.sizes).map(([size, qty]) => 
                                        qty > 0 ? `
                                        <div class="bg-gray-100 rounded px-2 py-1 text-center">
                                            <span class="text-xs text-gray-600">${size}</span>
                                            <p class="font-bold text-sm">${qty}</p>
                                        </div>
                                        ` : ''
                                    ).join('')}
                                </div>
                            </div>
                        </div>

                        <!-- Personalização -->
                        ${item.sublimations && item.sublimations.length > 0 ? `
                        <div class="bg-white rounded-lg p-4">
                            <h6 class="font-semibold mb-3 text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                Personalização
                            </h6>
                            ${item.art_name ? `<p class="text-sm mb-2"><strong>Nome da Arte:</strong> ${item.art_name}</p>` : ''}
                            <div class="space-y-2">
                                ${item.sublimations.map(sub => {
                                    const sizeName = sub.size ? sub.size.name : sub.size_name;
                                    const sizeDimensions = sub.size ? sub.size.dimensions : '';
                                    const locationName = sub.location ? sub.location.name : sub.location_name;
                                    const appType = sub.application_type ? sub.application_type.toUpperCase() : 'APLICAÇÃO';
                                    
                                    return `
                                    <div class="flex justify-between items-center bg-gray-50 rounded p-3 text-sm">
                                        <div>
                                            <strong>
                                                ${sizeName ? sizeName : appType}${sizeDimensions ? ` (${sizeDimensions})` : ''}
                                            </strong>
                                            ${locationName ? ` - ${locationName}` : ''}
                                            <span class="text-gray-600">x${sub.quantity}</span>
                                            ${sub.color_count > 0 ? `<br><span class="text-xs text-gray-500">${sub.color_count} ${sub.color_count == 1 ? 'Cor' : 'Cores'}${sub.has_neon ? ' + Neon' : ''}</span>` : ''}
                                        </div>
                                        <div class="text-right">
                                            <div class="text-gray-600">R$ ${parseFloat(sub.unit_price).toFixed(2).replace('.', ',')} × ${sub.quantity}</div>
                                            ${sub.discount_percent > 0 ? `<div class="text-xs text-green-600">-${sub.discount_percent}%</div>` : ''}
                                            <div class="font-bold">R$ ${parseFloat(sub.final_price).toFixed(2).replace('.', ',')}</div>
                                        </div>
                                    </div>
                                `}).join('')}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    `).join('')}
                </div>

                <!-- Data de Entrega -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Entrega
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm"><strong>Data de Pedido:</strong></span>
                            <span class="text-sm">${new Date(order.created_at).toLocaleDateString('pt-BR')}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm"><strong>Data de Entrega:</strong></span>
                            <span class="text-sm font-bold text-orange-600">${order.delivery_date ? new Date(order.delivery_date).toLocaleDateString('pt-BR') : 'Não definida'}</span>
                        </div>
                    </div>
                </div>

                <!-- Pagamento -->
                ${payment ? `
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Pagamento
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div><strong>Data de Entrada:</strong> ${new Date(payment.entry_date).toLocaleDateString('pt-BR')}</div>
                        <div><strong>Formas de Pagamento:</strong></div>
                        ${payment.payment_methods.map(method => `
                            <div class="flex justify-between bg-gray-50 rounded p-2">
                                <span class="capitalize">${method.method}</span>
                                <span class="font-bold">R$ ${parseFloat(method.amount).toFixed(2).replace('.', ',')}</span>
                            </div>
                        `).join('')}
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between"><span>Total:</span><strong>R$ ${parseFloat(order.total).toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between"><span>Pago:</span><strong class="text-green-600">R$ ${parseFloat(payment.entry_amount).toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between"><span>${payment.remaining_amount < 0 ? 'Crédito do Cliente:' : 'Restante:'}</span><strong class="${payment.remaining_amount > 0 ? 'text-orange-600' : 'text-green-600'}">R$ ${Math.abs(parseFloat(payment.remaining_amount)).toFixed(2).replace('.', ',')}</strong></div>
                        </div>
                    </div>
                </div>
                ` : ''}

                <!-- Comentários -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Comentários
                    </h4>
                    
                    <!-- Formulário de Novo Comentário -->
                    <div class="mb-4 bg-gray-50 rounded-lg p-3">
                        <textarea id="comment-text-${order.id}" placeholder="Escreva seu comentário..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                  rows="3"></textarea>
                        <button onclick="addComment(${order.id})" 
                                class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                            Adicionar Comentário
                        </button>
                    </div>

                    <!-- Lista de Comentários -->
                    <div id="comments-list-${order.id}" class="space-y-3">
                        ${order.comments && order.comments.length > 0 ? order.comments.map(comment => `
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-500">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-semibold text-sm text-blue-900">${comment.user_name}</span>
                                    <span class="text-xs text-gray-500">${new Date(comment.created_at).toLocaleString('pt-BR')}</span>
                                </div>
                                <p class="text-sm text-gray-700">${comment.comment}</p>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 text-center py-4">Nenhum comentário ainda.</p>'}
                    </div>
                </div>

                <!-- Log de Atendimento -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Log de Atendimento
                    </h4>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        ${order.logs && order.logs.length > 0 ? order.logs.map(log => `
                            <div class="flex items-start space-x-3 text-sm border-l-2 ${
                                log.action === 'status_changed' ? 'border-purple-500 bg-purple-50' :
                                log.action === 'comment_added' ? 'border-blue-500 bg-blue-50' :
                                log.action === 'order_edited' ? 'border-orange-500 bg-orange-50' :
                                'border-gray-500 bg-gray-50'
                            } rounded p-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    ${log.action === 'status_changed' ? 
                                        '<svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' :
                                      log.action === 'comment_added' ? 
                                        '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>' :
                                      log.action === 'order_edited' ? 
                                        '<svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>' :
                                        '<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
                                    }
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-semibold text-gray-900">${log.user_name}</span>
                                        <span class="text-xs text-gray-500">${new Date(log.created_at).toLocaleString('pt-BR')}</span>
                                    </div>
                                    <p class="text-gray-700">${log.description}</p>
                                    ${log.action === 'order_edited' && log.changes ? (() => {
                                        let changesHtml = '<div class="mt-2 space-y-1">';
                                        const changes = typeof log.changes === 'string' ? JSON.parse(log.changes) : log.changes;
                                        
                                        const fieldLabels = {
                                            'name': 'Nome', 'phone_primary': 'Telefone', 'email': 'E-mail',
                                            'fabric': 'Tecido', 'color': 'Cor', 'collar': 'Gola', 'model': 'Modelo',
                                            'detail': 'Detalhe', 'print_type': 'Personalização', 'quantity': 'Quantidade',
                                            'sizes': 'Tamanhos', 'delivery_date': 'Data Entrega', 'total': 'Total',
                                        };
                                        
                                        const formatValue = (val) => {
                                            if (val === null || val === undefined) return '-';
                                            if (typeof val === 'object') return JSON.stringify(val);
                                            return val;
                                        };
                                        
                                        if (changes.client) {
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700">📋 Cliente:</div>';
                                            Object.entries(changes.client).forEach(([field, change]) => {
                                                changesHtml += '<div class="text-xs ml-2"><span class="font-medium">' + (fieldLabels[field] || field) + ':</span> <span class="line-through text-red-500">' + formatValue(change.old) + '</span> → <span class="text-green-600">' + formatValue(change.new) + '</span></div>';
                                            });
                                        }
                                        
                                        if (changes.order) {
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700 mt-1">📦 Pedido:</div>';
                                            Object.entries(changes.order).forEach(([field, change]) => {
                                                changesHtml += '<div class="text-xs ml-2"><span class="font-medium">' + (fieldLabels[field] || field) + ':</span> <span class="line-through text-red-500">' + formatValue(change.old) + '</span> → <span class="text-green-600">' + formatValue(change.new) + '</span></div>';
                                            });
                                        }
                                        
                                        if (changes.items) {
                                            Object.entries(changes.items).forEach(([itemId, itemChange]) => {
                                                if (itemChange.type === 'modified' && itemChange.changes) {
                                                    changesHtml += '<div class="text-xs font-semibold text-orange-700 mt-1">👕 Item ' + itemId + ':</div>';
                                                    Object.entries(itemChange.changes).forEach(([field, change]) => {
                                                        if (field !== 'sublimations' && field !== 'files') {
                                                            changesHtml += '<div class="text-xs ml-2"><span class="font-medium">' + (fieldLabels[field] || field) + ':</span> <span class="line-through text-red-500">' + formatValue(change.old) + '</span> → <span class="text-green-600">' + formatValue(change.new) + '</span></div>';
                                                        }
                                                    });
                                                } else if (itemChange.type === 'added') {
                                                    changesHtml += '<div class="text-xs font-semibold text-green-700 mt-1">➕ Novo Item adicionado</div>';
                                                } else if (itemChange.type === 'removed') {
                                                    changesHtml += '<div class="text-xs font-semibold text-red-700 mt-1">➖ Item removido</div>';
                                                }
                                            });
                                        }
                                        
                                        changesHtml += '</div>';
                                        return changesHtml;
                                    })() : ''}
                                </div>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 text-center py-4">Nenhum log de atendimento ainda.</p>'}
                    </div>
                </div>
            `;
            
            document.getElementById('modal-title').textContent = `Pedido #${String(order.id).padStart(6, '0')}`;
            document.getElementById('modal-content').innerHTML = html;
        }

        function addComment(orderId) {
            const commentText = document.getElementById(`comment-text-${orderId}`).value;

            if (!commentText) {
                showNotification('Por favor, escreva um comentário', 'error');
                return;
            }

            fetch(`/kanban/order/${orderId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    comment: commentText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Comentário adicionado com sucesso!', 'success');
                    // Recarregar detalhes do pedido
                    openOrderModal(orderId);
                } else {
                    showNotification('Erro ao adicionar comentário', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao adicionar comentário', 'error');
            });
        }

        function downloadAllFiles(orderId) {
            window.open(`/kanban/download-files/${orderId}`, '_blank');
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Fechar modal ao clicar fora
        document.getElementById('order-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });

        // Mostrar/ocultar campos de data personalizada
        document.querySelector('select[name="period"]').addEventListener('change', function() {
            const startDateField = document.getElementById('start-date-field');
            const endDateField = document.getElementById('end-date-field');
            const startDateInput = startDateField.querySelector('input[name="start_date"]');
            const endDateInput = endDateField.querySelector('input[name="end_date"]');
            
            if (this.value === 'custom') {
                startDateField.style.display = 'block';
                endDateField.style.display = 'block';
            } else {
                startDateField.style.display = 'none';
                endDateField.style.display = 'none';
                // Limpar os valores quando não for custom
                startDateInput.value = '';
                endDateInput.value = '';
            }
        });

        // Funções de Cancelamento
        let currentCancellationOrderId = null;

        function openCancellationModal(orderId) {
            currentCancellationOrderId = orderId;
            document.getElementById('cancellation-reason').value = '';
            document.getElementById('cancellation-modal').classList.remove('hidden');
            
            // Configurar o botão de confirmação
            const confirmBtn = document.getElementById('confirm-cancellation-btn');
            confirmBtn.onclick = () => submitCancellation(orderId);
        }

        function closeCancellationModal() {
            document.getElementById('cancellation-modal').classList.add('hidden');
            currentCancellationOrderId = null;
        }

        function submitCancellation(orderId) {
            const reason = document.getElementById('cancellation-reason').value;
            
            if (!reason.trim()) {
                showNotification('Por favor, informe o motivo do cancelamento', 'error');
                return;
            }
            
            const confirmBtn = document.getElementById('confirm-cancellation-btn');
            const originalText = confirmBtn.textContent;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Enviando...';
            
            fetch(`/pedidos/${orderId}/cancelar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Solicitação de cancelamento enviada com sucesso!', 'success');
                    closeCancellationModal();
                    closeOrderModal();
                    // Recarregar a página para atualizar o status
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Erro ao solicitar cancelamento', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao processar solicitação', 'error');
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = originalText;
            });
        }

        // Fechar modal de cancelamento ao clicar fora
        document.getElementById('cancellation-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancellationModal();
            }
        });
    </script>
@endsection
