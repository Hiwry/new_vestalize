@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('admin.personalization-prices.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
            Preços de Personalização
        </a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-gray-900 dark:text-gray-100">{{ $setting->display_name }}</span>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Configurações - {{ $setting->display_name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Configure preços, descontos e opções especiais</p>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="px-6 py-3 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800">
            <p class="text-sm text-green-700 dark:text-green-300 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </p>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.personalization-settings.update', $setting->personalization_type) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Info Básico -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome de Exibição</label>
                        <input type="text" name="display_name" value="{{ old('display_name', $setting->display_name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input type="checkbox" name="active" value="1" {{ $setting->active ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativo</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
                    <textarea name="description" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">{{ old('description', $setting->description) }}</textarea>
                </div>

                <!-- Cobrança por Cor -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Cobrança por Cor
                    </h3>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="charge_by_color" value="1" {{ $setting->charge_by_color ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500"
                                   onchange="toggleColorPricing(this)">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cobrar por quantidade de cores</span>
                        </label>
                        
                        <div id="color-pricing-fields" class="{{ $setting->charge_by_color ? '' : 'hidden' }}">
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Preço por cor adicional</label>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-1">R$</span>
                                        <input type="number" step="0.01" name="color_price_per_unit" value="{{ old('color_price_per_unit', $setting->color_price_per_unit) }}" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-red-500 dark:text-red-400 mb-1">Custo por cor adicional</label>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-1">R$</span>
                                        <input type="number" step="0.01" name="color_cost_per_unit" value="{{ old('color_cost_per_unit', $setting->color_cost_per_unit) }}" 
                                               class="w-full px-3 py-2 border border-red-200 dark:border-red-900/30 rounded-lg bg-red-50 dark:bg-red-900/10 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 placeholder-gray-400">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cores mínimas</label>
                                    <input type="number" name="min_colors" value="{{ old('min_colors', $setting->min_colors) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cores máximas</label>
                                    <input type="number" name="max_colors" value="{{ old('max_colors', $setting->max_colors) }}" placeholder="∞"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Descontos por Múltiplas Aplicações -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Descontos por Múltiplas Aplicações
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        Aplique descontos automáticos quando o cliente adicionar múltiplas aplicações do mesmo tipo.
                    </p>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">2ª Aplicação</label>
                            <div class="flex items-center">
                                <input type="number" step="0.01" name="discount_2nd_application" 
                                       value="{{ old('discount_2nd_application', $setting->discount_2nd_application) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">3ª Aplicação</label>
                            <div class="flex items-center">
                                <input type="number" step="0.01" name="discount_3rd_application" 
                                       value="{{ old('discount_3rd_application', $setting->discount_3rd_application) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">4ª+ Aplicações</label>
                            <div class="flex items-center">
                                <input type="number" step="0.01" name="discount_4th_plus_application" 
                                       value="{{ old('discount_4th_plus_application', $setting->discount_4th_plus_application) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações Gerais -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Configurações Gerais
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="has_sizes" value="1" {{ $setting->has_sizes ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Usa tamanhos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_locations" value="1" {{ $setting->has_locations ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Usa locais</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_special_options" value="1" {{ $setting->has_special_options ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Opções especiais</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                <a href="{{ route('admin.personalization-prices.index') }}" 
                   class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                    ← Voltar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white stay-white text-sm font-medium rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>

    <!-- Opções Especiais -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Opções Especiais (Adicionais)
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Opções que adicionam valor ao preço base (ex: Dourado, Neon, Holográfico)</p>
            </div>
        </div>

        <div class="p-6">
            <!-- Lista de Opções Especiais -->
            <div class="space-y-3 mb-6">
                @forelse($specialOptions as $option)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $option->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $option->charge_type === 'percentage' ? 'Percentual' : 'Valor Fixo' }}: 
                                <span class="text-green-600 dark:text-green-400 font-semibold">{{ $option->formatted_value }}</span>
                                <span class="text-gray-300 dark:text-gray-600 mx-2">|</span>
                                Custo: <span class="text-red-500 dark:text-red-400">R$ {{ number_format($option->cost, 2, ',', '.') }}</span>
                                @if($option->description)
                                    <span class="ml-2">• {{ $option->description }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('admin.personalization-settings.special-options.toggle', [$setting->personalization_type, $option->id]) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg border {{ $option->active ? 'border-green-300 text-green-700 bg-green-50 dark:border-green-700 dark:text-green-200 dark:bg-green-900/30' : 'border-gray-300 text-gray-600 bg-white dark:border-gray-600 dark:text-gray-200 dark:bg-gray-700' }}">
                                {{ $option->active ? 'Ativa' : 'Inativa' }}
                            </button>
                        </form>
                        <form id="delete-form-{{ $option->id }}" action="{{ route('admin.personalization-settings.special-options.destroy', [$setting->personalization_type, $option->id]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="openDeleteModal('{{ $option->id }}', '{{ $option->name }}')" class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <p>Nenhuma opção especial cadastrada</p>
                </div>
                @endforelse
            </div>

            <!-- Formulário Nova Opção -->
            <form action="{{ route('admin.personalization-settings.special-options.store', $setting->personalization_type) }}" method="POST" 
                  class="border-t border-gray-200 dark:border-gray-700 pt-4">
                @csrf
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Adicionar Nova Opção</h4>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <input type="text" name="name" placeholder="Nome (ex: Dourado)" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <select name="charge_type" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="percentage">Percentual (%)</option>
                            <option value="fixed">Valor Fixo (R$)</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" step="0.01" name="charge_value" placeholder="Preço" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="cost" placeholder="Custo" required
                               class="w-full px-3 py-2 border border-red-200 dark:border-red-900/30 rounded-lg bg-red-50 dark:bg-red-900/10 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 placeholder-gray-400">
                    </div>
                    <div>
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white stay-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <input type="text" name="description" placeholder="Descrição (opcional)"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-slate-700 transform transition-all scale-100 opacity-100">
        <div class="p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                <svg class="h-6 w-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Remover Opção Especial?</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Você tem certeza que deseja remover a opção <span id="delete-item-name" class="font-semibold text-gray-900 dark:text-gray-200"></span>? Esta ação não pode ser desfeita.</p>
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white stay-white rounded-lg font-medium shadow-lg shadow-red-500/30 transition-all transform hover:scale-105">
                    Sim, Remover
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleColorPricing(checkbox) {
    const fields = document.getElementById('color-pricing-fields');
    if (checkbox.checked) {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}

let itemToDeleteId = null;

function openDeleteModal(id, name) {
    itemToDeleteId = id;
    document.getElementById('delete-item-name').textContent = name || 'Selecionada';
    document.getElementById('delete-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    itemToDeleteId = null;
}

function confirmDelete() {
    if (itemToDeleteId) {
        document.getElementById('delete-form-' + itemToDeleteId).submit();
    }
}
</script>
@endpush
@endsection
