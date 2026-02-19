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
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-300 font-bold shadow-lg shadow-indigo-200 dark:shadow-none mb-20" style="color: #ffffff !important;">
                Cadastrar Estoque
            </button>
        </div>
    </form>
</div>

<script>
// Função principal de inicialização
function initStockCreatePage() {
    // Escopo inicial: tentar pegar o container principal deste conteúdo para evitar conflitos com elementos fantasmas
    // O container principal é a div com classe bg-white que envolve o form
    // Mas para garantir, vamos buscar os elementos diretos, e revalidá-los nos eventos.
    
    // Vamos usar seletores mais específicos se possível, mas IDs deveriam ser únicos.
    // Se ajax-navigation estiver duplicando IDs, vamos tentar pegar o último (geralmente o novo) ou o visível.
    
    function getVisibleElement(id) {
        const elements = document.querySelectorAll('#' + id);
        if (elements.length > 1) {
            console.warn(`Encontrados ${elements.length} elementos com ID ${id}. Usando o último/visível.`);
            // Tenta encontrar o visível
            for (let el of elements) {
                if (el.offsetParent !== null) return el;
            }
            // Se nenhum visível (pode estar em carregamento), retorna o último
            return elements[elements.length - 1];
        }
        return document.getElementById(id);
    }

    const cutTypeSelect = getVisibleElement('cut_type_id');
    const fabricSelect = getVisibleElement('fabric_id');
    const fabricTypeSelect = getVisibleElement('fabric_type_id');
    const fabricTypeContainer = getVisibleElement('fabric_type_container');
    
    // Debug
    console.log('Script cadastrar estoque carregado v4 (AJAX compatible)');
    if (cutTypeSelect) console.log('CutType select encontrado:', cutTypeSelect.offsetParent !== null ? 'Visível' : 'Oculto');
    if (fabricSelect) console.log('Fabric select encontrado:', fabricSelect.offsetParent !== null ? 'Visível' : 'Oculto');
    
    if (!cutTypeSelect || !fabricSelect) {
        console.warn('Elementos necessários não encontrados, abortando inicialização');
        return;
    }

    // Verificar se há um tipo de corte selecionado via old() ou URL
    const urlParams = new URLSearchParams(window.location.search);
    const cutTypeFromUrl = urlParams.get('cut_type_id');
    
    // Inicialização
    if (cutTypeFromUrl) {
        cutTypeSelect.value = cutTypeFromUrl;
        const fabricFromUrl = urlParams.get('fabric_id');
        if (!fabricFromUrl) {
            updateFabricByCutType(cutTypeFromUrl, fabricSelect);
        }
    } else if (cutTypeSelect.value) {
        // Autocomplete ou Old Input
        if (!fabricSelect.value || fabricSelect.value == "") {
            updateFabricByCutType(cutTypeSelect.value, fabricSelect);
        }
    }
    
    // Event Listeners - usar named functions para poder remover
    function handleCutTypeChange(e) {
        // Garantir que estamos mexendo nos elementos relacionados ao input que disparou o evento
        // Isso resolve problemas se houver múltiplos forms ou fantasmas
        const currentCutTypeSelect = e.target;
        const form = currentCutTypeSelect.closest('form');
        const currentFabricSelect = form ? form.querySelector('[name="fabric_id"]') : fabricSelect;
        
        const cutTypeId = currentCutTypeSelect.value;
        console.log('Cut type changed to:', cutTypeId);
        
        if (cutTypeId) {
            updateFabricByCutType(cutTypeId, currentFabricSelect);
        }
    }

    function handleFabricChange(e) {
        const currentFabricSelect = e.target;
        const form = currentFabricSelect.closest('form');
        const currentFabricTypeSelect = form ? form.querySelector('[name="fabric_type_id"]') : fabricTypeSelect;
        const currentFabricTypeContainer = document.getElementById('fabric_type_container');
        
        const fabricId = currentFabricSelect.value;
        console.log('Fabric changed to:', fabricId, '- calling updateFabricTypes without selectedTypeId');
        updateFabricTypes(fabricId, currentFabricTypeSelect, currentFabricTypeContainer);
    }

    // Remover listeners antigos se existirem (para evitar duplicação em AJAX navigation)
    cutTypeSelect.removeEventListener('change', handleCutTypeChange);
    fabricSelect.removeEventListener('change', handleFabricChange);
    
    // Adicionar listeners
    cutTypeSelect.addEventListener('change', handleCutTypeChange);
    fabricSelect.addEventListener('change', handleFabricChange);

    // Se já tiver um tecido selecionado ao carregar
    if (fabricSelect.value) {
        updateFabricTypes(fabricSelect.value, fabricTypeSelect, fabricTypeContainer, '{{ old('fabric_type_id') ?? request('fabric_type_id') }}');
    }

    // Função principal para buscar tecido
    function updateFabricByCutType(cutTypeId, targetFabricSelect) {
        if (!cutTypeId || !targetFabricSelect) return;
        
        console.log('Buscando tecido para corte:', cutTypeId, 'Target element:', targetFabricSelect);

        // Apenas desabilitar, NÃO limpar as opções
        targetFabricSelect.disabled = true;
        
        fetch(`/api/stocks/fabric-by-cut-type?cut_type_id=${cutTypeId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Dados recebidos:', data);
                
                if (data.success && data.fabric_id) {
                    // Tentar selecionar o tecido
                    targetFabricSelect.value = data.fabric_id;
                    
                    // Forçar atualização visual do select
                    // Isso garante que o navegador atualize o elemento visualmente
                    targetFabricSelect.blur();
                    targetFabricSelect.focus();
                    targetFabricSelect.blur();
                    
                    // Verificar se o valor mudou
                    if (targetFabricSelect.value == data.fabric_id) {
                        console.log('Tecido selecionado com sucesso:', data.fabric_name);
                        
                        // Buscar o container de tipos relativo a este select
                        const form = targetFabricSelect.closest('form');
                        const targetTypeSelect = form ? form.querySelector('[name="fabric_type_id"]') : fabricTypeSelect;
                        const targetTypeContainer = document.getElementById('fabric_type_container');
                        
                        // Buscar tipos de tecido para este novo tecido com o selectedTypeId
                        updateFabricTypes(data.fabric_id, targetTypeSelect, targetTypeContainer, data.fabric_type_id);
                        
                        // NÃO disparar evento change manualmente para evitar loops e race conditions
                    } else {
                        console.warn('Tecido retornado pela API não disponível na lista:', data.fabric_id);
                    }
                }
                
                targetFabricSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erro ao buscar tecido:', error);
                targetFabricSelect.disabled = false;
            });
    }
    
    function updateFabricTypes(fabricId, targetSelect, targetContainer, selectedTypeId = null) {
        if (!targetSelect || !targetContainer) return;

        console.log('updateFabricTypes chamado:', { fabricId, selectedTypeId, targetSelect });

        if (!fabricId) {
            targetContainer.classList.add('hidden');
            targetSelect.innerHTML = '<option value="">Selecione...</option>';
            return;
        }

        // Mostrar loading
        targetContainer.classList.remove('hidden');
        targetSelect.disabled = true;
        targetSelect.innerHTML = '<option value="">Carregando...</option>';

        fetch(`/api/stocks/fabric-types?fabric_id=${fabricId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Tipos de tecido recebidos:', data);
                
                targetSelect.innerHTML = '<option value="">Selecione...</option>';
                
                if (data.success && data.fabric_types && data.fabric_types.length > 0) {
                    data.fabric_types.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;
                        if (selectedTypeId && selectedTypeId == type.id) {
                            option.selected = true;
                            console.log('Tipo de tecido marcado como selecionado:', type.name, 'ID:', type.id);
                        }
                        targetSelect.appendChild(option);
                    });
                    targetContainer.classList.remove('hidden');
                    
                    // Se temos um selectedTypeId, forçar a seleção após popular
                    if (selectedTypeId) {
                        targetSelect.value = selectedTypeId;
                        
                        // Forçar refresh visual
                        targetSelect.blur();
                        targetSelect.focus();
                        targetSelect.blur();
                        
                        // Verificar se foi selecionado
                        if (targetSelect.value == selectedTypeId) {
                            console.log(' Tipo de tecido selecionado com sucesso:', targetSelect.options[targetSelect.selectedIndex]?.text);
                        } else {
                            console.warn(' Falha ao selecionar tipo de tecido:', selectedTypeId);
                        }
                    }
                } else {
                    console.log('Nenhum tipo de tecido encontrado ou tecido sem tipos específicos');
                    targetContainer.classList.add('hidden');
                }
                
                targetSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erro ao buscar tipos de tecido:', error);
                targetSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                targetSelect.disabled = false;
            });
    }
}

// Inicializar quando o DOM estiver pronto (page load normal)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStockCreatePage);
} else {
    initStockCreatePage();
}

// Reinicializar quando o conteúdo for carregado via AJAX
document.addEventListener('content-loaded', function() {
    console.log('AJAX content-loaded event detected, reinitializing stock create page');
    initStockCreatePage();
});

document.addEventListener('ajax-content-loaded', function() {
    console.log('AJAX ajax-content-loaded event detected, reinitializing stock create page');
    initStockCreatePage();
});
</script>
@endsection

