@extends('layouts.admin')

@push('styles')
<style>
    .card-hover {
        transition: all 0.2s ease;
    }
    .card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .dark .card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Preços SUB. TOTAL</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure preços e adicionais para cada tipo de produto</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($tenant->id)
                <!-- Toggle SUB. TOTAL -->
                <form method="POST" action="{{ route('admin.sublimation-products.toggle-enabled') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-colors {{ $tenant->sublimation_total_enabled ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border-green-300 dark:border-green-700' : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-300 dark:border-gray-600' }}">
                        <span class="w-2 h-2 rounded-full mr-2 {{ $tenant->sublimation_total_enabled ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $tenant->sublimation_total_enabled ? 'Habilitada' : 'Desabilitada' }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Cards de Tipos de Produto -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($productTypes as $typeData)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden card-hover">
            <!-- Card Header -->
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        {!! $typeData['icon'] !!}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $typeData['label'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">SUB. TOTAL</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-5">
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $typeData['prices_count'] }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Faixas</div>
                    </div>
                    <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                        <div class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ $typeData['addons_count'] }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Adicionais</div>
                    </div>
                    <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                        @if($typeData['min_price'])
                        <div class="text-lg font-semibold text-green-600 dark:text-green-400">{{ number_format($typeData['min_price'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Min R$</div>
                        @else
                        <div class="text-lg font-semibold text-gray-400">-</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Sem preço</div>
                        @endif
                    </div>
                </div>

                <!-- Action Button -->
                <a href="{{ route('admin.sublimation-products.edit-type', $typeData['slug']) }}" 
                   class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 text-white text-sm font-medium rounded-md transition-colors duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Configurar
                </a>
            </div>
        </div>
        @endforeach

        <!-- Card Adicionar Novo Tipo -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border-2 border-dashed border-gray-300 dark:border-gray-600 overflow-hidden card-hover">
            <div class="p-6 flex flex-col items-center justify-center h-full min-h-[200px]">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Novo Tipo</h3>
                <form method="POST" action="{{ route('admin.sublimation-products.types.store') }}" class="w-full max-w-xs">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" name="name" placeholder="Ex: Caneca, Toalha" required
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-center">
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
