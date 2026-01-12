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

                <div>
                    <label for="price" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Preço Adicional (R$)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all @error('price') border-red-500 dark:border-red-500 @enderror"
                           value="{{ old('price', $option->price) }}"
                           placeholder="0.00">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe 0 se não houver custo adicional</p>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cost" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Custo (R$)</label>
                    <input type="number" id="cost" name="cost" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all @error('cost') border-red-500 dark:border-red-500 @enderror"
                           value="{{ old('cost', $option->cost ?? '0.00') }}"
                           placeholder="0.00">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Custo base desta opção (usado para cálculo de lucro)</p>
                    @error('cost')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>


                @if(count($parents) > 0)
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">{{ $parentLabel }} * (selecione um ou mais)</label>
                        <div class="border border-gray-300 dark:border-slate-600 rounded-lg p-4 max-h-48 overflow-y-auto bg-white dark:bg-slate-800">
                            @foreach($parents as $parent)
                                <div class="flex items-center mb-2 last:mb-0">
                                    <input type="checkbox" 
                                           id="parent_{{ $parent->id }}" 
                                           name="parent_ids[]" 
                                           value="{{ $parent->id }}"
                                           {{ in_array($parent->id, old('parent_ids', $option->parents->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-slate-600 rounded">
                                    <label for="parent_{{ $parent->id }}" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                        {{ $parent->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selecione um ou mais {{ strtolower($parentLabel) }}(s) para associar</p>
                        @error('parent_ids')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="order" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Ordem de Exibição</label>
                    <input type="number" id="order" name="order" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all @error('order') border-red-500 dark:border-red-500 @enderror"
                           value="{{ old('order', $option->order) }}"
                           placeholder="0">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Menor número aparece primeiro</p>
                    @error('order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col space-y-3">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="active" name="active" value="1" 
                                   {{ old('active', $option->active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 border-gray-300 dark:border-gray-500 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="active" class="font-medium text-gray-700 dark:text-gray-300">
                                Ativo
                            </label>
                            <p class="text-gray-500 dark:text-gray-400">Esta opção estará disponível para seleção</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1" 
                                   {{ old('is_pinned', $option->is_pinned) ? 'checked' : '' }}
                                   class="h-4 w-4 text-yellow-500 focus:ring-yellow-500 border-gray-300 dark:border-gray-500 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_pinned" class="font-medium text-gray-700 dark:text-gray-300">
                                Fixar no Topo
                            </label>
                            <p class="text-gray-500 dark:text-gray-400">Esta opção aparecerá no início da lista</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.product-options.index', ['type' => $option->type]) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                    <button type="submit"
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
