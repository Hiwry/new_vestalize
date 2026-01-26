<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>OS {{ $order->id }} - Arte/Personalizacao</title>
    <style>
        @page {
            margin: 20mm;
            size: A4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .page {
            width: 100%;
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: auto;
        }
        .item-container {
            width: 100%;
            height: 48%; /* Tenta ocupar metade da area util */
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 2px dashed #ccc;
            display: block;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header-cell {
            padding: 5px;
            vertical-align: middle;
            text-align: center;
            border-radius: 4px;
        }
        .main-content {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
            height: 85%;
        }
        .box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px;
        }
        .box-title {
            font-size: 11px;
            font-weight: bold;
            color: #475569;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }
        .size-badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 4px;
            margin: 1px;
            text-align: center;
            min-width: 25px;
        }
        .size-qty {
            font-size: 14px;
            font-weight: bold;
            display: block;
        }
        .size-label {
            font-size: 10px;
            display: block;
        }
    </style>
</head>
<body>
    @foreach($order->items->chunk(2) as $chunk)
    <div class="page">
        @foreach($chunk as $item)
        <div class="item-container">
            <!-- Corpo com Borda e Margens Internas -->
            <div style="border: 2px solid #cbd5e1; border-radius: 8px; padding: 15px; margin-top: 5px; height: 95%;">
                
                <!-- Header Compacto -->
                <table class="header-table">
                    <tr>
                        <td style="width: 15%; background: #475569; color: white;" class="header-cell">
                            <strong>ITEM {{ $item->item_number ?? $loop->parent->iteration * 2 - (2 - $loop->iteration) }}</strong>
                        </td>
                        <td style="width: 55%; background: #6366f1; color: white;" class="header-cell">
                            <span style="font-size: 16px; font-weight: bold;">{{ \Illuminate\Support\Str::upper($item->art_name ?? 'SEM NOME') }}</span>
                            <span style="background: rgba(255,255,255,0.2); padding: 1px 6px; border-radius: 10px; font-size: 10px; margin-left: 5px;">OS {{ $order->id }}</span>
                        </td>
                        <td style="width: 15%; background: #f1f5f9;" class="header-cell">
                            @php
                                $contractType = isset($order->contract_type) ? \Illuminate\Support\Str::upper($order->contract_type) : null;
                            @endphp
                            <strong style="color: {{ ($order->is_event || $contractType === 'EVENTO') ? '#dc3545' : '#475569' }};">
                                {{ ($order->is_event || $contractType === 'EVENTO') ? 'EVENTO' : 'PEDIDO' }}
                            </strong>
                        </td>
                        <td style="width: 15%; background: #f1f5f9;" class="header-cell">
                            <div style="font-size: 8px; color: #64748b;">ENTREGA</div>
                            <strong>{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m') : 'N/A' }}</strong>
                        </td>
                    </tr>
                </table>

                <table class="main-content" style="width: 100%; height: 85%;">
                    <tr>
                    <!-- Coluna Esquerda: Detalhes da Personalização (30%) -->
                    <td style="width: 30%; vertical-align: top;">
                        <!-- Especificações Básicas -->
                        <div class="box" style="margin-bottom: 5px;">
                            <div class="box-title">Geral</div>
                            <table style="width: 100%; font-size: 12px;">
                                <tr><td style="color: #64748b;">Qtd:</td><td style="font-weight: bold;">{{ $item->quantity }}</td></tr>
                                <tr><td style="color: #64748b;">Tipo:</td><td style="font-weight: bold;">{{ $item->print_type }}</td></tr>
                                <tr><td style="color: #64748b;">Local:</td><td style="font-weight: bold;">{{ strtoupper($item->color) }}</td></tr>
                            </table>
                        </div>

                        <!-- Aplicações (Sublimations) -->
                        <div class="box" style="margin-bottom: 5px;">
                            <div class="box-title" style="color: #6366f1;">Aplicações</div>
                            @if($item->sublimations && $item->sublimations->count() > 0)
                                @foreach($item->sublimations as $sub)
                                    @php
                                        $locationName = '-';
                                        if ($sub->location) {
                                            $locationName = $sub->location->name;
                                        } elseif ($sub->location_name) {
                                            // Se for numérico, tentar buscar o nome
                                            if (is_numeric($sub->location_name)) {
                                                $locModel = \App\Models\SublimationLocation::find($sub->location_name);
                                                $locationName = $locModel ? $locModel->name : $sub->location_name;
                                            } else {
                                                $locationName = $sub->location_name;
                                            }
                                        }

                                        $sizeName = $sub->size ? $sub->size->name : ($sub->size_name ?? '-');
                                        $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                                    @endphp
                                    <div style="border-bottom: 1px dashed #cbd5e1; padding-bottom: 4px; margin-bottom: 4px;">
                                        <div style="font-weight: bold; color: #4338ca; font-size: 11px;">#{{ $loop->iteration }} {{ $appType }}</div>
                                        <div style="font-size: 10px; color: #1e293b;">Local: {{ $locationName }}</div>
                                        <div style="font-size: 10px; color: #1e293b;">Tam: {{ $sizeName }} | Qtd: {{ $sub->quantity }}</div>
                                        @if($sub->color_count)<div style="font-size: 10px; color: #c026d3;">Cores: {{ $sub->color_count }}</div>@endif
                                        @if($sub->seller_notes)<div style="font-size: 9px; color: #b45309; background: #fffbeb;">Obs: {{ $sub->seller_notes }}</div>@endif
                                    </div>
                                @endforeach
                            @else
                                <div style="color: #cbd5e1; font-size: 9px; text-align: center;">Sem aplicações extras</div>
                            @endif
                        </div>

                         <!-- Vendedor -->
                         <div style="font-size: 9px; color: #64748b; text-align: center;">
                            Vend: {{ strtoupper(\Illuminate\Support\Str::limit($order->seller, 15)) }}
                        </div>
                    </td>

                    <!-- Coluna Direita: Imagem Horizontal Grande (70%) -->
                    <td style="width: 70%; vertical-align: top;">
                         <div class="box" style="height: 340px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; overflow: hidden; position: relative;">
                             @php
                                $imageData = $itemImages[$item->id] ?? [];
                                $coverImageUrl = $imageData['coverImageUrl'] ?? null;
                                // Fallback para imagem do pedido se item não tiver imagem
                                if (!$coverImageUrl && isset($orderCoverImage) && $orderCoverImage['hasCoverImage']) {
                                    $coverImageUrl = $orderCoverImage['coverImageUrl'];
                                }
                            @endphp
                            
                            @if($coverImageUrl)
                                <img src="{{ $coverImageUrl }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            @else
                                <div style="color: #cbd5e1; padding: 20px;">
                                    <div style="font-size: 24px; margin-bottom: 10px;">🖼️</div>
                                    <div>SEM IMAGEM</div>
                                    <div style="font-size: 9px;">Adicione uma capa ao pedido ou ao item</div>
                                </div>
                            @endif
                        </div>
                        
                        @php
                            $hasPersonalizationNotes = false;
                            if($item->sublimations) {
                                foreach($item->sublimations as $sub) {
                                    if ($sub->seller_notes) {
                                        $hasPersonalizationNotes = true;
                                        break;
                                    }
                                }
                            }
                        @endphp

                        @if($hasPersonalizationNotes || $order->notes)
                        <div style="margin-top: 5px; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 4px; padding: 6px;">
                            <strong style="color: #b45309; font-size: 9px;">OBSERVAÇÕES:</strong>
                            <span style="font-size: 9px; color: #78350f;">
                                {{ $order->notes }}
                            </span>
                        </div>
                        @endif
                    </td>
                </tr>
            </table>
            </div> <!-- Fim do Corpo com Borda -->
        </div>
        @endforeach
    </div>
    @endforeach
</body>
</html>
