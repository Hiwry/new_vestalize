@extends('catalog.layouts.catalog')

@section('title', $tenant->name . ' - Catálogo')

@section('content')

<!-- Category Filter Tabs -->
<div class="category-bar">
    <div class="category-tabs">
        <a href="{{ route('catalog.show', $storeCode) }}" 
           class="category-tab {{ !$activeCategory ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Todos
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('catalog.show', ['storeCode' => $storeCode, 'category' => $cat->slug]) }}" 
               class="category-tab {{ $activeCategory && $activeCategory->id === $cat->id ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>

<!-- Products Grid -->
@if($products->count() > 0)
    <div class="products-grid">
        @foreach($products as $index => $product)
            @php
                $imageUrl = $product->primary_image_url;
            @endphp
            <div class="product-card" style="animation-delay: {{ $index * 0.05 }}s">
                <a href="{{ route('catalog.product', ['storeCode' => $storeCode, 'product' => $product->id]) }}" style="text-decoration:none;color:inherit;">
                    <div class="product-card-image">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->title }}" loading="lazy">
                        @else
                            <div class="no-image"><i class="fas fa-image"></i></div>
                        @endif
                        @if($product->track_stock && !$product->isInStock())
                            <div class="product-badge out-of-stock">Esgotado</div>
                        @endif
                    </div>

                <div class="product-card-body">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        @if($product->category)
                            <div class="product-card-category">{{ $product->category->name }}</div>
                        @endif
                    </div>
                    <div class="product-card-title">{{ $product->title }}</div>
                    <div class="product-card-prices" style="margin-top: auto;">
                        <span class="product-price-retail" style="font-size: 18px; color: #1e293b;">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                        @if($product->hasWholesalePrice())
                            <div class="product-price-wholesale" style="background: #f0fdf4; color: #16a34a; padding: 4px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                <i class="fas fa-tag"></i> 
                                <span style="font-size: 11px; font-weight: 700;">R$ {{ number_format($product->wholesale_price, 2, ',', '.') }}</span>
                                <span style="font-size: 10px; opacity: 0.8;">({{ $product->wholesale_min_qty }}+ un.)</span>
                            </div>
                        @endif
                    </div>
                </div>
            </a>

            <div style="padding: 0 16px 16px;">
                @if(!$product->track_stock || $product->isInStock())
                    @if($product->cut_type_id)
                        <a href="{{ route('catalog.product', ['storeCode' => $storeCode, 'product' => $product->id]) }}" 
                           class="product-quick-add" 
                           style="background: #1e293b; border-radius: 12px; height: 44px; font-weight: 700; text-decoration: none;">
                            <i class="fas fa-eye" style="font-size: 12px; margin-right: 4px;"></i> Ver Detalhes
                        </a>
                    @else
                        <button class="product-quick-add" 
                                style="background: #1e293b; border-radius: 12px; height: 44px; font-weight: 700;"
                                onclick="event.stopPropagation(); addToCart({{ $product->id }}, null, null, 1)">
                            <i class="fas fa-shopping-cart" style="font-size: 12px; margin-right: 4px;"></i> Adicionar
                        </button>
                    @endif
                @else
                    <button class="product-quick-add" disabled
                            style="background: #94a3b8; border-radius: 12px; height: 44px; font-weight: 700; cursor: not-allowed; opacity: 0.6;">
                        <i class="fas fa-ban" style="font-size: 12px; margin-right: 4px;"></i> Esgotado
                    </button>
                @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div style="display: flex; justify-content: center; padding: 24px 0;">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>Nenhum produto encontrado</h3>
        <p>Tente outra categoria ou volte mais tarde.</p>
    </div>
@endif

@endsection
