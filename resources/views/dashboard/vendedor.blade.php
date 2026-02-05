@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Meu Dashboard</h1>
    
    <!-- Filtros e Ações -->
    <div class="flex items-center gap-4">
        <!-- Filtro de Período -->
        <form method="GET" action="{{ route('dashboard') }}" id="periodFilterForm" class="flex items-center gap-2">
            <label for="period" class="text-sm font-medium text-gray-700 dark:text-gray-300">Período:</label>
            <select name="period" id="period" 
                    onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="today" {{ ($period ?? 'month') === 'today' ? 'selected' : '' }}>Hoje</option>
                <option value="week" {{ ($period ?? 'month') === 'week' ? 'selected' : '' }}>Esta Semana</option>
                <option value="month" {{ ($period ?? 'month') === 'month' ? 'selected' : '' }}>Este Mês</option>
                <option value="year" {{ ($period ?? 'month') === 'year' ? 'selected' : '' }}>Este Ano</option>
                <option value="custom" {{ ($period ?? 'month') === 'custom' ? 'selected' : '' }}>Personalizado</option>
            </select>
            
            @if(($period ?? 'month') === 'custom')
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') ?? '' }}" 
                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') ?? '' }}" 
                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
            @endif
        </form>
        
        <!-- Ações Rápidas -->
        <div class="flex gap-2">
            <a href="{{ route('pdv.index') }}" 
               class="px-4 py-2 bg-green-600 !text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                Abrir PDV
            </a>
            <a href="{{ route('orders.wizard.start') }}" 
               class="px-4 py-2 bg-indigo-600 !text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                Novo Pedido
            </a>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas Principais -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Meus Pedidos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Meus Pedidos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalPedidos }}</p>
                @if(isset($variacaoPedidos))
                <p class="text-xs mt-2 {{ $variacaoPedidos >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $variacaoPedidos >= 0 ? '+' : '' }}{{ number_format($variacaoPedidos, 1) }}% vs período anterior
                </p>
                @endif
            </div>
            <div class="bg-indigo-100 dark:bg-indigo-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Meu Faturamento -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Meu Faturamento</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($totalFaturamento, 2, ',', '.') }}</p>
                @if(isset($variacaoFaturamento))
                <p class="text-xs mt-2 {{ $variacaoFaturamento >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $variacaoFaturamento >= 0 ? '+' : '' }}{{ number_format($variacaoFaturamento, 1) }}% vs período anterior
                </p>
                @endif
            </div>
            <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Ticket Médio -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Ticket Médio</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($ticketMedio ?? 0, 2, ',', '.') }}</p>
                @if(isset($variacaoTicketMedio))
                <p class="text-xs mt-2 {{ $variacaoTicketMedio >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $variacaoTicketMedio >= 0 ? '+' : '' }}{{ number_format($variacaoTicketMedio, 1) }}% vs período anterior
                </p>
                @endif
            </div>
            <div class="bg-purple-100 dark:bg-purple-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Clientes Atendidos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Clientes Atendidos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $clientesAtendidos ?? 0 }}</p>
            </div>
            <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Cards Secundários -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Pedidos Hoje -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pedidos Hoje</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pedidosHoje }}</p>
            </div>
            <div class="bg-orange-100 dark:bg-orange-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Vendas PDV -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Vendas PDV</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $vendasPDV ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">R$ {{ number_format($vendasPDVValor ?? 0, 2, ',', '.') }}</p>
            </div>
            <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pedidos Online -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pedidos Online</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pedidosOnline ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">R$ {{ number_format($pedidosOnlineValor ?? 0, 2, ',', '.') }}</p>
            </div>
            <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pagamentos Pendentes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pagamentos Pendentes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pagamentosPendentes->count() }}</p>
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Total: R$ {{ number_format($totalPendente, 2, ',', '.') }}</p>
            </div>
            <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Minhas Vendas Mensais -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Minhas Vendas Mensais (Últimos 12 Meses)</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="vendasMensaisChart"></canvas>
        </div>
    </div>

    <!-- Meus Pedidos por Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Meus Pedidos por Status</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<!-- Gráficos Adicionais -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Faturamento Diário -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento Diário (Últimos 30 Dias)</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoChart"></canvas>
        </div>
    </div>

    <!-- Vendas PDV vs Pedidos Online -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">PDV vs Online</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="pdvVsOnlineChart"></canvas>
        </div>
    </div>
