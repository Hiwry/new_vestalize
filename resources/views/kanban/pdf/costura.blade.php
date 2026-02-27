<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>OS {{ $order->id }} - Costura</title>
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
            font-size: 14px;
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
            height: 48.5%; /* Otimizado para caber 2 por pagina */
            margin-bottom: 2px;
            padding-bottom: 2px;
            border-bottom: 1px dashed #ccc;
            display: block;
            overflow: hidden;
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
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            background: #f1f5f9;
        }
        .size-badge {
            display: block;
            padding: 4px 8px;
            border-radius: 6px;
            margin-bottom: 4px;
            text-align: left;
            border-left: 5px solid transparent;
        }
        .size-qty {
            font-size: 18px;
            font-weight: bold;
            float: right;
            margin-top: -2px;
        }
        .size-label {
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>
    @foreach($order->items->chunk(2) as $chunk)
    <div class="page">
        @foreach($chunk as $item)
        <div class="item-container">
            <!-- Corpo com Borda e Margens Internas Compactas -->
            <div style="border: 2px solid #cbd5e1; border-radius: 8px; padding: 10px; margin-top: 2px; height: 98%;">
                
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
                            <strong>{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->subWeekdays(5)->format('d/m') : 'N/A' }}</strong>
                        </td>
                    </tr>
                </table>

                <table class="main-content" style="width: 100%;">
                    <tr>
                    <!-- Coluna Esquerda: Detalhes, Specs e Tamanhos (30%) -->
                    <td style="width: 30%; vertical-align: top;">
                        <!-- Especificações -->
                        <div class="box" style="margin-bottom: 5px;">
                            <div class="box-title">Especificações</div>
                            <table style="width: 100%; font-size: 12px;">
                                <tr><td style="color: #64748b;">Tecido:</td><td style="font-weight: bold; word-break: break-word;">{{ $item->fabric }}</td></tr>
                                <tr><td style="color: #64748b;">Cor:</td><td style="font-weight: bold; word-break: break-word;">{{ $item->color }}</td></tr>
                                <tr><td style="color: #64748b;">Gola:</td><td style="font-weight: bold;">{{ $item->collar }}</td></tr>
                                <tr><td style="color: #64748b;">Modelo:</td><td style="font-weight: bold;">{{ $item->model }}</td></tr>
                                @if($item->print_type)
                                <tr><td style="color: #64748b;">Tipo:</td><td style="font-weight: bold; color: #6366f1;">{{ $item->print_type }}</td></tr>
                                @endif
                            </table>
                        </div>

                        <!-- Adicionais -->
                        <div class="box" style="margin-bottom: 5px;">
                            <div class="box-title" style="color: #ef4444;">Detalhes</div>
                            @if($item->collar_color)<div style="font-size: 11px; color: #0369a1; margin-bottom: 2px;">• Gola: {{ $item->collar_color }}</div>@endif
                            @if($item->detail_color)<div style="font-size: 11px; color: #0369a1; margin-bottom: 2px;">• Detalhe: {{ $item->detail_color }}</div>@endif
                            
                            @php
                                $sublimationAddons = is_array($item->sublimation_addons) ? $item->sublimation_addons : json_decode($item->sublimation_addons, true);
                            @endphp
                            @if($sublimationAddons)
                                @php $addonNames = \App\Models\SublimationProductAddon::whereIn('id', $sublimationAddons)->pluck('name')->toArray(); @endphp
                                @foreach($addonNames as $name)
                                    <div style="font-size: 9px; color: #991b1b; background: #fee2e2; padding: 2px; border-radius: 3px; margin-bottom: 2px;">+ {{ $name }}</div>
                                @endforeach
                            @endif
                            @if(!$item->collar_color && !$item->detail_color && !$sublimationAddons)
                                <div style="color: #cbd5e1; font-size: 9px; text-align: center;">Nenhum detalhe extra</div>
                            @endif
                        </div>

                        <!-- Tamanhos -->
                        <div class="box" style="margin-bottom: 5px;">
                            <div class="box-title">Tamanhos (Total: {{ $item->quantity }})</div>
                            <div style="text-align: center;">
                                @php
                                    $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                                    $sizeOrder = ['PP','P','M','G','GG','EXG','G1','G2','G3','ESPECIAL'];
                                    $sortedSizes = [];
                                    foreach ($sizeOrder as $sizeKey) {
                                        if (isset($itemSizes[$sizeKey])) {
                                            $sortedSizes[$sizeKey] = $itemSizes[$sizeKey];
                                        }
                                    }
                                    foreach ($itemSizes as $sizeKey => $qty) {
                                        $normalized = strtoupper(trim($sizeKey));
                                        if (!array_key_exists($normalized, $sortedSizes)) {
                                            $sortedSizes[$normalized] = $qty;
                                        }
                                    }
                                    $sizeColors = [
                                        'PP' => '#FF8C00', 'P' => '#FFD700', 'M' => '#4169E1', 'G' => '#DC143C',
                                        'GG' => '#32CD32', 'EXG' => '#8A2BE2', 'G1' => '#78909C', 'ESPECIAL' => '#E91E63'
                                    ];
                                @endphp
                                @foreach($sortedSizes as $size => $qty)
                                    @php $size = strtoupper(trim($size)); @endphp
                                    @if($qty > 0)
                                    <div class="size-badge" style="background: {{ $sizeColors[$size] ?? '#78909C' }}; color: {{ $size === 'P' ? '#333' : 'white' }};">
                                        <span class="size-label">{{ $size }}</span>
                                        <span class="size-qty">{{ $qty }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                         <!-- Vendedor -->
                         <div style="font-size: 9px; color: #64748b; text-align: center;">
                            Vend: {{ strtoupper(\Illuminate\Support\Str::limit($order->seller, 15)) }}
                        </div>
                    </td>

                    <!-- Coluna Direita: Imagem Horizontal Grande (70%) -->
                    <td style="width: 70%; vertical-align: top;">
                         <div class="box" style="height: 380px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; overflow: hidden; position: relative;">
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
                                    <div style="font-size: 24px; margin-bottom: 10px;"></div>
                                    <div>SEM IMAGEM</div>
                                    <div style="font-size: 9px;">Adicione uma capa ao pedido ou ao item</div>
                                </div>
                            @endif
                        </div>
                        
                        @php
                            $autoNotes = [];
                            if($item->sublimations) {
                                foreach($item->sublimations as $sub) {
                                    $loc = $sub->location;
                                    if (!$loc && is_numeric($sub->location_id)) {
                                        $loc = \App\Models\SublimationLocation::find($sub->location_id);
                                    }
                                    
                                    if ($loc && $loc->show_in_pdf) {
                                        $type = $sub->application_type ? strtoupper($sub->application_type) : 'PERSONALIZAÇÃO';
                                        
                                        // Usar template personalizado se disponível
                                        if (!empty($loc->pdf_note)) {
                                            $note = str_replace(
                                                ['{LOCAL}', '{local}', '{TIPO}', '{tipo}'],
                                                [strtoupper($loc->name), $loc->name, $type, strtolower($type)],
                                                $loc->pdf_note
                                            );
                                            $autoNotes[] = $note;
                                        } else {
                                            $autoNotes[] = strtoupper($loc->name) . " ABERTA PARA " . $type;
                                        }
                                    }
                                }
                            }
                            $autoNotes = array_unique($autoNotes);
                        @endphp

                        @if(!empty($autoNotes) || $item->art_notes || $order->notes)
                        <div style="margin-top: 5px; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 4px; padding: 6px;">
                            <strong style="color: #b45309; font-size: 11px;">OBSERVAÇÕES:</strong>
                            <div style="font-size: 11px; color: #78350f; line-height: 1.4;">
                                @foreach($autoNotes as $autoNote)
                                    <div style="font-weight: bold; color: #dc2626;">• {{ $autoNote }}</div>
                                @endforeach
                                
                                {{ $item->art_notes }} 
                                @if($item->art_notes && $order->notes) | @endif 
                                {{ $order->notes }}
                            </div>
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
