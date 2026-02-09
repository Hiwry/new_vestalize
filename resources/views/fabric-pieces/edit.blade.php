@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('fabric-pieces.index') }}" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Editar Peça #{{ $piece->id }}</h1>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $piece->status_color }}">{{ $piece->status_label }}</span>
            </div>
        </div>

        {{-- Formulário --}}
        <form action="{{ route('fabric-pieces.update', $piece->id) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                {{-- Loja e Produto --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja *</label>
                        <select name="store_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store_id', $piece->store_id) == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Tecido</label>
                        <select name="fabric_type_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            <option value="">Selecione...</option>
                            @foreach($fabricTypes as $fabricType)
                                <option value="{{ $fabricType->id }}" {{ old('fabric_type_id', $piece->fabric_type_id) == $fabricType->id ? 'selected' : '' }}>{{ $fabricType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor</label>
                        <select name="color_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            <option value="">Selecione...</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}" {{ old('color_id', $piece->color_id) == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Fornecedor e NF --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fornecedor</label>
                        <input type="text" name="supplier" value="{{ old('supplier', $piece->supplier) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número da Nota Fiscal</label>
                        <input type="text" name="invoice_number" value="{{ old('invoice_number', $piece->invoice_number) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chave de Acesso NF-e</label>
                    <input type="text" name="invoice_key" value="{{ old('invoice_key', $piece->invoice_key) }}" maxlength="44"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg font-mono text-sm">
                </div>

                {{-- Características Físicas --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peso Original (kg)</label>
                        <input type="number" step="0.001" name="weight" value="{{ old('weight', $piece->weight) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peso Atual (kg)</label>
                        <input type="number" step="0.001" name="weight_current" value="{{ old('weight_current', $piece->weight_current) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metragem (m)</label>
                        <input type="number" step="0.01" name="meters" value="{{ old('meters', $piece->meters) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prateleira</label>
                        <input type="text" name="shelf" value="{{ old('shelf', $piece->shelf) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de Barras</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $piece->barcode) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                </div>

                {{-- Origem e Destino --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="between_stores" name="between_stores" value="1" 
                               {{ old('between_stores', $piece->between_stores) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                               onchange="toggleStoreSelectors()">
                        <label for="between_stores" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Transferência entre lojas
                        </label>
                    </div>

                    {{-- Campos de texto para origem/destino normal --}}
                    <div id="text_origin_destination" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Origem</label>
                            <input type="text" name="origin" value="{{ old('origin', $piece->origin) }}" placeholder="Cidade/Estado"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destino</label>
                            <input type="text" name="destination" value="{{ old('destination', $piece->destination) }}" placeholder="Cidade/Estado"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                        </div>
                    </div>

                    {{-- Seletores de loja para transferência entre lojas --}}
                    <div id="store_origin_destination" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja de Origem</label>
                            <select name="origin_store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                                <option value="">Selecione...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('origin_store_id', $piece->origin_store_id) == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja de Destino</label>
                            <select name="destination_store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                                <option value="">Selecione...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('destination_store_id', $piece->destination_store_id) == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Preços --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço por Kg (Venda)</label>
                        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $piece->sale_price) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Recebimento</label>
                        <input type="date" name="received_at" value="{{ old('received_at', $piece->received_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                </div>

                {{-- Observações --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">{{ old('notes', $piece->notes) }}</textarea>
                </div>

                {{-- Info de status --}}
                @if($piece->opened_at || $piece->sold_at)
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Histórico</h4>
                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        @if($piece->opened_at)
                            <p> Aberta em: {{ $piece->opened_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($piece->sold_at)
                            <p> Vendida em: {{ $piece->sold_at->format('d/m/Y H:i') }}</p>
                            @if($piece->soldBy)
                                <p> Vendedor: {{ $piece->soldBy->name }}</p>
                            @endif
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('fabric-pieces.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleStoreSelectors() {
        const checkbox = document.getElementById('between_stores');
        const textFields = document.getElementById('text_origin_destination');
        const storeFields = document.getElementById('store_origin_destination');
        
        if (checkbox.checked) {
            textFields.classList.add('hidden');
            storeFields.classList.remove('hidden');
        } else {
            textFields.classList.remove('hidden');
            storeFields.classList.add('hidden');
        }
    }

    // Inicializar estado ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        toggleStoreSelectors();
    });
</script>
@endpush
