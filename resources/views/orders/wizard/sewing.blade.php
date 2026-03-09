@extends('layouts.admin')

@section('content')
<script>
(function() {
    function initSewingPage() {
        console.log('Initializing Sewing Page...');
        if (typeof window.loadOptions === 'function') window.loadOptions();
        if (typeof window.initSublimationForm === 'function') window.initSublimationForm();
        
        // Form submit handler
        const form = document.getElementById('sewing-form');
        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', function(e) {
                if (typeof window.handleSewingFormSubmit === 'function') {
                    window.handleSewingFormSubmit(e);
                }
            });
            form.dataset.listenerAttached = 'true';
        }

        // Size input listeners
        document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
            input.addEventListener('change', function() {
                if (typeof window.calculateTotal === 'function') {
                    window.calculateTotal();
                }
            });
        });

        // Wizard size input listeners
        document.querySelectorAll('.wizard-size-input').forEach(input => {
            input.addEventListener('input', function() {
                if (typeof window.calculateWizardTotal === 'function') {
                    window.calculateWizardTotal();
                }
            });
        });

        if (typeof window.updateFabricPieceSelection === 'function') {
            window.updateFabricPieceSelection();
        }
    }

    // Expose initialization for AJAX loading
    window._sewingInitSetup = function() {
        document.removeEventListener('ajax-content-loaded', initSewingPage);
        document.addEventListener('ajax-content-loaded', initSewingPage);
    };
    window._sewingInitSetup();

    // Also run on DOMContentLoaded for initial load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSewingPage);
    } else {
        initSewingPage();
    }
})();
</script>
<style>
/* AnimaÃ§Ãµes Premium */
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideInRight { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
@keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
@keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-3px) rotate(1deg); } }

.animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
.animate-slide-right { animation: slideInRight 0.4s ease-out forwards; }
.animate-float { animation: float 3s ease-in-out infinite; }

.delay-100 { animation-delay: 0.1s; opacity: 0; }
.delay-200 { animation-delay: 0.2s; opacity: 0; }

.glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
.dark .glass-card { background: rgba(15, 23, 42, 0.8); }

.hover-lift { transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.hover-lift:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15); }

/* Dashboard visual parity */
.sewing-dashboard-shell {
    max-width: 1600px;
    margin: 0 auto;
    padding: 1rem 1rem 1.5rem;
}

.sewing-dashboard-shell .glass-card {
    background: var(--card-bg) !important;
    border: 1px solid var(--border) !important;
    box-shadow: var(--shadow) !important;
}

/* Host card must not trap fixed wizard modal */
.wizard-host-card {
    overflow: visible !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.sewing-dashboard-shell .sewing-ui-surface {
    background: var(--card-bg) !important;
    border: 1px solid var(--border) !important;
    box-shadow: var(--shadow) !important;
}

.sewing-dashboard-shell .sewing-ui-muted {
    background: var(--input-bg) !important;
    border: 1px solid var(--border) !important;
}

.sewing-dashboard-shell .text-ui-primary { color: var(--foreground) !important; }
.sewing-dashboard-shell .text-ui-muted { color: var(--muted) !important; }

@media (min-width: 640px) {
    .sewing-dashboard-shell {
        padding: 1.25rem 1.5rem 1.75rem;
    }
}

@media (min-width: 1024px) {
    .sewing-dashboard-shell {
        padding: 1.25rem 2rem 2rem;
    }
}

/* Mobile responsiveness */
@media (max-width: 640px) {
    .size-grid-mobile { grid-template-columns: repeat(5, 1fr) !important; gap: 0.375rem !important; }
    .size-grid-mobile input { padding: 0.375rem !important; font-size: 12px !important; }
    .size-grid-mobile label { font-size: 10px !important; }
}

/* Wizard modal comfort */
.sewing-wizard-panel {
    width: 100%;
    height: 100%;
    max-height: 100%;
    min-height: 0;
    border-radius: 1.25rem !important;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

#wizard-content {
    overscroll-behavior: contain;
    min-height: 0;
}

#sewing-wizard-modal {
    position: fixed !important;
    inset: 0 !important;
    z-index: 10000 !important;
    overflow: hidden !important;
}

#sewing-wizard-modal .wizard-overlay {
    position: absolute;
    inset: 0;
}

#sewing-wizard-modal .wizard-frame {
    position: absolute;
    z-index: 1;
    top: 10vh;
    bottom: 10vh;
    left: calc(var(--sidebar-width, 0px) + ((100vw - var(--sidebar-width, 0px)) * 0.1));
    right: calc((100vw - var(--sidebar-width, 0px)) * 0.1);
    min-width: 0;
}

@media (max-width: 1023px) {
    #sewing-wizard-modal .wizard-frame {
        top: 6vh;
        bottom: 6vh;
        left: 1rem;
        right: 1rem;
    }
}

#sewing-wizard-modal .sewing-wizard-panel {
    background: var(--card-bg) !important;
    border-color: var(--border) !important;
    box-shadow: var(--shadow) !important;
}

#sewing-wizard-modal .wizard-head,
#sewing-wizard-modal .wizard-foot {
    background: color-mix(in srgb, var(--card-bg) 95%, transparent) !important;
    border-color: var(--border) !important;
}

#sewing-wizard-modal .wizard-bar-track {
    background: var(--input-bg) !important;
}

#wizard-options-personalizacao {
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 0.9rem;
}

.wizard-personalization-card {
    min-height: 110px;
}

@media (max-width: 640px) {
    .wizard-personalization-card {
        min-height: 98px;
    }
}
</style>

