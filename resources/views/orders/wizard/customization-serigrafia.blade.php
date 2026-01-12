@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">S</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Personalização - Serigrafia</h1>
                        <p class="text-sm text-gray-600">Configuração de aplicações serigráficas</p>
                    </div>
                </div>
                <a href="{{ route('orders.wizard.sewing') }}" class="text-gray-600 hover:text-gray-900 text-sm">
                    ← Voltar
                </a>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-1">
                <div class="bg-gray-800 h-1 rounded-full" style="width: 60%"></div>
            </div>
            <div class="flex justify-between mt-2">
                <span class="text-xs text-gray-500">Etapa 3 de 5</span>
                <span class="text-xs text-gray-500">60%</span>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-8">

            <form method="POST" action="{{ route('orders.wizard.customization') }}" id="customization-form" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="total_shirts" value="{{ session('total_shirts', 0) }}" id="total-shirts">
                <input type="hidden" name="sublimations" id="sublimations-data">
                <input type="hidden" name="personalization_type" value="serigrafia">

                <!-- Informações do Pedido -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-2">Informações do Pedido</h3>
                    <p class="text-sm text-gray-700">Total de camisas: <strong id="display-total-shirts" class="text-gray-900">{{ session('total_shirts', 0) }}</strong></p>
                </div>

                <!-- Nome da Arte -->
                <div class="mb-6">
                    <label for="art_name" class="block text-sm font-medium text-gray-900 mb-2">Nome da Arte *</label>
                    <input type="text" id="art_name" name="art_name" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 bg-white"
                           placeholder="Ex: Logo Empresa XYZ">
                </div>


                <!-- Arquivos da Arte -->
                <div class="mb-6">
                    <label for="art_files" class="block text-sm font-medium text-gray-900 mb-2">Arquivos da Arte (múltiplos) *</label>
                    <input type="file" id="art_files" name="art_files[]" multiple
                           required class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 bg-white"
                           onchange="displayFileList()">
                    <p class="text-xs text-gray-600 mt-1">Você pode selecionar múltiplos arquivos (AI, PDF, PNG, JPG, etc) - Obrigatório pelo menos 1 arquivo</p>
                    <div id="file-list" class="mt-3 space-y-2"></div>
                </div>

                <!-- Tamanhos de Aplicação Serigrafia -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Selecione o Tamanho da Aplicação</h3>
                    <div class="grid grid-cols-3 gap-4" id="size-buttons">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                </div>

                <!-- Aplicações Adicionadas -->
                <div id="applications-container" class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aplicações Adicionadas</h3>
                    <p class="text-gray-600 text-sm mb-3" id="no-applications">Nenhuma aplicação adicionada ainda.</p>
                    <div id="applications-list" class="space-y-3">
                    </div>
                </div>

                <!-- Resumo de Valores -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-3">Resumo de Valores</h3>
                    <p class="text-xs text-gray-600 mb-4">* A partir de 3 aplicações: 2 com valor cheio + demais com 50% de desconto</p>
                    <div id="price-breakdown" class="space-y-2 text-sm">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-4 border-t border-gray-300 mt-4">
                        <span class="text-gray-900">Total:</span>
                        <span id="total-price" class="text-gray-900">R$ 0,00</span>
                    </div>
                </div>

                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('orders.wizard.sewing') }}" class="px-6 py-3 text-gray-600 hover:text-gray-900 text-sm">← Voltar</a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm font-medium">
                        Continuar →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para adicionar aplicação -->
    <div id="application-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg border border-gray-200 p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Adicionar Aplicação - <span id="modal-size-name"></span></h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-900 mb-2">Local da Aplicação *</label>
                <select id="modal-location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 bg-white">
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-900 mb-2">Quantidade de Cores * (máx. 6)</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="selectColorCount(1)" class="color-btn p-3 border-2 border-gray-300 rounded hover:border-gray-500 bg-white" data-colors="1">1 Cor</button>
                    <button type="button" onclick="selectColorCount(2)" class="color-btn p-3 border-2 border-gray-300 rounded hover:border-gray-500 bg-white" data-colors="2">2 Cores</button>
                    <button type="button" onclick="selectColorCount(3)" class="color-btn p-3 border-2 border-gray-300 rounded hover:border-gray-500 bg-white" data-colors="3">3 Cores</button>
                    <button type="button" onclick="selectColorCount(4)" class="color-btn p-3 border-2 border-gray-300 rounded hover:border-gray-500 bg-white" data-colors="4">4 Cores</button>
                    <button type="button" onclick="selectColorCount(5)" class="color-btn p-3 border-2 border-gray-300 rounded hover:border-gray-500 bg-white" data-colors="5">5 Cores</button>
                    <button type="button" onclick="selectColorCount(6)" class="color-btn p-3 border-2 border-gray-300 rounded hover:border-gray-500 bg-white" data-colors="6">6 Cores</button>
                </div>
                <input type="hidden" id="modal-color-count" value="1">
            </div>

            <div class="mb-4 space-y-2">
                <p class="text-sm font-semibold text-gray-900">Efeitos Especiais (+50% cada)</p>
                <label class="flex items-center">
                    <input type="checkbox" id="modal-effect-dourado" onchange="updateModalPrices()"
                           class="h-4 w-4 text-gray-800 focus:ring-gray-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm font-medium text-gray-900">Dourado</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" id="modal-effect-prata" onchange="updateModalPrices()"
                           class="h-4 w-4 text-gray-800 focus:ring-gray-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm font-medium text-gray-900">Prata</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" id="modal-effect-neon" onchange="updateModalPrices()"
                           class="h-4 w-4 text-gray-800 focus:ring-gray-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm font-medium text-gray-900">Neon</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-900 mb-2">Quantidade de Aplicações *</label>
                <input type="number" id="modal-quantity" min="1" value="1" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 bg-white">
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                <p class="text-sm text-gray-700">Valor base: <strong id="modal-base-price" class="text-gray-900">R$ 0,00</strong></p>
                <p class="text-sm text-gray-700" id="neon-info" style="display: none;">Acréscimo neon: <strong id="modal-neon-price" class="text-gray-900">R$ 0,00</strong></p>
                <p class="text-sm text-gray-700">Valor unitário: <strong id="modal-unit-price" class="text-gray-900">R$ 0,00</strong></p>
                <p class="text-sm text-gray-700">Subtotal: <strong id="modal-subtotal" class="text-gray-900">R$ 0,00</strong></p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" 
                        class="px-6 py-2 text-gray-600 hover:text-gray-900 text-sm">
                    Cancelar
                </button>
                <button type="button" onclick="addApplication()" 
                        class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 text-sm font-medium">
                    Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        let sizes = [];
        let locations = [];
        let colors = [];
        let applications = [];
        let totalShirts = {{ session('total_shirts', 0) }};
        let currentSize = '';
        let selectedColorCount = 1;

        // Apenas A4, ESCUDO e A3 para Serigrafia
        const allowedSizes = ['A4', 'ESCUDO', 'A3'];

        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });

        function loadData() {
            Promise.all([
                fetch('/api/sublimation-sizes').then(r => r.json()),
                fetch('/api/sublimation-locations').then(r => r.json()),
                fetch('/api/serigraphy-colors').then(r => r.json())
            ]).then(([sizesData, locationsData, colorsData]) => {
                sizes = sizesData.filter(s => allowedSizes.includes(s.name));
                locations = locationsData;
                colors = colorsData;
                renderSizeButtons();
                renderLocationOptions();
                renderColorButtons();
            });
        }

        function renderColorButtons() {
            // Atualizar botões de cores com preços
            colors.forEach(color => {
                const colorCount = color.id;
                const btn = document.querySelector(`[data-colors="${colorCount}"]`);
                if (btn && color.price > 0) {
                    btn.innerHTML = `${color.name}<br><span class="text-xs text-gray-600">+R$ ${parseFloat(color.price).toFixed(2).replace('.', ',')}</span>`;
                }
            });
        }

        function renderSizeButtons() {
            const container = document.getElementById('size-buttons');
            container.innerHTML = sizes.map(size => `
                <button type="button" onclick="openModal(${size.id})" 
                        class="p-4 border-2 border-gray-300 rounded-lg hover:border-gray-500 hover:bg-gray-50 transition text-center bg-white">
                    <div class="font-bold text-lg text-gray-900">${size.name}</div>
                    <div class="text-sm text-gray-600">${size.dimensions || ''}</div>
                    <div class="text-xs text-gray-800 mt-2" id="price-${size.id}">Carregando...</div>
                </button>
            `).join('');

            sizes.forEach(size => {
                fetch(`/api/sublimation-price/${size.id}/${totalShirts}`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById(`price-${size.id}`).textContent = 
                            `R$ ${parseFloat(data.price).toFixed(2).replace('.', ',')}`;
                    });
            });
        }

        function renderLocationOptions() {
            const select = document.getElementById('modal-location');
            select.innerHTML = '<option value="">Selecione</option>' + 
                locations.map(loc => `<option value="${loc.id}">${loc.name}</option>`).join('');
        }

        function openModal(sizeId) {
            currentSize = sizes.find(s => s.id === sizeId);
            const dimensions = currentSize.dimensions || '';
            document.getElementById('modal-size-name').textContent = dimensions ? `${currentSize.name} (${dimensions})` : currentSize.name;
            document.getElementById('modal-quantity').value = 1;
            document.getElementById('modal-color-count').value = 1;
            ['modal-effect-dourado','modal-effect-prata','modal-effect-neon'].forEach(id => { const el = document.getElementById(id); if (el) el.checked = false; });
            selectedColorCount = 1;
            
            // Reset color buttons
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.classList.remove('border-gray-800', 'bg-gray-100');
            });
            document.querySelector('[data-colors="1"]').classList.add('border-gray-800', 'bg-gray-100');
            
            fetch(`/api/sublimation-price/${sizeId}/${totalShirts}`)
                .then(r => r.json())
                .then(data => {
                    currentSize.price = parseFloat(data.price);
                    updateModalPrices();
                });

            document.getElementById('application-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('application-modal').classList.add('hidden');
            document.getElementById('modal-location').value = '';
            document.getElementById('modal-quantity').value = 1;
        }

        function selectColorCount(count) {
            selectedColorCount = count;
            document.getElementById('modal-color-count').value = count;
            
            // Update button styles
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.classList.remove('border-gray-800', 'bg-gray-100');
            });
            document.querySelector(`[data-colors="${count}"]`).classList.add('border-gray-800', 'bg-gray-100');
            
            updateModalPrices();
        }

        function updateModalPrices() {
            const quantity = parseInt(document.getElementById('modal-quantity').value) || 1;
            const effectsSelected = [
                document.getElementById('modal-effect-dourado').checked,
                document.getElementById('modal-effect-prata').checked,
                document.getElementById('modal-effect-neon').checked,
            ].filter(Boolean).length;
            const basePrice = currentSize.price;
            
            // Buscar preço das cores selecionadas
            const colorData = colors.find(c => c.id === selectedColorCount);
            const colorPrice = colorData ? parseFloat(colorData.price) : 0;
            
            // Calcular acréscimo neon (50% do valor base do tamanho)
            const neonSurcharge = hasNeon ? (basePrice * 0.5) : 0;
            
            // Preço unitário = base + cores + neon
            const unitPrice = basePrice + colorPrice + neonSurcharge;
            const subtotal = unitPrice * quantity;

            document.getElementById('modal-base-price').textContent = `R$ ${(basePrice + colorPrice).toFixed(2).replace('.', ',')}`;
            
            if (effectsSelected > 0) {
                document.getElementById('neon-info').style.display = 'block';
                document.getElementById('modal-neon-price').textContent = `R$ ${neonSurcharge.toFixed(2).replace('.', ',')}`;
            } else {
                document.getElementById('neon-info').style.display = 'none';
            }
            
            document.getElementById('modal-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('modal-subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        }

        document.getElementById('modal-quantity').addEventListener('input', updateModalPrices);


        function displayFileList() {
            const fileInput = document.getElementById('art_files');
            const fileList = document.getElementById('file-list');
            const files = Array.from(fileInput.files);

            if (files.length === 0) {
                fileList.innerHTML = '';
                return;
            }

            fileList.innerHTML = files.map((file) => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded border">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <div class="text-sm font-medium">${file.name}</div>
                            <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function addApplication() {
            const locationId = parseInt(document.getElementById('modal-location').value);
            const quantity = parseInt(document.getElementById('modal-quantity').value);
            const colorCount = selectedColorCount;
            const effectsSelected = [
                document.getElementById('modal-effect-dourado').checked,
                document.getElementById('modal-effect-prata').checked,
                document.getElementById('modal-effect-neon').checked,
            ].filter(Boolean).length;

            if (!locationId || !quantity || !colorCount) {
                alert('Por favor, preencha todos os campos.');
                return;
            }

            const location = locations.find(l => l.id === locationId);
            const basePrice = currentSize.price;
            
            // Buscar preço das cores
            const colorData = colors.find(c => c.id === colorCount);
            const colorPrice = colorData ? parseFloat(colorData.price) : 0;
            
            // Calcular neon (50% do valor base do tamanho)
            const neonSurcharge = hasNeon ? (basePrice * 0.5) : 0;
            
            // Preço unitário = base + cores + neon
            const unitPrice = basePrice + colorPrice + neonSurcharge;

            applications.push({
                size_id: currentSize.id,
                size_name: currentSize.name,
                size_dimensions: currentSize.dimensions,
                location_id: locationId,
                location_name: location.name,
                quantity: quantity,
                color_count: colorCount,
                effects_count: effectsSelected, effects_surcharge: neonSurcharge, effects_labels: effectsSelected > 0 ? ['Dourado','Prata','Neon'].filter((_,idx) => [document.getElementById('modal-effect-dourado').checked, document.getElementById('modal-effect-prata').checked, document.getElementById('modal-effect-neon').checked][idx]) : [],
                unit_price: unitPrice
            });

            closeModal();
            renderApplications();
            calculatePrices();
        }

        function removeApplication(index) {
            applications.splice(index, 1);
            renderApplications();
            calculatePrices();
        }

        function renderApplications() {
            const container = document.getElementById('applications-list');
            const noApps = document.getElementById('no-applications');

            if (!container || !noApps) return;

            if (applications.length === 0) {
                noApps.style.display = 'block';
                container.innerHTML = '';
                return;
            }

            noApps.style.display = 'none';
            
            const html = applications.map((app, index) => {
                const effectsText = app.effects_labels && app.effects_labels.length ? ` + ${app.effects_labels.join(', ')}` : '';
                return `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${app.location_name}: ${app.quantity}x ${app.size_name} (${app.size_dimensions}) - ${app.color_count} ${app.color_count === 1 ? 'Cor' : 'Cores'}${effectsText}</div>
                        <div class="text-sm text-gray-600">R$ ${app.unit_price.toFixed(2).replace('.', ',')} cada</div>
                    </div>
                    <button type="button" onclick="removeApplication(${index})" 
                            class="text-gray-600 hover:text-gray-800 px-3 py-1 text-sm">
                        Remover
                    </button>
                </div>
            `}).join('');
            
            container.innerHTML = html;
        }

        function calculatePrices() {
            if (applications.length === 0) {
                document.getElementById('price-breakdown').innerHTML = '<p class="text-gray-500 text-sm">Nenhuma aplicação adicionada.</p>';
                document.getElementById('total-price').textContent = 'R$ 0,00';
                document.getElementById('sublimations-data').value = '';
                return;
            }

            // Calcular subtotais
            const items = applications.map(app => ({
                ...app,
                subtotal: app.unit_price * app.quantity
            }));

            // Ordenar por subtotal (maior primeiro)
            items.sort((a, b) => b.subtotal - a.subtotal);

            // Aplicar desconto: se >= 3 aplicações, 2 primeiras integrais, demais 50% off
            let total = 0;
            const breakdown = items.map((item, index) => {
                let discount = 0;
                if (applications.length >= 3 && index >= 2) {
                    discount = 50;
                }
                
                const finalPrice = item.subtotal * (1 - discount / 100);
                total += finalPrice;

                item.discount_percent = discount;
                item.final_price = finalPrice;

                return `
                    <div class="flex justify-between">
                        <span class="text-gray-700">${item.location_name} - ${item.quantity}x ${item.size_name} (${item.color_count} ${item.color_count === 1 ? 'cor' : 'cores'})${discount > 0 ? ` (-${discount}%)` : ''}</span>
                        <span class="${discount > 0 ? 'text-gray-900' : 'text-gray-900'}">R$ ${finalPrice.toFixed(2).replace('.', ',')}</span>
                    </div>
                `;
            }).join('');

            document.getElementById('price-breakdown').innerHTML = breakdown;
            document.getElementById('total-price').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;

            // Atualizar campo hidden com dados das aplicações
            document.getElementById('sublimations-data').value = JSON.stringify(items);
        }
</script>
@endpush
@endsection
