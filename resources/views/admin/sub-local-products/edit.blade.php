@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Editar Produto - {{ $subLocalProduct->name }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
        <form action="{{ route('admin.sub-local-products.update', $subLocalProduct->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="name">Nome do Produto</label>
                    <input id="name" name="name" type="text" class="form-input w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" value="{{ $subLocalProduct->name }}" required>
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="category">Categoria</label>
                    <select id="category" name="category" class="form-select w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" required>
                        <option value="vestuario" {{ $subLocalProduct->category == 'vestuario' ? 'selected' : '' }}>Vestuário</option>
                        <option value="canecas" {{ $subLocalProduct->category == 'canecas' ? 'selected' : '' }}>Canecas</option>
                        <option value="acessorios" {{ $subLocalProduct->category == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                        <option value="diversos" {{ $subLocalProduct->category == 'diversos' ? 'selected' : '' }}>Diversos</option>
                    </select>
                </div>

                <!-- Preço -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="price">Preço de Venda (R$)</label>
                    <input id="price" name="price" type="number" step="0.01" class="form-input w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" value="{{ $subLocalProduct->price }}" required>
                </div>

                <!-- Ordem de Exibição -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="sort_order">Ordem de Exibição</label>
                    <input id="sort_order" name="sort_order" type="number" class="form-input w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" value="{{ $subLocalProduct->sort_order }}">
                </div>

                <!-- Imagem -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="image">Imagem do Produto (Deixe em branco para manter a atual)</label>
                    @if($subLocalProduct->image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($subLocalProduct->image) }}" alt="{{ $subLocalProduct->name }}" class="w-24 h-24 rounded-lg object-cover border border-gray-200">
                        </div>
                    @endif
                    <input id="image" name="image" type="file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*">
                </div>

                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="description">Descrição (Opcional)</label>
                    <textarea id="description" name="description" rows="3" class="form-textarea w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md">{{ $subLocalProduct->description }}</textarea>
                </div>

                <!-- Status Ativo -->
                <div class="md:col-span-2 space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $subLocalProduct->is_active ? 'checked' : '' }} class="form-checkbox text-indigo-600 rounded">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Produto Ativo (Disponível no Wizard)</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="requires_customization" value="1" {{ $subLocalProduct->requires_customization ? 'checked' : '' }} class="form-checkbox text-indigo-600 rounded">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Requer Personalização (Passar pela etapa de arte)</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.sub-local-products.index') }}" class="btn border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg">Cancelar</a>
                <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold">Atualizar Produto</button>
            </div>
        </form>
    </div>
</div>
@endsection