<div class="sewing-dashboard-shell">
    <!-- Progress Bar Premium -->
    <div class="mb-6 sm:mb-8 animate-fade-in-up">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-3 sm:mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#7c3aed] text-white stay-white rounded-xl sm:rounded-2xl flex items-center justify-center text-sm sm:text-base font-black shadow-xl shadow-purple-500/30 animate-float">2</div>
                <div>
                    <span class="text-base sm:text-xl font-black text-gray-900 dark:text-white">Costura e <span class="text-[#7c3aed]">PersonalizaÃ§Ã£o</span></span>
                    <p class="text-[10px] sm:text-xs text-gray-500 dark:text-slate-400 mt-0.5 font-bold uppercase tracking-widest">Etapa 2 de 5</p>
                </div>
            </div>
            <div class="flex items-center bg-white/80 dark:bg-slate-800/60 backdrop-blur-sm px-3 sm:px-4 py-2 rounded-xl sm:rounded-2xl border border-gray-100 dark:border-slate-700 shadow-lg animate-slide-right">
                <div class="text-right mr-3 sm:mr-4">
                    <div class="text-[9px] sm:text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase tracking-widest">Progresso</div>
                    <div class="text-lg sm:text-2xl font-black text-[#7c3aed] dark:text-purple-400 leading-none">40%</div>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-4 border-purple-100 dark:border-purple-900/30 flex items-center justify-center relative">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8" viewBox="0 0 36 36">
                        <path class="stroke-gray-200 dark:stroke-slate-700" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="stroke-[#7c3aed]" stroke-dasharray="40, 100" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-1.5 sm:h-2 shadow-inner overflow-hidden">
            <div class="bg-[#7c3aed] h-full rounded-full transition-all duration-500 ease-out shadow-lg shadow-purple-500/40" style="width: 40%"></div>
        </div>
    </div>

    <!-- Messages Premium -->


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- FormulÃ¡rio de Adicionar Item -->
        <div class="lg:col-span-2">
            <div class="glass-card wizard-host-card sewing-ui-surface rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-100 dark:border-slate-800 overflow-hidden">
                <!-- Header Premium -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 sewing-ui-surface">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#7c3aed] rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30 border border-[#7c3aed]">
                            <i class="fa-solid fa-plus text-white stay-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-xl font-black text-ui-primary" id="form-title">Adicionar Novo Item</h1>
                            <p class="text-[10px] sm:text-sm text-ui-muted mt-0.5 font-medium">Configure os detalhes do item de costura</p>
                        </div>
                    </div>
                </div>

                    <div class="p-6">
                        <form method="POST" action="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" data-action-url="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" id="sewing-form" class="space-y-5" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="add_item" id="form-action">
                            <input type="hidden" name="editing_item_id" value="" id="editing-item-id">

                            <!-- PersonalizaÃ§Ã£o agora Ã© selecionada dentro do modal -->
                            <div id="hidden-personalizacao-container"></div>
                            <!-- Campos escondidos para envio ao backend -->
                            <input type="hidden" name="quantity" id="quantity" value="0">
                            <input type="hidden" name="unit_price" id="unit_price" value="0">
                            <input type="hidden" name="unit_cost" id="unit_cost" value="0">
                            <input type="hidden" name="art_notes" id="art_notes" value="">
                            <!-- PersonalizaÃ§Ã£o movida para o Wizard (Etapa 1) -->

                            <!-- Wizard Trigger / Main Configuration Card -->
                            <div id="normal-wizard-trigger" class="p-5 sewing-ui-surface rounded-lg border border-gray-200 dark:border-slate-700 space-y-3">

                                <label class="block text-sm font-semibold text-ui-primary">Configuração do Item</label>
                                
                                <div class="sewing-ui-muted rounded-xl border border-gray-200 dark:border-slate-700 p-6 shadow-sm flex flex-col items-center justify-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-[#7c3aed] dark:text-purple-400 mb-2">
                                        <i class="fa-solid fa-layer-group text-3xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-ui-primary" id="summary-title">Configurar Novo Item</h4>
                                        <p class="text-sm text-ui-muted mt-1 max-w-md mx-auto" id="summary-desc">Clique abaixo para iniciar a configuraÃ§Ã£o completa do item (Tecido, Modelo, Tamanhos, etc).</p>
                                    </div>
                                    <button type="button" onclick="openSewingWizard()" class="px-6 py-3 bg-white text-[#7c3aed] border border-[#7c3aed] font-bold rounded-xl shadow-lg shadow-purple-500/10 hover:bg-purple-50 transition-all transform hover:scale-105">
                                        Iniciar Configuração
                                    </button>
                                     
                                    <!-- Selected Options Summary (Hidden initially) -->
                                    <div id="main-summary-tags" class="hidden mt-4 flex flex-wrap gap-2 justify-center">
                                        <!-- Populated JS -->
                                    </div>
                                    
                                    <!-- Price Preview (Hidden initially) -->
                                    <div id="main-price-preview" class="hidden mt-2">
                                         <span class="text-lg font-bold text-[#7c3aed] dark:text-purple-400">Total: <span id="main-price-value">R$ 0,00</span></span>
                                    </div>
                                </div>

                                <!-- Hidden Inputs to store ALL wizard values -->
                                <input type="hidden" name="tecido" id="tecido_hidden">
                                <input type="hidden" name="tipo_tecido" id="tipo_tecido_hidden">
                                <input type="hidden" name="cor" id="cor_hidden">
                                <input type="hidden" name="tipo_corte" id="tipo_corte_hidden">
                                <input type="hidden" id="detalhe_hidden">
                                <input type="hidden" name="detail_color" id="detail_color_hidden">
                                <input type="hidden" name="gola" id="gola_hidden">
                                <input type="hidden" name="collar_color" id="collar_color_hidden">
                                <!-- Sizes hidden inputs will be dynamically managed/appended or we can keep the container hidden -->
                                <div id="hidden-sizes-container" class="hidden">
                                     <!-- JS will map wizard inputs to here before submit if needed, or we just rely on the form inside modal to be the 'real' inputs if we move the form tag? 
                                          The form tag wraps the whole content. So inputs inside the modal ARE inside the form.
                                          We just need to ensure unique IDs if we duplicate.
                                          Actually, if we move the inputs TO the modal, we don't need hidden copies if the modal is inside the form.
                                          Let's check: The modal is inside <form id="sewing-form"> ?
                                          Line 215 is the modal div.
                                          Line 454 was the end of the form.
                                          So yes, existing modal IS inside the form. 
                                          We can just place the actual inputs inside the modal steps!
                                      -->
                                </div>
                            </div>

                            <div class="p-5 sewing-ui-surface rounded-lg border border-gray-200 dark:border-slate-700 space-y-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-ui-primary">Peça de Tecido do Estoque</label>
                                        <p class="text-xs text-ui-muted mt-1">Opcional. Vincule uma peça para consumir saldo automaticamente ao confirmar o pedido.</p>
                                    </div>
                                    <span id="fabric-piece-current-badge" class="hidden px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        Peça vinculada
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Selecionar peça</label>
                                        <select name="fabric_piece_id" id="fabric_piece_id"
                                                onchange="updateFabricPieceSelection()"
                                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                            <option value="">Não usar peça de tecido</option>
                                            @foreach(($fabricPieces ?? []) as $piece)
                                                <option value="{{ $piece->id }}">
                                                    {{ $piece->display_name }} | Saldo {{ number_format($piece->available_quantity, $piece->control_unit === 'metros' ? 2 : 3, ',', '.') }} {{ $piece->control_unit === 'metros' ? 'm' : 'kg' }} | {{ $piece->store?->name ?? 'Loja' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Quantidade da peça</label>
                                        <input type="number"
                                               name="fabric_piece_quantity"
                                               id="fabric_piece_quantity"
                                               min="0.001"
                                               step="0.001"
                                               placeholder="0,000"
                                               class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                    </div>
                                </div>

                                <input type="hidden" name="fabric_piece_unit" id="fabric_piece_unit">

                                <div id="fabric-piece-selection-info" class="hidden rounded-xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-900/10 px-4 py-3">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                        <div>
                                            <span class="block text-emerald-700 dark:text-emerald-300 font-semibold uppercase tracking-wide">Unidade</span>
                                            <span id="fabric-piece-unit-label" class="text-gray-900 dark:text-white font-bold">-</span>
                                        </div>
                                        <div>
                                            <span class="block text-emerald-700 dark:text-emerald-300 font-semibold uppercase tracking-wide">Saldo disponível</span>
                                            <span id="fabric-piece-available-label" class="text-gray-900 dark:text-white font-bold">-</span>
                                        </div>
                                        <div>
                                            <span class="block text-emerald-700 dark:text-emerald-300 font-semibold uppercase tracking-wide">Loja</span>
                                            <span id="fabric-piece-store-label" class="text-gray-900 dark:text-white font-bold">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SUBLIMAÃ‡ÃƒO FULLPAGE FORM (Hidden - Shows when SUB.TOTAL is selected) -->
                            <div id="sublimation-fullpage-form" class="hidden">
                                <div class="p-4 sm:p-5 bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-700">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-slate-800">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sublimação Total</h3>
                                            <p id="fullpage-step-subtitle" class="text-sm text-gray-500 dark:text-slate-400">Etapa 1 de 3 · Configuração</p>
                                        </div>
                                        <button type="button" onclick="hideSubFullpageForm()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition-colors">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Stepper -->
                                    <div id="fullpage-step-indicator" class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">
                                        <div data-step="1" class="fullpage-step-chip flex items-center gap-2 px-3 py-2 rounded-lg border border-[#7c3aed]/40 bg-[#7c3aed]/10 text-[#7c3aed]">
                                            <span class="w-6 h-6 rounded-full bg-[#7c3aed] text-white text-xs font-bold flex items-center justify-center">1</span>
                                            <span class="text-xs sm:text-sm font-semibold">Configuração</span>
                                        </div>
                                        <div data-step="2" class="fullpage-step-chip flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400">
                                            <span class="w-6 h-6 rounded-full bg-gray-200 dark:bg-slate-700 text-[11px] font-bold flex items-center justify-center">2</span>
                                            <span class="text-xs sm:text-sm font-semibold">Produção</span>
                                        </div>
                                        <div data-step="3" class="fullpage-step-chip flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400">
                                            <span class="w-6 h-6 rounded-full bg-gray-200 dark:bg-slate-700 text-[11px] font-bold flex items-center justify-center">3</span>
                                            <span class="text-xs sm:text-sm font-semibold">Revisão</span>
                                        </div>
                                    </div>

                                    <div class="space-y-5">
                                        <!-- Step 1 -->
                                        <div id="fullpage-step-1" class="fullpage-sub-step space-y-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tipo de Produto *</label>
                                                    <select id="fullpage_sub_type" onchange="loadFullpageSubAddons()" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                                                        <option value="">Selecione o tipo</option>
                                                        @if(isset($sublimationTypes))
                                                        @foreach($sublimationTypes as $type)
                                                        <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nome da Arte *</label>
                                                    <input type="text" id="fullpage_art_name" placeholder="Ex: Logo Empresa ABC" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tecido *</label>
                                                    <select id="fullpage_sub_fabric_type" onchange="toggleFullpageSpecialFabric(); calculateFullpageSubTotal();" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                                                        <option value="">Selecione</option>
                                                        <option value="PP">PP</option>
                                                        <option value="CACHARREL">CACHARREL</option>
                                                        <option value="OUTRO">OUTRO TECIDO</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Modelo *</label>
                                                    <select id="fullpage_sub_model" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                                                        <option value="">Selecione</option>
                                                        <option value="BASICA">BASICA</option>
                                                        <option value="BABYLOOK">BABYLOOK</option>
                                                        <option value="INFANTIL">INFANTIL</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="fullpage-special-fabric-fields" class="hidden p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 space-y-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-yellow-700 dark:text-yellow-300 mb-1">Nome do Tecido</label>
                                                    <input type="text" id="fullpage_sub_fabric_custom" placeholder="Ex: PP Elastano Alure" class="w-full px-3 py-2 rounded-lg border border-yellow-300 dark:border-yellow-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-yellow-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-yellow-700 dark:text-yellow-300 mb-1">Acrescimo por tecido especial (R$)</label>
                                                    <input type="number" id="fullpage_sub_fabric_surcharge" value="10.00" step="0.01" min="0" oninput="calculateFullpageSubTotal()" class="w-full px-3 py-2 rounded-lg border border-yellow-300 dark:border-yellow-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-yellow-500">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Cor do Tecido</label>
                                                    <input type="text" id="fullpage_sub_fabric_color" value="BRANCO" readonly class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Gola Padrão</label>
                                                    <input type="text" id="fullpage_sub_base_collar" value="REDONDA" readonly class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 2 -->
                                        <div id="fullpage-step-2" class="fullpage-sub-step hidden space-y-4">
                                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                                                <div class="xl:col-span-2 space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Tamanhos e Quantidades</label>
                                                        <div class="grid grid-cols-5 gap-2 mb-2">
                                                            @foreach(['PP', 'P', 'M', 'G', 'GG'] as $size)
                                                            <div class="text-center">
                                                                <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">{{ $size }}</label>
                                                                <input type="number" data-size="{{ $size }}" min="0" value="0" onchange="calculateFullpageSubTotal()" class="fullpage-sub-size w-full px-1 py-2 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-400">
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="grid grid-cols-5 gap-2">
                                                            @foreach(['EXG', 'G1', 'G2', 'G3', 'Esp.'] as $size)
                                                            <div class="text-center">
                                                                <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">{{ $size }}</label>
                                                                <input type="number" data-size="{{ $size == 'Esp.' ? 'Especial' : $size }}" min="0" value="0" onchange="calculateFullpageSubTotal()" class="fullpage-sub-size w-full px-1 py-2 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-400">
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Adicionais</label>
                                                        <div id="fullpage-sub-addons" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 bg-gray-50 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700">
                                                            <p class="text-sm text-gray-400 col-span-full text-center py-2">Selecione um tipo primeiro</p>
                                                        </div>
                                                    </div>

                                                    <div class="p-3 bg-gray-50 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700">
                                                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                                            <input type="checkbox" id="fullpage_has_addon_colors" onchange="renderFullpageAddonColorFields()" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                                                            Terá cor nos adicionais?
                                                        </label>
                                                        <div id="fullpage-addon-color-fields" class="hidden space-y-2"></div>
                                                    </div>
                                                </div>

                                                <div class="space-y-3">
                                                    <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-lg border border-gray-200 dark:border-slate-700 text-center">
                                                        <span class="block text-xs text-gray-500 dark:text-slate-400 mb-0.5">Total de Peças</span>
                                                        <span class="text-2xl font-bold text-gray-900 dark:text-white" id="fullpage-total-qty">0</span>
                                                    </div>
                                                    <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-lg border border-gray-200 dark:border-slate-700 text-center">
                                                        <span class="block text-xs text-gray-500 dark:text-slate-400 mb-0.5">Preço Unitário</span>
                                                        <span class="text-xl font-bold text-gray-900 dark:text-white" id="fullpage-unit-price">R$ 0,00</span>
                                                    </div>
                                                    <div class="bg-gray-100 dark:bg-slate-800 p-4 rounded-lg text-center border border-gray-200 dark:border-slate-700">
                                                        <span class="block text-sm text-gray-600 dark:text-slate-400 mb-1">Total do Item</span>
                                                        <span class="text-3xl font-bold text-gray-900 dark:text-white" id="fullpage-total-price">R$ 0,00</span>
                                                        <p id="fullpage-price-breakdown" class="text-xs text-gray-500 dark:text-slate-400 mt-1">Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 3 -->
                                        <div id="fullpage-step-3" class="fullpage-sub-step hidden space-y-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div class="space-y-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Arquivo Corel</label>
                                                        <label class="flex flex-col items-center justify-center w-full h-20 border border-dashed border-gray-300 dark:border-slate-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                                            <i class="fa-solid fa-upload text-gray-400 text-sm mb-0.5"></i>
                                                            <span class="text-xs text-gray-500">.CDR, .AI, .PDF</span>
                                                            <input type="file" id="fullpage_corel_file" class="hidden" accept=".cdr,.ai,.pdf,.eps">
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Imagem Capa</label>
                                                        <label class="flex flex-col items-center justify-center w-full h-20 border border-dashed border-gray-300 dark:border-slate-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                                            <i class="fa-solid fa-image text-gray-400 text-sm mb-0.5"></i>
                                                            <span class="text-xs text-gray-500">PNG, JPG</span>
                                                            <input type="file" id="fullpage_cover_image" class="hidden" accept="image/*">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Observações</label>
                                                        <textarea id="fullpage_notes" rows="6" placeholder="Observações para produção..." class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-4 rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/60">
                                                <p class="text-sm text-gray-600 dark:text-slate-300 mb-2">Resumo final</p>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                                    <div class="text-gray-700 dark:text-slate-300">Peças: <span id="fullpage-total-qty-review" class="font-semibold text-gray-900 dark:text-white">0</span></div>
                                                    <div class="text-gray-700 dark:text-slate-300">Unitário: <span id="fullpage-unit-price-review" class="font-semibold text-gray-900 dark:text-white">R$ 0,00</span></div>
                                                    <div class="text-gray-700 dark:text-slate-300">Total: <span id="fullpage-total-price-review" class="font-semibold text-gray-900 dark:text-white">R$ 0,00</span></div>
                                                </div>
                                                <p id="fullpage-price-breakdown-review" class="text-xs text-gray-500 dark:text-slate-400 mt-2">Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Nav -->
                                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <button id="fullpage-step-prev" type="button" onclick="goToPrevFullpageSubStep()" class="hidden w-full sm:w-auto px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                            <i class="fa-solid fa-arrow-left mr-1"></i>
                                            Voltar
                                        </button>

                                        <div class="w-full sm:w-auto sm:ml-auto flex gap-2">
                                            <button id="fullpage-step-next" type="button" onclick="goToNextFullpageSubStep()" class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-[#7c3aed] text-white font-semibold hover:bg-[#6d28d9] transition-colors">
                                                Próxima
                                                <i class="fa-solid fa-arrow-right ml-1"></i>
                                            </button>

                                            <button id="fullpage-step-submit" type="button" onclick="submitFullpageSubItem()" class="hidden w-full sm:w-auto px-5 py-2.5 rounded-lg bg-white hover:bg-gray-50 border border-gray-300 dark:border-slate-600 text-gray-800 dark:text-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 font-semibold transition-colors flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-plus"></i>
                                                Adicionar Item
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="sewing-wizard-modal" class="fixed inset-0 z-[10000] hidden" role="dialog" aria-modal="true">

                                <!-- Backdrop -->
                                <div class="wizard-overlay bg-black/60 backdrop-blur-sm transition-opacity" 
                                     onclick="closeSewingWizard()"></div>

                                <!-- Modal Panel -->
                                <div class="wizard-frame">
                                    <div class="sewing-wizard-panel bg-white dark:bg-slate-900 rounded-none shadow-xl w-full h-full max-w-none max-h-none overflow-hidden transition-all animate-fade-in-up border border-gray-200 dark:border-slate-700">
                                        
                                        <!-- Header -->
                                        <div class="wizard-head px-5 sm:px-6 lg:px-8 py-3 sm:py-4 flex-none border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
                                            <div>
                                                <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight">Configurar Modelo</h3>
                                                <p class="text-[10px] text-gray-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5" id="wizard-step-title">Etapa 1 de 5</p>
                                            </div>
                                            <button type="button" onclick="closeSewingWizard()" class="text-gray-400 hover:text-gray-500 transition-colors">
                                                <i class="fa-solid fa-xmark text-xl"></i>
                                            </button>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="wizard-bar-track w-full bg-gray-100 dark:bg-slate-800 h-1 flex-none">
                                            <div id="wizard-progress" class="bg-[#7c3aed] h-full transition-all duration-300" style="width: 20%"></div>
                                        </div>

                                        <!-- Steps Content -->
                                        <div class="flex-1 overflow-y-auto min-h-0 p-4 sm:p-6 lg:p-7 custom-scrollbar" id="wizard-content">
                                            
                                            <!-- Step 1: PersonalizaÃ§Ã£o -->
                                            <div id="step-1" class="wizard-step">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a PersonalizaÃ§Ã£o</h4>
                                                <p class="text-xs text-gray-500 mb-4">VocÃª pode selecionar mÃºltiplas opÃ§Ãµes.</p>
                                                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4" id="wizard-options-personalizacao">
                                                    <!-- Filled by JS -->
                                                </div>
                                            </div>

                                            <!-- Step SUB: SublimaÃ§Ã£o Total (shown when SUB.TOTAL is selected) -->
                                            <div id="step-sub" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Configurar SublimaÃ§Ã£o Total</h4>
                                                
                                                <div class="space-y-5">
                                                    <!-- Tipo de Produto SUB.TOTAL -->
                                                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tipo de Produto</label>
                                                        <select id="sub_wizard_type" onchange="loadSubWizardAddons()" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500">
                                                            <option value="">Selecione o tipo</option>
                                                            @if(isset($sublimationTypes))
                                                            @foreach($sublimationTypes as $type)
                                                            <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <!-- Adicionais -->
                                                    <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Adicionais</label>
                                                        <div id="sub-wizard-addons" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                                            <p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>
                                                        </div>
                                                    </div>

                                                    <!-- Nome da Arte -->
                                                    <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Nome da Arte *</label>
                                                        <input type="text" id="sub_wizard_art_name" placeholder="Ex: Logo Empresa ABC" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500">
                                                    </div>

                                                    <!-- Tamanhos e Quantidades -->
                                                    <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tamanhos e Quantidades</label>
                                                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3 mb-3">
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">PP</label>
                                                                <input type="number" data-size="PP" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">P</label>
                                                                <input type="number" data-size="P" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">M</label>
                                                                <input type="number" data-size="M" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G</label>
                                                                <input type="number" data-size="G" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">GG</label>
                                                                <input type="number" data-size="GG" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3">
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">EXG</label>
                                                                <input type="number" data-size="EXG" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G1</label>
                                                                <input type="number" data-size="G1" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G2</label>
                                                                <input type="number" data-size="G2" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G3</label>
                                                                <input type="number" data-size="G3" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">Esp.</label>
                                                                <input type="number" data-size="Especial" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Total de PeÃ§as e PreÃ§o -->
                                                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg text-center">
                                                                <span class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Total de PeÃ§as</span>
                                                                <span class="text-2xl font-black text-purple-600 dark:text-purple-400" id="sub-wizard-total-qty">0</span>
                                                            </div>
                                                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg text-center">
                                                                <span class="block text-xs text-gray-600 dark:text-slate-400 mb-1">PreÃ§o UnitÃ¡rio</span>
                                                                <span class="text-2xl font-black text-green-600 dark:text-green-400" id="sub-wizard-unit-price">R$ 0,00</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Arquivos -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-200 dark:border-slate-700">
                                                            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Arquivo Corel</label>
                                                            <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                <i class="fa-solid fa-file-import text-gray-400 text-xl mb-1"></i>
                                                                <span class="text-xs text-gray-500">.CDR, .AI, .PDF</span>
                                                                <input type="file" id="sub_wizard_corel" class="hidden" accept=".cdr,.ai,.pdf,.eps">
                                                            </label>
                                                        </div>
                                                        <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-200 dark:border-slate-700">
                                                            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Imagem de Capa</label>
                                                            <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                <i class="fa-solid fa-image text-gray-400 text-xl mb-1"></i>
                                                                <span class="text-xs text-gray-500">PNG, JPG, WEBP</span>
                                                                <input type="file" id="sub_wizard_cover" class="hidden" accept="image/*">
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- ObservaÃ§Ãµes -->
                                                    <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">ObservaÃ§Ãµes</label>
                                                        <textarea id="sub_wizard_notes" rows="2" placeholder="ObservaÃ§Ãµes importantes para a produÃ§Ã£o..." class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm"></textarea>
                                                    </div>

                                                    <!-- Total Final -->
                                                    <div class="p-4 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl text-white text-center">
                                                        <span class="block text-sm opacity-80 mb-1">Total do Item</span>
                                                        <span class="text-3xl font-black" id="sub-wizard-total-price">R$ 0,00</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 2: Tecido -->
                                            <div id="step-2" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione o Tecido</h4>
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-600 dark:text-slate-400 mb-2">Tecido</label>
                                                        <select id="wizard_tecido" onchange="loadWizardTiposTecido()" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] outline-none transition-all">
                                                            <option value="">Selecione o tecido</option>
                                                            <!-- Options populated by JS -->
                                                        </select>
                                                    </div>
                                                    <div id="wizard-tipo-tecido-container" class="hidden">
                                                        <label class="block text-xs font-semibold text-gray-600 dark:text-slate-400 mb-2">Tipo de Tecido</label>
                                                        <select id="wizard_tipo_tecido" onchange="onWizardTipoTecidoChange()" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] outline-none transition-all">
                                                            <option value="">Selecione o tipo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 3: Cor do Tecido -->
                                            <div id="step-3" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Cor do Tecido</h4>
                                                <!-- Search/Filter could go here -->
                                                <select id="wizard_cor" onchange="//Handled by next button filtered check or immediate JS" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] outline-none transition-all mb-4">
                                                    <option value="">Selecione uma cor</option>
                                                </select>
                                                <div id="wizard-colors-grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                                                    <!-- Visually rich color picker populated by JS -->
                                                </div>
                                            </div>

                                            <!-- Step 4: Tipo de Corte -->
                                            <div id="step-4" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione o Tipo de Corte</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="wizard-options-corte">
                                                    <div class="p-8 text-center text-gray-500">Carregando opÃ§Ãµes...</div>
                                                </div>
                                            </div>

                                            <!-- Step 5: Detalhe -->
                                            <div id="step-5" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Selecione o(s) Detalhe(s)</h4>
                                                <p class="text-[10px] text-gray-500 mb-3">VocÃª pode selecionar mÃºltiplos detalhes.</p>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 mb-4" id="wizard-options-detalhe">
                                                    <!-- Filled by JS -->
                                                </div>
                                                
                                                <div class="space-y-3">
                                                    <div class="p-3 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="checkbox" id="different_detail_color_cb" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]" onchange="toggleDetailColorUI()">
                                                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Cor do detalhe diferente do tecido?</span>
                                                        </label>
                                                    </div>
                                                    
                                                    <div id="individual-colors-toggle-container" class="hidden p-3 bg-purple-50 dark:bg-purple-900/10 rounded-xl border border-purple-200 dark:border-purple-800">
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="checkbox" id="individual_detail_colors_cb" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]" onchange="wizardData.individual_detail_colors = this.checked; renderWizardDetailColorOptions();">
                                                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Definir cores individuais por detalhe?</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 6: Cor do Detalhe -->
                                            <div id="step-6" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Cor do Detalhe</h4>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="wizard-options-cor-detalhe">
                                                     <!-- Filled by JS (using existing loop logic or JS render) -->
                                                </div>
                                            </div>

                                            <!-- Step 7: Gola -->
                                            <div id="step-7" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Gola</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4" id="wizard-options-gola">
                                                    <!-- Filled by JS -->
                                                </div>
                                                <div class="p-3 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="different_collar_color_cb" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Cor da gola diferente do tecido?</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Step 8: Cor da Gola -->
                                            <div id="step-8" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Cor da Gola</h4>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="wizard-options-cor-gola">
                                                    <!-- Filled by JS -->
                                                </div>
                                            </div>

                                            <!-- Step 9: Tamanhos -->
                                            <div id="step-9" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Defina os Tamanhos e Quantidades</h4>
                                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mb-4" id="wizard-sizes-grid">
                                                    <!-- Standard Sizes -->
                                                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'Especial'] as $size)
                                                    <div>
                                                        <label class="block text-[10px] text-gray-500 dark:text-slate-400 mb-1 font-bold text-center uppercase">{{ $size }}</label>
                                                        <input type="number" data-size="{{ $size }}" min="0" value="0" class="wizard-size-input w-full px-1 py-1.5 border border-gray-200 dark:border-slate-700 rounded-lg text-center font-bold bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] transition-all text-sm">
                                                    </div>
                                                    @endforeach
                                                </div>
                                                
                                                 <!-- Checkbox para acrÃ©scimo independente (apenas para Infantil/Baby look) -->
                                                <div id="wizard-surcharge-container" class="hidden mb-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="wizard_apply_surcharge" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Aplicar acrÃ©scimo de tamanho especial</span>
                                                    </label>
                                                </div>

                                                <!-- Checkbox para Modelagem do Cliente (Aparece se Especial > 0) -->
                                                <div id="wizard-modeling-container" class="hidden mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="wizard_is_client_modeling" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Tamanho especial Ã© pela modelagem do cliente?</span>
                                                    </label>
                                                </div>
                                                
                                                <div class="flex justify-between items-center bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-100 dark:border-purple-800/50">
                                                    <span class="text-sm font-bold text-gray-700 dark:text-slate-300">Total de PeÃ§as:</span>
                                                    <span class="text-2xl font-black text-[#7c3aed]" id="wizard-total-pieces">0</span>
                                                </div>
                                            </div>

                                            <!-- Step 10: Imagem e Obs -->
                                            <div id="step-10" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">PersonalizaÃ§Ã£o e Detalhes Finais</h4>
                                                
                                                <div class="space-y-5">
                                                    <!-- Image Upload -->
                                                    <div class="p-4 border border-dashed border-gray-300 dark:border-slate-600 rounded-xl bg-gray-50 dark:bg-slate-800/50 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors text-center cursor-pointer relative" onclick="document.getElementById('wizard_file_input').click()">
                                                        <input type="file" id="wizard_file_input" class="hidden" accept="image/*" onchange="previewWizardImage(this)">
                                                        
                                                        <div id="wizard-image-placeholder" class="py-4">
                                                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                                                            <p class="text-sm font-semibold text-gray-600 dark:text-slate-300">Clique para enviar a imagem de capa</p>
                                                            <p class="text-xs text-gray-400">PNG, JPG ou WEBP (Max. 10MB)</p>
                                                        </div>
                                                        <div id="wizard-image-preview-container" class="hidden relative inline-block group">
                                                             <img id="wizard-image-preview" class="h-32 object-contain rounded-lg shadow-sm border border-gray-200">
                                                             <button onclick="event.stopPropagation(); clearWizardImage()" class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs shadow-md hover:bg-red-600 transition-transform hover:scale-110 z-10"><i class="fa-solid fa-times"></i></button>
                                                        </div>
                                                    </div>

                                                    <!-- Notes -->
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">ObservaÃ§Ãµes</label>
                                                        <textarea id="wizard_notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed]" placeholder="Alguma observaÃ§Ã£o importante para a produÃ§Ã£o?"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 11: Resumo Final -->
                                            <div id="step-11" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-center text-gray-900 dark:text-white mb-6">ConferÃªncia Final</h4>
                                                
                                                <div class="bg-gray-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 space-y-4">
                                                    <!-- Dynamic Summary List -->
                                                    <div class="space-y-3 text-sm">
                                                        <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">Tecido:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-tecido-val">-</span>
                                                        </div>
                                                        <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">Cor:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-cor-val">-</span>
                                                        </div>
                                                        <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">Modelo:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-modelo-val">-</span>
                                                        </div>
                                                         <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">PeÃ§as:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-pecas-val">0</span>
                                                        </div>
                                                    </div>

                                                    <!-- Prices -->
                                                    <div class="mt-6 pt-4 border-t border-gray-300 dark:border-slate-600">
                                                        <h5 class="font-bold text-gray-900 dark:text-white mb-3">Custos e Valores</h5>
                                                        
                                                        <!-- Admin Only Unit Cost -->
                                                        <div class="flex justify-between items-center p-3 bg-white dark:bg-slate-900 border border-red-200 dark:border-red-900/30 rounded-lg mb-3" 
                                                             style="display: {{ auth()->user()->isAdmin() ? 'flex' : 'none' }}">
                                                            <span class="text-red-600 dark:text-red-400 font-bold text-sm">Custo UnitÃ¡rio:</span>
                                                            <div class="flex items-center">
                                                                <span class="text-red-600 dark:text-red-400 font-bold mr-1">R$</span>
                                                                <input type="number" id="wizard_unit_cost" class="w-20 bg-transparent text-right font-bold text-red-600 dark:text-red-400 border-none p-0 focus:ring-0" value="0.00" step="0.01">
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-between items-center p-4 bg-white dark:bg-slate-900 border border-purple-200 dark:border-purple-900/30 rounded-xl shadow-sm">
                                                            <span class="text-[#7c3aed] dark:text-purple-400 font-bold">Valor UnitÃ¡rio:</span>
                                                            <span class="text-2xl font-black text-[#7c3aed] dark:text-purple-400" id="wizard-final-price">R$ 0,00</span>
                                                        </div>
                                                    </div>

                                                    <button type="button" onclick="submitSewingWizard()" class="w-full py-4 mt-6 bg-[#7c3aed] hover:bg-[#6d28d9] text-white stay-white font-bold rounded-xl shadow-lg shadow-purple-500/20 transition-all transform hover:scale-[1.02]">
                                                        Confirmar e Adicionar Item
                                                    </button>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Footer -->
                                        <div class="wizard-foot px-5 sm:px-6 lg:px-8 py-3 sm:py-4 flex-none border-t border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-none">
                                            <button type="button" id="wizard-prev-btn" onclick="wizardPrevStep()" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200 text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                                                â† Voltar
                                            </button>
                                            <div class="flex gap-2">
                                                <button type="button" id="wizard-next-btn" onclick="wizardNextStep()" class="px-6 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white stay-white text-sm font-bold rounded-lg transition-all shadow-md shadow-purple-500/20">
                                                    PrÃ³ximo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tamanhos (Moved above) -->

                            <!-- <!-- BotÃµes (Removido - controllado pelo Wizard) -->
                            <!-- <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-slate-700"> ... </div> -->
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Resumo dos Itens -->
            <div class="lg:col-span-1" id="items-sidebar-container">
                @include('orders.wizard.partials.items_sidebar')
            </div>
        </div>
    </div>
    <!-- Modal de ConfirmaÃ§Ã£o de ExclusÃ£o -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-slate-700 transform transition-all scale-100 opacity-100">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Remover Item?</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Esta aÃ§Ã£o nÃ£o pode ser desfeita. O item serÃ¡ removido permanentemente do pedido.</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white stay-white rounded-lg font-medium shadow-lg shadow-red-500/30 transition-all transform hover:scale-105">
                        Sim, Remover
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($sublimationEnabled) && $sublimationEnabled)
@include('orders.wizard.partials.sublimation_modal')
@endif

