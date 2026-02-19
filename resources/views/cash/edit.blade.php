@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-semibold dark:text-gray-100 mb-6">Editar Transação</h1>

        @if ($errors->any())
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
            <strong>Erro ao atualizar transação:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
            <form method="POST" action="{{ route('cash.update', $cash) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo *
                        </label>
                        <select id="type" 
                                name="type" 
                                required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm @error('type') border-red-500 dark:border-red-500 @enderror">
                            <option value="">Selecione...</option>
                            <option value="entrada" {{ old('type', $cash->type) === 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="saida" {{ old('type', $cash->type) === 'saida' ? 'selected' : '' }}>Saída</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status *
                        </label>
                        <select id="status" 
                                name="status" 
                                required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm @error('status') border-red-500 dark:border-red-500 @enderror">
                            <option value="">Selecione...</option>
                            <option value="pendente" {{ old('status', $cash->status ?? 'confirmado') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="confirmado" {{ old('status', $cash->status ?? 'confirmado') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                            <option value="cancelado" {{ old('status', $cash->status ?? 'confirmado') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoria *
                        </label>
                        <input type="text" 
                               id="category" 
                               name="category" 
                               value="{{ old('category', $cash->category) }}"
                               required
                               placeholder="Ex: Venda, Sangria, Despesa"
                               list="category-suggestions"
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm @error('category') border-red-500 dark:border-red-500 @enderror">
                        <datalist id="category-suggestions">
                            <option value="Venda">
                            <option value="Sangria">
                            <option value="Despesa">
                            <option value="Compra">
                            <option value="Pagamento">
                        </datalist>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descrição *
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              required
                              placeholder="Descreva a transação..."
                              class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm @error('description') border-red-500 dark:border-red-500 @enderror">{{ old('description', $cash->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Valor (R$) *
                        </label>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               step="0.01"
                               min="0.01"
                               value="{{ old('amount', $cash->amount) }}"
                               required
                               placeholder="0,00"
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm @error('amount') border-red-500 dark:border-red-500 @enderror">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Forma(s) de Pagamento *
                        </label>
                        <div class="space-y-3">
                            @php
                                $hasMultipleMethods = $cash->payment_methods && is_array($cash->payment_methods) && count($cash->payment_methods) > 1;
                                $paymentMethodsData = $cash->payment_methods ?? [];
                                if (empty($paymentMethodsData) && $cash->payment_method) {
                                    $paymentMethodsData = [['method' => $cash->payment_method, 'amount' => $cash->amount]];
                                }
                            @endphp
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="use-multiple-payment" onchange="togglePaymentMethods()" {{ $hasMultipleMethods ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                <label for="use-multiple-payment" class="text-sm text-gray-700 dark:text-gray-300">Usar múltiplas formas de pagamento</label>
                            </div>
                            
                            <!-- Método único (padrão) -->
                            <div id="single-payment-method" {{ $hasMultipleMethods ? 'class="hidden"' : '' }}>
                                <select id="payment_method" 
                                        name="payment_method" 
                                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm @error('payment_method') border-red-500 dark:border-red-500 @enderror">
                                    <option value="">Selecione...</option>
                                    <option value="dinheiro" {{ old('payment_method', $cash->payment_method) === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                    <option value="entrada_dinheiro" {{ old('payment_method', $cash->payment_method) === 'entrada_dinheiro' ? 'selected' : '' }}>Entrada em Dinheiro</option>
                                    <option value="pix" {{ old('payment_method', $cash->payment_method) === 'pix' ? 'selected' : '' }}>PIX</option>
                                    <option value="cartao" {{ old('payment_method', $cash->payment_method) === 'cartao' ? 'selected' : '' }}>Cartão</option>
                                    <option value="transferencia" {{ old('payment_method', $cash->payment_method) === 'transferencia' ? 'selected' : '' }}>Transferência</option>
                                    <option value="boleto" {{ old('payment_method', $cash->payment_method) === 'boleto' ? 'selected' : '' }}>Boleto</option>
                                    <option value="debito_conta" {{ old('payment_method', $cash->payment_method) === 'debito_conta' ? 'selected' : '' }}>Débito em Conta</option>
                                    <option value="credito_conta" {{ old('payment_method', $cash->payment_method) === 'credito_conta' ? 'selected' : '' }}>Crédito em Conta</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Múltiplos métodos -->
                            <div id="multiple-payment-methods" {{ !$hasMultipleMethods ? 'class="hidden"' : '' }}>
                                <div id="payment-methods-container" class="space-y-2 mb-2">
                                    <!-- Métodos serão adicionados aqui via JavaScript -->
                                </div>
                                <button type="button" onclick="addPaymentMethod()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    + Adicionar Forma de Pagamento
                                </button>
                                <input type="hidden" name="payment_methods" id="payment-methods-data" value="{{ json_encode($paymentMethodsData) }}">
                                <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-700/30 rounded">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Total: <span id="payment-total" class="font-semibold">R$ 0,00</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data da Transação *
                    </label>
                    <input type="date" 
                           id="transaction_date" 
                           name="transaction_date" 
                           value="{{ old('transaction_date', $cash->transaction_date->format('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm @error('transaction_date') border-red-500 dark:border-red-500 @enderror">
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observações
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="2"
                              placeholder="Observações adicionais (opcional)"
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500">{{ old('notes', $cash->notes) }}</textarea>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('cash.index') }}" 
                       class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                        ← Voltar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition">
                        Atualizar Transação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let paymentMethods = [];
        let paymentMethodIdCounter = 0;

        // Carregar dados existentes
        @if($hasMultipleMethods)
        const existingMethods = @json($paymentMethodsData);
        existingMethods.forEach((method, index) => {
            paymentMethods.push({
                id: paymentMethodIdCounter++,
                method: method.method || method['method'],
                amount: parseFloat(method.amount || method['amount']) || 0
            });
        });
        @endif

        // Inicializar ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            @if($hasMultipleMethods)
            renderPaymentMethods();
            @endif
        });

        function togglePaymentMethods() {
            const checkbox = document.getElementById('use-multiple-payment');
            const singleMethod = document.getElementById('single-payment-method');
            const multipleMethods = document.getElementById('multiple-payment-methods');
            const paymentMethodSelect = document.getElementById('payment_method');

            if (checkbox.checked) {
                singleMethod.classList.add('hidden');
                multipleMethods.classList.remove('hidden');
                paymentMethodSelect.removeAttribute('required');
                
                // Se não houver métodos adicionados, adicionar um
                if (paymentMethods.length === 0) {
                    addPaymentMethod();
                }
            } else {
                singleMethod.classList.remove('hidden');
                multipleMethods.classList.add('hidden');
                paymentMethodSelect.setAttribute('required', 'required');
                paymentMethods = [];
                updatePaymentMethodsData();
            }
        }

        function addPaymentMethod() {
            const id = paymentMethodIdCounter++;
            paymentMethods.push({
                id: id,
                method: '',
                amount: 0
            });
            renderPaymentMethods();
        }

        function removePaymentMethod(id) {
            paymentMethods = paymentMethods.filter(pm => pm.id !== id);
            renderPaymentMethods();
        }

        function renderPaymentMethods() {
            const container = document.getElementById('payment-methods-container');
            const amountInput = document.getElementById('amount');
            
            if (paymentMethods.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">Nenhum método de pagamento adicionado</p>';
                document.getElementById('payment-total').textContent = 'R$ 0,00';
                return;
            }

            container.innerHTML = paymentMethods.map((pm, index) => `
                <div class="flex gap-2 items-end p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Método ${index + 1}</label>
                        <select onchange="updatePaymentMethod(${pm.id}, 'method', this.value)" 
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                            <option value="">Selecione...</option>
                            <option value="dinheiro" ${pm.method === 'dinheiro' ? 'selected' : ''}>Dinheiro</option>
                            <option value="entrada_dinheiro" ${pm.method === 'entrada_dinheiro' ? 'selected' : ''}>Entrada em Dinheiro</option>
                            <option value="pix" ${pm.method === 'pix' ? 'selected' : ''}>PIX</option>
                            <option value="cartao" ${pm.method === 'cartao' ? 'selected' : ''}>Cartão</option>
                            <option value="transferencia" ${pm.method === 'transferencia' ? 'selected' : ''}>Transferência</option>
                            <option value="boleto" ${pm.method === 'boleto' ? 'selected' : ''}>Boleto</option>
                            <option value="debito_conta" ${pm.method === 'debito_conta' ? 'selected' : ''}>Débito em Conta</option>
                            <option value="credito_conta" ${pm.method === 'credito_conta' ? 'selected' : ''}>Crédito em Conta</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Valor</label>
                        <input type="number" step="0.01" min="0.01" 
                               value="${pm.amount}" 
                               onchange="updatePaymentMethod(${pm.id}, 'amount', parseFloat(this.value) || 0)"
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                    </div>
                    <button type="button" onclick="removePaymentMethod(${pm.id})" 
                            class="px-3 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `).join('');

            updatePaymentMethodsData();
        }

        function updatePaymentMethod(id, field, value) {
            const pm = paymentMethods.find(p => p.id === id);
            if (pm) {
                pm[field] = value;
                updatePaymentMethodsData();
            }
        }

        function updatePaymentMethodsData() {
            const total = paymentMethods.reduce((sum, pm) => sum + (parseFloat(pm.amount) || 0), 0);
            document.getElementById('payment-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            document.getElementById('payment-methods-data').value = JSON.stringify(paymentMethods);
            
            // Atualizar campo amount automaticamente
            const amountInput = document.getElementById('amount');
            if (amountInput && document.getElementById('use-multiple-payment').checked) {
                amountInput.value = total.toFixed(2);
            }
        }

        // Validar antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const useMultiple = document.getElementById('use-multiple-payment').checked;
            
            if (useMultiple) {
                if (paymentMethods.length === 0) {
                    e.preventDefault();
                    alert('Adicione pelo menos um método de pagamento');
                    return false;
                }
                
                const hasInvalid = paymentMethods.some(pm => !pm.method || !pm.amount || pm.amount <= 0);
                if (hasInvalid) {
                    e.preventDefault();
                    alert('Preencha todos os métodos de pagamento corretamente');
                    return false;
                }
            }
        });
    </script>
</body>
</html>

