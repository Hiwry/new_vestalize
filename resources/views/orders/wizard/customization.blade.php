@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-[#7c3aed] text-white rounded-full flex items-center justify-center text-xs font-medium">3</div>
                    <div>
                        <span class="text-base font-medium text-[#7c3aed]">Personalização</span>
                        <p class="text-xs text-gray-500">Etapa 3 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-[#7c3aed]">60%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-[#7c3aed] h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 60%"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Personalização dos Itens</h1>
                        <p class="text-sm text-gray-600">Configure as aplicações para cada item</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('orders.wizard.customization') }}" id="customization-form" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <input type="hidden" name="total_shirts" value="{{ session('total_shirts', 0) }}" id="total-shirts">
                    <input type="hidden" name="sublimations" id="sublimations-data">

                    <!-- Informações do Pedido -->
                    @if($order->items->count() > 1)
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Selecione os Itens</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <!-- Checkbox para vincular todos os itens -->
                            <div class="mb-4 p-3 bg-purple-50 border border-purple-200 rounded-md">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="link-all-items" onchange="toggleLinkAllItems()" 
                                           class="w-4 h-4 text-[#7c3aed] border-gray-300 rounded focus:ring-purple-500">
                                    <span class="ml-2 text-sm font-medium text-gray-900">
                                        <svg class="w-4 h-4 inline-block mr-1 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                        Aplicar mesma personalização em TODOS os itens
                                    </span>
                                </label>
                                <p class="text-xs text-gray-600 mt-1 ml-6">Marque esta opção se todos os itens terão exatamente a mesma arte/escudo/logo</p>
                            </div>
                            
                            <!-- Lista de itens com checkboxes individuais -->
                            <div id="items-selection-container">
                                <p class="text-xs text-gray-600 mb-2">Ou selecione itens específicos:</p>
                                <div class="space-y-2" id="items-checkboxes">
                                    @foreach($order->items as $item)
                                    <label class="flex items-center p-2 bg-white border border-gray-200 rounded-md hover:border-[#7c3aed] cursor-pointer transition-colors item-checkbox-label">
                                        <input type="checkbox" name="linked_items[]" value="{{ $item->id }}" 
                                               class="item-checkbox w-4 h-4 text-[#7c3aed] border-gray-300 rounded focus:ring-purple-500"
                                               onchange="updateLinkedItemsCount()">
                                        <div class="ml-3 flex-1">
                                            <span class="text-sm font-medium text-gray-900">Item {{ $item->item_number }}</span>
                                            <span class="text-xs text-gray-500 ml-2">{{ $item->model }} - {{ $item->print_type }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $item->quantity }} pç</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Resumo de itens vinculados -->
                            <div id="linked-items-summary" class="mt-3 p-2 bg-green-50 border border-green-200 rounded-md hidden">
                                <div class="flex items-center text-sm text-green-800">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span><strong id="linked-count">0</strong> itens receberão esta personalização (<strong id="linked-total-qty">0</strong> peças no total)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="linked_items[]" value="{{ $order->items->first()->id }}">
                    @endif

                    <!-- Nome da Arte -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Nome da Arte *</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <input type="text" id="art_name" name="art_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-[#7c3aed] focus:ring-1 focus:ring-purple-500"
                                   placeholder="Ex: Logo Empresa - DTF">
                        </div>
                    </div>


                    <!-- Arquivos da Arte -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Arquivos da Arte *</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <input type="file" id="art_files" name="art_files[]" multiple
                                   required class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-[#7c3aed]"
                                   onchange="displayFileList()">
                            <p class="text-xs text-gray-500 mt-2">Múltiplos arquivos: AI, PDF, PNG, JPG, CDR - Obrigatório pelo menos 1 arquivo</p>
                            <div id="file-list" class="mt-3 space-y-2"></div>
                        </div>
                    </div>

                    <!-- Tamanhos de Aplicação -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Adicionar Aplicações</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <p class="text-xs text-gray-600 mb-3">Selecione o tamanho da aplicação</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="size-buttons">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Aplicações Adicionadas -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Aplicações Adicionadas</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <p class="text-sm text-gray-500 mb-3" id="no-applications">Nenhuma aplicação adicionada</p>
                            <div id="applications-list" class="space-y-2">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="bg-purple-50 rounded-md p-4 border border-purple-100">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-900">Resumo</h3>
                            <div class="text-xs text-gray-600">
                                Total de peças: <span class="font-medium text-gray-900">{{ session('total_shirts', 0) }}</span>
                            </div>
                        </div>
                        <div id="price-breakdown" class="space-y-1 text-sm text-gray-600 mb-3">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-purple-200">
                            <span class="text-sm font-medium text-gray-900">Total:</span>
                            <span id="total-price" class="text-lg font-bold text-[#7c3aed]">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Botões de Navegação -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('orders.wizard.sewing') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Voltar
                        </a>
                        <button type="submit" 
                                style="color: white !important;"
                                class="px-6 py-2 bg-[#7c3aed] text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 flex items-center">
                            Continuar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="delete-confirmation-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-slate-900 rounded-lg max-w-md w-full shadow-xl border border-gray-200 dark:border-slate-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmar Remoção</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-slate-400">Deseja realmente remover esta aplicação?</p>
                <div id="delete-item-info" class="mt-3 p-3 bg-gray-50 dark:bg-slate-800/50 rounded-md text-sm border border-gray-200 dark:border-slate-700">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()" 
                        class="px-4 py-2 bg-red-600 dark:bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:hover:bg-red-700 transition-colors">
                    Remover
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Minimalista -->
    <div id="application-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full shadow-xl">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <span id="modal-size-name"></span>
                </h3>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Local da Aplicação *</label>
                    <select id="modal-location" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-[#7c3aed] focus:ring-1 focus:ring-purple-500">
                        <option value="">Selecione</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                    <input type="number" id="modal-quantity" min="1" value="1" 
                           onchange="updateModalPrices()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-[#7c3aed] focus:ring-1 focus:ring-purple-500">
                </div>

                @if(auth()->check() && auth()->user()->isAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor Unitário (R$) *</label>
                    <input type="number" id="modal-unit-price-input" step="0.01" min="0" value="0" 
                           onchange="updateModalPriceFromInput()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-[#7c3aed] focus:ring-1 focus:ring-purple-500">
                </div>
                @endif

                <div class="bg-gray-50 rounded-md p-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Valor unitário:</span>
                        <span class="font-medium text-gray-900" id="modal-unit-price">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-900" id="modal-subtotal">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="button" onclick="addApplication()" id="add-application-btn"
                        style="color: white !important;"
                        class="px-4 py-2 bg-[#7c3aed] text-white text-sm font-medium rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                    Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
(function() {
    // State
    window.sublimationSizes = [];
    window.sublimationLocations = [];
    window.sublimations = [];
    window.totalShirts = {{ session('total_shirts', 0) }};
    window.currentSubSize = null;
    window.isUserAdmin = {{ auth()->check() && auth()->user()->isAdmin() ? 'true' : 'false' }};
    window.pendingDeleteIndex = null;

    // Items data for calculating totals
    const itemsQtyMap = {
        @foreach($order->items as $item)
        '{{ $item->id }}': {{ $item->quantity }},
        @endforeach
    };

    function initCustomizationPage() {
        console.log('Initializing Customization Page...');
        window.sublimations = [];
        
        // Listeners
        const form = document.getElementById('customization-form');
        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', function(e) {
                document.getElementById('sublimations-data').value = JSON.stringify(window.sublimations);
            });
            form.dataset.listenerAttached = 'true';
        }

        const artFiles = document.getElementById('art_files');
        if (artFiles && !artFiles.dataset.listenerAttached) {
            artFiles.addEventListener('change', window.displayFileList);
            artFiles.dataset.listenerAttached = 'true';
        }

        @if($order->items->count() > 1)
        // Select first item by default if nothing selected
        const checked = document.querySelectorAll('.item-checkbox:checked');
        if (checked.length === 0) {
            const firstCheckbox = document.querySelector('.item-checkbox');
            if (firstCheckbox) {
                firstCheckbox.checked = true;
                window.updateLinkedItemsCount();
            }
        }
        @endif

        window.loadCustomizationData();
    }

    window.toggleLinkAllItems = function() {
        const linkAll = document.getElementById('link-all-items');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const container = document.getElementById('items-selection-container');
        if (!linkAll) return;
        
        checkboxes.forEach(cb => {
            cb.checked = linkAll.checked;
            cb.disabled = linkAll.checked;
        });
        
        if (container) {
            if (linkAll.checked) container.classList.add('opacity-50');
            else container.classList.remove('opacity-50');
        }
        window.updateLinkedItemsCount();
    }

    window.updateLinkedItemsCount = function() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        const summary = document.getElementById('linked-items-summary');
        const countEl = document.getElementById('linked-count');
        const qtyEl = document.getElementById('linked-total-qty');
        const hiddenTotal = document.getElementById('total-shirts');
        
        if (!summary || !countEl || !qtyEl) return;
        
        if (checkboxes.length === 0) {
            summary.classList.add('hidden');
            return;
        }
        
        let totalQty = 0;
        checkboxes.forEach(cb => {
            totalQty += itemsQtyMap[cb.value] || 0;
        });
        
        countEl.textContent = checkboxes.length;
        qtyEl.textContent = totalQty;
        summary.classList.remove('hidden');
        
        window.totalShirts = totalQty;
        if (hiddenTotal) hiddenTotal.value = totalQty;
    }

    window.loadCustomizationData = function() {
        Promise.all([
            fetch('/api/sublimation-sizes').then(r => r.json()),
            fetch('/api/sublimation-locations').then(r => r.json())
        ]).then(([sizesData, locationsData]) => {
            window.sublimationSizes = sizesData;
            window.sublimationLocations = locationsData;
            window.renderSizeButtons();
            window.renderLocationOptions();
        });
    }

    window.renderSizeButtons = function() {
        const container = document.getElementById('size-buttons');
        if (!container) return;
        container.innerHTML = window.sublimationSizes.map(size => `
            <button type="button" onclick="window.openSubModal(${size.id})" 
                    class="p-3 border border-gray-300 rounded-md hover:border-[#7c3aed] hover:bg-purple-50 transition text-center">
                <div class="font-medium text-sm">${size.name}</div>
                <div class="text-xs text-gray-500 mt-1">${size.dimensions || ''}</div>
            </button>
        `).join('');
    }

    window.renderLocationOptions = function() {
        const select = document.getElementById('modal-location');
        if (!select) return;
        select.innerHTML = '<option value="">Selecione</option>' + 
            window.sublimationLocations.map(loc => `<option value="${loc.id}">${loc.name}</option>`).join('');
    }

    window.openSubModal = function(sizeId) {
        window.currentSubSize = window.sublimationSizes.find(s => s.id === sizeId);
        if (!window.currentSubSize) return;
        
        const dimensions = window.currentSubSize.dimensions || '';
        document.getElementById('modal-size-name').textContent = dimensions ? `${window.currentSubSize.name} (${dimensions})` : window.currentSubSize.name;
        document.getElementById('modal-quantity').value = 1;
        document.getElementById('modal-location').value = '';
        document.getElementById('modal-unit-price').textContent = 'Carregando...';
        document.getElementById('modal-subtotal').textContent = 'R$ 0,00';
        
        const addBtn = document.getElementById('add-application-btn');
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.textContent = 'Carregando...';
        }
        
        document.getElementById('application-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        fetch(`/api/sublimation-price/${sizeId}/${window.totalShirts}`)
            .then(r => r.json())
            .then(data => {
                window.currentSubSize.price = parseFloat(data.price);
                window.updateModalPrices();
                if (addBtn) {
                    addBtn.disabled = false;
                    addBtn.textContent = 'Adicionar';
                }
            })
            .catch(() => {
                alert('Erro ao carregar preço');
                window.closeSubModal();
            });
    }

    window.closeSubModal = function() {
        document.getElementById('application-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        window.currentSubSize = null;
    }

    window.updateModalPrices = function() {
        if (!window.currentSubSize) return;
        const qty = parseInt(document.getElementById('modal-quantity').value) || 1;
        const price = window.currentSubSize.price || 0;
        const sub = price * qty;
        document.getElementById('modal-unit-price').textContent = `R$ ${price.toFixed(2).replace('.', ',')}`;
        document.getElementById('modal-subtotal').textContent = `R$ ${sub.toFixed(2).replace('.', ',')}`;
        const input = document.getElementById('modal-unit-price-input');
        if (input) input.value = price.toFixed(2);
    }

    window.updateModalPriceFromInput = function() {
        if (!window.isUserAdmin) return;
        const val = parseFloat(document.getElementById('modal-unit-price-input').value) || 0;
        if (window.currentSubSize) window.currentSubSize.price = val;
        window.updateModalPrices();
    }

    window.addApplication = function() {
        const locId = document.getElementById('modal-location').value;
        const qty = parseInt(document.getElementById('modal-quantity').value);
        if (!locId || !qty || !window.currentSubSize) {
            alert('Preencha todos os campos');
            return;
        }
        const loc = window.sublimationLocations.find(l => l.id == locId);
        const price = window.currentSubSize.price || 0;
        window.sublimations.push({
            size_id: window.currentSubSize.id,
            size_name: window.currentSubSize.name,
            size_dimensions: window.currentSubSize.dimensions,
            location_id: locId,
            location_name: loc ? loc.name : 'N/A',
            quantity: qty,
            unit_price: price,
            subtotal: price * qty
        });
        window.renderApplications();
        window.updatePriceBreakdown();
        window.closeSubModal();
    }

    window.renderApplications = function() {
        const container = document.getElementById('applications-list');
        const noApps = document.getElementById('no-applications');
        if (!container || !noApps) return;
        if (window.sublimations.length === 0) {
            noApps.classList.remove('hidden');
            container.innerHTML = '';
            return;
        }
        noApps.classList.add('hidden');
        container.innerHTML = window.sublimations.map((app, index) => `
            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md">
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${app.size_name} - ${app.location_name}</div>
                    <div class="text-xs text-gray-500">${app.quantity}x R$ ${app.unit_price.toFixed(2).replace('.', ',')}</div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-900">R$ ${app.subtotal.toFixed(2).replace('.', ',')}</span>
                    <button type="button" onclick="window.removeApplication(${index})" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
            </div>`).join('');
    }

    window.removeApplication = function(index) {
        window.pendingDeleteIndex = index;
        const app = window.sublimations[index];
        const info = document.getElementById('delete-item-info');
        if (info) {
            info.innerHTML = `
                <div class="font-medium text-gray-900">${app.size_name} - ${app.location_name}</div>
                <div class="text-xs text-gray-500 mt-1">Quantidade: ${app.quantity} | Valor: R$ ${app.subtotal.toFixed(2).replace('.', ',')}</div>`;
        }
        document.getElementById('delete-confirmation-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    window.closeDeleteModal = function() {
        document.getElementById('delete-confirmation-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        window.pendingDeleteIndex = null;
    }

    window.confirmDelete = function() {
        if (window.pendingDeleteIndex !== null) {
            window.sublimations.splice(window.pendingDeleteIndex, 1);
            window.renderApplications();
            window.updatePriceBreakdown();
        }
        window.closeDeleteModal();
    }

    window.updatePriceBreakdown = function() {
        const total = window.sublimations.reduce((sum, app) => sum + app.subtotal, 0);
        const totalEl = document.getElementById('total-price');
        if (totalEl) totalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        const breakdown = document.getElementById('price-breakdown');
        if (!breakdown) return;
        if (window.sublimations.length === 0) {
            breakdown.innerHTML = '<p class="text-sm text-gray-500">Nenhuma aplicação adicionada</p>';
        } else {
            breakdown.innerHTML = window.sublimations.map(app => 
                `<div class="flex justify-between"><span>${app.size_name} - ${app.location_name} (${app.quantity}x)</span><span>R$ ${app.subtotal.toFixed(2).replace('.', ',')}</span></div>`
            ).join('');
        }
    }

    window.displayFileList = function() {
        const files = document.getElementById('art_files').files;
        const container = document.getElementById('file-list');
        if (!container) return;
        container.innerHTML = Array.from(files).map(file => `
            <div class="flex items-center p-2 bg-white border border-gray-200 rounded text-sm">
                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-gray-700">${file.name}</span>
                <span class="ml-auto text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</span>
            </div>`).join('');
    }

    // Initialization
    window._customizationInitSetup = function() {
        document.removeEventListener('ajax-content-loaded', initCustomizationPage);
        document.addEventListener('ajax-content-loaded', initCustomizationPage);
    };
    window._customizationInitSetup();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomizationPage);
    } else {
        initCustomizationPage();
    }
})();
</script>
@endpush
@endsection
