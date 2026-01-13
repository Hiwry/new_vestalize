@extends('layouts.admin')

@section('content')
<style>
/* Estilo minimalista inspirado em planilha */
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

<!-- Header Compacto -->
<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Consulta de Estoque</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400">Visualização somente leitura</p>
    </div>
</div>

<!-- Filtros Compactos -->
<div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-3 mb-4">
    <form method="GET" action="{{ route('stocks.view') }}">
        <div class="grid grid-cols-6 gap-2 mb-2">
            <select name="store_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <option value="">Todas Lojas</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                @endforeach
            </select>

            <select name="fabric_type_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <option value="">Todos Tipos de Tecido</option>
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
                @foreach($sizes as $sizeOption)
                    <option value="{{ $sizeOption }}" {{ $sizeOption == $size ? 'selected' : '' }}>{{ $sizeOption }}</option>
                @endforeach
            </select>

            <div class="flex gap-1">
                <button type="submit" class="flex-1 px-2 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700">
                    Filtrar
                </button>
                <a href="{{ route('stocks.view') }}" class="px-2 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs font-semibold hover:bg-gray-300 dark:hover:bg-gray-600">
                    Limpar
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabela Estilo Planilha -->
<div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        @forelse($groupedStocks as $key => $group)
        <div class="border-b-2 border-gray-300 dark:border-gray-600">
            <!-- Cabeçalho do Grupo -->
            <div class="bg-gray-100 dark:bg-gray-700 px-3 py-2 flex items-center justify-between text-xs font-bold">
                <div class="flex items-center gap-3">
                    <span class="text-indigo-700 dark:text-indigo-400">{{ $group['store']['name'] }}</span>
                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 rounded-full font-semibold border border-amber-300 dark:border-amber-700">
                        🧵 {{ $group['fabric_type']['name'] ?? $group['fabric']['name'] ?? '-' }}
                    </span>
                    @if($group['cut_type']['name'] ?? null)
                    <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300 rounded-full font-semibold border border-purple-300 dark:border-purple-700">
                        ✂️ {{ $group['cut_type']['name'] }}
                    </span>
                    @endif
                    @if($group['shelf'] ?? null)
                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">📍 {{ $group['shelf'] }}</span>
                    @endif
                </div>
            </div>

            <!-- Tabela de Tamanhos -->
            <table class="w-full stock-table">
                <thead>
                    <tr>
                        <th class="text-left">COR</th>
                        @foreach($sizes as $sizeCol)
                        <th>{{ $sizeCol }}</th>
                        @endforeach
                        <th class="bg-indigo-50 dark:bg-indigo-900/30">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="text-left font-semibold text-gray-900 dark:text-gray-100">
                            {{ $group['color']['name'] ?? '-' }}
                        </td>
                        @foreach($sizes as $sizeCol)
                        <td class="stock-cell">
                            @if(isset($group['sizes'][$sizeCol]))
                            @php
                                $sizeData = $group['sizes'][$sizeCol];
                                $qty = $sizeData['available'];
                                $reserved = $sizeData['reserved'];
                                $minStock = $sizeData['min_stock'] ?? 5;
                                
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
                            {{ $group['group_total'] }}
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

<!-- Legenda -->
<div class="mt-4 flex items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
    <span class="font-semibold">Legenda:</span>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-high rounded"></div>
        <span>Alto</span>
    </div>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-medium rounded"></div>
        <span>Médio</span>
    </div>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-low rounded"></div>
        <span>Baixo</span>
    </div>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-zero rounded"></div>
        <span>Zerado</span>
    </div>
    <span class="ml-4 text-orange-600 dark:text-orange-400">(N) = Reservado</span>
</div>

@endsection
