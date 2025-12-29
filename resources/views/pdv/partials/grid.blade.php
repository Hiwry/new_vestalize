<div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($paginatedItems as $item)
        @if($type == 'products')
            @if($item instanceof \App\Models\Product)
                <!-- Product Card -->
                <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                    <div class="flex-1 mb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg leading-tight mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $item->title }}
                                </h3>
                                @if($item->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                        {{ $item->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            @if($item->price)
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    R$ {{ number_format($item->price, 2, ',', '.') }}
                                    @if($item->sale_type && $item->sale_type != 'unidade')
                                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ {{ $item->sale_type == 'kg' ? 'Kg' : 'Metro' }}</span>
                                    @endif
                                </p>
                            @endif
                            
                            @if($item->allow_application)
                                <div class="flex items-center gap-1.5 mt-2 text-sm text-green-600 dark:text-green-400 font-medium bg-green-50 dark:bg-green-900/20 w-fit px-2 py-1 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Permite aplicação
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <button onclick="openAddProductModal({{ $item->id }}, 'product')" 
                            class="w-full py-2.5 bg-gray-900 dark:bg-indigo-600 text-white rounded-xl hover:bg-gray-800 dark:hover:bg-indigo-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Adicionar
                    </button>
                </div>
            @else
                <!-- Product Option Card -->
                <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700 flex flex-col h-full border-l-4 border-l-purple-500">
                    <div class="flex-1 mb-4">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg leading-tight mb-1">{{ $item->name }}</h3>
                        <p class="text-sm text-purple-600 dark:text-purple-400 font-medium mb-3">Tipo de Corte</p>
                        
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            R$ {{ number_format($item->price, 2, ',', '.') }}
                        </p>
                    </div>
                    <button onclick="openAddProductModal({{ $item->id }}, 'product_option')" 
                            class="w-full py-2.5 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Adicionar
                    </button>
                </div>
            @endif
        @else
            <!-- Generic Stock Item Card -->
            <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                <div class="flex-1 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg leading-tight mb-1">{{ $item->title }}</h3>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $item->type_label ?? 'Item' }}</p>
                            
                            @if($type == 'fabric_pieces')
                                <div class="mt-2 space-y-0.5">
                                    <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                        {{ $item->fabric_type_name ?? 'Tecido' }}
                                    </p>
                                    @if($item->supplier_name)
                                        <p class="text-[10px] text-gray-400 font-medium">Fornecedor: {{ $item->supplier_name }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @if(isset($item->stock_quantity))
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold {{ $item->stock_quantity > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $item->stock_quantity }} un
                            </span>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        @if(isset($item->price) && $item->price > 0)
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                R$ {{ number_format($item->price, 2, ',', '.') }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500 italic flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Definir valor no carrinho
                            </p>
                        @endif

                        @if($type == 'fabric_pieces' && isset($item->original_id))
                            <p class="text-xs text-gray-400 mt-2 font-mono">ID: {{ $item->original_id }}</p>
                        @endif
                    </div>
                </div>
                <button onclick="openAddProductModal({{ $item->id }}, '{{ substr($type, 0, -1) }}')" 
                        class="w-full py-2.5 bg-gray-900 dark:bg-indigo-600 text-white rounded-xl hover:bg-gray-800 dark:hover:bg-indigo-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adicionar
                </button>
            </div>
        @endif
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Nenhum item encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Tente buscar por outro termo ou categoria.</p>
        </div>
    @endforelse
</div>

<!-- Pagination (Simplified) -->
<div class="mt-8 flex items-center justify-between">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        Mostrando {{ $paginatedItems->firstItem() ?? 0 }} - {{ $paginatedItems->lastItem() ?? 0 }} de {{ $paginatedItems->total() }} itens
    </p>
    <div class="flex gap-2">
        @if($paginatedItems->previousPageUrl())
            <a href="{{ $paginatedItems->previousPageUrl() }}&type={{ $type ?? 'products' }}&search={{ $search ?? '' }}" 
               class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                ← Anterior
            </a>
        @endif
        @if($paginatedItems->nextPageUrl())
            <a href="{{ $paginatedItems->nextPageUrl() }}&type={{ $type ?? 'products' }}&search={{ $search ?? '' }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                Próximo →
            </a>
        @endif
    </div>
</div>
