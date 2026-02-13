@extends('layouts.admin')

@section('content')
@php
    $today = \Carbon\Carbon::today();
    $ordersCollection = $orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->getCollection() : collect($orders);
    $dueTodayCount = $ordersCollection->filter(function ($order) use ($today) {
        return $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isSameDay($today);
    })->count();
    $lateOrdersCount = $ordersCollection->filter(function ($order) use ($today) {
        return $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isBefore($today);
    })->count();
    $upcomingOrders = $ordersCollection->filter(function ($order) {
        return !empty($order->delivery_date);
    })->sortBy(function ($order) {
        return \Carbon\Carbon::parse($order->delivery_date)->timestamp;
    })->take(5);
    $periodLabels = [
        'all' => 'Todo o periodo',
        'day' => 'Hoje',
        'week' => 'Semana util',
        'month' => 'Este mes',
        'custom' => 'Personalizado'
    ];
    $periodLabel = $periodLabels[$period] ?? 'Periodo';
    $periodRange = null;
    if (!empty($startDate) && !empty($endDate)) {
        $periodRange = \Carbon\Carbon::parse($startDate)->format('d/m') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m');
    } elseif (!empty($startDate)) {
        $periodRange = \Carbon\Carbon::parse($startDate)->format('d/m/Y');
    }
    $personalizationSummary = $ordersByPersonalization->map(function ($group) {
        return $group->count();
    })->sortDesc();
    $ordersWithoutDate = \App\Models\Order::where('is_draft', false)
        ->where('is_cancelled', false)
        ->whereNull('delivery_date')
        ->count();
@endphp

