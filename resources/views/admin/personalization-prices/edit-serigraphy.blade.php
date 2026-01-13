@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{}">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Preços de Personalização
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">Serigrafia</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.personalization-prices.update', $type) }}" id="prices-form">
        @csrf
        @method('PUT')

        <!-- Gerenciamento de Tamanhos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tamanhos de Aplicação</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie os tamanhos disponíveis</p>
                    </div>
                    <button type="button" onclick="console.log('Botão Adicionar Tamanho clicado'); window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-size-modal' }));" 
                            class="inline-flex items-center px-3 py-2 bg-green-600 dark:bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Tamanho
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="sizes-container">
                    @foreach($sizes as $size)
                    <div class="size-item border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50" data-size="{{ $size }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <input type="text" value="{{ $size }}" 
                                       class="size-name-input w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 font-medium transition-all" 
                                       onchange="updateSizeName(this, '{{ $size }}')">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tamanho de aplicação</p>
                            </div>
                            <button type="button" onclick="removeSize('{{ $size }}')" 
                                    class="ml-3 p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                    title="Remover tamanho">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <p class="font-medium mb-2">Como gerenciar tamanhos:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• <strong>Editar:</strong> Clique no campo e digite o novo nome</li>
                                <li>• <strong>Adicionar:</strong> Use o botão "Adicionar Tamanho"</li>
                                <li>• <strong>Remover:</strong> Excluirá todos os preços associados</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preços Base -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Preços Base</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Por tamanho e quantidade</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="row-count" class="text-sm text-gray-600 dark:text-gray-400">0 faixas</span>
                        <button type="button" onclick="addQuantityRow()" 
                                class="inline-flex items-center px-3 py-2 bg-indigo-600 dark:bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar Faixa
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="min-w-full" id="price-table">
                    <thead>
                        <tr id="table-header" class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">DE</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">ATÉ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="price-tbody">
                        @php
                            $quantityGroups = [];
                            foreach ($prices as $sizeName => $priceList) {
                                foreach ($priceList as $priceItem) {
                                    $qtyKey = $priceItem->quantity_from . '_' . $priceItem->quantity_to;
                                    if (!isset($quantityGroups[$qtyKey])) {
                                        $quantityGroups[$qtyKey] = [
                                            'from' => $priceItem->quantity_from,
                                            'to' => $priceItem->quantity_to,
                                            'prices' => []
                                        ];
                                    }
                                    $quantityGroups[$qtyKey]['prices'][$sizeName] = $priceItem->price;
                                }
                            }
                            uasort($quantityGroups, function($a, $b) {
                                return $a['from'] <=> $b['from'];
                            });
                        @endphp
                        
                        @forelse($quantityGroups as $qtyIndex => $qtyGroup)
                        <tr class="price-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700" data-index="{{ $loop->index }}">
                            <td class="px-4 py-3">
                                <input type="number" name="prices[{{ $loop->index }}][from]" value="{{ $qtyGroup['from'] }}" min="1" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all"
                                       placeholder="10">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="prices[{{ $loop->index }}][to]" value="{{ $qtyGroup['to'] }}" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all"
                                       placeholder="∞">
                            </td>
                            @foreach($sizes as $size)
                            <td class="px-4 py-3">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                                    <input type="number" name="prices[{{ $loop->parent->index }}][{{ $size }}]" value="{{ $qtyGroup['prices'][$size] ?? '' }}" step="0.01" min="0" data-size="{{ $size }}"
                                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all"
                                           placeholder="0,00">
                                </div>
                            </td>
                            @endforeach
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="removeRow(this)" 
                                        class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                        title="Remover linha">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-message">
                            <td colspan="7" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="font-medium">Nenhuma faixa configurada</p>
                                <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Preços por Cor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Preços por Cor</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure preços adicionais por número de cores</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="color-row-count" class="text-sm text-gray-600 dark:text-gray-400">0 faixas</span>
                        <button type="button" onclick="addColorRow()" 
                                class="inline-flex items-center px-3 py-2 bg-purple-600 dark:bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 dark:hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar Faixa
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="min-w-full" id="color-price-table">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">DE</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">ATÉ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">PREÇO</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="color-price-tbody">
                        @if(isset($colorPrices) && $colorPrices->count() > 0)
                            @foreach($colorPrices as $index => $colorPrice)
                            <tr class="color-price-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700" data-index="{{ $index }}">
                                <td class="px-4 py-3">
                                    <input type="number" name="color_prices[{{ $index }}][from]" value="{{ $colorPrice->quantity_from }}" min="1" required
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all"
                                           placeholder="10">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="color_prices[{{ $index }}][to]" value="{{ $colorPrice->quantity_to }}" min="1"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all"
                                           placeholder="∞">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                                        <input type="number" name="color_prices[{{ $index }}][price]" value="{{ $colorPrice->price }}" step="0.01" min="0"
                                               class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all"
                                               placeholder="0,00">
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="removeColorRow(this)" 
                                            class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            title="Remover linha">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr id="color-empty-message">
                            <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                <p class="font-medium">Nenhuma faixa de cor configurada</p>
                                <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calculadora -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Calculadora</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Teste os preços configurados</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Tamanho</label>
                        <select id="calc-size" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                            @foreach($sizes as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Quantidade</label>
                        <input type="number" id="calc-qty" value="10" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Nº de Cores</label>
                        <input type="number" id="calc-colors" value="1" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Preço base:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100" id="calc-base">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Preço por cor:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100" id="calc-color-price">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total cores (<span id="calc-colors-display">0</span>):</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100" id="calc-colors-total">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-600 text-base">
                            <span class="font-bold text-gray-900 dark:text-gray-100">Total por peça:</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400" id="calc-total">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span class="font-bold text-gray-900 dark:text-gray-100">Pedido (<span id="calc-qty-display">0</span> pçs):</span>
                            <span class="font-bold text-xl text-indigo-600 dark:text-indigo-400" id="calc-order-total">R$ 0,00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="flex flex-col sm:flex-row justify-between gap-3">
            <a href="{{ route('admin.personalization-prices.index') }}" 
               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
            <button type="submit" 
                    class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Salvar Preços
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let rowIndex = {{ is_array($quantityGroups) ? count($quantityGroups) : (isset($quantityGroups) ? $quantityGroups->count() : 0) }};
    let colorRowIndex = {{ isset($colorPrices) ? $colorPrices->count() : 0 }};
    let availableSizes = {!! json_encode($sizes) !!};

    document.addEventListener('DOMContentLoaded', function() {
        updateTableHeaders();
        updateCalculator();
        updateRowCount();
        updateColorRowCount();
        
        // Event listeners para calculadora
        document.getElementById('calc-size').addEventListener('change', updateCalculator);
        document.getElementById('calc-qty').addEventListener('input', updateCalculator);
        document.getElementById('calc-colors').addEventListener('input', updateCalculator);
        
        // Listeners para atualizações na tabela refletirem na calculadora em tempo real
        document.getElementById('price-table').addEventListener('input', updateCalculator);
        document.getElementById('color-price-table').addEventListener('input', updateCalculator);
    });

    function updateTableHeaders() {
        const headerRow = document.getElementById('table-header');
        const existingHeaders = headerRow.querySelectorAll('th');
        
        if (existingHeaders.length <= 3) {
            const fixedHeaders = [existingHeaders[0], existingHeaders[1], existingHeaders[2]];
            headerRow.innerHTML = '';
            headerRow.appendChild(fixedHeaders[0]);
            headerRow.appendChild(fixedHeaders[1]);
            
            availableSizes.forEach(size => {
                const th = document.createElement('th');
                th.textContent = size;
                th.className = 'px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase';
                headerRow.appendChild(th);
            });
            
            headerRow.appendChild(fixedHeaders[2]);
            updateAllTableRows();
        }
    }

    function updateAllTableRows() {
        const rows = document.querySelectorAll('.price-row');
        rows.forEach(row => updateTableRow(row));
    }

    function updateTableRow(row) {
        const cells = row.querySelectorAll('td');
        const fromCell = cells[0];
        const toCell = cells[1];
        const actionsCell = cells[cells.length - 1];
        
        const existingValues = {};
        availableSizes.forEach(size => {
            const existingInput = row.querySelector(`input[name*="[${size}]"]`);
            if (existingInput) existingValues[size] = existingInput.value;
        });
        
        row.innerHTML = '';
        row.appendChild(fromCell);
        row.appendChild(toCell);
        
        availableSizes.forEach(size => {
            const td = document.createElement('td');
            td.className = 'px-4 py-3';
            const div = document.createElement('div');
            div.className = 'relative';
            const span = document.createElement('span');
            span.className = 'absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm';
            span.textContent = 'R$';
            const input = document.createElement('input');
            input.type = 'number';
            input.name = `prices[${row.dataset.index}][${size}]`;
            input.step = '0.01';
            input.min = '0';
            input.setAttribute('data-size', size);
            input.className = 'w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all';
            input.placeholder = '0,00';
            if (existingValues[size]) input.value = existingValues[size];
            div.appendChild(span);
            div.appendChild(input);
            td.appendChild(div);
            row.appendChild(td);
        });
        
        row.appendChild(actionsCell);
    }

    function addNewSize() {
        const input = document.getElementById('new-size-name-input');
        const newSizeName = input.value;
        if (!newSizeName || !newSizeName.trim()) return;
        
        const sizeName = newSizeName.trim().toUpperCase();
        if (availableSizes.includes(sizeName)) {
            alert('Este tamanho já existe!');
            return;
        }
        
        availableSizes.push(sizeName);
        addSizeCard(sizeName);
        updateTableHeaders();
        updateCalculatorOptions();
        
        // Limpar e fechar
        input.value = '';
        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-size-modal' }));
    }

    function addSizeCard(sizeName) {
        const container = document.getElementById('sizes-container');
        const sizeCard = document.createElement('div');
        sizeCard.className = 'size-item border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50';
        sizeCard.setAttribute('data-size', sizeName);
        sizeCard.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <input type="text" value="${sizeName}" class="size-name-input w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 font-medium transition-all" onchange="updateSizeName(this, '${sizeName}')">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tamanho de aplicação</p>
                </div>
                <button type="button" onclick="removeSize('${sizeName}')" class="ml-3 p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Remover tamanho">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(sizeCard);
    }

    function updateSizeName(input, oldName) {
        const newName = input.value.trim().toUpperCase();
        if (!newName) {
            alert('O nome não pode estar vazio!');
            input.value = oldName;
            return;
        }
        if (newName !== oldName && availableSizes.includes(newName)) {
            alert('Este nome já existe!');
            input.value = oldName;
            return;
        }
        const index = availableSizes.indexOf(oldName);
        if (index !== -1) availableSizes[index] = newName;
        input.closest('.size-item').setAttribute('data-size', newName);
        updateTableHeaders();
        updateCalculatorOptions();
    }

    function removeSize(sizeName) {
        if (availableSizes.length <= 1) {
            alert('Deve haver pelo menos um tamanho!');
            return;
        }
        if (!confirm(`Remover "${sizeName}"?\n\nIsso excluirá todos os preços associados.`)) return;
        
        const index = availableSizes.indexOf(sizeName);
        if (index !== -1) availableSizes.splice(index, 1);
        
        const card = document.querySelector(`[data-size="${sizeName}"]`);
        if (card) card.remove();
        
        updateTableHeaders();
        updateCalculatorOptions();
    }

    function addQuantityRow() {
        const tbody = document.getElementById('price-tbody');
        const emptyMessage = document.getElementById('empty-message');
        if (emptyMessage) emptyMessage.remove();
        
        const newRow = document.createElement('tr');
        newRow.className = 'price-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700';
        newRow.setAttribute('data-index', rowIndex);
        
        let html = `
            <td class="px-4 py-3">
                <input type="number" name="prices[${rowIndex}][from]" value="" min="1" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all" placeholder="10">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="prices[${rowIndex}][to]" value="" min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all" placeholder="∞">
            </td>
        `;
        
        availableSizes.forEach(size => {
            html += `
                <td class="px-4 py-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                        <input type="number" name="prices[${rowIndex}][${size}]" value="" step="0.01" min="0" data-size="${size}"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all" placeholder="0,00">
                    </div>
                </td>
            `;
        });
        
        html += `
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeRow(this)" class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Remover linha">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        
        newRow.innerHTML = html;
        tbody.appendChild(newRow);
        rowIndex++;
        updateRowCount();
        newRow.querySelector('input').focus();
    }

    function removeRow(button) {
        if (confirm('Remover esta faixa?')) {
            const row = button.closest('tr');
            row.remove();
            updateRowCount();
            
            const tbody = document.getElementById('price-tbody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr id="empty-message">
                        <td colspan="7" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="font-medium">Nenhuma faixa configurada</p>
                            <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                        </td>
                    </tr>
                `;
            }
        }
    }

    function updateRowCount() {
        const rows = document.querySelectorAll('.price-row').length;
        document.getElementById('row-count').textContent = `${rows} faixa${rows !== 1 ? 's' : ''}`;
    }

    function addColorRow() {
        const tbody = document.getElementById('color-price-tbody');
        const emptyMessage = document.getElementById('color-empty-message');
        if (emptyMessage) emptyMessage.remove();
        
        const newRow = document.createElement('tr');
        newRow.className = 'color-price-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700';
        newRow.setAttribute('data-index', colorRowIndex);
        newRow.innerHTML = `
            <td class="px-4 py-3">
                <input type="number" name="color_prices[${colorRowIndex}][from]" value="" min="1" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all" placeholder="10">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="color_prices[${colorRowIndex}][to]" value="" min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all" placeholder="∞">
            </td>
            <td class="px-4 py-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                    <input type="number" name="color_prices[${colorRowIndex}][price]" value="" step="0.01" min="0"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all" placeholder="0,00">
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeColorRow(this)" class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Remover linha">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        colorRowIndex++;
        updateColorRowCount();
        newRow.querySelector('input').focus();
    }

    function removeColorRow(button) {
        if (confirm('Remover esta faixa de cor?')) {
            const row = button.closest('tr');
            row.remove();
            updateColorRowCount();
            
            const tbody = document.getElementById('color-price-tbody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr id="color-empty-message">
                        <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                            <p class="font-medium">Nenhuma faixa de cor configurada</p>
                            <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                        </td>
                    </tr>
                `;
            }
        }
    }

    function updateColorRowCount() {
        const rows = document.querySelectorAll('.color-price-row').length;
        document.getElementById('color-row-count').textContent = `${rows} faixa${rows !== 1 ? 's' : ''}`;
    }

    function updateCalculator() {
        const size = document.getElementById('calc-size').value;
        const qty = parseInt(document.getElementById('calc-qty').value) || 0;
        const colors = parseInt(document.getElementById('calc-colors').value) || 1;
        
        let basePrice = 0;
        let colorPrice = 0;

        // Find Base Price
        const priceRows = document.querySelectorAll('.price-row');
        for (const row of priceRows) {
            const fromVal = parseInt(row.querySelector('input[name*="[from]"]').value) || 0;
            const toInput = row.querySelector('input[name*="[to]"]');
            const toVal = toInput.value ? parseInt(toInput.value) : Infinity;

            if (qty >= fromVal && qty <= toVal) {
                const priceInput = row.querySelector(`input[data-size="${size}"]`);
                if (priceInput) {
                    basePrice = parseFloat(priceInput.value) || 0;
                }
                break;
            }
        }
        
        // Find Color Price
        if (colors > 1) {
            const colorRows = document.querySelectorAll('.color-price-row');
            for (const row of colorRows) {
                const fromVal = parseInt(row.querySelector('input[name*="[from]"]').value) || 0;
                const toInput = row.querySelector('input[name*="[to]"]');
                const toVal = toInput.value ? parseInt(toInput.value) : Infinity;

                if (qty >= fromVal && qty <= toVal) {
                    const priceInput = row.querySelector('input[name*="[price]"]');
                    if (priceInput) {
                        colorPrice = parseFloat(priceInput.value) || 0;
                    }
                    break;
                }
            }
        }
        
        const additionalColors = colors > 1 ? colors - 1 : 0;
        const colorsTotal = colorPrice * additionalColors;
        const totalPerPiece = basePrice + colorsTotal;
        const orderTotal = totalPerPiece * qty;
        
        document.getElementById('calc-base').textContent = `R$ ${basePrice.toFixed(2).replace('.', ',')}`;
        document.getElementById('calc-color-price').textContent = `R$ ${colorPrice.toFixed(2).replace('.', ',')}`;
        document.getElementById('calc-colors-display').textContent = colors;
        document.getElementById('calc-colors-total').textContent = `R$ ${colorsTotal.toFixed(2).replace('.', ',')}`;
        document.getElementById('calc-total').textContent = `R$ ${totalPerPiece.toFixed(2).replace('.', ',')}`;
        document.getElementById('calc-qty-display').textContent = qty;
        document.getElementById('calc-order-total').textContent = `R$ ${orderTotal.toFixed(2).replace('.', ',')}`;
    }

    function updateCalculatorOptions() {
        const calcSizeSelect = document.getElementById('calc-size');
        const currentValue = calcSizeSelect.value;
        calcSizeSelect.innerHTML = '';
        availableSizes.forEach(size => {
            const option = document.createElement('option');
            option.value = size;
            option.textContent = size;
            if (size === currentValue) option.selected = true;
            calcSizeSelect.appendChild(option);
        });
        if (!availableSizes.includes(currentValue) && availableSizes.length > 0) {
            calcSizeSelect.value = availableSizes[0];
        }
    }
</script>
@endpush
<!-- Modal Adicionar Tamanho -->
<div x-data="{ open: false }" 
     x-init="console.log('Alpine: Modal Adicionar Tamanho inicializado')"
     @open-modal.window="console.log('Evento open-modal recebido:', $event.detail); if ($event.detail === 'add-size-modal') { open = true; $nextTick(() => $refs.sizeInput.focus()) }"
     @close-modal.window="if ($event.detail === 'add-size-modal') open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    
    <div @click="open = false" 
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-200 dark:border-gray-700">
        
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Novo Tamanho de Aplicação</h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6">
            <label for="new-size-name-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Nome do Tamanho (ex: PEITO P, COSTAS G, MANGA)
            </label>
            <input type="text" id="new-size-name-input" x-ref="sizeInput"
                   @keydown.enter="addNewSize()"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all uppercase"
                   placeholder="DIGITE O NOME...">
            
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                O nome será convertido para letras maiúsculas automaticamente.
            </p>
        </div>

        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button @click="open = false" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Cancelar
            </button>
            <button onclick="addNewSize()"
                    class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 active:scale-95 transition-all">
                Confirmar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Permitir fechar com ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-size-modal' }));
        }
    });
</script>
@endpush
@endsection
