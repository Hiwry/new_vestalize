@extends('layouts.admin')

@section('content')
        @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- Cabeçalho -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Gerenciamento de Pedidos - Produção</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Visualize e gerencie pedidos por período e tipo de personalização</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
            <form method="GET" action="{{ route('production.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Período -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Período</label>
                        <select name="period" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Todo o Período</option>
                            <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Hoje</option>
                            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Esta Semana (Seg-Sex)</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Este Mês</option>
                            <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personalizado</option>
                        </select>
                    </div>

                    <!-- Data Início -->
                    <div id="start-date-field" style="{{ $period === 'custom' ? '' : 'display: none;' }}">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Início</label>
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

                    <!-- Tipo de Personalização -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Personalização</label>
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
                               placeholder="Número do pedido, nome do cliente, telefone ou nome da arte..."
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
            
            <!-- Botão para gerar PDF -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('production.pdf') }}?{{ http_build_query(request()->except('page')) }}" 
                   target="_blank"
                   class="inline-flex items-center px-6 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Gerar PDF da Lista
                </a>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    📄 Gera um PDF com os pedidos filtrados para impressão
                </p>
            </div>
        </div>

        <!-- Aviso de pedidos sem data de entrega -->
        @php
            $ordersWithoutDate = \App\Models\Order::where('is_draft', false)
                ->where('is_cancelled', false)
                ->whereNull('delivery_date')
                ->count();
        @endphp
        
        @if($ordersWithoutDate > 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-400 dark:text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                        Atenção: Existem <strong>{{ $ordersWithoutDate }} pedido(s)</strong> sem data de entrega definida.
                    </p>
                    <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">
                        Pedidos sem data de entrega não aparecem nos filtros por período. Acesse cada pedido e defina a data de entrega.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-500 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Pedidos</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalOrders }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 dark:bg-green-900/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Total</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($totalValue, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 dark:bg-yellow-900/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Por Status</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $ordersByStatus->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipos de Personalização</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $ordersByPersonalization->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Pedidos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pedidos Encontrados</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Mostrando {{ $orders->count() }} de {{ $totalOrders }} pedidos
                    @if($period === 'day')
                        ativos em produção
                    @elseif($period === 'week')
                        com entrega esta semana ({{ \Carbon\Carbon::parse($startDate)->format('d/m') }} a {{ \Carbon\Carbon::parse($endDate)->format('d/m') }})
                    @elseif($period === 'month')
                        com entrega este mês
                    @else
                        com entrega no período selecionado
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Personalização</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Pedido</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Entrega</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($orders as $order)
                                @php
                                    $firstItem = $order->items->first();
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->client->name ?? 'Cliente não encontrado' }}</div>
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

                <!-- Paginação -->
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
                            Não há pedidos para o período selecionado.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Mostrar/ocultar campos de data personalizada
        document.querySelector('select[name="period"]').addEventListener('change', function() {
            const startDateField = document.getElementById('start-date-field');
            const endDateField = document.getElementById('end-date-field');
            const startDateInput = startDateField.querySelector('input[name="start_date"]');
            const endDateInput = endDateField.querySelector('input[name="end_date"]');
            
            if (this.value === 'custom') {
                startDateField.style.display = 'block';
                endDateField.style.display = 'block';
            } else {
                startDateField.style.display = 'none';
                endDateField.style.display = 'none';
                // Limpar os valores quando não for custom
                startDateInput.value = '';
                endDateInput.value = '';
            }
        });
    </script>
@endsection
