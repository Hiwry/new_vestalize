@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('production-supplies.index') }}" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Editar Material</h1>
            </div>
            
            <form action="{{ route('production-supplies.destroy', $productionSupply) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Tem certeza que deseja excluir este material permanentemente?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 rounded-lg flex items-center gap-2 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        </div>

        {{-- Formulário --}}
        <form action="{{ route('production-supplies.update', $productionSupply) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                
                {{-- Dados Básicos --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                        <input type="text" name="name" value="{{ old('name', $productionSupply->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja/Local *</label>
                        <select name="store_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store_id', $productionSupply->store_id) == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo *</label>
                        <input type="text" name="type" value="{{ old('type', $productionSupply->type) }}" list="types_list" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                        <datalist id="types_list">
                            <option value="aviamento">
                            <option value="tinta">
                            <option value="embalagem">
                            <option value="papelaria">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cor</label>
                        <input type="text" name="color" value="{{ old('color', $productionSupply->color) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                </div>

                {{-- Estoque --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantidade *</label>
                        <input type="number" step="0.001" name="quantity" value="{{ old('quantity', $productionSupply->quantity) }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unidade *</label>
                        <select name="unit" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                            <option value="un" {{ old('unit', $productionSupply->unit) == 'un' ? 'selected' : '' }}>Unidade (un)</option>
                            <option value="kg" {{ old('unit', $productionSupply->unit) == 'kg' ? 'selected' : '' }}>Quilos (kg)</option>
                            <option value="lt" {{ old('unit', $productionSupply->unit) == 'lt' ? 'selected' : '' }}>Litros (lt)</option>
                            <option value="mt" {{ old('unit', $productionSupply->unit) == 'mt' ? 'selected' : '' }}>Metros (mt)</option>
                            <option value="pct" {{ old('unit', $productionSupply->unit) == 'pct' ? 'selected' : '' }}>Pacote (pct)</option>
                            <option value="cx" {{ old('unit', $productionSupply->unit) == 'cx' ? 'selected' : '' }}>Caixa (cx)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estoque Mínimo</label>
                        <input type="number" step="0.001" name="min_stock" value="{{ old('min_stock', $productionSupply->min_stock) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">
                    </div>
                </div>

                {{-- Observações --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg">{{ old('notes', $productionSupply->notes) }}</textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('production-supplies.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
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
