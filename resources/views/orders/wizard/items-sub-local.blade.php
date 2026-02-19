@extends('layouts.admin')

@section('content')
<div class="py-6 bg-[#f6f7fb] dark:bg-[#0b0f1a] min-h-screen">
    <div class="max-w-7xl mx-auto px-4">
        
        <!-- Header com cliente -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('orders.wizard.personalization-type') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-white hover:border-[#7c3aed] hover:text-[#7c3aed] dark:hover:text-[#7c3aed] transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Voltar
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sublimação Local</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Selecione os produtos para o pedido</p>
                </div>
            </div>
            @if(session('wizard.client'))
            <div class="bg-gray-100 dark:bg-gray-800 rounded-xl px-4 py-2 flex items-center gap-3">
                <div class="w-10 h-10 bg-[#7c3aed] rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(session('wizard.client.name'), 0, 1) }}
                </div>
                <div>
                    <p class="text-gray-900 dark:text-white font-medium text-sm">{{ session('wizard.client.name') }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">{{ session('wizard.client.phone_primary') ?? 'Sem telefone' }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Barra de Progresso -->
        <div class="bg-gray-200 dark:bg-gray-800 rounded-full h-2 mb-8">
            <div class="bg-gradient-to-r from-[#7c3aed] to-purple-500 h-2 rounded-full transition-all" style="width: 50%"></div>
        </div>

        <div class="bg-white dark:bg-slate-900/80 rounded-3xl p-4 md:p-6 shadow-xl shadow-black/5 dark:shadow-black/40 border border-gray-100 dark:border-slate-800">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start">
                <div class="space-y-6">
                    <!-- Search Bar -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-lg shadow-black/5 dark:shadow-black/30 border border-gray-200 dark:border-slate-800">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-100 mb-3">
                            <i class="fa-solid fa-magnifying-glass text-[#7c3aed]"></i>
                            Buscar Produto
                        </div>
                        <div class="relative">
                            <input type="text" id="product-search" 
                                   placeholder="Nome do produto..." 
                                   style="padding-left: 2.75rem !important;"
                                   class="w-full h-11 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white pr-4 rounded-xl border border-gray-200 dark:border-slate-700 focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition-all text-sm">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Categorias -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-lg shadow-black/5 dark:shadow-black/30 border border-gray-200 dark:border-slate-800">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-100 mb-3">
                            <i class="fa-solid fa-list text-[#7c3aed]"></i>
                            Categorias
                        </div>
                        <div class="flex flex-wrap gap-2.5">
                            <button class="category-btn active flex items-center gap-3 px-4 h-11 rounded-2xl bg-gradient-to-r from-[#7c3aed] to-purple-500 text-white font-semibold shadow-lg whitespace-nowrap transition text-sm md:text-base border border-transparent" data-category="all">
                                <i class="fa-solid fa-cart-shopping text-sm"></i> Todos
                            </button>
                            <button class="category-btn flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-slate-900 text-gray-700 dark:text-gray-200 font-semibold whitespace-nowrap transition text-sm md:text-base border border-gray-200 dark:border-slate-700 shadow-sm" data-category="vestuario">
                                <i class="fa-solid fa-shirt text-sm text-gray-500 dark:text-gray-300"></i> Vestuário
                            </button>
                            <button class="category-btn flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-slate-900 text-gray-700 dark:text-gray-200 font-semibold whitespace-nowrap transition text-sm md:text-base border border-gray-200 dark:border-slate-700 shadow-sm" data-category="canecas">
                                <i class="fa-solid fa-mug-hot text-sm text-gray-500 dark:text-gray-300"></i> Canecas
                            </button>
                            <button class="category-btn flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-slate-900 text-gray-700 dark:text-gray-200 font-semibold whitespace-nowrap transition text-sm md:text-base border border-gray-200 dark:border-slate-700 shadow-sm" data-category="acessorios">
                                <i class="fa-solid fa-gem text-sm text-gray-500 dark:text-gray-300"></i> Acessórios
                            </button>
                            <button class="category-btn flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-slate-900 text-gray-700 dark:text-gray-200 font-semibold whitespace-nowrap transition text-sm md:text-base border border-gray-200 dark:border-slate-700 shadow-sm" data-category="diversos">
                                <i class="fa-solid fa-shapes text-sm text-gray-500 dark:text-gray-300"></i> Diversos
                            </button>
                        </div>
                    </div>

                    {{-- Grid de Produtos --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" id="products-grid">
                            {{-- Gerado via JS para suportar paginação e busca --}}
                        </div>

                        <!-- Paginação -->
                        <div id="pagination-container" class="flex items-center justify-center gap-2 pt-6">
                            {{-- Gerado via JS --}}
                        </div>
                    </div>
                </div>

                {{-- Carrinho Lateral (Desktop Only) --}}
                <div class="hidden lg:block">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 sticky top-6 shadow-lg shadow-black/10 dark:shadow-black/40 border border-gray-200 dark:border-slate-800">
                        <h2 class="text-gray-900 dark:text-white font-bold text-xl mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Carrinho
                        </h2>

                        {{-- Lista de Itens --}}
                        <div id="cart-items" class="space-y-3 max-h-[50vh] overflow-y-auto mb-4">
                            <div id="empty-cart" class="text-center py-8 text-gray-400 dark:text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <p>Nenhum item adicionado</p>
                            </div>
                        </div>

                        {{-- Totais --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span id="cart-subtotal">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between text-gray-900 dark:text-white font-bold text-lg">
                                <span>Total</span>
                                <span id="cart-total">R$ 0,00</span>
                            </div>
                        </div>

                        {{-- Botão Continuar --}}
                        <button id="btn-continue" disabled style="color: white !important;" 
                                class="w-full mt-6 bg-gradient-to-r from-[#7c3aed] to-purple-500 text-white font-bold py-4 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-purple-500/30">
                            Continuar
                        </button>
                    </div>
                </div>
            </div>
        </div>
{{-- Mobile Cart Sticky Footer --}}
        <div class="lg:hidden fixed bottom-16 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-3 z-40 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span id="mobile-cart-count-text">0 itens</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white" id="mobile-cart-total">R$ 0,00</p>
                </div>
                <button id="btn-continue-mobile" disabled style="color: white !important;" 
                        class="px-6 py-3 bg-[#7c3aed] text-white font-bold rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                    Continuar →
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Quantidade -->
<div id="quantity-modal" class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 transform scale-95 opacity-0 transition-all shadow-2xl border border-gray-200 dark:border-gray-700" id="modal-content">
        <div class="text-center mb-6">
            <div id="modal-icon" class="w-20 h-20 mx-auto mb-4 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center border border-purple-200 dark:border-purple-800">
                <!-- Icon inserted via JS -->
            </div>
            <h3 id="modal-product-name" class="text-xl font-bold text-gray-900 dark:text-white">Produto</h3>
            <p id="modal-product-price" class="text-[#7c3aed] dark:text-purple-400 font-bold text-lg mt-1">R$ 0,00</p>
        </div>

        <!-- Campo de Preço Editável (aparece só quando permitido) -->
        <div id="price-edit-section" class="mb-6 hidden">
            <label class="text-gray-600 dark:text-gray-400 text-sm block mb-2 text-center">
                <i class="fa-solid fa-pen-to-square text-green-500 mr-1"></i>
                Preço Personalizado
            </label>
            <div class="flex items-center justify-center gap-2">
                <span class="text-gray-500 dark:text-gray-400 text-lg font-medium">R$</span>
                <input type="text" id="modal-custom-price" 
                       class="w-32 h-12 bg-green-50 dark:bg-green-900/20 text-gray-900 dark:text-white text-center text-xl font-bold rounded-xl border-2 border-green-400 dark:border-green-600 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="0,00">
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                Preço original: <span id="original-price-display" class="font-medium"></span>
            </p>
        </div>

        <style>
            /* Esconder spinners nativos do input number para centralização perfeita */
            #modal-quantity::-webkit-outer-spin-button,
            #modal-quantity::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            #modal-quantity {
                -moz-appearance: textfield;
            }
        </style>

        <div class="mb-6">
            <label class="text-gray-600 dark:text-gray-400 text-sm block mb-3 text-center">Quantidade</label>
            <div class="flex items-center justify-center gap-4">
                <button id="btn-decrease" class="w-10 h-10 bg-[#7c3aed] hover:bg-purple-700 text-white rounded-full flex items-center justify-center text-xl font-bold transition shadow-lg shadow-purple-500/20">-</button>
                <input type="number" id="modal-quantity" value="1" min="1" max="999" 
                       class="w-16 h-12 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-center text-xl font-bold rounded-xl border border-gray-200 dark:border-gray-600 focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] p-0">
                <button id="btn-increase" class="w-10 h-10 bg-[#7c3aed] hover:bg-purple-700 text-white rounded-full flex items-center justify-center text-xl font-bold transition shadow-lg shadow-purple-500/20">+</button>
            </div>
        </div>

        <!-- Subtotal -->
        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
            <span class="text-gray-500 dark:text-gray-400 text-sm">Subtotal: </span>
            <span id="modal-subtotal" class="text-[#7c3aed] dark:text-purple-400 font-bold text-lg">R$ 0,00</span>
        </div>

        <div class="flex gap-3">
            <button id="btn-cancel" class="flex-1 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-xl font-semibold transition border border-gray-200 dark:border-gray-600">
                Cancelar
            </button>
            <button id="btn-add-to-cart" style="color: white !important;" 
                    class="flex-1 py-3 bg-[#7c3aed] text-white rounded-xl font-semibold transition shadow-lg shadow-purple-500/20">
                Adicionar
            </button>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carregar carrinho do localStorage (persistência entre navegações)
    const CART_STORAGE_KEY = 'wizard_sub_local_cart';
    let cart = loadCartFromStorage();
    let selectedProduct = null;
    
    // Dados iniciais de produtos para JS
    const allProducts = @json($products);
    let currentPage = 1;
    let itemsPerPage = 6;
    let currentCategory = 'all';
    let searchQuery = '';

    // Modal elements
    const modal = document.getElementById('quantity-modal');
    const modalContent = document.getElementById('modal-content');

    // Atualizar UI com o carrinho carregado
    if (cart.length > 0) {
        updateCartUI();
    }
    
    // Renderizar produtos inicial
    renderProducts();

    // Função para salvar carrinho no localStorage
    function saveCartToStorage() {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
    }

    // Função para carregar carrinho do localStorage
    function loadCartFromStorage() {
        const stored = localStorage.getItem(CART_STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    // Limpar carrinho do localStorage (quando finalizar pedido)
    function clearCartStorage() {
        localStorage.removeItem(CART_STORAGE_KEY);
    }


    // Category filter
    const activeCatClasses = ['bg-gradient-to-r','from-[#7c3aed]','to-purple-500','text-white','shadow-lg','border-transparent'];
    const inactiveCatClasses = ['bg-white','dark:bg-slate-900','text-gray-700','dark:text-gray-200','border','border-gray-200','dark:border-slate-700','shadow-sm'];

    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => {
                b.classList.remove('active', ...activeCatClasses);
                b.classList.add(...inactiveCatClasses);
            });
            this.classList.add('active', ...activeCatClasses);
            this.classList.remove(...inactiveCatClasses);

            currentCategory = this.dataset.category;
            currentPage = 1;
            renderProducts();
        });
    });

    // Search input
    document.getElementById('product-search').addEventListener('input', function(e) {
        searchQuery = e.target.value.toLowerCase();
        currentPage = 1;
        renderProducts();
    });

    function renderProducts() {
        const grid = document.getElementById('products-grid');
        
        // 1. Filtrar
        const filtered = allProducts.filter(p => {
            const matchesCategory = currentCategory === 'all' || p.category === currentCategory;
            const matchesSearch = p.name.toLowerCase().includes(searchQuery);
            return matchesCategory && matchesSearch;
        });

        // 2. Paginar
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const paginated = filtered.slice(start, start + itemsPerPage);

        // 3. Renderizar Grid
        grid.innerHTML = '';
        if (paginated.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhum produto encontrado</p>
                </div>
            `;
        } else {
            paginated.forEach(product => {
                const card = createProductCard(product);
                grid.appendChild(card);
            });
        }

        // 4. Renderizar Controles de Paginação
        renderPagination(totalPages);
    }

    function createProductCard(product) {
        const div = document.createElement('div');
        div.className = 'product-card bg-white dark:bg-slate-900 rounded-2xl overflow-hidden cursor-pointer transition-all group shadow-[0_18px_50px_-30px_rgba(124,58,237,0.55)] border border-gray-100 dark:border-slate-800 hover:shadow-[0_24px_60px_-32px_rgba(124,58,237,0.75)]';
        
        const imageUrl = product.image ? `/storage/${product.image.replace('public/', '')}` : null;
        
        div.innerHTML = `
            <div class="aspect-[4/3] bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-900 p-4 flex items-center justify-center relative">
                ${imageUrl ? 
                    `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-500">` : 
                    `<svg class="w-12 h-12 md:w-16 md:h-16 text-gray-400 dark:text-gray-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>`
                }
                ${product.allow_price_edit ? 
                    `<div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-md">
                        <i class="fa-solid fa-pen text-[10px] mr-1"></i>Editável
                    </div>` : ''
                }
            </div>
            <div class="p-4 flex flex-col gap-2">
                <div>
                    <h3 class="text-gray-900 dark:text-white font-bold text-base md:text-lg leading-snug">${product.name}</h3>
                </div>
                <p class="text-[#7c3aed] dark:text-purple-300 font-bold text-lg">R$ ${parseFloat(product.price).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</p>
                <div class="flex">
                    <span class="inline-flex items-center justify-center w-full py-2 rounded-xl bg-gradient-to-r from-[#7c3aed] to-purple-500 text-white text-sm font-semibold shadow-lg shadow-purple-500/30">
                        + Adicionar
                    </span>
                </div>
            </div>
        `;

        div.addEventListener('click', () => handleProductClick(product));
        return div;
    }

    function renderPagination(totalPages) {
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        
        if (totalPages <= 1) return;

        // Botão Anterior
        const prevBtn = document.createElement('button');
        prevBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center border transition ${currentPage === 1 ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:border-[#7c3aed] hover:text-[#7c3aed] dark:border-slate-700 dark:text-gray-400'}`;
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => { currentPage--; renderProducts(); };
        container.appendChild(prevBtn);

        // Números das Páginas
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            const isActive = currentPage === i;
            pageBtn.className = `w-10 h-10 rounded-xl font-bold transition ${isActive ? 'bg-[#7c3aed] text-white shadow-lg shadow-purple-500/30 border-transparent' : 'border border-gray-200 text-gray-600 hover:border-[#7c3aed] hover:text-[#7c3aed] dark:border-slate-700 dark:text-gray-400'}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => { currentPage = i; renderProducts(); };
            container.appendChild(pageBtn);
        }

        // Botão Próximo
        const nextBtn = document.createElement('button');
        nextBtn.className = `w-10 h-10 rounded-xl flex items-center justify-center border transition ${currentPage === totalPages ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:border-[#7c3aed] hover:text-[#7c3aed] dark:border-slate-700 dark:text-gray-400'}`;
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => { currentPage++; renderProducts(); };
        container.appendChild(nextBtn);
    }

    function handleProductClick(product) {
        let iconHtml = '';
        const imageUrl = product.image ? `/storage/${product.image.replace('public/', '')}` : null;

        if (imageUrl) {
            iconHtml = `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover rounded-xl">`;
        } else {
            iconHtml = `<svg class="w-12 h-12 md:w-16 md:h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>`;
        }

        selectedProduct = {
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            originalPrice: parseFloat(product.price),
            requiresCustomization: product.requires_customization === true || product.requires_customization === 1,
            allowPriceEdit: product.allow_price_edit === true || product.allow_price_edit === 1,
            hasQuantityPricing: product.has_quantity_pricing === true || product.has_quantity_pricing === 1,
            quantityPricing: product.quantity_pricing || [],
            icon: iconHtml
        };

        document.getElementById('modal-product-name').textContent = selectedProduct.name;
        document.getElementById('modal-product-price').textContent = formatCurrency(selectedProduct.price);
        document.getElementById('modal-icon').innerHTML = selectedProduct.icon;
        document.getElementById('modal-quantity').value = 1;

        // Gerenciar campo de preço editável
        const priceEditSection = document.getElementById('price-edit-section');
        const customPriceInput = document.getElementById('modal-custom-price');
        const originalPriceDisplay = document.getElementById('original-price-display');
        
        if (selectedProduct.allowPriceEdit) {
            priceEditSection.classList.remove('hidden');
            customPriceInput.value = formatNumber(selectedProduct.price);
            originalPriceDisplay.textContent = formatCurrency(selectedProduct.originalPrice);
        } else {
            priceEditSection.classList.add('hidden');
        }

        // Atualizar subtotal inicial
        updateModalSubtotal();
        openModal();
    }

    // Modal logic (kept from previous version but adapted where needed)
    // ... (rest of the JS logic remains the same)

    // Quantity controls
    document.getElementById('btn-decrease').addEventListener('click', function() {
        const input = document.getElementById('modal-quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updateModalSubtotal();
        }
    });

    document.getElementById('btn-increase').addEventListener('click', function() {
        const input = document.getElementById('modal-quantity');
        input.value = parseInt(input.value) + 1;
        updateModalSubtotal();
    });

    // Atualizar subtotal quando digitar quantidade
    document.getElementById('modal-quantity').addEventListener('input', function() {
        updateModalSubtotal();
    });

    // Atualizar subtotal quando editar preço
    document.getElementById('modal-custom-price').addEventListener('input', function() {
        updateModalSubtotal();
    });

    // Cancel button
    document.getElementById('btn-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // Add to cart
    document.getElementById('btn-add-to-cart').addEventListener('click', function() {
        const quantity = parseInt(document.getElementById('modal-quantity').value);
        
        // Pegar preço customizado se permitido
        let finalPrice = selectedProduct.price;
        if (selectedProduct.allowPriceEdit) {
            const customPriceInput = document.getElementById('modal-custom-price');
            const customPrice = parseNumber(customPriceInput.value);
            if (customPrice > 0) {
                finalPrice = customPrice;
            }
        }
        
        // Verificar se já existe no carrinho com o MESMO preço
        const existing = cart.find(item => item.id === selectedProduct.id && item.price === finalPrice);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({
                ...selectedProduct,
                price: finalPrice,
                quantity: quantity
            });
        }

        updateCartUI();
        closeModal();
    });

    // Continue button (desktop)
    document.getElementById('btn-continue').addEventListener('click', handleContinue);
    
    // Continue button (mobile)
    const btnMobile = document.getElementById('btn-continue-mobile');
    if (btnMobile) btnMobile.addEventListener('click', handleContinue);
    
    function handleContinue() {
        if (cart.length === 0) return;

        // Check if ANY item in cart requires customization
        const requiresCustomization = cart.some(item => item.requiresCustomization === true);

        // Save cart to session via AJAX
        fetch('{{ route("orders.wizard.sewing") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                items: cart,
                type: 'sub_local',
                action: 'save_sub_local_items'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpar localStorage após enviar com sucesso
                clearCartStorage();
                
                if (requiresCustomization) {
                    window.location.href = '{{ route("orders.wizard.customization") }}';
                } else {
                    window.location.href = '{{ route("orders.wizard.payment") }}';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    function updateCartUI() {
        const container = document.getElementById('cart-items');
        const emptyState = document.getElementById('empty-cart');
        const btnContinue = document.getElementById('btn-continue');
        const btnContinueMobile = document.getElementById('btn-continue-mobile');
        const mobileCountText = document.getElementById('mobile-cart-count-text');
        const mobileTotal = document.getElementById('mobile-cart-total');

        if (cart.length === 0) {
            emptyState.style.display = 'block';
            btnContinue.disabled = true;
            if (btnContinueMobile) btnContinueMobile.disabled = true;
        } else {
            emptyState.style.display = 'none';
            btnContinue.disabled = false;
            if (btnContinueMobile) btnContinueMobile.disabled = false;
        }

        // Remove old items (keep empty state)
        container.querySelectorAll('.cart-item').forEach(el => el.remove());

        let total = 0;
        let totalItems = 0;
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            totalItems += item.quantity;

            const itemEl = document.createElement('div');
            itemEl.className = 'cart-item bg-purple-50 dark:bg-gray-700/50 rounded-xl p-3 flex items-center gap-3 border border-purple-100 dark:border-transparent';
            itemEl.innerHTML = `
                <div class="flex-1">
                    <p class="text-gray-900 dark:text-white font-medium text-sm">${item.name}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">${item.quantity}x ${formatCurrency(item.price)}</p>
                </div>
                <p class="text-[#7c3aed] dark:text-purple-400 font-bold">${formatCurrency(itemTotal)}</p>
                <button class="text-gray-400 hover:text-red-500 transition" onclick="removeFromCart(${index})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(itemEl);
        });

        document.getElementById('cart-subtotal').textContent = formatCurrency(total);
        document.getElementById('cart-total').textContent = formatCurrency(total);
        
        // Update mobile footer
        if (mobileCountText) mobileCountText.textContent = totalItems + (totalItems === 1 ? ' item' : ' itens');
        if (mobileTotal) mobileTotal.textContent = formatCurrency(total);
        
        // Salvar carrinho no localStorage
        saveCartToStorage();
    }

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCartUI();
        // O updateCartUI já salva no localStorage
    };

    function formatCurrency(value) {
        return 'R$ ' + value.toFixed(2).replace('.', ',');
    }

    function formatNumber(value) {
        return value.toFixed(2).replace('.', ',');
    }

    function parseNumber(str) {
        if (!str) return 0;
        // Remove tudo exceto números, vírgulas e pontos
        const cleaned = str.replace(/[^\d,\.]/g, '');
        // Substitui vírgula por ponto para parsing
        const normalized = cleaned.replace(',', '.');
        return parseFloat(normalized) || 0;
    }

    function updateModalSubtotal() {
        const quantity = parseInt(document.getElementById('modal-quantity').value) || 1;
        let price = selectedProduct.price;
        
        // Se tem preço por quantidade, aplicar
        if (selectedProduct.hasQuantityPricing && selectedProduct.quantityPricing.length > 0) {
            for (const tier of selectedProduct.quantityPricing) {
                if (quantity >= tier.min_quantity && quantity <= tier.max_quantity) {
                    price = tier.price;
                    break;
                }
            }
        }
        
        // Se permite edição de preço, pegar o valor digitado
        if (selectedProduct.allowPriceEdit) {
            const customPriceInput = document.getElementById('modal-custom-price');
            const customPrice = parseNumber(customPriceInput.value);
            if (customPrice > 0) {
                price = customPrice;
            }
        }
        
        const subtotal = price * quantity;
        document.getElementById('modal-subtotal').textContent = formatCurrency(subtotal);
        
        // Atualizar preço exibido quando tem tabela por quantidade
        if (selectedProduct.hasQuantityPricing && selectedProduct.quantityPricing.length > 0) {
            document.getElementById('modal-product-price').textContent = formatCurrency(price) + '/un';
        }
    }
});
</script>

<style>
.product-card.selected {
    ring: 2px;
    ring-color: #7c3aed;
}
</style>
@endsection
