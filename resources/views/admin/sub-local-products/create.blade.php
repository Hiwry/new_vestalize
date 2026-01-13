@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Novo Produto - Sub. Local</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Cadastre um novo produto de sublimação local</p>
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
    <form action="{{ route('admin.sub-local-products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

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
                            value="{{ old('name') }}"
                            placeholder="Ex: Caneca Porcelana"
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
                            <option value="vestuario" {{ old('category') == 'vestuario' ? 'selected' : '' }}>Vestuário</option>
                            <option value="canecas" {{ old('category') == 'canecas' ? 'selected' : '' }}>Canecas</option>
                            <option value="acessorios" {{ old('category') == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                            <option value="diversos" {{ old('category', 'diversos') == 'diversos' ? 'selected' : '' }}>Diversos</option>
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
                            value="{{ old('price') }}"
                            placeholder="0,00"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <!-- Custo -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Custo (R$)
                        </label>
                        <input 
                            type="number" 
                            name="cost" 
                            id="cost" 
                            step="0.01"
                            min="0"
                            value="{{ old('cost', 0) }}"
                            placeholder="0,00"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Valor de custo para cálculo de margem</p>
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
                            value="{{ old('sort_order', 0) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <!-- Imagem -->
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Imagem do Produto
                        </label>
                        <input 
                            id="image" 
                            name="image" 
                            type="file" 
                            accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50"
                        >
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Tamanho recomendado: 500x500px. Máximo 2MB.</p>
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
                        >{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Opções do Produto -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Opções do Produto</h3>
                
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Produto Ativo (Disponível no Wizard)</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="requires_customization" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Requer Personalização (Passar pela etapa de arte)</span>
                    </label>
                </div>
            </div>

            <!-- Seção de Tamanhos -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6" x-data="{ requiresSize: false }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tamanhos Disponíveis</h3>
                    <label class="flex items-center cursor-pointer">
                        <span class="mr-3 text-sm text-gray-700 dark:text-gray-300">Produto com Tamanhos</span>
                        <input type="checkbox" name="requires_size" value="1" x-model="requiresSize" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div x-show="requiresSize" x-collapse x-cloak class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <i class="fa-solid fa-info-circle text-indigo-500 mr-1"></i>
                        Selecione os tamanhos disponíveis para este produto (camisa, calça, casaco, etc):
                    </p>
                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2">
                        @php
                            $defaultSizes = ['PP', 'P', 'M', 'G', 'GG', 'XGG', 'EG', 'EGG', 'PLUS', '2', '4', '6', '8', '10', '12', '14', '16'];
                        @endphp
                        @foreach($defaultSizes as $size)
                            <label class="flex items-center justify-center px-3 py-2.5 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-indigo-400 transition-all has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/30 has-[:checked]:border-indigo-500 has-[:checked]:shadow-sm">
                                <input type="checkbox" name="available_sizes[]" value="{{ $size }}" class="sr-only peer">
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

            <!-- Seção de Preços por Quantidade -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6" 
                 x-data="quantityPricing([], false)">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fa-solid fa-layer-group text-indigo-500 mr-2"></i>
                        Preços por Quantidade
                    </h3>
                    <label class="flex items-center cursor-pointer">
                        <span class="mr-3 text-sm text-gray-700 dark:text-gray-300">Habilitar Tabela de Preços</span>
                        <input type="checkbox" name="has_quantity_pricing" value="1" x-model="enabled" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div x-show="enabled" x-collapse x-cloak class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-700/50 dark:to-gray-700/30 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <i class="fa-solid fa-info-circle text-indigo-500 mr-1"></i>
                        Configure faixas de quantidade com preços diferenciados (descontos por volume):
                    </p>
                    
                    <!-- Tabela de Preços -->
                    <div class="space-y-3">
                        <template x-for="(tier, index) in tiers" :key="index">
                            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Qtd. Mínima</label>
                                    <input type="number" 
                                           x-model.number="tier.min_quantity" 
                                           min="1"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Qtd. Máxima</label>
                                    <input type="number" 
                                           x-model.number="tier.max_quantity" 
                                           min="1"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Preço (R$)</label>
                                    <input type="number" 
                                           x-model.number="tier.price" 
                                           step="0.01"
                                           min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <button type="button" 
                                        @click="removeTier(index)"
                                        class="mt-5 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Botão Adicionar Faixa -->
                    <button type="button" 
                            @click="addTier()"
                            class="mt-4 w-full py-2 px-4 border-2 border-dashed border-indigo-300 dark:border-indigo-600 rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors text-sm font-medium">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Adicionar Faixa de Preço
                    </button>

                    <!-- Input hidden para enviar os dados -->
                    <input type="hidden" name="quantity_pricing" :value="JSON.stringify(tiers)">

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                        <i class="fa-solid fa-lightbulb text-amber-500 mr-1"></i>
                        Exemplo: 1-10 unidades = R$ 25,00 | 11-50 unidades = R$ 22,00 | 51+ unidades = R$ 18,00
                    </p>
                </div>
            </div>

            <!-- Opção de Edição de Preço no Pedido -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <i class="fa-solid fa-pen-to-square text-green-500 mr-2"></i>
                            Preço Editável no Pedido
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Permite que o vendedor altere o preço deste produto durante a criação do pedido
                        </p>
                    </div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="allow_price_edit" value="1" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                    </label>
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
                    Salvar Produto
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function quantityPricing(initialTiers, initialEnabled) {
    return {
        tiers: initialTiers && initialTiers.length > 0 ? initialTiers : [],
        enabled: initialEnabled,

        addTier() {
            const lastTier = this.tiers[this.tiers.length - 1];
            const newMinQty = lastTier ? (lastTier.max_quantity + 1) : 1;
            
            this.tiers.push({
                min_quantity: newMinQty,
                max_quantity: newMinQty + 9,
                price: 0
            });
        },

        removeTier(index) {
            this.tiers.splice(index, 1);
        }
    };
}
</script>
@endpush
@endsection

