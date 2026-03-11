@extends('layouts.admin')

@section('content')
<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="ow-shell">
    <style>
    .ow-shell {
        --sh-surface-from: #f3f4f8;
        --sh-surface-to: #eceff4;
        --sh-surface-border: #d8dce6;
        --sh-text-primary: #0f172a;
        --sh-text-secondary: #64748b;
        --sh-card-bg: #ffffff;
        --sh-card-border: #dde2ea;
        --sh-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --sh-accent: #7c3aed;
        --sh-accent-strong: #6d28d9;
        
        background: linear-gradient(180deg, var(--sh-surface-from) 0%, var(--sh-surface-to) 100%);
        border: 1px solid var(--sh-surface-border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        color: var(--sh-text-primary);
    }

    .dark .ow-shell {
        --sh-surface-from: #0d1830;
        --sh-surface-to: #0b1322;
        --sh-surface-border: rgba(148, 163, 184, 0.16);
        --sh-text-primary: #e5edf8;
        --sh-text-secondary: #91a4c0;
        --sh-card-bg: #10203a;
        --sh-card-border: rgba(148, 163, 184, 0.12);
        --sh-card-shadow: none;
        --sh-input-bg: #162847;

        background: linear-gradient(180deg, var(--sh-surface-from) 0%, var(--sh-surface-to) 100%) !important;
        box-shadow: none !important;
        border-color: var(--sh-surface-border) !important;
    }


    .dark.avento-theme .ow-card, .dark.avento-theme .ow-progress, .dark.avento-theme .ow-field-panel {
        background-color: var(--sh-card-bg) !important;
        box-shadow: none !important;
    }

    .dark.avento-theme .ow-shell input:not([type="color"]),
    .dark.avento-theme .ow-shell select,
    .dark.avento-theme .ow-shell textarea,
    .dark.avento-theme .ow-btn-ghost,
    .dark.avento-theme .ow-search-toggle,
    .dark.avento-theme .ow-search-panel div[class*="dark:bg-slate-800"] {
        background-color: var(--sh-input-bg) !important;
        background: var(--sh-input-bg) !important;
    }

    .ow-card-header {
        background: color-mix(in srgb, var(--sh-card-bg) 96%, var(--sh-accent) 4%) !important;
        border-bottom: 1px solid var(--sh-card-border) !important;
    }

    .ow-step-badge {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--sh-accent);
        color: #fff !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        box-shadow: none !important;
    }

    .sh-title { font-size: 24px; line-height: 1.1; font-weight: 800; color: var(--sh-text-primary); }
    .sh-subtitle { margin-top: 3px; font-size: 13px; font-weight: 600; color: var(--sh-text-secondary); }

    .ow-progress-fill {
        background: linear-gradient(90deg, var(--sh-accent), #a78bfa);
        box-shadow: none !important;
    }

    .payment-input {
        background: var(--sh-input-bg, #f8fafc) !important;
        border: 1px solid var(--sh-card-border) !important;
        color: var(--sh-text-primary) !important;
        border-radius: 12px !important;
        height: 46px;
        padding: 0.65rem 0.9rem;
        transition: all 0.18s ease;
    }

    .dark .payment-input {
        background: var(--sh-input-bg) !important;
    }

    /* Absolute Zero Shadow Kill - FINAL OVERRIDE */
    html.dark.avento-theme .ow-shell,
    html.dark.avento-theme .ow-shell *,
    html.dark.avento-theme .ow-shell *::before,
    html.dark.avento-theme .ow-shell *::after {
        box-shadow: none !important;
        text-shadow: none !important;
        filter: none !important;
        -webkit-filter: none !important;
        transition: none !important;
    }

    </style>
<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="ow-shell">
        <!-- Top Bar (Estilo Sales Hub) -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <span class="ow-step-badge">4</span>
                <div>
                    <h1 class="sh-title">Pagamento</h1>
                    <p class="sh-subtitle">Etapa 4 de 5 • Defina valores e formas de pagamento</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Passo Atual</div>
                <div class="text-2xl font-black text-[#7c3aed]">80%</div>
            </div>
        </div>

        <!-- Progress Widget -->
        <div class="ow-progress p-4 mb-8">
            <div class="w-full bg-[var(--sh-input-bg)] rounded-full h-2">
                <div class="ow-progress-fill h-2 rounded-full transition-all duration-700" style="width: 80%"></div>
            </div>
        </div>

        <!-- Main Content -->

        <div class="ow-card overflow-hidden">
            <!-- Clean Minimalist Header -->
            <div class="px-6 py-6 ow-card-header">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Simple Icon -->
                        <div class="w-12 h-12 bg-[#7c3aed] rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Title -->
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                                Configuração de Pagamento
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                                Defina valores, formas de pagamento e datas
                            </p>
                        </div>
                    </div>
                    
                    <!-- Progress Badge -->
                    <div class="hidden sm:block text-right">
                        <div class="text-xs text-gray-500 dark:text-slate-400">Etapa 4 de 5</div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">80%</div>
                    </div>
                </div>
                
                <!-- Simple Progress Bar -->
                <div class="mt-4">
                    <div class="w-full bg-[var(--sh-input-bg)] rounded-full h-1.5">
                        <div class="bg-purple-600 dark:bg-purple-500 h-full rounded-full transition-all" style="width: 80%"></div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Resumo do Pedido -->
                <div class="bg-[var(--sh-card-bg)] rounded-xl border border-[var(--sh-card-border)] p-6 mb-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-[#7c3aed] rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-tight">Resumo do Pedido</h2>
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
                        
                        <div class="border-t border-gray-200 dark:border-slate-700 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-gray-700 dark:text-slate-300">Subtotal (Etapa 2 + 3):</span>
                                <span class="font-medium text-gray-900 dark:text-white" id="subtotal">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div id="surcharges-breakdown" class="space-y-1.5 pt-1">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                        
                        <!-- Diluir Valor no Pedido -->
                        <div class="mt-4 pt-4 border-t border-[var(--sh-card-border)]">
                            <div class="bg-[var(--sh-input-bg)] border border-[var(--sh-card-border)] rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">Diluir Valor no Pedido</span>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 mb-4">O valor total será distribuído proporcionalmente entre todos os itens.</p>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 flex items-center bg-[var(--sh-surface-from)] border border-[var(--sh-card-border)] rounded-lg px-3 py-2 focus-within:border-[#7c3aed] focus-within:ring-1 focus-within:ring-[#7c3aed] transition-all">
                                        <span class="text-xs font-bold text-[#7c3aed] mr-2 flex-shrink-0">R$</span>
                                        <input type="number" id="dilution-target" step="0.01" min="0"
                                               placeholder="Ex: 2200.00"
                                               class="w-full bg-transparent text-sm text-gray-900 dark:text-white placeholder-gray-500/60 focus:outline-none border-none p-0">
                                    </div>
                                    <button type="button" onclick="applyDilution()"
                                            class="px-4 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold rounded-lg transition-all whitespace-nowrap stay-white">
                                        Aplicar
                                    </button>
                                    <button type="button" onclick="resetDilution()" id="dilution-reset-btn"
                                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-600 dark:text-slate-300 text-sm font-semibold rounded-lg transition-colors whitespace-nowrap hidden">
                                        Resetar
                                    </button>
                                </div>
                                <div id="dilution-feedback" class="mt-3 text-[11px] font-medium hidden"></div>
                            </div>
                        </div>

                        <!-- Acréscimo Especial (personalizável) -->
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-[var(--sh-card-border)]">
                            <div class="flex flex-col">
                                <span class="text-gray-900 dark:text-white text-sm font-semibold">Acréscimo Especial</span>
                                <span class="text-[11px] text-gray-500 dark:text-slate-400">Personalize o valor extra</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-bold text-orange-500">+R$</span>
                                <input type="number" id="custom_surcharge" step="0.01" min="0" value="0"
                                       onchange="calculateTotal()" oninput="calculateTotal()"
                                       class="w-24 px-3 py-1.5 text-right text-sm rounded-lg border border-[var(--sh-card-border)] bg-[var(--sh-input-bg)] text-gray-900 dark:text-white focus:border-[#7c3aed] focus:ring-1 focus:ring-[#7c3aed] transition-all">
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
                        
                        <div class="mt-4 pt-4 border-t-2 border-[var(--sh-card-border)]">
                            <div class="flex justify-between items-baseline">
                                <span class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">Total Final</span>
                                <span id="total-final" class="text-2xl font-black text-[#7c3aed] dark:text-purple-400 drop-shadow-sm">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
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
                    <input type="hidden" name="item_price_overrides" id="item-price-overrides-data">

                    <!-- Data de Entrada -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-purple-500 dark:bg-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Data de Entrada *</h3>
                        </div>
                        <div class="bg-[var(--sh-card-bg)] rounded-xl border border-[var(--sh-card-border)] p-4">
                            <input type="date" id="entry_date" name="entry_date" required
                                   value="{{ $sessionPaymentData['entry_date'] ?? $order->payments->first()->entry_date ?? $order->entry_date ?? date('Y-m-d') }}"
                                   class="w-full px-4 py-2.5 rounded-lg border border-[var(--sh-card-border)] bg-[var(--sh-input-bg)] text-gray-900 dark:text-white focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] transition-all text-sm">
                        </div>
                    </div>

                    <!-- Taxa de Entrega -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-purple-500 dark:bg-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H3a1 1 0 00-1 1v10h1a2 2 0 114 0h6a2 2 0 114 0h1V9a1 1 0 00-.293-.707L16 6h-3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Taxa de Entrega</h3>
                        </div>
                        <div class="bg-[var(--sh-card-bg)] rounded-xl border border-[var(--sh-card-border)] p-4">
                            <label for="delivery_fee" class="block text-xs text-gray-600 dark:text-slate-400 mb-2 font-medium">Valor da Taxa (R$)</label>
                            <input type="number" id="delivery_fee" name="delivery_fee" step="0.01" min="0" value="{{ $sessionPaymentData['delivery_fee'] ?? $order->delivery_fee ?? 0 }}"
                                   onchange="calculateTotal()"
                                   placeholder="0.00"
                                   class="w-full px-4 py-2.5 rounded-lg border border-[var(--sh-card-border)] bg-[var(--sh-input-bg)] text-gray-900 dark:text-white placeholder-gray-500 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] transition-all text-sm">
                        </div>
                    </div>

                    <!-- Desconto -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-purple-500 dark:bg-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Desconto</h3>
                        </div>
                        <div class="bg-[var(--sh-card-bg)] rounded-xl border border-[var(--sh-card-border)] p-4">
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
                                    <label for="discount_value" class="block text-xs text-gray-600 dark:text-slate-400 mb-2 font-medium">
                                        <span id="discount-label">Valor</span>
                                    </label>
                                    <input type="number" id="discount_value" step="0.01" min="0" value="0" disabled
                                           onchange="calculateTotal()"
                                           class="w-full px-4 py-2.5 rounded-lg border border-[var(--sh-card-border)] bg-[var(--sh-input-bg)] text-gray-900 dark:text-white placeholder-gray-500 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] transition-all text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                </div>
                            </div>
                            <div id="discount-preview" class="mt-3 p-3 bg-green-500/10 border border-green-500/20 rounded-xl hidden">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-green-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-green-700 dark:text-green-400 font-medium">
                                        Desconto de <strong id="discount-preview-text">R$ 0,00</strong> aplicado
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formas de Pagamento -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-purple-500 dark:bg-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Adicionar Pagamento</h3>
                        </div>

                        <div class="bg-[var(--sh-card-bg)] rounded-xl border border-[var(--sh-card-border)] p-4">
                            <!-- Formulário de Adicionar Pagamento -->
                            <div class="bg-[var(--sh-input-bg)] rounded-xl p-4 border border-[var(--sh-card-border)] mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-2">Forma de Pagamento</label>
                                        <select id="new-payment-method" 
                                                class="w-full px-4 py-2.5 rounded-lg border border-[var(--sh-card-border)] bg-[var(--sh-surface-from)] text-gray-900 dark:text-white focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] transition-all text-sm">
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
                                               class="w-full px-4 py-2.5 rounded-lg border border-[var(--sh-card-border)] bg-[var(--sh-surface-from)] text-gray-900 dark:text-white placeholder-gray-500 focus:border-[#7c3aed] focus:ring-2 focus:ring-[#7c3aed] transition-all text-sm">
                                    </div>
                                </div>
                                
                                <!-- Valor Sugerido -->
                                <div class="bg-purple-500/5 dark:bg-purple-500/5 rounded-xl p-4 mb-4 border border-purple-200 dark:border-purple-800/30">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Valor Sugerido (50%)</span>
                                        </div>
                                        <span class="text-xl font-bold text-[#7c3aed] dark:text-purple-400" id="suggested-amount">R$ 0,00</span>
                                    </div>
                                    <button type="button" onclick="useSuggestedAmount()" style="color: white !important;" 
                                            class="w-full mt-2 px-4 py-2 bg-[#7c3aed] text-white stay-white rounded-md text-sm font-semibold transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Aplicar Valor Sugerido
                                    </button>
                                </div>

                                <button type="button" onclick="addPaymentMethod()"
                                        class="w-full px-4 py-2.5 bg-[#7c3aed] text-white stay-white rounded-lg hover:bg-[#6d28d9] transition-all text-sm font-bold shadow-sm">
                                    Adicionar Pagamento
                                </button>
                            </div>

                            <!-- Lista de Pagamentos Adicionados -->
                            <div id="payment-methods-list" class="space-y-2 mb-4">
                            <!-- Será preenchido via JavaScript -->
                        </div>

                            <!-- Resumo de Pagamentos -->
                            <div class="p-4 bg-[var(--sh-input-bg)] rounded-xl border border-[var(--sh-card-border)]">
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
                                id="payment-continue-btn"
                                style="color: white !important;"
                                class="px-6 py-2 bg-[#7c3aed] text-white stay-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:ring-offset-2 flex items-center transition-all">
                            Continuar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </section>
</div>
</div>

<script>
(function() {
    // State
    window.paymentMethods = [];
    window.subtotal = {{ $order->subtotal }};
    window.originalSubtotal = {{ $order->subtotal }};
    window.deliveryFee = {{ $sessionPaymentData['delivery_fee'] ?? $order->delivery_fee ?? 0 }};
    window.sizeSurcharges = {};
    window.orderItems = @json($order->items);
    window.itemPriceOverrides = {}; // id -> { unit_price, total_price }
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
                const overridesInput = document.getElementById('item-price-overrides-data');
                
                if (methodsInput) methodsInput.value = JSON.stringify(window.paymentMethods);
                if (surchargesInput) surchargesInput.value = JSON.stringify(window.sizeSurcharges);
                if (discTypeInput) discTypeInput.value = window.discountType;
                if (discValInput) discValInput.value = window.discountValue;
                if (overridesInput) overridesInput.value = Object.keys(window.itemPriceOverrides).length > 0
                    ? JSON.stringify(window.itemPriceOverrides)
                    : '';
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

        // Calculate UNIT PRICE for surcharge lookup (subtotal / total pieces)
        const totalPieces = window.orderItems.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
        const unitPrice = totalPieces > 0 ? window.subtotal / totalPieces : window.subtotal;
        console.log('Size surcharge calculation:', { subtotal: window.subtotal, totalPieces, unitPrice });

        const promises = Object.entries(sizeQuantities).map(([size, quantity]) => {
            if (quantity > 0) {
                return fetch(`/api/size-surcharge/${size}?price=${unitPrice}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.surcharge) {
                            const surcharge = parseFloat(data.surcharge) * quantity;
                            window.sizeSurcharges[size] = surcharge;
                            totalSurcharge += surcharge;
                            surchargesHtml += `
                                <div class="flex justify-between text-[13px] py-0.5">
                                    <span class="text-gray-600 dark:text-slate-400">Acréscimo ${size} (${quantity}x):</span>
                                    <span class="font-bold text-orange-500/90">+R$ ${surcharge.toFixed(2).replace('.', ',')}</span>
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
                        <svg class="w-5 h-5 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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

    // ─── Dilution Feature ───
    window.applyDilution = function() {
        const targetInput = document.getElementById('dilution-target');
        const feedback = document.getElementById('dilution-feedback');
        const resetBtn = document.getElementById('dilution-reset-btn');
        const target = parseFloat(targetInput?.value);

        if (!target || target <= 0) {
            if (feedback) {
                feedback.textContent = 'Digite um valor válido maior que zero.';
                feedback.className = 'mt-2 text-xs font-medium text-red-600 dark:text-red-400';
                feedback.classList.remove('hidden');
            }
            return;
        }

        const currentSubtotal = window.originalSubtotal;
        if (currentSubtotal <= 0) {
            if (feedback) {
                feedback.textContent = 'O subtotal original é zero, não é possível diluir.';
                feedback.className = 'mt-2 text-xs font-medium text-red-600 dark:text-red-400';
                feedback.classList.remove('hidden');
            }
            return;
        }

        const factor = target / currentSubtotal;
        window.itemPriceOverrides = {};
        let newSubtotal = 0;

        window.orderItems.forEach((item, idx) => {
            const originalUnitPrice = parseFloat(item.unit_price) || 0;
            const originalTotalPrice = parseFloat(item.total_price) || 0;
            const qty = parseInt(item.quantity) || 1;

            let newUnitPrice, newTotalPrice;

            // Last item absorbs rounding difference
            if (idx === window.orderItems.length - 1) {
                newTotalPrice = parseFloat((target - newSubtotal).toFixed(2));
                newUnitPrice = qty > 0 ? parseFloat((newTotalPrice / qty).toFixed(4)) : 0;
            } else {
                newUnitPrice = parseFloat((originalUnitPrice * factor).toFixed(4));
                newTotalPrice = parseFloat((newUnitPrice * qty).toFixed(2));
            }

            newSubtotal += newTotalPrice;
            window.itemPriceOverrides[item.id] = { unit_price: newUnitPrice, total_price: newTotalPrice };
        });

        // Update window.subtotal so all total calculations use the new diluted value
        window.subtotal = target;
        window.calculateTotal();

        if (feedback) {
            const diff = target - currentSubtotal;
            const sign = diff >= 0 ? '+' : '-';
            feedback.innerHTML = `
                <div class="flex items-center gap-1.5 py-1 px-2.5 bg-green-500/10 text-green-600 dark:text-green-400 rounded-lg border border-green-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Diluição aplicada: <strong>R$ ${target.toFixed(2).replace('.', ',')}</strong> (${sign}R$ ${Math.abs(diff).toFixed(2).replace('.', ',')})</span>
                </div>`;
            feedback.classList.remove('hidden');
        }
        if (resetBtn) resetBtn.classList.remove('hidden');

        // Update the subtotal display in the order summary
        const subtotalEl = document.getElementById('subtotal');
        if (subtotalEl) subtotalEl.textContent = `R$ ${target.toFixed(2).replace('.', ',')}`;
    };

    window.resetDilution = function() {
        window.subtotal = window.originalSubtotal;
        window.itemPriceOverrides = {};
        window.calculateTotal();

        const targetInput = document.getElementById('dilution-target');
        const feedback = document.getElementById('dilution-feedback');
        const resetBtn = document.getElementById('dilution-reset-btn');

        if (targetInput) targetInput.value = '';
        if (feedback) {
            feedback.classList.add('hidden');
            feedback.innerHTML = '';
        }
        if (resetBtn) resetBtn.classList.add('hidden');

        const subtotalEl = document.getElementById('subtotal');
        if (subtotalEl) subtotalEl.textContent = `R$ ${window.originalSubtotal.toFixed(2).replace('.', ',')}`;
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
