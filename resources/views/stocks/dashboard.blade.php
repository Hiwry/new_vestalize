@extends('layouts.admin')

@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-primary/10 rounded-xl text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                Dashboard de Estoque
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-lg font-medium">
                Visão geral e métricas do estoque em tempo real.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Time Period Filter -->
            <form method="GET" action="{{ route('stocks.dashboard') }}" id="periodFilterForm" class="flex items-center gap-2">
                <select name="period" onchange="this.form.submit()" 
                        class="px-4 py-2.5 border-0 ring-1 ring-gray-200 dark:ring-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-semibold shadow-sm focus:ring-2 focus:ring-primary focus:outline-none cursor-pointer">
                    <option value="today" {{ ($period ?? 'month') === 'today' ? 'selected' : '' }}>Hoje</option>
                    <option value="week" {{ ($period ?? 'month') === 'week' ? 'selected' : '' }}>Esta Semana</option>
                    <option value="month" {{ ($period ?? 'month') === 'month' ? 'selected' : '' }}>Este Mês</option>
                    <option value="year" {{ ($period ?? 'month') === 'year' ? 'selected' : '' }}>Este Ano</option>
                </select>

                <!-- Custom Date Range -->
                @if(($period ?? 'month') === 'custom')
                @endif
            </form>

            <!-- Filters -->
             <form method="GET" action="{{ route('stocks.dashboard') }}" class="flex items-center gap-2">
                 @if(isset($period)) <input type="hidden" name="period" value="{{ $period }}"> @endif
                 
                <select name="fabric_id" onchange="this.form.submit()" 
                        class="px-4 py-2.5 border-0 ring-1 ring-gray-200 dark:ring-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-semibold shadow-sm focus:ring-2 focus:ring-primary focus:outline-none cursor-pointer">
                    <option value="">Tecido: Todos</option>
                    @foreach($fabrics as $f)
                        <option value="{{ $f->id }}" {{ request('fabric_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                    @endforeach
                </select>

                <select name="color_id" onchange="this.form.submit()" 
                        class="px-4 py-2.5 border-0 ring-1 ring-gray-200 dark:ring-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-semibold shadow-sm focus:ring-2 focus:ring-primary focus:outline-none cursor-pointer">
                    <option value="">Cor: Todas</option>
                    @foreach($colors as $c)
                        <option value="{{ $c->id }}" {{ request('color_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </form>

            <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-2 hidden md:block"></div>

            <!-- Action Buttons -->
            <a href="{{ route('stocks.history') }}" class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl shadow-lg transition-all duration-300 font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left" style="color: #ffffff !important;"></i>
                Histórico
            </a>
            <a href="{{ route('stocks.index', ['view' => 'table']) }}" class="px-5 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm transition-all duration-300 font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked"></i>
                Estoque
            </a>
        </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Total Items -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total de Peças</p>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ number_format($totalItems, 0, ',', '.') }}</h3>
                    <p class="text-xs font-bold text-purple-600 mt-3 bg-purple-50 dark:bg-purple-900/20 px-3 py-1.5 rounded-lg inline-block">
                        {{ $totalSKUs }} SKUs distintos
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-600 flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-none transform group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-cube text-xl text-white" style="color: #ffffff !important;"></i>
                </div>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Alertas de Baixo Estoque</p>
                    <h3 class="text-4xl font-black text-red-500 dark:text-red-500">{{ $lowStockCount }}</h3>
                    <p class="text-xs font-bold text-red-500 mt-3 bg-red-50 dark:bg-red-900/20 px-3 py-1.5 rounded-lg inline-block">
                        Itens abaixo do mínimo
                    </p>
                </div>
                 <div class="w-14 h-14 rounded-2xl bg-red-600 flex items-center justify-center shadow-lg shadow-red-200 dark:shadow-none transform group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-triangle-exclamation text-xl text-white" style="color: #ffffff !important;"></i>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <a href="{{ route('stock-requests.index', ['status' => 'pendente']) }}" class="block">
            <div class="h-full bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                <div class="flex justify-between items-start z-10 relative">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Solicitações Pendentes</p>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white">{{ $pendingRequests }}</h3>
                        <p class="text-xs font-bold text-amber-500 mt-3 bg-amber-50 dark:bg-amber-900/20 px-3 py-1.5 rounded-lg inline-block">
                            Aguardando aprovação
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-none transform group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-calendar-check text-xl text-white" style="color: #ffffff !important;"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Movements Today -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                     <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Movimentações Hoje</p>
                    <h3 class="text-4xl font-black text-emerald-500 dark:text-emerald-500">
                        {{ array_sum(array_slice($movementsData['entries'], -1)) + array_sum(array_slice($movementsData['exits'], -1)) }}
                    </h3>
                    <p class="text-xs font-bold text-emerald-500 mt-3 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5 rounded-lg inline-block">
                        Entradas e saídas registradas
                    </p>
                </div>
                 <div class="w-14 h-14 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-none transform group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-chart-line text-xl text-white" style="color: #ffffff !important;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Movimentações Recentes (7 dias)</h3>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        <span class="text-gray-500">Entradas</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="text-gray-500">Saídas</span>
                    </div>
                </div>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="movementsChart"></canvas>
            </div>
        </div>

        <!-- Secondary Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm flex flex-col">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Estoque por Loja</h3>
            <div class="relative flex-1 flex items-center justify-center">
                <canvas id="storeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Products List (previously Low Stock) -->
        <div class="bg-gray-900 rounded-3xl border border-gray-800 overflow-hidden shadow-lg">
            <div class="px-6 py-6 flex justify-between items-center">
                <h3 class="font-bold text-white flex items-center gap-2 text-lg">
                    <i class="fa-solid fa-fire text-orange-500"></i>
                    Produtos em Alta
                </h3>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Performance por SKU</span>
            </div>
            
            <!-- Table Header -->
            <div class="bg-[#8B5CF6] px-6 py-3 flex text-[10px] font-black text-white uppercase tracking-widest">
                <div class="w-1/2">Produto</div>
                <div class="w-1/4 text-center">Quantidade</div>
                <div class="w-1/4 text-right">Situação</div>
            </div>

            <div class="divide-y divide-gray-800">
                @forelse($lowStockItems as $item)
                <div class="px-6 py-4 flex items-center hover:bg-white/5 transition-colors">
                    <div class="w-1/2">
                        <div class="font-bold text-white text-sm">{{ $item->cutType->name ?? 'Item s/ nome' }}</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">{{ $item->store->name ?? '-' }}</div>
                    </div>
                    <div class="w-1/4 text-center">
                        <span class="text-sm font-bold text-white">{{ $item->quantity }} un.</span>
                    </div>
                    <div class="w-1/4 text-right">
                         <span class="text-xs font-bold {{ $item->quantity == 0 ? 'text-red-500' : 'text-amber-500' }}">
                            {{ $item->quantity == 0 ? 'Sem Estoque' : 'Baixo' }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <p class="text-sm">Nenhum registro encontrado.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions List -->
        <div class="bg-gray-900 rounded-3xl border border-gray-800 overflow-hidden shadow-lg flex flex-col">
            <div class="px-6 py-6 flex justify-between items-center">
                <h3 class="font-bold text-white flex items-center gap-2 text-lg">
                    <i class="fa-solid fa-clock-rotate-left text-purple-400"></i>
                    Fluxo Recente de Transações
                </h3>
                <a href="{{ route('stocks.history') }}" class="text-[10px] font-bold text-purple-400 hover:text-white uppercase tracking-widest transition-colors flex items-center gap-1">
                    Ver Todos <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </a>
            </div>
            
            <!-- Table Header -->
            <div class="bg-[#8B5CF6] px-6 py-3 flex text-[10px] font-black text-white uppercase tracking-widest">
                <div class="w-1/3">Referência</div>
                <div class="w-1/3">Identificação</div>
                <div class="w-1/3 text-right">Cronologia</div>
            </div>

            <div class="divide-y divide-gray-800 flex-1">
                @forelse($recentActivity as $activity)
                <div class="px-6 py-4 flex items-center hover:bg-white/5 transition-colors">
                    <div class="w-1/3">
                        <span class="px-2 py-1 rounded-md bg-white/10 border border-white/10 text-[10px] font-bold text-white uppercase tracking-wider">
                            {{ $activity->action_type == 'entrada' ? 'ENTRADA' : 'SAÍDA' }}
                        </span>
                    </div>
                    <div class="w-1/3">
                        <div class="font-bold text-white text-sm truncate">{{ $activity->cutType->name ?? 'Item' }}</div>
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5 truncate">{{ $activity->user->name ?? 'Sistema' }}</div>
                    </div>
                    <div class="w-1/3 text-right">
                        <span class="text-xs font-bold text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <p class="text-sm">Nenhuma atividade recente.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart.js Configuration
        Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.size = 11;

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
                        backgroundColor: '#34d399', // emerald-400
                        borderRadius: 4,
                        barThickness: 12,
                    },
                    {
                        label: 'Saídas',
                        data: {!! json_encode($movementsData['exits']) !!},
                        backgroundColor: '#f87171', // red-400
                        borderRadius: 4,
                        barThickness: 12,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Custom legend in HTML
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false,
                        },
                        ticks: { padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { padding: 10 }
                    }
                }
            }
        });

        // Store Distribution Chart
        const ctxStore = document.getElementById('storeChart').getContext('2d');
        const storeData = {!! json_encode($stockByStore) !!};
        
        // Handle empty scenario nicely
        const hasData = Object.keys(storeData).length > 0;
        
        new Chart(ctxStore, {
            type: 'doughnut',
            data: {
                labels: hasData ? Object.keys(storeData) : ['Sem dados'],
                datasets: [{
                    data: hasData ? Object.values(storeData) : [1],
                    backgroundColor: hasData ? [
                        '#818cf8', // Indigo
                        '#34d399', // Emerald
                        '#f472b6', // Pink
                        '#fbbf24', // Amber
                        '#60a5fa', // Blue
                    ] : ['#e5e7eb'], // Gray for empty
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'left',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
