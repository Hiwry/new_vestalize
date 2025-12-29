@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">2</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600 dark:text-indigo-400">Itens do Orçamento</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Etapa 2 de 4</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">50%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                <div class="bg-indigo-600 dark:bg-indigo-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 50%"></div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulário de Adicionar Item -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="form-title">Adicionar Novo Item</h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Configure os detalhes do item</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <form method="POST" action="{{ route('budget.items') }}" id="item-form" class="space-y-6" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="add_item" id="form-action">
                            <input type="hidden" name="editing_item_id" value="" id="editing-item-id">

                            <!-- Seção: Personalização -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Personalização *</h2>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">Selecione uma ou mais opções de personalização</p>
                                    <div class="grid grid-cols-2 gap-3" id="personalizacao-options">
                                        <!-- Será preenchido via JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Tecido -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Tecido</h2>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tecido *</label>
                                            <select name="tecido" id="tecido" onchange="loadTiposTecido()" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o tecido</option>
                                            </select>
                                        </div>
                                        <div id="tipo-tecido-container" style="display:none">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Tecido</label>
                                            <select name="tipo_tecido" id="tipo_tecido" onchange="onTipoTecidoChange()" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o tipo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Cor -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Cor do Tecido</h2>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cor *</label>
                                        <select name="cor" id="cor" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                            <option value="">Selecione a cor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Modelo e Detalhes -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Modelo e Detalhes</h2>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Corte *</label>
                                            <select name="tipo_corte" id="tipo_corte" onchange="onTipoCorteChange()" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o corte</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Detalhe</label>
                                            <select name="detalhe" id="detalhe" onchange="updatePrice()" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o detalhe</option>
                                            </select>
                                        </div>
                                        </div>
                                        <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Gola *</label>
                                        <select name="gola" id="gola" onchange="updatePrice()" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                            <option value="">Selecione a gola</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Quantidade Total -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 4h6"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Quantidade Total *</h2>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Informe a quantidade total de peças</p>
                                    
                                        <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total de Peças</label>
                                            <input type="number" 
                                                   name="quantity" 
                                                   id="quantity"
                                                   min="1"
                                               value="0"
                                                   required 
                                                   placeholder="Ex: 50"
                                               class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded text-center text-lg font-semibold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:placeholder-gray-400">
                                    </div>
                                </div>
                            </div>

                                                        <!-- Se��ǜo: Pre��os -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Pre�os</h2>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4" id="preco-calculado-container">
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Tipo de Corte:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100" id="price-corte">R$ 0,00</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Detalhe:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100" id="price-detalhe">R$ 0,00</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Gola:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100" id="price-gola">R$ 0,00</span>
                                        </div>
                                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-900 dark:text-gray-100 font-semibold">Valor Unit�rio:</span>
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400 text-lg" id="price-total">R$ 0,00</span>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="unit_price" id="unit_price" value="0">
                                <input type="hidden" name="preco_inclui_personalizacao" id="preco_inclui_personalizacao" value="0">
                            </div><!-- Seção: Observações do Item -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900 dark:text-gray-100">Observações do Item (opcional)</h2>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                                    <textarea name="notes" 
                                              id="notes" 
                                              rows="3" 
                                              placeholder="Ex: Cliente pediu urgência, observações especiais, etc."
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Informações importantes para este item</p>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" 
                                        onclick="cancelEdit()"
                                        id="cancel-edit-btn"
                                        class="hidden px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium">
                                    Cancelar Edição
                                </button>
                                <button type="submit" 
                                        id="submit-button"
                                        class="flex-1 px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition text-sm font-medium">
                                    <span id="submit-btn-text">Adicionar Item</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Itens Adicionados -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 sticky top-6">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Itens Adicionados</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($items ?? []) }} item(s)</p>
                    </div>
                    
                    <div class="p-6">
                            @if(empty($items))
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Nenhum item adicionado ainda</p>
                            <p class="text-xs text-gray-400">Preencha o formulário ao lado para adicionar o primeiro item</p>
                        </div>
                            @else
                            <div class="space-y-3">
                                @foreach($items as $index => $item)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md border border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item['print_type'] ?? 'Item ' . ($index + 1) }}
                                            @if(!empty($item['preco_inclui_personalizacao']) && $item['preco_inclui_personalizacao'] == '1')
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                                c/ arte
                                            </span>
                                            @endif
                                        </h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $item['quantity'] ?? 0 }} peças • R$ {{ number_format(($item['unit_price'] ?? 0) * ($item['quantity'] ?? 0), 2, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="flex gap-1">
                                            <button type="button" 
                                                    onclick="editItem({{ $index }})"
                                                    class="p-1 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" 
                                                    onclick="removeItem({{ $index }})"
                                                    class="p-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @if(!empty($item['fabric']) || !empty($item['color']))
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @if(!empty($item['fabric'])){{ $item['fabric'] }}@endif
                                        @if(!empty($item['fabric']) && !empty($item['color'])) • @endif
                                        @if(!empty($item['color'])){{ $item['color'] }}@endif
                                    </p>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total de Itens:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ count($items) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total de Peças:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ array_sum(array_column($items, 'quantity')) }}</span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('budget.items') }}" class="mt-6">
                                @csrf
                                <input type="hidden" name="action" value="continue">
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500 transition-all text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    Continuar para Personalização
                                </button>
                            </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        let options = {};
        let optionsWithParents = {};
        let selectedPersonalizacoes = [];
        const items = @json($items ?? []);

        document.addEventListener('DOMContentLoaded', function() {
            loadOptions();
        });

        function loadOptions() {
            fetch('/api/product-options')
                .then(response => response.json())
                .then(data => {
                    options = data;
                    return fetch('/api/product-options-with-parents');
                })
                .then(response => response.json())
                .then(data => {
                    optionsWithParents = data;
                    renderPersonalizacao();
                    renderTecidos();
                    renderCores();
                    renderTiposCorte();
                    renderDetalhes();
                    renderGolas();
                })
                .catch(error => {
                    console.error('Erro ao carregar opções:', error);
                    renderPersonalizacao();
                    renderTecidos();
                    renderCores();
                    renderTiposCorte();
                    renderDetalhes();
                    renderGolas();
                });
        }

        function renderPersonalizacao() {
            const container = document.getElementById('personalizacao-options');
            const items = options.personalizacao || [];
            
            container.innerHTML = items.map(item => `
                <label class="flex items-center p-3 border-2 rounded-md cursor-pointer hover:border-indigo-400 transition-all ${selectedPersonalizacoes.includes(item.id) ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/30' : 'border-gray-200 dark:border-gray-600'} min-h-[52px]">
                    <input type="checkbox" name="personalizacao[]" value="${item.id}" 
                           onchange="togglePersonalizacao(${item.id})"
                           class="personalizacao-checkbox mr-3 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" ${selectedPersonalizacoes.includes(item.id) ? 'checked' : ''}>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 break-words whitespace-normal leading-tight">${item.name}</span>
                </label>
            `).join('');
        }

        function togglePersonalizacao(id) {
            const index = selectedPersonalizacoes.indexOf(id);
            if (index > -1) {
                selectedPersonalizacoes.splice(index, 1);
            } else {
                selectedPersonalizacoes.push(id);
                
                // Verificar se é SUB. TOTAL
                const personalizacaoItem = (options.personalizacao || []).find(item => item.id === id);
                if (personalizacaoItem && personalizacaoItem.name && 
                    (personalizacaoItem.name.toUpperCase().includes('SUB') && personalizacaoItem.name.toUpperCase().includes('TOTAL'))) {
                    // Definir cor como branco automaticamente
                    setTimeout(() => {
                        renderCores(); // Re-renderizar cores primeiro
                        const corSelect = document.getElementById('cor');
                        if (corSelect) {
                            // Procurar opção "BRANCO" ou "Branco"
                            const brancaOption = Array.from(corSelect.options).find(opt => 
                                opt.text.toUpperCase().includes('BRANCO') || opt.text.toUpperCase().includes('BRANCA')
                            );
                            if (brancaOption) {
                                corSelect.value = brancaOption.value;
                            }
                        }
                        
                        // Zerar valores de tipo_corte, detalhe e gola
                        const tipoCorteSelect = document.getElementById('tipo_corte');
                        const detalheSelect = document.getElementById('detalhe');
                        const golaSelect = document.getElementById('gola');
                        
                        if (tipoCorteSelect) tipoCorteSelect.value = '';
                        if (detalheSelect) detalheSelect.value = '';
                        if (golaSelect) golaSelect.value = '';
                        
                        // Re-renderizar campos dependentes
                        renderTiposCorte();
                        renderDetalhes();
                        renderGolas();
                    }, 300);
                }
            }
            renderPersonalizacao();
            renderTecidos();
            renderCores();
            renderTiposCorte();
        }

        function renderTecidos() {
            const select = document.getElementById('tecido');
            let items = optionsWithParents.tecido || options.tecido || [];
            
            if (selectedPersonalizacoes.length > 0 && optionsWithParents.tecido) {
                items = items.filter(tecido => {
                    // Se não tem parent_ids, mostrar sempre
                    if (!tecido.parent_ids || tecido.parent_ids.length === 0) {
                        return true;
                    }
                    return tecido.parent_ids.some(parentId => selectedPersonalizacoes.includes(parentId));
                });
            }
            
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecione o tecido</option>' + 
                items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
            
            if (currentValue && items.find(item => item.id == currentValue)) {
                select.value = currentValue;
            } else {
                select.value = '';
                loadTiposTecido();
                // Atualizar cores quando tecido mudar
                renderCores();
            }
        }

        function loadTiposTecido() {
            const tecidoId = document.getElementById('tecido').value;
            const container = document.getElementById('tipo-tecido-container');
            const select = document.getElementById('tipo_tecido');
            
            if (!tecidoId) {
                container.style.display = 'none';
                if (select) {
                    select.value = '';
                    select.required = false;
                }
                // Atualizar cores e tipos de corte quando tecido for removido
                renderCores();
                renderTiposCorte();
                return;
            }

            const items = (options.tipo_tecido || []).filter(t => t.parent_id == tecidoId);
            
            if (items.length > 0) {
                container.style.display = 'block';
                const currentValue = select.value;
                select.innerHTML = '<option value="">Selecione o tipo</option>' + 
                    items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
                select.required = true;
                
                // Restaurar valor se ainda existir
                if (currentValue && items.find(item => item.id == currentValue)) {
                    select.value = currentValue;
                } else {
                    select.value = '';
                }
            } else {
                container.style.display = 'none';
                if (select) {
                    select.value = '';
                    select.required = false;
                }
            }
            
            // Atualizar cores e tipos de corte quando tecido/tipo_tecido mudar
            renderCores();
            renderTiposCorte();
        }

        function renderCores() {
            const select = document.getElementById('cor');
            let items = optionsWithParents.cor || options.cor || [];
            
            // Filtrar cores baseado em personalização e tecido selecionados
            if (selectedPersonalizacoes.length > 0 || document.getElementById('tecido').value) {
                const tecidoId = document.getElementById('tecido').value;
                items = items.filter(cor => {
                    // Se não tem parent_ids, mostrar sempre
                    if (!cor.parent_ids || cor.parent_ids.length === 0) {
                        return true;
                    }
                    
                    // Verificar se algum parent_id corresponde às personalizações selecionadas
                    const matchesPersonalizacao = selectedPersonalizacoes.length > 0 && 
                        cor.parent_ids.some(parentId => selectedPersonalizacoes.includes(parentId));
                    
                    // Verificar se algum parent_id corresponde ao tecido selecionado
                    const matchesTecido = tecidoId && cor.parent_ids.includes(parseInt(tecidoId));
                    
                    return matchesPersonalizacao || matchesTecido;
                });
            }
            
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecione a cor</option>' + 
                items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
            
            // Restaurar valor selecionado se ainda existir
            if (currentValue && items.find(item => item.id == currentValue)) {
                select.value = currentValue;
            } else {
                select.value = '';
            }
        }

        function renderTiposCorte() {
            const select = document.getElementById('tipo_corte');
            let items = optionsWithParents.tipo_corte || options.tipo_corte || [];
            
            // Filtrar tipo_corte baseado em personalização, tecido e tipo_tecido selecionados
            const tecidoId = document.getElementById('tecido').value;
            const tipoTecidoSelect = document.getElementById('tipo_tecido');
            const tipoTecidoId = tipoTecidoSelect ? tipoTecidoSelect.value : null;
            
            // Se não tem nenhuma seleção, mostrar TODOS os tipos de corte
            if (selectedPersonalizacoes.length === 0 && !tecidoId && !tipoTecidoId) {
                // Mostrar todos
            } else {
                // Filtrar baseado nas seleções
                items = items.filter(corte => {
                    // Se não tem parent_ids, mostrar sempre
                    if (!corte.parent_ids || corte.parent_ids.length === 0) {
                        return true;
                    }
                    
                    // Verificar se algum parent_id corresponde às personalizações selecionadas
                    const matchesPersonalizacao = selectedPersonalizacoes.length > 0 && 
                        corte.parent_ids.some(parentId => selectedPersonalizacoes.includes(parentId));
                    
                    // Verificar se algum parent_id corresponde ao tecido selecionado
                    const matchesTecido = tecidoId && corte.parent_ids.includes(parseInt(tecidoId));
                    
                    // Verificar se algum parent_id corresponde ao tipo_tecido selecionado
                    const matchesTipoTecido = tipoTecidoId && corte.parent_ids.includes(parseInt(tipoTecidoId));
                    
                    // Mostrar se corresponder a qualquer um dos critérios
                    return matchesPersonalizacao || matchesTecido || matchesTipoTecido;
                });
                
                // Se não tem itens após filtro, mostrar os sem parent_ids como fallback
                if (items.length === 0) {
                    const allItems = optionsWithParents.tipo_corte || options.tipo_corte || [];
                    items = allItems.filter(corte => !corte.parent_ids || corte.parent_ids.length === 0);
                }
            }
            
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecione o corte</option>' + 
                items.map(item => `<option value="${item.id}" data-price="${item.price}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
            
            // Restaurar valor selecionado se ainda existir
            if (currentValue && items.find(item => item.id == currentValue)) {
                select.value = currentValue;
            } else {
                select.value = '';
                // Se o tipo_corte mudou, atualizar detalhes e golas
                loadDetalhes();
                loadGolas();
            }
            
            updatePrice();
        }

        function renderDetalhes() {
            const select = document.getElementById('detalhe');
            let items = optionsWithParents.detalhe || options.detalhe || [];
            
            // Filtrar detalhes baseado em tipo_corte selecionado
            const tipoCorteId = document.getElementById('tipo_corte').value;
            if (tipoCorteId) {
                items = items.filter(detalhe => {
                    // Se não tem parent_ids, mostrar sempre
                    if (!detalhe.parent_ids || detalhe.parent_ids.length === 0) {
                        return true;
                    }
                    
                    // Verificar se algum parent_id corresponde ao tipo_corte selecionado
                    return detalhe.parent_ids.includes(parseInt(tipoCorteId));
                });
            } else {
                // Se não tem tipo_corte selecionado, mostrar apenas detalhes sem parent_ids
                items = items.filter(detalhe => !detalhe.parent_ids || detalhe.parent_ids.length === 0);
            }
            
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecione o detalhe</option>' + 
                items.map(item => `<option value="${item.id}" data-price="${item.price}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
            
            // Restaurar valor selecionado se ainda existir
            if (currentValue && items.find(item => item.id == currentValue)) {
                select.value = currentValue;
            } else {
                select.value = '';
            }
            
            updatePrice();
        }

        function loadDetalhes() {
            renderDetalhes();
        }

        function renderGolas() {
            const select = document.getElementById('gola');
            let items = optionsWithParents.gola || options.gola || [];
            
            // Filtrar golas baseado em tipo_corte selecionado
            const tipoCorteId = document.getElementById('tipo_corte').value;
            if (tipoCorteId) {
                items = items.filter(gola => {
                    // Se não tem parent_ids, mostrar sempre
                    if (!gola.parent_ids || gola.parent_ids.length === 0) {
                        return true;
                    }
                    
                    // Verificar se algum parent_id corresponde ao tipo_corte selecionado
                    return gola.parent_ids.includes(parseInt(tipoCorteId));
                });
            } else {
                // Se não tem tipo_corte selecionado, mostrar apenas golas sem parent_ids
                items = items.filter(gola => !gola.parent_ids || gola.parent_ids.length === 0);
            }
            
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecione a gola</option>' + 
                items.map(item => `<option value="${item.id}" data-price="${item.price}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
            
            // Restaurar valor selecionado se ainda existir
            if (currentValue && items.find(item => item.id == currentValue)) {
                select.value = currentValue;
            } else {
                select.value = '';
            }
            
            updatePrice();
        }

        function loadGolas() {
            renderGolas();
        }

        function onTipoTecidoChange() {
            renderCores();
            renderTiposCorte();
        }

        function onTipoCorteChange() {
            loadDetalhes();
            loadGolas();
            updatePrice();
        }

        function updatePrice() {
            // Verificar se está no modo de preço manual
            const precoManualToggle = document.getElementById('preco_manual_toggle');
            if (precoManualToggle && precoManualToggle.checked) {
                // No modo manual, não atualizar o unit_price automaticamente
                return;
            }

            const corteSelect = document.getElementById('tipo_corte');
            const detalheSelect = document.getElementById('detalhe');
            const golaSelect = document.getElementById('gola');

            // Verificar se SUB. TOTAL está selecionado
            const isSubTotal = selectedPersonalizacoes.some(id => {
                const item = (options.personalizacao || []).find(p => p.id === id);
                return item && item.name && 
                       (item.name.toUpperCase().includes('SUB') && item.name.toUpperCase().includes('TOTAL'));
            });

            // Se SUB. TOTAL estiver selecionado, zerar todos os valores
            let cortePrice = 0;
            let detalhePrice = 0;
            let golaPrice = 0;

            if (!isSubTotal) {
                cortePrice = parseFloat(corteSelect.options[corteSelect.selectedIndex]?.dataset.price || 0);
                detalhePrice = parseFloat(detalheSelect.options[detalheSelect.selectedIndex]?.dataset.price || 0);
                golaPrice = parseFloat(golaSelect.options[golaSelect.selectedIndex]?.dataset.price || 0);
            }

            const total = cortePrice + detalhePrice + golaPrice;

            document.getElementById('price-corte').textContent = 'R$ ' + cortePrice.toFixed(2).replace('.', ',');
            document.getElementById('price-detalhe').textContent = 'R$ ' + detalhePrice.toFixed(2).replace('.', ',');
            document.getElementById('price-gola').textContent = 'R$ ' + golaPrice.toFixed(2).replace('.', ',');
            document.getElementById('price-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            
            document.getElementById('unit_price').value = total.toFixed(2);
            document.getElementById('preco_inclui_personalizacao').value = '0';
        }

        // Função para alternar entre preço calculado e preço manual
        function togglePrecoManual() {
            const toggle = document.getElementById('preco_manual_toggle');
            const toggleBg = document.getElementById('toggle-bg');
            const toggleDot = document.getElementById('toggle-dot');
            const calculadoContainer = document.getElementById('preco-calculado-container');
            const manualContainer = document.getElementById('preco-manual-container');
            const precoIncluiPersonalizacao = document.getElementById('preco_inclui_personalizacao');

            if (toggle.checked) {
                // Ativar modo manual
                toggleBg.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                toggleBg.classList.add('bg-green-500', 'dark:bg-green-600');
                toggleDot.style.transform = 'translateX(20px)';
                
                calculadoContainer.classList.add('hidden');
                manualContainer.classList.remove('hidden');
                precoIncluiPersonalizacao.value = '1';
                
                // Limpar e focar no campo de preço manual
                const precoManualInput = document.getElementById('preco_manual_valor');
                precoManualInput.value = '';
                precoManualInput.focus();
                
                // Atualizar o unit_price para 0 até que o usuário digite
                document.getElementById('unit_price').value = '0';
            } else {
                // Desativar modo manual (voltar ao calculado)
                toggleBg.classList.remove('bg-green-500', 'dark:bg-green-600');
                toggleBg.classList.add('bg-gray-300', 'dark:bg-gray-600');
                toggleDot.style.transform = 'translateX(0)';
                
                calculadoContainer.classList.remove('hidden');
                manualContainer.classList.add('hidden');
                precoIncluiPersonalizacao.value = '0';
                
                // Recalcular preço baseado nos componentes
                updatePrice();
            }
        }

        // Função para atualizar o preço manual
        function updateManualPrice() {
            const precoManualInput = document.getElementById('preco_manual_valor');
            const valor = parseFloat(precoManualInput.value) || 0;
            
            // Atualizar exibição
            document.getElementById('price-total-manual').textContent = 'R$ ' + valor.toFixed(2).replace('.', ',');
            
            // Atualizar campo hidden
            document.getElementById('unit_price').value = valor.toFixed(2);
        }

        document.getElementById('item-form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.personalizacao-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos uma personalização.');
                return false;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            if (quantity === 0 || !quantity) {
                e.preventDefault();
                alert('Por favor, informe a quantidade de peças.');
                return false;
            }
            
            return true;
        });

        function editItem(index) {
            const item = items[index];
            
            document.getElementById('form-title').textContent = 'Editar Item';
            document.getElementById('submit-btn-text').textContent = 'Salvar Alterações';
            document.getElementById('form-action').value = 'update_item';
            document.getElementById('editing-item-id').value = index;
            document.getElementById('cancel-edit-btn').classList.remove('hidden');
            
            // Preencher personalização
            if (item.personalizacao) {
                const personalizacoes = Array.isArray(item.personalizacao) ? item.personalizacao : [item.personalizacao];
                selectedPersonalizacoes = personalizacoes.map(p => parseInt(p));
                renderPersonalizacao();
            }
            
            // Preencher tecido
            if (item.tecido) {
                document.getElementById('tecido').value = item.tecido;
                loadTiposTecido();
                
                setTimeout(() => {
                    if (item.tipo_tecido) {
                        document.getElementById('tipo_tecido').value = item.tipo_tecido;
                    }
                }, 100);
            }
            
            // Preencher cor
            if (item.cor) {
                document.getElementById('cor').value = item.cor;
            }
            
            // Preencher tipo de corte
            if (item.tipo_corte) {
                document.getElementById('tipo_corte').value = item.tipo_corte;
            }
            
            // Preencher detalhe
            if (item.detalhe) {
                document.getElementById('detalhe').value = item.detalhe;
            }
            
            // Preencher gola
            if (item.gola) {
                document.getElementById('gola').value = item.gola;
            }
            
            // Preencher quantidade
            document.getElementById('quantity').value = item.quantity || 0;
            
            // Preencher observações
            if (item.notes) {
                document.getElementById('notes').value = item.notes;
            }
            
            // Verificar se o item usa preço manual (com personalização incluída)
            const precoManualToggle = document.getElementById('preco_manual_toggle');
            if (item.preco_inclui_personalizacao == '1' || item.preco_inclui_personalizacao === true) {
                // Ativar modo manual
                precoManualToggle.checked = true;
                togglePrecoManual();
                
                // Preencher o valor manual
                document.getElementById('preco_manual_valor').value = item.unit_price || 0;
                updateManualPrice();
            } else {
                // Modo calculado - desativar toggle se estiver ativo
                if (precoManualToggle.checked) {
                    precoManualToggle.checked = false;
                    togglePrecoManual();
                }
                
                // Preencher preço unitário
                if (item.unit_price) {
                    document.getElementById('unit_price').value = item.unit_price;
                }
                
                // Atualizar cálculos
                updatePrice();
            }
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function cancelEdit() {
            document.getElementById('form-title').textContent = 'Adicionar Novo Item';
            document.getElementById('submit-btn-text').textContent = 'Adicionar Item';
            document.getElementById('form-action').value = 'add_item';
            document.getElementById('editing-item-id').value = '';
            document.getElementById('cancel-edit-btn').classList.add('hidden');
            
            document.getElementById('item-form').reset();
            selectedPersonalizacoes = [];
            renderPersonalizacao();
            
            // Resetar toggle de preço manual
            const precoManualToggle = document.getElementById('preco_manual_toggle');
            if (precoManualToggle.checked) {
                precoManualToggle.checked = false;
                togglePrecoManual();
            }
            
            // Resetar preços
            updatePrice();
        }

        function removeItem(index) {
            if (confirm('Deseja realmente remover este item?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("budget.items") }}';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('[name="_token"]').value;
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'remove_item';
                
                const indexInput = document.createElement('input');
                indexInput.type = 'hidden';
                indexInput.name = 'item_index';
                indexInput.value = index;
                
                form.appendChild(csrfInput);
                form.appendChild(actionInput);
                form.appendChild(indexInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
</script>
@endpush
@endsection


