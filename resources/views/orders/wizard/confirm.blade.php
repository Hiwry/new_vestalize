@extends('layouts.admin')

@push('styles')
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

    .dark.avento-theme .ow-shell input:not([type="color"]):not([type="checkbox"]):not([type="radio"]),
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

    /* Absolute Zero Shadow Kill - FINAL OVERRIDE */
    html.dark.avento-theme .ow-shell,
    html.dark.avento-theme .ow-shell *:not(input):not(label):not(button),
    html.dark.avento-theme .ow-shell *:not(input):not(label):not(button)::before,
    html.dark.avento-theme .ow-shell *:not(input):not(label):not(button)::after {
        box-shadow: none !important;
        text-shadow: none !important;
        filter: none !important;
        -webkit-filter: none !important;
        transition: none !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="ow-shell">
        <!-- Progress Bar Stepper -->
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <span class="ow-step-badge">5</span>
                    <div>
                        <h1 class="sh-title">Confirmação do Pedido</h1>
                        <p class="sh-subtitle">Etapa Final • Revise os detalhes antes de finalizar</p>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <div class="text-[10px] text-gray-500 font-black uppercase tracking-[0.2em] mb-1">Status do Pedido</div>
                    <div class="text-2xl font-black text-[#7c3aed]">PRONTO</div>
                </div>
            </div>
            
            <div class="relative">
                <div class="flex items-center justify-between w-full mb-2">
                    @foreach(['Início', 'Produtos', 'Arte', 'Pagamento', 'Confirmação'] as $stepIdx => $stepLabel)
                        <div class="flex flex-col items-center z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $stepIdx < 4 ? 'bg-[#7c3aed] text-white' : 'bg-[#7c3aed] text-white ring-4 ring-purple-500/20' }}">
                                @if($stepIdx < 4)
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                @else
                                    5
                                @endif
                            </div>
                            <span class="text-[10px] font-bold mt-2 uppercase tracking-tighter {{ $stepIdx <= 4 ? 'text-[#7c3aed]' : 'text-gray-400' }}">{{ $stepLabel }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="absolute top-4 left-0 w-full h-[2px] bg-gray-200 dark:bg-gray-700 -z-0">
                    <div class="h-full bg-[#7c3aed] transition-all duration-1000" style="width: 100%"></div>
                </div>
            </div>
        </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
        <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- Cliente -->
            <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-4 ow-field-panel">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Cliente</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Nome</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $order->client->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Telefone</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $order->client->phone_primary }}</span>
                    </div>
                    @if($order->client->email)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">E-mail</span>
                        <span class="text-gray-900 dark:text-white font-medium truncate block">{{ $order->client->email }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-4 ow-field-panel">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Itens do Pedido</h2>
                
                <div class="space-y-3">
                    @foreach($order->items as $index => $item)
                    @php
                        $isSubLocal = $item->print_type === 'Sublimação Local';
                        $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                        $itemSizes = $itemSizes ?? [];
                        $itemPrintDesc = is_array($item->print_desc) ? $item->print_desc : (is_string($item->print_desc) ? json_decode($item->print_desc, true) : []);
                        $itemFabricPiece = $itemPrintDesc['fabric_piece'] ?? null;
                        $personalizacaoSubtotal = $item->sublimations->sum('final_price');
                        $itemTotal = ($item->unit_price * $item->quantity) + $personalizacaoSubtotal;
                    @endphp
                    <div class="border border-gray-100 dark:border-slate-700/50 rounded-xl p-4 ow-field-panel">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-[#7c3aed] dark:text-purple-400 uppercase tracking-widest">ITEM {{ $index + 1 }}</span>
                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">Valor Unitário: R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                            </div>
                            <span class="text-xl font-black text-gray-900 dark:text-white">R$ {{ number_format($itemTotal, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-3">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Processo</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->print_type }}</span>
                            </div>
                            @if($isSubLocal)
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Modelo</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->model }}</span>
                            </div>
                            @else
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Tecido</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->fabric }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Cor</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->color }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Quantidade</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->quantity }} un</span>
                            </div>
                            @if(is_array($itemFabricPiece))
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Peça vinculada</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $itemFabricPiece['quantity_label'] ?? (($itemFabricPiece['quantity'] ?? 0) . ' ' . ($itemFabricPiece['unit'] ?? '')) }}</span>
                            </div>
                            @endif
                        </div>

                        @if(is_array($itemFabricPiece))
                        <div class="mb-3 rounded-lg border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-900/10 px-3 py-2">
                            <span class="text-[11px] uppercase tracking-wide font-semibold text-emerald-700 dark:text-emerald-300">Peça de tecido do estoque</span>
                            <p class="text-sm text-gray-900 dark:text-white font-medium mt-1">{{ $itemFabricPiece['label'] ?? ('Peça #' . ($itemFabricPiece['id'] ?? '')) }}</p>
                        </div>
                        @endif

                        <!-- Tamanhos e Acréscimos -->
                        <div class="space-y-2">
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $availableSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'un'];
                                    $itemSurcharges = [];
                                @endphp
                                @foreach($availableSizes as $size)
                                    @php
                                        $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? $itemSizes[strtoupper($size)] ?? 0;
                                        $qty = (int)$qty;
                                        
                                        // Calcular acréscimo se houver
                                        if ($qty > 0) {
                                            $surchargeModel = \App\Models\SizeSurcharge::getSurchargeForSize($size, $item->unit_price);
                                            if ($surchargeModel && $surchargeModel->surcharge > 0) {
                                                $itemSurcharges[$size] = [
                                                    'qty' => $qty,
                                                    'unit' => (float)$surchargeModel->surcharge,
                                                    'total' => (float)$surchargeModel->surcharge * $qty
                                                ];
                                            }
                                        }
                                    @endphp
                                    @if($qty > 0)
                                    <span class="px-3 py-1 bg-white dark:bg-[var(--sh-input-bg)] text-gray-700 dark:text-gray-300 text-[11px] font-black rounded-lg border border-gray-200 dark:border-slate-700/50 shadow-sm uppercase">
                                        {{ $size }}: {{ $qty }}
                                    </span>
                                    @endif
                                @endforeach
                            </div>

                            @if(!empty($itemSurcharges))
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-2 border border-dashed border-gray-200 dark:border-gray-700">
                                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1 block">Acréscimos de Tamanho Especial:</span>
                                <div class="flex flex-wrap gap-x-4 gap-y-1">
                                    @foreach($itemSurcharges as $size => $data)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">{{ $size }}:</span>
                                            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-500">
                                                {{ $data['qty'] }}x R$ {{ number_format($data['unit'], 2, ',', '.') }} = 
                                                <span class="text-[#7c3aed] dark:text-purple-400 font-bold">R$ {{ number_format($data['total'], 2, ',', '.') }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Personalizações -->
                        @if($item->sublimations->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-2">Personalizações:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($item->sublimations as $sub)
                                @php
                                    $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                                    $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APP';
                                @endphp
                                <span class="px-2 py-1 bg-purple-50 dark:bg-purple-900/30 text-[#7c3aed] dark:text-purple-300 text-xs font-medium rounded">
                                    {{ $locationName }} - R$ {{ number_format($sub->final_price, 2, ',', '.') }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagamento -->
            @if($payment && $payment->count() > 0)
            <div class="bg-[var(--sh-card-bg)] rounded-xl border border-[var(--sh-card-border)] p-4 ow-field-panel">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Pagamento</h2>
                <div class="space-y-2">
                    @foreach($payment as $paymentItem)
                    <div class="flex items-center justify-between text-sm bg-[var(--sh-input-bg)] p-3 rounded-full border border-[var(--sh-card-border)] shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-500/10 rounded-full flex items-center justify-center">
                                @php
                                    $method = strtolower($paymentItem->payment_method);
                                    $icon = 'fa-money-bill-wave';
                                    if(str_contains($method, 'pix')) $icon = 'fa-brands fa-pix';
                                    elseif(str_contains($method, 'cartão') || str_contains($method, 'credito')) $icon = 'fa-credit-card';
                                @endphp
                                <i class="fa-solid {{ $icon }} text-[#7c3aed] dark:text-purple-400"></i>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300 capitalize font-medium">{{ $paymentItem->payment_method }}</span>
                        </div>
                        <span class="text-gray-900 dark:text-white font-black">R$ {{ number_format($paymentItem->entry_amount, 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                    <span class="text-sm text-amber-700 dark:text-amber-300">Nenhum pagamento registrado</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Checkout (Direita) -->
        <div class="lg:col-span-1">
            <div class="bg-[var(--sh-card-bg)] rounded-xl border border-[var(--sh-card-border)] p-5 sticky top-6 ow-field-panel">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resumo</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                        <span class="text-gray-900 dark:text-white font-medium">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    
                    @if($order->delivery_fee > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Entrega</span>
                        <span class="text-gray-900 dark:text-white font-medium">R$ {{ number_format($order->delivery_fee, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    @if($order->discount > 0)
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>Desconto</span>
                        <span class="font-medium">- R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="border-t border-[var(--sh-card-border)] pt-3 mt-3">
                        <div class="flex justify-between items-baseline">
                            <span class="text-gray-900 dark:text-white font-bold uppercase tracking-tighter text-xs">Total Final</span>
                            <span class="text-2xl font-black text-[#7c3aed] dark:text-purple-400">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.finalize') : route('orders.wizard.finalize') }}" id="finalize-form" class="mt-6" enctype="multipart/form-data">
                    @csrf
                    
                    <label for="is_event_cb" class="flex items-center gap-3 p-3 bg-purple-500/5 dark:bg-purple-500/10 rounded-full cursor-pointer mb-4 border border-purple-200/30 dark:border-purple-500/20 hover:bg-purple-500/10 dark:hover:bg-purple-500/20 transition-colors select-none">
                        <input type="checkbox" name="is_event" value="1" id="is_event_cb"
                               {{ old('is_event', ($order->is_event ?? false)) ? 'checked' : '' }}
                               onchange="updateEventToggle(this.checked)" class="sr-only">
                        <!-- Custom toggle track -->
                        <div id="event-track"
                             class="relative w-11 h-6 rounded-full flex-shrink-0 transition-colors duration-200 flex items-center"
                             style="background-color: {{ old('is_event', ($order->is_event ?? false)) ? '#7c3aed' : '' }};"
                             data-off-class="1">
                            <div id="event-thumb"
                                 class="absolute w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                                 style="top: 2px; left: 2px; transform: translateX({{ old('is_event', ($order->is_event ?? false)) ? '20px' : '0px' }});"></div>
                        </div>
                        <div>
                            <span class="text-xs font-black text-gray-900 dark:text-white block uppercase tracking-tight">Prioridade Evento</span>
                            <span class="text-[9px] text-purple-600 dark:text-purple-400 uppercase font-black tracking-widest">Produção acelerada</span>
                        </div>
                    </label>

                    <button type="submit" id="finalize-btn" style="color: white !important;" 
                            class="w-full bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold py-4 px-4 rounded-xl transition shadow-lg stay-white">
                        <span id="finalize-text">Confirmar Pedido</span>
                        <span id="finalize-loading" class="hidden">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>Processando...
                        </span>
                    </button>
                    
                    <a href="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.payment') : route('orders.wizard.payment') }}" 
                       class="block text-center text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-[#7c3aed] dark:hover:text-purple-400 mt-4 transition uppercase tracking-wider">
                        ← Voltar para Pagamento
                    </a>
                </form>
            </div>
        </div>
        </section>
</div>

<!-- Modal de Confirmação -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeConfirmModal()"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Confirmar Pedido?</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">O pedido será enviado para produção.</p>
        <div class="flex gap-3">
            <button onclick="closeConfirmModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancelar
            </button>
            <button onclick="confirmFinalize()" style="color: white !important;" 
                    class="flex-1 px-4 py-2 bg-[#7c3aed] text-white rounded-lg transition">
                Confirmar
            </button>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
(function() {
    let formSubmitted = false;

    function initConfirmPage() {
        console.log('Initializing Confirm Page...');
        const finalizeForm = document.getElementById('finalize-form');
        if (finalizeForm && !finalizeForm.dataset.listenerAttached) {
            finalizeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!formSubmitted) {
                    window.openConfirmModal();
                }
            });
            finalizeForm.dataset.listenerAttached = 'true';
        }

        const escapeListener = function(e) {
            if (e.key === 'Escape') window.closeConfirmModal();
        };
        document.removeEventListener('keydown', escapeListener);
        document.addEventListener('keydown', escapeListener);
    }

    function openConfirmModal() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    window.openConfirmModal = openConfirmModal;

    function closeConfirmModal() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeConfirmModal = closeConfirmModal;

    function confirmFinalize() {
        formSubmitted = true;
        closeConfirmModal();
        
        const btn = document.getElementById('finalize-btn');
        const text = document.getElementById('finalize-text');
        const loading = document.getElementById('finalize-loading');
        
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75');
        }
        if (text) text.classList.add('hidden');
        if (loading) loading.classList.remove('hidden');
        
        const form = document.getElementById('finalize-form');
        if (form) form.submit();
    }
    window.confirmFinalize = confirmFinalize;

    // Expose initialization
    window._confirmInitSetup = function() {
        document.removeEventListener('ajax-content-loaded', initConfirmPage);
        document.addEventListener('ajax-content-loaded', initConfirmPage);
    };
    window._confirmInitSetup();

    window.updateEventToggle = function(checked) {
        const track = document.getElementById('event-track');
        const thumb = document.getElementById('event-thumb');
        if (!track || !thumb) return;
        if (checked) {
            track.style.backgroundColor = '#7c3aed';
            thumb.style.transform = 'translateX(20px)';
        } else {
            track.style.backgroundColor = '#d1d5db'; // gray-300
            // in dark mode override to slate-600
            if (document.documentElement.classList.contains('dark')) {
                track.style.backgroundColor = '#475569';
            }
            thumb.style.transform = 'translateX(0px)';
        }
    };

    // Init toggle visual state on load
    function initEventToggle() {
        const cb = document.getElementById('is_event_cb');
        const track = document.getElementById('event-track');
        if (cb && track && !cb.checked && !track.style.backgroundColor) {
            track.style.backgroundColor = document.documentElement.classList.contains('dark') ? '#475569' : '#d1d5db';
        }
    }

    // DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { initConfirmPage(); initEventToggle(); });
    } else {
        initConfirmPage();
        initEventToggle();
    }
})();
</script>
@endpush
@endsection
