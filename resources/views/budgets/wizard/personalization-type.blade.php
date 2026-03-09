@extends('layouts.admin')

@push('styles')
    @include('budgets.wizard.partials.order-theme')
    <style>
        .budget-personalization-option {
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #f9fafb 0%, #ffffff 100%);
            border: 1px solid rgba(124, 58, 237, 0.14);
            box-shadow: 0 10px 30px -18px rgba(17, 24, 39, 0.35);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .budget-personalization-option:hover {
            transform: translateY(-4px);
            border-color: rgba(124, 58, 237, 0.45);
            box-shadow: 0 22px 48px -20px rgba(124, 58, 237, 0.3), 0 14px 30px -22px rgba(0, 0, 0, 0.18);
        }

        .dark .budget-personalization-option {
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02)) padding-box,
                        radial-gradient(circle at 25% 20%, rgba(124, 58, 237, 0.2), transparent 48%) border-box,
                        #0d1728;
            border: 1px solid rgba(148, 163, 184, 0.32);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08),
                        0 24px 56px -28px rgba(15, 23, 42, 0.95),
                        0 20px 48px -32px rgba(124, 58, 237, 0.32);
        }
    </style>
@endpush

@section('content')
@php
    $options = [
        ['key' => 'sub_local', 'label' => 'Sublimação Local', 'description' => 'Aplicação pontual em áreas da peça.', 'icon' => 'fa-fire', 'bubble' => 'bg-purple-100 dark:bg-purple-900/30', 'iconColor' => 'text-[#7c3aed]'],
        ['key' => 'serigrafia', 'label' => 'Serigrafia', 'description' => 'Impressão por tela com uma ou mais cores.', 'icon' => 'fa-fill-drip', 'bubble' => 'bg-purple-100 dark:bg-purple-900/30', 'iconColor' => 'text-purple-600 dark:text-purple-400'],
        ['key' => 'dtf', 'label' => 'DTF', 'description' => 'Transfer digital com ótima definição.', 'icon' => 'fa-print', 'bubble' => 'bg-orange-100 dark:bg-orange-900/30', 'iconColor' => 'text-orange-600 dark:text-orange-400'],
        ['key' => 'bordado', 'label' => 'Bordado', 'description' => 'Aplicação bordada para acabamento premium.', 'icon' => 'fa-pen-nib', 'bubble' => 'bg-pink-100 dark:bg-pink-900/30', 'iconColor' => 'text-pink-600 dark:text-pink-400'],
        ['key' => 'emborrachado', 'label' => 'Emborrachado', 'description' => 'Etiquetas e detalhes em alto relevo.', 'icon' => 'fa-cube', 'bubble' => 'bg-green-100 dark:bg-green-900/30', 'iconColor' => 'text-green-600 dark:text-green-400'],
        ['key' => 'lisas', 'label' => 'Lisas', 'description' => 'Itens sem personalização, apenas costura.', 'icon' => 'fa-star', 'bubble' => 'bg-gray-100 dark:bg-gray-700', 'iconColor' => 'text-gray-600 dark:text-gray-300'],
        ['key' => 'sub_total', 'label' => 'Sublimação Total', 'description' => 'Peça toda estampada, estilo full print.', 'icon' => 'fa-image', 'bubble' => 'bg-gradient-to-br from-cyan-100 to-purple-100 dark:from-cyan-900/30 dark:to-purple-900/30', 'iconColor' => 'text-[#7c3aed]'],
    ];
