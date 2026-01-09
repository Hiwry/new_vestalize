@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Produto</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $subLocalProduct->name }}</p>
        </div>
        <a href="{{ route('admin.sub-local-products.index') }}" 
           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            ← Voltar
        </a>
    </div>
</div>

@if($errors->any())
<div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <form action="{{ route('admin.sub-local-products.update', $subLocalProduct->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Informações do Produto -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Produto</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome do Produto <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $subLocalProduct->name) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <!-- Categoria -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoria <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="category" 
                            name="category" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="vestuario" {{ old('category', $subLocalProduct->category) == 'vestuario' ? 'selected' : '' }}>Vestuário</option>
                            <option value="canecas" {{ old('category', $subLocalProduct->category) == 'canecas' ? 'selected' : '' }}>Canecas</option>
                            <option value="acessorios" {{ old('category', $subLocalProduct->category) == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                            <option value="diversos" {{ old('category', $subLocalProduct->category) == 'diversos' ? 'selected' : '' }}>Diversos</option>
                        </select>
                    </div>

                    <!-- Preço -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Preço de Venda (R$) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="price" 
                            id="price" 
                            step="0.01"
                            value="{{ old('price', $subLocalProduct->price) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <!-- Ordem de Exibição -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Ordem de Exibição
                        </label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            id="sort_order" 
                            value="{{ old('sort_order', $subLocalProduct->sort_order) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <!-- Imagem -->
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Imagem do Produto
                        </label>
                        @if($subLocalProduct->image)
                            <div class="mb-3 flex items-center gap-4">
                                <img src="{{ Storage::url($subLocalProduct->image) }}" alt="{{ $subLocalProduct->name }}" class="w-20 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Imagem atual</span>
                            </div>
                        @endif
                        <input 
                            id="image" 
                            name="image" 
                            type="file" 
                            accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50"
                        >
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Deixe em branco para manter a imagem atual. Máximo 2MB.</p>
                    </div>

                    <!-- Descrição -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrição (Opcional)
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >{{ old('description', $subLocalProduct->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Opções do Produto -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Opções do Produto</h3>
                
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $subLocalProduct->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Produto Ativo (Disponível no Wizard)</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="requires_customization" value="1" {{ $subLocalProduct->requires_customization ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Requer Personalização (Passar pela etapa de arte)</span>
                    </label>
                </div>
            </div>

            <!-- Seção de Tamanhos -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6" x-data="{ requiresSize: {{ $subLocalProduct->requires_size ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tamanhos Disponíveis</h3>
                    <label class="flex items-center cursor-pointer">
                        <span class="mr-3 text-sm text-gray-700 dark:text-gray-300">Produto com Tamanhos</span>
                        <input type="checkbox" name="requires_size" value="1" x-model="requiresSize" {{ $subLocalProduct->requires_size ? 'checked' : '' }} class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div x-show="requiresSize" x-collapse class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <i class="fa-solid fa-info-circle text-indigo-500 mr-1"></i>
                        Selecione os tamanhos disponíveis para este produto:
                    </p>
                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2">
                        @php
                            $defaultSizes = ['PP', 'P', 'M', 'G', 'GG', 'XGG', 'EG', 'EGG', 'PLUS', '2', '4', '6', '8', '10', '12', '14', '16'];
                            $selectedSizes = $subLocalProduct->available_sizes ?? [];
                        @endphp
                        @foreach($defaultSizes as $size)
                            <label class="flex items-center justify-center px-3 py-2.5 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-indigo-400 transition-all has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/30 has-[:checked]:border-indigo-500 has-[:checked]:shadow-sm">
                                <input type="checkbox" name="available_sizes[]" value="{{ $size }}" {{ in_array($size, $selectedSizes) ? 'checked' : '' }} class="sr-only peer">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                        <i class="fa-solid fa-lightbulb text-amber-500 mr-1"></i>
                        Dica: Os tamanhos selecionados aparecerão como opções durante o pedido no wizard.
                    </p>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.sub-local-products.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                    Atualizar Produto
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
