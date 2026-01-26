@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20" style="color: white !important;">3</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Personalização</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 3 de 4</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">75%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-2.5 shadow-inner">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out shadow-lg shadow-indigo-500/30 dark:shadow-indigo-600/30" style="width: 75%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800">
            
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-900/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                            <svg class="w-6 h-6 text-white" style="color: white !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Personalizações</h1>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Configure as personalizações de cada item</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                
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

                <!-- Resumo -->
                <!-- Resumo -->
                <div class="bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700 p-5 mb-6">
                    @php
                        // Correctly detect context (Edit vs Create)
                        if (isset($order)) {
                            // Edit Order Context
                            $totalItems = $order->items->count();
                            $totalPieces = $order->items->sum('quantity');
                            $totalApplications = 0;
                            $totalApplicationsCount = 0;
                            foreach($order->items as $item) {
                                $itemApplications = \App\Models\OrderSublimation::where('order_item_id', $item->id)->get();
                                $totalApplications += $itemApplications->sum('final_price');
                                $totalApplicationsCount += $itemApplications->count();
                            }
                        } else {
                            // Contexto de Orçamento (Budget)
                            $totalItems = count($itemPersonalizations);
                            $totalPieces = 0;
                            $totalApplications = 0;
                            $totalApplicationsCount = 0;
                            
                            foreach($itemPersonalizations as $itemData) {
                                $item = $itemData['item'];
                                $totalPieces += $item->quantity;
                                
                                // Em orçamento, as personalizações estão na sessão, mas aqui elas são passadas
                                // dentro de $itemData mas apenas IDs e nomes para o wizard, não valores totais calculados
                                // No entanto, o controller pode passar customs
                                
                                // Para o wizard de orçamento, talvez não tenhamos o total de aplicações calculado aqui ainda
                                // Vamos tentar pegar da sessão se disponível ou zerar
                                $customizations = session('budget_customizations', []);
                                foreach($customizations as $custom) {
                                    if (isset($custom['item_index']) && $custom['item_index'] == $item->id) {
                                        $totalApplications += $custom['final_price'] ?? 0;
                                        $totalApplicationsCount++;
                                    }
                                }
                            }
                        }
                        
                        $avgPerPiece = $totalPieces > 0 ? $totalApplications / $totalPieces : 0;
                    @endphp

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Total de Itens</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalItems }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Total de Peças</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white" id="total-pecas" data-total-pecas="{{ $totalPieces }}">{{ $totalPieces }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Total de Aplicações</p>
                            <p class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($totalApplications, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">{{ $totalApplicationsCount }} {{ $totalApplicationsCount == 1 ? 'aplicação' : 'aplicações' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1 font-medium">Custo por Peça</p>
                            <p class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($avgPerPiece, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-500">Média das aplicações</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Itens -->
                <div class="space-y-6">
                    @foreach($itemPersonalizations as $itemData)
                        @php
                            $item = $itemData['item'];
                            $persIds = $itemData['personalization_ids'];
                            $persNames = $itemData['personalization_names'];
                        @endphp
                        
                        <div class="border border-gray-200 dark:border-slate-700 rounded-xl overflow-hidden bg-white dark:bg-slate-800">
                            
                            <!-- Item Header -->
                            <div class="bg-gray-50 dark:bg-slate-800/50 px-5 py-4 border-b border-gray-200 dark:border-slate-700">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Item {{ $item->item_number }} <span class="text-xs text-gray-400 font-normal">(ID: {{ $item->id }})</span></h3>
                                        <p class="text-sm text-gray-600 dark:text-slate-400 mt-0.5">{{ $item->quantity }} peças • {{ $item->fabric }} • {{ $item->color }}</p>
                                    </div>
                                    <span class="text-xs px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-md font-medium">
                                        {{ count($persIds) }} {{ count($persIds) == 1 ? 'personalização' : 'personalizações' }}
                                    </span>
                                </div>
                                @php
                                    $itemTotalApplications = \App\Models\OrderSublimation::where('order_item_id', $item->id)->sum('final_price');
                                    $itemApplicationsCount = \App\Models\OrderSublimation::where('order_item_id', $item->id)->count();
                                    $itemAvgPerPiece = $item->quantity > 0 ? $itemTotalApplications / $item->quantity : 0;
                                @endphp
                                @if($itemApplicationsCount > 0)
                                    <div class="grid grid-cols-3 gap-3 text-xs bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-gray-200 dark:border-slate-700">
                                        <div>
                                            <span class="text-gray-500 dark:text-slate-400">Aplicações:</span>
                                            <span class="font-semibold text-gray-900 dark:text-white ml-1">{{ $itemApplicationsCount }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-slate-400">Total:</span>
                                            <span class="font-semibold text-indigo-600 dark:text-indigo-400 ml-1">R$ {{ number_format($itemTotalApplications, 2, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-slate-400">Por peça:</span>
                                            <span class="font-semibold text-indigo-600 dark:text-indigo-400 ml-1">R$ {{ number_format($itemAvgPerPiece, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Personalizações do Item -->
                            <div class="p-5 space-y-4">
                                @php
                                    // Para Budget wizard: buscar personalizações da sessão
                                    $sessionCustomizations = session('budget_customizations', []);
                                    
                                    // Filtrar customizações deste item
                                    $itemCustomizations = collect($sessionCustomizations)->filter(function($custom) use ($item) {
                                        return ($custom['item_index'] ?? null) == $item->id;
                                    });
                                    
                                    // Agrupar por tipo de personalização
                                    $groupedPersonalizations = $itemCustomizations->groupBy(function($custom) {
                                        return strtoupper($custom['personalization_name'] ?? $custom['personalization_type'] ?? '');
                                    });
                                @endphp
                                
                                @foreach($persIds as $persId)
                                    @php
                                        $persName = $persNames[$persId] ?? 'Personalização';
                                        $persNameUpper = strtoupper($persName);
                                        
                                        // Buscar personalizações deste tipo específico da sessão
                                        $existingPersonalizations = $groupedPersonalizations->get($persNameUpper, collect());
                                    @endphp
                                    
                                    <div class="bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-4">
                                        
                                        <!-- Tipo de Personalização -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/30 rounded flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $persName }}</h4>
                                            </div>
                                            <button 
                                                type="button"
                                                onclick="window.openPersonalizationModal({{ $item->id }}, '{{ $persName }}', {{ $persId }}, {{ $item->quantity }})"
                                                class="text-sm px-3 py-1.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors font-medium" style="color: white !important;">
                                                + Adicionar
                                            </button>
                                        </div>
                                        
                                        <!-- Lista de Personalizações Adicionadas -->
                                        <div id="personalizations-list-{{ $item->id }}-{{ $persId }}" class="space-y-2">
                                            @if($existingPersonalizations->count() > 0)
                                                @foreach($existingPersonalizations as $persIndex => $pers)
                                                    <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex-1">
                                                                <div class="flex items-center space-x-4 text-sm">
                                                                    @if(!empty($pers['location']))
                                                                        <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Local:</strong> {{ $pers['location'] }}</span>
                                                                    @endif
                                                                    @if(!empty($pers['size']))
                                                                        <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Tamanho:</strong> {{ $pers['size'] }}</span>
                                                                    @endif
                                                                    <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Qtd:</strong> {{ $pers['quantity'] ?? 0 }}</span>
                                                                    @if(!empty($pers['color_count']) && $pers['color_count'] > 1)
                                                                        <span class="text-gray-700 dark:text-slate-300"><strong class="dark:text-white">Cores:</strong> {{ $pers['color_count'] }}</span>
                                                                    @endif
                                                                    @if(($pers['final_price'] ?? 0) > 0)
                                                                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold">R$ {{ number_format($pers['final_price'], 2, ',', '.') }}</span>
                                                                    @else
                                                                        <span class="text-red-600 dark:text-red-400 font-semibold">R$ 0,00</span>
                                                                    @endif
                                                                </div>
                                                        </div>
                                                        <div class="flex space-x-2 ml-4">
                                                            <button 
                                                                type="button"
                                                                onclick="removeSessionPersonalization({{ $loop->parent->index }})" 
                                                                class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                                                                title="Remover personalização">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-sm text-gray-500 dark:text-slate-400 text-center py-4">Nenhuma personalização adicionada</p>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                                
                                @php
                                    // Personalizações que não correspondem a nenhum tipo selecionado
                                    $selectedTypes = collect($persNames)->map(fn($name) => strtoupper($name))->toArray();
                                    $orphanTypes = $groupedPersonalizations->keys()->diff($selectedTypes);
                                @endphp
                                
                                @if($orphanTypes->isNotEmpty())
                                    @foreach($orphanTypes as $orphanType)
                                        @php
                                            $orphanPersonalizations = $groupedPersonalizations->get($orphanType);
                                        @endphp
                                        
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 bg-yellow-100 dark:bg-yellow-900/30 rounded flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-medium text-yellow-900 dark:text-yellow-200">{{ $orphanType }}</h4>
                                                        <p class="text-xs text-yellow-700 dark:text-yellow-400">Tipo não selecionado na etapa anterior</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="space-y-2">
                                                @foreach($orphanPersonalizations as $pers)
                                                    <div class="p-3 bg-white dark:bg-slate-800 rounded border border-yellow-200 dark:border-yellow-700">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex-1">
                                                                <div class="flex items-center space-x-4 text-sm">
                                                                    @if($pers->location_name)
                                                                        <span class="text-gray-600 dark:text-slate-400">📍 {{ $pers->location_name }}</span>
                                                                    @endif
                                                                    @if($pers->size_name)
                                                                        <span class="text-gray-600 dark:text-slate-400">📏 {{ $pers->size_name }}</span>
                                                                    @endif
                                                                    @if($pers->quantity)
                                                                        <span class="text-gray-600 dark:text-slate-400">🔢 {{ $pers->quantity }} peças</span>
                                                                    @endif
                                                                    @if($pers->final_price)
                                                                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold">💰 R$ {{ number_format($pers->final_price, 2, ',', '.') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center space-x-2 ml-4">
                                                                <button type="button" 
                                                                    onclick="editPersonalization({{ $pers->id }})"
                                                                    class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                                                                    title="Editar">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                    </svg>
                                                                </button>
                                                                <button type="button"
                                                                    onclick="deletePersonalization({{ $pers->id }})"
                                                                    class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                                                    title="Excluir">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Navegação -->
                <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-slate-700">
                    <a href="{{ route('budget.items') }}" 
                       class="px-4 py-2 text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white font-medium">
                        ← Voltar
                    </a>
                    <a href="{{ route('budget.confirm') }}" 
                       class="px-6 py-2.5 text-sm bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 font-semibold transition-all" style="color: white !important;">
                        Continuar →
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal de Adicionar Personalização -->
    <div id="personalizationModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-800">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modalTitle">Adicionar Personalização</h3>
                <button type="button" onclick="closePersonalizationModal()" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form id="personalizationForm" action="{{ route('budget.customization') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                <input type="hidden" id="modal_item_id" name="item_id">
                <input type="hidden" id="modal_personalization_type" name="personalization_type">
                <input type="hidden" id="modal_personalization_id" name="personalization_id">
                <input type="hidden" id="editing_personalization_id" name="editing_personalization_id">

                <!-- Localização (oculto para SUB. TOTAL) -->
                <div id="locationField">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Localização</label>
                    <select id="location" name="location" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all" data-required-for="!SUB. TOTAL">
                        <option value="">Selecione...</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tamanho (REMOVIDO para Orçamento) -->
                <div id="sizeField" class="hidden">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Tamanho</label>
                    <select id="size" name="size" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                        <option value="PADRÃO">PADRÃO</option>
                    </select>
                </div>

                <!-- Campos ocultos para SUB. TOTAL (para evitar validação) -->
                <input type="hidden" id="location_hidden" name="location" value="" disabled>
                <input type="hidden" id="size_hidden" name="size" value="" disabled>
                <input type="hidden" id="quantity_hidden" name="quantity" value="1" disabled>



                <!-- Adicionais (para Sublimação Total) -->
                <div id="addonsField" class="hidden">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Adicionais</label>
                    
                    <!-- Checkbox para REGATA (desconto) -->
                    <div class="mb-3">
                        <label class="flex items-center">
                            <input type="checkbox" id="regataCheckbox" name="regata_discount" value="1"
                                   class="w-4 h-4 text-indigo-600 dark:text-indigo-500 border-gray-300 dark:border-slate-600 rounded focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-slate-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-slate-300">
                                <span class="font-semibold text-green-600 dark:text-green-400">REGATA</span> - Desconto de R$ 3,00
                            </span>
                        </label>
                    </div>
                    
                    <!-- Botão para adicionar outros adicionais -->
                    <div class="mb-3">
                        <button type="button" id="addAddonBtn" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900/30 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar Adicional
                        </button>
                    </div>
                    
                    <!-- Lista de adicionais adicionados -->
                    <div id="addonsList" class="space-y-2">
                        <!-- Adicionais serão adicionados aqui dinamicamente -->
                    </div>
                    
                    <!-- Select oculto para adicionais (para formulário) -->
                    <select id="addons" name="addons[]" multiple class="hidden">
                        <!-- Será preenchido dinamicamente -->
                    </select>
                    
                    <div id="addons-prices" class="mt-2 space-y-1">
                        <!-- Preços dos adicionais selecionados serão exibidos aqui -->
                    </div>
                </div>

                <!-- Quantidade (oculto para SUB. TOTAL) -->
                <div id="quantityField">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Quantidade</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" readonly
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 cursor-not-allowed focus:ring-0 transition-all" data-required-for="!SUB. TOTAL">
                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Quantidade automática conforme o item</p>
                </div>

                <!-- Cores (para Serigrafia e Emborrachado) -->
                <div id="colorCountField" class="hidden">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Número de Cores</label>
                    <input type="number" id="color_count" name="color_count" min="1" value="1"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Apenas para Serigrafia e Emborrachado</p>
                </div>

                <!-- Preço Calculado -->
                <div id="priceDisplay" class="hidden">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Preço por Aplicação:</span>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400" id="unitPrice">R$ 0,00</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-gray-600 dark:text-slate-400">Total desta Aplicação:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white" id="totalPrice">R$ 0,00</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-2 text-center" id="priceFormula">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>
                </div>
                <input type="hidden" id="unit_price" name="unit_price" value="0">
                <input type="hidden" id="final_price" name="final_price" value="0">



                <!-- Botões -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                    <button type="button" onclick="closePersonalizationModal()"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white font-medium">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 text-sm bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 font-semibold transition-all" style="color: white !important;">
                        Adicionar
                    </button>
                </div>
            </form>
            
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteConfirmationModal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-lg max-w-md w-full shadow-xl border border-gray-200 dark:border-slate-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmar Remoção</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-3">Deseja realmente remover esta personalização?</p>
                <div id="delete-item-info" class="p-3 bg-gray-50 dark:bg-slate-800/50 rounded-md text-sm border border-gray-200 dark:border-slate-700">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteConfirmationModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDeletePersonalization()" 
                        class="px-4 py-2 bg-red-600 dark:bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:hover:bg-red-700 transition-colors">
                    Remover
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        console.log("🚀 Script de personalização carregado!");

        // --- FUNÇÕES DE ADICIONAIS (Placeholder) ---
        function calculateAddonsTotal() {
            console.warn('calculateAddonsTotal: Funcionalidade não implementada completamente.');
            return 0;
        }

        function openAddonModal() {
            console.warn('openAddonModal: Funcionalidade não implementada.');
            alert('Gerenciamento de adicionais indisponível no momento.');
        }

        function updateAddonsPrices() {
             console.warn('updateAddonsPrices: Funcionalidade não implementada.');
        }
        // ------------------------------------------

        let currentItemId = null;
        let currentPersonalizationType = '';
        let currentPersonalizationId = '';
        let isSubmitting = false; // Flag para prevenir múltiplos envios
        let lastSubmitTime = 0; // Timestamp do último envio
        let pendingDeleteId = null; // ID da personalização pendente de exclusão
        
        // Cache para evitar requisições repetidas de preço
        let cachedBasePrice = null;
        let cachedPriceParams = '';

        // Dados de tamanhos por tipo
        const personalizationSizes = @json($personalizationData);
        const normalizeTypeKey = (type) => (type || '').toString().trim().toUpperCase();

        const personalizationForm = document.getElementById('personalizationForm');
        let listenerRegistered = false;
        
        if (!listenerRegistered) {
            personalizationForm.addEventListener('submit', handleFormSubmit);
            listenerRegistered = true;
        }
        
        // Função para carregar tamanhos
        function loadSizes(type) {
            const sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = '<option value="">Selecione...</option>';
            
            // Normalizar a chave do tipo (remover espaços, pontos, etc se necessário para bater com as chaves do array)
            // Mas aqui as chaves parecem ser strings diretas como 'DTF', 'SERIGRAFIA', etc.
            const typeKey = normalizeTypeKey(type);
            const typeData = personalizationSizes[typeKey] || personalizationSizes[type];
            
            if (typeData && typeData.sizes) {
                // Verificar se ?? um array antes de iterar
                const sizes = typeData.sizes;
                
                const processSize = (size) => {
                    // Filtrar "COR" para EMBORRACHADO e SERIGRAFIA - COR não é um tamanho válido para seleção
                    if ((typeKey === 'EMBORRACHADO' || typeKey === 'SERIGRAFIA') && size.size_name === 'COR') {
                        return; // Pular esta opção
                    }
                    
                    const option = document.createElement('option');
                    option.value = size.size_name;
                    option.textContent = size.size_name + (size.size_dimensions ? ` (${size.size_dimensions})` : '');
                    option.dataset.dimensions = size.size_dimensions || '';
                    sizeSelect.appendChild(option);
                };

                if (Array.isArray(sizes)) {
                    sizes.forEach(processSize);
                } else if (typeof sizes === 'object') {
                    // Se for um objeto (collection convertida para json as vezes vira objeto com índices numéricos)
                    Object.values(sizes).forEach(processSize);
                }
            }
        }
        // Carregar adicionais de sublimação
        function setupAddonListeners() {
            const addBtn = document.getElementById('addAddonBtn');
            const regataCheck = document.getElementById('regataCheckbox');
            const quantityInput = document.getElementById('quantity');

            // Remover listeners antigos para evitar duplicação
            if (addBtn) {
                addBtn.removeEventListener('click', openAddonModal);
                addBtn.addEventListener('click', openAddonModal);
            }

            if (regataCheck) {
                regataCheck.removeEventListener('change', updateAddonsPrices);
                regataCheck.addEventListener('change', updateAddonsPrices);
            }
            
            if (quantityInput) {
                quantityInput.removeEventListener('input', calculatePrice);
                quantityInput.removeEventListener('change', calculatePrice);
                quantityInput.addEventListener('input', calculatePrice);
                quantityInput.addEventListener('change', calculatePrice);
            }
        }


        // Tornar a função globalmente acessível
        window.openPersonalizationModal = function(itemId, persType, persId, itemQuantity = 1) {
            
            currentItemId = itemId;
            currentPersonalizationType = persType;
            currentPersonalizationId = persId;
            
            document.getElementById('modal_item_id').value = itemId;
            document.getElementById('modal_personalization_type').value = persType;
            document.getElementById('modal_personalization_id').value = persId;
            document.getElementById('editing_personalization_id').value = '';
            document.getElementById('modalTitle').textContent = `Adicionar ${persType}`;
            const normalizedType = normalizeTypeKey(persType);
            
            // Limpar cache de preço ao abrir modal
            cachedBasePrice = null;
            cachedPriceParams = '';

            // Limpar lista de adicionais e select oculto
            const addonsList = document.getElementById('addonsList');
            if(addonsList) addonsList.innerHTML = '';
            
            const addonsSelect = document.getElementById('addons');
            if(addonsSelect) addonsSelect.innerHTML = '';
            
            const addonsPrices = document.getElementById('addons-prices');
            if(addonsPrices) addonsPrices.innerHTML = '';
            
            const regataCheck = document.getElementById('regataCheckbox');
            if(regataCheck) regataCheck.checked = false;

            if (normalizedType === 'SERIGRAFIA' || normalizedType === 'EMBORRACHADO') {
                const colorCountField = document.getElementById('colorCountField');
                if (colorCountField) {
                    colorCountField.classList.remove('hidden');
                    document.getElementById('color_count').value = '1';
                }
            } else {
                const colorCountField = document.getElementById('colorCountField');
                if (colorCountField) colorCountField.classList.add('hidden');
            }
            
            const toggleField = (id, show) => {
                const el = document.getElementById(id);
                if (el) {
                    if (show) el.classList.remove('hidden');
                    else el.classList.add('hidden');
                }
            };

            // Mostrar/ocultar campos baseado no tipo de personalização
            if (normalizedType === 'SUB. TOTAL') {
                toggleField('locationField', false);
                toggleField('sizeField', false);
                toggleField('quantityField', true);
                toggleField('colorDetailsField', false);
                toggleField('addonsField', true);
                
                setupAddonListeners();
            } else if (normalizedType === 'DTF') {
                toggleField('locationField', true);
                toggleField('sizeField', true);
                toggleField('quantityField', true);
                toggleField('colorDetailsField', false);
                toggleField('addonsField', false);
            } else if (normalizedType === 'SUB. LOCAL') {
                toggleField('locationField', true);
                toggleField('sizeField', true);
                toggleField('quantityField', true);
                toggleField('colorDetailsField', false);
                toggleField('addonsField', false);
            } else {
                toggleField('locationField', true);
                toggleField('sizeField', true);
                toggleField('quantityField', true);
                toggleField('colorDetailsField', true);
                toggleField('addonsField', false);
            }

            // Definir quantidade automaticamente para TODOS os tipos
            if(document.getElementById('quantity')) {
                document.getElementById('quantity').value = itemQuantity;
            }
            
            setTimeout(() => {
                calculatePrice();
            }, 500);
            
            // Carregar tamanhos
            loadSizes(normalizedType);
            
            // Limpar formulário (mas preservar os campos hidden que acabamos de setar)
            // document.getElementById('personalizationForm').reset(); // Isso limparia os hiddens também
            // Resetar apenas campos visíveis
            document.getElementById('location').value = '';
            document.getElementById('size').value = '';
            // Fields removed: color_details, seller_notes, art_files, application_image, selected_files_list
            // All file upload related elements have been removed from the form

            // Resetar flag de submissão ao abrir modal
            isSubmitting = false;
            
            // Resetar botão de submit
            const submitBtn = document.getElementById('personalizationForm').querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Adicionar';
            }
            
            // Mostrar modal
            document.getElementById('personalizationModal').classList.remove('hidden');
        }

        // FUNÇÃO REMOVIDA: setupFormValidation() - Não é mais necessária
        // O event listener já está registrado no início do script

        function handleFormSubmit(event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            
            const currentTime = Date.now();
            if (currentTime - lastSubmitTime < 1000) {
                return false;
            }
            
            if (isSubmitting) {
                return false;
            }
            
            const persType = document.getElementById('modal_personalization_type').value;
            let isValid = true;
            let errorMessage = '';
            
            if (persType === 'SUB. TOTAL') {
                // Validation for art files removed
                /*
                const artFiles = document.getElementById('art_files').files.length;
                
                if (artFiles === 0) {
                    isValid = false;
                    errorMessage += 'Pelo menos um arquivo da arte é obrigatório.\n';
                }
                */
                
                if (isValid) {
                    document.getElementById('location').disabled = true;
                    document.getElementById('size').disabled = true;
                    document.getElementById('location_hidden').disabled = false;
                    document.getElementById('size_hidden').disabled = false;
                    
                    const form = document.getElementById('personalizationForm');
                    
                    // Budget wizard - always use budget route
                    form.action = '{{ route("budget.customization") }}';
                    
                    form.removeEventListener('submit', handleFormSubmit);
                    
                    const formData = new FormData(form);
                    const targetUrl = '{{ route("budget.customization") }}';
                    
                    isSubmitting = true;
                    lastSubmitTime = Date.now();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Enviando...';
                    }
                    
                    fetch(targetUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na resposta do servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            isSubmitting = false;
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'Adicionar Personalização';
                            }
                            alert('Erro: ' + (data.message || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Adicionar Personalização';
                        }
                        console.error('Erro ao enviar formulário:', error);
                        alert('Erro ao processar a solicitação: ' + error.message);
                    });
                }
            } else {
                const location = document.getElementById('location').value;
                const size = document.getElementById('size').value;
                
                if (!location) {
                    isValid = false;
                    errorMessage += 'Localização é obrigatória.\n';
                }
                
                // Forçar um tamanho padrão se estiver vazio para orçamentos
                if (!size) {
                    document.getElementById('size').value = 'PADRÃO';
                }
                
                if (isValid) {
                    document.getElementById('location').name = 'location';
                    document.getElementById('size').name = 'size';
                    
                    const form = document.getElementById('personalizationForm');
                    
                    // Budget wizard - always use budget route
                    form.action = '{{ route("budget.customization") }}';
                    
                    form.removeEventListener('submit', handleFormSubmit);
                    
                    const formData = new FormData(form);
                    const targetUrl = '{{ route("budget.customization") }}';
                    
                    isSubmitting = true;
                    lastSubmitTime = Date.now();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Enviando...';
                    }
                    
                    fetch(targetUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na resposta do servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            isSubmitting = false;
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'Adicionar Personalização';
                            }
                            alert('Erro: ' + (data.message || 'Erro desconhecido'));
                        }
                    })
                    .catch(error => {
                        isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Adicionar Personalização';
                        }
                        console.error('Erro ao enviar formulário:', error);
                        alert('Erro ao processar a solicitação: ' + error.message);
                    });
                }
            }
            
            if (!isValid) {
                alert('Por favor, corrija os seguintes erros:\n\n' + errorMessage);
                return false;
            }
        }

        window.closePersonalizationModal = function closePersonalizationModal() {
            document.getElementById('personalizationModal').classList.add('hidden');
            currentItemId = '';
            currentPersonalizationType = '';
            currentPersonalizationId = '';
            
            // File upload elements removed - no cleanup needed
        }

        // Removed handleApplicationImageChange and handleArtFilesChange




        // Dados dos adicionais (exceto REGATA que é checkbox)
        const availableAddons = [
            {id: 2, name: 'M. LONGA', price_adjustment: 20.00, description: 'Manga longa'},
            {id: 3, name: 'POLO', price_adjustment: 20.00, description: 'Camisa polo'},
            {id: 4, name: 'RIB. SUBLI', price_adjustment: 3.50, description: 'Rib sublimado'},
            {id: 5, name: 'PUNHO SU', price_adjustment: 3.50, description: 'Punho sublimado'},
            {id: 6, name: 'GOLA V', price_adjustment: 3.15, description: 'Gola V'},
            {id: 7, name: 'GOLA PAD', price_adjustment: 5.25, description: 'Gola padrão'},
            {id: 8, name: 'AERODRY', price_adjustment: 3.50, description: 'Tecido aerodry'},
            {id: 9, name: 'UV CACH', price_adjustment: 12.00, description: 'Proteção UV'},
            {id: 10, name: 'CREPE POLIE', price_adjustment: 10.50, description: 'Crepe poliéster'},
            {id: 11, name: 'COR DIFERE', price_adjustment: 11.67, description: 'Cor diferenciada'},
            {id: 12, name: 'DRYFIT', price_adjustment: 12.00, description: 'Tecido dryfit'},
            {id: 13, name: 'FRISO OU AD', price_adjustment: 4.00, description: 'Friso ou adesivo'},
            {id: 14, name: 'M. RAGLAN', price_adjustment: 7.00, description: 'Manga raglan'},
            {id: 15, name: 'GOLEIRO', price_adjustment: 20.00, description: 'Camiseta goleiro'},
            {id: 16, name: 'PP ELASTANO-ALURE', price_adjustment: 10.00, description: 'PP elastano allure'},
            {id: 17, name: 'M.L.DRY', price_adjustment: 38.75, description: 'Manga longa dry'},
            {id: 18, name: 'CINZA MESCLADO', price_adjustment: 2.34, description: 'Cinza mesclado'},
            {id: 19, name: 'SUB NOME/NUMERO SUB', price_adjustment: 8.00, description: 'Nome/número sublimado'},
            {id: 20, name: 'SERIGRAFIA NOME/NUME', price_adjustment: 15.00, description: 'Nome/número serigrafado'},
            {id: 21, name: 'MATERIAL ESPORTIVO', price_adjustment: 72.00, description: 'Material esportivo'},
        ];

        // Carregar adicionais de sublimação
        // A função setupAddonListeners já foi definida anteriormente e é usada para configurar os listeners
        
        // Modal para selecionar adicionais
        function openAddonModal() {
            // Criar modal dinâmico
            const modalHtml = `
                <div id="addonModal" class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Selecionar Adicional</h3>
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                ${availableAddons.map(addon => `
                                    <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
                                        <input type="checkbox" class="addon-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded" 
                                               value="${addon.id}" data-name="${addon.name}" data-price="${addon.price_adjustment}" data-description="${addon.description}">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">${addon.name}</span>
                                            <span class="text-gray-500 dark:text-gray-400">${addon.price_adjustment >= 0 ? ' +' : ' '}R$ ${Math.abs(addon.price_adjustment).toFixed(2).replace('.', ',')}</span>
                                        </span>
                                    </label>
                                `).join('')}
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button type="button" id="cancelAddon" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors">
                                    Cancelar
                                </button>
                                <button type="button" id="confirmAddon" class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Eventos do modal
            document.getElementById('cancelAddon').addEventListener('click', closeAddonModal);
            document.getElementById('confirmAddon').addEventListener('click', confirmAddAddon);
        }

        function closeAddonModal() {
            document.getElementById('addonModal').remove();
        }

        function confirmAddAddon() {
            const checkboxes = document.querySelectorAll('.addon-checkbox:checked');
            const addonsList = document.getElementById('addonsList');
            const addonsSelect = document.getElementById('addons');
            
            checkboxes.forEach(checkbox => {
                const addonId = checkbox.value;
                const addonName = checkbox.dataset.name;
                const addonPrice = parseFloat(checkbox.dataset.price);
                const addonDescription = checkbox.dataset.description;
                
                // Verificar se já foi adicionado
                if (document.querySelector(`[data-addon-id="${addonId}"]`)) {
                    return; // Já existe, pular
                }
                
                // Criar elemento visual
                const addonElement = document.createElement('div');
                addonElement.className = 'flex items-center justify-between p-2 bg-gray-50 dark:bg-slate-700 rounded border border-gray-200 dark:border-slate-600';
                addonElement.setAttribute('data-addon-id', addonId);
                addonElement.innerHTML = `
                    <div class="flex items-center">
                        <span class="font-medium text-gray-700 dark:text-slate-200">${addonName}</span>
                        <span class="ml-2 text-sm text-gray-500 dark:text-slate-400">${addonPrice >= 0 ? '+' : ''}R$ ${Math.abs(addonPrice).toFixed(2).replace('.', ',')}</span>
                    </div>
                    <button type="button" class="remove-addon text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors" data-addon-id="${addonId}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                
                addonsList.appendChild(addonElement);
                
                // Adicionar ao select oculto
                const option = document.createElement('option');
                option.value = addonId;
                option.selected = true;
                option.textContent = addonName;
                addonsSelect.appendChild(option);
                
                // Evento para remover
                addonElement.querySelector('.remove-addon').addEventListener('click', function() {
                    addonElement.remove();
                    option.remove();
                    updateAddonsPrices();
                });
            });
            
            updateAddonsPrices();
            closeAddonModal();
        }

        // Atualizar preços dos adicionais selecionados
        function updateAddonsPrices() {
            const addonsSelect = document.getElementById('addons');
            const selectedAddons = Array.from(addonsSelect.options).filter(opt => opt.selected);
            const pricesContainer = document.getElementById('addons-prices');
            const regataCheckbox = document.getElementById('regataCheckbox');
            
            let totalAddonPrice = 0;
            let pricesHtml = '';
            
            // Verificar desconto REGATA
            if (regataCheckbox && regataCheckbox.checked) {
                totalAddonPrice += -3.00; // Desconto de R$ 3,00
                pricesHtml += `<div class="text-xs text-green-600 dark:text-green-400">• REGATA: -R$ 3,00 (desconto)</div>`;
            }
            
            // Adicionais selecionados - usar dados dos adicionais disponíveis
            if (selectedAddons.length > 0) {
                if (pricesHtml) pricesHtml += '<div class="mt-2"></div>';
                
                selectedAddons.forEach(option => {
                    // Buscar o adicional nos dados disponíveis pelo ID
                    const addonId = parseInt(option.value);
                    const addonData = availableAddons.find(a => a.id === addonId);
                    
                    if (addonData) {
                        const price = addonData.price_adjustment;
                        const sign = price >= 0 ? '+' : '';
                        
                        totalAddonPrice += price;
                        pricesHtml += `<div class="text-xs text-gray-600 dark:text-slate-400">• ${addonData.name}: ${sign}R$ ${Math.abs(price).toFixed(2).replace('.', ',')}</div>`;
                    }
                });
            }
            
            if (totalAddonPrice !== 0) {
                const sign = totalAddonPrice >= 0 ? '+' : '';
                pricesHtml += `<div class="text-sm font-medium text-gray-900 dark:text-white mt-2">Total adicionais: ${sign}R$ ${Math.abs(totalAddonPrice).toFixed(2).replace('.', ',')}</div>`;
            }
            
            pricesContainer.innerHTML = pricesHtml;
            
            // Recalcular preço após atualizar adicionais
            calculatePrice();
        }


        // Função auxiliar para calcular total de adicionais
        function calculateAddonsTotal() {
            const addonsSelect = document.getElementById('addons');
            if (!addonsSelect) return 0;
            
            const selectedAddons = Array.from(addonsSelect.options).filter(opt => opt.selected);
            let total = 0;
            
            // Verificar desconto REGATA
            const regataCheckbox = document.getElementById('regataCheckbox');
            if (regataCheckbox && regataCheckbox.checked) {
                total += -3.00; // Desconto de R$ 3,00
            }
            
            selectedAddons.forEach(option => {
                const addonId = parseInt(option.value);
                const addonData = availableAddons.find(a => a.id === addonId);
                
                if (addonData) {
                    total += addonData.price_adjustment;
                }
            });
            
            // console.log('Addons Total:', total);
            return total;
        }

        // Calcular preço
        async function calculatePrice() {
            const persTypeRaw = document.getElementById('modal_personalization_type').value;
            const persType = normalizeTypeKey(persTypeRaw);
            let size = document.getElementById('size').value || 'PADRÃO';
            const colorCount = parseInt(document.getElementById('color_count')?.value || 1);
            
            let quantity = 1;
            const quantityField = document.getElementById('quantity');
            if (quantityField) {
                quantity = parseInt(quantityField.value) || 1;
            }
            
            if (persType === 'SUB. TOTAL') {
                if (!persType || quantity <= 0) {
                    document.getElementById('priceDisplay').classList.add('hidden');
                    return;
                }
            } else {
                if (!persType) {
                    document.getElementById('priceDisplay').classList.add('hidden');
                    return;
                }
                
                // Se o tamanho não estiver definido, usar um fallback inteligente baseado no tipo
                if (!size || size === 'PADRÃO') {
                    if (persType === 'SERIGRAFIA' || persType === 'EMBORRACHADO') {
                        size = 'ESCUDO';
                    } else if (persType === 'DTF') {
                        size = 'A5';
                    } else {
                        size = 'A4';
                    }
                }
            }
            
            let apiType = persType;
            if (persType === 'SUB. LOCAL') apiType = 'SUB. LOCAL';
            if (persType === 'SUB. TOTAL') apiType = 'SUB. TOTAL';
            
            try {
                const sizeForApi = persType === 'SUB. TOTAL' ? 'CACHARREL' : size;
                
                // Verificar cache antes de chamar API
                const currentParams = `${apiType}|${sizeForApi}|${quantity}`;
                let unitPrice = 0;
                let priceFound = false;
                
                if (cachedPriceParams === currentParams && cachedBasePrice !== null) {
                    unitPrice = cachedBasePrice;
                    priceFound = true;
                } else {
                    const apiUrl = `/api/personalization-prices/price?type=${apiType}&size=${encodeURIComponent(sizeForApi)}&quantity=${quantity}`;
                    
                    const response = await fetch(apiUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.price !== undefined && data.price !== null) {
                        unitPrice = parseFloat(data.price);
                        
                        if (unitPrice === 0 && apiType === 'SUB. TOTAL') {
                            unitPrice = 2.50; 
                        }
                        
                        cachedBasePrice = unitPrice;
                        cachedPriceParams = currentParams;
                        priceFound = true;
                    }
                }
                
                if (priceFound) {
                    const qty = parseInt(quantity);
                    const currentColorCount = parseInt(document.getElementById('color_count')?.value || 1);
                    
                    if (apiType === 'SUB. TOTAL') {
                        const addonsTotal = calculateAddonsTotal();
                        unitPrice += addonsTotal;
                    }
                    
                    if (apiType === 'SERIGRAFIA' || apiType === 'EMBORRACHADO') {
                        let colorPrice = 0;
                        
                        if (currentColorCount > 1) {
                            try {
                                const colorApiUrl = `/api/personalization-prices/price?type=${apiType}&size=COR&quantity=${qty}`;
                                const colorResponse = await fetch(colorApiUrl, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                                
                                const colorData = await colorResponse.json();
                                if (colorData.success && colorData.price !== undefined) {
                                    colorPrice = parseFloat(colorData.price);
                                }
                                
                                const extraColors = currentColorCount - 1;
                                unitPrice += (colorPrice * extraColors);
                            } catch (error) {
                                console.error('Erro ao buscar preço da cor:', error);
                            }
                        }
                        
                        if (currentColorCount >= 3 && colorPrice > 0) {
                            const applicationsWithDiscount = currentColorCount - 2;
                            const discountPerApplication = colorPrice * 0.5;
                            unitPrice -= (discountPerApplication * applicationsWithDiscount);
                        }
                    }
                        
                    const total = unitPrice * qty;
                    
                    document.getElementById('unitPrice').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
                    document.getElementById('totalPrice').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
                    document.getElementById('unit_price').value = unitPrice;
                    document.getElementById('final_price').value = total;
                    document.getElementById('priceDisplay').classList.remove('hidden');
                } else {
                    showDefaultPrice(quantity, persType);
                }
            } catch (error) {
                console.error('Erro ao calcular preço:', error);
                showDefaultPrice(quantity, persType);
            }
        }

        function showDefaultPrice(quantity, persType) {
            const normalizedType = normalizeTypeKey(persType);
            const defaultPrices = {
                'SERIGRAFIA': 5.00,
                'EMBORRACHADO': 8.00,
                'SUBLIMACAO': 3.50,
                'SUB. TOTAL': 2.50,
                'BORDADO': 12.00,
                'DTF': 4.00
            };
            
            let unitPrice = defaultPrices[normalizedType] || 5.00;

            // Adicionar pre?o dos adicionais se for SUB. TOTAL
            if (normalizedType === 'SUB. TOTAL') {
                const addonsTotal = calculateAddonsTotal();
                unitPrice += addonsTotal;
            }

            const total = unitPrice * quantity;
            
            document.getElementById('unitPrice').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('totalPrice').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('priceFormula').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')} × ${quantity} ${quantity === 1 ? 'peça' : 'peças'} (preço estimado)`;
            document.getElementById('unit_price').value = unitPrice;
            document.getElementById('final_price').value = total;
            document.getElementById('priceDisplay').classList.remove('hidden');
        }

        // Mostrar arquivos selecionados
        function displaySelectedFiles() {
            const fileInput = document.getElementById('art_files');
            const filesList = document.getElementById('selected_files_list');
            
            if (fileInput.files.length > 0) {
                let html = '<div class="text-xs font-medium text-gray-700 dark:text-slate-300 mb-1">Arquivos selecionados:</div>';
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                    html += `
                        <div class="flex items-center justify-between text-xs bg-gray-50 dark:bg-slate-800 px-2 py-1 rounded border border-gray-200 dark:border-slate-700">
                            <span class="truncate flex-1 text-gray-900 dark:text-white">📄 ${file.name}</span>
                            <span class="text-gray-500 dark:text-slate-400 ml-2">${sizeMB} MB</span>
                        </div>
                    `;
                }
                filesList.innerHTML = html;
            } else {
                filesList.innerHTML = '';
            }
        }

        // Adicionar listeners para recalcular preço
        
        let budgetCustomizationInitialized = false;
        function initBudgetCustomizationPage() {
            if (budgetCustomizationInitialized) return;
            budgetCustomizationInitialized = true;

            const sizeEl = document.getElementById('size');
            if (sizeEl) sizeEl.addEventListener('change', calculatePrice);

            const qtyEl = document.getElementById('quantity');
            if (qtyEl) qtyEl.addEventListener('input', calculatePrice);

            const colorCountField = document.getElementById('color_count');
            if (colorCountField) {
                colorCountField.addEventListener('input', calculatePrice);
                colorCountField.addEventListener('change', calculatePrice);
            }

            const artFiles = document.getElementById('art_files');
            if (artFiles) artFiles.addEventListener('change', displaySelectedFiles);

            const form = document.getElementById('personalizationForm');
            if (form && !form.dataset.listenerAttached) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    try {
                        const response = await fetch('{{ route("orders.wizard.customization") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });
                        const data = await response.json();
                        if (response.ok) {
                            closePersonalizationModal();
                            await updatePersonalizationsList();
                            showSuccessMessage('Personalização adicionada com sucesso!');
                        } else {
                            alert(data.message || 'Erro ao adicionar personalização');
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        alert('Erro ao adicionar personalização');
                    }
                });
                form.dataset.listenerAttached = 'true';
            }

            // Re-attach modal close listeners if not already attached
            const modal = document.getElementById('personalizationModal');
            if (modal && !modal.dataset.closeListenerAttached) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closePersonalizationModal();
                    }
                });
                modal.dataset.closeListenerAttached = 'true';
            }

            const deleteModal = document.getElementById('deleteConfirmationModal');
            if (deleteModal && !deleteModal.dataset.closeListenerAttached) {
                deleteModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                         // Check if function exists before calling, as it might be defined later
                         if (typeof closeDeleteConfirmationModal === 'function') {
                            closeDeleteConfirmationModal();
                         }
                    }
                });
                deleteModal.dataset.closeListenerAttached = 'true';
            }

        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBudgetCustomizationPage, { once: true });
        } else {
            initBudgetCustomizationPage();
        }
        document.addEventListener('ajax-content-loaded', initBudgetCustomizationPage);

        // Submit do formulário
                            

        // Removed invalid duplicate code block



        // Removed orphaned event listeners and closing brace


        // Função para atualizar a lista de personalizações dinamicamente
        async function updatePersonalizationsList() {
            // Recarregar a página para atualizar os dados
            window.location.reload();
        }

        function reapplyEventListeners() {
        }

        // Função para mostrar indicador de carregamento
        function showLoadingIndicator() {
            // Remover indicadores anteriores
            const existingIndicators = document.querySelectorAll('.loading-indicator');
            existingIndicators.forEach(indicator => indicator.remove());
            
            // Criar novo indicador
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'loading-indicator fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
            loadingDiv.innerHTML = `
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                    <span>Atualizando...</span>
                </div>
            `;
            
            document.body.appendChild(loadingDiv);
        }

        // Função para esconder indicador de carregamento
        function hideLoadingIndicator() {
            const indicators = document.querySelectorAll('.loading-indicator');
            indicators.forEach(indicator => indicator.remove());
        }

        // Função para mostrar mensagem de sucesso
        function showSuccessMessage(message) {
            // Remover mensagens anteriores
            const existingMessages = document.querySelectorAll('.success-message');
            existingMessages.forEach(msg => msg.remove());
            
            // Criar nova mensagem
            const successDiv = document.createElement('div');
            successDiv.className = 'success-message mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm';
            successDiv.textContent = message;
            
            // Inserir no topo da página
            const content = document.querySelector('.max-w-4xl.mx-auto.px-4.py-6');
            if (content) {
                content.insertBefore(successDiv, content.firstChild);
                
                // Remover após 3 segundos
                setTimeout(() => {
                    successDiv.remove();
                }, 3000);
            }
        }

        // Abrir modal de confirmação de exclusão
        window.removePersonalization = function(id) {
            console.log('🗑️ Solicitando remoção de personalização ID:', id);
            pendingDeleteId = id;
            
            // Buscar informações da personalização para mostrar no modal
            const personalizationCard = document.querySelector(`button[onclick*="deletePersonalization(${id})"]`)?.closest('.border');
            let info = `Personalização ID: ${id}`;
            
            if (personalizationCard) {
                const typeText = personalizationCard.querySelector('.font-medium')?.textContent;
                const priceText = personalizationCard.querySelector('.text-indigo-600')?.textContent;
                if (typeText) info = `<strong>${typeText}</strong>`;
                if (priceText) info += `<br><span class="text-gray-600">${priceText}</span>`;
            }
            
            document.getElementById('delete-item-info').innerHTML = info;
            document.getElementById('deleteConfirmationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        // Fechar modal de confirmação
        window.closeDeleteConfirmationModal = function() {
            console.log('✖️ Cancelando exclusão');
            document.getElementById('deleteConfirmationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            pendingDeleteId = null;
        }
        
        // Confirmar e executar exclusão
        window.confirmDeletePersonalization = async function() {
            if (!pendingDeleteId) {
                console.error('❌ Nenhuma personalização pendente para exclusão');
                return;
            }
            
            
            const id = pendingDeleteId;
            closeDeleteConfirmationModal();
            
            try {
                const response = await fetch(`/api/personalizations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (response.ok && data.success) {
                    // Atualizar interface dinamicamente
                    await updatePersonalizationsList();
                    
                    // Mostrar mensagem de sucesso
                    showSuccessMessage('Personalização removida com sucesso!');
                } else {
                    console.error('Erro ao remover personalização:', data.message);
                    alert('Erro ao remover personalização: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                alert('Erro ao remover personalização: ' + error.message);
            }
        }
        
        // Alias para deletePersonalization (usado nos botões das personalizações órfãs)
        window.deletePersonalization = function(id) {
            console.log('🔄 deletePersonalization alias chamado para ID:', id);
            return removePersonalization(id);
        }
        
        // Função para editar personalização
        window.editPersonalization = async function(id) {
            try {
                console.log('🔧 Editando personalização ID:', id);
                // Buscar dados da personalização
                const response = await fetch(`/api/personalizations/${id}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar personalização');
                }
                
                const data = await response.json();
                const pers = data.personalization;
                
                const persType = pers.application_type.toUpperCase();
                
                // Buscar o ID do tipo de personalização
                const persId = await fetch(`/api/product-options?type=personalizacao&name=${encodeURIComponent(persType)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(r => r.json())
                .then(data => data.id || '')
                .catch(function() { return ''; });
                
                
                // Preencher modal com dados da personalização
                document.getElementById('modal_item_id').value = pers.order_item_id;
                document.getElementById('modal_personalization_type').value = persType;
                document.getElementById('editing_personalization_id').value = pers.id; // ID da personalização existente para edição
                document.getElementById('modal_personalization_id').value = persId; // ID do tipo de personalização
                
                const toggleField = (id, show) => {
                    const el = document.getElementById(id);
                    if (el) {
                        if (show) el.classList.remove('hidden');
                        else el.classList.add('hidden');
                    }
                };

                // Mostrar/ocultar campos baseado no tipo
                toggleField('colorCountField', persType === 'SERIGRAFIA' || persType === 'EMBORRACHADO');
                
                if (persType === 'SUB. TOTAL') {
                    toggleField('locationField', false);
                    toggleField('sizeField', false);
                    toggleField('colorDetailsField', false);
                    toggleField('addonsField', true);
                    setupAddonListeners();
                } else if (persType === 'DTF') {
                    toggleField('locationField', true);
                    toggleField('sizeField', true);
                    toggleField('colorDetailsField', false);
                    toggleField('addonsField', false);
                } else if (persType === 'SUB. LOCAL') {
                    toggleField('locationField', true);
                    toggleField('sizeField', true);
                    toggleField('colorDetailsField', false);
                    toggleField('addonsField', false);
                } else {
                    toggleField('locationField', true);
                    toggleField('sizeField', true);
                    toggleField('colorDetailsField', true);
                    toggleField('addonsField', false);
                }
                
                // IMPORTANTE: Carregar tamanhos ANTES de definir o valor
                loadSizes(persType);
                
                // Aguardar um pouco para garantir que as opções foram carregadas
                await new Promise(resolve => setTimeout(resolve, 100));
                
                
                // Agora sim preencher os campos
                if (pers.location_id && document.getElementById('location')) {
                    document.getElementById('location').value = pers.location_id;
                } else if (pers.location_name && document.getElementById('location')) {
                    // Tentar encontrar pelo nome se não tiver ID
                    const locationSelect = document.getElementById('location');
                    for (let option of locationSelect.options) {
                        if (option.textContent === pers.location_name) {
                            locationSelect.value = option.value;
                            break;
                        }
                    }
                }
                
                if (pers.size_name && document.getElementById('size')) {
                    document.getElementById('size').value = pers.size_name;
                    
                    // Verificar se o valor foi realmente aplicado
                    const actualValue = document.getElementById('size').value;
                    if (actualValue !== pers.size_name) {
                        console.error('Tamanho não foi aplicado. Valor esperado:', pers.size_name, 'Valor atual:', actualValue);
                    }
                }
                if (pers.quantity && document.getElementById('quantity')) {
                    document.getElementById('quantity').value = pers.quantity;
                }
                if (pers.color_count && document.getElementById('color_count')) {
                    document.getElementById('color_count').value = pers.color_count;
                }
                if (pers.color_details && document.getElementById('color_details')) {
                    document.getElementById('color_details').value = pers.color_details;
                }
                if (pers.seller_notes && document.getElementById('seller_notes')) {
                    document.getElementById('seller_notes').value = pers.seller_notes;
                }
                
                // Atualizar título do modal
                document.getElementById('modalTitle').textContent = `Editar ${persType}`;
                
                // Abrir modal
                document.getElementById('personalizationModal').classList.remove('hidden');
                
            } catch (error) {
                console.error('Erro ao carregar personalização:', error);
                alert('Erro ao carregar personalização: ' + error.message);
            }
        }
        console.log("✅ Script de personalização inicializado com sucesso!");
</script>
@endpush
@endsection
