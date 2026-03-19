@extends('layouts.admin')

@section('content')
<style>
    /* Light mode variables (default) */
    .stp-page-shell {
        --stp-surface-from: #f3f4f8;
        --stp-surface-to: #eceff4;
        --stp-surface-border: #d8dce6;
        --stp-text-primary: #0f172a;
        --stp-text-secondary: #64748b;
        --stp-card-bg: #ffffff;
        --stp-soft-bg: #eef4ff;
        --stp-input-bg: #f8fafc;
        --stp-card-border: rgba(59, 130, 246, 0.12);
        --stp-accent: #7c3aed;
        --stp-accent-strong: #6d28d9;
        --stp-danger-bg: rgba(244, 63, 94, 0.08);
        --stp-danger-border: rgba(244, 63, 94, 0.18);
        --stp-danger-text: #be123c;
    }
    /* Dark mode override */
    html.dark .stp-page-shell {
        --stp-surface-from: #0d1830;
        --stp-surface-to: #0b1322;
        --stp-surface-border: rgba(148, 163, 184, 0.16);
        --stp-text-primary: #e5edf8;
        --stp-text-secondary: #91a4c0;
        --stp-card-bg: #10203a;
        --stp-soft-bg: #122746;
        --stp-input-bg: #162847;
        --stp-card-border: rgba(148, 163, 184, 0.12);
        --stp-danger-bg: rgba(244, 63, 94, 0.14);
        --stp-danger-border: rgba(244, 63, 94, 0.24);
        --stp-danger-text: #fecdd3;
    }
    .stp-select { color-scheme: light; }
    .stp-select option { color: #0f172a; background: #f8fbff; }
    .stp-page-shell {
        background: linear-gradient(180deg, var(--stp-surface-from) 0%, var(--stp-surface-to) 100%);
        min-height: calc(100vh - 7rem);
        --shadow: none !important;
        --avento-shadow-sm: none !important;
        --avento-shadow-md: none !important;
        --avento-shadow-lg: none !important;
        --avento-shadow-glow: none !important;
        --tw-shadow: 0 0 #0000 !important;
        --tw-shadow-colored: 0 0 #0000 !important;
    }
    .stp-page-shell,
    .stp-page-shell *,
    .stp-page-shell *::before,
    .stp-page-shell *::after {
        box-shadow: none !important;
        text-shadow: none !important;
        filter: none !important;
        --shadow: none !important;
        --avento-shadow-sm: none !important;
        --avento-shadow-md: none !important;
        --avento-shadow-lg: none !important;
        --avento-shadow-glow: none !important;
        --tw-shadow: 0 0 #0000 !important;
        --tw-shadow-colored: 0 0 #0000 !important;
        --tw-ring-shadow: 0 0 #0000 !important;
        --tw-ring-offset-shadow: 0 0 #0000 !important;
    }
    .stp-panel { background: var(--stp-card-bg) !important; border: 1px solid var(--stp-card-border) !important; backdrop-filter: none !important; }
    .stp-soft { background: var(--stp-soft-bg) !important; border: 1px solid var(--stp-card-border) !important; }
    .stp-field {
        background: var(--stp-input-bg) !important;
        border: 1px solid var(--stp-card-border) !important;
        outline: none !important;
        appearance: none !important;
        -webkit-appearance: none !important;
    }
    .stp-field::placeholder { color: var(--stp-text-secondary); }
    .stp-table-head { background: var(--stp-soft-bg) !important; }
    .stp-muted { color: var(--stp-text-secondary); }
    .stp-primary { background: var(--stp-accent); color: #ffffff !important; }
    .stp-primary:hover { background: var(--stp-accent-strong); color: #ffffff !important; }
    .stp-secondary { background: var(--stp-input-bg); border: 1px solid var(--stp-card-border); outline: none !important; color: var(--stp-text-primary) !important; }
    .stp-secondary:hover { background: var(--stp-soft-bg); }
    .stp-success { background: var(--stp-accent); border: 1px solid transparent !important; outline: none !important; color: #ffffff !important; }
    .stp-success:hover { background: var(--stp-accent-strong); color: #ffffff !important; }
    .stp-danger-btn { background: var(--stp-danger-bg); border: 1px solid var(--stp-danger-border) !important; outline: none !important; color: var(--stp-danger-text); }
    .stp-action-btn { background: var(--stp-input-bg); border: 1px solid var(--stp-card-border) !important; outline: none !important; color: var(--stp-text-primary) !important; }
    .stp-action-btn:hover { background: var(--stp-soft-bg); }
    .stp-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 120;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(2, 6, 23, 0.78);
        backdrop-filter: blur(10px);
    }
    .stp-modal-backdrop.stp-modal-open { display: flex; }
    .stp-modal-card {
        width: min(100%, 430px);
        background: var(--stp-card-bg) !important;
        border: 1px solid var(--stp-card-border) !important;
        border-radius: 28px;
    }
    .stp-modal-copy strong { color: var(--stp-text-primary) !important; }
    html.dark .stp-modal-copy strong { color: #ffffff !important; }
    .stp-link-disabled { opacity: .45; pointer-events: none; }
    .stp-table-wrap { background: var(--stp-soft-bg) !important; }
    .stp-page-shell,
    .stp-page-shell h1,
    .stp-page-shell h2,
    .stp-page-shell h3,
    .stp-page-shell h4,
    .stp-page-shell p,
    .stp-page-shell span,
    .stp-page-shell a,
    .stp-page-shell label,
    .stp-page-shell th,
    .stp-page-shell td,
    .stp-page-shell strong {
        color: var(--stp-text-primary) !important;
    }
    html.dark .stp-page-shell,
    html.dark .stp-page-shell h1,
    html.dark .stp-page-shell h2,
    html.dark .stp-page-shell h3,
    html.dark .stp-page-shell h4,
    html.dark .stp-page-shell p,
    html.dark .stp-page-shell span,
    html.dark .stp-page-shell a,
    html.dark .stp-page-shell label,
    html.dark .stp-page-shell th,
    html.dark .stp-page-shell td,
    html.dark .stp-page-shell strong {
        color: #ffffff !important;
    }
    .stp-page-shell .stp-muted,
    .stp-page-shell .text-slate-300,
    .stp-page-shell .text-sky-300,
    .stp-page-shell .text-rose-300 {
        color: var(--stp-text-secondary) !important;
    }
    /* Preserve semantic price colors based on theme */
    html:not(.dark) .stp-page-shell .text-sky-300 { color: #0284c7 !important; }
    html:not(.dark) .stp-page-shell .text-rose-300 { color: #be123c !important; }
    html.dark .stp-page-shell .text-sky-300 { color: #7dd3fc !important; }
    html.dark .stp-page-shell .text-rose-300 { color: #fda4af !important; }
    .stp-page-shell input,
    .stp-page-shell select,
    .stp-page-shell textarea,
    .stp-page-shell input::placeholder,
    .stp-page-shell textarea::placeholder {
        color: var(--stp-text-primary) !important;
    }
    html.dark .stp-page-shell input,
    html.dark .stp-page-shell select,
    html.dark .stp-page-shell textarea,
    html.dark .stp-page-shell input::placeholder,
    html.dark .stp-page-shell textarea::placeholder {
        color: #ffffff !important;
    }
    .stp-page-shell input,
    .stp-page-shell select,
    .stp-page-shell textarea,
    .stp-page-shell button,
    .stp-page-shell a {
        outline: none !important;
        border: none !important;
        background-clip: padding-box !important;
    }
    .dark .stp-panel,
    .dark .stp-soft,
    .dark .stp-field,
    .dark .stp-table-head,
    .dark select.stp-select,
    .dark input.stp-field {
        background-color: inherit;
    }
    .dark .stp-panel { background: var(--stp-card-bg) !important; }
    .dark .stp-soft { background: var(--stp-soft-bg) !important; }
    .dark .stp-field,
    .dark select.stp-select,
    .dark input.stp-field {
        background: var(--stp-input-bg) !important;
        border: 1px solid var(--stp-card-border) !important;
        color: var(--stp-text-primary) !important;
    }
    .dark .stp-table-head { background: var(--stp-soft-bg) !important; }
    .dark.avento-theme aside.stp-panel {
        background: var(--stp-card-bg) !important;
        background-color: var(--stp-card-bg) !important;
        border: 1px solid var(--stp-card-border) !important;
        border-right: 1px solid var(--stp-card-border) !important;
        box-shadow: none !important;
    }
    .dark.avento-theme form.stp-panel,
    .dark.avento-theme .stp-soft,
    .dark.avento-theme .stp-table-wrap {
        background: var(--stp-soft-bg) !important;
        background-color: var(--stp-soft-bg) !important;
        box-shadow: none !important;
    }
    .dark.avento-theme form.stp-panel {
        background: var(--stp-card-bg) !important;
        background-color: var(--stp-card-bg) !important;
    }
    .dark.avento-theme table#prices-table,
    .dark.avento-theme table#prices-table thead,
    .dark.avento-theme table#prices-table tbody,
    .dark.avento-theme table#prices-table tr,
    .dark.avento-theme table#prices-table td {
        background: transparent !important;
        background-color: transparent !important;
        color: #ffffff !important;
        border-color: var(--stp-card-border) !important;
        box-shadow: none !important;
    }
    .dark.avento-theme table#prices-table tbody tr:hover {
        background: transparent !important;
        background-color: transparent !important;
    }
</style>
@php
    $selectedTecidoId = $selectedTecidoId ?? $productType->tecido_id;
    $currentFabric = $tecidos->firstWhere('id', $selectedTecidoId);
    $currentFabricName = $currentFabric?->name ?? 'Nenhum tecido definido';
    $currentFabricEditUrl = $currentFabric ? route('admin.tecidos.edit', $currentFabric->id) : route('admin.tecidos.index');
    
    // Indica se estamos vendo o tecido padrao ou outro tecido do catalogo
    $isDefaultFabric = (int)$selectedTecidoId === (int)$productType->tecido_id;
@endphp

<div class="stp-page-shell -mx-4 px-4 py-5 md:-mx-6 md:px-6">
<div class="max-w-7xl mx-auto space-y-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-sm stp-muted">
                <a href="{{ route('admin.personalization-prices.edit', ['type' => 'SUB. TOTAL']) }}" class="hover:text-white transition-colors">Precos SUB. TOTAL</a>
                <span>/</span>
                <span class="text-slate-100">{{ $typeLabel }}</span>
            </nav>
            <div class="space-y-1">
                <h1 class="text-3xl font-bold text-white tracking-tight">Configurar {{ $typeLabel }}</h1>
                <p class="text-sm stp-muted">Tecido padrao, faixas e adicionais em uma unica tela.</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.personalization-prices.edit', ['type' => 'SUB. TOTAL']) }}" class="inline-flex min-h-[46px] items-center justify-center gap-2 rounded-2xl stp-secondary px-5 text-sm font-semibold text-white transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
            <button type="submit" form="prices-form" class="inline-flex min-h-[46px] items-center justify-center gap-2 rounded-2xl stp-primary px-5 text-sm font-semibold text-white transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Salvar
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.85fr)_360px]">
        <form method="POST" action="{{ route('admin.sublimation-products.update-type', $type) }}" id="prices-form" class="stp-panel rounded-[24px]">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-4 border-b border-slate-300/10 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Configuracao de Precos</p>
                    <h3 class="text-xl font-semibold text-white">Faixas para {{ strtoupper($currentFabricName) }}</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full stp-soft px-3 py-1.5 text-xs font-semibold text-white">
                        Tecido:
                        <strong id="selected-fabric-name">{{ strtoupper($currentFabricName) }}</strong>
                    </span>
                    <span class="rounded-full stp-soft px-3 py-1.5 text-xs font-semibold text-white">
                        <strong id="price-range-count">{{ $prices->count() }}</strong> faixas
                    </span>
                </div>
            </div>

            <div class="space-y-6 p-6">
                <section class="rounded-[24px] stp-soft p-5 space-y-5">
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Selecao de Tecido</p>
                        <h4 class="text-lg font-semibold text-white">Configurar faixas para:</h4>
                        @if($isDefaultFabric)
                            <div class="inline-flex items-center gap-2 rounded-full bg-indigo-500/20 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-300 border border-indigo-500/30">
                                Tecido Padrao do Produto
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                        <div>
                            <label for="tecido_id" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-300">Selecionar tecido</label>
                            <select name="tecido_id" id="tecido_id" class="stp-select stp-field w-full rounded-2xl px-4 py-3 text-white" required>
                                <option value="">Selecione um tecido...</option>
                                @foreach($tecidos as $tecido)
                                    <option value="{{ $tecido->id }}" {{ (int)$selectedTecidoId === (int)$tecido->id ? 'selected' : '' }}>
                                        {{ strtoupper($tecido->name) }} {{ (int)$productType->tecido_id === (int)$tecido->id ? '(PADRAO)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.tecidos.create') }}" class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-success px-5 text-sm font-bold text-white transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Novo tecido
                            </a>
                            <a href="{{ $currentFabricEditUrl }}" id="edit-selected-fabric" data-base-url="{{ url('/admin/tecidos') }}" class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-secondary px-5 text-sm font-bold text-white transition-colors {{ $currentFabric ? '' : 'stp-link-disabled' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar tecido selecionado
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.tecidos.index') }}" class="inline-flex items-center rounded-full stp-soft px-4 py-2 text-sm font-semibold text-white hover:border-slate-200/20">
                        Gerenciar catalogo de tecidos
                    </a>
                </section>

                {{-- Acréscimo GG/EXG toggle --}}
                <section class="rounded-[24px] stp-soft p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Acréscimo de Tamanho</p>
                            <h4 class="text-lg font-semibold text-white">Cobrar acréscimo GG / EXG automaticamente</h4>
                            <p class="text-sm stp-muted">Quando ativado, o sistema calcula e adiciona os acréscimos de tamanho no resumo do pedido. Desative se os acréscimos já estão incluídos como itens de corte separados.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="hidden" name="apply_size_surcharge" value="0">
                            <input type="checkbox" name="apply_size_surcharge" value="1" id="apply_size_surcharge"
                                class="sr-only peer"
                                {{ ($productType->apply_size_surcharge ?? true) ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-slate-600 peer-checked:bg-[#7c3aed] rounded-full transition-colors duration-200 peer-focus:outline-none"></div>
                            <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full transition-transform duration-200 peer-checked:translate-x-7"></div>
                        </label>
                    </div>
                </section>

                <section class="rounded-[24px] stp-soft p-5 space-y-5">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="space-y-1">
                            <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Faixas de preco</p>
                            <h4 class="text-lg font-semibold text-white">Precos para {{ strtoupper($currentFabricName) }}</h4>
                        </div>
                        <button type="button" onclick="addRow()" class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-success px-5 text-sm font-bold text-white transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar faixa
                        </button>
                    </div>

                    <div class="stp-table-wrap overflow-hidden rounded-[24px] border border-slate-300/10">
                        <table class="w-full border-collapse" id="prices-table">
                            <thead>
                                <tr class="stp-table-head text-left">
                                    <th class="px-4 py-4 text-xs font-extrabold uppercase tracking-[0.2em] text-slate-300">Quantidade</th>
                                    <th class="w-48 px-4 py-4 text-xs font-extrabold uppercase tracking-[0.2em] text-slate-300">Preco (R$)</th>
                                    <th class="w-20 px-4 py-4 text-center text-xs font-extrabold uppercase tracking-[0.2em] text-slate-300">Acao</th>
                                </tr>
                            </thead>
                            <tbody id="prices-tbody">
                                @forelse($prices as $index => $price)
                                    <tr class="border-t border-slate-300/10">
                                        <td class="px-4 py-4">
                                            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                                                <input type="number" name="prices[{{ $index }}][quantity_from]" value="{{ $price->quantity_from }}" min="1" required class="stp-field w-full rounded-2xl px-4 py-3 text-center text-white md:w-28">
                                                <span class="text-center text-sm font-bold stp-muted">ate</span>
                                                <input type="number" name="prices[{{ $index }}][quantity_to]" value="{{ $price->quantity_to }}" min="1" placeholder="Sem limite" class="stp-field w-full rounded-2xl px-4 py-3 text-center text-white md:w-32">
                                            </div>
                                            <input type="hidden" name="prices[{{ $index }}][id]" value="{{ $price->id }}">
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" name="prices[{{ $index }}][price]" value="{{ $price->price }}" step="0.01" min="0" required class="stp-field w-full rounded-2xl px-4 py-3 text-right font-extrabold text-white">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button type="button" onclick="removeRow(this)" class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-danger-btn transition-colors" title="Remover faixa">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-row">
                                        <td colspan="3" class="px-4 py-10 text-center text-sm font-semibold stp-muted">Nenhuma faixa cadastrada. Clique em <strong>Adicionar faixa</strong> para iniciar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </form>

        <aside class="stp-panel rounded-[24px]">
            <div class="border-b border-slate-300/10 px-6 py-5">
                <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Adicionais</p>
                <h3 class="mt-2 text-xl font-semibold text-white">Adicionais de {{ $typeLabel }}</h3>
                <p class="mt-2 text-sm stp-muted">Valores extras especificos deste tipo.</p>
            </div>

            <div class="space-y-6 p-6">
                <div class="space-y-3">
                    @forelse($addons as $addon)
                        <div class="flex items-center justify-between gap-3 rounded-[22px] stp-soft px-4 py-4">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-extrabold text-white">{{ strtoupper($addon->name) }}</div>
                                <div class="mt-1 text-xs stp-muted">Somente para {{ strtolower($typeLabel) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-extrabold {{ $addon->price >= 0 ? 'text-sky-300' : 'text-rose-300' }}">
                                    {{ $addon->price >= 0 ? '+' : '' }}R$ {{ number_format($addon->price, 2, ',', '.') }}
                                </span>
                                <button
                                    type="button"
                                    onclick="openEditAddonModal(this)"
                                    data-update-url="{{ route('admin.sublimation-products.addons.update', $addon) }}"
                                    data-addon-name="{{ $addon->name }}"
                                    data-addon-price="{{ number_format((float) $addon->price, 2, '.', '') }}"
                                    class="inline-flex h-10 items-center justify-center gap-2 rounded-full stp-action-btn px-3 text-xs font-bold text-white transition-colors"
                                    title="Editar adicional"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Editar</span>
                                </button>
                                <button
                                    type="button"
                                    onclick="openDeleteAddonModal(this)"
                                    data-delete-url="{{ route('admin.sublimation-products.addons.destroy', $addon) }}"
                                    data-addon-name="{{ strtoupper($addon->name) }}"
                                    data-addon-price-display="{{ $addon->price >= 0 ? '+' : '' }}R$ {{ number_format($addon->price, 2, ',', '.') }}"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-danger-btn transition-colors"
                                    title="Remover adicional"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0v11a2 2 0 002 2h4a2 2 0 002-2V7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[22px] stp-soft px-4 py-8 text-center text-sm font-semibold stp-muted">
                            Nenhum adicional cadastrado para {{ strtolower($typeLabel) }}.
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-300/10 pt-6">
                    <form method="POST" action="{{ route('admin.sublimation-products.addons.store', $type) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="addon-name" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-300">Nome do adicional</label>
                            <input type="text" id="addon-name" name="name" placeholder="Ex: DRYFIT PREMIUM" required class="stp-field w-full rounded-2xl px-4 py-3 text-white uppercase">
                        </div>
                        <div>
                            <label for="addon-price" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-300">Valor (R$)</label>
                            <input type="number" id="addon-price" name="price" step="0.01" placeholder="0,00" required class="stp-field w-full rounded-2xl px-4 py-3 text-right text-white">
                        </div>
                        <button type="submit" class="inline-flex w-full min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-primary px-5 text-sm font-bold text-white transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar adicional
                        </button>
                    </form>
                    <p class="mt-3 text-center text-xs stp-muted">Use valor negativo quando quiser aplicar desconto.</p>
                </div>
            </div>
        </aside>

        {{-- ═══ Modelos disponíveis para este tipo ═══ --}}
        <aside class="stp-panel rounded-[24px] xl:col-span-full">
            <div class="border-b border-slate-300/10 px-6 py-5">
                <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Modelos</p>
                <h3 class="mt-2 text-xl font-semibold text-white">Modelos de {{ $typeLabel }}</h3>
                <p class="mt-2 text-sm stp-muted">Modelos que aparecerao no dropdown do wizard para este tipo de produto.</p>
            </div>

            <div class="space-y-6 p-6">
                <form method="POST" action="{{ route('admin.sublimation-products.models.update', $type) }}" id="models-form">
                    @csrf
                    @method('PUT')

                    <div class="space-y-3" id="models-list">
                        @php $currentModels = $productType->models ?? []; @endphp
                        @forelse($currentModels as $index => $model)
                            <div class="flex items-center gap-3 rounded-[22px] stp-soft px-4 py-3" data-model-row>
                                <input type="text" name="models[]" value="{{ $model }}" readonly
                                       class="stp-field flex-1 rounded-2xl px-4 py-2.5 text-sm font-extrabold text-white uppercase">
                                <button type="button" onclick="removeModelRow(this)"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-danger-btn transition-colors flex-shrink-0"
                                        title="Remover modelo">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div id="models-empty" class="rounded-[22px] stp-soft px-4 py-8 text-center text-sm font-semibold stp-muted">
                                Nenhum modelo cadastrado. Os modelos padrao (BASICA, BABYLOOK, INFANTIL) serao usados.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-300/10 pt-6 mt-6">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="new-model-name" placeholder="Nome do modelo (ex: POLO)"
                                   class="stp-field flex-1 rounded-2xl px-4 py-3 text-white uppercase"
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();addModelRow();}">
                            <button type="button" onclick="addModelRow()"
                                    class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-success px-5 text-sm font-bold text-white transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit"
                                class="inline-flex w-full min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-primary px-5 text-sm font-bold text-white transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Modelos
                        </button>
                    </div>
                </form>
            </div>
        </aside>
        
        {{-- ═══ Golas disponíveis para este tipo ═══ --}}
        <aside class="stp-panel rounded-[24px] xl:col-span-full mt-6">
            <div class="border-b border-slate-300/10 px-6 py-5">
                <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Golas</p>
                <h3 class="mt-2 text-xl font-semibold text-white">Golas de {{ $typeLabel }}</h3>
                <p class="mt-2 text-sm stp-muted">Opções de gola que aparecerão no dropdown do wizard para este tipo de produto.</p>
            </div>

            <div class="space-y-6 p-6">
                <form method="POST" action="{{ route('admin.sublimation-products.collars.update', $type) }}" id="collars-form">
                    @csrf
                    @method('PUT')

                    <div class="space-y-3" id="collars-list">
                        @php $currentCollars = $productType->collars ?? []; @endphp
                        @forelse($currentCollars as $index => $collar)
                            <div class="flex items-center gap-3 rounded-[22px] stp-soft px-4 py-3" data-collar-row>
                                <input type="text" name="collars[]" value="{{ $collar }}" readonly
                                       class="stp-field flex-1 rounded-2xl px-4 py-2.5 text-sm font-extrabold text-white uppercase">
                                <button type="button" onclick="removeCollarRow(this)"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-danger-btn transition-colors flex-shrink-0"
                                        title="Remover gola">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div id="collars-empty" class="rounded-[22px] stp-soft px-4 py-8 text-center text-sm font-semibold stp-muted">
                                Nenhuma gola cadastrada. As golas padrão serão usadas.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-300/10 pt-6 mt-6">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="new-collar-name" placeholder="Nome da gola (ex: CARECA, V, POLO)"
                                   class="stp-field flex-1 rounded-2xl px-4 py-3 text-white uppercase"
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();addCollarRow();}">
                            <button type="button" onclick="addCollarRow()"
                                    class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-success px-5 text-sm font-bold text-white transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="button" onclick="submitCollarsForm()"
                                class="inline-flex w-full min-h-[48px] items-center justify-center gap-2 rounded-2xl stp-primary px-5 text-sm font-bold text-white transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Golas
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        <div id="edit-addon-modal" class="stp-modal-backdrop hidden" onclick="if (event.target === this) closeEditAddonModal()">
            <div class="stp-modal-card overflow-hidden">
                <div class="flex items-start justify-between gap-4 border-b border-slate-300/10 px-6 py-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Editar adicional</p>
                        <h3 id="edit-addon-modal-title" class="mt-2 text-xl font-semibold text-white">Atualizar adicional</h3>
                    </div>
                    <button type="button" onclick="closeEditAddonModal()" class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-action-btn text-white transition-colors" title="Fechar">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="POST" id="edit-addon-form" class="space-y-4 px-6 py-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit-addon-name" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-300">Nome do adicional</label>
                        <input type="text" id="edit-addon-name" name="name" required class="stp-field w-full rounded-2xl px-4 py-3 text-white uppercase">
                    </div>
                    <div>
                        <label for="edit-addon-price" class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-slate-300">Valor (R$)</label>
                        <input type="number" id="edit-addon-price" name="price" step="0.01" required class="stp-field w-full rounded-2xl px-4 py-3 text-right text-white">
                    </div>
                    <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeEditAddonModal()" class="inline-flex min-h-[46px] items-center justify-center rounded-2xl stp-secondary px-5 text-sm font-semibold text-white transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex min-h-[46px] items-center justify-center rounded-2xl stp-primary px-5 text-sm font-semibold text-white transition-colors">
                            Salvar alteracoes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="delete-addon-modal" class="stp-modal-backdrop hidden" onclick="if (event.target === this) closeDeleteAddonModal()">
            <div class="stp-modal-card overflow-hidden">
                <div class="border-b border-slate-300/10 px-6 py-5">
                    <p class="text-xs font-bold uppercase tracking-[0.24em] stp-muted">Confirmar exclusao</p>
                    <h3 class="mt-2 text-xl font-semibold text-white">Remover adicional</h3>
                </div>
                <div class="stp-modal-copy space-y-3 px-6 py-6">
                    <p class="text-sm text-white">
                        Voce esta prestes a remover <strong id="delete-addon-name"></strong>.
                    </p>
                    <p id="delete-addon-price" class="text-sm font-bold stp-muted"></p>
                    <p class="text-xs stp-muted">Essa acao nao podera ser desfeita.</p>
                </div>
                <form method="POST" id="delete-addon-form" class="flex flex-col gap-3 border-t border-slate-300/10 px-6 py-5 sm:flex-row sm:justify-end">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteAddonModal()" class="inline-flex min-h-[46px] items-center justify-center rounded-2xl stp-secondary px-5 text-sm font-semibold text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="inline-flex min-h-[46px] items-center justify-center rounded-2xl stp-danger-btn px-5 text-sm font-semibold text-white transition-colors">
                        Excluir adicional
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    window.rowIndex = {{ $prices->count() }};

    function getFabricLabel(select) {
        const option = select && select.options[select.selectedIndex];
        return option && option.value ? option.textContent.trim() : 'NENHUM TECIDO DEFINIDO';
    }

    function syncSelectedFabric() {
        const select = document.getElementById('tecido_id');
        const currentSelected = '{{ $selectedTecidoId }}';
        
        if (select && select.value && select.value !== currentSelected) {
            const url = new URL(window.location.href);
            url.searchParams.set('tecido_id', select.value);
            window.location.href = url.toString();
            return;
        }

        const name = document.getElementById('selected-fabric-name');
        const editLink = document.getElementById('edit-selected-fabric');
        const label = getFabricLabel(select);

        if (name) name.textContent = label;

        if (editLink && select) {
            if (select.value) {
                editLink.href = `${editLink.dataset.baseUrl}/${select.value}/edit`;
                editLink.classList.remove('stp-link-disabled');
            } else {
                editLink.href = '{{ route('admin.tecidos.index') }}';
                editLink.classList.add('stp-link-disabled');
            }
        }
    }

    function syncPriceRangeCount() {
        const tbody = document.getElementById('prices-tbody');
        const count = document.getElementById('price-range-count');
        if (!tbody || !count) return;
        count.textContent = tbody.querySelectorAll('tr:not(#empty-row)').length;
    }

    function ensureEmptyRow() {
        const tbody = document.getElementById('prices-tbody');
        if (!tbody || tbody.querySelector('tr:not(#empty-row)')) return;

        const row = document.createElement('tr');
        row.id = 'empty-row';
        row.innerHTML = '<td colspan="3" class="px-4 py-10 text-center text-sm font-semibold stp-muted">Nenhuma faixa cadastrada. Clique em <strong>Adicionar faixa</strong> para iniciar.</td>';
        tbody.appendChild(row);
    }

    window.addRow = function addRow() {
        const tbody = document.getElementById('prices-tbody');
        const emptyRow = document.getElementById('empty-row');
        if (!tbody) return;
        if (emptyRow) emptyRow.remove();

        const row = document.createElement('tr');
        row.className = 'border-t border-slate-300/10';
        row.innerHTML = `
            <td class="px-4 py-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                    <input type="number" name="prices[${window.rowIndex}][quantity_from]" min="1" required placeholder="De" class="stp-field w-full rounded-2xl px-4 py-3 text-center text-white md:w-28">
                    <span class="text-center text-sm font-bold stp-muted">ate</span>
                    <input type="number" name="prices[${window.rowIndex}][quantity_to]" min="1" placeholder="Sem limite" class="stp-field w-full rounded-2xl px-4 py-3 text-center text-white md:w-32">
                </div>
            </td>
            <td class="px-4 py-4">
                <input type="number" name="prices[${window.rowIndex}][price]" step="0.01" min="0" required placeholder="0,00" class="stp-field w-full rounded-2xl px-4 py-3 text-right font-extrabold text-white">
            </td>
            <td class="px-4 py-4 text-center">
                <button type="button" onclick="removeRow(this)" class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-danger-btn transition-colors" title="Remover faixa">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;

        tbody.appendChild(row);
        window.rowIndex++;
        syncPriceRangeCount();

        const firstInput = row.querySelector('input');
        if (firstInput) firstInput.focus();
    };

    window.removeRow = function removeRow(button) {
        if (!confirm('Remover esta faixa?')) return;
        const row = button.closest('tr');
        if (row) row.remove();
        ensureEmptyRow();
        syncPriceRangeCount();
    };

    function syncModalScrollLock() {
        const hasOpenModal = document.querySelector('.stp-modal-backdrop.stp-modal-open');
        document.body.style.overflow = hasOpenModal ? 'hidden' : '';
    }

    function toggleAddonModal(modalId, shouldOpen) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.toggle('hidden', !shouldOpen);
        modal.classList.toggle('stp-modal-open', shouldOpen);
        syncModalScrollLock();
    }

    window.openEditAddonModal = function openEditAddonModal(button) {
        const form = document.getElementById('edit-addon-form');
        const title = document.getElementById('edit-addon-modal-title');
        const nameInput = document.getElementById('edit-addon-name');
        const priceInput = document.getElementById('edit-addon-price');

        if (!form || !nameInput || !priceInput || !button) return;

        form.action = button.dataset.updateUrl || '';
        nameInput.value = (button.dataset.addonName || '').toUpperCase();
        priceInput.value = button.dataset.addonPrice || '';

        if (title) {
            title.textContent = `Editar ${nameInput.value || 'adicional'}`;
        }

        toggleAddonModal('edit-addon-modal', true);
        setTimeout(() => nameInput.focus(), 10);
    };

    window.closeEditAddonModal = function closeEditAddonModal() {
        toggleAddonModal('edit-addon-modal', false);
    };

    window.openDeleteAddonModal = function openDeleteAddonModal(button) {
        const form = document.getElementById('delete-addon-form');
        const name = document.getElementById('delete-addon-name');
        const price = document.getElementById('delete-addon-price');

        if (!form || !button) return;

        form.action = button.dataset.deleteUrl || '';
        if (name) name.textContent = button.dataset.addonName || 'este adicional';
        if (price) price.textContent = button.dataset.addonPriceDisplay || '';

        toggleAddonModal('delete-addon-modal', true);
    };

    window.closeDeleteAddonModal = function closeDeleteAddonModal() {
        toggleAddonModal('delete-addon-modal', false);
    };

    function hasEditTypePageElements() {
        return !!(
            document.getElementById('prices-form') &&
            document.getElementById('tecido_id') &&
            document.getElementById('prices-tbody')
        );
    }

    function initEditTypePage() {
        if (!hasEditTypePageElements()) {
            return;
        }

        const fabricSelect = document.getElementById('tecido_id');
        const addonName = document.getElementById('addon-name');
        const editAddonName = document.getElementById('edit-addon-name');

        if (fabricSelect) {
            if (fabricSelect.dataset.stpBound !== 'true') {
                fabricSelect.addEventListener('change', syncSelectedFabric);
                fabricSelect.dataset.stpBound = 'true';
            }
            syncSelectedFabric();
        }

        if (addonName) {
            if (addonName.dataset.stpBound !== 'true') {
                addonName.addEventListener('input', function () {
                    this.value = this.value.toUpperCase();
                });
                addonName.dataset.stpBound = 'true';
            }
        }

        if (editAddonName) {
            if (editAddonName.dataset.stpBound !== 'true') {
                editAddonName.addEventListener('input', function () {
                    this.value = this.value.toUpperCase();
                });
                editAddonName.dataset.stpBound = 'true';
            }
        }

        if (!window.__editTypeModalEscapeBound) {
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeEditAddonModal();
                    closeDeleteAddonModal();
                }
            });
            window.__editTypeModalEscapeBound = true;
        }

        ensureEmptyRow();
        syncPriceRangeCount();
    }

    if (window.__editTypeDomInitHandler) {
        document.removeEventListener('DOMContentLoaded', window.__editTypeDomInitHandler);
    }
    if (window.__editTypeAjaxInitHandler) {
        document.removeEventListener('ajax-content-loaded', window.__editTypeAjaxInitHandler);
    }

    window.__editTypeDomInitHandler = initEditTypePage;
    window.__editTypeAjaxInitHandler = initEditTypePage;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.__editTypeDomInitHandler, { once: true });
    } else {
        initEditTypePage();
    }

    document.addEventListener('ajax-content-loaded', window.__editTypeAjaxInitHandler);

    // ══════ Models management ══════
    window.addModelRow = function() {
        const input = document.getElementById('new-model-name');
        if (!input) return;
        const name = input.value.trim().toUpperCase();
        if (!name) { input.focus(); return; }

        // Check duplicate
        const existing = document.querySelectorAll('#models-list input[name="models[]"]');
        for (const el of existing) {
            if (el.value.toUpperCase() === name) {
                alert('Este modelo já existe.');
                input.focus();
                return;
            }
        }

        // Remove empty state
        const emptyEl = document.getElementById('models-empty');
        if (emptyEl) emptyEl.remove();

        const list = document.getElementById('models-list');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 rounded-[22px] stp-soft px-4 py-3';
        row.setAttribute('data-model-row', '');
        row.innerHTML = `
            <input type="text" name="models[]" value="${name}" readonly
                   class="stp-field flex-1 rounded-2xl px-4 py-2.5 text-sm font-extrabold text-white uppercase">
            <button type="button" onclick="removeModelRow(this)"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-danger-btn transition-colors flex-shrink-0"
                    title="Remover modelo">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        list.appendChild(row);
        input.value = '';
        input.focus();
    };

    window.removeModelRow = function(button) {
        const row = button.closest('[data-model-row]');
        if (row) row.remove();

        // Show empty state if no models left
        const remaining = document.querySelectorAll('#models-list [data-model-row]');
        if (remaining.length === 0) {
            const list = document.getElementById('models-list');
            const empty = document.createElement('div');
            empty.id = 'models-empty';
            empty.className = 'rounded-[22px] stp-soft px-4 py-8 text-center text-sm font-semibold stp-muted';
            empty.textContent = 'Nenhum modelo cadastrado. Os modelos padrao (BASICA, BABYLOOK, INFANTIL) serao usados.';
            list.appendChild(empty);
        }
    };

    // ══════ Collars management ══════
    window.addCollarRow = function() {
        const input = document.getElementById('new-collar-name');
        if (!input) return;
        const name = input.value.trim().toUpperCase();
        if (!name) { input.focus(); return; }

        // Check duplicate
        const existing = document.querySelectorAll('#collars-list input[name="collars[]"]');
        for (const el of existing) {
            if (el.value.toUpperCase() === name) {
                alert('Esta gola já existe.');
                input.focus();
                return;
            }
        }

        // Remove empty state
        const emptyEl = document.getElementById('collars-empty');
        if (emptyEl) emptyEl.remove();

        const list = document.getElementById('collars-list');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 rounded-[22px] stp-soft px-4 py-3';
        row.setAttribute('data-collar-row', '');
        row.innerHTML = `
            <input type="text" name="collars[]" value="${name}" readonly
                   class="stp-field flex-1 rounded-2xl px-4 py-2.5 text-sm font-extrabold text-white uppercase">
            <button type="button" onclick="removeCollarRow(this)"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full stp-danger-btn transition-colors flex-shrink-0"
                    title="Remover gola">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        list.appendChild(row);
        input.value = '';
        input.focus();
    };

    window.removeCollarRow = function(button) {
        const row = button.closest('[data-collar-row]');
        if (row) row.remove();

        // Show empty state if no collars left
        const remaining = document.querySelectorAll('#collars-list [data-collar-row]');
        if (remaining.length === 0) {
            const list = document.getElementById('collars-list');
            const empty = document.createElement('div');
            empty.id = 'collars-empty';
            empty.className = 'rounded-[22px] stp-soft px-4 py-8 text-center text-sm font-semibold stp-muted';
            empty.textContent = 'Nenhuma gola cadastrada. As golas padrão serão usadas.';
            list.appendChild(empty);
        }
    };

    window.submitCollarsForm = function() {
        // Auto-adiciona o que estiver digitado no input antes de submeter
        const input = document.getElementById('new-collar-name');
        if (input && input.value.trim()) {
            addCollarRow();
        }
        document.getElementById('collars-form').submit();
    };
</script>
@endsection
