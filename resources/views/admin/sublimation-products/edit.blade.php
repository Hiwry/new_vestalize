@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.sublimation-products.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                    @if($product->type == 'camisa')
                        <span class="text-xl">👕</span>
                    @elseif($product->type == 'conjunto')
                        <span class="text-xl">🏃</span>
                    @elseif($product->type == 'bandeira')
                        <span class="text-xl">🚩</span>
                    @else
                        <span class="text-xl">📦</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $types[$product->type] ?? 'Produto Personalizado' }}</p>
                </div>
            </div>
            <span class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $product->active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                {{ $product->active ? 'Ativo' : 'Inativo' }}
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
        <ul class="text-sm text-red-800 dark:text-red-200 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Coluna Esquerda: Info + Tabela de Preços -->
        <div class="space-y-6">
            <!-- Info Básica -->
            <form method="POST" action="{{ route('admin.sublimation-products.update', $product) }}">
                @csrf
                @method('PUT')
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-purple-50 dark:bg-purple-900/20">
                        <h2 class="text-base font-semibold text-purple-900 dark:text-purple-100">📋 Informações do Produto</h2>
                    </div>
                    
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nome *</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo *</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500">
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ $product->type == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="fabric_always_white" value="1" {{ $product->fabric_always_white ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">⚪ Tecido branco</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="active" value="1" {{ $product->active ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">✅ Ativo</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors">
                            Salvar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabela de Preços por Quantidade (estilo planilha) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                    <h2 class="text-base font-semibold text-blue-900 dark:text-blue-100">💰 Tabela de Preços</h2>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">Defina o preço por faixa de quantidade</p>
                </div>
                
                <div class="p-5">
                    <!-- Tabela existente -->
                    @if($prices->count() > 0)
                    <div class="overflow-x-auto mb-4">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="text-left py-2 px-3 font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">QUANTIDADE</th>
                                    <th class="text-center py-2 px-3 font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">PREÇO</th>
                                    <th class="text-center py-2 px-3 font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600" width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prices as $price)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-2 px-3 border border-gray-200 dark:border-gray-600 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $price->quantity_from }} - {{ $price->quantity_to ?? '∞' }}
                                    </td>
                                    <td class="py-2 px-3 border border-gray-200 dark:border-gray-600 text-center font-bold text-green-600 dark:text-green-400">
                                        R$ {{ number_format($price->price, 2, ',', '.') }}
                                    </td>
                                    <td class="py-2 px-3 border border-gray-200 dark:border-gray-600 text-center">
                                        <form method="POST" action="{{ route('admin.sublimation-products.prices.destroy', [$product, $price]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1" onclick="return confirm('Remover?')">✕</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-6 text-gray-500 dark:text-gray-400 text-sm">
                        Nenhuma faixa de preço cadastrada
                    </div>
                    @endif
                    
                    <!-- Adicionar nova faixa -->
                    <form method="POST" action="{{ route('admin.sublimation-products.prices.store', $product) }}" class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                        @csrf
                        <div class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">➕ Adicionar faixa:</div>
                        <div class="flex items-center gap-2">
                            <input type="number" name="quantity_from" required min="1" placeholder="De"
                                   class="w-20 px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="quantity_to" min="1" placeholder="Até"
                                   class="w-20 px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                            <span class="text-gray-400">=</span>
                            <div class="flex items-center">
                                <span class="text-gray-400 mr-1">R$</span>
                                <input type="number" name="price" required step="0.01" min="0" placeholder="0,00"
                                       class="w-24 px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                            </div>
                            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                                +
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Adicionais (estilo tabela simples) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/20">
                <h2 class="text-base font-semibold text-yellow-900 dark:text-yellow-100">🏷️ ADICIONAIS</h2>
                <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">Itens extras com valor fixo (+ ou -)</p>
            </div>
            
            <div class="p-5">
                <!-- Tabela de adicionais -->
                @if($addons->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="text-left py-2 px-3 font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">ITEM</th>
                                <th class="text-right py-2 px-3 font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">VALOR</th>
                                <th class="text-center py-2 px-3 border border-gray-200 dark:border-gray-600" width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($addons as $addon)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-2 px-3 border border-gray-200 dark:border-gray-600 font-medium text-gray-900 dark:text-gray-100">
                                    {{ strtoupper($addon->name) }}
                                </td>
                                <td class="py-2 px-3 border border-gray-200 dark:border-gray-600 text-right font-bold {{ $addon->price >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $addon->price >= 0 ? '+' : '' }}R$ {{ number_format($addon->price, 2, ',', '.') }}
                                </td>
                                <td class="py-2 px-3 border border-gray-200 dark:border-gray-600 text-center">
                                    <form method="POST" action="{{ route('admin.sublimation-products.addons.destroy', [$product, $addon]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1" onclick="return confirm('Remover?')">✕</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400 text-sm mb-4">
                    Nenhum adicional cadastrado
                </div>
                @endif
                
                <!-- Adicionar novo adicional -->
                <form method="POST" action="{{ route('admin.sublimation-products.addons.store', $product) }}" class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                    @csrf
                    <div class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">➕ Adicionar item:</div>
                    <div class="flex items-center gap-2">
                        <input type="text" name="name" required placeholder="Nome (ex: GOLA V, M. LONGA)"
                               class="flex-1 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 uppercase">
                        <div class="flex items-center">
                            <span class="text-gray-400 mr-1">R$</span>
                            <input type="number" name="price" required step="0.01" placeholder="0,00"
                                   class="w-24 px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right">
                        </div>
                        <button type="submit" class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded transition-colors">
                            +
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">💡 Use valores negativos para descontos (ex: -3.00 para REGATA)</p>
                </form>

                <!-- Sugestões rápidas -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">⚡ Adicionais comuns:</div>
                    <div class="flex flex-wrap gap-1">
                        @php
                            $suggestions = [
                                'REGATA' => -3.00,
                                'M. LONGA' => 20.00,
                                'POLO' => 20.00,
                                'GOLA V' => 3.15,
                                'DRYFIT' => 12.00,
                                'GOLEIRO' => 20.00,
                                'NOME/NÚMERO' => 8.00,
                            ];
                        @endphp
                        @foreach($suggestions as $name => $price)
                        <form method="POST" action="{{ route('admin.sublimation-products.addons.store', $product) }}" class="inline">
                            @csrf
                            <input type="hidden" name="name" value="{{ $name }}">
                            <input type="hidden" name="price" value="{{ $price }}">
                            <button type="submit" class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-purple-100 dark:hover:bg-purple-900/30 text-gray-700 dark:text-gray-300 rounded transition-colors">
                                {{ $name }} {{ $price >= 0 ? '+' : '' }}{{ number_format($price, 2, ',', '.') }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
