<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>OS <?php echo e($order->id); ?> - Costura</title>
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
    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div style="page-break-after: <?php echo e($loop->last ? 'auto' : 'always'); ?>; padding: 5px;">
        
        <!-- Item Header -->
        <div style="background: #475569; color: white; padding: 6px 12px; border-radius: 6px; margin-bottom: 8px; font-size: 11px; font-weight: bold;">
            ITEM <?php echo e($item->item_number ?? $loop->iteration); ?> - <?php echo e($item->quantity); ?> PECAS
        </div>
        
        <!-- Header Principal -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <!-- EVENTO -->
                <td style="width: 12%; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; text-align: center; vertical-align: middle; padding: 12px;">
                    <div style="font-size: 14px; font-weight: bold; color: <?php echo e(($order->is_event || (isset($order->contract_type) && strtoupper($order->contract_type) === 'EVENTO')) ? '#dc3545' : '#495057'); ?>;">
                        <?php if($order->is_event || (isset($order->contract_type) && strtoupper($order->contract_type) === 'EVENTO')): ?>
                            EVENTO
                        <?php else: ?>
                            PEDIDO
                        <?php endif; ?>
                    </div>
                </td>
                
                <!-- NOME DA ARTE + OS -->
                <td style="width: 60%; background: #6366f1; border-radius: 8px; text-align: center; vertical-align: middle; padding: 15px; color: white;">
                    <div style="font-size: 24px; font-weight: bold; color: white; margin-bottom: 5px;">
                        <?php echo e(strtoupper($item->art_name ?? 'SEM NOME')); ?>

                    </div>
                    <div style="font-size: 16px; font-weight: bold; color: white; background: rgba(255,255,255,0.2); display: inline-block; padding: 4px 15px; border-radius: 20px;">
                        OS <?php echo e($order->id); ?>

                    </div>
                    <div style="font-size: 9px; color: rgba(255,255,255,0.8); margin-top: 5px;">
                        Impresso em <?php echo e(now()->format('d/m/Y H:i')); ?>

                    </div>
                </td>
                
                <!-- DATA DE ENTREGA -->
                <td style="width: 14%; background: #f8f9fa; border: 2px solid #dee2e6; border-radius: 8px; text-align: center; vertical-align: middle; padding: 12px;">
                    <div style="font-size: 9px; color: #6c757d; text-transform: uppercase;">Data de Entrega</div>
                    <div style="font-size: 18px; font-weight: bold; color: #212529;">
                        <?php if($order->delivery_date): ?>
                            <?php echo e(\Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y')); ?>

                        <?php else: ?>
                            A DEFINIR
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Vendedor + Status -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <td style="width: 70%; background: #e0f2fe; border: 1px solid #0ea5e9; border-radius: 6px; text-align: center; padding: 8px 15px; font-weight: bold; color: #0369a1;">
                    VENDEDOR: <?php echo e(strtoupper($order->seller ?? 'N/A')); ?>

                </td>
                <?php if($order->stock_status): ?>
                <?php
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
                ?>
                <td style="width: 30%; <?php echo e($stockStyles[$order->stock_status] ?? $stockStyles['pending']); ?> border-radius: 6px; text-align: center; padding: 8px 15px; font-weight: bold;">
                    <?php echo e($stockLabels[$order->stock_status] ?? 'PENDENTE'); ?>

                </td>
                <?php endif; ?>
            </tr>
        </table>
        
        <!-- Layout Principal -->
        <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0;">
            <tr>
                <!-- TAMANHOS -->
                <td style="width: 10%; vertical-align: top;">
                    <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; padding: 10px;">
                        <div style="font-size: 10px; font-weight: bold; text-align: center; margin-bottom: 8px; text-transform: uppercase; color: #475569;">Tamanhos</div>
                        <?php
                            $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                            $itemSizes = $itemSizes ?? [];
                            $sizeColors = [
                                'PP' => '#FF8C00', 'P' => '#FFD700', 'M' => '#4169E1', 
                                'G' => '#DC143C', 'GG' => '#32CD32', 'EXG' => '#8A2BE2',
                                'G1' => '#78909C', 'G2' => '#78909C', 'G3' => '#78909C',
                                'ESPECIAL' => '#E91E63'
                            ];
                            $sizeTextColors = ['P' => '#333333'];
                        ?>
                        <?php $__currentLoopData = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? 0; ?>
                            <?php if($qty > 0): ?>
                            <div style="background: <?php echo e($sizeColors[$size]); ?>; color: <?php echo e($sizeTextColors[$size] ?? 'white'); ?>; padding: 6px 8px; margin-bottom: 5px; border-radius: 6px; text-align: center; font-weight: bold;">
                                <span style="font-size: 9px; display: block;"><?php echo e($size); ?></span>
                                <span style="font-size: 14px;"><?php echo e($qty); ?></span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div style="background: #6366f1; color: white; padding: 8px; margin-top: 8px; border-radius: 6px; text-align: center; font-weight: bold;">
                            <span style="font-size: 10px; display: block;">TOTAL</span>
                            <span style="font-size: 18px;"><?php echo e($item->quantity); ?></span>
                        </div>
                    </div>
                </td>
                
                <!-- IMAGEM DE CAPA -->
                <td style="width: 58%; vertical-align: top;">
                    <div style="background: #f1f5f9; border: 2px solid #cbd5e1; border-radius: 12px; padding: 15px; text-align: center; min-height: 340px;">
                        <div style="font-size: 11px; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">Imagem de Capa do Layout</div>
                        <?php
                            $imageData = $itemImages[$item->id] ?? [];
                            $hasCoverImage = $imageData['hasCoverImage'] ?? false;
                            $coverImageUrl = $imageData['coverImageUrl'] ?? null;
                            $coverImageBase64 = $imageData['coverImageBase64'] ?? false;
                        ?>
                        <?php if($hasCoverImage && $coverImageUrl): ?>
                            <?php
                                $imgSrc = $coverImageUrl;
                                if (!$coverImageBase64 && !str_starts_with($imgSrc, 'file://') && !str_starts_with($imgSrc, 'data:')) {
                                    $imgSrc = 'file://' . $imgSrc;
                                }
                            ?>
                            <img src="<?php echo e($imgSrc); ?>" alt="Capa" style="max-width: 100%; max-height: 300px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                        <?php else: ?>
                            <div style="color: #94a3b8; font-size: 14px; padding: 120px 20px;">Sem imagem de capa</div>
                        <?php endif; ?>
                    </div>
                </td>
                
                <!-- ESPECIFICACOES -->
                <td style="width: 20%; vertical-align: top;">
                    <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                        <div style="font-size: 10px; font-weight: bold; text-align: center; margin-bottom: 10px; text-transform: uppercase; color: #475569;">Especificacoes</div>
                        <?php
                            $specs = [
                                'Tecido' => $item->fabric,
                                'Cor' => $item->color,
                                'Gola' => $item->collar,
                                'Modelo' => $item->model,
                                'Detalhe' => $item->detail,
                                'Estampa' => $item->print_type,
                            ];
                        ?>
                        <?php $__currentLoopData = $specs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="background: #e2e8f0; border-radius: 6px; padding: 6px 10px; margin-bottom: 6px;">
                            <div style="font-size: 8px; color: #64748b; text-transform: uppercase;"><?php echo e($label); ?></div>
                            <div style="font-size: 11px; font-weight: bold; color: #1e293b;"><?php echo e($value ? strtoupper($value) : 'N/A'); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Alertas -->
        <?php
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
        ?>
        <?php if($isInfantil || $hasSleeveOrSide): ?>
        <div style="background: #fef3c7; border: 2px solid #f59e0b; border-left: 6px solid #f59e0b; border-radius: 6px; padding: 10px 15px; margin-top: 8px;">
            <strong style="color: #92400e; font-size: 11px;">ATENCAO - COSTURA</strong>
            <?php if($isInfantil): ?><p style="color: #92400e; font-size: 10px; margin: 3px 0 0 0;">PECA INFANTIL - CAMISA ABERTA PARA PERSONALIZACAO</p><?php endif; ?>
            <?php if($hasSleeveOrSide): ?><p style="color: #92400e; font-size: 10px; margin: 3px 0 0 0;">CAMISA ABERTA PARA PERSONALIZACAO EM MANGA/LATERAL</p><?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Observacoes -->
        <?php if($item->art_notes || $order->notes): ?>
        <div style="margin-top: 8px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 12px 15px;">
            <div style="font-size: 10px; margin-bottom: 6px; text-transform: uppercase; color: #475569; font-weight: bold;">OBSERVACOES</div>
            <div style="background: #e2e8f0; border-radius: 6px; padding: 10px; font-size: 11px; color: #1e293b;">
                <?php if($item->art_notes): ?><strong>Item:</strong> <?php echo e($item->art_notes); ?><?php if($order->notes): ?><br><?php endif; ?> <?php endif; ?>
                <?php if($order->notes): ?><strong>Pedido:</strong> <?php echo e($order->notes); ?><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/kanban/pdf/costura.blade.php ENDPATH**/ ?>