<div class="max-w-[1800px] mx-auto pb-24 space-y-8">
    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Hero / Header -->
    <section class="relative overflow-hidden rounded-3xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-[#0b0f17] shadow-lg">
        <div class="absolute -top-24 -right-24 h-64 w-64 bg-gradient-to-br from-indigo-500/20 via-purple-500/10 to-pink-500/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 h-64 w-64 bg-gradient-to-tr from-emerald-400/20 via-cyan-400/10 to-blue-500/20 blur-3xl"></div>
        <div class="relative z-10 p-6 md:p-8 flex flex-col lg:flex-row gap-6 lg:items-center lg:justify-between">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        Producao
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300 border border-gray-200 dark:border-white/10">
                        {{ $periodLabel }}
                        @if($periodRange)
                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ $periodRange }}</span>
                        @endif
                    </span>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-gray-900 dark:text-gray-100">Painel de Producao</h1>
                    <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 mt-2 max-w-2xl">
                        Visao rapida do pipeline, prioridades do dia e acompanhamento dos pedidos em producao.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('orders.wizard.start') }}" 
                       class="inline-flex items-center px-6 py-3 bg-[#7c3aed] text-white rounded-xl font-bold hover:bg-[#6d28d9] transition shadow-lg shadow-purple-500/20">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Novo Pedido
                    </a>
                    <a href="{{ route('production.kanban') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-200 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-white/10 transition border border-gray-200 dark:border-white/10">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                        Ver em Kanban
                    </a>
                    <a href="{{ route('production.pdf') }}?{{ http_build_query(request()->except('page')) }}" 
                       target="_blank"
                       class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/20">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        PDF do Periodo
                    </a>
                </div>
            </div>
            <div class="w-full lg:w-80">
                <div class="bg-gradient-to-br from-gray-50 to-white dark:from-[#0f172a] dark:to-[#0b0f17] border border-gray-200 dark:border-white/10 rounded-2xl p-5 shadow-inner">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Hoje</h3>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Entregas hoje</span>
                            <span class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ $dueTodayCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Atrasados</span>
                            <span class="text-lg font-black text-rose-600">{{ $lateOrdersCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Sem data</span>
                            <span class="text-lg font-black text-amber-600">{{ $ordersWithoutDate ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- KPIs -->
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Pedidos ativos</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">{{ $totalOrders }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Valor em producao</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">R$ {{ number_format($totalValue, 2, ',', '.') }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Status ativos</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">{{ $ordersByStatus->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Personalizacoes</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">{{ $ordersByPersonalization->count() }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                </div>
            </div>
        </div>
    </section>
    <!-- Pipeline + Personalizacao -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Pipeline por Status</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Visao rapida do fluxo atual</p>
                </div>
            </div>
            <div class="mt-5 flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                @foreach($statuses as $statusItem)
                    @php
                        $count = ($ordersByStatus[$statusItem->id] ?? collect())->count();
                    @endphp
                    <div class="min-w-[180px] flex-shrink-0 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/40">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex w-2.5 h-2.5 rounded-full" style="background-color: {{ $statusItem->color ?? '#9ca3af' }}"></span>
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">{{ $count }}</span>
                        </div>
                        <div class="mt-3 text-sm font-semibold text-gray-900 dark:text-gray-100 truncate" title="{{ $statusItem->name }}">
                            {{ $statusItem->name }}
                        </div>
                        <div class="mt-2 h-1.5 rounded-full bg-gray-200 dark:bg-gray-800">
                            @php
                                $progress = $totalOrders > 0 ? min(100, ($count / $totalOrders) * 100) : 0;
                            @endphp
                            <div class="h-1.5 rounded-full" style="background-color: {{ $statusItem->color ?? '#9ca3af' }}; width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Tipos de Personalizacao</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Distribuicao no periodo</p>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @forelse($personalizationSummary as $type => $count)
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $type }}</div>
                            <div class="mt-2 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700">
                                @php
                                    $p = $personalizationSummary->first() ? ($count / $personalizationSummary->first()) * 100 : 0;
                                @endphp
                                <div class="h-1.5 rounded-full bg-indigo-500" style="width: {{ $p }}%"></div>
                            </div>
                        </div>
                        <div class="text-sm font-black text-gray-700 dark:text-gray-200">{{ $count }}</div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 dark:text-gray-400">Nenhuma personalizacao no periodo.</div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Proximas entregas + alertas -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Proximas entregas</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pedidos com data definida</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Pedido</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($upcomingOrders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 text-sm font-semibold text-indigo-600 dark:text-indigo-400">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $order->client->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" style="background-color: {{ $order->status->color ?? '#6B7280' }}">
                                        {{ $order->status->name ?? 'Indefinido' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Nenhuma entrega com data definida no periodo atual.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm space-y-4">
            <div>
                <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Alertas rapidos</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Foco nas pendencias</p>
            </div>
            <div class="space-y-3">
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-600/30 dark:text-amber-300">
                    <p class="text-xs font-bold uppercase tracking-widest">Sem data</p>
                    <p class="text-sm mt-1">{{ $ordersWithoutDate ?? 0 }} pedido(s) sem data de entrega.</p>
                </div>
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-900/20 dark:border-rose-600/30 dark:text-rose-300">
                    <p class="text-xs font-bold uppercase tracking-widest">Atrasados</p>
                    <p class="text-sm mt-1">{{ $lateOrdersCount }} pedido(s) com entrega atrasada.</p>
                </div>
                <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-800 dark:bg-indigo-900/20 dark:border-indigo-600/30 dark:text-indigo-300">
                    <p class="text-xs font-bold uppercase tracking-widest">Entregas hoje</p>
                    <p class="text-sm mt-1">{{ $dueTodayCount }} pedido(s) para entrega hoje.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Filtros -->
    <section class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <details class="group" {{ ($search || $status || $personalizationType || $storeId || $period === 'custom') ? 'open' : '' }}>
            <summary class="flex items-center justify-between cursor-pointer">
                <div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-gray-100">Filtros avancados</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Refine a lista por periodo, status ou loja</p>
                </div>
                <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                    <span class="text-xs font-semibold">Expandir</span>
                    <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </summary>
            <div class="mt-6">
                <form method="GET" action="{{ route('production.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Periodo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Periodo</label>
                            <select name="period" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Todo o Periodo</option>
                                <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Hoje</option>
                                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Esta Semana (Seg-Sex)</option>
                                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Este Mes</option>
                                <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personalizado</option>
                            </select>
                        </div>

                        <!-- Data Inicio -->
                        <div id="start-date-field" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Inicio</label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ $startDate }}"
                                   class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                        </div>

                        <!-- Data Fim -->
                        <div id="end-date-field" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Fim</label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ $endDate }}"
                                   class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                                <option value="">Todos os Status</option>
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption->id }}" {{ $status == $statusOption->id ? 'selected' : '' }}>
                                        {{ $statusOption->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Personalizacao -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Personalizacao</label>
                            <select name="personalization_type" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                                <option value="">Todos os Tipos</option>
                                @foreach($personalizationTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $personalizationType == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Loja -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja</label>
                            <select name="store_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                                <option value="">Todas as Lojas</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Busca -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}"
                                   placeholder="Numero do pedido, nome do cliente, telefone ou nome da arte..."
                                   class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition">
                            Filtrar
                        </button>
                        <a href="{{ route('production.index') }}" 
                           class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </details>
    </section>
    <!-- Lista de Pedidos -->
    <section class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-black text-gray-900 dark:text-gray-100">Pedidos Encontrados</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Mostrando {{ $orders->count() }} de {{ $totalOrders }} pedidos
                @if($period === 'day')
                    ativos em producao
                @elseif($period === 'week')
                    com entrega esta semana ({{ \Carbon\Carbon::parse($startDate)->format('d/m') }} a {{ \Carbon\Carbon::parse($endDate)->format('d/m') }})
                @elseif($period === 'month')
                    com entrega este mes
                @elseif($period === 'all')
                    em qualquer periodo
                @else
                    com entrega no periodo selecionado
                @endif
            </p>
        </div>

        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Loja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vendedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Personalizacao</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Pedido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($orders as $order)
                            @php
                                $firstItem = $order->items->first();
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                        #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->client->name ?? 'Cliente nao encontrado' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->client->phone_primary ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        @if($order->store)
                                            {{ $order->store->name }}
                                        @elseif($order->store_id)
                                            {{ \App\Models\Store::find($order->store_id)?->name ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $order->seller ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
                                          style="background-color: {{ $order->status->color ?? '#6B7280' }}">
                                        {{ $order->status->name ?? 'Indefinido' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $firstItem->print_type ?? '-' }}</div>
                                    @if($firstItem && $firstItem->art_name)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $firstItem->art_name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    R$ {{ number_format($order->total, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('orders.show', $order->id) }}" 
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            Ver Detalhes
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum pedido encontrado</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($search || $status || $personalizationType)
                        Tente ajustar os filtros para encontrar pedidos.
                    @else
                        Nao ha pedidos para o periodo selecionado.
                    @endif
                </p>
            </div>
        @endif
    </section>
</div>

<script>
    document.querySelector('select[name="period"]').addEventListener('change', function() {
        const startDateField = document.getElementById('start-date-field');
        const endDateField = document.getElementById('end-date-field');
        const startDateInput = startDateField ? startDateField.querySelector('input[name="start_date"]') : null;
        const endDateInput = endDateField ? endDateField.querySelector('input[name="end_date"]') : null;
        
        if (this.value === 'custom') {
            if (startDateField) startDateField.style.display = 'block';
            if (endDateField) endDateField.style.display = 'block';
        } else {
            if (startDateField) startDateField.style.display = 'none';
            if (endDateField) endDateField.style.display = 'none';
            if (startDateInput) startDateInput.value = '';
            if (endDateInput) endDateInput.value = '';
        }
    });
</script>
@endsection
