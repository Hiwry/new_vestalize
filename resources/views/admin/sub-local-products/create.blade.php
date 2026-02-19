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
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <i class="fa-solid fa-plus-circle text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Novo Produto Personalizado</h1>
                <p class="text-gray-600 dark:text-gray-400">Cadastre um novo item para o wizard de vendas</p>
            </div>
        </div>
        <a href="{{ route('admin.sub-local-products.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Voltar para Lista
        </a>
    </div>
</div>

@if($errors->any())
<div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl flex items-start gap-3">
    <i class="fa-solid fa-circle-exclamation mt-1"></i>
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="max-w-5xl">
    <form action="{{ route('admin.sub-local-products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8 space-y-10">
                
                <!-- Informações Básicas -->
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="fa-solid fa-tag"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Informações Básicas</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-1">
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nome do Produto <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all"
                                   placeholder="Ex: Caneca Porcelana 325ml">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Categoria <span class="text-red-500">*</span></label>
                            <select id="category" name="category" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="canecas" {{ old('category') == 'canecas' ? 'selected' : '' }}>Canecas & Copos</option>
                                <option value="vestuario" {{ old('category') == 'vestuario' ? 'selected' : '' }}>Vestuário</option>
                                <option value="acessorios" {{ old('category') == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                                <option value="diversos" {{ old('category') == 'diversos' ? 'selected' : '' }}>Diversos</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Descrição Curta</label>
                            <textarea id="description" name="description" rows="2"
                                      class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all"
                                      placeholder="Descreva brevemente o produto...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100 dark:border-gray-700">

                <!-- Preços e Ordem -->
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Precificação e Exibição</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Preço de Venda <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">R$</span>
                                <input type="number" name="price" id="price" step="0.01" value="{{ old('price') }}" required
                                       class="w-full pl-16 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all"
                                       placeholder="0,00">
                            </div>
                        </div>

                        <div>
                            <label for="cost" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Preço de Custo</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">R$</span>
                                <input type="number" name="cost" id="cost" step="0.01" value="{{ old('cost', 0) }}"
                                       class="w-full pl-16 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all"
                                       placeholder="0,00">
                            </div>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ordem de Exibição</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100 dark:border-gray-700">

                <!-- Mídia -->
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Mídia do Produto</h3>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/30 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center">
                        <input type="file" name="image" id="image" accept="image/*" class="sr-only">
                        <label for="image" class="cursor-pointer group">
                            <div class="mx-auto w-16 h-16 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-400 group-hover:text-indigo-500 group-hover:scale-110 transition-all border border-gray-200 dark:border-gray-600 mb-4 shadow-sm">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                            </div>
                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-lg">Clique para fazer upload</span>
                            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">PNG, JPG ou WEBP. Máximo 2MB.</p>
                        </label>
                    </div>
                </section>

                <hr class="border-gray-100 dark:border-gray-700">

                <!-- Opções Avançadas -->
                <section class="space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Configurações do Wizard</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Switch Ativo -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100 block">Produto Ativo</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 italic">Disponível no canal de vendas</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/30 border border-gray-300 dark:border-white/50 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-indigo-600 shadow-inner"></div>
                            </label>
                        </div>

                        <!-- Switch Personalização -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100 block">Requer Personalização</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 italic">Será enviado para etapa de arte</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="requires_customization" value="1" checked class="sr-only peer">
                                <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/30 border border-gray-300 dark:border-white/50 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-indigo-600 shadow-inner"></div>
                            </label>
                        </div>

                        <!-- Switch Preço Editável -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100 block">Preço Editável</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 italic">Vendedor pode alterar valor</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_price_edit" value="1" class="sr-only peer">
                                <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/30 border border-gray-300 dark:border-white/50 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-green-600 shadow-inner"></div>
                            </label>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100 dark:border-gray-700">

                <!-- Tamanhos -->
                <section x-data="{ requiresSize: false }">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Grade de Tamanhos</h3>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="requires_size" value="1" x-model="requiresSize" class="sr-only peer">
                            <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/30 border border-gray-300 dark:border-white/50 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-indigo-600 shadow-inner"></div>
                        </label>
                    </div>

                    <div x-show="requiresSize" x-collapse x-cloak class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                            @php $defaultSizes = ['PP','P','M','G','GG','XGG','EG','EGG','PLUS','2','4','6','8','10','12','14','16']; @endphp
                            @foreach($defaultSizes as $size)
                                <label class="flex items-center justify-center px-3 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-indigo-400 transition-all has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/30 has-[:checked]:border-indigo-500 has-[:checked]:shadow-inner">
                                    <input type="checkbox" name="available_sizes[]" value="{{ $size }}" class="sr-only peer">
                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-400 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100 dark:border-gray-700">

                <!-- Preços por Quantidade -->
                <section x-data="quantityPricing([], false)">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Desconto por Quantidade</h3>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="has_quantity_pricing" value="1" x-model="enabled" class="sr-only peer">
                            <div class="relative w-14 h-7 bg-gray-200 dark:bg-white/30 border border-gray-300 dark:border-white/50 rounded-full peer peer-checked:after:translate-x-7 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all duration-300 peer-checked:bg-indigo-600 shadow-inner"></div>
                        </label>
                    </div>

                    <div x-show="enabled" x-collapse x-cloak class="bg-indigo-50/50 dark:bg-indigo-900/10 rounded-2xl p-6 border border-indigo-100 dark:border-indigo-900/30">
                        <div class="space-y-4">
                            <template x-for="(tier, index) in tiers" :key="index">
                                <div class="flex items-center gap-4 bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <div class="flex-1">
                                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Mínimo</label>
                                        <input type="number" x-model.number="tier.min_quantity" min="1" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-gray-100">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Máximo</label>
                                        <input type="number" x-model.number="tier.max_quantity" min="1" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-gray-100">
                                    </div>
                                    <div class="flex-2">
                                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Preço (R$)</label>
                                        <input type="number" x-model.number="tier.price" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-gray-100 font-bold text-indigo-600 dark:text-indigo-400">
                                    </div>
                                    <button type="button" @click="removeTier(index)" class="mt-5 p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </template>
                            
                            <button type="button" @click="addTier()" class="w-full py-4 border-2 border-dashed border-indigo-200 dark:border-indigo-800 rounded-xl text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all font-semibold flex items-center justify-center gap-2">
                                <i class="fa-solid fa-plus-circle"></i>
                                Adicionar Faixa de Preço
                            </button>
                            
                            <input type="hidden" name="quantity_pricing" :value="JSON.stringify(tiers)">
                        </div>
                    </div>
                </section>
            </div>

            <!-- Rodapé de Ações -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-8 py-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fa-solid fa-circle-info mr-1 text-indigo-500"></i>
                    Após salvar, você poderá gerenciar os itens adicionais do produto.
                </p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.sub-local-products.index') }}" 
                       class="px-6 py-2.5 text-gray-700 dark:text-gray-300 font-semibold hover:text-indigo-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            style="color: #ffffff !important;"
                            class="px-10 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all font-bold text-lg active:scale-95">
                        Criar Produto
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
