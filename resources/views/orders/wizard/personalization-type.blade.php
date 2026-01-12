@extends('layouts.admin')

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-0">
        
        <!-- Progress Bar -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Etapa 2 de 6</span>
                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">Tipo de Personalização</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 h-2 rounded-full transition-all duration-300" style="width: 33%"></div>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-8 animate-fade-in-up">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-gray-100 mb-2">
                Escolha o <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Tipo de Personalização</span>
            </h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Selecione um ou mais tipos de serviço para este pedido</p>
        </div>

        <!-- Client Info Card -->
        @if(session('wizard.client'))
        <div class="glass-card rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-4 mb-8 animate-fade-in-up">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-user text-white"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-900 dark:text-gray-100">{{ session('wizard.client.name') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ session('wizard.client.phone_primary') ?? 'Sem telefone' }}</p>
                </div>
                <a href="{{ route('orders.wizard.start') }}" class="ml-auto text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-bold">
                    Alterar cliente
                </a>
            </div>
        </div>
        @endif

        <!-- Personalization Types Grid - Multi-select -->
        <form id="personalization-form" action="{{ route('orders.wizard.items') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" id="personalization-grid">
                
                <!-- Sub. Local -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg hover-lift animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="sub_local" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center">
                        <div class="check-indicator absolute top-3 right-3 w-6 h-6 rounded-full bg-indigo-600 text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-fire text-2xl sm:text-3xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Sublimação Local</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Sublimação em áreas específicas da peça (gola, manga, etc.)</p>
                    </div>
                </label>

                <!-- Serigrafia -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg hover-lift animate-fade-in-up delay-200">
                    <input type="checkbox" name="types[]" value="serigrafia" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-indigo-600 text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-fill-drip text-2xl sm:text-3xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Serigrafia</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Impressão por tela em uma ou mais cores</p>
                    </div>
                </label>

                <!-- DTF -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg hover-lift animate-fade-in-up delay-300">
                    <input type="checkbox" name="types[]" value="dtf" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-indigo-600 text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-print text-2xl sm:text-3xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">DTF</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Direct to Film - Impressão digital com transfer</p>
                    </div>
                </label>

                <!-- Bordado -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg hover-lift animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="bordado" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-indigo-600 text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-pink-100 dark:bg-pink-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-pen-nib text-2xl sm:text-3xl text-pink-600 dark:text-pink-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Bordado</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Bordado computadorizado ou manual</p>
                    </div>
                </label>

                <!-- Emborrachado -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg hover-lift animate-fade-in-up delay-200">
                    <input type="checkbox" name="types[]" value="emborrachado" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-indigo-600 text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-cube text-2xl sm:text-3xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Emborrachado</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Aplicação de etiquetas e detalhes emborrachados</p>
                    </div>
                </label>

                <!-- Lisas -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg hover-lift animate-fade-in-up delay-300">
                    <input type="checkbox" name="types[]" value="lisas" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-indigo-600 text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-star text-2xl sm:text-3xl text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Lisas</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Peças sem personalização (apenas costura)</p>
                    </div>
                </label>

                <!-- Sub. Total -->
                <label class="personalization-option group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-4 sm:p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg hover-lift sm:col-span-2 lg:col-span-1 animate-fade-in-up delay-100">
                    <input type="checkbox" name="types[]" value="sub_total" class="hidden personalization-checkbox">
                    <div class="flex flex-col items-center text-center relative">
                        <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-indigo-600 text-white items-center justify-center hidden">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-cyan-100 to-indigo-100 dark:from-cyan-900/30 dark:to-indigo-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-image text-2xl sm:text-3xl text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Sublimação Total</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Sublimação em toda a peça (full print)</p>
                    </div>
                </label>

            </div>

            <!-- Selected Count Badge -->
            <div id="selection-info" class="hidden mt-6 text-center animate-fade-in-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-full">
                    <i class="fa-solid fa-check-double text-indigo-600 dark:text-indigo-400"></i>
                    <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300">
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
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/30 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-xl hover:scale-[1.02] transition-all">
                    Continuar
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                option.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-500/20');
                option.classList.remove('border-gray-200', 'dark:border-gray-700');
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
            } else {
                option.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-500/20');
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
});
</script>
@endpush
@endsection
