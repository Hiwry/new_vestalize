@extends('layouts.admin')

@push('styles')
<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] { -moz-appearance: textfield; }
</style>
@endpush

@section('content')
@php
    $selectedTecidoId = $selectedTecidoId ?? $productType->tecido_id;
    $currentFabric = $tecidos->firstWhere('id', $selectedTecidoId);
    $currentFabricName = $currentFabric?->name ?? 'Nenhum tecido definido';
    $currentFabricEditUrl = $currentFabric ? route('admin.tecidos.edit', $currentFabric->id) : route('admin.tecidos.index');
    $isDefaultFabric = (int)$selectedTecidoId === (int)$productType->tecido_id;
@endphp

<div class="max-w-7xl mx-auto">

    {{-- Breadcrumb + header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('admin.personalization-prices.edit', ['type' => 'SUB. TOTAL']) }}"
               class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Precos SUB. TOTAL</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ $typeLabel }}</span>
        </nav>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Configurar {{ $typeLabel }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tecido padrao, faixas e adicionais em uma unica tela.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.personalization-prices.edit', ['type' => 'SUB. TOTAL']) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar
                </a>
                <button type="submit" form="prices-form"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" style="stroke:#111827 !important">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Salvar
                </button>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-xl border border-emerald-200 dark:border-emerald-600/30 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-300">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 dark:border-red-600/30 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-300">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.85fr)_360px]">

        @if($type === 'bandeira')
        {{-- ===== BANDEIRA: Preco por Tamanho ===== --}}
        {{-- O form de precos por tamanho depende dos tamanhos configurados abaixo.
             Salva separado dos precos por quantidade. --}}
        <form method="POST" action="{{ route('admin.sublimation-products.update-type', $type) }}"
              id="prices-form"
              class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-3 border-b border-gray-200 dark:border-gray-700 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Configuracao de Precos</p>
                    <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">Preco por Tamanho</h3>
                </div>
                <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                    <strong>{{ count($productType->models ?? []) }}</strong> tamanhos configurados
                </span>
            </div>

            <div class="space-y-5 p-6">
                @php $flagModels = $productType->models ?? []; @endphp
                @if(empty($flagModels))
                    <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 px-4 py-6 text-center text-sm text-amber-700 dark:text-amber-300">
                        Nenhum tamanho cadastrado. Configure os tamanhos de bandeira na secao abaixo antes de definir os precos.
                    </div>
                @else
                    <section class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Preco por Tamanho</p>
                            <h4 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">Defina o preco unitario de cada dimensao</h4>
                        </div>
                        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full border-collapse" id="prices-table">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tamanho / Dimensao</th>
                                        <th class="w-52 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Preco Unitario (R$)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach($flagModels as $sizeKey)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase">{{ $sizeKey }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number"
                                                       name="flag_prices[{{ $sizeKey }}]"
                                                       value="{{ isset($sizePrices[$sizeKey]) ? number_format((float)$sizePrices[$sizeKey]->price, 2, '.', '') : '' }}"
                                                       step="0.01" min="0" placeholder="0,00"
                                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-right text-sm font-semibold text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            </div>
        </form>

        @else

        {{-- ===== OUTROS TIPOS: Preco por Faixa de Quantidade ===== --}}
        <form method="POST" action="{{ route('admin.sublimation-products.update-type', $type) }}"
              id="prices-form"
              class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-3 border-b border-gray-200 dark:border-gray-700 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Configuracao de Precos</p>
                    <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Faixas para <span id="header-fabric-name">{{ strtoupper($currentFabricName) }}</span>
                    </h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Tecido: <strong id="selected-fabric-name">{{ strtoupper($currentFabricName) }}</strong>
                    </span>
                    <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        <strong id="price-range-count">{{ $prices->count() }}</strong> faixas
                    </span>
                </div>
            </div>

            <div class="space-y-5 p-6">

                <section class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Selecao de Tecido</p>
                        <h4 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">Configurar faixas para:</h4>
                        @if($isDefaultFabric)
                            <span class="mt-2 inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                Tecido Padrao do Produto
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                        <div>
                            <label for="tecido_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Selecionar tecido</label>
                            <select name="tecido_id" id="tecido_id" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Selecione um tecido...</option>
                                @foreach($tecidos as $tecido)
                                    <option value="{{ $tecido->id }}" {{ (int)$selectedTecidoId === (int)$tecido->id ? 'selected' : '' }}>
                                        {{ strtoupper($tecido->name) }} {{ (int)$productType->tecido_id === (int)$tecido->id ? '(PADRAO)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.tecidos.create') }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Novo tecido
                            </a>
                            <a href="{{ $currentFabricEditUrl }}" id="edit-selected-fabric"
                               data-base-url="{{ url('/admin/tecidos') }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $currentFabric ? '' : 'pointer-events-none opacity-40' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar tecido selecionado
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.tecidos.index') }}"
                       class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:underline">
                        Gerenciar catalogo de tecidos
                    </a>
                </section>

                <section class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Faixas de preco</p>
                            <h4 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">
                                Precos para {{ strtoupper($currentFabricName) }}
                            </h4>
                        </div>
                        <button type="button" onclick="addRow()"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar faixa
                        </button>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full border-collapse" id="prices-table">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Quantidade</th>
                                    <th class="w-48 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Preco (R$)</th>
                                    <th class="w-16 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acao</th>
                                </tr>
                            </thead>
                            <tbody id="prices-tbody" class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @forelse($prices as $index => $price)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                                                <input type="number" name="prices[{{ $index }}][quantity_from]" value="{{ $price->quantity_from }}" min="1" required
                                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-center text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 md:w-24">
                                                <span class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">ate</span>
                                                <input type="number" name="prices[{{ $index }}][quantity_to]" value="{{ $price->quantity_to }}" min="1" placeholder="Sem limite"
                                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-center text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 md:w-28">
                                            </div>
                                            <input type="hidden" name="prices[{{ $index }}][id]" value="{{ $price->id }}">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="prices[{{ $index }}][price]" value="{{ $price->price }}" step="0.01" min="0" required
                                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-right text-sm font-semibold text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" onclick="removeRow(this)"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 dark:border-red-700/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                    title="Remover faixa">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-row">
                                        <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Nenhuma faixa cadastrada. Clique em <strong class="font-semibold">Adicionar faixa</strong> para iniciar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </form>

        @endif

        {{-- Adicionais --}}
        <aside class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Adicionais</p>
                <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">Adicionais de {{ $typeLabel }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Valores extras especificos deste tipo.</p>
            </div>

            <div class="space-y-5 p-6">
                <div class="space-y-2">
                    @forelse($addons as $addon)
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ strtoupper($addon->name) }}</div>
                                <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Somente para {{ strtolower($typeLabel) }}</div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-sm font-semibold {{ $addon->price >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $addon->price >= 0 ? '+' : '' }}R$ {{ number_format($addon->price, 2, ',', '.') }}
                                </span>
                                <button type="button"
                                        onclick="openEditAddonModal(this)"
                                        data-update-url="{{ route('admin.sublimation-products.addons.update', $addon) }}"
                                        data-addon-name="{{ $addon->name }}"
                                        data-addon-price="{{ number_format((float) $addon->price, 2, '.', '') }}"
                                        class="inline-flex h-8 items-center gap-1 rounded-lg border border-gray-300 dark:border-gray-600 px-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        title="Editar adicional">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                <button type="button"
                                        onclick="openDeleteAddonModal(this)"
                                        data-delete-url="{{ route('admin.sublimation-products.addons.destroy', $addon) }}"
                                        data-addon-name="{{ strtoupper($addon->name) }}"
                                        data-addon-price-display="{{ $addon->price >= 0 ? '+' : '' }}R$ {{ number_format($addon->price, 2, ',', '.') }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 dark:border-red-700/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        title="Remover adicional">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0v11a2 2 0 002 2h4a2 2 0 002-2V7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Nenhum adicional cadastrado para {{ strtolower($typeLabel) }}.
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                    <form method="POST" action="{{ route('admin.sublimation-products.addons.store', $type) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="addon-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do adicional</label>
                            <input type="text" id="addon-name" name="name" placeholder="Ex: DRYFIT PREMIUM" required
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 uppercase">
                        </div>
                        <div>
                            <label for="addon-price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Valor (R$)</label>
                            <input type="number" id="addon-price" name="price" step="0.01" placeholder="0,00" required
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-right text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar adicional
                        </button>
                    </form>
                    <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">Use valor negativo quando quiser aplicar desconto.</p>
                </div>
            </div>
        </aside>

        {{-- Modelos / Tamanhos de Bandeira --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm xl:col-span-full">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $type === 'bandeira' ? 'Tamanhos de Bandeira' : 'Modelos' }}</p>
                <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $type === 'bandeira' ? 'Tamanhos de ' . $typeLabel : 'Modelos de ' . $typeLabel }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $type === 'bandeira' ? 'Dimensões disponíveis (ex: 60x90CM, 90x120CM). Aparecerão no grid de quantidades do wizard.' : 'Modelos que aparecerao no dropdown do wizard para este tipo de produto.' }}</p>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.sublimation-products.models.update', $type) }}" id="models-form">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2" id="models-list">
                        @php
                            $currentModels = $productType->models ?? [];
                            $surchargeDisabled = array_map('strtoupper', $productType->models_surcharge_disabled ?? []);
                        @endphp
                        @forelse($currentModels as $index => $model)
                            @php $isDisabled = in_array(strtoupper($model), $surchargeDisabled); @endphp
                            <div class="flex items-center gap-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-3" data-model-row>
                                <input type="text" name="models[]" value="{{ $model }}" readonly
                                       class="flex-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase">
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">GG/EXG/G1/G2/G3/Esp</span>
                                    @if(!$isDisabled)
                                        <input type="hidden" name="models_surcharge_disabled[]" value="" class="surcharge-disabled-input" disabled>
                                    @else
                                        <input type="hidden" name="models_surcharge_disabled[]" value="{{ $model }}" class="surcharge-disabled-input">
                                    @endif
                                    <button type="button"
                                            onclick="toggleModelSurcharge(this)"
                                            data-model="{{ $model }}"
                                            data-enabled="{{ $isDisabled ? '0' : '1' }}"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $isDisabled ? 'bg-gray-300 dark:bg-gray-600' : 'bg-indigo-600' }}"
                                            role="switch"
                                            aria-checked="{{ $isDisabled ? 'false' : 'true' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 {{ $isDisabled ? 'translate-x-0' : 'translate-x-5' }}"></span>
                                    </button>
                                </div>
                                <button type="button" onclick="removeModelRow(this)"
                                        class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 dark:border-red-700/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        title="Remover modelo">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div id="models-empty" class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                @if($type === 'bandeira')
                                    Nenhum tamanho cadastrado. Adicione as dimensões disponíveis (ex: 60x90CM, 90x120CM, 1.20x1.80M).
                                @else
                                    Nenhum modelo cadastrado. Os modelos padrao (BASICA, BABYLOOK, INFANTIL) serao usados.
                                @endif
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-5">
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input type="text" id="new-model-name" placeholder="{{ $type === 'bandeira' ? 'Dimensão (ex: 60x90CM, 90x120CM)' : 'Nome do modelo (ex: POLO)' }}"
                                   class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 uppercase focus:border-indigo-500 focus:ring-indigo-500"
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();addModelRow();}">
                            <button type="button" onclick="addModelRow()"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Modelos
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($type !== 'bandeira')
        {{-- Golas --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm xl:col-span-full">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Golas</p>
                <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">Golas de {{ $typeLabel }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Opcoes de gola que aparecerao no dropdown do wizard para este tipo de produto.</p>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.sublimation-products.collars.update', $type) }}" id="collars-form">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2" id="collars-list">
                        @php $currentCollars = $productType->collars ?? []; @endphp
                        @forelse($currentCollars as $index => $collar)
                            <div class="flex items-center gap-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-3" data-collar-row>
                                <input type="text" name="collars[]" value="{{ $collar }}" readonly
                                       class="flex-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase">
                                <button type="button" onclick="removeCollarRow(this)"
                                        class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 dark:border-red-700/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        title="Remover gola">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div id="collars-empty" class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhuma gola cadastrada. As golas padrao serao usadas.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-5">
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input type="text" id="new-collar-name" placeholder="Nome da gola (ex: CARECA, V, POLO)"
                                   class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 uppercase focus:border-indigo-500 focus:ring-indigo-500"
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();addCollarRow();}">
                            <button type="button" onclick="addCollarRow()"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" onclick="submitCollarsForm()"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Golas
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Modal: Editar adicional --}}
<div id="edit-addon-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     onclick="if(event.target===this) closeEditAddonModal()">
    <div class="w-full max-w-md rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
        <div class="flex items-start justify-between gap-4 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Editar adicional</p>
                <h3 id="edit-addon-modal-title" class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">Atualizar adicional</h3>
            </div>
            <button type="button" onclick="closeEditAddonModal()"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" id="edit-addon-form" class="space-y-4 px-6 py-5">
            @csrf
            @method('PUT')
            <div>
                <label for="edit-addon-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do adicional</label>
                <input type="text" id="edit-addon-name" name="name" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 uppercase focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="edit-addon-price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Valor (R$)</label>
                <input type="number" id="edit-addon-price" name="price" step="0.01" required
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-right text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex flex-col gap-3 pt-1 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeEditAddonModal()"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Salvar alteracoes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Excluir adicional --}}
<div id="delete-addon-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     onclick="if(event.target===this) closeDeleteAddonModal()">
    <div class="w-full max-w-md rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Confirmar exclusao</p>
            <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">Remover adicional</h3>
        </div>
        <div class="space-y-2 px-6 py-5">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Voce esta prestes a remover <strong id="delete-addon-name" class="font-semibold text-gray-900 dark:text-gray-100"></strong>.
            </p>
            <p id="delete-addon-price" class="text-sm font-semibold text-gray-500 dark:text-gray-400"></p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Essa acao nao podera ser desfeita.</p>
        </div>
        <form method="POST" id="delete-addon-form"
              class="flex flex-col gap-3 border-t border-gray-200 dark:border-gray-700 px-6 py-4 sm:flex-row sm:justify-end">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteAddonModal()"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancelar
            </button>
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg border border-red-300 dark:border-red-600/50 bg-red-50 dark:bg-red-900/20 px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                Excluir adicional
            </button>
        </form>
    </div>
