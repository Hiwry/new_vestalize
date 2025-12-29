<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Orçamento #{{ $budget->budget_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #000;
            font-size: 18px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .header h2 {
            color: #000;
            font-size: 16px;
            margin-top: 5px;
            font-weight: bold;
        }
        .header p {
            color: #333;
            font-size: 9px;
            margin-top: 3px;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section h2 {
            font-size: 12px;
            color: #000;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            font-weight: bold;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 25%;
            padding: 3px 8px 3px 0;
            color: #000;
            font-weight: bold;
            font-size: 9px;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
            color: #000;
            font-size: 9px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #f5f5f5;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 9px;
        }
        .items-table td {
            padding: 8px 10px;
            border: 1px solid #000;
            font-size: 9px;
            vertical-align: middle;
        }
        .total-section {
            margin-top: 25px;
            float: right;
            width: 280px;
            border: 1px solid #ddd;
            background-color: #fafafa;
            padding: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
            line-height: 1.5;
        }
        .total-row:last-child {
            border-bottom: none;
        }
        .total-row.final {
            background-color: #000;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            border: 2px solid #000;
            margin-top: 8px;
            padding: 10px 12px;
        }
        .observations {
            clear: both;
            margin-top: 25px;
            padding: 12px;
            border: 1px solid #000;
            background-color: #fafafa;
        }
        .observations strong {
            font-size: 10px;
            display: block;
            margin-bottom: 6px;
        }
        .alert-box {
            background-color: #FEF3C7;
            border: 2px solid #F59E0B;
            border-left: 5px solid #F59E0B;
            padding: 10px 12px;
            margin: 8px 0;
            border-radius: 4px;
        }
        .alert-box strong {
            color: #92400E;
            font-size: 10px;
            display: block;
            margin-bottom: 4px;
        }
        .alert-box p {
            color: #78350F;
            font-size: 9px;
            margin: 0;
            line-height: 1.4;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #333;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
            margin-top: 5px;
        }
        .item-box {
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 15px;
            background-color: #fafafa;
        }
        .item-header {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #ccc;
        }
        .item-details {
            font-size: 9px;
            line-height: 1.7;
        }
        .item-details-row {
            margin-bottom: 4px;
        }
        .price-table {
            width: 100%;
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 8px;
        }
        .price-row.total {
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @if($settings->logo_path && file_exists(public_path($settings->logo_path)))
        <img src="{{ public_path($settings->logo_path) }}" alt="Logo" style="max-width: 200px; max-height: 80px; margin: 0 auto 10px;">
        @endif
        @if($settings->company_name)
        <h1>{{ strtoupper($settings->company_name) }}</h1>
        @endif
        <h2 style="color: #4F46E5; font-size: 20px; margin-top: 10px;">ORÇAMENTO #{{ $budget->budget_number }}</h2>
        <p>Emitido em {{ $budget->created_at->format('d/m/Y') }} | Válido até {{ \Carbon\Carbon::parse($budget->valid_until)->format('d/m/Y') }}</p>
        @if($settings->company_phone || $settings->company_email)
        <p style="margin-top: 8px; font-size: 10px;">
            @if($settings->company_phone)Tel: {{ $settings->company_phone }}@endif
            @if($settings->company_phone && $settings->company_email) | @endif
            @if($settings->company_email){{ $settings->company_email }}@endif
        </p>
        @endif
        <p style="margin-top: 5px;">
            <span class="status-badge status-{{ $budget->status }}">
                @if($budget->status === 'pending') PENDENTE
                @elseif($budget->status === 'approved') APROVADO
                @elseif($budget->status === 'rejected') REJEITADO
                @endif
            </span>
        </p>
    </div>

    <!-- Cliente -->
    <div class="info-section">
        <h2>Dados do Cliente</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">{{ $budget->client->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telefone:</div>
                <div class="info-value">{{ $budget->client->phone_primary }}</div>
            </div>
            @if($budget->client->email)
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $budget->client->email }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Costura -->
    @if($budget->items->count() > 0)
    <div class="info-section" style="margin-bottom: 18px;">
        @if(($modo ?? 'detalhado') === 'unificado')
        <h2>Itens (Valor Unitário já inclui Arte/Personalização)</h2>
        @else
        <h2>Costura</h2>
        @endif
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Item</th>
                    <th style="width: 14%;">Tecido</th>
                    <th style="width: 14%;">Cor</th>
                    <th style="width: 14%;">Modelo</th>
                    <th style="width: 10%;">Gola</th>
                    <th style="width: 10%; text-align: center;">Qtd</th>
                    <th style="width: 15%; text-align: right;">Unit.</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budget->items as $item)
                @php
                    $personalizationTypes = json_decode($item->personalization_types, true) ?? [];
                    $itemQuantity = $item->quantity;
                    $itemUnitPrice = $personalizationTypes['unit_price'] ?? 0;
                    $itemTotal = $item->item_total;
                    
                    // Para modo unificado, somar personalizações ao preço unitário
                    if (($modo ?? 'detalhado') === 'unificado') {
                        $itemPersonalizationsTotal = $item->customizations->sum('total_price');
                        $personalizationPerPiece = $itemQuantity > 0 ? ($itemPersonalizationsTotal / $itemQuantity) : 0;
                        $itemUnitPrice = $itemUnitPrice + $personalizationPerPiece;
                        $itemTotal = $itemUnitPrice * $itemQuantity;
                    }
                @endphp
                <tr>
                    <td style="text-align: center;"><strong>{{ $item->item_number }}</strong></td>
                    <td>{{ $item->fabric ?? '-' }}</td>
                    <td>{{ $item->color ?? '-' }}</td>
                    <td>{{ $personalizationTypes['model'] ?? '-' }}</td>
                    <td>{{ $personalizationTypes['collar'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ $itemQuantity }}</td>
                    <td style="text-align: right;">R$ {{ number_format($itemUnitPrice, 2, ',', '.') }}</td>
                    <td style="text-align: right;"><strong>R$ {{ number_format($itemTotal, 2, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif


    @php
        $allCustomizations = $budget->items->flatMap(function($item) {
            return $item->customizations;
        });
        
        // Agrupar por tipo para calcular descontos
        $serigraphyItems = $allCustomizations->filter(function($c) {
            return in_array($c->personalization_type, ['SERIGRAFIA', 'EMBORRACHADO']);
        })->sortByDesc('unit_price')->values();
        
        $sublimationItems = $allCustomizations->filter(function($c) {
            return $c->personalization_type === 'SUBLIMACAO_LOCAL' || $c->personalization_type === 'SUBLIMAÇÃO LOCAL';
        })->sortByDesc('unit_price')->values();
        
        // Marcar quais têm desconto
        $discountedIds = [];
        foreach($serigraphyItems as $index => $item) {
            if($index >= 2) { // A partir da 3ª (índice 2)
                $discountedIds[] = $item->id;
            }
        }
        foreach($sublimationItems as $index => $item) {
            if($index >= 1) { // A partir da 2ª (índice 1)
                $discountedIds[] = $item->id;
            }
        }
    @endphp
    @if($allCustomizations->count() > 0 && ($modo ?? 'detalhado') !== 'unificado')
    <!-- Personalizações -->
    <div class="info-section" style="margin-bottom: 18px;">
        <h2>Personalizações</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Tipo</th>
                    <th style="width: 16%;">Local</th>
                    <th style="width: 14%;">Tamanho</th>
                    <th style="width: 10%; text-align: center;">Cores</th>
                    <th style="width: 10%; text-align: center;">Qtd</th>
                    <th style="width: 16%; text-align: right;">Unit.</th>
                    <th style="width: 16%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allCustomizations as $custom)
                @php
                    // Mapear número de localização para nome legível
                    $locationMap = [
                        1 => 'Frente',
                        2 => 'Costas',
                        3 => 'Manga Esquerda',
                        4 => 'Manga Direita',
                        5 => 'Lateral Esquerda',
                        6 => 'Lateral Direita',
                        7 => 'Capuz',
                        8 => 'Bolso'
                    ];
                    $locationName = $locationMap[$custom->location] ?? $custom->location;
                    
                    // Verificar se tem desconto
                    $hasDiscount = in_array($custom->id, $discountedIds);
                    
                    // Calcular preço sem desconto para comparação
                    $priceWithoutDiscount = $custom->unit_price * $custom->quantity;
                @endphp
                <tr>
                    <td><strong>{{ $custom->personalization_type ?? 'N/A' }}</strong></td>
                    <td>{{ $locationName }}</td>
                    <td>{{ $custom->size }}</td>
                    <td style="text-align: center;">
                        @if(in_array($custom->personalization_type, ['SERIGRAFIA', 'EMBORRACHADO']))
                            {{ $custom->color_count ?? 1 }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $custom->quantity }}</td>
                    <td style="text-align: right;">
                        R$ {{ number_format($custom->unit_price, 2, ',', '.') }}
                    </td>
                    <td style="text-align: right;">
                        @if($hasDiscount)
                            <span style="text-decoration: line-through; color: #999; font-size: 8px;">R$ {{ number_format($priceWithoutDiscount, 2, ',', '.') }}</span><br>
                            <strong style="color: #16a34a;">R$ {{ number_format($custom->total_price, 2, ',', '.') }}</strong>
                            <span style="color: #16a34a; font-size: 8px; font-weight: bold;"> (-50%)</span>
                        @else
                            <strong>R$ {{ number_format($custom->total_price, 2, ',', '.') }}</strong>
                        @endif
                    </td>
                </tr>
                @if(in_array($custom->personalization_type, ['SERIGRAFIA', 'EMBORRACHADO']) && ($custom->color_count ?? 1) > 1)
                <tr>
                    <td colspan="7" style="font-size: 8px; color: #333; font-style: italic; padding: 4px 10px;">
                        Cálculo: R$ {{ number_format($custom->unit_price, 2, ',', '.') }} × {{ $custom->quantity }} peças com {{ $custom->color_count }} cores{{ ($custom->color_count ?? 1) >= 3 ? ' (desc. 50% a partir da 3ª cor)' : '' }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Itens (apenas no modo detalhado) -->
    @if(($modo ?? 'detalhado') !== 'unificado')
    <div class="info-section">
        <h2>Itens do Orçamento</h2>
        @foreach($budget->items as $item)
        @php
            $personalizationTypes = json_decode($item->personalization_types, true) ?? [];
            $itemQuantity = $item->quantity;
            $itemUnitPrice = $personalizationTypes['unit_price'] ?? 0;
            $itemTotal = $item->item_total;
            $itemPersonalizationsTotal = $item->customizations->sum('total_price');
            $personalizationPerPiece = $itemQuantity > 0 ? ($itemPersonalizationsTotal / $itemQuantity) : 0;
            $unitPriceWithPersonalization = $itemUnitPrice + $personalizationPerPiece;
            $itemGrandTotal = $itemTotal + $itemPersonalizationsTotal;
        @endphp
        <div class="item-box">
            <div class="item-header">
                Item {{ $item->item_number }} - {{ $personalizationTypes['print_type'] ?? 'Item ' . $item->item_number }}
            </div>
            <div class="item-details">
                <div class="item-details-row"><strong>Tecido:</strong> {{ $item->fabric }} | <strong>Cor:</strong> {{ $item->color }}</div>
                <div class="item-details-row"><strong>Modelo:</strong> {{ $personalizationTypes['model'] ?? '-' }} | <strong>Quantidade:</strong> {{ $itemQuantity }} peças</div>
                @if(!empty($personalizationTypes['collar']))
                <div class="item-details-row"><strong>Gola:</strong> {{ $personalizationTypes['collar'] }}</div>
                @endif
                @if(!empty($personalizationTypes['detail']))
                <div class="item-details-row"><strong>Detalhe:</strong> {{ $personalizationTypes['detail'] }}</div>
                @endif
            </div>
            <div class="price-table">
                <div class="price-row">
                    <span>Costura (unit.):</span>
                    <span>R$ {{ number_format($itemUnitPrice, 2, ',', '.') }}</span>
                </div>
                <div class="price-row">
                    <span>Costura (total):</span>
                    <span>R$ {{ number_format($itemTotal, 2, ',', '.') }}</span>
                </div>
                @if($itemPersonalizationsTotal > 0)
                <div class="price-row">
                    <span>Personalização (unit.):</span>
                    <span>R$ {{ number_format($personalizationPerPiece, 2, ',', '.') }}</span>
                </div>
                <div class="price-row">
                    <span>Personalização (total):</span>
                    <span>R$ {{ number_format($itemPersonalizationsTotal, 2, ',', '.') }}</span>
                </div>
                <div class="price-row" style="border-top: 1px solid #ccc; padding-top: 3px; margin-top: 3px;">
                    <span>Unitário Total:</span>
                    <span>R$ {{ number_format($unitPriceWithPersonalization, 2, ',', '.') }}</span>
                </div>
                @endif
                <div class="price-row total">
                    <span>TOTAL DO ITEM:</span>
                    <span>R$ {{ number_format($itemGrandTotal, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Total -->
    @php
        $itemsSubtotal = $budget->items->sum('item_total');
        $customizationsSubtotal = $budget->items->flatMap(fn($item) => $item->customizations)->sum('total_price');
    @endphp
    <div class="total-section">
        @if(($modo ?? 'detalhado') !== 'unificado')
        <div class="total-row">
            <span>Costura:</span>
            <span>R$ {{ number_format($itemsSubtotal, 2, ',', '.') }}</span>
        </div>
        @if($customizationsSubtotal > 0)
        <div class="total-row">
            <span>Personalizações:</span>
            <span style="color: #4F46E5;">R$ {{ number_format($customizationsSubtotal, 2, ',', '.') }}</span>
        </div>
        @endif
        @endif
        @if($budget->discount > 0)
        <div class="total-row">
            <span>Desconto:</span>
            <span style="color: #DC2626;">- R$ {{ number_format($budget->discount, 2, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row final">
            <span>VALOR TOTAL:</span>
            <span>R$ {{ number_format($budget->total, 2, ',', '.') }}</span>
        </div>
    </div>

    <!-- Observações -->
    @if($budget->observations)
    <div class="observations">
        <strong>Observações:</strong><br>
        {{ $budget->observations }}
    </div>
    @endif

    <!-- Observações do Vendedor -->
    @if($budget->admin_notes)
    <div class="observations" style="margin-top: 12px;">
        <strong>Informações Importantes:</strong><br>
        {!! nl2br(e($budget->admin_notes)) !!}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        @if($settings->footer_text)
        <p>{!! nl2br(e($settings->footer_text)) !!}</p>
        @else
        <p>Este orçamento é válido por 15 dias a partir da data de emissão.</p>
        @endif
        @if($settings->company_address || $settings->company_city || $settings->company_state)
        <p style="margin-top: 8px;">
            @if($settings->company_address){{ $settings->company_address }}@endif
            @if($settings->company_city || $settings->company_state)
                @if($settings->company_address), @endif
                {{ $settings->company_city }}@if($settings->company_city && $settings->company_state) - @endif{{ $settings->company_state }}
            @endif
            @if($settings->company_zip) | CEP: {{ $settings->company_zip }}@endif
        </p>
        @endif
        @if($settings->company_cnpj)
        <p style="margin-top: 5px;">CNPJ: {{ $settings->company_cnpj }}</p>
        @endif
        <p style="margin-top: 10px; font-size: 9px; color: #999;"><strong>Orçamento gerado por:</strong> {{ $budget->user->name }} | Sistema de Gestão</p>
    </div>
</body>
</html>

