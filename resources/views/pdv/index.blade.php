{{-- 
    PDV - Ponto de Venda
    Esta view é APENAS para vendas (PDV), NÃO deve conter:
    - Lista de estoque agrupado (@forelse($groupedStocks...))
    - Formulários de filtro de estoque
    - Tabelas de estoque
    - Qualquer código relacionado a gerenciamento de estoque
    
    O PDV apenas verifica estoque via API ao adicionar produtos ao carrinho.
--}}
@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-20"> <!-- Full background -->
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8"> <!-- Wider container -->
        
        <!-- Header & Controls -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Ponto de Venda</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gerencie suas vendas de forma rápida e eficiente.</p>
            </div>
            
            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 p-1 rounded-xl border border-gray-200 dark:border-gray-700 overflow-x-auto">
                <a href="{{ route('pdv.index', ['type' => 'products']) }}" data-type="products"
                   class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'products' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Produtos
                </a>
                <a href="{{ route('pdv.index', ['type' => 'fabric_pieces']) }}" data-type="fabric_pieces"
                   class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'fabric_pieces' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Tecidos
                </a>
                <a href="{{ route('pdv.index', ['type' => 'machines']) }}" data-type="machines"
                   class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'machines' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Máquinas
                </a>
                <a href="{{ route('pdv.index', ['type' => 'supplies']) }}" data-type="supplies"
                   class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'supplies' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Suprimentos
                </a>
                <a href="{{ route('pdv.index', ['type' => 'uniforms']) }}" data-type="uniforms"
                   class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'uniforms' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Uniformes
                </a>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 md:gap-8 items-start">
            {{-- Left Column: Products (full on mobile, 8 cols on desktop) --}}
            <div class="col-span-12 lg:col-span-8 space-y-4 md:space-y-6">
                
                {{-- Search Bar (Instant) - Compact on mobile --}}
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 md:pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           id="product-search"
                           value="{{ $search ?? '' }}"
                           placeholder="Buscar produto..." 
                           class="block w-full pl-10 md:pl-12 pr-4 py-3 md:py-4 border-none rounded-xl md:rounded-2xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500/20 shadow-sm dark:shadow-gray-900/50 text-sm md:text-lg transition-all">
                </div>

                {{-- Products Grid Container (AJAX Target) --}}
                <div id="products-grid-container">
                    @include('pdv.partials.grid')
                </div>
            </div> {{-- End Left Column --}}

            {{-- Right Column: Cart & Client (4 cols - hidden on mobile, full on lg) --}}
            <div class="hidden lg:block col-span-12 lg:col-span-4 relative">
                <div class="sticky top-6">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-[calc(100vh-6rem)]">
                        
                        <!-- 1. Header & Client Selection -->
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                            <!-- Selected Client Display -->
                            <div id="selected-client-display" class="hidden animate-fade-in-down">
                                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-3 border border-indigo-100 dark:border-indigo-800 flex justify-between items-center group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-gray-100 text-sm leading-tight" id="selected-client-name"></p>
                                            <p class="text-xs text-indigo-600 dark:text-indigo-400" id="selected-client-info"></p>
                                        </div>
                                    </div>
                                    <button onclick="clearSelectedClient()" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Client Search Input -->
                            <div id="client-search-container" class="relative">
                                <div class="relative">
                                    <input type="text" id="search-client" placeholder="Buscar cliente (opcional)..." 
                                           onkeydown="if(event.key === 'Enter') window.searchClient()"
                                           class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-shadow">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-pointer" onclick="window.searchClient()">
                                        <svg class="w-5 h-5 text-gray-400 hover:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <a href="{{ route('clients.create') }}" target="_blank" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 hover:underline">
                                        + Novo
                                    </a>
                                </div>
                                <div id="search-results" class="absolute w-full mt-1 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 max-h-60 overflow-y-auto hidden"></div>
                                <input type="hidden" id="client_id" name="client_id" value="">
                            </div>
                        </div>

                        <!-- 2. Cart Items (Scrollable) -->
                        <div class="flex-1 overflow-y-auto p-2 space-y-2 bg-gray-50/30 dark:bg-gray-900/30" id="cart-items-container">
                            <div id="cart-items" class="p-2 space-y-3">
                                @if(empty($cart))
                                    <div class="flex flex-col items-center justify-center h-48 text-gray-400 dark:text-gray-500">
                                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-sm">Seu carrinho está vazio</p>
                                    </div>
                                @else
                                    @foreach($cart as $item)
                                    <!-- Simplified Cart Item -->
                                    <div class="cart-item group bg-white dark:bg-gray-700 rounded-xl p-3 shadow-sm border border-gray-100 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors" data-item-id="{{ $item['id'] }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1 pr-2">
                                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm leading-tight">{{ $item['product_title'] }}</p>
                                                @if(isset($item['sale_type']) && $item['sale_type'] != 'unidade')
                                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Venda por {{ $item['sale_type'] == 'kg' ? 'Kg' : 'Metro' }}</p>
                                                @endif
                                            </div>
                                            <button onclick="removeCartItem('{{ $item['id'] }}')" class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        
                                        <div class="flex items-end justify-between">
                                            <div class="flex items-center bg-gray-50 dark:bg-gray-800 rounded-lg p-1 border border-gray-200 dark:border-gray-600">
                                                <input type="number" 
                                                       value="{{ $item['quantity'] }}" 
                                                       step="{{ isset($item['sale_type']) && $item['sale_type'] != 'unidade' ? '0.01' : '1' }}"
                                                       min="{{ isset($item['sale_type']) && $item['sale_type'] != 'unidade' ? '0.01' : '1' }}"
                                                       onchange="updateCartItem('{{ $item['id'] }}', this.value, null)"
                                                       class="w-12 p-0 text-center text-xs bg-transparent border-none text-gray-900 dark:text-gray-100 focus:ring-0">
                                                <span class="text-xs text-gray-400 px-1">×</span>
                                                <input type="number" 
                                                       value="{{ number_format($item['unit_price'], 2, '.', '') }}" 
                                                       step="0.01" min="0"
                                                       onchange="updateCartItem('{{ $item['id'] }}', null, this.value)"
                                                       class="w-16 p-0 text-right text-xs bg-transparent border-none text-gray-900 dark:text-gray-100 font-medium focus:ring-0">
                                            </div>
                                            <p class="font-bold text-gray-900 dark:text-white text-sm">
                                                R$ {{ number_format($item['total_price'], 2, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- 3. Footer (Totals & Actions) -->
                        <div class="bg-white dark:bg-gray-800 p-5 border-t border-gray-100 dark:border-gray-700 space-y-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">
                            <!-- Discount & Delivery (Collapsible or Compact) -->
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block">Desconto</label>
                                    <div class="flex">
                                        <select id="discount-type" class="px-1.5 py-1.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-l-lg text-xs focus:ring-1 focus:ring-indigo-500 border-r-0">
                                            <option value="fixed">R$</option>
                                            <option value="percent">%</option>
                                        </select>
                                        <input type="number" id="discount-input" placeholder="0,00" step="0.01" min="0" class="flex-1 w-full px-2 py-1.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-r-lg text-sm text-right focus:ring-1 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block">Entrega (R$)</label>
                                    <input type="number" id="delivery-fee-input" placeholder="0,00" class="w-full px-2 py-1.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-right focus:ring-1 focus:ring-indigo-500">
                                </div>
                            </div>
                            
                             <!-- Notes -->
                            <div>
                                <input type="text" id="notes-input" placeholder="Observações do pedido (opcional)" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-none rounded-lg text-xs focus:ring-1 focus:ring-indigo-500">
                            </div>

                            <!-- Total -->
                            <div class="flex justify-between items-end pt-2">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total a Pagar</span>
                                <span class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight" id="cart-total">
                                    @php
                                        $subtotal = !empty($cart) ? array_sum(array_column($cart, 'total_price')) : 0;
                                    @endphp
                                    R$ {{ number_format($subtotal, 2, ',', '.') }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="grid grid-cols-1 gap-2">
                                <button onclick="window.checkout()" id="checkout-btn" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-sm transition-transform active:scale-[0.98] flex justify-center items-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    <span>Finalizar Venda</span>
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </button>
                                <button onclick="window.checkoutWithoutClient()" id="checkout-without-client-btn" class="w-full py-2.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-xl font-medium text-xs transition-colors">
                                    Finalizar sem Cliente
                                </button>
                                <button onclick="window.clearCart()" class="text-xs text-red-500 hover:text-red-700 hover:underline text-center w-full mt-1">
                                    Limpar Carrinho
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End Grid -->
    </div> <!-- End Main Container -->

    {{-- Mobile Cart FAB (Floating Action Button) --}}
    <div id="mobile-cart-fab" class="lg:hidden fixed bottom-20 right-4 z-40">
        <button onclick="toggleMobileCart()" 
                class="relative w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg shadow-indigo-500/30 flex items-center justify-center hover:bg-indigo-700 active:scale-95 transition-all">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            {{-- Cart count badge --}}
            <span id="mobile-cart-count" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center {{ empty($cart) ? 'hidden' : '' }}">
                {{ count($cart ?? []) }}
            </span>
        </button>
        {{-- Total preview --}}
        <div class="absolute -left-20 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded font-bold whitespace-nowrap {{ empty($cart) ? 'hidden' : '' }}" id="mobile-cart-total-preview">
            @php $subtotal = !empty($cart) ? array_sum(array_column($cart, 'total_price')) : 0; @endphp
            R$ {{ number_format($subtotal, 2, ',', '.') }}
        </div>
    </div>

    {{-- Mobile Cart Drawer --}}
    <div id="mobile-cart-drawer" class="lg:hidden fixed inset-0 z-50 hidden">
        {{-- Backdrop --}}
        <div onclick="toggleMobileCart()" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        
        {{-- Drawer Content --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white dark:bg-gray-800 rounded-t-3xl max-h-[85vh] overflow-hidden transform transition-transform duration-300 translate-y-full" id="mobile-cart-content">
            {{-- Handle bar --}}
            <div class="flex justify-center py-2">
                <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Carrinho
                </h3>
                <button onclick="toggleMobileCart()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Cart Items --}}
            <div class="max-h-[40vh] overflow-y-auto p-4 space-y-3" id="mobile-cart-items">
                @if(empty($cart))
                    <p class="text-center text-gray-500 py-8">Carrinho vazio</p>
                @else
                    @foreach($cart as $item)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $item['product_title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['quantity'] }} × R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</p>
                        </div>
                        <p class="font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($item['total_price'], 2, ',', '.') }}</p>
                    </div>
                    @endforeach
                @endif
            </div>
            
            {{-- Footer with Total and Actions --}}
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 space-y-3">
                <div class="flex justify-between items-end">
                    <span class="text-sm text-gray-500">Total</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white" id="mobile-cart-total">
                        R$ {{ number_format($subtotal, 2, ',', '.') }}
                    </span>
                </div>
                <button onclick="window.checkoutWithoutClient(); toggleMobileCart();" 
                        class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-colors">
                    Finalizar Venda
                </button>
                <button onclick="window.clearCart(); toggleMobileCart();" 
                        class="w-full py-2 text-red-500 text-xs hover:underline">
                    Limpar Carrinho
                </button>
            </div>
        </div>
    </div>
</div> <!-- End Min-H-Screen -->

<script>
// Mobile cart toggle
function toggleMobileCart() {
    const drawer = document.getElementById('mobile-cart-drawer');
    const content = document.getElementById('mobile-cart-content');
    
    if (drawer.classList.contains('hidden')) {
        drawer.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('translate-y-full');
        }, 10);
    } else {
        content.classList.add('translate-y-full');
        setTimeout(() => {
            drawer.classList.add('hidden');
        }, 300);
    }
}
</script>

<!-- Modal Adicionar Produto -->
<div id="add-product-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title-dynamic">Adicionar Produto</h3>
            <button onclick="closeAddProductModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="product-modal-content" class="p-6 max-h-[calc(80vh-4rem)] overflow-y-auto">
            <!-- Conteúdo será preenchido via JavaScript -->
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Limpar Carrinho -->
<div id="clear-cart-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" onclick="if(event.target === this) closeClearCartModal()">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Limpar Carrinho</h3>
            <button onclick="closeClearCartModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-700 dark:text-gray-300">
                Deseja realmente limpar o carrinho? Esta ação não pode ser desfeita.
            </p>
        </div>
        
        <div class="flex gap-3 justify-end">
            <button onclick="closeClearCartModal()" 
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </button>
            <button onclick="confirmClearCart()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Limpar Carrinho
            </button>
        </div>
    </div>
</div>

<!-- Modal de Personalização SUB.LOCAL -->
<div id="sublocal-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeSublocalModal()">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-800" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white dark:bg-slate-900 z-10">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Adicionar Personalização SUB.LOCAL</h3>
            <button type="button" onclick="closeSublocalModal()" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6 space-y-5">
            <!-- Localização -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Localização *</label>
                <select id="sublocal-modal-location" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    <option value="">Selecione...</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tamanho -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Tamanho *</label>
                <select id="sublocal-modal-size" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    <option value="">Selecione...</option>
                </select>
            </div>

            <!-- Quantidade -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Quantidade *</label>
                <input type="number" id="sublocal-modal-quantity" min="1" value="1"
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Quantidade de peças para esta aplicação</p>
            </div>

            <!-- Preço Calculado -->
            <div id="sublocal-modal-price-display" class="hidden">
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Preço por Aplicação:</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400" id="sublocal-modal-unit-price">R$ 0,00</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-gray-600 dark:text-slate-400">Total desta Aplicação:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white" id="sublocal-modal-total-price">R$ 0,00</span>
                    </div>
                </div>
            </div>
            <input type="hidden" id="sublocal-modal-unit-price-value" value="0">
            <input type="hidden" id="sublocal-modal-final-price-value" value="0">

            <!-- Botões -->
            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-slate-800">
                <button type="button" onclick="closeSublocalModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmSublocalPersonalization()" 
                        class="flex-1 px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors font-semibold">
                    Adicionar
                </button>
            </div>
        </div>
</div>
</div>

@php
    // $jsItems agora vem do Controller
@endphp

@push('scripts')
<!-- Modal Formas de Pagamento (OUTSIDE main content) -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Formas de Pagamento</h3>
            <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Total da Venda: <span class="font-semibold text-lg text-gray-900 dark:text-gray-100" id="payment-total">R$ 0,00</span>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Adicione uma ou mais formas de pagamento para finalizar a venda
            </p>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adicionar Forma de Pagamento:</label>
            <div class="flex gap-2">
                <select id="new-payment-method" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione...</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="pix">PIX</option>
                    <option value="cartao">Cartão</option>
                    <option value="transferencia">Transferência</option>
                    <option value="boleto">Boleto</option>
                </select>
                <input type="number" 
                       id="new-payment-amount" 
                       step="0.01"
                       min="0.01"
                       placeholder="Valor"
                       class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <button type="button" onclick="addPaymentMethod()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Adicionar
                </button>
            </div>
        </div>
        
        <div id="payment-methods-list" class="space-y-2 mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhuma forma de pagamento adicionada</p>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Pago:</span>
                <span class="text-lg font-semibold text-green-600 dark:text-green-400" id="payment-total-paid">R$ 0,00</span>
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Restante:</span>
                <span class="text-lg font-semibold" id="payment-remaining">R$ 0,00</span>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closePaymentModal()" 
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button onclick="confirmPayment()" 
                    id="confirm-payment-btn"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                Finalizar Venda
            </button>
        </div>
    </div>
</div>
@endpush

<script>
// CRÍTICO: Definir funções no window IMEDIATAMENTE, antes de qualquer outro código
// Isso garante que as funções estejam disponíveis quando a página é carregada via AJAX
// e os elementos com onclick/onchange tentam chamá-las

// Função stub para openAddProductModal
// Queue calls when stub is executed, then retry when real function loads
window._pendingModalCalls = [];
window.openAddProductModal = window.openAddProductModal || function(itemId, type = 'product') {
    // Queue the call
    window._pendingModalCalls.push({itemId, type});
    // Retry after short delay (allows real function to load)
    setTimeout(() => {
        if (window._pendingModalCalls.length > 0 && typeof window._openAddProductModalReal === 'function') {
            const call = window._pendingModalCalls.shift();
            window._openAddProductModalReal(call.itemId, call.type);
        }
    }, 50);
};

// Função stub para checkStockForSizes
window.checkStockForSizes = window.checkStockForSizes || async function() {
    // Função stub silenciosa - apenas retorna sem fazer nada
    return;
};

// Função stub para updateTotalQuantity
window.updateTotalQuantity = window.updateTotalQuantity || function() {
    // Função stub silenciosa
    return;
};

// Função stub para calculateSizeSurcharges
window.calculateSizeSurcharges = window.calculateSizeSurcharges || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmAddProduct
window.confirmAddProduct = window.confirmAddProduct || async function() {
    console.warn('confirmAddProduct ainda não foi totalmente inicializada. Aguarde...');
    return false;
};

// Função stub para closeAddProductModal
window.closeAddProductModal = window.closeAddProductModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para clearSelectedClient
window.clearSelectedClient = window.clearSelectedClient || function() {
    // Função stub silenciosa
    return;
};

// Função stub para searchClient
window.searchClient = window.searchClient || function() {
    // Função stub silenciosa
    return;
};

// Função stub para clearCart
window.clearCart = window.clearCart || function() {
    // Função stub silenciosa
    return;
};

// Função stub para removeCartItem
window.removeCartItem = window.removeCartItem || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para checkout
window.checkout = window.checkout || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para checkoutWithoutClient
window.checkoutWithoutClient = window.checkoutWithoutClient || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para closeClearCartModal
window.closeClearCartModal = window.closeClearCartModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmClearCart
window.confirmClearCart = window.confirmClearCart || function() {
    // Função stub silenciosa
    return;
};

// Função stub para closeSublocalModal
window.closeSublocalModal = window.closeSublocalModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmSublocalPersonalization
window.confirmSublocalPersonalization = window.confirmSublocalPersonalization || function() {
    // Função stub silenciosa
    return;
};

(function() {
    // Evitar redeclaração ao carregar via AJAX
    if (typeof window.productsData !== 'undefined') {
        return; // Já foi declarado, não redeclarar
    }
    
    // Dados dos produtos
    // Dados dos produtos
    // Dados dos produtos
    window.pageItems = @json($jsItems);
    window.allItemsData = window.pageItems; // Compatibilidade
    window.locationsData = @json($locations);
    window.fabricsData = @json($fabrics);
    window.colorsData = @json($colors);
    window.currentStoreId = {{ $currentStoreId ?? 'null' }};
    window.sizesList = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
    
    // Função para atualizar itens via AJAX
    window.updatePageItems = function(newItems) {
        window.pageItems = newItems;
        window.allItemsData = window.pageItems;
    };
})();

// Aliases para compatibilidade (usar window.* para evitar redeclaração)
const currentStoreId = window.currentStoreId;
const sizesList = window.sizesList;

let currentProductId = null;
let currentProductType = 'product';

// Calcular preço de peça de tecido baseado em kg
window.calculateFabricPiecePrice = function() {
    const product = window.pageItems.find(p => p.id == currentProductId && p.type == currentProductType);
    if (!product || currentProductType !== 'fabric_piece') return;
    
    const quantityInput = document.getElementById('modal-quantity');
    const priceInput = document.getElementById('modal-unit-price');
    
    if (quantityInput && priceInput) {
        const kg = parseFloat(quantityInput.value) || 0;
        const pricePerKg = parseFloat(product.price_per_kg) || 0;
        const maxKg = parseFloat(product.weight_kg) || 999;
        
        // Validar que não excede o máximo disponível
        if (kg > maxKg) {
            quantityInput.value = maxKg;
            alert(`Quantidade máxima disponível: ${maxKg.toFixed(2)} kg`);
        }
        
        // Calcular preço total: kg * preço por kg
        const totalPrice = (parseFloat(quantityInput.value) || 0) * pricePerKg;
        priceInput.value = totalPrice.toFixed(2);
    }
};

// Abrir modal de adicionar produto
// IMPORTANTE: Definir no window imediatamente para estar disponível quando carregado via AJAX
window.openAddProductModal = function openAddProductModal(itemId, type = 'product') {
    // Register this as the real function for the stub to use
    window._openAddProductModalReal = window.openAddProductModal;
    
    currentProductId = itemId;
    currentProductType = type;
    
    // Encontrar o item na lista genérica pageItems
    const product = window.pageItems.find(p => p.id == itemId && p.type == type);
    
    if (!product) {
        console.error('Item não encontrado:', itemId, type);
        return;
    }
    
    const modal = document.getElementById('add-product-modal');
    const content = document.getElementById('product-modal-content');
    
    // Flags de Tipo
    const isProduct = type === 'product';
    const isProductOption = type === 'product_option';
    const isFabricPiece = type === 'fabric_piece';
    const isStockItem = !isProduct && !isProductOption; // fabric_piece, machine, supply, uniform
    
    // Configurações de Quantidade
    let quantityLabel = 'Quantidade';
    let quantityStep = '1';
    let quantityMin = '1';
    let quantityValue = '1';
    let isQuantityReadonly = false;
    
    if (isProduct && (product.sale_type === 'kg' || product.sale_type === 'metro')) {
        quantityLabel = `Quantidade (${product.sale_type === 'kg' ? 'Kg' : 'Metros'})`;
        quantityStep = '0.01';
        quantityMin = '0.01';
    }
    
    if (isFabricPiece) {
        quantityLabel = 'Quantidade (Kg)';
        quantityValue = product.weight_kg || '1'; // Valor padrão é o peso total
        quantityStep = '0.01';
        quantityMin = '0.01';
        isQuantityReadonly = false; // Permitir edição para venda por kg
    }
    
    // Configurações de Exibição
    // Mostrar Grade (Tamanhos) APENAS para ProductOption 
    // (Produtos normais usam input simples ou grade SE tiver category - mas vamos simplificar: Produtos normais -> Input simples. Options -> Grade)
    // A lógica original mostrava tamanhos para produtos também se não fosse tecido e nao fosse quick.
    // Vamos manter compatibilidade:
    const isFabric = isProduct && (product.sale_type === 'kg' || product.sale_type === 'metro');
    const isQuickProduct = isProduct && !product.category_id;
    const shouldShowSizes = !isStockItem && !isFabric && !isQuickProduct && !isProduct; // Simplificando: Apenas ProductOption mostra grade completa
    // Espere, a lógica original era: const shouldShowSizes = !isFabric && !isQuickProduct; 
    // Isso incluía Produtos e Options.
    // Mas NOVO layout de grid genericizado:
    // Produtos Normais -> Input Unico de Qtd (se não tiver grade específica, assumimos simples)
    // ProductOptions -> Grade de Tamanhos
    // StockItems -> Input Unico
    
    // Forçando apenas ProductOption a ter Grade por enquanto para evitar complexidade
    const shouldShowStockFields = isProductOption; 

    // HTML da Aplicação (produtos apenas)
    let applicationHtml = '';
    if (isProduct && product.allow_application && product.application_types && product.application_types.length > 0) {
        applicationHtml = `
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Aplicação:</label>
                <select id="modal-application-type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Sem aplicação</option>
                    ${product.application_types.includes('sublimacao_local') ? '<option value="sublimacao_local">Sublimação Local</option>' : ''}
                    ${product.application_types.includes('dtf') ? '<option value="dtf">DTF</option>' : ''}
                </select>
            </div>
        `;
    }

    // HTML SubLocal
    let sublocalHtml = '';
    if (isProductOption && product.allows_sublocal) {
        sublocalHtml = `
            <div class="mb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Personalizações SUB.LOCAL:</label>
                    <button type="button" onclick="openSublocalModal()" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        + Adicionar
                    </button>
                </div>
                <div id="sublocal-personalizations-list" class="space-y-3"></div>
            </div>
        `;
    }
    
    // Input Unitário HTML
    // Mostrado para TODOS exceto ProductOption (que tem preço fixo por tamanho/tipo)
    const showUnitPrice = !isProductOption;
    
    // Atualizar título do modal dinamicamente
    const modalTitle = document.getElementById('modal-title-dynamic');
    if(modalTitle) modalTitle.textContent = product.title;

    // HTML Principal
    content.innerHTML = `
        <div>
            <!-- Header Info (Price & Type) -->
            <div class="mb-5 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col">
                    ${isFabricPiece ? `
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Valor da Peça</span>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                R$ ${parseFloat(product.sale_price || product.price || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Preço por Kg (Venda): <span class="font-semibold text-gray-700 dark:text-gray-300">R$ ${parseFloat(product.price_per_kg || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                            | Peso: <span class="font-semibold text-gray-700 dark:text-gray-300">${parseFloat(product.weight_kg || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg</span>
                        </span>
                        <div class="mt-2 text-[10px] space-y-0.5 border-t border-gray-100 dark:border-gray-700 pt-1">
                            <span class="block text-indigo-600 dark:text-indigo-400 font-bold uppercase">${product.fabric_type_name || 'Tecido'}</span>
                            ${product.supplier_name ? `<span class="block text-gray-500 font-medium">Fornecedor: ${product.supplier_name}</span>` : ''}
                        </div>
                    ` : `
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Preço Unitário</span>
                        <div class="flex items-center gap-2">
                             <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                R$ ${parseFloat(product.price || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </span>
                            ${isProduct && product.sale_type !== 'unidade' ? `<span class="text-xs font-medium text-gray-400">/ ${product.sale_type === 'kg' ? 'Kg' : 'Mt'}</span>` : ''}
                        </div>
                    `}
                </div>
                 ${isFabricPiece ? `<span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold">Venda por Kg (máx: ${parseFloat(product.weight_kg || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 })} kg)</span>` : ''}
            </div>
            
            ${!shouldShowStockFields ? `
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">${quantityLabel}</label>
                <input type="number" 
                       id="modal-quantity" 
                       step="${quantityStep}"
                       min="${quantityMin}"
                       value="${quantityValue}"
                       ${isFabricPiece ? `max="${product.weight_kg || 999}" onchange="calculateFabricPiecePrice()"` : ''}
                       ${isQuantityReadonly ? 'readonly disabled' : ''}
                       class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-indigo-500 transition-shadow ${isQuantityReadonly ? 'cursor-not-allowed opacity-75' : ''}">
                ${isFabricPiece ? `<p class="text-xs text-gray-500 mt-1">Máximo disponível: ${parseFloat(product.weight_kg || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 })} kg</p>` : ''}
            </div>
            ` : ''}
            
            ${showUnitPrice ? `
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    ${isFabricPiece ? 'Valor Total da Peça' : (isProduct && product.sale_type === 'kg' ? 'Preço por Kg (Venda)' : 'Valor Unitário')} 
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">R$</span>
                    <input type="number" 
                           id="modal-unit-price" 
                           required
                           step="0.01"
                           min="0.00"
                           value="${product.price || 0}"
                           class="w-full pl-9 pr-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-medium focus:ring-1 focus:ring-indigo-500 transition-shadow">
                </div>
            </div>
            ` : `<input type="hidden" id="modal-unit-price" value="${product.price || 0}">`}
            
            ${applicationHtml}
            
            ${shouldShowStockFields ? `
            <!-- Hidden field para o ID do tipo de corte -->
            <input type="hidden" id="modal-cut-type-id" value="${product.id}">
            
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Cor <span class="text-red-500">*</span></label>
                <select id="modal-color-select" required
                        class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-1 focus:ring-indigo-500 text-sm transition-shadow">
                    <option value="">Selecione a cor...</option>
                    ${window.colorsData.map(color => `<option value="${color.id}">${color.name}</option>`).join('')}
                </select>
            </div>
            
            <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tamanhos</label>
                    <div class="flex items-center gap-3 text-[10px]">
                        <div class="text-gray-400">Qtd: <span id="total-quantity-display" class="font-bold text-gray-700 dark:text-gray-300">0</span></div>
                        <div class="text-gray-400">Total: <span id="total-surcharges-modal" class="font-bold text-gray-700 dark:text-gray-300">R$ 0,00</span></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-5 gap-2 gap-y-8 mb-4">
                    ${['PP','P','M','G','GG'].map(s => `
                    <div class="relative group">
                        <label class="block text-[10px] font-bold text-gray-400 mb-0.5 text-center uppercase">${s}</label>
                        <input type="number" id="modal-size-${s.toLowerCase()}" min="0" value="" placeholder="0"
                               onchange="checkStockForSizes(); ${s === 'GG' ? 'calculateSizeSurcharges();' : ''} updateTotalQuantity();" 
                               class="w-full px-1 py-1.5 border border-gray-200 dark:border-gray-600 rounded-md text-center text-sm font-medium bg-gray-50 dark:bg-gray-700 focus:ring-1 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-600 placeholder-gray-300 relative z-0">
                         <span id="stock-badge-${s.toLowerCase()}" class="pointer-events-none"></span>
                         ${s === 'GG' ? `<p class="hidden text-[9px] font-medium text-green-600 text-center absolute w-full -bottom-8" id="surcharge-gg"></p>` : ''}
                        <div id="stock-${s.toLowerCase()}" class="hidden absolute z-50 top-full left-0 w-32 bg-white shadow-lg rounded p-2 text-xs"></div>
                    </div>`).join('')}
                </div>
                <div class="grid grid-cols-4 gap-2 gap-y-8">
                     ${['EXG','G1','G2','G3'].map(s => `
                    <div class="relative group">
                        <label class="block text-[10px] font-bold text-gray-400 mb-0.5 text-center uppercase">${s}</label>
                        <input type="number" id="modal-size-${s.toLowerCase()}" min="0" value="" placeholder="0"
                               onchange="checkStockForSizes(); calculateSizeSurcharges(); updateTotalQuantity();" 
                               class="w-full px-1 py-1.5 border border-gray-200 dark:border-gray-600 rounded-md text-center text-sm font-medium bg-gray-50 dark:bg-gray-700 focus:ring-1 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-600 placeholder-gray-300 relative z-0">
                         <span id="stock-badge-${s.toLowerCase()}" class="pointer-events-none"></span>
                        <p class="hidden text-[9px] font-medium text-green-600 text-center absolute w-full -bottom-8" id="surcharge-${s.toLowerCase()}"></p>
                    </div>`).join('')}
                </div>
            </div>
            ` : ''}
            
            ${sublocalHtml}
            
            <div class="pt-4 mt-2 grid grid-cols-2 gap-3">
                <button onclick="closeAddProductModal()" class="w-full py-3.5 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors">
                    Cancelar
                </button>
                <button onclick="confirmAddProduct()" class="w-full py-3.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-transform active:scale-[0.98] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Adicionar
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    
    if (shouldShowStockFields) {
        setTimeout(() => calculateSizeSurcharges(), 100);
        document.getElementById('modal-quantity')?.addEventListener('input', () => { calculateSizeSurcharges(); checkStockAvailability(); });
        const colorSelect = document.getElementById('modal-color-select');
        if (colorSelect) {
            colorSelect.addEventListener('change', function() {
                loadStockByCutType(product.id);
                checkStockForSizes();
            });
        }
    }
}

// Atualizar total de quantidade
window.updateTotalQuantity = function updateTotalQuantity() {
    const sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
    let total = 0;
    
    sizes.forEach(size => {
        const input = document.getElementById(`modal-size-${size.toLowerCase()}`);
        if (input) {
            total += parseInt(input.value || 0);
        }
    });
    
    const totalQuantityDisplay = document.getElementById('total-quantity-display');
    if (totalQuantityDisplay) {
        totalQuantityDisplay.textContent = total;
        totalQuantityDisplay.className = total > 0 ? 'font-semibold text-indigo-600 dark:text-indigo-400' : 'font-semibold text-gray-400';
    }
    
    const totalItemsDisplay = document.getElementById('total-items-display');
    if (totalItemsDisplay) {
        totalItemsDisplay.textContent = total;
    }
}

// Encontrar loja prioritária que tem todos os tamanhos selecionados
function findPriorityStore(selectedSizes, stockBySizeData) {
    if (!stockBySizeData || !stockBySizeData.stock_by_size) {
        return null;
    }
    
    // Mapear lojas e verificar quais têm todos os tamanhos selecionados
    const storeScores = {};
    
    selectedSizes.forEach(size => {
        const sizeData = stockBySizeData.stock_by_size.find(s => s.size === size);
        if (sizeData && sizeData.stores) {
            sizeData.stores.forEach(store => {
                if (!storeScores[store.store_id]) {
                    storeScores[store.store_id] = {
                        store_id: store.store_id,
                        store_name: store.store_name,
                        hasAllSizes: true,
                        totalAvailable: 0,
                        sizesCount: 0
                    };
                }
                storeScores[store.store_id].totalAvailable += store.available || 0;
                storeScores[store.store_id].sizesCount++;
            });
        }
    });
    
    // Verificar quais lojas têm todos os tamanhos
    const selectedSizesCount = selectedSizes.length;
    let priorityStore = null;
    let maxTotalAvailable = 0;
    
    Object.values(storeScores).forEach(store => {
        if (store.sizesCount === selectedSizesCount && store.totalAvailable > maxTotalAvailable) {
            priorityStore = store;
            maxTotalAvailable = store.totalAvailable;
        }
    });
    
    return priorityStore;
}

// Verificar estoque para cada tamanho com informações por loja
// Verificar estoque para cada tamanho com informações por loja
window.checkStockForSizes = async function checkStockForSizes() {
    const colorSelect = document.getElementById('modal-color-select');
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    
    // Limpar badges se não houver seleção
    if (!colorSelect || !cutTypeId || !colorSelect.value) {
        sizesList.forEach(size => {
            const stockBadge = document.getElementById(`stock-badge-${size.toLowerCase()}`);
            if (stockBadge) stockBadge.innerHTML = '';
        });
        return;
    }
    
    const colorId = colorSelect.value;
    // const sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3']; // Usar global sizesList
    
    // Buscar dados de estoque (cachear se possível ou buscar sempre? buscar sempre é mais seguro no PDV)
    let stockBySizeData = null;
    try {
        const params = new URLSearchParams({ cut_type_id: cutTypeId, color_id: colorId });
        console.log('Buscando estoque:', `/api/stocks/by-cut-type?${params}`);
        const response = await fetch(`/api/stocks/by-cut-type?${params}`);
        stockBySizeData = await response.json();
        console.log('Resposta do estoque:', stockBySizeData);
    } catch (error) {
        console.error('Erro ao buscar estoque:', error);
        return;
    }
    
    if (!stockBySizeData || !stockBySizeData.success || !stockBySizeData.stock_by_size) {
        console.warn('Sem dados de estoque ou erro:', stockBySizeData?.message);
        return;
    }

    // Iterar pelos tamanhos
    sizesList.forEach(size => {
        const stockBadge = document.getElementById(`stock-badge-${size.toLowerCase()}`);
        if (!stockBadge) return;
        
        const sizeData = stockBySizeData.stock_by_size.find(s => s.size === size);
        
        if (sizeData && sizeData.available > 0) {
            // Lógica de Prioridade de Loja
            // 1. Tentar encontrar na loja atual
            let targetStore = null;
            let neededTransfer = false;
            
            // Tenta encontrar na currentStoreId
            if (window.currentStoreId) {
                targetStore = sizeData.stores.find(s => s.store_id == window.currentStoreId && s.available > 0);
            }
            
            // Se não tem na loja atual, pega a com maior estoque
            if (!targetStore) {
                // Ordena por quantidade descrescente
                const sortedStores = sizeData.stores.sort((a, b) => b.available - a.available);
                if (sortedStores.length > 0) {
                    targetStore = sortedStores[0];
                    neededTransfer = true; // Indica que vai precisar de transferência (fundo amarelo/laranja)
                }
            }
            
            if (targetStore) {
                // Formatar exibição: "100 - NomeLoja"
                // Se for da loja atual: Verde. Se for de outra: Laranja/Azul.
                const badgeColorClass = neededTransfer 
                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' 
                    : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300';
                
                // Abrevia o nome da loja se for muito longo para caber
                let storeName = targetStore.store_name;
                if (storeName.length > 10) storeName = storeName.substring(0, 10) + '...';
                
                stockBadge.className = `absolute top-full mt-1 left-0 right-0 mx-auto w-max max-w-full px-1.5 py-0.5 rounded text-[9px] font-bold shadow-sm whitespace-nowrap z-10 flex items-center justify-center gap-1 ${badgeColorClass}`;
                stockBadge.innerHTML = `<span>${targetStore.available}</span> <span class="opacity-75 font-medium border-l border-current pl-1">${storeName}</span>`;
                
                // Tooltip simples (title nativo)
                stockBadge.title = `Loja: ${targetStore.store_name} (${targetStore.available} un.)`;
            } else {
                 // Tem available > 0 no total, mas stores array vazio? (Caso raro de inconsistência)
                 stockBadge.innerHTML = '';
            }
            
        } else {
            // Sem estoque
            // Opcional: Mostrar "0" ou nada. Usuário pediu pra focar no "Se tem".
            // Se não tem, gera solicitação, mas visualmente no grid pode ficar vazio ou traço.
            stockBadge.className = 'absolute top-full mt-1 right-0 text-[8px] text-gray-400 font-medium px-1';
            stockBadge.innerHTML = '0';
        }
    });
}

// Buscar estoque por tipo de corte
async function loadStockByCutType(cutTypeId) {
    if (!cutTypeId) {
        return;
    }
    
    const stockList = document.getElementById('stock-by-size-list');
    const colorSelect = document.getElementById('modal-color-select');
    if (!stockList) return;
    
    const colorId = colorSelect?.value;
    
    try {
        // Buscar de todas as lojas (não filtrar por loja específica)
        const params = new URLSearchParams({
            cut_type_id: cutTypeId
        });
        
        if (colorId) {
            params.append('color_id', colorId);
        }
        
        const response = await fetch(`/api/stocks/by-cut-type?${params}`);
        const data = await response.json();
        
        if (data.success && data.stock_by_size && data.stock_by_size.length > 0) {
            let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">';
            
            data.stock_by_size.forEach(item => {
                const hasStock = item.available > 0;
                const bgColor = hasStock ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' : 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600';
                const textColor = hasStock ? 'text-green-800 dark:text-green-200' : 'text-gray-500 dark:text-gray-400';
                
                html += `
                    <div class="p-4 rounded-lg border-2 ${bgColor} transition-all hover:shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="text-base font-bold text-gray-900 dark:text-gray-100">Tamanho ${item.size}</span>
                            </div>
                            ${hasStock ? `
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Em estoque
                                </span>
                            ` : `
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                    ✗ Sem estoque
                                </span>
                            `}
                        </div>
                        
                        <div class="mb-2">
                            <div class="text-sm ${textColor}">
                                <span class="font-semibold">${item.available}</span> disponível
                                ${item.reserved > 0 ? `<span class="text-orange-600 dark:text-orange-400">(${item.reserved} reservado)</span>` : ''}
                            </div>
                        </div>
                        
                        ${item.stores && item.stores.length > 0 ? `
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Por Loja:</div>
                                <div class="space-y-1.5">
                                    ${item.stores.map(store => `
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 dark:text-gray-400 font-medium">${store.store_name}:</span>
                                            <span class="font-semibold ${store.available > 0 ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-500'}">
                                                ${store.available} disp.
                                                ${store.reserved > 0 ? `<span class="text-orange-600 dark:text-orange-400">(${store.reserved} res.)</span>` : ''}
                                            </span>
                                        </div>
                                        ${store.items && store.items.length > 0 ? `
                                            <div class="ml-2 text-xs text-gray-500 dark:text-gray-500">
                                                ${store.items.map(i => `${i.fabric} ${i.color}`).join(', ')}
                                            </div>
                                        ` : ''}
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            html += '</div>';
            stockList.innerHTML = html;
        } else {
            stockList.innerHTML = `
                <div class="text-center py-8 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-yellow-300 dark:border-yellow-700">
                    <svg class="w-16 h-16 mx-auto mb-4 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Nenhum estoque cadastrado
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ${colorId ? 'Para esta cor selecionada' : 'Selecione uma cor para verificar o estoque'}
                    </p>
                    ${colorId ? `
                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                ⚠ Solicitações de estoque serão criadas automaticamente ao finalizar a venda
                            </p>
                        </div>
                    ` : ''}
                </div>
            `;
        }
    } catch (error) {
        console.error('Erro ao buscar estoque:', error);
        stockList.innerHTML = `
            <div class="text-sm text-red-600 dark:text-red-400 text-center py-2">
                Erro ao carregar estoque
            </div>
        `;
    }
}

// Verificar disponibilidade de estoque em tempo real
async function checkStockAvailability() {
    if (!currentStoreId) {
        return;
    }
    
    const fabricId = document.getElementById('modal-fabric')?.value;
    const colorId = document.getElementById('modal-color')?.value;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    const size = document.getElementById('modal-size')?.value;
    const quantity = parseInt(document.getElementById('modal-quantity')?.value || 1);
    
    const stockInfo = document.getElementById('stock-info');
    const stockQuantity = document.getElementById('stock-quantity');
    const stockWarning = document.getElementById('stock-warning');
    const stockSuccess = document.getElementById('stock-success');
    
    // Ocultar mensagens anteriores
    if (stockWarning) stockWarning.classList.add('hidden');
    if (stockSuccess) stockSuccess.classList.add('hidden');
    
    // Verificar se todos os campos estão preenchidos
    if (!fabricId || !colorId || !cutTypeId || !size) {
        if (stockInfo) stockInfo.classList.add('hidden');
        return;
    }
    
    try {
        const params = new URLSearchParams({
            store_id: currentStoreId,
            fabric_id: fabricId,
            color_id: colorId,
            cut_type_id: cutTypeId,
            size: size,
            quantity: quantity
        });
        
        const response = await fetch(`/api/stocks/check?${params}`);
        const data = await response.json();
        
        if (data.success && stockInfo) {
            stockInfo.classList.remove('hidden');
            const available = data.available_quantity || 0;
            const hasStock = data.has_stock || false;
            
            if (stockQuantity) {
                stockQuantity.textContent = `${available} unidade(s)`;
            }
            
            if (hasStock) {
                stockInfo.className = 'mt-3 p-3 rounded-lg border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20';
                if (stockQuantity) stockQuantity.className = 'text-sm font-bold text-green-600 dark:text-green-400';
                if (stockSuccess) {
                    stockSuccess.classList.remove('hidden');
                    stockSuccess.textContent = `✓ Estoque suficiente para ${quantity} unidade(s)`;
                }
            } else {
                stockInfo.className = 'mt-3 p-3 rounded-lg border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20';
                if (stockQuantity) stockQuantity.className = 'text-sm font-bold text-red-600 dark:text-red-400';
                if (stockWarning) {
                    stockWarning.classList.remove('hidden');
                    stockWarning.innerHTML = `⚠ Estoque insuficiente! Disponível: ${available} unidade(s). <button type="button" onclick="createStockRequest()" class="text-blue-600 dark:text-blue-400 underline ml-1">Solicitar estoque</button>`;
                }
            }
        } else {
            if (stockInfo) stockInfo.classList.add('hidden');
        }
    } catch (error) {
        console.error('Erro ao verificar estoque:', error);
        if (stockInfo) stockInfo.classList.add('hidden');
    }
}

// Criar solicitação de estoque
async function createStockRequest() {
    if (!currentStoreId) {
        alert('Loja não identificada');
        return;
    }
    
    const fabricId = document.getElementById('modal-fabric')?.value;
    const colorId = document.getElementById('modal-color')?.value;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    const size = document.getElementById('modal-size')?.value;
    const quantity = parseInt(document.getElementById('modal-quantity')?.value || 1);
    
    if (!fabricId || !colorId || !cutTypeId || !size) {
        alert('Preencha todos os campos de especificação');
        return;
    }
    
    const fabricName = window.fabricsData.find(f => f.id == fabricId)?.name || 'Tecido';
    const colorName = window.colorsData.find(c => c.id == colorId)?.name || 'Cor';
    const cutTypeName = document.getElementById('modal-cut-type')?.value || 'Tipo de Corte';
    
    if (!confirm(`Deseja criar uma solicitação de estoque para:\n\nTecido: ${fabricName}\nCor: ${colorName}\nTipo de Corte: ${cutTypeName}\nTamanho: ${size}\nQuantidade: ${quantity} unidade(s)?`)) {
        return;
    }
    
    try {
        const response = await fetch('/stock-requests', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                requesting_store_id: currentStoreId,
                fabric_id: fabricId,
                color_id: colorId,
                cut_type_id: cutTypeId,
                size: size,
                requested_quantity: quantity,
                request_notes: `Solicitação criada automaticamente do PDV - Quantidade necessária: ${quantity}`
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Solicitação de estoque criada com sucesso!');
            // Atualizar informações de estoque
            checkStockAvailability();
        } else {
            alert('Erro ao criar solicitação: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao criar solicitação:', error);
        alert('Erro ao criar solicitação de estoque');
    }
}

// Calcular acréscimos de tamanhos especiais
// IMPORTANTE: Usar preço unitário, não o total, para determinar a faixa de acréscimo
async function calculateSizeSurcharges() {
    const unitPrice = parseFloat(document.getElementById('modal-unit-price')?.value || 0);
    const quantity = parseFloat(document.getElementById('modal-quantity')?.value || 1);
    // Usar preço unitário para buscar a faixa de acréscimo, não o total
    const priceForSurcharge = unitPrice;
    
    // Verificar quais tamanhos estão disponíveis (GG e EXG só para produtos não-tecido)
    const hasGG = document.getElementById('modal-size-gg') !== null;
    const hasEXG = document.getElementById('modal-size-exg') !== null;
    
    let sizes = ['G1', 'G2', 'G3'];
    if (hasGG) sizes.unshift('GG');
    if (hasEXG) sizes.unshift('EXG');
    
    let totalSurcharges = 0;
    
    for (const size of sizes) {
        const quantityInput = document.getElementById(`modal-size-${size.toLowerCase()}`);
        const surchargeDisplay = document.getElementById(`surcharge-${size.toLowerCase()}`);
        
        if (!quantityInput || !surchargeDisplay) continue;
        
        const qty = parseInt(quantityInput.value || 0);
        
        if (qty > 0 && priceForSurcharge > 0) {
            try {
                // Usar preço unitário para buscar a faixa de acréscimo
                const response = await fetch(`{{ url('/api/size-surcharge') }}/${size}?price=${priceForSurcharge}`);
                const data = await response.json();
                
                if (data.surcharge) {
                    const surchargePerUnit = parseFloat(data.surcharge);
                    const totalSurcharge = surchargePerUnit * qty;
                    totalSurcharges += totalSurcharge;
                    
                    surchargeDisplay.textContent = `R$ ${totalSurcharge.toFixed(2).replace('.', ',')}`;
                    surchargeDisplay.className = 'text-xs text-orange-600 dark:text-orange-400 mt-1 font-medium';
                } else {
                    surchargeDisplay.textContent = 'R$ 0,00';
                    surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                }
            } catch (error) {
                console.error(`Erro ao calcular acréscimo ${size}:`, error);
                surchargeDisplay.textContent = 'R$ 0,00';
                surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
            }
        } else {
            surchargeDisplay.textContent = 'R$ 0,00';
            surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
        }
    }
    
    const totalSurchargesElement = document.getElementById('total-surcharges-modal');
    if (totalSurchargesElement) {
        totalSurchargesElement.textContent = `R$ ${totalSurcharges.toFixed(2).replace('.', ',')}`;
    }
}

// Variável para controlar personalizações sub.local
let sublocalPersonalizations = [];
let sublocalCounter = 0;
let sublocalSizes = [];

// Carregar tamanhos disponíveis para SUB.LOCAL
async function loadSublocalSizes() {
    try {
        const response = await fetch('/api/personalization-prices/sizes?type=SUB. LOCAL');
        const data = await response.json();
        
        if (data.success && data.sizes) {
            sublocalSizes = data.sizes;
            const sizeSelect = document.getElementById('sublocal-modal-size');
            sizeSelect.innerHTML = '<option value="">Selecione...</option>';
            
            data.sizes.forEach(size => {
                const option = document.createElement('option');
                option.value = size.size_name;
                const dimensions = size.size_dimensions || '';
                option.textContent = dimensions ? `${size.size_name} (${dimensions})` : size.size_name;
                sizeSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar tamanhos:', error);
    }
}

// Abrir modal de sub.local
function openSublocalModal() {
    // Resetar campos
    document.getElementById('sublocal-modal-location').value = '';
    document.getElementById('sublocal-modal-size').value = '';
    document.getElementById('sublocal-modal-quantity').value = '1';
    document.getElementById('sublocal-modal-price-display').classList.add('hidden');
    
    // Carregar tamanhos
    loadSublocalSizes();
    
    // Mostrar modal
    document.getElementById('sublocal-modal').classList.remove('hidden');
    
    // Adicionar event listeners
    document.getElementById('sublocal-modal-location').addEventListener('change', calculateSublocalModalPrice);
    document.getElementById('sublocal-modal-size').addEventListener('change', calculateSublocalModalPrice);
    document.getElementById('sublocal-modal-quantity').addEventListener('input', calculateSublocalModalPrice);
}

// Fechar modal de sub.local
window.closeSublocalModal = function closeSublocalModal() {
    document.getElementById('sublocal-modal').classList.add('hidden');
}

// Calcular preço no modal de sub.local
async function calculateSublocalModalPrice() {
    const location = document.getElementById('sublocal-modal-location').value;
    const size = document.getElementById('sublocal-modal-size').value;
    const quantity = parseInt(document.getElementById('sublocal-modal-quantity').value || 1);
    
    if (!location || !size || quantity < 1) {
        document.getElementById('sublocal-modal-price-display').classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/api/personalization-prices/price?type=SUB. LOCAL&size=${encodeURIComponent(size)}&quantity=${quantity}`);
        const data = await response.json();
        
        if (data.success && data.price) {
            const unitPrice = parseFloat(data.price);
            const totalPrice = unitPrice * quantity;
            
            document.getElementById('sublocal-modal-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('sublocal-modal-total-price').textContent = `R$ ${totalPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('sublocal-modal-unit-price-value').value = unitPrice;
            document.getElementById('sublocal-modal-final-price-value').value = totalPrice;
            document.getElementById('sublocal-modal-price-display').classList.remove('hidden');
        } else {
            document.getElementById('sublocal-modal-price-display').classList.add('hidden');
        }
    } catch (error) {
        console.error('Erro ao calcular preço:', error);
        document.getElementById('sublocal-modal-price-display').classList.add('hidden');
    }
}

// Confirmar e adicionar personalização sub.local
window.confirmSublocalPersonalization = function confirmSublocalPersonalization() {
    const locationId = document.getElementById('sublocal-modal-location').value;
    const locationName = document.getElementById('sublocal-modal-location').selectedOptions[0]?.text || '';
    const sizeName = document.getElementById('sublocal-modal-size').value;
    const quantity = parseInt(document.getElementById('sublocal-modal-quantity').value || 1);
    const unitPrice = parseFloat(document.getElementById('sublocal-modal-unit-price-value').value || 0);
    const finalPrice = parseFloat(document.getElementById('sublocal-modal-final-price-value').value || 0);
    
    if (!locationId || !sizeName || quantity < 1 || unitPrice <= 0) {
        showNotification('Preencha todos os campos obrigatórios e verifique o preço', 'error');
        return;
    }
    
    const container = document.getElementById('sublocal-personalizations-list');
    if (!container) return;
    
    const id = sublocalCounter++;
    const personalizationHtml = `
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-700" data-sublocal-id="${id}">
            <div class="flex justify-between items-center mb-2">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">${locationName}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tamanho: ${sizeName} | Qtd: ${quantity}</p>
                    <p class="text-xs font-semibold text-green-600 dark:text-green-400">Total: R$ ${finalPrice.toFixed(2).replace('.', ',')}</p>
                </div>
                <button type="button" onclick="removeSublocalPersonalization(${id})" class="text-red-600 dark:text-red-400 hover:text-red-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', personalizationHtml);
    
    // Adicionar aos dados
    sublocalPersonalizations.push({
        id: id,
        location_id: locationId,
        location_name: locationName,
        size_name: sizeName,
        quantity: quantity,
        unit_price: unitPrice,
        final_price: finalPrice
    });
    
    // Fechar modal
    closeSublocalModal();
}

// Remover personalização sub.local
function removeSublocalPersonalization(id) {
    const element = document.querySelector(`[data-sublocal-id="${id}"]`);
    if (element) {
        element.remove();
    }
    sublocalPersonalizations = sublocalPersonalizations.filter(p => p.id !== id);
}

// Fechar modal
window.closeAddProductModal = function closeAddProductModal() {
    document.getElementById('add-product-modal').classList.add('hidden');
    currentProductId = null;
    currentProductType = 'product';
    
    // Resetar campos de tamanho (apenas os que existem)
    ['pp', 'p', 'm', 'g', 'gg', 'exg', 'g1', 'g2', 'g3'].forEach(size => {
        const input = document.getElementById(`modal-size-${size}`);
        if (input) input.value = 0;
        const stockDiv = document.getElementById(`stock-${size}`);
        if (stockDiv) stockDiv.innerHTML = '';
        const stockBadge = document.getElementById(`stock-badge-${size}`);
        if (stockBadge) {
            stockBadge.innerHTML = '';
            stockBadge.className = '';
        }
        const display = document.getElementById(`surcharge-${size}`);
        if (display) {
            display.textContent = '+ R$ 0,00';
            display.className = 'text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-1 text-center';
        }
    });
    const totalSurchargesElement = document.getElementById('total-surcharges-modal');
    if (totalSurchargesElement) {
        totalSurchargesElement.textContent = 'R$ 0,00';
    }
    
    // Limpar total de quantidade
    const totalQuantityDisplay = document.getElementById('total-quantity-display');
    if (totalQuantityDisplay) totalQuantityDisplay.textContent = '0';
    
    const totalItemsDisplay = document.getElementById('total-items-display');
    if (totalItemsDisplay) totalItemsDisplay.textContent = '0';
    
    // Limpar seleção de cor
    const colorSelect = document.getElementById('modal-color-select');
    if (colorSelect) colorSelect.value = '';
    
    // Limpar informações de estoque
    const stockList = document.getElementById('stock-by-size-list');
    if (stockList) {
        stockList.innerHTML = `
            <div class="text-sm text-gray-600 dark:text-gray-400 text-center py-4 bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium">Selecione a cor acima para visualizar o estoque disponível</p>
                <p class="text-xs mt-1">O estoque será exibido por tamanho e por loja</p>
            </div>
        `;
    }
    
    // Limpar personalizações sub.local
    const sublocalContainer = document.getElementById('sublocal-personalizations-list');
    if (sublocalContainer) {
        sublocalContainer.innerHTML = '';
    }
    sublocalPersonalizations = [];
    sublocalCounter = 0;
    
    // Fechar modal de sub.local se estiver aberto
    const sublocalModal = document.getElementById('sublocal-modal');
    if (sublocalModal) {
        sublocalModal.classList.add('hidden');
    }
}

// Confirmar adicionar produto
window.confirmAddProduct = async function confirmAddProduct() {
    if (!currentProductId) return;
    
    const product = window.pageItems.find(p => p.id == currentProductId && p.type == currentProductType);
    
    if (!product) return;
    
    // Para product_option (tipo de corte), usar o preço fixo do produto
    // Para produtos normais, usar o preço do input (se existir)
    const unitPriceInput = document.getElementById('modal-unit-price');
    let unitPrice;
    
    if (currentProductType === 'product_option') {
        // Usar preço fixo do tipo de corte
        unitPrice = parseFloat(product.price || 0);
        if (!unitPrice || unitPrice <= 0) {
            showNotification('Preço do produto não configurado', 'error');
            return;
        }
    } else {
        // Para produtos normais, validar o preço do input
        unitPrice = unitPriceInput ? parseFloat(unitPriceInput.value) : null;
        if (!unitPrice || unitPrice <= 0) {
            showNotification('Informe um preço unitário válido', 'error');
            if (unitPriceInput) {
                unitPriceInput.focus();
                unitPriceInput.classList.add('border-red-500');
            }
            return;
        }
    }
    
    const applicationType = document.getElementById('modal-application-type')?.value || null;
    
    // Coletar cor se for product_option
    const colorSelect = document.getElementById('modal-color-select');
    const selectedColorId = colorSelect?.value || null;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value || null;
    const fabricId = document.getElementById('modal-fabric-id')?.value || null;
    
    // Validar cor para product_option
    if (currentProductType === 'product_option') {
        if (!selectedColorId) {
            showNotification('Selecione a cor', 'error');
            return;
        }
    }
    
    // Verificar se existem inputs de tamanho (se não existirem, é um produto simples/tecido/quick-product)
    const hasSizeInputs = document.getElementById('modal-size-p') !== null;
    
    let totalQuantity = 0;
    let sizeQuantities = {};

    if (hasSizeInputs) {
        // Coletar quantidades de todos os tamanhos
        sizeQuantities = {
            'PP': parseInt(document.getElementById('modal-size-pp')?.value || 0),
            'P': parseInt(document.getElementById('modal-size-p')?.value || 0),
            'M': parseInt(document.getElementById('modal-size-m')?.value || 0),
            'G': parseInt(document.getElementById('modal-size-g')?.value || 0),
            'GG': parseInt(document.getElementById('modal-size-gg')?.value || 0),
            'EXG': parseInt(document.getElementById('modal-size-exg')?.value || 0),
            'G1': parseInt(document.getElementById('modal-size-g1')?.value || 0),
            'G2': parseInt(document.getElementById('modal-size-g2')?.value || 0),
            'G3': parseInt(document.getElementById('modal-size-g3')?.value || 0),
        };
        // Calcular quantidade total dos tamanhos
        totalQuantity = Object.values(sizeQuantities).reduce((sum, qty) => sum + qty, 0);
    } else {
        // Produto simples: pegar do input único
        totalQuantity = parseFloat(document.getElementById('modal-quantity')?.value || 0);
    }
    
    if (totalQuantity <= 0) {
        showNotification(hasSizeInputs ? 'Informe pelo menos uma quantidade para algum tamanho' : 'Informe uma quantidade válida maior que 0', 'error');
        return;
    }
    
    // Coletar personalizações sub.local (já estão no array sublocalPersonalizations)
    const sublocalPersonalizationsToSend = sublocalPersonalizations.map(p => ({
        location_id: p.location_id,
        location_name: p.location_name,
        size_name: p.size_name,
        quantity: p.quantity,
        unit_price: p.unit_price,
        final_price: p.final_price
    }));
    
    // Para product_option, adicionar cada tamanho como item separado ou enviar todos juntos
    if (currentProductType === 'product_option') {
        // Adicionar cada tamanho que tiver quantidade > 0 (sequencialmente para evitar problemas de sincronização)
        let itemsAdded = 0;
        let lastError = null;
        
        // Usar for...of com await para garantir que cada item seja adicionado antes do próximo
        const sizes = Object.entries(sizeQuantities).filter(([size, qty]) => qty > 0);
        
        for (const [size, qty] of sizes) {
            try {
                // Para tamanhos especiais (GG, EXG, G1, G2, G3), enviar size_quantities
                // para que o servidor calcule o acréscimo corretamente
                const sizeQuantitiesForSurcharge = {};
                if (['GG', 'EXG', 'G1', 'G2', 'G3'].includes(size)) {
                    sizeQuantitiesForSurcharge[size] = qty;
                }
                
                const result = await addProductToCart(
                    currentProductId, 
                    currentProductType, 
                    null, 
                    unitPrice, 
                    qty, 
                    applicationType, 
                    sizeQuantitiesForSurcharge, // Enviar size_quantities para calcular acréscimo
                    sublocalPersonalizationsToSend,
                    size, // tamanho específico
                    selectedColorId,
                    cutTypeId,
                    fabricId
                );
                if (result && result.success) {
                    itemsAdded++;
                    if (result.stock_request_created) {
                        // Marcar que houve solicitação de estoque (mostrar aviso no final)
                        lastError = { type: 'stock_request' };
                    }
                } else {
                    lastError = result || { type: 'unknown' };
                }
            } catch (error) {
                console.error(`Erro ao adicionar tamanho ${size}:`, error);
                lastError = error;
            }
        }
        
        if (itemsAdded > 0) {
            closeAddProductModal();
            // Buscar carrinho atualizado do servidor para garantir que temos todos os itens
            fetch('{{ route("pdv.cart.get") }}', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.cart) {
                    updateCartDisplay(data.cart, data.cart_total);
                }
            })
            .catch(err => console.error('Erro ao atualizar carrinho:', err));
            
            // Mostrar notificação apropriada
            if (lastError && lastError.type === 'stock_request') {
                showNotification(`${itemsAdded} item(ns) adicionado(s) ao carrinho. Algumas solicitações de estoque foram criadas.`, 'warning');
            } else if (lastError && lastError.type !== 'stock_request') {
                showNotification(`${itemsAdded} item(ns) adicionado(s), mas houve erros`, 'warning');
            } else {
                showNotification(`${itemsAdded} item(ns) adicionado(s) ao carrinho`, 'success');
            }
        } else if (lastError) {
            showNotification('Erro ao adicionar itens ao carrinho', 'error');
        }
    } else {
        // Para produtos normais, usar a lógica antiga
        const quantity = parseFloat(document.getElementById('modal-quantity')?.value || totalQuantity);
        try {
            const result = await addProductToCart(
                currentProductId, 
                currentProductType, 
                null, 
                unitPrice, 
                quantity, 
                applicationType, 
                sizeQuantities, 
                sublocalPersonalizationsToSend
            );
            closeAddProductModal();
            if (result && result.success) {
                if (result.stock_request_created) {
                    showNotification('Item adicionado ao carrinho. Solicitação de estoque criada automaticamente.', 'warning');
                } else {
                    showNotification('Item adicionado ao carrinho', 'success');
                }
            } else {
                showNotification(result?.message || 'Erro ao adicionar item ao carrinho', 'error');
            }
        } catch (error) {
            console.error('Erro ao adicionar produto:', error);
            showNotification('Erro ao adicionar item ao carrinho', 'error');
        }
    }
    
    // Limpar personalizações após adicionar
    sublocalPersonalizations = [];
    sublocalCounter = 0;
}

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Atualizar total quando desconto ou taxa mudar
document.getElementById('discount-input')?.addEventListener('input', updateTotal);
document.getElementById('discount-type')?.addEventListener('change', updateTotal);
document.getElementById('delivery-fee-input')?.addEventListener('input', updateTotal);

// Busca de produtos
// Busca de produtos - AGORA FEITA VIA BACKEND (PAGINAÇÃO)
// Listener removido pois a busca agora é via formulário GET
/*
document.getElementById('product-search')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const title = card.getAttribute('data-product-title');
        const category = card.getAttribute('data-product-category');
        
        if (title.includes(search) || category.includes(search)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
*/

// Listener removido - não há mais select de cliente, apenas campo de busca
// O botão de checkout sempre está habilitado agora (cliente é opcional)
// Não há mais validação de cliente obrigatório no frontend

// Função para adicionar produto ao carrinho
// Adicionar produto ao carrinho (Frontend)
window.addProductToCart = async function addProductToCart(itemId, type, productTitle, unitPrice, quantity = 1, applicationType = null, sizeQuantities = {}, sublocalPersonalizations = [], selectedSize = null, selectedColorId = null, cutTypeId = null, fabricId = null) {
    try {
        const body = {
            quantity: quantity,
            unit_price: unitPrice,
            size_quantities: sizeQuantities,
            item_type: type // Corrigido: era 'type', mas controller espera 'item_type'
        };
        
        if (type === 'product') {
            body.product_id = itemId;
            body.application_type = applicationType;
        } else if (type === 'product_option') {
            body.product_option_id = itemId;
            body.size = selectedSize;
            body.color_id = selectedColorId;
            body.cut_type_id = cutTypeId;
            body.fabric_id = fabricId;
            if (sublocalPersonalizations && sublocalPersonalizations.length > 0) {
                body.sublocal_personalizations = sublocalPersonalizations;
            }
        } else {
            // Generic types (fabric_piece, machine, supply, uniform)
            body.item_id = itemId;
        }
        
        const response = await fetch('{{ route("pdv.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
            // Retornar resultado para a função chamadora decidir quando mostrar notificação
            return { success: true, stock_request_created: data.stock_request_created || false };
        } else {
            return { success: false, message: data.message || 'Erro ao adicionar item' };
        }
    } catch (error) {
        console.error('Erro:', error);
        return { success: false, message: 'Erro ao adicionar item ao carrinho' };
    }
}

// Função para atualizar item do carrinho
window.updateCartItem = async function updateCartItem(itemId, quantity, unitPrice) {
    try {
        const body = {
            item_id: itemId
        };

        if (quantity !== null) {
            body.quantity = Math.max(0.01, parseFloat(quantity));
        }

        if (unitPrice !== null) {
            body.unit_price = Math.max(0, parseFloat(unitPrice));
        }

        const response = await fetch('{{ route("pdv.cart.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
        } else {
            showNotification(data.message || 'Erro ao atualizar item', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar item', 'error');
    }
}

// Função para atualizar desconto do item
window.updateItemDiscount = async function updateItemDiscount(itemId) {
    try {
        const discountType = document.getElementById(`item-discount-type-${itemId}`)?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById(`item-discount-value-${itemId}`)?.value || 0);
        
        const response = await fetch('{{ route("pdv.cart.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId,
                discount_type: discountType,
                discount_value: discountValue
            })
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
        } else {
            showNotification(data.message || 'Erro ao atualizar desconto', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar desconto', 'error');
    }
}

// Função para remover item do carrinho
async function removeCartItem(itemId) {
    try {
        const response = await fetch('{{ route("pdv.cart.remove") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId
            })
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
            showNotification('Item removido do carrinho', 'success');
        } else {
            showNotification(data.message || 'Erro ao remover item', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao remover item', 'error');
    }
}

// Função para limpar carrinho
// Abrir modal de confirmação para limpar carrinho
window.clearCart = function clearCart() {
    document.getElementById('clear-cart-modal').classList.remove('hidden');
}

// Fechar modal de confirmação
window.closeClearCartModal = function closeClearCartModal() {
    document.getElementById('clear-cart-modal').classList.add('hidden');
}

// Confirmar limpeza do carrinho
window.confirmClearCart = async function confirmClearCart() {
    closeClearCartModal();
    
    try {
        const response = await fetch('{{ route("pdv.cart.clear") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay([], 0);
            showNotification('Carrinho limpo', 'success');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao limpar carrinho', 'error');
    }
}

// Função para atualizar exibição do carrinho
function updateCartDisplay(cart, cartTotal) {
    const cartItemsContainer = document.getElementById('cart-items');
    
    if (!cart || cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Carrinho vazio</p>';
    } else {
        cartItemsContainer.innerHTML = cart.map(item => {
            let surchargesHtml = '';
            if (item.size_surcharges && Object.keys(item.size_surcharges).length > 0) {
                surchargesHtml = '<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">';
                surchargesHtml += '<p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Adicionais:</p>';
                for (const [size, data] of Object.entries(item.size_surcharges)) {
                    if (data.quantity > 0) {
                        surchargesHtml += `<p class="text-xs text-orange-600 dark:text-orange-400">${size} (${data.quantity}x): +R$ ${parseFloat(data.total).toFixed(2).replace('.', ',')}</p>`;
                    }
                }
                surchargesHtml += '</div>';
            }
            
            let sublocalHtml = '';
            if (item.sublocal_personalizations && item.sublocal_personalizations.length > 0) {
                sublocalHtml = '<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">';
                sublocalHtml += '<p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Personalizações SUB.LOCAL:</p>';
                item.sublocal_personalizations.forEach((personalization, index) => {
                    const locationName = personalization.location_name || 'Local não informado';
                    const sizeName = personalization.size_name ? ` - ${personalization.size_name}` : '';
                    sublocalHtml += `<p class="text-xs text-green-600 dark:text-green-400">${locationName}${sizeName} (${personalization.quantity}x): R$ ${parseFloat(personalization.final_price || 0).toFixed(2).replace('.', ',')}</p>`;
                });
                sublocalHtml += '</div>';
            }
            
            return `
            <div class="cart-item group bg-white dark:bg-gray-700 rounded-xl p-3 shadow-sm border border-gray-100 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors" data-item-id="${item.id}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1 pr-2">
                        <p class="font-medium text-gray-900 dark:text-gray-100 text-sm leading-tight">${item.product_title}${item.size ? ` - ${item.size}` : ''}</p>
                        ${item.type === 'fabric_piece' ? `
                            <div class="mt-1 flex flex-col gap-0.5">
                                <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50 dark:bg-indigo-900/20 px-1.5 rounded w-fit uppercase">${item.fabric_type_name || 'Tecido'}</span>
                                ${item.supplier_name ? `<span class="text-[9px] text-gray-500 dark:text-gray-400 font-medium ml-0.5">Forn: ${item.supplier_name}</span>` : ''}
                            </div>
                        ` : ''}
                        ${item.sale_type && item.sale_type !== 'unidade' ? `<p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Venda por ${item.sale_type === 'kg' ? 'Kg' : 'Metro'}</p>` : ''}
                        ${item.application_type ? `<p class="text-[10px] text-green-600 dark:text-green-400 mt-0.5">Aplicação: ${item.application_type === 'sublimacao_local' ? 'Sublimação Local' : 'DTF'}</p>` : ''}
                        ${surchargesHtml}
                        ${sublocalHtml}
                    </div>
                    <button onclick="removeCartItem('${item.id}')" class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 transition-opacity">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="flex items-end justify-between">
                    <div class="flex items-center bg-gray-50 dark:bg-gray-800 rounded-lg p-1 border border-gray-200 dark:border-gray-600">
                        <input type="number" 
                               value="${item.quantity}" 
                               step="${item.sale_type && item.sale_type !== 'unidade' ? '0.01' : '1'}"
                               min="${item.sale_type && item.sale_type !== 'unidade' ? '0.01' : '1'}"
                               onchange="updateCartItem('${item.id}', this.value, null)"
                               class="w-12 p-0 text-center text-xs bg-transparent border-none text-gray-900 dark:text-gray-100 focus:ring-0">
                        <span class="text-xs text-gray-400 px-1">×</span>
                        <input type="number" 
                               step="0.01"
                               value="${parseFloat(item.unit_price).toFixed(2)}" 
                               min="0"
                               onchange="updateCartItem('${item.id}', null, this.value)"
                               class="w-16 p-0 text-right text-xs bg-transparent border-none text-gray-900 dark:text-gray-100 font-medium focus:ring-0">
                    </div>
                    <p class="font-bold text-gray-900 dark:text-white text-sm">
                        R$ ${parseFloat(item.total_price - (item.item_discount || 0)).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </p>
                </div>
                
                <!-- Per-Item Discount -->
                <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 flex items-center gap-1.5">
                    <span class="text-[10px] text-gray-400">Desc:</span>
                    <select id="item-discount-type-${item.id}" 
                            class="px-1 py-0.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-[10px] focus:ring-1 focus:ring-indigo-500"
                            onchange="updateItemDiscount('${item.id}')">
                        <option value="fixed" ${(item.discount_type || 'fixed') === 'fixed' ? 'selected' : ''}>R$</option>
                        <option value="percent" ${item.discount_type === 'percent' ? 'selected' : ''}>%</option>
                    </select>
                    <input type="number" 
                           id="item-discount-value-${item.id}"
                           step="0.01"
                           min="0"
                           value="${item.discount_value || 0}"
                           onchange="updateItemDiscount('${item.id}')"
                           class="w-14 px-1 py-0.5 text-right text-[10px] bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:ring-1 focus:ring-indigo-500">
                    ${(item.item_discount || 0) > 0 ? `<span class="text-[10px] text-red-500 font-medium">-R$ ${parseFloat(item.item_discount).toFixed(2).replace('.', ',')}</span>` : ''}
                </div>
            </div>
        `;
        }).join('');
    }

    // Atualizar total usando os dados do servidor
    if (cart && cart.length > 0) {
        let subtotal = 0;
        let totalQuantity = 0; // Total de itens (quantidade)
        
        cart.forEach(item => {
            subtotal += parseFloat(item.total_price || 0);
            // Somar a quantidade de cada item
            totalQuantity += parseFloat(item.quantity || 0);
        });
        
        const discountType = document.getElementById('discount-type')?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
        let discount = 0;
        if (discountType === 'percent') {
            discount = subtotal * (discountValue / 100);
        } else {
            discount = discountValue;
        }
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;

        const subtotalEl = document.getElementById('cart-subtotal');
        if (subtotalEl) subtotalEl.textContent = 'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
        const totalEl = document.getElementById('cart-total');
        if (totalEl) totalEl.textContent = 'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        
        // Atualizar total de itens no carrinho (se existir elemento)
        const cartTotalItems = document.getElementById('cart-total-items');
        if (cartTotalItems) {
            cartTotalItems.textContent = totalQuantity;
        }
    } else {
        updateTotal();
        // Se carrinho vazio, zerar total de itens
        const cartTotalItems = document.getElementById('cart-total-items');
        if (cartTotalItems) {
            cartTotalItems.textContent = '0';
        }
    }
    
    updateCheckoutButtonState();
}

// Função para atualizar estado do botão de finalizar
function updateCheckoutButtonState() {
    const cartItems = document.querySelectorAll('.cart-item');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    if (checkoutBtn) {
        // Habilitar apenas se tiver itens no carrinho (cliente é opcional)
        if (cartItems.length > 0) {
            checkoutBtn.disabled = false;
        } else {
            checkoutBtn.disabled = true;
        }
    }
}

// Função para atualizar total
function updateTotal() {
    // Buscar carrinho do servidor para ter os valores corretos (incluindo sub.local)
    fetch('{{ route("pdv.cart.get") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const cart = data.cart || [];
        
        // Calcular subtotal somando o total_price de cada item (que já inclui sub.local e acréscimos)
        let subtotal = 0;
        cart.forEach(item => {
            // Usar o total_price do item que já vem do servidor (inclui sub.local, acréscimos de tamanho, etc)
            subtotal += parseFloat(item.total_price || 0);
        });

        const discountType = document.getElementById('discount-type')?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
        let discount = 0;
        if (discountType === 'percent') {
            discount = subtotal * (discountValue / 100);
        } else {
            discount = discountValue;
        }
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;

        document.getElementById('cart-subtotal').textContent = 
            'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('cart-total').textContent = 
            'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    })
    .catch(error => {
        console.error('Erro ao buscar carrinho:', error);
        // Fallback: extrair total_price dos itens exibidos no DOM
        const cartItems = document.querySelectorAll('.cart-item');
        let subtotal = 0;

        cartItems.forEach(item => {
            // Extrair o total_price do texto exibido - Updated to match new UI structure
            const priceElement = item.querySelector('p.font-bold.text-gray-900') || item.querySelector('p.text-sm.font-semibold');
            const totalPriceText = priceElement?.textContent;
            
            if (totalPriceText) {
                const match = totalPriceText.match(/R\$\s*([\d.,]+)/);
                if (match) {
                    // Converter formato brasileiro para número (ex: "256,10" -> 256.10)
                    const priceStr = match[1].replace(/\./g, '').replace(',', '.');
                    const price = parseFloat(priceStr);
                    if (!isNaN(price)) {
                        subtotal += price;
                    }
                }
            }
        });

        const discountType = document.getElementById('discount-type')?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
        let discount = 0;
        if (discountType === 'percent') {
            discount = subtotal * (discountValue / 100);
        } else {
            discount = discountValue;
        }
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;

        const subtotalEl = document.getElementById('cart-subtotal');
        if (subtotalEl) subtotalEl.textContent = 'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
        const totalEl = document.getElementById('cart-total');
        if (totalEl) totalEl.textContent = 'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    });
}

// Função para finalizar venda
// Variáveis globais para pagamento
let paymentMethods = [];
let checkoutData = null;

// Função para finalizar venda - abre modal de pagamento
// Função para finalizar venda checkout normal
window.checkout = async function checkout() {
    // Buscar valor do client_id - pode ser vazio, null ou um ID
    const clientIdElement = document.getElementById('client_id');
    let clientId = clientIdElement ? clientIdElement.value : null;
    
    // Normalizar: se for string vazia, undefined, null ou 'null', converter para null
    if (!clientId || clientId === '' || clientId === 'null' || clientId === 'undefined') {
        clientId = null;
    }
    
    console.log('Checkout - client_id:', clientId);
    
    // Buscar carrinho do servidor
    try {
        const response = await fetch('{{ route("pdv.cart.get") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        const cart = data.cart || [];
        
        if (cart.length === 0) {
            showNotification('Carrinho vazio', 'error');
            return;
        }
        
        // Calcular totais
        const subtotal = cart.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0);
        const discountType = document.getElementById('discount-type')?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
        let discount = 0;
        if (discountType === 'percent') {
            discount = subtotal * (discountValue / 100);
        } else {
            discount = discountValue;
        }
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;
        
        // Salvar dados do checkout
        checkoutData = {
            client_id: clientId,
            discount: discount,
            delivery_fee: deliveryFee,
            notes: document.getElementById('notes-input')?.value || '',
            total: total,
            subtotal: subtotal // Adicionar subtotal para referência
        };
        
        console.log('Checkout data:', checkoutData);
        
        // Resetar métodos de pagamento
        paymentMethods = [];
        
        // Atualizar modal de pagamento
        document.getElementById('payment-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        renderPaymentMethods();
        updatePaymentTotals();
        
        // Abrir modal de pagamento
        const modal = document.getElementById('payment-modal');
        if (modal) {
            modal.style.display = 'flex';
            // Auto-preencher o valor total no campo de pagamento
            const amountInput = document.getElementById('new-payment-amount');
            if (amountInput) {
                amountInput.value = total.toFixed(2);
                amountInput.focus();
                amountInput.select();
            }
        }
    } catch (error) {
        console.error('Erro ao buscar carrinho:', error);
        showNotification('Erro ao buscar carrinho', 'error');
    }
}

// Função para finalizar venda sem cliente - agora abre modal de pagamento
window.checkoutWithoutClient = async function checkoutWithoutClient() {
    console.log('checkoutWithoutClient: Iniciando...');
    
    // Buscar carrinho do servidor
    try {
        const response = await fetch('{{ route("pdv.cart.get") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        console.log('checkoutWithoutClient: Carrinho recebido:', data);
        
        const cart = data.cart || [];
        
        if (cart.length === 0) {
            showNotification('Carrinho vazio', 'error');
            return;
        }
        
        // Calcular totais
        const subtotal = cart.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0);
        const discountType = document.getElementById('discount-type')?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
        let discount = 0;
        if (discountType === 'percent') {
            discount = subtotal * (discountValue / 100);
        } else {
            discount = discountValue;
        }
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;
        
        console.log('checkoutWithoutClient: Total calculado:', total);
        
        // Salvar dados do checkout SEM CLIENTE (client_id = null)
        checkoutData = {
            client_id: null,
            discount: discount,
            delivery_fee: deliveryFee,
            notes: document.getElementById('notes-input')?.value || '',
            total: total,
            subtotal: subtotal
        };
        
        console.log('Checkout sem cliente, abrindo modal de pagamento:', checkoutData);
        
        // Resetar métodos de pagamento
        paymentMethods = [];
        
        // Atualizar modal de pagamento
        const paymentTotalEl = document.getElementById('payment-total');
        if (paymentTotalEl) {
            paymentTotalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        console.log('checkoutWithoutClient: Chamando renderPaymentMethods...');
        if (typeof renderPaymentMethods === 'function') {
            renderPaymentMethods();
        } else {
            console.error('renderPaymentMethods não é uma função!');
        }
        
        console.log('checkoutWithoutClient: Chamando updatePaymentTotals...');
        if (typeof updatePaymentTotals === 'function') {
            updatePaymentTotals();
        } else {
            console.error('updatePaymentTotals não é uma função!');
        }
        
        // Abrir modal de pagamento (igual ao checkout normal)
        console.log('checkoutWithoutClient: Abrindo modal...');
        const modal = document.getElementById('payment-modal');
        if (modal) {
            modal.style.display = 'flex';
            // Auto-preencher o valor total no campo de pagamento
            const amountInput = document.getElementById('new-payment-amount');
            if (amountInput) {
                amountInput.value = total.toFixed(2);
                amountInput.focus();
                amountInput.select();
            }
            console.log('checkoutWithoutClient: Modal aberto com sucesso!');
        } else {
            console.error('Elemento payment-modal não encontrado!');
        }
    } catch (error) {
        console.error('Erro ao buscar carrinho:', error);
        showNotification('Erro ao buscar carrinho', 'error');
    }
}

// Adicionar método de pagamento
window.addPaymentMethod = function addPaymentMethod() {
    const method = document.getElementById('new-payment-method').value;
    const amount = parseFloat(document.getElementById('new-payment-amount').value);
    
    console.log('Adding payment method:', method, amount);

    if (!method) {
        showNotification('Selecione uma forma de pagamento', 'error');
        return;
    }
    
    if (!amount || amount <= 0) {
        showNotification('Informe um valor válido', 'error');
        return;
    }
    
    paymentMethods.push({
        id: Date.now() + Math.random(),
        method: method,
        amount: amount
    });
    
    document.getElementById('new-payment-method').value = '';
    document.getElementById('new-payment-amount').value = '';
    
    // Focar no dropdown de método para a próxima inserção
    document.getElementById('new-payment-method').focus();
    
    renderPaymentMethods();
    updatePaymentTotals();
}

// Inicializar listeners do modal de pagamento usando delegação
document.addEventListener('keypress', function(e) {
    if (e.target && e.target.id === 'new-payment-amount' && e.key === 'Enter') {
        e.preventDefault();
        addPaymentMethod();
    }
});

document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'new-payment-method') {
        const amountInput = document.getElementById('new-payment-amount');
        if (amountInput && (!amountInput.value || amountInput.value === '0' || amountInput.value === '')) {
            // Calcular restante
            const total = checkoutData?.total || 0;
            const totalPaid = paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
            const remaining = Math.max(0, total - totalPaid);
            amountInput.value = remaining.toFixed(2);
        }
    }
});

// Remover método de pagamento
window.removePaymentMethod = function removePaymentMethod(id) {
    paymentMethods = paymentMethods.filter(pm => pm.id !== id);
    renderPaymentMethods();
    updatePaymentTotals();
}

// Renderizar lista de métodos de pagamento
function renderPaymentMethods() {
    const container = document.getElementById('payment-methods-list');
    
    if (paymentMethods.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhuma forma de pagamento adicionada</p>';
        return;
    }
    
    container.innerHTML = paymentMethods.map(pm => `
        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
                <span class="font-medium text-gray-900 dark:text-gray-100 capitalize">${pm.method}</span>
                <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">R$ ${pm.amount.toFixed(2).replace('.', ',')}</span>
            </div>
            <button onclick="removePaymentMethod(${pm.id})" 
                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');
}

// Atualizar totais do pagamento
function updatePaymentTotals() {
    const total = checkoutData?.total || 0;
    const totalPaid = paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
    const remaining = total - totalPaid;
    
    document.getElementById('payment-total-paid').textContent = `R$ ${totalPaid.toFixed(2).replace('.', ',')}`;
    
    const remainingElement = document.getElementById('payment-remaining');
    remainingElement.textContent = `R$ ${Math.abs(remaining).toFixed(2).replace('.', ',')}`;
    remainingElement.className = remaining >= 0 
        ? 'text-lg font-semibold text-gray-900 dark:text-gray-100' 
        : 'text-lg font-semibold text-orange-600 dark:text-orange-400';
    
    // Habilitar botão apenas se houver pelo menos um método de pagamento
    const confirmBtn = document.getElementById('confirm-payment-btn');
    confirmBtn.disabled = paymentMethods.length === 0;
}

// Fechar modal de pagamento
window.closePaymentModal = function closePaymentModal() {
    document.getElementById('payment-modal').style.display = 'none';
    paymentMethods = [];
    checkoutData = null;
}

// Confirmar pagamento e finalizar venda
window.confirmPayment = async function confirmPayment() {
    console.log('Confirm payment clicked. Methods:', paymentMethods.length);
    
    if (paymentMethods.length === 0) {
        showNotification('Adicione pelo menos uma forma de pagamento', 'error');
        return;
    }
    
    const confirmBtn = document.getElementById('confirm-payment-btn');
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Processando...';
    
    try {
        // Preparar dados do checkout, garantindo que client_id seja null se vazio
        const checkoutPayload = {
            ...checkoutData,
            payment_methods: paymentMethods
        };
        
        // Garantir que client_id seja null se vazio, undefined ou string vazia
        if (!checkoutPayload.client_id || 
            checkoutPayload.client_id === '' || 
            checkoutPayload.client_id === 'null' || 
            checkoutPayload.client_id === 'undefined') {
            checkoutPayload.client_id = null;
        }
        
        console.log('Enviando checkout payload:', JSON.stringify(checkoutPayload, null, 2));
        
        const checkoutUrl = '{{ route("pdv.checkout") }}';
        console.log('Sending checkout POST to:', checkoutUrl);
        
        const response = await fetch(checkoutUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(checkoutPayload)
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
             const text = await response.text();
             console.error('Checkout failed. Status:', response.status, 'Response:', text);
             throw new Error(`Erro do servidor: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Verificar se há solicitações de estoque criadas
            let message = `Pedido #${data.order_number} criado com sucesso!`;
            let notificationType = 'success';
            
            if (data.stock_requests_created && data.stock_requests_created.length > 0) {
                const requestsCount = data.stock_requests_created.length;
                const requestsInfo = data.stock_requests_created.map(r => 
                    `${r.size}: ${r.quantity}`
                ).join(', ');
                message += ` ${requestsCount} solicitação(ões) de estoque criada(s): ${requestsInfo}`;
                notificationType = 'warning';
            }
            
            showNotification(message, notificationType);
            
            // Fechar modal
            closePaymentModal();
            
            // Gerar nota da venda (abrir em nova aba)
            if (data.receipt_url) {
                window.open(data.receipt_url, '_blank');
            }
            
            // Limpar carrinho e redirecionar para o pedido
            sessionStorage.removeItem('pdv_cart');
            setTimeout(() => {
                window.location.href = '{{ route("orders.show", ":id") }}'.replace(':id', data.order_id);
            }, 1500);
        } else {
            const errorMessage = data.message || (data.errors ? JSON.stringify(data.errors) : 'Erro ao finalizar venda');
            console.error('Erro no checkout:', data);
            showNotification(errorMessage, 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Finalizar Venda';
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao finalizar venda: ' + error.message, 'error');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Finalizar Venda';
    }
}

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remover após 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Função para buscar clientes
window.searchClient = function searchClient() {
    const query = document.getElementById('search-client').value;
    const resultsDiv = document.getElementById('search-results');
    
    // Show results container
    resultsDiv.classList.remove('hidden');
    
    if (query.length < 3) {
        resultsDiv.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-2">Digite ao menos 3 caracteres para buscar</p>';
        return;
    }

    fetch(`/api/clients/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-2">Nenhum cliente encontrado</p>';
                return;
            }

            resultsDiv.innerHTML = data.map(client => `
                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border-2 border-gray-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md cursor-pointer transition-all"
                     onclick='selectClient(${JSON.stringify(client)})'>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">${client.name || 'Sem nome'}</p>
                            ${client.phone_primary ? `<p class="text-sm text-gray-600 dark:text-gray-400">${client.phone_primary}</p>` : ''}
                            ${client.cpf_cnpj ? `<p class="text-xs text-gray-500 dark:text-gray-500">${client.cpf_cnpj}</p>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Erro ao buscar clientes:', error);
            resultsDiv.innerHTML = '<p class="text-sm text-red-500 dark:text-red-400 p-2">Erro ao buscar clientes. Tente novamente.</p>';
        });
}

// Função para selecionar cliente
window.selectClient = function selectClient(client) {
    document.getElementById('client_id').value = client.id;
    
    // Mostrar cliente selecionado
    const displayDiv = document.getElementById('selected-client-display');
    const nameDiv = document.getElementById('selected-client-name');
    const infoDiv = document.getElementById('selected-client-info');
    
    nameDiv.textContent = client.name || 'Sem nome';
    
    let info = [];
    if (client.phone_primary) info.push(client.phone_primary);
    if (client.cpf_cnpj) info.push(client.cpf_cnpj);
    infoDiv.textContent = info.join(' • ') || '';
    
    displayDiv.classList.remove('hidden');
    
    // Limpar busca
    document.getElementById('search-client').value = '';
    // Corrigido linha duplicada
    document.getElementById('search-results').innerHTML = '';
    
    updateCheckoutButtonState();
}

// Função para limpar cliente selecionado
window.clearSelectedClient = function clearSelectedClient() {
    const clientIdElement = document.getElementById('client_id');
    if (clientIdElement) {
        clientIdElement.value = '';
        clientIdElement.removeAttribute('value'); // Garantir que não tenha valor
    }
    document.getElementById('selected-client-display').classList.add('hidden');
    document.getElementById('search-client').value = '';
    document.getElementById('search-client').value = '';
    document.getElementById('search-results').innerHTML = '';
    
    updateCheckoutButtonState();
}

// Permitir buscar ao pressionar Enter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-client');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchClient();
            }
        });
    }
});

// Calcular acréscimos de tamanho automaticamente quando GG, EXG, G1, G2, G3 são selecionados
// Usa os valores fixos do modelo SizeSurcharge


// Função para toggle do painel de estoque
window.toggleStockDetails = function() {
    const panel = document.getElementById('stock-details-panel');
    const icon = document.getElementById('stock-toggle-icon');
    if (panel && icon) {
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            panel.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
};

// --- AJAX Logic for Instant Search & Navigation ---

// Debounce wrapper
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Current state
let currentSearch = '{{ $search ?? "" }}';
let currentType = '{{ $type ?? "products" }}';

// Fetch function
async function fetchProducts(type, search, url = null) {
    const container = document.getElementById('products-grid-container');
    if (!container) return;

    // Show loading state (opacity)
    container.style.opacity = '0.5';
    container.style.transition = 'opacity 0.2s';
    
    // Build URL if not provided
    if (!url) {
        url = `{{ route('pdv.index') }}?type=${type}&search=${encodeURIComponent(search)}`;
    }

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Erro na requisição');

        const data = await response.json();
        
        // Update Grid HTML
        if (data.html) {
            container.innerHTML = data.html;
        }
        
        // SYNC DATA STATE: Explicitly update page items
        if (data.jsItems && typeof window.updatePageItems === 'function') {
            window.updatePageItems(data.jsItems);
        }
        
        // Update URL browser history (optional, good for navigation)
        // Only update if it's a new state search/tab, simplistic approach
        const newUrl = new URL(url);
        window.history.pushState({path: newUrl.href}, '', newUrl.href);
        
        // Update local state
        currentType = type;
        currentSearch = search;

    } catch (error) {
        console.error('Erro ao buscar produtos:', error);
        showNotification('Erro ao carregar produtos', 'error');
    } finally {
        container.style.opacity = '1';
    }
}

// Event Listener: Instant Search
const searchInput = document.getElementById('product-search');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function(e) {
        const val = e.target.value;
        fetchProducts(currentType, val);
    }, 500)); // 500ms delay
}

// Event Listener: Tabs
document.querySelectorAll('.pdv-tab-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Update active class immediately
        document.querySelectorAll('.pdv-tab-link').forEach(l => {
            l.classList.remove('bg-indigo-600', 'text-white');
            l.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
        });
        
        this.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
        this.classList.add('bg-indigo-600', 'text-white');

        const type = this.getAttribute('data-type');
        // Clear search when switching tabs? Or keep it? keeping it for now seems fine or clearing it.
        // Usually clearing search is better UX when switching categories context significantly.
        // Let's keep search empty for clean category switch as requested "ele apenas mude a parte onde ficam os produtos"
        // But if user wants to search across categories...
        // Let's keep the current search value if the input has value.
        const searchVal = document.getElementById('product-search')?.value || '';
        
        fetchProducts(type, searchVal);
    });
});

// Event Listener: Pagination (Delegation)
// Pagination links are inside #products-grid-container > .mt-8 > nav or just replaced HTML
document.addEventListener('click', function(e) {
    const link = e.target.closest('.pagination a, .page-link'); // Laravel default pagination classes usually
    if (link && document.getElementById('products-grid-container').contains(link)) {
        e.preventDefault();
        const url = link.href;
        // Parse params to keep state correct
        const urlObj = new URL(url);
        const params = new URLSearchParams(urlObj.search);
        const type = params.get('type') || currentType;
        const search = params.get('search') || currentSearch;
        
        fetchProducts(type, search, url);
    }
});

</script>
@endsection

