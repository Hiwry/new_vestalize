@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
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

    @if($errors->any())
    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $types[$type] }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure preços por tamanho e quantidade</p>
        </div>

        <form method="POST" action="{{ route('admin.personalization-prices.update', $type) }}" id="prices-form">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Gerenciamento de Tamanhos -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Tamanhos Disponíveis</h3>
                        </div>
                        <button type="button" onclick="openAddSizeModal()" 
                                class="inline-flex items-center px-3 py-2 bg-green-600 dark:bg-green-600 hover:bg-green-700 dark:hover:bg-green-700 text-white stay-white text-sm font-medium rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar Tamanho
                        </button>
                    </div>
                    
                    <div id="sizes-container" class="flex flex-wrap gap-2.5">
                        <!-- Tamanhos serão carregados aqui -->
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300" id="quantity-ranges-count">0 faixas de quantidade</span>
                    <button type="button" onclick="addNewQuantityRange()" 
                            class="inline-flex items-center px-3 py-2 bg-green-600 dark:bg-green-600 hover:bg-green-700 dark:hover:bg-green-700 text-white stay-white text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Faixa
                    </button>
                </div>

                <!-- Tabela de Preços -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="prices-table">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">DE</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">ATÉ</th>
                                <!-- Colunas de tamanhos serão adicionadas dinamicamente -->
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody id="prices-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Linhas serão adicionadas dinamicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Botões de Ação -->
                <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium bg-indigo-600 dark:bg-indigo-600 text-white stay-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar Preços
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Adicionar Tamanho -->
<div id="addSizeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/50 max-w-md w-full">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Adicionar Tamanho</h3>
                <button type="button" onclick="closeAddSizeModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-4">
            <label for="newSizeName" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">
                Nome do Tamanho
            </label>
            <input type="text" 
                   id="newSizeName" 
                   placeholder="Ex: Cacharrel, PP, A5, A1"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Digite o nome do novo tamanho (ex: Cacharrel, PP, A5)
            </p>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg flex items-center justify-end space-x-3">
            <button type="button" 
                    onclick="closeAddSizeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </button>
            <button type="button" 
                    onclick="confirmAddSize()"
                    class="px-5 py-2 text-sm font-medium text-white stay-white bg-indigo-600 dark:bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-700 rounded-md transition-colors">
                Adicionar
            </button>
        </div>
    </div>
</div>

