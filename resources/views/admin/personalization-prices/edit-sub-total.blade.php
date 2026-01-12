@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
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
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ $types[$type] }}</span>
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

    @if(session('error'))
    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Preços Base (CACHARREL/PP) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Preços Base</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">CACHARREL/PP - Faixas por quantidade</p>
                    </div>
                    <button type="button" onclick="addBasePriceRow()" 
                            class="inline-flex items-center px-3 py-2 bg-blue-600 dark:bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Faixa
                    </button>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.personalization-prices.update', $type) }}" id="base-prices-form">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">DE</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">ATÉ</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">PREÇO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody id="base-prices-tbody">
                                @if(isset($prices['CACHARREL']) && $prices['CACHARREL']->count() > 0)
                                    @foreach($prices['CACHARREL'] as $index => $price)
                                    <tr class="price-row border-b border-gray-100 dark:border-gray-700">
                                        <td class="px-4 py-3">
                                            <input type="number" name="base_prices[{{ $index }}][quantity_from]" 
                                                   value="{{ $price->quantity_from }}" min="1"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="base_prices[{{ $index }}][quantity_to]" 
                                                   value="{{ $price->quantity_to }}" min="1"
                                                   placeholder="∞"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                                                <input type="number" name="base_prices[{{ $index }}][price]" 
                                                       value="{{ $price->price }}" step="0.01" min="0"
                                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button" onclick="removeBasePriceRow(this)" 
                                                    class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr id="empty-base-message">
                                        <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="font-medium">Nenhuma faixa de preço base configurada</p>
                                            <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-blue-600 dark:bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Preços Base
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Adicionais -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Adicionais</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Preços dos adicionais de sublimação</p>
                    </div>
                    <button type="button" onclick="addAddonRow()" 
                            class="inline-flex items-center px-3 py-2 bg-purple-600 dark:bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 dark:hover:bg-purple-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Adicional
                    </button>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.addons.update') }}" id="addons-form">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">NOME</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">DESCRIÇÃO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">PREÇO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody id="addons-tbody">
                                @if($addons->count() > 0)
                                    @foreach($addons as $index => $addon)
                                    <tr class="addon-row border-b border-gray-100 dark:border-gray-700">
                                        <td class="px-4 py-3">
                                            <input type="text" name="addons[{{ $index }}][name]" 
                                                   value="{{ $addon->name }}"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all">
                                            <input type="hidden" name="addons[{{ $index }}][id]" value="{{ $addon->id }}">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="addons[{{ $index }}][description]" 
                                                   value="{{ $addon->description }}"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                                                <input type="number" name="addons[{{ $index }}][price_adjustment]" 
                                                       value="{{ $addon->price_adjustment }}" step="0.01"
                                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all">
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button" onclick="removeAddonRow(this)" 
                                                    class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr id="empty-addons-message">
                                        <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <p class="font-medium">Nenhum adicional configurado</p>
                                            <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-purple-600 dark:bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 dark:hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Adicionais
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Calculadora -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Calculadora de Preços</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Teste os preços configurados</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Quantidade -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Quantidade</label>
                    <input type="number" id="calc-quantity" min="1" value="10" 
                           onchange="updateCalculator()"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>
                
                <!-- Adicionais Selecionados -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Adicionais</label>
                    <select id="calc-addons" multiple 
                            onchange="updateCalculator()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                        <!-- Será preenchido via JavaScript -->
                    </select>
                </div>
                
                <!-- Resultado -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Resultado</label>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Preço base:</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="calc-base-price">R$ 0,00</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Adicionais:</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" id="calc-addons-price">R$ 0,00</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total:</div>
                        <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400" id="calc-total-price">R$ 0,00</div>
                    </div>
                </div>
                   <!-- Botão Voltar -->
                   <div class="mb-4">
                       <a href="{{ route('admin.personalization-prices.index') }}" 
                          class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                           <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                           </svg>
                           Voltar
                       </a>
                   </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let basePriceRowIndex = {{ isset($prices['CACHARREL']) ? $prices['CACHARREL']->count() : 0 }};
    let addonRowIndex = {{ $addons->count() }};
    
    // Adicionais disponíveis para a calculadora
    const availableAddons = @json($addons->toArray());

    document.addEventListener('DOMContentLoaded', function() {
        loadCalculatorAddons();
        updateCalculator();
    });

    // Funções para preços base
    function addBasePriceRow() {
        const tbody = document.getElementById('base-prices-tbody');
        const emptyMessage = document.getElementById('empty-base-message');
        
        if (emptyMessage) {
            emptyMessage.remove();
        }
        
        const row = document.createElement('tr');
        row.className = 'price-row border-b border-gray-100 dark:border-gray-700';
        row.innerHTML = `
            <td class="px-4 py-3">
                <input type="number" name="base_prices[${basePriceRowIndex}][quantity_from]" 
                       value="1" min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="base_prices[${basePriceRowIndex}][quantity_to]" 
                       value="" min="1" placeholder="∞"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
            </td>
            <td class="px-4 py-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                    <input type="number" name="base_prices[${basePriceRowIndex}][price]" 
                           value="0" step="0.01" min="0"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
                </div>
            </td>
            <td class="px-4 py-3">
                <button type="button" onclick="removeBasePriceRow(this)" 
                        class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        basePriceRowIndex++;
    }

    function removeBasePriceRow(button) {
        const row = button.closest('tr');
        row.remove();
        
        // Se não há mais linhas, mostrar mensagem vazia
        const tbody = document.getElementById('base-prices-tbody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-base-message">
                    <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="font-medium">Nenhuma faixa de preço base configurada</p>
                        <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                    </td>
                </tr>
            `;
        }
    }

    // Funções para adicionais
    function addAddonRow() {
        const tbody = document.getElementById('addons-tbody');
        const emptyMessage = document.getElementById('empty-addons-message');
        
        if (emptyMessage) {
            emptyMessage.remove();
        }
        
        const row = document.createElement('tr');
        row.className = 'addon-row border-b border-gray-100 dark:border-gray-700';
        row.innerHTML = `
            <td class="px-4 py-3">
                <input type="text" name="addons[${addonRowIndex}][name]" 
                       value=""
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all">
                <input type="hidden" name="addons[${addonRowIndex}][id]" value="">
            </td>
            <td class="px-4 py-3">
                <input type="text" name="addons[${addonRowIndex}][description]" 
                       value=""
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all">
            </td>
            <td class="px-4 py-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">R$</span>
                    <input type="number" name="addons[${addonRowIndex}][price_adjustment]" 
                           value="0" step="0.01"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-all">
                </div>
            </td>
            <td class="px-4 py-3">
                <button type="button" onclick="removeAddonRow(this)" 
                        class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        addonRowIndex++;
    }

    function removeAddonRow(button) {
        const row = button.closest('tr');
        row.remove();
        
        // Se não há mais linhas, mostrar mensagem vazia
        const tbody = document.getElementById('addons-tbody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-addons-message">
                    <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="font-medium">Nenhum adicional configurado</p>
                        <p class="text-sm mt-1">Clique no botão acima para adicionar</p>
                    </td>
                </tr>
            `;
        }
    }

    // Calculadora
    function loadCalculatorAddons() {
        const select = document.getElementById('calc-addons');
        select.innerHTML = '';
        
        availableAddons.forEach(addon => {
            const option = document.createElement('option');
            option.value = addon.id;
            const sign = parseFloat(addon.price_adjustment) >= 0 ? '+' : '';
            option.textContent = `${addon.name} ${sign}R$ ${Math.abs(parseFloat(addon.price_adjustment)).toFixed(2).replace('.', ',')}`;
            select.appendChild(option);
        });
    }

    async function updateCalculator() {
        const quantity = parseInt(document.getElementById('calc-quantity').value) || 1;
        const selectedAddonIds = Array.from(document.getElementById('calc-addons').selectedOptions).map(option => option.value);
        
        // Buscar preço base da API
        let basePrice = 0;
        try {
            const response = await fetch(`/api/personalization-prices/price?type=SUB. TOTAL&size=CACHARREL&quantity=${quantity}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            if (data.success && data.price) {
                basePrice = parseFloat(data.price);
            } else {
                console.warn('Preço não encontrado na API, usando preço padrão');
                basePrice = 36.00; // Preço padrão como fallback
            }
        } catch (error) {
            console.error('Erro ao buscar preço:', error);
            basePrice = 36.00; // Preço padrão como fallback
        }
        
        // Calcular total dos adicionais
        let addonsTotal = 0;
        selectedAddonIds.forEach(addonId => {
            const addon = availableAddons.find(a => a.id == addonId);
            if (addon) {
                addonsTotal += parseFloat(addon.price_adjustment);
            }
        });
        
        const totalPrice = basePrice + addonsTotal;
        
        // Atualizar display
        document.getElementById('calc-base-price').textContent = `R$ ${basePrice.toFixed(2).replace('.', ',')}`;
        document.getElementById('calc-addons-price').textContent = `R$ ${addonsTotal.toFixed(2).replace('.', ',')}`;
        document.getElementById('calc-total-price').textContent = `R$ ${totalPrice.toFixed(2).replace('.', ',')}`;
    }
</script>
@endpush
@endsection
