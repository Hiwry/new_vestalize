@extends('catalog.layouts.catalog')

@section('title', $product->title . ' - ' . $tenant->name)

@section('extra-styles')
<style>
    .product-detail {
        padding: 20px 0;
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }
    @media (min-width: 768px) {
        .product-detail { grid-template-columns: 1.1fr 0.9fr; gap: 48px; padding: 40px 20px 120px; }
    }

    .gallery-container {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .gallery-main {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        display: block;
    }
    .gallery-thumbs {
        display: flex;
        gap: 12px;
        padding: 16px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .gallery-thumbs::-webkit-scrollbar { display: none; }
    .gallery-thumb {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .gallery-thumb.active {
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    .product-info { padding: 0; }

    .product-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 20px;
        font-weight: 500;
    }
    .product-breadcrumb a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s;
    }
    .product-breadcrumb a:hover { color: var(--primary); }

    .product-detail-title {
        font-size: 36px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 12px;
        line-height: 1.1;
        letter-spacing: -1px;
    }

    .product-sku {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        padding: 4px 10px;
        background: #f8fafc;
        border-radius: 6px;
    }

    .price-section {
        margin-bottom: 32px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        display: inline-block;
        min-width: 200px;
    }
    .price-label {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }
    .price-main {
        font-size: 36px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -1.5px;
    }
    .price-wholesale-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
        padding: 8px 14px;
        background: #dcfce7;
        border-radius: 12px;
        color: #166534;
        font-size: 13px;
        font-weight: 600;
    }

    .option-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .option-selected {
        font-weight: 900;
        color: var(--primary);
    }

    /* Size Pills */
    .size-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 10px;
        margin-bottom: 32px;
    }
    .size-btn {
        position: relative;
        height: 52px;
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        background: white;
        color: #475569;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .size-btn:hover { border-color: #cbd5e1; transform: translateY(-1px); }
    .size-btn.active {
        border-color: #0f172a;
        background: #0f172a;
        color: white;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
    }
    .size-btn .qty-badge {
        font-size: 9px;
        color: #94a3b8;
        margin-top: 2px;
    }
    .size-btn.active .qty-badge { color: rgba(255,255,255,0.6); }

    /* Color Swatches */
    .color-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 32px;
    }
    .color-option {
        position: relative;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid white;
        box-shadow: 0 0 0 1px #e2e8f0;
    }
    .color-option.active {
        box-shadow: 0 0 0 2px white, 0 0 0 4px #0f172a;
        transform: scale(1.1);
    }
    .color-option .stock-hint {
        position: absolute;
        bottom: -22px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
        color: #94a3b8;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .color-option:hover .stock-hint { opacity: 1; }

    .quick-variant-card {
        margin-bottom: 20px;
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }
    .quick-variant-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }
    @media (min-width: 640px) {
        .quick-variant-grid { grid-template-columns: 1fr 1fr; }
    }
    .quick-variant-label {
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.4px;
    }
    .quick-variant-select {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        background: white;
        color: #334155;
        font-size: 14px;
        font-weight: 700;
        padding: 0 12px;
        outline: none;
        transition: all 0.2s;
    }
    .quick-variant-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-light);
    }
    .quick-variant-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .quick-variant-meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }
    .quick-color-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        display: inline-block;
        flex-shrink: 0;
    }

    /* Premium Aesthetic Refinements */
    .product-detail {
        opacity: 0;
        animation: fadeInUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    .gallery-container, .price-section, .wholesale-grid-container, .wholesale-mobile-grid {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    .action-section {
        margin-top: 40px;
        padding: 32px 0 100px; /* Extra padding for sticky cta */
        border-top: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Wholesale Grid - Desktop */
    .wholesale-grid-container {
        display: none;
    }
    @media (min-width: 1024px) {
        .wholesale-grid-container {
            display: block;
            margin-bottom: 32px;
            overflow: hidden;
            border-radius: 20px;
        }
    }

    .wholesale-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .wholesale-table th {
        background: #f8fafc;
        padding: 16px 12px;
        font-weight: 800;
        color: #475569;
        font-size: 11px;
    }

    .wholesale-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s;
    }

    .wholesale-table tr:hover td { background: #fcfdfe; }

    /* Wholesale Grid - Mobile (Cards) */
    .wholesale-mobile-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 32px;
    }
    @media (min-width: 1024px) {
        .wholesale-mobile-grid { display: none; }
    }

    .wholesale-card {
        padding: 16px;
        border-radius: 20px;
        background: white;
        border: 1px solid #f1f5f9;
    }

    .wholesale-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f8fafc;
    }

    .wholesale-card-size {
        font-weight: 900;
        color: #0f172a;
        font-size: 16px;
    }

    .wholesale-variant-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
    }

    .wholesale-variant-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .variant-color-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
    }

    .variant-color-name {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .wholesale-qty-input {
        width: 60px;
        height: 42px;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        text-align: center;
        font-weight: 800;
        font-size: 15px;
        color: #0f172a;
        transition: all 0.2s;
        -moz-appearance: textfield;
    }
    .wholesale-qty-input::-webkit-outer-spin-button,
    .wholesale-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none; margin: 0;
    }

    .wholesale-qty-input:focus {
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px var(--primary-light);
    }

    /* Sticky Bottom Bar (Mobile) */
    .mobile-sticky-cta {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 16px 20px 32px;
        border-top: 1px solid rgba(0,0,0,0.05);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 -10px 25px rgba(0,0,0,0.05);
        transform: translateY(100%);
        transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .mobile-sticky-cta.visible { transform: translateY(0); }

    .sticky-total-price {
        display: flex;
        flex-direction: column;
    }
    .sticky-total-price .label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
    .sticky-total-price .val { font-size: 20px; font-weight: 900; color: #0f172a; }

    .sticky-add-btn {
        flex: 1;
        height: 52px;
        border-radius: 16px;
        background: #0f172a;
        color: white;
        font-weight: 800;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Touch Optimizations */
    @media (max-width: 768px) {
        .product-detail-title { font-size: 28px; }
        .price-main { font-size: 32px; }
        .wholesale-grid-container { display: none; }
    }

    .qty-input-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        padding: 8px;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        width: fit-content;
    }
    .qty-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: none;
        background: white;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .qty-btn:hover { background: #f1f5f9; transform: scale(1.05); }
    .qty-display {
        width: 60px;
        text-align: center;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .add-btn {
        height: 64px;
        border-radius: 20px;
        background: #0f172a;
        color: white;
        font-size: 16px;
        font-weight: 800;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        min-width: 140px;
        flex-shrink: 0;
    }
    .add-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.25);
        background: #1e293b;
    }
    .add-btn:disabled { background: #94a3b8; cursor: not-allowed; opacity: 0.7; }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>
@endsection

@section('content')

<div class="product-detail">
    <!-- Gallery -->
    <div class="gallery-container" x-data="{ activeImage: '{{ $product->primary_image_url ?? '' }}' }">
        <template x-if="activeImage">
            <img :src="activeImage" alt="{{ $product->title }}" class="gallery-main">
        </template>
        <template x-if="!activeImage">
            <div style="width:100%;aspect-ratio:1;display:flex;align-items:center;justify-content:center;background:#f8fafc;color:#cbd5e1;font-size:64px;">
                <i class="fas fa-image"></i>
            </div>
        </template>

        @if($product->images->count() > 1)
            <div class="gallery-thumbs">
                @foreach($product->images as $img)
                    @php
                        $imgUrl = str_starts_with($img->image_path, 'http') 
                            ? $img->image_path 
                            : asset('storage/' . $img->image_path);
                    @endphp
                    <img src="{{ $imgUrl }}" alt="" class="gallery-thumb"
                         :class="{ 'active': activeImage === '{{ $imgUrl }}' }"
                         @click="activeImage = '{{ $imgUrl }}'">
                @endforeach
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="product-info" x-data='{ 
        qty: 1, 
        selectedSize: "", 
        selectedColor: "", 
        selectedColorHex: "", 
        bulkItems: {},
        variantStock: 0,
        stockData: @json($stockGrid),

        checkSpecificStock() {
            if (this.selectedSize && this.selectedColor) {
                this.variantStock = this.stockData[this.selectedSize] ? (this.stockData[this.selectedSize][this.selectedColor] || 0) : 0;
                if (this.qty > this.variantStock && this.variantStock > 0) this.qty = this.variantStock;
            } else {
                this.variantStock = 0;
            }
        },

        getVariantStock(size, color) {
            if (!size || !color) return 0;
            return this.stockData[size] ? (parseInt(this.stockData[size][color]) || 0) : 0;
        },

        getSizeAvailability(size) {
            if (this.selectedColor) {
                return this.getVariantStock(size, this.selectedColor);
            }

            const row = this.stockData[size] || {};
            return Object.values(row).reduce((sum, value) => sum + (parseInt(value) || 0), 0);
        },

        getColorAvailability(color) {
            if (this.selectedSize) {
                return this.getVariantStock(this.selectedSize, color);
            }

            return Object.values(this.stockData).reduce((sum, row) => {
                return sum + (parseInt((row || {})[color]) || 0);
            }, 0);
        },

        get totalBulkQty() {
            return Object.values(this.bulkItems).reduce((sum, q) => sum + (parseInt(q) || 0), 0);
        },
        get effectiveQty() {
            return this.totalBulkQty > 0 ? this.totalBulkQty : this.qty;
        },
        get subtotal() {
            const q = this.effectiveQty;
            const price = (q >= {{ $product->wholesale_min_qty ?? 999 }} && {{ $product->wholesale_price ? "true" : "false" }}) 
                ? {{ (float)($product->wholesale_price ?? $product->price) }} 
                : {{ (float)$product->price }};
            return price * q;
        }
    }'>
        <div class="product-breadcrumb">
            <a href="{{ route('catalog.show', $storeCode) }}">Catálogo</a>
            <i class="fas fa-chevron-right" style="font-size: 8px;"></i>
            @if($product->category)
                <a href="{{ route('catalog.show', ['storeCode' => $storeCode, 'category' => $product->category->slug]) }}">
                    {{ $product->category->name }}
                </a>
                <i class="fas fa-chevron-right" style="font-size: 8px;"></i>
            @endif
            <span>{{ Str::limit($product->title, 20) }}</span>
        </div>

        <h1 class="product-detail-title">{{ $product->title }}</h1>

        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
            @if($product->sku)
                <div class="product-sku" style="margin-bottom: 0;">SKU: {{ $product->sku }}</div>
            @endif

            @if($totalStock > 0)
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px;">
                    <div style="width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></div>
                    <span style="font-size: 11px; font-weight: 700; color: #16a34a; text-transform: uppercase;">{{ $totalStock }} em estoque</span>
                </div>
            @else
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px;">
                    <div style="width: 6px; height: 6px; background: #ef4444; border-radius: 50%;"></div>
                    <span style="font-size: 11px; font-weight: 700; color: #dc2626; text-transform: uppercase;">Esgotado</span>
                </div>
            @endif
        </div>

        <!-- Pricing -->
        <div class="price-section">
            <div class="price-label">Preço Unitário</div>
            <div class="price-main">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
            
            @if($product->hasWholesalePrice())
                <div class="price-wholesale-info">
                    <i class="fas fa-tag"></i>
                    <div>
                        Leve <strong>{{ $product->wholesale_min_qty }}+</strong> por 
                        <strong>R$ {{ number_format($product->wholesale_price, 2, ',', '.') }}</strong>
                    </div>
                </div>
            @endif
        </div>

        @if($product->catalog_description ?? $product->description)
            <div class="product-description">
                {!! nl2br(e($product->catalog_description ?? $product->description)) !!}
            </div>
        @endif

        {{-- Technical Details Grid --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 32px;">
            @if($product->tecido)
                <div style="padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px;">Tecido</div>
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $product->tecido->name }}</div>
                </div>
            @endif
            @if($product->modelo)
                <div style="padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px;">Modelo</div>
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $product->modelo->name }}</div>
                </div>
            @endif
            @if($product->personalizacao)
                <div style="padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px;">Personalização</div>
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $product->personalizacao->name }}</div>
                </div>
            @endif
            @if($product->cutType)
                <div style="padding: 12px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                    <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px;">Corte</div>
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $product->cutType->name }}</div>
                </div>
            @endif
        </div>

        {{-- Wholesale Grid Implementation --}}
        @if(count($stockGrid) > 0)
            <div class="quick-variant-card">
                <div class="option-title" style="margin-bottom: 10px;">
                    Selecione Cor e Tamanho
                    <span class="option-selected" x-show="selectedSize && selectedColor">
                        <span x-show="variantStock > 0" x-text="variantStock + ' disponiveis'"></span>
                        <span x-show="variantStock <= 0">Sem estoque</span>
                    </span>
                </div>

                <div class="quick-variant-grid">
                    <div>
                        <div class="quick-variant-label">Tamanho</div>
                        <select class="quick-variant-select"
                                x-model="selectedSize"
                                @change="checkSpecificStock()">
                            <option value="">Selecione um tamanho</option>
                            @foreach($stockSizes as $size => $qty)
                                <option value="{{ $size }}" :disabled="getSizeAvailability('{{ $size }}') <= 0">{{ $size }} ({{ $qty }} un.)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <div class="quick-variant-label">Cor</div>
                        <select class="quick-variant-select"
                                x-model="selectedColor"
                                @change="
                                    const opt = $event.target.options[$event.target.selectedIndex];
                                    selectedColorHex = opt ? (opt.dataset.hex || '') : '';
                                    checkSpecificStock();
                                ">
                            <option value="">Selecione uma cor</option>
                            @foreach($stockColors as $color)
                                <option value="{{ $color['name'] }}" data-hex="{{ $color['hex'] }}" :disabled="getColorAvailability('{{ $color['name'] }}') <= 0">
                                    {{ $color['name'] }} ({{ $color['total_qty'] }} un.)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="quick-variant-footer">
                    <div class="quick-variant-meta">
                        <span class="quick-color-dot" x-show="selectedColor" :style="'background:' + (selectedColorHex || '#cbd5e1')"></span>
                        <span x-text="selectedColor || 'Selecione uma cor para continuar'"></span>
                    </div>

                    <div class="qty-input-group">
                        <button type="button" class="qty-btn" @click="qty = Math.max(1, qty - 1)"><i class="fas fa-minus"></i></button>
                        <div class="qty-display" x-text="qty"></div>
                        <button type="button" class="qty-btn" @click="qty = Math.min(variantStock || 999, qty + 1)"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>

            <div class="option-title" style="margin-bottom: 8px;">
                Grade de Quantidades
                <span class="option-selected" x-show="totalBulkQty > 0" x-text="totalBulkQty + ' un. selecionadas'"></span>
            </div>
            
            {{-- Desktop Grid --}}
            <div class="wholesale-grid-container animate-fade-in-up">
                <table class="wholesale-table">
                    <thead>
                        <tr>
                            <th>Tamanho \ Cor</th>
                            @foreach($stockColors as $color)
                                <th>
                                    <div style="display:flex; flex-direction:column; align-items:center; gap:6px;">
                                        <div style="width:14px; height:14px; border-radius:50%; background:{{ $color['hex'] }}; border:1px solid rgba(0,0,0,0.1);"></div>
                                        <span style="font-size:10px;">{{ $color['name'] }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockSizes as $size => $totalSizeQty)
                            <tr>
                                <td class="size-col" style="font-weight:900;">{{ $size }}</td>
                                @foreach($stockColors as $color)
                                    <td>
                                        @php $stockQty = $stockGrid[$size][$color['name']] ?? 0; @endphp
                                        @if($stockQty > 0)
                                            <div style="display:flex; flex-direction:column; align-items:center;">
                                                <input type="number" min="0" max="{{ $stockQty }}" 
                                                    class="wholesale-qty-input" 
                                                    x-model.number="bulkItems['{{ $size }}_{{ $color['name'] }}']"
                                                    @input="if($event.target.value < 0) $event.target.value = 0; if($event.target.value > {{ $stockQty }}) $event.target.value = {{ $stockQty }};"
                                                    placeholder="0">
                                                <span class="wholesale-stock-info">{{ $stockQty }} disponíveis</span>
                                            </div>
                                        @else
                                            <span style="color:#cbd5e1; font-size:10px; font-weight:700;">Esgotado</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Grid (Cards) --}}
            <div class="wholesale-mobile-grid">
                @foreach($stockSizes as $size => $totalSizeQty)
                    <div class="wholesale-card">
                        <div class="wholesale-card-header">
                            <span class="wholesale-card-size">Tamanho {{ $size }}</span>
                            <span style="font-size:11px; color:#94a3b8;">Estoque total: {{ $totalSizeQty }}</span>
                        </div>
                        <div class="wholesale-variants">
                            @foreach($stockColors as $color)
                                @php $stockQty = $stockGrid[$size][$color['name']] ?? 0; @endphp
                                <div class="wholesale-variant-row">
                                    <div class="wholesale-variant-info">
                                        <div class="variant-color-dot" style="background: {{ $color['hex'] }};"></div>
                                        <span class="variant-color-name">{{ $color['name'] }}</span>
                                        <span style="font-size:10px; color:#94a3b8; margin-left:4px;">({{ $stockQty }} un.)</span>
                                    </div>
                                    @if($stockQty > 0)
                                        <input type="number" min="0" max="{{ $stockQty }}" 
                                               class="wholesale-qty-input" 
                                               x-model.number="bulkItems['{{ $size }}_{{ $color['name'] }}']"
                                               @input="if($event.target.value < 0) $event.target.value = 0; if($event.target.value > {{ $stockQty }}) $event.target.value = {{ $stockQty }};"
                                               placeholder="0">
                                    @else
                                        <span style="font-size:11px; color:#ef4444; font-weight:700;">Esgotado</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Original Selectors fallback --}}
                <div class="option-title">
                    Tamanho <span class="option-selected" x-text="selectedSize"></span>
                </div>
                <div class="size-grid">
                    @foreach($stockSizes as $size => $qty)
                        <button type="button" class="size-btn" 
                                :class="{ 'active': selectedSize === '{{ $size }}' }"
                                @click="selectedSize = '{{ $size }}'; checkSpecificStock()">
                            {{ $size }}
                        </button>
                    @endforeach
                </div>

                <div class="option-title">
                    Cor <span class="option-selected" x-text="selectedColor"></span>
                </div>
                
                @php 
                    $swatchLimit = 8;
                    $swatchColors = array_slice($stockColors, 0, $swatchLimit);
                    $dropdownColors = array_slice($stockColors, $swatchLimit);
                @endphp

                <div class="color-grid" style="margin-bottom: 12px;">
                    @foreach($swatchColors as $color)
                        <div class="color-option" 
                             :class="{ 'active': selectedColor === '{{ $color['name'] }}' }"
                             @click="selectedColor = '{{ $color['name'] }}'; selectedColorHex = '{{ $color['hex'] }}'; checkSpecificStock()"
                             style="background-color: {{ $color['hex'] }};"
                             title="{{ $color['name'] }}">
                        </div>
                    @endforeach
                </div>

                @if(count($dropdownColors) > 0)
                    <div style="margin-bottom: 24px;">
                        <select class="form-select" 
                                style="width:100%; height:48px; border-radius:12px; border:2px solid #f1f5f9; padding:0 12px; font-weight:600; color:#475569;"
                                x-model="selectedColor"
                                @change="
                                    const opt = $event.target.options[$event.target.selectedIndex];
                                    selectedColorHex = opt.dataset.hex;
                                    checkSpecificStock();
                                ">
                            <option value="">Outras cores...</option>
                            @foreach($dropdownColors as $color)
                                <option value="{{ $color['name'] }}" data-hex="{{ $color['hex'] }}">{{ $color['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding: 16px; background: #f8fafc; border-radius: 16px;">
                    <div>
                        <div class="option-title" style="margin-bottom: 2px;">Quantidade</div>
                        <div style="font-size: 11px; font-weight: 700; color: #94a3b8;" x-show="selectedSize && selectedColor">
                            Disponível: <span style="color:var(--primary);" x-text="variantStock + ' un.'"></span>
                        </div>
                    </div>
                    <div class="qty-input-group">
                        <button type="button" class="qty-btn" @click="qty = Math.max(1, qty - 1)"><i class="fas fa-minus"></i></button>
                        <div class="qty-display" x-text="qty"></div>
                        <button type="button" class="qty-btn" @click="qty = Math.min(variantStock || 999, qty + 1)"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
        @endif

            <div style="background: rgba(255,255,255,0.7); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); padding: 20px; border-radius: 24px; border: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 40px rgba(0,0,0,0.03); margin-top: 12px;">
                <div>
                    <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px; letter-spacing: 0.5px;">Subtotal</div>
                    <div style="font-size: 26px; font-weight: 900; color: #0f172a; line-height: 1;" x-text="formatMoney(subtotal)"></div>
                </div>
                
                @if($totalStock > 0 || (!$product->track_stock && count($stockSizes) === 0))
                    <button class="add-btn" 
                            style="height: 56px; padding: 0 32px; font-size: 15px; margin: 0;"
                            @click="
                                if (totalBulkQty > 0) {
                                    let items = [];
                                    for (let key in bulkItems) {
                                        if (bulkItems[key] > 0) {
                                            let [s, c] = key.split('_');
                                            items.push({ size: s, color: c, quantity: bulkItems[key] });
                                        }
                                    }
                                    if (items.length === 0) { alert('Escolha as quantidades na grade'); return; }
                                    addToCart({{ $product->id }}, null, null, null, items);
                                } else {
                                    if ({{ count($stockSizes) > 0 ? 'true' : 'false' }} && !selectedSize) { alert('Por favor, selecione um tamanho'); return; } 
                                    if ({{ count($stockColors) > 0 ? 'true' : 'false' }} && !selectedColor) { alert('Por favor, selecione uma cor'); return; }
                                    await addToCart({{ $product->id }}, selectedSize || null, selectedColor || null, qty);
                                    selectedSize = ''; selectedColor = ''; qty = 1; variantStock = 0;
                                }
                            ">
                        <i class="fas fa-shopping-bag"></i> Adicionar
                    </button>
                @else
                    <button class="add-btn" disabled style="height: 56px; padding: 0 24px;">
                        <i class="fas fa-ban"></i> Esgotado
                    </button>
                @endif
            </div>

            @if($tenant->phone)
                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $tenant->phone) }}?text={{ urlencode('Olá! Tenho interesse no produto: ' . $product->title) }}" 
                   target="_blank" class="contact-seller-btn" 
                   style="background: transparent; color: #22c55e; border: 2px solid #22c55e; height: 56px; border-radius: 20px; font-size: 15px; font-weight: 800;">
                    <i class="fab fa-whatsapp" style="font-size: 20px;"></i> Conversar no WhatsApp
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Sticky Mobile CTA --}}
<div class="mobile-sticky-cta" :class="{ 'visible': effectiveQty > 0 }" @scroll.window="if(window.scrollY > 200) {}">
    <div class="sticky-total-price">
        <span class="label">Subtotal</span>
        <span class="val" x-text="formatMoney(subtotal)"></span>
    </div>
    <button class="sticky-add-btn" 
            @click="
                if (totalBulkQty > 0) {
                    let items = [];
                    for (let key in bulkItems) {
                        if (bulkItems[key] > 0) {
                            let [s, c] = key.split('_');
                            items.push({ size: s, color: c, quantity: bulkItems[key] });
                        }
                    }
                    if (items.length === 0) { alert('Escolha as quantidades'); return; }
                    addToCart({{ $product->id }}, null, null, null, items);
                } else {
                    if ({{ count($stockSizes) > 0 ? 'true' : 'false' }} && !selectedSize) { alert('Selecione um tamanho'); return; } 
                    if ({{ count($stockColors) > 0 ? 'true' : 'false' }} && !selectedColor) { alert('Selecione uma cor'); return; }
                    await addToCart({{ $product->id }}, selectedSize || null, selectedColor || null, qty);
                    selectedSize = ''; selectedColor = ''; qty = 1; variantStock = 0;
                }
                // toggleCart();
            ">
        <i class="fas fa-shopping-bag"></i> 
        <span x-text="totalBulkQty > 0 ? 'Adicionar ' + totalBulkQty : 'Adicionar'"></span>
    </button>
</div>

@if($relatedProducts->count() > 0)
    <div class="related-section">
        <h2 class="related-title">Produtos Relacionados</h2>
        <div class="related-scroll">
            @foreach($relatedProducts as $related)
                <a href="{{ route('catalog.product', ['storeCode' => $storeCode, 'product' => $related->id]) }}" 
                   class="product-card related-card" style="text-decoration:none;color:inherit;">
                    <div class="product-card-image">
                        @php $relImg = $related->primary_image_url; @endphp
                        @if($relImg)
                            <img src="{{ $relImg }}" alt="{{ $related->title }}" loading="lazy">
                        @else
                            <div class="no-image"><i class="fas fa-image"></i></div>
                        @endif
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-title">{{ $related->title }}</div>
                        <span class="product-price-retail">R$ {{ number_format($related->price, 2, ',', '.') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

@endsection
