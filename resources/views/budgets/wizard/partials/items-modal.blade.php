<!-- MULTI-STEP MODAL -->
<div id="item-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="budget-modal-overlay fixed inset-0 bg-gray-900/75 transition-opacity" onclick="closeItemModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="budget-modal-shell relative transform overflow-hidden text-left transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div class="budget-modal-header px-4 py-4 sm:px-6 border-b flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100" id="modal-title">Configurar Item</h3>
                        <p class="text-xs text-gray-500 mt-1">Passo <span id="current-step-label">1</span> de 4</p>
                    </div>
                    <button type="button" onclick="closeItemModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Fechar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="budget-modal-stepper px-6 py-4 border-b">
                    <div class="flex items-center justify-between w-full max-w-sm mx-auto">
                        <div class="flex flex-col items-center step-indicator" data-step="1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-purple-600 text-white font-bold text-sm ring-4 ring-purple-100 dark:ring-purple-900/30" style="color: white !important;">1</div>
                            <span class="text-xs font-medium mt-2 text-purple-600 dark:text-purple-400">Tipo</span>
                        </div>
                        <div class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-700 mx-2 step-line" data-to="2"></div>
                        <div class="flex flex-col items-center step-indicator" data-step="2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-purple-600 text-white font-bold text-sm ring-4 ring-purple-100 dark:ring-purple-900/30" style="color: white !important;">2</div>
                            <span class="text-xs font-medium mt-2 text-gray-500">Tecido</span>
                        </div>
                        <div class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-700 mx-2 step-line" data-to="3"></div>
                        <div class="flex flex-col items-center step-indicator" data-step="3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold text-sm">3</div>
                            <span class="text-xs font-medium mt-2 text-gray-500">Modelo</span>
                        </div>
                        <div class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-700 mx-2 step-line" data-to="4"></div>
                        <div class="flex flex-col items-center step-indicator" data-step="4">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold text-sm">4</div>
                            <span class="text-xs font-medium mt-2 text-gray-500">Qtd</span>
                        </div>
                    </div>
                </div>

                <form id="item-form" method="POST" action="{{ route('budget.items') }}">
                    @csrf
                    <input type="hidden" name="action" value="add_item">

                    <div class="budget-modal-body px-6 py-6 min-h-[300px]">
                        <div id="step-1" class="step-content">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Qual o tipo de personalização?</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="personalizacao-options">
                                <div class="col-span-full text-center py-8">
                                    <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p id="step-1-error" class="text-red-500 text-sm mt-2 hidden">Selecione pelo menos uma opção.</p>
                        </div>

                        <div id="step-2" class="step-content hidden">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Escolha o material e a cor</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tecido *</label>
                                    <select name="tecido" id="tecido" onchange="loadTiposTecido()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                                <div id="tipo-tecido-container" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Tecido</label>
                                    <select name="tipo_tecido" id="tipo_tecido" onchange="renderAllDropdowns()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor *</label>
                                    <select name="cor" id="cor" class="w-full rounded-lg border-gray-300 dark:border-gray-600 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                            </div>
                            <p id="step-2-error" class="text-red-500 text-sm mt-2 hidden">Preencha todos os campos obrigatórios.</p>
                        </div>

                        <div id="step-3" class="step-content hidden">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Detalhes do Modelo</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Corte *</label>
                                    <select name="tipo_corte" id="tipo_corte" onchange="renderAllDropdowns()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gola</label>
                                        <select name="gola" id="gola" onchange="updatePrice()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                            <option value="">Selecione...</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Detalhe</label>
                                        <select name="detalhe" id="detalhe" onchange="updatePrice()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                            <option value="">Selecione...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <p id="step-3-error" class="text-red-500 text-sm mt-2 hidden">Selecione o tipo de corte.</p>
                        </div>

                        <div id="step-4" class="step-content hidden">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quantidade e Observações</h4>

                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-100 dark:border-purple-800 mb-6 flex justify-between items-center">
                                <div>
                                    <span class="text-xs text-purple-500 font-semibold uppercase">Preço Unitário Estimado</span>
                                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300" id="price-total-display">R$ 0,00</div>
                                </div>
                                <div class="w-32">
                                    <label class="block text-xs text-gray-500 mb-1">Quantidade *</label>
                                    <input type="number" name="quantity" id="quantity" min="1" value="1" required class="w-full rounded-md border-gray-300 text-center font-bold text-lg py-2 focus:ring-purple-500 focus:border-purple-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações (Opcional)</label>
                                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="Algum detalhe específico?"></textarea>
                            </div>

                            <input type="hidden" name="unit_price" id="unit_price" value="0">
                        </div>
                    </div>

                    <div class="budget-modal-footer px-6 py-4 border-t flex justify-between">
                        <button type="button" id="btn-prev" onclick="changeStep(-1)" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition hidden">
                            Voltar
                        </button>

                        <div class="flex-1"></div>

                        <button type="button" onclick="closeItemModal()" class="px-4 py-2 mr-3 text-gray-500 hover:text-gray-700 font-medium">
                            Cancelar
                        </button>

                        <button type="button" id="btn-next" onclick="changeStep(1)" style="color: white !important;" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium shadow-sm transition">
                            Próximo
                        </button>

                        <button type="submit" id="btn-save" style="color: white !important;" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-sm transition hidden">
                            Salvar Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
