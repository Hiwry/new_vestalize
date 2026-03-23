@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.product-options.index', ['type' => $option->type]) }}" 
                       class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Opções de Produtos
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">Editar Opção</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Editar Opção: {{ $option->name }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $types[$option->type] }}</p>
        </div>

        <form method="POST" action="{{ route('admin.product-options.update', $option->id) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Nome *</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all @error('name') border-red-500 dark:border-red-500 @enderror"
                           value="{{ old('name', $option->name) }}"
                           placeholder="Digite o nome da opção">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                @if($option->type === 'cor')
                <div>
                    <label for="color_hex" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Cor da Opção</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="color_hex" name="color_hex" 
                               class="h-10 w-20 border border-gray-300 dark:border-slate-600 rounded-lg cursor-pointer p-1 bg-white dark:bg-slate-800"
                               value="{{ old('color_hex', $option->color_hex ?? '#ffffff') }}">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Selecione a cor representativa</span>
                    </div>
                    @error('color_hex')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                @if(in_array($option->type, ['tipo_corte', 'detalhe', 'gola']))
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Preço (R$)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                               value="{{ old('price', $option->price) }}">
                    </div>
                    <div>
                        <label for="cost" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Custo (R$)</label>
                        <input type="number" id="cost" name="cost" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white"
                               value="{{ old('cost', $option->cost) }}">
                    </div>
                </div>
                @else
                <input type="hidden" name="price" value="{{ $option->price }}">
                <input type="hidden" name="cost" value="{{ $option->cost }}">
                @endif

                @if($option->type === 'tipo_corte')
                <label for="sem_acrescimo" class="admin-check-label items-start">
                    <input type="checkbox" id="sem_acrescimo" name="sem_acrescimo"
                           class="admin-check-input"
                           {{ old('sem_acrescimo', $option->sem_acrescimo) ? 'checked' : '' }}>
                    <span class="admin-check-ui" aria-hidden="true"></span>
                    <span class="admin-check-copy">
                        <span class="admin-check-title">Sem acréscimo de GG / EXG / Especial</span>
                        <span class="admin-check-hint">Marque se este modelo não cobra acréscimo por tamanho especial.</span>
                    </span>
                </label>
                @endif


                @if(count($parents) > 0)
                    <div>
                        <div class="flex items-center justify-between mb-2 gap-3">
                            <label class="block text-xs text-gray-600 dark:text-slate-400 font-medium">{{ $parentLabel }} * (selecione um ou mais)</label>
                            <label for="select_all_parents" class="admin-check-select-all">
                                <input type="checkbox" id="select_all_parents" class="admin-check-input">
                                <span class="admin-check-ui" aria-hidden="true"></span>
                                <span>Selecionar Todos</span>
                            </label>
                        </div>
                        <div class="admin-check-panel max-h-48 overflow-y-auto">
                            @foreach($parents as $parent)
                                <label for="parent_{{ $parent->id }}" class="admin-check-option" data-parent-option>
                                    <input type="checkbox" 
                                           id="parent_{{ $parent->id }}" 
                                           name="parent_ids[]" 
                                           data-parent-checkbox
                                           value="{{ $parent->id }}"
                                           {{ in_array($parent->id, old('parent_ids', $option->parents->pluck('id')->toArray())) ? 'checked' : '' }}
                                         class="admin-check-input">
                                     <span class="admin-check-ui" aria-hidden="true"></span>
                                    <span class="admin-check-title text-sm">{{ $parent->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        @push('scripts')
                        <script>
                            (function() {
                                const selectAllCb = document.getElementById('select_all_parents');
                                const parentCbs = document.querySelectorAll('[data-parent-checkbox]');
                                const syncParentOptionState = () => {
                                    parentCbs.forEach(cb => {
                                        cb.closest('[data-parent-option]')?.setAttribute('data-checked', cb.checked ? 'true' : 'false');
                                    });
                                };

                                if (selectAllCb) {
                                    selectAllCb.addEventListener('change', function() {
                                        parentCbs.forEach(cb => {
                                            cb.checked = selectAllCb.checked;
                                        });
                                        syncParentOptionState();
                                    });

                                    // Update select all state based on individual checkboxes
                                    const updateSelectAllState = () => {
                                        const allChecked = Array.from(parentCbs).every(cb => cb.checked);
                                        const someChecked = Array.from(parentCbs).some(cb => cb.checked);
                                        selectAllCb.checked = allChecked;
                                        selectAllCb.indeterminate = someChecked && !allChecked;
                                        syncParentOptionState();
                                    };

                                    parentCbs.forEach(cb => {
                                        cb.addEventListener('change', updateSelectAllState);
                                    });

                                    // Initial state
                                    updateSelectAllState();
                                }
                            })();
                        </script>
                        @endpush
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selecione um ou mais {{ strtolower($parentLabel) }}(s) para associar</p>
                        @error('parent_ids')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Campos Ocultos Order/Status -->
                <input type="hidden" name="order" value="{{ $option->order }}">
                <!-- Active checkbox logic needs to be handled: if we don't send 'active', Laravel might think unchecked? No, usually handled by $request->has('active') or boolean conversion. 
                     Wait, if we hide it, we should ensure it sends the current value.
                     If 'active' was a checkbox, sending a hidden input with value 1 works if true, value 0 if false.
                -->
                <input type="hidden" name="active" value="{{ $option->active ? 1 : 0 }}">
                <input type="hidden" name="is_pinned" value="{{ $option->is_pinned ? 1 : 0 }}">

                <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.product-options.index', ['type' => $option->type]) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                    <button type="submit"
                            style="color: white !important;"
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
