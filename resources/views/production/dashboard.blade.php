@extends('layouts.admin')

@section('content')
@php
    $selectedCount = is_array($selectedColumns) ? count($selectedColumns) : 0;
    $totalStatuses = $allStatuses ? $allStatuses->count() : 0;

    $periodLabels = [
        'week' => 'Semana',
        'month' => 'Mes',
        'quarter' => 'Trimestre',
        'year' => 'Ano',
        'custom' => 'Personalizado',
    ];
    $periodLabel = $periodLabels[$period] ?? 'Mes';

    $startLabel = $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '';
    $endLabel = $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '';

    $formattedAvg = null;
    if ($avgProductionTime) {
        $days = floor($avgProductionTime / 86400);
        $hours = floor(($avgProductionTime % 86400) / 3600);
        $minutes = floor(($avgProductionTime % 3600) / 60);
        $formattedAvg = '';
        if ($days > 0) $formattedAvg .= $days . 'd ';
        if ($hours > 0) $formattedAvg .= $hours . 'h ';
        $formattedAvg .= $minutes . 'm';
    }

    $maxOrders = 0;
    foreach ($statuses as $status) {
        $count = $ordersByStatus[$status->id] ?? 0;
        if ($count > $maxOrders) {
            $maxOrders = $count;
        }
    }
@endphp

