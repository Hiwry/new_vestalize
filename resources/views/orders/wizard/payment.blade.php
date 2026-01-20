@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <style>
        .payment-input {
            height: 46px;
            border-radius: 12px;
            padding: 0.65rem 0.9rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 8px 24px -16px rgba(124, 58, 237, 0.35);
            transition: all 0.18s ease;
        }
        .payment-input:focus {
            border-color: #7c3aed;
            outline: none;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.18);
        }
        .payment-input::placeholder {
            color: #94a3b8;
        }
        .payment-input::-webkit-outer-spin-button,
        .payment-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .payment-input {
            -moz-appearance: textfield;
        }
        .dark .payment-input {
            background: #0f172a;
            border-color: #24334d;
            color: #e2e8f0;
            box-shadow: 0 12px 30px -18px rgba(0, 0, 0, 0.7);
        }
        .dark .payment-input:focus {
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.25);
        }
        .dark .payment-input::placeholder {
            color: #64748b;
        }
    </style>
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#7c3aed] text-white stay-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]">4</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Pagamento</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 4 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">80%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-2.5 shadow-inner">
                <div class="bg-[#7c3aed] h-2.5 rounded-full transition-all duration-500 ease-out shadow-lg shadow-purple-500/30 dark:shadow-purple-600/30" style="width: 80%"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-900/50">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                             <div class="w-8 h-8 bg-[#7c3aed] rounded-lg flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]">
                                <svg class="w-5 h-5 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            Configuração de Pagamento
                        </h1>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 pl-10">Defina valores, formas de pagamento e datas</p>
            </div>

            <!-- Content -->
            <div class="p-6">
            <!-- Resumo do Pedido -->
                <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-5 mb-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                            <svg class="w-3 h-3 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        
                        <div class="border-t border-purple-200 dark:border-purple-800 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-gray-700 dark:text-slate-300">Subtotal (Etapa 2 + 3):</span>
                                <span class="font-medium text-gray-900 dark:text-white" id="subtotal">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div id="surcharges-breakdown" class="space-y-1">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                        
                        <!-- Acréscimo Especial (personalizável) -->
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-purple-100 dark:border-purple-800/50">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-700 dark:text-slate-300 text-sm">Acréscimo Especial:</span>
                                <span class="text-xs text-gray-500 dark:text-slate-400">(digitar valor)</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-orange-600 dark:text-orange-400">+R$</span>
                                <input type="number" id="custom_surcharge" step="0.01" min="0" value="0"
                                       onchange="calculateTotal()" oninput="calculateTotal()"
                                       class="w-20 px-2 py-1 text-right text-sm rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-[#7c3aed] dark:focus:border-[#7c3aed] focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed]">
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
                        
                        <div class="border-t border-purple-200 dark:border-purple-800 pt-2 mt-2">
                            <div class="flex justify-between text-base font-bold">
                                <span class="text-gray-900 dark:text-white">Total Final:</span>
                                <span id="total-final" class="text-[#7c3aed] dark:text-purple-400">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
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
                            <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Data de Entrada *</h3>
                        </div>
                        <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm">
                            <input type="date" id="entry_date" name="entry_date" required
                                   value="{{ $sessionPaymentData['entry_date'] ?? $order->payments->first()->entry_date ?? $order->entry_date ?? date('Y-m-d') }}"
                                   class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-[#7c3aed] dark:focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all text-sm">
                        </div>
                    </div>

                    <!-- Taxa de Entrega -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-2a9 9 0 11-18 0 9 9 0 0118 0z M9 16h6"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Taxa de Entrega</h3>
                        </div>
                        <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm">
                            <label for="delivery_fee" class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Valor da Taxa (R$)</label>
                            <input type="number" id="delivery_fee" name="delivery_fee" step="0.01" min="0" value="{{ $sessionPaymentData['delivery_fee'] ?? $order->delivery_fee ?? 0 }}"
                                   onchange="calculateTotal()"
                                   placeholder="0.00"
                                   class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-[#7c3aed] dark:focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all text-sm">
                        </div>
                    </div>

                    <!-- Desconto -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Desconto</h3>
                        </div>
                        <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="discount_type" class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Tipo de Desconto</label>
                                    <select id="discount_type" onchange="updateDiscountType()"
                                            class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-[#7c3aed] dark:focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all text-sm">
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
                                           class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-[#7c3aed] dark:focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all text-sm disabled:bg-gray-100 dark:disabled:bg-slate-700 disabled:cursor-not-allowed">
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
                            <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Adicionar Pagamento</h3>
                        </div>

                        <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-4 shadow-sm">
                            <!-- Formulário de Adicionar Pagamento -->
                            <div class="bg-white dark:bg-slate-800 rounded-md p-4 border border-gray-200 dark:border-slate-700 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Forma de Pagamento</label>
                                        <select id="new-payment-method" 
                                                class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-[#7c3aed] dark:focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all text-sm">
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
                                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-[#7c3aed] dark:focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] dark:focus:ring-[#7c3aed] transition-all text-sm">
                                    </div>
                                </div>
                                
                                <!-- Valor Sugerido -->
                                <div class="bg-gradient-to-r from-purple-50 to-purple-50 dark:from-purple-900/20 dark:to-purple-900/20 rounded-lg p-4 mb-4 border border-purple-200 dark:border-purple-800">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Valor Sugerido (50%)</span>
                                        </div>
                                        <span class="text-xl font-bold text-[#7c3aed] dark:text-purple-400" id="suggested-amount">R$ 0,00</span>
                                    </div>
                                    <button type="button" onclick="useSuggestedAmount()" style="color: white !important;" 
                                            class="w-full mt-2 px-4 py-2 bg-[#7c3aed] text-white stay-white rounded-md text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Aplicar Valor Sugerido
                                    </button>
                                </div>

                                <button type="button" onclick="addPaymentMethod()" style="color: white !important;" 
                                        class="w-full px-4 py-2.5 bg-[#7c3aed] text-white stay-white rounded-md transition-colors text-sm font-medium shadow-lg shadow-purple-500/20 dark:shadow-purple-600/20">
                                    Adicionar Pagamento
                                </button>
                            </div>

                            <!-- Lista de Pagamentos Adicionados -->
                            <div id="payment-methods-list" class="space-y-2 mb-4">
                            <!-- Será preenchido via JavaScript -->
                        </div>

                            <!-- Resumo de Pagamentos -->
                            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-md border border-purple-200 dark:border-purple-800">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-slate-300">Total Pago:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" id="total-paid">R$ 0,00</span>
                                </div>
                            <div class="flex justify-between text-sm">
                                    <span class="text-gray-700 dark:text-slate-300">Restante:</span>
                                    <span class="font-semibold text-[#7c3aed] dark:text-purple-400" id="remaining">R$ 0,00</span>
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
                                style="color: white !important;"
                                class="px-6 py-2 bg-[#7c3aed] text-white stay-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:ring-offset-2 flex items-center shadow-lg shadow-purple-500/20 dark:shadow-purple-600/20 transition-all">
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
</div>
</div>

<script>
(function() {
    // State
    window.paymentMethods = [];
    window.subtotal = {{ $order->subtotal }};
    window.deliveryFee = {{ $sessionPaymentData['delivery_fee'] ?? $order->delivery_fee ?? 0 }};
    window.sizeSurcharges = {};
    window.orderItems = @json($order->items);
    window.discountType = 'none';
    window.discountValue = 0;

    function initPaymentPage() {
        console.log('Initializing Payment Page...');
        window.paymentMethods = [];
        
        @if(isset($sessionPaymentData) && !empty($sessionPaymentData['payment_methods']))
            @php
                $sessionMethods = json_decode($sessionPaymentData['payment_methods'], true);
            @endphp
            @if($sessionMethods && is_array($sessionMethods))
                @foreach($sessionMethods as $pm)
                    window.paymentMethods.push({
                        id: {{ $pm['id'] ?? 'Date.now() + Math.random()' }},
                        method: '{{ $pm["method"] ?? "pix" }}',
                        amount: {{ $pm["amount"] ?? 0 }}
                    });
                @endforeach
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
                    window.paymentMethods.push({
                        id: Date.now() + Math.random(),
                        method: '{{ $pm["method"] ?? "pix" }}',
                        amount: {{ $pm["amount"] ?? 0 }}
                    });
                @endforeach
            @endif
        @endif

        // Listeners for elements that might be re-loaded
        const form = document.getElementById('payment-form');
        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', function(e) {
                // Prepare hidden fields before submit
                const methodsInput = document.getElementById('payment-methods-data');
                const surchargesInput = document.getElementById('size-surcharges-data');
                const discTypeInput = document.getElementById('discount-type-data');
                const discValInput = document.getElementById('discount-value-data');
                
                if (methodsInput) methodsInput.value = JSON.stringify(window.paymentMethods);
                if (surchargesInput) surchargesInput.value = JSON.stringify(window.sizeSurcharges);
                if (discTypeInput) discTypeInput.value = window.discountType;
                if (discValInput) discValInput.value = window.discountValue;
            });
            form.dataset.listenerAttached = 'true';
        }

        window.renderPaymentMethods();
        window.calculateSizeSurcharges();
        window.calculateTotal();
        window.calculatePayments();
        window.updateSuggestedAmount();
    }
    window.initPaymentPage = initPaymentPage;

    window.calculateSizeSurcharges = function() {
        const largeSizes = ['GG', 'EXG', 'G1', 'G2', 'G3', 'Especial', 'ESPECIAL'];
        let totalSurcharge = 0;
        let surchargesHtml = '';
        window.sizeSurcharges = {};

        const isRestricted = (item) => {
            const model = (item.model || '').toUpperCase();
            const detail = (item.detail || '').toUpperCase();
            return model.includes('INFANTIL') || model.includes('BABY LOOK') || 
                   detail.includes('INFANTIL') || detail.includes('BABY LOOK');
        };

        const shouldApplySurcharge = (item) => {
            if (!item.print_desc) return false;
            try {
                const desc = typeof item.print_desc === 'string' ? JSON.parse(item.print_desc) : item.print_desc;
                return !!desc.apply_surcharge;
            } catch (e) { return false; }
        };

        let sizeQuantities = {};
        window.orderItems.forEach(item => {
            if (isRestricted(item) && !shouldApplySurcharge(item)) return;
            const sizes = typeof item.sizes === 'string' ? JSON.parse(item.sizes) : item.sizes;
            if (sizes) {
                Object.entries(sizes).forEach(([size, qty]) => {
                    if (largeSizes.includes(size)) {
                        sizeQuantities[size] = (sizeQuantities[size] || 0) + parseInt(qty);
                    }
                });
            }
        });

        const promises = Object.entries(sizeQuantities).map(([size, quantity]) => {
            if (quantity > 0) {
                return fetch(`/api/size-surcharge/${size}?price=${window.subtotal}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.surcharge) {
                            const surcharge = parseFloat(data.surcharge) * quantity;
                            window.sizeSurcharges[size] = surcharge;
                            totalSurcharge += surcharge;
                            surchargesHtml += `
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-700 dark:text-slate-300">Acréscimo ${size} (${quantity}x):</span>
                                    <span class="font-medium text-orange-600 dark:text-orange-400">+R$ ${surcharge.toFixed(2).replace('.', ',')}</span>
                                </div>`;
                        }
                    });
            }
            return Promise.resolve();
        });

        Promise.all(promises).then(() => {
            let hasEspecial = false;
            window.orderItems.forEach(item => {
                const sizes = typeof item.sizes === 'string' ? JSON.parse(item.sizes) : item.sizes;
                if (sizes && Object.keys(sizes).some(s => s.toUpperCase() === 'ESPECIAL' && sizes[s] > 0)) hasEspecial = true;
            });
            if (hasEspecial) {
                totalSurcharge += 35.00;
                surchargesHtml += `
                    <div class="flex justify-between text-xs font-bold mt-1 pt-1 border-t border-gray-100 dark:border-slate-700">
                        <span class="text-gray-700 dark:text-slate-200">Taxa Especial (Setup):</span>
                        <span class="text-orange-600 dark:text-orange-400">R$ 35,00</span>
                    </div>`;
            }
            const breakdown = document.getElementById('surcharges-breakdown');
            if (breakdown) breakdown.innerHTML = surchargesHtml;
            window.calculateTotal();
        });
    };

    window.addPaymentMethod = function() {
        const method = document.getElementById('new-payment-method').value;
        const amount = parseFloat(document.getElementById('new-payment-amount').value) || 0;
        if (amount <= 0) {
            window.showToast('Por favor, informe um valor maior que zero.', 'error');
            return;
        }
        window.paymentMethods.push({ id: Date.now(), method: method, amount: amount });
        document.getElementById('new-payment-amount').value = '0';
        window.renderPaymentMethods();
        window.calculatePayments();
        window.updateSuggestedAmount();
        window.showToast('Pagamento adicionado com sucesso!', 'success');
    };

    window.removePaymentMethod = function(id) {
        window.paymentMethods = window.paymentMethods.filter(pm => pm.id !== id);
        window.renderPaymentMethods();
        window.calculatePayments();
        window.updateSuggestedAmount();
    };

    window.renderPaymentMethods = function() {
        const container = document.getElementById('payment-methods-list');
        if (!container) return;
        if (window.paymentMethods.length === 0) {
            container.innerHTML = '<p class="text-gray-500 dark:text-slate-400 text-xs">Nenhum pagamento adicionado ainda.</p>';
            return;
        }
        container.innerHTML = window.paymentMethods.map((pm, index) => `
            <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-md border border-gray-200 dark:border-slate-700 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">${pm.method}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Pagamento ${index + 1}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-base font-bold text-[#7c3aed] dark:text-purple-400">R$ ${pm.amount.toFixed(2).replace('.', ',')}</span>
                    <button type="button" onclick="window.removePaymentMethod(${pm.id})" class="text-red-600 hover:text-red-800 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
            </div>`).join('');
    };

    window.calculatePayments = function() {
        const totalPaid = window.paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
        const totalFinal = window.getTotalFinal();
        const remaining = totalFinal - totalPaid;
        const paidEl = document.getElementById('total-paid');
        const remEl = document.getElementById('remaining');
        if (paidEl) paidEl.textContent = `R$ ${totalPaid.toFixed(2).replace('.', ',')}`;
        if (remEl) {
            const label = remEl.closest('.flex').querySelector('span:first-child');
            if (remaining < 0) {
                if (label) label.textContent = 'Crédito do Cliente:';
                remEl.textContent = `R$ ${Math.abs(remaining).toFixed(2).replace('.', ',')}`;
                remEl.className = 'font-semibold text-green-600 dark:text-green-400';
            } else {
                if (label) label.textContent = 'Restante:';
                remEl.textContent = `R$ ${remaining.toFixed(2).replace('.', ',')}`;
                remEl.className = 'font-semibold text-[#7c3aed] dark:text-purple-400';
            }
        }
    };

    window.updateDiscountType = function() {
        window.discountType = document.getElementById('discount_type').value;
        const input = document.getElementById('discount_value');
        const label = document.getElementById('discount-label');
        if (!input || !label) return;
        if (window.discountType === 'none') {
            input.disabled = true;
            input.value = 0;
            window.discountValue = 0;
            document.getElementById('discount-preview')?.classList.add('hidden');
        } else {
            input.disabled = false;
            label.textContent = window.discountType === 'percentage' ? 'Porcentagem (%)' : 'Valor (R$)';
        }
        window.calculateTotal();
    };

    window.calculateDiscount = function() {
        if (window.discountType === 'none') return 0;
        window.discountValue = parseFloat(document.getElementById('discount_value')?.value) || 0;
        const totalSurcharges = Object.values(window.sizeSurcharges).reduce((sum, val) => sum + val, 0);
        const subtotalWithFees = window.subtotal + totalSurcharges + window.deliveryFee;
        if (window.discountType === 'percentage') {
            window.discountValue = Math.min(Math.max(window.discountValue, 0), 100);
            return (subtotalWithFees * window.discountValue) / 100;
        }
        return Math.min(window.discountValue, subtotalWithFees);
    };

    window.getTotalFinal = function() {
        const totalSurcharges = Object.values(window.sizeSurcharges).reduce((sum, val) => sum + val, 0);
        const customSurcharge = parseFloat(document.getElementById('custom_surcharge')?.value) || 0;
        return window.subtotal + totalSurcharges + customSurcharge + window.deliveryFee - window.calculateDiscount();
    };

    window.calculateTotal = function() {
        window.deliveryFee = parseFloat(document.getElementById('delivery_fee')?.value) || 0;
        const discount = window.calculateDiscount();
        const totalFinal = window.getTotalFinal();
        const feeDisp = document.getElementById('delivery-fee-display');
        const totalDisp = document.getElementById('total-final');
        if (feeDisp) feeDisp.textContent = `R$ ${window.deliveryFee.toFixed(2).replace('.', ',')}`;
        if (totalDisp) totalDisp.textContent = `R$ ${totalFinal.toFixed(2).replace('.', ',')}`;
        const summary = document.getElementById('discount-summary');
        const disp = document.getElementById('discount-display');
        if (summary && disp) {
            if (discount > 0) {
                summary.classList.remove('hidden');
                disp.textContent = `-R$ ${discount.toFixed(2).replace('.', ',')}`;
            } else summary.classList.add('hidden');
        }
        window.calculatePayments();
        window.updateSuggestedAmount();
    };

    window.updateSuggestedAmount = function() {
        const totalFinal = window.getTotalFinal();
        const paid = window.paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
        const rem = totalFinal - paid;
        const suggested = rem > 0 ? (rem >= totalFinal * 0.5 ? totalFinal * 0.5 : rem) : 0;
        const el = document.getElementById('suggested-amount');
        if (el) el.textContent = `R$ ${suggested.toFixed(2).replace('.', ',')}`;
    };

    window.useSuggestedAmount = function() {
        const totalFinal = window.getTotalFinal();
        const paid = window.paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
        const rem = totalFinal - paid;
        const suggested = rem > 0 ? (rem >= totalFinal * 0.5 ? totalFinal * 0.5 : rem) : 0;
        const input = document.getElementById('new-payment-amount');
        if (input) input.value = suggested.toFixed(2);
    };

    window.showToast = function(message, type = 'info') {
        const existing = document.getElementById('toast-notification');
        if (existing) existing.remove();
        const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-indigo-500' };
        const toast = document.createElement('div');
        toast.id = 'toast-notification';
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${colors[type] || colors.info} text-white transform transition-all duration-300 translate-x-full`;
        toast.innerHTML = `<span class="text-sm font-medium">${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-x-full'), 10);
        setTimeout(() => { toast.classList.add('translate-x-full'); setTimeout(() => toast.remove(), 300); }, 3000);
    };

    // Initialization
    window._paymentInitSetup = function() {
        document.removeEventListener('ajax-content-loaded', initPaymentPage);
        document.addEventListener('ajax-content-loaded', initPaymentPage);
    };
    window._paymentInitSetup();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPaymentPage);
    } else {
        initPaymentPage();
    }
})();
</script>
@endsection
