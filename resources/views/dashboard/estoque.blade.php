@extends('layouts.admin')

@section('content')
<style>
    /* Evita borda global em bolhas de ícone (regra do tema pega .rounded-full + .bg-*) */
    .estoque-icon-badge {
        border-radius: 9999px;
        padding: 0.75rem;
        border: 0 !important;
        box-shadow: none !important;
        outline: 0 !important;
    }
    .estoque-icon-badge svg {
        display: block;
    }
    .estoque-icon-blue { background-color: #dbeafe; }
    .estoque-icon-green { background-color: #dcfce7; }
    .estoque-icon-red { background-color: #fee2e2; }
    .estoque-icon-orange { background-color: #ffedd5; }
    .estoque-icon-yellow { background-color: #fef3c7; }
    .estoque-icon-indigo { background-color: #e0e7ff; }
    .dark .estoque-icon-blue { background-color: rgba(30, 58, 138, 0.3); }
    .dark .estoque-icon-green { background-color: rgba(20, 83, 45, 0.3); }
    .dark .estoque-icon-red { background-color: rgba(127, 29, 29, 0.3); }
    .dark .estoque-icon-orange { background-color: rgba(154, 52, 18, 0.3); }
    .dark .estoque-icon-yellow { background-color: rgba(113, 63, 18, 0.3); }
    .dark .estoque-icon-indigo { background-color: rgba(55, 48, 163, 0.3); }
</style>
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Dashboard de Estoque</h1>
    
    @if(isset($stores) && $stores->count() > 0)
    <div class="flex items-center gap-4">
        <label for="store_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Loja:</label>
        <form method="GET" action="{{ route('stocks.dashboard') }}" id="storeFilterForm" class="inline">
            <select name="store_id" id="store_filter" 
                    onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas as Lojas</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ (isset($selectedStoreId) && $selectedStoreId == $store->id) ? 'selected' : '' }}>
                        {{ $store->name }}@if($store->isMain()) (Principal)@endif
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif
</div>

<!-- Cards de Estatísticas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total de Itens em Estoque -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total de Itens</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalItensEstoque, 0, ',', '.') }}</p>
                <p class="text-xs text-blue-600 mt-2">{{ number_format($totalDisponivel, 0, ',', '.') }} disponíveis</p>
            </div>
            <div class="estoque-icon-badge estoque-icon-blue">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total de Quantidade -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Quantidade Total</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalQuantidade, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ number_format($totalReservado, 0, ',', '.') }} reservados</p>
            </div>
            <div class="estoque-icon-badge estoque-icon-green">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Estoque Baixo -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Estoque Baixo</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($estoqueBaixo, 0, ',', '.') }}</p>
                <p class="text-xs text-red-600 mt-2">Abaixo do mínimo</p>
            </div>
            <div class="estoque-icon-badge estoque-icon-red">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Solicitações Pendentes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Solicitações Pendentes</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($solicitacoesPendentes, 0, ',', '.') }}</p>
                <p class="text-xs text-orange-600 mt-2">{{ $solicitacoesHoje }} hoje</p>
            </div>
            <div class="estoque-icon-badge estoque-icon-orange">
                <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Cards Secundários -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Solicitações Aprovadas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Aprovadas</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($solicitacoesAprovadas, 0, ',', '.') }}</p>
            </div>
            <div class="estoque-icon-badge estoque-icon-green">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Em Transferência -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Em Transferência</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($solicitacoesEmTransferencia, 0, ',', '.') }}</p>
            </div>
            <div class="estoque-icon-badge estoque-icon-yellow">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Disponível -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Disponível</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalDisponivel, 0, ',', '.') }}</p>
            </div>
            <div class="estoque-icon-badge estoque-icon-indigo">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos e Tabelas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Solicitações por Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Solicitações por Status</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Movimentações por Dia -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Movimentações (Últimos 30 dias)</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="movimentacoesChart"></canvas>
        </div>
    </div>
</div>

