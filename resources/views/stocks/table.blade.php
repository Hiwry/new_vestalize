@extends('layouts.admin')

@section('content')
<style>
.stock-table {
    font-size: 11px;
    border-collapse: collapse;
}
.stock-table th {
    background: #f8f9fa;
    font-weight: 700;
    text-transform: uppercase;
    padding: 6px 8px;
    border: 1px solid #dee2e6;
    font-size: 10px;
}
.dark .stock-table th {
    background: #1f2937;
    border-color: #374151;
}
.stock-table td {
    padding: 4px 6px;
    border: 1px solid #e9ecef;
    text-align: center;
}
.dark .stock-table td {
    border-color: #374151;
}
.stock-cell {
    min-width: 35px;
    font-weight: 600;
}
.stock-high { background: #d4edda !important; color: #155724; }
.stock-medium { background: #fff3cd !important; color: #856404; }
.stock-low { background: #f8d7da !important; color: #721c24; }
.stock-zero { background: #f8f9fa !important; color: #6c757d; }
.dark .stock-high { background: #064e3b !important; color: #6ee7b7; }
.dark .stock-medium { background: #78350f !important; color: #fcd34d; }
.dark .stock-low { background: #7f1d1d !important; color: #fca5a5; }
.dark .stock-zero { background: #1f2937 !important; color: #6b7280; }
</style>

<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-4">
    {{-- Success message is shown in layout --}}

    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Consulta de Estoque</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Gerenciamento completo de inventario e insumos.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('stocks.dashboard') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-gray-300 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                <i class="fa-solid fa-chart-pie text-indigo-500"></i>
                Dashboard
            </a>
            <a href="{{ route('stocks.history') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-gray-300 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                <i class="fa-solid fa-clock-rotate-left text-gray-500"></i>
                Historico
            </a>
            <a href="{{ route('stock-requests.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-amber-300 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:text-amber-300 dark:border-amber-700 dark:hover:bg-amber-900/20">
                <i class="fa-solid fa-file-invoice"></i>
                Solicitacoes
            </a>
            <a href="{{ route('stocks.create') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded border border-emerald-300 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:border-emerald-700 dark:hover:bg-emerald-900/20">
                <i class="fa-solid fa-plus"></i>
                Novo Item
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-3">
        <form method="GET" action="{{ route('stocks.index') }}" class="space-y-2">
            <input type="hidden" name="view" value="table">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text"
                           name="search_id"
                           value="{{ request('search_id') }}"
                           placeholder="Buscar por ID..."
                           class="w-full text-xs pl-8 pr-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>

                <select name="store_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Todas Lojas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>

                <select name="fabric_type_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Todos Tecidos</option>
                    @foreach($fabricTypes as $fabricType)
                        <option value="{{ $fabricType->id }}" {{ request('fabric_type_id') == $fabricType->id ? 'selected' : '' }}>{{ $fabricType->name }}</option>
                    @endforeach
                </select>

                <select name="color_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Todas Cores</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ $colorId == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                    @endforeach
                </select>

                <select name="cut_type_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Todos Tipos</option>
                    @foreach($cutTypes as $cutType)
                        <option value="{{ $cutType->id }}" {{ $cutTypeId == $cutType->id ? 'selected' : '' }}>{{ $cutType->name }}</option>
                    @endforeach
                </select>

                <select name="size" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Todos Tamanhos</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size }}" {{ $size == request('size') ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <label class="inline-flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="low_stock" value="1" {{ $lowStock ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600">
                    Apenas estoque critico
                </label>

                <div class="flex gap-1">
                    <a href="{{ route('stocks.index', ['view' => 'table']) }}" class="px-2 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs font-semibold hover:bg-gray-300 dark:hover:bg-gray-600">
                        Limpar
                    </a>
                    <button type="submit" class="px-2 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700">
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            @forelse($groupedStocks as $key => $group)
            <div class="border-b-2 border-gray-300 dark:border-gray-600">
                <div class="bg-gray-100 dark:bg-gray-700 px-3 py-2 flex flex-col md:flex-row md:items-center justify-between gap-2 text-xs font-bold">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-indigo-700 dark:text-indigo-400">{{ $group['store']['name'] }}</span>
                        <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 rounded-full font-semibold border border-amber-300 dark:border-amber-700">
                            {{ $group['fabric_type']['name'] ?? $group['fabric']['name'] ?? '-' }}
                        </span>

                        @if($group['cut_type']['name'] ?? null)
                        <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300 rounded-full font-semibold border border-purple-300 dark:border-purple-700">
                            {{ $group['cut_type']['name'] }}
                        </span>
                        @endif

                        @php
                            $shelf = null;
                            foreach($sizes as $size) {
                                if(isset($group['sizes'][$size]['shelf']) && $group['sizes'][$size]['shelf']) {
                                    $shelf = $group['sizes'][$size]['shelf'];
                                    break;
                                }
                            }
                        @endphp

                        @if($shelf)
                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">
                            {{ $shelf }}
                        </span>
                        @endif
                    </div>

                    <div class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold">
                        Atualizado em {{ \Carbon\Carbon::parse($group['last_updated'])->format('d/m/Y') }}
                    </div>
                </div>

                <table class="w-full stock-table">
                    <thead>
                        <tr>
                            <th class="text-left">COR</th>
                            @foreach($sizes as $size)
                            <th>{{ $size }}</th>
                            @endforeach
                            <th class="bg-indigo-50 dark:bg-indigo-900/30">TOTAL</th>
                            <th class="text-right">ACOES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="text-left font-semibold text-gray-900 dark:text-gray-100">
                                @php
                                    $colorHex = $group['color']['hex'] ?? null;
                                    $colorSwatch = (is_string($colorHex) && preg_match('/^#?[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', trim($colorHex)))
                                        ? ('#' . ltrim(trim($colorHex), '#'))
                                        : '#6b7280';
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-3.5 h-3.5 rounded-full border border-white/30 dark:border-gray-500 shadow-sm"
                                          style="background-color: {{ $colorSwatch }};"
                                          title="{{ $group['color']['name'] ?? '-' }}"></span>
                                    <span>{{ $group['color']['name'] ?? '-' }}</span>
                                </div>
                            </td>

                            @foreach($sizes as $size)
                            <td class="stock-cell">
                                @if(isset($group['sizes'][$size]))
                                @php
                                    $sizeData = $group['sizes'][$size];
                                    $qty = (int)($sizeData['available_quantity'] ?? 0);
                                    $reserved = (int)($sizeData['reserved_quantity'] ?? 0);
                                    $minStock = (int)($sizeData['min_stock'] ?? 5);

                                    if ($qty == 0) $class = 'stock-zero';
                                    elseif ($qty < $minStock) $class = 'stock-low';
                                    elseif ($qty < $minStock * 2) $class = 'stock-medium';
                                    else $class = 'stock-high';
                                @endphp
                                <div class="{{ $class }} rounded px-1 py-0.5">
                                    <span class="block">
                                        {{ $qty }}
                                        @if($reserved > 0)
                                        <span class="text-orange-600 dark:text-orange-400 font-bold">({{ $reserved }})</span>
                                        @endif
                                    </span>
                                </div>
                                @else
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                                @endif
                            </td>
                            @endforeach

                            <td class="bg-indigo-50 dark:bg-indigo-900/30 font-bold text-indigo-700 dark:text-indigo-300">
                                {{ $group['total_available'] }}
                            </td>

                            <td class="text-right">
                                @php
                                    $firstStockId = null;
                                    if(isset($group['sizes']) && is_array($group['sizes'])) {
                                        foreach($sizes as $size) {
                                            if(isset($group['sizes'][$size]) && isset($group['sizes'][$size]['id']) && $group['sizes'][$size]['id'] > 0) {
                                                $firstStockId = (int)$group['sizes'][$size]['id'];
                                                break;
                                            }
                                        }
                                    }
                                @endphp

                                <div class="flex items-center justify-end gap-1">
                                    @if($firstStockId && $firstStockId > 0)
                                    <a href="{{ route('stocks.edit', [
                                        'store_id' => $group['store']['id'] ?? null,
                                        'fabric_id' => $group['fabric']['id'] ?? null,
                                        'fabric_type_id' => $group['fabric_type']['id'] ?? null,
                                        'color_id' => $group['color']['id'] ?? null,
                                        'cut_type_id' => $group['cut_type']['id'] ?? null
                                    ]) }}"
                                       class="inline-flex items-center justify-center w-7 h-7 rounded bg-purple-600 hover:bg-purple-700 text-white"
                                       title="Editar">
                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                    </a>

                                    <button type="button"
                                            onclick="openTransferModal({{ $firstStockId }}, '{{ addslashes($group['store']['name'] ?? '') }}', '{{ addslashes($group['fabric_type']['name'] ?? $group['fabric']['name'] ?? '') }}', '{{ addslashes($group['color']['name'] ?? '') }}', {{ json_encode($group['sizes']) }})"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-600 hover:bg-emerald-700 text-white"
                                            title="Transferir">
                                        <i class="fa-solid fa-arrow-right-arrow-left text-[11px]"></i>
                                    </button>

                                    <button type="button"
                                            onclick="openDeleteModal({{ $firstStockId }}, '{{ addslashes($group['fabric_type']['name'] ?? $group['fabric']['name'] ?? '') }}', '{{ addslashes($group['color']['name'] ?? '') }}')"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded bg-red-600 hover:bg-red-700 text-white"
                                            title="Excluir">
                                        <i class="fa-solid fa-trash text-[11px]"></i>
                                    </button>
                                    @else
                                    <span class="text-[11px] text-gray-500 italic">Indisponivel</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="font-medium">Nenhum estoque encontrado</p>
                <p class="text-sm mt-1">Tente ajustar os filtros</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-1 flex flex-wrap items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
        <span class="font-semibold">Legenda:</span>
        <div class="flex items-center gap-1">
            <div class="w-4 h-4 stock-high rounded"></div>
            <span>Alto</span>
        </div>
        <div class="flex items-center gap-1">
            <div class="w-4 h-4 stock-medium rounded"></div>
            <span>Medio</span>
        </div>
        <div class="flex items-center gap-1">
            <div class="w-4 h-4 stock-low rounded"></div>
            <span>Baixo</span>
        </div>
        <div class="flex items-center gap-1">
            <div class="w-4 h-4 stock-zero rounded"></div>
            <span>Zerado</span>
        </div>
        <span class="text-orange-600 dark:text-orange-400">(N) = Reservado</span>
    </div>
</div>

<!-- Modal de Transferencia -->
<div id="transfer-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all scale-100">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center">
                    <i class="fa-solid fa-arrow-right-arrow-left text-sm" style="color: #ffffff !important;"></i>
                </div>
                Transferir Estoque
            </h3>
            <button onclick="closeTransferModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form id="transfer-form" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Origem</label>
                <input type="text" id="transfer-from" readonly class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white font-bold text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Destino</label>
                <select name="target_store_id" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-green-500 dark:text-white text-sm font-bold appearance-none cursor-pointer">
                    <option value="">Selecione a loja de destino...</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Quantidades</label>
                <div id="transfer-sizes-container" class="space-y-2 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700 max-h-48 overflow-y-auto custom-scrollbar">
                    <!-- Inputs gerados via JS -->
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeTransferModal()" class="flex-1 px-4 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 shadow-lg shadow-green-200 dark:shadow-none transition-colors" style="color: #ffffff !important;">
                    Confirmar Transferencia
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Exclusao -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 text-red-500">
            <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
        </div>

        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Excluir Item?</h3>
        <p id="delete-message" class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
            Esta acao removera permanentemente o estoque selecionado.
        </p>

        <form id="delete-form" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancelar
            </button>
            <button type="submit" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 dark:shadow-none transition-colors" style="color: #ffffff !important;">
                Sim, excluir
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openTransferModal(stockId, storeName, fabric, color, sizes) {
    document.getElementById('transfer-from').value = `${storeName} - ${fabric} - ${color}`;
    document.getElementById('transfer-form').action = `/stocks/${stockId}/transfer`;

    const container = document.getElementById('transfer-sizes-container');
    container.innerHTML = '';

    let hasAvailable = false;
    const orderedSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];

    orderedSizes.forEach(size => {
        if (sizes && sizes[size]) {
            const data = sizes[size];
            const maxQty = data.available_quantity || 0;

            if (maxQty > 0) {
                hasAvailable = true;
                const div = document.createElement('div');
                div.className = 'flex items-center gap-4 p-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition-colors';
                div.innerHTML = `
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 text-xs">
                        ${size}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-bold text-gray-500 uppercase">Quantidade</span>
                            <span class="text-indigo-600 font-bold">Max: ${maxQty}</span>
                        </div>
                        <input type="number" name="quantities[${size}]" min="0" max="${maxQty}" placeholder="0"
                               class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-bold focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                `;
                container.appendChild(div);
            }
        }
    });

    if (!hasAvailable) {
        container.innerHTML = `
            <div class="text-center py-4">
                <p class="text-sm font-bold text-gray-400">Nenhum item disponivel para transferencia.</p>
            </div>
        `;
    }

    document.getElementById('transfer-modal').classList.remove('hidden');
}

function closeTransferModal() {
    document.getElementById('transfer-modal').classList.add('hidden');
}

document.getElementById('transfer-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTransferModal();
});

function openDeleteModal(stockId, fabric, color) {
    document.getElementById('delete-message').innerHTML = `Tem certeza que deseja excluir o estoque de <br><strong class="text-gray-900 dark:text-white">${fabric} - ${color}</strong>?`;
    document.getElementById('delete-form').action = `/stocks/${stockId}`;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

document.getElementById('delete-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush

@endsection
