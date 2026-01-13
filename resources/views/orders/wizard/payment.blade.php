@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">4</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Pagamento</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 4 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">80%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-2.5 shadow-inner">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out shadow-lg shadow-indigo-500/30 dark:shadow-indigo-600/30" style="width: 80%"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-900/50">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Configuração de Pagamento</h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Defina valores, formas de pagamento e datas</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
            <!-- Resumo do Pedido -->
                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800 p-5 mb-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                            <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Resumo do Pedido</h2>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-slate-300">Cliente:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $order->client->name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-slate-300">Total de Peças:</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="total-pieces">{{ $order->items->sum('quantity') }}</span>
                        </div>
                        
                        <div class="border-t border-indigo-200 dark:border-indigo-800 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-gray-700 dark:text-slate-300">Subtotal (Etapa 2 + 3):</span>
                                <span class="font-medium text-gray-900 dark:text-white" id="subtotal">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div id="surcharges-breakdown" class="space-y-1">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                        
                        <!-- Acréscimo Especial (personalizável) -->
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-indigo-100 dark:border-indigo-800/50">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-700 dark:text-slate-300 text-sm">Acréscimo Especial:</span>
                                <span class="text-xs text-gray-500 dark:text-slate-400">(digitar valor)</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-orange-600 dark:text-orange-400">+R$</span>
                                <input type="number" id="custom_surcharge" step="0.01" min="0" value="0"
                                       onchange="calculateTotal()" oninput="calculateTotal()"
                                       class="w-20 px-2 py-1 text-right text-sm rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-700 dark:text-slate-300">Taxa de Entrega:</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="delivery-fee-display">R$ 0,00</span>
                        </div>
                        
                        <div id="discount-summary" class="flex justify-between hidden">
                            <span class="text-gray-700 dark:text-slate-300">Desconto:</span>
                            <span class="font-medium text-green-600 dark:text-green-400" id="discount-display">-R$ 0,00</span>
                        </div>
                        
                        <div class="border-t border-indigo-200 dark:border-indigo-800 pt-2 mt-2">
                            <div class="flex justify-between text-base font-bold">
                                <span class="text-gray-900 dark:text-white">Total Final:</span>
                                <span id="total-final" class="text-indigo-600 dark:text-indigo-400">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Formulário -->
                <form method="POST" action="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.payment') : route('orders.wizard.payment') }}" id="payment-form" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="hidden" name="payment_methods" id="payment-methods-data">
                    <input type="hidden" name="size_surcharges" id="size-surcharges-data">
                    <input type="hidden" name="order_data" value="{{ json_encode($order->items->first()->sizes ?? []) }}">
                    <input type="hidden" name="discount_type" id="discount-type-data">
                    <input type="hidden" name="discount_value" id="discount-value-data">

                    <!-- Data de Entrada -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Data de Entrada *</h3>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                            <input type="date" id="entry_date" name="entry_date" required
                                   value="{{ $sessionPaymentData['entry_date'] ?? $order->payments->first()->entry_date ?? $order->entry_date ?? date('Y-m-d') }}"
                                   class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                        </div>
                    </div>

                    <!-- Taxa de Entrega -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-2a9 9 0 11-18 0 9 9 0 0118 0z M9 16h6"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Taxa de Entrega</h3>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                            <label for="delivery_fee" class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Valor da Taxa (R$)</label>
                            <input type="number" id="delivery_fee" name="delivery_fee" step="0.01" min="0" value="{{ $sessionPaymentData['delivery_fee'] ?? $order->delivery_fee ?? 0 }}"
                                   onchange="calculateTotal()"
                                   placeholder="0.00"
                                   class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                        </div>
                    </div>

                    <!-- Desconto -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Desconto</h3>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="discount_type" class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Tipo de Desconto</label>
                                    <select id="discount_type" onchange="updateDiscountType()"
                                            class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                        <option value="none">Sem Desconto</option>
                                        <option value="percentage">Porcentagem (%)</option>
                                        <option value="fixed">Valor Fixo (R$)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="discount_value" class="block text-xs text-gray-600 dark:text-slate-400 mb-2">
                                        <span id="discount-label">Valor</span>
                                    </label>
                                    <input type="number" id="discount_value" step="0.01" min="0" value="0" disabled
                                           onchange="calculateTotal()"
                                           class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm disabled:bg-gray-100 dark:disabled:bg-slate-700 disabled:cursor-not-allowed">
                                </div>
                            </div>
                            <div id="discount-preview" class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md hidden">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs text-green-800 dark:text-green-300">
                                        Desconto de <strong id="discount-preview-text">R$ 0,00</strong> aplicado
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formas de Pagamento -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Adicionar Pagamento</h3>
                        </div>

                        <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                            <!-- Formulário de Adicionar Pagamento -->
                            <div class="bg-white dark:bg-slate-800 rounded-md p-4 border border-gray-200 dark:border-slate-700 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Forma de Pagamento</label>
                                        <select id="new-payment-method" 
                                                class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                            <option value="pix">PIX</option>
                                            <option value="dinheiro">Dinheiro</option>
                                            <option value="cartao">Cartão</option>
                                            <option value="boleto">Boleto</option>
                                            <option value="transferencia">Transferência</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Valor (R$)</label>
                                        <input type="number" id="new-payment-amount" step="0.01" min="0" value="0"
                                               placeholder="0.00"
                                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all text-sm">
                                    </div>
                                </div>
                                
                                <!-- Valor Sugerido -->
                                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-4 mb-4 border border-indigo-200 dark:border-indigo-800">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Valor Sugerido (50%)</span>
                                        </div>
                                        <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400" id="suggested-amount">R$ 0,00</span>
                                    </div>
                                    <button type="button" onclick="useSuggestedAmount()" 
                                            class="w-full mt-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Aplicar Valor Sugerido
                                    </button>
                                </div>

                                <button type="button" onclick="addPaymentMethod()" 
                                        class="w-full px-4 py-2.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors text-sm font-medium shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                                    Adicionar Pagamento
                                </button>
                            </div>

                            <!-- Lista de Pagamentos Adicionados -->
                            <div id="payment-methods-list" class="space-y-2 mb-4">
                            <!-- Será preenchido via JavaScript -->
                        </div>

                            <!-- Resumo de Pagamentos -->
                            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-md border border-indigo-200 dark:border-indigo-800">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-slate-300">Total Pago:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" id="total-paid">R$ 0,00</span>
                                </div>
                            <div class="flex justify-between text-sm">
                                    <span class="text-gray-700 dark:text-slate-300">Restante:</span>
                                    <span class="font-semibold text-indigo-600 dark:text-indigo-400" id="remaining">R$ 0,00</span>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Navegação -->
                    <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-slate-800">
                        <a href="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.customization') : ($backRoute ?? route('orders.wizard.customization')) }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Voltar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white text-sm font-medium rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 flex items-center shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20 transition-all">
                            Continuar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        let paymentMethods = [];
        let subtotal = {{ $order->subtotal }};
        let deliveryFee = {{ $sessionPaymentData['delivery_fee'] ?? $order->delivery_fee ?? 0 }};
        let sizeSurcharges = {};
        let orderItems = @json($order->items);
        let discountType = 'none';
        let discountValue = 0;

        document.addEventListener('DOMContentLoaded', function() {
            // Priorizar dados da sessão (se o usuário voltou de uma etapa posterior)
            @if(isset($sessionPaymentData) && !empty($sessionPaymentData['payment_methods']))
                @php
                    $sessionMethods = json_decode($sessionPaymentData['payment_methods'], true);
                @endphp
                @if($sessionMethods && is_array($sessionMethods))
                    @foreach($sessionMethods as $pm)
                        paymentMethods.push({
                            id: {{ $pm['id'] ?? 'Date.now() + Math.random()' }},
                            method: '{{ $pm["method"] ?? "pix" }}',
                            amount: {{ $pm["amount"] ?? 0 }}
                        });
                    @endforeach
                    renderPaymentMethods();
                @endif
            @elseif($order->payments && $order->payments->count() > 0)
                @php
                    $existingPayment = $order->payments->first();
                    $existingMethods = is_array($existingPayment->payment_methods) 
                        ? $existingPayment->payment_methods 
                        : json_decode($existingPayment->payment_methods, true);
                @endphp
                @if($existingMethods && is_array($existingMethods))
                    @foreach($existingMethods as $pm)
                        paymentMethods.push({
                            id: Date.now() + Math.random(),
                            method: '{{ $pm["method"] ?? "pix" }}',
                            amount: {{ $pm["amount"] ?? 0 }}
                        });
                    @endforeach
                    renderPaymentMethods();
                @endif
            @endif

            calculateSizeSurcharges();
            calculateTotal(); // Atualizar totais com taxa de entrega
            calculatePayments(); // Calcular restante inicial
            updateSuggestedAmount(); // Atualizar valor sugerido
        });

        function calculateSizeSurcharges() {
            // Calcular acréscimos por tamanho (GG, EXG, G1, G2, G3)
            const largeSizes = ['GG', 'EXG', 'G1', 'G2', 'G3'];
            let totalSurcharge = 0;
            let surchargesHtml = '';
            sizeSurcharges = {}; // Reset global object

            // Helper to check if item is restricted (Infantil/Baby look)
            const isRestricted = (item) => {
                const model = (item.model || '').toUpperCase();
                const detail = (item.detail || '').toUpperCase();
                return model.includes('INFANTIL') || model.includes('BABY LOOK') || 
                       detail.includes('INFANTIL') || detail.includes('BABY LOOK');
            };

            // Helper to check if surcharge is forced via checkbox
            const shouldApplySurcharge = (item) => {
                if (!item.print_desc) return false;
                try {
                    const desc = typeof item.print_desc === 'string' ? JSON.parse(item.print_desc) : item.print_desc;
                    return !!desc.apply_surcharge;
                } catch (e) {
                    return false;
                }
            };

            // Iterate all items and their sizes
            let sizeQuantities = {}; // { 'GG': 5, 'EXG': 2 } - only for applicable items

            orderItems.forEach(item => {
                const restricted = isRestricted(item);
                const forced = shouldApplySurcharge(item);
                
                // If restricted AND not forced, skip this item's sizes for surcharge calculation
                if (restricted && !forced) {
                    return;
                }

                const sizes = typeof item.sizes === 'string' ? JSON.parse(item.sizes) : item.sizes;
                if (sizes) {
                    Object.entries(sizes).forEach(([size, qty]) => {
                        if (largeSizes.includes(size)) {
                            sizeQuantities[size] = (sizeQuantities[size] || 0) + parseInt(qty);
                        }
                    });
                }
            });

            // Now fetch surcharges for the aggregated quantities
            const promises = Object.entries(sizeQuantities).map(([size, quantity]) => {
                if (quantity > 0) {
                    return fetch(`/api/size-surcharge/${size}?price=${subtotal}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.surcharge) {
                                const surcharge = parseFloat(data.surcharge) * quantity;
                                sizeSurcharges[size] = surcharge;
                                totalSurcharge += surcharge;
                                
                                surchargesHtml += `
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-700 dark:text-slate-300">Acréscimo ${size} (${quantity}x):</span>
                                        <span class="font-medium text-orange-600 dark:text-orange-400">+R$ ${surcharge.toFixed(2).replace('.', ',')}</span>
                                    </div>
                                `;
                            }
                        });
                }
                return Promise.resolve();
            });

            Promise.all(promises).then(() => {
                document.getElementById('surcharges-breakdown').innerHTML = surchargesHtml;
                document.getElementById('size-surcharges-data').value = JSON.stringify(sizeSurcharges);
                calculateTotal();
            });
        }

        function addPaymentMethod() {
            const method = document.getElementById('new-payment-method').value;
            const amountInput = document.getElementById('new-payment-amount').value;
            const amount = parseFloat(amountInput) || 0;
            
            if (amount <= 0) {
                showToast('Por favor, informe um valor maior que zero.', 'error');
                return;
            }
            
            const id = Date.now();
            paymentMethods.push({
                id: id,
                method: method,
                amount: amount
            });
            
            // Limpar campos
            document.getElementById('new-payment-amount').value = '0';
            
            renderPaymentMethods();
            calculatePayments();
            updateSuggestedAmount();
            showToast('Pagamento adicionado com sucesso!', 'success');
        }
        
        // Função para exibir notificação visual (toast)
        function showToast(message, type = 'info') {
            // Remover toast existente
            const existingToast = document.getElementById('toast-notification');
            if (existingToast) existingToast.remove();
            
            // Definir cores por tipo
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-indigo-500'
            };
            const icons = {
                success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
                error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
                warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
            };
            
            // Criar elemento toast
            const toast = document.createElement('div');
            toast.id = 'toast-notification';
            toast.className = `fixed top-4 right-4 z-50 flex items-center p-4 rounded-lg shadow-lg ${colors[type]} text-white transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type]}</svg>
                <span class="text-sm font-medium">${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            document.body.appendChild(toast);
            
            // Animar entrada
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 10);
            
            // Remover após 4 segundos
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
        
        function useSuggestedAmount() {
            const totalFinal = getTotalFinal();
            const suggestedAmount = totalFinal * 0.5;
            document.getElementById('new-payment-amount').value = suggestedAmount.toFixed(2);
        }
        
        function updateSuggestedAmount() {
            const totalFinal = getTotalFinal();
            const totalPaid = paymentMethods.reduce((sum, pm) => sum + (parseFloat(pm.amount) || 0), 0);
            const remaining = totalFinal - totalPaid;
            const suggestedAmount = remaining > 0 ? (remaining >= totalFinal * 0.5 ? totalFinal * 0.5 : remaining) : 0;
            
            document.getElementById('suggested-amount').textContent = `R$ ${suggestedAmount.toFixed(2).replace('.', ',')}`;
        }

        function removePaymentMethod(id) {
            paymentMethods = paymentMethods.filter(pm => pm.id !== id);
            renderPaymentMethods();
            calculatePayments();
            updateSuggestedAmount();
        }

        function renderPaymentMethods() {
            const container = document.getElementById('payment-methods-list');
            
            if (paymentMethods.length === 0) {
                container.innerHTML = '<p class="text-gray-500 dark:text-slate-400 text-xs">Nenhum pagamento adicionado ainda.</p>';
                return;
            }

            container.innerHTML = paymentMethods.map((pm, index) => `
                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-md border border-gray-200 dark:border-slate-700 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">${pm.method}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400">Pagamento ${index + 1}</p>
                    </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-base font-bold text-indigo-600 dark:text-indigo-400">R$ ${pm.amount.toFixed(2).replace('.', ',')}</span>
                        <button type="button" onclick="removePaymentMethod(${pm.id})" 
                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                                title="Remover">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
        }


        function calculatePayments() {
            const totalPaid = paymentMethods.reduce((sum, pm) => sum + (parseFloat(pm.amount) || 0), 0);
            const totalFinal = getTotalFinal();
            const remaining = totalFinal - totalPaid;

            document.getElementById('total-paid').textContent = `R$ ${totalPaid.toFixed(2).replace('.', ',')}`;
            
            // Atualizar label e valor do restante/crédito
            const remainingElement = document.getElementById('remaining');
            const remainingLabel = remainingElement.closest('.flex').querySelector('span:first-child');
            if (remaining < 0) {
                remainingLabel.textContent = 'Crédito do Cliente:';
                remainingElement.textContent = `R$ ${Math.abs(remaining).toFixed(2).replace('.', ',')}`;
                remainingElement.className = 'font-semibold text-green-600 dark:text-green-400';
            } else {
                remainingLabel.textContent = 'Restante:';
                remainingElement.textContent = `R$ ${remaining.toFixed(2).replace('.', ',')}`;
                remainingElement.className = 'font-semibold text-indigo-600 dark:text-indigo-400';
            }

            // Atualizar campo hidden
            document.getElementById('payment-methods-data').value = JSON.stringify(paymentMethods);
        }

        function updateDiscountType() {
            discountType = document.getElementById('discount_type').value;
            const discountInput = document.getElementById('discount_value');
            const discountLabel = document.getElementById('discount-label');
            const discountPreview = document.getElementById('discount-preview');
            
            if (discountType === 'none') {
                discountInput.disabled = true;
                discountInput.value = 0;
                discountValue = 0;
                discountPreview.classList.add('hidden');
            } else {
                discountInput.disabled = false;
                if (discountType === 'percentage') {
                    discountLabel.textContent = 'Porcentagem (%)';
                    discountInput.max = 100;
                    discountInput.placeholder = 'Ex: 10';
                } else {
                    discountLabel.textContent = 'Valor (R$)';
                    discountInput.removeAttribute('max');
                    discountInput.placeholder = 'Ex: 10.00';
                }
            }
            
            calculateTotal();
        }

        function calculateDiscount() {
            if (discountType === 'none') {
                return 0;
            }
            
            discountValue = parseFloat(document.getElementById('discount_value').value) || 0;
            const totalSurcharges = Object.values(sizeSurcharges).reduce((sum, val) => sum + val, 0);
            const subtotalWithFees = subtotal + totalSurcharges + deliveryFee;
            
            let discount = 0;
            
            if (discountType === 'percentage') {
                // Limitar porcentagem entre 0 e 100
                discountValue = Math.min(Math.max(discountValue, 0), 100);
                discount = (subtotalWithFees * discountValue) / 100;
            } else if (discountType === 'fixed') {
                discount = discountValue;
                // Não permitir desconto maior que o total
                discount = Math.min(discount, subtotalWithFees);
            }
            
            return discount;
        }

        function calculateTotal() {
            deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
            const customSurcharge = parseFloat(document.getElementById('custom_surcharge').value) || 0;
            
            const totalSurcharges = Object.values(sizeSurcharges).reduce((sum, val) => sum + val, 0);
            const discount = calculateDiscount();
            const totalFinal = subtotal + totalSurcharges + customSurcharge + deliveryFee - discount;

            // Atualizar displays
            document.getElementById('delivery-fee-display').textContent = `R$ ${deliveryFee.toFixed(2).replace('.', ',')}`;
            document.getElementById('total-final').textContent = `R$ ${totalFinal.toFixed(2).replace('.', ',')}`;
            
            // Mostrar desconto no resumo
            if (discount > 0) {
                document.getElementById('discount-summary').classList.remove('hidden');
                document.getElementById('discount-display').textContent = `-R$ ${discount.toFixed(2).replace('.', ',')}`;
                
                // Mostrar preview do desconto
                document.getElementById('discount-preview').classList.remove('hidden');
                let discountText = '';
                if (discountType === 'percentage') {
                    discountText = `${discountValue}% (R$ ${discount.toFixed(2).replace('.', ',')})`;
                } else {
                    discountText = `R$ ${discount.toFixed(2).replace('.', ',')}`;
                }
                document.getElementById('discount-preview-text').textContent = discountText;
            } else {
                document.getElementById('discount-summary').classList.add('hidden');
                document.getElementById('discount-preview').classList.add('hidden');
            }
            
            // Atualizar campos hidden
            document.getElementById('discount-type-data').value = discountType;
            document.getElementById('discount-value-data').value = discountValue;
            
            calculatePayments();
            updateSuggestedAmount();
        }

        function getTotalFinal() {
            const totalSurcharges = Object.values(sizeSurcharges).reduce((sum, val) => sum + val, 0);
            const customSurcharge = parseFloat(document.getElementById('custom_surcharge').value) || 0;
            const discount = calculateDiscount();
            return subtotal + totalSurcharges + customSurcharge + deliveryFee - discount;
        }

</script>
@endpush
@endsection