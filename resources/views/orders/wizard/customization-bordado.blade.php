@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-[#7c3aed]">Etapa 3 de 5</span>
                <span class="text-sm text-gray-500">Personalização - Bordado</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-[#7c3aed] h-2 rounded-full" style="width: 60%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold mb-6">Personalização - Bordado</h1>

            <form method="POST" action="{{ route('orders.wizard.customization') }}" id="customization-form" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="total_shirts" value="{{ session('total_shirts', 0) }}" id="total-shirts">
                <input type="hidden" name="sublimations" id="sublimations-data">
                <input type="hidden" name="personalization_type" value="bordado">

                <!-- Informações do Pedido -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
                    <h3 class="font-semibold mb-2">Informações do Pedido</h3>
                    <p class="text-sm">Total de camisas: <strong id="display-total-shirts">{{ session('total_shirts', 0) }}</strong></p>
                </div>

                <!-- Nome da Arte -->
                <div class="mb-6">
                    <label for="art_name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Arte *</label>
                    <input type="text" id="art_name" name="art_name" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#7c3aed] focus:ring-[#7c3aed]"
                           placeholder="Ex: Logo Empresa XYZ">
                </div>


                <!-- Arquivos da Arte -->
                <div class="mb-6">
                    <label for="art_files" class="block text-sm font-medium text-gray-700 mb-2">Arquivos da Arte (múltiplos) *</label>
                    <input type="file" id="art_files" name="art_files[]" multiple
                           required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#7c3aed] focus:ring-[#7c3aed]"
                           onchange="displayFileList()">
                    <p class="text-xs text-gray-500 mt-1">Você pode selecionar múltiplos arquivos (AI, PDF, PNG, JPG, DST, etc) - Obrigatório pelo menos 1 arquivo</p>
                    <div id="file-list" class="mt-3 space-y-2"></div>
                </div>

                <!-- Tamanhos de Aplicação Bordado -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Selecione o Tamanho da Aplicação</h3>
                    <div class="grid grid-cols-3 gap-4" id="size-buttons">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                </div>

                <!-- Aplicações Adicionadas -->
                <div id="applications-container" class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Aplicações Adicionadas</h3>
                    <p class="text-gray-500 text-sm mb-3" id="no-applications">Nenhuma aplicação adicionada ainda.</p>
                    <div id="applications-list" class="space-y-3">
                    </div>
                </div>

                <!-- Resumo de Valores -->
                <div class="bg-green-50 rounded-lg p-4 border border-green-200 mb-6">
                    <h3 class="font-semibold mb-3">Resumo de Valores</h3>
                    <div id="price-breakdown" class="space-y-2 text-sm">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-3 border-t mt-3">
                        <span>Total:</span>
                        <span id="total-price">R$ 0,00</span>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <a href="{{ route('orders.wizard.sewing') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">← Voltar</a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[#7c3aed] text-white rounded-md hover:bg-[#7c3aed] focus:outline-none focus:ring-2 focus:ring-[#7c3aed]">
                        Continuar →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para adicionar aplicação -->
    <div id="application-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold mb-4">Adicionar Aplicação - <span id="modal-size-name"></span></h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Local da Aplicação *</label>
                <select id="modal-location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#7c3aed] focus:ring-[#7c3aed]">
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade de Aplicações *</label>
                <input type="number" id="modal-quantity" min="1" value="1" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#7c3aed] focus:ring-[#7c3aed]">
            </div>

            <div class="bg-gray-50 rounded p-3 mb-4">
                <p class="text-sm text-gray-600">Valor unitário: <strong id="modal-unit-price">R$ 0,00</strong></p>
                <p class="text-sm text-gray-600">Subtotal: <strong id="modal-subtotal">R$ 0,00</strong></p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="button" onclick="addApplication()" 
                        class="px-4 py-2 bg-[#7c3aed] text-white rounded-md hover:bg-[#7c3aed]">
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
        let applications = [];
        let totalShirts = {{ session('total_shirts', 0) }};
        let currentSize = '';

        // Apenas A4, ESCUDO e NOME para Bordado
        const allowedSizes = ['A4', 'ESCUDO', 'NOME'];

        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });

        function loadData() {
            Promise.all([
                fetch('/api/sublimation-sizes').then(r => r.json()),
                fetch('/api/sublimation-locations').then(r => r.json())
            ]).then(([sizesData, locationsData]) => {
                // Filtrar apenas os tamanhos permitidos para Bordado
                sizes = sizesData.filter(s => allowedSizes.includes(s.name));
                locations = locationsData;
                renderSizeButtons();
                renderLocationOptions();
            });
        }

        function renderSizeButtons() {
            const container = document.getElementById('size-buttons');
            container.innerHTML = sizes.map(size => `
                <button type="button" onclick="openModal(${size.id})" 
                        class="p-4 border-2 border-gray-300 rounded-lg hover:border-[#7c3aed] hover:bg-purple-50 transition text-center">
                    <div class="font-bold text-lg">${size.name}</div>
                    <div class="text-sm text-gray-600">${size.dimensions || ''}</div>
                    <div class="text-xs text-[#7c3aed] mt-2" id="price-${size.id}">Carregando...</div>
                </button>
            `).join('');

            // Buscar preços para a quantidade atual
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

        function updateModalPrices() {
            const quantity = parseInt(document.getElementById('modal-quantity').value) || 1;
            const unitPrice = currentSize.price;
            const subtotal = unitPrice * quantity;

            document.getElementById('modal-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('modal-subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        }

        document.getElementById('modal-quantity').addEventListener('input', updateModalPrices);

        function previewCoverImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('cover-preview-img').src = e.target.result;
                    document.getElementById('cover-preview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function displayFileList() {
            const fileInput = document.getElementById('art_files');
            const fileList = document.getElementById('file-list');
            const files = Array.from(fileInput.files);

            if (files.length === 0) {
                fileList.innerHTML = '';
                return;
            }

            fileList.innerHTML = files.map((file, index) => `
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

            if (!locationId || !quantity) {
                alert('Por favor, preencha todos os campos.');
                return;
            }

            const location = locations.find(l => l.id === locationId);

            applications.push({
                size_id: currentSize.id,
                size_name: currentSize.name,
                size_dimensions: currentSize.dimensions,
                location_id: locationId,
                location_name: location.name,
                quantity: quantity,
                unit_price: currentSize.price
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

            if (!container || !noApps) {
                return;
            }

            if (applications.length === 0) {
                noApps.style.display = 'block';
                container.innerHTML = '';
                return;
            }

            noApps.style.display = 'none';
            
            const html = applications.map((app, index) => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                    <div class="flex-1">
                        <div class="font-medium">${app.location_name}: ${app.quantity}x ${app.size_name} (${app.size_dimensions})</div>
                        <div class="text-sm text-gray-600">R$ ${app.unit_price.toFixed(2).replace('.', ',')} cada</div>
                    </div>
                    <button type="button" onclick="removeApplication(${index})" 
                            class="text-red-600 hover:text-red-800 px-3 py-1">
                        Remover
                    </button>
                </div>
            `).join('');
            
            container.innerHTML = html;
        }

        function calculatePrices() {
            if (applications.length === 0) {
                document.getElementById('price-breakdown').innerHTML = '<p class="text-gray-500 text-sm">Nenhuma aplicação adicionada.</p>';
                document.getElementById('total-price').textContent = 'R$ 0,00';
                document.getElementById('sublimations-data').value = '';
                return;
            }

            // Calcular subtotais SEM DESCONTO para Bordado
            const items = applications.map(app => {
                const subtotal = app.unit_price * app.quantity;
                return {
                    ...app,
                    subtotal: subtotal,
                    discount_percent: 0, // SEM DESCONTO
                    final_price: subtotal // Preço final = subtotal (sem desconto)
                };
            });

            // Calcular total
            let total = 0;
            const breakdown = items.map((item) => {
                total += item.final_price;

                return `
                    <div class="flex justify-between">
                        <span>${item.location_name} - ${item.quantity}x ${item.size_name}</span>
                        <span>R$ ${item.final_price.toFixed(2).replace('.', ',')}</span>
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
