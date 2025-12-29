@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">2</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Costura e Personalização</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 2 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">40%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-2.5 shadow-inner">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out shadow-lg shadow-indigo-500/30 dark:shadow-indigo-600/30" style="width: 40%"></div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulário de Adicionar Item -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800 overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-900/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white" id="form-title">Adicionar Novo Item</h1>
                                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Configure os detalhes do item de costura</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <form method="POST" action="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" data-action-url="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" id="sewing-form" class="space-y-5" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="add_item" id="form-action">
                            <input type="hidden" name="editing_item_id" value="" id="editing-item-id">

                            <!-- Personalização -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Personalização *</label>
                                <div class="grid grid-cols-2 gap-2" id="personalizacao-options">
                                    <!-- Será preenchido via JavaScript -->
                                </div>
                            </div>

                            <!-- Tecido e Tipo -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tecido</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <select name="tecido" id="tecido" onchange="loadTiposTecido()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                            <option value="">Selecione o tecido</option>
                                        </select>
                                    </div>
                                    <div id="tipo-tecido-container" style="display:none">
                                        <select name="tipo_tecido" id="tipo_tecido" onchange="onTipoTecidoChange()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                            <option value="">Selecione o tipo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Cor -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Cor do Tecido *</label>
                                <select name="cor" id="cor" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                    <option value="">Selecione a cor</option>
                                </select>
                            </div>

                            <!-- Modelo e Detalhes -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700 space-y-3">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white">Modelo e Detalhes</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <select name="tipo_corte" id="tipo_corte" onchange="onTipoCorteChange()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                        <option value="">Tipo de Corte *</option>
                                    </select>
                                    <select name="detalhe" id="detalhe" onchange="updatePrice()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                        <option value="">Detalhe</option>
                                    </select>
                                </div>
                                <select name="gola" id="gola" onchange="updatePrice()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                    <option value="">Gola *</option>
                                </select>
                            </div>

                            <!-- Tamanhos -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tamanhos e Quantidades</label>
                                <div class="grid grid-cols-5 gap-2 mb-2">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">PP</label>
                                        <input type="number" name="tamanhos[PP]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">P</label>
                                        <input type="number" name="tamanhos[P]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">M</label>
                                        <input type="number" name="tamanhos[M]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">G</label>
                                        <input type="number" name="tamanhos[G]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">GG</label>
                                        <input type="number" name="tamanhos[GG]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-5 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">EXG</label>
                                        <input type="number" name="tamanhos[EXG]" min="0" value="0" onchange="calculateTotal()" class="size-input-restricted w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">G1</label>
                                        <input type="number" name="tamanhos[G1]" min="0" value="0" onchange="calculateTotal()" class="size-input-restricted w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">G2</label>
                                        <input type="number" name="tamanhos[G2]" min="0" value="0" onchange="calculateTotal()" class="size-input-restricted w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">G3</label>
                                        <input type="number" name="tamanhos[G3]" min="0" value="0" onchange="calculateTotal()" class="size-input-restricted w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Especial</label>
                                        <input type="number" name="tamanhos[Especial]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                                    </div>
                                </div>
                                
                                <!-- Checkbox para acréscimo independente (apenas para Infantil/Baby look) -->
                                <div id="surcharge-checkbox-container" class="hidden mb-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="apply_surcharge" id="apply_surcharge" value="1" class="w-4 h-4 text-indigo-600 dark:text-indigo-500 border-gray-300 dark:border-slate-600 rounded focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-slate-700">
                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Aplicar acréscimo de tamanho especial (independente do tamanho)</span>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 ml-6">Marque esta opção se desejar cobrar o acréscimo mesmo sendo modelo Infantil ou Baby look.</p>
                                </div>
                                <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Total de peças:</span>
                                        <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400" id="total-pecas">0</span>
                                    </div>
                                </div>
                                <input type="hidden" name="quantity" id="quantity" value="0">
                                
                                <!-- Informações de Estoque por Tamanho -->
                                <div id="stock-info-section" class="hidden mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <h6 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Estoque Disponível por Tamanho:</h6>
                                    <div id="stock-by-size" class="space-y-2">
                                        <!-- Será preenchido via JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Resumo de Preços -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Resumo de Preços</label>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between p-2 bg-white dark:bg-slate-800 rounded">
                                        <span class="text-gray-600 dark:text-slate-400">Tipo de Corte:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" id="price-corte">R$ 0,00</span>
                                    </div>
                                    <div class="flex justify-between p-2 bg-white dark:bg-slate-800 rounded">
                                        <span class="text-gray-600 dark:text-slate-400">Detalhe:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" id="price-detalhe">R$ 0,00</span>
                                    </div>
                                    <div class="flex justify-between p-2 bg-white dark:bg-slate-800 rounded">
                                        <span class="text-gray-600 dark:text-slate-400">Gola:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" id="price-gola">R$ 0,00</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-indigo-600 dark:bg-indigo-500 rounded-lg mt-2">
                                        <span class="text-white font-bold">Valor Unitário:</span>
                                        <span class="font-bold text-white text-xl" id="price-total">R$ 0,00</span>
                                    </div>
                                    
                                    <!-- Custo Unitário -->
                                    <div class="flex flex-col mt-4 pt-4 border-t border-gray-200 dark:border-slate-700">
                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Custo Unitário (R$)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">R$</span>
                                            </div>
                                            <input type="number" name="unit_cost" id="unit_cost" step="0.01" min="0" value="0.00" class="w-full pl-8 px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm font-medium">
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Informe o custo de produção por unidade para cálculo de lucro.</p>
                                    </div>
                                </div>
                                <input type="hidden" name="unit_price" id="unit_price" value="0">
                            </div>

                            <!-- Imagem de Capa do Item -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Imagem de Capa do Item</label>
                                <div class="space-y-3">
                                    <!-- Preview Container -->
                                    <div id="cover-image-preview-container" class="hidden">
                                        <p class="text-xs text-gray-500 mb-2">Imagem Atual:</p>
                                        <div class="relative inline-block group">
                                            <img id="cover-image-preview" src="" alt="Capa do item" class="h-24 w-24 object-cover rounded-lg border border-gray-200 dark:border-slate-700">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Upload Input -->
                                    <div class="flex items-center justify-center w-full">
                                        <label for="item_cover_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-all">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Clique para enviar</span> ou arraste</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG ou WEBP (Max. 10MB)</p>
                                            </div>
                                            <input id="item_cover_image" name="item_cover_image" type="file" class="hidden" accept="image/*" onchange="previewCoverImage(this)" />
                                        </label>
                                    </div>
                                    <div id="file-name-display" class="hidden text-sm text-gray-600 dark:text-slate-400 text-center"></div>
                                </div>
                            </div>

                            <!-- Observações -->
                            <div class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Observações <span class="text-gray-500 dark:text-slate-400 font-normal text-xs">(opcional)</span></label>
                                <textarea name="art_notes" rows="3" placeholder="Informações importantes para a produção..." class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">{{ old('art_notes', isset($editData) ? ($editData['art_notes'] ?? '') : '') }}</textarea>
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-slate-700">
                                <a href="{{ isset($editData) ? route('orders.edit.client') : route('orders.wizard.client') }}" class="px-4 py-2 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium">
                                    ← Voltar
                                </a>
                                <button type="submit" id="submit-button" class="px-6 py-2.5 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-semibold rounded-lg transition-all text-sm">
                                    Adicionar Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Resumo dos Itens -->
            <div class="lg:col-span-1" id="items-sidebar-container">
                @include('orders.wizard.partials.items_sidebar')
            </div>
        </div>
    </div>
    <!-- Modal de Confirmação de Exclusão -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-slate-700 transform transition-all scale-100 opacity-100">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Remover Item?</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Esta ação não pode ser desfeita. O item será removido permanentemente do pedido.</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-lg shadow-red-500/30 transition-all transform hover:scale-105">
                        Sim, Remover
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        let itemToDeleteId = null;

        function openDeleteModal(itemId) {
            itemToDeleteId = itemId;
            document.getElementById('delete-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scrolling
            itemToDeleteId = null;
        }

        // Variável global para dados dos itens (agora 'let' para permitir atualização)
        let itemsData = {!! json_encode($order->items->toArray()) !!};

        async function confirmDelete() {
            if (!itemToDeleteId) return;

            const btn = document.querySelector('#delete-modal button.bg-red-600');
            const originalText = btn.innerText;
            btn.innerHTML = 'Removendo...';
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('action', 'delete_item');
                formData.append('item_id', itemToDeleteId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch("{{ route('orders.wizard.sewing') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Atualizar HTML da sidebar
                    document.getElementById('items-sidebar-container').innerHTML = data.html;
                    
                    // Atualizar dados dos itens
                    if (data.items_data) {
                        itemsData = data.items_data;
                    }
                    
                    // Fechar modal
                    closeDeleteModal();
                    
                    // Mostrar toast/notificação de sucesso (opcional)
                    // alert(data.message); 
                } else {
                    alert('Erro ao remover item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro na exclusão:', error);
                alert('Erro ao processar a exclusão.');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        document.getElementById('sewing-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            if (form.dataset.submitting === 'true') return;

            // Validações básicas (client-side)
            const checkboxes = document.querySelectorAll('.personalizacao-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            if (checkedCount === 0) {
                alert('Por favor, selecione pelo menos uma personalização.');
                return;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            if (quantity === 0) {
                alert('Por favor, adicione pelo menos uma peça nos tamanhos.');
                return;
            }

            // UI de processamento
            const submitBtn = document.getElementById('submit-button');
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processando...
            `;
            form.dataset.submitting = 'true';

            try {
                const formData = new FormData(form);
                
                // Garantir que headers de AJAX sejam enviados
                const actionUrl = form.dataset.actionUrl || form.action;
                console.log('Submitting sewing form to:', actionUrl);
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Erro HTTP na submissão:', response.status, text);
                    alert('Erro ao salvar item: ' + (response.statusText || 'erro HTTP'));
                    return;
                }

                const rawText = await response.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (parseError) {
                    console.error('Erro ao interpretar resposta JSON:', parseError, rawText);
                    alert('Erro ao salvar item: resposta inválida do servidor.');
                    return;
                }

                if (data.success) {
                    // Atualizar HTML da sidebar
                    if (data.html) {
                        document.getElementById('items-sidebar-container').innerHTML = data.html;
                    }

                    // Atualizar dados dos itens
                    if (data.items_data) {
                        itemsData = data.items_data;
                    }

                    // Limpar formulário se foi uma adição (não edição)
                    const action = document.getElementById('form-action').value;
                    if (action === 'add_item') {
                        cancelEdit(); // Helper que reseta o form
                    } else {
                        cancelEdit(); // Sai do modo de edição
                        // Mostrar mensagem específica de atualização
                    }

                    // Scroll para o topo ou feedback visual
                    // Opcional: mostrar toast
                    window.location.reload(); // garante que o item apareÇa mesmo se a atualizaÇõÇœo via JS falhar
                    
                } else {
                     // Lidar com erros de validação (se o servidor retornar JSON de erro 422)
                     if (data.errors) {
                         let msg = 'Erros de validação:\n';
                         for (let field in data.errors) {
                             msg += `- ${data.errors[field].join(', ')}\n`;
                         }
                         alert(msg);
                     } else {
                         alert(data.message || 'Erro ao salvar item.');
                     }
                }

            } catch (error) {
                console.error('Erro no envio:', error);
                alert('Ocorreu um erro ao processar sua solicitação.');
            } finally {
                // Restaurar botão
                submitBtn.disabled = false;
                submitBtn.innerHTML = document.getElementById('form-action').value === 'update_item' ? 'Salvar Alterações' : 'Adicionar Item';
                form.dataset.submitting = 'false';
            }
        });

        let optionsWithParents = {};
        let selectedPersonalizacoes = [];

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
                <label class="flex items-center px-3 py-2.5 border rounded-lg cursor-pointer transition-all ${
                    selectedPersonalizacoes.includes(item.id) 
                        ? 'border-indigo-500 dark:border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' 
                        : 'border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:border-indigo-300 dark:hover:border-indigo-600'
                }">
                    <input type="checkbox" name="personalizacao[]" value="${item.id}" 
                           onchange="togglePersonalizacao(${item.id})"
                           class="personalizacao-checkbox w-4 h-4 text-indigo-600 dark:text-indigo-500 border-gray-300 dark:border-slate-600 rounded focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-slate-700" ${selectedPersonalizacoes.includes(item.id) ? 'checked' : ''}>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">${item.name}</span>
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
                        
                        // Zerar valores de modelo, detalhe e gola
                        const modeloSelect = document.getElementById('modelo');
                        const detalheSelect = document.getElementById('detalhe');
                        const golaSelect = document.getElementById('gola');
                        
                        if (modeloSelect) modeloSelect.value = '';
                        if (detalheSelect) detalheSelect.value = '';
                        if (golaSelect) golaSelect.value = '';
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
                items.map(item => `<option value="${item.id}" data-cost="${item.cost || 0}">${item.name}</option>`).join('');
            
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
                    items.map(item => `<option value="${item.id}" data-cost="${item.cost || 0}">${item.name}</option>`).join('');
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

        function onTipoTecidoChange() {
            // Quando o tipo de tecido muda, atualizar cores e tipos de corte
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
                    
                    const tipoTecidoSelect = document.getElementById('tipo_tecido');
                    const tipoTecidoId = tipoTecidoSelect ? tipoTecidoSelect.value : null;
                    const matchesTipoTecido = tipoTecidoId && cor.parent_ids.includes(parseInt(tipoTecidoId));
                    
                    return matchesPersonalizacao || matchesTecido || matchesTipoTecido;
                });
            }
            
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecione a cor</option>' + 
                items.map(item => `<option value="${item.id}" data-cost="${item.cost || 0}">${item.name}</option>`).join('');
            
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
                items.map(item => `<option value="${item.id}" data-price="${item.price}" data-cost="${item.cost || 0}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
            
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
                items.map(item => `<option value="${item.id}" data-price="${item.price}" data-cost="${item.cost || 0}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
            
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
            checkRestrictedSizes();
        }

        function renderGolas() {
            const select = document.getElementById('gola');
            let items = optionsWithParents.gola || options.gola || [];
            
            console.log('Rendering Golas. Initial Items:', items);
            
            // Filtrar golas baseado em tipo_corte selecionado
            const tipoCorteId = document.getElementById('tipo_corte').value;
            console.log('Selected Cut ID:', tipoCorteId);

            if (tipoCorteId) {
                items = items.filter(gola => {
                    // Se não tem parent_ids, mostrar sempre
                    if (!gola.parent_ids || gola.parent_ids.length === 0) {
                        return true;
                    }
                    
                    // Verificar se algum parent_id corresponde ao tipo_corte selecionado
                    const match = gola.parent_ids.some(pid => pid == tipoCorteId);
                    console.log(`Checking Gola ${gola.name} (Parents: ${JSON.stringify(gola.parent_ids)}) against Cut ${tipoCorteId}: ${match}`);
                    return match;
                });
            } else {
                // Se não tem tipo_corte selecionado, mostrar apenas golas sem parent_ids
                items = items.filter(gola => !gola.parent_ids || gola.parent_ids.length === 0);
            }
            
            
            console.log('Filtered Golas:', items);
            
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecione a gola</option>' + 
                items.map(item => `<option value="${item.id}" data-price="${item.price}" data-cost="${item.cost || 0}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
            
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
            loadStockByCutType();
            checkRestrictedSizes();
        }
        
        function checkRestrictedSizes() {
            const tipoCorteSelect = document.getElementById('tipo_corte');
            const detalheSelect = document.getElementById('detalhe');
            
            const tipoCorteText = tipoCorteSelect.options[tipoCorteSelect.selectedIndex]?.text || '';
            const detalheText = detalheSelect.options[detalheSelect.selectedIndex]?.text || '';
            
            const isRestricted = (tipoCorteText.toUpperCase().includes('INFANTIL') || tipoCorteText.toUpperCase().includes('BABY LOOK')) || 
                                 (detalheText.toUpperCase().includes('INFANTIL') || detalheText.toUpperCase().includes('BABY LOOK'));
            
            const restrictedInputs = document.querySelectorAll('.size-input-restricted');
            const surchargeCheckboxContainer = document.getElementById('surcharge-checkbox-container');
            const surchargeCheckbox = document.getElementById('apply_surcharge');
            
            if (isRestricted) {
                // Mostrar checkbox de acréscimo
                if (surchargeCheckboxContainer) {
                    surchargeCheckboxContainer.classList.remove('hidden');
                }
            } else {
                // Ocultar checkbox de acréscimo e desmarcar
                if (surchargeCheckboxContainer) {
                    surchargeCheckboxContainer.classList.add('hidden');
                }
                if (surchargeCheckbox) {
                    surchargeCheckbox.checked = false;
                }
            }
        }
        
        // Buscar estoque por tipo de corte
        async function loadStockByCutType() {
            const cutTypeId = document.getElementById('tipo_corte')?.value;
            
            if (!cutTypeId) {
                const stockSection = document.getElementById('stock-info-section');
                if (stockSection) stockSection.classList.add('hidden');
                return;
            }
            
            try {
                const params = new URLSearchParams({
                    cut_type_id: cutTypeId
                });
                
                const response = await fetch(`/api/stocks/by-cut-type?${params}`);
                const data = await response.json();
                
                const stockSection = document.getElementById('stock-info-section');
                const stockBySize = document.getElementById('stock-by-size');
                
                if (data.success && data.stock_by_size && data.stock_by_size.length > 0) {
                    let html = '';
                    data.stock_by_size.forEach(item => {
                        const hasStock = item.available > 0;
                        const bgColor = hasStock ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600';
                        
                        // Mostrar detalhes por loja
                        let storeDetails = '';
                        if (item.stores && item.stores.length > 0) {
                            storeDetails = item.stores.map(store => {
                                const storeHasStock = store.available > 0;
                                const storeColor = storeHasStock ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500';
                                const storeBg = storeHasStock ? 'bg-green-100 dark:bg-green-900/40' : 'bg-gray-100 dark:bg-gray-600';
                                return `<span class="ml-2 px-2 py-0.5 text-xs rounded ${storeBg} ${storeColor}" title="${store.store_name}">
                                    ${store.store_name.replace('Loja ', '')}: ${store.available}${store.reserved > 0 ? ' (R:' + store.reserved + ')' : ''}
                                </span>`;
                            }).join('');
                        }
                        
                        html += `
                            <div class="p-2 ${bgColor} rounded border">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${item.size}:</span>
                                    ${hasStock ? `
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                            ✓ ${item.available} total
                                        </span>
                                    ` : `
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                            ✗ Sem estoque
                                        </span>
                                    `}
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    ${storeDetails}
                                </div>
                            </div>
                        `;
                    });
                    
                    if (stockSection && stockBySize) {
                        stockSection.classList.remove('hidden');
                        stockBySize.innerHTML = html;
                    }
                } else {
                    if (stockSection) {
                        stockSection.classList.remove('hidden');
                        if (stockBySize) {
                            stockBySize.innerHTML = '<p class="text-sm text-yellow-600 dark:text-yellow-400 text-center py-2">⚠ Nenhum estoque cadastrado para este produto</p>';
                        }
                    }
                }
            } catch (error) {
                console.error('Erro ao buscar estoque:', error);
                const stockSection = document.getElementById('stock-info-section');
                if (stockSection) stockSection.classList.add('hidden');
            }
        }
        
        // Adicionar listeners para mudanças nos tamanhos
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
                input.addEventListener('change', function() {
                    calculateTotal();
                });
            });
            
            // Listeners para recálculo de custo
            const corSelect = document.getElementById('cor');
            if(corSelect) corSelect.addEventListener('change', calculateCost);
            
            const tecidoSelect = document.getElementById('tecido');
            if(tecidoSelect) tecidoSelect.addEventListener('change', calculateCost);
            
            const tipoTecidoSelect = document.getElementById('tipo_tecido');
            if(tipoTecidoSelect) tipoTecidoSelect.addEventListener('change', calculateCost);
        });

        function calculateCost() {
            let totalCost = 0;
            
            // Helper function to safe parse float
            const parseVal = (val) => {
                const f = parseFloat(val);
                return isNaN(f) ? 0 : f;
            }

            // 1. Personalizações
            if (typeof options !== 'undefined' && options.personalizacao && typeof selectedPersonalizacoes !== 'undefined') {
                selectedPersonalizacoes.forEach(id => {
                    const opt = options.personalizacao.find(p => p.id == id);
                    if (opt) totalCost += parseVal(opt.cost);
                });
            }

            // 2. Selects
            ['tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola'].forEach(id => {
                const el = document.getElementById(id);
                if (el && el.selectedIndex >= 0) {
                    const opt = el.options[el.selectedIndex];
                    if (opt && opt.dataset.cost) {
                        totalCost += parseVal(opt.dataset.cost);
                    }
                }
            });

            console.log('Calculating cost:', totalCost);
            const costInput = document.getElementById('unit_cost');
            if (costInput) {
                costInput.value = totalCost.toFixed(2);
            }
        }

        function updatePrice() {
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
            
            calculateCost(); // Atualizar custo também
        }

        function calculateTotal() {
            const inputs = document.querySelectorAll('input[name^="tamanhos"]');
            let total = 0;
            
            inputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            
            document.getElementById('total-pecas').textContent = total;
            document.getElementById('quantity').value = total;
        }
        
        // Função antiga removida - agora usamos loadStockByCutType() que busca automaticamente
        // Mantida apenas para compatibilidade se houver outras referências
        async function checkStockForAllSizes() {
            const fabricId = document.getElementById('tecido')?.value;
            const colorId = document.getElementById('cor')?.value;
            const cutTypeId = document.getElementById('tipo_corte')?.value;
            const currentStoreId = {{ $currentStoreId ?? 'null' }};
            
            const stockInfoSection = document.getElementById('stock-info-section');
            const stockBySize = document.getElementById('stock-by-size');
            
            // Verificar se todos os campos necessários estão preenchidos
            if (!fabricId || !colorId || !cutTypeId || !currentStoreId) {
                if (stockInfoSection) stockInfoSection.classList.add('hidden');
                return;
            }
            
            const sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
            let stockHtml = '';
            let hasAnyStock = false;
            
            for (const size of sizes) {
                const sizeInput = document.querySelector(`input[name="tamanhos[${size}]"]`);
                const quantity = parseInt(sizeInput?.value || 0);
                
                if (quantity > 0) {
                    try {
                        const params = new URLSearchParams({
                            store_id: currentStoreId,
                            fabric_id: fabricId,
                            color_id: colorId,
                            cut_type_id: cutTypeId,
                            size: size,
                            quantity: quantity
                        });
                        
                        const response = await fetch(`/api/stocks/check?${params}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            const available = data.available_quantity || 0;
                            const stores = data.stores || [];
                            const storeInfo = stores.length
                                ? stores.map(s => `${s.store_name}: ${s.available}`).join(' | ')
                                : '';
                            const hasStock = data.has_stock || false;
                            hasAnyStock = true;
                            
                            if (hasStock) {
                                stockHtml += `
                                    <div class="flex items-center justify-between p-2 bg-green-50 dark:bg-green-900/20 rounded border border-green-200 dark:border-green-800">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">${size}:</span>
                                        <div class="flex flex-col text-right">
                                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">${available} disponível</span>
                                            ${storeInfo ? `<span class="text-[11px] text-gray-500 dark:text-gray-400">${storeInfo}</span>` : ''}
                                        </div>
                                    </div>
                                `;
                            } else {
                                stockHtml += `
                                    <div class="flex items-center justify-between p-2 bg-red-50 dark:bg-red-900/20 rounded border border-red-200 dark:border-red-800">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">${size}:</span>
                                        <div class="flex flex-col text-right">
                                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">${available} disponível (insuficiente)</span>
                                            ${storeInfo ? `<span class="text-[11px] text-gray-500 dark:text-gray-400">${storeInfo}</span>` : ''}
                                        </div>
                                        <button type="button" onclick="createStockRequestForSize('${size}', ${quantity})" class="text-xs text-blue-600 dark:text-blue-400 underline ml-2">
                                            Solicitar
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    } catch (error) {
                        console.error(`Erro ao verificar estoque para ${size}:`, error);
                    }
                }
            }
            
            if (hasAnyStock && stockInfoSection && stockBySize) {
                stockInfoSection.classList.remove('hidden');
                stockBySize.innerHTML = stockHtml || '<p class="text-sm text-gray-500">Nenhum tamanho selecionado</p>';
            } else {
                if (stockInfoSection) stockInfoSection.classList.add('hidden');
            }
        }
        
        // Criar solicitação de estoque para um tamanho específico
        async function createStockRequestForSize(size, quantity) {
            const fabricId = document.getElementById('tecido')?.value;
            const colorId = document.getElementById('cor')?.value;
            const cutTypeId = document.getElementById('tipo_corte')?.value;
            const currentStoreId = {{ $currentStoreId ?? 'null' }};
            
            if (!fabricId || !colorId || !cutTypeId || !currentStoreId) {
                alert('Preencha todos os campos de especificação');
                return;
            }
            
            const fabricName = document.getElementById('tecido')?.selectedOptions[0]?.text || 'Tecido';
            const colorName = document.getElementById('cor')?.selectedOptions[0]?.text || 'Cor';
            const cutTypeName = document.getElementById('tipo_corte')?.selectedOptions[0]?.text || 'Tipo de Corte';
            
            if (!confirm(`Deseja criar uma solicitação de estoque para:\n\nTecido: ${fabricName}\nCor: ${colorName}\nTipo de Corte: ${cutTypeName}\nTamanho: ${size}\nQuantidade: ${quantity} unidade(s)?`)) {
                return;
            }
            
            try {
                const response = await fetch('/stock-requests', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        requesting_store_id: currentStoreId,
                        fabric_id: fabricId,
                        color_id: colorId,
                        cut_type_id: cutTypeId,
                        size: size,
                        requested_quantity: quantity,
                        request_notes: `Solicitação criada automaticamente do wizard de pedidos - Quantidade necessária: ${quantity}`
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Solicitação de estoque criada com sucesso!');
                    // Atualizar informações de estoque
                    checkStockForAllSizes();
                } else {
                    alert('Erro ao criar solicitação: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao criar solicitação:', error);
                alert('Erro ao criar solicitação de estoque');
            }
        }

        /* Duplicate submit handler (legacy) disabled
        document.getElementById('sewing-form').addEventListener('submit', function(e) {
            return; // duplicate handler disabled
            const form = this;
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }

            const checkboxes = document.querySelectorAll('.personalizacao-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos uma personalização.');
                return false;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            if (quantity === 0) {
                e.preventDefault();
                alert('Por favor, adicione pelo menos uma peça nos tamanhos.');
                return false;
            }

            // Disable button and show processing state
            const submitBtn = document.getElementById('submit-button');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerText;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processando...
                `;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
            
            form.dataset.submitting = 'true';
            
            return true;
        });
        */

        // Função auxiliar para encontrar o ID de uma opção pelo nome
        function findOptionIdByName(optionsList, name) {
            if (!name || !optionsList || !optionsList.length) return null;
            const option = optionsList.find(opt => 
                opt.name === name || 
                opt.name.toLowerCase() === name.toLowerCase() ||
                opt.name.toLowerCase().includes(name.toLowerCase()) ||
                name.toLowerCase().includes(opt.name.toLowerCase())
            );
            return option ? option.id : null;
        }

        // Função auxiliar para selecionar uma opção em um select pelo nome
        function setSelectByName(selectId, value, optionType) {
            const select = document.getElementById(selectId);
            if (!select || !value) return false;
            
            // Primeiro, tentar encontrar na lista de opções carregadas
            const optionsList = optionsWithParents[optionType] || options[optionType] || [];
            const optionId = findOptionIdByName(optionsList, value);
            
            if (optionId) {
                select.value = optionId;
                console.log(`✅ ${selectId} selecionado por ID encontrado:`, optionId, 'para valor:', value);
                return true;
            }
            
            // Fallback: tentar definir diretamente (caso seja ID)
            if (!isNaN(value)) {
                select.value = value;
                if (select.value == value) {
                    console.log(`✅ ${selectId} selecionado diretamente por ID:`, value);
                    return true;
                }
            }
            
            // Fallback: procurar nas opções do select pelo texto
            for (let option of select.options) {
                if (option.textContent.trim() === value ||
                    option.textContent.trim().toLowerCase() === value.toLowerCase() ||
                    option.textContent.toLowerCase().includes(value.toLowerCase())) {
                    option.selected = true;
                    console.log(`✅ ${selectId} selecionado por texto:`, option.textContent.trim());
                    return true;
                }
            }
            
            console.warn(`⚠️ Não foi possível selecionar ${selectId} com valor:`, value);
            return false;
        }

        function editItem(itemId) {
            const itemData = itemsData.find(item => item.id == itemId);
            
            if (!itemData) {
                alert('Item não encontrado');
                return;
            }

            console.log('📝 Editando item:', itemData);

            document.getElementById('editing-item-id').value = itemId;
            document.getElementById('form-action').value = 'update_item';
            document.getElementById('form-title').textContent = 'Editar Item ' + itemData.item_number;
            document.getElementById('submit-button').innerHTML = 'Salvar Alterações';

            // Selecionar personalizações pelo nome
            const personalizacoes = itemData.print_type ? itemData.print_type.split(', ').map(p => p.trim()) : [];
            const persOptions = options.personalizacao || [];
            
            // Limpar seleção anterior
            selectedPersonalizacoes = [];
            
            personalizacoes.forEach(persName => {
                const persOption = persOptions.find(opt => 
                    opt.name === persName || 
                    opt.name.toLowerCase() === persName.toLowerCase()
                );
                if (persOption) {
                    selectedPersonalizacoes.push(persOption.id);
                    console.log(`✅ Personalização "${persName}" encontrada com ID:`, persOption.id);
                } else {
                    console.warn(`⚠️ Personalização "${persName}" não encontrada nas opções`);
                }
            });
            
            // Re-renderizar personalizações para atualizar checkboxes
            renderPersonalizacao();

            // Função auxiliar para forçar opção no select se não existir
            function forceSelectOption(selectId, optionName, optionType) {
                const select = document.getElementById(selectId);
                if (!select || !optionName) return false;
                
                const optionsList = optionsWithParents[optionType] || options[optionType] || [];
                const optionData = optionsList.find(opt => 
                    opt.name === optionName || 
                    opt.name.toLowerCase() === optionName.toLowerCase() ||
                    opt.name.toLowerCase().includes(optionName.toLowerCase()) ||
                    optionName.toLowerCase().includes(opt.name.toLowerCase())
                );
                
                if (!optionData) {
                    console.warn(`⚠️ Opção "${optionName}" não encontrada na lista ${optionType}`);
                    return false;
                }
                
                // Verificar se a opção já existe no select
                let optionExists = false;
                for (let opt of select.options) {
                    if (opt.value == optionData.id) {
                        opt.selected = true;
                        select.value = optionData.id;
                        optionExists = true;
                        console.log(`✅ ${selectId} selecionado (opção existente):`, optionData.name);
                        break;
                    }
                }
                
                // Se a opção não existe no select (devido a filtros), adicionar temporariamente
                if (!optionExists) {
                    const newOption = document.createElement('option');
                    newOption.value = optionData.id;
                    newOption.textContent = optionData.name + (optionData.price > 0 ? ` (+R$ ${parseFloat(optionData.price).toFixed(2).replace('.', ',')})` : '');
                    newOption.setAttribute('data-price', optionData.price || 0);
                    newOption.selected = true;
                    select.appendChild(newOption);
                    select.value = optionData.id;
                    console.log(`✅ ${selectId} adicionado e selecionado:`, optionData.name, '(opção foi adicionada pois não existia no select)');
                }
                
                return true;
            }

            // Extrair tecido e tipo de tecido do campo fabric (formato: "TECIDO - TIPO" ou apenas "TECIDO")
            let tecidoName = itemData.fabric;
            let tipoTecidoName = itemData.tipo_tecido || null;
            
            if (itemData.fabric && itemData.fabric.includes(' - ')) {
                const parts = itemData.fabric.split(' - ');
                tecidoName = parts[0].trim();
                tipoTecidoName = parts.slice(1).join(' - ').trim(); // Pega tudo após o primeiro " - "
                console.log('📝 Extracted from fabric:', { tecido: tecidoName, tipo_tecido: tipoTecidoName });
            }

            // Selecionar tecido pelo nome (usando nome extraído)
            setSelectByName('tecido', tecidoName, 'tecido');
            loadTiposTecido();

            // Usar timeouts maiores e garantir que as opções sejam forçadas
            setTimeout(() => {
                // Tentar selecionar tipo de tecido se existir
                if (tipoTecidoName) {
                    forceSelectOption('tipo_tecido', tipoTecidoName, 'tipo_tecido');
                }
                
                // Atualizar cores e tipos de corte após tecido ser selecionado
                renderCores();
                renderTiposCorte();
                
                // Selecionar cor e modelo com timeouts adicionais
                setTimeout(() => {
                    // Forçar seleção de cor
                    forceSelectOption('cor', itemData.color, 'cor');
                    
                    // Forçar seleção de tipo de corte (modelo)
                    if (itemData.model) {
                        forceSelectOption('tipo_corte', itemData.model, 'tipo_corte');
                        
                        // Atualizar detalhes e golas após tipo de corte
                        loadDetalhes();
                        loadGolas();
                        
                        setTimeout(() => {
                            // Forçar seleção de detalhe
                            if (itemData.detail) {
                                forceSelectOption('detalhe', itemData.detail, 'detalhe');
                            }
                            
                            // Forçar seleção de gola
                            if (itemData.collar) {
                                forceSelectOption('gola', itemData.collar, 'gola');
                            }
                            
                            updatePrice();
                            calculateTotal();
                        }, 300);
                    } else {
                        // Se não tem modelo, tentar selecionar gola diretamente
                        setTimeout(() => {
                            if (itemData.collar) {
                                forceSelectOption('gola', itemData.collar, 'gola');
                            }
                            updatePrice();
                            calculateTotal();
                        }, 300);
                    }
                }, 300);
            }, 400);

            const sizeInputs = document.querySelectorAll('input[name^="tamanhos"]');
            sizeInputs.forEach(input => input.value = 0);
            
            if (itemData.sizes && typeof itemData.sizes === 'object') {
                Object.entries(itemData.sizes).forEach(([size, qty]) => {
                    const input = document.querySelector(`input[name="tamanhos[${size}]"]`);
                    if (input) {
                        input.value = qty || 0;
                    }
                });
            }

            document.getElementById('unit_price').value = itemData.unit_price;
            document.getElementById('unit_cost').value = itemData.unit_cost || 0;

            // Restaurar estado do checkbox de acréscimo
            const surchargeCheckbox = document.getElementById('apply_surcharge');
            if (surchargeCheckbox && itemData.print_desc) {
                try {
                    const printDesc = JSON.parse(itemData.print_desc);
                    surchargeCheckbox.checked = !!printDesc.apply_surcharge;
                } catch (e) {
                    console.error('Erro ao parsing print_desc:', e);
                    surchargeCheckbox.checked = false;
                }
            } else if (surchargeCheckbox) {
                surchargeCheckbox.checked = false;
            }

            // Preview da Imagem de Capa
            const previewContainer = document.getElementById('cover-image-preview-container');
            const previewImage = document.getElementById('cover-image-preview');
            // Usar cover_image_url que vem do append ou construir caminho
            const imageUrl = itemData.cover_image_url || (itemData.cover_image ? '/storage/' + itemData.cover_image : null);
            
            if (imageUrl) {
                previewImage.src = imageUrl;
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
                previewImage.src = '';
            }
            
            // Limpar input de arquivo (não é possível setar valor, mas podemos limpar visualmente)
            document.getElementById('item_cover_image').value = '';
            document.getElementById('file-name-display').classList.add('hidden');

            updatePrice();
            calculateTotal();
            checkRestrictedSizes();

            document.getElementById('sewing-form').scrollIntoView({ behavior: 'smooth' });
        }
        
        function previewCoverImage(input) {
            const previewContainer = document.getElementById('cover-image-preview-container');
            const previewImage = document.getElementById('cover-image-preview');
            const fileNameDisplay = document.getElementById('file-name-display');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
                
                fileNameDisplay.textContent = 'Arquivo selecionado: ' + input.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            }
        }

        function cancelEdit() {
            document.getElementById('editing-item-id').value = '';
            document.getElementById('form-action').value = 'add_item';
            document.getElementById('form-title').textContent = 'Adicionar Novo Item';
            document.getElementById('submit-button').innerHTML = 'Adicionar Item';
            
            document.getElementById('sewing-form').reset();
            
            // Limpar preview de imagem
            document.getElementById('cover-image-preview-container').classList.add('hidden');
            document.getElementById('cover-image-preview').src = '';
            document.getElementById('file-name-display').classList.add('hidden');
            document.getElementById('file-name-display').textContent = '';
            
            document.querySelectorAll('.personalizacao-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            document.getElementById('unit_cost').value = '0.00';
        }

        @if(isset($editData))
        document.addEventListener('DOMContentLoaded', function() {
            const submitButton = document.getElementById('submit-button');
            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg';
            cancelButton.innerHTML = 'Cancelar Edição';
            cancelButton.onclick = cancelEdit;
            
            submitButton.parentNode.insertBefore(cancelButton, submitButton.nextSibling);
        });
        @endif

        async function togglePin(itemId) {
            try {
                const response = await fetch(`/order-items/${itemId}/toggle-pin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Recarregar a página para atualizar a ordem
                    window.location.reload();
                } else {
                    alert('Erro ao alterar status do item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao processar solicitação');
            }
        }
</script>
@endpush
@endsection
