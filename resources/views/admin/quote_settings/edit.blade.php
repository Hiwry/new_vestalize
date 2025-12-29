@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Configurar Orçamento Online (Self-Service)</h1>
        @if($settings->is_active)
        <a href="{{ route('quote.public', $settings->slug) }}" target="_blank" class="flex items-center text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
            <span class="mr-2">Ver página pública</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        </a>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.quote-settings.update') }}" x-data="quoteSettings">
        @csrf
        <input type="hidden" name="id" value="{{ $settings->id }}">

        <!-- Configurações Gerais -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Informações Básicas</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Slug / Link -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link Personalizado</label>
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                            {{ config('app.url') }}/solicitar-orcamento/
                        </span>
                        <input type="text" name="slug" value="{{ old('slug', $settings->slug) }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Este é o link que você enviará para seus clientes.</p>
                </div>

                <!-- Título -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título da Página</label>
                    <input type="text" name="title" value="{{ old('title', $settings->title) }}" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp para Recebimento</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="5582999999999" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Número completo com DDI e DDD (apenas números)</p>
                </div>

                <!-- Cor Principal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor Principal</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}" class="h-9 w-16 rounded border border-gray-300 cursor-pointer">
                        <input type="text" name="primary_color_text" x-model="color" @input="$refs.colorPicker.value = $event.target.value" class="block w-24 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase">
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center mt-6">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $settings->is_active) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                        Ativar Página de Orçamento
                    </label>
                </div>
            </div>

            <!-- Descrição -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição / Instruções (Opcional)</label>
                <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $settings->description) }}</textarea>
            </div>
        </div>

        <!-- Produtos Disponíveis (Repeater) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Produtos Disponíveis</h2>
                <button type="button" @click="addProduct()" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded text-sm font-medium hover:bg-indigo-100 transition">
                    + Adicionar Produto
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(product, index) in products" :key="index">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600 relative group">
                        <button type="button" @click="removeProduct(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- Ícone -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Ícone</label>
                                <select :name="'products['+index+'][icon]'" x-model="product.icon" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-sm">
                                    <option value="t-shirt">Camiseta</option>
                                    <option value="polo">Polo</option>
                                    <option value="hoodie">Moletom</option>
                                    <option value="hat">Boné</option>
                                    <option value="pants">Calça</option>
                                    <option value="apron">Avental</option>
                                    <option value="bag">Ecobag</option>
                                    <option value="other">Outro</option>
                                </select>
                                <div class="mt-2 flex justify-center text-gray-600 dark:text-gray-300">
                                    <!-- Preview simples do ícone -->
                                    <div x-show="product.icon === 't-shirt'"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div> 
                                    <!-- (Simplificado: usando icones genericos para demo, ideal seria componente de icone) -->
                                    <span x-text="getIconLabel(product.icon)" class="text-xs"></span>
                                </div>
                            </div>
                            
                            <!-- Detalhes -->
                            <div class="md:col-span-10 grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nome do Produto</label>
                                    <input type="text" :name="'products['+index+'][name]'" x-model="product.name" placeholder="Ex: Camiseta Básica Algodão" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Descrição Curta</label>
                                    <input type="text" :name="'products['+index+'][description]'" x-model="product.description" placeholder="Ex: Malha 30.1 penteada, costura reforçada" class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="products.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 rounded-lg dashed-border">
                    Nenhum produto cadastrado. Adicione produtos para que seus clientes possam escolher.
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transition transform hover:scale-105">
                Salvar Configurações
            </button>
        </div>
    </form>
</div>

@endsection

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quoteSettings', () => ({
            color: '{{ old('primary_color', $settings->primary_color) }}',
            products: {!! json_encode(old('products', $settings->products_json ?? [])) !!},
            
            addProduct() {
                this.products.push({
                    icon: 't-shirt',
                    name: '',
                    description: ''
                });
            },
            
            removeProduct(index) {
                this.products.splice(index, 1);
            },
            
            getIconLabel(icon) {
                const map = {
                    't-shirt': 'Camiseta', 'polo': 'Polo', 'hoodie': 'Moletom',
                    'hat': 'Boné', 'pants': 'Calça', 'apron': 'Avental',
                    'bag': 'Ecobag', 'other': 'Outro'
                };
                return map[icon] || icon;
            }
        }));
    });
</script>
