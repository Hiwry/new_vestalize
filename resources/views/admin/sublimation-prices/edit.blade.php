@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.sublimation-prices.index') }}" 
                       class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Preços de Sublimação
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">Editar Preços</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.sublimation-prices.update', $size) }}" id="prices-form">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $size->name }} ({{ $size->dimensions }})</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure as faixas de quantidade e seus respectivos preços</p>
            </div>

            <div class="p-6">
                <div id="prices-container" class="space-y-4">
                    @if($prices->count() > 0)
                        @foreach($prices as $index => $price)
                            @include('admin.sublimation-prices.partials.price-row', ['index' => $index, 'price' => $price])
                        @endforeach
                    @else
                        @include('admin.sublimation-prices.partials.price-row', ['index' => 0, 'price' => null])
                    @endif
                </div>

                <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="addPriceRow()" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Adicionar Faixa
                    </button>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('admin.sublimation-prices.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Voltar
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Preços
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Importante</h3>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                    <ul class="list-disc list-inside space-y-1">
                        <li>As faixas devem ser sequenciais e não se sobrepor</li>
                        <li>Deixe "Até" vazio para indicar "infinito" (última faixa)</li>
                        <li>As alterações se aplicam imediatamente a novos pedidos</li>
                        <li>Use o botão "Testar Preços" para verificar se está funcionando</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        let priceRowIndex = {{ $prices->count() }};

        function addPriceRow() {
            fetch(`{{ route('admin.sublimation-prices.add-row') }}?index=${priceRowIndex}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('prices-container').insertAdjacentHTML('beforeend', html);
                    priceRowIndex++;
                })
                .catch(error => {
                    console.error('Erro ao adicionar linha:', error);
                    alert('Erro ao adicionar linha. Tente novamente.');
                });
        }

        function removePriceRow(button) {
            const row = button.closest('.price-row');
            row.remove();
        }

        function validatePrices() {
            const rows = document.querySelectorAll('.price-row');
            const quantities = [];
            
            for (let row of rows) {
                const from = parseInt(row.querySelector('input[name*="[quantity_from]"]').value);
                const to = row.querySelector('input[name*="[quantity_to]"]').value;
                const toValue = to ? parseInt(to) : null;
                
                if (from && from > 0) {
                    quantities.push({ from, to: toValue, row });
                }
            }
            
            // Ordenar por quantidade inicial
            quantities.sort((a, b) => a.from - b.from);
            
            // Verificar sobreposições
            for (let i = 0; i < quantities.length - 1; i++) {
                const current = quantities[i];
                const next = quantities[i + 1];
                
                if (current.to && current.to >= next.from) {
                    alert(`Sobreposição detectada: Faixa ${current.from}-${current.to} se sobrepõe com ${next.from}-${next.to || '∞'}`);
                    return false;
                }
            }
            
            return true;
        }

        document.getElementById('prices-form').addEventListener('submit', function(e) {
            if (!validatePrices()) {
                e.preventDefault();
            }
        });
    </script>
</div>
@endsection
