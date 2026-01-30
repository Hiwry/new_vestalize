@extends('layouts.admin')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    /* Hide scrollbar for horizontal scroll on mobile */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Custom Scrollbar for Calendar Events */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }
</style>
@endpush




@section('content')
<script>
    // Definir componente globalmente para garantir acesso via x-data
    // Movido para dentro do content para funcionar com o sistema de navegação AJAX
    window.kanbanBoardIndex = function(ordersData, startDate) {
        return {
            view: 'kanban', // 'kanban' | 'calendar'
            calendarView: 'month', // 'month' | 'week' | 'day'
            currentDate: startDate ? new Date(startDate + 'T12:00:00') : new Date(),
            events: ordersData,
            
            get currentMonthName() {
                if (this.calendarView === 'day') {
                    return this.currentDate.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' });
                }
                if (this.calendarView === 'week') {
                    const start = new Date(this.currentDate);
                    start.setDate(this.currentDate.getDate() - this.currentDate.getDay());
                    const end = new Date(start);
                    end.setDate(start.getDate() + 6);
                    
                    if (start.getMonth() === end.getMonth()) {
                        return `${start.getDate()} - ${end.getDate()} de ${start.toLocaleString('pt-BR', { month: 'long', year: 'numeric' })}`;
                    } else {
                        return `${start.getDate()} de ${start.toLocaleString('pt-BR', { month: 'short' })} - ${end.getDate()} de ${end.toLocaleString('pt-BR', { month: 'short', year: 'numeric' })}`;
                    }
                }
                return this.currentDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' });
            },

            get calendarDays() {
                const days = [];
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();
                
                if (this.calendarView === 'day') {
                    days.push({ 
                        date: new Date(this.currentDate), 
                        isCurrentMonth: true, 
                        isToday: this.isToday(this.currentDate) 
                    });
                    return days;
                }

                if (this.calendarView === 'week') {
                    const current = new Date(this.currentDate);
                    const day = current.getDay(); // 0 (Domingo) - 6 (Sábado)
                    const startOfWeek = new Date(current);
                    startOfWeek.setDate(current.getDate() - day);

                    for (let i = 0; i < 7; i++) {
                        const d = new Date(startOfWeek);
                        d.setDate(startOfWeek.getDate() + i);
                        days.push({ 
                            date: d, 
                            isCurrentMonth: d.getMonth() === month, 
                            isToday: this.isToday(d) 
                        });
                    }
                    return days;
                }

                // Month View (Default)
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                
                // Domingo como primeiro dia
                const startDayOfWeek = firstDay.getDay(); 
                
                // Dias do mês anterior
                for (let i = startDayOfWeek; i > 0; i--) {
                    const d = new Date(year, month, 1 - i);
                    days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
                }
                
                // Dias do mês atual
                for (let i = 1; i <= lastDay.getDate(); i++) {
                    const d = new Date(year, month, i);
                    days.push({ date: d, isCurrentMonth: true, isToday: this.isToday(d) });
                }
                
                // Preencher grade (42 células = 6 linhas)
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

            prev() {
                if (this.calendarView === 'month') {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                } else if (this.calendarView === 'week') {
                    this.currentDate.setDate(this.currentDate.getDate() - 7);
                    this.currentDate = new Date(this.currentDate); // trigger reactivity
                } else { // day view
                    this.currentDate.setDate(this.currentDate.getDate() - 1);
                    this.currentDate = new Date(this.currentDate);
                }
            },

            next() {
                if (this.calendarView === 'month') {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                } else if (this.calendarView === 'week') {
                    this.currentDate.setDate(this.currentDate.getDate() + 7);
                    this.currentDate = new Date(this.currentDate);
                } else { // day view
                    this.currentDate.setDate(this.currentDate.getDate() + 1);
                    this.currentDate = new Date(this.currentDate);
                }
            },
            
            goToToday() {
                this.currentDate = new Date();
            },

            init() {
                const savedView = localStorage.getItem('kanban_view_mode');
                if (savedView) this.view = savedView;
                this.$watch('view', value => localStorage.setItem('kanban_view_mode', value));
            }
        };
    };
</script>

<!-- Flash Messages -->
@if(session('success'))
<div class="max-w-[1800px] mx-auto mb-4">
    <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-700 dark:text-green-300 hover:text-green-900 dark:hover:text-green-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="max-w-[1800px] mx-auto mb-4">
    <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="text-red-700 dark:text-red-300 hover:text-red-900 dark:hover:text-red-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif



<style>
    /* Force custom scrollbar */
    .kanban-board::-webkit-scrollbar {
        height: 14px;
    }
    .kanban-board::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .kanban-board::-webkit-scrollbar-thumb {
        background-color: #94a3b8;
        border-radius: 8px;
        border: 3px solid #f1f5f9;
    }
    .kanban-board::-webkit-scrollbar-thumb:hover {
        background-color: #64748b;
    }
    .dark .kanban-board::-webkit-scrollbar-track {
        background: #0f172a;
    }
    .dark .kanban-board::-webkit-scrollbar-thumb {
        background-color: #334155;
        border: 3px solid #0f172a;
    }
    
    /* Ultimate Shadow Override */
    :root {
        --kanban-card-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }
    html.dark {
        --kanban-card-shadow: none;
    }

    .custom-card-shadow {
        /* Fallback */
        box-shadow: var(--kanban-card-shadow);
    }
    
    html.dark .custom-card-shadow,
    html.dark .custom-card-shadow:hover,
    html.dark .kanban-card,
    html.dark .kanban-card:hover,
    html.dark .kanban-card-compact,
    html.dark .kanban-card-compact:hover {
        /* Ensure classes also don't interfere */
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        border-color: #262626 !important;
        background-image: none !important;
    }
</style>

<div class="max-w-[1800px] mx-auto">
        <!-- Calendar Data Preparation -->
        @php
            $calendarData = ($ordersForCalendar ?? collect())->map(function($order) {
                $firstItem = $order->items->first();
                $artName = null;
                if ($firstItem && $firstItem->sublimations) {
                    $firstSublimation = $firstItem->sublimations->first();
                    if ($firstSublimation) $artName = $firstSublimation->art_name;
                }
                $title = $firstItem?->art_name ?? ($order->client->name ?? 'Cliente');
                
                // Buscar imagem de capa
                $coverImage = $order->cover_image_url ?: $firstItem?->cover_image_url;

                return [
                    'id' => $order->id,
                    'title' => $title,
                    'client' => $order->client?->name ?? 'N/A',
                    'date' => $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : null,
                    'items_count' => $order->items->sum('quantity'),
                    'status_color' => $order->status->color ?? '#ccc',
                    'cover_image' => $coverImage,
                ];
            })->values();

            // Default start date from filter or today
            $startDate = $deliveryDateFilter ?? request('delivery_date') ?? null;
        @endphp

        <div x-data="kanbanBoardIndex({{ Js::from($calendarData) }}, '{{ $startDate }}')" x-cloak>

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Kanban de {{ $viewType === 'personalized' ? 'Personalizados' : 'Produção' }}</h1>
                <div class="text-sm text-gray-700 mt-1 dark:text-gray-500">
                    Total de Pedidos: <strong>{{ $ordersByStatus->flatten()->count() }}</strong>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- View Toggle -->
                <div class="flex items-center bg-white border border-gray-200 p-1 rounded-full shadow-sm dark:bg-[#0f111a] dark:border-[#1d2331]">
                    <button @click="view = 'kanban'" 
                            :class="{ 'bg-gray-100 text-gray-900 shadow dark:bg-[#1b1f2b] dark:text-white': view === 'kanban', 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200': view !== 'kanban' }"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Kanban
                    </button>
                    <button @click="view = 'calendar'" 
                            :class="{ 'bg-gray-100 text-gray-900 shadow dark:bg-[#1b1f2b] dark:text-white': view === 'calendar', 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200': view !== 'calendar' }"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Calendário
                    </button>
                </div>

                @if(Auth::user()->isAdmin() || Auth::user()->isProducao())
                <a href="{{ route('kanban.columns.index') }}" 
                   class="px-4 py-2 bg-[#7c3aed] text-white dark:text-white rounded-full hover:bg-[#6d28d9] flex items-center space-x-2 shadow-md transition-colors" style="color: white !important;">
                    <i class="fa-solid fa-gear text-white text-sm" style="color: white !important;"></i>
                    <span class="text-white font-bold text-sm" style="color: white !important;">Gerenciar Colunas</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Barra de Busca e Filtros -->
        <div class="rounded-2xl p-5 md:p-6 mb-6 animate-fade-in-down border border-gray-200 bg-white shadow-[0_12px_40px_-24px_rgba(0,0,0,0.08)] dark:border-[#1d2331] dark:bg-[#0d111a] dark:shadow-[0_12px_40px_-24px_rgba(0,0,0,0.8)]">
            <form method="GET" action="{{ route('kanban.index') }}" class="space-y-4">
                <input type="hidden" name="type" value="{{ $viewType }}">
                <!-- Busca -->
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative group">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center pointer-events-none text-gray-500 group-focus-within:text-gray-900 dark:group-focus-within:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search-input"
                               value="{{ $search ?? '' }}"
                               placeholder="Buscar por nº do pedido..." 
                               class="w-full pl-12 pr-4 h-[52px] rounded-full border border-gray-200 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition-all text-sm md:text-base dark:border-[#1f2533] dark:bg-[#0a0d15] dark:text-gray-100">
                    </div>
                    <button type="submit" 
                            class="px-6 md:px-10 py-3 bg-[#7c3aed] text-white stay-white dark:text-white rounded-full hover:bg-[#6d28d9] whitespace-nowrap font-bold transition-all shadow-lg shadow-purple-700/30 flex items-center justify-center gap-2 text-sm md:text-base">
                        <span class="text-white stay-white">Buscar Pedido</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2 flex items-center gap-2 uppercase tracking-wide dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Tipo de Personalização
                        </label>
                        <select name="personalization_type" class="w-full px-4 py-2.5 rounded-full border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition dark:border-[#1f2533] dark:bg-[#0a0d15] dark:text-gray-100">
                            <option value="">Todas as Personalizações</option>
                            @foreach($personalizationTypes ?? [] as $key => $name)
                                <option value="{{ $key }}" {{ request('personalization_type') == $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <a href="{{ route('production.pdf', request()->all()) }}" target="_blank" 
                           class="px-6 py-2.5 bg-indigo-50 text-indigo-700 rounded-full hover:bg-indigo-100 transition font-semibold flex items-center justify-center gap-2 border border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                             PDF
                        </a>
                        @if($search || request('personalization_type'))
                        <a href="{{ route('kanban.index', ['type' => $viewType]) }}" 
                           class="w-full px-6 py-2.5 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 whitespace-nowrap transition font-semibold flex items-center justify-center gap-2 border border-gray-200 dark:bg-[#111724] dark:text-gray-200 dark:hover:bg-[#131a29] dark:border-[#1f2533]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Limpar Filtros
                        </a>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2 flex items-center gap-2 uppercase tracking-wide dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Data de entrega
                        </label>
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <input type="date" name="delivery_date" value="{{ $deliveryDateFilter ?? request('delivery_date') }}" class="w-full pl-12 pr-4 py-2.5 rounded-full border border-gray-200 bg-white text-gray-900 focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition placeholder-gray-500 dark:border-[#1f2533] dark:bg-[#0a0d15] dark:text-gray-100">
                        </div>
                    </div>
                </div>
                
                @if($search || request('personalization_type') || ($deliveryDateFilter ?? request('delivery_date')))
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filtros ativos:</span>
                        @if($search)
                        <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 rounded-full text-sm font-medium flex items-center gap-2">
                            Busca: "{{ $search }}"
                            <a href="{{ route('kanban.index', array_merge(request()->except('search'), ['personalization_type' => request('personalization_type')])) }}" 
                               class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                        @endif
                        @if(request('personalization_type'))
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full text-sm font-medium flex items-center gap-2">
                            Personalização: {{ $personalizationTypes[request('personalization_type')] ?? request('personalization_type') }}
                            <a href="{{ route('kanban.index', array_merge(request()->except('personalization_type'), ['search' => request('search')])) }}" 
                               class="hover:text-purple-600 dark:hover:text-purple-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                        @endif
                        @if($deliveryDateFilter ?? request('delivery_date'))
                        <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 rounded-full text-sm font-medium flex items-center gap-2">
                            Entrega: {{ \Carbon\Carbon::parse($deliveryDateFilter ?? request('delivery_date'))->format('d/m/Y') }}
                            <a href="{{ route('kanban.index', array_merge(request()->except('delivery_date'), ['search' => request('search'), 'personalization_type' => request('personalization_type')])) }}" 
                               class="hover:text-amber-600 dark:hover:text-amber-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                        @endif
                    </div>
                </div>
                @endif
            </form>
        </div>

        @php
            $hasFilters = ($deliveryDateFilter ?? request('delivery_date')) || ($personalizationType ?? request('personalization_type')) || ($search ?? request('search'));
        @endphp

        @if($hasFilters)
        <div x-show="view === 'kanban'" class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Agenda por data de entrega</h3>
                <a href="{{ route('kanban.index', ['type' => $viewType]) }}" class="text-sm text-indigo-600 font-semibold hover:text-indigo-500">Voltar ao Kanban</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse(($ordersForCalendar ?? collect())->groupBy(fn($o) => optional($o->delivery_date)->format('Y-m-d') ?? 'sem_data') as $dateKey => $group)
                    @php $isNoDate = $dateKey === 'sem_data'; @endphp
                    <div class="border border-gray-200 rounded-lg shadow-sm bg-white">
                        <div class="px-3 py-2 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-800">
                                {{ $isNoDate ? 'Sem data' : \Carbon\Carbon::parse($dateKey)->format('d/m/Y') }}
                            </span>
                            <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{{ $group->count() }}</span>
                        </div>
                        <div class="p-3 space-y-3">
                            @foreach($group as $order)
                                @php
                                    $firstItem = $order->items->first();
                                    $displayName = $firstItem?->art_name ?? ($order->client?->name ?? 'Sem cliente');
                                    $storeName = $order->store?->name ?? 'Loja Principal';
                                    $coverImage = $order->cover_image_url ?: $firstItem?->cover_image_url;
                                @endphp
                                <div class="border border-gray-200 rounded-md p-3 bg-white shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start gap-3">
                                        @if($coverImage)
                                        <div class="flex-shrink-0 w-12 h-12 rounded overflow-hidden bg-gray-100 border border-gray-200">
                                            <img src="{{ $coverImage }}" class="w-full h-full object-cover">
                                        </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <a href="{{ route('orders.show', $order->id) }}" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100">
                                                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                                </a>
                                                @if($order->priority)
                                                    <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold
                                                        @if($order->priority === 'alta') bg-red-100 text-red-800
                                                        @elseif($order->priority === 'media') bg-yellow-100 text-yellow-800
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ ucfirst($order->priority) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900 truncate" title="{{ $displayName }}">{{ $displayName }}</p>
                                            <p class="text-xs text-gray-500 truncate" title="{{ $storeName }}">{{ $storeName }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-sm text-gray-500 py-6">Nenhum pedido para a data filtrada.</div>
                @endforelse
            </div>
        </div>
        @endif

        <div x-show="view === 'kanban'" class="kanban-board flex flex-nowrap gap-4 md:gap-6 overflow-x-scroll pb-6 mb-6 w-full snap-x snap-mandatory touch-pan-x">
            @foreach($statuses as $status)
                @php
                    // Gerar um gradiente baseado na cor do status
                    $baseColor = $status->color ?? '#6366f1';
                    $isBlue = str_contains($baseColor, '3b82f6') || str_contains($baseColor, 'blue');
                    $gradient = "linear-gradient(135deg, {$baseColor}, " . ($status->color_secondary ?? $baseColor) . ")";
                @endphp
                <div class="glass-card rounded-xl flex-shrink-0 overflow-hidden snap-start border border-gray-200 flex flex-col transition-all duration-300 bg-white dark:border-white/5 dark:bg-[rgba(18,24,34,0.95)]" style="min-width: 320px; max-width: 320px;">
                    <div class="px-5 py-4 font-bold flex flex-col justify-center text-gray-800 relative dark:text-gray-100">
                        <div class="flex items-center justify-between mb-1">
                             <span class="truncate tracking-wider uppercase text-[12px] font-black text-gray-800 dark:text-gray-100">{{ $status->name }}</span>
                             <button class="text-gray-500 hover:text-white transition-colors">
                                 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                             </button>
                        </div>
                        <span class="text-[11px] font-medium text-gray-600 dark:text-gray-500">
                            {{ ($ordersByStatus[$status->id] ?? collect())->count() }} {{ ($ordersByStatus[$status->id] ?? collect())->count() === 1 ? 'cartão corresponde' : 'cartões correspondem' }} aos filtros
                        </span>
                    </div>
                    <div class="kanban-column p-4 space-y-4 overflow-y-auto bg-transparent scrollbar-thin" style="height: calc(100vh - 400px); min-height: 500px;" data-status-id="{{ $status->id }}">
                        @foreach(($ordersByStatus[$status->id] ?? collect()) as $order)
                            @php
                                $firstItem = $order->items->first();
                                $coverImage = $order->cover_image_url ?: $firstItem?->cover_image_url;
                                $artName = $firstItem?->art_name;
                                $displayName = $artName ?? ($order->client?->name ?? 'Sem cliente');
                                $storeName = $order->store?->name ?? 'Loja Principal';
                                $filesCount = $order->items->sum(fn($item) => $item->files->count());
                                $printType = $firstItem?->print_type ?? 'Sem personalização';
                                $entryDate = $order->entry_date 
                                    ? \Carbon\Carbon::parse($order->entry_date) 
                                    : ($order->created_at ? \Carbon\Carbon::parse($order->created_at) : null);
                                $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date) : null;
                                $quantityTotal = $order->items->sum('quantity');
                                
                                // Verificar se tem sublimação local (para vendas PDV)
                                $hasSublocal = false;
                                $sublocalInfo = [];
                                if ($order->is_pdv) {
                                    foreach ($order->items as $item) {
                                        if ($item->sublimations) {
                                            foreach ($item->sublimations as $sub) {
                                                if ($sub->location_id || $sub->location_name) {
                                                    $hasSublocal = true;
                                                    $locationName = $sub->location ? $sub->location->name : ($sub->location_name ?? 'Local não informado');
                                                    $sizeName = $sub->size ? $sub->size->name : ($sub->size_name ?? '');
                                                    $sublocalInfo[] = [
                                                        'location' => $locationName,
                                                        'size' => $sizeName,
                                                        'quantity' => $sub->quantity,
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="kanban-card custom-card-shadow group/card bg-white border border-gray-200 text-gray-900 rounded-xl overflow-hidden {{ Auth::user()->isAdmin() || Auth::user()->isProducao() ? 'cursor-move' : 'cursor-pointer' }} hover:bg-gray-50 transition-all duration-200 dark:bg-[#22272e] dark:border-[#373e47] dark:text-gray-100 dark:hover:bg-[#2d333b]" 
                                 style="box-shadow: var(--kanban-card-shadow) !important;"
                                 draggable="{{ Auth::user()->isAdmin() || Auth::user()->isProducao() ? 'true' : 'false' }}" 
                                 data-order-id="{{ $order->id }}"
                                 onclick="event.stopPropagation(); if(typeof openOrderModal === 'function') { openOrderModal({{ $order->id }}); }">
                                
                                {{-- Imagem de Capa --}}
                                @if($coverImage)
                                <div class="px-2 pt-2">
                                    <div class="h-44 bg-gray-100 overflow-hidden rounded-lg border border-gray-200 dark:bg-[#0b1221] dark:border-[#0b1221]">
                                        <img src="{{ $coverImage }}" 
                                             alt="Capa do Pedido" 
                                             class="w-full h-full object-cover"
                                             onerror="this.parentElement.innerHTML='<div class=\'h-full w-full bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white/10\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>'">
                                    </div>
                                </div>
                                @endif

                                <!-- Conteúdo do Card -->
                                <div class="p-3 space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-900 rounded-md text-[11px] font-semibold border border-gray-200 dark:bg-[#111827] dark:text-gray-200 dark:border-[#2f3844]">
                                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                            </span>
                                            @if($order->edit_status === 'requested')
                                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-orange-500/20 text-orange-200 border border-orange-400/40">Editado</span>
                                            @endif
                                            @if($order->stock_separation_status === 'in_separation')
                                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-blue-500/20 text-blue-200 border border-blue-400/40 dark:bg-blue-600/20 dark:text-blue-100 dark:border-blue-500/40">Em Separação</span>
                                            @endif
                                            @if($order->stock_status === 'none')
                                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-red-100 text-red-700 border border-red-200 dark:bg-red-500/20 dark:text-red-200 dark:border-red-400/40">Sem estoque</span>
                                            @elseif($order->stock_status === 'partial')
                                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-100 dark:border-yellow-400/40">Estoque parcial</span>
                                            @elseif($order->stock_status === 'total')
                                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-green-100 text-green-700 border border-green-200 dark:bg-green-500/15 dark:text-green-200 dark:border-green-400/30">Estoque ok</span>
                                            @endif
                                            @if($order->is_event)
                                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold bg-purple-600 text-white border border-purple-600 dark:bg-purple-600 dark:text-white dark:border-purple-600">Evento</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1 text-gray-600 text-[11px] dark:text-gray-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <span>{{ $filesCount }} arq.</span>
                                        </div>
                                    </div>

                                    {{-- Status indicators removed for cleaner design --}}
                                    
                                    <!-- Informações Texto -->
                                    <div class="space-y-1">
                                        <h3 class="font-bold text-gray-900 text-[13px] leading-[1.3] dark:text-gray-100">
                                            {{ $displayName }}
                                        </h3>
                                        <p class="text-xs text-gray-600 flex items-center gap-2 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm9 4v9"></path></svg>
                                            <span class="truncate">{{ $storeName }}</span>
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-1 text-[11px] text-gray-600 dark:text-gray-300">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                            <span class="text-gray-800 font-semibold dark:text-gray-200">Tecido:</span>
                                            <span class="min-w-0 whitespace-normal break-words leading-snug">{{ $firstItem?->fabric ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0118 20.944a12.08 12.08 0 01-6 1.722 12.08 12.08 0 01-6-1.722A12.083 12.083 0 015.84 10.578L12 14z"></path></svg>
                                            <span class="text-gray-800 font-semibold dark:text-gray-200">Gola:</span>
                                            <span class="min-w-0 whitespace-normal break-words leading-snug">{{ $firstItem?->collar ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"></path></svg>
                                            <span class="text-gray-800 font-semibold dark:text-gray-200">Corte:</span>
                                            <span class="min-w-0 whitespace-normal break-words leading-snug">{{ $firstItem?->model ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 11h8m-6 4h6"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-gray-800 font-semibold dark:text-gray-200">Personalização:</span>
                                            <span class="min-w-0 whitespace-normal break-words leading-snug">{{ $printType }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2a3 3 0 00-.879-2.121M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2a3 3 0 01.879-2.121M12 12a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                                            <span class="text-gray-800 font-semibold dark:text-gray-200">Vendedor:</span>
                                            <span class="min-w-0 whitespace-normal break-words leading-snug">{{ $order->user?->name ?? 'Sem vendedor' }}{{ $order->store ? ' - ' . $order->store->name : '' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 4 9-4M4 10v6l8 4 8-4v-6"></path></svg>
                                            <span class="text-gray-800 font-semibold dark:text-gray-200">Qtd:</span>
                                            <span>{{ $quantityTotal }} unid</span>
                                        </div>
                                    </div>

                                    <!-- Footer do Card -->
                                    <div class="flex items-start justify-between text-[11px] font-medium text-gray-600 border-t border-gray-200 pt-3 dark:text-gray-400 dark:border-[#2f3844]">
                                        <div class="space-y-1">
                                            @if($entryDate)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span>Pedido: {{ $entryDate->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                            @if($deliveryDate)
                                                <div class="flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Entrega: {{ $deliveryDate->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="text-right space-y-1">
                                            <span class="block text-[12px] text-gray-400">Total</span>
                                            <div class="text-emerald-500 font-bold text-base">
                                                R$ {{ number_format($order->total, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            </div>


        <!-- Calendar View -->
        <div x-show="view === 'calendar'" 
             class="calendar-container" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100">
             
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-[calc(100vh-250px)] min-h-[600px] p-4">
                
                <!-- Cabeçalho do Calendário -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between mb-4 border-b border-gray-200 dark:border-gray-700 pb-4 gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-gray-100 leading-tight" 
                            x-text="currentMonthName.charAt(0).toUpperCase() + currentMonthName.slice(1)"></h2>
                        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1 shrink-0">
                            <button @click="prev()" class="p-1 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-colors">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button @click="goToToday()" class="px-2 md:px-3 py-1 text-xs md:text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                                Hoje
                            </button>
                            <button @click="next()" class="p-1 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-colors">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- View Switcher -->
                    <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button @click="calendarView = 'month'" 
                                :class="{ 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': calendarView === 'month', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': calendarView !== 'month' }"
                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs md:text-sm font-medium rounded-md transition-all min-w-[60px]">
                            Mês
                        </button>
                        <button @click="calendarView = 'week'" 
                                :class="{ 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': calendarView === 'week', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': calendarView !== 'week' }"
                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs md:text-sm font-medium rounded-md transition-all min-w-[70px]">
                            Semana
                        </button>
                        <button @click="calendarView = 'day'" 
                                :class="{ 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': calendarView === 'day', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': calendarView !== 'day' }"
                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs md:text-sm font-medium rounded-md transition-all min-w-[60px]">
                            Dia
                        </button>
                    </div>
                </div>

                <!-- Dias da Semana (Header) - Hide on Day view -->
                <div x-show="calendarView !== 'day'" class="grid grid-cols-7 mb-1 border-b border-gray-200 dark:border-gray-700">
                    <template x-for="day in ['D', 'S', 'T', 'Q', 'Q', 'S', 'S']">
                        <div class="text-center text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 py-1 sm:py-2" x-text="day"></div>
                    </template>
                </div>

                <!-- Grid do Calendário -->
                <div class="grid gap-px bg-gray-200 dark:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden flex-1"
                     :class="{ 'grid-cols-7': calendarView !== 'day', 'grid-cols-1': calendarView === 'day' }">
                    <template x-for="day in calendarDays" :key="day.date.toISOString()">
                        <div class="bg-white dark:bg-gray-800 p-1 sm:p-2 transition-colors relative group overflow-hidden flex flex-col"
                             :class="{ 
                                'bg-gray-50 dark:bg-gray-800/50 text-gray-400': !day.isCurrentMonth && calendarView === 'month',
                                'bg-blue-50/30 dark:bg-blue-900/10': day.isToday
                             }">
                             
                            <!-- Data -->
                            <div class="flex items-center justify-between mb-1 sm:mb-2 flex-shrink-0">
                                <span class="text-xs sm:text-sm font-medium w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center rounded-full"
                                      :class="{ 
                                        'bg-blue-600 text-white': day.isToday,
                                        'text-gray-700 dark:text-gray-300': !day.isToday && (day.isCurrentMonth || calendarView !== 'month'),
                                        'text-gray-400 dark:text-gray-600': !day.isToday && !day.isCurrentMonth && calendarView === 'month'
                                      }" 
                                      x-text="day.date.getDate()">
                                </span>
                                <span x-show="calendarView === 'day'" class="text-sm text-gray-500 dark:text-gray-400 font-medium" x-text="day.date.toLocaleDateString('pt-BR', { weekday: 'long' })"></span>
                            </div>

                            <!-- Eventos -->
                            <div class="space-y-1 overflow-y-auto custom-scrollbar flex-1">
                                <template x-for="event in getEventsForDay(day.date)">
                                    <div @click.stop="openOrderModal(event.id)"
                                         class="px-1.5 sm:px-3 py-1 sm:py-2 rounded mdCursorPointer transition-all border-l-2 sm:border-l-4 shadow-sm relative overflow-hidden mb-1"
                                         :style="`background-color: ${event.status_color}25; border-left-color: ${event.status_color};`">
                                        
                                        <div class="flex items-center sm:items-start gap-1.5 sm:gap-3">
                                            <!-- Miniatura da Imagem -->
                                            <template x-if="event.cover_image">
                                                <div class="flex-shrink-0 w-6 h-6 sm:w-10 sm:h-10 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-700 border border-black/5">
                                                    <img :src="event.cover_image" 
                                                         class="w-full h-full object-cover"
                                                         x-on:error="$el.style.display='none'">
                                                </div>
                                            </template>

                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-[10px] sm:text-sm truncate leading-tight" 
                                                     :style="`color: ${event.status_color}`"
                                                     x-text="event.title"></div>
                                                <div class="hidden sm:flex justify-between items-center mt-1">
                                                    <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 opacity-90" x-text="'#' + event.id.toString().padStart(5, '0')"></span>
                                                    <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 opacity-90" x-text="event.items_count + ' pçs'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detalhes extras apenas na visão de Dia -->
                                        <div x-show="calendarView === 'day'" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700/50">
                                            <div class="text-xs text-gray-700 dark:text-gray-300 font-medium truncate" x-text="event.client"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </div>

        </div> <!-- Close x-data -->
    </div> <!-- Close max-w -->

    <!-- Modal de Detalhes do Pedido -->
    <div id="order-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[70]">
        <div class="relative top-5 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-full max-w-7xl shadow-lg dark:shadow-gray-900/25 rounded-md bg-white dark:bg-gray-800 mb-5">
            <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div id="modal-title" class="flex-1 mr-4">Detalhes do Pedido</div>
                <button onclick="closeOrderModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="modal-content" class="space-y-6">
                <!-- Será preenchido via JavaScript - Layout em 2 colunas -->
            </div>
        </div>
    </div>

    <!-- Modal de Pagamento Adicional -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[70]">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-lg shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    Registrar Pagamento
                </h3>
                <button onclick="closePaymentModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="payment-form" onsubmit="submitPayment(event)" class="space-y-5">
                <input type="hidden" id="payment-order-id" name="order_id">
                
                <!-- Valor Restante -->
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-600/30">
                    <label class="block text-xs text-orange-700 dark:text-orange-400 font-medium mb-1">Valor Restante</label>
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="remaining-amount">R$ 0,00</div>
                </div>

                <!-- Valor a Pagar -->
                <div class="space-y-2">
                    <label for="payment-amount" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Valor a Pagar *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <input type="number" 
                               id="payment-amount" 
                               name="amount" 
                               step="0.01" 
                               min="0.01"
                               required
                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-green-500 dark:focus:border-green-500 focus:ring-1 focus:ring-green-500 dark:focus:ring-green-500 text-sm"
                               placeholder="0,00">
                    </div>
                </div>

                <!-- Forma de Pagamento -->
                <div class="space-y-2">
                    <label for="payment-method" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Forma de Pagamento *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <select id="payment-method" 
                                name="payment_method" 
                                required
                                class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-green-500 dark:focus:border-green-500 focus:ring-1 focus:ring-green-500 dark:focus:ring-green-500 text-sm">
                            <option value="">Selecione...</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="pix">PIX</option>
                            <option value="cartao">Cartão</option>
                            <option value="transferencia">Transferência</option>
                            <option value="boleto">Boleto</option>
                        </select>
                    </div>
                </div>

                <!-- Data do Pagamento -->
                <div class="space-y-2">
                    <label for="payment-date" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Data do Pagamento *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <input type="date" 
                               id="payment-date" 
                               name="payment_date" 
                               required
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-green-500 dark:focus:border-green-500 focus:ring-1 focus:ring-green-500 dark:focus:ring-green-500 text-sm">
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="closePaymentModal()"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-green-600 dark:bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Registrar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edição de Imagem de Capa -->
    <div id="cover-image-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[70]">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-lg shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    Editar Imagem de Capa
                </h3>
                <button onclick="closeCoverImageModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="cover-image-form" enctype="multipart/form-data" onsubmit="submitCoverImage(event)" class="space-y-5">
                @csrf
                <input type="hidden" id="cover-item-id" name="item_id">
                
                <!-- Informação sobre tamanho recomendado -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-600/30">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-xs text-blue-700 dark:text-blue-400 font-medium">Tamanho Recomendado</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">794 x 1123 pixels (A4 em 96 DPI)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Selecionar Imagem -->
                <div class="space-y-2">
                    <label for="cover-image-input" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">
                        Selecione uma imagem *
                    </label>
                    <input type="file" 
                           id="cover-image-input" 
                           name="cover_image" 
                           accept="image/*" 
                           onchange="previewCoverImage(this)"
                           class="hidden"
                           data-paste-upload="true"
                           data-paste-images-only="true"
                           data-paste-max-size="10"
                           data-paste-extensions="jpg,jpeg,png,gif">
                      <button type="button" 
                              onclick="window.openCoverImagePicker ? window.openCoverImagePicker() : document.getElementById('cover-image-input')?.click()"
                              class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white !text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg"
                              style="color: #fff;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Adicionar Imagem (Ctrl+V, Arraste ou Clique)
                    </button>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2">
                        💡 Use Ctrl+V para colar da área de transferência!
                    </p>
                </div>
                
                <!-- Preview da Imagem -->
                <div id="cover-preview" class="hidden">
                    <label class="block text-xs text-gray-600 dark:text-gray-300 font-medium mb-2">Visualização</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <img id="cover-preview-img" src="" alt="Preview" class="max-w-full max-h-64 mx-auto object-contain rounded-lg">
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeCoverImageModal()" 
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                        Cancelar
                    </button>
                      <button type="submit" 
                              class="px-6 py-2.5 bg-indigo-600 dark:bg-indigo-600 text-white !text-white text-sm font-medium rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition flex items-center"
                              style="color: #fff;">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar Imagem
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Movimentação -->
    <div id="move-confirm-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[70]">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-md shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    Confirmar Movimentação
                </h3>
                <button onclick="closeMoveConfirmModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Conteúdo -->
            <div class="mb-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Tem certeza que deseja mover este pedido para <strong id="move-confirm-status-name" class="text-indigo-600 dark:text-indigo-400"></strong>?
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Esta ação atualizará o status do pedido no kanban.
                </p>
            </div>
            
            <!-- Botões -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeMoveConfirmModal()" 
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                    Cancelar
                </button>
                <button type="button" onclick="confirmMoveCard()" 
                        class="px-6 py-2.5 bg-indigo-600 dark:bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirmar Movimentação
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitação de Edição -->
    <div id="editRequestModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-[70]">
        <div class="relative top-20 mx-auto p-5 border border-gray-300 dark:border-gray-700 w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-900/30">
                            <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Solicitar Edição</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pedido <span id="modalEditOrderId" class="font-semibold"></span></p>
                        </div>
                    </div>
                    <button onclick="closeEditRequestModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="mt-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Esta solicitação será enviada para aprovação do administrador.
                                </p>
                            </div>
                        </div>
                    </div>

                    <label for="editRequestReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motivo da Edição <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <textarea 
                        id="editRequestReason" 
                        rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Descreva o motivo pelo qual este pedido precisa ser editado..."
                        maxlength="1000"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo de 1000 caracteres</p>
                    <p id="editReasonError" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden">O motivo é obrigatório</p>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        onclick="closeEditRequestModal()" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                        Cancelar
                    </button>
                    <button 
                        onclick="submitEditRequest()" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                        Solicitar Edição
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitação de Antecipação -->
    <div id="delivery-request-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[1000]">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-lg shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    Solicitar Antecipação
                </h3>
                <button onclick="closeDeliveryRequestModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="delivery-request-form" onsubmit="submitDeliveryRequest(event)" class="space-y-5">
                <input type="hidden" id="delivery-order-id" name="order_id">
                <input type="hidden" id="current-delivery-date" name="current_delivery_date">
                
                <!-- Data de Entrega Atual -->
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-600/30">
                    <label class="block text-xs text-orange-700 dark:text-orange-400 font-medium mb-1">Data de Entrega Atual</label>
                    <div class="text-lg font-bold text-orange-600 dark:text-orange-400" id="current-delivery-display">-</div>
                </div>

                <!-- Nova Data Solicitada -->
                <div class="space-y-2">
                    <label for="requested-delivery-date" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Nova Data Solicitada *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <input type="date" 
                               id="requested-delivery-date" 
                               name="requested_delivery_date" 
                               required
                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-500 text-sm">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            A data deve ser anterior à data de entrega atual
                        </p>
                    </div>
                </div>

                <!-- Motivo da Solicitação -->
                <div class="space-y-2">
                    <label for="delivery-reason" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Motivo da Solicitação *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <textarea id="delivery-reason" 
                                  name="reason" 
                                  rows="4"
                                  required
                                  maxlength="500"
                                  placeholder="Explique o motivo da antecipação..."
                                  class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-500 text-sm resize-none"></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-right">Máximo 500 caracteres</p>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="closeDeliveryRequestModal()"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 dark:bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Enviar Solicitação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variáveis globais para confirmação de movimentação (fora da IIFE para serem acessíveis)
        let pendingMoveOrderId = null;
        let pendingMoveStatusId = null;
        
        // Garantir que o código seja executado após o DOM estar pronto
        (function() {
            // Função para busca com Enter
            window.handleSearchKeypress = function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    // Submeter o formulário pai do input de busca
                    event.target.closest('form').submit();
                }
            };

            // Drag and Drop functionality (apenas para administradores)
            let draggedElement = null;
            let isDragging = false;
            const isAdmin = {{ Auth::user()->isAdmin() ? 'true' : 'false' }};

            if (isAdmin) {
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
        }

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
            
            // Armazenar orderId no modal para uso posterior
            const orderModal = document.getElementById('order-modal');
            if (orderModal) {
                orderModal.setAttribute('data-current-order-id', orderId);
            }
            
            fetch(`/kanban/order/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    displayOrderDetails(data);
                    orderModal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao carregar detalhes do pedido', 'error');
                });
        }
        
        // Expor funções globalmente
        window.openOrderModal = openOrderModal;
        
        function closeOrderModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }
        
        window.closeOrderModal = closeOrderModal;

        function handleFileUpload(input, itemId) {
            if (!input.files || !input.files[0]) return;
            
            const file = input.files[0];
            const formData = new FormData();
            formData.append('item_id', itemId);
            formData.append('file', file);
            
            // Mostrar estado de carregamento
            const btn = input.nextElementSibling;
            const originalContent = btn.innerHTML;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Enviando...`;
            btn.disabled = true;
            
            fetch('/kanban/upload-item-file', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Arquivo enviado com sucesso!', 'success');
                    
                    // Adicionar arquivo à lista visualmente
                    const list = document.getElementById(`files-list-${itemId}`);
                    const noFilesMsg = document.getElementById(`no-files-msg-${itemId}`);
                    if (noFilesMsg) noFilesMsg.remove();
                    
                    const fileHtml = `
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-900 dark:text-gray-300 font-medium">${data.file.file_name}</span>
                                ${data.file.file_name.toLowerCase().endsWith('.cdr') || data.file.file_name.toLowerCase().endsWith('.cdrx') ? '<span class="ml-2 px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded border border-purple-200 dark:border-purple-800">Corel</span>' : ''}
                            </div>
                            <div class="flex space-x-2">
                                <a href="/storage/${data.file.file_path}" target="_blank" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    `;
                    list.insertAdjacentHTML('beforeend', fileHtml);

                    // Atualizar botão de download
                    const orderModal = document.getElementById('order-modal');
                    const orderId = orderModal ? orderModal.getAttribute('data-current-order-id') : null;
                    if (orderId) {
                        const downloadsList = document.getElementById(`downloads-list-${orderId}`);
                        if (downloadsList) {
                            let btn = document.getElementById(`btn-download-files-${orderId}`);
                            if (btn) {
                                // Update count
                                let count = parseInt(btn.getAttribute('data-count') || 0) + 1;
                                btn.setAttribute('data-count', count);
                                const span = btn.querySelector('.btn-text');
                                if (span) span.textContent = `Arquivos da Arte (${count})`;
                            } else {
                                // Create button
                                const btnHtml = `
                                    <button onclick="downloadAllFiles(${orderId})"
                                            id="btn-download-files-${orderId}"
                                            data-count="1"
                                            class="flex items-center justify-center px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                        <span class="btn-text text-white">Arquivos da Arte (1)</span>
                                    </button>
                                `;
                                downloadsList.insertAdjacentHTML('beforeend', btnHtml);
                            }
                        }
                    }
                    
                } else {
                    showNotification(data.message || 'Erro ao enviar arquivo', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao processar envio', 'error');
            })
            .finally(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                input.value = ''; // Reset input
            });
        }

        function displayOrderDetails(order) {
            const payment = order.payment;
            // Contar arquivos das personalizações E dos itens
            const totalFiles = order.items.reduce((sum, item) => {
                let itemFilesCount = 0;
                // Contar arquivos das personalizações
                if (item.sublimations) {
                    item.sublimations.forEach(sub => {
                        if (sub.files) {
                            itemFilesCount += sub.files.length;
                        }
                    });
                }
                // Contar arquivos dos itens
                if (item.files) {
                    itemFilesCount += item.files.length;
                }
                return sum + itemFilesCount;
            }, 0);
            
            let html = `
                <!-- Container principal com 2 colunas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- COLUNA ESQUERDA -->
                    <div class="space-y-6">
                        <!-- Ações do Pedido -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 border-2 border-blue-200 dark:border-blue-600/30">
                    <h4 class="font-semibold mb-3 text-blue-900 dark:text-blue-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        Ações
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="openEditRequestModal(${order.id})" 
                                class="px-3 py-1.5 text-sm bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-medium rounded-md transition-all flex items-center shadow">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Pedido
                        </button>
                        @if(Auth::user()->isAdmin() || Auth::user()->isProducao())
                        <div class="flex gap-2 flex-1">
                            <select id="move-status-select" 
                                    class="flex-1 px-3 py-1.5 text-sm bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-600/30 rounded-md text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
                                <option value="">Selecione a coluna...</option>
                                @foreach($statuses as $status)
                                <option value="{{ $status->id }}" data-status-id="{{ $status->id }}">
                                    {{ $status->name }}
                                </option>
                                @endforeach
                            </select>
                            <button onclick="moveCardToColumn(${order.id})" 
                                    class="px-4 py-1.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-md transition-all flex items-center shadow">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mover
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                    <!-- Botões de Download -->
                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-600/30">
                    <h4 class="font-semibold mb-3 text-indigo-900 dark:text-indigo-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Downloads
                    </h4>
                    <div class="flex flex-wrap gap-2" id="downloads-list-${order.id}">
                        <a href="/kanban/download-costura/${order.id}" target="_blank"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Costura (A4)
                        </a>
                        <a href="/kanban/download-personalizacao/${order.id}" target="_blank"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium bg-pink-600 hover:bg-pink-700 text-white rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Personalização (A4)
                        </a>
                        ${totalFiles > 0 ? `
                        <button onclick="downloadAllFiles(${order.id})"
                                id="btn-download-files-${order.id}"
                                data-count="${totalFiles}"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <span class="btn-text text-white">Arquivos da Arte (${totalFiles})</span>
                        </button>
                        ` : ''}
                    </div>
                </div>

                    <!-- Informações do Cliente -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Cliente
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <div><strong class="text-gray-900 dark:text-gray-100">Nome:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.name}</span></div>
                        <div><strong class="text-gray-900 dark:text-gray-100">Telefone:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.phone_primary || '-'}</span></div>
                        ${order.client.email ? `<div><strong class="text-gray-900 dark:text-gray-100">Email:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.email}</span></div>` : ''}
                        ${order.client.cpf_cnpj ? `<div><strong class="text-gray-900 dark:text-gray-100">CPF/CNPJ:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.cpf_cnpj}</span></div>` : ''}
                    </div>
                </div>

                    <!-- Vendedor -->
                ${order.seller ? `
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Vendedor
                    </h4>
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <div><strong class="text-gray-900 dark:text-gray-100">Nome:</strong> <span class="text-gray-700 dark:text-gray-300">${order.seller}</span></div>
                    </div>
                </div>
                ` : ''}

                    <!-- Itens do Pedido -->
                <div class="space-y-6">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Itens do Pedido (${order.items.length})
                    </h4>
                    
                    ${order.items.map((item, index) => {
                        // Verificar se tem tamanhos definidos (além de Único)
                        const sizeOrder = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
                        const normalizeSizeKey = (value) => {
                            if (value === null || value === undefined) return '';
                            return String(value)
                                .trim()
                                .toUpperCase()
                                .normalize('NFD')
                                .replace(/[\u0300-\u036f]/g, '');
                        };
                        const sortSizeEntries = (sizesObj) => {
                            return Object.entries(sizesObj || {}).sort(([a], [b]) => {
                                const aKey = normalizeSizeKey(a);
                                const bKey = normalizeSizeKey(b);
                                const aIndex = sizeOrder.indexOf(aKey);
                                const bIndex = sizeOrder.indexOf(bKey);
                                const aRank = aIndex === -1 ? 999 : aIndex;
                                const bRank = bIndex === -1 ? 999 : bIndex;
                                if (aRank !== bRank) return aRank - bRank;
                                return aKey.localeCompare(bKey);
                            });
                        };
                        const hasRealSizes = item.sizes && sortSizeEntries(item.sizes).some(([s, q]) => Number(q) > 0 && s !== 'Único' && s !== 'UN');
                        const isSimpleItem = !hasRealSizes;

                        return `
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border-2 border-indigo-200 dark:border-indigo-600/30 p-6">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-indigo-300 dark:border-indigo-600/30">
                            <h5 class="text-xl font-bold text-indigo-900 dark:text-indigo-300">Item ${item.item_number || index + 1}</h5>
                            <span class="text-sm bg-indigo-600 dark:bg-indigo-600 text-white px-3 py-1 rounded-full font-semibold">${item.quantity} peças</span>
                        </div>

                        <!-- Nome da Arte -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4 border-2 border-purple-200 dark:border-purple-600/30">
                            <div class="flex items-center justify-between mb-3">
                                <h6 class="font-semibold text-purple-900 dark:text-purple-300 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                    Nome da Arte
                                </h6>
                                <button onclick="saveArtName(${item.id})" 
                                        id="save-art-name-btn-${item.id}"
                                        class="px-3 py-1 bg-purple-600 text-white text-xs rounded-md hover:bg-purple-700 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Salvar
                                </button>
                            </div>
                            <input type="text" 
                                   id="art-name-input-${item.id}"
                                   value="${item.art_name || ''}"
                                   placeholder="Digite o nome da arte..."
                                   class="w-full px-3 py-2 border border-purple-300 dark:border-purple-600/30 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 rounded-md focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-500 focus:border-purple-500 dark:focus:border-transparent"
                                   onkeypress="if(event.key==='Enter') saveArtName(${item.id})">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-start">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Este nome aparecerá no card do Kanban e nos PDFs
                            </p>
                        </div>

                        <!-- Imagem de Capa -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Imagem de Capa
                                </h6>
                                <button onclick="editItemCoverImage(${item.id})" 
                                        class="px-3 py-1 bg-indigo-600 dark:bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                                    ${item.cover_image ? 'Alterar' : 'Adicionar'}
                                </button>
                            </div>
                            <div class="mb-2 flex items-start bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-600/30 rounded-md p-2">
                                <svg class="w-4 h-4 mr-1.5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-blue-700 dark:text-blue-400"><strong>Tamanho recomendado:</strong> 794 x 1123 pixels (A4 em 96 DPI)</span>
                            </div>
                            ${item.cover_image_url ? `
                                <img src="${item.cover_image_url}" alt="Capa" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 shadow-md bg-gray-50 dark:bg-gray-900/20" style="max-height: 600px; object-fit: contain;" onerror="this.parentElement.innerHTML='<div class=\'text-center text-gray-500 dark:text-gray-400 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/20\'><svg class=\'w-16 h-16 mx-auto mb-3 text-gray-400 dark:text-gray-500\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg><p class=\'text-sm font-medium mb-1 text-gray-700 dark:text-gray-300\'>Imagem não encontrada</p><p class=\'text-xs text-gray-500 dark:text-gray-400\'>O arquivo de imagem não foi encontrado no servidor</p></div>'">
                            ` : `
                                <div class="text-center text-gray-500 dark:text-gray-400 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/20">
                                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Nenhuma imagem de capa</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Adicione uma imagem de 794 x 1123 pixels</p>
                                </div>
                            `}
                        </div>

                        <!-- Detalhes da Costura -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                            <h6 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Costura
                            </h6>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm text-gray-700 dark:text-gray-300">
                                ${order.store ? `<div><strong class="text-gray-900 dark:text-gray-100">Loja:</strong> <span class="text-gray-700 dark:text-gray-300">${order.store.name}</span></div>` : ''}
                                <div><strong class="text-gray-900 dark:text-gray-100">Tecido:</strong> <span class="text-gray-700 dark:text-gray-300">${item.fabric}</span></div>
                                <div><strong class="text-gray-900 dark:text-gray-100">Cor:</strong> <span class="text-gray-700 dark:text-gray-300">${item.color}</span></div>
                                ${item.collar ? `<div><strong class="text-gray-900 dark:text-gray-100">Gola:</strong> <span class="text-gray-700 dark:text-gray-300">${item.collar}</span></div>` : ''}
                                ${item.detail ? `<div><strong class="text-gray-900 dark:text-gray-100">Detalhe:</strong> <span class="text-gray-700 dark:text-gray-300">${item.detail}</span></div>` : ''}
                                ${item.model ? `<div><strong class="text-gray-900 dark:text-gray-100">Tipo de Corte:</strong> <span class="text-gray-700 dark:text-gray-300">${item.model}</span></div>` : ''}
                                <div><strong class="text-gray-900 dark:text-gray-100">Personalização:</strong> <span class="text-gray-700 dark:text-gray-300">${item.print_type}</span></div>
                            </div>
                            
                            <div class="mt-4">
                                ${ (((item.print_type && item.print_type.trim() === 'Sublimação Local') || item.fabric === 'Produto Pronto') && isSimpleItem) ? `
                                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Quantidade Total</span>
                                            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">${item.quantity} unidades</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Tamanho</span>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">Único (UN)</p>
                                        </div>
                                    </div>
                                ` : `
                                    <strong class="block mb-2 text-gray-900 dark:text-gray-300">Tamanhos:</strong>
                                    <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                        ${sortSizeEntries(item.sizes).map(([size, qty]) => 
                                            Number(qty) > 0 ? `
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-md px-2 py-1 text-center border border-gray-200 dark:border-gray-600">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">${size}</span>
                                                <p class="font-bold text-sm text-gray-900 dark:text-gray-200">${qty}</p>
                                            </div>
                                            ` : ''
                                        ).join('')}
                                    </div>
                                `}
                            </div>
                        </div>

                        <!-- Personalização -->
                        ${item.sublimations && item.sublimations.length > 0 ? `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                            <h6 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                Personalização
                            </h6>
                            ${item.art_name ? `<p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong class="text-gray-900 dark:text-gray-100">Nome da Arte:</strong> <span class="text-gray-700 dark:text-gray-300">${item.art_name}</span></p>` : ''}
                            <div class="space-y-2">
                                ${item.sublimations.map(sub => {
                                    const sizeName = sub.size ? sub.size.name : sub.size_name;
                                    const sizeDimensions = sub.size ? sub.size.dimensions : '';
                                    const locationName = sub.location ? sub.location.name : sub.location_name;
                                    const appType = sub.application_type ? sub.application_type.toUpperCase() : 'APLICAÇÃO';
                                    const colorDetails = sub.color_details ? String(sub.color_details).trim() : '';
                                    const colorInfo = colorDetails
                                        ? `Cores: ${colorDetails}${sub.has_neon ? ' + Neon' : ''}`
                                        : (sub.color_count > 0 ? `${sub.color_count} ${sub.color_count == 1 ? 'Cor' : 'Cores'}${sub.has_neon ? ' + Neon' : ''}` : '');
                                    
                                    return `
                                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 rounded-md p-3 text-sm border border-gray-200 dark:border-gray-600">
                                        <div>
                                            <strong class="text-gray-900 dark:text-gray-200">
                                                ${sizeName ? sizeName : appType}${sizeDimensions ? ` (${sizeDimensions})` : ''}
                                            </strong>
                                            ${locationName ? ` <span class="text-gray-600 dark:text-gray-400">- ${locationName}</span>` : ''}
                                            <span class="text-gray-600 dark:text-gray-400"> x${sub.quantity}</span>
                                            ${colorInfo ? `<br><span class="text-xs text-gray-500 dark:text-gray-400">${colorInfo}</span>` : ''}
                                        </div>
                                        <div class="text-right">
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">R$ ${parseFloat(sub.unit_price).toFixed(2).replace('.', ',')} × ${sub.quantity}</div>
                                            ${sub.discount_percent > 0 ? `<div class="text-xs text-green-600 dark:text-green-400">-${sub.discount_percent}%</div>` : ''}
                                            <div class="font-bold text-gray-900 dark:text-gray-200">R$ ${parseFloat(sub.final_price).toFixed(2).replace('.', ',')}</div>
                                        </div>
                                    </div>
                                `}).join('')}
                            </div>
                        </div>
                        ` : ''}

                        <!-- Arquivos do Item -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mt-4">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Arquivos da Arte
                                </h6>
                                <div class="relative">
                                    <input type="file" id="file-upload-${item.id}" class="hidden" onchange="handleFileUpload(this, ${item.id})" accept=".cdr,.cdrx,.pdf,.jpg,.jpeg,.png,.ai,.eps,.svg">
                                    <button onclick="document.getElementById('file-upload-${item.id}').click()" 
                                            class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 transition flex items-center shadow-sm">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Adicionar Arquivo
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-2" id="files-list-${item.id}">
                                ${item.files && item.files.length > 0 ? item.files.map(file => `
                                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-gray-900 dark:text-gray-300 font-medium">${file.file_name}</span>
                                            ${file.file_name.toLowerCase().endsWith('.cdr') || file.file_name.toLowerCase().endsWith('.cdrx') ? '<span class="ml-2 px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded border border-purple-200 dark:border-purple-800">Corel</span>' : ''}
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="/storage/${file.file_path}" target="_blank" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                `).join('') : ''}
                                ${item.sublimations ? item.sublimations.map(sub => 
                                    sub.files && sub.files.length > 0 ? sub.files.map(file => `
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-gray-900 dark:text-gray-300 font-medium">${file.file_name}</span>
                                                ${file.file_name.toLowerCase().endsWith('.cdr') || file.file_name.toLowerCase().endsWith('.cdrx') ? '<span class="ml-2 px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded border border-purple-200 dark:border-purple-800">Corel</span>' : ''}
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="/storage/${file.file_path}" target="_blank" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    `).join('') : ''
                                ).filter(f => f).join('') : ''}
                                ${(!item.files || item.files.length === 0) && (!item.sublimations || !item.sublimations.some(sub => sub.files && sub.files.length > 0)) ? 
                                    '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2" id="no-files-msg-' + item.id + '">Nenhum arquivo anexado.</p>' : ''
                                }
                            </div>
                        </div>
                    </div>
                    `;
                    }).join('')}
                </div>
                    </div>

                    <!-- COLUNA DIREITA -->
                    <div class="space-y-6">
                        <!-- Data de Entrega e Solicitação de Antecipação -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Entrega
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data de Pedido:</strong></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">${new Date(order.created_at).toLocaleDateString('pt-BR')}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data de Entrega:</strong></span>
                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400">${order.delivery_date ? new Date(order.delivery_date).toLocaleDateString('pt-BR') : 'Não definida'}</span>
                        </div>
                        ${order.pending_delivery_request ? `
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-600/30 rounded-lg p-3">
                            <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-400 mb-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Solicitação Pendente
                            </p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-400">Nova data solicitada: ${order.pending_delivery_request.requested_delivery_date ? new Date(order.pending_delivery_request.requested_delivery_date).toLocaleDateString('pt-BR') : 'N/A'}</p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">Motivo: ${order.pending_delivery_request.reason || 'Não informado'}</p>
                        </div>
                        ` : `
                        <button type="button" 
                                class="delivery-request-btn px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition text-sm font-medium flex items-center"
                                data-order-id="${order.id}"
                                data-delivery-date="${order.delivery_date || ''}">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Solicitar Antecipação
                        </button>
                        `}
                    </div>
                </div>

                        <!-- Pagamento -->
                ${order.payments && order.payments.length > 0 ? `
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Pagamento
                    </h4>
                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        ${order.entry_date ? `<div><strong class="text-gray-900 dark:text-gray-100">Data de Entrada:</strong> <span class="text-gray-700 dark:text-gray-300">${new Date(order.entry_date).toLocaleDateString('pt-BR')}</span></div>` : ''}
                        <div><strong class="text-gray-900 dark:text-gray-100">Formas de Pagamento:</strong></div>
                        ${order.payments.map(payment => `
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3 border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="capitalize font-medium text-gray-900 dark:text-gray-200">${payment.payment_method}</span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100">R$ ${(() => {
                                        // Calcular valor do pagamento baseado em payment_methods
                                        if (payment.payment_methods && Array.isArray(payment.payment_methods)) {
                                            return payment.payment_methods.reduce((sum, m) => sum + parseFloat(m.amount || 0), 0).toFixed(2).replace('.', ',');
                                        }
                                        return parseFloat(payment.entry_amount || 0).toFixed(2).replace('.', ',');
                                    })()}</span>
                                </div>
                                ${payment.payment_date ? `
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    ${new Date(payment.payment_date).toLocaleDateString('pt-BR')}
                                </div>
                                ` : ''}
                            </div>
                        `).join('')}
                        ${(() => {
                            // Calcular total pago baseado nos payment_methods (fonte única de verdade)
                            let totalPago = 0;
                            order.payments.forEach(p => {
                                if (p.payment_methods && Array.isArray(p.payment_methods)) {
                                    p.payment_methods.forEach(method => {
                                        totalPago += parseFloat(method.amount || 0);
                                    });
                                } else {
                                    // Fallback para pagamentos antigos sem payment_methods
                                    totalPago += parseFloat(p.entry_amount || 0);
                                }
                            });
                            const restante = parseFloat(order.total) - totalPago;
                            return `
                        <div class="border-t border-gray-300 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between text-gray-700 dark:text-gray-300 py-1"><span>Total:</span><strong class="text-gray-900 dark:text-gray-100">R$ ${parseFloat(order.total).toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between text-gray-700 dark:text-gray-300 py-1"><span>Pago:</span><strong class="text-green-600 dark:text-green-400">R$ ${totalPago.toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between text-gray-700 dark:text-gray-300 py-1"><span>${restante < 0 ? 'Crédito do Cliente:' : 'Restante:'}</span><strong class="${restante > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400'}">R$ ${Math.abs(restante).toFixed(2).replace('.', ',')}</strong></div>
                        </div>
                        ${restante > 0 ? `
                        <div class="border-t border-gray-300 dark:border-gray-700 pt-3 mt-3">
                            <button onclick="openPaymentModal(${order.id}, ${restante})" 
                                    class="px-4 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition text-sm font-medium flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Registrar Pagamento Restante
                            </button>
                        </div>
                        ` : ''}
                            `;
                        })()}
                    </div>
                </div>
                ` : ''}

                        <!-- Comentários -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Comentários
                    </h4>
                    
                    <!-- Formulário de Novo Comentário -->
                    <div class="mb-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                        <textarea id="comment-text-${order.id}" placeholder="Escreva seu comentário..." 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 focus:border-indigo-500 dark:focus:border-transparent" 
                                  rows="3"></textarea>
                        <button onclick="addComment(${order.id})" 
                                class="mt-2 px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 text-sm font-medium">
                            Adicionar Comentário
                        </button>
                    </div>

                    <!-- Lista de Comentários -->
                    <div id="comments-list-${order.id}" class="space-y-3">
                        ${order.comments && order.comments.length > 0 ? order.comments.map(comment => `
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-500 dark:border-blue-400">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-semibold text-sm text-blue-900 dark:text-blue-300">${comment.user_name}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">${new Date(comment.created_at).toLocaleString('pt-BR')}</span>
                                </div>
                                <p class="text-sm text-blue-800 dark:text-blue-300">${comment.comment}</p>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhum comentário ainda.</p>'}
                    </div>
                </div>

                        <!-- Log de Atendimento -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Log de Atendimento
                    </h4>
                    <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                        ${order.logs && order.logs.length > 0 ? order.logs.map(log => `
                            <div class="flex items-start space-x-3 text-sm border-l-4 ${
                                log.action === 'status_changed' ? 'border-purple-500 dark:border-purple-400 bg-purple-50 dark:bg-purple-900/20' :
                                log.action === 'comment_added' ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20' :
                                log.action === 'order_edited' ? 'border-orange-500 dark:border-orange-400 bg-orange-50 dark:bg-orange-900/20' :
                                'border-gray-400 dark:border-gray-500 bg-gray-50 dark:bg-gray-700'
                            } rounded-md p-3 border-r border-t border-b border-gray-200 dark:border-gray-600">
                                <div class="flex-shrink-0 mt-0.5">
                                    ${log.action === 'status_changed' ? 
                                        '<svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' :
                                      log.action === 'comment_added' ? 
                                        '<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>' :
                                      log.action === 'order_edited' ? 
                                        '<svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>' :
                                        '<svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
                                    }
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">${log.user_name}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">${new Date(log.created_at).toLocaleString('pt-BR')}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">${log.description}</p>
                                    ${log.action === 'order_edited' && log.changes ? (() => {
                                        let changesHtml = '<div class="mt-2 space-y-1">';
                                        const changes = typeof log.changes === 'string' ? JSON.parse(log.changes) : log.changes;
                                        
                                        // Mapeamento de nomes de campos para português
                                        const fieldLabels = {
                                            'name': 'Nome',
                                            'phone_primary': 'Telefone',
                                            'phone_secondary': 'Telefone 2',
                                            'email': 'E-mail',
                                            'cpf_cnpj': 'CPF/CNPJ',
                                            'address': 'Endereço',
                                            'city': 'Cidade',
                                            'state': 'Estado',
                                            'zip_code': 'CEP',
                                            'category': 'Categoria',
                                            'fabric': 'Tecido',
                                            'color': 'Cor',
                                            'collar': 'Gola',
                                            'model': 'Modelo',
                                            'detail': 'Detalhe',
                                            'print_type': 'Personalização',
                                            'quantity': 'Quantidade',
                                            'unit_price': 'Preço Unitário',
                                            'total_price': 'Preço Total',
                                            'sizes': 'Tamanhos',
                                            'delivery_date': 'Data Entrega',
                                            'entry_date': 'Data Entrada',
                                            'discount': 'Desconto',
                                            'delivery_fee': 'Taxa Entrega',
                                            'total': 'Total',
                                            'notes': 'Observações',
                                            'seller': 'Vendedor',
                                            'art_name': 'Nome da Arte',
                                            'art_notes': 'Notas da Arte',
                                        };
                                        
                                        // Formatar valor para exibição
                                        const formatValue = (val) => {
                                            if (val === null || val === undefined) return '-';
                                            if (typeof val === 'object') return JSON.stringify(val);
                                            if (typeof val === 'number' && val.toString().includes('.')) {
                                                return 'R$ ' + parseFloat(val).toFixed(2).replace('.', ',');
                                            }
                                            return val;
                                        };
                                        
                                        // Cliente
                                        if (changes.client) {
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300">📋 Cliente:</div>';
                                            Object.entries(changes.client).forEach(([field, change]) => {
                                                const label = fieldLabels[field] || field;
                                                changesHtml += '<div class="text-xs ml-2 text-gray-600 dark:text-gray-400">' +
                                                    '<span class="font-medium">' + label + ':</span> ' +
                                                    '<span class="line-through text-red-500 dark:text-red-400">' + formatValue(change.old) + '</span>' +
                                                    ' → <span class="text-green-600 dark:text-green-400">' + formatValue(change.new) + '</span></div>';
                                            });
                                        }
                                        
                                        // Pedido
                                        if (changes.order) {
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300 mt-1">📦 Pedido:</div>';
                                            Object.entries(changes.order).forEach(([field, change]) => {
                                                const label = fieldLabels[field] || field;
                                                changesHtml += '<div class="text-xs ml-2 text-gray-600 dark:text-gray-400">' +
                                                    '<span class="font-medium">' + label + ':</span> ' +
                                                    '<span class="line-through text-red-500 dark:text-red-400">' + formatValue(change.old) + '</span>' +
                                                    ' → <span class="text-green-600 dark:text-green-400">' + formatValue(change.new) + '</span></div>';
                                            });
                                        }
                                        
                                        // Itens
                                        if (changes.items) {
                                            Object.entries(changes.items).forEach(([itemId, itemChange]) => {
                                                if (itemChange.type === 'modified' && itemChange.changes) {
                                                    changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300 mt-1">👕 Item ' + itemId + ' (modificado):</div>';
                                                    Object.entries(itemChange.changes).forEach(([field, change]) => {
                                                        if (field !== 'sublimations' && field !== 'files') {
                                                            const label = fieldLabels[field] || field;
                                                            changesHtml += '<div class="text-xs ml-2 text-gray-600 dark:text-gray-400">' +
                                                                '<span class="font-medium">' + label + ':</span> ' +
                                                                '<span class="line-through text-red-500 dark:text-red-400">' + formatValue(change.old) + '</span>' +
                                                                ' → <span class="text-green-600 dark:text-green-400">' + formatValue(change.new) + '</span></div>';
                                                        }
                                                    });
                                                } else if (itemChange.type === 'added') {
                                                    changesHtml += '<div class="text-xs font-semibold text-green-700 dark:text-green-300 mt-1">➕ Novo Item ' + itemId + ' adicionado</div>';
                                                } else if (itemChange.type === 'removed') {
                                                    changesHtml += '<div class="text-xs font-semibold text-red-700 dark:text-red-300 mt-1">➖ Item ' + itemId + ' removido</div>';
                                                }
                                            });
                                        }
                                        
                                        changesHtml += '</div>';
                                        return changesHtml;
                                    })() : ''}
                                </div>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhum log de atendimento ainda.</p>'}
                    </div>
                </div>
                    </div>
                </div>
            `;
            
            // Pegar o nome da arte do primeiro item (se existir)
            const firstArtName = order.items.find(item => item.art_name)?.art_name;
            const deliveryDate = order.delivery_date ? new Date(order.delivery_date).toLocaleDateString('pt-BR') : 'Sem data';
            const isEvent = order.is_event;
            
            // Criar título com OS e data à esquerda, nome da arte centralizado (se existir)
            const modalTitle = document.getElementById('modal-title');
            modalTitle.innerHTML = `
                <div class="flex items-start justify-between w-full">
                    <div class="flex flex-col">
                        <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">OS ${String(order.id).padStart(2, '0')}</span>
                        <span class="text-base font-semibold ${isEvent ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400'}">${deliveryDate}${isEvent ? ' - EVENTO' : ''}</span>
                    </div>
                    ${firstArtName ? `
                    <div class="flex-1 flex justify-center">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">${firstArtName}</span>
                    </div>
                    ` : ''}
                    <div class="w-24"></div>
                </div>
            `;
            
            document.getElementById('modal-content').innerHTML = html;
            
            // Configurar o select de status após inserir o HTML
            const statusSelect = document.getElementById('move-status-select');
            if (statusSelect && order.status_id) {
                statusSelect.value = order.status_id;
            }
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

        function openPaymentModal(orderId, remainingAmount) {
            document.getElementById('payment-order-id').value = orderId;
            document.getElementById('remaining-amount').textContent = `R$ ${parseFloat(remainingAmount).toFixed(2).replace('.', ',')}`;
            document.getElementById('payment-amount').value = parseFloat(remainingAmount).toFixed(2);
            document.getElementById('payment-amount').max = parseFloat(remainingAmount).toFixed(2);
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
            document.getElementById('payment-form').reset();
        }

        function submitPayment(event) {
            event.preventDefault();
            
            const orderId = document.getElementById('payment-order-id').value;
            const amount = parseFloat(document.getElementById('payment-amount').value);
            const paymentMethod = document.getElementById('payment-method').value;
            const paymentDate = document.getElementById('payment-date').value;

            fetch(`/kanban/order/${orderId}/add-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    amount: amount,
                    payment_method: paymentMethod,
                    payment_date: paymentDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Pagamento registrado com sucesso!', 'success');
                    closePaymentModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao registrar pagamento', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao registrar pagamento', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-[70]`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Fechar modal ao clicar fora
        document.getElementById('order-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });

        document.getElementById('cover-image-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCoverImageModal();
            }
        });

        document.getElementById('payment-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });

        // Event listener para botões de solicitar antecipação usando delegação de eventos
        document.addEventListener('click', function(e) {
            const deliveryBtn = e.target.closest('.delivery-request-btn');
            if (deliveryBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                const orderId = deliveryBtn.getAttribute('data-order-id');
                const deliveryDate = deliveryBtn.getAttribute('data-delivery-date');
                
                if (typeof window.openDeliveryRequestModal === 'function') {
                    window.openDeliveryRequestModal(orderId, deliveryDate);
                } else {
                    console.error('openDeliveryRequestModal não está disponível');
                    showNotification('Erro: Função não disponível. Recarregue a página.', 'error');
                }
            }
        });
        
        document.getElementById('delivery-request-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeliveryRequestModal();
            }
        });

        const editRequestModal = document.getElementById('editRequestModal');
        if (editRequestModal) {
            editRequestModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditRequestModal();
                }
            });
        }

        const moveConfirmModal = document.getElementById('move-confirm-modal');
        if (moveConfirmModal) {
            moveConfirmModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeMoveConfirmModal();
                }
            });
        }

        function openDeliveryRequestModal(orderId, currentDeliveryDate) {
            if (!orderId) {
                console.error('Order ID não fornecido');
                showNotification('Erro: ID do pedido não encontrado', 'error');
                return;
            }
            
            document.getElementById('delivery-order-id').value = orderId;
            
            // Validar e processar data de entrega
            if (!currentDeliveryDate || currentDeliveryDate === 'null' || currentDeliveryDate === '') {
                showNotification('Erro: Data de entrega não definida para este pedido', 'error');
                return;
            }
            
            // Tentar criar objeto Date
            let deliveryDate;
            try {
                deliveryDate = new Date(currentDeliveryDate);
                if (isNaN(deliveryDate.getTime())) {
                    throw new Error('Data inválida');
                }
            } catch (e) {
                console.error('Erro ao processar data:', e, 'Data recebida:', currentDeliveryDate);
                showNotification('Erro: Data de entrega inválida', 'error');
                return;
            }
            
            document.getElementById('current-delivery-date').value = currentDeliveryDate;
            
            // Formatar data para exibição
            document.getElementById('current-delivery-display').textContent = deliveryDate.toLocaleDateString('pt-BR');
            
            // Definir data máxima para o input (um dia antes da data atual)
            const maxDate = new Date(deliveryDate);
            maxDate.setDate(maxDate.getDate() - 1);
            document.getElementById('requested-delivery-date').max = maxDate.toISOString().split('T')[0];
            
            document.getElementById('delivery-request-modal').classList.remove('hidden');
        }

        function closeDeliveryRequestModal() {
            document.getElementById('delivery-request-modal').classList.add('hidden');
            document.getElementById('delivery-request-form').reset();
        }

        // Funções para modal de imagem de capa
        function editItemCoverImage(itemId) {
            document.getElementById('cover-item-id').value = itemId;
            document.getElementById('cover-image-modal').classList.remove('hidden');
        }

          function closeCoverImageModal() {
              document.getElementById('cover-image-modal').classList.add('hidden');
              document.getElementById('cover-image-form').reset();
              document.getElementById('cover-preview').classList.add('hidden');
          }

          function openCoverImagePicker() {
              const coverInput = document.getElementById('cover-image-input');
              if (!coverInput) return;

              // Abrir o seletor de arquivos diretamente para garantir o upload
              coverInput.click();
          }

        function previewCoverImage(input) {
            const preview = document.getElementById('cover-preview');
            const previewImg = document.getElementById('cover-preview-img');
            
            if (input.files && input.files.length > 0) {
                // Se houver múltiplos arquivos, usar o primeiro
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }
        
        // Listener para atualizar preview quando arquivo for adicionado via modal de paste
        document.addEventListener('DOMContentLoaded', function() {
            const coverImageInput = document.getElementById('cover-image-input');
            if (coverImageInput) {
                coverImageInput.addEventListener('change', function() {
                    previewCoverImage(this);
                });
            }

            document.addEventListener('paste', function(e) {
                const modal = document.getElementById('cover-image-modal');
                const input = document.getElementById('cover-image-input');
                if (!modal || modal.classList.contains('hidden') || !input) return;

                const items = e.clipboardData && e.clipboardData.items ? e.clipboardData.items : [];
                let file = null;
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    if (item.kind === 'file' && item.type && item.type.startsWith('image/')) {
                        file = item.getAsFile();
                        break;
                    }
                }

                if (!file) return;

                e.preventDefault();

                if (typeof DataTransfer !== 'undefined') {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }

                input.dispatchEvent(new Event('change', { bubbles: true }));

                if (typeof showNotification === 'function') {
                    showNotification('Arquivo colado com sucesso!', 'success');
                }
            });
            
            // Abrir modal automaticamente se houver pesquisa com resultado
            @if(isset($autoOpenOrderId) && $autoOpenOrderId)
            // Aguardar um pouco para garantir que a página carregou completamente
            setTimeout(function() {
                if (typeof openOrderModal === 'function') {
                    openOrderModal({{ $autoOpenOrderId }});
                }
            }, 300);
            @endif
        });

        function submitCoverImage(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const itemId = formData.get('item_id');
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="animate-pulse">Enviando...</span>';
            
            fetch(`/api/order-items/${itemId}/cover-image`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Erro ao fazer upload da imagem');
                    });
                }
                return response.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                console.log('Resposta do upload:', data);
                
                if (data.success) {
                    // Verificar informações de debug
                    if (data.debug) {
                        console.log('Debug info:', data.debug);
                        if (typeof data.debug.file_exists !== 'undefined' && !data.debug.file_exists) {
                            console.error('Arquivo não encontrado após upload!');
                            showNotification('Aviso: Arquivo pode não ter sido salvo corretamente', 'warning');
                        }
                        if (typeof data.debug.symlink_exists !== 'undefined' && !data.debug.symlink_exists) {
                            console.error('Symlink do storage não encontrado!');
                            showNotification('Aviso: Symlink do storage pode não estar configurado', 'warning');
                        }
                    }
                    
                    if (!data.cover_image_url) {
                        console.error('URL da imagem não foi gerada!', data);
                        showNotification('Erro: URL da imagem não foi gerada. Verifique os logs.', 'error');
                        return;
                    }
                    
                    showNotification('Imagem de capa atualizada com sucesso!', 'success');
                    closeCoverImageModal();
                    
                    // Recarregar detalhes do pedido no modal se estiver aberto
                    const orderModal = document.getElementById('order-modal');
                    const currentOrderId = orderModal ? orderModal.getAttribute('data-current-order-id') : null;
                    
                    if (currentOrderId && !orderModal.classList.contains('hidden')) {
                        // Recarregar os detalhes do pedido
                        fetch(`/kanban/order/${currentOrderId}`)
                            .then(response => response.json())
                            .then(orderData => {
                                displayOrderDetails(orderData);
                            })
                            .catch(error => {
                                console.error('Erro ao recarregar detalhes do pedido:', error);
                            });
                    }
                    
                    // Atualizar a imagem no card do kanban
                    if (data.order_id && data.cover_image_url) {
                        console.log('Atualizando card do kanban:', data.order_id, data.cover_image_url);
                        updateKanbanCardImage(data.order_id, data.cover_image_url);
                    } else {
                        console.error('Dados insuficientes para atualizar card:', data);
                    }
                } else {
                    showNotification(data.message || 'Erro ao atualizar imagem de capa', 'error');
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar imagem de capa:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                showNotification('Erro ao atualizar imagem de capa', 'error');
            });
        }

        function updateKanbanCardImage(orderId, imageUrl) {
            console.log('updateKanbanCardImage chamado:', orderId, imageUrl);
            
            // Encontrar o card do pedido no kanban
            const card = document.querySelector(`.kanban-card[data-order-id="${orderId}"]`);
            if (!card) {
                console.warn('Card não encontrado para orderId:', orderId);
                return;
            }
            
            // Encontrar o container da imagem no card
            const imageContainer = card.querySelector('.h-48');
            if (!imageContainer) {
                console.warn('Container de imagem não encontrado no card');
                return;
            }
            
            // Adicionar timestamp para forçar reload da imagem
            const imageUrlWithTimestamp = imageUrl + (imageUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
            
            // Atualizar a imagem
            const img = imageContainer.querySelector('img');
            if (img) {
                console.log('Atualizando imagem existente:', imageUrlWithTimestamp);
                img.src = imageUrlWithTimestamp;
                img.onerror = function() {
                    console.error('Erro ao carregar imagem:', imageUrlWithTimestamp);
                    // Se a imagem falhar ao carregar, mostrar placeholder
                    this.parentElement.innerHTML = '<div class=\'h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';
                };
                img.onload = function() {
                    console.log('Imagem carregada com sucesso:', imageUrlWithTimestamp);
                };
            } else {
                // Se não houver imagem, criar uma nova
                console.log('Criando nova imagem:', imageUrlWithTimestamp);
                imageContainer.innerHTML = `<img src="${imageUrlWithTimestamp}" alt="Capa do Pedido" class="w-full h-48 object-cover" style="object-fit: cover; object-position: center;" onerror="this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>'">`;
            }
        }

        function saveArtName(itemId) {
            const input = document.getElementById(`art-name-input-${itemId}`);
            const button = document.getElementById(`save-art-name-btn-${itemId}`);
            const artName = input.value.trim();

            if (!artName) {
                showNotification('Por favor, digite um nome para a arte', 'error');
                return;
            }

            // Mostrar loading
            const originalBtnText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="animate-pulse">Salvando...</span>';

            fetch(`/api/order-items/${itemId}/art-name`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    art_name: artName
                })
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalBtnText;
                
                if (data.success) {
                    showNotification('Nome da arte salvo com sucesso!', 'success');
                    
                    // Recarregar cards do kanban para atualizar o nome no overlay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao salvar nome da arte', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                button.disabled = false;
                button.innerHTML = originalBtnText;
                showNotification('Erro ao salvar nome da arte', 'error');
            });
        }

        function moveCardToColumn(orderId) {
            const selectElement = document.getElementById('move-status-select');
            const newStatusId = selectElement.value;

            if (!newStatusId) {
                showNotification('Por favor, selecione uma coluna', 'error');
                return;
            }

            // Armazenar dados da movimentação pendente
            pendingMoveOrderId = orderId;
            pendingMoveStatusId = newStatusId;
            
            // Mostrar modal de confirmação
            const statusName = selectElement.options[selectElement.selectedIndex].text;
            document.getElementById('move-confirm-status-name').textContent = statusName;
            document.getElementById('move-confirm-modal').classList.remove('hidden');
        }

        function closeMoveConfirmModal() {
            document.getElementById('move-confirm-modal').classList.add('hidden');
            pendingMoveOrderId = null;
            pendingMoveStatusId = null;
        }

        function confirmMoveCard() {
            if (!pendingMoveOrderId || !pendingMoveStatusId) {
                return;
            }

            // Fazer requisição para mover o card
            fetch('/kanban/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: pendingMoveOrderId,
                    status_id: pendingMoveStatusId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Card movido com sucesso!', 'success');
                    
                    // Fechar modais e recarregar página para atualizar o kanban
                    closeMoveConfirmModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    showNotification(data.message || 'Erro ao mover card', 'error');
                    closeMoveConfirmModal();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao mover card', 'error');
                closeMoveConfirmModal();
            });
        }
        
        // Expor função globalmente
        window.moveCardToColumn = moveCardToColumn;

        function submitDeliveryRequest(event) {
            event.preventDefault();
            
            const orderId = document.getElementById('delivery-order-id').value;
            const currentDeliveryDate = document.getElementById('current-delivery-date').value;
            const requestedDeliveryDate = document.getElementById('requested-delivery-date').value;
            const reason = document.getElementById('delivery-reason').value;

            // Validar se a data solicitada é anterior à atual
            if (new Date(requestedDeliveryDate) >= new Date(currentDeliveryDate)) {
                showNotification('A data solicitada deve ser anterior à data de entrega atual', 'error');
                return;
            }

            fetch(`/delivery-requests`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: orderId,
                    current_delivery_date: currentDeliveryDate,
                    requested_delivery_date: requestedDeliveryDate,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Solicitação enviada com sucesso!', 'success');
                    closeDeliveryRequestModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao enviar solicitação', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao enviar solicitação', 'error');
            });
        }
        
        // Variável global para armazenar o ID do pedido sendo editado
        let currentEditOrderId = null;

        // Funções do Modal de Solicitação de Edição
        function openEditRequestModal(orderId) {
            currentEditOrderId = orderId;
            const modal = document.getElementById('editRequestModal');
            if (modal) {
                document.getElementById('modalEditOrderId').textContent = '#' + String(orderId).padStart(6, '0');
                document.getElementById('editRequestReason').value = '';
                document.getElementById('editReasonError').classList.add('hidden');
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeEditRequestModal() {
            const modal = document.getElementById('editRequestModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentEditOrderId = null;
            }
        }

        function submitEditRequest() {
            const reason = document.getElementById('editRequestReason').value.trim();
            const errorElement = document.getElementById('editReasonError');

            if (!reason) {
                errorElement.classList.remove('hidden');
                document.getElementById('editRequestReason').focus();
                return;
            }

            errorElement.classList.add('hidden');

            // Desabilitar botão para evitar duplo clique
            const submitBtn = event.target;
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Enviando...';

            fetch(`/pedidos/${currentEditOrderId}/edit-request`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('Solicitação de edição enviada com sucesso!', 'success');
                    closeEditRequestModal();
                    
                    // Recarregar detalhes do pedido no modal se estiver aberto
                    const orderModal = document.getElementById('order-modal');
                    const currentOrderId = orderModal ? orderModal.getAttribute('data-current-order-id') : null;
                    
                    if (currentOrderId && !orderModal.classList.contains('hidden')) {
                        setTimeout(() => {
                            openOrderModal(parseInt(currentOrderId));
                        }, 500);
                    } else {
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    showNotification(data.message || 'Erro ao enviar solicitação de edição', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Erro detalhado:', error);
                let errorMessage = 'Erro ao enviar solicitação de edição';
                
                if (error.message) {
                    errorMessage = error.message;
                } else if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join(', ');
                }
                
                showNotification(errorMessage, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        }

        // Expor todas as funções necessárias globalmente
        window.addComment = addComment;
        window.downloadAllFiles = downloadAllFiles;
        window.openPaymentModal = openPaymentModal;
        window.closePaymentModal = closePaymentModal;
        window.submitPayment = submitPayment;
        window.submitDeliveryRequest = submitDeliveryRequest;
        window.closeDeliveryRequestModal = closeDeliveryRequestModal;
        window.openDeliveryRequestModal = openDeliveryRequestModal;
        window.showNotification = showNotification;
        window.saveArtName = saveArtName;
        window.editItemCoverImage = editItemCoverImage;
        window.closeCoverImageModal = closeCoverImageModal;
        window.submitCoverImage = submitCoverImage;
        window.closeMoveConfirmModal = closeMoveConfirmModal;
        window.confirmMoveCard = confirmMoveCard;
        window.openEditRequestModal = openEditRequestModal;
        window.closeEditRequestModal = closeEditRequestModal;
        window.submitEditRequest = submitEditRequest;
        window.handleFileUpload = handleFileUpload;
        window.previewCoverImage = previewCoverImage;
        window.openCoverImagePicker = openCoverImagePicker;
        window.closeCoverImageModal = closeCoverImageModal;
        })(); // Fim da IIFE
    </script>
    <div class="mt-10">
        <x-kanban-bottom-nav :active-type="$viewType" />
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="{{ asset('js/kanban-sortable.js') }}"></script>
@endsection
