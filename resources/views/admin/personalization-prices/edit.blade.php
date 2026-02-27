@extends('layouts.admin')

@push('styles')
<style>
    .size-pill {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .size-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    .price-input-sale {
        border-color: #d1d5db;
        background-color: #ffffff;
    }
    .price-input-sale:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .price-input-cost {
        border-color: #e5e7eb;
        background-color: #f9fafb;
    }
    .price-input-cost:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    .dark .price-input-sale {
        border-color: #4b5563;
        background-color: #1f2937;
    }
    .dark .price-input-sale:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
    }
    .dark .price-input-cost {
        border-color: #374151;
        background-color: #111827;
    }
    .dark .price-input-cost:focus {
        border-color: #fbbf24;
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.15);
    }
    .table-header-group {
        border-bottom: 2px solid #e5e7eb;
    }
    .dark .table-header-group {
        border-bottom: 2px solid #374151;
    }
    .price-row:hover {
        background-color: #f8fafc;
    }
    .dark .price-row:hover {
        background-color: rgba(55, 65, 81, 0.3);
    }
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
    .qty-col {
        width: 74px !important;
        max-width: 74px !important;
        min-width: 66px !important;
    }
    .actions-col {
        width: 46px !important;
        max-width: 46px !important;
        min-width: 42px !important;
    }
    .size-col {
        width: 182px !important;
        min-width: 182px !important;
    }
    #prices-table {
        width: auto !important;
        table-layout: fixed !important;
    }
    #prices-table thead {
        display: table-header-group !important;
    }
    #prices-table tbody {
        display: table-row-group !important;
    }
    #prices-table tr {
        display: table-row !important;
    }
    #prices-table th,
    #prices-table td {
        display: table-cell !important;
    }
    #prices-table col.qty-col-fixed {
        width: 74px !important;
    }
    #prices-table col.size-col-fixed {
        width: 182px !important;
    }
    #prices-table col.actions-col-fixed {
        width: 46px !important;
    }
    /* Aggressive overrides for Avento global theme */
    #prices-table input.w-full {
        padding: 5px 8px !important;
        height: 34px !important;
        min-height: 34px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
    }
    #prices-table .qty-col input {
        padding: 6px 4px !important;
        width: 62px !important;
        min-width: 62px !important;
        max-width: 62px !important;
        margin: 0 auto !important;
        display: block !important;
    }
    #prices-table th.qty-col,
    #prices-table td.qty-col {
        padding-left: 4px !important;
        padding-right: 4px !important;
    }
    #prices-table thead th:nth-child(1),
    #prices-table thead th:nth-child(2),
    #prices-table tbody td:nth-child(1),
    #prices-table tbody td:nth-child(2) {
        width: 1% !important;
        min-width: 74px !important;
        max-width: 74px !important;
        white-space: nowrap !important;
    }
    #prices-table td {
        height: 48px !important;
        padding-top: 3px !important;
        padding-bottom: 3px !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Preços de Personalização
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
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
    <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-600/30 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl fade-in">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl fade-in">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl fade-in">
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/50">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center flex-shrink-0 border border-indigo-100 dark:border-indigo-800/50 shadow-sm">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">{{ $types[$type] }}</h2>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">Gestão de precificação por quantidade e faixas de aplicação</p>
                </div>
            </div>

        </div>

        <form method="POST" action="{{ route('admin.personalization-prices.update', $type) }}" id="prices-form">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">

                <!-- Gerenciamento de Tamanhos -->
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-5 py-4 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 bg-violet-100 dark:bg-violet-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tamanhos Disponíveis</h3>
                            </div>
                            <button type="button" onclick="openAddSizeModal()" 
                                    class="inline-flex items-center px-3.5 py-2 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white stay-white text-xs font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Tamanho
                            </button>
                        </div>
                    </div>
                    
                    <div class="px-5 py-4">
                        <div id="sizes-container" class="flex flex-wrap gap-2.5">
                            <!-- Tamanhos serão carregados aqui -->
                        </div>
                    </div>
                </div>

                <!-- Faixas de Quantidade Bar -->
                <div class="flex items-center justify-between px-5 py-3.5 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300" id="quantity-ranges-count">0 faixas de quantidade</span>
                    </div>
                    <button type="button" onclick="addNewQuantityRange()" 
                            class="inline-flex items-center px-3.5 py-2 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white stay-white text-xs font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Faixa
                    </button>
                </div>

                <!-- Tabela de Preços -->
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-max" id="prices-table" style="table-layout: fixed !important;">
                        <colgroup id="prices-colgroup">
                            <col class="qty-col-fixed">
                            <col class="qty-col-fixed">
                            <col class="actions-col-fixed">
                        </colgroup>
                        <thead>
                            <!-- Linha principal de cabeçalho -->
                            <tr class="bg-gray-50 dark:bg-gray-700/50 table-header-group">
                                <th class="qty-col px-2 py-2.5 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qtd. De</th>
                                <th class="qty-col px-2 py-2.5 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qtd. Até</th>
                                <!-- Colunas de tamanhos serão adicionadas dinamicamente -->
                                <th class="actions-col px-1.5 py-2.5 text-center text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="prices-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/50">
                            <!-- Linhas serão adicionadas dinamicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Legenda -->
                <div class="flex items-center gap-5 text-xs text-gray-500 dark:text-gray-400 px-1">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600"></span>
                        <span>Preço de Venda</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700"></span>
                        <span>Preço de Custo</span>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-gray-100 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold bg-indigo-600 dark:bg-indigo-500 text-white stay-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all duration-200 shadow-sm hover:shadow-md">
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
<div id="addSizeModal" class="hidden fixed inset-0 bg-black/40 dark:bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl dark:shadow-gray-900/50 max-w-md w-full border border-gray-200 dark:border-gray-700 transform transition-all">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Adicionar Tamanho</h3>
                </div>
                <button type="button" onclick="closeAddSizeModal()" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-5">
            <label for="newSizeName" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 uppercase tracking-wider">
                Nome do Tamanho
            </label>
            <input type="text" 
                   id="newSizeName" 
                   placeholder="Ex: Cacharrel, PP, A5, A1"
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all placeholder-gray-400 dark:placeholder-gray-500">
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                O tamanho será adicionado como coluna na tabela de preços.
            </p>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex items-center justify-end space-x-3">
            <button type="button" 
                    onclick="closeAddSizeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </button>
            <button type="button" 
                    onclick="confirmAddSize()"
                    class="px-5 py-2 text-sm font-semibold text-white stay-white bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
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
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Nenhum tamanho adicionado</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Clique em "Adicionar Tamanho" para começar</p>
                </div>
            `;
            return;
        }
        
        availableSizes.forEach(size => {
            const sizeElement = document.createElement('div');
            sizeElement.className = 'size-pill inline-flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/50 rounded-lg px-3.5 py-2';
            sizeElement.innerHTML = `
                <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">${size}</span>
                <button type="button" onclick="removeSize('${size}')" 
                        class="text-indigo-400 dark:text-indigo-500 hover:text-red-500 dark:hover:text-red-400 transition-colors p-0.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(sizeElement);
        });
    }

    function renderTable() {
        const table = document.getElementById('prices-table');
        const tbody = document.getElementById('prices-tbody');
        const colgroup = document.getElementById('prices-colgroup');
        
        // Atualizar cabeçalho
        const headerRow = table.querySelector('thead tr');
        
        let colgroupHtml = `
            <col class="qty-col-fixed">
            <col class="qty-col-fixed">
        `;
        availableSizes.forEach(() => {
            colgroupHtml += `<col class="size-col-fixed">`;
        });
        colgroupHtml += `<col class="actions-col-fixed">`;
        colgroup.innerHTML = colgroupHtml;

        const tableWidth = (74 * 2) + (182 * availableSizes.length) + 46;
        table.style.width = `${tableWidth}px`;
        table.style.minWidth = `${tableWidth}px`;
        
        headerRow.innerHTML = `
            <th class="qty-col px-2 py-2.5 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qtd. De</th>
            <th class="qty-col px-2 py-2.5 text-left text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qtd. Até</th>
        `;
        headerRow.querySelectorAll('th.qty-col').forEach((th) => {
            th.style.width = '74px';
            th.style.minWidth = '74px';
            th.style.maxWidth = '74px';
            th.style.display = 'table-cell';
        });
        
        // Renderizar tamanhos
        availableSizes.forEach(size => {
            const th = document.createElement('th');
            th.className = 'size-col px-1.5 py-2.5 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider';
            th.innerHTML = `
                <div class="text-center">
                    <span class="text-[11px] font-bold text-gray-700 dark:text-gray-200">${size}</span>
                    <div class="flex justify-center gap-1 mt-0.5">
                        <span class="text-[9px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-1 py-0.5 rounded">Venda</span>
                        <span class="text-[9px] font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-1 py-0.5 rounded">Custo</span>
                    </div>
                </div>
            `;
            headerRow.appendChild(th);
        });
        
        // Adicionar coluna de ações
        const actionTh = document.createElement('th');
        actionTh.className = 'actions-col px-1.5 py-2.5 text-center';
        headerRow.appendChild(actionTh);
        
        // Renderizar linhas
        tbody.innerHTML = '';
        
        if (quantityRanges.length === 0 && availableSizes.length > 0) {
            const emptyRow = document.createElement('tr');
            const colSpan = availableSizes.length + 3;
            emptyRow.innerHTML = `
                <td colspan="${colSpan}" class="text-center py-10 text-gray-400 dark:text-gray-500">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm font-medium">Nenhuma faixa de quantidade</p>
                    <p class="text-xs mt-1">Clique em "Adicionar Faixa" para definir preços</p>
                </td>
            `;
            tbody.appendChild(emptyRow);
        } else {
            quantityRanges.forEach((range, index) => {
                const row = createPriceRow(range, index);
                tbody.appendChild(row);
            });
        }

        tbody.querySelectorAll('tr').forEach((row) => {
            [0, 1].forEach((idx) => {
                const cell = row.children[idx];
                if (!cell) return;
                cell.colSpan = 1;
                cell.style.width = '74px';
                cell.style.minWidth = '74px';
                cell.style.maxWidth = '74px';
                cell.style.display = 'table-cell';
            });
        });
    }

    function createPriceRow(range, index) {
        const row = document.createElement('tr');
        row.className = 'price-row transition-colors fade-in';
        
        let html = `
            <td class="qty-col px-2 py-2.5">
                <input type="number" name="prices[${index}][quantity_from]" 
                       value="${range.quantity_from}" min="1" required
                       class="qty-input w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-all text-center font-medium"
                       placeholder="1">
            </td>
            <td class="qty-col px-2 py-2.5">
                <input type="number" name="prices[${index}][quantity_to]" 
                       value="${range.quantity_to || ''}" min="1"
                       placeholder="∞"
                       class="qty-input w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition-all text-center font-medium"
                       title="Deixe vazio para infinito">
            </td>
        `;
        
        availableSizes.forEach(size => {
            const price = range.prices[size] || '';
            const cost = (range.costs && range.costs[size]) || '';
            
            html += `
                <td class="size-col px-1.5 py-2.5">
                    <div class="flex gap-1 items-center">
                        <div class="relative flex-1" style="min-width:82px;">
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-emerald-500 dark:text-emerald-400 text-[11px] font-bold pointer-events-none" style="z-index:2;">R$</span>
                            <input type="number" name="prices[${index}][${size.toLowerCase()}][price]" 
                                   value="${price}" step="0.01" min="0"
                                   placeholder="0,00"
                                   title="Venda — ${size}"
                                   class="price-input-sale w-full pl-8 pr-1.5 py-1.5 border rounded-lg text-xs text-gray-900 dark:text-white focus:outline-none transition-all text-right font-medium tabular-nums">
                        </div>
                        <div class="relative flex-1" style="min-width:82px;">
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-amber-500 dark:text-amber-400 text-[11px] font-bold pointer-events-none" style="z-index:2;">R$</span>
                            <input type="number" name="prices[${index}][${size.toLowerCase()}][cost]" 
                                   value="${cost}" step="0.01" min="0"
                                   placeholder="0,00"
                                   title="Custo — ${size}"
                                   class="price-input-cost w-full pl-8 pr-1.5 py-1.5 border rounded-lg text-xs text-gray-900 dark:text-white focus:outline-none transition-all text-right font-medium tabular-nums">
                        </div>
                    </div>
                </td>
            `;
        });
        
        html += `
            <td class="actions-col px-1.5 py-2.5 text-center">
                <button type="button" onclick="removePriceRow(${index})" 
                        class="inline-flex items-center justify-center p-2 text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200"
                        title="Remover faixa">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        
        row.innerHTML = html;
        row.querySelectorAll('td.qty-col').forEach((cell) => {
            cell.colSpan = 1;
            cell.style.width = '74px';
            cell.style.minWidth = '74px';
            cell.style.maxWidth = '74px';
            cell.style.display = 'table-cell';
        });
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
@endsection
