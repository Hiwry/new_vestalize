@extends('catalog.layouts.catalog')

@section('title', 'Checkout - ' . $tenant->name)

@section('extra-styles')
<style>
    .checkout-layout {
        padding: 20px 0;
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    @media (min-width: 768px) {
        .checkout-layout { grid-template-columns: 1fr 380px; }
    }

    .checkout-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
    }

    .checkout-title {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .checkout-subtitle {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        color: var(--primary);
    }

    /* Summary Panel */
    .summary-item {
        display: flex;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .summary-item:last-child { border-bottom: none; }

    .summary-item-img {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #f8fafc;
        overflow: hidden;
        flex-shrink: 0;
    }
    .summary-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .summary-item-info {
        flex: 1;
        min-width: 0;
    }

    .summary-item-title {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }
    .summary-item-variant {
        font-size: 11px;
        color: #94a3b8;
    }
    .summary-item-price {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
    }

    .summary-totals {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 2px solid #e2e8f0;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin-bottom: 6px;
    }
    .summary-row.total {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        margin-top: 8px;
    }

    .wholesale-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
    }

    .submit-btn {
        width: 100%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: 14px;
        padding: 16px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s;
        margin-top: 16px;
    }
    .submit-btn:hover {
        filter: brightness(1.1);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--primary);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .back-link:hover { text-decoration: underline; }

    .form-required { color: #ef4444; }
</style>
@endsection

@section('content')

<a href="{{ route('catalog.show', $storeCode) }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Continuar comprando
</a>

<form action="{{ route('catalog.processCheckout', $storeCode) }}" method="POST" id="checkout-form">
    @csrf
    <div class="checkout-layout">

        <!-- Customer Info -->
        <div>
            <div class="checkout-card">
                <h1 class="checkout-title">Finalizar Pedido</h1>
                <p class="checkout-subtitle">Preencha seus dados para concluir o pedido</p>

                <div class="section-title"><i class="fas fa-user"></i> Seus Dados</div>

                <div class="form-group">
                    <label class="form-label">Nome Completo <span class="form-required">*</span></label>
                    <input type="text" name="customer_name" class="form-input" placeholder="Seu nome completo" required value="{{ old('customer_name') }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label class="form-label">Telefone / WhatsApp <span class="form-required">*</span></label>
                        <input type="text" name="customer_phone" class="form-input" placeholder="(00) 00000-0000" required value="{{ old('customer_phone') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CPF</label>
                        <input type="text" name="customer_cpf" class="form-input" placeholder="000.000.000-00" value="{{ old('customer_cpf') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="customer_email" class="form-input" placeholder="seu@email.com" value="{{ old('customer_email') }}">
                </div>

                <div class="section-title" style="margin-top: 20px;"><i class="fas fa-truck"></i> Entrega</div>

                <div class="form-group">
                    <label class="form-label">Endereço de Entrega</label>
                    <textarea name="delivery_address" class="form-input" placeholder="Rua, número, bairro, cidade, CEP...">{{ old('delivery_address') }}</textarea>
                </div>

                <div class="section-title" style="margin-top: 20px;"><i class="fas fa-comment-dots"></i> Observações</div>

                <div class="form-group">
                    <label class="form-label">Observações do Pedido</label>
                    <textarea name="notes" class="form-input" placeholder="Alguma observação especial sobre o pedido?">{{ old('notes') }}</textarea>
                </div>

                <div class="section-title" style="margin-top: 24px;"><i class="fas fa-credit-card"></i> Forma de Pagamento</div>
                
                <div class="payment-methods" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                    <label class="payment-option" style="cursor: pointer; border: 2px solid #e2e8f0; border-radius: 16px; padding: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; transition: all 0.2s;">
                        <input type="radio" name="payment_method" value="pix" checked style="display: none;">
                        <div class="payment-icon" style="font-size: 24px; color: #10b981;"><i class="fab fa-pix"></i></div>
                        <div class="payment-label" style="font-size: 13px; font-weight: 700; color: #1e293b;">PIX</div>
                        <div class="payment-desc" style="font-size: 11px; color: #94a3b8; text-align: center;">Pagamento via QR Code</div>
                    </label>
                    <label class="payment-option" style="cursor: pointer; border: 2px solid #e2e8f0; border-radius: 16px; padding: 16px; display: flex; flex-direction: column; align-items: center; gap: 8px; transition: all 0.2s;">
                        <input type="radio" name="payment_method" value="delivery" style="display: none;">
                        <div class="payment-icon" style="font-size: 24px; color: #1e293b;"><i class="fas fa-truck"></i></div>
                        <div class="payment-label" style="font-size: 13px; font-weight: 700; color: #1e293b;">Na Entrega</div>
                        <div class="payment-desc" style="font-size: 11px; color: #94a3b8; text-align: center;">Pague ao receber</div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div>
            <div class="checkout-card" style="position: sticky; top: 80px;">
                <div class="section-title"><i class="fas fa-shopping-bag"></i> Resumo do Pedido</div>

                @foreach($cartSummary['items'] as $key => $item)
                    <div class="summary-item">
                        <div class="summary-item-img">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="">
                            @else
                                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#cbd5e1;"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div class="summary-item-info">
                            <div class="summary-item-title">{{ $item['title'] }}</div>
                            <div class="summary-item-variant">
                                {{ $item['quantity'] }}x R$ {{ number_format($item['effective_price'], 2, ',', '.') }}
                                @if($item['size'] || $item['color'])
                                    · {{ implode(' · ', array_filter([$item['size'], $item['color']])) }}
                                @endif
                            </div>
                        </div>
                        <div class="summary-item-price">
                            R$ {{ number_format($item['line_total'], 2, ',', '.') }}
                        </div>
                    </div>
                @endforeach

                <div class="summary-totals">
                    <div class="summary-row">
                        <span style="color: #64748b;">Subtotal ({{ $cartSummary['total_items'] }} itens)</span>
                        <span style="font-weight: 600;">R$ {{ number_format($cartSummary['subtotal'], 2, ',', '.') }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>R$ {{ number_format($cartSummary['total'], 2, ',', '.') }}</span>
                    </div>
                </div>

                @if($cartSummary['is_wholesale'])
                    <div class="wholesale-badge">
                        <i class="fas fa-tag"></i> Preço atacado aplicado
                    </div>
                @endif

                <button type="submit" class="submit-btn" id="submit-btn">
                    <i class="fas fa-check-circle"></i> Confirmar Pedido
                </button>

                <p style="text-align: center; font-size: 11px; color: #94a3b8; margin-top: 10px;">
                    <i class="fas fa-shield-alt"></i> Seu pedido ficará pendente até ser aprovado pela loja
                </p>
            </div>
        </div>

    </div>
</form>

@endsection

@section('extra-scripts')
<script>
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    });

    // Payment method selection styling
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(opt => {
        opt.addEventListener('click', function() {
            paymentOptions.forEach(o => {
                o.style.borderColor = '#e2e8f0';
                o.style.background = 'transparent';
                o.querySelector('.payment-icon').style.filter = 'grayscale(1)';
            });
            this.style.borderColor = 'var(--primary)';
            this.style.background = 'var(--primary-light)';
            const icon = this.querySelector('.payment-icon');
            icon.style.filter = 'none';
            this.querySelector('input').checked = true;
        });
    });

    // Initialize first one
    paymentOptions[0].click();

    // Phone mask
    document.querySelector('input[name="customer_phone"]').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 11) v = v.substring(0, 11);
        if (v.length > 6) {
            v = '(' + v.substring(0,2) + ') ' + v.substring(2,7) + '-' + v.substring(7);
        } else if (v.length > 2) {
            v = '(' + v.substring(0,2) + ') ' + v.substring(2);
        } else if (v.length > 0) {
            v = '(' + v;
        }
        e.target.value = v;
    });

    // CPF mask
    const cpfInput = document.querySelector('input[name="customer_cpf"]');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 9) {
                v = v.substring(0,3) + '.' + v.substring(3,6) + '.' + v.substring(6,9) + '-' + v.substring(9);
            } else if (v.length > 6) {
                v = v.substring(0,3) + '.' + v.substring(3,6) + '.' + v.substring(6);
            } else if (v.length > 3) {
                v = v.substring(0,3) + '.' + v.substring(3);
            }
            e.target.value = v;
        });
    }
</script>
@endsection