</div>

<script>
    window.rowIndex = {{ $prices->count() }};

    const inputCls = 'w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500';
    const dangerBtnCls = 'inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 dark:border-red-700/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors';

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
        const headerName = document.getElementById('header-fabric-name');
        const editLink = document.getElementById('edit-selected-fabric');
        const label = getFabricLabel(select);

        if (name) name.textContent = label;
        if (headerName) headerName.textContent = label;

        if (editLink && select) {
            if (select.value) {
                editLink.href = `${editLink.dataset.baseUrl}/${select.value}/edit`;
                editLink.classList.remove('pointer-events-none', 'opacity-40');
            } else {
                editLink.href = '{{ route('admin.tecidos.index') }}';
                editLink.classList.add('pointer-events-none', 'opacity-40');
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
        row.innerHTML = '<td colspan="3" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma faixa cadastrada. Clique em <strong class="font-semibold">Adicionar faixa</strong> para iniciar.</td>';
        tbody.appendChild(row);
    }

    window.addRow = function addRow() {
        const tbody = document.getElementById('prices-tbody');
        const emptyRow = document.getElementById('empty-row');
        if (!tbody) return;
        if (emptyRow) emptyRow.remove();

        const row = document.createElement('tr');
        row.className = 'border-t border-gray-200 dark:border-gray-700';
        row.innerHTML = `
            <td class="px-4 py-3">
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <input type="number" name="prices[${window.rowIndex}][quantity_from]" min="1" required placeholder="De"
                           class="${inputCls} text-center md:w-24">
                    <span class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">ate</span>
                    <input type="number" name="prices[${window.rowIndex}][quantity_to]" min="1" placeholder="Sem limite"
                           class="${inputCls} text-center md:w-28">
                </div>
            </td>
            <td class="px-4 py-3">
                <input type="number" name="prices[${window.rowIndex}][price]" step="0.01" min="0" required placeholder="0,00"
                       class="${inputCls} text-right font-semibold">
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeRow(this)" class="${dangerBtnCls}" title="Remover faixa">
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

    function toggleModal(id, open) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('hidden', !open);
        el.classList.toggle('flex', open);
        document.body.style.overflow = open ? 'hidden' : '';
    }

    window.openEditAddonModal = function(button) {
        const form = document.getElementById('edit-addon-form');
        const title = document.getElementById('edit-addon-modal-title');
        const nameInput = document.getElementById('edit-addon-name');
        const priceInput = document.getElementById('edit-addon-price');
        if (!form || !nameInput || !priceInput || !button) return;

        form.action = button.dataset.updateUrl || '';
        nameInput.value = (button.dataset.addonName || '').toUpperCase();
        priceInput.value = button.dataset.addonPrice || '';
        if (title) title.textContent = `Editar ${nameInput.value || 'adicional'}`;

        toggleModal('edit-addon-modal', true);
        setTimeout(() => nameInput.focus(), 10);
    };

    window.closeEditAddonModal = function() { toggleModal('edit-addon-modal', false); };

    window.openDeleteAddonModal = function(button) {
        const form = document.getElementById('delete-addon-form');
        const nameEl = document.getElementById('delete-addon-name');
        const priceEl = document.getElementById('delete-addon-price');
        if (!form || !button) return;

        form.action = button.dataset.deleteUrl || '';
        if (nameEl) nameEl.textContent = button.dataset.addonName || 'este adicional';
        if (priceEl) priceEl.textContent = button.dataset.addonPriceDisplay || '';

        toggleModal('delete-addon-modal', true);
    };

    window.closeDeleteAddonModal = function() { toggleModal('delete-addon-modal', false); };

    function initEditTypePage() {
        if (!document.getElementById('prices-form')) return;

        const fabricSelect = document.getElementById('tecido_id');
        const addonName = document.getElementById('addon-name');
        const editAddonName = document.getElementById('edit-addon-name');

        if (fabricSelect && fabricSelect.dataset.stpBound !== 'true') {
            fabricSelect.addEventListener('change', syncSelectedFabric);
            fabricSelect.dataset.stpBound = 'true';
            syncSelectedFabric();
        }

        [addonName, editAddonName].forEach(function(inp) {
            if (inp && inp.dataset.stpBound !== 'true') {
                inp.addEventListener('input', function() { this.value = this.value.toUpperCase(); });
                inp.dataset.stpBound = 'true';
            }
        });

        if (!window.__editTypeModalEscapeBound) {
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') { closeEditAddonModal(); closeDeleteAddonModal(); }
            });
            window.__editTypeModalEscapeBound = true;
        }

        ensureEmptyRow();
        syncPriceRangeCount();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEditTypePage, { once: true });
    } else {
        initEditTypePage();
    }
    document.addEventListener('ajax-content-loaded', initEditTypePage);

    window.addModelRow = function() {
        const input = document.getElementById('new-model-name');
        if (!input) return;
        const name = input.value.trim().toUpperCase();
        if (!name) { input.focus(); return; }

        for (const el of document.querySelectorAll('#models-list input[name="models[]"]')) {
            if (el.value.toUpperCase() === name) {
                alert('Este modelo ja existe.');
                input.focus();
                return;
            }
        }

        const emptyEl = document.getElementById('models-empty');
        if (emptyEl) emptyEl.remove();

        const list = document.getElementById('models-list');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-3';
        row.setAttribute('data-model-row', '');
        row.innerHTML = `
            <input type="text" name="models[]" value="${name}" readonly
                   class="flex-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase">
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">GG/EXG/G1/G2/G3/Esp</span>
                <input type="hidden" name="models_surcharge_disabled[]" value="" class="surcharge-disabled-input" disabled>
                <button type="button"
                        onclick="toggleModelSurcharge(this)"
                        data-model="${name}"
                        data-enabled="1"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none bg-indigo-600"
                        role="switch" aria-checked="true">
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 translate-x-5"></span>
                </button>
            </div>
            <button type="button" onclick="removeModelRow(this)"
                    class="${dangerBtnCls} flex-shrink-0" title="Remover modelo">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        list.appendChild(row);
        input.value = '';
        input.focus();
    };

    window.toggleModelSurcharge = function(btn) {
        const isEnabled = btn.getAttribute('data-enabled') === '1';
        const newEnabled = !isEnabled;
        const modelName = btn.getAttribute('data-model');
        const thumb = btn.querySelector('span');
        const hiddenInput = btn.closest('[data-model-row]').querySelector('.surcharge-disabled-input');

        btn.setAttribute('data-enabled', newEnabled ? '1' : '0');
        btn.setAttribute('aria-checked', newEnabled ? 'true' : 'false');
        btn.classList.toggle('bg-indigo-600', newEnabled);
        btn.classList.toggle('bg-gray-300', !newEnabled);
        if (thumb) {
            thumb.classList.toggle('translate-x-5', newEnabled);
            thumb.classList.toggle('translate-x-0', !newEnabled);
        }

        if (hiddenInput) {
            if (newEnabled) {
                hiddenInput.value = '';
                hiddenInput.disabled = true;
            } else {
                hiddenInput.value = modelName;
                hiddenInput.disabled = false;
            }
        }
    };

    window.removeModelRow = function(button) {
        const row = button.closest('[data-model-row]');
        if (row) row.remove();

        if (document.querySelectorAll('#models-list [data-model-row]').length === 0) {
            const list = document.getElementById('models-list');
            const empty = document.createElement('div');
            empty.id = 'models-empty';
            empty.className = 'rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400';
            empty.textContent = 'Nenhum modelo cadastrado. Os modelos padrao (BASICA, BABYLOOK, INFANTIL) serao usados.';
            list.appendChild(empty);
        }
    };

    window.addCollarRow = function() {
        const input = document.getElementById('new-collar-name');
        if (!input) return;
        const name = input.value.trim().toUpperCase();
        if (!name) { input.focus(); return; }

        for (const el of document.querySelectorAll('#collars-list input[name="collars[]"]')) {
            if (el.value.toUpperCase() === name) {
                alert('Esta gola ja existe.');
                input.focus();
                return;
            }
        }

        const emptyEl = document.getElementById('collars-empty');
        if (emptyEl) emptyEl.remove();

        const list = document.getElementById('collars-list');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-3';
        row.setAttribute('data-collar-row', '');
        row.innerHTML = `
            <input type="text" name="collars[]" value="${name}" readonly
                   class="flex-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase">
            <button type="button" onclick="removeCollarRow(this)"
                    class="${dangerBtnCls} flex-shrink-0" title="Remover gola">
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

        if (document.querySelectorAll('#collars-list [data-collar-row]').length === 0) {
            const list = document.getElementById('collars-list');
            const empty = document.createElement('div');
            empty.id = 'collars-empty';
            empty.className = 'rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400';
            empty.textContent = 'Nenhuma gola cadastrada. As golas padrao serao usadas.';
            list.appendChild(empty);
        }
    };

    window.submitCollarsForm = function() {
        const input = document.getElementById('new-collar-name');
        if (input && input.value.trim()) addCollarRow();
        document.getElementById('collars-form').submit();
    };
</script>
@endsection
