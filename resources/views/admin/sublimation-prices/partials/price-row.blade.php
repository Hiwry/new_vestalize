<div class="price-row bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg p-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">De (quantidade) *</label>
            <input type="number" 
                   name="prices[{{ $index }}][quantity_from]" 
                   value="{{ $price ? $price->quantity_from : '' }}"
                   min="1" 
                   required
                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all"
                   placeholder="Ex: 1">
        </div>

        <div>
            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Até (quantidade)</label>
            <input type="number" 
                   name="prices[{{ $index }}][quantity_to]" 
                   value="{{ $price ? $price->quantity_to : '' }}"
                   min="1"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all"
                   placeholder="Vazio = ∞">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deixe vazio para "infinito"</p>
        </div>

        <div>
            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Preço (R$) *</label>
            <input type="number" 
                   name="prices[{{ $index }}][price]" 
                   value="{{ $price ? $price->price : '' }}"
                   step="0.01" 
                   min="0" 
                   required
                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all"
                   placeholder="Ex: 15.00">
        </div>

        <div class="flex items-end">
            <button type="button" 
                    onclick="removePriceRow(this)"
                    class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-600 dark:bg-red-600 text-white text-sm rounded-md hover:bg-red-700 dark:hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Remover
            </button>
        </div>
    </div>

    @if($price)
        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-slate-700/50 rounded-lg p-2 border border-gray-200 dark:border-slate-600">
            <span class="font-medium">Faixa atual:</span>
            @if($price->quantity_to)
                {{ $price->quantity_from }} a {{ $price->quantity_to }} peças = R$ {{ number_format($price->price, 2, ',', '.') }}
            @else
                {{ $price->quantity_from }}+ peças = R$ {{ number_format($price->price, 2, ',', '.') }}
            @endif
        </div>
    @endif
</div>
