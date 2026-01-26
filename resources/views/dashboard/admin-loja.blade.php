@extends('layouts.admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Ponto de Venda - {{ $store->name ?? 'Minha Loja' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Resumo de desempenho desta unidade.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Filtro de Período -->
            <form method="GET" action="{{ route('dashboard') }}" id="periodFilterForm" class="flex items-center gap-2">
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
            
            <!-- Filtros Avançados -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2 border-l border-gray-200 dark:border-gray-700 pl-3">
                @if(isset($period)) <input type="hidden" name="period" value="{{ $period }}"> @endif
                
                @if(isset($vendedores) && $vendedores->count() > 0)
                <select name="vendor_id" onchange="this.form.submit()" 
                        class="px-3 py-2 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition-all">
                    <option value="">Vendedor: Todos</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                    @endforeach
                </select>
                @endif

            </form>

            

            @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('pdv'))
            <!-- Acesso Rápido ao PDV -->
            <a href="{{ route('pdv.index') }}" 
               class="px-4 py-2 bg-indigo-600 !text-white rounded-xl hover:bg-indigo-700 transition shadow-sm text-sm font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Abrir PDV
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Card de Assinatura -->
@if(Auth::user()->tenant)
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-xl">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ Auth::user()->tenant->currentPlan->name ?? 'Sem Plano' }}
                    </span>
                    @if(Auth::user()->tenant->currentPlan)
                        <span class="text-xs text-gray-500 dark:text-gray-400">R$ {{ number_format(Auth::user()->tenant->currentPlan->price, 2, ',', '.') }}/mês</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    @if(Auth::user()->tenant->subscription_ends_at)
                        @if(Auth::user()->tenant->subscription_ends_at->isPast())
                            <span class="text-red-400">Expirada</span>
                        @elseif(Auth::user()->tenant->subscription_ends_at->diffInDays(now()) < 7)
                            <span class="text-amber-400">Expira {{ Auth::user()->tenant->subscription_ends_at->diffForHumans() }}</span>
                        @else
                            <span class="text-emerald-400">Ativa até {{ Auth::user()->tenant->subscription_ends_at->format('d/m/Y') }}</span>
                        @endif
                    @elseif(Auth::user()->tenant->trial_ends_at && Auth::user()->tenant->trial_ends_at->isFuture())
                        <span class="text-cyan-400">Teste até {{ Auth::user()->tenant->trial_ends_at->format('d/m/Y') }}</span>
                    @else
                        <span class="text-gray-500">Nenhuma assinatura ativa</span>
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('subscription.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
            Gerenciar
            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
@endif

<!-- Grid de KPIs Principais -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- Total de Pedidos -->
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pedidos Unidade</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalPedidos, 0, '.', '.') }}</h3>
                @if(isset($variacaoPedidos))
                <div class="flex items-center mt-2">
                    <span class="flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $variacaoPedidos >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $variacaoPedidos >= 0 ? '+' : '' }}{{ number_format($variacaoPedidos, 1) }}%
                    </span>
                    <span class="ml-2 text-xs text-gray-500">vs período ant.</span>
                </div>
                @endif
            </div>
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
    </div>

    <!-- Faturamento -->
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
                    <span class="ml-2 text-xs text-gray-500">vs período ant.</span>
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
                <p class="text-xs text-gray-500 mt-2">Atualize para ver faturamento.</p>
            </div>
            <div class="p-3 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
        </div>
    </div>
    @endif

    <!-- Total de Clientes -->
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clientes</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalClientes, 0, '.', '.') }}</h3>
                <p class="text-xs text-gray-500 mt-2">Base desta unidade</p>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Ticket Médio -->
    @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
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
    <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300 relative overflow-hidden">
        <div class="absolute inset-0 bg-gray-100/50 dark:bg-gray-900/50 backdrop-blur-[1px] z-10 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 px-3 py-1 rounded-full shadow-sm border border-gray-200 dark:border-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">Disponível no Plano Pro</span>
            </div>
        </div>
        <div class="flex items-start justify-between filter blur-[2px]">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket Médio</p>
                <h3 class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">R$ ***,**</h3>
            </div>
            <div class="p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            </div>
        </div>
    </div>
    @endif
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

