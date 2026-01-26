<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Orçamento #{{ $budget->budget_number }}</title>
    <style>
        @page {
            margin: 0px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 40px 40px 20px 40px;
            background-color: #fff;
        }
        
        /* Header */
        .header-container {
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }
        .company-logo {
            max-width: 180px;
            max-height: 80px;
        }
        .company-details {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        /* Budget Title & Meta */
        .budget-meta {
            margin-bottom: 30px;
            width: 100%;
        }
        .budget-title {
            font-size: 24px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
        }
        .budget-dates {
            font-size: 11px;
            color: #6b7280;
        }
        .validity-badge {
            display: inline-block;
            background-color: #eff6ff;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            margin-left: 10px;
        }

        /* Client Info */
        .client-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-grid {
            width: 100%;
        }
        .info-label {
            width: 15%;
            font-weight: bold;
            color: #374151;
            padding: 2px 0;
        }
        .info-value {
            color: #111827;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            text-align: left;
            padding: 10px;
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .item-desc-title {
            font-weight: bold;
            color: #111827;
            font-size: 12px;
            margin-bottom: 2px;
        }
        .item-desc-meta {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .pers-tag {
            display: inline-block;
            background-color: #eef2ff;
            color: #4338ca;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 4px;
            border: 1px solid #c7d2fe;
        }

        /* Totals */
        .totals-container {
            width: 250px;
            margin-left: auto;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
            color: #4b5563;
        }
        .total-row.final {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            font-weight: bold;
            font-size: 16px;
            color: #111827;
            align-items: center;
        }
        .total-row.final .value {
            color: #4f46e5;
        }
        .discount-text {
            color: #dc2626;
        }

        /* Notes & Footer */
        .notes-section {
            margin-top: 30px;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 6px;
            font-size: 10px;
            background-color: #ffffff;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
    </style>
</head>
<body>
    @php
        $tenant = auth()->user()->tenant;
        $logoPath = null;
        // Prioritize Tenant Logo, fallback to Settings
        if ($tenant && $tenant->logo_path && file_exists(public_path('storage/' . $tenant->logo_path))) {
            $logoPath = public_path('storage/' . $tenant->logo_path);
        } elseif ($settings && $settings->logo_path) {
             if (file_exists(public_path($settings->logo_path))) {
                 $logoPath = public_path($settings->logo_path);
             } elseif (file_exists(public_path('storage/' . $settings->logo_path))) {
                 $logoPath = public_path('storage/' . $settings->logo_path);
             }
        }
    @endphp

    <!-- Company Header -->
    <table class="header-container">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo" class="company-logo">
                @else
                    <div class="company-name">{{ $settings->company_name ?? 'MINHA EMPRESA' }}</div>
                @endif
            </td>
            <td style="width: 50%; vertical-align: top;" class="company-details">
                <div class="company-name">{{ $settings->company_name ?? $budget->store->name ?? '' }}</div>
                @if($settings->company_cnpj) CNPJ: {{ $settings->company_cnpj }}<br> @endif
                @if($settings->company_address) 
                    {{ $settings->company_address }}
                    @if($settings->company_city) - {{ $settings->company_city }}/{{ $settings->company_state }} @endif
                    <br>
                @endif
                @if($settings->company_phone) Tel: {{ $settings->company_phone }}<br> @endif
                @if($settings->company_email) {{ $settings->company_email }} @endif
            </td>
        </tr>
    </table>

    <!-- Budget Title & Info -->
    <table class="budget-meta">
        <tr>
            <td style="width: 60%;">
                <div class="budget-title">ORÇAMENTO #{{ $budget->budget_number }}</div>
                <div class="budget-dates">
                    Emitido em: {{ $budget->created_at->format('d/m/Y') }}
                    <span class="validity-badge">
                        Válido até: {{ \Carbon\Carbon::parse($budget->valid_until)->format('d/m/Y') }}
                    </span>
                </div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div style="font-size: 10px; color: #6b7280; margin-bottom: 2px;">STATUS DO ORÇAMENTO</div>
                <div style="font-weight: bold; font-size: 12px; text-transform: uppercase; color: {{ $budget->status === 'approved' ? '#16a34a' : '#ea580c' }};">
                    @if($budget->status === 'pending') PENDENTE
                    @elseif($budget->status === 'approved') APROVADO
                    @elseif($budget->status === 'rejected') REJEITADO
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Client Info -->
    <div class="client-box">
        <div class="section-title">Dados do Cliente</div>
        <table class="info-grid">
            @if($budget->is_quick)
            <tr>
                <td class="info-label">Nome:</td>
                <td class="info-value">{{ $budget->contact_name }}</td>
                <td class="info-label">Tel/WhatsApp:</td>
                <td class="info-value">{{ $budget->contact_phone }}</td>
            </tr>
            @else
            <tr>
                <td class="info-label">Cliente:</td>
                <td class="info-value"><strong>{{ $budget->client->name ?? 'Consumidor Final' }}</strong></td>
                <td class="info-label">Telefone:</td>
                <td class="info-value">{{ $budget->client->phone_primary ?? '-' }}</td>
            </tr>
            @if($budget->client && $budget->client->email)
            <tr>
                <td class="info-label">Email:</td>
                <td class="info-value" colspan="3">{{ $budget->client->email }}</td>
            </tr>
            @endif
            @endif
        </table>
    </div>

    <!-- Items Table (Unified) -->
    <div class="section-title" style="margin-bottom: 10px; padding-left: 5px;">Itens do Orçamento</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 50%;">Descrição do Item & Personalizações</th>
                <th style="width: 10%; text-align: center;">Qtd.</th>
                <th style="width: 15%; text-align: right;">V. Unit.</th>
                <th style="width: 20%; text-align: right;">V. Total</th>
            </tr>
        </thead>
        <tbody>
            @if($budget->is_quick)
                <!-- Quick Budget Single Item Logic -->
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>
                        <div class="item-desc-title">Orçamento Rápido - {{ $budget->technique }}</div>
                        <div class="item-desc-meta">
                            {{ $budget->product_internal ? 'Produto: ' . $budget->product_internal : '' }}
                            {{ $budget->deadline_days ? '| Prazo: ' . $budget->deadline_days . ' dias' : '' }}
                        </div>
                    </td>
                    <td style="text-align: center;">{{ $budget->quantity }}</td>
                    <td style="text-align: right;">R$ {{ number_format($budget->unit_price, 2, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold;">R$ {{ number_format($budget->total, 2, ',', '.') }}</td>
                </tr>
            @else
                <!-- Normal Budget Items -->
                @foreach($budget->items as $item)
                    @php
                        $personalizationTypes = json_decode($item->personalization_types, true) ?? [];
                        
                        // Price Calculations
                        $sewingTotal = $item->item_total;
                        $sewingUnit = $item->quantity > 0 ? $sewingTotal / $item->quantity : 0;

                        $persTotal = $item->customizations->sum('total_price');
                        $persUnit = $item->quantity > 0 ? $persTotal / $item->quantity : 0;

                        $finalItemTotal = $sewingTotal + $persTotal;
                        $finalUnitPrice = $item->quantity > 0 ? ($finalItemTotal / $item->quantity) : 0;
                        
                        // Mode check (controller usually passes $modo, fallback to request)
                        $isDetailed = ($modo ?? request('modo') ?? 'detalhado') !== 'unificado';
                    @endphp
                    <tr>
                        <td style="text-align: center; color: #6b7280;">{{ $loop->iteration }}</td>
                        <td>
                            <div class="item-desc-title">
                                {{ $personalizationTypes['print_type'] ?? 'Item Personalizado' }}
                                {{ !empty($personalizationTypes['model']) ? ' - ' . $personalizationTypes['model'] : '' }}
                            </div>
                            <div class="item-desc-meta">
                                <strong>Tecido:</strong> {{ $item->fabric }} | <strong>Cor:</strong> {{ $item->color }}
                                {{ !empty($personalizationTypes['collar']) ? '| Gola: ' . $personalizationTypes['collar'] : '' }}
                            </div>
                            
                            <!-- Personalization Tags Summary -->
                            @if($item->customizations->count() > 0)
                                <div style="margin-top: 6px;">
                                    @foreach($item->customizations as $cust)
                                        <span class="pers-tag">
                                            {{ $cust->personalization_type }}: {{ $cust->location }}
                                            @if($cust->color_count > 1) ({{ $cust->color_count }} cores) @endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        
                        <!-- Unit Price -->
                        <td style="text-align: right; vertical-align: top;">
                            @if($isDetailed && $persTotal > 0)
                                <div style="font-size: 9px; color: #6b7280;">Costura: R$ {{ number_format($sewingUnit, 2, ',', '.') }}</div>
                                <div style="font-size: 9px; color: #6b7280;">Pers.: R$ {{ number_format($persUnit, 2, ',', '.') }}</div>
                                <div style="border-top: 1px solid #e5e7eb; margin-top: 2px; padding-top: 2px; font-weight: bold; color: #111827;">
                                    R$ {{ number_format($finalUnitPrice, 2, ',', '.') }}
                                </div>
                            @else
                                <span style="font-weight: bold; color: #111827;">
                                    R$ {{ number_format($finalUnitPrice, 2, ',', '.') }}
                                </span>
                            @endif
                        </td>

                        <!-- Total Price -->
                        <td style="text-align: right; vertical-align: top;">
                            @if($isDetailed && $persTotal > 0)
                                <div style="font-size: 9px; color: #6b7280;">R$ {{ number_format($sewingTotal, 2, ',', '.') }}</div>
                                <div style="font-size: 9px; color: #6b7280;">R$ {{ number_format($persTotal, 2, ',', '.') }}</div>
                                <div style="border-top: 1px solid #e5e7eb; margin-top: 2px; padding-top: 2px; font-weight: bold; color: #111827;">
                                    R$ {{ number_format($finalItemTotal, 2, ',', '.') }}
                                </div>
                            @else
                                <span style="font-weight: bold; color: #111827;">
                                    R$ {{ number_format($finalItemTotal, 2, ',', '.') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Totals & Notes Layout -->
    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top; padding-right: 30px;">
                @if($budget->observations)
                <div class="notes-section" style="margin-top: 0;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Observações:</div>
                    {{ $budget->observations }}
                </div>
                @endif
                
                @if($budget->admin_notes)
                <div class="notes-section" style="background-color: #fffbeb; border-color: #fcd34d;">
                    <div style="font-weight: bold; margin-bottom: 5px; color: #92400e;">Notas Importantes:</div>
                    {!! nl2br(e($budget->admin_notes)) !!}
                </div>
                @endif
            </td>
            <td style="width: 250px; vertical-align: top;">
                <div class="totals-container">
                    @if(!$budget->is_quick)
                        <div class="total-row">
                            <span>Subtotal Itens:</span>
                            <span>R$ {{ number_format($budget->items->sum('item_total'), 2, ',', '.') }}</span>
                        </div>
                        @php
                            $totalCustoms = $budget->items->flatMap->customizations->sum('total_price');
                        @endphp
                        @if($totalCustoms > 0)
                        <div class="total-row">
                            <span>Total Personalizações:</span>
                            <span>R$ {{ number_format($totalCustoms, 2, ',', '.') }}</span>
                        </div>
                        @endif
                    @endif
                    
                    @if($budget->discount > 0)
                    <div class="total-row discount-text">
                        <span>Desconto:</span>
                        <span>- R$ {{ number_format($budget->discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="total-row final">
                        <span>TOTAL GERAL:</span>
                        <span class="value">R$ {{ number_format($budget->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        @if($settings->footer_text)
            {!! nl2br(e($settings->footer_text)) !!}
        @else
            Este orçamento não garante reserva de estoque. Os valores podem sofrer alteração após o prazo de validade.<br>
            Prazo de produção a combinar no momento do fechamento do pedido.
        @endif
        <br><br>
        Orçamento gerado em {{ now()->format('d/m/Y H:i') }} por {{ $budget->user->name }}.
    </div>

</body>
</html>
