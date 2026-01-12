<div class="price-row slide-in bg-gradient-to-r from-gray-50 to-blue-50 border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-start">
        <!-- Quantidade De -->
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                <i class="fas fa-play text-indigo-500 text-xs mr-1"></i>
                De (quantidade) *
            </label>
            <div class="relative">
                <input type="number" 
                       name="prices[{{ $index }}][quantity_from]" 
                       value="{{ $price ? $price->quantity_from : '' }}"
                       min="1" 
                       required
                       onchange="validatePriceRow(this)"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                       placeholder="Ex: 1">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-hashtag text-gray-400 text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Quantidade Até -->
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                <i class="fas fa-stop text-indigo-500 text-xs mr-1"></i>
                Até (quantidade)
            </label>
            <div class="relative">
                <input type="number" 
                       name="prices[{{ $index }}][quantity_to]" 
                       value="{{ $price ? $price->quantity_to : '' }}"
                       min="1"
                       onchange="validatePriceRow(this)"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                       placeholder="Ex: 10">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-infinity text-gray-400 text-sm"></i>
                </div>
            </div>
            <div class="text-xs text-gray-500 flex items-center">
                <i class="fas fa-info-circle mr-1"></i>
                <span>Deixe vazio para "infinito"</span>
            </div>
        </div>

        <!-- Preço -->
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                <i class="fas fa-dollar-sign text-green-500 text-xs mr-1"></i>
                Preço (R$) *
            </label>
            <div class="relative">
                <input type="number" 
                       name="prices[{{ $index }}][price]" 
                       value="{{ $price ? $price->price : '' }}"
                       step="0.01" 
                       min="0" 
                       required
                       onchange="validatePriceRow(this)"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                       placeholder="Ex: 15.00">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-400 text-sm font-medium">R$</span>
                </div>
            </div>
        </div>

        <!-- Resumo da faixa -->
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                <i class="fas fa-eye text-blue-500 text-xs mr-1"></i>
                Resumo
            </label>
            <div class="bg-white border border-gray-200 rounded-lg p-3 text-sm">
                <div id="range-summary-{{ $index }}" class="text-gray-600">
                    @if($price)
                        @if($price->quantity_to)
                            {{ $price->quantity_from }} a {{ $price->quantity_to }} peças
                        @else
                            {{ $price->quantity_from }}+ peças
                        @endif
                    @else
                        Configure os valores
                    @endif
                </div>
                <div id="price-summary-{{ $index }}" class="font-semibold text-green-600">
                    @if($price)
                        R$ {{ number_format($price->price, 2, ',', '.') }}
                    @else
                        R$ 0,00
                    @endif
                </div>
            </div>
        </div>

        <!-- Botão de remover -->
        <div class="flex flex-col justify-end h-full">
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 opacity-0">
                    Ação
                </label>
                <button type="button" 
                        onclick="removePriceRow(this)"
                        class="group w-full px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <i class="fas fa-trash mr-2 group-hover:rotate-12 transition-transform"></i>
                    Remover
                </button>
            </div>
        </div>
    </div>

    <!-- Campos ocultos para tamanho -->
    <input type="hidden" name="prices[{{ $index }}][size_name]" value="{{ $sizeName }}">
    <input type="hidden" name="prices[{{ $index }}][size_dimensions]" value="{{ $price ? $price->size_dimensions : '' }}">

    <!-- Validação visual -->
    <div id="validation-{{ $index }}" class="mt-3 hidden">
        <div class="flex items-center text-sm">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
            <span class="text-yellow-700">Verificando sobreposições...</span>
        </div>
    </div>
</div>