@endphp

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="bw-shell">
        <div class="bw-progress mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bw-step-badge rounded-xl flex items-center justify-center text-sm font-bold">2</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Tipos de Personalização</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 2 de 4</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">50%</div>
                </div>
            </div>
            <div class="w-full h-2.5 bw-progress-track">
                <div class="h-2.5 bw-progress-fill" style="width: 50%"></div>
            </div>
        </div>

        <div class="bw-card overflow-hidden">
            <div class="px-6 py-5 bw-card-header">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#7c3aed] flex items-center justify-center shadow-lg shadow-purple-500/25">
                        <i class="fa-solid fa-paintbrush text-white text-sm"></i>
                    </div>
                    Escolha como este orçamento será personalizado
                </h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 pl-10">Selecione um ou mais serviços para direcionar a configuração dos itens.</p>
            </div>

            <div class="p-6">
                @if(session('budget.client.name'))
                    <div class="bw-panel px-5 py-4 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#7c3aed] to-purple-600 flex items-center justify-center text-white font-bold shadow-lg shadow-purple-500/30">
                            {{ strtoupper(substr(session('budget.client.name'), 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">{{ session('budget.client.name') }}</p>
                            <p class="text-sm text-gray-500 dark:text-slate-400">{{ session('budget.client.phone_primary') ?: 'Sem telefone cadastrado' }}</p>
                        </div>
                        <a href="{{ route('budget.start') }}" class="sm:ml-auto text-sm font-semibold text-[#7c3aed] hover:text-purple-700 dark:text-purple-300">
                            Alterar cliente
                        </a>
                    </div>
                @endif

                <form id="personalization-form" method="POST" action="{{ route('budget.personalization-type') }}">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($options as $option)
                            <label class="budget-personalization-option group rounded-2xl p-5 cursor-pointer border-2 border-gray-200 dark:border-slate-700">
                                <input type="checkbox"
                                       name="types[]"
                                       value="{{ $option['key'] }}"
                                       class="hidden personalization-checkbox"
                                       {{ in_array($option['key'], $selectedTypes ?? []) ? 'checked' : '' }}>
                                <div class="flex flex-col items-center text-center relative">
                                    <div class="check-indicator absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#7c3aed] text-white items-center justify-center hidden shadow-lg shadow-purple-500/30">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </div>
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform {{ $option['bubble'] }}">
                                        <i class="fa-solid {{ $option['icon'] }} text-2xl sm:text-3xl {{ $option['iconColor'] }}"></i>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">{{ $option['label'] }}</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ $option['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div id="selection-info" class="hidden mt-6 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-full">
                            <i class="fa-solid fa-check-double text-[#7c3aed]"></i>
                            <span class="text-sm font-bold text-[#7c3aed] dark:text-purple-300">
                                <span id="selection-count">0</span> tipo(s) selecionado(s)
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <a href="{{ route('budget.start') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bw-ghost-btn font-bold text-sm">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                            Voltar
                        </a>

                        <button type="submit"
                                id="continue-btn"
                                disabled
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bw-primary-btn font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Continuar
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@push('page-scripts')
<script>
(function() {
    const checkboxes = document.querySelectorAll('.personalization-checkbox');
    const continueBtn = document.getElementById('continue-btn');
    const selectionInfo = document.getElementById('selection-info');
    const selectionCount = document.getElementById('selection-count');
    const options = document.querySelectorAll('.budget-personalization-option');

    function updateSelection() {
        const checked = document.querySelectorAll('.personalization-checkbox:checked');
        const count = checked.length;

        selectionCount.textContent = count;
        selectionInfo.classList.toggle('hidden', count === 0);
        continueBtn.disabled = count === 0;

        options.forEach(option => {
            const checkbox = option.querySelector('.personalization-checkbox');
            const indicator = option.querySelector('.check-indicator');

            if (checkbox.checked) {
                option.classList.add('border-[#7c3aed]', 'ring-2', 'ring-[#7c3aed]/20');
                option.classList.remove('border-gray-200', 'dark:border-slate-700');
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
            } else {
                option.classList.remove('border-[#7c3aed]', 'ring-2', 'ring-[#7c3aed]/20');
                option.classList.add('border-gray-200', 'dark:border-slate-700');
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
            }
        });
    }

    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const checkbox = this.querySelector('.personalization-checkbox');
            checkbox.checked = !checkbox.checked;
            updateSelection();
        });
    });

    document.getElementById('personalization-form').addEventListener('submit', function(e) {
        if (document.querySelectorAll('.personalization-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Selecione pelo menos um tipo de personalização.');
        }
    });

    updateSelection();
})();
</script>
@endpush
@endsection
