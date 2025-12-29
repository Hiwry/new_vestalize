@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Produto</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Edite as informações do produto</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Título *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $product->title) }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descrição</label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Categoria e Subcategoria -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Categoria</label>
                    <select id="category_id" 
                            name="category_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="subcategory_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subcategoria</label>
                    <select id="subcategory_id" 
                            name="subcategory_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecione uma subcategoria</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" 
                                    data-category="{{ $subcategory->category_id }}"
                                    {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Preço e Tipo de Venda -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Preço 
                        <span id="price-label" class="text-xs text-gray-500 dark:text-gray-400">(por unidade)</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           step="0.01"
                           min="0"
                           value="{{ old('price', $product->price) }}"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="price-hint">
                        Ex: Para linhas, agulhas, máquinas, costura = preço por unidade
                    </p>
                </div>

                <div>
                    <label for="sale_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Venda *</label>
                    <select id="sale_type" 
                            name="sale_type"
                            required
                            onchange="updatePriceLabel()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="unidade" {{ old('sale_type', $product->sale_type ?? 'unidade') == 'unidade' ? 'selected' : '' }}>Unidade (Linhas, Agulhas, Máquinas, Costura, etc.)</option>
                        <option value="kg" {{ old('sale_type', $product->sale_type) == 'kg' ? 'selected' : '' }}>Por Kg (Tecidos e Malhas)</option>
                        <option value="metro" {{ old('sale_type', $product->sale_type) == 'metro' ? 'selected' : '' }}>Por Metro (Tecidos e Malhas)</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="sale-type-hint">
                        Use "Unidade" para produtos como linhas, agulhas, máquinas e costura
                    </p>
                </div>
            </div>

            <!-- Configurações de Aplicação (para camisa/calça) -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center mb-4">
                    <input type="checkbox" 
                           id="allow_application" 
                           name="allow_application" 
                           value="1"
                           {{ old('allow_application', $product->allow_application) ? 'checked' : '' }}
                           onchange="toggleApplicationTypes()"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="allow_application" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Permitir Aplicação (para camisa/calça)
                    </label>
                </div>

                <div id="application-types-container" style="display: {{ old('allow_application', $product->allow_application) ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipos de Aplicação Permitidos:</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="app_sublimacao_local" 
                                   name="application_types[]" 
                                   value="sublimacao_local"
                                   {{ in_array('sublimacao_local', old('application_types', $product->application_types ?? [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="app_sublimacao_local" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Sublimação Local
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="app_dtf" 
                                   name="application_types[]" 
                                   value="dtf"
                                   {{ in_array('dtf', old('application_types', $product->application_types ?? [])) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="app_dtf" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                DTF
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="tecido_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tecido</label>
                    <select id="tecido_id" 
                            name="tecido_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecione um tecido</option>
                        @foreach($tecidos as $tecido)
                            <option value="{{ $tecido->id }}" {{ old('tecido_id', $product->tecido_id) == $tecido->id ? 'selected' : '' }}>
                                {{ $tecido->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="personalizacao_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Personalização</label>
                    <select id="personalizacao_id" 
                            name="personalizacao_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecione uma personalização</option>
                        @foreach($personalizacoes as $personalizacao)
                            <option value="{{ $personalizacao->id }}" {{ old('personalizacao_id', $product->personalizacao_id) == $personalizacao->id ? 'selected' : '' }}>
                                {{ $personalizacao->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="modelo_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Modelo</label>
                    <select id="modelo_id" 
                            name="modelo_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecione um modelo</option>
                        @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id }}" {{ old('modelo_id', $product->modelo_id) == $modelo->id ? 'selected' : '' }}>
                                {{ $modelo->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ordem</label>
                <input type="number" 
                       id="order" 
                       name="order" 
                       value="{{ old('order', $product->order) }}"
                       min="0"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
            </div>

            @if($product->images && $product->images->count() > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Imagens Atuais</label>
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images as $image)
                    <div class="relative">
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             alt="{{ $product->title }}"
                             class="w-full h-32 object-cover rounded {{ $image->is_primary ? 'ring-2 ring-indigo-500' : '' }}">
                        <div class="absolute top-2 right-2 flex gap-1">
                            @if(!$image->is_primary)
                            <form action="{{ route('admin.products.set-primary-image', $image) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-indigo-600 text-white p-1 rounded text-xs" title="Definir como principal">
                                    ⭐
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.products.delete-image', $image) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta imagem?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white p-1 rounded text-xs" title="Excluir">
                                    ✕
                                </button>
                            </form>
                        </div>
                        @if($image->is_primary)
                        <div class="absolute bottom-2 left-2 bg-indigo-600 text-white px-2 py-1 rounded text-xs">Principal</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adicionar Novas Imagens</label>
                <input type="file" 
                       id="images" 
                       name="images[]" 
                       multiple
                       accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100">
            </div>

            <div class="flex items-center">
                <input type="checkbox" 
                       id="active" 
                       name="active" 
                       value="1"
                       {{ old('active', $product->active) ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Produto ativo</label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" 
               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Atualizar
            </button>
        </div>
    </form>
</div>

@push('page-scripts')
<script>
// Filtrar subcategorias por categoria
document.getElementById('category_id')?.addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');
    const options = subcategorySelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const optionCategory = option.getAttribute('data-category');
            if (categoryId === '' || optionCategory === categoryId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        }
    });
    
    // Reset subcategory if it doesn't belong to selected category
    if (categoryId && subcategorySelect.value) {
        const selectedOption = subcategorySelect.options[subcategorySelect.selectedIndex];
        if (selectedOption.getAttribute('data-category') !== categoryId) {
            subcategorySelect.value = '';
        }
    }
});

// Toggle tipos de aplicação
function toggleApplicationTypes() {
    const allowApplication = document.getElementById('allow_application').checked;
    const container = document.getElementById('application-types-container');
    
    if (container) {
        container.style.display = allowApplication ? 'block' : 'none';
        
        // Desmarcar checkboxes se desabilitar aplicação
        if (!allowApplication) {
            document.getElementById('app_sublimacao_local').checked = false;
            document.getElementById('app_dtf').checked = false;
        }
    }
}

// Atualizar label do preço baseado no tipo de venda
function updatePriceLabel() {
    const saleType = document.getElementById('sale_type').value;
    const priceLabel = document.getElementById('price-label');
    const priceHint = document.getElementById('price-hint');
    const saleTypeHint = document.getElementById('sale-type-hint');
    
    if (saleType === 'unidade') {
        priceLabel.textContent = '(por unidade)';
        priceHint.textContent = 'Ex: Para linhas, agulhas, máquinas, costura = preço por unidade';
        saleTypeHint.textContent = 'Use "Unidade" para produtos como linhas, agulhas, máquinas e costura';
    } else if (saleType === 'kg') {
        priceLabel.textContent = '(por kg)';
        priceHint.textContent = 'Ex: Tecido algodão = R$ 25,00 por kg';
        saleTypeHint.textContent = 'Use "Por Kg" para tecidos e malhas vendidos por peso';
    } else if (saleType === 'metro') {
        priceLabel.textContent = '(por metro)';
        priceHint.textContent = 'Ex: Tecido malha = R$ 15,00 por metro';
        saleTypeHint.textContent = 'Use "Por Metro" para tecidos e malhas vendidos por comprimento';
    }
}

// Executar ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    updatePriceLabel();
});
</script>
@endpush
@endsection

