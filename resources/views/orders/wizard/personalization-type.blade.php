@extends('layouts.admin')

@push('styles')
<style>
    /* Realça os cards no dark mode */
    .personalization-option {
        position: relative;
        overflow: hidden;
        background: linear-gradient(145deg, #f9fafb 0%, #ffffff 100%);
        border: 1px solid rgba(124, 58, 237, 0.14);
        box-shadow: 0 10px 30px -18px rgba(17, 24, 39, 0.35);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, background 0.25s ease;
    }
    .personalization-option:hover {
        transform: translateY(-4px);
        border-color: rgba(124, 58, 237, 0.45);
        box-shadow: 0 22px 48px -20px rgba(124, 58, 237, 0.45), 0 14px 30px -22px rgba(0, 0, 0, 0.55);
    }
    .personalization-option .check-indicator {
        box-shadow: 0 8px 18px -8px rgba(124, 58, 237, 0.55);
    }
    .personalization-option .icon-bubble {
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25), 0 10px 24px -14px rgba(124, 58, 237, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.14);
    }
    .dark .personalization-option {
        background: linear-gradient(165deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02)) padding-box,
                    radial-gradient(circle at 25% 20%, rgba(124, 58, 237, 0.20), transparent 48%) border-box,
                    #0d1728;
        border: 1px solid rgba(148, 163, 184, 0.32);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08),
                    0 24px 56px -28px rgba(15, 23, 42, 0.95),
                    0 20px 48px -32px rgba(124, 58, 237, 0.45);
    }
    .dark .personalization-option:hover {
        border-color: rgba(124, 58, 237, 0.75);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12),
                    0 30px 70px -32px rgba(124, 58, 237, 0.65),
                    0 24px 56px -36px rgba(0, 0, 0, 0.9);
    }
    .dark .personalization-option .icon-bubble {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.08), rgba(124, 58, 237, 0.15));
        border-color: rgba(124, 58, 237, 0.35);
        box-shadow: 0 12px 28px -18px rgba(124, 58, 237, 0.75);
    }
</style>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-0">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#7c3aed] text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]">2</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Tipo de Personalização</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 2 de 6</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">33%</div>
                </div>
            </div>
            <div class="w-full bg-white dark:bg-slate-800/80 rounded-full h-2.5 shadow-inner border border-gray-200 dark:border-slate-700">
                <div class="bg-[#7c3aed] h-2.5 rounded-full transition-all duration-500 ease-out" style="width: 33%"></div>
            </div>
        </div>

        <!-- Header -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 mb-8 shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-100 dark:border-slate-700 backdrop-filter blur-16">
            <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                     <div class="w-8 h-8 bg-[#7c3aed] rounded-lg flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]">
                        <i class="fa-solid fa-paintbrush text-white text-sm"></i>
                    </div>
                    Escolha o <span class="bg-gradient-to-r from-[#7c3aed] to-purple-600 bg-clip-text text-transparent">Tipo de Personalização</span>
                </h1>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 pl-10">Selecione um ou mais tipos de serviço para este pedido</p>
        </div>

        <!-- Client Info Card -->
        @if(session('wizard.client'))
        <div class="glass-card rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-4 mb-8 animate-fade-in-up">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7c3aed] to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-[#7c3aed]/30">
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
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer hover:shadow-lg animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="sub_local" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center">
                        <div class="check-indicator absolute top-3 right-3 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-fire text-2xl sm:text-3xl text-[#7c3aed] dark:text-[#7c3aed]"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Sublimação Local</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Sublimação em áreas específicas da peça (gola, manga, etc.)</p>
                    </div>
                </label>

                <!-- Serigrafia -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer hover:shadow-lg animate-fade-in-up delay-200">
                    <input type="checkbox" name="types[]" value="serigrafia" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-fill-drip text-2xl sm:text-3xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Serigrafia</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Impressão por tela em uma ou mais cores</p>
                    </div>
                </label>

                <!-- DTF -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer hover:shadow-lg animate-fade-in-up delay-300">
                    <input type="checkbox" name="types[]" value="dtf" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-print text-2xl sm:text-3xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">DTF</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Direct to Film - Impressão digital com transfer</p>
                    </div>
                </label>

                <!-- Bordado -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer hover:shadow-lg animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="bordado" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-pink-100 dark:bg-pink-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-pen-nib text-2xl sm:text-3xl text-pink-600 dark:text-pink-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Bordado</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Bordado computadorizado ou manual</p>
                    </div>
                </label>

                <!-- Emborrachado -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer hover:shadow-lg animate-fade-in-up delay-200">
                    <input type="checkbox" name="types[]" value="emborrachado" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-cube text-2xl sm:text-3xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Emborrachado</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Aplicação de etiquetas e detalhes emborrachados</p>
                    </div>
                </label>

                <!-- Lisas -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer hover:shadow-lg animate-fade-in-up delay-300">
                    <input type="checkbox" name="types[]" value="lisas" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
                            <i class="fa-solid fa-star text-2xl sm:text-3xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Lisas</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Peças sem personalização (apenas costura)</p>
                    </div>
                </label>

                <!-- Sub. Total -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] transition-all cursor-pointer hover:shadow-lg sm:col-span-2 lg:col-span-1 animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="sub_total" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-cyan-100 to-purple-100 dark:from-cyan-900/30 dark:to-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform icon-bubble">
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
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-[#7c3aed] to-purple-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-[#7c3aed]/30 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-xl transition-all">
                    Continuar
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>

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
            const indicator = option.querySelector('.check-indicator');
            
            if (checkbox.checked) {
                option.classList.add('border-[#7c3aed]', 'ring-2', 'ring-[#7c3aed]/20');
                option.classList.remove('border-gray-200', 'dark:border-gray-700');
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
            } else {
                option.classList.remove('border-[#7c3aed]', 'ring-2', 'ring-[#7c3aed]/20');
                option.classList.add('border-gray-200', 'dark:border-gray-700');
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
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
