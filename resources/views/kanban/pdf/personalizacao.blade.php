<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} - Personalização</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 9px;
            line-height: 1.1;
            padding: 5px;
            margin: 0;
            width: 100%;
            background-color: #ffffff;
        }
        @page {
            size: A4;
            margin: 8mm;
        }
        .header {
            text-align: center;
            margin-bottom: 6px;
            border-bottom: 2px solid #EC4899;
            padding-bottom: 4px;
        }
        .header h1 {
            font-size: 14px;
            color: #EC4899;
            margin-bottom: 2px;
        }
        .header .subtitle {
            font-size: 10px;
            color: #666;
        }
        .section {
            margin-bottom: 6px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 3px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #EC4899;
            margin-bottom: 4px;
            padding-bottom: 2px;
            border-bottom: 2px solid #EC4899;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-top: 5px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 2px 8px 2px 0;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 2px 0;
        }
        .cover-image {
            text-align: center;
            margin: 6px 0;
        }
        .cover-image img {
            max-width: 100%;
            max-height: 120px;
            border: 1px solid #EC4899;
            border-radius: 3px;
        }
        .application-item {
            background-color: #F9FAFB;
            border-left: 2px solid #2c3e50;
            padding: 4px;
            margin-bottom: 4px;
        }
        .application-header {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
            font-size: 9px;
        }
        .application-details {
            font-size: 8px;
            color: #666;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-top: 3px;
            padding-top: 3px;
            border-top: 1px dashed #ddd;
            font-size: 8px;
        }
        .total-box {
            background-color: #FEF3C7;
            padding: 6px;
            margin-top: 6px;
            border-radius: 3px;
            border: 1px solid #F59E0B;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 9px;
        }
        .total-row.final {
            font-size: 12px;
            font-weight: bold;
            color: #059669;
            border-top: 1px solid #F59E0B;
            padding-top: 4px;
            margin-top: 3px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Informações da Empresa -->
    @if(isset($companySettings) && $companySettings)
    <div style="text-align: center; margin-bottom: 4px; padding: 4px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 3px;">
        @if($companySettings->logo_path && file_exists(public_path($companySettings->logo_path)))
        <div style="text-align: center; margin-bottom: 3px;">
            @php
                $logoPath = public_path($companySettings->logo_path);
                $imageData = base64_encode(file_get_contents($logoPath));
                $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
                $imageSrc = "data:image/{$imageType};base64,{$imageData}";
            @endphp
            <img src="{{ $imageSrc }}" alt="Logo" style="max-height: 40px; max-width: 150px; object-fit: contain;">
        </div>
        @endif
        @if($companySettings->company_name)
        <div style="font-size: 12px; font-weight: 700; color: #2c3e50; margin-bottom: 2px;">{{ $companySettings->company_name }}</div>
        @endif
    </div>
    @endif
    
    <!-- Cabeçalho Principal -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; padding: 4px; background-color: #f8f9fa; border: 1px solid #2c3e50; border-radius: 3px;">
        <div style="font-size: 12px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">OS {{ $order->id }}</div>
        <div style="text-align: center;">
            <div style="font-size: 11px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">
                @php
                    $firstItem = $order->items->first();
                    $firstArtName = $firstItem ? $firstItem->art_name : null;
                @endphp
                {{ $firstArtName ?? 'SEM NOME' }}
            </div>
        </div>
        <div style="font-size: 9px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">
            @if($order->delivery_date)
                {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                @if($order->is_event || (isset($order->contract_type) && strtoupper($order->contract_type) === 'EVENTO'))
                    - <span style="color: #FF6B35;">🎉 EVENTO</span>
                @endif
            @else
                SEM DATA
            @endif
        </div>
    </div>

    <!-- Nome do Vendedor -->
    <div style="margin-bottom: 4px; padding: 3px; background-color: #e3f2fd; border: 1px solid #2196f3; border-radius: 3px; text-align: center;">
        <div style="font-size: 10px; font-weight: 600; color: #1976d2; font-family: 'Montserrat', sans-serif;">
            VENDEDOR: {{ $order->seller }}
        </div>
    </div>



    <!-- Itens do Pedido -->
    @foreach($order->items as $item)
    <div style="margin-bottom: 4px; background-color: #ffffff; border: 1px solid #e9ecef; padding: 3px; border-radius: 3px;">
        <div style="background-color: #f8f9fa; color: #2c3e50; padding: 2px 3px; margin: -3px -3px 3px -3px; border-radius: 3px 3px 0 0; font-size: 9px; font-weight: bold; border-bottom: 1px solid #e9ecef;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->quantity }} peças
        </div>

        {{-- Especificações Compactas do Item --}}
        <div style="background-color: #f1f5f9; padding: 3px; margin-bottom: 4px; border-radius: 2px; font-size: 7px; display: flex; justify-content: space-between;">
            <span><strong>Tecido:</strong> {{ $item->fabric ?? 'N/A' }}</span>
            <span><strong>Cor:</strong> {{ $item->color ?? 'N/A' }}</span>
            <span><strong>Gola:</strong> {{ $item->collar ?? 'N/A' }}</span>
            <span><strong>Modelo:</strong> {{ $item->model ?? 'N/A' }}</span>
            <span><strong>Estampa:</strong> {{ $item->print_type ?? 'N/A' }}</span>
        </div>

        <div style="padding: 3px;">
            <!-- Imagem de Capa -->
            @php
                // Acessar dados da imagem do array separado (evita conflito com accessors)
                $imageData = $itemImages[$item->id] ?? [];
                $hasCoverImage = $imageData['hasCoverImage'] ?? false;
                $coverImageInfo = $imageData['coverImageInfo'] ?? null;
                $coverImageUrl = $imageData['coverImageUrl'] ?? null;
                $coverImageBase64 = $imageData['coverImageBase64'] ?? false;
            @endphp
            @if($hasCoverImage && $coverImageInfo && $coverImageUrl)
            <div style="text-align: center; background-color: white; margin-bottom: 3px; padding: 2px; border: 1px solid #e9ecef; border-radius: 3px;">
                @php
                    $imgSrc = $coverImageUrl;
                    // Se não for base64, adicionar file://
                    if (!$coverImageBase64) {
                        if (!str_starts_with($imgSrc, 'file://') && !str_starts_with($imgSrc, 'data:')) {
                            $imgSrc = 'file://' . $imgSrc;
                        }
                    }
                @endphp
                <img src="{{ $imgSrc }}" alt="Capa" style="max-width: 220px; max-height: 140px; border-radius: 4px; margin-bottom: 3px; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">
            </div>
            @endif

            <!-- Aplicações -->
            @if($item->sublimations && $item->sublimations->count() > 0)
            <div style="margin-bottom: 4px;">
                <div style="font-weight: bold; margin-bottom: 2px; font-size: 10px; color: #2c3e50;">APLICAÇÕES:</div>
                
                @foreach($item->sublimations as $index => $sub)
                @php
                    // Buscar nome do tamanho
                    $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                    $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                    
                    // Buscar nome do local corretamente
                    $locationName = '-';
                    if ($sub->location) {
                        $locationName = $sub->location->name;
                    } elseif ($sub->location_id) {
                        $location = \App\Models\SublimationLocation::find($sub->location_id);
                        $locationName = $location ? $location->name : $sub->location_name;
                    } elseif ($sub->location_name) {
                        // Se location_name é um número, tentar buscar pelo ID
                        if (is_numeric($sub->location_name)) {
                            $location = \App\Models\SublimationLocation::find($sub->location_name);
                            $locationName = $location ? $location->name : 'Local ' . $sub->location_name;
                        } else {
                            $locationName = $sub->location_name;
                        }
                    }
                    
                    // Tipo de personalização
                    $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                @endphp
                <div style="display: table; width: 100%; margin-bottom: 4px; background-color: #F9FAFB; border-left: 2px solid #2c3e50; padding: 4px;">
                    <div style="display: table-row;">
                        <!-- Imagem da Aplicação -->
                        @if($sub->application_image && extension_loaded('gd'))
                        <div style="display: table-cell; width: 40%; vertical-align: top; text-align: center; padding-right: 6px;">
                            @php
                                $appImagePath = storage_path('app/public/' . $sub->application_image);
                                $appImageData = '';
                                
                                if (file_exists($appImagePath)) {
                                    try {
                                        $imageInfo = @getimagesize($appImagePath);
                                        if ($imageInfo) {
                                            $maxWidth = 140;
                                            $maxHeight = 140;
                                            
                                            // Carregar imagem original
                                            $sourceImage = null;
                                            if ($imageInfo['mime'] == 'image/jpeg') {
                                                $sourceImage = @imagecreatefromjpeg($appImagePath);
                                            } elseif ($imageInfo['mime'] == 'image/png') {
                                                $sourceImage = @imagecreatefrompng($appImagePath);
                                            } elseif ($imageInfo['mime'] == 'image/gif') {
                                                $sourceImage = @imagecreatefromgif($appImagePath);
                                            }
                                            
                                            if ($sourceImage) {
                                                $width = imagesx($sourceImage);
                                                $height = imagesy($sourceImage);
                                                
                                                // Calcular proporções
                                                $ratio = min($maxWidth / $width, $maxHeight / $height);
                                                $newWidth = (int)($width * $ratio);
                                                $newHeight = (int)($height * $ratio);
                                                
                                                // Criar imagem redimensionada
                                                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                                                imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                                
                                                // Converter para base64
                                                ob_start();
                                                imagejpeg($newImage, null, 85);
                                                $imageContent = ob_get_clean();
                                                
                                                $appImageData = 'data:image/jpeg;base64,' . base64_encode($imageContent);
                                                
                                                imagedestroy($sourceImage);
                                                imagedestroy($newImage);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Silenciar erro
                                    }
                                }
                            @endphp
                            @if($appImageData)
                                <img src="{{ $appImageData }}" alt="Aplicação {{ $index + 1 }}" 
                                     style="max-width: 140px; max-height: 140px; border: 1px solid #e9ecef; border-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">
                            @endif
                        </div>
                        @endif
                        
                        <!-- Tabela de Informações da Aplicação -->
                        <div style="display: table-cell; width: 60%; vertical-align: top;">
                            <div style="background-color: #7b1fa2; color: white; padding: 3px 6px; margin-bottom: 4px; border-radius: 3px; font-size: 9px; font-weight: bold; text-align: center;">
                                APLICAÇÃO {{ $index + 1 }} - {{ $appType }}
                            </div>
                            
                            <table style="width: 100%; border-collapse: collapse; font-size: 8px;">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="border: 1px solid #dee2e6; padding: 3px; text-align: left; font-weight: bold; color: #495057; font-size: 8px;">LOCAL</th>
                                        <th style="border: 1px solid #dee2e6; padding: 3px; text-align: left; font-weight: bold; color: #495057; font-size: 8px;">TAMANHO</th>
                                        <th style="border: 1px solid #dee2e6; padding: 3px; text-align: left; font-weight: bold; color: #495057; font-size: 8px;">QTD</th>
                                        <th style="border: 1px solid #dee2e6; padding: 3px; text-align: left; font-weight: bold; color: #495057; font-size: 8px;">CORES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid #dee2e6; padding: 3px; background-color: #e3f2fd; color: #1976d2; font-weight: 600; font-size: 8px;">
                                            {{ $locationName }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 3px; background-color: #f3e5f5; color: #7b1fa2; font-weight: 600; font-size: 8px;">
                                            {{ $sizeName ?? '-' }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 3px; background-color: #e8f5e8; color: #388e3c; font-weight: 600; font-size: 8px;">
                                            {{ $sub->quantity }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 3px; background-color: #fff3e0; color: #f57c00; font-weight: 600; font-size: 8px;">
                                            @if($sub->color_count > 0)
                                                {{ $sub->color_count }} cor{{ $sub->color_count > 1 ? 'es' : '' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
            @endif

            <!-- Tamanhos -->
            <div style="margin-bottom: 6px;">
                <div style="font-weight: 600; margin-bottom: 3px; font-size: 11px; color: #2c3e50; font-family: 'Montserrat', sans-serif;">Tamanhos</div>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #e9ecef; margin-bottom: 3px; border-radius: 3px; overflow: hidden;">
                    <thead>
                        <tr>
                            @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                            @php
                                $sizeColors = [
                                    'PP' => '#FF8C00',      // LARANJA
                                    'P' => '#FFD700',       // AMARELO
                                    'M' => '#4169E1',       // AZUL
                                    'G' => '#DC143C',       // VERMELHO
                                    'GG' => '#32CD32',      // VERDE
                                    'EXG' => '#8A2BE2',     // ROXO
                                    'G1' => '#FFFFFF',      // BRANCO
                                    'G2' => '#FFFFFF',      // BRANCO
                                    'G3' => '#FFFFFF',      // BRANCO
                                    'ESPECIAL' => '#FFFFFF' // BRANCO
                                ];
                                $backgroundColor = $sizeColors[$size] ?? '#f8f9fa';
                            @endphp
                            <th style="border: 1px solid #e9ecef; padding: 4px; text-align: center; font-size: 8px; font-weight: 600; color: #000000; background-color: {{ $backgroundColor }}; font-family: 'Montserrat', sans-serif;">{{ $size }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @php
                                // Garantir que sizes seja um array
                                $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                                $itemSizes = $itemSizes ?? [];
                            @endphp
                            @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                            @php
                                $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? 0;
                            @endphp
                            <td style="border: 1px solid #e9ecef; padding: 4px; text-align: center; font-size: 9px; font-weight: 700; background-color: #ffffff; color: #000000; font-family: 'Montserrat', sans-serif;">{{ $qty }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
                
                <div style="display: flex; justify-content: space-between; align-items: center; background-color: #f8f9fa; color: #2c3e50; padding: 4px; border-radius: 3px; border: 1px solid #e9ecef;">
                    <div style="font-size: 9px; font-weight: 600; font-family: 'Montserrat', sans-serif;">TOTAL</div>
                    <div style="font-size: 11px; font-weight: 700; font-family: 'Montserrat', sans-serif;">{{ $item->quantity }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach



    <!-- Observações -->
    @if($order->notes)
    <div style="margin-top: 4px; border: 1px solid #ddd; padding: 4px; border-radius: 3px;">
        <div style="font-size: 10px; font-weight: bold; color: #EC4899; margin-bottom: 2px;">OBSERVAÇÕES</div>
        <div style="padding: 3px; background-color: #FEF3C7; border-left: 3px solid #F59E0B; font-size: 8px;">
            {{ $order->notes }}
        </div>
    </div>
    @endif

    <div class="footer">
        Impresso em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
