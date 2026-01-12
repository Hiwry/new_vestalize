@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Cadastrar Estoque</h1>
    <a href="{{ route('stocks.index') }}" 
       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
        ← Voltar
    </a>
</div>

@if(session('error'))
<div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
</div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <form method="POST" action="{{ route('stocks.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Loja -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Loja <span class="text-red-500">*</span>
                </label>
                <select name="store_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Selecione a loja...</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ (old('store_id') ?? request('store_id')) == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
                @error('store_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo de Corte -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de Corte <span class="text-red-500">*</span>
                </label>
                <select name="cut_type_id" id="cut_type_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Selecione o tipo de corte...</option>
                    @foreach($cutTypes as $cutType)
                        <option value="{{ $cutType->id }}" {{ (old('cut_type_id') ?? request('cut_type_id')) == $cutType->id ? 'selected' : '' }}>
                            {{ $cutType->name }}
                        </option>
                    @endforeach
                </select>
                @error('cut_type_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tecido -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tecido <span class="text-red-500">*</span>
                </label>
                <select name="fabric_id" id="fabric_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Selecione o tecido...</option>
                    @foreach($fabrics as $fabric)
                        <option value="{{ $fabric->id }}" {{ (old('fabric_id') ?? request('fabric_id')) == $fabric->id ? 'selected' : '' }}>
                            {{ $fabric->name }}
                        </option>
                    @endforeach
                </select>
                @error('fabric_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo de Tecido (Específico) -->
            <div id="fabric_type_container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de Tecido (Específico)
                </label>
                <select name="fabric_type_id" id="fabric_type_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Selecione...</option>
                </select>
                @error('fabric_type_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opcional: Selecione caso queira especificar o tipo</p>
            </div>

            <!-- Cor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cor <span class="text-red-500">*</span>
                </label>
                <select name="color_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Selecione a cor...</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ (old('color_id') ?? request('color_id')) == $color->id ? 'selected' : '' }}>
                            {{ $color->name }}
                        </option>
                    @endforeach
                </select>
                @error('color_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tamanhos e Quantidades -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Quantidades por Tamanho <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-3 md:grid-cols-5 gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    @foreach($sizes as $size)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 text-center">
                            {{ $size }}
                        </label>
                        <input type="number" 
                               name="sizes[{{ $size }}]" 
                               value="{{ old("sizes.{$size}", 0) }}"
                               min="0"
                               step="1"
                               placeholder="0"
                               class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Informe a quantidade para cada tamanho. Deixe em 0 para tamanhos sem estoque.
                </p>
                @error('sizes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prateleira/Estante (comum para todos os tamanhos) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Prateleira/Estante
                </label>
                <input type="text" 
                       name="shelf" 
                       value="{{ old('shelf') }}"
                       placeholder="Ex: A1, B5, C3"
                       maxlength="50"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Informe a prateleira/estante onde todos os tamanhos estão armazenados (ex: A1, B5).
                </p>
                @error('shelf')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Configurações de Estoque -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Estoque Mínimo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Estoque Mínimo
                </label>
                <input type="number" 
                       name="min_stock" 
                       value="{{ old('min_stock', 0) }}"
                       min="0"
                       step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Aplicado a todos os tamanhos</p>
                @error('min_stock')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estoque Máximo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Estoque Máximo
                </label>
                <input type="number" 
                       name="max_stock" 
                       value="{{ old('max_stock') }}"
                       min="0"
                       step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Aplicado a todos os tamanhos</p>
                @error('max_stock')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Observações -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Observações
            </label>
            <textarea name="notes" 
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('stocks.index') }}" 
               class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Cadastrar Estoque
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cutTypeSelect = document.getElementById('cut_type_id');
    const fabricSelect = document.getElementById('fabric_id');
    const fabricTypeSelect = document.getElementById('fabric_type_id');
    const fabricTypeContainer = document.getElementById('fabric_type_container');
    
    // Salvar opções originais de tecido
    const originalFabricOptions = Array.from(fabricSelect.options).map(opt => ({
        value: opt.value,
        text: opt.text,
        selected: opt.selected
    }));
    
    // Verificar se há um tipo de corte selecionado via old() ou URL
    const urlParams = new URLSearchParams(window.location.search);
    const cutTypeFromUrl = urlParams.get('cut_type_id');
    
    if (cutTypeFromUrl) {
        cutTypeSelect.value = cutTypeFromUrl;
        
        // Se já tiver tecido na URL, não precisa buscar via API
        const fabricFromUrl = urlParams.get('fabric_id');
        if (!fabricFromUrl) {
            updateFabricByCutType(cutTypeFromUrl);
        }
    } else if (cutTypeSelect.value) {
        // Se não tiver na URL mas tiver selecionado (ex: via old()), verifica se precisa buscar
        // Só busca se não tiver tecido selecionado
        if (!fabricSelect.value) {
            updateFabricByCutType(cutTypeSelect.value);
        }
    }
    
    // Listener para mudanças no tipo de corte
    cutTypeSelect.addEventListener('change', function() {
        const cutTypeId = this.value;
        if (cutTypeId) {
            updateFabricByCutType(cutTypeId);
        } else {
            // Se não houver tipo de corte selecionado, restaurar opções originais
            restoreFabricOptions();
        }
    });
    
    // Listener para mudanças no tecido
    fabricSelect.addEventListener('change', function() {
        const fabricId = this.value;
        updateFabricTypes(fabricId);
    });

    // Se já tiver um tecido selecionado ao carregar (ex: old() ou default), buscar tipos
    if (fabricSelect.value) {
        updateFabricTypes(fabricSelect.value, '{{ old('fabric_type_id') ?? request('fabric_type_id') }}');
    }
    
    function updateFabricTypes(fabricId, selectedTypeId = null) {
        if (!fabricId) {
            fabricTypeContainer.classList.add('hidden');
            fabricTypeSelect.innerHTML = '<option value="">Selecione...</option>';
            return;
        }

        // Mostrar loading
        fabricTypeContainer.classList.remove('hidden');
        fabricTypeSelect.disabled = true;
        fabricTypeSelect.innerHTML = '<option value="">Carregando...</option>';

        fetch(`/api/stocks/fabric-types?fabric_id=${fabricId}`)
            .then(response => response.json())
            .then(data => {
                fabricTypeSelect.innerHTML = '<option value="">Selecione...</option>';
                
                if (data.success && data.fabric_types && data.fabric_types.length > 0) {
                    data.fabric_types.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;
                        if (selectedTypeId && selectedTypeId == type.id) {
                            option.selected = true;
                        }
                        fabricTypeSelect.appendChild(option);
                    });
                    fabricTypeContainer.classList.remove('hidden');
                } else {
                    // Se não tiver sub-tipos, esconder container
                    fabricTypeContainer.classList.add('hidden');
                }
                
                fabricTypeSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erro ao buscar tipos de tecido:', error);
                fabricTypeSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                fabricTypeSelect.disabled = false;
            });
    }
    
    function restoreFabricOptions() {
        fabricSelect.innerHTML = '';
        originalFabricOptions.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            option.selected = opt.selected;
            fabricSelect.appendChild(option);
        });
    }
    
    function updateFabricByCutType(cutTypeId) {
        if (!cutTypeId) {
            restoreFabricOptions();
            return;
        }
        
        // Mostrar loading
        const originalFabricValue = fabricSelect.value;
        fabricSelect.disabled = true;
        fabricSelect.innerHTML = '<option value="">Carregando...</option>';
        
        fetch(`/api/stocks/fabric-by-cut-type?cut_type_id=${cutTypeId}`)
            .then(response => response.json())
            .then(data => {
                // Restaurar opções originais
                restoreFabricOptions();
                
                if (data.success && data.fabric_id) {
                    // Selecionar o tecido encontrado
                    fabricSelect.value = data.fabric_id;
                    
                    // Buscar tipos de tecido para este novo tecido
                    updateFabricTypes(data.fabric_id, data.fabric_type_id);
                    
                    // Se o tecido foi encontrado automaticamente, mostrar mensagem
                    if (data.fabric_name) {
                        console.log(`Tecido "${data.fabric_name}" selecionado automaticamente.`);
                    }
                }
                
                fabricSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erro ao buscar tecido:', error);
                // Restaurar opções em caso de erro
                restoreFabricOptions();
                if (originalFabricValue) {
                    fabricSelect.value = originalFabricValue;
                }
                fabricSelect.disabled = false;
            });
    }
});
</script>
@endsection

