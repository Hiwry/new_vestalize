@extends('layouts.admin')

@push('styles')
    @include('budgets.wizard.partials.order-theme')
@endpush

@section('content')
@php
    $clientSession = session('budget.client', []);
    $client = null;
    if (!empty($clientSession['id'])) {
        $client = \App\Models\Client::find($clientSession['id']);
    }

    $items = session('budget_items', []);
    $customizations = session('budget_customizations', []);
    $itemsSubtotal = array_sum(array_map(function ($item) {
        return ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
    }, $items));
    $customizationsTotal = array_sum(array_map(function ($custom) {
        return $custom['final_price'] ?? 0;
    }, $customizations));
    $grandTotal = $itemsSubtotal + $customizationsTotal;
@endphp

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="bw-shell">
        <div class="bw-progress mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bw-step-badge rounded-xl flex items-center justify-center text-sm font-bold">4</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Confirmação do Orçamento</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 4 de 4</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">100%</div>
                </div>
            </div>
            <div class="w-full h-2.5 bw-progress-track">
                <div class="h-2.5 bw-progress-fill" style="width: 100%"></div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-4 shadow-sm">
                <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-2">Revise os campos antes de finalizar.</p>
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-6 items-start">
            <div class="space-y-6">
                <div class="bw-card overflow-hidden">
                    <div class="px-6 py-5 bw-card-header">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-[#7c3aed] flex items-center justify-center shadow-lg shadow-purple-500/25">
                                <i class="fa-solid fa-user text-white text-sm"></i>
                            </div>
                            Dados do cliente
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 pl-10">Confirme os dados de contato antes de gerar o orçamento.</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bw-muted-panel px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Nome</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white">{{ $clientSession['name'] ?? $client?->name ?? 'Não informado' }}</p>
                        </div>
                        <div class="bw-muted-panel px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Telefone</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white">{{ $clientSession['phone_primary'] ?? $client?->phone_primary ?? 'Não informado' }}</p>
                        </div>
                        <div class="bw-muted-panel px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">E-mail</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white">{{ $clientSession['email'] ?? $client?->email ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bw-card overflow-hidden">
                    <div class="px-6 py-5 bw-card-header">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-[#7c3aed] flex items-center justify-center shadow-lg shadow-purple-500/25">
                                <i class="fa-solid fa-layer-group text-white text-sm"></i>
                            </div>
                            Itens do orçamento
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 pl-10">Revise itens, personalizações e totais antes de confirmar.</p>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($items as $index => $item)
                            @php
                                $itemTotal = ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
                                $itemCustomizations = array_values(array_filter($customizations, function ($custom) use ($index) {
                                    return (int) ($custom['item_index'] ?? -1) === $index;
                                }));
                                $itemCustomTotal = array_sum(array_map(function ($custom) {
                                    return $custom['final_price'] ?? 0;
                                }, $itemCustomizations));
                            @endphp
                            <article class="bw-item-card p-5">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <span class="bw-chip">ITEM {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                            @if(count($itemCustomizations) > 0)
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">{{ count($itemCustomizations) }} personalização(ões)</span>
                                            @endif
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $item['print_type'] ?? 'Item Personalizado' }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                                            {{ $item['fabric'] ?? 'Sem tecido' }}
                                            @if(!empty($item['color']))
                                                • {{ $item['color'] }}
                                            @endif
                                            @if(!empty($item['model']))
                                                • {{ $item['model'] }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="lg:text-right">
                                        <p class="text-xs uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400">Total do item</p>
                                        <p class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">R$ {{ number_format($itemTotal + $itemCustomTotal, 2, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <div class="bw-muted-panel px-4 py-3">
                                        <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Quantidade</p>
                                        <p class="text-base font-bold text-gray-900 dark:text-white">{{ $item['quantity'] ?? 0 }} peça(s)</p>
                                    </div>
                                    <div class="bw-muted-panel px-4 py-3">
                                        <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Valor unitário</p>
                                        <p class="text-base font-bold text-gray-900 dark:text-white">R$ {{ number_format($item['unit_price'] ?? 0, 2, ',', '.') }}</p>
                                    </div>
                                    <div class="bw-muted-panel px-4 py-3">
                                        <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Gola</p>
                                        <p class="text-base font-bold text-gray-900 dark:text-white">{{ $item['gola'] ?? $item['collar'] ?? 'Sem gola extra' }}</p>
                                    </div>
                                    <div class="bw-muted-panel px-4 py-3">
                                        <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Detalhe</p>
                                        <p class="text-base font-bold text-gray-900 dark:text-white">{{ $item['detail'] ?? 'Sem detalhe' }}</p>
                                    </div>
                                </div>

                                @if(count($itemCustomizations) > 0)
                                    <div class="mt-4 rounded-2xl border border-purple-200 dark:border-purple-900/40 bg-purple-50/70 dark:bg-purple-900/10 px-4 py-4">
                                        <p class="text-[11px] uppercase tracking-wide font-semibold text-purple-600 dark:text-purple-300 mb-3">Personalizações do item</p>
                                        <div class="space-y-3">
                                            @foreach($itemCustomizations as $custom)
                                                <div class="rounded-xl border border-white/80 dark:border-slate-800 bg-white/80 dark:bg-slate-900/50 px-4 py-3">
                                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                                        <div>
                                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $custom['personalization_name'] ?? 'Personalização' }}</p>
                                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                                                {{ $custom['location'] ?? 'Sem local' }}
                                                                @if(!empty($custom['size']))
                                                                    • {{ $custom['size'] }}
                                                                @endif
                                                                • {{ $custom['quantity'] ?? 0 }} peça(s)
                                                            </p>
                                                        </div>
                                                        <p class="text-sm font-bold text-[#7c3aed] dark:text-[#a78bfa]">R$ {{ number_format($custom['final_price'] ?? 0, 2, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($item['notes']))
                                    <div class="mt-4 rounded-2xl border border-gray-200 dark:border-slate-700 bg-gray-50/80 dark:bg-slate-800/60 px-4 py-3">
                                        <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Observações do item</p>
                                        <p class="text-sm text-gray-700 dark:text-slate-300">{{ $item['notes'] }}</p>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside>
                <div class="bw-card bw-sidebar-card overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-800">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Resumo final</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Inclua observações e finalize o orçamento.</p>
                    </div>

                    <form method="POST" action="{{ route('budget.finalize') }}" id="finalize-form" class="p-5 space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Observações gerais</label>
                            <textarea name="observations" rows="3" placeholder="Observações gerais sobre o orçamento..." class="w-full rounded-xl border border-gray-300 dark:border-slate-600 px-4 py-3 text-sm">{{ old('observations') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                Observações do vendedor
                            </label>
                            <textarea name="admin_notes" rows="5" placeholder="Prazo, condições de pagamento, acréscimos por tamanho, observações comerciais..." class="w-full rounded-xl border border-gray-300 dark:border-slate-600 px-4 py-3 text-sm">{{ old('admin_notes') }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">Essas informações aparecerão no PDF do orçamento.</p>
                        </div>

                        <div class="rounded-2xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 px-4 py-4 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-slate-400">Subtotal itens</span>
                                <span class="font-semibold text-gray-900 dark:text-white">R$ {{ number_format($itemsSubtotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-slate-400">Personalizações</span>
                                <span class="font-semibold text-gray-900 dark:text-white">R$ {{ number_format($customizationsTotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-purple-200 dark:border-purple-800/40 pt-3 flex justify-between items-center">
                                <div>
                                    <p class="text-xs uppercase tracking-wide font-semibold text-purple-600 dark:text-purple-300">Total do orçamento</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Válido por 15 dias</p>
                                </div>
                                <p class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">R$ {{ number_format($grandTotal, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <button type="submit" id="finalize-btn" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bw-success-btn font-semibold text-sm">
                            <span id="finalize-text">Finalizar orçamento</span>
                            <span id="finalize-loading" class="hidden"><i class="fa-solid fa-spinner fa-spin"></i> Processando...</span>
                        </button>

                        <a href="{{ route('budget.customization') }}" class="inline-flex items-center justify-center gap-2 w-full px-5 py-3 bw-ghost-btn font-semibold text-sm">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            Voltar para personalização
                        </a>
                    </form>
                </div>
            </aside>
        </div>
    </section>
</div>

<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/55" onclick="closeConfirmModal()"></div>
    <div class="relative bw-card max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Finalizar orçamento?</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">O orçamento será salvo e ficará disponível para PDF e conversão em pedido.</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeConfirmModal()" class="flex-1 px-4 py-2.5 bw-ghost-btn font-medium text-sm">
                Cancelar
            </button>
            <button type="button" onclick="confirmFinalize()" class="flex-1 px-4 py-2.5 bw-success-btn font-medium text-sm">
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
        const finalizeForm = document.getElementById('finalize-form');
        if (finalizeForm && !finalizeForm.dataset.listenerAttached) {
            finalizeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!formSubmitted) {
                    openConfirmModal();
                }
            });
            finalizeForm.dataset.listenerAttached = 'true';
        }
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

        if (btn) btn.disabled = true;
        if (text) text.classList.add('hidden');
        if (loading) loading.classList.remove('hidden');

        document.getElementById('finalize-form').submit();
    }
    window.confirmFinalize = confirmFinalize;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConfirmPage);
    } else {
        initConfirmPage();
    }
})();
</script>
@endpush
@endsection
