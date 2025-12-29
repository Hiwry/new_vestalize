@extends('layouts.catalog')

@section('content')

@if(isset($message))
<div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
    <p class="text-yellow-800 dark:text-yellow-200">{{ $message }}</p>
</div>
@endif

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-3 sm:p-4 lg:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('catalog.index') }}" class="space-y-3 sm:space-y-4">
        <!-- Busca -->
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 sm:mb-2">Buscar</label>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Buscar itens..."
                   class="w-full px-3 py-2 sm:py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm sm:text-base">
        </div>

        <!-- Filtro de categoria -->
        <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 sm:mb-2">Categoria</label>
            <select name="category" class="w-full px-3 py-2 sm:py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm sm:text-base">
                <option value="">Todas</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (string)request('category') === (string)$category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Botões de Ação -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-2">
            <button type="submit" class="flex-1 sm:flex-none px-4 sm:px-6 py-2.5 sm:py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 active:bg-indigo-800 transition text-sm sm:text-base font-medium shadow-sm hover:shadow-md">
                <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar
                </span>
            </button>
            <a href="{{ route('catalog.index') }}" class="flex-1 sm:flex-none px-4 sm:px-6 py-2.5 sm:py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 transition text-sm sm:text-base font-medium text-center">
                Limpar Filtros
            </a>
        </div>
    </form>
</div>

<!-- Lista de Itens -->
@if($items->count() > 0)
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
    @foreach($items as $item)
    <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow dark:shadow-gray-900/25 overflow-hidden hover:shadow-xl dark:hover:shadow-gray-900/50 transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98]">
        <a href="{{ route('catalog.show', $item->id) }}" class="block">
            @if($item->image_path)
                <div class="aspect-square bg-gray-200 dark:bg-gray-700 overflow-hidden relative">
                    <img src="{{ url('/storage/' . $item->image_path) }}" 
                         alt="{{ $item->title }}"
                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                         loading="lazy"
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><svg class=\'w-12 h-12 sm:w-16 sm:h-16 text-gray-400 dark:text-gray-500\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\' /></svg></div>';">
                </div>
            @else
                <div class="aspect-square bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            
            <div class="p-3 sm:p-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1.5 sm:mb-2 line-clamp-2 min-h-[2.5rem] sm:min-h-[3rem]">
                    {{ $item->title }}
                </h3>

                @if($item->subtitle)
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-2 sm:mb-3 line-clamp-2 hidden sm:block">
                    {{ $item->subtitle }}
                </p>
                @endif

                @if($item->category)
                <div class="flex flex-wrap gap-1 sm:gap-1.5 mt-2">
                    <span class="text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded">
                        {{ $item->category->name }}
                    </span>
                </div>
                @endif
            </div>
        </a>
    </div>
    @endforeach
</div>

<!-- Paginação -->
<div class="mt-4 sm:mt-6">
    <div class="flex justify-center">
        {{ $items->links() }}
    </div>
</div>
@else
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-8 sm:p-12 text-center">
    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 dark:text-gray-500 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
    </svg>
    <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhum produto encontrado</h3>
    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 px-4">
        @if(request()->has('search') || request()->has('category'))
            Tente ajustar os filtros de busca.
        @else
            O catálogo está vazio no momento.
        @endif
    </p>
</div>
@endif
@endsection

