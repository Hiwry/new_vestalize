@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Relatório de Peças de Tecido</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Resumo e estatísticas do estoque</p>
        </div>
        <a href="{{ route('fabric-pieces.index') }}" 
           class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Voltar
        </a>
    </div>

    {{-- Filtros de Período --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Início</label>
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Fim</label>
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja</label>
                <select name="store_id" class="px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">Aplicar</button>
        </form>
    </div>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Cadastradas</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totals['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Ativas</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totals['ativas'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Vendidas</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totals['vendidas'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Peso Total (kg)</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totals['peso_total'], 2, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Valor em Estoque</div>
            <div class="text-xl font-bold text-red-600 dark:text-red-400">R$ {{ number_format($totals['custo_total'], 2, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">Valor Vendas</div>
            <div class="text-xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($totals['valor_vendas'], 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Histórico e Vendas --}}
    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 mt-8 flex items-center gap-2">
        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Desempenho de Vendas (kg / R$)
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Vendas por Tipo de Tecido --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Vendas por Tipo de Tecido</h3>
                <span class="text-xs text-gray-500 uppercase">Resumo kg</span>
            </div>
            <div class="p-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="pb-2">Tecido</th>
                            <th class="pb-2 text-center">Peso Vendido</th>
                            <th class="pb-2 text-right">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($salesByFabricType as $row)
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">{{ $row['name'] }}</td>
                            <td class="py-2 text-center font-semibold text-indigo-600 dark:text-indigo-400">{{ number_format($row['kg'], 2, ',', '.') }} kg</td>
                            <td class="py-2 text-right text-gray-900 dark:text-gray-100 font-medium">R$ {{ number_format($row['value'], 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500 italic">Sem vendas no período</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Vendas por Peça --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Vendas por Peça (Top 10)</h3>
                <span class="text-xs text-gray-500 uppercase">Eficiência</span>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="pb-2">NF</th>
                            <th class="pb-2">Fornecedor</th>
                            <th class="pb-2">Tecido/Cor</th>
                            <th class="pb-2 text-center">Peso Vendido</th>
                            <th class="pb-2 text-right">Valor Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($salesByPiece as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="py-2">
                                <a href="{{ route('fabric-pieces.edit', $row['id']) }}" 
                                   class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                                   title="Clique para ver a peça">
                                    {{ $row['nf'] ?? 'NF-' . $row['id'] }}
                                </a>
                            </td>
                            <td class="py-2 text-gray-600 dark:text-gray-400">{{ $row['supplier'] ?? '-' }}</td>
                            <td class="py-2 text-gray-900 dark:text-gray-100">{{ $row['fabric'] }} / {{ $row['color'] }}</td>
                            <td class="py-2 text-center text-gray-900 dark:text-gray-100 font-medium">{{ number_format($row['kg'], 2, ',', '.') }} kg</td>
                            <td class="py-2 text-right text-green-600 dark:text-green-400 font-semibold">R$ {{ number_format($row['value'], 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500 italic">Sem vendas no período</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Histórico Detalhado --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 mb-8">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Histórico Detalhado de Vendas</h3>
            <span class="px-2 py-1 text-[10px] bg-blue-100 text-blue-700 rounded-full font-bold uppercase">Últimos 50 itens</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr class="text-left text-gray-500 dark:text-gray-400">
                        <th class="px-4 py-3 uppercase tracking-wider font-bold">Data/Hora</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold">NF</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold">Fornecedor</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold">Tecido/Cor</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold text-center">Quantidade</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold text-right">Unitário</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold text-right">Total</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold">Venda</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold">Loja</th>
                        <th class="px-4 py-3 uppercase tracking-wider font-bold">Vendedor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($salesHistory as $sale)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $sale->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('fabric-pieces.edit', $sale->fabric_piece_id) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                               title="Ver peça de tecido">
                                {{ $sale->fabricPiece->invoice_number ?? 'NF-' . $sale->fabric_piece_id }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $sale->fabricPiece->supplier ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                            {{ $sale->fabricPiece->fabricType->name ?? 'N/A' }} / {{ $sale->fabricPiece->color->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-gray-100">
                            {{ number_format($sale->quantity, 2, ',', '.') }} {{ $sale->unit }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                            R$ {{ number_format($sale->unit_price, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400">
                            R$ {{ number_format($sale->total_price, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($sale->order_id && $sale->order)
                                @if($sale->order->is_pdv)
                                    <a href="{{ route('pdv.sales.edit', $sale->order_id) }}" 
                                       class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium"
                                       title="Ver venda PDV">
                                        <span class="text-xs bg-emerald-100 dark:bg-emerald-900/30 px-1 rounded mr-1">PDV</span>#{{ str_pad($sale->order_id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                @else
                                    <a href="{{ route('orders.show', $sale->order_id) }}" 
                                       class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                                       title="Ver detalhes do pedido">
                                        <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 px-1 rounded mr-1">PED</span>#{{ str_pad($sale->order_id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $sale->store->slug ?? $sale->store->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $sale->soldBy->name ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500 italic">
                            Nenhuma venda registrada no período selecionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Estoque Atual --}}
    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 mt-8 flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        Status do Estoque Atual
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
        {{-- Por Loja --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Distribuição por Loja</h3>
            </div>
            <div class="p-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="pb-2">Loja</th>
                            <th class="pb-2 text-center">Ativas</th>
                            <th class="pb-2 text-center">Vendidas</th>
                            <th class="pb-2 text-right">Peso (kg)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($byStore as $row)
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">{{ $row['name'] }}</td>
                            <td class="py-2 text-center text-green-600 dark:text-green-400">{{ $row['ativas'] }}</td>
                            <td class="py-2 text-center text-blue-600 dark:text-blue-400">{{ $row['vendidas'] }}</td>
                            <td class="py-2 text-right text-gray-900 dark:text-gray-100 font-medium">{{ number_format($row['peso'], 2, ',', '.') }} kg</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Por Tipo de Tecido --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Quantidade em Estoque por Tecido</h3>
            </div>
            <div class="p-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="pb-2">Tipo</th>
                            <th class="pb-2 text-center">Peças</th>
                            <th class="pb-2 text-right">Peso Total (kg)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($byFabricType as $row)
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">{{ $row['name'] ?? 'Não definido' }}</td>
                            <td class="py-2 text-center text-gray-900 dark:text-gray-100">{{ $row['count'] }}</td>
                            <td class="py-2 text-right text-gray-900 dark:text-gray-100 font-medium">{{ number_format($row['peso'], 2, ',', '.') }} kg</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Por Fornecedor --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Top Fornecedores (Estoque)</h3>
            </div>
            <div class="p-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="pb-2">Fornecedor</th>
                            <th class="pb-2 text-center">Peças</th>
                            <th class="pb-2 text-right">Valor em Estoque</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($bySupplier as $row)
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">{{ $row['name'] ?? 'Não informado' }}</td>
                            <td class="py-2 text-center text-gray-900 dark:text-gray-100">{{ $row['count'] }}</td>
                            <td class="py-2 text-right text-gray-900 dark:text-gray-100">R$ {{ number_format($row['custo'], 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
