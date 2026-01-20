@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Cabeçalho -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Converter Orçamento em Pedido</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Orçamento #{{ $budget->budget_number }}</p>
        </div>
        <a href="{{ route('budget.show', $budget->id) }}" class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar para detalhes
        </a>
    </div>

    <!-- Erros de Validação -->
    @if ($errors->any())
    <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Corrija os seguintes erros:</h3>
                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('budget.convert-to-order', $budget->id) }}" method="POST" enctype="multipart/form-data" id="convert-form">
        @csrf

        <!-- Informações do Orçamento -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informações do Orçamento</h2>
                </div>
            </div>
            <div class="p-6 bg-white dark:bg-gray-800 space-y-6">
                <!-- Cliente e Valor -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        @if($budget->client)
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Cliente Vinculado:</span>
                                    <span class="font-medium text-gray-900 dark:text-white block">{{ $budget->client->name }}</span>
                                    @if($budget->client->phone_primary)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->client->phone_primary }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('clients.edit', $budget->client->id) }}" 
                                target="_blank"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar Cliente
                                </a>
                            </div>
                            <input type="hidden" name="client_id" value="{{ $budget->client_id }}">
                        @else
                            <div x-data="{ clientType: 'existing' }" class="space-y-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Contato do Orçamento Rápido:</span>
                                        <span class="font-medium text-gray-900 dark:text-white block">{{ $budget->contact_name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->contact_phone }}</span>
                                    </div>
                                    <div class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold uppercase rounded">
                                        Vincular Cliente Necessário
                                    </div>
                                </div>

                                <div class="flex p-1 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <button type="button" 
                                            @click="clientType = 'existing'"
                                            :class="clientType === 'existing' ? 'bg-white dark:bg-gray-600 shadow-sm text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'"
                                            class="flex-1 px-3 py-1.5 text-xs font-medium rounded-md transition-all">
                                        Cliente Existente
                                    </button>
                                    <button type="button" 
                                            @click="clientType = 'new'"
                                            :class="clientType === 'new' ? 'bg-white dark:bg-gray-600 shadow-sm text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'"
                                            class="flex-1 px-3 py-1.5 text-xs font-medium rounded-md transition-all">
                                        Criar Novo Cliente
                                    </button>
                                </div>
                                
                                <div x-show="clientType === 'existing'" class="pt-1">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        Selecionar Cliente <span class="text-red-500">*</span>
                                    </label>
                                    <select name="client_id" x-bind:required="clientType === 'existing'" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 transition-all">
                                        <option value="">Selecione um cliente...</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->phone_primary }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="clientType === 'new'" class="pt-1 space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nome completo <span class="text-red-500">*</span></label>
                                        <input type="text" name="new_client_name" value="{{ $budget->contact_name }}" x-bind:required="clientType === 'new'"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp <span class="text-red-500">*</span></label>
                                        <input type="text" name="new_client_phone" value="{{ $budget->contact_phone }}" x-bind:required="clientType === 'new'"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 transition-all">
                                    </div>
                                    <input type="hidden" name="create_new_client" x-bind:value="clientType === 'new' ? '1' : '0'">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                        <span class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Valor Total:</span>
                        <span class="font-bold text-lg text-indigo-600 dark:text-indigo-400 block" id="original-total">R$ {{ number_format($budget->total, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Opções Adicionais: Evento e Desconto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Checkbox Evento -->
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_event" 
                                   value="1"
                                   class="w-5 h-5 text-amber-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-amber-500 dark:focus:ring-amber-400 focus:ring-2">
                            <div class="ml-3">
                                <span class="font-medium text-gray-900 dark:text-white">🎉 Pedido para Evento</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Marque se este pedido é para um evento específico</p>
                            </div>
                        </label>
                    </div>

                    <!-- Campo de Desconto -->
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            💰 Desconto
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium text-sm">R$</span>
                                <input type="number" 
                                       name="discount_amount" 
                                       id="discount_amount"
                                       step="0.01" 
                                       min="0"
                                       max="{{ $budget->total }}"
                                       value="0"
                                       onchange="updateFinalTotal()"
                                       oninput="updateFinalTotal()"
                                       class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-all">
                            </div>
                            <div class="text-right min-w-[100px]">
                                <span class="text-xs text-gray-600 dark:text-gray-400 block">Valor Final:</span>
                                <span class="font-bold text-green-600 dark:text-green-400" id="final-total">R$ {{ number_format($budget->total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <input type="hidden" name="original_total" value="{{ $budget->total }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens do Orçamento -->
        @foreach($budget->items as $index => $item)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-600 dark:bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                                {{ $index + 1 }}
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ json_decode($item->personalization_types)->print_type ?? 'Item' }}
                            </h3>
                        </div>
                        <div class="mt-2 ml-11 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <strong class="mr-1">Qtd:</strong> {{ $item->quantity }} peças
                            </span>
                            <span class="inline-flex items-center">
                                <strong class="mr-1">Tecido:</strong> {{ $item->fabric }}
                            </span>
                            <span class="inline-flex items-center">
                                <strong class="mr-1">Cor:</strong> {{ $item->color }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6 bg-white dark:bg-gray-800">
                <!-- Tamanhos -->
                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <label class="flex items-center text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Distribuição de Tamanhos <span class="text-red-500 ml-1">*</span>
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-2">(Total: {{ $item->quantity }} peças)</span>
                    </label>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        @foreach($availableSizes as $size)
                        <div class="relative">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5 text-center">{{ $size }}</label>
                            <input type="number" 
                                   name="sizes[{{ $index }}][{{ $size }}]" 
                                   min="0" 
                                   value="0"
                                   class="w-full px-3 py-2 text-center text-sm font-medium border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all"
                                   onchange="updateTotalSizes({{ $index }}, {{ $item->quantity }})">
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                        <p id="size-total-{{ $index }}" class="text-sm text-gray-600 dark:text-gray-300">
                            Total informado: <span class="font-bold text-lg" id="size-sum-{{ $index }}">0</span> / <span class="font-bold text-lg">{{ $item->quantity }}</span> peças
                        </p>
                    </div>
                    @error('sizes.' . $index)
                        <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Upload de Arquivos -->
                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <label class="flex items-center text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Arquivos (Imagens e Arquivos Corel)
                    </label>
                    <input type="file" 
                           name="item_files[{{ $index }}][]" 
                           multiple 
                           accept=".jpg,.jpeg,.png,.gif,.cdr,.ai,.pdf,.svg"
                           class="hidden"
                           id="file-input-{{ $index }}"
                           data-paste-upload="true"
                           data-paste-max-size="10"
                           data-paste-extensions="jpg,jpeg,png,gif,cdr,ai,pdf,svg">
                    <button type="button" 
                            onclick="window.pasteModal.open(document.getElementById('file-input-{{ $index }}'))"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Adicionar Arquivos (Ctrl+V, Arraste ou Clique)
                    </button>
                    <div id="files-preview-{{ $index }}" class="mt-3"></div>
                </div>

                <!-- Personalizações do Item -->
                @if($item->customizations->count() > 0)
                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <h4 class="flex items-center text-sm font-medium text-gray-900 dark:text-white mb-3">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Personalizações
                    </h4>
                    <div class="space-y-3">
                        @foreach($item->customizations as $customIndex => $custom)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-900 dark:text-white block mb-1">{{ $custom->personalization_type }}</span>
                                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-600 dark:text-gray-400">
                                        <span><strong>Local:</strong> {{ $custom->location }}</span>
                                        <span><strong>Tamanho:</strong> {{ $custom->size }}</span>
                                        <span><strong>Qtd:</strong> {{ $custom->quantity }}</span>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <span class="font-bold text-sm text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                        R$ {{ number_format($custom->total_price, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Upload de Imagem por Aplicação -->
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <svg class="w-4 h-4 inline mr-1 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Imagem desta aplicação (opcional)
                                </label>
                                <input type="file" 
                                       name="customization_images[{{ $index }}][{{ $customIndex }}]"
                                       accept="image/jpeg,image/png,image/jpg,image/gif"
                                       class="hidden"
                                       id="custom-image-{{ $index }}-{{ $customIndex }}"
                                       data-paste-upload="true"
                                       data-paste-images-only="true"
                                       data-paste-max-size="5"
                                       data-paste-extensions="jpg,jpeg,png,gif">
                                <button type="button" 
                                        onclick="window.pasteModal.open(document.getElementById('custom-image-{{ $index }}-{{ $customIndex }}'))"
                                        class="w-full inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white text-sm font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Adicionar Imagem da Aplicação
                                </button>
                                <div id="custom-image-preview-{{ $index }}-{{ $customIndex }}" class="mt-2"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Data de Entrega -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Data de Entrega</h2>
                </div>
            </div>
            <div class="p-6 bg-white dark:bg-gray-800">
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                    Data de Entrega Prevista <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       name="delivery_date" 
                       value="{{ \App\Helpers\DateHelper::calculateDeliveryDate(\Carbon\Carbon::now(), 15)->format('Y-m-d') }}"
                       class="max-w-xs px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all">
                @error('delivery_date')
                    <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Pagamento -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pagamento</h2>
                </div>
            </div>
            <div class="p-6 space-y-4 bg-white dark:bg-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Forma de Pagamento <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method" 
                                required
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all">
                            <option value="" class="dark:bg-gray-700">Selecione...</option>
                            @foreach($paymentMethods as $key => $label)
                            <option value="{{ $key }}" class="dark:bg-gray-700">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Valor do Pagamento <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">R$</span>
                            <input type="number" 
                                   name="payment_amount" 
                                   step="0.01" 
                                   min="0"
                                   value="{{ $budget->total }}"
                                   required
                                   class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all">
                        </div>
                        @error('payment_amount')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Observações do Pagamento
                    </label>
                    <textarea name="payment_notes" 
                              rows="3"
                              placeholder="Ex: Entrada de 50%, restante na entrega"
                              class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all resize-none"></textarea>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <a href="{{ route('budget.show', $budget->id) }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            </a>
            <button type="submit" id="submit-btn"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 dark:from-green-600 dark:to-green-700 dark:hover:from-green-700 dark:hover:to-green-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <svg class="w-5 h-5 mr-2" id="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg class="w-5 h-5 mr-2 animate-spin hidden" id="loading-icon" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="submit-text">Converter em Pedido</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Função para atualizar o total final com desconto
    const originalTotal = {{ $budget->total }};
    
    function updateFinalTotal() {
        const discountInput = document.getElementById('discount_amount');
        const finalTotalEl = document.getElementById('final-total');
        const paymentAmountInput = document.querySelector('input[name="payment_amount"]');
        
        let discount = parseFloat(discountInput.value) || 0;
        
        // Limitar desconto ao valor máximo
        if (discount > originalTotal) {
            discount = originalTotal;
            discountInput.value = discount;
        }
        if (discount < 0) {
            discount = 0;
            discountInput.value = 0;
        }
        
        const finalTotal = originalTotal - discount;
        
        // Atualizar exibição
        finalTotalEl.textContent = 'R$ ' + finalTotal.toFixed(2).replace('.', ',');
        
        // Atualizar valor do pagamento
        if (paymentAmountInput) {
            paymentAmountInput.value = finalTotal.toFixed(2);
        }
    }

    // Mostrar preview dos arquivos adicionados
    document.addEventListener('DOMContentLoaded', function() {
        // Listener para todos os inputs de arquivo
        document.querySelectorAll('input[type="file"][data-paste-upload="true"]').forEach(input => {
            input.addEventListener('change', function() {
                const previewId = this.id.replace('file-input-', 'files-preview-')
                                       .replace('custom-image-', 'custom-image-preview-');
                const previewContainer = document.getElementById(previewId);
                
                if (!previewContainer) return;
                
                previewContainer.innerHTML = '';
                
                if (this.files.length === 0) return;
                
                const filesArray = Array.from(this.files);
                
                filesArray.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg mr-2 mb-2';
                    
                    const isImage = file.type.startsWith('image/');
                    
                    if (isImage) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            fileItem.innerHTML = `
                                <img src="${e.target.result}" class="w-10 h-10 object-cover rounded" alt="${file.name}">
                                <span class="text-xs text-gray-700 dark:text-gray-300">${file.name}</span>
                                <span class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</span>
                            `;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        const icon = getFileIconEmoji(file.name);
                        fileItem.innerHTML = `
                            <span class="text-2xl">${icon}</span>
                            <span class="text-xs text-gray-700 dark:text-gray-300">${file.name}</span>
                            <span class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</span>
                        `;
                    }
                    
                    previewContainer.appendChild(fileItem);
                });
            });
        });
    });
    
    function getFileIconEmoji(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            pdf: '📄',
            cdr: '🎨',
            ai: '🎨',
            svg: '🖼️',
        };
        return icons[ext] || '📎';
    }

    // Atualizar total de tamanhos
    function updateTotalSizes(itemIndex, expectedTotal) {
        const inputs = document.querySelectorAll(`input[name^="sizes[${itemIndex}]"]`);
        let sum = 0;
        inputs.forEach(input => {
            sum += parseInt(input.value) || 0;
        });
        
        const sumElement = document.getElementById(`size-sum-${itemIndex}`);
        const totalElement = document.getElementById(`size-total-${itemIndex}`);
        
        sumElement.textContent = sum;
        
        // Mudar cor conforme o total
        if (sum === expectedTotal) {
            totalElement.className = 'text-sm text-green-600 dark:text-green-400 font-medium';
            sumElement.className = 'font-bold text-lg text-green-600 dark:text-green-400';
        } else if (sum > expectedTotal) {
            totalElement.className = 'text-sm text-red-600 dark:text-red-400 font-medium';
            sumElement.className = 'font-bold text-lg text-red-600 dark:text-red-400';
        } else {
            totalElement.className = 'text-sm text-gray-600 dark:text-gray-300';
            sumElement.className = 'font-bold text-lg text-gray-900 dark:text-white';
        }
    }

    // Validar antes de enviar
    document.getElementById('convert-form').addEventListener('submit', function(e) {
        const items = {{ $budget->items->count() }};
        let errors = [];
        
        // Validar tamanhos
        for (let i = 0; i < items; i++) {
            const inputs = document.querySelectorAll(`input[name^="sizes[${i}]"]`);
            let sum = 0;
            inputs.forEach(input => {
                sum += parseInt(input.value) || 0;
            });
            
            if (sum === 0) {
                errors.push(`Item ${i + 1}: Informe a distribuição de tamanhos`);
            }
        }
        
        // Validar forma de pagamento
        const paymentMethod = document.querySelector('select[name="payment_method"]');
        if (!paymentMethod.value) {
            errors.push('Selecione a forma de pagamento');
        }
        
        // Se houver erros, mostrar e prevenir envio
        if (errors.length > 0) {
            e.preventDefault();
            
            // Criar mensagem de erro visível
            let errorHtml = '<div class="fixed top-4 right-4 z-50 max-w-md bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg shadow-lg animate-shake">';
            errorHtml += '<div class="flex items-start">';
            errorHtml += '<svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">';
            errorHtml += '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>';
            errorHtml += '</svg>';
            errorHtml += '<div class="flex-1">';
            errorHtml += '<h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Corrija os seguintes erros:</h3>';
            errorHtml += '<ul class="text-sm text-red-700 dark:text-red-300 space-y-1">';
            errors.forEach(error => {
                errorHtml += `<li>• ${error}</li>`;
            });
            errorHtml += '</ul>';
            errorHtml += '</div>';
            errorHtml += '<button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200">';
            errorHtml += '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">';
            errorHtml += '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>';
            errorHtml += '</svg>';
            errorHtml += '</button>';
            errorHtml += '</div>';
            errorHtml += '</div>';
            
            // Remover alertas anteriores
            document.querySelectorAll('.animate-shake').forEach(el => el.remove());
            
            // Adicionar novo alerta
            document.body.insertAdjacentHTML('beforeend', errorHtml);
            
            // Scroll para o primeiro erro
            if (errors[0].includes('Item')) {
                const itemNum = parseInt(errors[0].match(/Item (\d+)/)[1]) - 1;
                const sizeSection = document.getElementById(`size-total-${itemNum}`);
                if (sizeSection) {
                    sizeSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    sizeSection.parentElement.classList.add('ring-2', 'ring-red-500');
                    setTimeout(() => {
                        sizeSection.parentElement.classList.remove('ring-2', 'ring-red-500');
                    }, 3000);
                }
            }
            
            // Auto-remover após 7 segundos
            setTimeout(() => {
                document.querySelectorAll('.animate-shake').forEach(el => el.remove());
            }, 7000);
            
            return false;
        }
        
        // Se passou na validação, mostrar loading
        const submitBtn = document.getElementById('submit-btn');
        const checkIcon = document.getElementById('check-icon');
        const loadingIcon = document.getElementById('loading-icon');
        const submitText = document.getElementById('submit-text');
        
        submitBtn.disabled = true;
        checkIcon.classList.add('hidden');
        loadingIcon.classList.remove('hidden');
        submitText.textContent = 'Processando...';
    });
    
    // Se voltar com erros, remover loading
    @if ($errors->any())
    window.addEventListener('DOMContentLoaded', function() {
        const submitBtn = document.getElementById('submit-btn');
        const checkIcon = document.getElementById('check-icon');
        const loadingIcon = document.getElementById('loading-icon');
        const submitText = document.getElementById('submit-text');
        
        if (submitBtn) {
            submitBtn.disabled = false;
            checkIcon.classList.remove('hidden');
            loadingIcon.classList.add('hidden');
            submitText.textContent = 'Converter em Pedido';
        }
        
        // Scroll para o topo para ver os erros
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    @endif
</script>
@endpush
@endsection

