@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@push('styles')
<style>
    .stf-page-shell {
        --stf-surface-from: #f7f9ff;
        --stf-surface-to: #edf3ff;
        --stf-text-primary: #10203a;
        --stf-text-secondary: #60708a;
        --stf-card-bg: #ffffff;
        --stf-soft-bg: #eef4ff;
        --stf-input-bg: #31588f;
        --stf-card-border: rgba(59, 130, 246, 0.12);
        --stf-accent: #7c3aed;
        --stf-accent-strong: #6d28d9;
        --stf-success-bg: rgba(16, 185, 129, 0.12);
        --stf-success-text: #0f766e;
        --stf-danger-bg: rgba(244, 63, 94, 0.08);
        --stf-danger-border: rgba(244, 63, 94, 0.18);
        --stf-danger-text: #be123c;
        --stf-link-color: #6d28d9;
        --stf-link-hover: #5b21b6;
        --stf-edit-color: #6d28d9;
        --stf-delete-bg: rgba(124, 58, 237, 0.1);
        --stf-delete-border: rgba(124, 58, 237, 0.18);
        --stf-delete-text: #6d28d9;
        --stf-delete-hover: rgba(124, 58, 237, 0.16);
        --stf-overlay: rgba(15, 23, 42, 0.32);
        background: linear-gradient(180deg, var(--stf-surface-from) 0%, var(--stf-surface-to) 100%);
        min-height: calc(100vh - 7rem);
    }
    html.dark .stf-page-shell {
        --stf-surface-from: #0d1830;
        --stf-surface-to: #0b1322;
        --stf-text-primary: #e5edf8;
        --stf-text-secondary: #91a4c0;
        --stf-card-bg: #10203a;
        --stf-soft-bg: #122746;
        --stf-input-bg: #162847;
        --stf-card-border: rgba(148, 163, 184, 0.12);
        --stf-success-bg: rgba(16, 185, 129, 0.18);
        --stf-success-text: #bbf7d0;
        --stf-danger-bg: rgba(244, 63, 94, 0.14);
        --stf-danger-border: rgba(244, 63, 94, 0.24);
        --stf-danger-text: #fecdd3;
        --stf-link-color: #c4b5fd;
        --stf-link-hover: #ddd6fe;
        --stf-edit-color: #c4b5fd;
        --stf-delete-bg: rgba(124, 58, 237, 0.16);
        --stf-delete-border: rgba(124, 58, 237, 0.2);
        --stf-delete-text: #f5f3ff;
        --stf-delete-hover: rgba(124, 58, 237, 0.24);
        --stf-overlay: rgba(2, 6, 23, 0.78);
    }
    .stf-page-shell,
    .stf-page-shell *,
    .stf-page-shell *::before,
    .stf-page-shell *::after {
        box-shadow: none !important;
        text-shadow: none !important;
        filter: none !important;
        --shadow: none !important;
        --tw-shadow: 0 0 #0000 !important;
        --tw-shadow-colored: 0 0 #0000 !important;
        --tw-ring-shadow: 0 0 #0000 !important;
        --tw-ring-offset-shadow: 0 0 #0000 !important;
    }
    .stf-page-shell,
    .stf-page-shell h1,
    .stf-page-shell h2,
    .stf-page-shell h3,
    .stf-page-shell p,
    .stf-page-shell span,
    .stf-page-shell a,
    .stf-page-shell th,
    .stf-page-shell td,
    .stf-page-shell label {
        color: var(--stf-text-primary) !important;
    }
    .stf-muted { color: var(--stf-text-secondary) !important; }
    .stf-card {
        background: var(--stf-card-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
    }
    .stf-btn-primary {
        background: var(--stf-accent) !important;
        border: 1px solid transparent !important;
        color: #ffffff !important;
    }
    .stf-page-shell a.stf-btn-primary,
    .stf-page-shell a.stf-btn-success,
    .stf-page-shell button.stf-btn-primary,
    .stf-page-shell button.stf-btn-success,
    .stf-page-shell button.stf-btn-secondary {
        color: #ffffff !important;
    }
    .stf-btn-primary *,
    .stf-btn-success *,
    .stf-btn-secondary * {
        color: inherit !important;
    }
    .stf-btn-primary:hover { background: var(--stf-accent-strong) !important; }
    .stf-btn-secondary {
        background: var(--stf-input-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
        color: var(--stf-text-primary) !important;
    }
    .stf-btn-secondary:hover { background: var(--stf-soft-bg) !important; }
    .stf-btn-success {
        background: #16a34a !important;
        border: 1px solid transparent !important;
        color: #ffffff !important;
    }
    .stf-btn-success:hover { background: #15803d !important; }
    .stf-table {
        background: var(--stf-card-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    .stf-table thead,
    .stf-table thead tr,
    .stf-table thead th {
        background: var(--stf-soft-bg) !important;
    }
    .stf-table tbody,
    .stf-table tbody tr,
    .stf-table tbody td {
        background: var(--stf-card-bg) !important;
        border-color: var(--stf-card-border) !important;
    }
    .stf-table tbody tr:hover,
    .stf-table tbody tr:hover td {
        background: var(--stf-soft-bg) !important;
    }
    .stf-status-active {
        background: var(--stf-success-bg) !important;
        color: var(--stf-success-text) !important;
        border: 1px solid rgba(16, 185, 129, 0.15) !important;
    }
    .stf-status-inactive {
        background: var(--stf-danger-bg) !important;
        color: var(--stf-danger-text) !important;
        border: 1px solid var(--stf-danger-border) !important;
    }
    .stf-link {
        color: var(--stf-link-color) !important;
    }
    .stf-link:hover {
        color: var(--stf-link-hover) !important;
    }
    .stf-row-action {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }
    .stf-row-action svg {
        width: 18px;
        height: 18px;
    }
    .stf-edit-btn {
        background: var(--stf-input-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
        color: var(--stf-edit-color) !important;
    }
    .stf-edit-btn:hover {
        background: var(--stf-soft-bg) !important;
        color: var(--stf-link-hover) !important;
    }
    .stf-delete {
        background: var(--stf-delete-bg) !important;
        border: 1px solid var(--stf-delete-border) !important;
        color: var(--stf-delete-text) !important;
    }
    .stf-delete:hover {
        background: var(--stf-delete-hover) !important;
    }
    .stf-action-trigger {
        background: var(--stf-input-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
    }
    .stf-action-trigger:hover {
        background: var(--stf-soft-bg) !important;
    }
    .stf-page-shell .fixed.inset-0.overflow-y-auto > div.mb-6.bg-white {
        background: transparent !important;
        border: 0 !important;
        border-radius: 24px !important;
        box-shadow: none !important;
        color: var(--stf-text-primary) !important;
    }
    .stf-page-shell .fixed.inset-0.overflow-y-auto > div.fixed.inset-0.transform > div.absolute.inset-0.bg-gray-500 {
        background: var(--stf-overlay) !important;
    }
    .stf-modal-panel {
        background: var(--stf-card-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
        border-radius: 24px !important;
    }
    .stf-modal-title {
        color: var(--stf-text-primary) !important;
    }
    .stf-modal-copy {
        color: var(--stf-text-secondary) !important;
    }
    .stf-modal-input {
        background: var(--stf-input-bg) !important;
        background-color: var(--stf-input-bg) !important;
        background-image: none !important;
        border: 1px solid var(--stf-card-border) !important;
        color: #ffffff !important;
        border-radius: 16px !important;
        min-height: 48px;
        width: 100%;
        -webkit-text-fill-color: #ffffff !important;
        caret-color: #ffffff !important;
    }
    .stf-page-shell .stf-modal-panel input.stf-modal-input,
    .stf-page-shell .stf-modal-panel textarea.stf-modal-input,
    .stf-page-shell .stf-modal-panel select.stf-modal-input {
        background: var(--stf-input-bg) !important;
        background-color: var(--stf-input-bg) !important;
        background-image: none !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
    }
    .stf-page-shell .stf-modal-panel input.stf-modal-input:focus,
    .stf-page-shell .stf-modal-panel textarea.stf-modal-input:focus,
    .stf-page-shell .stf-modal-panel select.stf-modal-input:focus {
        background: var(--stf-input-bg) !important;
        background-color: var(--stf-input-bg) !important;
        color: #ffffff !important;
    }
    .stf-modal-input::placeholder {
        color: rgba(255, 255, 255, 0.72) !important;
    }
    .stf-modal-textarea {
        min-height: 112px;
        resize: vertical;
    }
    .stf-modal-error {
        color: #fda4af !important;
    }
    .stf-checkbox-row {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
    .stf-checkbox-input {
        width: 18px;
        height: 18px;
        accent-color: var(--stf-accent);
    }
    .stf-modal-toggle {
        min-height: 48px;
        width: 100%;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0 1rem;
        background: var(--stf-input-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
        border-radius: 16px !important;
        color: #ffffff !important;
    }
    .stf-modal-toggle span {
        color: #ffffff !important;
    }
    .stf-modal-summary {
        background: var(--stf-soft-bg) !important;
        border: 1px solid var(--stf-card-border) !important;
        border-radius: 20px !important;
    }
</style>
@endpush

@section('content')
<div class="stf-page-shell -mx-4 px-4 py-5 md:-mx-6 md:px-6">
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold">Tecidos</h1>
                <p class="stf-muted text-sm">Gerencie os tecidos do catalogo com o mesmo tema da area de costura.</p>
            </div>
            <button type="button"
               x-data
               x-on:click="$dispatch('open-modal', 'fabric-create')"
               class="stf-btn-primary inline-flex min-h-[48px] items-center justify-center gap-2 rounded-2xl px-6 text-sm font-semibold text-white transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Novo tecido
            </button>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.products.index') }}"
               class="stf-btn-primary inline-flex min-h-[44px] items-center justify-center gap-2 rounded-2xl px-4 text-sm font-semibold text-white transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Produtos
            </a>
            <a href="{{ route('admin.personalizacoes.index') }}"
               class="stf-btn-success inline-flex min-h-[44px] items-center justify-center gap-2 rounded-2xl px-4 text-sm font-semibold text-white transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Personalizacoes
            </a>
            <a href="{{ route('admin.modelos.index') }}"
               class="stf-btn-primary inline-flex min-h-[44px] items-center justify-center gap-2 rounded-2xl px-4 text-sm font-semibold text-white transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7A2 2 0 019.172 20.414l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Modelos
            </a>
        </div>

        @if(session('success'))
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-200">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
            {{ session('error') }}
        </div>
        @endif

        <div class="stf-card overflow-hidden rounded-[28px]">
            <div class="overflow-x-auto">
                <table class="stf-table min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] stf-muted">Nome</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] stf-muted">Descricao</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] stf-muted">Ordem</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] stf-muted">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] stf-muted">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tecidos as $tecido)
                        <tr>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-semibold">{{ strtoupper($tecido->name) }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm stf-muted">{{ Str::limit($tecido->description ?? '-', 50) }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-semibold">{{ $tecido->order }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($tecido->active)
                                    <span class="stf-status-active inline-flex rounded-full px-3 py-1 text-xs font-semibold">Ativo</span>
                                @else
                                    <span class="stf-status-inactive inline-flex rounded-full px-3 py-1 text-xs font-semibold">Inativo</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <button type="button"
                                            x-data
                                            x-on:click="$dispatch('open-modal', 'fabric-edit-{{ $tecido->id }}')"
                                            class="stf-row-action stf-edit-btn"
                                            title="Editar tecido"
                                            aria-label="Editar tecido">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button"
                                            x-data
                                            x-on:click="$dispatch('open-modal', 'fabric-delete-{{ $tecido->id }}')"
                                            class="stf-row-action stf-delete"
                                            title="Excluir tecido"
                                            aria-label="Excluir tecido">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="stf-muted text-sm">Nenhum tecido cadastrado.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <x-modal name="fabric-create" maxWidth="md" :show="old('_modal') === 'fabric-create'" focusable>
            <div class="stf-modal-panel p-6 space-y-6">
                <div class="space-y-2">
                        <h3 class="stf-modal-title text-xl font-semibold">Novo tecido</h3>
                        <p class="stf-modal-copy text-sm">
                            Cadastre um novo tecido sem sair da listagem.
                        </p>
                </div>

                <form action="{{ route('admin.tecidos.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_modal" value="fabric-create">

                    <div class="space-y-2">
                        <label for="fabric-create-name" class="block text-xs font-semibold uppercase tracking-[0.22em] stf-modal-copy">Nome</label>
                        <input id="fabric-create-name"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="stf-modal-input px-4 py-3 text-sm font-semibold"
                               required>
                        @error('name')
                            <p class="stf-modal-error text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="fabric-create-description" class="block text-xs font-semibold uppercase tracking-[0.22em] stf-modal-copy">Descricao</label>
                        <textarea id="fabric-create-description"
                                  name="description"
                                  class="stf-modal-input stf-modal-textarea px-4 py-3 text-sm"
                                  placeholder="Opcional">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="stf-modal-error text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 md:items-end">
                        <div class="space-y-2">
                            <label for="fabric-create-order" class="block text-xs font-semibold uppercase tracking-[0.22em] stf-modal-copy">Ordem</label>
                            <input id="fabric-create-order"
                                   type="number"
                                   name="order"
                                   min="0"
                                   value="{{ old('order') }}"
                                   class="stf-modal-input px-4 py-3 text-sm font-semibold"
                                   placeholder="Automatica">
                            @error('order')
                                <p class="stf-modal-error text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <span class="block text-xs font-semibold uppercase tracking-[0.22em] stf-modal-copy">Status</span>
                            <label for="fabric-create-active" class="stf-modal-toggle text-sm font-medium">
                                <input id="fabric-create-active"
                                       type="checkbox"
                                       name="active"
                                       value="1"
                                       class="stf-checkbox-input"
                                       {{ old('active', '1') ? 'checked' : '' }}>
                                <span>Ativo</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit" class="stf-btn-primary inline-flex w-full min-h-[46px] items-center justify-center rounded-2xl px-4 text-sm font-semibold transition-colors">
                            Salvar tecido
                        </button>
                        <button type="button"
                                x-data
                                x-on:click="$dispatch('close-modal', 'fabric-create')"
                                class="stf-btn-secondary inline-flex w-full min-h-[46px] items-center justify-center rounded-2xl px-4 text-sm font-semibold transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        @foreach($tecidos as $tecido)
            <x-modal name="fabric-edit-{{ $tecido->id }}" maxWidth="sm" :show="old('_modal') === 'fabric-edit-' . $tecido->id" focusable>
                <div class="stf-modal-panel p-6 space-y-5">
                    <div class="space-y-2">
                        <h3 class="stf-modal-title text-xl font-semibold">Editar tecido</h3>
                        <p class="stf-modal-copy text-sm">
                            Altere o nome diretamente aqui, sem sair da listagem.
                        </p>
                    </div>

                    <form action="{{ route('admin.tecidos.update', $tecido) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_modal" value="fabric-edit-{{ $tecido->id }}">

                        <div class="space-y-2">
                            <label for="fabric-name-{{ $tecido->id }}" class="block text-xs font-semibold uppercase tracking-[0.22em] stf-modal-copy">Nome</label>
                            <input id="fabric-name-{{ $tecido->id }}"
                                   type="text"
                                   name="name"
                                   value="{{ old('name', $tecido->name) }}"
                                   class="stf-modal-input px-4 py-3 text-sm font-semibold"
                                   required>
                            @error('name')
                                @if(old('_modal') === 'fabric-edit-' . $tecido->id)
                                    <p class="stf-modal-error text-sm">{{ $message }}</p>
                                @endif
                            @enderror
                        </div>

                        <input type="hidden" name="description" value="{{ $tecido->description }}">
                        <input type="hidden" name="order" value="{{ $tecido->order }}">
                        @if($tecido->active)
                            <input type="hidden" name="active" value="1">
                        @endif

                        <div class="stf-modal-summary px-4 py-4 space-y-2">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="stf-modal-copy">Descricao</span>
                                <span class="text-right font-medium">{{ $tecido->description ? Str::limit($tecido->description, 40) : '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="stf-modal-copy">Ordem</span>
                                <span class="font-medium">{{ $tecido->order }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button type="submit" class="stf-btn-primary inline-flex w-full min-h-[46px] items-center justify-center rounded-2xl px-4 text-sm font-semibold transition-colors">
                                Salvar nome
                            </button>
                            <button type="button"
                                    x-data
                                    x-on:click="$dispatch('close-modal', 'fabric-edit-{{ $tecido->id }}')"
                                    class="stf-btn-secondary inline-flex w-full min-h-[46px] items-center justify-center rounded-2xl px-4 text-sm font-semibold transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="fabric-delete-{{ $tecido->id }}" maxWidth="sm">
                <div class="stf-modal-panel p-6 space-y-5">
                    <div class="space-y-2">
                        <h3 class="stf-modal-title text-xl font-semibold">Excluir tecido</h3>
                        <p class="stf-modal-copy text-sm">
                            Tem certeza que deseja excluir <strong>{{ strtoupper($tecido->name) }}</strong>? Esta acao nao pode ser desfeita.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <form action="{{ route('admin.tecidos.destroy', $tecido) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="stf-delete inline-flex w-full min-h-[46px] items-center justify-center rounded-2xl px-4 text-sm font-semibold transition-colors">
                                Confirmar exclusao
                            </button>
                        </form>

                        <button type="button"
                                x-data
                                x-on:click="$dispatch('close-modal', 'fabric-delete-{{ $tecido->id }}')"
                                class="stf-btn-secondary inline-flex w-full min-h-[46px] items-center justify-center rounded-2xl px-4 text-sm font-semibold transition-colors">
                            Cancelar
                        </button>
                    </div>
                </div>
            </x-modal>
        @endforeach
    </div>
</div>
@endsection
