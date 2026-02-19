@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Histórico de Vendas</h1>
</div>

<!-- Estatísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Total de Vendas</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_sales'] }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Faturamento Total</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Total Pago</div>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">R$ {{ number_format($stats['total_paid'], 2, ',', '.') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Ticket Médio</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($stats['avg_ticket'], 2, ',', '.') }}</div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
    <form method="GET" action="{{ route('sales-history.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Nº da venda, cliente ou vendedor..."
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            @if(Auth::user()->isAdmin())
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendedor</label>
                <select name="user_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="">Todos</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ $userId == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="">Todos</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}" {{ $statusId == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                <select name="is_pdv" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="">Todos</option>
                    <option value="1" {{ $isPdv === '1' ? 'selected' : '' }}>PDV</option>
                    <option value="0" {{ $isPdv === '0' ? 'selected' : '' }}>Pedidos</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Inicial</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Final</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Tabela -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vendedor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pago</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($history as $sale)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            #{{ str_pad($sale->order_id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $sale->sale_date->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $sale->user->name ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $sale->client->name ?? 'Sem cliente' }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            R$ {{ number_format($sale->total, 2, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            R$ {{ number_format($sale->total_paid, 2, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $sale->is_cancelled ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' }}">
                            {{ $sale->status->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($sale->is_pdv)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">
                                PDV
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                Pedido
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Nenhum histórico encontrado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $history->links() }}
    </div>
</div>
@endsection
