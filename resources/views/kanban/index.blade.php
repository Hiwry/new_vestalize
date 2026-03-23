@extends('layouts.admin')

@push('styles')
<style>
    /* Dashboard Premium Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(40px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes pulse-soft {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-slide-in-right { animation: slideInRight 0.5s ease-out forwards; }
    .animate-pulse-soft { animation: pulse-soft 2s ease-in-out infinite; }

    /* Fix: avento-theme.css overrides pl-12 on search input */
    #search-input {
        padding-left: 3rem !important;
        padding-right: 1.5rem !important;
        border: none !important;
        border-radius: 1rem !important;
    }

    /* Glassmorphism & Hover Effects */
    .glass-card {
        background: #1e293b;
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 12px;
    }
    .dark .glass-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .hover-lift {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .hover-lift:hover {
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.3);
    }

    /* Original Kanban Styles */
    [x-cloak] { display: none !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4b5563; }

    #order-modal .order-modal-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }
    .dark #order-modal .order-modal-card {
        background: #0b1221;
        border-color: #1f2937;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45);
    }
    /* ... rest of modal styles ... */
    #order-modal .order-modal-card.accent {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .dark #order-modal .order-modal-card.accent {
        background: rgba(30, 41, 59, 0.4);
        border-color: rgba(255, 255, 255, 0.05);
    }
    #order-modal .order-modal-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: #0f172a;
    }
    .dark #order-modal .order-modal-title { color: #e5e7eb; }
    #order-modal .order-modal-title svg { color: #4f46e5; }
    #order-modal .order-action-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: stretch;
    }
    #order-modal .order-action-grid > * { flex: 1 1 220px; }
    #order-modal .btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-radius: 12px;
        font-weight: 700;
        padding: 0.65rem 1rem;
        transition: all 0.18s ease;
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.18);
        color: #fff;
    }
    #order-modal .btn-modern:hover { transform: translateY(-1px); }
    #order-modal .btn-modern svg { color: currentColor; }
    #order-modal .btn-primary { 
        background: #4f46e5; 
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06);
    }
    #order-modal .btn-primary:active { background: #4338ca; }
    
    #order-modal .btn-warning,
    #order-modal .btn-rose { 
        background: transparent; 
        color: #1f2937;
        border: 1px solid #d1d5db; 
        box-shadow: none; 
    }
    .dark #order-modal .btn-warning,
    .dark #order-modal .btn-rose {
        background: rgba(255, 255, 255, 0.03);
        color: #e5e7eb;
        border-color: rgba(255, 255, 255, 0.1);
    }
    #order-modal .btn-warning:hover,
    #order-modal .btn-rose:hover { background: #f3f4f6; }
    .dark #order-modal .btn-warning:hover,
    .dark #order-modal .btn-rose:hover { background: rgba(255, 255, 255, 0.08); }

    #order-modal .btn-success { 
        background: #10b981; 
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);
    }
    #order-modal .btn-success:active { background: #059669; }
    #order-modal .btn-neutral {
        background: #fff;
        color: #111827;
        border: 1px solid #e2e8f0;
        box-shadow: none;
    }
    #order-modal .btn-neutral:hover { border-color: #cbd5e1; }
    .dark #order-modal .btn-neutral { background: #0f172a; color: #e5e7eb; border-color: #1f2937; }
    #order-modal .pill-soft {
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        background: #eef2ff;
        color: #4338ca;
        font-weight: 700;
    }
    .dark #order-modal .pill-soft { background: rgba(124, 58, 237, 0.15); color: #c4b5fd; }
    #order-modal .surface-muted {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .dark #order-modal .surface-muted { background: #111827; border-color: #1f2937; }
    #order-modal .order-subcard {
        background: #ffffff;
        border: 1px solid #d7e0ee;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .dark #order-modal .order-subcard {
        background: #0f172a;
        border-color: #334155;
    }
    #order-modal .order-subcard-title {
        color: #1f2937;
        font-weight: 700;
    }
    .dark #order-modal .order-subcard-title { color: #e5e7eb; }
    #order-modal .btn-compact {
        min-height: 34px;
        padding: 0.4rem 0.85rem;
        border-radius: 10px;
        font-size: 0.75rem;
        line-height: 1rem;
    }
    #order-modal .file-empty-state {
        color: #475569;
        font-weight: 500;
        text-align: center;
        padding: 0.65rem 0.75rem;
        border-radius: 10px;
        border: 1px dashed #cbd5e1;
        background: #ffffff;
    }
    .dark #order-modal .file-empty-state {
        color: #cbd5e1;
        border-color: #475569;
        background: #0f172a;
    }
</style>
@endpush




