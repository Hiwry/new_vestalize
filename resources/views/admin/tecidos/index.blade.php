@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Tecidos</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gerencie os tecidos do catalogo.</p>
        </div>
        <button type="button"
           x-data
           x-on:click="$dispatch('open-modal', 'fabric-create')"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo tecido
        </button>
    </div>

    <div class="mb-5 flex flex-wrap gap-2">
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Produtos
        </a>
        <a href="{{ route('admin.personalizacoes.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            Personalizacoes
        </a>
        <a href="{{ route('admin.modelos.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7A2 2 0 019.172 20.414l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            Modelos
        </a>
    </div>

    @if(session('success'))
    <div class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-200 dark:border-emerald-600/30 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-300">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-5 flex items-center gap-2 rounded-xl border border-red-200 dark:border-red-600/30 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-700 dark:text-red-300">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Descricao</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ordem</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tecidos as $tecido)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ strtoupper($tecido->name) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($tecido->description ?? '-', 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $tecido->order }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($tecido->active)
                                <span class="inline-flex rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Ativo</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Inativo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        x-data
                                        x-on:click="$dispatch('open-modal', 'fabric-edit-{{ $tecido->id }}')"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        title="Editar tecido">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button"
                                        x-data
                                        x-on:click="$dispatch('open-modal', 'fabric-delete-{{ $tecido->id }}')"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 dark:border-red-700/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        title="Excluir tecido">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            Nenhum tecido cadastrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: Novo tecido --}}
    <x-modal name="fabric-create" maxWidth="md" :show="old('_modal') === 'fabric-create'" focusable>
        <div class="p-6 space-y-5">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Novo tecido</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Cadastre um novo tecido sem sair da listagem.</p>
            </div>

            <form action="{{ route('admin.tecidos.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="fabric-create">

                <div>
                    <label for="fabric-create-name" class="mb-1.5 block text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Nome</label>
                    <input id="fabric-create-name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fabric-create-description" class="mb-1.5 block text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Descricao</label>
                    <textarea id="fabric-create-description"
                              name="description"
                              rows="3"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 resize-y"
                              placeholder="Opcional">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2 md:items-end">
                    <div>
                        <label for="fabric-create-order" class="mb-1.5 block text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Ordem</label>
                        <input id="fabric-create-order"
                               type="number"
                               name="order"
                               min="0"
                               value="{{ old('order') }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Automatica">
                        @error('order')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span class="mb-1.5 block text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Status</span>
                        <label for="fabric-create-active" class="admin-check-label">
                            <input id="fabric-create-active"
                                   type="checkbox"
                                   name="active"
                                   value="1"
                                   class="admin-check-input"
                                   {{ old('active', '1') ? 'checked' : '' }}>
                            <span class="admin-check-ui" aria-hidden="true"></span>
                            <span class="admin-check-copy">
                                <span class="admin-check-title">Ativo</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="space-y-3 pt-1">
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        Salvar tecido
                    </button>
                    <button type="button"
                            x-data
                            x-on:click="$dispatch('close-modal', 'fabric-create')"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    @foreach($tecidos as $tecido)
        {{-- Modal: Editar tecido --}}
        <x-modal name="fabric-edit-{{ $tecido->id }}" maxWidth="sm" :show="old('_modal') === 'fabric-edit-' . $tecido->id" focusable>
            <div class="p-6 space-y-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Editar tecido</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Altere o nome diretamente aqui, sem sair da listagem.</p>
                </div>

                <form action="{{ route('admin.tecidos.update', $tecido) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="fabric-edit-{{ $tecido->id }}">

                    <div>
                        <label for="fabric-name-{{ $tecido->id }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Nome</label>
                        <input id="fabric-name-{{ $tecido->id }}"
                               type="text"
                               name="name"
                               value="{{ old('name', $tecido->name) }}"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('name')
                            @if(old('_modal') === 'fabric-edit-' . $tecido->id)
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @endif
                        @enderror
                    </div>

                    <input type="hidden" name="description" value="{{ $tecido->description }}">
                    <input type="hidden" name="order" value="{{ $tecido->order }}">
                    @if($tecido->active)
                        <input type="hidden" name="active" value="1">
                    @endif

                    <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 px-4 py-3 space-y-2">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Descricao</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100 text-right">{{ $tecido->description ? Str::limit($tecido->description, 40) : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Ordem</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $tecido->order }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                            Salvar nome
                        </button>
                        <button type="button"
                                x-data
                                x-on:click="$dispatch('close-modal', 'fabric-edit-{{ $tecido->id }}')"
                                class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Modal: Excluir tecido --}}
        <x-modal name="fabric-delete-{{ $tecido->id }}" maxWidth="sm">
            <div class="p-6 space-y-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Excluir tecido</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Tem certeza que deseja excluir <strong class="font-semibold text-gray-900 dark:text-gray-100">{{ strtoupper($tecido->name) }}</strong>? Esta acao nao pode ser desfeita.
                    </p>
                </div>

                <div class="space-y-3">
                    <form action="{{ route('admin.tecidos.destroy', $tecido) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg border border-red-300 dark:border-red-600/50 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                            Confirmar exclusao
                        </button>
                    </form>

                    <button type="button"
                            x-data
                            x-on:click="$dispatch('close-modal', 'fabric-delete-{{ $tecido->id }}')"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </x-modal>
    @endforeach

</div>
@endsection
