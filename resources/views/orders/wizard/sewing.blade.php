@extends('layouts.admin')

@section('content')
<style>
/* Animações Premium */
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

/* Mobile responsiveness */
@media (max-width: 640px) {
    .size-grid-mobile { grid-template-columns: repeat(5, 1fr) !important; gap: 0.375rem !important; }
    .size-grid-mobile input { padding: 0.375rem !important; font-size: 12px !important; }
    .size-grid-mobile label { font-size: 10px !important; }
}
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-0">
    <!-- Progress Bar Premium -->
    <div class="mb-6 sm:mb-8 animate-fade-in-up">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-3 sm:mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#7c3aed] text-white rounded-xl sm:rounded-2xl flex items-center justify-center text-sm sm:text-base font-black shadow-xl shadow-purple-500/30 animate-float">2</div>
                <div>
                    <span class="text-base sm:text-xl font-black text-gray-900 dark:text-white">Costura e <span class="text-[#7c3aed]">Personalização</span></span>
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
    @if(session('success'))
    <div class="mb-4 sm:mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-lg animate-fade-in-up">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-green-500/30">
                <i class="fa-solid fa-check text-xs sm:text-sm"></i>
            </div>
            <p class="text-xs sm:text-sm font-bold text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 sm:mb-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-lg animate-fade-in-up">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-red-500/30">
                <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
            </div>
            <p class="text-xs sm:text-sm font-bold text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Formulário de Adicionar Item -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-2xl sm:rounded-3xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-100 dark:border-slate-800 overflow-hidden animate-fade-in-up delay-100">
                <!-- Header Premium -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 bg-white dark:from-slate-800/50 dark:to-slate-900/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/10 border border-gray-200 dark:border-slate-700">
                            <i class="fa-solid fa-plus text-[#7c3aed] text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-xl font-black text-gray-900 dark:text-white" id="form-title">Adicionar Novo Item</h1>
                            <p class="text-[10px] sm:text-sm text-gray-500 dark:text-slate-400 mt-0.5 font-medium">Configure os detalhes do item de costura</p>
                        </div>
                    </div>
                </div>

                    <div class="p-6">
                        <form method="POST" action="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" data-action-url="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" id="sewing-form" class="space-y-5" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="add_item" id="form-action">
                            <input type="hidden" name="editing_item_id" value="" id="editing-item-id">

                            <!-- Personalização agora é selecionada dentro do modal -->
                            <div id="hidden-personalizacao-container"></div>
                            <!-- Campos escondidos para envio ao backend -->
                            <input type="hidden" name="quantity" id="quantity" value="0">
                            <input type="hidden" name="unit_price" id="unit_price" value="0">
                            <input type="hidden" name="unit_cost" id="unit_cost" value="0">
                            <input type="hidden" name="art_notes" id="art_notes" value="">
                            <!-- Personalização movida para o Wizard (Etapa 1) -->

                            <!-- Wizard Trigger / Main Configuration Card -->
                            <div class="p-5 bg-white dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700 space-y-3">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white">Configuração do Item</label>
                                
                                <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 shadow-sm flex flex-col items-center justify-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-[#7c3aed] dark:text-purple-400 mb-2">
                                        <i class="fa-solid fa-layer-group text-3xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white" id="summary-title">Configurar Novo Item</h4>
                                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 max-w-md mx-auto" id="summary-desc">Clique abaixo para iniciar a configuração completa do item (Tecido, Modelo, Tamanhos, etc).</p>
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
                                <input type="hidden" name="detalhe" id="detalhe_hidden">
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
                            <div id="sewing-wizard-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
                                <!-- Backdrop -->
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
                                     onclick="closeSewingWizard()"></div>

                                <!-- Modal Panel -->
                                <div class="absolute inset-0 flex items-start justify-center p-4 pt-10 sm:pt-14 overflow-y-auto">
                                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col transform transition-all animate-fade-in-up border border-gray-200 dark:border-slate-700">
                                        
                                        <!-- Header -->
                                        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-black text-gray-900 dark:text-white">Configurar Modelo</h3>
                                                <p class="text-xs text-gray-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5" id="wizard-step-title">Etapa 1 de 5</p>
                                            </div>
                                            <button type="button" onclick="closeSewingWizard()" class="text-gray-400 hover:text-gray-500 transition-colors">
                                                <i class="fa-solid fa-xmark text-xl"></i>
                                            </button>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="w-full bg-gray-100 dark:bg-slate-800 h-1">
                                            <div id="wizard-progress" class="bg-[#7c3aed] h-full transition-all duration-300" style="width: 20%"></div>
                                        </div>

                                        <!-- Steps Content -->
                                        <div class="flex-1 overflow-y-auto p-6" id="wizard-content">
                                            
                                            <!-- Step 1: Personalização -->
                                            <div id="step-1" class="wizard-step">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Personalização</h4>
                                                <p class="text-xs text-gray-500 mb-4">Você pode selecionar múltiplas opções.</p>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="wizard-options-personalizacao">
                                                    <!-- Filled by JS -->
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
                                                    <div class="p-8 text-center text-gray-500">Carregando opções...</div>
                                                </div>
                                            </div>

                                            <!-- Step 5: Detalhe -->
                                            <div id="step-5" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione o Detalhe</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4" id="wizard-options-detalhe">
                                                    <!-- Filled by JS -->
                                                </div>
                                                <div class="p-3 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="different_detail_color_cb" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Cor do detalhe diferente do tecido?</span>
                                                    </label>
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
                                                <div class="grid grid-cols-4 gap-3 mb-4" id="wizard-sizes-grid">
                                                    <!-- Standard Sizes -->
                                                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'Especial'] as $size)
                                                    <div>
                                                        <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1 font-bold text-center">{{ $size }}</label>
                                                        <input type="number" data-size="{{ $size }}" min="0" value="0" class="wizard-size-input w-full px-2 py-2 border border-gray-200 dark:border-slate-700 rounded-lg text-center font-bold bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] transition-all">
                                                    </div>
                                                    @endforeach
                                                </div>
                                                
                                                 <!-- Checkbox para acréscimo independente (apenas para Infantil/Baby look) -->
                                                <div id="wizard-surcharge-container" class="hidden mb-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="wizard_apply_surcharge" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Aplicar acréscimo de tamanho especial</span>
                                                    </label>
                                                </div>

                                                <!-- Checkbox para Modelagem do Cliente (Aparece se Especial > 0) -->
                                                <div id="wizard-modeling-container" class="hidden mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="wizard_is_client_modeling" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Tamanho especial é pela modelagem do cliente?</span>
                                                    </label>
                                                </div>
                                                
                                                <div class="flex justify-between items-center bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-100 dark:border-purple-800/50">
                                                    <span class="text-sm font-bold text-gray-700 dark:text-slate-300">Total de Peças:</span>
                                                    <span class="text-2xl font-black text-[#7c3aed]" id="wizard-total-pieces">0</span>
                                                </div>
                                            </div>

                                            <!-- Step 10: Imagem e Obs -->
                                            <div id="step-10" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Personalização e Detalhes Finais</h4>
                                                
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
                                                             <button onclick="event.stopPropagation(); clearWizardImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-md hover:bg-red-600"><i class="fa-solid fa-times"></i></button>
                                                        </div>
                                                    </div>

                                                    <!-- Notes -->
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Observações</label>
                                                        <textarea id="wizard_notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed]" placeholder="Alguma observação importante para a produção?"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 11: Resumo Final -->
                                            <div id="step-11" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-center text-gray-900 dark:text-white mb-6">Conferência Final</h4>
                                                
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
                                                            <span class="text-gray-500 dark:text-slate-400">Peças:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-pecas-val">0</span>
                                                        </div>
                                                    </div>

                                                    <!-- Prices -->
                                                    <div class="mt-6 pt-4 border-t border-gray-300 dark:border-slate-600">
                                                        <h5 class="font-bold text-gray-900 dark:text-white mb-3">Custos e Valores</h5>
                                                        
                                                        <!-- Admin Only Unit Cost -->
                                                        <div class="flex justify-between items-center p-3 bg-white dark:bg-slate-900 border border-red-200 dark:border-red-900/30 rounded-lg mb-3" 
                                                             style="display: {{ auth()->user()->isAdmin() ? 'flex' : 'none' }}">
                                                            <span class="text-red-600 dark:text-red-400 font-bold text-sm">Custo Unitário:</span>
                                                            <div class="flex items-center">
                                                                <span class="text-red-600 dark:text-red-400 font-bold mr-1">R$</span>
                                                                <input type="number" id="wizard_unit_cost" class="w-20 bg-transparent text-right font-bold text-red-600 dark:text-red-400 border-none p-0 focus:ring-0" value="0.00" step="0.01">
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-between items-center p-4 bg-white dark:bg-slate-900 border border-purple-200 dark:border-purple-900/30 rounded-xl shadow-sm">
                                                            <span class="text-[#7c3aed] dark:text-purple-400 font-bold">Valor Unitário:</span>
                                                            <span class="text-2xl font-black text-[#7c3aed] dark:text-purple-400" id="wizard-final-price">R$ 0,00</span>
                                                        </div>
                                                    </div>

                                                    <button type="button" onclick="submitSewingWizard()" class="w-full py-4 mt-6 bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 transition-all transform hover:scale-[1.02]">
                                                        Confirmar e Adicionar Item
                                                    </button>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Footer -->
                                        <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-b-2xl">
                                            <button type="button" id="wizard-prev-btn" onclick="wizardPrevStep()" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200 text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                                                ← Voltar
                                            </button>
                                            <div class="flex gap-2">
                                                <button type="button" id="wizard-next-btn" onclick="wizardNextStep()" class="px-6 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold rounded-lg transition-all shadow-md shadow-purple-500/20">
                                                    Próximo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tamanhos (Moved above) -->

                            <!-- <!-- Botões (Removido - controllado pelo Wizard) -->
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
    <!-- Modal de Confirmação de Exclusão -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-slate-700 transform transition-all scale-100 opacity-100">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Remover Item?</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Esta ação não pode ser desfeita. O item será removido permanentemente do pedido.</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-lg shadow-red-500/30 transition-all transform hover:scale-105">
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

