@extends('layouts.admin')

@section('content')
<div class="px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Dashboard de Estoque
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Visão geral e métricas do estoque em tempo real.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Filtro de Período -->
            <form method="GET" action="{{ route('stocks.dashboard') }}" id="periodFilterForm" class="flex items-center gap-2">
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
                    <button type="submit" class="p-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
                @endif
            </form>

            <!-- Filtros Avançados -->
            <form method="GET" action="{{ route('stocks.dashboard') }}" class="flex flex-wrap items-center gap-2 border-l border-gray-200 dark:border-gray-700 pl-3">
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

                <select name="fabric_id" onchange="this.form.submit()" 
                        class="px-3 py-2 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition-all">
                    <option value="">Tecido: Todos</option>
                    @foreach($fabrics as $f)
                        <option value="{{ $f->id }}" {{ request('fabric_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                    @endforeach
                </select>

                <select name="color_id" onchange="this.form.submit()" 
                        class="px-3 py-2 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition-all">
                    <option value="">Cor: Todas</option>
                    @foreach($colors as $c)
                        <option value="{{ $c->id }}" {{ request('color_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </form>

            <div class="flex gap-2 border-l border-gray-200 dark:border-gray-700 pl-3">
                <a href="{{ route('stocks.history') }}" class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition flex items-center gap-2 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Histórico
                </a>
                <a href="{{ route('stocks.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Estoque
                </a>
            </div>
        </div>
    </div>

    <!-- Cards KPI -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Itens -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Peças</h3>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalItems, 0, ',', '.') }}</p>
                </div>
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $totalSKUs }} SKUs distintos</p>
        </div>

        <!-- Alerta Estoque Baixo -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Alertas de Baixo Estoque</h3>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $lowStockCount }}</p>
                </div>
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg text-red-600 dark:text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Itens abaixo do mínimo definido</p>
        </div>

        <!-- Solicitações Pendentes -->
        <a href="{{ route('stock-requests.index', ['status' => 'pendente']) }}" class="block">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-yellow-500 hover:shadow-lg transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Solicitações Pendentes</h3>
                        <p class="text-2xl font-bold {{ $pendingRequests > 0 ? 'text-amber-600' : 'text-gray-800 dark:text-white' }} mt-1">{{ $pendingRequests }}</p>
                    </div>
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Aguardando aprovação</p>
            </div>
        </a>

        <!-- Estatísticas Gerais -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-green-500">
             <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Movimentações Hoje</h3>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        {{ array_sum(array_slice($movementsData['entries'], -1)) + array_sum(array_slice($movementsData['exits'], -1)) }}
                    </p>
                </div>
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg text-green-600 dark:text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Entradas e saídas registradas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Gráfico de Movimentações -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Movimentações Recentes (7 dias)</h3>
            <div class="h-64 relative">
                <canvas id="movementsChart"></canvas>
            </div>
        </div>

        <!-- Distribuição por Loja -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Estoque por Loja</h3>
            <div class="h-64 relative flex justify-center">
                <canvas id="storeChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Itens com Estoque Baixo -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-900/30 flex justify-between items-center">
                <h3 class="font-bold text-red-800 dark:text-red-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Prioridade de Reposição
                </h3>
                <a href="{{ route('stocks.index', ['filter_low_stock' => 1]) }}" class="text-xs text-red-600 hover:text-red-800 underline">Ver todos</a>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($lowStockItems as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800 dark:text-gray-200">{{ $item->cutType->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->store->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-2 text-xs text-gray-500">
                            SIZE: {{ $item->size }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200">
                                {{ $item->quantity }} / {{ (int)$item->min_stock }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">Nenhum alerta de estoque baixo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Atividade Recente -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white">Últimas Atividades</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentActivity as $activity)
                <div class="px-4 py-3 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <div class="mt-1">
                        @if($activity->action_type == 'entrada')
                            <span class="w-2 h-2 rounded-full bg-green-500 block"></span>
                        @elseif($activity->action_type == 'saida')
                            <span class="w-2 h-2 rounded-full bg-red-500 block"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-blue-500 block"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ ucfirst($activity->action_type) }} - {{ $activity->cutType->name ?? 'Item' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ $activity->user->name ?? 'Sistema' }} • {{ $activity->store->name ?? '-' }}
                        </p>
                    </div>
                    <div class="text-xs text-gray-500 whitespace-nowrap">
                        {{ $activity->action_date->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="px-4 py-4 text-center text-gray-500">Nenhuma atividade recente.</div>
                @endforelse
            </div>
            <div class="p-2 text-center bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('stocks.history') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver histórico completo &rarr;</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart.js Configuration
        Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#4B5563';
        Chart.defaults.font.family = "'Inter', sans-serif";

        // Movements Chart
        const ctxMovements = document.getElementById('movementsChart').getContext('2d');
        new Chart(ctxMovements, {
            type: 'bar',
            data: {
                labels: {!! json_encode($movementsData['labels']) !!},
                datasets: [
                    {
                        label: 'Entradas',
                        data: {!! json_encode($movementsData['entries']) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.5)', // green-500
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    },
                    {
                        label: 'Saídas',
                        data: {!! json_encode($movementsData['exits']) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.5)', // red-500
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Store Distribution Chart
        const ctxStore = document.getElementById('storeChart').getContext('2d');
        const storeData = {!! json_encode($stockByStore) !!};
        
        new Chart(ctxStore, {
            type: 'doughnut',
            data: {
                labels: Object.keys(storeData),
                datasets: [{
                    data: Object.values(storeData),
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.7)', // Indigo
                        'rgba(59, 130, 246, 0.7)', // Blue
                        'rgba(168, 85, 247, 0.7)', // Purple
                        'rgba(236, 72, 153, 0.7)', // Pink
                        'rgba(249, 115, 22, 0.7)', // Orange
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                            position: 'right',
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
