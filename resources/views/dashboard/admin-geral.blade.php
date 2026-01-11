@extends('layouts.admin')

@section('content')
<div class="mb-6 space-y-4">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                @if(Auth::user()->tenant)
                    {{ Auth::user()->tenant->name }}
                @else
                    Painel Administrativo
                @endif
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Resumo de desempenho e atividades principais.</p>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filtro de Período -->
            <form method="GET" action="{{ route('dashboard') }}" id="periodFilterForm" class="flex items-center gap-2">
                @if(isset($selectedStoreId))
                    <input type="hidden" name="store_id" value="{{ $selectedStoreId }}">
                @endif
                <select name="period" id="period" 
                        onchange="this.form.submit()"
                        class="px-4 py-2 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition-all">
                    <option value="today" {{ ($period ?? 'month') === 'today' ? 'selected' : '' }}>Hoje</option>
                    <option value="week" {{ ($period ?? 'month') === 'week' ? 'selected' : '' }}>Esta Semana</option>
                    <option value="month" {{ ($period ?? 'month') === 'month' ? 'selected' : '' }}>Este Mês</option>
                    <option value="year" {{ ($period ?? 'month') === 'year' ? 'selected' : '' }}>Este Ano</option>
                    <option value="custom" {{ ($period ?? 'month') === 'custom' ? 'selected' : '' }}>Personalizado</option>
                </select>
                
                @if(($period ?? 'month') === 'custom')
                <div class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') ?? '' }}" 
                           class="px-3 py-2 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') ?? '' }}" 
                           class="px-3 py-2 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                @endif
            </form>
            
            <!-- Filtro de Loja -->
            @if(isset($stores) && $stores->count() > 1)
            <form method="GET" action="{{ route('dashboard') }}" id="storeFilterForm" class="flex items-center gap-2 border-l border-gray-200 dark:border-gray-700 pl-3">
                @if(isset($period))
                    <input type="hidden" name="period" value="{{ $period }}">
                @endif
                <select name="store_id" id="store_filter" 
                        onchange="this.form.submit()"
                        class="px-4 py-2 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition-all">
                    <option value="">Todas as Unidades</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ (isset($selectedStoreId) && $selectedStoreId == $store->id) ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            @endif
            
            <!-- Botão de Reset Layout -->
            <button onclick="window.dashboardWidgets?.resetLayout()" 
                    class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all flex items-center gap-1"
                    title="Resetar layout dos widgets">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="hidden sm:inline">Resetar Layout</span>
            </button>
        </div>
    </div>
</div>

{{-- Checklist de Configuração Inicial (Apenas para novos usuários ou configuração pendente) --}}
@php
    $setupSteps = [
        ['title' => 'Personalizar Marca', 'done' => auth()->user()->tenant->logo_path ? true : false, 'url' => route('settings.branding.edit'), 'icon' => 'palette'],
        ['title' => 'Cadastrar Primeira Unidade', 'done' => \App\Models\Store::count() > 0, 'url' => route('settings.company'), 'icon' => 'store'],
        ['title' => 'Criar Primeiro Pedido', 'done' => $totalPedidos > 0, 'url' => route('orders.index'), 'icon' => 'plus-circle'],
    ];
    $completedSteps = collect($setupSteps)->where('done', true)->count();
    $totalSteps = count($setupSteps);
    $setupFinished = $completedSteps === $totalSteps;
@endphp

