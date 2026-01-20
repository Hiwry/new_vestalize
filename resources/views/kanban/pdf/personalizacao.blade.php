<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>OS {{ $order->id }} - Arte/Personalizacao</title>
    <style>
        @page {
            margin: 5mm;
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
    <div style="page-break-inside: avoid; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; padding: 10px; background: white;">
        
        <!-- Item Header -->
        <div style="background: #475569; color: white; padding: 6px 12px; border-radius: 6px; margin-bottom: 8px; font-size: 11px; font-weight: bold;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->quantity }} PEÇAS
        </div>
        
        <!-- Header Principal -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <!-- EVENTO -->
                <td style="width: 12%; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; text-align: center; vertical-align: middle; padding: 12px;">
                    @php
                        $contractType = isset($order->contract_type) ? \Illuminate\Support\Str::upper($order->contract_type) : null;
                    @endphp
                    <div style="font-size: 14px; font-weight: bold; color: {{ ($order->is_event || $contractType === 'EVENTO') ? '#dc3545' : '#495057' }};">
                        @if($order->is_event || $contractType === 'EVENTO')
                            EVENTO
                        @else
                            PEDIDO
                        @endif
                    </div>
                </td>
                
                <!-- NOME DA ARTE + OS -->
                <td style="width: 60%; background: #6366f1; border-radius: 8px; text-align: center; vertical-align: middle; padding: 15px; color: white;">
                    <div style="font-size: 24px; font-weight: bold; color: white; margin-bottom: 5px;">
                        {{ \Illuminate\Support\Str::upper($item->art_name ?? 'SEM NOME') }}
                    </div>
                    <div style="font-size: 16px; font-weight: bold; color: white; background: rgba(255,255,255,0.2); display: inline-block; padding: 4px 15px; border-radius: 20px;">
                        OS {{ $order->id }}
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
        
        <!-- Vendedor -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <td style="width: 100%; background: #e0f2fe; border: 1px solid #0ea5e9; border-radius: 6px; text-align: center; padding: 8px 15px; font-weight: bold; color: #0369a1;">
                    VENDEDOR: {{ strtoupper($order->seller ?? 'N/A') }}
                </td>
            </tr>
        </table>

        <!-- Especificacoes Compactas (Fita Azul Clara) -->
        @php
            $printDesc = is_array($item->print_desc) ? $item->print_desc : (is_string($item->print_desc) ? json_decode($item->print_desc, true) : []);
            $isClientModeling = $printDesc['is_client_modeling'] ?? false;
        @endphp
        <div style="background: #f1f5f9; padding: 4px 10px; margin-bottom: 6px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 9px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td><strong>Tecido:</strong> {{ strtoupper($item->fabric ?? 'N/A') }}</td>
                    <td><strong>Cor:</strong> {{ strtoupper($item->color ?? 'N/A') }}</td>
                    <td><strong>Gola:</strong> {{ strtoupper($item->collar ?? 'N/A') }}</td>
                    <td><strong>Modelo:</strong> {{ strtoupper($item->model ?? 'N/A') }}</td>
                    <td><strong>Estampa:</strong> {{ strtoupper($item->print_type ?? 'N/A') }}</td>
                    @if($isClientModeling)
                    <td style="background: #fef08a; border: 1px solid #eab308; padding: 2px 6px; border-radius: 4px; font-weight: bold; color: #854d0e;">ESPECIAL - MODELAGEM CLIENTE</td>
                    @endif
                </tr>
            </table>
        </div>

        <!-- Layout Central -->
        <div style="text-align: center; margin-bottom: 8px;">
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
                <img src="{{ $imgSrc }}" alt="Capa" style="max-height: 200px; max-width: 100%; border-radius: 8px; border: 1px solid #dee2e6;">
            @endif
        </div>

        <!-- APLICAÇÕES -->
        @if($item->sublimations && $item->sublimations->count() > 0)
        <div style="margin-bottom: 12px;">
            <div style="font-weight: bold; margin-bottom: 6px; font-size: 11px; color: #334155; border-left: 3px solid #6366f1; padding-left: 8px;">APLICAÇÕES:</div>
            
            @foreach($item->sublimations as $index => $sub)
            @php
                $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
            @endphp
            <table style="width: 100%; border-collapse: separate; border-spacing: 0 5px; margin-bottom: 8px;">
                <tr>
                    <!-- Imagem da PersonalizaÃ§Ã£o -->
                    <td style="width: 35%; vertical-align: top; text-align: center; padding-right: 15px;">
                        @if($sub->application_image && extension_loaded('gd'))
                            @php
                                $appImagePath = storage_path('app/public/' . $sub->application_image);
                                $appImageData = '';
                                if (file_exists($appImagePath)) {
                                    try {
                                        $imageInfo = @getimagesize($appImagePath);
                                        if ($imageInfo) {
                                            $sourceImage = null;
                                            if ($imageInfo['mime'] == 'image/jpeg') $sourceImage = @imagecreatefromjpeg($appImagePath);
                                            elseif ($imageInfo['mime'] == 'image/png') $sourceImage = @imagecreatefrompng($appImagePath);
                                            
                                            if ($sourceImage) {
                                                $width = imagesx($sourceImage);
                                                $height = imagesy($sourceImage);
                                                $ratio = min(180 / $width, 180 / $height);
                                                $newWidth = (int)($width * $ratio);
                                                $newHeight = (int)($height * $ratio);
                                                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                                                imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                                ob_start();
                                                imagejpeg($newImage, null, 85);
                                                $appImageData = 'data:image/jpeg;base64,' . base64_encode(ob_get_clean());
                                                imagedestroy($sourceImage);
                                                imagedestroy($newImage);
                                            }
                                        }
                                    } catch (\Exception $e) {}
                                }
                            @endphp
                            @if($appImageData)
                                <img src="{{ $appImageData }}" style="max-height: 140px; max-width: 100%; border-radius: 6px; border: 1px solid #e2e8f0; padding: 4px; background: white;">
                            @endif
                        @endif
                    </td>

                    <!-- Tabela de Dados da PersonalizaÃ§Ã£o -->
                    <td style="width: 65%; vertical-align: top;">
                        <div style="background-color: #7e22ce; color: white; padding: 6px 12px; border-radius: 6px; margin-bottom: 6px; font-size: 11px; font-weight: bold; text-align: center;">
                            APLICAÇÃO {{ $index + 1 }} - {{ $appType }}
                        </div>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f1f5f9;">
                                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #475569; font-size: 10px;">LOCAL</th>
                                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #475569; font-size: 10px;">TAMANHO</th>
                                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #475569; font-size: 10px;">QTD</th>
                                    <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #475569; font-size: 10px;">CORES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="border: 1px solid #e2e8f0; padding: 10px; background-color: #f0f9ff; color: #0284c7; font-weight: bold; font-size: 11px;">{{ strtoupper($locationName ?? '-') }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 10px; background-color: #faf5ff; color: #7e22ce; font-weight: bold; font-size: 11px;">{{ strtoupper($sizeName ?? '-') }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 10px; background-color: #f0fdf4; color: #166534; font-weight: bold; font-size: 11px;">{{ $sub->quantity }}</td>
                                    <td style="border: 1px solid #e2e8f0; padding: 10px; background-color: #fffaf1; color: #9a3412; font-weight: bold; font-size: 11px;">
                                        @if($sub->color_count > 0)
                                            {{ $sub->color_count }} CORES
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @if($sub->seller_notes)
                        <div style="margin-top: 6px; background: #fffbeb; border: 1px solid #fef3c7; padding: 6px 10px; border-radius: 4px; font-size: 10px; color: #92400e;">
                            <strong>Nota:</strong> {{ $sub->seller_notes }}
                        </div>
                        @endif
                    </td>
                </tr>
            </table>
            @if(!$loop->last) <hr style="border: none; border-top: 1px dashed #e2e8f0; margin: 10px 0;"> @endif
            @endforeach
        </div>
        @endif

        <!-- Tamanhos (Tabela Horizontal Colorida) -->
        <div style="margin-bottom: 12px;">
            <div style="font-weight: bold; margin-bottom: 6px; font-size: 11px; color: #334155;">Tamanhos</div>
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                <thead>
                    <tr>
                        @php
                            $sizeColors = [
                                'PP' => '#FF8C00', 'P' => '#FFD700', 'M' => '#4169E1', 
                                'G' => '#DC143C', 'GG' => '#32CD32', 'EXG' => '#8A2BE2',
                                'G1' => '#f1f5f9', 'G2' => '#f1f5f9', 'G3' => '#f1f5f9',
                                'ESPECIAL' => '#f1f5f9'
                            ];
                            $sizeTextColors = ['P' => '#333', 'G1' => '#475569', 'G2' => '#475569', 'G3' => '#475569', 'ESPECIAL' => '#475569'];
                        @endphp
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                        <th style="border: 1px solid #e2e8f0; padding: 6px; background-color: {{ $sizeColors[$size] }}; color: {{ $sizeTextColors[$size] ?? 'white' }}; font-size: 10px; text-align: center;">{{ $size }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @php
                            $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                            $normalizedSizes = [];
                            foreach ($itemSizes ?? [] as $sKey => $sQty) {
                                $normalizedKey = \Illuminate\Support\Str::upper(trim($sKey));
                                $normalizedSizes[$normalizedKey] = ($normalizedSizes[$normalizedKey] ?? 0) + intval($sQty);
                            }
                        @endphp
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                            @php $qty = $normalizedSizes[\Illuminate\Support\Str::upper($size)] ?? 0; @endphp
                            <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: center; font-size: 12px; font-weight: bold; background: white;">{{ $qty }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-top: none; padding: 6px 12px; font-weight: bold; display: table; width: 100%;">
                <div style="display: table-row;">
                    <div style="display: table-cell; font-size: 10px;">TOTAL</div>
                    <div style="display: table-cell; text-align: right; font-size: 14px;">{{ $item->quantity }}</div>
                </div>
            </div>
        </div>

        {{-- Observacoes apenas de personalizaÃ§Ã£o, nÃ£o de costura --}}
        @php
            $hasPersonalizationNotes = false;
            foreach($item->sublimations as $sub) {
                if ($sub->seller_notes) {
                    $hasPersonalizationNotes = true;
                    break;
                }
            }
        @endphp
        @if($hasPersonalizationNotes || $order->notes)
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px;">
            <div style="font-size: 9px; margin-bottom: 4px; text-transform: uppercase; color: #475569; font-weight: bold;">OBSERVAÃ‡Ã•ES</div>
            <div style="font-size: 10px; color: #1e293b; line-height: 1.3;">
                @foreach($item->sublimations as $sub)
                    @if($sub->seller_notes)
                        <strong>App {{ $loop->iteration }}:</strong> {{ $sub->seller_notes }}<br>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <div style="text-align: center; font-size: 8px; color: #94a3b8; margin-top: 20px;">
            Impresso em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
    @endforeach
</body>
</html>