{{-- Scripts moved to content for AJAX execution --}}
<script>
    // Dados iniciais
    let availableSizes = []; // Começa vazio, será preenchido com dados do banco ou pelo usuário
    let quantityRanges = []; // Faixas de quantidade
    let priceRowIndex = 0;

    // Inicializar página
    function initPricesPage() {
        const form = document.getElementById('prices-form');
        if (!form || form.dataset.initialized === 'true') return;
        form.dataset.initialized = 'true';

        // Resetar dados para evitar duplicidade em navegacao AJAX
        availableSizes = [];
        quantityRanges = [];
        priceRowIndex = 0;

        loadExistingData();
        renderSizes();
        renderTable();
        bindPricePageEvents();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPricesPage);
    } else {
        initPricesPage();
    }
    document.addEventListener('ajax-content-loaded', initPricesPage);

    function loadExistingData() {
        console.log('=== CARREGANDO DADOS EXISTENTES ===');
        console.log('Prices count:', {{ $prices->count() }});
        
        @if($prices->count() > 0)
            console.log('Dados encontrados, processando...');
            
            const pricesData = @json($prices);
            console.log('Raw prices data:', pricesData);
            
            // Agrupar preços por faixa de quantidade
            const priceGroups = {};
            
            pricesData.forEach(function(price) {
                const key = price.quantity_from + '_' + (price.quantity_to || 'null');
                console.log('Processing price:', price);
                
                if (!priceGroups[key]) {
                    priceGroups[key] = {
                        quantity_from: price.quantity_from,
                        quantity_to: price.quantity_to,
                        prices: {},
                        costs: {}
                    };
                }
                priceGroups[key].prices[price.size_name] = price.price;
                priceGroups[key].costs[price.size_name] = price.cost;
            });
            
            console.log('Price groups after processing:', priceGroups);
            
            // Converter para array
            Object.values(priceGroups).forEach((group, index) => {
                quantityRanges.push({
                    id: index,
                    quantity_from: group.quantity_from,
                    quantity_to: group.quantity_to,
                    prices: group.prices,
                    costs: group.costs
                });
            });
            
            // Extrair tamanhos únicos dos dados existentes
            const existingSizes = new Set();
            pricesData.forEach(function(price) {
                existingSizes.add(price.size_name);
            });
            
            // Atualizar lista de tamanhos disponíveis
            availableSizes = Array.from(existingSizes);
            
            console.log('Final available sizes:', availableSizes);
            console.log('Final quantity ranges:', quantityRanges);
        @else
            console.log('Nenhum dado encontrado');
        @endif
        updateQuantityRangesCount();
    }

    function renderSizes() {
        const container = document.getElementById('sizes-container');
        container.innerHTML = '';
        
        if (availableSizes.length === 0) {
            container.innerHTML = `
                <div class="w-full text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum tamanho adicionado</p>
                </div>
            `;
            return;
        }
        
        availableSizes.forEach(size => {
            const sizeElement = document.createElement('div');
            sizeElement.className = 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500 rounded-lg px-4 py-2.5 shadow-sm hover:shadow-md transition-all';
            sizeElement.innerHTML = `
                <span class="text-sm font-semibold text-gray-900 dark:text-white">${size}</span>
                <button type="button" onclick="removeSize('${size}')" 
                        class="text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(sizeElement);
        });
    }

    function renderTable() {
        const table = document.getElementById('prices-table');
        const tbody = document.getElementById('prices-tbody');
        
        // Atualizar cabeçalho
        const headerRow = table.querySelector('thead tr');
        headerRow.innerHTML = `
            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">DE</th>
            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">ATÉ</th>
        `;
        
        availableSizes.forEach(size => {
            const th = document.createElement('th');
            th.className = 'px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider';
            th.textContent = size;
            headerRow.appendChild(th);
        });
        
        headerRow.innerHTML += '<th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">AÇÕES</th>';
        
        // Renderizar linhas
        tbody.innerHTML = '';
        quantityRanges.forEach((range, index) => {
            const row = createPriceRow(range, index);
            tbody.appendChild(row);
        });
    }

    function createPriceRow(range, index) {
        const row = document.createElement('tr');
        row.className = 'price-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors';
        
        let html = `
            <td class="px-4 py-3.5">
                <input type="number" name="prices[${index}][quantity_from]" 
                       value="${range.quantity_from}" min="1" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
            </td>
            <td class="px-4 py-3.5">
                <input type="number" name="prices[${index}][quantity_to]" 
                       value="${range.quantity_to || ''}" min="1"
                       placeholder="∞"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
            </td>
        `;
        
        availableSizes.forEach(size => {
            const price = range.prices[size] || '';
            const cost = (range.costs && range.costs[size]) || '';
            
            html += `
                <td class="px-4 py-3.5">
                    <div class="flex flex-col gap-2">
                        <div class="relative group">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-xs">R$</span>
                            <input type="number" name="prices[${index}][${size.toLowerCase()}][price]" 
                                   value="${price}" step="0.01" min="0"
                                   placeholder="Preço"
                                   title="Preço de Venda"
                                   class="w-full pl-11 pr-3 py-1.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all placeholder-gray-400">
                        </div>
                        <div class="relative group">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-red-500 dark:text-red-400 text-xs">R$</span>
                            <input type="number" name="prices[${index}][${size.toLowerCase()}][cost]" 
                                   value="${cost}" step="0.01" min="0"
                                   placeholder="Custo"
                                   title="Preço de Custo"
                                   class="w-full pl-11 pr-3 py-1.5 border border-red-200 dark:border-red-900/30 rounded-lg text-sm bg-red-50 dark:bg-red-900/10 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 transition-all placeholder-gray-400">
                        </div>
                    </div>
                </td>
            `;
        });
        
        html += `
            <td class="px-4 py-3.5">
                <button type="button" onclick="removePriceRow(${index})" 
                        class="inline-flex items-center p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        
        row.innerHTML = html;
        return row;
    }

    function updateQuantityRangesCount() {
        document.getElementById('quantity-ranges-count').textContent = 
            `${quantityRanges.length} faixa${quantityRanges.length !== 1 ? 's' : ''} de quantidade`;
    }

    // Funções do Modal
    function openAddSizeModal() {
        document.getElementById('addSizeModal').classList.remove('hidden');
        document.getElementById('newSizeName').value = '';
        document.getElementById('newSizeName').focus();
    }

    function closeAddSizeModal() {
        document.getElementById('addSizeModal').classList.add('hidden');
    }

    function confirmAddSize() {
        const input = document.getElementById('newSizeName');
        const newSize = input.value.trim().toUpperCase();
        
        if (!newSize) {
            input.focus();
            return;
        }
        
        if (availableSizes.includes(newSize)) {
            alert('Este tamanho já existe!');
            input.focus();
            return;
        }
        
        availableSizes.push(newSize);
        renderSizes();
        renderTable();
        closeAddSizeModal();
    }

    function removeSize(sizeName) {
        if (confirm(`Deseja remover o tamanho "${sizeName}"?`)) {
            availableSizes = availableSizes.filter(s => s !== sizeName);
            
            // Remover preços e custos deste tamanho de todas as faixas
            quantityRanges.forEach(range => {
                delete range.prices[sizeName];
                if (range.costs) delete range.costs[sizeName];
            });
            
            renderSizes();
            renderTable();
        }
    }

    function addNewQuantityRange() {
        const newRange = {
            id: priceRowIndex++,
            quantity_from: '',
            quantity_to: '',
            prices: {},
            costs: {}
        };
        quantityRanges.push(newRange);
        renderTable();
        updateQuantityRangesCount();
    }

    function removePriceRow(index) {
        if (confirm('Deseja remover esta faixa de quantidade?')) {
            quantityRanges.splice(index, 1);
            renderTable();
            updateQuantityRangesCount();
        }
    }
    function bindPricePageEvents() {
        const sizeInput = document.getElementById('newSizeName');
        if (sizeInput && !sizeInput.dataset.listenerAttached) {
            sizeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    confirmAddSize();
                }
            });
            sizeInput.dataset.listenerAttached = 'true';
        }

        if (!document.body.dataset.priceModalEscAttached) {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAddSizeModal();
                }
            });
            document.body.dataset.priceModalEscAttached = 'true';
        }
    }


    // Expor funções para navegação AJAX
    window.openAddSizeModal = openAddSizeModal;
    window.closeAddSizeModal = closeAddSizeModal;
    window.confirmAddSize = confirmAddSize;
    window.removeSize = removeSize;
    window.addNewQuantityRange = addNewQuantityRange;
    window.removePriceRow = removePriceRow;
</script>
{{-- endpush --}}
@endsection
