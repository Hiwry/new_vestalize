@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.personalization-prices.index') }}"
                       class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Preços de Personalização
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ $types[$type] }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
            <svg class="mt-0.5 w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-800 dark:text-red-200">
            <svg class="mt-0.5 w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $types[$type] }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Gerencie os tipos de produto. Cada tipo possui suas próprias faixas de preço, tecidos, adicionais e modelos — configurados de forma independente.
        </p>
    </div>

    {{-- Types card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tipos de Produto</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Adicione tipos como Conjunto Esportivo, Bandeira, Winderbanner, etc. Cada um é configurado separado.
            </p>
        </div>

        {{-- Quick add + custom add --}}
        @php
            $productTypeNames = collect($productTypes ?? [])->map(fn($t) => mb_strtolower(trim($t->name ?? '')))->toArray();
            $quickTypeNames   = ['Conjunto Esportivo', 'Bandeira', 'Winderbanner'];
        @endphp

        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">Adicionar tipo rápido</p>

            <div class="flex flex-wrap gap-2 mb-5">
                @foreach($quickTypeNames as $quickName)
                    @php $alreadyExists = in_array(mb_strtolower($quickName), $productTypeNames, true); @endphp
                    @if($alreadyExists)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-xs font-medium text-emerald-700 dark:text-emerald-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $quickName }}
                        </span>
                    @else
                        <form method="POST" action="{{ route('admin.sublimation-products.types.store') }}">
                            @csrf
                            <input type="hidden" name="name" value="{{ $quickName }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-300 hover:border-gray-900 dark:hover:border-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                {{ $quickName }}
                            </button>
                        </form>
                    @endif
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.sublimation-products.types.store') }}" class="flex flex-col sm:flex-row gap-2 max-w-lg">
                @csrf
                <input type="text" name="name"
                       placeholder="Novo tipo (ex: Regata, Cropped, Mochila…)"
                       class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 dark:focus:border-gray-300 dark:focus:ring-gray-300 transition-colors"
                       required maxlength="100">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adicionar Tipo
                </button>
            </form>
        </div>

        {{-- Types grid --}}
        <div class="p-6">
            @php $productTypes = collect($productTypes ?? []); @endphp

            @if($productTypes->isEmpty())
                <div class="flex flex-col items-center justify-center py-14 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nenhum tipo cadastrado ainda</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Use os atalhos ou o campo acima para adicionar os primeiros tipos</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($productTypes as $productType)
                        <div class="group relative flex flex-col rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:border-gray-400 dark:hover:border-gray-500 hover:shadow-sm transition-all">

                            {{-- Delete / Padrão badge --}}
                            <div class="absolute top-3 right-3">
                                @if($productType->tenant_id === auth()->user()->tenant_id)
                                    {{-- Hidden delete form, submitted by the confirm modal --}}
                                    <form id="delete-form-{{ $productType->id }}"
                                          method="POST"
                                          action="{{ route('admin.sublimation-products.types.destroy', $productType) }}"
                                          class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="openDeleteModal({{ $productType->id }}, '{{ addslashes($productType->name) }}')"
                                            class="flex items-center justify-center w-7 h-7 rounded-lg border border-red-200 dark:border-red-800/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100"
                                            title="Excluir tipo">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50">
                                        Padrão
                                    </span>
                                @endif
                            </div>

                            {{-- Type info --}}
                            <div class="flex-1 pr-8 mb-4">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug">{{ $productType->name }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-mono mt-1">{{ $productType->slug }}</p>
                            </div>

                            {{-- Configure button --}}
                            <a href="{{ route('admin.sublimation-products.edit-type', $productType->slug) }}"
                               class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-xs font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                Configurar
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Delete confirmation modal --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Excluir tipo</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Tem certeza que deseja excluir <strong id="delete-type-name" class="text-gray-700 dark:text-gray-200"></strong>?
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeDeleteModal()"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let _deleteFormId = null;

    function openDeleteModal(typeId, typeName) {
        _deleteFormId = typeId;
        document.getElementById('delete-type-name').textContent = typeName;
        const modal = document.getElementById('delete-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        _deleteFormId = null;
        const modal = document.getElementById('delete-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function confirmDelete() {
        if (_deleteFormId) {
            document.getElementById('delete-form-' + _deleteFormId).submit();
        }
    }

    // Close on backdrop click
    document.getElementById('delete-modal').addEventListener('click', function (e) {
        if (e.target === this) closeDeleteModal();
    });

    window.openDeleteModal  = openDeleteModal;
    window.closeDeleteModal = closeDeleteModal;
    window.confirmDelete    = confirmDelete;
</script>
@endpush

@endsection