<!-- Tabelas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Estoque por Loja -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Estoque por Loja</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Loja</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Itens</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Disponível</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($estoquePorLoja as $loja)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $loja->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ number_format($loja->total_itens, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ number_format($loja->total_quantidade, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">{{ number_format($loja->total_disponivel, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">Nenhum estoque encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Produtos Mais Solicitados -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Produtos Mais Solicitados (30 dias)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tamanho</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantidade</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($produtosMaisSolicitados as $produto)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            {{ $produto->fabric->name ?? 'N/A' }} - {{ $produto->color->name ?? 'N/A' }}<br>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $produto->cutType->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $produto->size }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ number_format($produto->total_solicitado, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">Nenhuma solicitação encontrada</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Solicitações Recentes -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Solicitações Recentes</h2>
        <a href="{{ route('stock-requests.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Ver todas</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Loja Origem</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Loja Destino</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantidade</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($solicitacoesRecentes as $solicitacao)
                <tr>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">#{{ str_pad($solicitacao->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $solicitacao->fabric->name ?? 'N/A' }} - {{ $solicitacao->color->name ?? 'N/A' }}<br>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $solicitacao->cutType->name ?? 'N/A' }} - {{ $solicitacao->size }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $solicitacao->requestingStore->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $solicitacao->targetStore->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ number_format($solicitacao->requested_quantity, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $statusColors = [
                                'pendente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'aprovado' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'rejeitado' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'em_transferencia' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'concluido' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'cancelado' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            ];
                            $statusLabels = [
                                'pendente' => 'Pendente',
                                'aprovado' => 'Aprovado',
                                'rejeitado' => 'Rejeitado',
                                'em_transferencia' => 'Em Transferência',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                            ];
                            $color = $statusColors[$solicitacao->status] ?? 'bg-gray-100 text-gray-800';
                            $label = $statusLabels[$solicitacao->status] ?? ucfirst($solicitacao->status);
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">{{ $label }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $solicitacao->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">Nenhuma solicitação encontrada</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Movimentações Recentes -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Movimentações Recentes (24h)</h2>
        <a href="{{ route('stock-history.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Ver histórico completo</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data/Hora</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ação</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantidade</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuário</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($movimentacoesRecentes as $movimentacao)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $movimentacao->action_date->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $actionLabels = [
                                'create' => 'Criação',
                                'update' => 'Atualização',
                                'reserve' => 'Reserva',
                                'release' => 'Liberação',
                                'use' => 'Uso',
                                'add' => 'Adição',
                                'transfer' => 'Transferência',
                            ];
                            $label = $actionLabels[$movimentacao->action_type] ?? ucfirst($movimentacao->action_type);
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">{{ $label }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        @if($movimentacao->stock)
                            {{ $movimentacao->stock->fabric->name ?? 'N/A' }} - {{ $movimentacao->stock->color->name ?? 'N/A' }}<br>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $movimentacao->stock->cutType->name ?? 'N/A' }} - {{ $movimentacao->size }}</span>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold {{ $movimentacao->quantity_change > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $movimentacao->quantity_change > 0 ? '+' : '' }}{{ number_format($movimentacao->quantity_change, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $movimentacao->user->name ?? 'Sistema' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">Nenhuma movimentação encontrada</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Solicitações por Status
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($solicitacoesPorStatus);
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(key => {
                const labels = {
                    'pendente': 'Pendente',
                    'aprovado': 'Aprovado',
                    'rejeitado': 'Rejeitado',
                    'em_transferencia': 'Em Transferência',
                    'concluido': 'Concluído',
                    'cancelado': 'Cancelado'
                };
                return labels[key] || key;
            }),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    '#FCD34D',
                    '#10B981',
                    '#EF4444',
                    '#3B82F6',
                    '#6B7280',
                    '#DC2626'
                ],
                borderWidth: 0,
                hoverBorderWidth: 0,
                borderColor: 'transparent',
                hoverBorderColor: 'transparent'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de Movimentações por Dia
    const movimentacoesCtx = document.getElementById('movimentacoesChart').getContext('2d');
    const movimentacoesData = @json($movimentacoesPorDia);
    
    new Chart(movimentacoesCtx, {
        type: 'line',
        data: {
            labels: movimentacoesData.map(item => {
                const date = new Date(item.dia);
                return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            }),
            datasets: [{
                label: 'Movimentações',
                data: movimentacoesData.map(item => item.total_movimentacoes),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection

