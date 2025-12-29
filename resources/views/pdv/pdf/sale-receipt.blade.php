<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Nota de Venda #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #000;
        }
        .header h1 {
            font-size: 24px;
            color: #000;
            margin-bottom: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header .subtitle {
            font-size: 14px;
            color: #333;
            font-weight: bold;
        }
        .company-info {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
        }
        .company-info h2 {
            font-size: 16px;
            color: #000;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .company-info p {
            font-size: 10px;
            color: #333;
            margin: 2px 0;
            line-height: 1.3;
        }
        .section {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #000;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
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
            width: 35%;
            font-size: 11px;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
            font-size: 11px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th {
            background-color: #000;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #000;
        }
        .items-table td {
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 11px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #000;
            color: white;
            text-align: right;
        }
        .total-section .total-label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .total-section .total-value {
            font-size: 20px;
            font-weight: bold;
        }
        .payment-section {
            margin-top: 15px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
        }
        .payment-method {
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        .payment-method:last-child {
            border-bottom: none;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($companySettings) && $companySettings->logo_path && file_exists(public_path($companySettings->logo_path)))
            <img src="{{ public_path($companySettings->logo_path) }}" alt="Logo" style="max-height: 80px; margin-bottom: 10px;">
        @else
            <h1>NOTA DE VENDA</h1>
        @endif
        
        <div class="company-details" style="font-size: 11px; margin-bottom: 10px;">
            <div style="font-weight: bold; font-size: 14px; margin-bottom: 2px;">{{ $companySettings->company_name ?? 'Nóbrega Confecções' }}</div>
            
            @if($companySettings->company_address || $companySettings->company_city)
            <div>
                {{ $companySettings->company_address ?? '' }}
                @if($companySettings->company_city), {{ $companySettings->company_city }}@endif
                @if($companySettings->company_state) - {{ $companySettings->company_state }}@endif
                @if($companySettings->company_zip) - CEP: {{ $companySettings->company_zip }}@endif
            </div>
            @endif
            
            <div>
                @if($companySettings->company_phone)Tel: {{ $companySettings->company_phone }}@endif
                @if($companySettings->company_phone && $companySettings->company_email) | @endif
                @if($companySettings->company_email)Email: {{ $companySettings->company_email }}@endif
            </div>
            
            @if($companySettings->company_cnpj)
            <div>CNPJ: {{ $companySettings->company_cnpj }}</div>
            @endif
        </div>

        <div class="subtitle" style="font-size: 16px; margin-top: 5px; padding-top: 5px; border-top: 1px solid #ccc;">Venda #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <!-- Informações do Cliente -->
    <div class="section">
        <div class="section-title">DADOS DO CLIENTE</div>
        <div class="info-grid">
            @if($order->client)
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">{{ $order->client->name }}</div>
            </div>
            @if($order->client->phone_primary)
            <div class="info-row">
                <div class="info-label">Telefone:</div>
                <div class="info-value">{{ $order->client->phone_primary }}</div>
            </div>
            @endif
            @if($order->client->cpf_cnpj)
            <div class="info-row">
                <div class="info-label">CPF/CNPJ:</div>
                <div class="info-value">{{ $order->client->cpf_cnpj }}</div>
            </div>
            @endif
            @else
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">Venda sem cliente cadastrado</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Informações da Venda -->
    <div class="section">
        <div class="section-title">INFORMAÇÕES DA VENDA</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Data da Venda:</div>
                <div class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Vendedor:</div>
                <div class="info-value">{{ $order->user->name ?? 'N/A' }}</div>
            </div>
            @if($order->notes)
            <div class="info-row">
                <div class="info-label">Observações:</div>
                <div class="info-value">{{ $order->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Itens da Venda -->
    <div class="section">
        <div class="section-title">ITENS DA VENDA</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Descrição</th>
                    <th>Quantidade</th>
                    <th>Preço Unit.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                @php
                    // Calcular acréscimos de tamanho
                    $sizes = is_string($item->sizes) ? json_decode($item->sizes, true) : $item->sizes;
                    $sizeSurcharges = [];
                    $totalSurcharges = 0;
                    
                    if (is_array($sizes) && !empty($sizes)) {
                        foreach (['GG', 'EXG', 'G1', 'G2', 'G3'] as $size) {
                            $qty = $sizes[$size] ?? 0;
                            if ($qty > 0 && $item->unit_price > 0) {
                                $surchargeData = \App\Models\SizeSurcharge::getSurchargeForSize($size, $item->unit_price);
                                if ($surchargeData) {
                                    $surchargeTotal = $surchargeData->surcharge * $qty;
                                    $sizeSurcharges[$size] = [
                                        'quantity' => $qty,
                                        'surcharge_per_unit' => $surchargeData->surcharge,
                                        'total' => $surchargeTotal,
                                    ];
                                    $totalSurcharges += $surchargeTotal;
                                }
                            }
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $item->print_type }}</strong>
                        @if($item->fabric)
                        <br><small>Tecido: {{ $item->fabric }}</small>
                        @endif
                        @if($item->color)
                        <br><small>Cor: {{ $item->color }}</small>
                        @endif
                        @if($sizes && is_array($sizes))
                        <br><small>
                            Tamanhos: 
                            @foreach($sizes as $size => $qty)
                                @if($qty > 0)
                                    {{ $size }}({{ $qty }}) 
                                @endif
                            @endforeach
                        </small>
                        @endif
                    </td>
                    <td>
                        {{ number_format($item->quantity, 2, ',', '.') }}
                        @if($item->print_type === 'Peça de Tecido' || (isset($item->sale_type) && $item->sale_type === 'kg'))
                            kg
                        @endif
                    </td>
                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}</td>
                </tr>
                @if(!empty($sizeSurcharges))
                @foreach($sizeSurcharges as $size => $surchargeData)
                <tr style="background-color: #f0f0f0;">
                    <td></td>
                    <td style="padding-left: 30px;">
                        <small>
                            <strong>Acréscimo Tamanho {{ $size }}:</strong><br>
                            {{ $surchargeData['quantity'] }} unidade(s) × R$ {{ number_format($surchargeData['surcharge_per_unit'], 2, ',', '.') }}
                        </small>
                    </td>
                    <td>{{ number_format($surchargeData['quantity'], 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($surchargeData['surcharge_per_unit'], 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($surchargeData['total'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
                @endif
                @if($item->sublimations && $item->sublimations->count() > 0)
                    @foreach($item->sublimations as $sublimation)
                    <tr style="background-color: #f0f0f0;">
                        <td></td>
                        <td style="padding-left: 30px;">
                            <small>
                                <strong>Personalização SUB.LOCAL:</strong><br>
                                @php
                                    $locationName = $sublimation->location ? $sublimation->location->name : ($sublimation->location_name ?? 'Local não informado');
                                    $sizeName = $sublimation->size ? $sublimation->size->name : ($sublimation->size_name ?? '');
                                @endphp
                                {{ $locationName }}
                                @if($sizeName)
                                - Tamanho: {{ $sizeName }}
                                @endif
                            </small>
                        </td>
                        <td>{{ number_format($sublimation->quantity, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($sublimation->unit_price, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($sublimation->final_price, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @php
                        // Calcular total do item incluindo acréscimos e personalizações
                        $itemBaseTotal = $item->unit_price * $item->quantity;
                        $itemSublimationsTotal = $item->sublimations->sum('final_price');
                        $itemTotalWithSurcharges = $itemBaseTotal + $totalSurcharges + $itemSublimationsTotal;
                    @endphp
                    @if($totalSurcharges > 0 || $itemSublimationsTotal > 0)
                    <tr style="background-color: #e8e8e8; font-weight: bold; border-top: 2px solid #000;">
                        <td colspan="4" style="text-align: right; padding-right: 10px;">Subtotal do Item:</td>
                        <td>R$ {{ number_format($itemTotalWithSurcharges, 2, ',', '.') }}</td>
                    </tr>
                    @endif
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totais -->
    <div class="total-section">
        <div class="total-label">TOTAL DA VENDA</div>
        <div class="total-value">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
    </div>

    <!-- Formas de Pagamento -->
    @if($payment && $payment->payment_methods)
    <div class="payment-section">
        <div class="section-title" style="border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 5px;">FORMA(S) DE PAGAMENTO</div>
        @php
            $paymentMethods = is_array($payment->payment_methods) ? $payment->payment_methods : json_decode($payment->payment_methods, true);
        @endphp
        @if(is_array($paymentMethods))
            @foreach($paymentMethods as $method)
            <div class="payment-method">
                <strong>{{ ucfirst($method['method'] ?? 'N/A') }}:</strong> 
                R$ {{ number_format($method['amount'] ?? 0, 2, ',', '.') }}
            </div>
            @endforeach
        @endif
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ccc; font-weight: bold;">
            Total Pago: R$ {{ number_format($payment->entry_amount ?? 0, 2, ',', '.') }}
            @if($payment->remaining_amount > 0)
            <br><small style="color: #d32f2f;">Restante: R$ {{ number_format($payment->remaining_amount, 2, ',', '.') }}</small>
            @endif
        </div>
    </div>
    @endif

    <!-- Rodapé -->
    <div class="footer">
        <p>Esta é uma nota de venda gerada automaticamente pelo sistema PDV.</p>
        <p>Data de impressão: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

