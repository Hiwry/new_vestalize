@extends('layouts.admin')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    @if(session('success'))
    <div class="bg-emerald-500 text-white p-4 rounded-xl shadow-lg flex items-center justify-between animate-fade-in-down">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-check text-white"></i>
            </div>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
        @if(session('movement_id'))
        <a href="{{ route('stocks.movements.print', session('movement_id')) }}" target="_blank" 
           class="px-4 py-2 bg-white text-emerald-600 rounded-lg font-bold text-sm hover:bg-emerald-50 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            Imprimir Nota
        </a>
        @endif
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3 tracking-tight">
                <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl text-white shadow-lg shadow-emerald-200 dark:shadow-none transform rotate-3">
                    <i class="fa-solid fa-boxes-stacked text-2xl text-white" style="color: #ffffff !important;"></i>
                </div>
                Estoque Geral
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg font-medium ml-1">
                Gerenciamento completo de inventário e insumos.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
             <!-- Action Buttons -->
            <div class="flex gap-2">
                 <a href="{{ route('stocks.dashboard') }}" class="px-5 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-chart-pie text-indigo-500"></i>
                    Dashboard
                </a>
                <a href="{{ route('stocks.history') }}" class="px-5 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-clock-rotate-left text-gray-500"></i>
                    Histórico
                </a>
                <a href="{{ route('stock-requests.index') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-lg shadow-amber-200 dark:shadow-none transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95" style="color: #ffffff !important;">
                    <i class="fa-solid fa-file-invoice text-white" style="color: #ffffff !important;"></i>
                    Solicitações
                </a>
                <a href="{{ route('stocks.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-lg shadow-emerald-200 dark:shadow-none transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95" style="color: #ffffff !important;">
                    <i class="fa-solid fa-plus text-white" style="color: #ffffff !important;"></i>
                    Novo Item
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
        <form method="GET" action="{{ route('stocks.index') }}" class="space-y-4">
            <input type="hidden" name="view" value="table">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                    <input type="text" name="search_id" value="{{ request('search_id') }}" placeholder="Buscar por ID..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all dark:text-white text-sm font-medium">
                </div>

                <select name="store_id" class="w-full py-2.5 px-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all dark:text-white text-sm font-medium appearance-none cursor-pointer">
                    <option value="">Todas Lojas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>

                <select name="fabric_type_id" class="w-full py-2.5 px-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all dark:text-white text-sm font-medium appearance-none cursor-pointer">
                    <option value="">Todos Tecidos</option>
                    @foreach($fabricTypes as $fabricType)
                        <option value="{{ $fabricType->id }}" {{ request('fabric_type_id') == $fabricType->id ? 'selected' : '' }}>{{ $fabricType->name }}</option>
                    @endforeach
                </select>

                <select name="color_id" class="w-full py-2.5 px-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all dark:text-white text-sm font-medium appearance-none cursor-pointer">
                    <option value="">Todas Cores</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ $colorId == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                    @endforeach
                </select>

                <select name="cut_type_id" class="w-full py-2.5 px-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all dark:text-white text-sm font-medium appearance-none cursor-pointer">
                    <option value="">Todos Tipos</option>
                    @foreach($cutTypes as $cutType)
                        <option value="{{ $cutType->id }}" {{ $cutTypeId == $cutType->id ? 'selected' : '' }}>{{ $cutType->name }}</option>
                    @endforeach
                </select>
                
                 <select name="size" class="w-full py-2.5 px-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all dark:text-white text-sm font-medium appearance-none cursor-pointer">
                    <option value="">Todos Tamanhos</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size }}" {{ $size == request('size') ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="low_stock" value="1" {{ $lowStock ? 'checked' : '' }} class="peer sr-only">
                        <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-300 group-hover:text-indigo-600 transition-colors">Apenas estoque crítico</span>
                </label>

                <div class="flex gap-3">
                    <a href="{{ route('stocks.index', ['view' => 'table']) }}" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Limpar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95" style="color: #ffffff !important;">
                        <i class="fa-solid fa-filter text-white" style="color: #ffffff !important;"></i>
                        Filtrar Resultados
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Stock Groups -->
    <div class="space-y-6">
        @forelse($groupedStocks as $key => $group)
        <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <!-- Group Header -->
            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                         <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white shadow-sm">
                            <i class="fa-solid fa-store text-xs text-white" style="color: #ffffff !important;"></i>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $group['store']['name'] }}</span>
                    </div>

                    <div class="h-6 w-px bg-gray-300 dark:bg-gray-600 hidden md:block"></div>

                    <span class="px-3 py-1 rounded-full bg-amber-500 text-white text-xs font-bold shadow-sm flex items-center gap-1" style="color: #ffffff !important;">
                        <i class="fa-solid fa-scroll text-[10px] text-white" style="color: #ffffff !important;"></i>
                        {{ $group['fabric_type']['name'] ?? $group['fabric']['name'] ?? '-' }}
                    </span>

                    @if($group['cut_type']['name'] ?? null)
                    <span class="px-3 py-1 rounded-full bg-purple-500 text-white text-xs font-bold shadow-sm flex items-center gap-1" style="color: #ffffff !important;">
                        <i class="fa-solid fa-scissors text-[10px] text-white" style="color: #ffffff !important;"></i>
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
                    <span class="px-3 py-1 rounded-full bg-blue-500 text-white text-xs font-bold shadow-sm flex items-center gap-1" style="color: #ffffff !important;">
                        <i class="fa-solid fa-location-dot text-[10px] text-white" style="color: #ffffff !important;"></i>
                        {{ $shelf }}
                    </span>
                    @endif
                </div>
                
                 <div class="flex items-center gap-2 text-xs font-medium text-gray-400">
                    <i class="fa-regular fa-clock"></i>
                    Atualizado em {{ \Carbon\Carbon::parse($group['last_updated'])->format('d/m/Y') }}
                </div>
            </div>

            <!-- Content Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">Cor</th>
                            @foreach($sizes as $size)
                            <th scope="col" class="px-3 py-4 text-center font-bold text-gray-400">{{ $size }}</th>
                            @endforeach
                            <th scope="col" class="px-6 py-4 text-center font-bold bg-gray-50 dark:bg-gray-900/30">Total</th>
                            <th scope="col" class="px-6 py-4 text-right font-bold">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full border border-gray-200 dark:border-gray-600 shadow-sm" style="background-color: {{ $group['color']['hex'] ?? '#ccc' }}"></div>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $group['color']['name'] ?? '-' }}</span>
                                </div>
                            </td>
                            
                            @foreach($sizes as $size)
                            <td class="px-2 py-4 text-center">
                                @if(isset($group['sizes'][$size]))
                                    @php
                                        $sizeData = $group['sizes'][$size];
                                        $qty = $sizeData['available_quantity'];
                                        $reserved = $sizeData['reserved_quantity'];
                                        $minStock = $sizeData['min_stock'] ?? 5;
                                        
                                        $bgClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                                        if ($qty == 0) $bgClass = 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500';
                                        elseif ($qty < $minStock) $bgClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                                        elseif ($qty < $minStock * 2) $bgClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                                    @endphp
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="px-2.5 py-1 rounded-lg font-bold text-xs {{ $bgClass }}">
                                            {{ $qty }}
                                        </span>
                                        @if($reserved > 0)
                                            <span class="text-[10px] font-bold text-orange-500 mt-1" title="Reservado">R: {{ $reserved }}</span>
                                        @endif
                                        <span class="text-[9px] text-gray-300 dark:text-gray-600 mt-0.5">#{{ $sizeData['id'] }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-200 dark:text-gray-700">-</span>
                                @endif
                            </td>
                            @endforeach

                             <td class="px-6 py-4 text-center bg-gray-50 dark:bg-gray-900/30">
                                <span class="px-3 py-1 rounded-full bg-indigo-500 text-white text-xs font-bold shadow-sm" style="color: #ffffff !important;">
                                    {{ $group['total_available'] }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
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

                                <div class="flex items-center justify-end gap-2">
                                    @if($firstStockId && $firstStockId > 0)
                                    <a href="{{ route('stocks.edit', [
                                        'store_id' => $group['store']['id'] ?? null,
                                        'fabric_id' => $group['fabric']['id'] ?? null,
                                        'fabric_type_id' => $group['fabric_type']['id'] ?? null,
                                        'color_id' => $group['color']['id'] ?? null,
                                        'cut_type_id' => $group['cut_type']['id'] ?? null
                                    ]) }}" 
                                       class="w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all shadow-sm shadow-blue-200 dark:shadow-none" title="Editar" style="color: #ffffff !important;">
                                        <i class="fa-solid fa-pen text-xs text-white" style="color: #ffffff !important;"></i>
                                    </a>
                                    
                                    <button type="button" onclick="openTransferModal({{ $firstStockId }}, '{{ addslashes($group['store']['name'] ?? '') }}', '{{ addslashes($group['fabric_type']['name'] ?? $group['fabric']['name'] ?? '') }}', '{{ addslashes($group['color']['name'] ?? '') }}', {{ json_encode($group['sizes']) }})" 
                                       class="w-8 h-8 flex items-center justify-center bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all shadow-sm shadow-green-200 dark:shadow-none" title="Transferir" style="color: #ffffff !important;">
                                        <i class="fa-solid fa-arrow-right-arrow-left text-xs text-white" style="color: #ffffff !important;"></i>
                                    </button>
                                    
                                    <button type="button" onclick="openDeleteModal({{ $firstStockId }}, '{{ addslashes($group['fabric_type']['name'] ?? $group['fabric']['name'] ?? '') }}', '{{ addslashes($group['color']['name'] ?? '') }}')" 
                                       class="w-8 h-8 flex items-center justify-center bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all shadow-sm shadow-red-200 dark:shadow-none" title="Excluir" style="color: #ffffff !important;">
                                        <i class="fa-solid fa-trash text-xs text-white" style="color: #ffffff !important;"></i>
                                    </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Indisponível</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center shadow-sm border border-gray-100 dark:border-gray-700">
             <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-box-open text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum estoque encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400">Tente ajustar os filtros ou cadastre um novo item.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal de Transferência -->
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
                    Confirmar Transferência
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Exclusão -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 text-red-500">
             <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
        </div>
        
        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Excluir Item?</h3>
        <p id="delete-message" class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
            Esta ação removerá permanentemente o estoque selecionado.
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
                <p class="text-sm font-bold text-gray-400">Nenhum item disponível para transferência.</p>
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
