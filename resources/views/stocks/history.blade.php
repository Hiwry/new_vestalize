@extends('layouts.admin')

@section('content')
<div class="px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Histórico de Movimentações
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Registro detalhado de entradas, saídas e alterações de estoque.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('stocks.dashboard') }}" class="px-4 py-2 !bg-indigo-600 dark:!bg-indigo-700 !text-white border border-indigo-200 dark:border-indigo-600 rounded-lg hover:!bg-indigo-700 dark:hover:!bg-indigo-800 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('stocks.index') }}" class="px-4 py-2 !bg-gray-700 dark:!bg-gray-800 !text-white border border-gray-600 dark:border-gray-700 rounded-lg hover:!bg-gray-800 dark:hover:!bg-gray-900 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                Voltar ao Estoque
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" action="{{ route('stocks.history') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            <!-- Loja -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Loja</label>
                <select name="store_id" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Produto (Busca) -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar (Tecido, Cor, Corte)</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: Algodão, Azul, Camiseta..." 
                       class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Tipo de Ação -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Ação</label>
                <select name="action_type" class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action_type') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Data Início -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">De</label>
                <input type="date" name="date_start" value="{{ request('date_start') }}" 
                       class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Data Fim -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Até</label>
                <input type="date" name="date_end" value="{{ request('date_end') }}" 
                       class="w-full text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Botões -->
            <div class="lg:col-span-6 flex gap-2 justify-end">
                <button type="submit" class="px-5 py-2 bg-white text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar Resultados
                </button>
                @if(request()->anyFilled(['store_id', 'search', 'action_type', 'date_start', 'date_end', 'user_id']))
                <a href="{{ route('stocks.history') }}" class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Limpar
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Data/Hora</th>
                        <th class="px-4 py-3">Usuário</th>
                        <th class="px-4 py-3">Ação</th>
                        <th class="px-4 py-3">Produto / Tamanho</th>
                        <th class="px-4 py-3 text-center">Qtde.</th>
                        <th class="px-4 py-3">Detalhes / Obs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($history as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $item->action_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            {{ $item->user->name ?? 'Sistema' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($item->action_type == 'entrada') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200
                                @elseif($item->action_type == 'saida') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200
                                @elseif($item->action_type == 'edicao') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ ucfirst($item->action_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ $item->cutType->name ?? '-' }}
                                    @if($item->size) <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded">{{ $item->size }}</span> @endif
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $item->color->name ?? '-' }} | {{ $item->fabric->name ?? '-' }}
                                </span>
                                <span class="text-xs text-indigo-500">{{ $item->store->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->quantity_change > 0)
                                <span class="text-green-600 font-bold">+{{ $item->quantity_change }}</span>
                            @elseif($item->quantity_change < 0)
                                <span class="text-red-600 font-bold">{{ $item->quantity_change }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm">
                            <div class="space-y-1">
                                {{-- Link para Pedido --}}
                                @if($item->order)
                                    <div>
                                        <a href="{{ route('orders.show', $item->order->id) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium hover:underline">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                            Pedido #{{ $item->order->id }}
                                        </a>
                                    </div>
                                @endif

                                {{-- Link para Solicitação --}}
                                @if($item->stockRequest)
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-purple-600 dark:text-purple-400 font-medium">
                                            Solicitação #{{ $item->stockRequest->id }}
                                        </span>
                                        @if($item->stockRequest->order)
                                            <span class="text-gray-400">→</span>
                                            <a href="{{ route('orders.show', $item->stockRequest->order->id) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium hover:underline">
                                                Pedido #{{ $item->stockRequest->order->id }}
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                @if($item->notes)
                                    <div class="bg-gray-700 dark:bg-gray-800 p-2 rounded text-xs border border-gray-600 dark:border-gray-700 !text-white font-bold">
                                        {{ $item->notes }}
                                    </div>
                                @endif
                                
                                @if(!empty($item->metadata) && isset($item->metadata['changes']))
                                    <div class="text-xs space-y-1 mt-1 border-t pt-1 border-gray-100 dark:border-gray-700">
                                        @foreach($item->metadata['changes'] as $field => $change)
                                            <div class="flex items-center gap-1">
                                                <span class="font-semibold capitalize">{{ str_replace('_', ' ', $field) }}:</span>
                                                <span class="text-red-500">{{ $change['old'] }}</span>
                                                <span>→</span>
                                                <span class="text-green-500">{{ $change['new'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma movimentação encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $history->links() }}
        </div>
    </div>
</div>
@endsection