@section('content')
<script>
    // Definir componente globalmente para garantir acesso via x-data
    // Movido para dentro do content para funcionar com o sistema de navegação AJAX
    window.kanbanBoardIndex = function(config) {
        return {
            hasAgenda: !!config.hasAgenda,
            view: 'kanban', // 'kanban' | 'calendar'
            calendarView: 'month', // 'month' | 'week' | 'day'
            currentDate: config.startDate ? new Date(config.startDate + 'T12:00:00') : new Date(),
            events: [],
            eventsLoaded: false,
            eventsLoading: false,
            eventsError: null,
            
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
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const dateString = `${year}-${month}-${day}`;
                return this.events.filter(event => event.date === dateString);
            },

            get agendaGroups() {
                const grouped = this.events.reduce((acc, event) => {
                    const key = event.date || 'sem_data';
                    if (!acc[key]) acc[key] = [];
                    acc[key].push(event);
                    return acc;
                }, {});

                return Object.keys(grouped)
                    .sort((a, b) => {
                        if (a === 'sem_data') return 1;
                        if (b === 'sem_data') return -1;
                        return a.localeCompare(b);
                    })
                    .map(key => ({
                        key,
                        label: key === 'sem_data'
                            ? 'Sem data'
                            : new Date(key + 'T12:00:00').toLocaleDateString('pt-BR', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }),
                        orders: grouped[key],
                    }));
            },

            async ensureCalendarDataLoaded() {
                if (this.eventsLoaded || this.eventsLoading) return;

                this.eventsLoading = true;
                this.eventsError = null;

                try {
                    const params = new URLSearchParams(window.location.search);
                    const response = await fetch('{{ route('kanban.calendar-data') }}?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!response.ok) {
                        throw new Error('Erro ' + response.status);
                    }

                    const data = await response.json();
                    this.events = Array.isArray(data.events) ? data.events : [];
                    this.eventsLoaded = true;
                } catch (error) {
                    this.eventsError = 'Nao foi possivel carregar a agenda.';
                } finally {
                    this.eventsLoading = false;
                }
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
            
            today() {
                this.currentDate = new Date();
            },

            init() {
                const savedView = localStorage.getItem('kanban_view_mode');
                if (savedView) this.view = savedView;
                this.$watch('view', value => {
                    localStorage.setItem('kanban_view_mode', value);
                    if (value === 'calendar') {
                        this.ensureCalendarDataLoaded();
                    }
                });

                if (this.hasAgenda || this.view === 'calendar') {
                    this.ensureCalendarDataLoaded();
                }
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
        @php
            $hasFilters = request('start_date') || request('end_date') || ($personalizationType ?? request('personalization_type')) || ($search ?? request('search'));
            $boardTotal = collect($countsByStatus ?? [])->sum();
            $initialStartDate = request('start_date') ?? null;
        @endphp

        <div x-data="kanbanBoardIndex({ hasAgenda: {{ $hasFilters ? 'true' : 'false' }}, startDate: '{{ $initialStartDate }}' })" x-cloak>

        <!-- Header Premium com Animação -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 mb-8 animate-fade-in-up">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-600 flex items-center justify-center text-white border border-purple-500/20 shadow-lg shadow-purple-500/5" style="color: #ffffff !important;">
                        <i class="fa-solid fa-table-columns text-xl sm:text-2xl" style="color: #ffffff !important;"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                            Kanban <span class="text-purple-500">{{ $viewType === 'personalized' ? 'Personalizados' : 'Produção' }}</span>
                        </h1>
                        <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] sm:tracking-[0.3em]">
                            Total de Pedidos: <span class="text-purple-500/80">{{ $boardTotal }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 animate-slide-in-right">
                <div class="glass-card p-1 rounded-2xl flex items-center bg-gray-100/50 dark:bg-slate-800/40">
                    <button @click="view = 'kanban'" 
                            :class="{ 'bg-purple-600 dark:bg-purple-500 text-white shadow-md': view === 'kanban', 'text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white': view !== 'kanban' }"
                            :style="view === 'kanban' ? 'color: #ffffff !important;' : ''"
                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                        <i class="fa-solid fa-table-columns" :style="view === 'kanban' ? 'color: #ffffff !important;' : ''"></i>
                        <span :style="view === 'kanban' ? 'color: #ffffff !important;' : ''">Kanban</span>
                    </button>
                    <button @click="view = 'calendar'" 
                            :class="{ 'bg-purple-600 dark:bg-purple-500 text-white shadow-md': view === 'calendar', 'text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white': view !== 'calendar' }"
                            :style="view === 'calendar' ? 'color: #ffffff !important;' : ''"
                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days" :style="view === 'calendar' ? 'color: #ffffff !important;' : ''"></i>
                        Calendário
                    </button>
                </div>

                @if(Auth::user()->isAdmin() || Auth::user()->isProducao())
                <a href="{{ route('kanban.columns.index') }}" 
                   class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg hover:shadow-purple-600/30 transition-all active:scale-95 flex items-center gap-2"
                   style="color: #ffffff !important;">
                    <i class="fa-solid fa-sliders-up" style="color: #ffffff !important;"></i>
                    <span style="color: #ffffff !important;">Gerenciar Colunas</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Barra de Busca e Filtros -->
        <!-- Barra de Busca e Filtros Premium -->
        <div class="glass-card p-4 sm:p-6 mb-8 animate-fade-in-up delay-100 border border-gray-100/50 dark:border-white/5 bg-white/50 dark:bg-slate-900/40 backdrop-blur-xl shadow-2xl">
            <form method="GET" action="{{ route('kanban.index') }}" class="space-y-6">
                <input type="hidden" name="type" value="{{ $viewType }}">
                
                <!-- Busca Principal -->
                <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                    <div class="flex-1 relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-purple-500 transition-colors">
                            <i class="fa-solid fa-magnifying-glass text-lg"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search-input"
                               value="{{ $search ?? '' }}"
                               placeholder="Buscar por nº do pedido, cliente ou item..." 
                               class="w-full pl-12 pr-6 h-[56px] rounded-2xl border-none bg-gray-100/50 dark:bg-slate-800/50 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-purple-500/50 transition-all text-sm font-bold tracking-tight">
                    </div>
                    <button type="submit" 
                            class="h-[56px] px-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest transition-all shadow-lg shadow-purple-600/20 hover:shadow-xl hover:shadow-purple-600/40 hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 shrink-0"
                            style="color: #ffffff !important;">
                        <i class="fa-solid fa-magnifying-glass" style="color: #ffffff !important;"></i>
                        <span style="color: #ffffff !important;">Buscar</span>
                    </button>
                </div>
                
                <!-- Filtros Secundários -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Tipo de Personalização</label>
                        <div class="relative">
                            <select name="personalization_type" class="w-full pl-4 pr-10 py-3.5 rounded-xl border-none bg-gray-100/50 dark:bg-slate-800/50 text-gray-700 dark:text-slate-200 text-xs font-bold focus:ring-2 focus:ring-purple-500/50 transition appearance-none cursor-pointer">
                                <option value="">Todas as Personalizações</option>
                                @foreach($personalizationTypes ?? [] as $key => $name)
                                    <option value="{{ $key }}" {{ request('personalization_type') == $key ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[10px]"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Período de Entrega</label>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1">
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-3.5 rounded-xl border-none bg-gray-100/50 dark:bg-slate-800/50 text-gray-700 dark:text-slate-200 text-xs font-bold focus:ring-2 focus:ring-purple-500/50 transition">
                            </div>
                            <span class="text-gray-400 font-black text-[10px] uppercase">a</span>
                            <div class="relative flex-1">
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-3.5 rounded-xl border-none bg-gray-100/50 dark:bg-slate-800/50 text-gray-700 dark:text-slate-200 text-xs font-bold focus:ring-2 focus:ring-purple-500/50 transition">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-end gap-3">
                        <button type="submit" formaction="{{ route('production.pdf') }}" formtarget="_blank"
                                class="flex-1 py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:shadow-purple-600/30 hover:scale-[1.02] active:scale-95 transition font-black text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 shadow-sm"
                                style="color: #ffffff !important;">
                            <span style="color: #ffffff !important;">Exportar PDF</span>
                        </button>
                        @if($search || request('personalization_type') || request('start_date') || request('end_date'))
                        <a href="{{ route('kanban.index', ['type' => $viewType]) }}" 
                           class="flex-1 py-3.5 bg-rose-500/10 text-rose-500 rounded-xl hover:bg-rose-500/20 transition font-black text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 border border-rose-500/20">
                            <i class="fa-solid fa-trash-can"></i>
                            Limpar
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if($hasFilters)
        <div x-show="view === 'kanban' || view === 'calendar'" class="animate-fade-in-up delay-200 mb-12">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-600 flex items-center justify-center text-white border border-purple-500/20 shadow-lg shadow-purple-500/5">
                        <i class="fa-solid fa-calendar-list text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Agenda <span class="text-indigo-500">Planejada</span></h3>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em]">Visualização cronológica por data de entrega</p>
                    </div>
                </div>
                <a href="{{ route('kanban.index', ['type' => $viewType]) }}" 
                   class="px-5 py-2.5 bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:text-indigo-500 transition-all flex items-center gap-2 border border-gray-200 dark:border-white/5">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar ao Kanban
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <template x-if="eventsLoading">
                    <div class="col-span-full py-20 flex flex-col items-center justify-center text-center glass-card border-dashed">
                        <div class="w-20 h-20 rounded-[2rem] bg-gray-100/50 dark:bg-slate-800/50 flex items-center justify-center text-gray-300 mb-4 border border-gray-200 dark:border-white/5">
                            <i class="fa-solid fa-spinner animate-spin text-3xl"></i>
                        </div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Carregando agenda</h4>
                    </div>
                </template>

                <template x-if="eventsError && !eventsLoading">
                    <div class="col-span-full py-20 flex flex-col items-center justify-center text-center glass-card border-dashed">
                        <div class="w-20 h-20 rounded-[2rem] bg-rose-500/10 flex items-center justify-center text-rose-400 mb-4 border border-rose-500/20">
                            <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                        </div>
                        <h4 class="text-xs font-black text-rose-500 uppercase tracking-widest" x-text="eventsError"></h4>
                    </div>
                </template>

                <template x-if="!eventsLoading && !eventsError && agendaGroups.length === 0">
                    <div class="col-span-full py-20 flex flex-col items-center justify-center text-center glass-card border-dashed">
                        <div class="w-20 h-20 rounded-[2rem] bg-gray-100/50 dark:bg-slate-800/50 flex items-center justify-center text-gray-300 mb-4 border border-gray-200 dark:border-white/5">
                            <i class="fa-solid fa-calendar-xmark text-3xl"></i>
                        </div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Nenhum pedido encontrado</h4>
                        <p class="text-[10px] font-bold text-gray-500 dark:text-gray-600 mt-2">Tente ajustar seus filtros de busca</p>
                    </div>
                </template>

                <template x-for="group in agendaGroups" :key="group.key">
                    <div class="glass-card rounded-[2rem] border border-gray-100/50 dark:border-white/5 bg-white/50 dark:bg-slate-900/40 backdrop-blur-xl shadow-xl overflow-hidden flex flex-col h-full hover:shadow-2xl transition-all duration-500 group/agenda">
                        <div class="px-6 py-5 border-b border-gray-100/50 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                    <i class="fa-solid fa-calendar-day text-xs"></i>
                                </div>
                                <span class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                    <span x-text="group.label"></span>
                                </span>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-indigo-500/10 text-[10px] font-black text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shadow-sm" x-text="group.orders.length"></span>
                        </div>
                        <div class="p-4 space-y-4 flex-1">
                            <template x-for="order in group.orders" :key="order.id">
                                <div x-on:click="openOrderModal(order.id)" 
                                     class="group/item relative p-4 rounded-2xl border border-gray-100 dark:border-white/5 bg-white dark:bg-slate-800/50 hover:bg-indigo-500/5 hover:border-indigo-500/30 transition-all duration-300 cursor-pointer shadow-sm hover:shadow-md">
                                    <div class="flex items-start gap-4">
                                        <template x-if="order.cover_image">
                                            <div class="flex-shrink-0 w-14 h-14 rounded-xl overflow-hidden bg-gray-100 dark:bg-slate-900 border border-gray-200 dark:border-white/10 shadow-inner">
                                                <img :src="order.cover_image" class="w-full h-full object-cover group-hover/item:scale-110 transition-transform duration-500">
                                            </div>
                                        </template>
                                        <template x-if="!order.cover_image">
                                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gray-100/50 dark:bg-slate-900/50 flex items-center justify-center text-gray-300 dark:text-gray-700 border border-dashed border-gray-200 dark:border-white/5">
                                                <i class="fa-solid fa-image text-xl"></i>
                                            </div>
                                        </template>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span class="px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[10px] font-black border border-indigo-500/20">
                                                    #<span x-text="String(order.id).padStart(6, '0')"></span>
                                                </span>
                                                <template x-if="order.priority">
                                                    <div class="w-2 h-2 rounded-full shadow-lg pulse-soft"
                                                         :class="{
                                                             'bg-rose-500 shadow-rose-500/50': order.priority === 'alta',
                                                             'bg-amber-500 shadow-amber-500/50': order.priority === 'media',
                                                             'bg-emerald-500 shadow-emerald-500/50': order.priority && !['alta', 'media'].includes(order.priority)
                                                         }"></div>
                                                </template>
                                            </div>
                                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight truncate mb-0.5" :title="order.title" x-text="order.title"></p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" :title="order.store" x-text="order.store"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
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
                <div class="glass-card rounded-2xl flex-shrink-0 overflow-hidden snap-start border border-gray-200/50 flex flex-col transition-all duration-300 bg-white/80 dark:border-white/5 dark:bg-slate-900/60 backdrop-blur-xl shadow-lg hover:shadow-2xl animate-fade-in-up" 
                     style="min-width: 340px; max-width: 340px; animation-delay: {{ $loop->index * 100 }}ms">
                    
                    <div class="px-6 py-5 flex flex-col justify-center border-b border-gray-100/50 dark:border-white/5">
                        <div class="flex items-center justify-between mb-2">
                             <div class="flex items-center gap-3">
                                 <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-white border border-purple-500/20">
                                     <i class="fa-solid fa-layer-group text-xs"></i>
                                 </div>
                                 <div>
                                     <h2 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-[0.2em]">{{ $status->name }}</h2>
                                     <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Produção Planejada</p>
                                 </div>
                             </div>
                             <span class="px-3 py-1 rounded-full bg-purple-500/10 dark:bg-purple-500/20 text-[10px] font-black text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                {{ $countsByStatus[$status->id] ?? 0 }}
                             </span>
                        </div>
                    </div>
                    <div class="kanban-column p-4 space-y-4 overflow-y-auto bg-transparent scrollbar-thin" style="height: calc(100vh - 400px); min-height: 500px;" data-status-id="{{ $status->id }}">
                        @include('kanban.partials.order-cards', [
                            'orders'      => ($ordersByStatus[$status->id] ?? collect()),
                            'viewType'    => $viewType,
                            'columnIndex' => $loop->index,
                            'compactOnly' => in_array(\Illuminate\Support\Str::lower($status->name), ['concluído', 'concluido', 'entregue'], true),
                        ])

                        {{-- Load More button — only when there are more orders beyond the initial page --}}
                        @php
                            $statusTotal  = $countsByStatus[$status->id] ?? 0;
                            $statusLoaded = ($ordersByStatus[$status->id] ?? collect())->count();
                            $statusRemaining = $statusTotal - $statusLoaded;
                        @endphp
                        @if($statusRemaining > 0)
                            <div class="kanban-load-more pt-2 pb-1 text-center"
                                 data-status-id="{{ $status->id }}"
                                 data-next-page="2"
                                 data-remaining="{{ $statusRemaining }}">
                                <button onclick="kanbanLoadMore(this.closest('.kanban-load-more'))"
                                        class="w-full py-2.5 px-4 rounded-xl border border-purple-500/20 bg-purple-500/5 hover:bg-purple-500/10 text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-ellipsis"></i>
                                    <span class="load-more-label">Carregar mais ({{ $statusRemaining }} restantes)</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            </div>


        <!-- Calendar View Premium -->
        <div x-show="view === 'calendar'" 
             class="calendar-container animate-fade-in-up" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100">
             
            <div class="glass-card shadow-2xl border border-gray-100 dark:border-white/5 flex flex-col h-[calc(100vh-250px)] min-h-[600px] p-6 bg-white/50 dark:bg-slate-900/60 backdrop-blur-xl rounded-3xl">
                
                <!-- Cabeçalho do Calendário Premium -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between mb-6 border-b border-gray-100 dark:border-white/5 pb-6 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-600 flex items-center justify-center text-white border border-purple-500/20">
                            <i class="fa-solid fa-calendar-days text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white leading-tight tracking-tight uppercase" 
                                x-text="currentMonthName.charAt(0).toUpperCase() + currentMonthName.slice(1)"></h2>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Planejamento Mensal de entregas</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 bg-gray-100/50 dark:bg-slate-800/50 rounded-xl p-1 shrink-0 border border-gray-200 dark:border-white/5">
                        <button @click="prev()" class="p-2 hover:bg-white dark:hover:bg-slate-700 rounded-lg transition-all text-gray-400 hover:text-purple-500">
                            <i class="fa-solid fa-chevron-left text-sm"></i>
                        </button>
                        <button @click="today()" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-slate-300 hover:text-purple-500 transition-colors">
                            Hoje
                        </button>
                        <button @click="next()" class="p-2 hover:bg-white dark:hover:bg-slate-700 rounded-lg transition-all text-gray-400 hover:text-purple-500">
                            <i class="fa-solid fa-chevron-right text-sm"></i>
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


                <!-- Dias da Semana (Header) - Hide on Day view -->
                <div x-show="calendarView !== 'day'" class="grid grid-cols-7 mb-1 border-b border-gray-200 dark:border-gray-700">
                    <template x-for="day in ['D', 'S', 'T', 'Q', 'Q', 'S', 'S']">
                        <div class="text-center text-[10px] sm:text-xs font-bold text-gray-400 dark:text-gray-500 py-1 sm:py-2" x-text="day"></div>
                    </template>
                </div>

                <!-- Grid do Calendário Premium -->
                <div class="grid gap-px bg-gray-200/30 dark:bg-white/5 border border-gray-200/50 dark:border-white/5 rounded-2xl overflow-hidden flex-1 backdrop-blur-md"
                     :class="{ 'grid-cols-7': calendarView !== 'day', 'grid-cols-1': calendarView === 'day' }">
                    <template x-for="day in calendarDays" :key="day.date.toISOString()">
                        <div class="bg-white/60 dark:bg-slate-900/40 p-2 sm:p-3 transition-all relative group overflow-hidden flex flex-col min-h-[100px] sm:min-h-[120px]"
                             :class="{ 
                                'bg-gray-50/50 dark:bg-slate-900/20 text-gray-300 dark:text-gray-600': !day.isCurrentMonth && calendarView === 'month',
                                'ring-2 ring-inset ring-purple-500/30 dark:ring-purple-500/20 bg-purple-500/5': day.isToday
                             }">
                             
                            <!-- Data Premium -->
                            <div class="flex items-center justify-between mb-3 flex-shrink-0">
                                <span class="text-[10px] sm:text-xs font-black w-7 h-7 flex items-center justify-center rounded-lg transition-all"
                                      :class="{ 
                                        'bg-purple-600 text-white shadow-lg shadow-purple-600/30': day.isToday,
                                        'text-gray-900 dark:text-white': !day.isToday && (day.isCurrentMonth || calendarView !== 'month'),
                                        'text-gray-400 dark:text-gray-700': !day.isToday && !day.isCurrentMonth && calendarView === 'month'
                                      }" 
                                      x-text="day.date.getDate()">
                                </span>
                                <span x-show="calendarView === 'day'" class="text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="day.date.toLocaleDateString('pt-BR', { weekday: 'long' })"></span>
                            </div>

                            <!-- Eventos Premium -->
                            <div class="space-y-2 overflow-y-auto custom-scrollbar flex-1 pr-1">
                                <template x-for="event in getEventsForDay(day.date)">
                                    <div @click.stop="openOrderModal(event.id)"
                                         class="px-2 py-2 rounded-xl border border-white/10 shadow-sm transition-all hover:scale-[1.02] cursor-pointer relative overflow-hidden group/event"
                                         :style="`background-color: ${event.status_color}15; border-left: 3px solid ${event.status_color};`">
                                        
                                        <div class="flex items-center gap-2">
                                            <!-- Miniatura da Imagem Premium -->
                                            <template x-if="event.cover_image">
                                                <div class="flex-shrink-0 w-8 h-8 rounded-lg overflow-hidden bg-gray-100 dark:bg-slate-800 border border-white/5">
                                                    <img :src="event.cover_image" 
                                                         class="w-full h-full object-cover"
                                                         x-on:error="$el.style.display='none'">
                                                </div>
                                            </template>

                                            <div class="flex-1 min-w-0">
                                                <div class="font-black text-[9px] uppercase tracking-wide truncate leading-tight" 
                                                     :style="`color: ${event.status_color}`"
                                                     x-text="event.title"></div>
                                                <div class="flex items-center justify-between mt-0.5">
                                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter" x-text="'#' + event.id"></span>
                                                    <span class="text-[8px] font-bold text-purple-500 uppercase tracking-tighter" x-text="event.items_count + ' pçs'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detalhes em visão de Dia -->
                                        <div x-show="calendarView === 'day'" class="mt-2 pt-2 border-t border-white/5">
                                            <p class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest truncate" x-text="event.client"></p>
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

    <!-- Modal de Detalhes do Pedido Premium -->
    <div id="order-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md overflow-y-auto h-full w-full hidden z-[200] transition-all duration-500">
        <div class="relative top-5 mx-auto p-0 border border-white/10 w-full max-w-7xl shadow-2xl rounded-3xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-2xl mb-10 animate-slide-in-bottom">
            <div class="flex justify-between items-center p-8 border-b border-gray-100 dark:border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-600 flex items-center justify-center text-white border border-purple-500/20" style="color: #ffffff !important;">
                        <i class="fa-solid fa-file-invoice text-xl" style="color: #ffffff !important;"></i>
                    </div>
                    <div>
                        <h2 id="modal-title" class="text-xl md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Detalhes do Pedido</h2>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Informações detalhadas e acompanhamento</p>
                    </div>
                </div>
                <button onclick="closeOrderModal()" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-400 hover:text-rose-500 hover:bg-rose-500/10 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <div id="modal-content" class="p-8 space-y-8">
                <!-- Será preenchido via JavaScript - Layout em 2 colunas -->
            </div>
        </div>
    </div>

    <!-- Modal de Confirmacao para Remover Arquivo Premium -->
    <div id="delete-file-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl flex items-center justify-center z-[250] hidden transition-all">
        <div class="bg-white/95 dark:bg-slate-900/95 rounded-[2rem] shadow-2xl max-w-md w-full mx-4 border border-white/10 backdrop-blur-2xl transform transition-all">
            <div class="p-8">
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-20 h-20 bg-rose-500/10 dark:bg-rose-500/20 rounded-[2rem] flex items-center justify-center text-rose-500 mb-6 border border-rose-500/20">
                        <i class="fa-solid fa-trash-can-clock text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">
                        Remover arquivo
                    </h2>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed px-4">
                        Tem certeza que deseja remover <span id="delete-file-name" class="text-rose-500"></span>? Esta ação não pode ser desfeita.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button"
                            onclick="closeDeleteFileModal()"
                            class="px-6 py-4 bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                    <button type="button"
                            id="delete-file-confirm-btn"
                            onclick="confirmDeleteFile()"
                            class="px-6 py-4 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-lg shadow-rose-600/30">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Pagamento Adicional Premium -->
    <div id="payment-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl overflow-y-auto h-full w-full hidden z-[200] transition-all duration-500">
        <div class="relative top-20 mx-auto p-0 border border-white/10 w-full max-w-lg shadow-2xl rounded-[2.5rem] bg-white dark:bg-slate-900 overflow-hidden animate-slide-in-bottom">
            <!-- Header do Modal Premium -->
            <div class="flex justify-between items-center p-8 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                        <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Pagamento</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Registrar nova transação</p>
                    </div>
                </div>
                <button onclick="closePaymentModal()" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-400 hover:text-rose-500 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form id="payment-form" onsubmit="submitPayment(event)" class="p-8 space-y-6">
                <input type="hidden" id="payment-order-id" name="order_id">
                
                <!-- Valor Restante Premium -->
                <div class="p-6 rounded-3xl bg-purple-500/5 border border-purple-500/10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-purple-500/60 uppercase tracking-widest mb-1">Valor Restante</p>
                        <div class="text-3xl font-black text-purple-600 dark:text-purple-400 tracking-tight" id="remaining-amount">R$ 0,00</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                        <i class="fa-solid fa-scale-balanced text-xl"></i>
                    </div>
                </div>

                <!-- Valor a Pagar Premium -->
                <div class="space-y-3">
                    <label for="payment-amount" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Valor a Liquidar *</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                            <span class="text-sm font-black">R$</span>
                        </div>
                        <input type="number" 
                               id="payment-amount" 
                               name="amount" 
                               step="0.01" 
                               min="0.01"
                               required
                               class="w-full pl-12 pr-6 py-4 rounded-2xl border border-gray-100 dark:border-white/5 dark:bg-slate-800/50 dark:text-white dark:placeholder-gray-500 focus:border-purple-500 dark:focus:border-purple-500 focus:ring-0 transition-all text-base font-bold shadow-inner"
                               placeholder="0,00">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Forma de Pagamento Premium -->
                    <div class="space-y-3">
                        <label for="payment-method" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Meio de Pagamento *</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <select id="payment-method" 
                                    name="payment_method" 
                                    required
                                    class="w-full pl-12 pr-10 py-4 rounded-2xl border border-gray-100 dark:border-white/5 dark:bg-slate-800/50 dark:text-white focus:border-purple-500 dark:focus:border-purple-500 focus:ring-0 transition-all text-sm font-bold appearance-none shadow-inner cursor-pointer">
                                <option value="">Selecione...</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="pix">PIX</option>
                                <option value="cartao">Cartão</option>
                                <option value="transferencia">Transferência</option>
                                <option value="boleto">Boleto</option>
                            </select>
                        </div>
                    </div>

                    <!-- Data do Pagamento Premium -->
                    <div class="space-y-3">
                        <label for="payment-date" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data *</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>
                            <input type="date" 
                                   id="payment-date" 
                                   name="payment_date" 
                                   required
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full pl-12 pr-6 py-4 rounded-2xl border border-gray-100 dark:border-white/5 dark:bg-slate-800/50 dark:text-white focus:border-purple-500 dark:focus:border-purple-500 focus:ring-0 transition-all text-sm font-bold shadow-inner">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" 
                            class="w-full py-5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-purple-600/20 active:scale-[0.98]">
                        Confirmar Pagamento
                    </button>
                    <button type="button" 
                            onclick="closePaymentModal()" 
                            class="w-full py-5 bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edição de Imagem de Capa Premium -->
    <div id="cover-image-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl flex items-center justify-center p-4 hidden z-[200] transition-all duration-500 overflow-y-auto">
        <div class="relative my-auto mx-auto p-0 border border-white/10 w-full max-w-lg shadow-2xl rounded-[2.5rem] bg-white dark:bg-slate-900 overflow-hidden animate-slide-in-bottom">
            <!-- Header do Modal Premium -->
            <div class="flex justify-between items-center p-8 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white border border-indigo-400/30">
                        <i class="fa-solid fa-image text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Capa</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Personalizar visual do item</p>
                    </div>
                </div>
                <button onclick="closeCoverImageModal()" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-400 hover:text-rose-500 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form id="cover-image-form" enctype="multipart/form-data" onsubmit="submitCoverImage(event)" class="p-8 space-y-6">
                @csrf
                <input type="hidden" id="cover-item-id" name="item_id">
                
                <!-- Informação Premium -->
                <div class="flex items-start gap-4 p-5 rounded-3xl bg-blue-500/10 border border-blue-500/20 shadow-inner">
                    <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center text-white flex-shrink-0">
                        <i class="fa-solid fa-circle-info text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">Recomendação</p>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400">794 x 1123 pixels (A4 em 96 DPI)</p>
                    </div>
                </div>
                
                <!-- Selecionar Imagem Premium -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Selecione o Arquivo *</label>
                    <div class="relative group">
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
                                class="w-full flex flex-col items-center justify-center h-48 rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 hover:bg-purple-500/5 hover:border-purple-500/30 transition-all cursor-pointer group/upload">
                            <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-gray-100 dark:border-white/10 flex items-center justify-center text-gray-300 group-hover/upload:text-purple-500 group-hover/upload:scale-110 transition-all mb-4">
                                <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                            </div>
                            <span class="text-[10px] font-black text-gray-400 group-hover/upload:text-purple-500 uppercase tracking-widest">Ctrl+V, Arraste ou Clique</span>
                        </button>
                    </div>
                    <div id="cover-image-preview" class="hidden mt-4 rounded-2xl overflow-hidden border border-gray-100 dark:border-white/5 shadow-lg">
                        <img id="cover-preview-img" src="" alt="Preview" class="w-full h-auto">
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" 
                            class="w-full py-5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-purple-600/20 active:scale-[0.98]">
                        Salvar Nova Capa
                    </button>
                    <button type="button" 
                            onclick="closeCoverImageModal()" 
                            class="w-full py-5 bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- Modal de Confirmação de Movimentação Premium -->
    <div id="move-confirm-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl flex items-center justify-center z-[250] hidden transition-all">
        <div class="bg-white/95 dark:bg-slate-900/95 rounded-[2.5rem] shadow-2xl max-w-md w-full mx-4 border border-white/10 backdrop-blur-2xl transform transition-all animate-slide-in-bottom">
            <div class="p-8">
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-20 h-20 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-[2rem] flex items-center justify-center text-indigo-500 mb-6 border border-indigo-500/20">
                        <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h11m0 0L12 4m3 3-3 3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 17H9m0 0 3 3m-3-3 3-3" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">
                        Mover Pedido
                    </h2>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed px-4">
                        Tem certeza que deseja mover este pedido para <span id="move-confirm-status-name" class="text-indigo-600 dark:text-indigo-400"></span>?
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button"
                            onclick="closeMoveConfirmModal()"
                            class="px-6 py-4 bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                    <button type="button"
                            onclick="confirmMoveCard()"
                            class="px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-lg shadow-indigo-600/30">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitação de Edição Premium -->
    <div id="editRequestModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl flex items-center justify-center z-[250] hidden transition-all">
        <div class="bg-white/95 dark:bg-slate-900/95 rounded-[2.5rem] shadow-2xl max-w-lg w-full mx-4 border border-white/10 backdrop-blur-2xl overflow-hidden animate-slide-in-bottom">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-amber-500/10 dark:bg-amber-500/20 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-500/20">
                            <i class="fa-solid fa-pen-to-square text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Solicitar Edição</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Pedido #<span id="modalEditOrderId"></span></p>
                        </div>
                    </div>
                    <button onclick="closeEditRequestModal()" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-400 hover:text-rose-500 transition-all flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-5 rounded-3xl bg-blue-500/5 border border-blue-500/10 shadow-inner">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 flex-shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <p class="text-[11px] font-bold text-blue-700 dark:text-blue-400">
                            Esta solicitação será enviada para aprovação do administrador do sistema.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label for="editRequestReason" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                            Motivo da Edição *
                        </label>
                        <textarea 
                            id="editRequestReason" 
                            rows="4" 
                            class="w-full px-6 py-4 rounded-3xl border border-gray-100 dark:border-white/5 dark:bg-slate-800/50 dark:text-white dark:placeholder-gray-500 focus:border-purple-500 dark:focus:border-purple-500 focus:ring-0 transition-all text-sm font-bold shadow-inner resize-none"
                            placeholder="Descreva detalhadamente o motivo..."
                            maxlength="1000"></textarea>
                        <div class="flex justify-between items-center px-2">
                            <p id="editReasonError" class="text-[10px] font-bold text-rose-500 uppercase tracking-widest hidden">O motivo é obrigatório</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-auto">Máx. 1000 chars</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-8">
                    <button onclick="submitEditRequest()" 
                            class="w-full py-5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-purple-600/20 active:scale-[0.98]">
                        Enviar Solicitação
                    </button>
                    <button onclick="closeEditRequestModal()" 
                            class="w-full py-5 bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitação de Antecipação -->
    <!-- Modal de Solicitação de Antecipação Premium -->
    <div id="delivery-request-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl flex items-center justify-center z-[250] hidden transition-all">
        <div class="bg-white/95 dark:bg-slate-900/95 rounded-[2.5rem] shadow-2xl max-w-lg w-full mx-4 border border-white/10 backdrop-blur-2xl overflow-hidden animate-slide-in-bottom">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-blue-500/10 dark:bg-blue-500/20 rounded-2xl flex items-center justify-center text-blue-500 border border-blue-500/20">
                            <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Antecipação</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Solicitar prioridade de entrega</p>
                        </div>
                    </div>
                    <button onclick="closeDeliveryRequestModal()" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-800 text-gray-400 hover:text-rose-500 transition-all flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form id="delivery-request-form" onsubmit="submitDeliveryRequest(event)" class="space-y-6">
                    <input type="hidden" id="delivery-order-id" name="order_id">
                    <input type="hidden" id="current-delivery-date" name="current_delivery_date">
                    
                    <div class="p-6 rounded-3xl bg-amber-500/5 border border-amber-500/10 flex items-center justify-between translate-y-[-8px]">
                        <div>
                            <p class="text-[10px] font-black text-amber-500/60 uppercase tracking-widest mb-1">Entrega Atual</p>
                            <div class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight" id="current-delivery-display">-</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                            <i class="fa-solid fa-calendar-check text-xl"></i>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label for="requested-delivery-date" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nova Data Desejada *</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                                <i class="fa-solid fa-calendar-plus"></i>
                            </div>
                            <input type="date" 
                                   id="requested-delivery-date" 
                                   name="requested_delivery_date" 
                                   required
                                   class="w-full pl-12 pr-6 py-4 rounded-2xl border border-gray-100 dark:border-white/5 dark:bg-slate-800/50 dark:text-white focus:border-purple-500 dark:focus:border-purple-500 focus:ring-0 transition-all text-sm font-bold shadow-inner">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label for="delivery-reason" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Motivo da Prioridade *</label>
                        <textarea id="delivery-reason" 
                                  name="reason" 
                                  rows="3"
                                  required
                                  maxlength="500"
                                  class="w-full px-6 py-4 rounded-3xl border border-gray-100 dark:border-white/5 dark:bg-slate-800/50 dark:text-white dark:placeholder-gray-500 focus:border-purple-500 dark:focus:border-purple-500 focus:ring-0 transition-all text-sm font-bold shadow-inner resize-none"
                                  placeholder="Explique o motivo..."></textarea>
                    </div>

                    <div class="flex flex-col gap-3 pt-4">
                        <button type="submit" 
                                class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-blue-600/20 active:scale-[0.98]">
                            Enviar Solicitação
                        </button>
                        <button type="button" 
                                onclick="closeDeliveryRequestModal()" 
                                class="w-full py-5 bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-300 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 dark:hover:bg-slate-700 transition-all">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
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
                    
                    // Inicializar paginação se houver itens
                    if (data.items && data.items.length > 0) {
                        currentModalItemIndex = 0;
                        modalItemsCount = data.items.length;
                        setTimeout(updateModalItemVisibility, 50);
                    }
                    
                    orderModal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao carregar detalhes do pedido', 'error');
                });
        }
        
        // Lógica de paginação de itens no modal
        let currentModalItemIndex = 0;
        let modalItemsCount = 0;

        function updateModalItemVisibility() {
            for (let i = 0; i < modalItemsCount; i++) {
                const el = document.getElementById(`modal-item-${i}`);
                if (el) {
                    if (i === currentModalItemIndex) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }
            }
            const counterEl = document.getElementById('modal-item-counter');
            if (counterEl) {
                counterEl.textContent = `Item ${currentModalItemIndex + 1} de ${modalItemsCount}`;
            }
            
            const prevBtn = document.getElementById('modal-item-prev-btn');
            const nextBtn = document.getElementById('modal-item-next-btn');
            if (prevBtn) prevBtn.disabled = currentModalItemIndex === 0;
            if (nextBtn) nextBtn.disabled = currentModalItemIndex === modalItemsCount - 1;
        }

        function nextModalItem() {
            if (currentModalItemIndex < modalItemsCount - 1) {
                currentModalItemIndex++;
                updateModalItemVisibility();
            }
        }

        function prevModalItem() {
            if (currentModalItemIndex > 0) {
                currentModalItemIndex--;
                updateModalItemVisibility();
            }
        }

        window.nextModalItem = nextModalItem;
        window.prevModalItem = prevModalItem;

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
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600" data-file-row data-file-id="${data.file.id}" data-file-type="item" data-file-name="${encodeURIComponent(data.file.file_name || '')}">
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
                                <button type="button" onclick="event.stopPropagation(); deleteOrderFile(${itemId}, ${data.file.id}, 'item')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Remover arquivo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
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
                                            class="btn-modern btn-success">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                        <span class="btn-text text-white" style="color: white !important;">Arquivos da Arte (1)</span>
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

        let pendingDeleteFile = null;

        function deleteOrderFile(itemId, fileId, fileType) {
            const list = document.getElementById(`files-list-${itemId}`);
            const row = list ? list.querySelector(`[data-file-id="${fileId}"][data-file-type="${fileType}"]`) : null;
            const encodedName = row ? row.getAttribute('data-file-name') : null;
            const fileName = encodedName ? decodeURIComponent(encodedName) : null;
            openDeleteFileModal(itemId, fileId, fileType, fileName);
        }

        function openDeleteFileModal(itemId, fileId, fileType, fileName) {
            const modal = document.getElementById('delete-file-modal');
            const nameEl = document.getElementById('delete-file-name');
            if (!modal) return;

            pendingDeleteFile = { itemId, fileId, fileType };
            if (nameEl) {
                nameEl.textContent = fileName ? `"${fileName}"` : 'este arquivo';
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteFileModal() {
            const modal = document.getElementById('delete-file-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            pendingDeleteFile = null;
        }

        function confirmDeleteFile() {
            if (!pendingDeleteFile) return;
            const { itemId, fileId, fileType } = pendingDeleteFile;
            const confirmBtn = document.getElementById('delete-file-confirm-btn');
            const originalText = confirmBtn ? confirmBtn.textContent : null;

            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Removendo...';
            }

            fetch('/kanban/delete-file', {
                method: 'POST',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    file_id: fileId,
                    file_type: fileType
                })
            })
            .then(async response => {
                let data = {};
                const contentType = response.headers.get('content-type') || '';
                try {
                    if (contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        const text = await response.text();
                        data = { message: text };
                    }
                } catch (error) {
                    data = {};
                }
                if (!response.ok || !data.success) {
                    const message = data.message || `Erro ao remover arquivo (status ${response.status})`;
                    throw new Error(message);
                }
                return data;
            })
            .then(() => {
                const list = document.getElementById(`files-list-${itemId}`);
                if (list) {
                    const row = list.querySelector(`[data-file-id="${fileId}"][data-file-type="${fileType}"]`);
                    if (row) row.remove();

                    const remaining = list.querySelectorAll('[data-file-row]').length;
                    if (remaining === 0) {
                        if (!document.getElementById(`no-files-msg-${itemId}`)) {
                            list.insertAdjacentHTML('beforeend',
                                `<p class="file-empty-state" id="no-files-msg-${itemId}">Nenhum arquivo anexado.</p>`
                            );
                        }
                    }
                }

                const orderModal = document.getElementById('order-modal');
                const orderId = orderModal ? orderModal.getAttribute('data-current-order-id') : null;
                if (orderId) {
                    const btn = document.getElementById(`btn-download-files-${orderId}`);
                    if (btn) {
                        const current = parseInt(btn.getAttribute('data-count') || '0', 10);
                        const next = Math.max(0, current - 1);
                        if (next > 0) {
                            btn.setAttribute('data-count', next);
                            const span = btn.querySelector('.btn-text');
                            if (span) {
                                span.textContent = `Arquivos da Arte (${next})`;
                                span.style.setProperty('color', 'white', 'important');
                            }
                        } else {
                            btn.remove();
                        }
                    }
                }

                showNotification('Arquivo removido com sucesso!', 'success');
                if (orderId) {
                    fetch(`/kanban/order/${orderId}`)
                        .then(response => response.json())
                        .then(orderData => {
                            displayOrderDetails(orderData);
                        })
                        .catch(error => {
                            console.error('Erro ao recarregar detalhes do pedido:', error);
                        });
                }
                closeDeleteFileModal();
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification(error.message || 'Erro ao remover arquivo', 'error');
                closeDeleteFileModal();
            })
            .finally(() => {
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = originalText || 'Confirmar remocao';
                }
            });
        }

        window.deleteOrderFile = deleteOrderFile;
        window.openDeleteFileModal = openDeleteFileModal;
        window.closeDeleteFileModal = closeDeleteFileModal;
        window.confirmDeleteFile = confirmDeleteFile;

        function displayOrderDetails(order) {
            const payment = order.payment;
            const isPersonalized = order.origin === 'personalized';
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
                // Contar arquivo corel direto no item (SUB. TOTAL)
                if (item.corel_file_path) {
                    itemFilesCount += 1;
                }
                return sum + itemFilesCount;
            }, 0);
            
            let html = `
                <!-- Container principal com 2 colunas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 order-modal-layout">
                    <!-- COLUNA ESQUERDA -->
                    <div class="space-y-6">
                        <!-- Ações do Pedido -->
                        <div class="order-modal-card accent p-4">
                            <h4 class="order-modal-title mb-3">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                Ações
                            </h4>
                            <div class="order-action-grid">
                                <button onclick="openEditRequestModal(${order.id})" 
                                        class="btn-modern btn-warning min-w-[180px]">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Editar Pedido
                                </button>
                                @if(Auth::user()->isAdmin() || Auth::user()->isProducao())
                                <div class="flex items-center gap-2 w-full">
                                    <select id="move-status-select" 
                                            class="flex-1 h-[46px] px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-slate-200 dark:border-slate-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400">
                                        <option value="">Selecione a coluna...</option>
                                        @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" data-status-id="{{ $status->id }}">
                                            {{ $status->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <button onclick="moveCardToColumn(${order.id})" 
                                            class="btn-modern btn-primary h-[46px] min-w-[140px]">
                                        <i class="fa-solid fa-arrow-right"></i>
                                        Mover
                                    </button>
                                </div>
                                @endif
                                @if(Auth::user()->isAdminGeral())
                                <div class="flex items-center gap-2 w-full">
                                    <select id="transfer-user-select-${order.id}"
                                            class="flex-1 h-[46px] px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-slate-200 dark:border-slate-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400">
                                        <option value="">Transferir para...</option>
                                    </select>
                                    <button onclick="transferOrder(${order.id})"
                                            class="btn-modern btn-warning h-[46px] min-w-[140px]">
                                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                        Transferir
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Botões de Download -->
                        <div class="order-modal-card accent p-4">
                            <h4 class="order-modal-title mb-3">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                Downloads
                            </h4>
                            <div class="flex flex-wrap gap-2" id="downloads-list-${order.id}">
                                <a href="/kanban/download-costura/${order.id}" target="_blank"
                                   class="btn-modern btn-primary">
                                    <i class="fa-solid fa-download"></i>
                                    ${isPersonalized ? 'Folha Personalizado (A4)' : 'Folha Costura (A4)'}
                                </a>
                                <a href="/kanban/download-personalizacao/${order.id}" target="_blank"
                                   class="btn-modern btn-rose">
                                    <i class="fa-solid fa-download"></i>
                                    Folha Personalização (A4)
                                </a>
                                ${totalFiles > 0 ? `
                                <button onclick="downloadAllFiles(${order.id})"
                                        id="btn-download-files-${order.id}"
                                        data-count="${totalFiles}"
                                        class="btn-modern btn-success">
                                    <i class="fa-solid fa-file-zipper"></i>
                                    <span class="btn-text text-white" style="color: white !important;">Arquivos da Arte (${totalFiles})</span>
                                </button>
                                ` : ''}
                            </div>
                        </div>

                    <!-- Informações do Cliente -->
                <div class="order-modal-card p-4">
                    <h4 class="order-modal-title mb-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Cliente
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <div><strong class="text-gray-900 dark:text-gray-100">Nome:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client?.name || 'Sem cliente'}</span></div>
                        <div><strong class="text-gray-900 dark:text-gray-100">Telefone:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client?.phone_primary || '-'}</span></div>
                        ${order.client?.email ? `<div><strong class="text-gray-900 dark:text-gray-100">Email:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.email}</span></div>` : ''}
                        ${order.client?.cpf_cnpj ? `<div><strong class="text-gray-900 dark:text-gray-100">CPF/CNPJ:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.cpf_cnpj}</span></div>` : ''}
                    </div>
                </div>

                    <!-- Vendedor -->
                ${order.seller ? `
                <div class="order-modal-card p-4">
                    <h4 class="order-modal-title mb-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Itens do Pedido (${order.items.length})
                        </h4>
                        ${order.items.length > 1 ? `
                        <div class="flex items-center gap-2">
                            <button type="button" id="modal-item-prev-btn" onclick="prevModalItem()" class="btn-modern btn-neutral btn-compact px-3 disabled:opacity-50 disabled:cursor-not-allowed" title="Anterior">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <span class="text-sm font-bold text-gray-600 dark:text-gray-400 min-w-[80px] text-center" id="modal-item-counter">Item 1 de ${order.items.length}</span>
                            <button type="button" id="modal-item-next-btn" onclick="nextModalItem()" class="btn-modern btn-neutral btn-compact px-3 disabled:opacity-50 disabled:cursor-not-allowed" title="Próximo">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                        ` : ''}
                    </div>
                    
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
                        const coverImageUrl = item.cover_image_url
                            || (item.cover_image
                                ? (item.cover_image.startsWith('http')
                                    ? item.cover_image
                                    : (item.cover_image.startsWith('/storage/') || item.cover_image.startsWith('storage/')
                                        ? item.cover_image
                                        : `/storage/${item.cover_image.replace(/^\/+/, '')}`))
                                : null);

                        return `
                    <div id="modal-item-${index}" class="order-modal-card accent p-6 space-y-5 ${index === 0 ? '' : 'hidden'} fade-in">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200 dark:border-white/5">
                            <h5 class="text-xl font-bold text-indigo-900 dark:text-indigo-300">Item ${item.item_number || index + 1}</h5>
                            <span class="text-sm bg-indigo-600 dark:bg-indigo-600 !text-white px-3 py-1 rounded-full font-semibold" style="color: #fff !important;">${item.quantity} peças</span>
                        </div>

                        <!-- Nome da Arte -->
                        <div class="order-subcard p-4 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <h6 class="order-subcard-title flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                    Nome da Arte
                                </h6>
                                <button onclick="saveArtName(${item.id})" 
                                        id="save-art-name-btn-${item.id}"
                                        class="btn-modern btn-primary btn-compact !text-white flex items-center">
                                    Salvar
                                </button>
                            </div>
                            <input type="text" 
                                   id="art-name-input-${item.id}"
                                   value="${item.art_name || ''}"
                                   placeholder="Digite o nome da arte..."
                                   class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-gray-800 text-slate-800 dark:text-gray-100 placeholder-slate-400 dark:placeholder-slate-400 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 focus:border-indigo-500 dark:focus:border-indigo-400"
                                   onkeypress="if(event.key==='Enter') saveArtName(${item.id})">
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 flex items-start">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0 mt-0.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Este nome aparecerá no card do Kanban e nos PDFs
                            </p>
                        </div>

                        <!-- Imagem de Capa -->
                        <div class="order-subcard p-4 mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="order-subcard-title flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Imagem de Capa
                                </h6>
                                <button onclick="editItemCoverImage(${item.id})" 
                                        class="btn-modern btn-primary btn-compact !text-white">
                                    ${item.cover_image ? 'Alterar' : 'Adicionar'}
                                </button>
                            </div>
                            <div class="mb-2 flex items-start bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg p-2">
                                <svg class="w-4 h-4 mr-1.5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-indigo-700 dark:text-indigo-300"><strong>Tamanho recomendado:</strong> 794 x 1123 pixels (A4 em 96 DPI)</span>
                            </div>
                            ${coverImageUrl ? `
                                <img src="${coverImageUrl}" alt="Capa" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 shadow-md bg-gray-50 dark:bg-gray-900/20" style="max-height: 600px; object-fit: contain;" onerror="this.parentElement.innerHTML='<div class=\'text-center text-gray-500 dark:text-gray-400 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/20\'><svg class=\'w-16 h-16 mx-auto mb-3 text-gray-400 dark:text-gray-500\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg><p class=\'text-sm font-medium mb-1 text-gray-700 dark:text-gray-300\'>Imagem não encontrada</p><p class=\'text-xs text-gray-500 dark:text-gray-400\'>O arquivo de imagem não foi encontrado no servidor</p></div>'">
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
                        <div class="surface-muted p-4 mb-4">
                            <h6 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                ${isPersonalized ? 'Personalizado' : 'Costura'}
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
                        <div class="surface-muted p-4">
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
                        <div class="order-subcard p-4 mt-4">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="order-subcard-title flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Arquivos da Arte
                                </h6>
                                <div class="relative">
                                    <input type="file" id="file-upload-${item.id}" class="hidden" onchange="handleFileUpload(this, ${item.id})" accept=".cdr,.cdrx,.pdf,.jpg,.jpeg,.png,.ai,.eps,.svg">
                                    <button onclick="document.getElementById('file-upload-${item.id}').click()" 
                                            class="btn-modern btn-primary btn-compact !text-white flex items-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Adicionar Arquivo
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-2" id="files-list-${item.id}">
                                ${item.corel_file_path ? `
                                    <div class="flex items-center justify-between bg-purple-50 dark:bg-purple-900/20 rounded-md p-2 text-sm border border-purple-200 dark:border-purple-700">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-gray-900 dark:text-gray-300 font-medium">${item.corel_file_path.split('/').pop()}</span>
                                            <span class="ml-2 px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded border border-purple-200 dark:border-purple-800">Corel</span>
                                        </div>
                                        <a href="/storage/${item.corel_file_path}" target="_blank" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300" title="Baixar arquivo Corel">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                ` : ''}
                                ${item.files && item.files.length > 0 ? item.files.map(file => `
                                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600" data-file-row data-file-id="${file.id}" data-file-type="item" data-file-name="${encodeURIComponent(file.file_name || '')}">
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
                                            <button type="button" onclick="event.stopPropagation(); deleteOrderFile(${item.id}, ${file.id}, 'item')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Remover arquivo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                `).join('') : ''}
                                ${item.sublimations ? item.sublimations.map(sub => 
                                    sub.files && sub.files.length > 0 ? sub.files.map(file => `
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600" data-file-row data-file-id="${file.id}" data-file-type="sublimation" data-file-name="${encodeURIComponent(file.file_name || '')}">
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
                                                <button type="button" onclick="event.stopPropagation(); deleteOrderFile(${item.id}, ${file.id}, 'sublimation')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Remover arquivo">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    `).join('') : ''
                                ).filter(f => f).join('') : ''}
                                ${(!item.files || item.files.length === 0) && (!item.sublimations || !item.sublimations.some(sub => sub.files && sub.files.length > 0)) && !item.corel_file_path ? 
                                    '<p class="file-empty-state" id="no-files-msg-' + item.id + '">Nenhum arquivo anexado.</p>' : ''
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
                <div class="order-modal-card p-4">
                    <h4 class="order-modal-title mb-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="delivery-request-btn btn-modern btn-primary w-full justify-center"
                                data-order-id="${order.id}"
                                data-delivery-date="${order.delivery_date || ''}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Solicitar Antecipação
                        </button>
                        `}
                    </div>
                </div>

                        <!-- Pagamento -->
                ${order.payments && order.payments.length > 0 ? `
                <div class="order-modal-card p-4">
                    <h4 class="order-modal-title mb-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    class="btn-modern btn-success w-full justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="order-modal-card p-4">
                    <h4 class="order-modal-title mb-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="btn-modern btn-primary mt-2">
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
                <div class="order-modal-card p-4">
                    <h4 class="order-modal-title mb-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 104 0M9 5a2 2 0 114 0"></path></svg><span>Cliente:</span></div>';
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
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300 mt-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path></svg><span>Pedido:</span></div>';
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
                                                    changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300 mt-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V6a4 4 0 118 0v1m-9 0h10l1 13H6L7 7z"></path></svg><span>Item ' + itemId + ' (modificado):</span></div>';
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
                                                    changesHtml += '<div class="text-xs font-semibold text-green-700 dark:text-green-300 mt-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg><span>Novo Item ' + itemId + ' adicionado</span></div>';
                                                } else if (itemChange.type === 'removed') {
                                                    changesHtml += '<div class="text-xs font-semibold text-red-700 dark:text-red-300 mt-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg><span>Item ' + itemId + ' removido</span></div>';
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
            if (modalTitle) {
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
            } else {
                console.warn('VESTALIZE: Elemento #modal-title não encontrado no DOM');
            }
            
            const modalContent = document.getElementById('modal-content');
            if (modalContent) {
                modalContent.innerHTML = html;

                modalItemsCount = Array.isArray(order.items) ? order.items.length : 0;
                if (modalItemsCount === 0) {
                    currentModalItemIndex = 0;
                } else if (currentModalItemIndex >= modalItemsCount) {
                    currentModalItemIndex = modalItemsCount - 1;
                }
                updateModalItemVisibility();
            } else {
                console.warn('VESTALIZE: Elemento #modal-content não encontrado no DOM');
            }
            
            // Configurar o select de status após inserir o HTML
            const statusSelect = document.getElementById('move-status-select');
            if (statusSelect && order.status_id) {
                statusSelect.value = order.status_id;
            }

            // Preencher select de transferência se existir
            const transferSelect = document.getElementById(`transfer-user-select-${order.id}`);
            if (transferSelect) {
                loadTenantUsersIntoSelect(transferSelect, order.user ? order.user.id : null);
            }
        }

        let _cachedTenantUsers = null;
        function loadTenantUsersIntoSelect(selectEl, currentUserId) {
            if (_cachedTenantUsers) {
                populateTransferSelect(selectEl, _cachedTenantUsers, currentUserId);
                return;
            }
            fetch('/kanban/tenant-users')
                .then(r => r.json())
                .then(data => {
                    _cachedTenantUsers = data.users || [];
                    populateTransferSelect(selectEl, _cachedTenantUsers, currentUserId);
                })
                .catch(() => {});
        }

        function populateTransferSelect(selectEl, users, currentUserId) {
            selectEl.innerHTML = '<option value="">Transferir para...</option>';
            users.forEach(u => {
                if (u.id == currentUserId) return;
                const opt = document.createElement('option');
                opt.value = u.id;
                opt.textContent = u.name;
                selectEl.appendChild(opt);
            });
        }

        function transferOrder(orderId) {
            const selectEl = document.getElementById(`transfer-user-select-${orderId}`);
            if (!selectEl || !selectEl.value) {
                showNotification('Selecione um vendedor para transferir o pedido.', 'error');
                return;
            }
            const newUserId = selectEl.value;
            fetch(`/kanban/order/${orderId}/transfer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ user_id: newUserId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Atualiza o modal com os dados novos
                    fetch(`/kanban/order/${orderId}`)
                        .then(res => res.json())
                        .then(updatedOrder => displayOrderDetails(updatedOrder));
                } else {
                    showNotification(data.error || 'Erro ao transferir pedido.', 'error');
                }
            })
            .catch(() => showNotification('Erro ao transferir pedido.', 'error'));
        }
        window.transferOrder = transferOrder;

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
                    const badge = document.getElementById(`comment-badge-${orderId}`);
                    if (badge) {
                        const current = parseInt(badge.dataset.count || '0', 10);
                        const next = current + 1;
                        badge.dataset.count = String(next);
                        const countEl = badge.querySelector('[data-comment-count]');
                        if (countEl) countEl.textContent = String(next);
                        badge.classList.remove('hidden');
                        badge.setAttribute('title', `${next} comentarios`);
                    }
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
              document.getElementById('cover-image-preview').classList.add('hidden');
          }

          function openCoverImagePicker() {
              const coverInput = document.getElementById('cover-image-input');
              if (!coverInput) return;

              // Abrir o seletor de arquivos diretamente para garantir o upload
              coverInput.click();
          }

        function previewCoverImage(input) {
            const preview = document.getElementById('cover-image-preview');
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

            // Drag-and-drop support for the cover image dropzone
            const dropzoneBtn = document.querySelector('#cover-image-modal button[onclick*="openCoverImagePicker"]');
            if (dropzoneBtn) {
                dropzoneBtn.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('border-purple-500/50', 'bg-purple-500/10');
                });
                dropzoneBtn.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-purple-500/50', 'bg-purple-500/10');
                });
                dropzoneBtn.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-purple-500/50', 'bg-purple-500/10');
                    const input = document.getElementById('cover-image-input');
                    if (!input || !e.dataTransfer.files.length) return;
                    const file = e.dataTransfer.files[0];
                    if (!file.type.startsWith('image/')) return;
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                    previewCoverImage(input);
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

        function parseJsonResponse(response, requestLabel) {
            const contentType = response.headers.get('content-type') || '';

            return response.text().then(text => {
                let data = null;

                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (error) {
                        console.error(`Resposta nao-JSON em ${requestLabel}:`, {
                            status: response.status,
                            contentType,
                            bodyPreview: text.slice(0, 200)
                        });
                    }
                }

                if (!response.ok) {
                    throw new Error(data?.message || data?.error || `Erro HTTP ${response.status}`);
                }

                if (!data) {
                    throw new Error('Resposta invalida do servidor.');
                }

                return data;
            });
        }

        function submitCoverImage(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const itemId = formData.get('item_id');
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="animate-pulse">Enviando...</span>';
            
            fetch(`/order-items/${itemId}/cover-image`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => parseJsonResponse(response, 'upload da imagem de capa'))
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

        function createKanbanCardImagePlaceholder() {
            const placeholder = document.createElement('div');
            placeholder.className = 'h-full w-full bg-gradient-to-br from-indigo-500/10 to-purple-500/10 flex items-center justify-center';
            placeholder.innerHTML = '<i class="fa-solid fa-image text-purple-500/20 text-3xl"></i>';
            return placeholder;
        }

        function createKanbanCardImageElement(imageUrl) {
            const image = document.createElement('img');
            image.src = imageUrl;
            image.alt = 'Capa do Pedido';
            image.className = 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110';
            return image;
        }

        function createKanbanCardImageOverlay() {
            const overlay = document.createElement('div');
            overlay.className = 'absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none';
            return overlay;
        }

        function ensureKanbanCardImageContainer(card) {
            let imageContainer = card.querySelector('[data-card-cover-container]');
            if (imageContainer) {
                return imageContainer;
            }

            const contentContainer = card.querySelector('[data-card-content]');
            if (!contentContainer) {
                return null;
            }

            let wrapper = card.querySelector('[data-card-cover-wrapper]');
            if (!wrapper) {
                wrapper = document.createElement('div');
                wrapper.className = 'px-3 pt-3';
                wrapper.setAttribute('data-card-cover-wrapper', 'true');
                card.insertBefore(wrapper, contentContainer);
            }

            imageContainer = document.createElement('div');
            imageContainer.className = 'h-44 bg-gray-100/50 dark:bg-slate-900/50 overflow-hidden rounded-xl border border-gray-200 dark:border-white/5 relative group-hover/img shadow-inner';
            imageContainer.setAttribute('data-card-cover-container', 'true');
            wrapper.appendChild(imageContainer);

            return imageContainer;
        }

        function renderKanbanCardImage(imageContainer, imageUrl) {
            const image = createKanbanCardImageElement(imageUrl);

            image.addEventListener('error', function() {
                console.error('Erro ao carregar imagem:', imageUrl);
                imageContainer.replaceChildren(createKanbanCardImagePlaceholder());
            }, { once: true });

            image.addEventListener('load', function() {
                console.log('Imagem carregada com sucesso:', imageUrl);
            }, { once: true });

            imageContainer.replaceChildren(image, createKanbanCardImageOverlay());
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
            const imageContainer = ensureKanbanCardImageContainer(card);
            if (!imageContainer) {
                console.warn('Container de imagem não encontrado no card');
                return;
            }
            
            // Adicionar timestamp para forçar reload da imagem
            const imageUrlWithTimestamp = imageUrl + (imageUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
            
            // Atualizar a imagem
            console.log('Atualizando imagem do card:', imageUrlWithTimestamp);
            renderKanbanCardImage(imageContainer, imageUrlWithTimestamp);
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

            fetch(`/order-items/${itemId}/art-name`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    art_name: artName
                })
            })
            .then(response => parseJsonResponse(response, 'salvar nome da arte'))
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
        window.deleteOrderFile = deleteOrderFile;
        window.previewCoverImage = previewCoverImage;
        window.openCoverImagePicker = openCoverImagePicker;
        window.closeCoverImageModal = closeCoverImageModal;
        })(); // Fim da IIFE
    </script>

    <script>
    /**
     * Load the next page of orders for a Kanban column via AJAX.
     * @param {HTMLElement} container – the .kanban-load-more element
     */
    async function kanbanLoadMore(container) {
        const btn        = container.querySelector('button');
        const label      = container.querySelector('.load-more-label');
        const statusId   = container.dataset.statusId;
        const nextPage   = parseInt(container.dataset.nextPage, 10);
        const column     = container.closest('.kanban-column');

        btn.disabled = true;
        label.textContent = 'Carregando...';

        try {
            const params = new URLSearchParams(window.location.search);
            params.set('status_id', statusId);
            params.set('page', nextPage);

            const resp = await fetch('/kanban/load-more?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!resp.ok) throw new Error('Erro ' + resp.status);

            const data = await resp.json();

            if (data.success && data.html) {
                // Insert new cards before the load-more container
                const tmp = document.createElement('div');
                tmp.innerHTML = data.html;
                while (tmp.firstChild) {
                    column.insertBefore(tmp.firstChild, container);
                }

                if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                    window.Alpine.initTree(column);
                }
            }

            if (data.hasMore) {
                container.dataset.nextPage = nextPage + 1;
                const remaining = parseInt(container.dataset.remaining, 10) - (data.count || 0);
                container.dataset.remaining = Math.max(0, remaining);
                label.textContent = 'Carregar mais (' + Math.max(0, remaining) + ' restantes)';
                btn.disabled = false;
            } else {
                container.remove();
            }
        } catch (e) {
            label.textContent = 'Erro ao carregar. Tente novamente.';
            btn.disabled = false;
        }
    }
    window.kanbanLoadMore = kanbanLoadMore;
    </script>

    <div class="mt-10">
        <x-kanban-bottom-nav :active-type="$viewType" />
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="{{ asset('js/kanban-sortable.js') }}"></script>
@endsection
