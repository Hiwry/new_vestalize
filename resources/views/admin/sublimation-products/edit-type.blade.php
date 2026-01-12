@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.sublimation-products.index') }}" 
                       class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Preços SUB. TOTAL
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ $typeLabel }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal: Preços -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-purple-50 dark:bg-purple-900/20">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            {!! $typeIcon !!}
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $typeLabel }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configuração Geral e Faixas de Preço</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.sublimation-products.update-type', $type) }}" id="prices-form">
                    @csrf
                    @method('PUT')

                    <div class="p-6 space-y-6">
                        <!-- Configuração de Tecido -->
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <label for="tecido_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tecido Padrão para {{ $typeLabel }}
                            </label>
                            <div class="relative">
                                <select name="tecido_id" id="tecido_id" required
                                        class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="">Selecione um tecido...</option>
                                    @foreach($tecidos as $tecido)
                                        <option value="{{ $tecido->id }}" {{ ($productType->tecido_id == $tecido->id) ? 'selected' : '' }}>
                                            {{ strtoupper($tecido->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Este tecido será exibido automaticamente na folha de costura e no pedido para este tipo de produto.
                            </p>
                        </div>
                        <!-- Tabela de Preços -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse" id="prices-table">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">QUANTIDADE</th>
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 w-40">PREÇO (R$)</th>
                                        <th class="py-3 px-4 text-center border border-gray-200 dark:border-gray-600 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody id="prices-tbody">
                                    @forelse($prices as $index => $price)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-2 px-4 border border-gray-200 dark:border-gray-600">
                                            <div class="flex items-center gap-2">
                                                <input type="number" name="prices[{{ $index }}][quantity_from]" 
                                                       value="{{ $price->quantity_from }}" min="1" required
                                                       class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                                                <span class="text-gray-400">-</span>
                                                <input type="number" name="prices[{{ $index }}][quantity_to]" 
                                                       value="{{ $price->quantity_to }}" min="1"
                                                       placeholder="∞"
                                                       class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 border border-gray-200 dark:border-gray-600">
                                            <input type="number" name="prices[{{ $index }}][price]" 
                                                   value="{{ $price->price }}" step="0.01" min="0" required
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right font-bold text-green-600 dark:text-green-400">
                                            <input type="hidden" name="prices[{{ $index }}][id]" value="{{ $price->id }}">
                                        </td>
                                        <td class="py-2 px-4 border border-gray-200 dark:border-gray-600 text-center">
                                            <button type="button" onclick="removeRow(this)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr id="empty-row">
                                        <td colspan="3" class="py-8 text-center text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                            Nenhuma faixa de preço. Clique em "Adicionar Faixa".
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Botão adicionar faixa -->
                        <div class="flex justify-center">
                            <button type="button" onclick="addRow()" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Faixa
                            </button>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.sublimation-products.index') }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Voltar
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 text-sm font-medium bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar Preços
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Coluna Lateral: Adicionais deste tipo -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/20">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Adicionais</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Exclusivos para {{ $typeLabel }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-5 space-y-2 max-h-[400px] overflow-y-auto">
                @forelse($addons as $addon)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ strtoupper($addon->name) }}</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm {{ $addon->price >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $addon->price >= 0 ? '+' : '' }}R$ {{ number_format($addon->price, 2, ',', '.') }}
                        </span>
                        <form method="POST" action="{{ route('admin.sublimation-products.addons.destroy', $addon) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Remover?')" class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhum adicional cadastrado</p>
                @endforelse
            </div>

            <!-- Form adicionar -->
            <div class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <form method="POST" action="{{ route('admin.sublimation-products.addons.store', $type) }}" class="space-y-2">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" name="name" placeholder="Nome (ex: GOLA V)" required
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 uppercase">
                        <input type="number" name="price" step="0.01" placeholder="R$" required
                               class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar
                    </button>
                </form>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Valor negativo para desconto</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rowIndex = {{ $prices->count() }};

    function addRow() {
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const tbody = document.getElementById('prices-tbody');
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700/50';
        row.innerHTML = `
            <td class="py-2 px-4 border border-gray-200 dark:border-gray-600">
                <div class="flex items-center gap-2">
                    <input type="number" name="prices[${rowIndex}][quantity_from]" 
                           min="1" required placeholder="De"
                           class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                    <span class="text-gray-400">-</span>
                    <input type="number" name="prices[${rowIndex}][quantity_to]" 
                           min="1" placeholder="∞"
                           class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                </div>
            </td>
            <td class="py-2 px-4 border border-gray-200 dark:border-gray-600">
                <input type="number" name="prices[${rowIndex}][price]" 
                       step="0.01" min="0" required placeholder="0,00"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right font-bold text-green-600 dark:text-green-400">
            </td>
            <td class="py-2 px-4 border border-gray-200 dark:border-gray-600 text-center">
                <button type="button" onclick="removeRow(this)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        rowIndex++;
        row.querySelector('input').focus();
    }

    function removeRow(btn) {
        if (confirm('Remover esta faixa?')) {
            btn.closest('tr').remove();
        }
    }
</script>
@endpush
@endsection
