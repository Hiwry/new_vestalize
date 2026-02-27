<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Nota do Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        :root {
            --brand-primary: {{ $order->tenant?->primary_color ?? '#000' }};
            --brand-secondary: {{ $order->tenant?->secondary_color ?? '#333' }};
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #000;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--brand-primary);
        }
        .header h1 {
            font-size: 20px;
            color: var(--brand-primary);
            margin-bottom: 3px;
            font-weight: bold;
        }
        .header .subtitle {
            font-size: 12px;
            color: var(--brand-secondary);
        }
        .company-info {
            text-align: left;
            margin-bottom: 10px;
            padding: 6px 8px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
        }
        .company-info h2 {
            font-size: 14px;
            color: var(--brand-primary);
            margin-bottom: 2px;
            font-weight: bold;
        }
        .company-info p {
            font-size: 8px;
            color: #333;
            margin: 0;
            line-height: 1.2;
        }
        .section {
            margin-bottom: 12px;
            padding: 8px;
            border: 1px solid var(--brand-primary);
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: var(--brand-primary);
            margin-bottom: 8px;
            padding: 6px 10px;
            text-transform: uppercase;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 3px 10px 3px 0;
            width: 30%;
            font-size: 12px;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
            font-size: 12px;
        }
        .item-section {
            margin-bottom: 12px;
            padding: 8px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
        }
        .item-header {
            font-size: 13px;
            font-weight: bold;
            color: var(--brand-primary);
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }
        .application-item {
            background-color: white;
            border-left: 3px solid var(--brand-secondary);
            padding: 6px;
            margin-bottom: 6px;
            font-size: 9px;
        }
        .application-header {
            font-weight: bold;
            color: var(--brand-primary);
            margin-bottom: 2px;
        }
        .application-details {
            color: #333;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed var(--brand-secondary);
            font-size: 9px;
        }
        .total-section {
            background-color: var(--brand-primary);
            color: white;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
        }
        .total-section h3 {
            font-size: 14px;
            margin-bottom: 3px;
        }
        .total-section .amount {
            font-size: 20px;
            font-weight: bold;
        }
        .payment-info {
            background-color: #f0f0f0;
            border: 1px solid var(--brand-primary);
            padding: 8px;
            margin: 12px 0;
        }
        .payment-info h4 {
            color: var(--brand-primary);
            font-size: 11px;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .payment-details {
            font-size: 10px;
            color: #000;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #000;
            border-top: 1px solid var(--brand-primary);
            padding-top: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid var(--brand-secondary);
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>NOTA DO PEDIDO</h1>
        <div class="subtitle">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <!-- Informações da Empresa -->
    <div class="company-info" style="padding: 6px 8px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-row;">
                @php
                    $finalLogo = null;
                    if (isset($companySettings->logo_path) && $companySettings->logo_path && file_exists(public_path($companySettings->logo_path))) {
                        $finalLogo = public_path($companySettings->logo_path);
                    } else {
                        $finalLogo = public_path('vestalize.svg');
                    }
                @endphp

                @if($finalLogo && file_exists($finalLogo))
                <div style="display: table-cell; vertical-align: middle; width: 25%; padding-right: 15px;">
                    <img src="{{ $finalLogo }}" 
                         alt="Logo" 
                         style="max-height: 55px; max-width: 130px; object-fit: contain;">
                </div>
                @endif
                <div style="display: table-cell; vertical-align: middle; {{ ($finalLogo && file_exists($finalLogo)) ? 'width: 75%;' : 'width: 100%;' }}">
                    <h2 style="margin: 0 0 3px 0; font-size: 14px; text-transform: uppercase;">{{ $companySettings->company_name ?? ($order->tenant?->name ?? 'Vestalize') }}</h2>
                    <div style="font-size: 8px; line-height: 1.3;">
                        @if($companySettings->company_address || $companySettings->company_city)
                        <span>{{ $companySettings->company_address }}@if($companySettings->company_city), {{ $companySettings->company_city }}@endif @if($companySettings->company_state) - {{ $companySettings->company_state }}@endif @if($companySettings->company_zip) | CEP: {{ $companySettings->company_zip }}@endif</span><br>
                        @endif
                        
                        <div style="margin-top: 2px;">
                            @if($companySettings->company_cnpj)
                            <strong>CNPJ:</strong> {{ $companySettings->company_cnpj }}
                            @endif
                            @if($companySettings->company_cnpj && ($companySettings->company_phone || $companySettings->company_email)) | @endif
                            
                            @if($companySettings->company_phone)
                            <strong>Tel:</strong> {{ $companySettings->company_phone }}
                            @endif
                            @if($companySettings->company_phone && $companySettings->company_email) | @endif
                            
                            @if($companySettings->company_email)
                            <strong>E-mail:</strong> {{ $companySettings->company_email }}
                            @endif
                        </div>

                        @if($companySettings->company_website)
                        <div style="margin-top: 1px;">
                            <strong>Site:</strong> {{ $companySettings->company_website }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Cliente -->
    <div class="section">
        <div class="section-title">DADOS DO CLIENTE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">{{ $order->client?->name ?? 'Consumidor Final (Não identificado)' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telefone:</div>
                <div class="info-value">{{ $order->client?->phone_primary ?? 'N/A' }}</div>
            </div>
            @if($order->client?->email)
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $order->client->email }}</div>
            </div>
            @endif
            @if($order->client?->cpf_cnpj)
            <div class="info-row">
                <div class="info-label">CPF/CNPJ:</div>
                <div class="info-value">{{ $order->client->cpf_cnpj }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Status e Datas -->
    <div class="section">
        <div class="section-title">STATUS DO PEDIDO</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Status Atual:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->status?->name ?? 'Novo')) }}" 
                          style="background-color: {{ $order->status?->color ?? '#666' }}20; color: {{ $order->status?->color ?? '#666' }}">
                        {{ $order->status?->name ?? 'Novo' }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Data do Pedido:</div>
                <div class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($order->delivery_date)
            <div class="info-row">
                <div class="info-label">Data de Entrega:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Itens do Pedido -->
    @foreach($order->items as $item)
    @php
        // Garantir que sizes seja um array
        $itemSizesForSum = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) && !empty($item->sizes) ? json_decode($item->sizes, true) : []);
        $itemSizesForSum = $itemSizesForSum ?? [];
        
        // Priorizar a quantidade total do item se a soma dos tamanhos for zero
        $sumSizes = array_sum($itemSizesForSum);
        $totalQuantity = ($sumSizes > 0) ? $sumSizes : (int)$item->quantity;
    @endphp
    <div class="item-section">
        <div class="item-header">ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->print_type }} ({{ $totalQuantity }} {{ $totalQuantity > 1 ? 'UNIDADES' : 'UNIDADE' }})</div>
        
        <!-- Nome da Arte -->
        @if($item->art_name)
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">NOME DA ARTE:</div>
            <div style="font-size: 12px; font-weight: bold; padding: 5px; background-color: white; text-align: center; border: 1px solid #E5E7EB;">
                {{ $item->art_name }}
            </div>
        </div>
        @endif


        <!-- Detalhes da Costura -->
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">ESPECIFICAÇÕES DA COSTURA:</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Tecido:</div>
                    <div class="info-value">{{ $item->fabric }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cor:</div>
                    <div class="info-value">{{ $item->color }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tipo de Impressão:</div>
                    <div class="info-value">{{ $item->print_type }}</div>
                </div>
            </div>
        </div>
        <div class="price-row">
            <span>Quantidade Total: {{ $totalQuantity }} unidades</span>
                <span>Preço Unitário da Costura: R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                <span><strong>Valor da Costura: R$ {{ number_format($item->unit_price * $totalQuantity, 2, ',', '.') }}</strong></span>
            </div>
        </div>

        <!-- Aplicações -->
        @if($item->sublimations && $item->sublimations->count() > 0)
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">APLICAÇÕES DE PERSONALIZAÇÃO:</div>
            
            @foreach($item->sublimations as $index => $sub)
            @php
                $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
            @endphp
            <div class="application-item">
                <div class="application-header">
                    Aplicação {{ $index + 1 }}: 
                    @if($sizeName)
                        {{ $sizeName }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif
                    @else
                        {{ $appType }}
                    @endif
                </div>
                <div class="application-details">
                    @if($locationName)<strong>Local:</strong> {{ $locationName }} | @endif
                    <strong>Quantidade:</strong> {{ $sub->quantity }}
                    @if($sub->color_count > 0)
                    | <strong>Cores:</strong> {{ $sub->color_count }}
                    @endif
                    @if($sub->has_neon)
                    | <strong>Neon:</strong> Sim
                    @endif
                </div>
                <div class="price-row">
                    <span>Preço Unitário: R$ {{ number_format($sub->unit_price, 2, ',', '.') }} × {{ $sub->quantity }}</span>
                    @if($sub->discount_percent > 0)
                    <span style="color: #059669;">Desconto: {{ $sub->discount_percent }}%</span>
                    @endif
                    <span><strong>Subtotal: R$ {{ number_format($sub->final_price, 2, ',', '.') }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Tamanhos -->
        <!-- Tamanhos -->
        <div style="margin-bottom: 8px;">
            @php
                $allSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'];
                // Garantir que sizes seja um array
                $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) && !empty($item->sizes) ? json_decode($item->sizes, true) : []);
                $itemSizes = $itemSizes ?? [];

                // Verificação de tamanhos reais
                $hasRealSizes = false;
                if (!empty($itemSizes)) {
                    foreach ($itemSizes as $size => $quantity) {
                        $qty = (int)$quantity;
                        if ($qty > 0 && strtoupper($size) !== 'ÚNICO' && strtoupper($size) !== 'UN' && strtoupper($size) !== 'UNICO') {
                            $hasRealSizes = true;
                            break;
                        }
                    }
                }

                $isSimpleItem = !$hasRealSizes;
                $printType = trim($item->print_type ?? '');
                $fabric = trim($item->fabric ?? '');
                $orderOrigin = $order->origin ?? '';
                
                // Mostrar apenas total se for simples/sem tamanhos marcados, ou se for módulo personalizado sem tamanhos
                $shouldShowTotalOnly = $isSimpleItem || ($printType === 'Sublimação Local' || $fabric === 'Produto Pronto' || $orderOrigin === 'personalized');
            @endphp

            @if($shouldShowTotalOnly)
                <div style="font-weight: bold; margin-bottom: 3px; font-size: 12px;">QUANTIDADE:</div>
                <div style="border: 2px solid #000; padding: 8px; text-align: center; font-weight: bold; font-size: 14px; background-color: #f0f0f0;">
                    Quantidade Total: {{ $totalQuantity }}
                </div>
            @else
                <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px; border-bottom: 1px solid #000;">TAMANHOS (VERTICAL):</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 12px; border: 1px solid #000;">
                    <thead>
                        <tr style="background-color: #000 !important; color: #fff !important;">
                            <th style="border: 1px solid #000; padding: 6px; text-align: left; font-weight: bold; width: 60%;">Tamanho</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold;">Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allSizes as $size)
                        @php
                            $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? 0;
                        @endphp
                        @if($qty > 0)
                        <tr>
                            <td style="border: 1px solid #000; padding: 6px; text-align: left; font-weight: bold; background-color: #f8f8f8;">{{ $size }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; background-color: #e5e7eb; font-size: 14px;">{{ $qty }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

                @if(isset($printDesc['is_client_modeling']) && $printDesc['is_client_modeling'])
                <div style="margin-top: 8px; padding: 6px; background-color: #d1fae5; border: 1px solid #10b981; color: #065f46; font-weight: bold; font-size: 11px; text-transform: uppercase; text-align: center;">
                    * Tamanho Especial: Cliente possui modelagem própria
                </div>
                @endif
            @endif
        </div>

        <!-- Acréscimos de Tamanhos Especiais -->
        @php
            // Calcular total do item (costura + personalizações) para usar no cálculo dos acréscimos
            $itemTotalBeforeSurcharges = ($item->unit_price * $totalQuantity);
            if ($item->sublimations && $item->sublimations->count() > 0) {
                foreach ($item->sublimations as $sub) {
                    $itemTotalBeforeSurcharges += $sub->final_price;
                }
            }
            
            // Tamanhos que têm acréscimo
            $sizesWithSurcharge = ['GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL', 'Especial'];
            $hasSurcharges = false;
            $totalSurcharges = 0;
            $surchargesDetails = [];
            
            // Verificar se é restrito e se deve aplicar acréscimo
            $model = strtoupper($item->model ?? '');
            $detail = strtoupper($item->detail ?? '');
            $isRestricted = str_contains($model, 'INFANTIL') || str_contains($model, 'BABY LOOK') || 
                            str_contains($detail, 'INFANTIL') || str_contains($detail, 'BABY LOOK');
            
            $printDesc = is_string($item->print_desc) ? json_decode($item->print_desc, true) : $item->print_desc;
            $applySurcharge = filter_var($printDesc['apply_surcharge'] ?? false, FILTER_VALIDATE_BOOLEAN);
            
            // Se for restrito e NÃO tiver checkbox marcado, não calcula acréscimos
            if (!$isRestricted || $applySurcharge) {
                foreach ($sizesWithSurcharge as $size) {
                    $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? 0;
                    if ($qty > 0) {
                        // Buscar acréscimo no banco de dados baseado no total do item
                        $surchargeData = \App\Models\SizeSurcharge::getSurchargeForSize($size, $itemTotalBeforeSurcharges);
                        if ($surchargeData) {
                            $surchargePerUnit = $surchargeData->surcharge;
                            $totalSurchargeForSize = $surchargePerUnit * $qty;
                            $totalSurcharges += $totalSurchargeForSize;
                            $hasSurcharges = true;
                            $surchargesDetails[] = [
                                'size' => $size,
                                'quantity' => $qty,
                                'surcharge_per_unit' => $surchargePerUnit,
                                'total' => $totalSurchargeForSize
                            ];
                        }
                    }
                }
            }
        @endphp
        
        @if($hasSurcharges)
        <div style="margin-bottom: 8px; padding: 6px; background-color: #fff3cd; border: 1px solid #ffc107; border-left: 3px solid #ff9800;">
            <div style="font-weight: bold; margin-bottom: 4px; font-size: 10px; color: #856404;">ACRÉSCIMOS DE TAMANHOS ESPECIAIS:</div>
            @foreach($surchargesDetails as $detail)
            <div class="price-row" style="font-size: 9px; margin-top: 3px;">
                <span>Acréscimo {{ $detail['size'] }} ({{ $detail['quantity'] }}x R$ {{ number_format($detail['surcharge_per_unit'], 2, ',', '.') }}):</span>
                <span style="color: #ff9800; font-weight: bold;">+R$ {{ number_format($detail['total'], 2, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="price-row" style="font-size: 10px; margin-top: 4px; padding-top: 4px; border-top: 1px solid #ffc107; font-weight: bold;">
                <span>Total de Acréscimos:</span>
                <span style="color: #ff5722; font-weight: bold;">+R$ {{ number_format($totalSurcharges, 2, ',', '.') }}</span>
            </div>
        </div>
        @endif
    </div>
    @endforeach

    <!-- Resumo Financeiro -->
    <div class="total-section">
        <h3>TOTAL DO PEDIDO</h3>
        <div class="amount">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
    </div>

    <!-- Informações de Pagamento -->
    @if($payment)
    <div class="payment-info">
        <h4>INFORMAÇÕES DE PAGAMENTO</h4>
        <div class="payment-details">
            <div style="margin-bottom: 5px;">
                <strong>Total Pago:</strong> R$ {{ number_format($payment->entry_amount, 2, ',', '.') }}
            </div>
            <div style="margin-bottom: 5px;">
                <strong>{{ $payment->remaining_amount < 0 ? 'Crédito do Cliente:' : 'Restante:' }}</strong> R$ {{ number_format(abs($payment->remaining_amount), 2, ',', '.') }}
            </div>
            <div>
                <strong>Método:</strong> {{ ucfirst($payment->method) }}
            </div>
            @if($payment->notes)
            <div style="margin-top: 5px;">
                <strong>Observações:</strong> {{ $payment->notes }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Observações -->
    @if($order->notes)
    <div class="section">
        <div class="section-title">OBSERVAÇÕES</div>
        <div style="padding: 8px; background-color: #f5f5f5; border-left: 4px solid var(--brand-primary); font-size: 9px; line-height: 1.4;">
            {{ $order->notes }}
        </div>
    </div>
    @endif

    <!-- Rodapé -->
    <div class="footer">
        <p><strong>Obrigado pela preferência!</strong></p>
        <p>Esta nota serve como comprovante do seu pedido.</p>
        <p>Para dúvidas, entre em contato conosco.</p>
        @if($companySettings->company_phone || $companySettings->company_email)
        <p>
            @if($companySettings->company_phone)Tel: {{ $companySettings->company_phone }}@endif
            @if($companySettings->company_phone && $companySettings->company_email) | @endif
            @if($companySettings->company_email)Email: {{ $companySettings->company_email }}@endif
        </p>
        @endif
        <p>Impresso em {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
