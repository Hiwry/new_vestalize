@extends('catalog.layouts.catalog')

@section('title', $fabricPiece->display_name . ' - ' . $tenant->name)

@section('extra-styles')
<style>
    .fabric-piece-layout {
        max-width: 1100px;
        margin: 0 auto;
        padding: 28px 0 80px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    @media (min-width: 960px) {
        .fabric-piece-layout {
            grid-template-columns: 0.95fr 1.05fr;
            gap: 36px;
        }
    }
    .fabric-piece-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }
    .fabric-piece-hero {
        min-height: 360px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at top left, rgba(15, 118, 110, 0.18), transparent 38%),
            linear-gradient(135deg, #f8fafc, #e2e8f0);
        color: #0f172a;
        font-size: 88px;
    }
    .fabric-piece-body {
        padding: 28px;
    }
    .fabric-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }
    .fabric-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: #ecfeff;
        color: #0f766e;
    }
    .fabric-piece-title {
        font-size: 34px;
        line-height: 1.05;
        letter-spacing: -1px;
        color: #0f172a;
        font-weight: 900;
        margin-bottom: 10px;
    }
    .fabric-piece-subtitle {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 22px;
    }
    .fabric-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 22px;
    }
    .fabric-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 16px;
    }
    .fabric-info-box span {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-bottom: 6px;
        font-weight: 700;
    }
    .fabric-info-box strong {
        font-size: 22px;
        color: #0f172a;
        font-weight: 900;
    }
    .fabric-detail-list {
        display: grid;
        gap: 12px;
    }
    .fabric-detail-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }
    .fabric-detail-row:last-child {
        border-bottom: none;
    }
    .fabric-detail-row span {
        color: #64748b;
    }
    .fabric-detail-row strong {
        color: #0f172a;
        text-align: right;
    }
    .fabric-buy-box {
        padding: 24px;
        background: #0f172a;
        color: white;
        border-radius: 24px;
        position: sticky;
        top: 84px;
    }
    .fabric-buy-box small {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.62);
        margin-bottom: 6px;
        font-weight: 700;
    }
    .fabric-buy-price {
        font-size: 34px;
        font-weight: 900;
        letter-spacing: -1px;
        margin-bottom: 4px;
    }
    .fabric-buy-hint {
        color: rgba(255,255,255,0.72);
        font-size: 13px;
        margin-bottom: 22px;
    }
    .fabric-field {
        margin-bottom: 16px;
    }
    .fabric-field label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.72);
        margin-bottom: 8px;
    }
    .fabric-field input {
        width: 100%;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.06);
        color: white;
        padding: 14px 16px;
        border-radius: 16px;
        font-size: 16px;
        font-weight: 700;
    }
    .fabric-field input:focus {
        outline: none;
        border-color: #14b8a6;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2);
    }
    .fabric-total-preview {
        margin-top: 6px;
        color: rgba(255,255,255,0.72);
        font-size: 13px;
    }
    .fabric-add-btn {
        width: 100%;
        border: none;
        border-radius: 18px;
        padding: 16px;
        background: linear-gradient(135deg, #14b8a6, #0f766e);
        color: white;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.2s ease, filter 0.2s ease;
    }
    .fabric-add-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.06);
    }
    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="fabric-piece-layout">
    <div class="fabric-piece-card">
        <div class="fabric-piece-hero">
            <i class="fas fa-ruler-combined"></i>
        </div>
        <div class="fabric-piece-body">
            <div class="fabric-meta">
                <span class="fabric-pill">
                    <i class="fas fa-layer-group"></i>
                    {{ $fabricPiece->fabricType?->name ?? $fabricPiece->fabric?->name ?? 'Tecido' }}
                </span>
                <span class="fabric-pill" style="background:#eff6ff;color:#1d4ed8;">
                    <i class="fas fa-box-open"></i>
                    {{ number_format($fabricPiece->available_quantity, $fabricPiece->control_unit === 'metros' ? 2 : 3, ',', '.') }}
                    {{ $fabricPiece->control_unit === 'metros' ? 'm' : 'kg' }}
                </span>
            </div>

            <h1 class="fabric-piece-title">{{ $fabricPiece->display_name }}</h1>
            <p class="fabric-piece-subtitle">
                Peça disponível para venda no catálogo. O saldo é controlado em {{ strtolower($fabricPiece->control_unit_label) }}
                e será abatido conforme a quantidade comprada.
            </p>

            <div class="fabric-info-grid">
                <div class="fabric-info-box">
                    <span>Preço por {{ $fabricPiece->control_unit === 'metros' ? 'metro' : 'kg' }}</span>
                    <strong>R$ {{ number_format($fabricPiece->sale_price, 2, ',', '.') }}</strong>
                </div>
                <div class="fabric-info-box">
                    <span>Saldo Atual</span>
                    <strong>{{ number_format($fabricPiece->available_quantity, $fabricPiece->control_unit === 'metros' ? 2 : 3, ',', '.') }} {{ $fabricPiece->control_unit === 'metros' ? 'm' : 'kg' }}</strong>
                </div>
            </div>

            <div class="fabric-detail-list">
                <div class="fabric-detail-row">
                    <span>Cor</span>
                    <strong>{{ $fabricPiece->color?->name ?? 'Não informada' }}</strong>
                </div>
                <div class="fabric-detail-row">
                    <span>Fornecedor</span>
                    <strong>{{ $fabricPiece->supplier ?: 'Não informado' }}</strong>
                </div>
                <div class="fabric-detail-row">
                    <span>Nota / referência</span>
                    <strong>{{ $fabricPiece->invoice_number ?: ('Peça #' . $fabricPiece->id) }}</strong>
                </div>
                <div class="fabric-detail-row">
                    <span>Loja de origem</span>
                    <strong>{{ $fabricPiece->store?->name ?? 'Não informada' }}</strong>
                </div>
                <div class="fabric-detail-row">
                    <span>Status</span>
                    <strong>{{ $fabricPiece->status_label }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="fabric-buy-box">
            <small>Comprar tecido</small>
            <div class="fabric-buy-price">R$ {{ number_format($fabricPiece->sale_price, 2, ',', '.') }}</div>
            <div class="fabric-buy-hint">
                por {{ $fabricPiece->control_unit === 'metros' ? 'metro' : 'kg' }}.
                Saldo máximo: {{ number_format($fabricPiece->available_quantity, $fabricPiece->control_unit === 'metros' ? 2 : 3, ',', '.') }}
                {{ $fabricPiece->control_unit === 'metros' ? 'm' : 'kg' }}.
            </div>

            <div class="fabric-field">
                <label for="fabric-piece-quantity">Quantidade ({{ $fabricPiece->control_unit === 'metros' ? 'm' : 'kg' }})</label>
                <input
                    type="number"
                    id="fabric-piece-quantity"
                    min="{{ $fabricPiece->control_unit === 'metros' ? '0.01' : '0.001' }}"
                    step="{{ $fabricPiece->control_unit === 'metros' ? '0.01' : '0.001' }}"
                    max="{{ $fabricPiece->available_quantity }}"
                    value="{{ $fabricPiece->available_quantity < 1 ? $fabricPiece->available_quantity : ($fabricPiece->control_unit === 'metros' ? '1.00' : '1.000') }}"
                >
                <div class="fabric-total-preview">
                    Total estimado: <strong id="fabric-piece-line-total">R$ 0,00</strong>
                </div>
            </div>

            <button class="fabric-add-btn" onclick="addFabricPieceToCart()">
                <i class="fas fa-shopping-bag"></i> Adicionar ao carrinho
            </button>
        </div>

        @if($relatedPieces->count() > 0)
            <div style="margin-top: 24px;">
                <h2 style="font-size:18px;font-weight:800;color:#1e293b;margin-bottom:8px;">Outras peças disponíveis</h2>
                <p style="font-size:13px;color:#64748b;">Peças da mesma família para complementar o pedido.</p>

                <div class="related-grid">
                    @foreach($relatedPieces as $relatedPiece)
                        <a href="{{ route('catalog.fabric-piece', ['storeCode' => $storeCode, 'piece' => $relatedPiece->id]) }}"
                           class="product-card"
                           style="text-decoration:none;color:inherit;">
                            <div class="product-card-image">
                                <div class="no-image"><i class="fas fa-ruler-combined"></i></div>
                                <div class="product-badge" style="background:#0f766e;">
                                    {{ number_format($relatedPiece->available_quantity, $relatedPiece->control_unit === 'metros' ? 2 : 3, ',', '.') }}
                                    {{ $relatedPiece->control_unit === 'metros' ? 'm' : 'kg' }}
                                </div>
                            </div>
                            <div class="product-card-body">
                                <div class="product-card-category">{{ $relatedPiece->fabricType?->name ?? $relatedPiece->fabric?->name ?? 'Tecido' }}</div>
                                <div class="product-card-title">{{ $relatedPiece->display_name }}</div>
                                <div class="product-price-retail">R$ {{ number_format($relatedPiece->sale_price, 2, ',', '.') }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function updateFabricPieceTotal() {
        const input = document.getElementById('fabric-piece-quantity');
        const maxQuantity = {{ (float) $fabricPiece->available_quantity }};
        const unitPrice = {{ (float) $fabricPiece->sale_price }};
        const decimals = {{ $fabricPiece->control_unit === 'metros' ? 2 : 3 }};
        let quantity = parseFloat(input.value || 0);

        if (quantity > maxQuantity) {
            quantity = maxQuantity;
            input.value = maxQuantity.toFixed(decimals);
        }

        if (quantity < 0) {
            quantity = 0;
            input.value = '0';
        }

        document.getElementById('fabric-piece-line-total').textContent =
            'R$ ' + (quantity * unitPrice).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    async function addFabricPieceToCart() {
        const quantity = parseFloat(document.getElementById('fabric-piece-quantity').value || 0);

        if (!quantity || quantity <= 0) {
            showToast('Informe uma quantidade válida.', 'error');
            return;
        }

        await addToCart(null, null, null, quantity, null, 'fabric_piece', {
            fabric_piece_id: {{ $fabricPiece->id }}
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('fabric-piece-quantity');
        if (input) {
            input.addEventListener('input', updateFabricPieceTotal);
        }
        updateFabricPieceTotal();
    });
</script>
@endsection
