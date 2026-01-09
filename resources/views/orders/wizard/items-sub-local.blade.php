@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header com cliente -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('orders.wizard.personalization-type') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sublimação Local</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Selecione os produtos para o pedido</p>
                </div>
            </div>
            @if(session('wizard.client'))
            <div class="bg-gray-100 dark:bg-gray-800 rounded-xl px-4 py-2 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
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
            <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: 50%"></div>
        </div>

        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Catálogo de Produtos (3 colunas) -->
            <div class="lg:col-span-3">
                <div class="flex gap-3 mb-6 overflow-x-auto pb-2">
                    <button class="category-btn active px-5 py-2.5 bg-indigo-600 text-white rounded-full font-medium whitespace-nowrap transition" data-category="all">
                        Todos
                    </button>
                    <button class="category-btn px-5 py-2.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full font-medium whitespace-nowrap hover:bg-gray-300 dark:hover:bg-gray-700 transition" data-category="vestuario">
                        Vestuário
                    </button>
                    <button class="category-btn px-5 py-2.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full font-medium whitespace-nowrap hover:bg-gray-300 dark:hover:bg-gray-700 transition" data-category="canecas">
                        Canecas
                    </button>
                    <button class="category-btn px-5 py-2.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full font-medium whitespace-nowrap hover:bg-gray-300 dark:hover:bg-gray-700 transition" data-category="acessorios">
                        Acessórios
                    </button>
                    <button class="category-btn px-5 py-2.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full font-medium whitespace-nowrap hover:bg-gray-300 dark:hover:bg-gray-700 transition" data-category="diversos">
                        Diversos
                    </button>
                </div>

                <!-- Grid de Produtos -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" id="products-grid">
                    
                    @foreach($products as $product)
                    <!-- Product Card -->
                    <div class="product-card bg-white dark:bg-gray-800 rounded-2xl overflow-hidden cursor-pointer hover:ring-2 hover:ring-indigo-500 transition-all group shadow-sm border border-gray-200 dark:border-gray-700" 
                         data-category="{{ $product->category }}" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" 
                         data-requires-customization="{{ $product->requires_customization ? 'true' : 'false' }}">
                        <div class="aspect-square bg-gray-100 dark:bg-gradient-to-br dark:from-gray-700 dark:to-gray-800 p-4 flex items-center justify-center relative">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <svg class="w-20 h-20 text-gray-400 dark:text-gray-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-gray-900 dark:text-white font-bold text-lg truncate">{{ $product->name }}</h3>
                            <p class="text-indigo-600 dark:text-indigo-400 font-bold text-xl mt-1">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sticky top-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h2 class="text-gray-900 dark:text-white font-bold text-xl mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Carrinho
                    </h2>

                    <!-- Lista de Itens -->
                    <div id="cart-items" class="space-y-3 max-h-[50vh] overflow-y-auto mb-4">
                        <div id="empty-cart" class="text-center py-8 text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <p>Nenhum item adicionado</p>
                        </div>
                    </div>

                    <!-- Totais -->
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

                    <!-- Botão Continuar -->
                    <button id="btn-continue" disabled
                            class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Continuar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Quantidade -->