@push('scripts')
<script>
        // Options for Wizard
        let options = {
            tecido: @json($fabrics ?? []),
            cor: @json($colors ?? []),
            personalizacao: @json($personalizationOptions ?? [])
        };
        
        const storageUrl = "{{ asset('storage') }}/";

        // Ícones e cores específicos por tipo de personalização
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

        @php
            $safeSublimationTypes = isset($sublimationTypes) 
                ? $sublimationTypes->map(fn($t) => ['slug' => $t->slug, 'name' => $t->name])->values()
                : [];
            $safePreselectedTypes = $preselectedTypes ?? [];
        @endphp

        // SUB. TOTAL - Dados e Configurações
        const sublimationEnabled = {{ isset($sublimationEnabled) && $sublimationEnabled ? 'true' : 'false' }};
        const sublimationTypes = @json($safeSublimationTypes);
        let sublimationAddonsCache = {};
        
        // Tipos de personalização pré-selecionados na etapa anterior
        const preselectedPersonalizationTypes = @json($safePreselectedTypes);

        let itemToDeleteId = null;

        function openDeleteModal(itemId) {
            itemToDeleteId = itemId;
            document.getElementById('delete-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scrolling
            itemToDeleteId = null;
        }

        // Variável global para dados dos itens (agora 'let' para permitir atualização)
        let itemsData = {!! json_encode($order->items->toArray()) !!};

        async function confirmDelete() {
            if (!itemToDeleteId) return;

            const btn = document.querySelector('#delete-modal button.bg-red-600');
            const originalText = btn.innerText;
            btn.innerHTML = 'Removendo...';
            btn.disabled = true;

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
                    // Atualizar HTML da sidebar
                    document.getElementById('items-sidebar-container').innerHTML = data.html;
                    
                    // Atualizar dados dos itens
                    if (data.items_data) {
                        itemsData = data.items_data;
                    }
                    
                    // Fechar modal
                    closeDeleteModal();
                    
                    // Mostrar toast/notificação de sucesso (opcional)
                    // alert(data.message); 
                } else {
                    alert('Erro ao remover item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro na exclusão:', error);
                alert('Erro ao processar a exclusão.');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        document.getElementById('sewing-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            if (form.dataset.submitting === 'true') return;

            // Validação atualizada para o Wizard
            const personalizacaoInputs = document.querySelectorAll('input[name="personalizacao[]"]');
            
            if (personalizacaoInputs.length === 0) {
                 // Fallback check for preselected (edit mode)
                 const preselected = document.querySelectorAll('.preselected-personalization');
                 if (preselected.length === 0) {
                     alert('Por favor, selecione pelo menos uma personalização.');
                     return;
                 }
            }

            const quantity = parseInt(document.getElementById('quantity').value || 0); // Ensure val or 0
             // Get total quantity from inputs if quantity hidden is not updated (it should be by calculateTotal)
            
            if (quantity === 0) {
                 // Check if any size input has value > 0
                 let hasSize = false;
                 document.querySelectorAll('input[name^="tamanhos"]').forEach(i => {
                     if(parseInt(i.value) > 0) hasSize = true;
                 });
                 if (!hasSize) {
                     alert('Por favor, adicione pelo menos uma peça nos tamanhos.');
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
                
                // Garantir que headers de AJAX sejam enviados
                const actionUrl = form.dataset.actionUrl || form.getAttribute('action');
                console.log('Submitting sewing form to:', actionUrl);
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
                    console.error('Erro HTTP na submissão:', response.status, text);
                    alert('Erro ao salvar item: ' + (response.statusText || 'erro HTTP'));
                    return;
                }

                const rawText = await response.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (parseError) {
                    console.error('Erro ao interpretar resposta JSON:', parseError, rawText);
                    alert('Erro ao salvar item: resposta inválida do servidor.');
                    return;
                }

                if (data.success) {
                    // Atualizar HTML da sidebar
                    if (data.html) {
                        document.getElementById('items-sidebar-container').innerHTML = data.html;
                    }

                    // Atualizar dados dos itens
                    if (data.items_data) {
                        itemsData = data.items_data;
                    }

                    // Limpar formulário se foi uma adição (não edição)
                    const action = document.getElementById('form-action').value;
                    if (action === 'add_item') {
                        cancelEdit(); // Helper que reseta o form
                    } else {
                        cancelEdit(); // Sai do modo de edição
                        // Mostrar mensagem específica de atualização
                    }

                    // Scroll para o topo ou feedback visual
                    // Opcional: mostrar toast
                    window.location.reload(); // garante que o item apareÇa mesmo se a atualizaÇõÇœo via JS falhar
                    
                } else {
                     // Lidar com erros de validação (se o servidor retornar JSON de erro 422)
                     if (data.errors) {
                         let msg = 'Erros de validação:\n';
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
                alert('Ocorreu um erro ao processar sua solicitação.');
            } finally {
                // Restaurar botão
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = document.getElementById('form-action').value === 'update_item' ? 'Salvar Alterações' : 'Adicionar Item';
                }
                form.dataset.submitting = 'false';
            }
        });

        let optionsWithParents = {};

        document.addEventListener('DOMContentLoaded', function() {
            loadOptions();
        });

        function loadOptions() {
            fetch('/api/product-options')
                .then(response => response.json())
                .then(data => {
                    options = data;
                    return fetch('/api/product-options-with-parents');
                })
                .then(response => response.json())
                .then(data => {
                    optionsWithParents = data;
                    console.log('Options loaded.');
                    // Pre-load logic if needed or just wait for wizard open
                })
                .catch(error => {
                    console.error('Erro ao carregar opções:', error);
                });
        }

        // (Funções de renderização legadas removidas e substituídas pelo Wizard JS)
        
        async function loadStockByCutType() {
            // (Esta função era usada pelo sistema antigo, mas o Wizard reimplantou sua própria versão ou usa esta se compatível)
            // O Wizard chama loadStockByCutType(), então devemos manter esta função se ela não foi duplicada.
            // Verificando minha edição anterior: Eu NÃO incluí loadStockByCutType no bloco do wizard.
            // Onde está loadStockByCutType agora?
            // Está nas linhas 1106-1183 do original.
            // Se eu remover tudo até 1186, eu vou remover loadStockByCutType!
            // ERRO NO PLANO: loadStockByCutType é necessário.
            // Vou Abortar a remoção neste bloco e fazer uma remoção mais seletiva.
        }

        // (Funções legadas removidas: togglePersonalizacao, renderTecidos, etc.)
        
        // --- FUNÇõES UTILITÁRIAS MANTIDAS ---
        
        // Buscar estoque por tipo de corte
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
                        
                        // Mostrar detalhes por loja
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
                                            ✓ ${item.available} total
                                        </span>
                                    ` : `
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                            ✗ Sem estoque
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
                            stockBySize.innerHTML = '<p class="text-sm text-yellow-600 dark:text-yellow-400 text-center py-2">⚠ Nenhum estoque cadastrado para este produto</p>';
                        }
                    }
                }
            } catch (error) {
                console.error('Erro ao buscar estoque:', error);
                const stockSection = document.getElementById('stock-info-section');
                if (stockSection) stockSection.classList.add('hidden');
            }
        }
        
        // Adicionar listeners para mudanças nos tamanhos
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
                input.addEventListener('change', function() {
                    calculateTotal();
                });
            });
            
            // Listeners para recálculo de custo

        });



        // --- SEWING WIZARD LOGIC (11 Steps) ---
        let wizardCurrentStep = 1;
        const wizardTotalSteps = 11; 
        const isAdmin = @json(auth()->user()->isAdmin());
        
        let wizardData = {
            tecido: null,
            tipo_tecido: null,
            cor: null,
            tipo_corte: null,
            detalhe: null,
            detail_color: null,
            gola: null,
            collar_color: null,
            personalizacao: [], // Array of IDs
            image: null,
            notes: '',
            sizes: {}, // {PP: 5, M: 2 ...}
            unit_cost: 0
        };

        // Global filter for subsequent steps
        let selectedPersonalizacoes = [];

        function openSewingWizard() {
            document.getElementById('sewing-wizard-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Initialize or Restore State?
            // For now, simple reset if empty, or keep state if editing
            if (!wizardData.tecido && document.getElementById('tecido_hidden').value) {
                // TODO: Load from hidden inputs if editing an existing item (Edit Mode)
                // This would require parsing the hidden inputs back into wizardData
            }
            
            // Start or Update UI
            updateWizardUI();
        }

        function closeSewingWizard() {
            document.getElementById('sewing-wizard-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function wizardNextStep() {
            if (!validateStep(wizardCurrentStep)) return;

            if (wizardCurrentStep < wizardTotalSteps) {
                // Skip logic for Detail Color
                if (wizardCurrentStep === 5) {
                    const isDifferentDetail = document.getElementById('different_detail_color_cb').checked;
                    const detail = wizardData.detalhe;
                    // Skip Step 6 if no detail or same color as fabric requested
                    if (!detail || detail.name.toLowerCase().includes('sem') || !isDifferentDetail) {
                        wizardData.detail_color = wizardData.cor;
                        wizardCurrentStep += 2; // Jump to Step 7 (Gola)
                        loadWizardOptionsForStep(wizardCurrentStep);
                        updateWizardUI();
                        return;
                    }
                }

                // Skip logic for Collar Color
                if (wizardCurrentStep === 7) {
                    const isDifferentCollar = document.getElementById('different_collar_color_cb').checked;
                    const gola = wizardData.gola;
                    // Skip Step 8 if no collar or same color as fabric requested
                    if (!gola || gola.name.toLowerCase().includes('sem') || !isDifferentCollar) {
                        wizardData.collar_color = wizardData.cor;
                        wizardCurrentStep += 2; // Jump to Step 9 (Tamanhos)
                        loadWizardOptionsForStep(wizardCurrentStep);
                        updateWizardUI();
                        return;
                    }
                }

                wizardCurrentStep++;
                loadWizardOptionsForStep(wizardCurrentStep);
                updateWizardUI();
            }
        }

        function wizardPrevStep() {
            if (wizardCurrentStep > 1) {
                // Skip logic backward for Detail Color
                if (wizardCurrentStep === 7) {
                    const isDifferentDetail = document.getElementById('different_detail_color_cb').checked;
                    const detail = wizardData.detalhe;
                    if (!detail || detail.name.toLowerCase().includes('sem') || !isDifferentDetail) {
                        wizardCurrentStep -= 2; // Go back to Step 5 (Detalhe)
                        updateWizardUI();
                        return;
                    }
                }

                // Skip logic backward for Sizes
                if (wizardCurrentStep === 9) {
                    const isDifferentCollar = document.getElementById('different_collar_color_cb').checked;
                    const gola = wizardData.gola;
                    if (!gola || gola.name.toLowerCase().includes('sem') || !isDifferentCollar) {
                        wizardCurrentStep -= 2; // Go back to Step 7 (Gola)
                        updateWizardUI();
                        return;
                    }
                }

                wizardCurrentStep--;
                updateWizardUI();
            }
        }
        
        function validateStep(step) {
            if (step === 1) { // Personalização
                 if (!wizardData.personalizacao || wizardData.personalizacao.length === 0) {
                     alert('Selecione pelo menos uma personalização.');
                     return false;
                 }
            }
            if (step === 2) { // Tecido
                if (!wizardData.tecido) {
                    alert('Selecione um tecido para continuar.');
                    return false;
                }
            }
            if (step === 3) { // Cor
                if (!wizardData.cor) {
                    alert('Selecione uma cor para continuar.');
                    return false;
                }
            }
            if (step === 4) { // Corte
                 if (!wizardData.tipo_corte) {
                    alert('Selecione um tipo de corte.');
                    return false;
                }
            }
            if (step === 9) { // Sizes
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

        function updateWizardUI() {
            // Update Title
            const titles = [
                "Personalização", "Tecido", "Cor do Tecido", "Modelo", "Detalhe", "Cor do Detalhe", 
                "Gola", "Cor da Gola", "Tamanhos", "Imagem / Obs", "Resumo"
            ];
            document.getElementById('wizard-step-title').textContent = `${titles[wizardCurrentStep-1]} (Etapa ${wizardCurrentStep} de ${wizardTotalSteps})`;
            
            // Progress Bar
            const progress = (wizardCurrentStep / wizardTotalSteps) * 100;
            document.getElementById('wizard-progress').style.width = `${progress}%`;

            // Toggle Steps
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
            
            // Update Prev Button
            const prevBtn = document.getElementById('wizard-prev-btn');
            const nextBtn = document.getElementById('wizard-next-btn');
            
            if (prevBtn) {
                prevBtn.disabled = wizardCurrentStep === 1;
                prevBtn.classList.toggle('opacity-50', wizardCurrentStep === 1);
                prevBtn.classList.toggle('cursor-not-allowed', wizardCurrentStep === 1);
            }
            
            // Hide Next button on last step (Step 10 is Summary with its own Confirm button)
            // Actually Step 10 has "Confirmar", so hide the standard "Next" button on Step 10
            if (nextBtn) {
                if(wizardCurrentStep === wizardTotalSteps) {
                    nextBtn.classList.add('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                }
            }

            // If Step 11, update summary values
            if (wizardCurrentStep === 11) {
                updateFinalSummary();
            }
        }

        function getOptionList(possibleKeys) {
            for (const key of possibleKeys) {
                if (options[key] && Array.isArray(options[key]) && options[key].length) {
                    return options[key];
                }
                if (optionsWithParents[key] && Array.isArray(optionsWithParents[key]) && optionsWithParents[key].length) {
                    return optionsWithParents[key];
                }
            }
            return [];
        }

        function filterByParent(items, parentId) {
            if (!parentId) return items;
            return items.filter(item => {
                if (Array.isArray(item.parent_ids)) {
                    return item.parent_ids.includes(parseInt(parentId)) || item.parent_ids.includes(parentId);
                }
                if (item.parent_id !== undefined && item.parent_id !== null) {
                    return item.parent_id == parentId;
                }
                return true;
            });
        }

        function renderOptionCards(containerId, fieldKey, sourceKeys, parentId = null) {
            const container = document.getElementById(containerId);
            if (!container) return;

            let items = getOptionList(sourceKeys);
            if (parentId) {
                items = filterByParent(items, parentId);
            }

            if (!items || items.length === 0) {
                container.innerHTML = '<div class="col-span-full text-center text-sm text-gray-500">Nenhuma opção disponível.</div>';
                return;
            }

            container.innerHTML = items.map(item => {
                const isActive = wizardData[fieldKey] && wizardData[fieldKey].id == item.id;
                const price = parseFloat(item.price || 0);
                return `
                    <div class="wizard-option-card p-4 rounded-xl border ${isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-slate-700'} hover:border-[#7c3aed] dark:hover:border-[#7c3aed] cursor-pointer transition-all"
                        onclick="selectWizardOption('${fieldKey}', '${item.id}', '${item.name.replace(/'/g, '')}', ${price}, true)">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-800 dark:text-gray-100">${item.name}</span>
                            ${price > 0 ? `<span class="text-xs font-bold text-[#7c3aed]">R$ ${price.toFixed(2).replace('.', ',')}</span>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderWizardCorteOptions() {
            renderOptionCards('wizard-options-corte', 'tipo_corte', ['tipo_corte', 'corte', 'cut_types']);
        }

        function renderWizardDetalheOptions() {
            renderOptionCards('wizard-options-detalhe', 'detalhe', ['detalhe', 'detail']);
        }

        function renderWizardDetailColorOptions() {
            const detailId = wizardData.detalhe ? wizardData.detalhe.id : null;
            if (!detailId) {
                const container = document.getElementById('wizard-options-cor-detalhe');
                if (container) container.innerHTML = '<div class="col-span-full text-center text-sm text-gray-500">Selecione um detalhe primeiro.</div>';
                return;
            }
            renderOptionCards('wizard-options-cor-detalhe', 'detail_color', ['cor', 'cor_detalhe', 'detail_color'], detailId);
        }

        function renderWizardGolaOptions() {
            renderOptionCards('wizard-options-gola', 'gola', ['gola', 'collar']);
        }

        function renderWizardCollarColorOptions() {
            const collarId = wizardData.gola ? wizardData.gola.id : null;
            if (!collarId) {
                const container = document.getElementById('wizard-options-cor-gola');
                if (container) container.innerHTML = '<div class="col-span-full text-center text-sm text-gray-500">Selecione a gola primeiro.</div>';
                return;
            }
            renderOptionCards('wizard-options-cor-gola', 'collar_color', ['cor', 'cor_gola', 'collar_color'], collarId);
        }

        function loadWizardOptionsForStep(step) {
            if (step === 1) renderWizardPersonalizacao();
            if (step === 2) loadWizardTecidos();
            if (step === 3) loadWizardCores(); // Uses Fabric ID
            if (step === 4) renderWizardCorteOptions();
            if (step === 5) renderWizardDetalheOptions();
            if (step === 6) renderWizardDetailColorOptions();
            if (step === 7) renderWizardGolaOptions();
            if (step === 8) renderWizardCollarColorOptions();
            // Step 9 is static inputs
            // Step 10 is static uploads
        }
        
        function selectWizardOption(field, value, name, price = 0, autoAdvance = true) {
            // Update local state
            wizardData[field] = { id: value, name: name, price: parseFloat(price) };
            
            // Log for debug
            console.log(`Selected ${field}:`, wizardData[field]);

            // Visual Feedback (Highlighter)
            // Remove ring from siblings
            const container = document.getElementById(`wizard-options-${field.replace('_', '-')}`) 
                           || document.getElementById(`wizard-options-${field}`); // fallback
            
            if (container) {
                 const cards = container.querySelectorAll('.wizard-option-card');
                 cards.forEach(c => c.classList.remove('ring-2', 'ring-[#7c3aed]'));
                 // This is tricky as we don't pass the element 'this' here easily unless we change the HTML calls
                 // But the HTML calls usually do: onclick="selectWizardOption(..., this)" or handle it inline.
                 // My rendered HTML does inline class toggling. Good.
            }

            if (autoAdvance) {
                setTimeout(() => wizardNextStep(), 300);
            }
        }

        // --- Step 1: Personalização ---
        function renderWizardPersonalizacao() {
            const container = document.getElementById('wizard-options-personalizacao');
            if(!container) return;
            
            // Check if options exist
            if (!options.personalizacao || options.personalizacao.length === 0) {
                container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma opção disponível.</p>';
                return;
            }

            container.innerHTML = options.personalizacao.map(item => {
                const isSelected = wizardData.personalizacao.includes(item.id.toString()) || wizardData.personalizacao.includes(item.id);
                const activeClass = isSelected ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20' : '';
                const key = (item.slug || item.name || '').toString().trim().toLowerCase().replace(/\s+/g, '_');
                const style = personalizationIconMap[key] || personalizationIconMap.default;
                
                return `
                <div class="wizard-option-card group cursor-pointer p-4 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md transition-all flex flex-col items-center gap-2 ${activeClass}"
                     data-id="${item.id}"
                     onclick="toggleWizardPersonalizacao(this)">
                    <div class="w-10 h-10 rounded-full ${style.bubble} flex items-center justify-center ${style.color}">
                         <i class="fa-solid ${style.icon}"></i>
                    </div>
                    <span class="text-xs font-bold text-center text-gray-700 dark:text-slate-300 group-hover:text-[#7c3aed]">${item.name}</span>
                </div>
                `;
            }).join('');
        }

        function toggleWizardPersonalizacao(element) {
            const id = element.dataset.id.toString();
            const index = wizardData.personalizacao.indexOf(id);
            
            if (index > -1) {
                // Remove
                wizardData.personalizacao.splice(index, 1);
                element.classList.remove('ring-2', 'ring-[#7c3aed]', 'bg-purple-50', 'dark:bg-purple-900/20');
            } else {
                // Add
                wizardData.personalizacao.push(id);
                element.classList.add('ring-2', 'ring-[#7c3aed]', 'bg-purple-50', 'dark:bg-purple-900/20');
            }
            
            // Sync with global for filtering
            selectedPersonalizacoes = [...wizardData.personalizacao];
            
            // Update hidden input logic
             const hiddenContainer = document.getElementById('hidden-personalizacao-container');
             if(hiddenContainer) {
                 hiddenContainer.innerHTML = wizardData.personalizacao.map(pid => 
                     `<input type="hidden" name="personalizacao[]" value="${pid}">`
                 ).join('');
             }
        }

        // --- Step 2: Tecidos ---
        function loadWizardTecidos() {
            const select = document.getElementById('wizard_tecido');
            if(!select) return;
            
            if (select.options.length <= 1) {
                let items = options.tecido || [];
                
                if (selectedPersonalizacoes && selectedPersonalizacoes.length > 0) {
                    items = items.filter(tecido => {
                        if (!tecido.parent_ids || tecido.parent_ids.length === 0) return true;
                        return tecido.parent_ids.some(parentId => selectedPersonalizacoes.includes(parentId) || selectedPersonalizacoes.includes(parentId.toString()));
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
        
        function loadWizardTiposTecido() {
             const select = document.getElementById('wizard_tecido');
             const typeContainer = document.getElementById('wizard-tipo-tecido-container');
             const typeSelect = document.getElementById('wizard_tipo_tecido');
             
             const fabricId = select.value;
             if(!fabricId) {
                 wizardData.tecido = null;
                 return;
             }
             
              const fabricName = select.options[select.selectedIndex].text;
              
              // Only overwrite if changed or null
              if (!wizardData.tecido || wizardData.tecido.id != fabricId) {
                  wizardData.tecido = { id: fabricId, name: fabricName, price: 0 };
              }
              
              // Loads subtypes
              const subItems = (options.tipo_tecido || []).filter(t => t.parent_id == fabricId);
              if(subItems.length > 0) {
                  typeContainer.classList.remove('hidden');
                  
                  // Avoid unnecessary re-rendering if options are the same? 
                  // For now just ensure value is preserved.
                  typeSelect.innerHTML = '<option value="">Selecione o tipo</option>' + 
                     subItems.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
                  
                  if (wizardData.tipo_tecido) {
                      // Check if current tipo_tecido is still valid for this fabric
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
             
             // Reload colors since they depend on fabric
             loadWizardCores(); // Pre-load next step
        }
        
        function onWizardTipoTecidoChange() {
             const select = document.getElementById('wizard_tipo_tecido');
             if(select.value) {
                 wizardData.tipo_tecido = { id: select.value, name: select.options[select.selectedIndex].text };
             } else {
                 wizardData.tipo_tecido = null;
             }
        }

        // --- Step 2: Cores ---
        // --- Step 3: Cores ---
        function loadWizardCores() {
             const container = document.getElementById('wizard-colors-grid');
             const select = document.getElementById('wizard_cor'); // Search select
             if(!container) return;
             
             let items = options.cor || [];
             const tecidoId = wizardData.tecido ? wizardData.tecido.id : null;
             
             if (selectedPersonalizacoes.length > 0 || tecidoId) {
                items = items.filter(cor => {
                    if (!cor.parent_ids || cor.parent_ids.length === 0) return true;
                    // Fix: Ensure comparison logic is valid
                    // Assuming parent_ids matches either Personalization IDs or Fabric IDs
                    const matchesP = selectedPersonalizacoes.length > 0 && cor.parent_ids.some(pid => selectedPersonalizacoes.includes(pid.toString()));
                    const matchesT = tecidoId && cor.parent_ids.includes(parseInt(tecidoId));
                    return matchesP || matchesT;
                });
             }
             
             // Fill Grid
             container.innerHTML = items.map(color => {
                const isActive = wizardData.cor && wizardData.cor.id == color.id;
                const activeClass = isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20' : '';
                return `
                <div class="wizard-option-card group cursor-pointer p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md transition-all flex flex-col items-center gap-2 ${activeClass}"
                     data-id="${color.id}"
                     onclick="selectWizardColor(this)">
                    <div class="w-8 h-8 rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.hex_code || '#ccc'}"></div>
                    <span class="text-xs font-bold text-center text-gray-700 dark:text-slate-300 group-hover:text-[#7c3aed]">${color.name}</span>
                </div>
                `;
             }).join('');
            
            // Fill Select options for text search
             if(select) {
                 select.innerHTML = '<option value="">Selecione uma cor</option>' + 
                    items.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                 
                 if (wizardData.cor) {
                     select.value = wizardData.cor.id;
                 }

                 // Bind select change
                 select.onchange = function() {
                     if(this.value) {
                         const mockEl = { dataset: { id: this.value } };
                         selectWizardColor(mockEl);
                     }
                 };
             }
        }

        function selectWizardColor(element) {
            const id = element.dataset ? element.dataset.id : element; // Fallback? No, always use object from select
            // Actually, for select.onchange above, I passed an object with dataset.id
            const color = (options.cor || []).find(c => c.id == id);
            if(color) {
                wizardData.cor = { id: color.id, name: color.name };
                document.getElementById('wizard_cor').value = color.id;
                wizardNextStep();
            }
        }
        
        // --- Step 8: Calculate Total ---
        function calculateWizardTotal() {
            let total = 0;
            let especialQty = 0;
            document.querySelectorAll('.wizard-size-input').forEach(input => {
                const qty = parseInt(input.value) || 0;
                total += qty;
                if (input.dataset.size === 'Especial') especialQty = qty;
            });
            document.getElementById('wizard-total-pieces').textContent = total;
            document.getElementById('summary-pecas-val').textContent = total;

            // Show/Hide modeling checkbox
            const modelingContainer = document.getElementById('wizard-modeling-container');
            if (modelingContainer) {
                if (especialQty > 0) {
                    modelingContainer.classList.remove('hidden');
                } else {
                    modelingContainer.classList.add('hidden');
                    document.getElementById('wizard_is_client_modeling').checked = false;
                }
            }

            return total;
        }
        // Bind input change
        document.addEventListener('input', function(e) {
            if(e.target.classList.contains('wizard-size-input')) {
                calculateWizardTotal();
            }
        });

        // --- Step 9: Image & Notes ---
        function previewWizardImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('wizard-image-preview').src = e.target.result;
                    document.getElementById('wizard-image-preview-container').classList.remove('hidden');
                    document.getElementById('wizard-image-placeholder').classList.add('hidden');
                    // Store file logic handled by form submit of input element
                    wizardData.image = input.files[0];
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function clearWizardImage() {
             document.getElementById('wizard_file_input').value = '';
             document.getElementById('wizard-image-preview-container').classList.add('hidden');
             document.getElementById('wizard-image-placeholder').classList.remove('hidden');
             wizardData.image = null;
             
             // Clear existing hidden image if any
             const existingImg = document.getElementById('existing_cover_image_hidden');
             if(existingImg) existingImg.value = '';
        }
        
        // --- Step 10: Summary & Submit ---
        function updateFinalSummary() {
            document.getElementById('summary-tecido-val').textContent = wizardData.tecido ? wizardData.tecido.name : '-';
            document.getElementById('summary-cor-val').textContent = wizardData.cor ? wizardData.cor.name : '-';
            document.getElementById('summary-modelo-val').textContent = wizardData.tipo_corte ? wizardData.tipo_corte.name : '-';
            
            // Calculate Prices
            let unitPrice = 0;
            if(wizardData.tipo_corte) unitPrice += wizardData.tipo_corte.price;
            if(wizardData.detalhe) unitPrice += wizardData.detalhe.price;
            if(wizardData.gola) unitPrice += wizardData.gola.price;
            
            // Apply Surcharge
            const applySurcharge = document.getElementById('wizard_apply_surcharge').checked;
            // TODO: Surcharge logic (R$ 5.00 extra, etc.) - Legacy logic was complicated. 
            // For now just sum base options.
            
            document.getElementById('wizard-final-price').textContent = 'R$ ' + unitPrice.toFixed(2).replace('.', ',');
            wizardData.unit_price = unitPrice;
        }
        
        function submitSewingWizard() {
            // 1. Populate Hidden Inputs in Main Form
            document.getElementById('tecido_hidden').value = wizardData.tecido ? wizardData.tecido.id : '';
            document.getElementById('tipo_tecido_hidden').value = wizardData.tipo_tecido ? wizardData.tipo_tecido.id : '';
            document.getElementById('cor_hidden').value = wizardData.cor ? wizardData.cor.id : '';
            document.getElementById('tipo_corte_hidden').value = wizardData.tipo_corte ? wizardData.tipo_corte.id : '';
            document.getElementById('detalhe_hidden').value = wizardData.detalhe ? wizardData.detalhe.id : '';
            document.getElementById('detail_color_hidden').value = wizardData.detail_color ? wizardData.detail_color.id : '';
            document.getElementById('gola_hidden').value = wizardData.gola ? wizardData.gola.id : '';
            document.getElementById('collar_color_hidden').value = wizardData.collar_color ? wizardData.collar_color.id : '';
            
            // Populate apply_surcharge and is_client_modeling
            const hiddenInputs = [
                { id: 'apply_surcharge_hidden', name: 'apply_surcharge', value: document.getElementById('wizard_apply_surcharge').checked ? '1' : '0' },
                { id: 'is_client_modeling_hidden', name: 'is_client_modeling', value: document.getElementById('wizard_is_client_modeling').checked ? '1' : '0' },
                { id: 'existing_cover_image_hidden', name: 'existing_cover_image', value: (typeof wizardData.image === 'string') ? wizardData.image : '' }
            ];

            hiddenInputs.forEach(meta => {
                let input = document.getElementById(meta.id);
                if(!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.id = meta.id;
                    input.name = meta.name;
                    document.getElementById('sewing-form').appendChild(input);
                }
                input.value = meta.value;
            });

            // 2. Populate Sizes
            const sizeContainer = document.getElementById('hidden-sizes-container');
            sizeContainer.innerHTML = '';
            let totalQty = 0;
            for (const [size, qty] of Object.entries(wizardData.sizes)) {
                if(qty > 0) {
                   const input = document.createElement('input');
                   input.type = 'hidden';
                   input.name = `tamanhos[${size}]`; // Correct name format for backend
                   input.value = qty;
                   sizeContainer.appendChild(input);
                   totalQty += parseInt(qty) || 0;
                }
            }
            // Atualiza quantidade total esperada pelo backend
            const qtyInput = document.getElementById('quantity');
            if (qtyInput) {
                qtyInput.value = totalQty;
            }
            
            // 3. Populate Notes & Cost
            const notes = document.getElementById('wizard_notes').value;
             // Append notes input if not exists
            if(!document.querySelector('input[name="art_notes"]')) {
                 const nInput = document.createElement('input');
                 nInput.type = 'hidden';
                 nInput.name = 'art_notes';
                 nInput.value = notes;
                 sizeContainer.appendChild(nInput); // reuse container
            } else {
                 document.querySelector('input[name="art_notes"]').value = notes;
            }
            
            // Unit Cost (Admin)
            if (isAdmin) {
                const cost = document.getElementById('wizard_unit_cost').value;
                 // Append cost input
                 const cInput = document.createElement('input');
                 cInput.type = 'hidden';
                 cInput.name = 'unit_cost';
                 cInput.value = cost;
                 sizeContainer.appendChild(cInput);
            }
            // Unit price
            const unitPriceInput = document.getElementById('unit_price');
            if (unitPriceInput) {
                const finalPrice = wizardData.unit_price || parseFloat((document.getElementById('wizard-final-price')?.textContent || '0').replace(/[R$\\s\\.]/g,'').replace(',','.')) || 0;
                unitPriceInput.value = finalPrice;
            }
            
            // 4. Populate Personalizacao inputs
            const personalizacaoContainer = document.getElementById('hidden-personalizacao-container');
            personalizacaoContainer.innerHTML = '';
            wizardData.personalizacao.forEach(pId => {
                const pInput = document.createElement('input');
                pInput.type = 'hidden';
                pInput.name = 'personalizacao[]';
                pInput.value = pId;
                personalizacaoContainer.appendChild(pInput);
            });
            
            // 5. Ensure file input has correct name
            document.getElementById('wizard_file_input').name = 'item_cover_image';
            
            // 6. Submit Form (Use requestSubmit to trigger AJAX listener)
            const form = document.getElementById('sewing-form');
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }



        
        // Legacy stock checking functions (checkStockForAllSizes, createStockRequestForSize) 
        // removed as they referenced old DOM elements.
        // Stock checking should be integrated into the Wizard steps (e.g., Step 9).

        /* Duplicate submit handler (legacy) disabled
        document.getElementById('sewing-form').addEventListener('submit', function(e) {
            return; // duplicate handler disabled
            const form = this;
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }

            const checkboxes = document.querySelectorAll('.personalizacao-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos uma personalização.');
                return false;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            if (quantity === 0) {
                e.preventDefault();
                alert('Por favor, adicione pelo menos uma peça nos tamanhos.');
                return false;
            }

            // Disable button and show processing state
            const submitBtn = document.getElementById('submit-button');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerText;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processando...
                `;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
            
            form.dataset.submitting = 'true';
            
            return true;
        });
        */

        // Legacy functions removed to prevent syntax/reference errors.
        // Editing flows should utilize openSewingWizard(itemData) logic.
        
        async function editItem(itemId) {
            await populateWizardFromItem(itemId, false);
        }

        async function duplicateItem(itemId) {
            await populateWizardFromItem(itemId, true);
        }

        async function populateWizardFromItem(itemId, isDuplicate) {
            const item = itemsData.find(i => i.id == itemId);
            if (!item) {
                alert('Item não encontrado.');
                return;
            }

            // Ensure options are loaded (they come from AJAX)
            if (Object.keys(optionsWithParents).length === 0) {
                 console.log('Waiting for options to load...');
                 await new Promise(resolve => setTimeout(resolve, 800));
                 if (Object.keys(optionsWithParents).length === 0) {
                     alert('As opções de produtos ainda estão carregando. Por favor, aguarde um segundo e tente novamente.');
                     return;
                 }
            }

            // Reset wizardData
            wizardData = {
                tecido: null,
                tipo_tecido: null,
                cor: null,
                tipo_corte: null,
                detalhe: null,
                detail_color: null,
                gola: null,
                collar_color: null,
                personalizacao: [],
                image: item.cover_image || null,
                imageUrl: item.cover_image_url || null,
                notes: item.art_notes || '',
                sizes: {},
                unit_cost: item.unit_cost || 0
            };

            // Set editing ID
            document.getElementById('editing-item-id').value = isDuplicate ? '' : itemId;
            document.getElementById('form-action').value = isDuplicate ? 'add_item' : 'update_item';
            document.getElementById('form-title').textContent = isDuplicate ? 'Duplicar Item' : 'Editar Item';
            
            // Parse print_desc for wizard_ids or fallback
            let printDesc = {};
            try {
                printDesc = typeof item.print_desc === 'string' ? JSON.parse(item.print_desc) : item.print_desc;
            } catch(e) { console.error('Erro ao parsear print_desc', e); }

            const wIds = printDesc.wizard_ids || {};
            
            // Helper to find option by name if ID is missing
            const findOptionByName = (listKey, name) => {
                const list = getOptionList([listKey]);
                if (!name) return null;
                const cleanName = name.split(' - ')[0].trim().toLowerCase();
                return list.find(o => o.name.toLowerCase().includes(cleanName)) || null;
            };

            // Restore from IDs or Name fallback
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

            if (wIds.detalhe) {
                const detail = getOptionList(['detalhe']).find(o => o.id == wIds.detalhe);
                if (detail) wizardData.detalhe = { id: detail.id, name: detail.name, price: parseFloat(detail.price || 0) };
            } else {
                 const opt = findOptionByName('detalhe', item.detail);
                 if(opt) wizardData.detalhe = { id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) };
            }

            if (wIds.detail_color) {
                const dc = getOptionList(['cor']).find(o => o.id == wIds.detail_color);
                if (dc) wizardData.detail_color = { id: dc.id, name: dc.name, price: 0 };
            } else {
                 const opt = findOptionByName('cor', item.detail_color);
                 if(opt) wizardData.detail_color = { id: opt.id, name: opt.name, price: 0 };
            }

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
                // Fallback for personalizacao is harder because it's a string of names
                if (item.print_type) {
                    const names = item.print_type.split(',').map(n => n.trim().toLowerCase());
                    const allP = getOptionList(['personalizacao']);
                    wizardData.personalizacao = allP
                        .filter(p => names.includes(p.name.toLowerCase()))
                        .map(p => p.id.toString());
                }
            }

            // Sizes
            let itemSizes = {};
            try {
                itemSizes = typeof item.sizes === 'string' ? JSON.parse(item.sizes) : item.sizes;
            } catch(e) {}
            wizardData.sizes = itemSizes || {};

            // Pre-fill checkboxes for "different color" if needed
            if (wizardData.detail_color && wizardData.cor && wizardData.detail_color.id != wizardData.cor.id) {
                document.getElementById('different_detail_color_cb').checked = true;
            } else {
                document.getElementById('different_detail_color_cb').checked = false;
            }
            if (wizardData.collar_color && wizardData.cor && wizardData.collar_color.id != wizardData.cor.id) {
                document.getElementById('different_collar_color_cb').checked = true;
            } else {
                document.getElementById('different_collar_color_cb').checked = false;
            }

            // Sync other UI elements
            document.getElementById('wizard_notes').value = wizardData.notes;
            document.getElementById('wizard_unit_cost').value = wizardData.unit_cost;
            document.getElementById('wizard_apply_surcharge').checked = !!printDesc.apply_surcharge;
            document.getElementById('wizard_is_client_modeling').checked = !!printDesc.is_client_modeling;

            // Fill size inputs
            document.querySelectorAll('.wizard-size-input').forEach(input => {
                const s = input.dataset.size;
                input.value = wizardData.sizes[s] || 0;
            });
            
            calculateWizardTotal();
            
            // Image preview
            if (wizardData.imageUrl) {
                document.getElementById('wizard-image-preview').src = wizardData.imageUrl;
                document.getElementById('wizard-image-preview-container').classList.remove('hidden');
                document.getElementById('wizard-image-placeholder').classList.add('hidden');
            } else if (wizardData.image && typeof wizardData.image === 'string') {
                document.getElementById('wizard-image-preview').src = storageUrl + wizardData.image;
                document.getElementById('wizard-image-preview-container').classList.remove('hidden');
                document.getElementById('wizard-image-placeholder').classList.add('hidden');
            } else {
                clearWizardImage();
            }

            // Sync global filter
            selectedPersonalizacoes = [...wizardData.personalizacao];

            // Open Wizard
            wizardCurrentStep = isDuplicate ? 4 : 1; 
            openSewingWizard();
        }
        
        function previewCoverImage(input) {
            const previewContainer = document.getElementById('cover-image-preview-container');
            const previewImage = document.getElementById('cover-image-preview');
            const fileNameDisplay = document.getElementById('file-name-display');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
                
                fileNameDisplay.textContent = 'Arquivo selecionado: ' + input.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            }
        }


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
            
            // Limpar preview de imagem
            if (coverPreviewContainer) coverPreviewContainer.classList.add('hidden');
            if (coverPreview) coverPreview.src = '';
            if (fileNameDisplay) {
                fileNameDisplay.classList.add('hidden');
                fileNameDisplay.textContent = '';
            }
            
            document.querySelectorAll('.personalizacao-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset wizard data
            wizardData = {
                tecido: null, tipo_tecido: null, cor: null, tipo_corte: null,
                detalhe: null, detail_color: null, gola: null, collar_color: null,
                personalizacao: [], image: null, imageUrl: null, notes: '', sizes: {}, unit_cost: 0
            };
            selectedPersonalizacoes = [];
            wizardCurrentStep = 1;
            closeSewingWizard();
        }

        @if(isset($editData))
        document.addEventListener('DOMContentLoaded', function() {
            const submitButton = document.getElementById('submit-button');
            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg';
            cancelButton.innerHTML = 'Cancelar Edição';
            cancelButton.onclick = cancelEdit;
            
            submitButton.parentNode.insertBefore(cancelButton, submitButton.nextSibling);
        });
        @endif

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
                    // Recarregar a página para atualizar a ordem
                    window.location.reload();
                } else {
                    alert('Erro ao alterar status do item: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao processar solicitação');
            }
        }

        // ==========================================
        // FUNÇÕES DO MODAL SUB. TOTAL
        // ==========================================
        
        function openSublimationModal() {
            const modal = document.getElementById('sublimation-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                // Reset form
                document.getElementById('sublimation-form').reset();
                document.getElementById('sub-total-pecas').textContent = '0';
                document.getElementById('sub-total-price').textContent = 'R$ 0,00';
                document.getElementById('sub_quantity').value = 0;
            }
        }
        
        function closeSublimationModal() {
            const modal = document.getElementById('sublimation-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
        
        async function loadSublimationAddons() {
            const typeSlug = document.getElementById('sublimation_type').value;
            const container = document.getElementById('sublimation-addons-container');
            
            if (!typeSlug) {
                container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>';
                return;
            }
            
            // Verificar cache
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
                    // Buscar preço base
                    calculateSublimationPrice();
                } else {
                    container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Nenhum adicional</p>';
                }
            } catch (error) {
                console.error('Erro ao carregar adicionais:', error);
                container.innerHTML = '<p class="text-sm text-red-500 col-span-full">Erro ao carregar</p>';
            }
        }
        
        function renderSublimationAddons(addons) {
            const container = document.getElementById('sublimation-addons-container');
            
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
        
        function calculateSublimationTotal() {
            const inputs = document.querySelectorAll('.sub-size-input');
            let total = 0;
            inputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById('sub-total-pecas').textContent = total;
            document.getElementById('sub_quantity').value = total;
            
            // Recalcular preço
            calculateSublimationPrice();
        }
        
        async function calculateSublimationPrice() {
            const typeSlug = document.getElementById('sublimation_type').value;
            const quantity = parseInt(document.getElementById('sub_quantity').value) || 0;
            
            if (!typeSlug || quantity === 0) {
                updateSublimationPreview();
                return;
            }
            
            try {
                const response = await fetch(`/api/sublimation-total/price/${typeSlug}/${quantity}`);
                const data = await response.json();
                
                if (data.success) {
                    let basePrice = parseFloat(data.price);
                    
                    // Somar adicionais
                    const selectedAddons = document.querySelectorAll('input[name="sublimation_addons[]"]:checked');
                    selectedAddons.forEach(addon => {
                        basePrice += parseFloat(addon.dataset.price);
                    });
                    
                    document.getElementById('sub_unit_price').value = basePrice.toFixed(2);
                    updateSublimationPreview();
                }
            } catch (error) {
                console.error('Erro ao buscar preço:', error);
            }
        }
        
        function updateSublimationPreview() {
            const unitPrice = parseFloat(document.getElementById('sub_unit_price').value) || 0;
            const quantity = parseInt(document.getElementById('sub_quantity').value) || 0;
            const total = unitPrice * quantity;
            document.getElementById('sub-total-price').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
        }
        
        // Form submit do modal SUB. TOTAL
        document.addEventListener('DOMContentLoaded', function() {
            // Listener global para alternar a cor do detalhe
            const detalheSelect = document.getElementById('detalhe');
            const detailColorContainer = document.getElementById('detail_color_container');
            const detailColorSelect = document.getElementById('detail_color');
            if (detalheSelect && detailColorContainer) {
                const toggleDetailColor = function() {
                    if (detalheSelect.value) {
                        detailColorContainer.style.display = 'block';
                    } else {
                        detailColorContainer.style.display = 'none';
                        if (detailColorSelect) detailColorSelect.value = '';
                    }
                };
                detalheSelect.addEventListener('change', toggleDetailColor);
                toggleDetailColor();
            }

            const sublimationForm = document.getElementById('sublimation-form');
            if (sublimationForm) {
                sublimationForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const quantity = parseInt(document.getElementById('sub_quantity').value) || 0;
                    if (quantity === 0) {
                        alert('Adicione pelo menos uma peça nos tamanhos.');
                        return;
                    }
                    
                    const artName = document.getElementById('sub_art_name').value.trim();
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
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
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
        });
</script>
@endpush
@endsection
