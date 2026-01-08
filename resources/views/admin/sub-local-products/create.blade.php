@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Novo Produto - Sub. Local</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
        <form action="{{ route('admin.sub-local-products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="name">Nome do Produto</label>
                    <input id="name" name="name" type="text" class="form-input w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" placeholder="Ex: Caneca Porcelana" required>
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="category">Categoria</label>
                    <select id="category" name="category" class="form-select w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" required>
                        <option value="vestuario">Vestuário</option>
                        <option value="canecas">Canecas</option>
                        <option value="acessorios">Acessórios</option>
                        <option value="diversos" selected>Diversos</option>
                    </select>
                </div>

                <!-- Preço -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="price">Preço de Venda (R$)</label>
                    <input id="price" name="price" type="number" step="0.01" class="form-input w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" placeholder="0,00" required>
                </div>

                <!-- Ordem de Exibição -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="sort_order">Ordem de Exibição</label>
                    <input id="sort_order" name="sort_order" type="number" class="form-input w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md" value="0">
                </div>

                <!-- Imagem -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="image">Imagem do Produto</label>
                    <input id="image" name="image" type="file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*">
                    <p class="mt-2 text-xs text-gray-500">Tamanho recomendado: 500x500px. Máximo 2MB.</p>
                </div>

                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="description">Descrição (Opcional)</label>
                    <textarea id="description" name="description" rows="3" class="form-textarea w-full bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md"></textarea>
                </div>

                <!-- Status Ativo -->
                <div class="md:col-span-2 space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="form-checkbox text-indigo-600 rounded">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Produto Ativo (Disponível no Wizard)</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="requires_customization" value="1" checked class="form-checkbox text-indigo-600 rounded">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Requer Personalização (Passar pela etapa de arte)</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.sub-local-products.index') }}" class="btn border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg">Cancelar</a>
                <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold">Salvar Produto</button>
            </div>
        </form>
    </div>
</div>
@endsection
