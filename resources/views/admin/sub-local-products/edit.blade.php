@extends('layouts.admin')

@section('content')
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
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3 tracking-tight">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl text-white shadow-lg shadow-indigo-200 dark:shadow-none transform rotate-3">
                    <i class="fa-solid fa-pen-to-square text-2xl text-white" style="color: #ffffff !important;"></i>
                </div>
                Editar Produto
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg font-medium ml-1">
                {{ $subLocalProduct->name }}
            </p>
        </div>
        <a href="{{ route('admin.sub-local-products.index') }}" 
           class="px-5 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95">
            <i class="fa-solid fa-arrow-left text-indigo-500"></i>
            Voltar
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-2xl">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
        <form id="main-product-form" action="{{ route('admin.sub-local-products.update', $subLocalProduct->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <!-- Informações do Produto -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-indigo-500"></i>
                        Informações do Produto
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Nome do Produto <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                value="{{ old('name', $subLocalProduct->name) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            >
                        </div>

                        <!-- Categoria -->
                        <div>
                            <label for="category" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Categoria <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="category" 
                                name="category" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            >
                                <option value="vestuario" {{ old('category', $subLocalProduct->category) == 'vestuario' ? 'selected' : '' }}>Vestuário</option>
                                <option value="canecas" {{ old('category', $subLocalProduct->category) == 'canecas' ? 'selected' : '' }}>Canecas</option>
                                <option value="acessorios" {{ old('category', $subLocalProduct->category) == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                                <option value="diversos" {{ old('category', $subLocalProduct->category) == 'diversos' ? 'selected' : '' }}>Diversos</option>
                            </select>
                        </div>

                        <!-- Preço -->
                        <div>
                            <label for="price" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Preço de Venda (R$) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="price" 
                                id="price" 
                                step="0.01"
                                value="{{ old('price', $subLocalProduct->price) }}"
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            >
                        </div>

                        <!-- Custo -->
                        <div>
                            <label for="cost" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Custo (R$)
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="cost" 
                                    id="cost" 
                                    step="0.01"
                                    min="0"
                                    value="{{ old('cost', $subLocalProduct->cost ?? 0) }}"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                >
                            </div>
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium ml-1">Valor de custo para cálculo de margem</p>
                        </div>

                        <!-- Ordem de Exibição -->
                        <div>
                            <label for="sort_order" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Ordem de Exibição
                            </label>
                            <input 
                                type="number" 
                                name="sort_order" 
                                id="sort_order" 
                                value="{{ old('sort_order', $subLocalProduct->sort_order) }}"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            >
                        </div>

                        <!-- Imagem -->
                        <div class="md:col-span-2">
                            <label for="image" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Imagem do Produto
                            </label>
                            <div class="p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50/50 dark:bg-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                @if($subLocalProduct->image)
                                    <div class="mb-4 flex items-center gap-4 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 w-fit">
                                        <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                                            <img src="{{ Storage::url($subLocalProduct->image) }}" alt="{{ $subLocalProduct->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">Imagem atual</p>
                                            <p class="text-xs text-indigo-500 font-medium">Será substituída se enviar nova</p>
                                        </div>
                                    </div>
                                @endif
                                <input 
                                    id="image" 
                                    name="image" 
                                    type="file" 
                                    accept="image/*"
                                    class="w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-black file:bg-indigo-50 dark:file:bg-indigo-600 file:text-indigo-700 dark:file:text-white hover:file:bg-indigo-100 dark:hover:file:bg-indigo-700 transition-all cursor-pointer"
                                    style="color-scheme: dark;"
                                >
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 font-medium">Deixe em branco para manter a imagem atual. Máximo 2MB.</p>
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Descrição (Opcional)
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            >{{ old('description', $subLocalProduct->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Opções do Produto -->
                <div class="border-t border-gray-100 dark:border-gray-700 pt-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-indigo-500"></i>
                        Opções do Produto
                    </h3>
                    
                    <div class="space-y-4 bg-gray-50 dark:bg-gray-700/30 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <label class="flex items-center cursor-pointer p-2 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ $subLocalProduct->is_active ? 'checked' : '' }} class="sr-only peer text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/10 border border-gray-300 dark:border-white/20 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-emerald-500 shadow-inner"></div>
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-bold">Produto Ativo (Disponível no Wizard)</span>
                        </label>

                        <label class="flex items-center cursor-pointer p-2 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <input type="checkbox" name="requires_customization" value="1" {{ $subLocalProduct->requires_customization ? 'checked' : '' }} class="sr-only peer text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/10 border border-gray-300 dark:border-white/20 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-indigo-600 shadow-inner"></div>
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-bold">Requer Personalização (Passar pela etapa de arte)</span>
                        </label>
                    </div>
                </div>

                <!-- Seção de Tamanhos -->
                <div class="border-t border-gray-100 dark:border-gray-700 pt-8" x-data="{ requiresSize: {{ $subLocalProduct->requires_size ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-ruler-combined text-indigo-500"></i>
                            Tamanhos Disponíveis
                        </h3>
                        <label class="flex items-center cursor-pointer">
                            <span class="mr-3 text-sm text-gray-700 dark:text-gray-300 font-bold">Habilitar Tamanhos</span>
                            <input type="checkbox" name="requires_size" value="1" x-model="requiresSize" {{ $subLocalProduct->requires_size ? 'checked' : '' }} class="sr-only peer">
                            <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/10 border border-gray-300 dark:border-white/20 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-indigo-600 shadow-inner"></div>
                        </label>
                    </div>

                    <div x-show="requiresSize" x-collapse x-cloak class="bg-indigo-50/50 dark:bg-gray-700/30 rounded-2xl p-6 border border-indigo-100 dark:border-gray-600">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-indigo-500"></i>
                            Selecione os tamanhos disponíveis para este produto:
                        </p>
                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2">
                            @php
                                $defaultSizes = ['PP', 'P', 'M', 'G', 'GG', 'XGG', 'EG', 'EGG', 'PLUS', '2', '4', '6', '8', '10', '12', '14', '16'];
                                $selectedSizes = $subLocalProduct->available_sizes ?? [];
                            @endphp
                            @foreach($defaultSizes as $size)
                                <div class="contents">
                                    <input type="checkbox" 
                                           id="size_{{ $size }}" 
                                           name="available_sizes[]" 
                                           value="{{ $size }}" 
                                           {{ in_array($size, $selectedSizes) ? 'checked' : '' }} 
                                           class="peer sr-only">
                                    <label for="size_{{ $size }}" 
                                           class="flex items-center justify-center px-3 py-2.5 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-500 transition-all peer-checked:!bg-indigo-600 peer-checked:!border-indigo-600 peer-checked:shadow-md select-none text-gray-400 peer-checked:!text-white font-black text-sm">
                                        {{ $size }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Seção de Preços por Quantidade -->
                <div class="border-t border-gray-100 dark:border-gray-700 pt-8" 
                     x-data="quantityPricing({{ json_encode($subLocalProduct->quantity_pricing ?? []) }}, {{ $subLocalProduct->has_quantity_pricing ? 'true' : 'false' }})">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-500"></i>
                            Preços por Quantidade
                        </h3>
                        <label class="flex items-center cursor-pointer">
                            <span class="mr-3 text-sm text-gray-700 dark:text-gray-300 font-bold">Habilitar Tabela</span>
                            <input type="checkbox" name="has_quantity_pricing" value="1" x-model="enabled" class="sr-only peer">
                            <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/10 border border-gray-300 dark:border-white/20 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-purple-600 shadow-inner"></div>
                        </label>
                    </div>

                    <div x-show="enabled" x-collapse x-cloak class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl p-6 border border-indigo-100 dark:border-gray-700 shadow-sm">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 flex items-center gap-2 bg-white/50 dark:bg-gray-700/50 p-3 rounded-lg w-fit">
                            <i class="fa-solid fa-lightbulb text-amber-500"></i>
                            Configure faixas de quantidade com preços diferenciados (descontos por volume).
                        </p>
                        
                        <!-- Tabela de Preços -->
                        <div class="space-y-4">
                            <template x-for="(tier, index) in tiers" :key="index">
                                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4 bg-white dark:bg-gray-700/50 p-4 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="grid grid-cols-3 gap-4 flex-1 w-full">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Qtd. Mínima</label>
                                            <input type="number" 
                                                   x-model.number="tier.min_quantity" 
                                                   min="1"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Qtd. Máxima</label>
                                            <input type="number" 
                                                   x-model.number="tier.max_quantity" 
                                                   min="1"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Preço (R$)</label>
                                            <input type="number" 
                                                   x-model.number="tier.price" 
                                                   step="0.01"
                                                   min="0"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-bold text-emerald-600 dark:text-emerald-400">
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="removeTier(index)"
                                            class="w-full sm:w-auto p-2.5 text-red-500 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-lg transition-colors border border-red-100 dark:border-red-900/30">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Botão Adicionar Faixa -->
                        <button type="button" 
                                @click="addTier()"
                                class="mt-6 w-full py-3 px-4 border-2 border-dashed border-indigo-300 dark:border-indigo-700/50 rounded-xl !text-white hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all font-bold text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-plus"></i>
                            Adicionar Faixa de Preço
                        </button>
                    </div>
                </div>

                <!-- Opção de Edição de Preço no Pedido -->
                <div class="border-t border-gray-100 dark:border-gray-700 pt-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50 dark:bg-gray-700/30 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-pen-to-square text-green-500"></i>
                                Preço Editável no Pedido
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-lg">
                                Permite que o vendedor altere o preço deste produto manualmente durante a criação de um pedido (overriding).
                            </p>
                        </div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_price_edit" value="1" {{ $subLocalProduct->allow_price_edit ? 'checked' : '' }} class="sr-only peer">
                            <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/10 border border-gray-300 dark:border-white/20 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-green-600 shadow-inner"></div>
                        </label>
                    </div>
                </div>


                </form>

                <!-- Seção de Adicionais -->
                <div class="border-t border-gray-100 dark:border-gray-700 pt-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-tags text-yellow-500"></i>
                            Adicionais do Produto
                        </h3>
                    </div>

                    <div class="bg-gray-50/80 dark:bg-gray-700/30 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-circle-plus text-yellow-500"></i>
                            Cadastre itens extras opcionais (ex: Embalagem de Presente, Gravação):
                        </p>

                        <!-- Lista de Adicionais -->
                        <div class="space-y-3 mb-8">
                            @forelse($subLocalProduct->addons as $addon)
                                <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm transition-transform hover:scale-[1.01]">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center border border-yellow-100 dark:border-yellow-900/30">
                                            <i class="fa-solid fa-plus text-yellow-600 dark:text-yellow-400"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ $addon->name }}</span>
                                            <span class="block text-xs text-green-600 dark:text-green-400 font-black tracking-wide bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full w-fit mt-1">+ R$ {{ number_format($addon->price, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.sub-local-products.addons.destroy', [$subLocalProduct->id, $addon->id]) }}" method="POST" onsubmit="return confirm('Excluir este adicional?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-8 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    <div class="w-12 h-12 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                        <i class="fa-solid fa-hashtag text-xl"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum adicional cadastrado.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Formulário para Novo Adicional -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-indigo-100 dark:border-indigo-900/30 shadow-md shadow-indigo-100 dark:shadow-none relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                            <h4 class="text-xs font-black text-indigo-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-bolt"></i> Novo Adicional
                            </h4>
                            <form action="{{ route('admin.sub-local-products.addons.store', $subLocalProduct->id) }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                                @csrf
                                <div class="flex-1">
                                    <label class="sr-only">Nome</label>
                                    <input type="text" name="addon_name" required placeholder="Nome do adicional (ex: Embalagem)" 
                                           class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-shadow">
                                </div>
                                <div class="w-full sm:w-40 relative">
                                    <label class="sr-only">Preço</label>
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 text-sm font-bold z-10 w-10 pointer-events-none">R$</span>
                                    <input type="number" name="addon_price" required step="0.01" min="0" placeholder="0,00"
                                           style="padding-left: 4rem !important;"
                                           class="w-full pl-16 pr-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-shadow font-medium">
                                </div>
                                <button type="submit" 
                                        style="color: #ffffff !important;"
                                        class="px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 dark:hover:shadow-none transition-all duration-300 shrink-0 flex items-center justify-center gap-2 active:scale-95">
                                    <i class="fa-solid fa-plus text-white"></i> Adicionar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.sub-local-products.index') }}" 
                       class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-bold text-sm active:scale-95">
                        Cancelar
                    </a>
                    <button type="submit" 
                            form="main-product-form"
                            style="color: #ffffff !important;"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 dark:shadow-none transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-check text-white"></i>
                        Salvar Alterações
                    </button>
                </div>
            </div>
        
    </div>
</div>

@endsection

