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


    .dark.avento-theme .ow-card, .dark.avento-theme .ow-progress, .dark.avento-theme .ow-field-panel, .dark.avento-theme .personalization-option, .dark.avento-theme .glass-card {
        background-color: var(--sh-card-bg) !important;
        box-shadow: none !important;
        background-image: none !important;
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

    /* Personalization card adjustments */
    .personalization-option {
        background: var(--sh-card-bg) !important;
        border: 1px solid var(--sh-card-border) !important;
        box-shadow: var(--sh-card-shadow) !important;
        border-radius: 20px !important;
        position: relative;
        transition: all 0.3s ease !important;
    }
    
    .personalization-option.selected {
        border-color: var(--sh-accent) !important;
        background-color: color-mix(in srgb, var(--sh-accent) 5%, var(--sh-card-bg)) !important;
        transform: translateY(-2px);
    }

    .selected-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--sh-accent);
        color: white;
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: none;
        z-index: 10;
    }

    .personalization-option.selected .selected-badge {
        display: block;
    }
    
    .dark .personalization-option {
        background: var(--sh-card-bg) !important;
    }

    .glass-card { background: var(--sh-card-bg) !important; border-color: var(--sh-card-border) !important; }

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
@endpush

@section('content')
<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="ow-shell">
        <!-- Top Bar (Estilo Sales Hub) -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <span class="ow-step-badge">2</span>
                <div>
                    <h1 class="sh-title">Tipo de Personalização</h1>
                    <p class="sh-subtitle">Etapa 2 de 5 • Selecione os serviços desejados</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Passo Atual</div>
                <div class="text-2xl font-black text-[#7c3aed]">40%</div>
            </div>
        </div>

        <!-- Progress Widget -->
        <div class="ow-progress p-4 mb-8">
            <div class="w-full bg-gray-100 dark:bg-slate-800/50 rounded-full h-2">
                <div class="ow-progress-fill h-2 rounded-full transition-all duration-700" style="width: 40%"></div>
            </div>
        </div>

        <!-- Section Header (Sub-título do Hub) -->
        <div class="ow-card overflow-hidden mb-8">
            <div class="px-6 py-5 ow-card-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#7c3aed] rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-paintbrush text-white text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Escolha o Tipo de Personalização
                    </h2>
                </div>
            </div>
        </div>

        <!-- Client Info Card -->
        @if(session('wizard.client'))
        <div class="glass-card rounded-2xl border border-gray-100 dark:border-gray-700 p-4 mb-8 animate-fade-in-up">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#7c3aed] rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-user text-white"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-900 dark:text-gray-100">{{ session('wizard.client.name') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ session('wizard.client.phone_primary') ?? 'Sem telefone' }}</p>
                </div>
                <a href="{{ route('orders.wizard.start') }}" class="ml-auto text-sm text-[#7c3aed] hover:text-[#7c3aed] dark:text-[#7c3aed] font-bold">
                    Alterar cliente
                </a>
            </div>
        </div>
        @endif

        <!-- Personalization Types Grid - Multi-select -->
        <form id="personalization-form" action="{{ route('orders.wizard.items') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" id="personalization-grid">
                
                <!-- Sub. Local -->
                <label class="personalization-option group rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="sub_local" class="hidden personalization-checkbox">
                    <div class="selected-badge">Selecionado</div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-fire text-2xl sm:text-3xl text-[#7c3aed] dark:text-[#7c3aed]"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Sublimação Local</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Sublimação em áreas específicas da peça (gola, manga, etc.)</p>
                    </div>
                </label>

                <!-- Serigrafia -->
                <label class="personalization-option group rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer animate-fade-in-up delay-200">
                    <input type="checkbox" name="types[]" value="serigrafia" class="hidden personalization-checkbox">
                    <div class="selected-badge">Selecionado</div>
                    <div class="flex flex-col items-center text-center relative">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-fill-drip text-2xl sm:text-3xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Serigrafia</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Impressão por tela em uma ou mais cores</p>
                    </div>
                </label>

                <!-- DTF -->
                <label class="personalization-option group rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer animate-fade-in-up delay-300">
                    <input type="checkbox" name="types[]" value="dtf" class="hidden personalization-checkbox">
                    <div class="selected-badge">Selecionado</div>
                    <div class="flex flex-col items-center text-center relative">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-print text-2xl sm:text-3xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">DTF</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Direct to Film - Impressão digital com transfer</p>
                    </div>
                </label>

                <!-- Bordado -->
                <label class="personalization-option group rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="bordado" class="hidden personalization-checkbox">
                    <div class="selected-badge">Selecionado</div>
                    <div class="flex flex-col items-center text-center relative">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-pink-100 dark:bg-pink-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-pen-nib text-2xl sm:text-3xl text-pink-600 dark:text-pink-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Bordado</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Bordado computadorizado ou manual</p>
                    </div>
                </label>

                <!-- Emborrachado -->
                <label class="personalization-option group rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer animate-fade-in-up delay-200">
                    <input type="checkbox" name="types[]" value="emborrachado" class="hidden personalization-checkbox">
                    <div class="selected-badge">Selecionado</div>
                    <div class="flex flex-col items-center text-center relative">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-cube text-2xl sm:text-3xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Emborrachado</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Aplicação de etiquetas e detalhes emborrachados</p>
                    </div>
                </label>

                <!-- Lisas -->
                <label class="personalization-option group rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer animate-fade-in-up delay-300">
                    <input type="checkbox" name="types[]" value="lisas" class="hidden personalization-checkbox">
                    <div class="selected-badge">Selecionado</div>
                    <div class="flex flex-col items-center text-center relative">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-star text-2xl sm:text-3xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Lisas</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Peças sem personalização (apenas costura)</p>
                    </div>
                </label>

                <!-- Sub. Total -->
                <label class="personalization-option group rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer sm:col-span-2 lg:col-span-1 animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="sub_total" class="hidden personalization-checkbox">
                    <div class="selected-badge">Selecionado</div>
                    <div class="flex flex-col items-center text-center relative">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-image text-2xl sm:text-3xl text-[#7c3aed] dark:text-[#7c3aed]"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Sublimação Total</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Sublimação em toda a peça (full print)</p>
                    </div>
                </label>

            </div>

            <!-- Selected Count Badge -->
            <div id="selection-info" class="hidden mt-6 text-center animate-fade-in-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-full">
                    <i class="fa-solid fa-check-double text-[#7c3aed] dark:text-[#7c3aed]"></i>
                    <span class="text-sm font-bold text-[#7c3aed] dark:text-[#7c3aed]">
                        <span id="selection-count">0</span> tipo(s) selecionado(s)
                    </span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 animate-fade-in-up">
                <a href="{{ route('orders.wizard.start') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-bold text-sm">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                    Voltar
                </a>
                
                <button type="submit" id="continue-btn" disabled
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-[#7c3aed] text-white rounded-xl font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    Continuar
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
            </div>
        </form>
    </section>
</div>

    </div>
</div>

@push('page-scripts')
<script>
(function() {
    const checkboxes = document.querySelectorAll('.personalization-checkbox');
    const continueBtn = document.getElementById('continue-btn');
    const selectionInfo = document.getElementById('selection-info');
    const selectionCount = document.getElementById('selection-count');
    const options = document.querySelectorAll('.personalization-option');
    
    function updateSelection() {
        const checked = document.querySelectorAll('.personalization-checkbox:checked');
        const count = checked.length;
        
        // Update count display
        selectionCount.textContent = count;
        selectionInfo.classList.toggle('hidden', count === 0);
        
        // Enable/disable button
        continueBtn.disabled = count === 0;
        
        // Update visual state of cards
        options.forEach(option => {
            const checkbox = option.querySelector('.personalization-checkbox');
            
            if (checkbox.checked) {
                option.classList.add('selected');
                option.classList.remove('border-gray-200', 'dark:border-gray-700');
            } else {
                option.classList.remove('selected');
                option.classList.add('border-gray-200', 'dark:border-gray-700');
            }
        });
    }
    
    // Add click handler for each option
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            const checkbox = this.querySelector('.personalization-checkbox');
            checkbox.checked = !checkbox.checked;
            updateSelection();
        });
    });
    
    // Handle form submission
    document.getElementById('personalization-form').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.personalization-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Selecione pelo menos um tipo de personalização.');
        }
    });
})();
</script>
@endpush
@endsection