</div>

<!-- Tabelas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top 5 Meus Clientes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top 5 Meus Clientes</h2>
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
</div>

<!-- Próximas Entregas -->
@php
    use App\Models\Order;
    use Carbon\Carbon;
    $proximasEntregas = Order::where('is_draft', false)
        ->where('user_id', Auth::id())
        ->whereBetween('delivery_date', [Carbon::today(), Carbon::today()->addDays(7)])
        ->with(['client', 'status'])
        ->orderBy('delivery_date', 'asc')
        ->limit(10)
        ->get();
@endphp

@if($proximasEntregas->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Próximas Entregas (Próximos 7 dias)</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data de Entrega</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($proximasEntregas as $pedido)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                        <a href="{{ route('orders.show', $pedido->id) }}" 
                           class="hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline">
                            #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $pedido->client ? $pedido->client->name : 'Sem cliente' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $pedido->delivery_date ? $pedido->delivery_date->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                              style="background-color: {{ $pedido->status->color }}20; color: {{ $pedido->status->color }}">
                            {{ $pedido->status->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Meus Pedidos Recentes -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Meus Pedidos Recentes</h2>
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
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum pedido encontrado</td>
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
        
        $vendasMensaisData = $pedidosPorMes->map(function($item) {
            return [
                'mes' => $item->mes ?? '',
                'total' => (float)($item->faturamento ?? 0)
            ];
        })->toArray();
        
        $pdvVsOnlineData = [
            ['label' => 'PDV', 'value' => $vendasPDVValor ?? 0],
            ['label' => 'Online', 'value' => $pedidosOnlineValor ?? 0]
        ];
    @endphp
    
    const dashboardData = {
        statusData: @json($statusData),
        faturamentoData: @json($faturamentoData),
        vendasMensaisData: @json($vendasMensaisData),
        pdvVsOnlineData: @json($pdvVsOnlineData)
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
        
        // Destruir gráficos existentes (evitar conflito com IDs globais do DOM)
        const safeDestroy = (chart) => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        };
        safeDestroy(window.statusChart);
        safeDestroy(window.faturamentoChart);
        safeDestroy(window.vendasMensaisChart);
        safeDestroy(window.pdvVsOnlineChart);
        
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
        
        // Gráfico de Vendas Mensais
        const vendasMensaisCanvas = document.getElementById('vendasMensaisChart');
        if (vendasMensaisCanvas) {
            const vendasMensaisData = dashboardData.vendasMensaisData || [];
            const vendasMensaisLabels = vendasMensaisData.map(item => {
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
            const vendasMensaisValues = vendasMensaisData.map(item => parseFloat(item.total || 0));
            
            window.vendasMensaisChart = new Chart(vendasMensaisCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: vendasMensaisLabels.length > 0 ? vendasMensaisLabels : ['Sem dados'],
                    datasets: [{
                        label: 'Faturamento (R$)',
                        data: vendasMensaisValues.length > 0 ? vendasMensaisValues : [0],
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
        
        return true;
    }
    function ensureChartReady(callback) {
        if (typeof callback !== 'function') return;
        if (typeof window.ensureChartJsLoaded === 'function') {
            window.ensureChartJsLoaded(callback);
            return;
        }
        if (typeof Chart !== 'undefined') {
            callback();
            return;
        }
        setTimeout(function() {
            ensureChartReady(callback);
        }, 50);
    }

    function scheduleInit() {
        ensureChartReady(function() {
            setTimeout(function() {
                initCharts();
            }, 100);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleInit);
    } else {
        scheduleInit();
    }

    document.addEventListener('ajax-content-loaded', function() {
        scheduleInit();
    });

    window.addEventListener('dark-mode-toggled', function() {
        scheduleInit();
    });

    window.addEventListener('load', function() {
        setTimeout(function() {
            if (!window.statusChart) {
                scheduleInit();
            }
        }, 500);
    });
})();
</script>
@endpush

