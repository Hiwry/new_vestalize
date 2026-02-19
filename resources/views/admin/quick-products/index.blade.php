@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Cadastro Rápido de Produtos</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Cadastre rapidamente malhas, tecidos e outros produtos para venda no PDV</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Cadastro de Malhas/Tecidos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center mb-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Cadastrar Malha/Tecido</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Algodão, Brim, Malha, etc. (por Kg ou Metro)</p>
            </div>
        </div>

        <form action="{{ route('admin.quick-products.fabric.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="fabric_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome da Malha/Tecido *
                </label>
                <input type="text" 
                       id="fabric_title" 
                       name="title" 
                       value="{{ old('title') }}"
                       placeholder="Ex: Algodão, Brim, Malha Estampada"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="fabric_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Preço *
                    </label>
                    <input type="number" 
                           id="fabric_price" 
                           name="price" 
                           step="0.01"
                           min="0"
                           value="{{ old('price') }}"
                           placeholder="0.00"
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fabric_sale_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tipo de Venda *
                    </label>
                    <select id="fabric_sale_type" 
                            name="sale_type"
                            required
                            onchange="updateFabricPriceLabel()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="kg" {{ old('sale_type') == 'kg' ? 'selected' : '' }}>Por Kg</option>
                        <option value="metro" {{ old('sale_type') == 'metro' ? 'selected' : '' }}>Por Metro</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="fabric-price-hint">
                        Preço por kg
                    </p>
                </div>
            </div>

            <div>
                <label for="fabric_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descrição (opcional)
                </label>
                <textarea id="fabric_description" 
                          name="description" 
                          rows="2"
                          placeholder="Descrição do tecido/malha..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" 
                       id="fabric_active" 
                       name="active" 
                       value="1"
                       {{ old('active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="fabric_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    Produto ativo
                </label>
            </div>

            <button type="submit" 
                    class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cadastrar Malha/Tecido
            </button>
        </form>
    </div>

    <!-- Cadastro de Outros Produtos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center mb-4">
            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg mr-3">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Cadastrar Outros Produtos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Linhas, Agulhas, Máquinas, Costura, etc. (por Unidade)</p>
            </div>
        </div>

        <form action="{{ route('admin.quick-products.product.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="product_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome do Produto *
                </label>
                <input type="text" 
                       id="product_title" 
                       name="title" 
                       value="{{ old('title') }}"
                       placeholder="Ex: Linha de Costura, Agulha, Máquina de Costura"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Preço por Unidade *
                </label>
                <input type="number" 
                       id="product_price" 
                       name="price" 
                       step="0.01"
                       min="0"
                       value="{{ old('price') }}"
                       placeholder="0.00"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Preço unitário do produto
                </p>
                @error('price')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="product_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descrição (opcional)
                </label>
                <textarea id="product_description" 
                          name="description" 
                          rows="2"
                          placeholder="Descrição do produto..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" 
                       id="product_active" 
                       name="active" 
                       value="1"
                       {{ old('active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="product_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    Produto ativo
                </label>
            </div>

            <button type="submit" 
                    class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Cadastrar Produto
            </button>
        </form>
    </div>
</div>

<!-- Link para gerenciamento completo -->
<div class="mt-6 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Gerenciamento Completo</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Para editar produtos, adicionar imagens ou configurações avançadas, acesse a página completa de produtos.
            </p>
        </div>
        <a href="{{ route('admin.products.index') }}" 
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
            Ver Todos os Produtos
        </a>
    </div>
</div>

@push('page-scripts')
<script>
function updateFabricPriceLabel() {
    const saleType = document.getElementById('fabric_sale_type').value;
    const hint = document.getElementById('fabric-price-hint');
    
    if (saleType === 'kg') {
        hint.textContent = 'Preço por kg';
    } else if (saleType === 'metro') {
        hint.textContent = 'Preço por metro';
    }
}

// Executar ao carregar
document.addEventListener('DOMContentLoaded', function() {
    updateFabricPriceLabel();
});
</script>
@endpush
@endsection

