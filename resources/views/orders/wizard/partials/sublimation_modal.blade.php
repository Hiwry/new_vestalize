<!-- Modal SUB. TOTAL -->
<div id="sublimation-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-2xl w-full border border-gray-200 dark:border-slate-700 my-8 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Adicionar Item SUB. TOTAL</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Configure todos os detalhes da sublimação total</p>
                    </div>
                </div>
                <button type="button" onclick="closeSublimationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <form id="sublimation-form" method="POST" action="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="action" value="add_sublimation_item">
            
            <div class="p-6 space-y-5">
                <!-- Tipo de Produto -->
                <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tipo de Produto *</label>
                    <select name="sublimation_type" id="sublimation_type" required onchange="loadSublimationAddons()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-all text-sm">
                        <option value="">Selecione o tipo</option>
                        @if(isset($sublimationTypes))
                        @foreach($sublimationTypes as $type)
                        <option value="{{ $type->slug }}">{{ $type->name }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>

                <!-- Adicionais -->
                <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Adicionais</label>
                    <div id="sublimation-addons-container" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>
                    </div>
                </div>

                <!-- Nome da Arte -->
                <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Nome da Arte *</label>
                    <input type="text" name="art_name" id="sub_art_name" required placeholder="Ex: Logo Empresa ABC" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 transition-all text-sm">
                </div>

                <!-- Tamanhos -->
                <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tamanhos e Quantidades *</label>
                    <div class="grid grid-cols-5 gap-2 mb-2">
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">PP</label>
                            <input type="number" name="tamanhos[PP]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">P</label>
                            <input type="number" name="tamanhos[P]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">M</label>
                            <input type="number" name="tamanhos[M]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G</label>
                            <input type="number" name="tamanhos[G]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">GG</label>
                            <input type="number" name="tamanhos[GG]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div class="grid grid-cols-5 gap-2">
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">EXG</label>
                            <input type="number" name="tamanhos[EXG]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G1</label>
                            <input type="number" name="tamanhos[G1]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G2</label>
                            <input type="number" name="tamanhos[G2]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G3</label>
                            <input type="number" name="tamanhos[G3]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">Esp.</label>
                            <input type="number" name="tamanhos[Especial]" min="0" value="0" onchange="calculateSublimationTotal()" class="sub-size-input w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <input type="hidden" name="quantity" id="sub_quantity" value="0">
                    <div class="mt-3 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Total de peças:</span>
                            <span class="text-xl font-bold text-purple-600 dark:text-purple-400" id="sub-total-pecas">0</span>
                        </div>
                    </div>
                </div>

                <!-- Arquivos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Arquivo Corel -->
                    <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Arquivo Corel</label>
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-xs text-gray-500 mt-1">.CDR, .AI, .PDF</span>
                            <input type="file" name="corel_file" class="hidden" accept=".cdr,.ai,.pdf,.eps">
                        </label>
                    </div>
                    
                    <!-- Imagem de Capa -->
                    <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Imagem de Capa</label>
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP</span>
                            <input type="file" name="item_cover_image" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>

                <!-- Preços -->
                <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Preços</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Preço Unitário (R$) *</label>
                            <input type="number" name="unit_price" id="sub_unit_price" step="0.01" min="0" value="0" required onchange="updateSublimationPreview()" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-right font-bold text-green-600 dark:text-green-400">
                        </div>
                        <div class="hidden">
                            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Custo Unitário (R$)</label>
                            <input type="number" name="unit_cost" id="sub_unit_cost" step="0.01" min="0" value="0" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-right">
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Total do Item:</span>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400" id="sub-total-price">R$ 0,00</span>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Observações</label>
                    <textarea name="art_notes" rows="2" placeholder="Observações importantes para a produção..." class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm"></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 flex justify-between items-center sticky bottom-0">
                <button type="button" onclick="closeSublimationModal()" class="px-4 py-2 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit" id="submit-sublimation-btn" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-all text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Adicionar Item
                </button>
            </div>
        </form>
    </div>
</div>
