<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>OS {{ $order->id }} - Costura</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    @foreach($order->items as $item)
    <div style="page-break-inside: avoid; border-bottom: 2px dashed #cbd5e1; padding: 10px 0; margin-bottom: 10px;">
        
        <!-- Item Header -->
        <div style="background: #475569; color: white; padding: 4px 10px; border-radius: 6px; margin-bottom: 6px; font-size: 10px; font-weight: bold;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->quantity }} PEÇAS
        </div>
        
        <!-- Header Principal -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                <!-- EVENTO -->
                <td style="width: 12%; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; text-align: center; vertical-align: middle; padding: 8px;">
                    @php
                        $contractType = isset($order->contract_type) ? \Illuminate\Support\Str::upper($order->contract_type) : null;
                    @endphp
                    <div style="font-size: 12px; font-weight: bold; color: {{ ($order->is_event || $contractType === 'EVENTO') ? '#dc3545' : '#495057' }};">
                        @if($order->is_event || $contractType === 'EVENTO')
                            EVENTO
                        @else
                            PEDIDO
                        @endif
                    </div>
                </td>
                
                <!-- NOME DA ARTE + OS -->
                <td style="width: 63%; background: #6366f1; border-radius: 6px; text-align: center; vertical-align: middle; padding: 10px; color: white;">
                    <div style="font-size: 20px; font-weight: bold; color: white; margin-bottom: 2px;">
                        {{ \Illuminate\Support\Str::upper($item->art_name ?? 'SEM NOME') }}
                    </div>
                    <div style="font-size: 14px; font-weight: bold; color: white; background: rgba(255,255,255,0.2); display: inline-block; padding: 2px 12px; border-radius: 20px;">
                        OS {{ $order->id }}
                    </div>
                </td>
                
                <!-- DATA DE ENTREGA -->
                <td style="width: 25%; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; text-align: center; vertical-align: middle; padding: 8px;">
                    <div style="font-size: 8px; color: #6c757d; text-transform: uppercase;">Entrega</div>
                    <div style="font-size: 16px; font-weight: bold; color: #212529;">
                        @if($order->delivery_date)
                            {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                        @else
                            A DEFINIR
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Vendedor + Status -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <tr>
                <td style="width: 65%; background: #e0f2fe; border: 1px solid #0ea5e9; border-radius: 6px; text-align: center; padding: 5px 12px; font-weight: bold; color: #0369a1; font-size: 9px;">
                    VENDEDOR: {{ strtoupper($order->seller ?? 'N/A') }}
                </td>
                @if($order->stock_status)
                @php
                    $stockStyles = [
                        'total' => 'background: #dcfce7; border: 1px solid #22c55e; color: #166534;',
                        'partial' => 'background: #fef3c7; border: 1px solid #f59e0b; color: #92400e;',
                        'none' => 'background: #fee2e2; border: 1px solid #ef4444; color: #991b1b;',
                        'pending' => 'background: #f3f4f6; border: 1px solid #9ca3af; color: #4b5563;',
                    ];
                    $stockLabels = [
                        'total' => 'ESTOQUE TOTAL',
                        'partial' => 'ESTOQUE PARCIAL',
                        'none' => 'SEM ESTOQUE',
                        'pending' => 'VERIFICANDO',
                    ];
                @endphp
                <td style="width: 35%; {{ $stockStyles[$order->stock_status] ?? $stockStyles['pending'] }} border-radius: 6px; text-align: center; padding: 5px 12px; font-weight: bold; font-size: 9px;">
                    {{ $stockLabels[$order->stock_status] ?? 'PENDENTE' }}
                </td>
                @endif
            </tr>
        </table>
        
        <!-- Layout Principal -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- TAMANHOS -->
                <td style="width: 15%; vertical-align: top; padding-right: 8px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px;">
                        <div style="font-size: 8px; font-weight: bold; text-align: center; margin-bottom: 6px; text-transform: uppercase; color: #475569;">Tamanhos</div>
                        @php
                            $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                            $itemSizes = $itemSizes ?? [];
                            $sizeColors = [
                                'PP' => '#FF8C00', 'P' => '#FFD700', 'M' => '#4169E1', 
                                'G' => '#DC143C', 'GG' => '#32CD32', 'EXG' => '#8A2BE2',
                                'G1' => '#78909C', 'G2' => '#78909C', 'G3' => '#78909C',
                                'Especial' => '#E91E63', 'ESPECIAL' => '#E91E63'
                            ];
                            $sizeTextColors = ['P' => '#333333'];
                            $normalizedSizes = [];
                            foreach ($itemSizes as $sKey => $sQty) {
                                $normalizedKey = \Illuminate\Support\Str::upper(trim($sKey));
                                $normalizedSizes[$normalizedKey] = ($normalizedSizes[$normalizedKey] ?? 0) + intval($sQty);
                            }
                        @endphp
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                            @php $qty = $normalizedSizes[\Illuminate\Support\Str::upper($size)] ?? 0; @endphp
                            @if($qty > 0)
                            <div style="background: {{ $sizeColors[$size] ?? '#78909C' }}; color: {{ $sizeTextColors[$size] ?? 'white' }}; padding: 4px; margin-bottom: 4px; border-radius: 4px; text-align: center; font-weight: bold;">
                                <span style="font-size: 8px; display: block;">{{ $size }}</span>
                                <span style="font-size: 12px;">{{ $qty }}</span>
                            </div>
                            @endif
                        @endforeach
                        <div style="background: #6366f1; color: white; padding: 6px; margin-top: 6px; border-radius: 4px; text-align: center; font-weight: bold;">
                            <span style="font-size: 8px; display: block;">TOTAL</span>
                            <span style="font-size: 14px;">{{ $item->quantity }}</span>
                        </div>
                    </div>
                </td>
                
                <!-- IMAGEM DE CAPA -->
                <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                    <div style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px; text-align: center;">
                        <div style="font-size: 9px; color: #64748b; margin-bottom: 8px; text-transform: uppercase;">Imagem do Layout</div>
                        @php
                            $imageData = $itemImages[$item->id] ?? [];
                            $hasCoverImage = $imageData['hasCoverImage'] ?? false;
                            $coverImageUrl = $imageData['coverImageUrl'] ?? null;
                            $coverImageBase64 = $imageData['coverImageBase64'] ?? false;
                        @endphp
                        @if($hasCoverImage && $coverImageUrl)
                            @php
                                $imgSrc = $coverImageUrl;
                                if (!$coverImageBase64 && !str_starts_with($imgSrc, 'file://') && !str_starts_with($imgSrc, 'data:')) {
                                    $imgSrc = 'file://' . $imgSrc;
                                }
                            @endphp
                            <img src="{{ $imgSrc }}" alt="Capa" style="max-width: 100%; max-height: 250px; border-radius: 6px;">
                        @else
                            <div style="color: #94a3b8; font-size: 12px; padding: 60px 10px;">Sem imagem</div>
                        @endif
                    </div>

                    <!-- Alertas -->
                    @php
                        $model = strtolower($item->model ?? '');
                        $isInfantil = preg_match('/\binfantil\b/i', $model) || preg_match('/\bcrianca\b/i', $model);
                        $hasSleeveOrSide = false;
                        if (isset($item->sublimations) && $item->sublimations) {
                            foreach ($item->sublimations as $sub) {
                                if (isset($sub->location_id) && in_array($sub->location_id, [3, 4, 5, 6])) {
                                    $hasSleeveOrSide = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($isInfantil || $hasSleeveOrSide)
                    <div style="background: #fef3c7; border: 1px solid #f59e0b; border-left: 4px solid #f59e0b; border-radius: 4px; padding: 6px 10px; margin-top: 8px;">
                        <strong style="color: #92400e; font-size: 9px;">ATENCAO - COSTURA</strong>
                        @if($isInfantil)<p style="color: #92400e; font-size: 8px; margin: 2px 0 0 0;">PECA INFANTIL - CAMISA ABERTA PARA PERSONALIZACAO</p>@endif
                        @if($hasSleeveOrSide)<p style="color: #92400e; font-size: 8px; margin: 2px 0 0 0;">CAMISA ABERTA PARA PERSONALIZACAO EM MANGA/LATERAL</p>@endif
                    </div>
                    @endif
                </td>
                
                <!-- ESPECIFICAÇÕES + ADICIONAIS -->
                <td style="width: 35%; vertical-align: top;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-bottom: 8px;">
                        <div style="font-size: 8px; font-weight: bold; text-align: center; margin-bottom: 8px; text-transform: uppercase; color: #475569;">Especificações</div>
                        @php
                            $specs = [
                                'Tecido' => $item->fabric,
                                'Cor' => $item->color,
                                'Gola' => $item->collar,
                                'Modelo' => $item->model,
                                'Detalhe' => $item->detail,
                                'Estampa' => $item->print_type,
                            ];
                        @endphp
                        @foreach($specs as $label => $value)
                        <div style="background: #e2e8f0; border-radius: 4px; padding: 4px 8px; margin-bottom: 4px;">
                            <div style="font-size: 7px; color: #64748b; text-transform: uppercase;">{{ $label }}</div>
                            <div style="font-size: 9px; font-weight: bold; color: #1e293b;">{{ $value ? \Illuminate\Support\Str::upper($value) : 'N/A' }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div style="background: #ffffff; border: 1px solid #ef4444; border-radius: 8px; padding: 10px;">
                        <div style="font-size: 8px; font-weight: bold; text-align: center; margin-bottom: 8px; text-transform: uppercase; color: #ef4444;">Adicionais / Detalhes</div>
                        
                        @php
                            $printDesc = is_array($item->print_desc) ? $item->print_desc : (is_string($item->print_desc) ? json_decode($item->print_desc, true) : []);
                            $isClientModeling = $printDesc['is_client_modeling'] ?? false;
                        @endphp
                        @if($isClientModeling)
                            <div style="background: #fef08a; border: 1px solid #eab308; border-radius: 4px; padding: 4px; margin-bottom: 6px; color: #854d0e; font-size: 9px; font-weight: bold; text-align: center;">
                                MODELAGEM CLIENTE
                            </div>
                        @endif
                        
                        @if($item->collar_color)
                            <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 4px; padding: 4px; margin-bottom: 4px; color: #0369a1; font-size: 8px; font-weight: bold;">
                                GOLA: {{ strtoupper($item->collar_color) }}
                            </div>
                        @endif
                        @if($item->detail_color)
                            <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 4px; padding: 4px; margin-bottom: 4px; color: #0369a1; font-size: 8px; font-weight: bold;">
                                DETALHE: {{ strtoupper($item->detail_color) }}
                            </div>
                        @endif

                        @if($item->sublimation_addons)
                            @php
                                $addonIds = is_array($item->sublimation_addons) ? $item->sublimation_addons : json_decode($item->sublimation_addons, true);
                                $addonNames = \App\Models\SublimationProductAddon::whereIn('id', $addonIds)->pluck('name')->toArray();
                            @endphp
                            @foreach($addonNames as $addonName)
                                <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 4px; padding: 4px; margin-bottom: 4px; color: #991b1b; font-size: 8px; font-weight: bold;">
                                    + {{ strtoupper($addonName) }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Observacoes -->
        @if($item->art_notes || $order->notes)
        <div style="margin-top: 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px;">
            <div style="font-size: 8px; margin-bottom: 4px; text-transform: uppercase; color: #475569; font-weight: bold;">OBSERVACOES</div>
            <div style="background: #e2e8f0; border-radius: 4px; padding: 6px; font-size: 9px; color: #1e293b;">
                @if($item->art_notes)<strong>Item:</strong> {{ $item->art_notes }}@if($order->notes)<br>@endif @endif
                @if($order->notes)<strong>Pedido:</strong> {{ $order->notes }}@endif
            </div>
        </div>
        @endif
    </div>
    @endforeach
    <div style="text-align: center; font-size: 8px; color: #94a3b8; margin-top: 10px;">
        Impresso em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