@if(!$setupFinished)
<div class="mb-8 bg-gradient-to-r from-indigo-600 to-violet-700 rounded-2xl p-6 text-white shadow-xl shadow-indigo-200 dark:shadow-none relative overflow-hidden" id="setup-checklist">
    <!-- Decoração de fundo -->
    <div class="absolute top-0 right-0 -transtale-y-1/2 translate-x-1/4 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
    
    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex-1">
            <h2 class="text-xl font-bold mb-2">Bem-vindo ao Vestalize, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h2>
            <p class="text-indigo-100 mb-4 text-sm max-w-xl">Vamos deixar tudo pronto para você começar. Complete os passos abaixo para configurar sua conta.</p>
            
            <!-- Barra de Progresso -->
            <div class="flex items-center gap-4 mb-2">
                <div class="flex-1 h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white transition-all duration-500" style="width: {{ ($completedSteps / $totalSteps) * 100 }}%"></div>
                </div>
                <span class="text-xs font-bold">{{ $completedSteps }}/{{ $totalSteps }} Concluídos</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach($setupSteps as $step)
            <a href="{{ $step['url'] }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition group {{ $step['done'] ? 'opacity-60' : '' }}">
                <div class="h-8 w-8 rounded-lg flex items-center justify-center {{ $step['done'] ? 'bg-green-400 text-white' : 'bg-white/20 text-white' }}">
                    @if($step['done'])
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <i class="fa-solid fa-{{ $step['icon'] }} text-sm"></i>
                    @endif
                </div>
                <span class="text-sm font-medium {{ $step['done'] ? 'line-through decoration-white/50' : '' }}">{{ $step['title'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Grid de KPIs Principais -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 widget-container" data-dashboard-widgets id="kpi-grid">
    <!-- Total de Pedidos -->
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pedidos Totais</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalPedidos, 0, '.', '.') }}</h3>
                @if(isset($variacaoPedidos))
                <div class="flex items-center mt-2">
                    <span class="flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $variacaoPedidos >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $variacaoPedidos >= 0 ? '+' : '' }}{{ number_format($variacaoPedidos, 1) }}%
                    </span>
                    <span class="ml-2 text-xs text-gray-500 text-nowrap">vs período ant.</span>
                </div>
                @endif
            </div>
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
    </div>

    <!-- Faturamento Total -->
    @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Faturamento</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($totalFaturamento, 2, ',', '.') }}</h3>
                @if(isset($variacaoFaturamento))
                <div class="flex items-center mt-2">
                    <span class="flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $variacaoFaturamento >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $variacaoFaturamento >= 0 ? '+' : '' }}{{ number_format($variacaoFaturamento, 1) }}%
                    </span>
                    <span class="ml-2 text-xs text-gray-500 text-nowrap">vs período ant.</span>
                </div>
                @endif
            </div>
            <div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    @else
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Faturamento</p>
                <h3 class="mt-2 text-sm font-bold text-gray-400 italic">Disponível no Plano Pro</h3>
                <p class="text-xs text-gray-500 mt-2">Atualize para ver faturamento detalhado.</p>
            </div>
            <div class="p-3 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
        </div>
    </div>
    @endif

    <!-- Ticket Médio -->
    @if(!Auth::user()->tenant || Auth::user()->tenant->canAccess('financial'))
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket Médio</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($ticketMedio ?? 0, 2, ',', '.') }}</h3>
                @if(isset($variacaoTicketMedio))
                <div class="flex items-center mt-2">
                    <span class="flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $variacaoTicketMedio >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $variacaoTicketMedio >= 0 ? '+' : '' }}{{ number_format($variacaoTicketMedio, 1) }}%
                    </span>
                    <span class="ml-2 text-xs text-gray-500 text-nowrap">vs ant.</span>
                </div>
                @endif
            </div>
            <div class="p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
        </div>
    </div>
    @else
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket Médio</p>
                <h3 class="mt-2 text-sm font-bold text-gray-400 italic">Disponível no Plano Pro</h3>
                <p class="text-xs text-gray-500 mt-2">Atualize para ver métricas.</p>
            </div>
            <div class="p-3 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
        </div>
    </div>
    @endif

    <!-- Clientes Novos -->
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clientes</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalClientes, 0, '.', '.') }}</h3>
                <p class="text-xs text-gray-500 mt-2">Base total ativa</p>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Grid Adicional / Canais de Venda -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('pdv'))
    <!-- Vendas PDV (Só se tiver acesso) -->
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-dashed border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">PDV Presencial</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $vendasPDV ?? 0 }} vendas</p>
                <p class="text-sm text-green-600 font-medium">R$ {{ number_format($vendasPDVValor ?? 0, 2, ',', '.') }}</p>
            </div>
            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
    </div>
    @endif

    <!-- Pedidos Online -->
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-dashed border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Pedidos Online</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $pedidosOnline ?? 0 }} pedidos</p>
                <p class="text-sm text-indigo-600 font-medium">R$ {{ number_format($pedidosOnlineValor ?? 0, 2, ',', '.') }}</p>
            </div>
            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
        </div>
    </div>

    <!-- Pedidos Hoje -->
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-dashed border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Atividade Hoje</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $pedidosHoje }} novos</p>
                <p class="text-sm text-gray-500 font-medium">Capturados hoje</p>
            </div>
            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
    <!-- Inadimplência / Pendentes (Só se tiver financeiro) -->
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-dashed border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Recebimentos</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $pagamentosPendentes->count() }} pendentes</p>
                <p class="text-sm text-red-500 font-medium">R$ {{ number_format($totalPendente, 2, ',', '.') }}</p>
            </div>
            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Seção de Gráficos de Desempenho -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 widget-container">
    <!-- Pedidos por Status -->
    <div id="widget-fluxo-pedidos" class="dashboard-widget bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 {{ (Auth::user()->tenant && !Auth::user()->tenant->canAccess('reports_simple')) ? 'opacity-50 grayscale pointer-events-none relative overflow-hidden' : '' }}">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Fluxo de Pedidos</h2>
            <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg">Por Status</span>
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="statusChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('reports_simple'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-gray-800/60 backdrop-blur-[2px] z-10 p-4 text-center">
                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Relatórios Bloqueados</p>
                <p class="text-xs text-gray-500">Upgrade para ver gráficos</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Faturamento Temporal -->
    <div id="widget-tendencia-receita" class="dashboard-widget bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 {{ (Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial')) ? 'opacity-50 grayscale pointer-events-none relative overflow-hidden' : '' }}">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Tendência de Receita</h2>
            @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                <span class="text-xs text-gray-500">Valor (R$)</span>
            </div>
            @endif
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-gray-800/60 backdrop-blur-[2px] z-10 p-4 text-center">
                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Upgrade para Plano Pro</p>
                <p class="text-xs text-gray-500">Visualize métricas financeiras</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8 widget-container">
    <!-- Faturamento por Loja (Se múltiplo) -->
    @if(isset($faturamentoPorLoja) && $faturamentoPorLoja->count() > 1 && Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Performance por Unidade</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoLojaChart"></canvas>
        </div>
    </div>
    @elseif(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
    <!-- Faturamento Mensal (Destaque) -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Evolução Mensal</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoMensalChart"></canvas>
        </div>
    </div>
    @else
    <!-- Fallback if no financial: Show status or something else -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 opacity-60">
        <h2 class="text-lg font-bold text-gray-400 mb-6">Métricas de Vendas Detalhadas</h2>
        <div class="flex flex-col items-center justify-center h-[300px] text-center">
             <p class="text-sm text-gray-500">Upgrade para Plano Pro para desbloquear</p>
        </div>
    </div>
    @endif

    <!-- Formas de Pagamento -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 {{ (Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial')) ? 'opacity-50 grayscale pointer-events-none relative overflow-hidden' : '' }}">
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Meios de Pagamento</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="pagamentoChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-gray-800/60 backdrop-blur-[2px] z-10 p-4 text-center">
                <p class="text-xs font-bold text-gray-500">Bloqueado no Plano Atual</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Tabelas -->
<!-- Seção de Tabelas de Detalhamento -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 widget-container">
    <!-- Top 10 Clientes -->
    @if(!Auth::user()->tenant || (Auth::user()->tenant && Auth::user()->tenant->canAccess('crm')))
    <div id="widget-ranking-clientes" class="dashboard-widget bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Ranking de Clientes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-white dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pedidos</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Investido</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($topClientes as $cliente)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs mr-3">
                                    {{ substr($cliente->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cliente->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">{{ $cliente->total_pedidos }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 dark:text-green-400 text-right">R$ {{ number_format($cliente->total_gasto, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                            Nenhum cliente registrado no período.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Top Vendedores ou Pagamentos Pendentes -->
    @if(isset($topVendedores) && $topVendedores->isNotEmpty())
        @if(!Auth::user()->tenant || (Auth::user()->tenant && Auth::user()->tenant->canAccess('reports_simple')))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Desempenho Comercial</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendedor</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metas/Pedidos</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Faturamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($topVendedores as $vendedor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $vendedor->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">{{ $vendedor->total_pedidos }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400 text-right">R$ {{ number_format($vendedor->total_faturamento, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @elseif(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 italic">Contas a Receber</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-white dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo Devedor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($pagamentosPendentes as $pagamento)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400">
                            <a href="{{ route('orders.show', $pagamento->order->id) }}">#{{ str_pad($pagamento->order->id, 6, '0', STR_PAD_LEFT) }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $pagamento->order->client ? $pagamento->order->client->name : 'Consumidor Final' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600 dark:text-red-400 text-right">R$ {{ number_format($pagamento->remaining_amount, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                            Nenhum pagamento pendente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Produtos Mais Vendidos -->
@if(isset($produtosMaisVendidos) && $produtosMaisVendidos->isNotEmpty() && (!Auth::user()->tenant || (Auth::user()->tenant && Auth::user()->tenant->canAccess('reports_simple'))))
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Produtos Mais Vendidos</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Produto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantidade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faturamento</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($produtosMaisVendidos as $produto)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $produto->print_type ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $produto->total_vendido }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ {{ number_format($produto->total_faturamento, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Pedidos Recentes -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pedidos e Vendas Recentes</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedido/Venda</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($pedidosRecentes as $pedido)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($pedido->is_pdv)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                Venda
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                Pedido
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                        <a href="{{ route('orders.show', $pedido->id) }}" 
                           class="hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline">
                            #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $pedido->client ? $pedido->client->name : 'Sem cliente' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                              style="background-color: {{ $pedido->status->color }}20; color: {{ $pedido->status->color }}">
                            {{ $pedido->status->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $pedido->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum pedido ou venda encontrado</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/dashboard-widgets.js') }}"></script>
<script>
(function() {
    'use strict';
    
    // Preparar dados dos gráficos
    @php
        // Garantir que pedidosPorStatus seja uma collection
        $pedidosPorStatusCollection = is_array($pedidosPorStatus) ? collect($pedidosPorStatus) : $pedidosPorStatus;
        $statusData = $pedidosPorStatusCollection->map(function($item) {
            // Se for objeto, acessar como propriedade; se for array, acessar como array
            $status = is_object($item) ? ($item->status ?? $item['status'] ?? 'Sem Status') : ($item['status'] ?? 'Sem Status');
            $color = is_object($item) ? ($item->color ?? $item['color'] ?? '#9ca3af') : ($item['color'] ?? '#9ca3af');
            $total = is_object($item) ? (int)($item->total ?? $item['total'] ?? 0) : (int)($item['total'] ?? 0);
            
            return [
                'status' => $status,
                'color' => $color,
                'total' => $total
            ];
        })->toArray();
        
        $faturamentoData = $faturamentoDiario->map(function($item) {
            $dia = is_object($item) ? ($item->dia ?? '') : ($item['dia'] ?? '');
            $total = is_object($item) ? (float)($item->total ?? 0) : (float)($item['total'] ?? 0);
            return [
                'dia' => $dia,
                'total' => $total
            ];
        })->toArray();
        
        $faturamentoMensalData = $pedidosPorMes->map(function($item) {
            $mes = is_object($item) ? ($item->mes ?? '') : ($item['mes'] ?? '');
            $faturamento = is_object($item) ? (float)($item->faturamento ?? 0) : (float)($item['faturamento'] ?? 0);
            return [
                'mes' => $mes,
                'total' => $faturamento
            ];
        })->toArray();
        
        $faturamentoLojaData = isset($faturamentoPorLoja) ? $faturamentoPorLoja->map(function($item) {
            return [
                'name' => $item['name'] ?? '',
                'total' => (float)($item['total_faturamento'] ?? 0)
            ];
        })->toArray() : [];
        
        $pagamentoData = isset($distribuicaoPagamento) ? $distribuicaoPagamento->map(function($item) {
            return [
                'method' => $item['method'] ?? '',
                'total' => (float)($item['total'] ?? 0)
            ];
        })->toArray() : [];
    @endphp
    
    const dashboardData = {
        statusData: @json($statusData ?? []),
        faturamentoData: @json($faturamentoData ?? []),
        faturamentoMensalData: @json($faturamentoMensalData ?? []),
        faturamentoLojaData: @json($faturamentoLojaData ?? []),
        pagamentoData: @json($pagamentoData ?? [])
    };
    
    // Debug: verificar dados
    console.log('=== DASHBOARD DATA ===');
    console.log('Status Data:', dashboardData.statusData);
    console.log('Faturamento Data:', dashboardData.faturamentoData);
    console.log('Faturamento Mensal Data:', dashboardData.faturamentoMensalData);
    console.log('Faturamento Loja Data:', dashboardData.faturamentoLojaData);
    console.log('Pagamento Data:', dashboardData.pagamentoData);
    console.log('Chart.js disponível:', typeof Chart !== 'undefined');
    
    // Função para inicializar gráficos
    function initCharts() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js não está disponível');
            return false;
        }
        
        console.log('Inicializando gráficos...');
        
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(0, 0, 0, 0.1)';
        const borderColor = isDark ? '#1f2937' : '#ffffff';
        
        // Destruir gráficos existentes se houver
        if (window.statusChart && typeof window.statusChart.destroy === 'function') {
            try { window.statusChart.destroy(); } catch(e) {}
        }
        if (window.faturamentoChart && typeof window.faturamentoChart.destroy === 'function') {
            try { window.faturamentoChart.destroy(); } catch(e) {}
        }
        if (window.faturamentoMensalChart && typeof window.faturamentoMensalChart.destroy === 'function') {
            try { window.faturamentoMensalChart.destroy(); } catch(e) {}
        }
        if (window.faturamentoLojaChart && typeof window.faturamentoLojaChart.destroy === 'function') {
            try { window.faturamentoLojaChart.destroy(); } catch(e) {}
        }
        if (window.pagamentoChart && typeof window.pagamentoChart.destroy === 'function') {
            try { window.pagamentoChart.destroy(); } catch(e) {}
        }
        
        // Gráfico de Status (Pizza)
        const statusCanvas = document.getElementById('statusChart');
        if (!statusCanvas) {
            console.error('Canvas statusChart não encontrado');
            return false;
        }
        
        const statusData = dashboardData.statusData || [];
        console.log('Status Data:', statusData);
        
        // Filtrar apenas dados com total > 0
        const validStatusData = statusData.filter(item => item && item.total > 0);
        
        if (validStatusData.length === 0) {
            console.warn('Sem dados válidos para gráfico de status');
            statusCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
        } else {
        
        const statusLabels = validStatusData.map(item => item.status || 'Sem Status');
        const statusValues = validStatusData.map(item => parseInt(item.total) || 0);
        const statusColors = validStatusData.map(item => item.color || '#9ca3af');
        
        console.log('Criando gráfico de status com:', { labels: statusLabels, values: statusValues, colors: statusColors });
        
        try {
            window.statusChart = new Chart(statusCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusColors,
                        borderWidth: 2,
                        borderColor: borderColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: textColor, padding: 15, font: { size: 12 } }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#374151' : '#ffffff',
                            titleColor: isDark ? '#f9fafb' : '#111827',
                            bodyColor: isDark ? '#e5e7eb' : '#374151',
                            borderColor: isDark ? '#4b5563' : '#e5e7eb',
                            borderWidth: 1
                        }
                    }
                }
            });
            console.log('Gráfico de status criado com sucesso');
        } catch (error) {
            console.error('Erro ao criar gráfico de status:', error);
            statusCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
        }
        }
        
        // Gráfico de Faturamento Diário (Linha)
        const faturamentoCanvas = document.getElementById('faturamentoChart');
        if (!faturamentoCanvas) {
            console.warn('Canvas faturamentoChart não encontrado');
        } else {
            console.log('Criando gráfico de faturamento diário');
            const faturamentoData = dashboardData.faturamentoData || [];
            console.log('Faturamento Diário Data:', faturamentoData);
            
            if (!faturamentoData || faturamentoData.length === 0 || faturamentoData.every(item => !item.total || item.total === 0)) {
                console.warn('Sem dados para gráfico de faturamento diário');
                faturamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            } else {
                const faturamentoLabels = faturamentoData.map(item => {
                    if (!item.dia) return '';
                    try {
                        const parts = String(item.dia).split(/[-/]/);
                        if (parts.length >= 3) {
                            const d = new Date(parts[0], parts[1] - 1, parts[2]);
                            return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                        }
                        return '';
                    } catch (e) {
                        return '';
                    }
                }).filter(label => label !== '');
                const faturamentoValues = faturamentoData.map(item => parseFloat(item.total || 0));
                
                console.log('Faturamento Labels:', faturamentoLabels);
                console.log('Faturamento Values:', faturamentoValues);
                
                try {
                    window.faturamentoChart = new Chart(faturamentoCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: faturamentoLabels.length > 0 ? faturamentoLabels : ['Sem dados'],
                            datasets: [{
                                label: 'Faturamento (R$)',
                                data: faturamentoValues.length > 0 ? faturamentoValues : [0],
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: isDark ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(99, 102, 241)',
                                pointBorderColor: borderColor,
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        callback: function(value) {
                                            return 'R$ ' + value.toLocaleString('pt-BR');
                                        }
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de faturamento diário criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de faturamento diário:', error);
                    faturamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            }
        }
        
        // Gráfico de Faturamento Mensal (Linha)
        const faturamentoMensalCanvas = document.getElementById('faturamentoMensalChart');
        if (!faturamentoMensalCanvas) {
            console.warn('Canvas faturamentoMensalChart não encontrado');
        } else {
            console.log('Criando gráfico de faturamento mensal');
            console.log('Faturamento Mensal Data:', dashboardData.faturamentoMensalData);
            const faturamentoMensalData = dashboardData.faturamentoMensalData || [];
            
            if (!faturamentoMensalData || faturamentoMensalData.length === 0 || faturamentoMensalData.every(item => !item.total || item.total === 0)) {
                console.warn('Sem dados para gráfico de faturamento mensal');
                faturamentoMensalCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            } else {
            const faturamentoMensalLabels = faturamentoMensalData.map(item => {
                if (!item.mes) return '';
                try {
                    const parts = String(item.mes).split('-');
                    if (parts.length >= 2) {
                        const d = new Date(parts[0], parts[1] - 1);
                        return d.toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' });
                    }
                    return '';
                } catch (e) {
                    return '';
                }
                }).filter(label => label !== '');
                const faturamentoMensalValues = faturamentoMensalData.map(item => parseFloat(item.total || 0));
                
                console.log('Faturamento Mensal Labels:', faturamentoMensalLabels);
                console.log('Faturamento Mensal Values:', faturamentoMensalValues);
                
                try {
                    window.faturamentoMensalChart = new Chart(faturamentoMensalCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: faturamentoMensalLabels.length > 0 ? faturamentoMensalLabels : ['Sem dados'],
                            datasets: [{
                                label: 'Faturamento (R$)',
                                data: faturamentoMensalValues.length > 0 ? faturamentoMensalValues : [0],
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: isDark ? 'rgba(34, 197, 94, 0.2)' : 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(34, 197, 94)',
                                pointBorderColor: borderColor,
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        callback: function(value) {
                                            return 'R$ ' + value.toLocaleString('pt-BR');
                                        }
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de faturamento mensal criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de faturamento mensal:', error);
                    faturamentoMensalCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            }
        }
        
        // Gráfico de Faturamento por Loja (Barras)
        const faturamentoLojaCanvas = document.getElementById('faturamentoLojaChart');
        if (!faturamentoLojaCanvas) {
            console.warn('Canvas faturamentoLojaChart não encontrado');
        } else {
            if (dashboardData.faturamentoLojaData && dashboardData.faturamentoLojaData.length > 0 && dashboardData.faturamentoLojaData.some(item => item.total > 0)) {
                const faturamentoLojaLabels = dashboardData.faturamentoLojaData.map(item => item.name || 'Sem nome');
                const faturamentoLojaValues = dashboardData.faturamentoLojaData.map(item => parseFloat(item.total || 0));
                
                console.log('Criando gráfico de faturamento por loja:', { labels: faturamentoLojaLabels, values: faturamentoLojaValues });
                
                try {
                    window.faturamentoLojaChart = new Chart(faturamentoLojaCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: faturamentoLojaLabels,
                            datasets: [{
                                label: 'Faturamento (R$)',
                                data: faturamentoLojaValues,
                                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                                borderColor: 'rgb(99, 102, 241)',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        callback: function(value) {
                                            return 'R$ ' + value.toLocaleString('pt-BR');
                                        }
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de faturamento por loja criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de faturamento por loja:', error);
                    faturamentoLojaCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            } else {
                console.warn('Faturamento por Loja: sem dados');
                faturamentoLojaCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            }
        }
        
        // Gráfico de Formas de Pagamento (Pizza)
        const pagamentoCanvas = document.getElementById('pagamentoChart');
        if (!pagamentoCanvas) {
            console.warn('Canvas pagamentoChart não encontrado');
        } else {
            console.log('Pagamento Data:', dashboardData.pagamentoData);
            if (dashboardData.pagamentoData && dashboardData.pagamentoData.length > 0 && dashboardData.pagamentoData.some(item => item.total > 0)) {
                const pagamentoLabels = dashboardData.pagamentoData.map(item => {
                    const methodNames = {
                        'dinheiro': 'Dinheiro',
                        'pix': 'PIX',
                        'cartao': 'Cartão',
                        'transferencia': 'Transferência',
                        'boleto': 'Boleto'
                    };
                    return methodNames[item.method] || item.method;
                });
                const pagamentoValues = dashboardData.pagamentoData.map(item => parseFloat(item.total || 0));
                const pagamentoColors = [
                    'rgba(34, 197, 94, 0.8)',   // Verde - PIX
                    'rgba(59, 130, 246, 0.8)',  // Azul - Cartão
                    'rgba(234, 179, 8, 0.8)',   // Amarelo - Dinheiro
                    'rgba(168, 85, 247, 0.8)',  // Roxo - Transferência
                    'rgba(239, 68, 68, 0.8)'    // Vermelho - Boleto
                ];
                
                console.log('Criando gráfico de formas de pagamento:', { labels: pagamentoLabels, values: pagamentoValues });
                
                try {
                    window.pagamentoChart = new Chart(pagamentoCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: pagamentoLabels,
                            datasets: [{
                                data: pagamentoValues,
                                backgroundColor: pagamentoColors.slice(0, pagamentoLabels.length),
                                borderWidth: 2,
                                borderColor: borderColor
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: textColor, padding: 15, font: { size: 12 } }
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                            return context.label + ': R$ ' + context.parsed.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de formas de pagamento criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de formas de pagamento:', error);
                    pagamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            } else {
                console.warn('Formas de Pagamento: sem dados');
                pagamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            }
        }
        
        console.log('Gráficos inicializados');
        return true;
    }
    
    // Aguardar Chart.js e DOM estarem prontos
    function waitAndInit() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js não está disponível');
            setTimeout(waitAndInit, 100);
            return;
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initCharts, 200);
            });
        } else {
            setTimeout(initCharts, 200);
        }
    }
    
    // Inicializar quando a página carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitAndInit);
    } else {
        waitAndInit();
    }
    
    // Fallback: tentar novamente após um tempo
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (!window.statusChart && typeof Chart !== 'undefined') {
                console.log('Tentando inicializar gráficos novamente...');
                initCharts();
            }
        }, 1000);
    });
})();
</script>
@endpush