<div id="quantity-modal" class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 transform scale-95 opacity-0 transition-all shadow-2xl border border-gray-200 dark:border-gray-700" id="modal-content">
        <div class="text-center mb-6">
            <div id="modal-icon" class="w-20 h-20 mx-auto mb-4 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center border border-indigo-200 dark:border-indigo-800">
                <!-- Icon inserted via JS -->
            </div>
            <h3 id="modal-product-name" class="text-xl font-bold text-gray-900 dark:text-white">Produto</h3>
            <p id="modal-product-price" class="text-indigo-600 dark:text-indigo-400 font-bold text-lg mt-1">R$ 0,00</p>
        </div>

        <div class="mb-6">
            <label class="text-gray-600 dark:text-gray-400 text-sm block mb-3 text-center">Quantidade</label>
            <div class="flex items-center justify-center gap-3">
                <button id="btn-decrease" class="w-12 h-12 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-xl text-2xl font-bold transition border border-gray-200 dark:border-gray-600">-</button>
                <input type="number" id="modal-quantity" value="1" min="1" max="999" 
                       class="w-20 h-12 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-center text-xl font-bold rounded-xl border border-gray-200 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button id="btn-increase" class="w-12 h-12 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-xl text-2xl font-bold transition border border-gray-200 dark:border-gray-600">+</button>
            </div>
        </div>

        <div class="flex gap-3">
            <button id="btn-cancel" class="flex-1 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-xl font-semibold transition border border-gray-200 dark:border-gray-600">
                Cancelar
            </button>
            <button id="btn-add-to-cart" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition shadow-lg shadow-indigo-500/20">
                Adicionar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = [];
    let selectedProduct = null;

    // Modal elements
    const modal = document.getElementById('quantity-modal');
    const modalContent = document.getElementById('modal-content');

    // Category filter
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => {
                b.classList.remove('active', 'bg-indigo-600', 'text-white');
                b.classList.add('bg-gray-800', 'text-gray-300');
            });
            this.classList.add('active', 'bg-indigo-600', 'text-white');
            this.classList.remove('bg-gray-800', 'text-gray-300');

            const category = this.dataset.category;
            document.querySelectorAll('.product-card').forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Product click - open modal
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            let iconHtml = '';
            const img = this.querySelector('img');
            const svg = this.querySelector('svg');

            if (img) {
                iconHtml = img.outerHTML;
            } else if (svg) {
                iconHtml = svg.outerHTML;
            }

            selectedProduct = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                requiresCustomization: this.dataset.requiresCustomization === 'true',
                icon: iconHtml
            };

            document.getElementById('modal-product-name').textContent = selectedProduct.name;
            document.getElementById('modal-product-price').textContent = formatCurrency(selectedProduct.price);
            document.getElementById('modal-icon').innerHTML = selectedProduct.icon;
            document.getElementById('modal-quantity').value = 1;

            openModal();
        });
    });

    // Quantity controls
    document.getElementById('btn-decrease').addEventListener('click', function() {
        const input = document.getElementById('modal-quantity');
        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    });

    document.getElementById('btn-increase').addEventListener('click', function() {
        const input = document.getElementById('modal-quantity');
        input.value = parseInt(input.value) + 1;
    });

    // Cancel button
    document.getElementById('btn-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // Add to cart
    document.getElementById('btn-add-to-cart').addEventListener('click', function() {
        const quantity = parseInt(document.getElementById('modal-quantity').value);
        
        // Check if product already in cart
        const existing = cart.find(item => item.id === selectedProduct.id);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({
                ...selectedProduct,
                quantity: quantity
            });
        }

        updateCartUI();
        closeModal();
    });

    // Continue button
    document.getElementById('btn-continue').addEventListener('click', function() {
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
    });

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

        if (cart.length === 0) {
            emptyState.style.display = 'block';
            btnContinue.disabled = true;
        } else {
            emptyState.style.display = 'none';
            btnContinue.disabled = false;
        }

        // Remove old items (keep empty state)
        container.querySelectorAll('.cart-item').forEach(el => el.remove());

        let total = 0;
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            const itemEl = document.createElement('div');
            itemEl.className = 'cart-item bg-gray-700/50 rounded-xl p-3 flex items-center gap-3';
            itemEl.innerHTML = `
                <div class="flex-1">
                    <p class="text-white font-medium text-sm">${item.name}</p>
                    <p class="text-gray-400 text-xs">${item.quantity}x ${formatCurrency(item.price)}</p>
                </div>
                <p class="text-indigo-400 font-bold">${formatCurrency(itemTotal)}</p>
                <button class="text-gray-500 hover:text-red-500 transition" onclick="removeFromCart(${index})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(itemEl);
        });

        document.getElementById('cart-subtotal').textContent = formatCurrency(total);
        document.getElementById('cart-total').textContent = formatCurrency(total);
    }

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCartUI();
    };

    function formatCurrency(value) {
        return 'R$ ' + value.toFixed(2).replace('.', ',');
    }
});
</script>

<style>
.product-card.selected {
    ring: 2px;
    ring-color: rgb(99 102 241);
}
</style>
@endsection
