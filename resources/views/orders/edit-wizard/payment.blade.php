@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">4</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Pagamento e Valores</span>
                        <p class="text-xs text-gray-500">Etapa 4 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600">80%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 80%"></div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Editar Pagamento e Valores</h1>
                        <p class="text-sm text-gray-600">Configure os valores e forma de pagamento</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('orders.edit-wizard.payment') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Data de Entrega -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Data de Entrega *</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <input type="date" name="delivery_date" 
                                   value="{{ !empty($editData['payment']['delivery_date']) ? \Carbon\Carbon::parse($editData['payment']['delivery_date'])->format('Y-m-d') : ($order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">
                                @if($order->delivery_date)
                                    Data atual: <strong>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</strong>. Deixe em branco para manter esta data ou altere para uma nova data.
                                @else
                                    Data não definida. Preencha com a data de entrega ou deixe em branco para calcular 15 dias úteis a partir de hoje.
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Valores -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Valores do Pedido</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal (R$) *</label>
                                    <input type="number" name="subtotal" 
                                           value="{{ $editData['payment']['subtotal'] ?? $order->subtotal ?? 0 }}" 
                                           step="0.01" min="0" required
                                           onchange="calculateTotal()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Desconto (R$)</label>
                                    <input type="number" name="discount" 
                                           value="{{ $editData['payment']['discount'] ?? $order->discount ?? 0 }}" 
                                           step="0.01" min="0"
                                           onchange="calculateTotal()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Taxa de Entrega (R$)</label>
                                    <input type="number" name="delivery_fee" 
                                           value="{{ $editData['payment']['delivery_fee'] ?? $order->delivery_fee ?? 0 }}" 
                                           step="0.01" min="0"
                                           onchange="calculateTotal()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Total (R$) *</label>
                                    <input type="number" name="total" 
                                           value="{{ $editData['payment']['total'] ?? $order->total ?? 0 }}" 
                                           step="0.01" min="0" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-gray-100"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Forma de Pagamento -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Forma de Pagamento *</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Método de Pagamento</label>
                                    <select name="payment_method" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        <option value="">Selecione...</option>
                                        <option value="pix" {{ ($editData['payment']['payment_method'] ?? '') == 'pix' ? 'selected' : '' }}>PIX</option>
                                        <option value="dinheiro" {{ ($editData['payment']['payment_method'] ?? '') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                        <option value="cartao" {{ ($editData['payment']['payment_method'] ?? '') == 'cartao' ? 'selected' : '' }}>Cartão</option>
                                        <option value="multiplo" {{ ($editData['payment']['payment_method'] ?? '') == 'multiplo' ? 'selected' : '' }}>Múltiplas Formas</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Data de Entrada *</label>
                                    <input type="date" name="entry_date" 
                                           value="{{ $editData['payment']['entry_date'] ?? now()->format('Y-m-d') }}" 
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Observações</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <textarea name="notes" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                      placeholder="Observações sobre o pedido...">{{ $editData['payment']['notes'] ?? $order->notes ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- Botões de Navegação -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <a href="{{ route('orders.edit-wizard.customization') }}" 
                           class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Voltar
                        </a>
                        <button type="submit" 
                                class="flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                            Continuar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            const subtotal = parseFloat(document.querySelector('input[name="subtotal"]').value) || 0;
            const discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
            const deliveryFee = parseFloat(document.querySelector('input[name="delivery_fee"]').value) || 0;
            
            const total = subtotal - discount + deliveryFee;
            document.querySelector('input[name="total"]').value = total.toFixed(2);
        }

        // Calcular total inicial
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
@endsection