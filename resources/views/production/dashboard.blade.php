@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Dashboard de Produção</h1>
</div>

<!-- Filtro de Período e Colunas -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
    <form method="GET" action="{{ route('production.dashboard') }}" id="dashboard-filter-form" class="space-y-4">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Período</label>
                <select name="period" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Semana</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Mês</option>
                    <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Trimestre</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Ano</option>
                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Personalizado</option>
                </select>
            </div>
            @if($period == 'custom')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Inicial</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Final</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>
            @endif
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                Filtrar
            </button>
        </div>
        
        <!-- Seleção de Colunas -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Colunas do Kanban</label>
                <div class="flex gap-2">
                    <button type="button" onclick="selectAllColumns()" class="text-xs px-2 py-1 text-indigo-600 dark:text-indigo-400 hover:underline">
                        Selecionar Todas
                    </button>
                    <button type="button" onclick="deselectAllColumns()" class="text-xs px-2 py-1 text-gray-600 dark:text-gray-400 hover:underline">
                        Desmarcar Todas
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($allStatuses as $status)
                <label class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <input type="checkbox" 
                           name="columns[]" 
                           value="{{ $status->id }}"
                           {{ in_array($status->id, $selectedColumns) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 column-checkbox">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $status->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
    </form>
</div>

<!-- Estatísticas Gerais -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Total de Pedidos</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalOrders }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Em Produção</div>
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $ordersInProduction }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Tempo Médio Total</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            @if($avgProductionTime)
                @php
                    $days = floor($avgProductionTime / 86400);
                    $hours = floor(($avgProductionTime % 86400) / 3600);
                    $minutes = floor(($avgProductionTime % 3600) / 60);
                    $formatted = '';
                    if ($days > 0) $formatted .= $days . 'd ';
                    if ($hours > 0) $formatted .= $hours . 'h ';
                    $formatted .= $minutes . 'm';
                @endphp
                {{ $formatted }}
            @else
                N/A
            @endif
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Setor Mais Lento</div>
        <div class="text-lg font-bold text-red-600 dark:text-red-400">
            {{ $slowestStatus['status_name'] ?? 'N/A' }}
        </div>
        @if($slowestStatus)
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ $slowestStatus['avg_formatted'] ?? 'N/A' }}
        </div>
        @endif
    </div>
</div>

<!-- Carrossel de Pedidos por Data de Entrega -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Pedidos por Data de Entrega</h2>
        <div class="flex gap-2">
            <button onclick="changeDeliveryFilter('today')" 
                    class="px-4 py-2 rounded-md text-sm font-medium transition {{ $deliveryFilter == 'today' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Hoje
            </button>
            <button onclick="changeDeliveryFilter('week')" 
                    class="px-4 py-2 rounded-md text-sm font-medium transition {{ $deliveryFilter == 'week' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Esta Semana
            </button>
            <button onclick="changeDeliveryFilter('month')" 
                    class="px-4 py-2 rounded-md text-sm font-medium transition {{ $deliveryFilter == 'month' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Este Mês
            </button>
        </div>
    </div>
    
    @if(isset($deliveryOrders) && $deliveryOrders && $deliveryOrders->count() > 0)
    <div class="relative">
        <!-- Carrossel Container -->
        <div id="delivery-carousel" class="overflow-hidden">
            <div class="flex gap-4 overflow-x-auto pb-4" id="carousel-track">
                @foreach($deliveryOrders as $order)
                @php
                    $firstItem = $order->items->first();
                    $coverImage = $order->cover_image_url ?? $firstItem?->cover_image_url;
                    $artName = $firstItem?->art_name;
                    $displayName = $artName ?? ($order->client?->name ?? 'Sem cliente');
                    $storeName = $order->store?->name ?? 'Loja Principal';
                @endphp
                <div class="carousel-slide flex-shrink-0" style="min-width: 320px; max-width: 320px;">
                    <div class="kanban-card bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow dark:shadow-gray-900/25 rounded-lg overflow-hidden cursor-pointer hover:shadow-xl dark:hover:shadow-gray-900/50 transition-all duration-200 border"
                         onclick="window.location.href='{{ route('orders.show', $order->id) }}'">
                        
                        <!-- Imagem de Capa -->
                        @if($coverImage)
                        <div class="h-48 bg-gray-200 dark:bg-gray-700 overflow-hidden">
                            <img src="{{ $coverImage }}" 
                                 alt="Capa do Pedido" 
                                 class="w-full h-48 object-cover"
                                 style="object-fit: cover; object-position: center;"
                                 onerror="this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>'">
                        </div>
                        @else
                        <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        @endif

                        <!-- Conteúdo do Card -->
                        <div class="p-4">
                            <!-- Número do Pedido e Cliente -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('orders.show', $order->id) }}" 
                                           class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/50 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                        </a>
                                        @if($order->is_event)
                                        <span class="text-xs font-medium bg-red-500 dark:bg-red-600 text-white px-2 py-1 rounded-full">
                                            EVENTO
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $order->items->sum('quantity') }} pçs
                                    </span>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm truncate mb-1" title="{{ $displayName }}">
                                    {{ $displayName }}
                                </h3>
                                @if($storeName)
                                <div class="flex items-center text-xs text-indigo-700 dark:text-indigo-400">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 6l9 4 9-4m-9 4v6" />
                                    </svg>
                                    <span class="truncate" title="{{ $storeName }}">
                                        <strong>Loja:</strong> {{ $storeName }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            <!-- Data de Entrega -->
                            @if($order->delivery_date)
                            <div class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Data de Entrega</div>
                                    <div class="text-xs font-semibold text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                                @php
                                    $deliveryDate = \Carbon\Carbon::parse($order->delivery_date)->startOfDay();
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $daysUntilDelivery = (int) $today->diffInDays($deliveryDate, false);
                                @endphp
                                <div class="mt-1 text-xs font-medium {{ $daysUntilDelivery < 0 ? 'text-red-600 dark:text-red-400' : ($daysUntilDelivery == 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400') }}">
                                    @if($daysUntilDelivery < 0)
                                        Atrasado {{ abs($daysUntilDelivery) }} dia(s)
                                    @elseif($daysUntilDelivery == 0)
                                        Entrega hoje!
                                    @else
                                        Em {{ $daysUntilDelivery }} dia(s)
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Informações do Pedido -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Itens:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $order->items->sum('quantity') }} pçs</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium" style="background: {{ $order->status->color ?? '#6B7280' }}20; color: {{ $order->status->color ?? '#6B7280' }}">
                                        {{ $order->status->name ?? 'Sem status' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Controles do Carrossel -->
        <div class="flex justify-center items-center mt-4 gap-2">
            <button onclick="previousSlide()" 
                    class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button onclick="nextSlide()" 
                    class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
    @else
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        Nenhum pedido encontrado para o período selecionado
    </div>
    @endif
</div>

<!-- Tempo Médio por Setor -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Tempo Médio por Setor</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Setor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tempo Médio</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tempo Mínimo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tempo Máximo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedidos</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($statuses as $status)
                @php
                    $stat = collect($statusStats)->firstWhere('status_id', $status->id);
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $status->name }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stat['avg_formatted'] ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $stat['min_formatted'] ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $stat['max_formatted'] ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $ordersByStatus[$status->id] ?? 0 }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pedidos por Status -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Pedidos por Status</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($statuses as $status)
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $status->name }}</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $ordersByStatus[$status->id] ?? 0 }}
            </div>
        </div>
        @endforeach
    </div>
