@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
                <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">2</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Costura e Personalização</span>
                        <p class="text-xs text-gray-500">Etapa 2 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600">40%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 40%"></div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Editar Itens do Pedido</h1>
                        <p class="text-sm text-gray-600">Gerencie os itens de costura e personalização</p>
                    </div>
                </div>
        </div>

            <div class="p-6">
                <!-- Lista de Itens Atuais -->
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Itens Atuais do Pedido</h2>
                    
                    @if($order->items->count() > 0)
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                            <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-indigo-100 rounded-md flex items-center justify-center">
                                            <span class="text-xs font-medium text-indigo-600">{{ $item->item_number }}</span>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">Item {{ $item->item_number }}</h3>
                                        </div>
                                    <div class="flex space-x-2">
                                        <button type="button" 
                                                data-item-id="{{ $item->id }}"
                                                class="edit-item-btn inline-flex items-center px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Editar
                                        </button>
                                        <form method="POST" action="{{ route('orders.edit.sewing') }}" class="inline">
                    @csrf
                                            <input type="hidden" name="action" value="delete_item">
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            <button type="submit" onclick="return confirm('Deseja remover este item?')" 
                                                    class="inline-flex items-center px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            <div>
                                        <span class="text-gray-600">Personalização:</span>
                                        <p class="font-medium">{{ $item->print_type }}</p>
                                    </div>
                                        <div>
                                        <span class="text-gray-600">Tecido:</span>
                                        <p class="font-medium">{{ $item->fabric }}</p>
                                        </div>
                                        <div>
                                        <span class="text-gray-600">Cor:</span>
                                        <p class="font-medium">{{ $item->color }}</p>
                                        </div>
                                        <div>
                                        <span class="text-gray-600">Quantidade:</span>
                                        <p class="font-medium">{{ $item->quantity }} peças</p>
                            </div>
                        </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Valor Unitário:</span>
                                        <span class="text-sm font-medium">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                                        </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">Total:</span>
                                        <span class="text-lg font-bold text-indigo-600">R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                        </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total de Itens:</span>
                                <span class="text-sm font-medium">{{ $order->items->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total de Peças:</span>
                                <span class="text-sm font-medium">{{ $order->total_items }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                <span class="text-lg font-semibold text-gray-900">Subtotal:</span>
                                <span class="text-xl font-bold text-indigo-600">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">Nenhum item encontrado</p>
                            <p class="text-xs text-gray-400">Os itens do pedido serão exibidos aqui</p>
                        </div>
                    @endif
                            </div>

                <!-- Botões de Navegação -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <a href="{{ route('orders.edit.client') }}" 
                       class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Voltar
                    </a>
                    <a href="{{ route('orders.edit.customization') }}" 
                       class="flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                        Continuar
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Item -->
    <div id="editItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Editar Item</h3>
                    <button onclick="if(typeof window.closeEditModal === 'function') window.closeEditModal(); else closeEditModal();" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        </button>
        </div>

                <form method="POST" action="{{ route('orders.edit.sewing') }}" id="editItemForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="update_item">
                    <input type="hidden" name="editing_item_id" id="editingItemId">
                    
                    <div class="space-y-6">
                    <!-- Personalização -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Personalização *</label>
                            <div class="grid grid-cols-2 gap-3" id="personalizacao-options">
                                <!-- Será preenchido via JavaScript -->
                </div>
            </div>

                    <!-- Tecido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tecido *</label>
                            <select name="tecido" id="tecido" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                            <option value="">Selecione o tecido</option>
                        </select>
                                    </div>
                                    
                    <!-- Tipo de Tecido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Tecido</label>
                            <select name="tipo_tecido" id="tipo_tecido" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Selecione o tipo</option>
                                            </select>
                                        </div>

                        <!-- Cor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cor *</label>
                            <select name="cor" id="cor" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                            <option value="">Selecione a cor</option>
                        </select>
                                        </div>

                        <!-- Tipo de Corte -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Corte *</label>
                            <select name="tipo_corte" id="tipo_corte" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                            <option value="">Selecione o tipo de corte</option>
                        </select>
                                        </div>

                        <!-- Detalhe -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Detalhe</label>
                            <select name="detalhe" id="detalhe" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Selecione o detalhe</option>
                                            </select>
                                        </div>

                        <!-- Gola -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gola *</label>
                            <select name="gola" id="gola" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                            <option value="">Selecione a gola</option>
                                            </select>
                                        </div>

                        <!-- Tamanhos -->
                                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamanhos *</label>
                            <div class="grid grid-cols-4 gap-3" id="tamanhos-container">
                                <!-- Será preenchido via JavaScript -->
                                        </div>
                                    </div>

                        <!-- Preço Unitário -->
                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário *</label>
                            <input type="number" name="unit_price" id="unit_price" step="0.01" min="0" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                        </div>

    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="if(typeof window.closeEditModal === 'function') window.closeEditModal(); else closeEditModal();" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>

<script>
        // Definir variáveis globais
        let currentItemData = null;
        let productOptions = {};

        // Definir função editItem IMEDIATAMENTE (antes de qualquer uso)
        function editItem(itemId) {
            console.log('🔧 editItem chamado com ID:', itemId, typeof itemId);
            
            if (!itemId) {
                console.error('❌ ID do item não fornecido!');
                alert('Erro: ID do item não fornecido.');
                return;
            }
            
            // Verificar se o modal existe
            const modal = document.getElementById('editItemModal');
            if (!modal) {
                console.error('❌ Modal editItemModal não encontrado!');
                alert('Erro: Modal de edição não encontrado. Recarregue a página.');
                return;
            }
            
            console.log('✅ Modal encontrado, abrindo...');
            
            // Limpar formulário antes de preencher
            const form = document.getElementById('editItemForm');
            if (form) {
                form.reset();
            }
            
            // Mostrar modal imediatamente (com loading)
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Buscar dados do item
            console.log('📡 Buscando dados do item na API:', `/api/order-items/${itemId}`);
            fetch(`/api/order-items/${itemId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('📥 Resposta recebida:', response.status, response.statusText);
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Dados do item recebidos:', data);
                    currentItemData = data;
                    
                    // Garantir que as opções estão carregadas antes de preencher
                    if (Object.keys(productOptions).length === 0) {
                        console.log('⏳ Opções não carregadas, carregando agora...');
                        loadProductOptions().then(() => {
                            console.log('✅ Opções carregadas, preenchendo formulário...');
                            setTimeout(() => populateEditForm(data), 300);
                        });
                    } else {
                        console.log('✅ Opções já carregadas, preenchendo formulário...');
                        setTimeout(() => populateEditForm(data), 300);
                    }
                })
                .catch(error => {
                    console.error('❌ Erro ao carregar item:', error);
                    alert('Erro ao carregar dados do item: ' + error.message);
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
        }
        
        // Expor função globalmente IMEDIATAMENTE
        window.editItem = editItem;
        console.log('✅ Função editItem definida e exposta globalmente:', typeof window.editItem);

        // Carregar opções de produtos
        async function loadProductOptions() {
            try {
                const response = await fetch('/api/product-options', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                productOptions = await response.json();
                populateSelects();
            } catch (error) {
                console.error('Erro ao carregar opções:', error);
            }
        }

        function populateSelects() {
            // Personalização
            const personalizacaoContainer = document.getElementById('personalizacao-options');
            personalizacaoContainer.innerHTML = '';
            
            if (productOptions.personalizacao) {
                productOptions.personalizacao.forEach(option => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center';
                    div.innerHTML = `
                        <input type="checkbox" name="personalizacao[]" value="${option.id}" id="personalizacao_${option.id}" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="personalizacao_${option.id}" class="ml-2 text-sm text-gray-700">${option.name}</label>
                    `;
                    personalizacaoContainer.appendChild(div);
                });
            }

            // Tecido
            populateSelect('tecido', productOptions.tecido);
            populateSelect('tipo_tecido', productOptions.tipo_tecido);
            populateSelect('cor', productOptions.cor);
            populateSelect('tipo_corte', productOptions.tipo_corte);
            populateSelect('detalhe', productOptions.detalhe);
            populateSelect('gola', productOptions.gola);

            // Tamanhos
            const tamanhosContainer = document.getElementById('tamanhos-container');
            tamanhosContainer.innerHTML = '';
            
            if (productOptions.tamanho && productOptions.tamanho.length > 0) {
                productOptions.tamanho.forEach(size => {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <label class="block text-xs text-gray-600 mb-1">${size.name}</label>
                        <input type="number" name="tamanhos[${size.name}]" min="0" value="0" 
                               class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    `;
                    tamanhosContainer.appendChild(div);
                });
            } else {
                // Fallback: criar campos básicos se não houver dados da API
                const tamanhosBasicos = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'Especial'];
                tamanhosBasicos.forEach(tamanho => {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <label class="block text-xs text-gray-600 mb-1">${tamanho}</label>
                        <input type="number" name="tamanhos[${tamanho}]" min="0" value="0" 
                               class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    `;
                    tamanhosContainer.appendChild(div);
                });
            }
        }

        function populateSelect(selectId, options) {
            const select = document.getElementById(selectId);
            if (!select || !options) return;

            // Limpar opções existentes (exceto a primeira)
            while (select.children.length > 1) {
                select.removeChild(select.lastChild);
            }

            options.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.id;
                optionElement.textContent = option.name;
                select.appendChild(optionElement);
            });
        }


        function populateEditForm(item) {
            console.log('📝 Preenchendo formulário com dados:', item);
            
            try {
                // Preencher ID do item
                const editingItemId = document.getElementById('editingItemId');
                if (editingItemId) {
                    editingItemId.value = item.id || '';
                    console.log('✅ ID do item preenchido:', item.id);
                }
                
                // Preencher preço unitário
                const unitPriceInput = document.getElementById('unit_price');
                if (unitPriceInput && item.unit_price) {
                    unitPriceInput.value = parseFloat(item.unit_price).toFixed(2);
                    console.log('✅ Preço unitário preenchido:', item.unit_price);
                }

                // Preencher personalização usando IDs
                if (item.print_type_ids && item.print_type_ids.length > 0) {
                    console.log('✅ Preenchendo personalização por IDs:', item.print_type_ids);
                    // Desmarcar todos primeiro
                    document.querySelectorAll('input[name="personalizacao[]"]').forEach(cb => cb.checked = false);
                    // Marcar os selecionados
                    item.print_type_ids.forEach(id => {
                        const checkbox = document.querySelector(`input[name="personalizacao[]"][value="${id}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                            console.log(`✅ Personalização ${id} marcada`);
                        } else {
                            console.warn(`⚠️ Checkbox de personalização ${id} não encontrado`);
                        }
                    });
                } else if (item.print_type) {
                    console.log('⚠️ Usando fallback para personalização por nome:', item.print_type);
                    // Fallback: tentar por nome
                    const personalizacoes = item.print_type.split(', ');
                    personalizacoes.forEach(p => {
                        // Tentar encontrar por nome no texto do label
                        document.querySelectorAll('input[name="personalizacao[]"]').forEach(cb => {
                            const label = document.querySelector(`label[for="${cb.id}"]`);
                            if (label && label.textContent.trim().includes(p.trim())) {
                                cb.checked = true;
                                console.log(`✅ Personalização "${p.trim()}" marcada por nome`);
                            }
                        });
                    });
                }

                // Preencher campos usando IDs quando disponíveis
                if (item.fabric_id) {
                    setSelectValueById('tecido', item.fabric_id);
                    console.log('✅ Tecido preenchido por ID:', item.fabric_id);
                } else if (item.fabric) {
                    setSelectValue('tecido', item.fabric);
                    console.log('✅ Tecido preenchido por nome:', item.fabric);
                }
                
                if (item.color_id) {
                    setSelectValueById('cor', item.color_id);
                    console.log('✅ Cor preenchida por ID:', item.color_id);
                } else if (item.color) {
                    setSelectValue('cor', item.color);
                    console.log('✅ Cor preenchida por nome:', item.color);
                }
                
                if (item.model_id) {
                    setSelectValueById('tipo_corte', item.model_id);
                    console.log('✅ Tipo de corte preenchido por ID:', item.model_id);
                } else if (item.model) {
                    setSelectValue('tipo_corte', item.model);
                    console.log('✅ Tipo de corte preenchido por nome:', item.model);
                }
                
                if (item.detail_id) {
                    setSelectValueById('detalhe', item.detail_id);
                    console.log('✅ Detalhe preenchido por ID:', item.detail_id);
                } else if (item.detail) {
                    setSelectValue('detalhe', item.detail);
                    console.log('✅ Detalhe preenchido por nome:', item.detail);
                }
                
                if (item.collar_id) {
                    setSelectValueById('gola', item.collar_id);
                    console.log('✅ Gola preenchida por ID:', item.collar_id);
                } else if (item.collar) {
                    setSelectValue('gola', item.collar);
                    console.log('✅ Gola preenchida por nome:', item.collar);
                }

                // Preencher tamanhos
                if (item.sizes) {
                    let sizes;
                    if (typeof item.sizes === 'string') {
                        try {
                            sizes = JSON.parse(item.sizes);
                        } catch (e) {
                            console.warn('⚠️ Erro ao parsear sizes:', e);
                            sizes = {};
                        }
                    } else {
                        sizes = item.sizes;
                    }
                    
                    if (sizes && typeof sizes === 'object') {
                        console.log('✅ Preenchendo tamanhos:', sizes);
                        let filledCount = 0;
                        Object.entries(sizes).forEach(([size, quantity]) => {
                            const input = document.querySelector(`input[name="tamanhos[${size}]"]`);
                            if (input) {
                                input.value = quantity || 0;
                                filledCount++;
                            } else {
                                console.warn(`⚠️ Input de tamanho "${size}" não encontrado`);
                            }
                        });
                        console.log(`✅ ${filledCount} tamanhos preenchidos`);
                    }
                }
                
                // Preencher nome da arte se houver campo
                const artNameInput = document.getElementById('art_name');
                if (artNameInput && item.art_name) {
                    artNameInput.value = item.art_name;
                    console.log('✅ Nome da arte preenchido:', item.art_name);
                }
                
                // Preencher notas da arte se houver campo
                const artNotesInput = document.getElementById('art_notes');
                if (artNotesInput && item.art_notes) {
                    artNotesInput.value = item.art_notes;
                    console.log('✅ Notas da arte preenchidas');
                }
                
                console.log('✅ Formulário preenchido com sucesso!');
            } catch (error) {
                console.error('❌ Erro ao preencher formulário:', error);
                alert('Erro ao preencher formulário: ' + error.message);
            }
        }

        function setSelectValueById(selectId, valueId) {
            const select = document.getElementById(selectId);
            if (!select) {
                console.warn(`⚠️ setSelectValueById: Select "${selectId}" não encontrado`);
                return false;
            }
            
            console.log(`🔍 setSelectValueById: Procurando ID ${valueId} em ${selectId} (${select.options.length} opções)`);
            
            // Procurar por ID exato
            for (let option of select.options) {
                if (option.value == valueId) {
                    option.selected = true;
                    select.value = valueId;
                    console.log(`✅ setSelectValueById: "${selectId}" = "${option.textContent.trim()}" (ID: ${valueId})`);
                    return true;
                }
            }
            
            console.warn(`⚠️ setSelectValueById: ID ${valueId} não encontrado em ${selectId}`);
            return false;
        }

        function setSelectValue(selectId, value) {
            const select = document.getElementById(selectId);
            if (!select) {
                console.warn(`⚠️ setSelectValue: Select "${selectId}" não encontrado`);
                return false;
            }
            
            if (!value) {
                console.warn(`⚠️ setSelectValue: Valor vazio para "${selectId}"`);
                return false;
            }
            
            console.log(`🔍 setSelectValue: Procurando "${value}" em ${selectId} (${select.options.length} opções)`);

            // Procurar por correspondência exata primeiro
            for (let option of select.options) {
                const optText = option.textContent.trim();
                const optTextClean = optText.replace(/\s*\(.*?\)\s*$/, '').trim(); // Remove preço em parênteses
                
                if (optText === value || optTextClean === value) {
                    option.selected = true;
                    select.value = option.value;
                    console.log(`✅ setSelectValue: "${selectId}" = "${optText}" (correspondência exata)`);
                    return true;
                }
            }
            
            // Procurar por correspondência parcial (includes)
            for (let option of select.options) {
                const optText = option.textContent.trim();
                if (optText.toLowerCase().includes(value.toLowerCase()) || 
                    value.toLowerCase().includes(optText.toLowerCase().replace(/\s*\(.*?\)\s*$/, '').trim())) {
                    option.selected = true;
                    select.value = option.value;
                    console.log(`✅ setSelectValue: "${selectId}" = "${optText}" (correspondência parcial)`);
                    return true;
                }
            }
            
            console.warn(`⚠️ setSelectValue: Valor "${value}" não encontrado em ${selectId}`);
            return false;
        }

        function closeEditModal() {
            const modal = document.getElementById('editItemModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            const form = document.getElementById('editItemForm');
            if (form) {
                form.reset();
            }
        }
        
        // Expor função globalmente
        window.closeEditModal = closeEditModal;


        // Event listener único para todos os cliques
        document.addEventListener('click', function(e) {
            // Verificar se é clique em botão de editar
            const editButton = e.target.closest('.edit-item-btn');
            if (editButton) {
                e.preventDefault();
                e.stopPropagation();
                
                const itemId = editButton.getAttribute('data-item-id');
                console.log('🔧 Botão de editar clicado!', {
                    itemId: itemId,
                    editItemExists: typeof window.editItem,
                    editItemType: typeof window.editItem
                });
                
                if (!itemId) {
                    console.error('❌ data-item-id não encontrado no botão!');
                    alert('Erro: ID do item não encontrado.');
                    return;
                }
                
                if (typeof window.editItem !== 'function') {
                    console.error('❌ window.editItem não é uma função!', typeof window.editItem);
                    alert('Erro: Função de edição não carregada. Recarregue a página.');
                    return;
                }
                
                try {
                    const itemIdNum = parseInt(itemId);
                    if (isNaN(itemIdNum)) {
                        throw new Error('ID do item inválido: ' + itemId);
                    }
                    window.editItem(itemIdNum);
                } catch (error) {
                    console.error('❌ Erro ao chamar editItem:', error);
                    alert('Erro ao editar item: ' + error.message);
                }
                return;
            }
            
            // Verificar se é clique fora do modal para fechar
            const modal = document.getElementById('editItemModal');
            if (modal && !modal.classList.contains('hidden')) {
                const modalContent = modal.querySelector('.relative');
                if (modalContent && !modalContent.contains(e.target) && e.target === modal) {
                    closeEditModal();
                }
            }
        });

        // Carregar opções quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Página carregada, iniciando carregamento de opções...');
            
            // Verificar se o modal existe
            const modal = document.getElementById('editItemModal');
            if (modal) {
                console.log('✅ Modal editItemModal encontrado');
            } else {
                console.error('❌ Modal editItemModal NÃO encontrado!');
            }
            
            // Carregar opções de produtos
            loadProductOptions();
            
            // Verificar quantos botões existem
            const editButtons = document.querySelectorAll('.edit-item-btn');
            console.log(`✅ ${editButtons.length} botões de editar encontrados na página.`);
            
            // Verificar se editItem está disponível
            if (typeof window.editItem === 'function') {
                console.log('✅ Função editItem disponível globalmente');
            } else {
                console.error('❌ Função editItem NÃO está disponível globalmente!');
            }
        });
    </script>
@endsection
