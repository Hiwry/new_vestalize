@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">3</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Personalização</span>
                        <p class="text-xs text-gray-500">Etapa 3 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600">60%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 60%"></div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Editar Personalização</h1>
                        <p class="text-sm text-gray-600">Configure as aplicações para cada item</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('orders.edit-wizard.customization') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Nome da Arte -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Nome da Arte *</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <input type="text" name="art_name" value="{{ $editData['personalization']['art_name'] ?? '' }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                   placeholder="Ex: Logo Empresa - DTF">
                        </div>
                    </div>


                    <!-- Arquivos da Arte -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Arquivos da Arte</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <input type="file" name="art_files[]" multiple
                                   class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-2">Múltiplos arquivos: AI, PDF, PNG, JPG, CDR</p>
                        </div>
                    </div>

                    <!-- Aplicações Existentes -->
                    @if(isset($editData['personalization']['sublimations']))
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Aplicações Configuradas</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <div class="space-y-2">
                                @foreach($editData['personalization']['sublimations'] as $index => $app)
                                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $app['size_name'] }} - {{ $app['location_name'] }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $app['quantity'] }}x R$ {{ number_format($app['unit_price'], 2, ',', '.') }}
                                            @if(isset($app['application_size']) && $app['application_size'])
                                                • {{ $app['application_size'] }}
                                            @endif
                                            @if(isset($app['color_quantity']) && $app['color_quantity'] > 1)
                                                • {{ $app['color_quantity'] }} cores
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm font-medium text-gray-900">R$ {{ number_format($app['subtotal'], 2, ',', '.') }}</div>
                                        <button type="button" 
                                                onclick="editSublimation({{ $index }})"
                                                class="p-1 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-all"
                                                title="Editar aplicação">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" 
                                                onclick="removeSublimation({{ $index }})"
                                                class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-all"
                                                title="Remover aplicação">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
        </div>
        @endif

                    <!-- Campo hidden para sublimações -->
                    <input type="hidden" name="sublimations" value="{{ json_encode($editData['personalization']['sublimations'] ?? []) }}">

                    <!-- Botões de Navegação -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <a href="{{ route('orders.edit-wizard.sewing') }}" 
                           class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Voltar
                        </a>
                        <button type="submit" 
                                class="flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                            Continuar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Sublimação -->
    <div id="editSublimationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Editar Aplicação</h3>
                </div>
                <form id="editSublimationForm" class="p-6 space-y-4">
                @csrf
                    <input type="hidden" id="editSublimationIndex" name="index">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tamanho da Aplicação</label>
                        <select id="editApplicationSize" name="application_size" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                onchange="calculatePrice()">
                            <option value="">Selecione o tamanho</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->size_name }}" data-name="{{ $size->size_name }}" data-dimensions="{{ $size->size_dimensions }}">
                                    {{ $size->size_name }} ({{ $size->size_dimensions }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                        <select id="editLocationName" name="location_name" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                required>
                            <option value="">Selecione a localização</option>
                            @foreach($sublimationLocations as $location)
                                <option value="{{ $location->name }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade de Cores</label>
                        <select id="editColorQuantity" name="color_quantity" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                onchange="calculatePrice()">
                            <option value="1" data-multiplier="1.0">1 cor (sem adicional)</option>
                            <option value="2" data-multiplier="1.2">2 cores (+20%)</option>
                            <option value="3" data-multiplier="1.4">3 cores (+40%)</option>
                            <option value="4" data-multiplier="1.6">4 cores (+60%)</option>
                            <option value="5" data-multiplier="1.8">5 cores (+80%)</option>
                            <option value="6" data-multiplier="2.0">6 cores (+100%)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                        <input type="number" id="editQuantity" name="quantity" min="1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" 
                               onchange="calculatePrice()"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$)</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="editUnitPrice" name="unit_price" step="0.01" min="0" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-gray-50" 
                                   readonly>
                            <span class="text-sm text-gray-500">Calculado automaticamente</span>
                    </div>
                </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeEditSublimationModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                            Cancelar
                        </button>
                    <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                            Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <script>
        // Dados das sublimações para edição
        let sublimationsData = @json($editData['personalization']['sublimations'] ?? []);
        
        // Debug: verificar dados iniciais
        console.log('Dados iniciais das sublimações:', sublimationsData);

        // Função para calcular preço automaticamente
        async function calculatePrice() {
            const applicationSizeSelect = document.getElementById('editApplicationSize');
            const colorQuantitySelect = document.getElementById('editColorQuantity');
            const quantityInput = document.getElementById('editQuantity');
            const unitPriceInput = document.getElementById('editUnitPrice');
            
            if (!applicationSizeSelect || !colorQuantitySelect || !quantityInput || !unitPriceInput) return;
            
            const sizeId = applicationSizeSelect.value;
            const quantity = parseInt(quantityInput.value) || 1;
            const selectedColorOption = colorQuantitySelect.options[colorQuantitySelect.selectedIndex];
            
            if (sizeId && selectedColorOption && selectedColorOption.value) {
                try {
                    // Buscar preço base da API de personalização para SERIGRAFIA
                    const response = await fetch(`/api/personalization-price?type=SERIGRAFIA&size=${encodeURIComponent(sizeId)}&quantity=${quantity}`);
                    const priceData = await response.json();
                    
                    if (priceData.success && priceData.price) {
                        const colorMultiplier = parseFloat(selectedColorOption.getAttribute('data-multiplier')) || 1;
                        const finalPrice = priceData.price * colorMultiplier;
                        
                        unitPriceInput.value = finalPrice.toFixed(2);
                        
                        console.log('Preço calculado:', {
                            basePrice: priceData.price,
                            colorMultiplier: colorMultiplier,
                            finalPrice: finalPrice,
                            quantity: quantity,
                            sizeName: priceData.size_name
                        });
                    } else {
                        unitPriceInput.value = '';
                        console.warn('Preço não encontrado:', priceData);
                    }
                } catch (error) {
                    console.error('Erro ao buscar preço:', error);
                    unitPriceInput.value = '';
                }
            } else {
                unitPriceInput.value = '';
            }
        }

        function editSublimation(index) {
            const sublimation = sublimationsData[index];
            if (!sublimation) return;

            // Preencher o modal com os dados atuais
            document.getElementById('editSublimationIndex').value = index;
            
            // Definir valores dos selects
            const applicationSizeSelect = document.getElementById('editApplicationSize');
            const locationSelect = document.getElementById('editLocationName');
            const colorQuantitySelect = document.getElementById('editColorQuantity');
            
            if (applicationSizeSelect) {
                // Buscar pelo nome do tamanho
                for (let i = 0; i < applicationSizeSelect.options.length; i++) {
                    if (applicationSizeSelect.options[i].getAttribute('data-name') === sublimation.size_name) {
                        applicationSizeSelect.value = applicationSizeSelect.options[i].value;
                        break;
                    }
                }
            }
            
            if (locationSelect) {
                locationSelect.value = sublimation.location_name || '';
            }
            
            if (colorQuantitySelect) {
                colorQuantitySelect.value = sublimation.color_quantity || '1';
            }
            
            document.getElementById('editQuantity').value = sublimation.quantity || '';
            document.getElementById('editUnitPrice').value = sublimation.unit_price || '';

            // Mostrar o modal
            document.getElementById('editSublimationModal').classList.remove('hidden');
        }

        function removeSublimation(index) {
            if (confirm('Tem certeza que deseja remover esta aplicação?')) {
                sublimationsData.splice(index, 1);
                updateSublimationsDisplay();
            }
        }

        function closeEditSublimationModal() {
            document.getElementById('editSublimationModal').classList.add('hidden');
        }

        function updateSublimationsDisplay() {
            // Atualizar o campo hidden do formulário principal
            const sublimationsInput = document.querySelector('input[name="sublimations"]');
            if (sublimationsInput) {
                sublimationsInput.value = JSON.stringify(sublimationsData);
            }

            // Atualizar a exibição das aplicações
            const applicationsContainer = document.querySelector('.space-y-2');
            if (applicationsContainer && sublimationsData.length > 0) {
                applicationsContainer.innerHTML = sublimationsData.map((app, index) => {
                    // Garantir que os valores sejam números
                    const quantity = parseInt(app.quantity) || 0;
                    const unitPrice = parseFloat(app.unit_price) || 0;
                    const subtotal = parseFloat(app.subtotal) || (quantity * unitPrice);
                    
                    // Formatar valores para exibição brasileira
                    const formatPrice = (value) => {
                        return value.toFixed(2).replace('.', ',');
                    };
                    
                    return `
                        <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">${app.size_name} - ${app.location_name}</div>
                                <div class="text-xs text-gray-500">
                                    ${quantity}x R$ ${formatPrice(unitPrice)}
                                    ${app.application_size ? ` • ${app.application_size}` : ''}
                                    ${app.color_quantity && app.color_quantity > 1 ? ` • ${app.color_quantity} cores` : ''}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="text-sm font-medium text-gray-900">R$ ${formatPrice(subtotal)}</div>
                                <button type="button" 
                                        onclick="editSublimation(${index})"
                                        class="p-1 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-all"
                                        title="Editar aplicação">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                        onclick="removeSublimation(${index})"
                                        class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-all"
                                        title="Remover aplicação">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
            } else if (applicationsContainer) {
                applicationsContainer.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Nenhuma aplicação configurada</p>';
            }
        }

        // Manipular o envio do formulário de edição
        document.getElementById('editSublimationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const index = parseInt(document.getElementById('editSublimationIndex').value);
            const formData = new FormData(this);
            
            // Garantir que os valores sejam números válidos
            const quantity = parseInt(formData.get('quantity')) || 0;
            const unitPrice = parseFloat(formData.get('unit_price')) || 0;
            const subtotal = quantity * unitPrice;
            
            // Buscar nome do tamanho selecionado
            const applicationSizeSelect = document.getElementById('editApplicationSize');
            const selectedSizeOption = applicationSizeSelect.options[applicationSizeSelect.selectedIndex];
            const sizeName = selectedSizeOption ? selectedSizeOption.getAttribute('data-name') : '';
            
            // Atualizar os dados
            sublimationsData[index] = {
                size_name: sizeName,
                location_name: formData.get('location_name'),
                application_size: formData.get('application_size'),
                color_quantity: parseInt(formData.get('color_quantity')) || 1,
                quantity: quantity,
                unit_price: unitPrice,
                subtotal: subtotal
            };

            console.log('Dados atualizados:', sublimationsData[index]);
            
            closeEditSublimationModal();
            updateSublimationsDisplay();
        });

        // Fechar modal ao clicar fora
        document.getElementById('editSublimationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditSublimationModal();
            }
        });
    </script>
@endsection