</div>


<script>
// Selecionar todas as colunas
function selectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(cb => {
        cb.checked = true;
    });
}

// Desmarcar todas as colunas
function deselectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(cb => {
        cb.checked = false;
    });
}

// Auto-submit quando colunas forem alteradas
document.querySelectorAll('.column-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        // Pequeno delay para permitir múltiplas seleções
        setTimeout(() => {
            document.getElementById('dashboard-filter-form').submit();
        }, 300);
    });
});

// Carrossel de Pedidos - Scroll Horizontal
let currentIndex = 0;
let totalSlides = {{ isset($deliveryOrders) ? $deliveryOrders->count() : 0 }};
let cardWidth = 336; // 320px (min-width) + 16px (gap)
let autoSlideInterval = null;

function updateSlidesPerView() {
    const container = document.getElementById('carousel-track');
    if (!container) return;
    
    const containerWidth = container.offsetWidth;
    if (window.innerWidth >= 1024) {
        cardWidth = 336; // 3 cards
    } else if (window.innerWidth >= 768) {
        cardWidth = 336; // 2 cards
    } else {
        cardWidth = 336; // 1 card
    }
}

function nextSlide() {
    const track = document.getElementById('carousel-track');
    if (!track) return;
    
    updateSlidesPerView();
    const maxScroll = track.scrollWidth - track.offsetWidth;
    
    if (track.scrollLeft < maxScroll) {
        track.scrollBy({ left: cardWidth, behavior: 'smooth' });
    } else {
        track.scrollTo({ left: 0, behavior: 'smooth' });
    }
    resetAutoSlide();
}

function previousSlide() {
    const track = document.getElementById('carousel-track');
    if (!track) return;
    
    updateSlidesPerView();
    
    if (track.scrollLeft > 0) {
        track.scrollBy({ left: -cardWidth, behavior: 'smooth' });
    } else {
        track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
    }
    resetAutoSlide();
}

function startAutoSlide() {
    if (totalSlides <= 3) return; // Não precisa auto-slide se todos cabem na tela
    
    autoSlideInterval = setInterval(() => {
        nextSlide();
    }, 5000); // Muda a cada 5 segundos
}

function resetAutoSlide() {
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }
    startAutoSlide();
}

// Tornar a função globalmente acessível
window.changeDeliveryFilter = function(filter) {
    const form = document.getElementById('dashboard-filter-form');
    if (!form) return;
    
    // Adicionar input hidden com o filtro
    let filterInput = form.querySelector('input[name="delivery_filter"]');
    if (!filterInput) {
        filterInput = document.createElement('input');
        filterInput.type = 'hidden';
        filterInput.name = 'delivery_filter';
        form.appendChild(filterInput);
    }
    filterInput.value = filter;
    
    form.submit();
};

// Inicializar scripts quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    updateSlidesPerView();
    startAutoSlide();
    
    // Ajustar ao redimensionar a janela
    window.addEventListener('resize', updateSlidesPerView);
    
    // Pausar auto-slide ao passar o mouse
    const carousel = document.getElementById('delivery-carousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        });
        carousel.addEventListener('mouseleave', () => {
            startAutoSlide();
        });
    }
});
</script>
@endsection