<div class="space-y-6 prod-dashboard">
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 p-6">
        <div class="absolute -top-20 -right-16 h-40 w-40 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute -bottom-24 left-10 h-48 w-48 rounded-full bg-purple-500/20 blur-3xl"></div>
        <div class="relative flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                    <span class="h-2 w-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
                    Producao ativa
                </div>
                <h1 class="mt-3 text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">Dashboard de Producao</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 max-w-2xl">
                    Visao geral do fluxo de pedidos, prazos e gargalos do periodo selecionado.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <div class="flex items-center gap-3 rounded-2xl bg-gray-50 dark:bg-gray-700/40 px-4 py-3 border border-gray-100 dark:border-gray-700/60">
                        <div class="h-10 w-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-white stay-white flex items-center justify-center prod-kpi-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total de pedidos</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $totalOrders }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl bg-gray-50 dark:bg-gray-700/40 px-4 py-3 border border-gray-100 dark:border-gray-700/60">
                        <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-white stay-white flex items-center justify-center prod-kpi-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v16H4z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Em producao</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ordersInProduction }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl bg-gray-50 dark:bg-gray-700/40 px-4 py-3 border border-gray-100 dark:border-gray-700/60">
                        <div class="h-10 w-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-white stay-white flex items-center justify-center prod-kpi-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Tempo medio total</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $formattedAvg ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:w-80">
                <div class="bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 space-y-3">
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Periodo ativo</div>
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $periodLabel }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">De {{ $startLabel }} ate {{ $endLabel }}</div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Colunas ativas</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $selectedCount }}/{{ $totalStatuses }}</span>
                    </div>
                    @if($slowestStatus)
                    <div class="rounded-xl bg-white/80 dark:bg-gray-800/70 p-3 border border-gray-100 dark:border-gray-700/60">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Setor mais lento</div>
                        <div class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $slowestStatus['status_name'] ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $slowestStatus['avg_formatted'] ?? 'N/A' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6">
        <form method="GET" action="{{ route('production.dashboard') }}" id="dashboard-filter-form" class="space-y-6">
            <input type="hidden" name="filter_submitted" value="1">
            <div class="grid gap-4 lg:grid-cols-12 items-end">
                <div class="lg:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Periodo</label>
                    <select name="period" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Semana</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Mes</option>
                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Trimestre</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Ano</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>
                <div id="custom-range-fields" class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4 {{ $period == 'custom' ? '' : 'hidden' }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    </div>
                </div>
                <div class="lg:col-span-3 flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white stay-white rounded-xl hover:bg-indigo-700 transition font-semibold">
                        Filtrar
                    </button>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">Colunas do Kanban</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Escolha as etapas que deseja acompanhar.</div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="selectAllColumns()" class="text-xs px-2 py-1 text-indigo-600 dark:text-indigo-400 hover:underline">
                            Selecionar todas
                        </button>
                        <button type="button" onclick="deselectAllColumns()" class="text-xs px-2 py-1 text-gray-600 dark:text-gray-400 hover:underline">
                            Desmarcar todas
                        </button>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    @foreach($allStatuses as $status)
                    <label class="group relative flex items-center gap-2 rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-700/40 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-200 cursor-pointer transition hover:shadow prod-column-chip">
                        <input type="checkbox"
                               name="columns[]"
                               value="{{ $status->id }}"
                               {{ in_array($status->id, $selectedColumns) ? 'checked' : '' }}
                               class="sr-only peer column-checkbox">
                        <span class="h-2.5 w-2.5 rounded-full relative z-10 prod-chip-dot" style="background: {{ $status->color ?? '#94a3b8' }}"></span>
                        <span class="truncate relative z-10 prod-column-label">{{ $status->name }}</span>
                        <span class="pointer-events-none absolute inset-0 rounded-2xl border border-transparent z-0 prod-chip-bg"></span>
                    </label>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total de pedidos</div>
                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalOrders }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-indigo-100 dark:bg-indigo-500/20 text-white stay-white flex items-center justify-center prod-kpi-icon prod-kpi-icon-strong">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3h14v4H5zM5 9h14v12H5z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Pedidos criados no periodo</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Em producao</div>
                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $ordersInProduction }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-100 dark:bg-blue-500/20 text-white stay-white flex items-center justify-center prod-kpi-icon prod-kpi-icon-strong">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Pedidos ativos no fluxo</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tempo medio total</div>
                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $formattedAvg ?? 'N/A' }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 text-white stay-white flex items-center justify-center prod-kpi-icon prod-kpi-icon-strong">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Media considerando pedidos ativos</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Setor mais lento</div>
                    <div class="text-lg font-semibold text-red-600 dark:text-red-400">{{ $slowestStatus['status_name'] ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $slowestStatus['avg_formatted'] ?? 'Sem dados' }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-red-100 dark:bg-red-500/20 text-white stay-white flex items-center justify-center prod-kpi-icon prod-kpi-icon-strong">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l-2 2m8-6a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Maior tempo medio entre etapas</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Pedidos por data de entrega</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Priorize o que vence primeiro e evite atrasos.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="changeDeliveryFilter('today')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ $deliveryFilter == 'today' ? 'bg-indigo-600 text-white stay-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Hoje
                </button>
                <button onclick="changeDeliveryFilter('week')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ $deliveryFilter == 'week' ? 'bg-indigo-600 text-white stay-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Esta semana
                </button>
                <button onclick="changeDeliveryFilter('month')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ $deliveryFilter == 'month' ? 'bg-indigo-600 text-white stay-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Este mes
                </button>
            </div>
        </div>

        @if(isset($deliveryOrders) && $deliveryOrders && $deliveryOrders->count() > 0)
        <div class="relative">
            <div id="delivery-carousel" class="overflow-hidden">
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-thin" id="carousel-track">
                    @foreach($deliveryOrders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $coverImage = $order->cover_image_url ?? $firstItem?->cover_image_url;
                        $artName = $firstItem?->art_name;
                        $displayName = $artName ?? ($order->client?->name ?? 'Sem cliente');
                        $storeName = $order->store?->name ?? 'Loja Principal';
                    @endphp
                    <div class="carousel-slide flex-shrink-0" style="min-width: 320px; max-width: 320px;">
                        <div class="kanban-card bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow dark:shadow-gray-900/25 rounded-2xl overflow-hidden cursor-pointer hover:shadow-xl dark:hover:shadow-gray-900/50 transition-all duration-200 border"
                             onclick="window.location.href='{{ route('orders.show', $order->id) }}'">
                            @if($coverImage)
                            <div class="h-44 bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <img src="{{ $coverImage }}"
                                     alt="Capa do Pedido"
                                     class="w-full h-44 object-cover"
                                     style="object-fit: cover; object-position: center;"
                                     onerror="this.parentElement.innerHTML='<div class=\'h-44 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>'">
                            </div>
                            @else
                            <div class="h-44 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            @endif

                            <div class="p-4">
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('orders.show', $order->id) }}"
                                               class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/50 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                            </a>
                                            @if($order->is_event)
                                            <span class="text-xs font-medium bg-red-500 dark:bg-red-600 text-white stay-white px-2 py-1 rounded-full">
                                                EVENTO
                                            </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $order->items->sum('quantity') }} pcs
                                        </span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm truncate mb-1" title="{{ $displayName }}">
                                        {{ $displayName }}
                                    </h3>
                                    @if($storeName)
                                    <div class="flex items-center text-xs text-indigo-700 dark:text-indigo-400">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 6l9 4 9-4m-9 4v6" />
                                        </svg>
                                        <span class="truncate" title="{{ $storeName }}">
                                            <strong>Loja:</strong> {{ $storeName }}
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                @if($order->delivery_date)
                                <div class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Data de entrega</div>
                                        <div class="text-xs font-semibold text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    @php
                                        $deliveryDate = \Carbon\Carbon::parse($order->delivery_date)->startOfDay();
                                        $today = \Carbon\Carbon::now()->startOfDay();
                                        $daysUntilDelivery = (int) $today->diffInDays($deliveryDate, false);
                                    @endphp
                                    <div class="mt-1 text-xs font-medium {{ $daysUntilDelivery < 0 ? 'text-red-600 dark:text-red-400' : ($daysUntilDelivery == 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400') }}">
                                        @if($daysUntilDelivery < 0)
                                            Atrasado {{ abs($daysUntilDelivery) }} dia(s)
                                        @elseif($daysUntilDelivery == 0)
                                            Entrega hoje!
                                        @else
                                            Em {{ $daysUntilDelivery }} dia(s)
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">Itens:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $order->items->sum('quantity') }} pcs</span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium" style="background: {{ $order->status->color ?? '#6B7280' }}20; color: {{ $order->status->color ?? '#6B7280' }}">
                                            {{ $order->status->name ?? 'Sem status' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-center items-center mt-4 gap-2">
                <button onclick="previousSlide()"
                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button onclick="nextSlide()"
                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
        @else
        <div class="text-center py-10 text-gray-500 dark:text-gray-400">
            Nenhum pedido encontrado para o periodo selecionado
        </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-5 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mapa do fluxo</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">Status ativos</span>
            </div>
            <div class="space-y-3">
                @forelse($statuses as $status)
                @php
                    $count = $ordersByStatus[$status->id] ?? 0;
                    $percentage = $maxOrders > 0 ? round(($count / $maxOrders) * 100) : 0;
                    $barColor = $status->color ?? '#7c3aed';
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-24 text-xs text-gray-600 dark:text-gray-400 truncate" title="{{ $status->name }}">{{ $status->name }}</div>
                    <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-700/60 overflow-hidden">
                        <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background: {{ $barColor }}"></div>
                    </div>
                    <div class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ $count }}</div>
                </div>
                @empty
                <div class="text-sm text-gray-500 dark:text-gray-400">Nenhum status selecionado.</div>
                @endforelse
            </div>
        </div>
        <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tempo medio por setor</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Setor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tempo medio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tempo minimo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tempo maximo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedidos</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($statuses as $status)
                        @php
                            $stat = collect($statusStats)->firstWhere('status_id', $status->id);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $status->name }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $stat['avg_formatted'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $stat['min_formatted'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $stat['max_formatted'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $ordersByStatus[$status->id] ?? 0 }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/60 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Pedidos por status</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($statuses as $status)
            @php
                $count = $ordersByStatus[$status->id] ?? 0;
                $percentage = $maxOrders > 0 ? round(($count / $maxOrders) * 100) : 0;
                $barColor = $status->color ?? '#7c3aed';
            @endphp
            <div class="rounded-2xl border border-gray-100 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-700/40 p-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $status->name }}</div>
                <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $count }}
                </div>
                <div class="mt-2 h-1.5 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                    <div class="h-1.5 rounded-full" style="width: {{ $percentage }}%; background: {{ $barColor }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    :root:not(.dark) .prod-dashboard .prod-kpi-icon,
    :root:not(.dark) .prod-dashboard .prod-kpi-icon svg {
        color: #ffffff !important;
    }
    :root:not(.dark) .prod-dashboard .prod-kpi-icon-strong {
        background-color: #7c3aed !important;
    }
    :root:not(.dark) .prod-dashboard .prod-kpi-icon-strong.bg-blue-100 {
        background-color: #3b82f6 !important;
    }
    :root:not(.dark) .prod-dashboard .prod-kpi-icon-strong.bg-emerald-100 {
        background-color: #10b981 !important;
    }
    :root:not(.dark) .prod-dashboard .prod-kpi-icon-strong.bg-red-100 {
        background-color: #ef4444 !important;
    }
    :root:not(.dark) .prod-dashboard .bg-indigo-600,
    :root:not(.dark) .prod-dashboard .bg-indigo-600 *,
    :root:not(.dark) .prod-dashboard .bg-red-500,
    :root:not(.dark) .prod-dashboard .bg-red-500 * {
        color: #ffffff !important;
    }
    :root:not(.dark) .prod-dashboard .prod-column-chip {
        background-color: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
    }
    :root:not(.dark) .prod-dashboard .prod-column-chip.is-selected {
        background-color: #7c3aed !important;
        border-color: #7c3aed !important;
    }
    :root:not(.dark) .prod-dashboard .prod-column-chip.is-selected .prod-column-label {
        color: #ffffff !important;
        font-weight: 600;
    }
    :root:not(.dark) .prod-dashboard .prod-column-chip.is-selected .prod-column-label,
    :root:not(.dark) .prod-dashboard .prod-column-chip.is-selected .prod-column-label * {
        color: #ffffff !important;
    }
</style>

<script>
function selectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(cb => {
        cb.checked = true;
    });
    syncColumnChips();
    const form = document.getElementById('dashboard-filter-form');
    if (form) {
        setTimeout(() => form.submit(), 50);
    }
}

function deselectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(cb => {
        cb.checked = false;
    });
    syncColumnChips();
    const form = document.getElementById('dashboard-filter-form');
    if (form) {
        setTimeout(() => form.submit(), 50);
    }
}

function syncColumnChips() {
    document.querySelectorAll('.prod-column-chip').forEach(chip => {
        const checkbox = chip.querySelector('.column-checkbox');
        if (checkbox && checkbox.checked) {
            chip.classList.add('is-selected');
        } else {
            chip.classList.remove('is-selected');
        }
    });
}

document.querySelectorAll('.column-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        syncColumnChips();
        setTimeout(() => {
            document.getElementById('dashboard-filter-form').submit();
        }, 300);
    });
});

const periodSelect = document.querySelector('select[name="period"]');
const customRange = document.getElementById('custom-range-fields');

function syncCustomRange() {
    if (!periodSelect || !customRange) return;
    const isCustom = periodSelect.value === 'custom';
    customRange.classList.toggle('hidden', !isCustom);
    customRange.querySelectorAll('input').forEach(input => {
        input.disabled = !isCustom;
    });
}

let totalSlides = {{ isset($deliveryOrders) ? $deliveryOrders->count() : 0 }};
let cardWidth = 336;
let autoSlideInterval = null;

function updateSlidesPerView() {
    const container = document.getElementById('carousel-track');
    if (!container) return;
    if (window.innerWidth >= 1024) {
        cardWidth = 336;
    } else if (window.innerWidth >= 768) {
        cardWidth = 336;
    } else {
        cardWidth = 336;
    }
}

