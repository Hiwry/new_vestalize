@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Editar Estoque</h1>
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
    <form method="POST" action="{{ route('stocks.update-group') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Loja (Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Loja
                </label>
                <input type="text" value="{{ $store->name }}" readonly
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                <input type="hidden" name="store_id" value="{{ $store->id }}">
            </div>

            <!-- Tipo de Corte (Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de Corte
                </label>
                <input type="text" value="{{ $cutType->name }}" readonly
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                <input type="hidden" name="cut_type_id" value="{{ $cutType->id }}">
            </div>

            <!-- Tecido (Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tecido
                </label>
                <input type="text" value="{{ $fabric->name }}" readonly
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                <input type="hidden" name="fabric_id" value="{{ $fabric->id }}">
            </div>

            <!-- Tipo de Tecido (Readonly) -->
            @if(isset($fabricType) && $fabricType)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de Tecido
                </label>
                <input type="text" value="{{ $fabricType->name }}" readonly
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                <input type="hidden" name="fabric_type_id" value="{{ $fabricType->id }}">
            </div>
            @endif

            <!-- Cor (Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cor
                </label>
                <input type="text" value="{{ $color->name }}" readonly
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                <input type="hidden" name="color_id" value="{{ $color->id }}">
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
                               value="{{ old("sizes.{$size}", $currentQuantities[$size] ?? 0) }}"
                               min="0"
                               step="1"
                               placeholder="0"
                               class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @if(isset($reservedQuantities[$size]) && $reservedQuantities[$size] > 0)
                        <p class="text-xs text-orange-500 text-center mt-1">
                            Res: {{ $reservedQuantities[$size] }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Informe a quantidade total física para cada tamanho. O sistema calculará automaticamente o disponível subtraindo as reservas.
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
                       value="{{ old('shelf', $commonShelf) }}"
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
                       value="{{ old('min_stock', $commonMinStock) }}"
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
                       value="{{ old('max_stock', $commonMaxStock) }}"
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
                Observações do Produto (Fixo)
            </label>
            <textarea name="notes" 
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $commonNotes) }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Motivo da Edição -->
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
            <label class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-2">
                Motivo da Alteração / Justificativa (Para Histórico)
            </label>
            <textarea name="edit_reason" 
                      rows="2"
                      placeholder="Ex: Ajuste de inventário, correção de cadastro..."
                      class="w-full px-3 py-2 border border-blue-300 dark:border-blue-600 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Esta observação ficará registrada no histórico de alterações.</p>
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('stocks.index') }}" 
               class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection
