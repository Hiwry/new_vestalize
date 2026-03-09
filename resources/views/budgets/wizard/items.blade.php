@extends('layouts.admin')

@push('styles')
    @include('budgets.wizard.partials.order-theme')
    <style>
        #item-modal .budget-modal-shell {
            background: var(--bw-card-bg);
            border: 1px solid var(--bw-card-border);
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.28);
        }

        #item-modal .budget-modal-header,
        #item-modal .budget-modal-footer,
        #item-modal .budget-modal-stepper {
            background: color-mix(in srgb, var(--bw-card-bg) 94%, var(--bw-accent) 6%);
            border-color: var(--bw-card-border);
        }

        #item-modal .budget-modal-body {
            background: var(--bw-card-bg);
        }

        #item-modal .budget-modal-overlay {
            backdrop-filter: blur(10px);
        }
    </style>
@endpush

@section('content')
@php
    $itemsTotal = array_sum(array_map(function ($item) {
        return ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 0);
    }, $items));
    $itemCount = count($items);
    $piecesCount = array_sum(array_map(function ($item) {
        return (int) ($item['quantity'] ?? 0);
    }, $items));
    $averageTicket = $itemCount > 0 ? $itemsTotal / $itemCount : 0;
@endphp

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-24 md:pb-6">
    <section class="bw-shell">
        <div class="bw-progress mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bw-step-badge rounded-xl flex items-center justify-center text-sm font-bold">2</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Itens do Orçamento</span>
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

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-6 items-start">
            <div class="space-y-6">
                @if(session('success'))
                    <div class="rounded-2xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 shadow-sm">
                        <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 shadow-sm">
                        <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-4 shadow-sm">
                        <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-2">Revise os campos antes de continuar.</p>
                        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bw-card overflow-hidden">
                    <div class="px-6 py-5 bw-card-header flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-[#7c3aed] flex items-center justify-center shadow-lg shadow-purple-500/25">
                                    <i class="fa-solid fa-shirt text-white text-sm"></i>
                                </div>
                                Monte os itens do orçamento
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 pl-10">Adicione as peças, revise o subtotal e avance para a etapa de personalização.</p>
                        </div>
                        <button type="button"
                                onclick="openItemModal()"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 bw-primary-btn font-semibold text-sm">
                            <i class="fa-solid fa-plus text-xs"></i>
                            Adicionar item
                        </button>
                    </div>

                    <div class="p-6">
                        @if(empty($items))
                            <div class="bw-empty-state text-center py-14 px-6">
                                <div class="w-16 h-16 mx-auto rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-box-open text-2xl text-[#7c3aed]"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhum item configurado ainda</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400 max-w-md mx-auto mb-6">Abra o assistente para montar o primeiro item e começar a compor o orçamento.</p>
                                <button type="button"
                                        onclick="openItemModal()"
                                        class="inline-flex items-center gap-2 px-5 py-3 bw-primary-btn font-semibold text-sm">
                                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                                    Configurar primeiro item
                                </button>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($items as $index => $item)
                                    @php
                                        $itemTotal = ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 0);
                                        $collarValue = $item['gola'] ?? $item['collar'] ?? '';
                                    @endphp
                                    <article class="bw-item-card p-5">
                                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                            <div class="space-y-3">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="bw-chip">ITEM {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-slate-400">Configuração principal</span>
                                                </div>
                                                <div>
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
                                            </div>

                                            <div class="lg:text-right">
                                                <p class="text-xs uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400">Total estimado</p>
                                                <p class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">R$ {{ number_format($itemTotal, 2, ',', '.') }}</p>
                                                <button type="button"
                                                        onclick="removeItem({{ $index }})"
                                                        class="mt-3 inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-sm font-medium">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                    Remover
                                                </button>
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
                                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $collarValue ?: 'Sem gola extra' }}</p>
                                            </div>
                                            <div class="bw-muted-panel px-4 py-3">
                                                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Detalhe</p>
                                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $item['detail'] ?: 'Sem detalhe' }}</p>
                                            </div>
                                        </div>

                                        @if(!empty($item['notes']))
                                            <div class="mt-4 rounded-2xl border border-gray-200 dark:border-slate-700 bg-gray-50/80 dark:bg-slate-800/60 px-4 py-3">
                                                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Observações</p>
                                                <p class="text-sm text-gray-700 dark:text-slate-300">{{ $item['notes'] }}</p>
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 xl:hidden">
                    <a href="{{ route('budget.client.show') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bw-ghost-btn font-semibold text-sm">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Voltar para cliente
                    </a>

                    @if(!empty($items))
                        <form method="POST" action="{{ route('budget.items') }}" class="w-full sm:w-auto">
                            @csrf
                            <input type="hidden" name="action" value="continue">
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bw-success-btn font-semibold text-sm">
                                Continuar
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <aside class="space-y-6">
                <div class="bw-card bw-sidebar-card overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-800">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Resumo do orçamento</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Volume e valores antes da etapa de personalização.</p>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bw-stat-card p-4">
                                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Itens</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $itemCount }}</p>
                            </div>
                            <div class="bw-stat-card p-4">
                                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Peças</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $piecesCount }}</p>
                            </div>
                            <div class="bw-stat-card p-4">
                                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Ticket médio</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">R$ {{ number_format($averageTicket, 2, ',', '.') }}</p>
                            </div>
                            <div class="bw-stat-card p-4">
                                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 dark:text-slate-400 mb-1">Subtotal</p>
                                <p class="text-lg font-bold text-[#7c3aed] dark:text-[#a78bfa]">R$ {{ number_format($itemsTotal, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                            @forelse($items as $index => $item)
                                <div class="rounded-2xl border border-gray-200 dark:border-slate-700 bg-gray-50/70 dark:bg-slate-800/70 px-4 py-3">
                                    <div class="flex justify-between items-start gap-3">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">Item {{ $index + 1 }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">{{ $item['print_type'] ?? 'Item' }}</p>
                                        </div>
                                        <span class="text-sm font-bold text-[#7c3aed] dark:text-[#a78bfa]">
                                            R$ {{ number_format(($item['unit_price'] ?? 0) * ($item['quantity'] ?? 0), 2, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="mt-3 space-y-1 text-xs text-gray-500 dark:text-slate-400">
                                        @if(!empty($item['fabric']))
                                            <div class="flex justify-between gap-3">
                                                <span>Tecido</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right">{{ $item['fabric'] }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between gap-3">
                                            <span>Quantidade</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $item['quantity'] ?? 0 }} pç</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-gray-300 dark:border-slate-700 px-4 py-8 text-center">
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Os itens adicionados aparecerão aqui.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="rounded-2xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 px-4 py-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs uppercase tracking-wide font-semibold text-purple-600 dark:text-purple-300">Total atual</p>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Antes das personalizações</p>
                                </div>
                                <p class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">R$ {{ number_format($itemsTotal, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <a href="{{ route('budget.client.show') }}" class="hidden xl:inline-flex items-center justify-center gap-2 w-full px-5 py-3 bw-ghost-btn font-semibold text-sm">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            Voltar para cliente
                        </a>

                        @if(!empty($items))
                            <form method="POST" action="{{ route('budget.items') }}">
                                @csrf
                                <input type="hidden" name="action" value="continue">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bw-success-btn font-semibold text-sm">
                                    Seguir para personalização
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>
</div>

@include('budgets.wizard.partials.items-modal')
@endsection

@push('scripts')
    @include('budgets.wizard.partials.items-script')
@endpush
