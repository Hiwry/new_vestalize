<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>OS {{ $order->id }} - Costura</title>
    <style>
        @page {
            margin: 8mm;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
        }
    </style>
</head>
<body>
    @foreach($order->items as $item)
    <div style="page-break-after: {{ $loop->last ? 'auto' : 'always' }}; padding: 5px;">
        
        <!-- Item Header -->
        <div style="background: #475569; color: white; padding: 6px 12px; border-radius: 6px; margin-bottom: 8px; font-size: 11px; font-weight: bold;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->quantity }} PECAS
        </div>
        
        <!-- Header Principal -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <!-- EVENTO -->
                <td style="width: 12%; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; text-align: center; vertical-align: middle; padding: 12px;">
                    <div style="font-size: 14px; font-weight: bold; color: {{ ($order->is_event || (isset($order->contract_type) && strtoupper($order->contract_type) === 'EVENTO')) ? '#dc3545' : '#495057' }};">
                        @if($order->is_event || (isset($order->contract_type) && strtoupper($order->contract_type) === 'EVENTO'))
                            EVENTO
                        @else
                            PEDIDO
                        @endif
                    </div>
                </td>
                
                <!-- NOME DA ARTE + OS -->
                <td style="width: 60%; background: #6366f1; border-radius: 8px; text-align: center; vertical-align: middle; padding: 15px; color: white;">
                    <div style="font-size: 24px; font-weight: bold; color: white; margin-bottom: 5px;">
                        {{ strtoupper($item->art_name ?? 'SEM NOME') }}
                    </div>
                    <div style="font-size: 16px; font-weight: bold; color: white; background: rgba(255,255,255,0.2); display: inline-block; padding: 4px 15px; border-radius: 20px;">
                        OS {{ $order->id }}
                    </div>
                    <div style="font-size: 9px; color: rgba(255,255,255,0.8); margin-top: 5px;">
                        Impresso em {{ now()->format('d/m/Y H:i') }}
                    </div>
                </td>
                
                <!-- DATA DE ENTREGA -->
                <td style="width: 14%; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; text-align: center; vertical-align: middle; padding: 12px;">
                    <div style="font-size: 9px; color: #6c757d; text-transform: uppercase;">Data de Entrega</div>
                    <div style="font-size: 18px; font-weight: bold; color: #212529;">
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
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <td style="width: 70%; background: #e0f2fe; border: 1px solid #0ea5e9; border-radius: 6px; text-align: center; padding: 8px 15px; font-weight: bold; color: #0369a1;">
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
                <td style="width: 30%; {{ $stockStyles[$order->stock_status] ?? $stockStyles['pending'] }} border-radius: 6px; text-align: center; padding: 8px 15px; font-weight: bold;">
                    {{ $stockLabels[$order->stock_status] ?? 'PENDENTE' }}
                </td>
                @endif
            </tr>
        </table>
        
        <!-- Layout Principal -->
        <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0;">
            <tr>
                <!-- TAMANHOS -->
                <td style="width: 10%; vertical-align: top;">
                    <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; padding: 10px;">
                        <div style="font-size: 10px; font-weight: bold; text-align: center; margin-bottom: 8px; text-transform: uppercase; color: #475569;">Tamanhos</div>
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
                        @endphp
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                            @php $qty = $itemSizes[$size] ?? $itemSizes[strtoupper($size)] ?? $itemSizes[strtolower($size)] ?? 0; @endphp
                            @if($qty > 0)
                            <div style="background: {{ $sizeColors[$size] ?? '#78909C' }}; color: {{ $sizeTextColors[$size] ?? 'white' }}; padding: 6px 8px; margin-bottom: 5px; border-radius: 6px; text-align: center; font-weight: bold;">
                                <span style="font-size: 9px; display: block;">{{ $size }}</span>
                                <span style="font-size: 14px;">{{ $qty }}</span>
                            </div>
                            @endif
                        @endforeach
                        <div style="background: #6366f1; color: white; padding: 8px; margin-top: 8px; border-radius: 6px; text-align: center; font-weight: bold;">
                            <span style="font-size: 10px; display: block;">TOTAL</span>
                            <span style="font-size: 18px;">{{ $item->quantity }}</span>
                        </div>
                    </div>
                </td>
                
                <!-- IMAGEM DE CAPA -->
                <td style="width: 58%; vertical-align: top;">
                    <div style="background: #f1f5f9; border: 2px solid #cbd5e1; border-radius: 12px; padding: 15px; text-align: center; min-height: 340px;">
                        <div style="font-size: 11px; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">Imagem de Capa do Layout</div>
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
                            <img src="{{ $imgSrc }}" alt="Capa" style="max-width: 100%; max-height: 300px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                        @else
                            <div style="color: #94a3b8; font-size: 14px; padding: 120px 20px;">Sem imagem de capa</div>
                        @endif
                    </div>
                </td>
                
                <!-- ESPECIFICACOES -->
                <td style="width: 17%; vertical-align: top;">
                    <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                        <div style="font-size: 10px; font-weight: bold; text-align: center; margin-bottom: 10px; text-transform: uppercase; color: #475569;">Especificacoes</div>
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
                        <div style="background: #e2e8f0; border-radius: 6px; padding: 6px 10px; margin-bottom: 6px;">
                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;">{{ $label }}</div>
                            <div style="font-size: 11px; font-weight: bold; color: #1e293b;">{{ $value ? strtoupper($value) : 'N/A' }}</div>
                        </div>
                        @endforeach
                    </div>
                </td>

                <!-- ADICIONAIS -->
                <td style="width: 15%; vertical-align: top;">
                    <div style="background: #ffffff; border: 2px solid #ef4444; border-radius: 10px; padding: 12px;">
                        <div style="font-size: 10px; font-weight: bold; text-align: center; margin-bottom: 10px; text-transform: uppercase; color: #ef4444;">Adicionais / Detalhes</div>
                        
                        {{-- Modelagem do Cliente --}}
                        @php
                            $printDesc = is_array($item->print_desc) ? $item->print_desc : (is_string($item->print_desc) ? json_decode($item->print_desc, true) : []);
                            $isClientModeling = $printDesc['is_client_modeling'] ?? false;
                        @endphp
                        @if($isClientModeling)
                            <div style="background: #fef08a; border: 2px solid #eab308; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; color: #854d0e; font-size: 11px; font-weight: bold; text-align: center;">
                                ESPECIAL - MODELAGEM DO CLIENTE
                            </div>
                        @endif
                        
                        {{-- Cores Adicionais (Gola/Detalhe) --}}
                        @if($item->collar_color)
                            <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px; padding: 6px 10px; margin-bottom: 5px; color: #0369a1; font-size: 10px; font-weight: bold;">
                                GOLA: {{ strtoupper($item->collar_color) }}
                            </div>
                        @endif
                        @if($item->detail_color)
                            <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px; padding: 6px 10px; margin-bottom: 5px; color: #0369a1; font-size: 10px; font-weight: bold;">
                                DETALHE: {{ strtoupper($item->detail_color) }}
                            </div>
                        @endif

                        {{-- Adicionais do Item (Sublimação Total) --}}
                        @if($item->sublimation_addons)
                            @php
                                $addonIds = is_array($item->sublimation_addons) ? $item->sublimation_addons : json_decode($item->sublimation_addons, true);
                                $addonNames = \App\Models\SublimationProductAddon::whereIn('id', $addonIds)->pluck('name')->toArray();
                            @endphp
                            @foreach($addonNames as $addonName)
                                <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; padding: 6px 10px; margin-bottom: 5px; color: #991b1b; font-size: 10px; font-weight: bold;">
                                    + {{ strtoupper($addonName) }}
                                </div>
                            @endforeach
                        @endif

                        {{-- Detalhes das Sublimações Individuais (Personalização Local/PDV) --}}
                        @foreach($item->sublimations as $sub)
                            @if($sub->application_type === 'sub. total' && $sub->addons)
                                @php
                                    $subAddons = is_array($sub->addons) ? $sub->addons : json_decode($sub->addons, true);
                                @endphp
                                @if($subAddons)
                                    @foreach($subAddons as $subAddon)
                                        <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; padding: 6px 10px; margin-bottom: 5px; color: #991b1b; font-size: 10px; font-weight: bold;">
                                            + {{ strtoupper(str_replace('_', ' ', $subAddon)) }}
                                        </div>
                                    @endforeach
                                @endif
                                @if($sub->regata_discount)
                                    <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; padding: 6px 10px; margin-bottom: 5px; color: #991b1b; font-size: 10px; font-weight: bold;">
                                        + REGATA
                                    </div>
                                @endif
                            @endif
                        @endforeach

                        @if(!$isClientModeling && !$item->collar_color && !$item->detail_color && !$item->sublimation_addons && $item->sublimations->whereNotNull('color_details')->isEmpty() && $item->sublimations->whereNotNull('seller_notes')->isEmpty() && $item->sublimations->where('application_type', 'sub. total')->whereNotNull('addons')->isEmpty())
                            <div style="color: #94a3b8; font-size: 10px; text-align: center; padding-top: 50px;">Nenhum detalhe adicional</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        
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
        <div style="background: #fef3c7; border: 2px solid #f59e0b; border-left: 6px solid #f59e0b; border-radius: 6px; padding: 10px 15px; margin-top: 8px;">
            <strong style="color: #92400e; font-size: 11px;">ATENCAO - COSTURA</strong>
            @if($isInfantil)<p style="color: #92400e; font-size: 10px; margin: 3px 0 0 0;">PECA INFANTIL - CAMISA ABERTA PARA PERSONALIZACAO</p>@endif
            @if($hasSleeveOrSide)<p style="color: #92400e; font-size: 10px; margin: 3px 0 0 0;">CAMISA ABERTA PARA PERSONALIZACAO EM MANGA/LATERAL</p>@endif
        </div>
        @endif
        
        <!-- Observacoes -->
        @if($item->art_notes || $order->notes)
        <div style="margin-top: 8px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 12px 15px;">
            <div style="font-size: 10px; margin-bottom: 6px; text-transform: uppercase; color: #475569; font-weight: bold;">OBSERVACOES</div>
            <div style="background: #e2e8f0; border-radius: 6px; padding: 10px; font-size: 11px; color: #1e293b;">
                @if($item->art_notes)<strong>Item:</strong> {{ $item->art_notes }}@if($order->notes)<br>@endif @endif
                @if($order->notes)<strong>Pedido:</strong> {{ $order->notes }}@endif
            </div>
        </div>
        @endif
    </div>
    @endforeach
</body>
</html>
