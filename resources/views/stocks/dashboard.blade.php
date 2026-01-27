@extends('layouts.admin')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3 tracking-tight">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl text-white shadow-lg shadow-indigo-200 dark:shadow-none">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="#ffffff" viewBox="0 0 24 24" style="color: white !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                Dashboard de Estoque
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg font-medium ml-1">
                Visão geral estratégica e controle de inventário.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
            <!-- Filter Group -->
            <div class="p-1 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-wrap gap-2">
                <!-- Time Period Filter -->
                <form method="GET" action="{{ route('stocks.dashboard') }}" id="periodFilterForm">
                    <select name="period" onchange="this.form.submit()" 
                            class="px-4 py-2 border-0 bg-white dark:bg-gray-800 rounded-xl text-gray-700 dark:text-gray-200 text-sm font-bold shadow-sm focus:ring-2 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <option value="today" {{ ($period ?? 'month') === 'today' ? 'selected' : '' }}>Hoje</option>
                        <option value="week" {{ ($period ?? 'month') === 'week' ? 'selected' : '' }}>Semana</option>
                        <option value="month" {{ ($period ?? 'month') === 'month' ? 'selected' : '' }}>Mês</option>
                        <option value="year" {{ ($period ?? 'month') === 'year' ? 'selected' : '' }}>Ano</option>
                    </select>
                </form>

                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

                <!-- Filters -->
                 <form method="GET" action="{{ route('stocks.dashboard') }}" class="flex items-center gap-2">
                     @if(isset($period)) <input type="hidden" name="period" value="{{ $period }}"> @endif
                     
                    <select name="fabric_id" onchange="this.form.submit()" 
                            class="w-32 sm:w-auto px-4 py-2 border-0 bg-white dark:bg-gray-800 rounded-xl text-gray-700 dark:text-gray-200 text-sm font-semibold shadow-sm focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <option value="">Tecido: Todos</option>
                        @foreach($fabrics as $f)
                            <option value="{{ $f->id }}" {{ request('fabric_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
    
                    <select name="color_id" onchange="this.form.submit()" 
                            class="w-32 sm:w-auto px-4 py-2 border-0 bg-white dark:bg-gray-800 rounded-xl text-gray-700 dark:text-gray-200 text-sm font-semibold shadow-sm focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <option value="">Cor: Todas</option>
                        @foreach($colors as $c)
                            <option value="{{ $c->id }}" {{ request('color_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('stocks.history') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95" style="color: #ffffff !important;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #ffffff !important;"></i>
                    Histórico
                </a>
                <a href="{{ route('stocks.index', ['view' => 'table']) }}" class="px-5 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-boxes-stacked text-indigo-500"></i>
                    Estoque
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Total Items -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-purple-500/20 transition-all"></div>
            
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total de Peças</p>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalItems, 0, ',', '.') }}</h3>
                    <div class="mt-4 flex items-center gap-2">
                         <span class="px-2.5 py-1 rounded-full bg-purple-600 text-white text-xs font-bold shadow-sm" style="color: #ffffff !important;">
                            {{ $totalSKUs }} SKUs
                        </span>
                        <span class="text-xs text-gray-400 font-medium">em estoque</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-none text-white">
                    <i class="fa-solid fa-layer-group text-xl text-white" style="color: #ffffff !important;"></i>
                </div>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
             <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/10 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-red-500/20 transition-all"></div>
            
            <div class="flex justify-between items-start z-10 relative">
                <div>
                     <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Alertas</p>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $lowStockCount }}</h3>
                    <div class="mt-4 flex items-center gap-2">
                         <span class="px-2.5 py-1 rounded-full bg-red-600 text-white text-xs font-bold shadow-sm flex items-center gap-1" style="color: #ffffff !important;">
                            <i class="fa-solid fa-arrow-down text-[10px]" style="color: white !important;"></i> Baixo Estoque
                        </span>
                    </div>
                </div>
                 <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center shadow-lg shadow-red-200 dark:shadow-none text-white">
                    <i class="fa-solid fa-triangle-exclamation text-xl text-white" style="color: #ffffff !important;"></i>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <a href="{{ route('stock-requests.index', ['status' => 'pendente']) }}" class="block">
            <div class="h-full bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-xl transition-all duration-300 hover:border-amber-200 dark:hover:border-amber-800">
               <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-amber-500/20 transition-all"></div>
                
                <div class="flex justify-between items-start z-10 relative">
                    <div>
                         <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Solicitações</p>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $pendingRequests }}</h3>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full bg-amber-500 text-white text-xs font-bold shadow-sm" style="color: #ffffff !important;">
                                Pendentes
                            </span>
                             <span class="text-xs text-gray-400 text-[10px] group-hover:translate-x-1 transition-transform">ver todas &rarr;</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-none text-white">
                        <i class="fa-solid fa-clipboard-check text-xl text-white" style="color: #ffffff !important;"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Movements Today -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
             <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-500/20 transition-all"></div>
            
            <div class="flex justify-between items-start z-10 relative">
                <div>
                     <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Movimentações</p>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">
                        {{ array_sum(array_slice($movementsData['entries'], -1)) + array_sum(array_slice($movementsData['exits'], -1)) }}
                    </h3>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500 text-white text-xs font-bold shadow-sm" style="color: #ffffff !important;">
                            Hoje
                        </span>
                        <span class="text-xs text-gray-400 font-medium">entradas/saídas</span>
                    </div>
                </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-none text-white">
                    <i class="fa-solid fa-chart-line text-xl text-white" style="color: #ffffff !important;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Movimentações Recentes</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fluxo de estoque dos últimos 7 dias</p>
                </div>
                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 p-1.5 rounded-lg border border-gray-100 dark:border-gray-600">
                    <div class="flex items-center gap-2 px-2 py-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200 dark:shadow-none"></span>
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Entradas</span>
                    </div>
                    <div class="flex items-center gap-2 px-2 py-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm shadow-red-200 dark:shadow-none"></span>
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Saídas</span>
                    </div>
                </div>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="movementsChart"></canvas>
            </div>
        </div>

        <!-- Secondary Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm flex flex-col hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">Estoque por Loja</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Distribuição atual do inventário</p>
            <div class="relative flex-1 flex items-center justify-center min-h-[250px]">
                <canvas id="storeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock / High Demand -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition-all">
            <div class="px-8 py-6 flex justify-between items-center border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 shadow-lg shadow-orange-200 dark:shadow-none flex items-center justify-center text-white">
                        <i class="fa-solid fa-fire" style="color: #ffffff !important;"></i>
                    </div>
                     <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">Baixo Estoque</h3>
                        <p class="text-xs font-medium text-gray-500 font-mono uppercase">Top 5 Críticos</p>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-8 py-4">Produto</th>
                            <th class="px-4 py-4 text-center">Qtd.</th>
                            <th class="px-8 py-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($lowStockItems as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-8 py-4">
                                <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->cutType->name ?? 'Item s/ nome' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                    <i class="fa-solid fa-store text-[10px]"></i>
                                    {{ $item->store->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-3 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 font-mono font-bold text-sm text-gray-900 dark:text-white">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-right">
                                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->quantity == 0 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' }}">
                                    {{ $item->quantity == 0 ? 'Sem Estoque' : 'Baixo' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 mb-3">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <p class="font-medium">Estoque saudável!</p>
                                    <p class="text-xs">Nenhum item com estoque baixo.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Transactions List -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition-all">
            <div class="px-8 py-6 flex justify-between items-center border-b border-gray-100 dark:border-gray-700">
                 <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500 shadow-lg shadow-indigo-200 dark:shadow-none flex items-center justify-center text-white">
                        <i class="fa-solid fa-clock-rotate-left" style="color: #ffffff !important;"></i>
                    </div>
                     <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">Transações Recentes</h3>
                        <p class="text-xs font-medium text-gray-500 font-mono uppercase">Últimos movimentos</p>
                    </div>
                </div>
                <a href="{{ route('stocks.history') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                    Ver Tudo
                </a>
            </div>
            
            <div class="overflow-x-auto">
                 <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-8 py-4">Tipo</th>
                            <th class="px-4 py-4">Detalhes</th>
                            <th class="px-8 py-4 text-right">Data</th>
                        </tr>
                    </thead>
                     <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentActivity as $activity)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-8 py-4 w-24">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg text-white shadow-sm {{ $activity->action_type == 'entrada' ? 'bg-emerald-500 shadow-emerald-200 dark:shadow-none' : 'bg-red-500 shadow-red-200 dark:shadow-none' }}">
                                    <i class="fa-solid {{ $activity->action_type == 'entrada' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-xs" style="color: #ffffff !important;"></i>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-900 dark:text-white text-sm truncate max-w-[150px]">{{ $activity->cutType->name ?? 'Item' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5 truncate max-w-[150px]">{{ $activity->user->name ?? 'Sistema' }}</div>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <span class="text-xs font-medium text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center text-gray-500">
                                <p class="text-sm">Nenhuma atividade recente.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Variáveis globais para rastrear instâncias dos gráficos
    let movementsChartInstance = null;
    let storeChartInstance = null;

    function initializeCharts() {
        // Destruir instâncias antigas se existirem
        if (movementsChartInstance) {
            movementsChartInstance.destroy();
            movementsChartInstance = null;
        }
        if (storeChartInstance) {
            storeChartInstance.destroy();
            storeChartInstance = null;
        }

        // Verificar se os elementos existem na página
        const movementsEl = document.getElementById('movementsChart');
        const storeEl = document.getElementById('storeChart');
        
        if (!movementsEl || !storeEl) {
            console.log('Dashboard: Elementos de gráfico não encontrados');
            return;
        }

        // Chart.js Global Config
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#64748B';
        
        // Setup Charts
        const ctxMovements = movementsEl.getContext('2d');
        movementsChartInstance = new Chart(ctxMovements, {
            type: 'bar',
            data: {
                labels: {!! json_encode($movementsData['labels']) !!},
                datasets: [
                    {
                        label: 'Entradas',
                        data: {!! json_encode($movementsData['entries']) !!},
                        backgroundColor: '#10B981', // emerald-500
                        borderRadius: 6,
                        barThickness: 16,
                        hoverBackgroundColor: '#059669'
                    },
                    {
                        label: 'Saídas',
                        data: {!! json_encode($movementsData['exits']) !!},
                        backgroundColor: '#EF4444', // red-500
                        borderRadius: 6,
                        barThickness: 16,
                        hoverBackgroundColor: '#DC2626'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1F2937', padding: 12, cornerRadius: 8 } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: document.documentElement.classList.contains('dark') ? '#374151' : '#F3F4F6', drawBorder: false },
                        border: { display: false }
                    },
                    x: { 
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });

        const ctxStore = storeEl.getContext('2d');
        const storeData = {!! json_encode($stockByStore) !!};
        const hasData = Object.keys(storeData).length > 0;

        storeChartInstance = new Chart(ctxStore, {
            type: 'doughnut',
            data: {
                labels: hasData ? Object.keys(storeData) : ['Sem dados'],
                datasets: [{
                    data: hasData ? Object.values(storeData) : [1],
                    backgroundColor: hasData ? ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'] : ['#E5E7EB'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 12 } } }
                }
            }
        });
    }

    // Inicializar quando a página carregar (primeira vez OU navegação AJAX)
    document.addEventListener('DOMContentLoaded', initializeCharts);
    document.addEventListener('content:loaded', initializeCharts);
</script>
@endpush
@endsection
