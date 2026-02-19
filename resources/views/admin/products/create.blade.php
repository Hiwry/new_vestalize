@extends('layouts.admin')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Novo Produto</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-medium">Preencha as informações para adicionar um novo item ao seu catálogo.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i> Voltar para Lista
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left column (Main Info) --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Section: Basic Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/40 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-circle-info text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informações Básicas</h2>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Título do Produto *</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Ex: Camiseta Oversized"
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all dark:text-white">
                                @error('title') <p class="mt-2 text-xs text-red-500 font-bold tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Descrição detalhada</label>
                                <textarea id="description" name="description" rows="5" placeholder="Descreva os diferenciais, medidas e detalhes do produto..."
                                          class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all dark:text-white resize-none">{{ old('description') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="category_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoria</label>
                                        <button type="button" onclick="openQuickModal('category')" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 uppercase"><i class="fa-solid fa-plus mr-1"></i> Nova</button>
                                    </div>
                                    <select id="category_id" name="category_id"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all dark:text-white appearance-none cursor-pointer">
                                        <option value="">Selecione uma categoria</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="subcategory_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subcategoria</label>
                                        <button type="button" onclick="openQuickModal('subcategory')" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 uppercase"><i class="fa-solid fa-plus mr-1"></i> Nova</button>
                                    </div>
                                    <select id="subcategory_id" name="subcategory_id"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all dark:text-white appearance-none cursor-pointer">
                                        <option value="">Selecione uma subcategoria</option>
                                        @foreach($subcategories as $subcategory)
                                            <option value="{{ $subcategory->id }}" data-category="{{ $subcategory->category_id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section: Attributes/Config --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/40 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-sliders text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Configurações do Catálogo</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="tecido_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tecido</label>
                                    <a href="{{ route('admin.tecidos.create') }}" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline"><i class="fa-solid fa-plus"></i></a>
                                </div>
                                <select id="tecido_id" name="tecido_id" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20 dark:text-white appearance-none">
                                    <option value="">Nenhum</option>
                                    @foreach($tecidos as $tecido)
                                        <option value="{{ $tecido->id }}" {{ old('tecido_id') == $tecido->id ? 'selected' : '' }}>{{ $tecido->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="personalizacao_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Personalização</label>
                                    <a href="{{ route('admin.personalizacoes.create') }}" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline"><i class="fa-solid fa-plus"></i></a>
                                </div>
                                <select id="personalizacao_id" name="personalizacao_id" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20 dark:text-white appearance-none">
                                    <option value="">Nenhuma</option>
                                    @foreach($personalizacoes as $personalizacao)
                                        <option value="{{ $personalizacao->id }}" {{ old('personalizacao_id') == $personalizacao->id ? 'selected' : '' }}>{{ $personalizacao->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="modelo_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modelo</label>
                                    <a href="{{ route('admin.modelos.create') }}" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline"><i class="fa-solid fa-plus"></i></a>
                                </div>
                                <select id="modelo_id" name="modelo_id" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20 dark:text-white appearance-none">
                                    <option value="">Nenhum</option>
                                    @foreach($modelos as $modelo)
                                        <option value="{{ $modelo->id }}" {{ old('modelo_id') == $modelo->id ? 'selected' : '' }}>{{ $modelo->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="cut_type_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo de Corte (Estoque)</label>
                                </div>
                                <select id="cut_type_id" name="cut_type_id" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20 dark:text-white appearance-none">
                                    <option value="">Nenhum</option>
                                    @foreach($cutTypes ?? [] as $cutType)
                                        <option value="{{ $cutType->id }}" {{ old('cut_type_id') == $cutType->id ? 'selected' : '' }}>{{ $cutType->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-gray-400 mt-1">Vincula tamanhos e cores do estoque ao catálogo</p>
                            </div>
                        </div>

                        {{-- Customization Options --}}
                        <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-700/50">
                            <div class="flex items-center gap-4 mb-4">
                                <div x-data="{ toggled: {{ old('allow_application') ? 'true' : 'false' }} }">
                                    <label for="allow_application" class="inline-flex items-center cursor-pointer group">
                                        <div class="relative">
                                            <input type="checkbox" id="allow_application" name="allow_application" value="1" 
                                                   {{ old('allow_application') ? 'checked' : '' }}
                                                   onchange="toggleApplicationTypes()"
                                                   class="sr-only">
                                            <div class="block w-14 h-8 bg-gray-200 dark:bg-gray-700 rounded-full transition-colors peer-checked:bg-indigo-600"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform peer-checked:translate-x-6"></div>
                                        </div>
                                        <span class="ml-3 text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 transition-colors">Permitir Aplicação de Estampa (para camisetas/calças)</span>
                                    </label>
                                </div>
                            </div>

                            <div id="application-types-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 overflow-hidden transition-all duration-300" style="display: {{ old('allow_application') ? 'grid' : 'none' }};">
                                <label class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 cursor-pointer hover:border-indigo-500/50 transition-all">
                                    <input type="checkbox" name="application_types[]" value="sublimacao_local" id="app_sublimacao_local" 
                                           {{ in_array('sublimacao_local', old('application_types', [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-200 rounded focus:ring-indigo-500/20">
                                    <span class="ml-3 text-xs font-bold text-gray-600 dark:text-gray-400">Sublimação Local</span>
                                </label>
                                <label class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 cursor-pointer hover:border-indigo-500/50 transition-all">
                                    <input type="checkbox" name="application_types[]" value="dtf" id="app_dtf" 
                                           {{ in_array('dtf', old('application_types', [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-200 rounded focus:ring-indigo-500/20">
                                    <span class="ml-3 text-xs font-bold text-gray-600 dark:text-gray-400">DTF</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column (Pricing, Status, Media) --}}
            <div class="space-y-8">
                {{-- Pricing card --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/40 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-tag text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Preço e Venda</h2>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="sale_type" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Tipo de Venda *</label>
                            <select id="sale_type" name="sale_type" required onchange="updatePriceLabel()"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20 dark:text-white appearance-none cursor-pointer">
                                <option value="unidade" {{ old('sale_type', 'unidade') == 'unidade' ? 'selected' : '' }}>Unidade (Padrão)</option>
                                <option value="kg" {{ old('sale_type') == 'kg' ? 'selected' : '' }}>Por Quilograma (Kg)</option>
                                <option value="metro" {{ old('sale_type') == 'metro' ? 'selected' : '' }}>Por Metro (m)</option>
                            </select>
                        </div>

                        <div>
                            <label for="price" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                Preço Base <span id="price-label" class="text-indigo-500">(por unidade)</span>
                            </label>
                            <div class="flex items-center w-full px-4 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all">
                                <span class="text-gray-400 font-bold shrink-0">R$</span>
                                <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" placeholder="0,00"
                                       class="w-full pl-3 pr-0 py-3 bg-transparent border-none outline-none focus:ring-0 transition-all dark:text-white font-bold text-lg">
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 dark:text-gray-500 leading-tight italic" id="price-hint">Dica: Selecione o tipo de venda para ver exemplos.</p>
                        </div>

                        <div class="pt-4 border-t border-gray-50 dark:border-gray-700/50 flex flex-col gap-4">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }} 
                                       class="w-5 h-5 text-indigo-600 bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-lg focus:ring-indigo-500/20 transition-all">
                                <span class="ml-3 text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 transition-colors">Produto Disponível</span>
                            </label>

                            <div>
                                <label for="order" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Ordem de Exibição</label>
                                <input type="number" id="order" name="order" value="{{ old('order', 0) }}" min="0"
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 outline-none dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Images card --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/40 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-camera text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Galeria de Fotos</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="relative group">
                            <label for="images" class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed border-gray-100 dark:border-gray-700/50 rounded-3xl hover:border-indigo-500/50 dark:hover:border-indigo-500/50 transition-all bg-gray-50/50 dark:bg-gray-900/30 cursor-pointer">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-3 group-hover:scale-110 transition-transform"></i>
                                    <p class="mb-1 text-sm text-gray-700 dark:text-gray-300 font-bold">Clique para upload</p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400">JPG, PNG ou WEBP (Max 2MB)</p>
                                </div>
                                <input type="file" id="images" name="images[]" multiple accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div id="image-preview" class="grid grid-cols-4 gap-2"></div>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-normal italic">
                            <i class="fa-solid fa-quote-left mr-1 opacity-30 text-xs"></i> 
                            Dica: A primeira imagem selecionada será exibida como destaque no catálogo prinicipal.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Form Actions --}}
        <div class="mt-12 sticky bottom-8 flex justify-end gap-3 px-6 py-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 z-10 transition-all hover:bg-white dark:hover:bg-gray-800">
            <a href="{{ route('admin.products.index') }}" 
               class="px-8 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-sm rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                Descartar
            </a>
            <button type="submit" 
                    class="px-10 py-3 bg-indigo-600 text-white font-bold text-sm rounded-2xl shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:shadow-indigo-500/50 transition-all transform hover:-translate-y-0.5 active:scale-95">
                <i class="fa-solid fa-check mr-2"></i> Salvar Produto
            </button>
        </div>
    </form>
</div>

{{-- Category Modal --}}
<div id="modal-category" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeQuickModal('category')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
            <form id="form-quick-category" onsubmit="submitQuickForm(event, 'category')">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nova Categoria</h3>
                        <button type="button" onclick="closeQuickModal('category')" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Nome da Categoria</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 outline-none dark:text-white">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeQuickModal('category')" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Cancelar</button>
                        <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-all">Criar Categoria</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Subcategory Modal --}}
<div id="modal-subcategory" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeQuickModal('subcategory')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
            <form id="form-quick-subcategory" onsubmit="submitQuickForm(event, 'subcategory')">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nova Subcategoria</h3>
                        <button type="button" onclick="closeQuickModal('subcategory')" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Categoria Pai</label>
                            <select name="category_id" id="quick-sub-category-id" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-500/20 dark:text-white appearance-none">
                                <option value="">Selecione uma categoria</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Nome da Subcategoria</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 outline-none dark:text-white">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="closeQuickModal('subcategory')" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Cancelar</button>
                        <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-all">Criar Subcategoria</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
        container.style.display = allowApplication ? 'grid' : 'none';
        
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
    
    if (saleType === 'unidade') {
        priceLabel.textContent = '(por unidade)';
        priceHint.textContent = 'Use para itens avulsos como máquinas, agulhas ou aviamentos.';
    } else if (saleType === 'kg') {
        priceLabel.textContent = '(por quilograma)';
        priceHint.textContent = 'Use para tecidos e malhas vendidos por peso.';
    } else if (saleType === 'metro') {
        priceLabel.textContent = '(por metro)';
        priceHint.textContent = 'Use para rolos ou retalhos vendidos por comprimento.';
    }
}

// Preview simples de imagens
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-100';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
});

// Executar ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    updatePriceLabel();
});

// Checkbox logic style
document.querySelector('.dot').parentElement.querySelector('input').addEventListener('change', function() {
    this.nextElementSibling.nextElementSibling.classList.toggle('translate-x-6');
});
// Modais de Criação Rápida
function openQuickModal(type) {
    document.getElementById(`modal-${type}`).classList.remove('hidden');
    
    // Se for subcategoria, pré-selecionar a categoria atual se houver
    if (type === 'subcategory') {
        const currentCat = document.getElementById('category_id').value;
        if (currentCat) {
            document.getElementById('quick-sub-category-id').value = currentCat;
        }
    }
}

function closeQuickModal(type) {
    document.getElementById(`modal-${type}`).classList.add('hidden');
    document.getElementById(`form-quick-${type}`).reset();
}

async function submitQuickForm(event, type) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Criando...';
    
    try {
        const url = type === 'category' ? '{{ route("admin.categories.store") }}' : '{{ route("admin.subcategories.store") }}';
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Adicionar ao select correspondente
            const selectId = type === 'category' ? 'category_id' : 'subcategory_id';
            const select = document.getElementById(selectId);
            const item = type === 'category' ? data.category : data.subcategory;
            
            const option = new Option(item.name, item.id);
            if (type === 'subcategory') {
                option.setAttribute('data-category', item.category_id);
            }
            
            select.add(option);
            select.value = item.id;
            
            // Se criou categoria, também atualizar o select dentro do modal de subcategoria
            if (type === 'category') {
                const subCatSelect = document.getElementById('quick-sub-category-id');
                subCatSelect.add(new Option(item.name, item.id));
            }
            
            // Disparar evento de change para atualizar lógicas dependentes
            select.dispatchEvent(new Event('change'));
            
            closeQuickModal(type);
            
            // Feedback visual opcional
            alert(data.message);
        } else {
            alert('Erro ao criar: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error(error);
        alert('Erro ao processar a requisição.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}
</script>

<style>
/* Estilo customizado para o toggle caseiro se necessário */
.peer-checked\:translate-x-6:checked ~ .dot {
    transform: translateX(1.5rem);
}
.peer-checked\:bg-indigo-600:checked + div {
    background-color: #4f46e5;
}
</style>
@endpush
@endsection

