@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 animate-fade-in-up">
    
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-4">
            <div class="flex flex-col gap-2">
                <span class="inline-block px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black tracking-widest uppercase w-fit">
                    Visão Geral do Negócio
                </span>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-adaptive leading-none">
                    @if(Auth::user()->tenant)
                        <span class="text-gradient">{{ Auth::user()->tenant->name }}</span>
                    @else
                        Painel <span class="text-gradient">Administrativo</span>
                    @endif
                </h1>
            </div>
            <p class="text-muted text-lg font-medium max-w-2xl">
                Resumo de desempenho e atividades principais do seu ecossistema.
            </p>
            
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <!-- Filtro de Período -->
                <form method="GET" action="{{ route('dashboard') }}" id="periodFilterForm" class="flex flex-wrap items-center gap-3">
                    @if(isset($selectedStoreId))
                        <input type="hidden" name="store_id" value="{{ $selectedStoreId }}">
                    @endif
                    <div class="relative group">
                        <select name="period" id="period" 
                                onchange="if(this.value !== 'custom') this.form.submit(); else document.getElementById('customDateFields').classList.remove('hidden');"
                                class="pl-4 pr-10 py-2.5 bg-card-bg border border-border rounded-2xl text-white text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none cursor-pointer hover:bg-white/5 min-w-[140px]">
                            <option value="today" {{ ($period ?? 'month') === 'today' ? 'selected' : '' }}>Hoje</option>
                            <option value="week" {{ ($period ?? 'month') === 'week' ? 'selected' : '' }}>Esta Semana</option>
                            <option value="month" {{ ($period ?? 'month') === 'month' ? 'selected' : '' }}>Este Mês</option>
                            <option value="year" {{ ($period ?? 'month') === 'year' ? 'selected' : '' }}>Este Ano</option>
                            <option value="custom" {{ ($period ?? 'month') === 'custom' ? 'selected' : '' }}>Personalizado</option>
                        </select>
                        <i class="fa-solid fa-calendar-days absolute right-4 top-1/2 -translate-y-1/2 text-muted group-hover:text-primary transition-colors pointer-events-none text-xs"></i>
                    </div>

                    <div id="customDateFields" class="{{ ($period ?? 'month') === 'custom' ? '' : 'hidden' }} flex items-center gap-2 animate-fade-in">
                        <input type="date" name="start_date" value="{{ isset($startDate) ? $startDate->format('Y-m-d') : '' }}" 
                               class="px-4 py-2 bg-card-bg border border-border rounded-xl text-white text-xs font-bold focus:ring-1 focus:ring-primary/30 outline-none">
                        <span class="text-muted text-xs font-black">ATÉ</span>
                        <input type="date" name="end_date" value="{{ isset($endDate) ? $endDate->format('Y-m-d') : '' }}" 
                               class="px-4 py-2 bg-card-bg border border-border rounded-xl text-white text-xs font-bold focus:ring-1 focus:ring-primary/30 outline-none">
                        <button type="submit" class="w-10 h-10 bg-primary/10 text-primary border border-primary/20 rounded-xl hover:bg-primary hover:text-white transition-all shadow-lg active:scale-95">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                </form>

                <!-- Filtro de Unidade -->
                @if(isset($stores) && $stores->count() > 1)
                <form method="GET" action="{{ route('dashboard') }}" id="storeFilterForm" class="flex items-center gap-2">
                    @if(isset($period))
                        <input type="hidden" name="period" value="{{ $period }}">
                    @endif
                    <div class="relative group">
                        <select name="store_id" id="store_filter" 
                                onchange="this.form.submit()"
                                class="pl-4 pr-10 py-2.5 bg-card-bg border border-border rounded-2xl text-white text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all appearance-none cursor-pointer hover:bg-white/5 min-w-[180px]">
                            <option value="">Todas as Unidades</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ (isset($selectedStoreId) && $selectedStoreId == $store->id) ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-store absolute right-4 top-1/2 -translate-y-1/2 text-muted group-hover:text-primary transition-colors pointer-events-none text-xs"></i>
                    </div>
                </form>
                @endif

                <button onclick="window.dashboardWidgets?.resetLayout()" 
                        class="p-2.5 rounded-2xl bg-card-bg border border-border text-muted hover:text-white hover:bg-white/5 transition-all group"
                        title="Resetar Layout">
                    <i class="fa-solid fa-arrows-rotate text-sm group-hover:rotate-180 transition-transform duration-500"></i>
                </button>
            </div>
        </div>

        <div class="hidden lg:block">
            <div class="px-6 py-4 rounded-3xl bg-card-bg border border-border shadow-2xl backdrop-blur-md relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white">
                        <i class="fa-solid fa-bolt-lightning text-xl stay-white"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-muted font-bold">Status Global</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <p class="text-sm font-bold text-adaptive tracking-tight">Sistema Online</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 widget-container" data-dashboard-widgets id="kpi-grid">
        <!-- Total Orders -->
        <div class="group relative rounded-3xl bg-card-bg border border-border p-8 transition-all duration-300 hover:border-primary/50 hover-lift shadow-2xl overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors"></div>
            <div class="flex flex-col h-full justify-between gap-6">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-2xl group-hover:scale-110 transition-transform shadow-inner">
                        <i class="fa-solid fa-box-open stay-white"></i>
                    </div>
                    @if(isset($variacaoPedidos))
                    <span class="px-2 py-1 rounded-lg text-[10px] font-black {{ $variacaoPedidos >= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                        {{ $variacaoPedidos >= 0 ? '↑' : '↓' }} {{ abs(number_format($variacaoPedidos, 1)) }}%
                    </span>
                    @endif
                </div>
                <div>
                    <h3 class="text-4xl font-black text-adaptive tracking-tighter">{{ number_format($totalPedidos, 0, '.', '.') }}</h3>
                    <p class="text-xs font-black text-muted uppercase tracking-widest mt-1">Pedidos Totais</p>
                </div>
            </div>
        </div>

        <!-- Faturamento -->
        <div class="group relative rounded-3xl bg-card-bg border border-border p-8 transition-all duration-300 {{ Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial') ? 'opacity-75 grayscale' : 'hover:border-emerald-500/50 hover-lift shadow-2xl' }} overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
            @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
            <div class="flex flex-col h-full justify-between gap-6">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 flex items-center justify-center text-white text-2xl group-hover:scale-110 transition-transform shadow-inner">
                        <i class="fa-solid fa-sack-dollar stay-white"></i>
                    </div>
                    @if(isset($variacaoFaturamento))
                    <span class="px-2 py-1 rounded-lg text-[10px] font-black {{ $variacaoFaturamento >= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                         {{ $variacaoFaturamento >= 0 ? '↑' : '↓' }} {{ abs(number_format($variacaoFaturamento, 1)) }}%
                    </span>
                    @endif
                </div>
                <div>
                    <h3 class="text-4xl font-black text-adaptive tracking-tighter">R$ {{ number_format($totalFaturamento, 0, ',', '.') }}</h3>
                    <p class="text-xs font-black text-muted uppercase tracking-widest mt-1">Faturamento Bruto</p>
                </div>
            </div>
            @else
            <div class="flex flex-col h-full justify-center items-center text-center space-y-4">
                <div class="w-12 h-12 rounded-xl bg-purple-600 flex items-center justify-center text-white">
                    <i class="fa-solid fa-lock text-xl stay-white"></i>
                </div>
                <p class="text-[10px] font-black uppercase text-muted tracking-widest leading-tight">Métrica Bloqueada<br><span class="text-primary">Upgrade Pro</span></p>
            </div>
            @endif
        </div>

        <!-- Ticket Médio -->
        <div class="group relative rounded-3xl bg-card-bg border border-border p-8 transition-all duration-300 {{ Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial') ? 'opacity-75 grayscale' : 'hover:border-purple-500/50 hover-lift shadow-2xl' }} overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-colors"></div>
            @if(!Auth::user()->tenant || Auth::user()->tenant->canAccess('financial'))
            <div class="flex flex-col h-full justify-between gap-6">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-2xl bg-purple-600 flex items-center justify-center text-white text-2xl group-hover:scale-110 transition-transform shadow-inner">
                        <i class="fa-solid fa-chart-pie stay-white"></i>
                    </div>
                    @if(isset($variacaoTicketMedio))
                    <span class="px-2 py-1 rounded-lg text-[10px] font-black {{ $variacaoTicketMedio >= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                         {{ $variacaoTicketMedio >= 0 ? '↑' : '↓' }} {{ abs(number_format($variacaoTicketMedio, 1)) }}%
                    </span>
                    @endif
                </div>
                <div>
                    <h3 class="text-4xl font-black text-adaptive tracking-tighter">R$ {{ number_format($ticketMedio ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-xs font-black text-muted uppercase tracking-widest mt-1">Ticket Médio</p>
                </div>
            </div>
            @else
            <div class="flex flex-col h-full justify-center items-center text-center space-y-4">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-muted">
                    <i class="fa-solid fa-lock text-xl"></i>
                </div>
                <p class="text-[10px] font-black uppercase text-muted tracking-widest leading-tight">Relatórios VIP<br><span class="text-purple-400">Plano Pro</span></p>
            </div>
            @endif
        </div>

        <!-- Stock Requests -->
        <div class="group relative rounded-3xl bg-card-bg border border-border p-8 transition-all duration-300 hover:border-amber-500/50 hover-lift shadow-2xl overflow-hidden">
            <a href="{{ route('stock-requests.index', ['status' => 'pendente']) }}" class="flex flex-col h-full justify-between gap-6">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-colors"></div>
                <div class="flex items-start justify-between relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-amber-600 flex items-center justify-center text-white text-2xl group-hover:scale-110 transition-transform shadow-inner">
                        <i class="fa-solid fa-truck-ramp-box stay-white"></i>
                    </div>
                    @if($solicitacoesPendentesCount > 0)
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    @endif
                </div>
                <div class="relative z-10">
                    <h3 class="text-4xl font-black text-adaptive tracking-tighter">{{ $solicitacoesPendentesCount }}</h3>
                    <p class="text-xs font-black text-muted uppercase tracking-widest mt-1">Logística Pendente</p>
                </div>
            </a>
        </div>
    </div>

<!-- Sales Channels Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('pdv'))
    <div class="rounded-3xl bg-card-bg border border-border p-6 hover-lift transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white">
                <i class="fa-solid fa-store text-sm stay-white"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-muted">Vendas PDV</span>
        </div>
        <p class="text-2xl font-black text-adaptive tracking-tight">{{ $vendasPDV ?? 0 }} <span class="text-xs font-medium text-muted">vendas</span></p>
        <p class="text-sm font-bold text-emerald-500 mt-1">R$ {{ number_format($vendasPDVValor ?? 0, 2, ',', '.') }}</p>
    </div>
    @endif

    <div class="rounded-3xl bg-card-bg border border-border p-6 hover-lift transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white">
                <i class="fa-solid fa-globe text-sm stay-white"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-muted">Pedidos Online</span>
        </div>
        <p class="text-2xl font-black text-adaptive tracking-tight">{{ $pedidosOnline ?? 0 }} <span class="text-xs font-medium text-muted">pedidos</span></p>
        <p class="text-sm font-bold text-primary mt-1">R$ {{ number_format($pedidosOnlineValor ?? 0, 2, ',', '.') }}</p>
    </div>

    <div class="rounded-3xl bg-card-bg border border-border p-6 hover-lift transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white">
                <i class="fa-solid fa-calendar-day text-sm stay-white"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-muted">Atividade Hoje</span>
        </div>
        <p class="text-2xl font-black text-adaptive tracking-tight">{{ $pedidosHoje }} <span class="text-xs font-medium text-muted">novos</span></p>
        <div class="flex items-center gap-2 mt-1">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
            <p class="text-xs font-bold text-muted">Capturados agora</p>
        </div>
    </div>

    @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
    <div class="rounded-3xl bg-card-bg border border-border p-6 hover-lift transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center text-white">
                <i class="fa-solid fa-hand-holding-dollar text-sm stay-white"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-muted">Recebimentos</span>
        </div>
        <p class="text-2xl font-black text-adaptive tracking-tight">{{ $pagamentosPendentes->count() }} <span class="text-xs font-medium text-muted">pendentes</span></p>
        <p class="text-sm font-bold text-red-500 mt-1">R$ {{ number_format($totalPendente, 2, ',', '.') }}</p>
    </div>
    @endif
</div>

<!-- Evolution Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12 widget-container">
    <!-- Pedidos por Status -->
    <div id="widget-fluxo-pedidos" class="rounded-3xl bg-card-bg border border-border p-8 shadow-2xl overflow-hidden {{ (Auth::user()->tenant && !Auth::user()->tenant->canAccess('reports_simple')) ? 'opacity-50 grayscale pointer-events-none relative' : '' }}">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-chart-donut text-primary"></i>
                Fluxo de Pedidos
            </h2>
            <span class="text-[10px] font-black uppercase tracking-widest text-muted">Snapshot por Status</span>
        </div>
        <div style="height: 320px; position: relative;" class="p-2">
            <canvas id="statusChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('reports_simple'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40 backdrop-blur-sm z-10 p-6 text-center rounded-2xl">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-muted mb-4">
                    <i class="fa-solid fa-lock text-xl"></i>
                </div>
                <p class="text-sm font-black text-white uppercase tracking-widest mb-1">Métricas Avançadas</p>
                <p class="text-xs font-medium text-muted">Disponível nos planos Core e Pro</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Tendência de Receita -->
    <div id="widget-tendencia-receita" class="rounded-3xl bg-card-bg border border-border p-8 shadow-2xl overflow-hidden {{ (Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial')) ? 'opacity-50 grayscale pointer-events-none relative' : '' }}">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-chart-line text-emerald-500"></i>
                Tendência de Receita
            </h2>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary shadow-[0_0_8px_rgba(124,58,237,0.5)]"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-muted">Performance Diária</span>
                </div>
            </div>
        </div>
        <div style="height: 320px; position: relative;" class="p-2">
            <canvas id="faturamentoChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial'))
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40 backdrop-blur-sm z-10 p-6 text-center rounded-2xl">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-muted mb-4">
                    <i class="fa-solid fa-crown text-xl text-amber-500"></i>
                </div>
                <p class="text-sm font-black text-white uppercase tracking-widest mb-1">Visão Financeira</p>
                <p class="text-xs font-medium text-muted text-nowrap">Upgrade para Plano Pro necessário</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12 widget-container">
    <!-- Performance por Unidade -->
    <div class="lg:col-span-2 rounded-3xl bg-card-bg border border-border p-8 shadow-2xl overflow-hidden relative">
        <h2 class="text-xl font-bold text-white mb-8 flex items-center gap-3">
             <i class="fa-solid fa-ranking-stars text-primary"></i>
             @if(isset($faturamentoPorLoja) && $faturamentoPorLoja->count() > 1) Performance por Unidade @else Evolução Mensal @endif
        </h2>
        <div style="height: 320px; position: relative;">
            @if(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
                <canvas id="{{ (isset($faturamentoPorLoja) && $faturamentoPorLoja->count() > 1) ? 'faturamentoLojaChart' : 'faturamentoMensalChart' }}"></canvas>
            @else
                <div class="flex flex-col items-center justify-center h-full text-center space-y-4">
                     <i class="fa-solid fa-lock-open text-4xl text-muted/20"></i>
                     <p class="text-sm font-medium text-muted">Histórico financeiro detalhado indisponível no plano atual.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Meios de Pagamento -->
    <div class="rounded-3xl bg-card-bg border border-border p-8 shadow-2xl overflow-hidden relative {{ (Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial')) ? 'opacity-50 grayscale pointer-events-none' : '' }}">
        <h2 class="text-xl font-bold text-white mb-8 flex items-center gap-3">
            <i class="fa-solid fa-credit-card text-purple-400"></i>
            Meios de Pagamento
        </h2>
        <div style="height: 320px; position: relative;">
            <canvas id="pagamentoChart"></canvas>
            @if(Auth::user()->tenant && !Auth::user()->tenant->canAccess('financial'))
            <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-tighter text-muted">Upgrade Pro Solicitado</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12 widget-container">
    <!-- Top Clients Ranking -->
    @if(!Auth::user()->tenant || (Auth::user()->tenant && Auth::user()->tenant->canAccess('crm')))
    <div id="widget-ranking-clientes" class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl transition-all hover:border-primary/30">
        <div class="px-8 py-6 border-b border-border bg-primary/5 flex items-center justify-between">
            <h2 class="text-lg font-black text-adaptive italic tracking-tight">VIP Clientes</h2>
            <i class="fa-solid fa-crown text-amber-500"></i>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-indigo-50/50 dark:bg-primary/10">
                        <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Cliente</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black text-muted uppercase tracking-widest">Pedidos</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-muted uppercase tracking-widest">Total Gasto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @forelse($topClientes as $cliente)
                    <tr class="transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/20 text-primary flex items-center justify-center font-black text-xs ring-1 ring-primary/30 group-hover:scale-110 transition-transform">
                                    {{ substr($cliente->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-adaptive tracking-tight">{{ $cliente->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center text-sm font-medium text-muted">{{ $cliente->total_pedidos }}</td>
                        <td class="px-8 py-5 text-right font-black text-emerald-500 tracking-tight">R$ {{ number_format($cliente->total_gasto, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-12 text-center text-muted font-medium italic">Nenhum registro no período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Sales Team or Receivables -->
    @if(isset($topVendedores) && $topVendedores->isNotEmpty())
        @if(!Auth::user()->tenant || (Auth::user()->tenant && Auth::user()->tenant->canAccess('reports_simple')))
        <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl transition-all hover:border-primary/30">
            <div class="px-8 py-6 border-b border-border bg-primary/5 flex items-center justify-between">
                <h2 class="text-lg font-black text-adaptive italic tracking-tight">Time Comercial</h2>
                <i class="fa-solid fa-medal text-primary"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-indigo-50/50 dark:bg-primary/10">
                            <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Vendedor</th>
                            <th class="px-8 py-4 text-center text-[10px] font-black text-muted uppercase tracking-widest">Metas/Pedidos</th>
                            <th class="px-8 py-4 text-right text-[10px] font-black text-muted uppercase tracking-widest">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @foreach($topVendedores as $vendedor)
                        <tr class="transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-adaptive tracking-tight">{{ $vendedor->name }}</span>
                            </td>
                            <td class="px-8 py-5 text-center text-sm font-medium text-muted">{{ $vendedor->total_pedidos }}</td>
                            <td class="px-8 py-5 text-right font-black text-primary tracking-tight">R$ {{ number_format($vendedor->total_faturamento, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @elseif(Auth::user()->tenant && Auth::user()->tenant->canAccess('financial'))
    <div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl transition-all hover:border-red-500/30">
        <div class="px-8 py-6 border-b border-border bg-primary/5 flex items-center justify-between">
            <h2 class="text-lg font-black text-adaptive italic tracking-tight">Contas a Receber</h2>
            <i class="fa-solid fa-hourglass-half text-red-500"></i>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-indigo-50/50 dark:bg-primary/10">
                        <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Protocolo</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Cliente</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-muted uppercase tracking-widest">Saldo Devedor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @forelse($pagamentosPendentes as $pagamento)
                    <tr class="transition-colors group">
                        <td class="px-8 py-5 text-sm font-black text-primary">
                            <a href="{{ route('orders.show', $pagamento->order->id) }}" class="hover:underline">#{{ str_pad($pagamento->order->id, 6, '0', STR_PAD_LEFT) }}</a>
                        </td>
                        <td class="px-8 py-5 text-sm font-medium text-adaptive">
                            {{ $pagamento->order->client ? $pagamento->order->client->name : 'Consumidor Final' }}
                        </td>
                        <td class="px-8 py-5 text-right font-black text-red-500 tracking-tight">R$ {{ number_format($pagamento->remaining_amount, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-12 text-center text-muted font-medium italic">Nenhum pagamento pendente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Trending Products Section -->
@if(isset($produtosMaisVendidos) && $produtosMaisVendidos->isNotEmpty() && (!Auth::user()->tenant || (Auth::user()->tenant && Auth::user()->tenant->canAccess('reports_simple'))))
<div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl mb-12">
    <div class="px-8 py-6 border-b border-border flex items-center justify-between bg-primary/5">
        <h2 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fa-solid fa-fire text-orange-500"></i>
            Produtos em Alta
        </h2>
        <span class="text-[10px] font-black uppercase tracking-widest text-muted">Performance por SKU</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-indigo-50/50 dark:bg-primary/10">
                    <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Produto</th>
                    <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Quantidade</th>
                    <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Faturamento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                @foreach($produtosMaisVendidos as $produto)
                <tr class="transition-colors">
                    <td class="px-8 py-5 text-sm font-bold text-adaptive tracking-tight">{{ $produto->print_type ?? 'N/A' }}</td>
                    <td class="px-8 py-5 text-sm font-medium text-muted">{{ $produto->total_vendido }} un.</td>
                    <td class="px-8 py-5 text-sm font-black text-emerald-500">R$ {{ number_format($produto->total_faturamento, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Recent Activity Timeline -->
<div class="rounded-3xl bg-card-bg border border-border overflow-hidden shadow-2xl mb-12">
    <div class="px-8 py-6 border-b border-border bg-primary/5 flex items-center justify-between">
        <h2 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fa-solid fa-clock-rotate-left text-primary"></i>
            Fluxo Recente de Transações
        </h2>
        <a href="{{ route('orders.index') }}" class="text-[10px] font-black text-primary hover:text-primary-hover uppercase tracking-widest transition-colors">
            Ver Todos <i class="fa-solid fa-chevron-right ml-1"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-indigo-50/50 dark:bg-primary/10">
                    <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Tipo</th>
                    <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Referência</th>
                    <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Identificação</th>
                    <th class="px-8 py-4 text-left text-[10px] font-black text-muted uppercase tracking-widest">Situação</th>
                    <th class="px-8 py-4 text-right text-[10px] font-black text-muted uppercase tracking-widest">Volume</th>
                    <th class="px-8 py-4 text-right text-[10px] font-black text-muted uppercase tracking-widest">Cronologia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                @forelse($pedidosRecentes as $pedido)
                <tr class="hover:bg-white/5 transition-all group">
                    <td class="px-8 py-5">
                        @if($pedido->is_pdv)
                            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase tracking-tighter border border-emerald-500/20">Venda Direta</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[9px] font-black uppercase tracking-tighter border border-primary/20">Pedido Web</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-sm font-black text-primary group-hover:translate-x-1 transition-transform">
                        <a href="{{ route('orders.show', $pedido->id) }}">#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</a>
                    </td>
                    <td class="px-8 py-5 text-sm font-medium text-white">
                        {{ $pedido->client ? $pedido->client->name : 'Consumidor Final' }}
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest" 
                              style="background-color: {{ $pedido->status->color }}15; color: {{ $pedido->status->color }}; border: 1px solid {{ $pedido->status->color }}30">
                            {{ $pedido->status->name }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-sm font-black text-adaptive text-right">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                    <td class="px-8 py-5 text-right font-bold text-muted text-[10px]">{{ $pedido->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-muted font-medium italic">Nenhuma movimentação detectada recentemente.</td>
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
        $pedidosPorStatusCollection = is_array($pedidosPorStatus) ? collect($pedidosPorStatus) : $pedidosPorStatus;
        $statusData = $pedidosPorStatusCollection->map(function($item) {
            $status = is_object($item) ? ($item->status ?? $item['status'] ?? 'Sem Status') : ($item['status'] ?? 'Sem Status');
            $color = is_object($item) ? ($item->color ?? $item['color'] ?? '#9ca3af') : ($item['color'] ?? '#9ca3af');
            $total = is_object($item) ? (int)($item->total ?? $item['total'] ?? 0) : (int)($item['total'] ?? 0);
            return ['status' => $status, 'color' => $color, 'total' => $total];
        })->toArray();
        
        $faturamentoData = $faturamentoDiario->map(function($item) {
            $dia = is_object($item) ? ($item->dia ?? '') : ($item['dia'] ?? '');
            $total = is_object($item) ? (float)($item->total ?? 0) : (float)($item['total'] ?? 0);
            return ['dia' => $dia, 'total' => $total];
        })->toArray();
        
        $faturamentoMensalData = $pedidosPorMes->map(function($item) {
            $mes = is_object($item) ? ($item->mes ?? '') : ($item['mes'] ?? '');
            $faturamento = is_object($item) ? (float)($item->faturamento ?? 0) : (float)($item['faturamento'] ?? 0);
            return ['mes' => $mes, 'total' => $faturamento];
        })->toArray();
        
        $faturamentoLojaData = isset($faturamentoPorLoja) ? $faturamentoPorLoja->map(function($item) {
            return ['name' => $item['name'] ?? '', 'total' => (float)($item['total_faturamento'] ?? 0)];
        })->toArray() : [];
        
        $pagamentoData = isset($distribuicaoPagamento) ? $distribuicaoPagamento->map(function($item) {
            return ['method' => $item['method'] ?? '', 'total' => (float)($item['total'] ?? 0)];
        })->toArray() : [];
    @endphp
    
    const dashboardData = {
        statusData: @json($statusData ?? []),
        faturamentoData: @json($faturamentoData ?? []),
        faturamentoMensalData: @json($faturamentoMensalData ?? []),
        faturamentoLojaData: @json($faturamentoLojaData ?? []),
        pagamentoData: @json($pagamentoData ?? [])
    };
    
    function initCharts() {
        if (typeof Chart === 'undefined') return;
        
        const isDark = document.documentElement.classList.contains('dark');
        Chart.defaults.font.family = "'Inter', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', sans-serif";
        const textColor = isDark ? '#a1a1aa' : '#1e293b';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
        const borderColor = isDark ? '#030303' : '#ffffff';
        const primaryColor = '#7c3aed';
        
        // Cleanup existing instances
        [window.statusChart, window.faturamentoChart, window.faturamentoMensalChart, window.faturamentoLojaChart, window.pagamentoChart].forEach(chart => {
            if (chart && typeof chart.destroy === 'function') chart.destroy();
        });
        
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor, usePointStyle: true, padding: 25, font: { family: "'Inter', 'Segoe UI', 'Roboto', sans-serif", size: 12, weight: '700' } }
                },
                tooltip: {
                    backgroundColor: isDark ? '#050505' : '#ffffff',
                    titleColor: isDark ? '#ffffff' : '#0f172a',
                    bodyColor: isDark ? '#a1a1aa' : '#64748b',
                    titleFont: { family: "'Inter', 'Segoe UI', 'Roboto', sans-serif", weight: '800', size: 14 },
                    bodyFont: { family: "'Inter', 'Segoe UI', 'Roboto', sans-serif", weight: '700', size: 13 },
                    padding: 16,
                    cornerRadius: 16,
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    displayColors: true,
                    boxPadding: 8
                }
            }
        };

        // Status Chart (Doughnut)
        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            const validData = dashboardData.statusData.filter(i => i.total > 0);
            if (validData.length > 0) {
                window.statusChart = new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: validData.map(i => i.status),
                        datasets: [{
                            data: validData.map(i => i.total),
                            backgroundColor: validData.map(i => i.color),
                            hoverBackgroundColor: validData.map(i => i.color),
                            borderWidth: 0,
                            hoverOffset: 0,
                            spacing: 0
                        }]
                    },
                    options: {
                        ...commonOptions,
                        cutout: '80%',
                        plugins: { ...commonOptions.plugins, legend: { ...commonOptions.plugins.legend, position: 'right' } }
                    }
                });
            }
        }

        // Revenue Trend Chart (Area)
        const fatCanvas = document.getElementById('faturamentoChart');
        if (fatCanvas && dashboardData.faturamentoData.length > 0) {
            const ctx = fatCanvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, 'rgba(124, 58, 237, 0.4)');
            gradient.addColorStop(1, 'rgba(124, 58, 237, 0)');

            window.faturamentoChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dashboardData.faturamentoData.map(i => {
                        const d = i.dia.split(/[-/]/);
                        return d.length >= 3 ? `${d[2]}/${d[1]}` : '';
                    }),
                    datasets: [{
                        label: 'Faturamento',
                        data: dashboardData.faturamentoData.map(i => i.total),
                        borderColor: primaryColor,
                        borderWidth: 4,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 0,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: primaryColor,
                        pointHoverBorderColor: primaryColor,
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { ...commonOptions.plugins, legend: { display: false } },
                    interaction: { intersect: false, mode: 'index' },
                    scales: {
                        y: { 
                            grid: { color: gridColor, drawBorder: false }, 
                            ticks: { color: textColor, font: { family: 'Inter', size: 10, weight: '700' }, callback: v => v > 0 ? 'R$ ' + v.toLocaleString('pt-BR') : '' }
                        },
                        x: { grid: { display: false }, ticks: { color: textColor, font: { family: 'Inter', size: 10, weight: '700' } } }
                    }
                }
            });
        }

        // Payments Distribution
        const pagCanvas = document.getElementById('pagamentoChart');
        if (pagCanvas && dashboardData.pagamentoData.some(i => i.total > 0)) {
            window.pagamentoChart = new Chart(pagCanvas, {
                type: 'doughnut',
                data: {
                    labels: dashboardData.pagamentoData.map(i => i.method.toUpperCase()),
                    datasets: [{
                        data: dashboardData.pagamentoData.map(i => i.total),
                        backgroundColor: ['#7c3aed', '#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                        hoverBackgroundColor: ['#7c3aed', '#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 0,
                        spacing: 0
                    }]
                },
                options: { ...commonOptions, cutout: '75%' }
            });
        }

        // Performance by Unit or Monthly Evolution
        const lojaCanvas = document.getElementById('faturamentoLojaChart') || document.getElementById('faturamentoMensalChart');
        if (lojaCanvas) {
            const isLoja = lojaCanvas.id === 'faturamentoLojaChart';
            const data = isLoja ? dashboardData.faturamentoLojaData : dashboardData.faturamentoMensalData;
            
            if (data.length > 0) {
                window.lojaChart = new Chart(lojaCanvas, {
                    type: isLoja ? 'bar' : 'line',
                    data: {
                        labels: data.map(i => isLoja ? i.name : i.mes),
                        datasets: [{
                            label: 'Faturamento',
                            data: data.map(i => i.total),
                            backgroundColor: isLoja ? primaryColor : '#10b981',
                            hoverBackgroundColor: isLoja ? primaryColor : '#10b981',
                            borderColor: isLoja ? primaryColor : '#10b981',
                            borderWidth: isLoja ? 0 : 4,
                            borderRadius: 12,
                            tension: 0.4,
                            fill: !isLoja
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: { ...commonOptions.plugins, legend: { display: false } },
                        scales: {
                            y: { grid: { color: gridColor }, ticks: { color: textColor, font: { family: 'Inter', size: 10, weight: '700' } } },
                            x: { grid: { display: false }, ticks: { color: textColor, font: { family: 'Inter', size: 10, weight: '700' } } }
                        }
                    }
                });
            }
        }
    }
    
    function waitAndInit() {
        if (typeof Chart === 'undefined') {
            setTimeout(waitAndInit, 250);
            return;
        }
        initCharts();
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitAndInit);
    } else {
        waitAndInit();
    }
    
    document.addEventListener('ajax-content-loaded', () => setTimeout(initCharts, 250));
})();
</script>
@endpush


