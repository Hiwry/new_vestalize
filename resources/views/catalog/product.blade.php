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
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .option-selected {
        font-weight: 700;
        color: #475569;
        font-size: 14px;
    }

    /* Size Pills - Riachuelo Style */
    .size-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 24px;
    }
    .size-btn {
        position: relative;
        min-width: 48px;
        height: auto;
        padding: 10px 16px 6px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        background: white;
        color: #333;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
    }
    .size-btn:hover {
        border-color: #333;
    }
    .size-btn.active {
        border-color: #333;
        border-width: 2px;
        background: white;
        color: #333;
        font-weight: 700;
    }
    .size-btn.disabled-size {
        opacity: 0.35;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .size-btn .qty-badge {
        font-size: 10px;
        color: #999;
        font-weight: 500;
        line-height: 1;
    }
    .size-btn.active .qty-badge {
        color: #666;
    }

    /* Color Swatches - Riachuelo Style */
    .color-section {
        margin-bottom: 24px;
    }
    .color-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 0;
    }
    .color-option {
        position: relative;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);
    }
    .color-option.active {
        box-shadow: 0 0 0 2px white, 0 0 0 3px #333;
        transform: scale(1.05);
    }
    .color-option.disabled-color {
        opacity: 0.25;
        cursor: not-allowed;
    }
    .color-option .stock-hint {
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 9px;
        font-weight: 600;
        white-space: nowrap;
        color: #999;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .color-option:hover .stock-hint { opacity: 1; }

    .variant-availability {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: #f5f5f5;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #666;
        font-weight: 500;
    }
    .variant-availability .avail-count {
        font-weight: 700;
        color: #333;
    }
    .variant-availability.out-of-stock {
        background: #fff5f5;
        color: #d32f2f;
    }

    /* Premium Aesthetic Refinements */
    .product-detail {
        opacity: 0;
        animation: fadeInUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    .gallery-container, .price-section {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    .action-section {
        margin-top: 40px;
        padding: 32px 0 100px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Sticky Bottom Bar */
    .mobile-sticky-cta {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 12px 16px;
        padding-bottom: max(12px, env(safe-area-inset-bottom));
        border-top: 1px solid #e0e0e0;
        z-index: 1000;
        display: none;
        align-items: center;
        gap: 12px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    .mobile-sticky-cta.visible {
        transform: translateY(0);
    }
    @media (max-width: 768px) {
        .mobile-sticky-cta { display: flex; }
    }

    .sticky-total-price {
        display: flex;
        flex-direction: column;
        min-width: 80px;
    }
    .sticky-total-price .label {
        font-size: 10px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .sticky-total-price .val {
        font-size: 18px;
        font-weight: 800;
        color: #1a1a1a;
    }

    .sticky-add-btn {
        flex: 1;
        height: 48px;
        border-radius: 8px;
        background: #1b7b3c;
        color: white;
        font-weight: 700;
        font-size: 15px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .sticky-add-btn:hover {
        background: #15632f;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Quantity section */
    .qty-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding: 14px 16px;
        background: #fafafa;
        border-radius: 10px;
        border: 1px solid #f0f0f0;
    }
    .qty-input-group {
        display: flex;
        align-items: center;
        gap: 0;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }
    .qty-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: white;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s;
        border-radius: 0;
        font-size: 16px;
    }
    .qty-btn:hover {
        background: #f5f5f5;
    }
    .qty-display {
        width: 48px;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        color: #333;
        border-left: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        line-height: 40px;
    }

    /* Add to Cart Button - Riachuelo Style */
    .add-btn {
        width: 100%;
        height: 52px;
        border-radius: 8px;
        background: #1b7b3c;
        color: white;
        font-size: 16px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .add-btn:hover:not(:disabled) {
        background: #15632f;
    }
    .add-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Mobile Optimizations */
    @media (max-width: 768px) {
        .product-detail {
            padding: 0 0 120px;
            gap: 16px;
        }
        .product-detail-title { font-size: 22px; margin-bottom: 8px; }
        .price-main { font-size: 28px; }
        .price-section {
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .gallery-container {
            border-radius: 0;
            margin: 0 -16px;
        }
        .product-info {
            padding: 0 16px;
        }
        .size-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .size-btn {
            min-width: unset;
            padding: 10px 8px 6px;
            font-size: 14px;
        }
        .color-option {
            width: 32px;
            height: 32px;
        }
        .add-btn {
            display: none;
        }
    }

    /* Divider */
    .section-divider {
        height: 1px;
        background: #f0f0f0;
        margin: 4px 0 20px;
    }

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
    }'
    x-init="$watch('subtotal', val => { const el = document.getElementById('sticky-subtotal'); if (el) el.textContent = formatMoney(val); })">
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

        {{-- Color Section --}}
        @if(count($stockColors) > 0)
            <div class="color-section">
                <div class="option-title">
                    Cor <span class="option-selected" x-text="selectedColor"></span>
                </div>
                <div class="color-grid">
                    @foreach($stockColors as $color)
                        <div class="color-option"
                             :class="{ 'active': selectedColor === '{{ $color['name'] }}', 'disabled-color': getColorAvailability('{{ $color['name'] }}') <= 0 }"
                             @click="if(getColorAvailability('{{ $color['name'] }}') > 0) { selectedColor = '{{ $color['name'] }}'; selectedColorHex = '{{ $color['hex'] }}'; checkSpecificStock(); }"
                             style="background-color: {{ $color['hex'] }};"
                             title="{{ $color['name'] }}">
                            <span class="stock-hint" x-text="getColorAvailability('{{ $color['name'] }}') + ' un.'"></span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="section-divider"></div>

        {{-- Size Section --}}
        @if(count($stockSizes) > 0)
            <div class="option-title">
                Tamanho <span class="option-selected" x-text="selectedSize"></span>
            </div>
            <div class="size-grid">
                @foreach($stockSizes as $size => $qty)
                    <button type="button" class="size-btn"
                            :class="{ 'active': selectedSize === '{{ $size }}', 'disabled-size': getSizeAvailability('{{ $size }}') <= 0 }"
                            @click="if(getSizeAvailability('{{ $size }}') > 0) { selectedSize = '{{ $size }}'; checkSpecificStock(); }">
                        {{ $size }}
                        <span class="qty-badge" x-text="getSizeAvailability('{{ $size }}') + ' un.'"></span>
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Availability indicator --}}
        <template x-if="selectedSize && selectedColor">
            <div class="variant-availability" :class="{ 'out-of-stock': variantStock <= 0 }">
                <template x-if="variantStock > 0">
                    <span><span class="avail-count" x-text="variantStock"></span> disponíveis</span>
                </template>
                <template x-if="variantStock <= 0">
                    <span>Sem estoque para esta combinação</span>
                </template>
            </div>
        </template>

        {{-- Quantity --}}
        <div class="qty-section">
            <div>
                <div style="font-size: 14px; font-weight: 700; color: #333;">Quantidade</div>
            </div>
            <div class="qty-input-group">
                <button type="button" class="qty-btn" @click="qty = Math.max(1, qty - 1)"><i class="fas fa-minus"></i></button>
                <div class="qty-display" x-text="qty"></div>
                <button type="button" class="qty-btn" @click="qty = Math.min(variantStock || 999, qty + 1)"><i class="fas fa-plus"></i></button>
            </div>
        </div>

            @if($totalStock > 0 || (!$product->track_stock && count($stockSizes) === 0))
                <button class="add-btn"
                        @click="
                            if ({{ count($stockSizes) > 0 ? 'true' : 'false' }} && !selectedSize) { alert('Por favor, selecione um tamanho'); return; }
                            if ({{ count($stockColors) > 0 ? 'true' : 'false' }} && !selectedColor) { alert('Por favor, selecione uma cor'); return; }
                            await addToCart({{ $product->id }}, selectedSize || null, selectedColor || null, qty);
                            selectedSize = ''; selectedColor = ''; qty = 1; variantStock = 0;
                        ">
                    <i class="fas fa-shopping-bag"></i> Adicionar à sacola
                </button>
            @else
                <button class="add-btn" disabled>
                    <i class="fas fa-ban"></i> Esgotado
                </button>
            @endif

            <div style="text-align: center; margin-top: 8px;">
                <span style="font-size: 12px; color: #999;">Subtotal: </span>
                <span style="font-size: 16px; font-weight: 800; color: #333;" x-text="formatMoney(subtotal)"></span>
            </div>

            @if($tenant->phone)
                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $tenant->phone) }}?text={{ urlencode('Olá! Tenho interesse no produto: ' . $product->title) }}" 
                   target="_blank" class="contact-seller-btn" 
                   style="background: transparent; color: #22c55e; border: 2px solid #22c55e; height: 56px; border-radius: 20px; font-size: 15px; font-weight: 800;">
                    <i class="fab fa-whatsapp" style="font-size: 20px;"></i> Conversar no WhatsApp
                </a>
            @endif
        </div>

        {{-- Sticky Mobile CTA --}}
        <div class="mobile-sticky-cta visible">
            <div class="sticky-total-price">
                <span class="label">Subtotal</span>
                <span class="val" id="sticky-subtotal">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
            </div>
            <button class="sticky-add-btn"
                    onclick="
                        var sz = document.querySelector('.product-info')._x_dataStack?.[0]?.selectedSize;
                        var sc = document.querySelector('.product-info')._x_dataStack?.[0]?.selectedColor;
                        var q = document.querySelector('.product-info')._x_dataStack?.[0]?.qty ?? 1;
                        @if(count($stockSizes) > 0) if (!sz) { alert('Selecione um tamanho'); return; } @endif
                        @if(count($stockColors) > 0) if (!sc) { alert('Selecione uma cor'); return; } @endif
                        addToCart({{ $product->id }}, sz || null, sc || null, q);
                    ">
                <i class="fas fa-shopping-bag"></i>
                <span>Adicionar à sacola</span>
            </button>
        </div>
    </div>
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
