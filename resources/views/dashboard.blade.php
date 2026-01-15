@extends('layouts.admin')

@section('content')
<style>
/* Animações Premium */
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

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

@keyframes countUp {
    from { opacity: 0; transform: scale(0.5); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

.animate-slide-in-right {
    animation: slideInRight 0.5s ease-out forwards;
}

.animate-pulse-soft {
    animation: pulse-soft 2s ease-in-out infinite;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-count-up {
    animation: countUp 0.5s ease-out forwards;
}

/* Delays de animação */
.delay-100 { animation-delay: 0.1s; opacity: 0; }
.delay-200 { animation-delay: 0.2s; opacity: 0; }
.delay-300 { animation-delay: 0.3s; opacity: 0; }
.delay-400 { animation-delay: 0.4s; opacity: 0; }
.delay-500 { animation-delay: 0.5s; opacity: 0; }

/* Efeito Shimmer para loading */
.shimmer-bg {
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

/* Scrollbar Customizada */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

/* Glassmorphism para Cards */
.glass-card {
    background: #030303;
    border: 1px solid rgba(255, 255, 255, 0.04);
    border-radius: 12px;
}

.dark .glass-card {
    background: #030303;
    border: 1px solid rgba(255, 255, 255, 0.04);
}

/* Hover Effects */
.hover-lift {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.hover-lift:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.dark .hover-lift:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
}

/* Stat Card Gradient Borders */
.stat-card-gradient {
    position: relative;
    overflow: hidden;
}

.stat-card-gradient::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 2px;
    background: linear-gradient(135deg, var(--gradient-from), var(--gradient-to));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card-gradient:hover::before {
    opacity: 1;
}

/* Mobile Responsiveness */
@media (max-width: 640px) {
    .widget-container {
        gap: 1rem !important;
    }
    
    .dashboard-widget {
        border-radius: 1.5rem !important;
    }
    
    .dashboard-widget .px-8 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .dashboard-widget .py-8 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    .dashboard-widget .p-8 {
        padding: 1rem !important;
    }
    
    .carousel-slide {
        min-width: 280px !important;
        max-width: 280px !important;
    }
}
</style>

<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-6 sm:space-y-8">
    <!-- Header Premium com Animação -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 pb-2 animate-fade-in-up">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center text-purple-500">
                <i class="fa-solid fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    Dashboard <span class="text-purple-500">Produção</span>
                </h1>
                    <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] sm:tracking-[0.3em]">Visão Geral e métricas operacionais</p>
                </div>
            </div>
        </div>

        <!-- Filtros Rápidos / Período -->
        <div class="glass-card p-1.5 sm:p-2 rounded-2xl flex flex-wrap items-center gap-1.5 sm:gap-2 animate-slide-in-right">
            <form method="GET" action="{{ route('production.dashboard') }}" id="dashboard-filter-form" class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <input type="hidden" name="filter_submitted" value="1">
                <input type="hidden" name="delivery_filter" value="{{ $deliveryFilter ?? 'today' }}">
                
                <div class="relative group">
                    <i class="fa-solid fa-calendar-days absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-gray-400 text-[9px] sm:text-[10px] group-hover:text-indigo-500 transition-colors"></i>
                    <select name="period" onchange="this.form.submit()" 
                            class="pl-8 sm:pl-10 pr-6 sm:pr-8 py-2 sm:py-2.5 bg-transparent border-none rounded-xl text-[10px] sm:text-xs font-black text-gray-400 dark:text-slate-300 uppercase tracking-widest focus:ring-0 appearance-none cursor-pointer">
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Semana</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Mês</option>
                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Trimestre</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Ano</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>

                @if($period == 'custom')
                <div class="flex items-center gap-1 sm:gap-2">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="px-2 sm:px-4 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[10px] sm:text-xs font-bold text-gray-600 dark:text-slate-300 focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    <span class="text-gray-400 font-bold text-[10px] sm:text-xs uppercase">até</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="px-2 sm:px-4 py-2 bg-white dark:bg-slate-800 border-none rounded-xl text-[10px] sm:text-xs font-bold text-gray-600 dark:text-slate-300 focus:ring-2 focus:ring-indigo-500 shadow-sm">
                    <button type="submit" class="p-2 sm:p-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-indigo-600/30 transition-all active:scale-95">
                        <i class="fa-solid fa-arrows-rotate text-sm"></i>
                    </button>
                </div>
                @endif
                
                <!-- Gatilho do Modal de Colunas -->
                <button type="button" @click="$dispatch('open-modal', 'kanban-columns')" 
                        class="px-3 sm:px-4 py-2 sm:py-2.5 bg-transparent text-gray-500 dark:text-slate-400 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest hover:bg-white/5 transition-all flex items-center gap-1.5 sm:gap-2 border border-white/10 active:scale-95">
                    <i class="fa-solid fa-sliders text-purple-500"></i> 
                    <span class="hidden sm:inline">Colunas</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Cards de Estatísticas Principais -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        @php
            $stats = [
                [
                    'title' => 'Total Pedidos',
                    'value' => $totalOrders ?? 0,
                    'icon' => 'fa-box',
                    'gradient' => 'from-blue-500 to-cyan-500',
                    'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'change' => '+12%',
                    'changeType' => 'up'
                ],
                [
                    'title' => 'Em Produção',
                    'value' => collect($ordersByStatus ?? [])->sum(),
                    'icon' => 'fa-cogs',
                    'gradient' => 'from-amber-500 to-orange-500',
                    'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                    'text' => 'text-amber-600 dark:text-amber-400',
                    'change' => '85%',
                    'changeType' => 'neutral'
                ],
                [
                    'title' => 'Para Hoje',
                    'value' => isset($deliveryOrders) ? $deliveryOrders->filter(fn($o) => \Carbon\Carbon::parse($o->delivery_date)->isToday())->count() : 0,
                    'icon' => 'fa-clock',
                    'gradient' => 'from-rose-500 to-pink-500',
                    'bg' => 'bg-rose-50 dark:bg-rose-900/20',
                    'text' => 'text-rose-600 dark:text-rose-400',
                    'change' => 'Urgente',
                    'changeType' => 'urgent'
                ],
                [
                    'title' => 'Atrasados',
                    'value' => isset($deliveryOrders) ? $deliveryOrders->filter(fn($o) => \Carbon\Carbon::parse($o->delivery_date)->isPast() && !\Carbon\Carbon::parse($o->delivery_date)->isToday())->count() : 0,
                    'icon' => 'fa-exclamation-triangle',
                    'gradient' => 'from-red-500 to-rose-600',
                    'bg' => 'bg-red-50 dark:bg-red-900/20',
                    'text' => 'text-red-600 dark:text-red-400',
                    'change' => 'Crítico',
                    'changeType' => 'down'
                ],
            ];
        @endphp

        @foreach($stats as $index => $stat)
        <div class="stat-card-gradient glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-gray-100/50 dark:border-slate-800/50 shadow-lg hover-lift animate-fade-in-up delay-{{ ($index + 1) * 100 }}"
             style="--gradient-from: {{ $stat['gradient'] == 'from-blue-500 to-cyan-500' ? '#3b82f6' : ($stat['gradient'] == 'from-amber-500 to-orange-500' ? '#f59e0b' : ($stat['gradient'] == 'from-rose-500 to-pink-500' ? '#f43f5e' : '#ef4444')) }};
                    --gradient-to: {{ $stat['gradient'] == 'from-blue-500 to-cyan-500' ? '#06b6d4' : ($stat['gradient'] == 'from-amber-500 to-orange-500' ? '#f97316' : ($stat['gradient'] == 'from-rose-500 to-pink-500' ? '#ec4899' : '#e11d48')) }};">
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 flex items-center justify-center text-purple-500">
                    <i class="fa-solid {{ $stat['icon'] }} text-xl"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-full
                    {{ $stat['changeType'] == 'up' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                    {{ $stat['changeType'] == 'down' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                    {{ $stat['changeType'] == 'neutral' ? 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300' : '' }}
                    {{ $stat['changeType'] == 'urgent' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 animate-pulse-soft' : '' }}">
                    {{ $stat['change'] }}
                </span>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] sm:text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest">{{ $stat['title'] }}</p>
                <p class="text-2xl sm:text-4xl font-black text-gray-900 dark:text-white tabular-nums animate-count-up" data-target="{{ $stat['value'] }}">
                    {{ $stat['value'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Container de Widgets Arrastáveis -->
    <div class="widget-container grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8 pb-12">
        
        <!-- Widget: Pedidos por Data de Entrega (Full Width) -->
        <div class="dashboard-widget lg:col-span-3 glass-card rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl shadow-gray-200/50 dark:shadow-black/40 border border-gray-100/50 dark:border-slate-800/50 overflow-hidden animate-fade-in-up delay-500" id="widget-delivery-carousel">
            <div class="px-4 sm:px-8 py-4 sm:py-8 border-b border-gray-100/50 dark:border-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-3 sm:gap-4 widget-drag-handle cursor-grab active:cursor-grabbing">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                        <i class="fa-solid fa-truck-fast text-sm sm:text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Cronograma de <span class="bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent">Entrega</span></h2>
                        <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest hidden sm:block">Pedidos prioritários e prazos próximos</p>
                    </div>
                </div>

                <div class="flex items-center gap-1 bg-gray-50/80 dark:bg-slate-800/80 p-1 rounded-xl sm:rounded-2xl backdrop-blur-sm">
                    <button onclick="changeDeliveryFilter('today')" 
                            class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all {{ $deliveryFilter == 'today' ? 'bg-white dark:bg-slate-700 text-indigo-600 shadow-md' : 'text-gray-400 hover:text-gray-600 dark:hover:text-slate-300' }}">
                        Hoje
                    </button>
                    <button onclick="changeDeliveryFilter('week')" 
                            class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all {{ $deliveryFilter == 'week' ? 'bg-white dark:bg-slate-700 text-indigo-600 shadow-md' : 'text-gray-400 hover:text-gray-600 dark:hover:text-slate-300' }}">
                        Semana
                    </button>
                    <button onclick="changeDeliveryFilter('month')" 
                            class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all {{ $deliveryFilter == 'month' ? 'bg-white dark:bg-slate-700 text-indigo-600 shadow-md' : 'text-gray-400 hover:text-gray-600 dark:hover:text-slate-300' }}">
                        Mês
                    </button>
                </div>
            </div>

            <div class="p-4 sm:p-8 widget-content">
                @if(isset($deliveryOrders) && $deliveryOrders->count() > 0)
                <div class="relative group/carousel">
                    <!-- Carrossel Container -->
                    <div id="delivery-carousel" class="overflow-hidden">
                        <div class="flex gap-4 sm:gap-6 overflow-x-auto pb-4 sm:pb-6 scrollbar-hide snap-x snap-mandatory" id="carousel-track">
                            @foreach($deliveryOrders as $orderIndex => $order)
                            @php
                                $firstItem = $order->items->first();
                                $coverImage = $order->cover_image_url ?? $firstItem?->cover_image_url;
                                $artName = $firstItem?->art_name;
                                $displayName = $artName ?? ($order->client?->name ?? 'Sem cliente');
                                $storeName = $order->store?->name ?? 'Loja Principal';
                                $deliveryDate = \Carbon\Carbon::parse($order->delivery_date)->startOfDay();
                                $today = \Carbon\Carbon::now()->startOfDay();
                                $daysUntilDelivery = (int) $today->diffInDays($deliveryDate, false);
                                $statusColor = $daysUntilDelivery < 0 ? 'red' : ($daysUntilDelivery <= 3 ? 'orange' : 'green');
                            @endphp
                            <div class="carousel-slide flex-shrink-0 snap-start" style="min-width: 280px; max-width: 320px;">
                                <div class="group/card relative bg-white dark:bg-slate-800 rounded-2xl sm:rounded-[2rem] border border-gray-100 dark:border-slate-700 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden cursor-pointer"
                                     onclick="window.location.href='{{ route('orders.show', $order->id) }}'"
                                     style="animation-delay: {{ $orderIndex * 0.1 }}s">
                                    
                                    <!-- Imagem de Capa Premium -->
                                    <div class="relative h-36 sm:h-44 overflow-hidden">
                                        @if($coverImage)
                                        <img src="{{ $coverImage }}" class="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-700">
                                        @else
                                        <div class="w-full h-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                                            <i class="fa-solid fa-shirt text-3xl sm:text-4xl text-white/30"></i>
                                        </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                                        
                                        <!-- ID Badge -->
                                        <div class="absolute top-3 sm:top-4 left-3 sm:left-4 px-2 sm:px-3 py-1 sm:py-1.5 bg-white/20 backdrop-blur-md rounded-lg sm:rounded-xl border border-white/30 text-[9px] sm:text-[10px] font-black text-white tracking-widest uppercase">
                                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                        </div>

                                        @if($order->is_event)
                                        <div class="absolute top-3 sm:top-4 right-3 sm:right-4">
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-red-500/40 animate-pulse-soft">
                                                <i class="fa-solid fa-bolt text-[10px] sm:text-xs"></i>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="absolute bottom-3 sm:bottom-4 left-3 sm:left-4 right-3 sm:right-4">
                                            <span class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 bg-{{ $statusColor }}-500/90 backdrop-blur-sm rounded-full text-[9px] sm:text-[10px] font-black text-white uppercase tracking-wider">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                                {{ $daysUntilDelivery == 0 ? 'Hoje' : ($daysUntilDelivery < 0 ? 'Atrasado' : "Em {$daysUntilDelivery}d") }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-4 sm:p-6">
                                        <h3 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight truncate mb-1">
                                            {{ $displayName }}
                                        </h3>
                                        <div class="flex items-center gap-1.5 mb-3 sm:mb-4 opacity-60">
                                            <i class="fa-solid fa-store text-[7px] sm:text-[8px]"></i>
                                            <span class="text-[8px] sm:text-[9px] font-bold uppercase tracking-wider truncate">{{ $storeName }}</span>
                                        </div>

                                        <!-- Barra de Progresso -->
                                        <div class="space-y-1.5 sm:space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest">Prazo</span>
                                                <span class="text-[9px] sm:text-[10px] font-bold text-gray-500">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m') }}</span>
                                            </div>
                                            <div class="w-full h-1 sm:h-1.5 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-{{ $statusColor }}-400 to-{{ $statusColor }}-600 transition-all duration-1000 rounded-full" 
                                                     style="width: {{ max(10, min(100, 100 - ($daysUntilDelivery * 10))) }}%"></div>
                                            </div>
                                        </div>

                                        <!-- Footer -->
                                        <div class="mt-4 sm:mt-6 pt-3 sm:pt-4 border-t border-gray-50 dark:border-slate-700/50 flex items-center justify-between">
                                            <div class="flex items-center gap-1.5 sm:gap-2">
                                                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-gray-50 dark:bg-slate-700 flex items-center justify-center">
                                                    <i class="fa-solid fa-box-open text-[8px] sm:text-[10px] text-gray-400"></i>
                                                </div>
                                                <span class="text-[9px] sm:text-[10px] font-black text-gray-900 dark:text-white">{{ $order->items->sum('quantity') }} pçs</span>
                                            </div>
                                            <span class="text-[8px] sm:text-[9px] font-bold text-gray-400 uppercase">{{ $order->status->name ?? 'S/ Status' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Controles do Carrossel -->
                    <button onclick="previousSlide()" class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-gray-100 dark:border-slate-700 items-center justify-center opacity-0 group-hover/carousel:opacity-100 group-hover/carousel:-translate-x-6 transition-all hover:scale-110 active:scale-95">
                        <i class="fa-solid fa-chevron-left text-gray-400"></i>
                    </button>
                    <button onclick="nextSlide()" class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-gray-100 dark:border-slate-700 items-center justify-center opacity-0 group-hover/carousel:opacity-100 group-hover/carousel:translate-x-6 transition-all hover:scale-110 active:scale-95">
                        <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    </button>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 sm:py-20 text-gray-400 opacity-40">
                    <i class="fa-solid fa-calendar-xmark text-3xl sm:text-4xl mb-3 sm:mb-4"></i>
                    <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.15em] sm:tracking-[0.2em] text-center">Nenhuma entrega programada para este período</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Widget: Tempo Médio por Setor -->
        <div class="dashboard-widget lg:col-span-2 glass-card rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl shadow-gray-200/50 dark:shadow-black/40 border border-gray-100/50 dark:border-slate-800/50 overflow-hidden animate-fade-in-up delay-300" id="widget-sector-performance">
            <div class="px-4 sm:px-8 py-4 sm:py-8 border-b border-gray-100/50 dark:border-slate-800/50 flex items-center justify-between">
                <div class="flex items-center gap-3 sm:gap-4 widget-drag-handle cursor-grab active:cursor-grabbing">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                        <i class="fa-solid fa-microchip text-sm sm:text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Performance por <span class="bg-gradient-to-r from-purple-500 to-violet-600 bg-clip-text text-transparent">Setor</span></h2>
                        <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest hidden sm:block">Eficiência granulada da produção</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 widget-content">
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="w-full min-w-[400px]">
                        <thead>
                            <tr class="text-left border-b border-gray-50 dark:border-slate-800">
                                <th class="pb-3 sm:pb-4 text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest px-3 sm:px-4">Estágio</th>
                                <th class="pb-3 sm:pb-4 text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest px-3 sm:px-4">Ciclo Médio</th>
                                <th class="pb-3 sm:pb-4 text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest px-3 sm:px-4 hidden sm:table-cell">Pico (Max)</th>
                                <th class="pb-3 sm:pb-4 text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest px-3 sm:px-4 text-right">Volume</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                            @foreach($statuses as $status)
                            @php $stat = collect($statusStats)->firstWhere('status_id', $status->id); @endphp
                            <tr class="group hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 sm:py-4 px-3 sm:px-4">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full shadow-sm" style="background-color: {{ $status->color ?? '#6366f1' }}; box-shadow: 0 0 8px {{ $status->color ?? '#6366f1' }}40;"></div>
                                        <span class="text-[10px] sm:text-xs font-black text-gray-700 dark:text-slate-300 uppercase tracking-tight">{{ $status->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 sm:py-4 px-3 sm:px-4">
                                    <span class="text-xs sm:text-sm font-black text-gray-900 dark:text-white tabular-nums">{{ $stat['avg_formatted'] ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3 sm:py-4 px-3 sm:px-4 italic text-gray-400 text-[10px] sm:text-xs hidden sm:table-cell">
                                    {{ $stat['max_formatted'] ?? 'N/A' }}
                                </td>
                                <td class="py-3 sm:py-4 px-3 sm:px-4 text-right">
                                    <span class="text-[9px] sm:text-[10px] font-black text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md sm:rounded-lg">
                                        {{ $ordersByStatus[$status->id] ?? 0 }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Widget: Distribuição de Carga -->
        <div class="dashboard-widget glass-card rounded-[1.5rem] sm:rounded-[2.5rem] shadow-2xl shadow-gray-200/50 dark:shadow-black/40 border border-gray-100/50 dark:border-slate-800/50 overflow-hidden animate-fade-in-up delay-400" id="widget-load-distribution">
            <div class="px-4 sm:px-8 py-4 sm:py-8 border-b border-gray-100/50 dark:border-slate-800/50 flex items-center justify-between">
                <div class="flex items-center gap-3 sm:gap-4 widget-drag-handle cursor-grab active:cursor-grabbing">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-layer-group text-sm sm:text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Distribuição</h2>
                        <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest hidden sm:block">Carga atual no sistema</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 widget-content space-y-4 sm:space-y-6">
                @foreach($statuses->take(6) as $status)
                @php 
                    $count = $ordersByStatus[$status->id] ?? 0;
                    $percent = $totalOrders > 0 ? ($count / $totalOrders) * 100 : 0;
                @endphp
                <div class="space-y-1.5 sm:space-y-2 group">
                    <div class="flex justify-between items-end">
                        <span class="text-[8px] sm:text-[9px] font-black text-gray-500 uppercase tracking-widest group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">{{ $status->name }}</span>
                        <span class="text-[10px] sm:text-xs font-black text-gray-900 dark:text-white">{{ $count }}</span>
                    </div>
                    <div class="w-full h-1.5 sm:h-2 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out group-hover:opacity-80" 
                             style="width: {{ $percent }}%; background: linear-gradient(90deg, {{ $status->color ?? '#6366f1' }}, {{ $status->color ?? '#6366f1' }}cc);"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<!-- Modal de Colunas do Kanban - Estilo Premium -->
<div x-data="{ open: false }" 
     @open-modal.window="if ($event.detail === 'kanban-columns') open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center p-4">
    
    <div @click="open = false" 
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="relative glass-card rounded-[2rem] sm:rounded-[3rem] shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-100/50 dark:border-slate-800/50">
        
        <div class="px-6 sm:px-10 py-6 sm:py-10">
            <div class="flex justify-between items-center mb-6 sm:mb-10">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                        <i class="fa-solid fa-toggle-on text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Visibilidade do Kanban</h3>
                        <p class="text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-[0.15em] sm:tracking-[0.2em]">Selecione quais setores exibir</p>
                    </div>
                </div>
                <button @click="open = false" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-gray-400"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('production.dashboard') }}" class="space-y-6 sm:space-y-8">
                <input type="hidden" name="filter_submitted" value="1">
                <input type="hidden" name="period" value="{{ $period }}">
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-6">
                    @foreach($allStatuses as $status)
                    <label class="group relative flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 hover:border-indigo-500 transition-all cursor-pointer has-[:checked]:bg-indigo-50/50 dark:has-[:checked]:bg-indigo-900/20 has-[:checked]:border-indigo-300 dark:has-[:checked]:border-indigo-600">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="columns[]" value="{{ $status->id }}"
                                   {{ in_array($status->id, $selectedColumns) ? 'checked' : '' }}
                                   class="peer hidden">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg sm:rounded-xl border-2 border-gray-200 dark:border-slate-700 peer-checked:bg-gradient-to-br peer-checked:from-indigo-500 peer-checked:to-purple-600 peer-checked:border-transparent transition-all flex items-center justify-center">
                                <i class="fa-solid fa-check text-[8px] sm:text-[10px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <span class="text-[10px] sm:text-xs font-black text-gray-700 dark:text-slate-300 uppercase tracking-tight">{{ $status->name }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="flex gap-3 sm:gap-4 pt-4 sm:pt-6 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="document.querySelectorAll('input[name=\'columns[]\']').forEach(i => i.checked = false)"
                            class="flex-1 py-4 sm:py-5 rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-gray-400 border border-gray-100 dark:border-slate-800 hover:text-gray-900 dark:hover:text-white hover:border-gray-300 dark:hover:border-slate-600 transition-all active:scale-98">
                        Limpar
                    </button>
                    <button type="submit" class="flex-[2] py-4 sm:py-5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl sm:rounded-2xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-600/30 hover:shadow-2xl hover:shadow-indigo-600/40 hover:scale-[1.02] active:scale-98 transition-all">
                        Aplicar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/dashboard-widgets.js') }}"></script>

<script>
// Carrossel de Pedidos - Scroll Horizontal
function nextSlide() {
    const track = document.getElementById('carousel-track');
    if (!track) return;
    const slideWidth = window.innerWidth < 640 ? 296 : 340;
    track.scrollBy({ left: slideWidth, behavior: 'smooth' });
}

function previousSlide() {
    const track = document.getElementById('carousel-track');
    if (!track) return;
    const slideWidth = window.innerWidth < 640 ? 296 : 340;
    track.scrollBy({ left: -slideWidth, behavior: 'smooth' });
}

function changeDeliveryFilter(filter) {
    const form = document.getElementById('dashboard-filter-form');
    let filterInput = form.querySelector('input[name="delivery_filter"]');
    if (filterInput) filterInput.value = filter;
    form.submit();
}

// Animação de contagem para números
function animateCountUp(element) {
    const target = parseInt(element.getAttribute('data-target')) || 0;
    const duration = 1000;
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Touch support para carrossel no mobile
let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('carousel-track');
    
    if (track) {
        track.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        track.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
    }
    
    // Animar números
    document.querySelectorAll('[data-target]').forEach(el => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCountUp(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        observer.observe(el);
    });
    
    // Inicializar widgets
    if (window.dashboardWidgets) window.dashboardWidgets.init();
});

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            nextSlide();
        } else {
            previousSlide();
        }
    }
}
</script>
@endsection