function nextSlide() {
    const track = document.getElementById('carousel-track');
    if (!track) return;

    updateSlidesPerView();
    const maxScroll = track.scrollWidth - track.offsetWidth;

    if (track.scrollLeft < maxScroll) {
        track.scrollBy({ left: cardWidth, behavior: 'smooth' });
    } else {
        track.scrollTo({ left: 0, behavior: 'smooth' });
    }
    resetAutoSlide();
}

function previousSlide() {
    const track = document.getElementById('carousel-track');
    if (!track) return;

    updateSlidesPerView();

    if (track.scrollLeft > 0) {
        track.scrollBy({ left: -cardWidth, behavior: 'smooth' });
    } else {
        track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
    }
    resetAutoSlide();
}

function startAutoSlide() {
    if (totalSlides <= 3) return;
    autoSlideInterval = setInterval(() => {
        nextSlide();
    }, 5000);
}

function resetAutoSlide() {
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }
    startAutoSlide();
}

window.changeDeliveryFilter = function(filter) {
    const form = document.getElementById('dashboard-filter-form');
    if (!form) return;

    let filterInput = form.querySelector('input[name="delivery_filter"]');
    if (!filterInput) {
        filterInput = document.createElement('input');
        filterInput.type = 'hidden';
        filterInput.name = 'delivery_filter';
        form.appendChild(filterInput);
    }
    filterInput.value = filter;

    form.submit();
};

document.addEventListener('DOMContentLoaded', function() {
    syncColumnChips();
    syncCustomRange();
    if (periodSelect) {
        periodSelect.addEventListener('change', syncCustomRange);
    }

    updateSlidesPerView();
    startAutoSlide();

    window.addEventListener('resize', updateSlidesPerView);

    const carousel = document.getElementById('delivery-carousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        });
        carousel.addEventListener('mouseleave', () => {
            startAutoSlide();
        });
    }
});
</script>
@endsection
