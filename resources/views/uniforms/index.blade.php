@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Uniformes e EPIs</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Controle de Estoque (Botas, Capacetes, Uniformes)</p>
        </div>
        <div>
            <a href="{{ route('uniforms.create') }}" 
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo Item
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('uniforms.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, tamanho, cor..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todos</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                 <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">Filtrar</button>
            </div>
        </form>
    </div>

    {{-- Tabela Matrix --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-900 z-10 w-64 shadow-sm">Produto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tamanho</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cor/Gênero</th>
                        
                        {{-- Colunas de Lojas --}}
                        @foreach($stores as $store)
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l border-gray-200 dark:border-gray-700">
                                {{ $store->name }}
                            </th>
                        @endforeach

                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products as $product)
                        @php
                            $key = $product->name . '|' . $product->type . '|' . $product->color . '|' . $product->gender . '|' . $product->size;
                            $rowTotal = 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            {{-- Nome Fixo --}}
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-700 shadow-sm z-10">
                                {{ $product->name }}
                                <div class="text-xs font-normal text-gray-500">{{ ucfirst($product->type) }}</div>
                            </td>
                            
                            {{-- Tamanho --}}
                            <td class="px-4 py-3 text-sm font-bold text-gray-800 dark:text-gray-200">
                                {{ $product->size ?? '-' }}
                            </td>

                            {{-- Detalhes --}}
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                <div>{{ $product->color ?? '-' }}</div>
                                @if($product->gender)
                                    <div class="text-xs italic">{{ $product->gender }}</div>
                                @endif
                            </td>

                            {{-- Colunas de Estoque por Loja --}}
                            @foreach($stores as $store)
                                @php
                                    $item = $stockData[$key][$store->id] ?? null;
                                    $qty = $item ? $item->quantity : 0;
                                    $rowTotal += $qty;
                                    $isLow = $item && $item->min_stock > 0 && $qty <= $item->min_stock;
                                @endphp
                                <td class="px-4 py-3 text-center text-sm border-l border-gray-200 dark:border-gray-700 {{ $qty > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">
                                    @if($item)
                                        <a href="{{ route('uniforms.edit', $item) }}" class="hover:underline {{ $isLow ? 'text-red-500 font-bold' : '' }}">
                                            {{ $qty == 0 ? '-' : number_format($qty, 0) }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach

                            {{-- Total da Linha --}}
                            <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-gray-100 border-l border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                {{ number_format($rowTotal, 0) }}
                            </td>

                            {{-- Ações --}}
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('uniforms.create', ['name' => $product->name, 'type' => $product->type, 'color' => $product->color, 'size' => $product->size, 'gender' => $product->gender]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 text-xs font-medium">
                                   + Add
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($stores) + 6 }}" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p>Nenhum item encontrado</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