@push('page-scripts')
<script>
(function() {
    const storageUrl = "{{ asset('storage') }}/";
    window.storageUrl = storageUrl;
    
    // Options for Wizard
    let options = {
        tecido: @json($fabrics ?? []),
        cor: @json($colors ?? []),
        personalizacao: @json($personalizationOptions ?? [])
    };
    window.options = options;
    const fabricPiecesData = @json(
        collect($fabricPieces ?? [])->map(fn($piece) => [
            'id' => $piece->id,
            'label' => $piece->display_name,
            'unit' => $piece->control_unit,
            'unit_label' => $piece->control_unit === 'metros' ? 'Metros' : 'Kg',
            'unit_suffix' => $piece->control_unit === 'metros' ? 'm' : 'kg',
            'available_quantity' => (float) $piece->available_quantity,
            'store_name' => $piece->store?->name,
        ])->values()
    );
    window.fabricPiecesData = fabricPiecesData;

    function resetFabricPieceSelection() {
        const select = document.getElementById('fabric_piece_id');
        const qtyInput = document.getElementById('fabric_piece_quantity');
        const unitInput = document.getElementById('fabric_piece_unit');
        const info = document.getElementById('fabric-piece-selection-info');
        const badge = document.getElementById('fabric-piece-current-badge');

        if (select) select.value = '';
        if (qtyInput) {
            qtyInput.value = '';
            qtyInput.removeAttribute('max');
            qtyInput.step = '0.001';
        }
        if (unitInput) unitInput.value = '';
        if (info) info.classList.add('hidden');
        if (badge) badge.classList.add('hidden');
    }
    window.resetFabricPieceSelection = resetFabricPieceSelection;

    function updateFabricPieceSelection() {
        const select = document.getElementById('fabric_piece_id');
        const qtyInput = document.getElementById('fabric_piece_quantity');
        const unitInput = document.getElementById('fabric_piece_unit');
        const info = document.getElementById('fabric-piece-selection-info');
        const badge = document.getElementById('fabric-piece-current-badge');
        const unitLabel = document.getElementById('fabric-piece-unit-label');
        const availableLabel = document.getElementById('fabric-piece-available-label');
        const storeLabel = document.getElementById('fabric-piece-store-label');

        if (!select || !qtyInput || !unitInput) return;

        const piece = fabricPiecesData.find(item => String(item.id) === String(select.value));
        if (!piece) {
            resetFabricPieceSelection();
            return;
        }

        unitInput.value = piece.unit;
        qtyInput.step = piece.unit === 'metros' ? '0.01' : '0.001';
        qtyInput.min = piece.unit === 'metros' ? '0.01' : '0.001';
        qtyInput.max = piece.available_quantity;

        if (!qtyInput.value) {
            qtyInput.value = piece.available_quantity < 1
                ? piece.available_quantity
                : (piece.unit === 'metros' ? '1.00' : '1.000');
        }

        if (unitLabel) unitLabel.textContent = piece.unit_label;
        if (availableLabel) {
            availableLabel.textContent = `${Number(piece.available_quantity).toLocaleString('pt-BR', {
                minimumFractionDigits: piece.unit === 'metros' ? 2 : 3,
                maximumFractionDigits: piece.unit === 'metros' ? 2 : 3
            })} ${piece.unit_suffix}`;
        }
        if (storeLabel) storeLabel.textContent = piece.store_name || 'N/A';
        if (info) info.classList.remove('hidden');
        if (badge) badge.classList.remove('hidden');
    }
    window.updateFabricPieceSelection = updateFabricPieceSelection;

    // Ãcones e cores especÃ­ficos por tipo de personalizaÃ§Ã£o
    const personalizationIconMap = {
        dtf:            { icon: 'fa-print',        bubble: 'bg-orange-100 dark:bg-orange-900/30',  color: 'text-orange-600 dark:text-orange-400' },
        serigrafia:     { icon: 'fa-fill-drip',    bubble: 'bg-indigo-100 dark:bg-indigo-900/30',  color: 'text-indigo-600 dark:text-indigo-400' },
        bordado:        { icon: 'fa-pen-nib',      bubble: 'bg-pink-100 dark:bg-pink-900/30',      color: 'text-pink-600 dark:text-pink-400' },
        emborrachado:   { icon: 'fa-cubes',        bubble: 'bg-emerald-100 dark:bg-emerald-900/30',color: 'text-emerald-600 dark:text-emerald-400' },
        sub_local:      { icon: 'fa-layer-group',  bubble: 'bg-purple-100 dark:bg-purple-900/30',  color: 'text-purple-600 dark:text-purple-400' },
        sub_total:      { icon: 'fa-image',        bubble: 'bg-cyan-100 dark:bg-cyan-900/30',      color: 'text-cyan-700 dark:text-cyan-300' },
        lisas:          { icon: 'fa-shirt',        bubble: 'bg-gray-100 dark:bg-slate-800',        color: 'text-gray-600 dark:text-gray-300' },
        default:        { icon: 'fa-shirt',        bubble: 'bg-gray-100 dark:bg-slate-800',        color: 'text-[#7c3aed] dark:text-[#7c3aed]' }
    };
    window.personalizationIconMap = personalizationIconMap;

    function normalizePersonalizationKey(value) {
        return (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^\w]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }
    window.normalizePersonalizationKey = normalizePersonalizationKey;

    function getPersonalizationSortWeight(item) {
        const key = normalizePersonalizationKey(item?.slug || item?.name || '');
        if (key.includes('dtf')) return 10;
        if (key.includes('serigraf')) return 20;
        if (key.includes('bordad')) return 30;
        if (key.includes('emborrach')) return 40;
        if (key.includes('sub_local') || (key.includes('sub') && key.includes('local'))) return 50;
        if (key.includes('sub_total') || (key.includes('sub') && key.includes('total'))) return 60;
        if (key.includes('lisa')) return 70;
        return 900;
    }
    window.getPersonalizationSortWeight = getPersonalizationSortWeight;

    // Mapa de cores conhecidas por nome
    const colorNameToHex = {
        'preto': '#000000', 'black': '#000000',
        'branco': '#FFFFFF', 'white': '#FFFFFF',
        'azul': '#2563EB', 'blue': '#2563EB', 'azul marinho': '#1E3A5F', 'azul royal': '#4169E1', 'azul celeste': '#87CEEB', 'azul turquesa': '#40E0D0',
        'vermelho': '#DC2626', 'red': '#DC2626', 'vermelho escuro': '#8B0000', 'bordÃ´': '#800020', 'vinho': '#722F37',
        'verde': '#16A34A', 'green': '#16A34A', 'verde limÃ£o': '#32CD32', 'verde escuro': '#006400', 'verde musgo': '#8A9A5B', 'verde militar': '#4B5320', 'verde Ã¡gua': '#66CDAA',
        'amarelo': '#F59E0B', 'yellow': '#F59E0B', 'amarelo ouro': '#FFD700', 'mostarda': '#FFDB58',
        'laranja': '#EA580C', 'orange': '#EA580C',
        'rosa': '#EC4899', 'pink': '#EC4899', 'rosa claro': '#FFB6C1', 'rosa pink': '#FF69B4', 'rosa bebÃª': '#F4C2C2',
        'roxo': '#7C3AED', 'purple': '#7C3AED', 'violeta': '#8B5CF6', 'lilÃ¡s': '#C8A2C8',
        'cinza': '#6B7280', 'gray': '#6B7280', 'grey': '#6B7280', 'cinza claro': '#D1D5DB', 'cinza escuro': '#374151', 'cinza mescla': '#9CA3AF', 'mescla': '#9CA3AF', 'chumbo': '#36454F',
        'marrom': '#92400E', 'brown': '#92400E', 'cafÃ©': '#6F4E37', 'chocolate': '#7B3F00', 'caramelo': '#FFD59A', 'bege': '#F5F5DC',
        'nude': '#E3BC9A', 'salmÃ£o': '#FA8072', 'coral': '#FF7F50', 'creme': '#FFFDD0', 'off-white': '#FAF9F6',
        'dourado': '#FFD700', 'gold': '#FFD700', 'prata': '#C0C0C0', 'silver': '#C0C0C0',
        'cyan': '#06B6D4', 'ciano': '#06B6D4', 'magenta': '#D946EF', 'fucsia': '#FF00FF'
    };
    window.colorNameToHex = colorNameToHex;

    function getColorHex(colorName) {
        if (!colorName) return '#ccc';
        const normalized = colorName.toLowerCase().trim();
        return colorNameToHex[normalized] || '#ccc';
    }
    window.getColorHex = getColorHex;

    @php
        $safeSublimationTypes = isset($sublimationTypes) 
            ? $sublimationTypes->map(fn($t) => ['slug' => $t->slug, 'name' => $t->name])->values()
            : [];
        $safePreselectedTypes = $preselectedTypes ?? [];
    @endphp

    // SUB. TOTAL - Dados e ConfiguraÃ§Ãµes
    const sublimationEnabled = {{ isset($sublimationEnabled) && $sublimationEnabled ? 'true' : 'false' }};
    window.sublimationEnabled = sublimationEnabled;
    const sublimationTypes = @json($safeSublimationTypes);
    window.sublimationTypes = sublimationTypes;
    let sublimationAddonsCache = {};
    window.sublimationAddonsCache = sublimationAddonsCache;
    
    // Tipos de personalizaÃ§Ã£o prÃ©-selecionados na etapa anterior
    const preselectedPersonalizationTypes = @json($safePreselectedTypes);
    window.preselectedPersonalizationTypes = preselectedPersonalizationTypes;

    let itemToDeleteId = null;

    function openDeleteModal(itemId) {
        itemToDeleteId = itemId;
        const modal = document.getElementById('delete-modal');
        if (modal) modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }
    window.openDeleteModal = openDeleteModal;

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        if (modal) modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        itemToDeleteId = null;
    }
    window.closeDeleteModal = closeDeleteModal;

    // VariÃ¡vel global para dados dos itens
    let itemsData = {!! json_encode($order->items->toArray()) !!};
    window.itemsData = itemsData;

    async function confirmDelete() {
        if (!itemToDeleteId) return;

        const btn = document.querySelector('#delete-modal button.bg-red-600');
        const originalText = btn ? btn.innerText : 'Sim, Remover';
        if (btn) {
            btn.innerHTML = 'Removendo...';
            btn.disabled = true;
        }

        try {
            const formData = new FormData();
            formData.append('action', 'delete_item');
            formData.append('item_id', itemToDeleteId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch("{{ route('orders.wizard.sewing') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Optimistic: The item is already being updated by the sidebar HTML below
                // handle instant visual feedback
                const itemEl = document.getElementById(`sidebar-item-${itemToDeleteId}`);
                if (itemEl) itemEl.style.opacity = '0';

                // Atualizar HTML da sidebar
                const sidebar = document.getElementById('items-sidebar-container');
                if (sidebar) {
                    sidebar.innerHTML = data.html;
                }
                
                // Atualizar dados dos itens
                if (data.items_data) {
                    itemsData = data.items_data;
                    window.itemsData = itemsData;
                }
                
                if (window.showToast) window.showToast('Item removido com sucesso!', 'success');
                closeDeleteModal();
            } else {
                alert('Erro ao remover item: ' + (data.message || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Erro na exclusÃ£o:', error);
            alert('Erro ao processar a exclusÃ£o.');
        } finally {
            if (btn) {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }
    }
    window.confirmDelete = confirmDelete;

    async function handleSewingFormSubmit(e) {
        if (e) e.preventDefault();
        
        const form = document.getElementById('sewing-form');
        if (!form || form.dataset.submitting === 'true') return;

        // ValidaÃ§Ã£o atualizada para o Wizard
        const personalizacaoInputs = document.querySelectorAll('input[name="personalizacao[]"]');
        
        if (personalizacaoInputs.length === 0) {
             const preselected = document.querySelectorAll('.preselected-personalization');
             if (preselected.length === 0) {
                 alert('Por favor, selecione pelo menos uma personalizaÃ§Ã£o.');
                 return;
             }
        }

        const quantity = parseInt(document.getElementById('quantity').value || 0);
        
        if (quantity === 0) {
             let hasSize = false;
             document.querySelectorAll('input[name^="tamanhos"]').forEach(i => {
                 if(parseInt(i.value) > 0) hasSize = true;
             });
             if (!hasSize) {
                 alert('Por favor, adicione pelo menos uma peÃ§a nos tamanhos.');
                 return;
             }
        }

        // UI de processamento
        const submitBtn = document.getElementById('submit-button');
        let originalText = '';
        if (submitBtn) {
            originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processando...
            `;
        }
        form.dataset.submitting = 'true';

        try {
            const formData = new FormData(form);
            const actionUrl = form.dataset.actionUrl || form.getAttribute('action');
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Erro HTTP na submissÃ£o:', response.status, text);
                alert('Erro ao salvar item: ' + (response.statusText || 'erro HTTP'));
                return;
            }

            const data = await response.json();

            if (data.success) {
                if (data.html) {
                    const sidebar = document.getElementById('items-sidebar-container');
                    if (sidebar) sidebar.innerHTML = data.html;
                }

                if (data.items_data) {
                    itemsData = data.items_data;
                    window.itemsData = itemsData;
                }

                const action = document.getElementById('form-action').value;
                if (action === 'add_item') {
                    // Reset form instead of reload
                    if (typeof window.resetForm === 'function') window.resetForm();
                    else form.reset();
                    
                    if (window.showToast) window.showToast('Item adicionado com sucesso!', 'success');
                } else {
                    if (window.showToast) window.showToast('Item atualizado com sucesso!', 'success');
                }
                
                cancelEdit(); 
                
            } else {
                 if (data.errors) {
                     let msg = 'Erros de validaÃ§Ã£o:\n';
                     for (let field in data.errors) {
                         msg += `- ${data.errors[field].join(', ')}\n`;
                     }
                     alert(msg);
                 } else {
                     alert(data.message || 'Erro ao salvar item.');
                 }
            }

        } catch (error) {
            console.error('Erro no envio:', error);
            alert('Ocorreu um erro ao processar sua solicitaÃ§Ã£o.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = document.getElementById('form-action').value === 'update_item' ? 'Salvar AlteraÃ§Ãµes' : 'Adicionar Item';
            }
            if (form) form.dataset.submitting = 'false';
        }
    }
    function resetForm() {
        if (typeof window.resetSewingWizard === 'function') {
            window.resetSewingWizard();
        }
        
        const form = document.getElementById('sewing-form');
        if (form) {
            form.reset();
            const editingId = document.getElementById('editing-item-id');
            if (editingId) editingId.value = '';
            
            const formAction = document.getElementById('form-action');
            if (formAction) formAction.value = 'add_item';
            
            const formTitle = document.getElementById('form-title');
            if (formTitle) formTitle.innerText = 'Adicionar Novo Item';

            // Reset selected types in Step 1
            const types = document.querySelectorAll('.personalization-type-checkbox');
            types.forEach(cb => cb.checked = false);
            
            // Clear current customization tags
            const tags = document.getElementById('main-summary-tags');
            if (tags) {
                tags.innerHTML = '';
                tags.classList.add('hidden');
            }
            
            // Back to step 1
            if (typeof window.goToWizardStep === 'function') {
                window.goToWizardStep(1);
            }

            resetFabricPieceSelection();
        }
    }
    window.resetForm = resetForm;

    window.handleSewingFormSubmit = handleSewingFormSubmit;

    let optionsWithParents = {};
    window.optionsWithParents = optionsWithParents;

    function loadOptions() {
        fetch('/api/product-options')
            .then(response => response.json())
            .then(data => {
                options = data;
                window.options = options;
                return fetch('/api/product-options-with-parents');
            })
            .then(response => response.json())
            .then(data => {
                optionsWithParents = data;
                window.optionsWithParents = optionsWithParents;
                console.log('Options loaded.');
                if (typeof window.loadWizardOptionsForStep === 'function' && typeof window.wizardCurrentStep !== 'undefined') {
                    loadWizardOptionsForStep(window.wizardCurrentStep);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar opÃ§Ãµes:', error);
            });
    }
    window.loadOptions = loadOptions;

    async function loadStockByCutType() {
        const cutTypeId = document.getElementById('tipo_corte')?.value;
        
        if (!cutTypeId) {
            const stockSection = document.getElementById('stock-info-section');
            if (stockSection) stockSection.classList.add('hidden');
            return;
        }
        
        try {
            const params = new URLSearchParams({
                cut_type_id: cutTypeId
            });
            
            const response = await fetch(`/api/stocks/by-cut-type?${params}`);
            const data = await response.json();
            
            const stockSection = document.getElementById('stock-info-section');
            const stockBySize = document.getElementById('stock-by-size');
            
            if (data.success && data.stock_by_size && data.stock_by_size.length > 0) {
                let html = '';
                data.stock_by_size.forEach(item => {
                    const hasStock = item.available > 0;
                    const bgColor = hasStock ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600';
                    
                    let storeDetails = '';
                    if (item.stores && item.stores.length > 0) {
                        storeDetails = item.stores.map(store => {
                            const storeHasStock = store.available > 0;
                            const storeColor = storeHasStock ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500';
                            const storeBg = storeHasStock ? 'bg-green-100 dark:bg-green-900/40' : 'bg-gray-100 dark:bg-gray-600';
                            return `<span class="ml-2 px-2 py-0.5 text-xs rounded ${storeBg} ${storeColor}" title="${store.store_name}">
                                ${store.store_name.replace('Loja ', '')}: ${store.available}${store.reserved > 0 ? ' (R:' + store.reserved + ')' : ''}
                            </span>`;
                        }).join('');
                    }
                    
                    html += `
                        <div class="p-2 ${bgColor} rounded border">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${item.size}:</span>
                                ${hasStock ? `
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                         ${item.available} total
                                    </span>
                                ` : `
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                         Sem estoque
                                    </span>
                                `}
                            </div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                ${storeDetails}
                            </div>
                        </div>
                    `;
                });
                
                if (stockSection && stockBySize) {
                    stockSection.classList.remove('hidden');
                    stockBySize.innerHTML = html;
                }
            } else {
                if (stockSection) {
                    stockSection.classList.remove('hidden');
                    if (stockBySize) {
                        stockBySize.innerHTML = '<p class="text-sm text-yellow-600 dark:text-yellow-400 text-center py-2"> Nenhum estoque cadastrado para este produto</p>';
                    }
                }
            }
        } catch (error) {
            console.error('Erro ao buscar estoque:', error);
            const stockSection = document.getElementById('stock-info-section');
            if (stockSection) stockSection.classList.add('hidden');
        }
    }
    window.loadStockByCutType = loadStockByCutType;

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        const qtyInput = document.getElementById('quantity');
        if (qtyInput) qtyInput.value = total;
        
        // Update sidebar if needed...
    }
    window.calculateTotal = calculateTotal;



    // --- WIZARD LOGIC ---
    let wizardCurrentStep = 1;
    window.wizardCurrentStep = wizardCurrentStep;
    const wizardTotalSteps = 11;
    window.wizardTotalSteps = wizardTotalSteps;
    const isAdmin = @json(auth()->user()->isAdmin());
    window.isAdmin = isAdmin;

    let wizardData = {
        tecido: null,
        tipo_tecido: null,
        cor: null,
        tipo_corte: null,
        detalhe: [], // Alterado para array
        detail_color: null,
        detail_colors: {}, // Novo: para cores individuais por detalhe
        individual_detail_colors: false, // Novo toggle
        gola: null,
        collar_color: null,
        personalizacao: [],
        image: null,
        imageUrl: null,
        notes: '',
        sizes: {},
        unit_cost: 0,
        unit_price: 0
    };
    window.wizardData = wizardData;

    let selectedPersonalizacoes = [];
    window.selectedPersonalizacoes = selectedPersonalizacoes;

    function ensureSewingWizardPortal() {
        const sewingForm = document.getElementById('sewing-form');
        const modalInsideForm = sewingForm ? sewingForm.querySelector('#sewing-wizard-modal') : null;
        const modal = modalInsideForm || document.getElementById('sewing-wizard-modal');
        if (!modal) return null;

        document.querySelectorAll('#sewing-wizard-modal').forEach(existingModal => {
            if (existingModal !== modal) existingModal.remove();
        });

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        return modal;
    }
    window.ensureSewingWizardPortal = ensureSewingWizardPortal;

    function openSewingWizard() {
        const modal = ensureSewingWizardPortal();
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            updateWizardUI();
        }
    }
    window.openSewingWizard = openSewingWizard;

    function closeSewingWizard() {
        const modal = ensureSewingWizardPortal();
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    window.closeSewingWizard = closeSewingWizard;

    function wizardNextStep() {
        if (!validateStep(wizardCurrentStep)) return;

        // Check if SUB.TOTAL is selected at step 1 - redirect to step-sub
        if (wizardCurrentStep === 1 && isSublimationTotalSelected()) {
            // Close modal and show fullpage sublimation form
            closeSewingWizard();
            showSubFullpageForm();
            return;
        }


        if (wizardCurrentStep < wizardTotalSteps) {
            // Skip logic for Detail Color
            if (wizardCurrentStep === 5) {
                const isDifferentDetail = document.getElementById('different_detail_color_cb')?.checked;
                if (!hasWizardRealDetail() || !isDifferentDetail) {
                    wizardData.detail_color = wizardData.cor;
                    wizardCurrentStep += 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            // Skip logic for Collar Color
            if (wizardCurrentStep === 7) {
                const isDifferentCollar = document.getElementById('different_collar_color_cb')?.checked;
                const gola = wizardData.gola;
                if (!gola || !gola.name || gola.name.toLowerCase().includes('sem') || !isDifferentCollar) {
                    wizardData.collar_color = wizardData.cor;
                    wizardCurrentStep += 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            wizardCurrentStep++;
            window.wizardCurrentStep = wizardCurrentStep;
            updateWizardUI();
        }
    }
    window.wizardNextStep = wizardNextStep;

    function wizardPrevStep() {
        // Handle going back from step-sub
        if (isInSublimationMode) {
            isInSublimationMode = false;
            window.isInSublimationMode = false;
            
            // Hide step-sub and sublimation submit button
            const subStep = document.getElementById('step-sub');
            if (subStep) subStep.classList.add('hidden');
            
            const subSubmitBtn = document.getElementById('wizard-sub-submit-btn');
            if (subSubmitBtn) subSubmitBtn.classList.add('hidden');
            
            // Show next button
            const nextBtn = document.getElementById('wizard-next-btn');
            if (nextBtn) nextBtn.classList.remove('hidden');
            
            // Reset sublimation step
            resetSubWizardStep();
            
            // Go back to step 1
            wizardCurrentStep = 1;
            window.wizardCurrentStep = wizardCurrentStep;
            updateWizardUI();
            return;
        }
        
        if (wizardCurrentStep > 1) {
            // Skip logic backward for Detail Color
            if (wizardCurrentStep === 7) {
                const isDifferentDetail = document.getElementById('different_detail_color_cb')?.checked;
                if (!hasWizardRealDetail() || !isDifferentDetail) {
                    wizardCurrentStep -= 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            // Skip logic backward for Sizes
            if (wizardCurrentStep === 9) {
                const isDifferentCollar = document.getElementById('different_collar_color_cb')?.checked;
                const gola = wizardData.gola;
                if (!gola || !gola.name || gola.name.toLowerCase().includes('sem') || !isDifferentCollar) {
                    wizardCurrentStep -= 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            wizardCurrentStep--;
            window.wizardCurrentStep = wizardCurrentStep;
            updateWizardUI();
        }
    }
    window.wizardPrevStep = wizardPrevStep;


    function validateStep(step) {
        if (step === 1) {
             if (!wizardData.personalizacao || wizardData.personalizacao.length === 0) {
                 alert('Selecione pelo menos uma personalização.');
                 return false;
             }
        }
        if (step === 2) {
            if (!wizardData.tecido) {
                alert('Selecione um tecido para continuar.');
                return false;
            }
        }
        if (step === 3) {
            if (!wizardData.cor) {
                alert('Selecione uma cor para continuar.');
                return false;
            }
        }
        if (step === 4) {
             if (!wizardData.tipo_corte) {
                alert('Selecione um tipo de corte.');
                return false;
            }
        }
        if (step === 9) {
            const total = calculateWizardTotal();
            if (total <= 0) {
                alert('Informe a quantidade de peças (pelo menos 1).');
                return false;
            }
            wizardData.sizes = {};
            document.querySelectorAll('.wizard-size-input').forEach(input => {
                const val = parseInt(input.value) || 0;
                if(val > 0) wizardData.sizes[input.dataset.size] = val;
            });
        }
        return true;
    }
    window.validateStep = validateStep;

    function updateWizardUI() {
        const titles = [
            "Personalização", "Tecido", "Cor do Tecido", "Modelo", "Detalhe", "Cor do Detalhe", 
            "Gola", "Cor da Gola", "Tamanhos", "Imagem / Obs", "Resumo"
        ];
        const titleEl = document.getElementById('wizard-step-title');
        if (titleEl) titleEl.textContent = `${titles[wizardCurrentStep-1]} (Etapa ${wizardCurrentStep} de ${wizardTotalSteps})`;
        
        const progressEl = document.getElementById('wizard-progress');
        if (progressEl) progressEl.style.width = `${(wizardCurrentStep / wizardTotalSteps) * 100}%`;

        for (let i = 1; i <= wizardTotalSteps; i++) {
            const stepEl = document.getElementById(`step-${i}`);
            if (stepEl) {
                if (i === wizardCurrentStep) {
                    stepEl.classList.remove('hidden');
                    loadWizardOptionsForStep(wizardCurrentStep);
                } else {
                    stepEl.classList.add('hidden');
                }
            }
        }
        
        const prevBtn = document.getElementById('wizard-prev-btn');
        if (prevBtn) {
            prevBtn.disabled = wizardCurrentStep === 1;
            prevBtn.classList.toggle('opacity-50', wizardCurrentStep === 1);
            prevBtn.classList.toggle('cursor-not-allowed', wizardCurrentStep === 1);
        }
        
        const nextBtn = document.getElementById('wizard-next-btn');
        if (nextBtn) {
            if(wizardCurrentStep === wizardTotalSteps) {
                nextBtn.classList.add('hidden');
            } else {
                nextBtn.classList.remove('hidden');
            }
        }

        const submitBtn = document.getElementById('wizard-submit-btn');
        if (submitBtn) {
            if (wizardCurrentStep === wizardTotalSteps) {
                submitBtn.classList.remove('hidden');
            } else {
                submitBtn.classList.add('hidden');
            }
        }

        if (wizardCurrentStep === 11) {
            updateFinalSummary();
        }
    }
    window.updateWizardUI = updateWizardUI;

    function getOptionList(possibleKeys) {
        for (const key of possibleKeys) {
            if (optionsWithParents && optionsWithParents[key] && Array.isArray(optionsWithParents[key]) && optionsWithParents[key].length) {
                return optionsWithParents[key];
            }
            if (options && options[key] && Array.isArray(options[key]) && options[key].length) {
                return options[key];
            }
        }
        return [];
    }
    window.getOptionList = getOptionList;

    function getWizardDetalhes() {
        if (Array.isArray(wizardData.detalhe)) return wizardData.detalhe.filter(Boolean);
        if (wizardData.detalhe) return [wizardData.detalhe];
        return [];
    }
    window.getWizardDetalhes = getWizardDetalhes;

    function getWizardPrimaryDetalhe() {
        return getWizardDetalhes()[0] || null;
    }
    window.getWizardPrimaryDetalhe = getWizardPrimaryDetalhe;

    function hasWizardRealDetail() {
        return getWizardDetalhes().some(detail => {
            const name = (detail?.name || '').toLowerCase();
            return name && !name.includes('sem');
        });
    }
    window.hasWizardRealDetail = hasWizardRealDetail;

    function filterByParent(items, parentId) {
        if (!parentId) return items;

        const parentIds = (Array.isArray(parentId) ? parentId : [parentId]).map(id => id?.toString());

        return items.filter(item => {
            if (Array.isArray(item.parent_ids)) {
                return item.parent_ids.some(pid => parentIds.includes(pid?.toString()));
            }
            if (item.parent_id !== undefined && item.parent_id !== null) {
                return parentIds.includes(item.parent_id?.toString());
            }
            return true;
        });
    }
    window.filterByParent = filterByParent;

    function renderWizardPersonalizacao() {
        const container = document.getElementById('wizard-options-personalizacao');
        if (!container) return;

        const personalizacaoList = getOptionList(['personalizacao']);
        if (personalizacaoList.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma opção disponível.</p>';
            return;
        }

        container.innerHTML = personalizacaoList.map(item => {
            const isSelected = wizardData.personalizacao.includes(item.id.toString()) || wizardData.personalizacao.includes(item.id);
            const activeClass = isSelected ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            const key = normalizePersonalizationKey(item.slug || item.name || '');
            const style = personalizationIconMap[key] || personalizationIconMap.default;

            return `
            <label class="wizard-option-card wizard-personalization-card group cursor-pointer p-3 sm:p-3.5 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md transition-all flex flex-col items-center justify-center gap-2 ${activeClass}">
                <input type="checkbox" class="personalizacao-checkbox hidden" value="${item.id ?? ''}" ${isSelected ? 'checked' : ''} onchange="syncWizardPersonalizacaoUI()">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full ${style.bubble} flex items-center justify-center ${style.color}">
                     <i class="fa-solid ${style.icon} text-base"></i>
                </div>
                <span class="text-[11px] sm:text-xs font-bold text-center leading-tight text-gray-700 dark:text-slate-300 group-hover:text-[#7c3aed]">${item.name}</span>
            </label>
            `;
        }).join('');

        syncWizardPersonalizacaoUI();
    }
    window.renderWizardPersonalizacao = renderWizardPersonalizacao;

    function syncWizardPersonalizacaoUI() {
        const container = document.getElementById('wizard-options-personalizacao');
        if (!container) return;

        const cards = container.querySelectorAll('.wizard-option-card');
        const selectedIds = [];

        cards.forEach(card => {
            const checkbox = card.querySelector('.personalizacao-checkbox');
            if (!checkbox) return;

            const isChecked = checkbox.checked;
            card.classList.toggle('ring-2', isChecked);
            card.classList.toggle('ring-[#7c3aed]', isChecked);
            card.classList.toggle('bg-purple-50', isChecked);
            card.classList.toggle('dark:bg-purple-900/20', isChecked);
            card.classList.toggle('shadow-sm', isChecked);

            if (isChecked && checkbox.value !== '') {
                selectedIds.push(checkbox.value.toString());
            }
        });

        wizardData.personalizacao = selectedIds;
        selectedPersonalizacoes = [...wizardData.personalizacao];
        window.selectedPersonalizacoes = selectedPersonalizacoes;

        const hiddenContainer = document.getElementById('hidden-personalizacao-container');
        if (hiddenContainer) {
            hiddenContainer.innerHTML = selectedIds
                .map(pid => `<input type="hidden" name="personalizacao[]" value="${pid}">`)
                .join('');
        }
    }
    window.syncWizardPersonalizacaoUI = syncWizardPersonalizacaoUI;

    function toggleWizardPersonalizacao(element) {
        if (!element) return;
        const checkbox = element.querySelector('.personalizacao-checkbox');
        if (!checkbox) return;
        checkbox.checked = !checkbox.checked;
        syncWizardPersonalizacaoUI();
    }
    window.toggleWizardPersonalizacao = toggleWizardPersonalizacao;

    // --- Step 2: Tecidos ---
    function loadWizardTecidos() {
        const select = document.getElementById('wizard_tecido');
        if(!select) return;
        
        if (select.options.length <= 1) {
            let items = getOptionList(['tecido']);
            const tipoTecidoItems = getOptionList(['tipo_tecido']);
            const selectedIds = (selectedPersonalizacoes || []).map(id => id.toString());
            
            if (selectedIds.length > 0) {
                items = items.filter(tecido => {
                    const parentIds = Array.isArray(tecido.parent_ids) ? tecido.parent_ids.map(id => id.toString()) : [];
                    if (parentIds.length === 0) return true;
                    if (parentIds.some(pid => selectedIds.includes(pid))) return true;
                    // Se não houver no tecido, checar nos tipos de tecido vinculados
                    const hasTypeMatch = tipoTecidoItems.some(tipo => {
                        const tipoParentId = (tipo.parent_id || '').toString();
                        if (tipoParentId !== tecido.id.toString()) return false;
                        const tipoParentIds = Array.isArray(tipo.parent_ids) ? tipo.parent_ids.map(id => id.toString()) : [];
                        return tipoParentIds.some(pid => selectedIds.includes(pid));
                    });
                    return hasTypeMatch;
                });
            }

            select.innerHTML = '<option value="">Selecione o tecido</option>' + 
                items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
        }

        if (wizardData.tecido) {
            select.value = wizardData.tecido.id;
            loadWizardTiposTecido();
        }
    }
    window.loadWizardTecidos = loadWizardTecidos;
    
    function loadWizardTiposTecido() {
         const select = document.getElementById('wizard_tecido');
         const typeContainer = document.getElementById('wizard-tipo-tecido-container');
         const typeSelect = document.getElementById('wizard_tipo_tecido');
         
         if (!select || !typeContainer || !typeSelect) return;

         const fabricId = select.value;
         if(!fabricId) {
             wizardData.tecido = null;
             return;
         }
         
          const fabricName = select.options[select.selectedIndex].text;
          
          if (!wizardData.tecido || wizardData.tecido.id != fabricId) {
              wizardData.tecido = { id: fabricId, name: fabricName, price: 0 };
          }
          
          const subItems = filterByParent(getOptionList(['tipo_tecido']), fabricId);
          if(subItems.length > 0) {
              typeContainer.classList.remove('hidden');
              typeSelect.innerHTML = '<option value="">Selecione o tipo</option>' + 
                 subItems.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
              
              if (wizardData.tipo_tecido) {
                  const stillValid = subItems.find(s => s.id == wizardData.tipo_tecido.id);
                  if (stillValid) {
                      typeSelect.value = wizardData.tipo_tecido.id;
                  } else {
                      wizardData.tipo_tecido = null;
                  }
              }
          } else {
              typeContainer.classList.add('hidden');
              wizardData.tipo_tecido = null;
          }
         
         loadWizardCores(); 
    }
    window.loadWizardTiposTecido = loadWizardTiposTecido;
    
    function onWizardTipoTecidoChange() {
         const select = document.getElementById('wizard_tipo_tecido');
         if(select && select.value) {
             wizardData.tipo_tecido = { id: select.value, name: select.options[select.selectedIndex].text };
         } else {
             wizardData.tipo_tecido = null;
         }
    }
    window.onWizardTipoTecidoChange = onWizardTipoTecidoChange;

    function renderSelectableOptionCards(containerId, items, selectedId, onClickName) {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (!items.length) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma opção disponível.</p>';
            return;
        }

        container.innerHTML = items.map(item => {
            const isActive = selectedId && selectedId.toString() === item.id.toString();
            const activeClass = isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            const price = parseFloat(item.price || 0);
            return `
                <button type="button" class="wizard-option-card text-left p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                    onclick="${onClickName}('${item.id}')">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">${item.name}</div>
                    <div class="text-xs text-gray-500 mt-1">${price > 0 ? `+ R$ ${price.toFixed(2).replace('.', ',')}` : 'Sem acréscimo'}</div>
                </button>
            `;
        }).join('');
    }

    function renderWizardCortes() {
        const items = filterByParent(getOptionList(['tipo_corte', 'corte']), selectedPersonalizacoes);
        renderSelectableOptionCards('wizard-options-corte', items, wizardData.tipo_corte?.id, 'selectWizardCorte');
    }
    window.renderWizardCortes = renderWizardCortes;

    function selectWizardCorte(id) {
        const cut = getOptionList(['tipo_corte', 'corte']).find(item => item.id == id);
        if (!cut) return;
        wizardData.tipo_corte = { id: cut.id, name: cut.name, price: parseFloat(cut.price || 0) };
        wizardData.detalhe = [];
        wizardData.detail_color = null;
        wizardData.detail_colors = {};
        updateWizardUI();
        wizardNextStep();
    }
    window.selectWizardCorte = selectWizardCorte;

    function renderWizardDetalhes() {
        const container = document.getElementById('wizard-options-detalhe');
        if (!container) return;

        const items = filterByParent(getOptionList(['detalhe']), wizardData.tipo_corte?.id || null);
        if (!items.length) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhum detalhe disponível.</p>';
            return;
        }

        const selectedIds = getWizardDetalhes().map(detail => detail.id.toString());
        container.innerHTML = items.map(item => {
            const isActive = selectedIds.includes(item.id.toString());
            const activeClass = isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            const price = parseFloat(item.price || 0);
            return `
                <button type="button" class="wizard-option-card text-left p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                    onclick="toggleWizardDetalhe('${item.id}')">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">${item.name}</div>
                    <div class="text-xs text-gray-500 mt-1">${price > 0 ? `+ R$ ${price.toFixed(2).replace('.', ',')}` : 'Sem acréscimo'}</div>
                </button>
            `;
        }).join('');

        toggleDetailColorUI();
    }
    window.renderWizardDetalhes = renderWizardDetalhes;

    function toggleWizardDetalhe(id) {
        const detail = getOptionList(['detalhe']).find(item => item.id == id);
        if (!detail) return;

        const details = getWizardDetalhes();
        const detailId = detail.id.toString();
        const index = details.findIndex(item => item.id.toString() === detailId);

        if (index >= 0) {
            details.splice(index, 1);
            delete wizardData.detail_colors[detailId];
        } else {
            details.push({ id: detail.id, name: detail.name, price: parseFloat(detail.price || 0) });
        }

        wizardData.detalhe = details;
        if (!hasWizardRealDetail()) {
            wizardData.detail_color = wizardData.cor;
            wizardData.detail_colors = {};
            wizardData.individual_detail_colors = false;
        }

        renderWizardDetalhes();
        renderWizardDetailColorOptions();
    }
    window.toggleWizardDetalhe = toggleWizardDetalhe;

    function toggleDetailColorUI() {
        const hasDifferentColor = !!document.getElementById('different_detail_color_cb')?.checked && hasWizardRealDetail();
        const individualContainer = document.getElementById('individual-colors-toggle-container');
        const individualCheckbox = document.getElementById('individual_detail_colors_cb');

        if (individualContainer) individualContainer.classList.toggle('hidden', !hasDifferentColor);

        if (!hasDifferentColor) {
            wizardData.individual_detail_colors = false;
            wizardData.detail_colors = {};
            wizardData.detail_color = wizardData.cor;
            if (individualCheckbox) individualCheckbox.checked = false;
        } else if (individualCheckbox) {
            wizardData.individual_detail_colors = !!individualCheckbox.checked;
        }

        renderWizardDetailColorOptions();
    }
    window.toggleDetailColorUI = toggleDetailColorUI;

    function renderWizardDetailColorOptions() {
        const container = document.getElementById('wizard-options-cor-detalhe');
        if (!container) return;

        const colors = getOptionList(['cor']);
        if (!document.getElementById('different_detail_color_cb')?.checked || !hasWizardRealDetail()) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Os detalhes usarão a mesma cor do tecido.</p>';
            return;
        }

        if (!colors.length) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma cor disponível.</p>';
            return;
        }

        const details = getWizardDetalhes();
        if (wizardData.individual_detail_colors && details.length > 1) {
            container.innerHTML = details.map(detail => {
                const detailId = detail.id.toString();
                const selectedColorId = (wizardData.detail_colors[detailId] || '').toString();
                return `
                    <div class="col-span-full border border-gray-200 dark:border-slate-700 rounded-xl p-3">
                        <div class="text-sm font-bold text-gray-900 dark:text-white mb-3">${detail.name}</div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            ${colors.map(color => {
                                const activeClass = selectedColorId === color.id.toString() ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
                                return `
                                    <button type="button" class="wizard-option-card p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                                        onclick="selectWizardDetailColor('${detailId}', '${color.id}')">
                                        <div class="w-8 h-8 mx-auto rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.color_hex || color.hex_code || getColorHex(color.name)}"></div>
                                        <div class="mt-2 text-xs font-bold text-center text-gray-700 dark:text-slate-300">${color.name}</div>
                                    </button>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }).join('');
            return;
        }

        const selectedColorId = (wizardData.detail_color?.id || '').toString();
        container.innerHTML = colors.map(color => {
            const activeClass = selectedColorId === color.id.toString() ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            return `
                <button type="button" class="wizard-option-card p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                    onclick="selectWizardDetailColor('', '${color.id}')">
                    <div class="w-8 h-8 mx-auto rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.color_hex || color.hex_code || getColorHex(color.name)}"></div>
                    <div class="mt-2 text-xs font-bold text-center text-gray-700 dark:text-slate-300">${color.name}</div>
                </button>
            `;
        }).join('');
    }
    window.renderWizardDetailColorOptions = renderWizardDetailColorOptions;

    function selectWizardDetailColor(detailId, colorId) {
        const color = getOptionList(['cor']).find(item => item.id == colorId);
        if (!color) return;

        if (detailId) {
            wizardData.detail_colors[detailId.toString()] = color.id.toString();
        } else {
            wizardData.detail_color = { id: color.id, name: color.name, price: 0 };
        }

        renderWizardDetailColorOptions();
    }
    window.selectWizardDetailColor = selectWizardDetailColor;

    function renderWizardGolas() {
        const items = filterByParent(getOptionList(['gola']), wizardData.tipo_corte?.id || null);
        renderSelectableOptionCards('wizard-options-gola', items, wizardData.gola?.id, 'selectWizardGola');
    }
    window.renderWizardGolas = renderWizardGolas;

    function selectWizardGola(id) {
        const collar = getOptionList(['gola']).find(item => item.id == id);
        if (!collar) return;
        wizardData.gola = { id: collar.id, name: collar.name, price: parseFloat(collar.price || 0) };
        updateWizardUI();
        wizardNextStep();
    }
    window.selectWizardGola = selectWizardGola;

    function loadWizardOptionsForStep(step) {
        switch (step) {
            case 1:
                renderWizardPersonalizacao();
                break;
            case 2:
                loadWizardTecidos();
                break;
            case 3:
                loadWizardCores();
                break;
            case 4:
                renderWizardCortes();
                break;
            case 5:
                renderWizardDetalhes();
                break;
            case 6:
                renderWizardDetailColorOptions();
                break;
            case 7:
                renderWizardGolas();
                break;
            default:
                break;
        }
    }
    window.loadWizardOptionsForStep = loadWizardOptionsForStep;

    function goToWizardStep(step) {
        wizardCurrentStep = Math.min(Math.max(parseInt(step, 10) || 1, 1), wizardTotalSteps);
        window.wizardCurrentStep = wizardCurrentStep;
        updateWizardUI();
    }
    window.goToWizardStep = goToWizardStep;

    // --- Step 3: Cores ---
    function loadWizardCores() {
         const container = document.getElementById('wizard-colors-grid');
         const select = document.getElementById('wizard_cor'); 
         if(!container) return;
         
         let items = getOptionList(['cor']);
         const tecidoId = wizardData.tecido ? wizardData.tecido.id : null;
         const tipoTecidoId = wizardData.tipo_tecido ? wizardData.tipo_tecido.id : null;
         const allowedParentIds = [
             ...(selectedPersonalizacoes || []),
             ...(tecidoId ? [tecidoId] : []),
             ...(tipoTecidoId ? [tipoTecidoId] : [])
         ].map(id => id.toString());
         
         if (allowedParentIds.length > 0) {
            items = items.filter(cor => {
                if (!cor.parent_ids || cor.parent_ids.length === 0) return true;
                return cor.parent_ids.some(pid => allowedParentIds.includes(pid.toString()));
            });
         }
         
         // Filter to only Branco (white) if sublimation personalization is selected
         const personalizacaoOptions = getOptionList(['personalizacao']);
         const selectedPersonalizacoesNames = (selectedPersonalizacoes || []).map(id => {
             const p = personalizacaoOptions.find(opt => opt.id.toString() === id.toString());
             return p ? p.name.toUpperCase() : '';
         });
         
         const isSublimacaoSelected = selectedPersonalizacoesNames.some(name => 
             name.includes('SUB') || name.includes('SUBLIMACAO') || name.includes('SUBLIMAÇÃO')
         );
         
         if (isSublimacaoSelected) {
             items = items.filter(cor => 
                 cor.name.toUpperCase() === 'BRANCO' || cor.name.toUpperCase() === 'WHITE'
             );
         }
         
         container.innerHTML = items.map(color => {
            const isActive = wizardData.cor && wizardData.cor.id == color.id;
            const activeClass = isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            return `
            <div class="wizard-option-card group cursor-pointer p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md transition-all flex flex-col items-center gap-2 ${activeClass}"
                 data-id="${color.id}"
                 onclick="selectWizardColor(this)">
                <div class="w-8 h-8 rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.color_hex || color.hex_code || getColorHex(color.name)}"></div>
                <span class="text-xs font-bold text-center text-gray-700 dark:text-slate-300 group-hover:text-[#7c3aed]">${color.name}</span>
            </div>
            `;
         }).join('');
        
         if(select) {
             select.innerHTML = '<option value="">Selecione uma cor</option>' + 
                items.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
             
             if (wizardData.cor) {
                 select.value = wizardData.cor.id;
             }

             select.onchange = function() {
                 if(this.value) {
                     const mockEl = { dataset: { id: this.value } };
                     selectWizardColor(mockEl);
                 }
             };
         }
    }
    window.loadWizardCores = loadWizardCores;

    function selectWizardColor(element) {
        const id = element.dataset ? element.dataset.id : element; 
        const color = (options.cor || []).find(c => c.id == id);
        if(color) {
            wizardData.cor = { id: color.id, name: color.name };
            const select = document.getElementById('wizard_cor');
            if (select) select.value = color.id;
            wizardNextStep();
        }
    }
    window.selectWizardColor = selectWizardColor;
        
    // --- Step 8: Calculate Total ---
    function calculateWizardTotal() {
        const inputs = document.querySelectorAll('.wizard-size-input');
        let total = 0;
        let especialQty = 0;
        
        inputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            total += qty;
            if (input.dataset.size === 'Especial') especialQty = qty;
        });
        
        const totalPiecesEl = document.getElementById('wizard-total-pieces');
        if (totalPiecesEl) totalPiecesEl.textContent = total;
        
        const summaryPecasVal = document.getElementById('summary-pecas-val');
        if (summaryPecasVal) summaryPecasVal.textContent = total;

        const modelingContainer = document.getElementById('wizard-modeling-container');
        if (modelingContainer) {
            if (especialQty > 0) {
                modelingContainer.classList.remove('hidden');
            } else {
                modelingContainer.classList.add('hidden');
                const modelingCheckbox = document.getElementById('wizard_is_client_modeling');
                if (modelingCheckbox) modelingCheckbox.checked = false;
            }
        }

        return total;
    }
    window.calculateWizardTotal = calculateWizardTotal;

    function previewWizardImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('wizard-image-preview');
                const previewContainer = document.getElementById('wizard-image-preview-container');
                const placeholder = document.getElementById('wizard-image-placeholder');
                
                if (previewImg) previewImg.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
                
                wizardData.image = input.files[0];
                window.wizardData = wizardData;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    window.previewWizardImage = previewWizardImage;

    function clearWizardImage() {
        const fileInput = document.getElementById('wizard_file_input');
        if (fileInput) fileInput.value = '';
        
        const previewContainer = document.getElementById('wizard-image-preview-container');
        const placeholder = document.getElementById('wizard-image-placeholder');
        
        if (previewContainer) previewContainer.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
        
        wizardData.image = null;
        window.wizardData = wizardData;
        
        const existingImg = document.getElementById('existing_cover_image_hidden');
        if(existingImg) existingImg.value = '';
    }
    window.clearWizardImage = clearWizardImage;

    function previewSublimationImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('sub-image-preview');
                const previewContainer = document.getElementById('sub-image-preview-container');
                const placeholder = document.getElementById('sub-image-placeholder');
                
                if (previewImg) previewImg.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    window.previewSublimationImage = previewSublimationImage;

    function clearSublimationImage() {
        const fileInput = document.getElementById('sub_wizard_file_input');
        if (fileInput) fileInput.value = '';
        
        const previewContainer = document.getElementById('sub-image-preview-container');
        const placeholder = document.getElementById('sub-image-placeholder');
        
        if (previewContainer) previewContainer.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
    }
    window.clearSublimationImage = clearSublimationImage;

    function updateFinalSummary() {
        const summaryTecido = document.getElementById('summary-tecido-val');
        if (summaryTecido) summaryTecido.textContent = wizardData.tecido ? wizardData.tecido.name : '-';
        
        const summaryCor = document.getElementById('summary-cor-val');
        if (summaryCor) summaryCor.textContent = wizardData.cor ? wizardData.cor.name : '-';
        
        const summaryModelo = document.getElementById('summary-modelo-val');
        if (summaryModelo) summaryModelo.textContent = wizardData.tipo_corte ? wizardData.tipo_corte.name : '-';
        
        let unitPrice = 0;
        if(wizardData.tipo_corte) unitPrice += parseFloat(wizardData.tipo_corte.price || 0);
        unitPrice += getWizardDetalhes().reduce((sum, detail) => sum + parseFloat(detail.price || 0), 0);
        if(wizardData.gola) unitPrice += parseFloat(wizardData.gola.price || 0);
        
        const finalPriceEl = document.getElementById('wizard-final-price');
        if (finalPriceEl) finalPriceEl.textContent = 'R$ ' + unitPrice.toFixed(2).replace('.', ',');
        
        wizardData.unit_price = unitPrice;
        window.wizardData = wizardData;
    }
    window.updateFinalSummary = updateFinalSummary;
        
    function submitSewingWizard() {
        const tecidoHidden = document.getElementById('tecido_hidden');
        if (tecidoHidden) tecidoHidden.value = wizardData.tecido ? wizardData.tecido.id : '';
        
        const tipoTecidoHidden = document.getElementById('tipo_tecido_hidden');
        if (tipoTecidoHidden) tipoTecidoHidden.value = wizardData.tipo_tecido ? wizardData.tipo_tecido.id : '';
        
        const corHidden = document.getElementById('cor_hidden');
        if (corHidden) corHidden.value = wizardData.cor ? wizardData.cor.id : '';
        
        const tipoCorteHidden = document.getElementById('tipo_corte_hidden');
        if (tipoCorteHidden) tipoCorteHidden.value = wizardData.tipo_corte ? wizardData.tipo_corte.id : '';
        
        const detalheHidden = document.getElementById('detalhe_hidden');
        if (detalheHidden) {
            detalheHidden.value = '';
            detalheHidden.disabled = true;
        }
        
        const detailColorHidden = document.getElementById('detail_color_hidden');
        if (detailColorHidden) detailColorHidden.value = wizardData.detail_color ? wizardData.detail_color.id : '';
        
        const golaHidden = document.getElementById('gola_hidden');
        if (golaHidden) golaHidden.value = wizardData.gola ? wizardData.gola.id : '';
        
        const collarColorHidden = document.getElementById('collar_color_hidden');
        if (collarColorHidden) collarColorHidden.value = wizardData.collar_color ? wizardData.collar_color.id : '';
        
        const hiddenInputs = [
            { id: 'apply_surcharge_hidden', name: 'apply_surcharge', value: document.getElementById('wizard_apply_surcharge')?.checked ? '1' : '0' },
            { id: 'is_client_modeling_hidden', name: 'is_client_modeling', value: document.getElementById('wizard_is_client_modeling')?.checked ? '1' : '0' },
            { id: 'existing_cover_image_hidden', name: 'existing_cover_image', value: (typeof wizardData.image === 'string') ? wizardData.image : '' }
        ];

        hiddenInputs.forEach(meta => {
            let input = document.getElementById(meta.id);
            if(!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.id = meta.id;
                input.name = meta.name;
                const form = document.getElementById('sewing-form');
                if (form) form.appendChild(input);
            }
            input.value = meta.value;
        });

        const sizeContainer = document.getElementById('hidden-sizes-container');
        if (sizeContainer) {
            sizeContainer.innerHTML = '';
            getWizardDetalhes().forEach(detail => {
                const detailInput = document.createElement('input');
                detailInput.type = 'hidden';
                detailInput.name = 'detalhe[]';
                detailInput.value = detail.id;
                sizeContainer.appendChild(detailInput);
            });

            if (wizardData.individual_detail_colors) {
                Object.entries(wizardData.detail_colors || {}).forEach(([detailId, colorId]) => {
                    if (!colorId) return;
                    const colorInput = document.createElement('input');
                    colorInput.type = 'hidden';
                    colorInput.name = `detail_color_map[${detailId}]`;
                    colorInput.value = colorId;
                    sizeContainer.appendChild(colorInput);
                });
            }

            let totalQty = 0;
            for (const [size, qty] of Object.entries(wizardData.sizes)) {
                if(qty > 0) {
                   const input = document.createElement('input');
                   input.type = 'hidden';
                   input.name = `tamanhos[${size}]`;
                   input.value = qty;
                   sizeContainer.appendChild(input);
                   totalQty += parseInt(qty) || 0;
                }
            }
            const qtyInput = document.getElementById('quantity');
            if (qtyInput) qtyInput.value = totalQty;
        }
        
        const notes = document.getElementById('wizard_notes')?.value || '';
        const artNotesInput = document.querySelector('input[name="art_notes"]');
        if(!artNotesInput) {
             const nInput = document.createElement('input');
             nInput.type = 'hidden';
             nInput.name = 'art_notes';
             nInput.value = notes;
             if (sizeContainer) sizeContainer.appendChild(nInput);
        } else {
             artNotesInput.value = notes;
        }
        
        if (isAdmin) {
            const cost = document.getElementById('wizard_unit_cost')?.value || 0;
             const cInput = document.createElement('input');
             cInput.type = 'hidden';
             cInput.name = 'unit_cost';
             cInput.value = cost;
             if (sizeContainer) sizeContainer.appendChild(cInput);
        }

        const unitPriceInput = document.getElementById('unit_price');
        if (unitPriceInput) {
            const finalPrice = wizardData.unit_price || parseFloat((document.getElementById('wizard-final-price')?.textContent || '0').replace(/[R$\s\.]/g,'').replace(',','.')) || 0;
            unitPriceInput.value = finalPrice;
        }

        const formActionInput = document.getElementById('form-action');
        const editingItemIdInput = document.getElementById('editing-item-id');
        if (formActionInput?.value === 'update_item') {
            const validEditingItem = itemsData.some(item => item.id?.toString() === editingItemIdInput?.value?.toString());
            if (!validEditingItem) {
                formActionInput.value = 'add_item';
                if (editingItemIdInput) editingItemIdInput.value = '';
            }
        }
        
        const personalizacaoContainer = document.getElementById('hidden-personalizacao-container');
        if (personalizacaoContainer) {
            personalizacaoContainer.innerHTML = '';
            wizardData.personalizacao.forEach(pId => {
                const pInput = document.createElement('input');
                pInput.type = 'hidden';
                pInput.name = 'personalizacao[]';
                pInput.value = pId;
                personalizacaoContainer.appendChild(pInput);
            });
        }
        
        const wizardFile = document.getElementById('wizard_file_input');
        if (wizardFile) wizardFile.name = 'item_cover_image';
        
        const form = document.getElementById('sewing-form');
        if (form) {
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    }
    window.submitSewingWizard = submitSewingWizard;

    async function editItem(itemId) {
        await populateWizardFromItem(itemId, false);
    }
    window.editItem = editItem;

    async function duplicateItem(itemId) {
        await populateWizardFromItem(itemId, true);
    }
    window.duplicateItem = duplicateItem;

    async function populateWizardFromItem(itemId, isDuplicate) {
        const item = itemsData.find(i => i.id == itemId);
        if (!item) {
            alert('Item não encontrado.');
            return;
        }

        if (Object.keys(optionsWithParents).length === 0) {
             console.log('Waiting for options to load...');
             await new Promise(resolve => setTimeout(resolve, 800));
             if (Object.keys(optionsWithParents).length === 0) {
                 alert('As opções de produtos ainda estão carregando. Por favor, aguarde um segundo e tente novamente.');
                 return;
             }
        }

        wizardData = {
            tecido: null,
            tipo_tecido: null,
            cor: null,
            tipo_corte: null,
            detalhe: [],
            detail_color: null,
            detail_colors: {},
            individual_detail_colors: false,
            gola: null,
            collar_color: null,
            personalizacao: [],
            image: item.cover_image || null,
            imageUrl: item.cover_image_url || null,
            notes: item.art_notes || '',
            sizes: {},
            unit_cost: item.unit_cost || 0
        };
        window.wizardData = wizardData;

        const editingItemId = document.getElementById('editing-item-id');
        if (editingItemId) editingItemId.value = isDuplicate ? '' : itemId;
        
        const formAction = document.getElementById('form-action');
        if (formAction) formAction.value = isDuplicate ? 'add_item' : 'update_item';
        
        const formTitle = document.getElementById('form-title');
        if (formTitle) formTitle.textContent = isDuplicate ? 'Duplicar Item' : 'Editar Item';
        
        let printDesc = {};
        try {
            printDesc = typeof item.print_desc === 'string' ? JSON.parse(item.print_desc) : item.print_desc;
        } catch(e) { console.error('Erro ao parsear print_desc', e); }

        const wIds = printDesc.wizard_ids || {};
        const fabricPiece = printDesc.fabric_piece || null;
        
        const findOptionByName = (listKey, name) => {
            const list = getOptionList([listKey]);
            if (!name) return null;
            const cleanName = name.split(' - ')[0].trim().toLowerCase();
            return list.find(o => o.name && o.name.toLowerCase().includes(cleanName)) || null;
        };

        if (wIds.tecido) {
            const tissue = getOptionList(['tecido']).find(o => o.id == wIds.tecido);
            if (tissue) wizardData.tecido = { id: tissue.id, name: tissue.name, price: parseFloat(tissue.price || 0) };
        } else {
             const opt = findOptionByName('tecido', item.fabric);
             if(opt) wizardData.tecido = { id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) };
        }

        if (wIds.tipo_tecido) {
            const subTissue = getOptionList(['tipo_tecido']).find(o => o.id == wIds.tipo_tecido);
             if (subTissue) wizardData.tipo_tecido = { id: subTissue.id, name: subTissue.name, price: parseFloat(subTissue.price || 0) };
        }

        if (wIds.cor) {
            const color = getOptionList(['cor']).find(o => o.id == wIds.cor);
            if (color) wizardData.cor = { id: color.id, name: color.name, price: 0 };
        } else {
             const opt = findOptionByName('cor', item.color);
             if(opt) wizardData.cor = { id: opt.id, name: opt.name, price: 0 };
        }

        if (wIds.tipo_corte) {
            const cut = getOptionList(['tipo_corte', 'corte']).find(o => o.id == wIds.tipo_corte);
            if (cut) wizardData.tipo_corte = { id: cut.id, name: cut.name, price: parseFloat(cut.price || 0) };
        } else {
             const opt = findOptionByName('tipo_corte', item.model);
             if(opt) wizardData.tipo_corte = { id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) };
        }

        if (Array.isArray(wIds.detalhe)) {
            wizardData.detalhe = wIds.detalhe
                .map(detailId => getOptionList(['detalhe']).find(o => o.id == detailId))
                .filter(Boolean)
                .map(detail => ({ id: detail.id, name: detail.name, price: parseFloat(detail.price || 0) }));
        } else if (wIds.detalhe) {
            const detail = getOptionList(['detalhe']).find(o => o.id == wIds.detalhe);
            if (detail) wizardData.detalhe = [{ id: detail.id, name: detail.name, price: parseFloat(detail.price || 0) }];
        } else if (item.detail) {
            wizardData.detalhe = item.detail
                .split(',')
                .map(name => findOptionByName('detalhe', name.trim()))
                .filter(Boolean)
                .map(opt => ({ id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) }));
        }

        if (wIds.detail_color) {
            const dc = getOptionList(['cor']).find(o => o.id == wIds.detail_color);
            if (dc) wizardData.detail_color = { id: dc.id, name: dc.name, price: 0 };
        } else {
             const opt = findOptionByName('cor', item.detail_color);
             if(opt) wizardData.detail_color = { id: opt.id, name: opt.name, price: 0 };
        }

        if (wIds.detail_color_map && typeof wIds.detail_color_map === 'object') {
            wizardData.detail_colors = Object.fromEntries(
                Object.entries(wIds.detail_color_map).map(([detailId, colorId]) => [detailId.toString(), colorId?.toString()])
            );
        }
        wizardData.individual_detail_colors = !!wIds.individual_detail_colors;

        if (wIds.gola) {
            const collar = getOptionList(['gola']).find(o => o.id == wIds.gola);
            if (collar) wizardData.gola = { id: collar.id, name: collar.name, price: parseFloat(collar.price || 0) };
        } else {
             const opt = findOptionByName('gola', item.collar);
             if(opt) wizardData.gola = { id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) };
        }

        if (wIds.collar_color) {
            const cc = getOptionList(['cor']).find(o => o.id == wIds.collar_color);
            if (cc) wizardData.collar_color = { id: cc.id, name: cc.name, price: 0 };
        } else {
             const opt = findOptionByName('cor', item.collar_color);
             if(opt) wizardData.collar_color = { id: opt.id, name: opt.name, price: 0 };
        }

        if (Array.isArray(wIds.personalizacao)) {
            wizardData.personalizacao = wIds.personalizacao.map(id => id.toString());
        } else {
            if (item.print_type) {
                const names = item.print_type.split(',').map(n => n.trim().toLowerCase());
                const allP = getOptionList(['personalizacao']);
                wizardData.personalizacao = allP
                    .filter(p => p.name && names.includes(p.name.toLowerCase()))
                    .map(p => p.id.toString());
            }
        }

        let itemSizes = {};
        try {
            itemSizes = typeof item.sizes === 'string' ? JSON.parse(item.sizes) : item.sizes;
        } catch(e) {}
        wizardData.sizes = itemSizes || {};

        const diffDetailCb = document.getElementById('different_detail_color_cb');
        if (diffDetailCb) {
            diffDetailCb.checked = !!wizardData.individual_detail_colors || (wizardData.detail_color && wizardData.cor && wizardData.detail_color.id != wizardData.cor.id);
        }
        const individualDetailColorsCb = document.getElementById('individual_detail_colors_cb');
        if (individualDetailColorsCb) {
            individualDetailColorsCb.checked = !!wizardData.individual_detail_colors;
        }
        
        const diffCollarCb = document.getElementById('different_collar_color_cb');
        if (diffCollarCb) {
            diffCollarCb.checked = (wizardData.collar_color && wizardData.cor && wizardData.collar_color.id != wizardData.cor.id);
        }

        const wizardNotes = document.getElementById('wizard_notes');
        if (wizardNotes) wizardNotes.value = wizardData.notes;
        
        const wizardUnitCost = document.getElementById('wizard_unit_cost');
        if (wizardUnitCost) wizardUnitCost.value = wizardData.unit_cost;
        
        const applySurcharge = document.getElementById('wizard_apply_surcharge');
        if (applySurcharge) applySurcharge.checked = !!printDesc.apply_surcharge;
        
        const isClientModeling = document.getElementById('wizard_is_client_modeling');
        if (isClientModeling) isClientModeling.checked = !!printDesc.is_client_modeling;

        const fabricPieceSelect = document.getElementById('fabric_piece_id');
        const fabricPieceQty = document.getElementById('fabric_piece_quantity');
        const fabricPieceUnit = document.getElementById('fabric_piece_unit');
        if (fabricPiece && fabricPieceSelect && fabricPieceQty) {
            fabricPieceSelect.value = fabricPiece.id || '';
            fabricPieceQty.value = fabricPiece.quantity || '';
            if (fabricPieceUnit) fabricPieceUnit.value = fabricPiece.unit || '';
            updateFabricPieceSelection();
        } else {
            resetFabricPieceSelection();
        }

        document.querySelectorAll('.wizard-size-input').forEach(input => {
            const s = input.dataset.size;
            input.value = wizardData.sizes[s] || 0;
        });
        
        calculateWizardTotal();
        
        const previewImg = document.getElementById('wizard-image-preview');
        const previewContainer = document.getElementById('wizard-image-preview-container');
        const placeholder = document.getElementById('wizard-image-placeholder');
        
        if (wizardData.imageUrl) {
            if (previewImg) previewImg.src = wizardData.imageUrl;
            if (previewContainer) previewContainer.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        } else if (wizardData.image && typeof wizardData.image === 'string') {
            if (previewImg) previewImg.src = storageUrl + wizardData.image;
            if (previewContainer) previewContainer.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        } else {
            clearWizardImage();
        }

        selectedPersonalizacoes = [...wizardData.personalizacao];
        window.selectedPersonalizacoes = selectedPersonalizacoes;

        wizardCurrentStep = isDuplicate ? 4 : 1; 
        window.wizardCurrentStep = wizardCurrentStep;
        openSewingWizard();
    }
    window.populateWizardFromItem = populateWizardFromItem;
        
    function previewCoverImage(input) {
        const previewContainer = document.getElementById('cover-image-preview-container');
        const previewImage = document.getElementById('cover-image-preview');
        const fileNameDisplay = document.getElementById('file-name-display');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImage) previewImage.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
            if (fileNameDisplay) {
                fileNameDisplay.textContent = 'Arquivo selecionado: ' + input.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            }
        }
    }
    window.previewCoverImage = previewCoverImage;

    function cancelEdit() {
        const editingItemId = document.getElementById('editing-item-id');
        const formAction = document.getElementById('form-action');
        const formTitle = document.getElementById('form-title');
        const submitButton = document.getElementById('submit-button');
        const sewingForm = document.getElementById('sewing-form');
        const coverPreviewContainer = document.getElementById('cover-image-preview-container');
        const coverPreview = document.getElementById('cover-image-preview');
        const fileNameDisplay = document.getElementById('file-name-display');
        
        if (editingItemId) editingItemId.value = '';
        if (formAction) formAction.value = 'add_item';
        if (formTitle) formTitle.textContent = 'Adicionar Novo Item';
        if (submitButton) submitButton.innerHTML = 'Adicionar Item';
        if (sewingForm) sewingForm.reset();
        
        if (coverPreviewContainer) coverPreviewContainer.classList.add('hidden');
        if (coverPreview) coverPreview.src = '';
        if (fileNameDisplay) {
            fileNameDisplay.classList.add('hidden');
            fileNameDisplay.textContent = '';
        }
        
        document.querySelectorAll('.personalizacao-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        wizardData = {
            tecido: null, tipo_tecido: null, cor: null, tipo_corte: null,
            detalhe: [], detail_color: null, detail_colors: {}, individual_detail_colors: false,
            gola: null, collar_color: null, personalizacao: [], image: null, imageUrl: null, notes: '', sizes: {}, unit_cost: 0
        };
        window.wizardData = wizardData;
        selectedPersonalizacoes = [];
        window.selectedPersonalizacoes = selectedPersonalizacoes;
        wizardCurrentStep = 1;
        window.wizardCurrentStep = wizardCurrentStep;
        resetFabricPieceSelection();
        closeSewingWizard();
    }
    window.cancelEdit = cancelEdit;

    async function togglePin(itemId) {
        try {
            const response = await fetch(`/order-items/${itemId}/toggle-pin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await response.json();
            if (data.success) {
                // Update sidebar HTML
                const sidebar = document.getElementById('items-sidebar-container');
                if (sidebar && data.html) {
                    sidebar.innerHTML = data.html;
                }
                
                // Update itemsData global
                if (data.items_data) {
                    itemsData = data.items_data;
                    window.itemsData = itemsData;
                }
                
                if (window.showToast) window.showToast(data.message || 'Status alterado!', 'success');
            } else {
                alert('Erro ao alterar status do item: ' + (data.message || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao processar solicitação');
        }
    }
    window.togglePin = togglePin;

    function openSublimationModal() {
        const modal = document.getElementById('sublimation-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            const form = document.getElementById('sublimation-form');
            if (form) form.reset();
            const totalPecas = document.getElementById('sub-total-pecas');
            if (totalPecas) totalPecas.textContent = '0';
            const totalPrice = document.getElementById('sub-total-price');
            if (totalPrice) totalPrice.textContent = 'R$ 0,00';
            const qtyInput = document.getElementById('sub_quantity');
            if (qtyInput) qtyInput.value = 0;
        }
    }
    window.openSublimationModal = openSublimationModal;
    
    function closeSublimationModal() {
        const modal = document.getElementById('sublimation-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    window.closeSublimationModal = closeSublimationModal;
    
    async function loadSublimationAddons() {
        const typeSlug = document.getElementById('sublimation_type')?.value;
        const container = document.getElementById('sublimation-addons-container');
        if (!typeSlug || !container) {
            if (container) container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>';
            return;
        }
        
        if (sublimationAddonsCache[typeSlug]) {
            renderSublimationAddons(sublimationAddonsCache[typeSlug]);
            return;
        }
        
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Carregando...</p>';
        
        try {
            const response = await fetch(`/api/sublimation-total/addons/${typeSlug}`);
            const data = await response.json();
            if (data.success) {
                sublimationAddonsCache[typeSlug] = data.data;
                renderSublimationAddons(data.data);
                calculateSublimationPrice();
            } else {
                container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Nenhum adicional</p>';
            }
        } catch (error) {
            console.error('Erro ao carregar adicionais:', error);
            container.innerHTML = '<p class="text-sm text-red-500 col-span-full">Erro ao carregar</p>';
        }
    }
    window.loadSublimationAddons = loadSublimationAddons;
    
    function renderSublimationAddons(addons) {
        const container = document.getElementById('sublimation-addons-container');
        if (!container) return;
        
        if (!addons || addons.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Nenhum adicional disponível</p>';
            return;
        }
        
        container.innerHTML = addons.map(addon => `
            <label class="flex items-center px-3 py-2 border rounded-lg cursor-pointer transition-all border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:border-purple-300 dark:hover:border-purple-600">
                <input type="checkbox" name="sublimation_addons[]" value="${addon.id}" data-price="${addon.price}" onchange="calculateSublimationPrice()" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">${addon.name}</span>
                ${addon.price != 0 ? `<span class="ml-auto text-xs ${addon.price >= 0 ? 'text-green-600' : 'text-red-600'}">${addon.price >= 0 ? '+' : ''}R$ ${parseFloat(addon.price).toFixed(2).replace('.', ',')}</span>` : ''}
            </label>
        `).join('');
    }
    window.renderSublimationAddons = renderSublimationAddons;
    
    function calculateSublimationTotal() {
        const inputs = document.querySelectorAll('.sub-size-input');
        let total = 0;
        inputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        const totalPecas = document.getElementById('sub-total-pecas');
        if (totalPecas) totalPecas.textContent = total;
        const qtyInput = document.getElementById('sub_quantity');
        if (qtyInput) qtyInput.value = total;
        calculateSublimationPrice();
    }
    window.calculateSublimationTotal = calculateSublimationTotal;
    
    async function calculateSublimationPrice() {
        const typeSlug = document.getElementById('sublimation_type')?.value;
        const quantity = parseInt(document.getElementById('sub_quantity')?.value) || 0;
        
        if (!typeSlug || quantity === 0) {
            updateSublimationPreview();
            return;
        }
        
        try {
            const response = await fetch(`/api/sublimation-total/price/${typeSlug}/${quantity}`);
            const data = await response.json();
            if (data.success) {
                let basePrice = parseFloat(data.price);
                const selectedAddons = document.querySelectorAll('input[name="sublimation_addons[]"]:checked');
                selectedAddons.forEach(addon => {
                    basePrice += parseFloat(addon.dataset.price);
                });
                const unitPriceInput = document.getElementById('sub_unit_price');
                if (unitPriceInput) unitPriceInput.value = basePrice.toFixed(2);
                updateSublimationPreview();
            }
        } catch (error) {
            console.error('Erro ao buscar preço:', error);
        }
    }
    window.calculateSublimationPrice = calculateSublimationPrice;
    
    function updateSublimationPreview() {
        const unitPrice = parseFloat(document.getElementById('sub_unit_price')?.value) || 0;
        const quantity = parseInt(document.getElementById('sub_quantity')?.value) || 0;
        const total = unitPrice * quantity;
        const totalPriceEl = document.getElementById('sub-total-price');
        if (totalPriceEl) totalPriceEl.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    }
    window.updateSublimationPreview = updateSublimationPreview;

    function initSublimationForm() {
        const sublimationForm = document.getElementById('sublimation-form');
        if (sublimationForm) {
            sublimationForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const quantity = parseInt(document.getElementById('sub_quantity')?.value) || 0;
                if (quantity === 0) {
                    alert('Adicione pelo menos uma peça nos tamanhos.');
                    return;
                }
                const artName = document.getElementById('sub_art_name')?.value.trim();
                if (!artName) {
                    alert('Informe o nome da arte.');
                    return;
                }
                const btn = document.getElementById('submit-sublimation-btn');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Adicionando...';
                try {
                    const formData = new FormData(sublimationForm);
                    const actionUrl = sublimationForm.getAttribute('action');
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        closeSublimationModal();
                        window.location.reload();
                    } else {
                        alert(data.message || 'Erro ao adicionar item.');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao processar solicitação.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        }
    }
    window.initSublimationForm = initSublimationForm;

    // ========================
    // SUBLIMATION WIZARD STEP (Integrated into main wizard)
    // ========================
    
    let subWizardAddons = [];
    let subWizardUnitPrice = 0;
    window.subWizardAddons = subWizardAddons;
    window.subWizardUnitPrice = subWizardUnitPrice;
    
    // Check if any selected personalization is SUB.TOTAL
    function isSublimationTotalSelected() {
        const personalizacaoOptions = getOptionList(['personalizacao']);
        const selectedIds = selectedPersonalizacoes || [];
        
        for (const id of selectedIds) {
            const p = personalizacaoOptions.find(opt => opt.id.toString() === id.toString());
            if (p) {
                if (!p.name) continue;
                const name = p.name.toUpperCase();
                if (name.includes('SUB') && (name.includes('TOTAL') || name.includes('SUBLIM'))) {
                    return true;
                }
            }
        }
        return false;
    }
    window.isSublimationTotalSelected = isSublimationTotalSelected;
    
    // Load addons for selected sublimation type
    async function loadSubWizardAddons() {
        const typeSlug = document.getElementById('sub_wizard_type')?.value;
        const container = document.getElementById('sub-wizard-addons');
        if (!container) return;
        
        if (!typeSlug) {
            container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>';
            subWizardAddons = [];
            return;
        }
        
        container.innerHTML = '<p class="text-sm text-gray-500 col-span-full"><i class="fa-solid fa-spinner animate-spin mr-2"></i>Carregando...</p>';
        
        try {
            const response = await fetch(`/api/sublimation-total/addons/${typeSlug}`);
            const data = await response.json();
            
            if (data.success && data.addons && data.addons.length > 0) {
                subWizardAddons = data.addons;
                container.innerHTML = data.addons.map(addon => {
                    const priceText = addon.price > 0 ? `+R$${parseFloat(addon.price).toFixed(2).replace('.', ',')}` : 
                                      addon.price < 0 ? `-R$${Math.abs(parseFloat(addon.price)).toFixed(2).replace('.', ',')}` : '';
                    return `
                    <label class="flex items-center gap-2 p-2 bg-white dark:bg-slate-700 rounded-lg border border-gray-200 dark:border-slate-600 cursor-pointer hover:border-purple-400 transition-colors">
                        <input type="checkbox" name="sub_addons[]" value="${addon.id}" onchange="calculateSubWizardTotal()" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300 flex-1">${addon.name}</span>
                        ${priceText ? `<span class="text-xs font-bold ${addon.price > 0 ? 'text-green-600' : 'text-red-500'}">${priceText}</span>` : ''}
                    </label>
                    `;
                }).join('');
            } else {
                container.innerHTML = '<p class="text-sm text-gray-500 col-span-full">Nenhum adicional disponível</p>';
                subWizardAddons = [];
            }
        } catch (error) {
            console.error('Error loading addons:', error);
            container.innerHTML = '<p class="text-sm text-red-500 col-span-full">Erro ao carregar adicionais</p>';
        }
        
        calculateSubWizardTotal();
    }
    window.loadSubWizardAddons = loadSubWizardAddons;
    
    // Calculate total for sublimation wizard
    async function calculateSubWizardTotal() {
        const typeSlug = document.getElementById('sub_wizard_type')?.value;
        const sizeInputs = document.querySelectorAll('.sub-wizard-size');
        
        let totalQty = 0;
        sizeInputs.forEach(input => {
            totalQty += parseInt(input.value) || 0;
        });
        
        // Update quantity display
        const qtyEl = document.getElementById('sub-wizard-total-qty');
        if (qtyEl) qtyEl.textContent = totalQty;
        
        // Get selected addons price adjustment
        let addonsAdjustment = 0;
        const selectedAddonCheckboxes = document.querySelectorAll('#sub-wizard-addons input[type="checkbox"]:checked');
        selectedAddonCheckboxes.forEach(cb => {
            const addonId = parseInt(cb.value);
            const addon = subWizardAddons.find(a => a.id === addonId);
            if (addon) addonsAdjustment += parseFloat(addon.price) || 0;
        });
        
        // Fetch base price from API
        let baseUnitPrice = 0;
        if (typeSlug && totalQty > 0) {
            try {
                const response = await fetch(`/api/sublimation-total/price/${typeSlug}/${totalQty}`);
                const data = await response.json();
                if (data.success && data.price) {
                    baseUnitPrice = parseFloat(data.price);
                }
            } catch (error) {
                console.error('Error fetching price:', error);
            }
        }
        
        // Calculate final unit price with addons
        subWizardUnitPrice = baseUnitPrice + addonsAdjustment;
        const totalPrice = subWizardUnitPrice * totalQty;
        
        // Update displays
        const unitPriceEl = document.getElementById('sub-wizard-unit-price');
        if (unitPriceEl) unitPriceEl.textContent = 'R$ ' + subWizardUnitPrice.toFixed(2).replace('.', ',');
        
        const totalPriceEl = document.getElementById('sub-wizard-total-price');
        if (totalPriceEl) totalPriceEl.textContent = 'R$ ' + totalPrice.toFixed(2).replace('.', ',');
        
        window.subWizardUnitPrice = subWizardUnitPrice;
    }
    window.calculateSubWizardTotal = calculateSubWizardTotal;
    
    // Submit sublimation item from wizard step
    async function submitSubWizardItem() {
        const typeSlug = document.getElementById('sub_wizard_type')?.value;
        const artName = document.getElementById('sub_wizard_art_name')?.value?.trim();
        
        if (!typeSlug) {
            alert('Selecione o tipo de produto.');
            return false;
        }
        
        if (!artName) {
            alert('Informe o nome da arte.');
            return false;
        }
        
        // Get sizes
        const sizeInputs = document.querySelectorAll('.sub-wizard-size');
        const tamanhos = {};
        let totalQty = 0;
        sizeInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                tamanhos[input.dataset.size] = qty;
                totalQty += qty;
            }
        });
        
        if (totalQty === 0) {
            alert('Adicione pelo menos uma peça.');
            return false;
        }
        
        // Get selected addons
        const selectedAddons = [];
        document.querySelectorAll('#sub-wizard-addons input[type="checkbox"]:checked').forEach(cb => {
            selectedAddons.push(parseInt(cb.value));
        });
        
        // Build form data
        const formData = new FormData();
        formData.append('action', 'add_sublimation_item');
        formData.append('sublimation_type', typeSlug);
        formData.append('art_name', artName);
        formData.append('tamanhos', JSON.stringify(tamanhos));
        formData.append('quantity', totalQty);
        formData.append('unit_price', subWizardUnitPrice);
        formData.append('art_notes', document.getElementById('sub_wizard_notes')?.value || '');
        formData.append('_token', document.querySelector('input[name="_token"]')?.value);
        
        selectedAddons.forEach(id => formData.append('sublimation_addons[]', id));
        
        const coverFile = document.getElementById('sub_wizard_cover')?.files?.[0];
        if (coverFile) formData.append('item_cover_image', coverFile);
        
        const corelFile = document.getElementById('sub_wizard_corel')?.files?.[0];
        if (corelFile) formData.append('corel_file', corelFile);
        
        try {
            const response = await fetch('{{ isset($editData) ? route("orders.edit.sewing") : route("orders.wizard.sewing") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                closeSewingWizard();
                window.location.reload();
                return true;
            } else {
                alert(data.message || 'Erro ao adicionar item.');
                return false;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao processar solicitação.');
            return false;
        }
    }
    window.submitSubWizardItem = submitSubWizardItem;
    
    // Reset sublimation wizard step
    function resetSubWizardStep() {
        const typeSelect = document.getElementById('sub_wizard_type');
        if (typeSelect) typeSelect.value = '';
        
        const artInput = document.getElementById('sub_wizard_art_name');
        if (artInput) artInput.value = '';
        
        document.querySelectorAll('.sub-wizard-size').forEach(input => input.value = 0);
        
        const notesInput = document.getElementById('sub_wizard_notes');
        if (notesInput) notesInput.value = '';
        
        const addonsContainer = document.getElementById('sub-wizard-addons');
        if (addonsContainer) addonsContainer.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>';
        
        subWizardAddons = [];
        subWizardUnitPrice = 0;
        
        document.getElementById('sub-wizard-total-qty').textContent = '0';
        document.getElementById('sub-wizard-unit-price').textContent = 'R$ 0,00';
        document.getElementById('sub-wizard-total-price').textContent = 'R$ 0,00';
    }
    window.resetSubWizardStep = resetSubWizardStep;

    // ========================
    // FULLPAGE SUBLIMATION FORM (Outside modal, in main content area)
    // ========================
    
    let fullpageSubAddons = [];
    let fullpageSubUnitPrice = 0;
    let fullpageSubBaseUnitPrice = 0;
    let fullpageSubAddonsAdjustment = 0;
    let fullpageSubFabricSurcharge = 0;
    let fullpageSubCurrentStep = 1;
    
    // Show fullpage sublimation form
    function showSubFullpageForm() {
        const normalTrigger = document.getElementById('normal-wizard-trigger');
        const fullpageForm = document.getElementById('sublimation-fullpage-form');
        const sewingWizardModal = document.getElementById('sewing-wizard-modal');
        
        if (normalTrigger) normalTrigger.classList.add('hidden');
        if (fullpageForm) fullpageForm.classList.remove('hidden');
        if (sewingWizardModal) sewingWizardModal.classList.add('hidden');
        
        document.body.style.overflow = 'auto';
        setFullpageSubStep(1);
        calculateFullpageSubTotal();
    }
    window.showSubFullpageForm = showSubFullpageForm;
    
    // Hide fullpage sublimation form
    function hideSubFullpageForm() {
        const normalTrigger = document.getElementById('normal-wizard-trigger');
        const fullpageForm = document.getElementById('sublimation-fullpage-form');
        
        if (normalTrigger) normalTrigger.classList.remove('hidden');
        if (fullpageForm) fullpageForm.classList.add('hidden');
        
        // Reset form
        resetFullpageSubForm();
    }
    window.hideSubFullpageForm = hideSubFullpageForm;
    function getFullpageSubTotalQty() {
        let totalQty = 0;
        document.querySelectorAll('.fullpage-sub-size').forEach(input => {
            totalQty += parseInt(input.value, 10) || 0;
        });
        return totalQty;
    }

    function validateFullpageSubStep(step) {
        if (step === 1) {
            const typeSlug = document.getElementById('fullpage_sub_type')?.value || '';
            const artName = document.getElementById('fullpage_art_name')?.value?.trim() || '';
            const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
            const modelType = document.getElementById('fullpage_sub_model')?.value || '';
            const fabricCustom = document.getElementById('fullpage_sub_fabric_custom')?.value?.trim() || '';

            if (!typeSlug) {
                alert('Selecione o tipo de produto.');
                return false;
            }
            if (!artName) {
                alert('Informe o nome da arte.');
                return false;
            }
            if (!fabricType) {
                alert('Selecione o tecido.');
                return false;
            }
            if (fabricType === 'OUTRO' && !fabricCustom) {
                alert('Informe o nome do tecido especial.');
                return false;
            }
            if (!modelType) {
                alert('Selecione o modelo.');
                return false;
            }
        }

        if (step === 2) {
            if (getFullpageSubTotalQty() <= 0) {
                alert('Informe ao menos uma peça nos tamanhos.');
                return false;
            }

            const hasAddonColors = !!document.getElementById('fullpage_has_addon_colors')?.checked;
            if (hasAddonColors) {
                const selectedAddons = getSelectedFullpageAddonIds();
                if (!selectedAddons.length) {
                    alert('Marque ao menos um adicional para informar cor.');
                    return false;
                }

                for (const addonId of selectedAddons) {
                    const input = document.querySelector(`#fullpage-addon-color-fields input[data-addon-color-id="${addonId}"]`);
                    if (!input || !input.value.trim()) {
                        alert('Preencha a cor de todos os adicionais selecionados.');
                        return false;
                    }
                }
            }
        }

        return true;
    }

    function setFullpageSubStep(step) {
        const safeStep = Math.min(3, Math.max(1, parseInt(step, 10) || 1));
        fullpageSubCurrentStep = safeStep;

        const titles = {
            1: 'Etapa 1 de 3 · Configuração',
            2: 'Etapa 2 de 3 · Produção',
            3: 'Etapa 3 de 3 · Revisão'
        };
        const subtitle = document.getElementById('fullpage-step-subtitle');
        if (subtitle) subtitle.textContent = titles[safeStep];

        document.querySelectorAll('#sublimation-fullpage-form .fullpage-sub-step').forEach((section, idx) => {
            section.classList.toggle('hidden', (idx + 1) !== safeStep);
        });

        document.querySelectorAll('#fullpage-step-indicator .fullpage-step-chip').forEach(chip => {
            const chipStep = parseInt(chip.dataset.step, 10);
            const bubble = chip.querySelector('span');

            chip.classList.remove(
                'border-[#7c3aed]/40', 'bg-[#7c3aed]/10', 'text-[#7c3aed]',
                'border-purple-200', 'dark:border-purple-700', 'text-purple-600', 'dark:text-purple-300',
                'border-gray-200', 'dark:border-slate-700', 'text-gray-500', 'dark:text-slate-400'
            );
            if (bubble) {
                bubble.classList.remove(
                    'bg-[#7c3aed]', 'text-white',
                    'bg-purple-100', 'dark:bg-purple-900/40', 'text-purple-700', 'dark:text-purple-300',
                    'bg-gray-200', 'dark:bg-slate-700'
                );
            }

            if (chipStep === safeStep) {
                chip.classList.add('border-[#7c3aed]/40', 'bg-[#7c3aed]/10', 'text-[#7c3aed]');
                if (bubble) bubble.classList.add('bg-[#7c3aed]', 'text-white');
            } else if (chipStep < safeStep) {
                chip.classList.add('border-purple-200', 'dark:border-purple-700', 'text-purple-600', 'dark:text-purple-300');
                if (bubble) bubble.classList.add('bg-purple-100', 'dark:bg-purple-900/40', 'text-purple-700', 'dark:text-purple-300');
            } else {
                chip.classList.add('border-gray-200', 'dark:border-slate-700', 'text-gray-500', 'dark:text-slate-400');
                if (bubble) bubble.classList.add('bg-gray-200', 'dark:bg-slate-700');
            }
        });

        const prevBtn = document.getElementById('fullpage-step-prev');
        const nextBtn = document.getElementById('fullpage-step-next');
        const submitBtn = document.getElementById('fullpage-step-submit');

        if (prevBtn) prevBtn.classList.toggle('hidden', safeStep === 1);
        if (nextBtn) nextBtn.classList.toggle('hidden', safeStep === 3);
        if (submitBtn) submitBtn.classList.toggle('hidden', safeStep !== 3);
    }
    window.setFullpageSubStep = setFullpageSubStep;

    function goToNextFullpageSubStep() {
        if (!validateFullpageSubStep(fullpageSubCurrentStep)) return;
        setFullpageSubStep(fullpageSubCurrentStep + 1);
    }
    window.goToNextFullpageSubStep = goToNextFullpageSubStep;

    function goToPrevFullpageSubStep() {
        setFullpageSubStep(fullpageSubCurrentStep - 1);
    }
    window.goToPrevFullpageSubStep = goToPrevFullpageSubStep;
    // Legacy SUB.TOTAL block replaced by overrides below.
    let isSubmittingFullpage = false;

    // --- SUB.TOTAL fullpage overrides ---
    function toggleFullpageSpecialFabric() {
        const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
        const wrapper = document.getElementById('fullpage-special-fabric-fields');
        const customInput = document.getElementById('fullpage_sub_fabric_custom');
        const surchargeInput = document.getElementById('fullpage_sub_fabric_surcharge');
        if (!wrapper || !customInput || !surchargeInput) return;

        if (fabricType === 'OUTRO') {
            wrapper.classList.remove('hidden');
            customInput.required = true;
            if ((parseFloat(surchargeInput.value || '0') || 0) <= 0) surchargeInput.value = '10.00';
        } else {
            wrapper.classList.add('hidden');
            customInput.required = false;
            customInput.value = '';
            surchargeInput.value = '0.00';
        }
    }
    window.toggleFullpageSpecialFabric = toggleFullpageSpecialFabric;

    function getSelectedFullpageAddonIds() {
        const ids = [];
        document.querySelectorAll('#fullpage-sub-addons input[type="checkbox"]:checked').forEach(cb => {
            ids.push(parseInt(cb.value, 10));
        });
        return ids;
    }

    function getFullpageFabricSurcharge() {
        const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
        if (fabricType !== 'OUTRO') return 0;
        const inputValue = parseFloat(document.getElementById('fullpage_sub_fabric_surcharge')?.value || '0');
        return Number.isFinite(inputValue) ? Math.max(0, inputValue) : 0;
    }

    function renderFullpageAddonColorFields() {
        const wrapper = document.getElementById('fullpage-addon-color-fields');
        const hasColors = document.getElementById('fullpage_has_addon_colors')?.checked;
        if (!wrapper) return;

        if (!hasColors) {
            wrapper.classList.add('hidden');
            wrapper.innerHTML = '';
            return;
        }

        const currentValues = {};
        wrapper.querySelectorAll('input[data-addon-color-id]').forEach(input => {
            currentValues[input.dataset.addonColorId] = input.value;
        });

        const selectedIds = getSelectedFullpageAddonIds();
        if (!selectedIds.length) {
            wrapper.classList.remove('hidden');
            wrapper.innerHTML = '<p class="text-xs text-gray-500 dark:text-slate-400">Selecione pelo menos um adicional para informar a cor.</p>';
            return;
        }

        wrapper.innerHTML = '';
        selectedIds.forEach(addonId => {
            const addon = fullpageSubAddons.find(a => parseInt(a.id, 10) === addonId);
            if (!addon) return;

            const row = document.createElement('div');

            const label = document.createElement('label');
            label.className = 'block text-xs font-medium text-gray-600 dark:text-slate-300 mb-1';
            label.textContent = addon.name;

            const input = document.createElement('input');
            input.type = 'text';
            input.dataset.addonColorId = addon.id;
            input.placeholder = `Cor para ${addon.name}`;
            input.className = 'w-full px-2.5 py-2 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-1 focus:ring-purple-500';
            input.value = currentValues[String(addon.id)] || '';

            row.appendChild(label);
            row.appendChild(input);
            wrapper.appendChild(row);
        });

        wrapper.classList.remove('hidden');
    }
    window.renderFullpageAddonColorFields = renderFullpageAddonColorFields;

    async function loadFullpageSubAddons() {
        const typeSlug = document.getElementById('fullpage_sub_type')?.value;
        const container = document.getElementById('fullpage-sub-addons');
        if (!container) return;

        if (!typeSlug) {
            container.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4">Selecione um tipo primeiro</p>';
            fullpageSubAddons = [];
            renderFullpageAddonColorFields();
            calculateFullpageSubTotal();
            return;
        }

        container.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4"><i class="fa-solid fa-spinner animate-spin mr-2"></i>Carregando...</p>';

        try {
            const response = await fetch(`/api/sublimation-total/addons/${typeSlug}`);
            const payload = await response.json();
            const addons = Array.isArray(payload?.addons) ? payload.addons : (Array.isArray(payload?.data) ? payload.data : []);

            if (payload?.success && addons.length > 0) {
                fullpageSubAddons = addons;
                container.innerHTML = addons.map(addon => {
                    const addonPrice = parseFloat(addon.price) || 0;
                    const priceText = addonPrice > 0
                        ? `+R$${addonPrice.toFixed(2).replace('.', ',')}`
                        : addonPrice < 0
                            ? `-R$${Math.abs(addonPrice).toFixed(2).replace('.', ',')}`
                            : '';
                    return `
                    <label class="flex items-center gap-2 p-2.5 bg-gray-50 dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer hover:border-purple-400 transition-colors">
                        <input type="checkbox" name="fullpage_addons[]" value="${addon.id}" onchange="calculateFullpageSubTotal(); renderFullpageAddonColorFields();" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300 flex-1">${addon.name}</span>
                        ${priceText ? `<span class="text-xs font-bold ${addonPrice > 0 ? 'text-green-600' : 'text-red-500'}">${priceText}</span>` : ''}
                    </label>
                    `;
                }).join('');
            } else {
                fullpageSubAddons = [];
                container.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4">Nenhum adicional disponivel</p>';
            }
        } catch (error) {
            console.error('Error loading addons:', error);
            fullpageSubAddons = [];
            container.innerHTML = '<p class="text-sm text-red-500 col-span-full text-center py-4">Erro ao carregar adicionais</p>';
        }

        renderFullpageAddonColorFields();
        calculateFullpageSubTotal();
    }
    window.loadFullpageSubAddons = loadFullpageSubAddons;

    async function calculateFullpageSubTotal() {
        const typeSlug = document.getElementById('fullpage_sub_type')?.value;
        const sizeInputs = document.querySelectorAll('.fullpage-sub-size');
        let totalQty = 0;

        sizeInputs.forEach(input => {
            totalQty += parseInt(input.value, 10) || 0;
        });

        const qtyEl = document.getElementById('fullpage-total-qty');
        if (qtyEl) qtyEl.textContent = totalQty;
        const qtyReviewEl = document.getElementById('fullpage-total-qty-review');
        if (qtyReviewEl) qtyReviewEl.textContent = totalQty;

        const selectedAddonIds = getSelectedFullpageAddonIds();
        fullpageSubAddonsAdjustment = selectedAddonIds.reduce((sum, addonId) => {
            const addon = fullpageSubAddons.find(a => parseInt(a.id, 10) === addonId);
            return sum + (addon ? (parseFloat(addon.price) || 0) : 0);
        }, 0);

        fullpageSubFabricSurcharge = getFullpageFabricSurcharge();
        fullpageSubBaseUnitPrice = 0;

        if (typeSlug && totalQty > 0) {
            try {
                const response = await fetch(`/api/sublimation-total/price/${typeSlug}/${totalQty}`);
                const payload = await response.json();
                if (payload?.success) {
                    fullpageSubBaseUnitPrice = parseFloat(payload.price) || 0;
                }
            } catch (error) {
                console.error('Error fetching price:', error);
            }
        }

        fullpageSubUnitPrice = fullpageSubBaseUnitPrice + fullpageSubAddonsAdjustment + fullpageSubFabricSurcharge;
        if (fullpageSubUnitPrice < 0) fullpageSubUnitPrice = 0;

        const totalPrice = fullpageSubUnitPrice * totalQty;
        const formatMoney = value => `R$ ${Number(value || 0).toFixed(2).replace('.', ',')}`;
        const addonSign = fullpageSubAddonsAdjustment < 0 ? '-' : '+';
        const fabricSign = fullpageSubFabricSurcharge < 0 ? '-' : '+';

        const unitPriceEl = document.getElementById('fullpage-unit-price');
        if (unitPriceEl) unitPriceEl.textContent = formatMoney(fullpageSubUnitPrice);
        const unitPriceReviewEl = document.getElementById('fullpage-unit-price-review');
        if (unitPriceReviewEl) unitPriceReviewEl.textContent = formatMoney(fullpageSubUnitPrice);

        const totalPriceEl = document.getElementById('fullpage-total-price');
        if (totalPriceEl) totalPriceEl.textContent = formatMoney(totalPrice);
        const totalPriceReviewEl = document.getElementById('fullpage-total-price-review');
        if (totalPriceReviewEl) totalPriceReviewEl.textContent = formatMoney(totalPrice);

        const breakdownEl = document.getElementById('fullpage-price-breakdown');
        if (breakdownEl) {
            const breakdownText = `Base ${formatMoney(fullpageSubBaseUnitPrice)} ${addonSign} Adicionais ${formatMoney(Math.abs(fullpageSubAddonsAdjustment))} ${fabricSign} Tecido ${formatMoney(Math.abs(fullpageSubFabricSurcharge))}`;
            breakdownEl.textContent = breakdownText;
            const breakdownReviewEl = document.getElementById('fullpage-price-breakdown-review');
            if (breakdownReviewEl) breakdownReviewEl.textContent = breakdownText;
        }
    }
    window.calculateFullpageSubTotal = calculateFullpageSubTotal;

    async function submitFullpageSubItem() {
        if (isSubmittingFullpage) return false;

        const submitBtn = document.querySelector('#sublimation-fullpage-form button[onclick*="submitFullpageSubItem"]');
        const idleHtml = '<i class="fa-solid fa-plus"></i> Adicionar Item';
        const loadingHtml = '<i class="fa-solid fa-spinner animate-spin"></i> Adicionando...';
        const setSubmittingState = (loading) => {
            isSubmittingFullpage = loading;
            if (!submitBtn) return;
            submitBtn.disabled = loading;
            submitBtn.innerHTML = loading ? loadingHtml : idleHtml;
        };

        const typeSlug = document.getElementById('fullpage_sub_type')?.value || '';
        const artName = document.getElementById('fullpage_art_name')?.value?.trim() || '';
        const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
        const modelType = document.getElementById('fullpage_sub_model')?.value || '';
        const fabricCustom = document.getElementById('fullpage_sub_fabric_custom')?.value?.trim() || '';
        const fabricColor = document.getElementById('fullpage_sub_fabric_color')?.value || 'BRANCO';
        const baseCollar = document.getElementById('fullpage_sub_base_collar')?.value || 'REDONDA';
        const hasAddonColors = !!document.getElementById('fullpage_has_addon_colors')?.checked;

        if (!typeSlug) {
            alert('Selecione o tipo de produto.');
            return false;
        }
        if (!artName) {
            alert('Informe o nome da arte.');
            return false;
        }
        if (!fabricType) {
            alert('Selecione o tecido.');
            return false;
        }
        if (fabricType === 'OUTRO' && !fabricCustom) {
            alert('Informe o nome do tecido especial.');
            return false;
        }
        if (!modelType) {
            alert('Selecione o modelo.');
            return false;
        }

        setSubmittingState(true);

        const sizeInputs = document.querySelectorAll('.fullpage-sub-size');
        const tamanhos = {};
        let totalQty = 0;
        sizeInputs.forEach(input => {
            const qty = parseInt(input.value, 10) || 0;
            if (qty > 0) {
                tamanhos[input.dataset.size] = qty;
                totalQty += qty;
            }
        });

        if (totalQty === 0) {
            alert('Adicione pelo menos uma peca.');
            setSubmittingState(false);
            return false;
        }

        await calculateFullpageSubTotal();

        const selectedAddons = getSelectedFullpageAddonIds();
        const addonColorMap = {};
        if (hasAddonColors) {
            selectedAddons.forEach(addonId => {
                const input = document.querySelector(`#fullpage-addon-color-fields input[data-addon-color-id="${addonId}"]`);
                if (input && input.value.trim()) {
                    addonColorMap[String(addonId)] = input.value.trim();
                }
            });
        }

        const formData = new FormData();
        formData.append('action', 'add_sublimation_item');
        formData.append('sublimation_type', typeSlug);
        formData.append('art_name', artName);

        formData.append('fabric_type', fabricType);
        formData.append('fabric_custom', fabricCustom);
        formData.append('fabric_color', fabricColor);
        formData.append('model_type', modelType);
        formData.append('base_collar', baseCollar);
        formData.append('fabric_surcharge', String(getFullpageFabricSurcharge()));
        formData.append('has_addon_colors', hasAddonColors ? '1' : '0');
        formData.append('addon_color_map', JSON.stringify(addonColorMap));

        Object.keys(tamanhos).forEach(size => {
            formData.append(`tamanhos[${size}]`, tamanhos[size]);
        });

        formData.append('quantity', String(totalQty));
        formData.append('unit_price', Number(fullpageSubUnitPrice || 0).toFixed(2));
        formData.append('art_notes', document.getElementById('fullpage_notes')?.value || '');
        formData.append('_token', document.querySelector('input[name="_token"]')?.value || '');

        selectedAddons.forEach(id => formData.append('sublimation_addons[]', String(id)));

        const coverFile = document.getElementById('fullpage_cover_image')?.files?.[0];
        if (coverFile) formData.append('item_cover_image', coverFile);

        const corelFile = document.getElementById('fullpage_corel_file')?.files?.[0];
        if (corelFile) formData.append('corel_file', corelFile);

        try {
            const response = await fetch('{{ isset($editData) ? route("orders.edit.sewing") : route("orders.wizard.sewing") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();
            if (!data.success) {
                alert(data.message || 'Erro ao adicionar item.');
                return false;
            }

            const sidebar = document.getElementById('items-sidebar-container');
            if (sidebar && data.html) sidebar.innerHTML = data.html;

            if (data.items_data) {
                itemsData = data.items_data;
                window.itemsData = itemsData;
            }

            hideSubFullpageForm();
            resetFullpageSubForm();

            if (window.showToast) window.showToast('Item SUB. TOTAL adicionado!', 'success');
            return true;
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao processar solicitacao.');
            return false;
        } finally {
            setSubmittingState(false);
        }
    }
    window.submitFullpageSubItem = submitFullpageSubItem;

    function resetFullpageSubForm() {
        const typeSelect = document.getElementById('fullpage_sub_type');
        if (typeSelect) typeSelect.value = '';

        const artInput = document.getElementById('fullpage_art_name');
        if (artInput) artInput.value = '';

        const fabricTypeSelect = document.getElementById('fullpage_sub_fabric_type');
        if (fabricTypeSelect) fabricTypeSelect.value = '';

        const modelSelect = document.getElementById('fullpage_sub_model');
        if (modelSelect) modelSelect.value = '';

        const fabricCustomInput = document.getElementById('fullpage_sub_fabric_custom');
        if (fabricCustomInput) fabricCustomInput.value = '';

        const fabricSurchargeInput = document.getElementById('fullpage_sub_fabric_surcharge');
        if (fabricSurchargeInput) fabricSurchargeInput.value = '10.00';

        const hasAddonColors = document.getElementById('fullpage_has_addon_colors');
        if (hasAddonColors) hasAddonColors.checked = false;

        const addonColorFields = document.getElementById('fullpage-addon-color-fields');
        if (addonColorFields) {
            addonColorFields.innerHTML = '';
            addonColorFields.classList.add('hidden');
        }

        document.querySelectorAll('.fullpage-sub-size').forEach(input => {
            input.value = 0;
        });

        const notesInput = document.getElementById('fullpage_notes');
        if (notesInput) notesInput.value = '';

        const coverInput = document.getElementById('fullpage_cover_image');
        if (coverInput) coverInput.value = '';

        const corelInput = document.getElementById('fullpage_corel_file');
        if (corelInput) corelInput.value = '';

        const addonsContainer = document.getElementById('fullpage-sub-addons');
        if (addonsContainer) addonsContainer.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4">Selecione um tipo primeiro</p>';

        fullpageSubAddons = [];
        fullpageSubUnitPrice = 0;
        fullpageSubBaseUnitPrice = 0;
        fullpageSubAddonsAdjustment = 0;
        fullpageSubFabricSurcharge = 0;

        const qtyEl = document.getElementById('fullpage-total-qty');
        if (qtyEl) qtyEl.textContent = '0';
        const qtyReviewEl = document.getElementById('fullpage-total-qty-review');
        if (qtyReviewEl) qtyReviewEl.textContent = '0';

        const unitPriceEl = document.getElementById('fullpage-unit-price');
        if (unitPriceEl) unitPriceEl.textContent = 'R$ 0,00';
        const unitPriceReviewEl = document.getElementById('fullpage-unit-price-review');
        if (unitPriceReviewEl) unitPriceReviewEl.textContent = 'R$ 0,00';

        const totalPriceEl = document.getElementById('fullpage-total-price');
        if (totalPriceEl) totalPriceEl.textContent = 'R$ 0,00';
        const totalPriceReviewEl = document.getElementById('fullpage-total-price-review');
        if (totalPriceReviewEl) totalPriceReviewEl.textContent = 'R$ 0,00';

        const breakdownEl = document.getElementById('fullpage-price-breakdown');
        if (breakdownEl) breakdownEl.textContent = 'Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00';
        const breakdownReviewEl = document.getElementById('fullpage-price-breakdown-review');
        if (breakdownReviewEl) breakdownReviewEl.textContent = 'Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00';

        toggleFullpageSpecialFabric();
        setFullpageSubStep(1);
    }
    window.resetFullpageSubForm = resetFullpageSubForm;

    // --- Clipboard Paste Listener ---
    document.addEventListener('paste', function(e) {
        // Encontrar se algum modal relevante estÃ¡ aberto
        const wizardModal = document.getElementById('sewing-wizard-modal');
        const subModal = document.getElementById('sublimation-modal');

        let targetInput = null;
        let previewFn = null;

        if (subModal && !subModal.classList.contains('hidden')) {
            targetInput = document.getElementById('sub_wizard_file_input');
            previewFn = window.previewSublimationImage;
        } else if (wizardModal && !wizardModal.classList.contains('hidden') && window.wizardCurrentStep === 10) {
            targetInput = document.getElementById('wizard_file_input');
            previewFn = window.previewWizardImage;
        }

        if (!targetInput) return;

        const items = (e.clipboardData || window.clipboardData).items;
        if (!items) return;

        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const blob = items[i].getAsFile();
                if (!blob) continue;

                // Criar arquivo fake do blob
                const file = new File([blob], "pasted-image-" + Date.now() + ".png", { type: "image/png" });

                // Usar DataTransfer para simular seleÃ§Ã£o de arquivo
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                targetInput.files = dataTransfer.files;

                // Disparar preview
                if (previewFn) previewFn(targetInput);
                
                // Mostrar notificaÃ§Ã£o se disponÃ­vel
                if (window.showToast) {
                    window.showToast('Imagem colada com sucesso!', 'success');
                } else if (typeof showNotification === 'function') {
                    showNotification('Imagem colada com sucesso!', 'success');
                }
                
                e.preventDefault();
                break;
            }
        }
    });
})();


</script>
@endpush
@endsection




