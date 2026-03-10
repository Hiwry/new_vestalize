<div id="products-grid" class="pdv-products-grid">
    @forelse($paginatedItems as $item)
        @if($type == 'products')
            @if($item instanceof \App\Models\Product)
                @php
                    $productImage = $item->primary_image_url ?? ($item->image_path ? Storage::url($item->image_path) : null);
                @endphp

                <div class="pdv-product-card">
                    <div class="pdv-product-media">
                        @if($productImage)
                            <div class="pdv-product-thumb">
                                <img src="{{ $productImage }}" alt="{{ $item->title }}">
                            </div>
                        @else
                            <div class="pdv-product-thumb pdv-product-thumb-placeholder">
                                <i class="fa-solid fa-shirt"></i>
                            </div>
                        @endif

                        <div class="pdv-product-summary">
                            <div class="pdv-product-head">
                                <div class="min-w-0 flex-1">
                                    <span class="pdv-product-label">Produto</span>
                                    <h3 class="pdv-product-title line-clamp-2">{{ $item->title }}</h3>
                                </div>
                            </div>

                            <div class="pdv-product-meta">
                                @if($item->category)
                                    <span class="pdv-chip">{{ $item->category->name }}</span>
                                @endif
                                @if($item->cut_type_id)
                                    <span class="pdv-chip pdv-chip-accent">Cor + tamanho</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pdv-product-footer">
                        <div>
                            <p class="pdv-product-price-label">Preco</p>
                            <p class="pdv-product-price">
                                R$ {{ number_format($item->price ?? 0, 2, ',', '.') }}
                            </p>
                        </div>

                        <button onclick="openAddProductModal({{ $item->id }}, 'product')"
                                class="pdv-grid-action pdv-product-action py-3 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm"
                                style="color: white !important;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: white !important;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span style="color: white !important;">Adicionar</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="pdv-product-card">
                    <div class="pdv-product-media">
                        <div class="pdv-product-thumb pdv-product-thumb-placeholder">
                            <i class="fa-solid fa-scissors"></i>
                        </div>

                        <div class="pdv-product-summary">
                            <div class="pdv-product-head">
                                <div class="min-w-0 flex-1">
                                    <span class="pdv-product-label">Tipo de corte</span>
                                    <h3 class="pdv-product-title line-clamp-2">{{ $item->name }}</h3>
                                </div>
                            </div>

                            <div class="pdv-product-meta">
                                <span class="pdv-chip pdv-chip-accent">Opcao</span>
                            </div>
                        </div>
                    </div>

                    <div class="pdv-product-footer">
                        <div>
                            <p class="pdv-product-price-label">Preco</p>
                            <p class="pdv-product-price">R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                        </div>

                        <button onclick="openAddProductModal({{ $item->id }}, 'product_option')"
                                class="pdv-grid-action pdv-product-action py-3 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm"
                                style="color: white !important;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: white !important;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span style="color: white !important;">Adicionar</span>
                        </button>
                    </div>
                </div>
            @endif
        @else
            <div class="pdv-product-card">
                <div class="pdv-product-media">
                    <div class="pdv-product-thumb pdv-product-thumb-placeholder">
                        @if($type === 'fabric_pieces')
                            <i class="fa-solid fa-ruler-combined"></i>
                        @elseif($type === 'machines')
                            <i class="fa-solid fa-gears"></i>
                        @elseif($type === 'supplies')
                            <i class="fa-solid fa-box-open"></i>
                        @else
                            <i class="fa-solid fa-shirt"></i>
                        @endif
                    </div>

                    <div class="pdv-product-summary">
                        <div class="pdv-product-head">
                            <div class="min-w-0 flex-1">
                                <span class="pdv-product-label">{{ $item->type_label ?? 'Item' }}</span>
                                <h3 class="pdv-product-title line-clamp-2">{{ $item->title }}</h3>
                            </div>
                        </div>

                        <div class="pdv-product-meta">
                            @if(isset($item->stock_quantity))
                                <span class="pdv-chip {{ $item->stock_quantity > 0 ? 'pdv-chip-success' : 'pdv-chip-danger' }}">
                                    {{ $item->stock_label ?? $item->stock_quantity }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pdv-product-footer">
                    <div>
                        <p class="pdv-product-price-label">Preco</p>
                        @if(isset($item->price) && $item->price > 0)
                            <p class="pdv-product-price">R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                        @else
                            <p class="text-sm text-gray-500 italic mt-1">Definir valor</p>
                        @endif
                    </div>

                    <button onclick="openAddProductModal({{ $item->id }}, '{{ substr($type, 0, -1) }}')"
                            class="pdv-grid-action pdv-product-action py-3 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm"
                            style="color: white !important;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: white !important;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span style="color: white !important;">Adicionar</span>
                    </button>
                </div>
            </div>
        @endif
    @empty
        <div class="col-span-full">
            <div class="pdv-empty-state min-h-[280px]">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <div>
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-[var(--pdv-text-primary)]">Nenhum item encontrado</h3>
                    <p class="text-gray-500 dark:text-[var(--pdv-text-secondary)] mt-1 text-sm">Tente ajustar sua busca ou trocar a categoria.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="pdv-pagination">
    <p class="text-xs md:text-sm text-gray-600 dark:text-[var(--pdv-text-secondary)]">
        {{ $paginatedItems->firstItem() ?? 0 }}-{{ $paginatedItems->lastItem() ?? 0 }} de {{ $paginatedItems->total() }}
    </p>

    <div class="pdv-pagination-actions">
        @if($paginatedItems->previousPageUrl())
            <button type="button"
               data-url="{{ $paginatedItems->previousPageUrl() }}"
               class="pdv-pagination-btn">
                &larr; Ant.
            </button>
        @endif
        @if($paginatedItems->nextPageUrl())
            <button type="button"
               data-url="{{ $paginatedItems->nextPageUrl() }}"
               class="pdv-pagination-btn">
                Prox. &rarr;
            </button>
        @endif
    </div>
</div>
