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
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 italic tracking-tighter">Lista de Pedidos</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Gerencie e acompanhe todos os pedidos personalizados</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('personalized.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-[#7c3aed] text-white rounded-xl font-bold hover:bg-[#6d28d9] transition shadow-lg shadow-purple-500/20">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    + Novo Personalizado
                </a>
                <a href="{{ route('kanban.index', ['type' => 'personalized']) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-200 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-white/10 transition border border-gray-200 dark:border-white/10">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                    Ver em Kanban
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
            <form method="GET" action="{{ route('production.index') }}" class="space-y-4">
                <input type="hidden" name="type" value="personalized">
                
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

                    <!-- Busca -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Número do pedido, nome do cliente, etc..."
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('production.index', ['type' => 'personalized']) }}" 
                       class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de Pedidos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pedidos Encontrados</h3>
                 <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Mostrando {{ $orders->count() }} de {{ $totalOrders }} pedidos
                </p>
            </div>

            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedido</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Itens</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Entrega</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->client->name ?? 'Cliente não encontrado' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->client->phone_primary ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
                                              style="background-color: {{ $order->status->color ?? '#6B7280' }}">
                                            {{ $order->status->name ?? 'Indefinido' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $order->items->count() }} item(ns)
                                        </div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">
                                            {{ $order->items->pluck('name')->join(', ') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        R$ {{ number_format($order->total, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('orders.show', $order->id) }}" 
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            Ver Detalhes
                                        </a>
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum pedido personalizado encontrado</h3>
                </div>
            @endif
        </div>

    <script>
        // Mostrar/ocultar campos de data personalizada (igual ao original)
        document.querySelector('select[name="period"]').addEventListener('change', function() {
            const startDateField = document.getElementById('start-date-field');
            const endDateField = document.getElementById('end-date-field');
            
            if (this.value === 'custom') {
                startDateField.style.display = 'block';
                endDateField.style.display = 'block';
            } else {
                startDateField.style.display = 'none';
                endDateField.style.display = 'none';
            }
        });
    </script>
@endsection