<!-- Gráficos -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Pedidos por Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 {{ (!Auth::user()->tenant || Auth::user()->tenant->canAccess('reports_simple')) ? '' : 'opacity-50 grayscale pointer-events-none relative overflow-hidden' }}">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Pedidos por Status</h2>
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

    <!-- Faturamento Diário -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 {{ (!Auth::user()->tenant || Auth::user()->tenant->canAccess('financial')) ? '' : 'opacity-50 grayscale pointer-events-none relative overflow-hidden' }}">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento Diário (Últimos 30 Dias)</h2>
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

<!-- Gráficos Adicionais -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Faturamento por Vendedor -->
    @if(isset($topVendedores) && $topVendedores->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento por Vendedor</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="vendedorChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('reports_simple'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-gray-800/60 backdrop-blur-[2px] z-10 p-4 text-center">
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Relatórios Bloqueados</p>
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- Vendas PDV vs Pedidos Online -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">PDV vs Online</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="pdvVsOnlineChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('reports_simple'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-gray-800/60 backdrop-blur-[2px] z-10 p-4 text-center">
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Relatórios Bloqueados</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Formas de Pagamento -->
    @if(isset($distribuicaoPagamento) && $distribuicaoPagamento->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Formas de Pagamento</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="pagamentoChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-gray-800/60 backdrop-blur-[2px] z-10 p-4 text-center">
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Financeiro Bloqueado</p>
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- Faturamento Diário -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento Diário</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoChart2"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/60 dark:bg-gray-800/60 backdrop-blur-[2px] z-10 p-4 text-center">
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Financeiro Bloqueado</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Tabelas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top 5 Clientes -->
    @if(!Auth::user()->tenant || Auth::user()->tenant->canAccess('crm'))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top 5 Clientes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($topClientes as $cliente)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $cliente->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $cliente->total_pedidos }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ {{ number_format($cliente->total_gasto, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum cliente encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Vendedores da Loja -->
    @if(isset($topVendedores) && $topVendedores->isNotEmpty())
        @if(!Auth::user()->tenant || Auth::user()->tenant->canAccess('reports_simple'))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Vendedores da Loja</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Vendedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faturamento</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($topVendedores as $vendedor)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $vendedor->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $vendedor->total_pedidos }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ {{ number_format($vendedor->total_faturamento, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        @endif
    @else
    <!-- Pagamentos Pendentes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pagamentos Pendentes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Restante</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pagamentosPendentes as $pagamento)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                            <a href="{{ route('orders.show', $pagamento->order->id) }}" 
                               class="hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline">
                                #{{ str_pad($pagamento->order->id, 6, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $pagamento->order->client ? $pagamento->order->client->name : 'Sem cliente' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-orange-600 dark:text-orange-400">R$ {{ number_format($pagamento->remaining_amount, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum pagamento pendente</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Produtos Mais Vendidos -->
@if(isset($produtosMaisVendidos) && $produtosMaisVendidos->isNotEmpty())
    @if(!Auth::user()->tenant || Auth::user()->tenant->canAccess('reports_simple'))
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
<script>
(function() {
    'use strict';
    
    @php
        $statusData = $pedidosPorStatus->map(function($item) {
            return [
                'status' => $item['status'] ?? 'Sem Status',
                'color' => $item['color'] ?? '#9ca3af',
                'total' => (int)($item['total'] ?? 0)
            ];
        })->toArray();
        
        $faturamentoData = $faturamentoDiario->map(function($item) {
            return [
                'dia' => $item->dia ?? '',
                'total' => (float)($item->total ?? 0)
            ];
        })->toArray();
        
        $vendedorData = isset($topVendedores) ? $topVendedores->map(function($item) {
            return [
                'name' => $item->name ?? '',
                'total' => (float)($item->total_faturamento ?? 0)
            ];
        })->toArray() : [];
        
        $pdvVsOnlineData = [
            ['label' => 'PDV', 'value' => $vendasPDVValor ?? 0],
            ['label' => 'Online', 'value' => $pedidosOnlineValor ?? 0]
        ];
        
        $pagamentoData = isset($distribuicaoPagamento) ? $distribuicaoPagamento->map(function($item) {
            return [
                'method' => $item['method'] ?? '',
                'total' => (float)($item['total'] ?? 0)
            ];
        })->toArray() : [];
    @endphp
    
    const dashboardData = {
        statusData: @json($statusData),
        faturamentoData: @json($faturamentoData),
        vendedorData: @json($vendedorData),
        pdvVsOnlineData: @json($pdvVsOnlineData),
        pagamentoData: @json($pagamentoData)
    };
    
    function initCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js não está disponível');
            return false;
        }
        
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(0, 0, 0, 0.1)';
        const borderColor = isDark ? '#1f2937' : '#ffffff';
        
        // Destruir gráficos existentes
        if (window.statusChart) window.statusChart.destroy();
        if (window.faturamentoChart) window.faturamentoChart.destroy();
        if (window.vendedorChart) window.vendedorChart.destroy();
        if (window.pdvVsOnlineChart) window.pdvVsOnlineChart.destroy();
        if (window.pagamentoChart) window.pagamentoChart.destroy();
        
        // Gráfico de Status
        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            const statusData = dashboardData.statusData || [];
            const statusLabels = statusData.length > 0 ? statusData.map(item => item.status) : ['Sem dados'];
            const statusValues = statusData.length > 0 ? statusData.map(item => item.total) : [1];
            const statusColors = statusData.length > 0 ? statusData.map(item => item.color) : ['#9ca3af'];
            
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
        }
        
        // Gráfico de Faturamento Diário
        const faturamentoCanvas = document.getElementById('faturamentoChart');
        if (faturamentoCanvas) {
            const faturamentoData = dashboardData.faturamentoData || [];
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
        }
        
        // Gráfico de Faturamento por Vendedor
        const vendedorCanvas = document.getElementById('vendedorChart');
        if (vendedorCanvas && dashboardData.vendedorData && dashboardData.vendedorData.length > 0) {
            const vendedorLabels = dashboardData.vendedorData.map(item => item.name);
            const vendedorValues = dashboardData.vendedorData.map(item => parseFloat(item.total || 0));
            
            window.vendedorChart = new Chart(vendedorCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: vendedorLabels,
                    datasets: [{
                        label: 'Faturamento (R$)',
                        data: vendedorValues,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
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
        }
        
        // Gráfico PDV vs Online
        const pdvVsOnlineCanvas = document.getElementById('pdvVsOnlineChart');
        if (pdvVsOnlineCanvas && dashboardData.pdvVsOnlineData) {
            const pdvVsOnlineLabels = dashboardData.pdvVsOnlineData.map(item => item.label);
            const pdvVsOnlineValues = dashboardData.pdvVsOnlineData.map(item => parseFloat(item.value || 0));
            
            window.pdvVsOnlineChart = new Chart(pdvVsOnlineCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: pdvVsOnlineLabels,
                    datasets: [{
                        data: pdvVsOnlineValues,
                        backgroundColor: ['rgba(34, 197, 94, 0.8)', 'rgba(59, 130, 246, 0.8)'],
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
        }
        
        // Gráfico de Formas de Pagamento
        const pagamentoCanvas = document.getElementById('pagamentoChart');
        if (pagamentoCanvas && dashboardData.pagamentoData && dashboardData.pagamentoData.length > 0) {
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
                'rgba(34, 197, 94, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(234, 179, 8, 0.8)',
                'rgba(168, 85, 247, 0.8)', 'rgba(239, 68, 68, 0.8)'
            ];
            
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
        }
        
        return true;
    }
    
    function waitAndInit() {
        if (typeof Chart !== 'undefined' && document.readyState === 'complete') {
            setTimeout(function() {
                initCharts();
            }, 100);
        } else {
            setTimeout(waitAndInit, 50);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitAndInit);
    } else {
        waitAndInit();
    }
    
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (!window.statusChart) {
                initCharts();
            }
        }, 500);
    });
})();
</script>
@endpush

