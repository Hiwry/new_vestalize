<div id="products-grid" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6">
    @forelse($paginatedItems as $item)
        @if($type == 'products')
            @if($item instanceof \App\Models\Product)
                {{-- Product Card --}}
                <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl p-3 md:p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                    {{-- Product Image (if exists) --}}
                    @if($item->image_path)
                    <div class="aspect-square rounded-lg overflow-hidden mb-2 md:mb-3 bg-gray-100 dark:bg-gray-700">
                        <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    
                    <div class="flex-1 mb-2 md:mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm md:text-lg leading-tight mb-0.5 md:mb-1 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $item->title }}
                        </h3>
                        @if($item->category)
                            <span class="hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                {{ $item->category->name }}
                            </span>
                        @endif
                        
                        <div class="mt-2 md:mt-4">
                            @if($item->price)
                                <p class="text-base md:text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    R$ {{ number_format($item->price, 2, ',', '.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <button onclick="openAddProductModal({{ $item->id }}, 'product')" 
                            class="w-full py-2 md:py-2.5 bg-indigo-600 text-white rounded-lg md:rounded-xl hover:bg-indigo-700 transition-colors font-medium flex items-center justify-center gap-1 md:gap-2 text-sm">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden sm:inline">Adicionar</span>
                    </button>
                </div>
            @else
                {{-- Product Option Card --}}
                <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl p-3 md:p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700 flex flex-col h-full border-l-4 border-l-purple-500">
                    <div class="flex-1 mb-2 md:mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm md:text-lg leading-tight mb-0.5 line-clamp-2">{{ $item->name }}</h3>
                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium mb-2">Tipo de Corte</p>
                        
                        <p class="text-base md:text-2xl font-bold text-purple-600 dark:text-purple-400">
                            R$ {{ number_format($item->price, 2, ',', '.') }}
                        </p>
                    </div>
                    <button onclick="openAddProductModal({{ $item->id }}, 'product_option')" 
                            class="w-full py-2 md:py-2.5 bg-purple-600 text-white rounded-lg md:rounded-xl hover:bg-purple-700 transition-colors font-medium flex items-center justify-center gap-1 md:gap-2 text-sm">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="hidden sm:inline">Adicionar</span>
                    </button>
                </div>
            @endif
        @else
            {{-- Generic Stock Item Card --}}
            <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl p-3 md:p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                <div class="flex-1 mb-2 md:mb-4">
                    <div class="flex justify-between items-start gap-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 text-sm md:text-lg leading-tight mb-0.5 line-clamp-2">{{ $item->title }}</h3>
                            <p class="text-[10px] md:text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $item->type_label ?? 'Item' }}</p>
                        </div>
                        @if(isset($item->stock_quantity))
                            <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 md:px-2 md:py-1 rounded-lg text-[10px] md:text-xs font-bold {{ $item->stock_quantity > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $item->stock_quantity }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="mt-2 md:mt-4">
                        @if(isset($item->price) && $item->price > 0)
                            <p class="text-base md:text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                R$ {{ number_format($item->price, 2, ',', '.') }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400 italic">Definir valor</p>
                        @endif
                    </div>
                </div>
                <button onclick="openAddProductModal({{ $item->id }}, '{{ substr($type, 0, -1) }}')" 
                        class="w-full py-2 md:py-2.5 bg-indigo-600 text-white rounded-lg md:rounded-xl hover:bg-indigo-700 transition-colors font-medium flex items-center justify-center gap-1 md:gap-2 text-sm">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="hidden sm:inline">Adicionar</span>
                </button>
            </div>
        @endif
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12 md:py-16 text-center">
            <div class="bg-gray-100 dark:bg-gray-800 p-3 md:p-4 rounded-full mb-3 md:mb-4">
                <svg class="w-6 h-6 md:w-8 md:h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-gray-100">Nenhum item encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Tente outra busca.</p>
        </div>
    @endforelse
</div>

{{-- Pagination (Mobile optimized) --}}
<div class="mt-6 md:mt-8 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">
        {{ $paginatedItems->firstItem() ?? 0 }}-{{ $paginatedItems->lastItem() ?? 0 }} de {{ $paginatedItems->total() }}
    </p>
    <div class="flex gap-2">
        @if($paginatedItems->previousPageUrl())
            <a href="{{ $paginatedItems->previousPageUrl() }}&type={{ $type ?? 'products' }}&search={{ $search ?? '' }}" 
               class="px-3 py-1.5 md:px-4 md:py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                ← Ant.
            </a>
        @endif
        @if($paginatedItems->nextPageUrl())
            <a href="{{ $paginatedItems->nextPageUrl() }}&type={{ $type ?? 'products' }}&search={{ $search ?? '' }}" 
               class="px-3 py-1.5 md:px-4 md:py-2 bg-indigo-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-indigo-700 transition-colors">
                Próx. →
            </a>
        @endif
    </div>
</div>

