@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-md">
            {{ session('error') }}
        </div>
        @endif

        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Orçamento #{{ $budget->budget_number }}
                    @if($budget->is_quick)
                    <span class="ml-2 px-2 py-1 text-sm bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-lg">⚡ Rápido</span>
                    @endif
                    @if($budget->order_number)
                    <span class="text-lg text-indigo-600 dark:text-indigo-400">→ Pedido #{{ $budget->order_number }}</span>
                    @endif
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Criado em {{ $budget->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-sm rounded-full
                    @if($budget->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                    @elseif($budget->status === 'approved') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                    @elseif($budget->status === 'rejected') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                    @endif">
                    @if($budget->status === 'pending') Pendente
                    @elseif($budget->status === 'approved') Aprovado
                    @elseif($budget->status === 'rejected') Rejeitado
                    @endif
                </span>
                
                <!-- Dropdown de PDF -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 text-sm font-medium inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Baixar PDF
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        
                        <div class="p-2">
                            <p class="px-3 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Escolha o formato
                            </p>
                            
                            <!-- PDF Detalhado -->
                            <a href="{{ route('budget.pdf', $budget->id) }}" 
                               class="flex items-start gap-3 px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-md flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                        PDF Detalhado
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Valores separados (camisa + arte)
                                    </p>
                                </div>
                            </a>
                            
                            <!-- PDF Valor Único -->
                            <a href="{{ route('budget.pdf', ['id' => $budget->id, 'modo' => 'unificado']) }}" 
                               class="flex items-start gap-3 px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-md flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                        PDF Valor Único
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Valor por peça já com arte inclusa
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Cliente/Contato -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        @if($budget->is_quick) Contato @else Cliente @endif
                    </h2>
                    <div class="space-y-2 text-sm">
                        @if($budget->is_quick)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Nome:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->contact_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">WhatsApp:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->contact_phone }}</span>
                        </div>
                        @else
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Nome:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->client->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Telefone:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->client->phone_primary ?? 'N/A' }}</span>
                        </div>
                        @if($budget->client && $budget->client->email)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Email:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->client->email }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>

                @if($budget->is_quick)
                <!-- Detalhes do Orçamento Rápido -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg shadow-sm border border-emerald-200 dark:border-emerald-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <span class="text-xl mr-2">⚡</span> Detalhes do Orçamento Rápido
                    </h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3">
                            <span class="text-gray-600 dark:text-gray-400 text-xs">Personalização</span>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $budget->technique }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3">
                            <span class="text-gray-600 dark:text-gray-400 text-xs">Quantidade</span>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $budget->quantity }} peças</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3">
                            <span class="text-gray-600 dark:text-gray-400 text-xs">Valor Unitário</span>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">R$ {{ number_format($budget->unit_price, 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3">
                            <span class="text-gray-600 dark:text-gray-400 text-xs">Prazo</span>
                            <p class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ $budget->deadline_days }} dias</p>
                        </div>
                    </div>
                    @if($budget->product_internal)
                    <div class="mt-4 pt-4 border-t border-emerald-200 dark:border-emerald-700">
                        <span class="text-gray-600 dark:text-gray-400 text-xs">Produto (interno)</span>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->product_internal }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Personalizações -->
                @php
                    $allCustomizations = $budget->items->flatMap(function($item) {
                        return $item->customizations;
                    });
                @endphp
                @if($allCustomizations->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Personalizações</h2>
                    <div class="space-y-2">
                        @foreach($allCustomizations as $index => $custom)
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-md p-3 border border-indigo-100 dark:border-indigo-800">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 space-y-3">
                                    <div class="grid grid-cols-5 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400 text-xs">Tipo:</span>
                                            <p class="font-medium text-indigo-700 dark:text-indigo-300">{{ $custom->personalization_type ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400 text-xs">Localização:</span>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $custom->location }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400 text-xs">Tamanho:</span>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $custom->size }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400 text-xs">Quantidade:</span>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $custom->quantity ?? 0 }} peças</p>
                                        </div>
                                        @if(in_array($custom->personalization_type, ['SERIGRAFIA', 'EMBORRACHADO']))
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400 text-xs">Cores:</span>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $custom->color_count ?? 1 }} {{ ($custom->color_count ?? 1) > 1 ? 'aplicações' : 'aplicação' }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Breakdown de valores -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-2 text-xs space-y-1">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Valor unitário:</span>
                                            <span class="text-gray-900 dark:text-gray-100 font-medium">R$ {{ number_format($custom->unit_price, 2, ',', '.') }}</span>
                                        </div>
                                        @if(in_array($custom->personalization_type, ['SERIGRAFIA', 'EMBORRACHADO']) && ($custom->color_count ?? 1) > 1)
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Com {{ $custom->color_count }} cores{{ ($custom->color_count ?? 1) >= 3 ? ' (desconto na 3ª+)' : '' }}</span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between pt-1 border-t border-gray-200 dark:border-gray-600">
                                            <span class="text-gray-700 dark:text-gray-300 font-medium">Total:</span>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-bold">R$ {{ number_format($custom->total_price, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="text-gray-500 dark:text-gray-400 italic">
                                            (R$ {{ number_format($custom->unit_price, 2, ',', '.') }} × {{ $custom->quantity ?? 0 }} peças)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!$budget->is_quick)
                <!-- Itens -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Itens do Orçamento</h2>
                    <div class="space-y-4">
                        @foreach($budget->items as $item)
                        @php
                            $personalizationTypes = json_decode($item->personalization_types, true) ?? [];
                            $itemQuantity = $item->quantity;
                            $itemUnitPrice = $personalizationTypes['unit_price'] ?? 0;
                            $itemTotal = $item->item_total;
                            
                            // Calcular total de personalizações deste item
                            $itemPersonalizationsTotal = $item->customizations->sum('total_price');
                            
                            // Calcular valor unitário com personalização
                            $personalizationPerPiece = $itemQuantity > 0 ? ($itemPersonalizationsTotal / $itemQuantity) : 0;
                            $unitPriceWithPersonalization = $itemUnitPrice + $personalizationPerPiece;
                            
                            // Total geral do item
                            $itemGrandTotal = $itemTotal + $itemPersonalizationsTotal;
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-md p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ $item->item_number }}</span>
                                    </div>
                                    <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $personalizationTypes['print_type'] ?? 'Item ' . $item->item_number }}</h3>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Tecido:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $item->fabric }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Cor:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $item->color }}</span>
                                </div>
                                @if(!empty($personalizationTypes['model']))
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Modelo:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $personalizationTypes['model'] }}</span>
                                </div>
                                @endif
                                @if(!empty($personalizationTypes['collar']))
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Gola:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $personalizationTypes['collar'] }}</span>
                                </div>
                                @endif
                                @if(!empty($personalizationTypes['detail']))
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Detalhe:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $personalizationTypes['detail'] }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Quantidade:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $itemQuantity }} peças</span>
                                </div>
                            </div>

                            <!-- Breakdown de Valores -->
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Costura (unitário):</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($itemUnitPrice, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Costura (total):</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($itemTotal, 2, ',', '.') }}</span>
                                    </div>
                                    
                                    @if($itemPersonalizationsTotal > 0)
                                    <div class="flex justify-between text-indigo-600 dark:text-indigo-400">
                                        <span>Personalização (unitário):</span>
                                        <span class="font-medium">R$ {{ number_format($personalizationPerPiece, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-indigo-600 dark:text-indigo-400">
                                        <span>Personalização (total):</span>
                                        <span class="font-medium">R$ {{ number_format($itemPersonalizationsTotal, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">Unitário Total:</span>
                                        <span class="font-bold text-green-600 dark:text-green-400">R$ {{ number_format($unitPriceWithPersonalization, 2, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-500">
                                        <span class="font-bold text-gray-900 dark:text-gray-100">TOTAL DO ITEM:</span>
                                        <span class="font-bold text-lg text-indigo-600 dark:text-indigo-400">R$ {{ number_format($itemGrandTotal, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if(!empty($personalizationTypes['notes']))
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Observações:</span>
                                <p class="text-xs text-gray-900 dark:text-gray-100 mt-1">{{ $personalizationTypes['notes'] }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($budget->observations)
                <!-- Observações -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Observações Gerais</h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $budget->observations }}</p>
                </div>
                @endif

                @if($budget->admin_notes)
                <!-- Observações do Vendedor -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow-sm border border-yellow-200 dark:border-yellow-800 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="text-xl mr-2">📌</span>
                        Observações do Vendedor
                    </h2>
                    <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $budget->admin_notes }}</div>
                    <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-3 italic">
                        Acréscimo GG/EXG, Prazo, Pagamento, etc.
                    </p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Resumo Financeiro -->
                @php
                    $itemsSubtotal = $budget->items->sum('item_total');
                    $customizationsSubtotal = $budget->items->flatMap(fn($item) => $item->customizations)->sum('total_price');
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Resumo Financeiro</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal Itens:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($itemsSubtotal, 2, ',', '.') }}</span>
                        </div>
                        @if($customizationsSubtotal > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Personalizações:</span>
                            <span class="font-medium text-indigo-600 dark:text-indigo-400">R$ {{ number_format($customizationsSubtotal, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($budget->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Desconto:</span>
                            <span class="font-medium text-red-600 dark:text-red-400">
                                - R$ {{ number_format($budget->discount, 2, ',', '.') }}
                            </span>
                        </div>
                        @endif
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-gray-900 dark:text-gray-100">Valor Total:</span>
                                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                    R$ {{ number_format($budget->total, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center bg-gray-100 dark:bg-gray-700 rounded py-2">
                            Válido até {{ \Carbon\Carbon::parse($budget->valid_until)->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <!-- Informações -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Criado por:</span>
                            <p class="font-medium text-gray-900 dark:text-gray-100 mt-1">{{ $budget->user->name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Data de criação:</span>
                            <p class="font-medium text-gray-900 dark:text-gray-100 mt-1">{{ $budget->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Última atualização:</span>
                            <p class="font-medium text-gray-900 dark:text-gray-100 mt-1">{{ $budget->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações</h2>
                    <div class="space-y-3">
                        @if($budget->status === 'pending')
                        <form action="{{ route('budget.approve', $budget->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-green-600 dark:bg-green-600 text-white text-center rounded-md hover:bg-green-700 dark:hover:bg-green-700 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aprovar Orçamento
                            </button>
                        </form>
                        
                        <form action="{{ route('budget.reject', $budget->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Tem certeza que deseja rejeitar este orçamento?')"
                                    class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-red-600 dark:bg-red-600 text-white text-center rounded-md hover:bg-red-700 dark:hover:bg-red-700 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Rejeitar Orçamento
                            </button>
                        </form>
                        @endif
                        
                        @if($budget->status === 'approved')
                        <a href="{{ route('budget.convert-form', $budget->id) }}"
                           class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-blue-600 dark:bg-blue-600 text-white text-center rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Converter em Pedido
                        </a>
                        @endif
                        
                        <!-- WhatsApp Button -->
                        <div class="flex gap-2">
                            <a href="{{ route('budget.whatsapp', $budget->id) }}" target="_blank"
                               class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-green-500 dark:bg-green-600 text-white text-center rounded-md hover:bg-green-600 dark:hover:bg-green-700 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Enviar WhatsApp
                            </a>

                            <button type="button" onclick="copyBudgetInfo(this)"
                                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                    title="Copiar informações">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>

@push('scripts')
<script>
async function copyBudgetInfo(btn) {
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';

    try {
        const response = await fetch('{{ route("budget.whatsapp", $budget->id) }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        if (data.success && data.message) {
            await navigator.clipboard.writeText(data.message);
            
            // Show success state
            btn.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }, 2000);
        } else {
            throw new Error('Falha ao obter dados');
        }
    } catch (err) {
        console.error('Erro ao copiar:', err);
        btn.innerHTML = '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }, 2000);
    }
}
</script>
@endpush
                        
                        @if(!Auth::user()->isAdmin())
                        <form action="{{ route('budget.request-edit', $budget->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-orange-500 dark:bg-orange-500 text-white text-center rounded-md hover:bg-orange-600 dark:hover:bg-orange-600 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Solicitar Edição
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('budget.index') }}" 
                           class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

