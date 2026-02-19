@extends('catalog.layouts.catalog')

@section('title', 'Pedido Confirmado - ' . $tenant->name)

@section('extra-styles')
<style>
    .confirmation-container {
        max-width: 600px;
        margin: 40px auto;
        text-align: center;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 20px;
        box-shadow: 0 8px 24px rgba(16,185,129,0.3);
        animation: scaleIn 0.5s ease-out;
    }

    @keyframes scaleIn {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .confirmation-title {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .confirmation-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 24px;
    }

    .order-code-box {
        background: #f0f9ff;
        border: 2px solid #bae6fd;
        border-radius: 16px;
        padding: 16px 24px;
        display: inline-block;
        margin-bottom: 24px;
    }

    .order-code-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }

    .order-code-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: 2px;
    }

    .confirmation-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        text-align: left;
        margin-bottom: 20px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: #64748b; }
    .detail-value { font-weight: 600; color: #1e293b; }

    .items-list {
        margin-top: 16px;
    }
    .items-list-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .order-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 13px;
    }
    .order-item-name {
        color: #475569;
    }
    .order-item-total {
        font-weight: 600;
        color: #1e293b;
    }

    .total-final {
        display: flex;
        justify-content: space-between;
        padding-top: 12px;
        margin-top: 12px;
        border-top: 2px solid #e2e8f0;
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
    }
    .status-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }
    .status-paid {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }
    .action-buttons a, .action-buttons button {
        flex: 1;
        padding: 12px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
        cursor: pointer;
    }

    /* PIX Styles */
    .pix-block {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .pix-qr {
        width: 180px;
        height: 180px;
        margin: 0 auto 16px;
        background: white;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .pix-qr img { width: 100%; height: 100%; }
    .pix-copy-box {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
    }
    .pix-input {
        flex: 1;
        border: none;
        font-size: 12px;
        color: #475569;
        font-family: monospace;
        background: transparent;
        outline: none;
    }
    .pix-copy-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }
</style>
@endsection

@section('content')

<div class="confirmation-container animate-in">
    <div class="success-icon">
        <i class="fas fa-check"></i>
    </div>

    <h1 class="confirmation-title">Pedido Enviado!</h1>
    <p class="confirmation-subtitle">Seu pedido foi recebido e está aguardando aprovação da loja.</p>

    <div class="order-code-box">
        <div class="order-code-label">Código do Pedido</div>
        <div class="order-code-value">{{ $catalogOrder->order_code }}</div>
    </div>

    @if($catalogOrder->payment_method === 'pix' && !empty($catalogOrder->payment_data['qr_code_base64']))
        <div class="pix-block animate-in" style="animation-delay: 0.2s">
            <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Pague com PIX</h2>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">Escaneie o QR Code ou copie a chave abaixo</p>

            <div class="pix-qr">
                <img src="data:image/png;base64,{{ $catalogOrder->payment_data['qr_code_base64'] }}" alt="QR Code PIX">
            </div>

            <div class="order-code-label">PIX Copia e Cola</div>
            <div class="pix-copy-box">
                <input type="text" value="{{ $catalogOrder->payment_data['qr_code'] }}" class="pix-input" readonly id="pix-payload">
                <button class="pix-copy-btn" onclick="copyPix()">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>

            @if(!empty($catalogOrder->payment_data['ticket_url']) && $catalogOrder->payment_data['ticket_url'] !== '#')
                <a href="{{ $catalogOrder->payment_data['ticket_url'] }}" target="_blank" 
                   style="display: inline-block; margin-top: 16px; font-size: 12px; color: var(--primary); font-weight: 600; text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> Ver comprovante no Mercado Pago
                </a>
            @endif
        </div>
    @elseif($catalogOrder->payment_method === 'pix')
        <div class="pix-block animate-in" style="animation-delay: 0.2s">
            @if($catalogOrder->payment_status === 'failed')
                <h2 style="font-size: 18px; font-weight: 800; color: #b91c1c; margin-bottom: 4px;">Pagamento não concluído</h2>
                <p style="font-size: 13px; color: #7f1d1d;">
                    O pagamento foi marcado como falho. Entre em contato com a loja para refazer o pagamento.
                </p>
            @else
                <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Pagamento pendente</h2>
                <p style="font-size: 13px; color: #64748b;">
                    A loja confirmará seu pagamento manualmente e seguirá com o pedido após a confirmação.
                </p>
            @endif
        </div>
    @endif

    <div class="confirmation-card">
        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="status-badge status-pending">
                <i class="fas fa-clock"></i> {{ $catalogOrder->status_label }}
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Cliente</span>
            <span class="detail-value">{{ $catalogOrder->customer_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Telefone</span>
            <span class="detail-value">{{ $catalogOrder->customer_phone }}</span>
        </div>
        @if($catalogOrder->customer_email)
        <div class="detail-row">
            <span class="detail-label">E-mail</span>
            <span class="detail-value">{{ $catalogOrder->customer_email }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Modo</span>
            <span class="detail-value" style="text-transform: capitalize;">{{ $catalogOrder->pricing_mode }}</span>
        </div>

        <div class="items-list">
            <div class="items-list-title">Itens do Pedido</div>
            @foreach($catalogOrder->items as $item)
                <div class="order-item-row">
                    <span class="order-item-name">
                        {{ $item['quantity'] }}x {{ $item['title'] }}
                        @if(!empty($item['size']) || !empty($item['color']))
                            ({{ implode(', ', array_filter([$item['size'] ?? null, $item['color'] ?? null])) }})
                        @endif
                    </span>
                    <span class="order-item-total">R$ {{ number_format($item['total'], 2, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="total-final">
            <span>Total</span>
            <span>{{ $catalogOrder->formatted_total }}</span>
        </div>
    </div>

    <div class="action-buttons">
        <a href="{{ route('catalog.show', $storeCode) }}" 
           style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
            <i class="fas fa-store"></i> Voltar
        </a>

        <a href="{{ route('catalog.confirmation.pdf', ['storeCode' => $storeCode, 'orderCode' => $catalogOrder->order_code]) }}" 
           target="_blank"
           style="background: #4f46e5; color: white; border: none;">
            <i class="fas fa-file-pdf"></i> Baixar PDF
        </a>

        @if($tenant->phone)
            @php
                $waMessage = "Olá! Fiz o pedido *{$catalogOrder->order_code}* pelo catálogo.\n\n*Itens:*";
                foreach($catalogOrder->items as $item) {
                    $waMessage .= "\n- {$item['quantity']}x {$item['title']}";
                    if (!empty($item['size']) || !empty($item['color'])) {
                        $waMessage .= " (" . implode(', ', array_filter([$item['size'] ?? null, $item['color'] ?? null])) . ")";
                    }
                }
                $waMessage .= "\n\n*Total:* {$catalogOrder->formatted_total}";
            @endphp
            <a href="https://wa.me/55{{ preg_replace('/\D/', '', $tenant->phone) }}?text={{ urlencode($waMessage) }}" 
               target="_blank"
               style="background: #25d366; color: white; border: none;">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        @endif
    </div>
</div>

@endsection

@section('extra-scripts')
<script>
    function copyPix() {
        const input = document.getElementById('pix-payload');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        const btn = document.querySelector('.pix-copy-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = 'var(--primary)';
        }, 2000);
    }
</script>
@endsection
