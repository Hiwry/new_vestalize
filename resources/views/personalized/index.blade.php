@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-20">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header & Controls -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Venda Personalizada</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Gestão de vendas de produtos personalizados (Canecas, Camisas, etc).</p>
            </div>
            
            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 p-1 rounded-xl border border-gray-200 dark:border-gray-700 overflow-x-auto">
                @foreach($categories as $key => $label)
                <button onclick="filterCategory('{{ $key }}')"
                   id="cat-btn-{{ $key }}"
                   class="category-btn px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == $key ? 'bg-pink-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   style="{{ $type == $key ? 'color: white !important;' : '' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 md:gap-8 items-start">
            {{-- Left Column: Products Grid --}}
            <div class="col-span-12 lg:col-span-8 space-y-4 md:space-y-6">
                
                {{-- Search Bar --}}
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 md:pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-gray-400 group-focus-within:text-pink-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           id="product-search"
                           placeholder="Buscar item personalizado..." 
                           class="block w-full pl-10 md:pl-12 pr-4 py-3 md:py-4 border-none rounded-xl md:rounded-2xl bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-pink-500/20 shadow-sm dark:shadow-gray-900/50 text-sm md:text-lg transition-all">
                </div>

                {{-- Products Grid Container --}}
                <div id="products-grid-container">
                    @include('personalized.partials.grid')
                </div>
            </div>

            {{-- Right Column: Cart --}}
            <div class="hidden lg:block col-span-12 lg:col-span-4 relative">
                <div class="sticky top-6">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-[calc(100vh-6rem)]">
                        
                        <!-- Header -->
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Carrinho Personalizado
                            </h3>
                            
                            <!-- Client Selector (Simplified) -->
                             <div class="mt-4">
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cliente</label>
                                <select id="client-select" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                                    <option value="">Cliente Balcão / Não identificado</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                             </div>
                        </div>

                        <!-- Cart Items -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/30 dark:bg-gray-900/30" id="cart-container">
                            @include('personalized.partials.cart-items')
                        </div>

                        <!-- Footer -->
                        <div class="bg-white dark:bg-gray-800 p-5 border-t border-gray-100 dark:border-gray-700 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">
                            <div class="flex justify-between items-center mb-5 pb-4 border-b border-dashed border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</span>
                                <span class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight" id="cart-total">
                                    R$ {{ number_format(array_sum(array_column($cart, 'total_price')), 2, ',', '.') }}
                                </span>
                            </div>

                            <button onclick="checkout()" class="w-full py-4 bg-pink-600 hover:bg-pink-700 text-white rounded-xl font-bold shadow-lg shadow-pink-200 dark:shadow-none transition-all active:scale-[0.99] flex justify-center items-center gap-3" style="color: white !important;">
                                <span style="color: white !important;">FINALIZAR PEDIDO</span>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: white !important;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </button>
                            
                            <button onclick="clearCart()" class="w-full mt-3 py-2 text-xs text-red-500 hover:text-red-700 text-center">
                                Limpar Carrinho
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Item -->
<div id="add-item-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Adicionar Item</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6 space-y-4">
            <input type="hidden" id="modal-product-id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade</label>
                <input type="number" id="modal-quantity" value="1" min="1" class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome para Estampar (Opcional)</label>
                <input type="text" id="modal-client-name" placeholder="Ex: Maria" class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações de Personalização</label>
                <textarea id="modal-notes" rows="3" placeholder="Ex: Foto enviada no WhatsApp..." class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

             <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço Unitário (R$)</label>
                <input type="number" id="modal-price" step="0.01" class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="text-xs text-gray-500 mt-1">Deixe em branco para usar o preço da tabela.</p>
            </div>
            
            <div id="modal-addons-wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adicionais</label>
                <div id="modal-addons-container" class="space-y-2 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg max-h-40 overflow-y-auto">
                    <!-- Checkboxes will be injected here -->
                </div>
            </div>

            <button onclick="confirmAddToCart()" class="w-full py-3 bg-pink-600 hover:bg-pink-700 text-white rounded-xl font-bold" style="color: #ffffff !important;">
                <span class="text-white" style="color: #ffffff !important;">Adicionar ao Carrinho</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal Finalizar Pedido -->
<div id="checkout-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Finalizar Pedido</h3>
            <button onclick="closeCheckoutModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6 space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Forma de Pagamento</label>
                <select id="checkout-payment-method" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm dark:text-white">
                    <option value="pix">Pix</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cartao_credito">Cartão de Crédito</option>
                    <option value="cartao_debito">Cartão de Débito</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desconto (R$)</label>
                <input type="number" id="checkout-discount" value="0" step="0.01" min="0" class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações do Pedido</label>
                <textarea id="checkout-notes" rows="3" placeholder="Ex: Entregar amanhã..." class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>
            
            <button id="confirm-checkout-btn" onclick="confirmCheckout()" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold shadow-lg shadow-green-200 dark:shadow-none transition-all active:scale-[0.99]" style="color: #ffffff !important;">
                <span class="text-white" style="color: #ffffff !important;">Confirmar Venda</span>
            </button>
        </div>
    </div>
</div>

<script>
    function filterCategory(type) {
        // Visual update
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('bg-pink-600', 'text-white');
            btn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            btn.style.color = ''; // Reset inline style
        });
        
        const activeBtn = document.getElementById('cat-btn-' + type);
        if(activeBtn) {
            activeBtn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            activeBtn.classList.add('bg-pink-600', 'text-white');
            activeBtn.style.color = 'white !important';
            activeBtn.setAttribute('style', 'color: white !important;');
        }

        // AJAX Call
        const container = document.getElementById('products-grid-container');
        container.innerHTML = '<div class="col-span-12 flex justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pink-500"></div></div>';

        const url = new URL('{{ route("personalized.index") }}');
        url.searchParams.set('type', type);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p class="text-center text-red-500">Erro ao carregar itens.</p>';
        });
    }

    function openModal(id, name, price, addons = []) {
        document.getElementById('modal-product-id').value = id;
        document.getElementById('modal-title').textContent = name;
        document.getElementById('modal-price').value = price; // Suggested price
        
        // Render Addons
        const addonsWrapper = document.getElementById('modal-addons-wrapper');
        const addonsContainer = document.getElementById('modal-addons-container');
        addonsContainer.innerHTML = '';
        
        if (addons && addons.length > 0) {
            addonsWrapper.classList.remove('hidden');
            addons.forEach(addon => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer';
                div.onclick = (e) => {
                    if(e.target.type !== 'checkbox') {
                        const cb = div.querySelector('input');
                        cb.checked = !cb.checked;
                    }
                };
                
                div.innerHTML = `
                    <div class="flex items-center gap-2">
                        <input type="checkbox" value="${addon.id}" data-price="${addon.price}" data-name="${addon.name}" class="addon-checkbox w-4 h-4 text-pink-600 rounded border-gray-300 focus:ring-pink-500">
                        <span class="text-sm text-gray-700 dark:text-gray-200">${addon.name}</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">+ R$ ${parseFloat(addon.price).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span>
                `;
                addonsContainer.appendChild(div);
            });
        } else {
            addonsWrapper.classList.add('hidden');
        }

        document.getElementById('add-item-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('add-item-modal').classList.add('hidden');
    }

    function confirmAddToCart() {
        const productId = document.getElementById('modal-product-id').value;
        const quantity = document.getElementById('modal-quantity').value;
        const note = document.getElementById('modal-notes').value;
        const clientName = document.getElementById('modal-client-name').value;
        const price = document.getElementById('modal-price').value;

        const selectedAddons = [];
        document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
            selectedAddons.push({
                id: cb.value,
                name: cb.dataset.name,
                price: cb.dataset.price
            });
        });

        fetch('{{ route("personalized.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
                customization_note: note,
                client_name: clientName,
                price: price,
                addons: selectedAddons
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload(); 
            } else {
                alert('Erro ao adicionar item');
            }
        });
    }

    function clearCart() {
        if(!confirm('Limpar carrinho?')) return;
        fetch('{{ route("personalized.cart.clear") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => location.reload());
    }

    function checkout() {
        const cartTotal = parseFloat(document.getElementById('cart-total').innerText.replace('R$', '').replace('.', '').replace(',', '.').trim());
        if(cartTotal <= 0) {
            alert('Carrinho vazio!');
            return;
        }
        document.getElementById('checkout-modal').classList.remove('hidden');
    }

    function closeCheckoutModal() {
        document.getElementById('checkout-modal').classList.add('hidden');
    }

    function confirmCheckout() {
        console.log('confirmCheckout triggered');
        const clientId = document.getElementById('client-select').value;
        const method = document.getElementById('checkout-payment-method').value;
        const discount = document.getElementById('checkout-discount').value;
        const notes = document.getElementById('checkout-notes').value;

        console.log('Checkout data:', { clientId, method, discount, notes });

        // Show loading state
        const btn = document.getElementById('confirm-checkout-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="text-white">Processando...</span>';
        btn.disabled = true;

        fetch('{{ route("personalized.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                client_id: clientId,
                payment_method: method,
                discount: discount,
                notes: notes
            })
        })
        .then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json');
            const data = isJson ? await response.json() : null;

            if (response.ok && data && data.success) {
                alert(data.message);
                
                // Trigger PDF Download
                if (data.order_id) {
                    const pdfUrl = '{{ route("orders.client-receipt", ["id" => ":id"]) }}'.replace(':id', data.order_id);
                    window.open(pdfUrl, '_blank');
                }
                
                location.reload();
            } else {
                const errorMsg = data ? data.message : (response.status === 403 ? 'Erro 403: Acesso Negado/Proibido.' : 'Erro ' + response.status);
                console.error('Checkout error:', response.status, data);
                alert('Erro ao finalizar venda: ' + errorMsg);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Erro de conexão ou erro inesperado ao processar venda.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
    window.confirmCheckout = confirmCheckout;
</script>
@endsection
