@extends('layouts.admin')

@section('content')
<style>
    .cash-col {
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: #ffffff;
        overflow: hidden;
    }

    .dark .cash-col {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(2, 6, 23, 0.98));
        border-color: rgba(148, 163, 184, 0.2);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    .cash-col-header {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.22);
    }

    .dark .cash-col-header {
        border-bottom-color: rgba(148, 163, 184, 0.16);
    }

    .cash-col-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 700;
        background: rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .dark .cash-col-count {
        background: rgba(15, 23, 42, 0.8);
        color: #e2e8f0;
    }

    .cash-col-dropzone {
        padding: 0.75rem;
        display: grid;
        gap: 0.75rem;
        min-height: 24rem;
        max-height: 37.5rem;
        overflow-y: auto;
        transition: background-color 0.18s ease, box-shadow 0.18s ease;
    }

    .cash-col-dropzone.cash-drop-active {
        background: rgba(99, 102, 241, 0.08);
        box-shadow: inset 0 0 0 2px rgba(99, 102, 241, 0.35);
    }

    .dark .cash-col-dropzone.cash-drop-active {
        background: rgba(99, 102, 241, 0.14);
        box-shadow: inset 0 0 0 2px rgba(129, 140, 248, 0.5);
    }

    .cash-kanban-card.cash-card-dragging {
        opacity: 0.45;
        transform: scale(0.985);
    }

    .cash-col--pendente .cash-col-header {
        background: linear-gradient(90deg, rgba(251, 146, 60, 0.22), rgba(251, 191, 36, 0.16));
    }

    .cash-col--confirmado .cash-col-header {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.2), rgba(14, 165, 233, 0.14));
    }

    .cash-col--cancelado .cash-col-header {
        background: linear-gradient(90deg, rgba(248, 113, 113, 0.22), rgba(251, 146, 60, 0.12));
    }

    .cash-col--sangria .cash-col-header {
        background: linear-gradient(90deg, rgba(168, 85, 247, 0.22), rgba(99, 102, 241, 0.14));
    }

    .dark .cash-col--pendente .cash-col-header {
        background: linear-gradient(90deg, rgba(194, 65, 12, 0.4), rgba(146, 64, 14, 0.22));
    }

    .dark .cash-col--confirmado .cash-col-header {
        background: linear-gradient(90deg, rgba(67, 56, 202, 0.38), rgba(14, 116, 144, 0.25));
    }

    .dark .cash-col--cancelado .cash-col-header {
        background: linear-gradient(90deg, rgba(185, 28, 28, 0.42), rgba(120, 53, 15, 0.22));
    }

    .dark .cash-col--sangria .cash-col-header {
        background: linear-gradient(90deg, rgba(126, 34, 206, 0.4), rgba(67, 56, 202, 0.22));
    }

    .cash-kanban-card {
        border-color: rgba(148, 163, 184, 0.24);
        background: linear-gradient(180deg, #ffffff, #f8fafc);
    }

    .cash-kanban-card:hover {
        border-color: rgba(99, 102, 241, 0.35);
    }

    .dark .cash-kanban-card {
        background: linear-gradient(180deg, rgba(2, 6, 23, 0.94), rgba(15, 23, 42, 0.92));
        border-color: rgba(71, 85, 105, 0.45);
    }

    .dark .cash-kanban-card:hover {
        border-color: rgba(129, 140, 248, 0.55);
    }

    .cash-view-btn {
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: rgba(248, 250, 252, 0.95);
        color: #334155;
    }

    .dark .cash-view-btn {
        border-color: rgba(71, 85, 105, 0.6);
        background: rgba(15, 23, 42, 0.75);
        color: #cbd5e1;
    }

    .cash-view-btn.is-active {
        border-color: transparent;
        background: linear-gradient(90deg, #7c3aed, #4f46e5);
        color: #ffffff;
        box-shadow: 0 0 0 1px rgba(129, 140, 248, 0.25), 0 8px 18px rgba(79, 70, 229, 0.25);
    }
</style>
<div class="max-w-[1800px] mx-auto">
    <!-- Cabeçalho com ações principais -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold dark:text-gray-100">Controle de Caixa</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie transações e visualize relatórios</p>
        </div>
        <div class="flex gap-3">
            <button id="btn-report-simplified" onclick="openReportSimplified()"
                    class="px-4 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Relatório Simplificado
            </button>
            <button id="btn-report-detailed" onclick="openReportDetailed()"
                    class="px-4 py-2 bg-blue-600 dark:bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Relatório Detalhado
            </button>
            <a href="{{ route('cash.create') }}" 
               class="px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Transação
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Cards de Resumo Simplificado (Apenas informações essenciais) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Saldo Atual (Confirmado) - Mais importante -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 border-l-4 border-blue-500 dark:border-blue-600">
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1"> Saldo Atual</p>
            <p class="text-3xl font-bold {{ $saldoAtual >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                R$ {{ number_format($saldoAtual, 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dinheiro em caixa</p>
        </div>

        <!-- Total Entradas do Período -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 border-l-4 border-green-500 dark:border-green-600">
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1"> Entradas (Período)</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                R$ {{ number_format($totalEntradas, 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Período filtrado</p>
        </div>

        <!-- Total Saídas do Período -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 border-l-4 border-red-500 dark:border-red-600">
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1"> Saídas (Período)</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400">
                R$ {{ number_format($totalSaidas, 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Período filtrado</p>
        </div>
    </div>

    <!-- Filtros Compactos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4 mb-6">
        <form method="GET" action="{{ route('cash.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Inicial</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Data Final</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                <select name="type" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="entrada" {{ $type === 'entrada' ? 'selected' : '' }}>Entradas</option>
                    <option value="saida" {{ $type === 'saida' ? 'selected' : '' }}>Saídas</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition text-sm">
                    Filtrar
                </button>
                <a href="{{ route('cash.index') }}" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-center transition text-sm">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Visualizacao em Grid (ate 8 notas por linha) -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold dark:text-gray-100">Transações Confirmadas</h2>
            <div class="flex gap-2">
                <button onclick="toggleView('grid')" id="btn-grid" class="cash-view-btn is-active px-3 py-1.5 rounded-md text-sm transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Grid
                </button>
                <button onclick="toggleView('kanban')" id="btn-kanban" class="cash-view-btn px-3 py-1.5 rounded-md text-sm transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                    </svg>
                    Kanban
                </button>
            </div>
        </div>

        <!-- Visualização em Grid -->
        <div id="view-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-4">
            @forelse($confirmadas->take(8) as $transaction)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition overflow-hidden">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-semibold px-2 py-1 rounded {{ $transaction->type === 'entrada' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                            {{ $transaction->type === 'entrada' ? '↑ Entrada' : '↓ Saída' }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->transaction_date->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ $transaction->category }}</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $transaction->description }}</p>
                    
                    <!-- Meios de Pagamento -->
                    @php
                        $paymentMethods = $transaction->payment_methods ?? [];

                        // Se vier como string (ex: JSON ou texto simples), tentar decodificar
                        if (!is_array($paymentMethods) && !empty($paymentMethods)) {
                            $decoded = json_decode($paymentMethods, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $paymentMethods = $decoded;
                            } else {
                                // Qualquer coisa não decodificável vira array vazio para evitar foreach em string
                                $paymentMethods = [];
                            }
                        }

                        // Fallback: se ainda estiver vazio mas houver payment_method/amount simples, cria array único
                        if ((empty($paymentMethods) || !is_array($paymentMethods)) && $transaction->payment_method) {
                            $paymentMethods = [[
                                'method' => $transaction->payment_method,
                                'amount' => $transaction->amount,
                            ]];
                        }
                    @endphp
                    <div class="mb-3 space-y-1">
                        @foreach($paymentMethods as $method)
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600 dark:text-gray-400 capitalize">{{ $method['method'] ?? $transaction->payment_method }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">R$ {{ number_format($method['amount'] ?? $transaction->amount, 2, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                        <span class="text-lg font-bold {{ $transaction->type === 'entrada' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $transaction->type === 'entrada' ? '+' : '-' }} R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </span>
                        @if($transaction->order_id)
                        <a href="{{ route('orders.show', $transaction->order_id) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            Pedido #{{ str_pad($transaction->order_id, 6, '0', STR_PAD_LEFT) }}
                        </a>
                        @endif
                    </div>
                    
                    <div class="mt-2 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                        <span>Vendedor: {{ $transaction->user_name ?? 'Sistema' }}</span>
                        <a href="{{ route('cash.edit', $transaction) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">Nenhuma transação confirmada no período</p>
                @if(isset($pendentesVenda) && $pendentesVenda->count() > 0)
                    <p class="text-amber-600 dark:text-amber-400 text-sm mt-3">
                        Existem {{ $pendentesVenda->count() }} venda(s) pendente(s) aguardando aprovação no PDV.
                    </p>
                    <a href="{{ route('cash.approvals.index') }}"
                       class="inline-flex items-center gap-2 mt-3 px-3 py-2 rounded-md bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                        Ir para Aprovações PDV
                    </a>
                @elseif(isset($confirmadasTotal) && $confirmadasTotal > 0)
                    <p class="text-indigo-600 dark:text-indigo-400 text-sm mt-3">
                        Existem {{ $confirmadasTotal }} transação(ões) confirmada(s) fora do período filtrado.
                    </p>
                    <a href="{{ route('cash.index') }}"
                       class="inline-flex items-center gap-2 mt-3 px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                        Limpar filtros
                    </a>
                @elseif(isset($pendentesVendaTotal) && $pendentesVendaTotal > 0)
                    <p class="text-amber-600 dark:text-amber-400 text-sm mt-3">
                        Existem {{ $pendentesVendaTotal }} venda(s) pendente(s) para aprovação no PDV.
                    </p>
                    <a href="{{ route('cash.approvals.index') }}"
                       class="inline-flex items-center gap-2 mt-3 px-3 py-2 rounded-md bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                        Ir para Aprovações PDV
                    </a>
                @endif
            </div>
            @endforelse
        </div>

        <!-- Visualização Kanban (original) -->
        <div id="view-kanban" class="hidden grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 overflow-x-auto pb-4">
            <div class="cash-col cash-col--pendente min-h-96 flex-shrink-0" style="min-width: 280px;">
                <div class="cash-col-header">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center justify-between">
                        <span>Pendente</span>
                        <span class="cash-col-count js-col-count" data-col-count="pendente">{{ $pendentes->count() }}</span>
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total: R$ {{ number_format($totalPendentes, 2, ',', '.') }}</p>
                </div>
                <div class="cash-col-dropzone js-kanban-dropzone" data-drop-status="pendente">
                    @forelse($pendentes as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                    @empty
                    <p class="js-empty-message text-center text-gray-500 dark:text-gray-400 text-sm py-8">Nenhuma transacao pendente</p>
                    @endforelse
                </div>
            </div>

            <div class="cash-col cash-col--confirmado min-h-96 flex-shrink-0" style="min-width: 280px;">
                <div class="cash-col-header">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center justify-between">
                        <span>Confirmado</span>
                        <span class="cash-col-count js-col-count" data-col-count="confirmado">{{ $confirmadas->count() }}</span>
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Saldo: R$ {{ number_format($totalConfirmadas, 2, ',', '.') }}</p>
                </div>
                <div class="cash-col-dropzone js-kanban-dropzone" data-drop-status="confirmado">
                    @forelse($confirmadas as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                    @empty
                    <p class="js-empty-message text-center text-gray-500 dark:text-gray-400 text-sm py-8">Nenhuma transacao confirmada</p>
                    @endforelse
                </div>
            </div>

            <div class="cash-col cash-col--cancelado min-h-96 flex-shrink-0" style="min-width: 280px;">
                <div class="cash-col-header">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center justify-between">
                        <span>Cancelado</span>
                        <span class="cash-col-count js-col-count" data-col-count="cancelado">{{ $canceladas->count() }}</span>
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total: R$ {{ number_format($totalCanceladas, 2, ',', '.') }}</p>
                </div>
                <div class="cash-col-dropzone js-kanban-dropzone" data-drop-status="cancelado">
                    @forelse($canceladas as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                    @empty
                    <p class="js-empty-message text-center text-gray-500 dark:text-gray-400 text-sm py-8">Nenhuma transacao cancelada</p>
                    @endforelse
                </div>
            </div>

            <div class="cash-col cash-col--sangria min-h-96 flex-shrink-0" style="min-width: 280px;">
                <div class="cash-col-header">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center justify-between">
                        <span>Sangria</span>
                        <span class="cash-col-count">{{ $sangrias->count() }}</span>
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total: R$ {{ number_format($totalSangrias, 2, ',', '.') }}</p>
                </div>
                <div class="cash-col-dropzone">
                    @forelse($sangrias as $transaction)
                    @include('cash.partials.transaction-card', ['transaction' => $transaction])
                    @empty
                    <p class="js-empty-message text-center text-gray-500 dark:text-gray-400 text-sm py-8">Nenhuma sangria registrada</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Relatório Simplificado -->
<div id="modal-report-simplified" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-full max-w-4xl shadow-lg dark:shadow-gray-900/25 rounded-md bg-white dark:bg-gray-800 mb-10">
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold dark:text-gray-100">Relatório Simplificado</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Resumo por período (dia, semana ou mês)</p>
            </div>
            <button id="btn-close-simplified" onclick="closeReportSimplified()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Período</label>
                <select id="report-simplified-period"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    <option value="day">Dia</option>
                    <option value="week">Semana</option>
                    <option value="month" selected>Mês</option>
                    <option value="year">Ano</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de referência</label>
                <input type="date" id="report-simplified-date" value="{{ $latestCashReferenceDate ?? date('Y-m-d') }}" 
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
            </div>
            <button id="btn-load-simplified" class="md:col-span-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm justify-self-start">
                Carregar Relatório
            </button>
        </div>
        
        <div id="report-simplified-content" class="space-y-4">
            <!-- Conteúdo será preenchido via JavaScript -->
        </div>
    </div>
</div>

<!-- Modal Relatório Detalhado -->
<div id="modal-report-detailed" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-full max-w-6xl shadow-lg dark:shadow-gray-900/25 rounded-md bg-white dark:bg-gray-800 mb-10">
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-semibold dark:text-gray-100">Relatório Detalhado</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Descrição completa de cada transação</p>
            </div>
            <button id="btn-close-detailed" onclick="closeReportDetailed()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Inicial</label>
                <input type="date" id="report-detailed-start" value="{{ $startDate }}" 
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Final</label>
                <input type="date" id="report-detailed-end" value="{{ $endDate }}" 
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
            </div>
            <div class="col-span-2">
                <button id="btn-load-detailed" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm">
                    Carregar Relatório
                </button>
            </div>
        </div>
        
        <div id="report-detailed-content" class="space-y-4 max-h-[600px] overflow-y-auto">
            <!-- Conteúdo será preenchido via JavaScript -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Definir funções globais primeiro
    window.openReportSimplified = function() {
        document.getElementById('modal-report-simplified').classList.remove('hidden');
        loadSimplifiedReport();
    };

    window.closeReportSimplified = function() {
        document.getElementById('modal-report-simplified').classList.add('hidden');
    };

    window.openReportDetailed = function() {
        document.getElementById('modal-report-detailed').classList.remove('hidden');
        loadDetailedReport();
    };

    window.closeReportDetailed = function() {
        document.getElementById('modal-report-detailed').classList.add('hidden');
    };

    function formatCurrencyBRL(value) {
        const number = Number(value);
        if (Number.isNaN(number)) {
            return 'R$ 0,00';
        }

        return `R$ ${number.toFixed(2).replace('.', ',')}`;
    }

    function formatDateBr(dateStr) {
        if (!dateStr) {
            return '';
        }

        const [year, month, day] = String(dateStr).split('-');
        if (!year || !month || !day) {
            return dateStr;
        }

        return `${day}/${month}/${year}`;
    }

    function buildSimplifiedPeriodLabel(periodInfo) {
        if (!periodInfo || !periodInfo.inicio || !periodInfo.fim) {
            return '';
        }

        const labels = {
            day: 'Dia',
            week: 'Semana',
            month: 'Mês',
            year: 'Ano',
            custom: 'Período',
        };
        const tipo = periodInfo.tipo || 'custom';
        const label = labels[tipo] || 'Período';
        return `${label}: ${formatDateBr(periodInfo.inicio)} até ${formatDateBr(periodInfo.fim)}`;
    }

    function loadSimplifiedReport() {
        const period = document.getElementById('report-simplified-period')?.value || 'month';
        const date = document.getElementById('report-simplified-date')?.value || "{{ $latestCashReferenceDate ?? date('Y-m-d') }}";
        const content = document.getElementById('report-simplified-content');
        const params = new URLSearchParams({ period, date });
        content.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400">Carregando...</p>';

        fetch(`{{ route('cash.report.simplified') }}?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const paymentRows = Object.entries(data.resumo?.por_meio_pagamento || {});
                    const commissionRows = Array.isArray(data.resumo?.comissoes_por_vendedor)
                        ? data.resumo.comissoes_por_vendedor
                        : [];
                    const periodLabel = buildSimplifiedPeriodLabel(data.periodo);

                    let html = `
                        ${periodLabel ? `<p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${periodLabel}</p>` : ''}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Entradas</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">${formatCurrencyBRL(data.resumo.total_entradas)}</p>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Saídas</p>
                                <p class="text-2xl font-bold text-red-600 dark:text-red-400">${formatCurrencyBRL(data.resumo.total_saidas)}</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Saldo</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${formatCurrencyBRL(data.resumo.saldo)}</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Produtos</p>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">${data.resumo.total_produtos}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="font-semibold dark:text-gray-100 mb-2">Por Meio de Pagamento</h3>
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-600">
                                            <th class="text-left py-2 text-gray-700 dark:text-gray-300">Meio</th>
                                            <th class="text-right py-2 text-gray-700 dark:text-gray-300">Entradas</th>
                                            <th class="text-right py-2 text-gray-700 dark:text-gray-300">Saídas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;

                    if (paymentRows.length === 0) {
                        html += `
                            <tr>
                                <td colspan="3" class="py-3 text-center text-gray-500 dark:text-gray-400">Sem movimentação no período selecionado</td>
                            </tr>
                        `;
                    } else {
                        paymentRows.forEach(([method, values]) => {
                            html += `
                                <tr class="border-b border-gray-200 dark:border-gray-600">
                                    <td class="py-2 capitalize text-gray-900 dark:text-gray-100">${String(method).replace(/_/g, ' ')}</td>
                                    <td class="py-2 text-right text-green-600 dark:text-green-400">${formatCurrencyBRL(values.entradas)}</td>
                                    <td class="py-2 text-right text-red-600 dark:text-red-400">${formatCurrencyBRL(values.saidas)}</td>
                                </tr>
                            `;
                        });
                    }

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="font-semibold dark:text-gray-100 mb-2">Comissões por Vendedor</h3>
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-600">
                                            <th class="text-left py-2 text-gray-700 dark:text-gray-300">Vendedor</th>
                                            <th class="text-right py-2 text-gray-700 dark:text-gray-300">Total</th>
                                            <th class="text-right py-2 text-gray-700 dark:text-gray-300">Transações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;

                    if (commissionRows.length === 0) {
                        html += `
                            <tr>
                                <td colspan="3" class="py-3 text-center text-gray-500 dark:text-gray-400">Sem comissões no período selecionado</td>
                            </tr>
                        `;
                    } else {
                        commissionRows.forEach(vendedor => {
                            html += `
                                <tr class="border-b border-gray-200 dark:border-gray-600">
                                    <td class="py-2 text-gray-900 dark:text-gray-100">${vendedor.nome}</td>
                                    <td class="py-2 text-right text-green-600 dark:text-green-400">${formatCurrencyBRL(vendedor.total)}</td>
                                    <td class="py-2 text-right text-gray-600 dark:text-gray-400">${vendedor.transacoes}</td>
                                </tr>
                            `;
                        });
                    }

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total de Descontos</p>
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">${formatCurrencyBRL(data.resumo.total_descontos)}</p>
                        </div>
                    `;

                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<p class="text-center text-red-500">Erro ao carregar relatório</p>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                content.innerHTML = `<p class="text-center text-red-500">Erro ao carregar relatório: ${error.message}</p>`;
            });
    }

    function loadDetailedReport() {
        const startDate = document.getElementById('report-detailed-start').value;
        const endDate = document.getElementById('report-detailed-end').value;
        const content = document.getElementById('report-detailed-content');
        content.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400">Carregando...</p>';

        fetch(`{{ route('cash.report.detailed') }}?start_date=${startDate}&end_date=${endDate}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    let html = `<p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Total de transações: <strong>${data.total_transacoes}</strong></p>`;

                    data.detalhes.forEach(transacao => {
                        html += `
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold dark:text-gray-100">${transacao.categoria}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">${transacao.descricao}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold ${transacao.tipo === 'entrada' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
                                            ${transacao.tipo === 'entrada' ? '+' : '-'} R$ ${parseFloat(transacao.valor).toFixed(2).replace('.', ',')}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">${transacao.data} ${transacao.hora}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1"><strong>Meios de Pagamento:</strong></p>
                                    <div class="space-y-1">
                        `;

                        // Garantir que meios_pagamento seja um array
                        const meiosPagamento = Array.isArray(transacao.meios_pagamento) ? transacao.meios_pagamento : [];
                        if (meiosPagamento.length === 0 && transacao.valor) {
                            // Se não houver array mas houver valor, criar array com método único
                            meiosPagamento.push({
                                method: transacao.tipo === 'entrada' ? 'dinheiro' : 'saida',
                                amount: transacao.valor
                            });
                        }
                        
                        meiosPagamento.forEach(method => {
                            html += `
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-600 dark:text-gray-400 capitalize">${(method.method || 'N/A').replace(/_/g, ' ')}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">R$ ${parseFloat(method.amount || 0).toFixed(2).replace('.', ',')}</span>
                                </div>
                            `;
                        });

                        html += `
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Vendedor: ${transacao.vendedor}</p>
                        `;

                        if (transacao.pedido) {
                            html += `
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Pedido #${transacao.pedido.numero}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Cliente: ${transacao.pedido.cliente}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Itens: ${transacao.pedido.itens.length}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Subtotal: R$ ${parseFloat(transacao.pedido.subtotal).toFixed(2).replace('.', ',')}</p>
                                    ${transacao.pedido.desconto > 0 ? `<p class="text-xs text-yellow-600 dark:text-yellow-400">Desconto: R$ ${parseFloat(transacao.pedido.desconto).toFixed(2).replace('.', ',')}</p>` : ''}
                                    <p class="text-xs font-semibold text-gray-900 dark:text-white">Total: R$ ${parseFloat(transacao.pedido.total).toFixed(2).replace('.', ',')}</p>
                                </div>
                            `;
                        }

                        if (transacao.observacoes) {
                            html += `<p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><strong>Observações:</strong> ${transacao.observacoes}</p>`;
                        }

                        html += `
                            </div>
                        </div>
                    `;

                    if (Number(data.resumo?.total_transacoes || 0) === 0) {
                        const ultimaData = data.ultima_data_com_movimento
                            ? formatDateBr(data.ultima_data_com_movimento)
                            : null;
                        html += `
                            <div class="mb-4 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-sm">
                                Sem movimentação confirmada nesse período.
                                ${ultimaData ? ` Última data com movimentação: <strong>${ultimaData}</strong>.` : ''}
                            </div>
                        `;
                    }
                    });

                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<p class="text-center text-red-500">Erro ao carregar relatório</p>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                content.innerHTML = `<p class="text-center text-red-500">Erro ao carregar relatório: ${error.message}</p>`;
            });
    }

    const cashStatusUrlTemplate = @json(route('cash.update-status', ['cash' => '__ID__']));
    const cashCsrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function getCashStatusUrl(transactionId) {
        return cashStatusUrlTemplate.replace('__ID__', encodeURIComponent(String(transactionId)));
    }

    function normalizeKanbanStatus(status) {
        const allowed = ['pendente', 'confirmado', 'cancelado'];
        const normalized = String(status || '').toLowerCase();
        return allowed.includes(normalized) ? normalized : null;
    }

    function updateColumnCount(status, delta) {
        const normalized = normalizeKanbanStatus(status);
        if (!normalized) {
            return;
        }

        const counter = document.querySelector(`.js-col-count[data-col-count="${normalized}"]`);
        if (!counter) {
            return;
        }

        const current = parseInt(counter.textContent?.trim() || '0', 10) || 0;
        counter.textContent = String(Math.max(0, current + delta));
    }

    function ensureDropzoneEmptyState(dropzone) {
        if (!dropzone) {
            return;
        }

        const status = normalizeKanbanStatus(dropzone.dataset.dropStatus);
        const cards = Array.from(dropzone.children).filter((child) => child.classList?.contains('cash-kanban-card'));
        const currentEmpty = dropzone.querySelector('.js-empty-message');

        if (cards.length > 0) {
            currentEmpty?.remove();
            return;
        }

        if (currentEmpty) {
            return;
        }

        const labels = {
            pendente: 'Nenhuma transacao pendente',
            confirmado: 'Nenhuma transacao confirmada',
            cancelado: 'Nenhuma transacao cancelada',
        };

        const emptyText = status ? (labels[status] || 'Nenhum item') : 'Nenhum item';
        const message = document.createElement('p');
        message.className = 'js-empty-message text-center text-gray-500 dark:text-gray-400 text-sm py-8';
        message.textContent = emptyText;
        dropzone.appendChild(message);
    }

    function setViewButtonState(view) {
        const btnGrid = document.getElementById('btn-grid');
        const btnKanban = document.getElementById('btn-kanban');
        if (!btnGrid || !btnKanban) {
            return;
        }

        btnGrid.classList.toggle('is-active', view === 'grid');
        btnKanban.classList.toggle('is-active', view === 'kanban');
    }

    function bindKanbanDragAndDrop() {
        const board = document.getElementById('view-kanban');
        if (!board) {
            return;
        }

        const dropzones = board.querySelectorAll('.js-kanban-dropzone[data-drop-status]');
        if (!dropzones.length) {
            return;
        }

        let draggedCard = null;
        let sourceDropzone = null;
        let sourceStatus = null;

        const clearDropHighlights = () => {
            dropzones.forEach((zone) => zone.classList.remove('cash-drop-active'));
        };

        const resetDragState = () => {
            if (draggedCard) {
                draggedCard.classList.remove('cash-card-dragging');
            }

            clearDropHighlights();
            draggedCard = null;
            sourceDropzone = null;
            sourceStatus = null;
        };

        const cards = board.querySelectorAll('.cash-kanban-card[draggable="true"]');
        cards.forEach((card) => {
            if (card.dataset.dragBound === '1') {
                return;
            }

            card.dataset.dragBound = '1';
            card.addEventListener('dragstart', (event) => {
                const cardStatus = normalizeKanbanStatus(card.dataset.status);
                const category = String(card.dataset.category || '').toLowerCase();

                if (!cardStatus || category === 'sangria' || card.dataset.updating === '1') {
                    event.preventDefault();
                    return;
                }

                draggedCard = card;
                sourceDropzone = card.closest('.js-kanban-dropzone');
                sourceStatus = cardStatus;

                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', card.dataset.transactionId || '');
                }

                requestAnimationFrame(() => card.classList.add('cash-card-dragging'));
            });

            card.addEventListener('dragend', () => {
                resetDragState();
            });
        });

        dropzones.forEach((zone) => {
            if (zone.dataset.dropBound === '1') {
                return;
            }

            zone.dataset.dropBound = '1';

            zone.addEventListener('dragover', (event) => {
                if (!draggedCard) {
                    return;
                }

                event.preventDefault();
                zone.classList.add('cash-drop-active');

                if (event.dataTransfer) {
                    event.dataTransfer.dropEffect = 'move';
                }
            });

            zone.addEventListener('dragleave', (event) => {
                if (zone.contains(event.relatedTarget)) {
                    return;
                }

                zone.classList.remove('cash-drop-active');
            });

            zone.addEventListener('drop', async (event) => {
                event.preventDefault();
                zone.classList.remove('cash-drop-active');

                if (!draggedCard || !sourceDropzone || !sourceStatus) {
                    resetDragState();
                    return;
                }

                const targetStatus = normalizeKanbanStatus(zone.dataset.dropStatus);
                const transactionId = draggedCard.dataset.transactionId;

                if (!targetStatus || !transactionId) {
                    resetDragState();
                    return;
                }

                if (targetStatus === sourceStatus) {
                    if (zone !== sourceDropzone) {
                        zone.appendChild(draggedCard);
                        ensureDropzoneEmptyState(sourceDropzone);
                        ensureDropzoneEmptyState(zone);
                    }
                    resetDragState();
                    return;
                }

                const previousSibling = draggedCard.previousElementSibling;
                const originalDropzone = sourceDropzone;

                zone.appendChild(draggedCard);
                draggedCard.dataset.status = targetStatus;
                draggedCard.dataset.updating = '1';
                draggedCard.classList.add('pointer-events-none', 'opacity-70');

                updateColumnCount(sourceStatus, -1);
                updateColumnCount(targetStatus, 1);
                ensureDropzoneEmptyState(originalDropzone);
                ensureDropzoneEmptyState(zone);

                try {
                    const response = await fetch(getCashStatusUrl(transactionId), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': cashCsrfToken,
                        },
                        body: JSON.stringify({ status: targetStatus }),
                    });

                    const payload = await response.json().catch(() => ({}));
                    if (!response.ok || !payload.success) {
                        throw new Error(payload.message || 'Falha ao atualizar status');
                    }
                } catch (error) {
                    console.error('Erro ao mover card do kanban:', error);

                    if (previousSibling && previousSibling.parentElement === originalDropzone) {
                        previousSibling.insertAdjacentElement('afterend', draggedCard);
                    } else {
                        originalDropzone.prepend(draggedCard);
                    }

                    draggedCard.dataset.status = sourceStatus;
                    updateColumnCount(targetStatus, -1);
                    updateColumnCount(sourceStatus, 1);
                    ensureDropzoneEmptyState(originalDropzone);
                    ensureDropzoneEmptyState(zone);
                    alert('Nao foi possivel mover o card. Tente novamente.');
                } finally {
                    draggedCard.dataset.updating = '0';
                    draggedCard.classList.remove('pointer-events-none', 'opacity-70');
                    resetDragState();
                }
            });
        });

        dropzones.forEach((zone) => ensureDropzoneEmptyState(zone));
    }

    // Toggle entre Grid e Kanban
    window.toggleView = function(view) {
        const gridView = document.getElementById('view-grid');
        const kanbanView = document.getElementById('view-kanban');
        if (!gridView || !kanbanView) {
            return;
        }

        const normalizedView = view === 'kanban' ? 'kanban' : 'grid';
        gridView.classList.toggle('hidden', normalizedView !== 'grid');
        kanbanView.classList.toggle('hidden', normalizedView !== 'kanban');
        setViewButtonState(normalizedView);
        localStorage.setItem('cash-view', normalizedView);
    }

    // Restaurar visualização salva e configurar event listeners
    document.addEventListener('DOMContentLoaded', function() {
        bindKanbanDragAndDrop();

        const savedView = localStorage.getItem('cash-view') || 'grid';
        toggleView(savedView);

        // Event listeners para os botões de relatórios
        const btnReportSimplified = document.getElementById('btn-report-simplified');
        const btnReportDetailed = document.getElementById('btn-report-detailed');
        const btnCloseSimplified = document.getElementById('btn-close-simplified');
        const btnCloseDetailed = document.getElementById('btn-close-detailed');
        const btnLoadSimplified = document.getElementById('btn-load-simplified');
        const btnLoadDetailed = document.getElementById('btn-load-detailed');
        const reportSimplifiedPeriod = document.getElementById('report-simplified-period');
        const reportSimplifiedDate = document.getElementById('report-simplified-date');

        if (btnReportSimplified) {
            btnReportSimplified.addEventListener('click', function() {
                window.openReportSimplified();
            });
        }

        if (btnReportDetailed) {
            btnReportDetailed.addEventListener('click', function() {
                window.openReportDetailed();
            });
        }

        if (btnCloseSimplified) {
            btnCloseSimplified.addEventListener('click', function() {
                window.closeReportSimplified();
            });
        }

        if (btnCloseDetailed) {
            btnCloseDetailed.addEventListener('click', function() {
                window.closeReportDetailed();
            });
        }

        if (btnLoadSimplified) {
            btnLoadSimplified.addEventListener('click', function() {
                loadSimplifiedReport();
            });
        }

        if (reportSimplifiedPeriod) {
            reportSimplifiedPeriod.addEventListener('change', function() {
                loadSimplifiedReport();
            });
        }

        if (reportSimplifiedDate) {
            reportSimplifiedDate.addEventListener('change', function() {
                loadSimplifiedReport();
            });
        }

        if (btnLoadDetailed) {
            btnLoadDetailed.addEventListener('click', function() {
                loadDetailedReport();
            });
        }
    });


    // Fechar modais ao clicar fora
    document.getElementById('modal-report-simplified')?.addEventListener('click', function(e) {
        if (e.target === this) window.closeReportSimplified();
    });

    document.getElementById('modal-report-detailed')?.addEventListener('click', function(e) {
        if (e.target === this) window.closeReportDetailed();
    });
</script>
@endpush
@endsection
