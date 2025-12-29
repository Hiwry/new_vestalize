

<?php $__env->startSection('content'); ?>
<style>
    .dark .confirm-page .text-gray-900,
    .dark .confirm-page .text-gray-800,
    .dark .confirm-page .text-gray-700,
    .dark .confirm-page .text-black { color: rgb(226 232 240); }
</style>
<div class="confirm-page max-w-7xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">5</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Confirmação do Pedido</span>
                        <p class="text-xs text-gray-500 dark:text-slate-500 dark:text-slate-400 mt-0.5">Etapa 5 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">100%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-2.5 shadow-inner">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out shadow-lg shadow-indigo-500/30 dark:shadow-indigo-600/30" style="width: 100%"></div>
            </div>
        </div>

        <!-- Messages -->
        <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-300 dark:text-green-300"><?php echo e(session('success')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-300"><?php echo e(session('error')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal - Resumo do Pedido -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-lg shadow-sm dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-700 dark:border-slate-800">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/30">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmação do Pedido</h1>
                                <p class="text-sm text-gray-600 dark:text-slate-400 dark:text-slate-300 dark:text-slate-400">Pedido #<?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?> - Aguardando Confirmação</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        
                        <!-- ETAPA 1: Dados do Cliente -->
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-5 h-5 rounded-md flex items-center justify-center" class="bg-indigo-100 dark:bg-indigo-900/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-sm font-medium text-gray-900 dark:text-white">Dados do Cliente</h2>
                            </div>

                            <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Nome Completo:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($order->client->name); ?></p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Telefone:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($order->client->phone_primary); ?></p>
                                    </div>
                                    <?php if($order->client->email): ?>
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">E-mail:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($order->client->email); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if($order->client->cpf_cnpj): ?>
                            <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">CPF/CNPJ:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($order->client->cpf_cnpj); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if($order->client->address): ?>
                            <div class="md:col-span-2">
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Endereço:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($order->client->address); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if($order->client->category): ?>
                            <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Categoria:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($order->client->category); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                        <!-- ETAPA 2: Itens de Costura -->
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-5 h-5 rounded-md flex items-center justify-center" class="bg-indigo-100 dark:bg-indigo-900/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <h2 class="text-sm font-medium text-gray-900 dark:text-white">Item <?php echo e($index + 1); ?> - Costura</h2>
                            </div>

                            <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Personalização:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->print_type); ?></p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Tecido:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->fabric); ?></p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Cor:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->color); ?></p>
                                    </div>
                                    <?php if($item->collar): ?>
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Gola:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->collar); ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($item->detail): ?>
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Detalhe:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->detail); ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($item->model): ?>
                                    <div>
                                        <span class="text-xs text-gray-600 dark:text-slate-400">Tipo de Corte:</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->model); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Tamanhos -->
                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4 mb-4">
                                    <span class="text-xs text-gray-600 dark:text-slate-400 block mb-3 font-semibold">Distribuição de Tamanhos:</span>
                                    <div class="grid grid-cols-5 gap-2 mb-3">
                                        <?php
                                            $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                                            $itemSizes = $itemSizes ?? [];
                                            $availableSizes = ['PP', 'P', 'M', 'G', 'GG'];
                                            $sizeColors = [
                                                'PP' => 'bg-orange-100 dark:bg-orange-900/30 border-orange-300 dark:border-orange-700 text-orange-900 dark:text-orange-200',
                                                'P' => 'bg-yellow-100 dark:bg-yellow-900/30 border-yellow-300 dark:border-yellow-700 text-yellow-900 dark:text-yellow-200',
                                                'M' => 'bg-blue-100 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700 text-blue-900 dark:text-blue-200',
                                                'G' => 'bg-red-100 dark:bg-red-900/30 border-red-300 dark:border-red-700 text-red-900 dark:text-red-200',
                                                'GG' => 'bg-green-100 dark:bg-green-900/30 border-green-300 dark:border-green-700 text-green-900 dark:text-green-200',
                                            ];
                                            $totalSizes = 0;
                                        ?>
                                        <?php $__currentLoopData = $availableSizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? 0;
                                                $qty = (int)$qty;
                                                $totalSizes += $qty;
                                                $colorClass = $sizeColors[$size] ?? 'bg-gray-100 dark:bg-slate-800 border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white';
                                            ?>
                                            <div class="<?php echo e($colorClass); ?> rounded-lg px-3 py-2 text-center border-2">
                                                <span class="text-xs font-semibold block mb-1"><?php echo e($size); ?></span>
                                                <p class="font-bold text-base"><?php echo e($qty); ?></p>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg px-4 py-2 border border-indigo-200 dark:border-indigo-800">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white text-center">
                                            Total: <span class="text-indigo-600 dark:text-indigo-400 text-lg"><?php echo e($totalSizes > 0 ? $totalSizes : $item->quantity); ?></span> peças
                                            <?php if($totalSizes > 0 && $totalSizes != $item->quantity): ?>
                                                <span class="text-xs text-orange-600 dark:text-orange-400 block mt-1">(Quantidade do item: <?php echo e($item->quantity); ?>)</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Detalhamento de Preços da Costura -->
                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4">
                                    <span class="text-xs text-gray-600 dark:text-slate-400 block mb-2 font-semibold">Detalhamento de Preços - Costura:</span>
                                    <div class="bg-white dark:bg-slate-800 rounded-lg p-3 border border-gray-200 dark:border-slate-700">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600 dark:text-slate-400">Preço unitário (costura):</span>
                                            <span class="font-medium text-gray-900 dark:text-white">R$ <?php echo e(number_format($item->unit_price, 2, ',', '.')); ?></span>
                                        </div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600 dark:text-slate-400">Quantidade:</span>
                                            <span class="font-medium text-gray-900 dark:text-white"><?php echo e($item->quantity); ?> peças</span>
                                        </div>
                                        <div class="flex justify-between text-sm pt-2 border-t border-gray-200 dark:border-slate-700">
                                            <span class="text-gray-900 dark:text-white font-semibold">Subtotal costura:</span>
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">R$ <?php echo e(number_format($item->unit_price * $item->quantity, 2, ',', '.')); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                <!-- ETAPA 3: Personalização -->
                <?php if($item->sublimations->count() > 0): ?>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-5 h-5 rounded-md flex items-center justify-center" class="bg-indigo-100 dark:bg-indigo-900/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-sm font-medium text-gray-900 dark:text-white">Item <?php echo e($index + 1); ?> - Personalização</h2>
                    </div>

                            <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                        <?php
                            // Buscar o nome da arte da primeira personalização deste item
                            $artName = $item->sublimations->first()->art_name ?? null;
                        ?>
                        <?php if($artName): ?>
                        <div class="mb-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <div>
                                    <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Nome da Arte:</span>
                                    <p class="text-sm font-bold text-indigo-900 dark:text-indigo-100"><?php echo e($artName); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($item->cover_image || $item->cover_image_url): ?>
                        <?php
                            $imageUrl = $item->cover_image_url;
                            $imageExists = !empty($imageUrl);
                            $coverImagePath = $item->cover_image;
                        ?>
                        <div class="mb-4">
                            <span class="text-xs text-gray-600 dark:text-slate-400 block mb-2 font-semibold">Imagem de Capa:</span>
                            <div class="text-center">
                                <?php if($imageExists && $imageUrl): ?>
                                <img src="<?php echo e($imageUrl); ?>" 
                                     alt="Capa" 
                                     class="max-w-xs rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm mx-auto"
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <?php endif; ?>
                                
                                <div class="text-gray-500 dark:text-slate-400 text-sm py-4 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg <?php echo e($imageExists && $imageUrl ? 'hidden' : ''); ?>">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-600 dark:text-slate-300 font-medium">Erro ao carregar imagem</p>
                                    <?php if($coverImagePath): ?>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1"><?php echo e(basename($coverImagePath)); ?></p>
                                    <?php endif; ?>
                                    <?php if(!$imageExists): ?>
                                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Arquivo não encontrado no servidor</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                                <!-- Aplicações -->
                                <div class="mb-4">
                                    <span class="text-xs text-gray-600 dark:text-slate-400 block mb-2">Aplicações:</span>
                                    <div class="space-y-3">
                                        <?php $__currentLoopData = $item->sublimations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-gray-200">
                                            <?php
                                                $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                                                $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                                                $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                                                $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                                            ?>
                                            
                                            <!-- Cabeçalho com tipo de aplicação -->
                                            <div class="mb-3 pb-3 border-b border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-base font-bold text-gray-900 dark:text-white"><?php echo e($appType); ?></h3>
                                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">R$ <?php echo e(number_format($sub->final_price, 2, ',', '.')); ?></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Grade de informações principais -->
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                                                <!-- LOCAL -->
                                                <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-3 border border-gray-200">
                                                    <span class="text-xs text-gray-600 dark:text-slate-400 font-semibold block mb-1">LOCAL</span>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                        <?php echo e($locationName ?? 'Não especificado'); ?>

                                                    </p>
                                                </div>
                                                
                                                <!-- TAMANHO -->
                                                <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-3 border border-gray-200">
                                                    <span class="text-xs text-gray-600 dark:text-slate-400 font-semibold block mb-1">TAMANHO</span>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                        <?php if($sizeName): ?>
                                                            <?php echo e($sizeName); ?>

                                                            <?php if($sizeDimensions): ?>
                                                                <span class="text-xs">(<?php echo e($sizeDimensions); ?>)</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            Não especificado
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                
                                                <!-- QTD (DTD) -->
                                                <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-3 border border-gray-200">
                                                    <span class="text-xs text-gray-600 dark:text-slate-400 font-semibold block mb-1">QTD</span>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo e($sub->quantity); ?></p>
                                                </div>
                                                
                                                <!-- CORES -->
                                                <div class="bg-gray-50 dark:bg-slate-800 rounded-lg p-3 border border-gray-200">
                                                    <span class="text-xs text-gray-600 dark:text-slate-400 font-semibold block mb-1">CORES</span>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                        <?php echo e($sub->color_count > 0 ? $sub->color_count . ' cor' . ($sub->color_count > 1 ? 'es' : '') : '1 cor'); ?>

                                                        <?php if($sub->has_neon): ?>
                                                            <span class="text-xs text-indigo-600 dark:text-indigo-400">+ Neon</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <!-- Detalhamento de Preços da Aplicação -->
                                            <div class="bg-gray-50 dark:bg-slate-800 rounded p-3 border border-gray-200">
                                                <span class="text-xs text-gray-600 dark:text-slate-400 block mb-2">Detalhamento de Preços - Aplicação:</span>
                                                <div class="space-y-1">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-600 dark:text-slate-400">Preço unitário (aplicação):</span>
                                                        <span class="font-medium">R$ <?php echo e(number_format($sub->unit_price, 2, ',', '.')); ?></span>
                                                    </div>
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-600 dark:text-slate-400">Quantidade:</span>
                                                        <span class="font-medium"><?php echo e($sub->quantity); ?> peças</span>
                                                    </div>
                                                    <?php if($sub->discount_percent > 0): ?>
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-600 dark:text-slate-400">Desconto:</span>
                                                        <span class="font-medium text-green-600 dark:text-green-400">-<?php echo e($sub->discount_percent); ?>%</span>
                                                    </div>
                                                    <?php endif; ?>
                                                    <div class="flex justify-between text-xs pt-1 border-t border-gray-200">
                                                        <span class="text-gray-600 dark:text-slate-400 font-medium">Subtotal aplicação:</span>
                                                        <span class="font-bold" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">R$ <?php echo e(number_format($sub->final_price, 2, ',', '.')); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>

                        <?php if($item->files->count() > 0): ?>
                        <div>
                                    <span class="text-xs text-gray-600 dark:text-slate-400 block mb-2">Arquivos da Arte:</span>
                            <div class="space-y-1">
                                <?php $__currentLoopData = $item->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center text-xs text-gray-700 dark:text-slate-400">
                                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo e($file->file_name); ?>

                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Resumo Total do Item -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 mb-3">
                        <div class="w-5 h-5 rounded-md flex items-center justify-center" class="bg-indigo-100 dark:bg-indigo-900/30">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-sm font-medium text-gray-900 dark:text-white">Resumo Total - Item <?php echo e($index + 1); ?></h2>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                        <?php
                            $costuraSubtotal = $item->unit_price * $item->quantity;
                            $personalizacaoSubtotal = $item->sublimations->sum('final_price');
                            $itemTotal = $costuraSubtotal + $personalizacaoSubtotal;
                            
                            // Calcular valor unitário por camisa
                            $costuraUnitaria = $item->unit_price;
                            $personalizacaoUnitaria = $item->quantity > 0 ? ($personalizacaoSubtotal / $item->quantity) : 0;
                            $valorPorCamisa = $costuraUnitaria + $personalizacaoUnitaria;
                        ?>
                        
                        <!-- Resumo: Valor por Camisa -->
                        <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 p-4 mb-4">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Valor por Camisa</h3>
                                </div>
                                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">R$ <?php echo e(number_format($valorPorCamisa, 2, ',', '.')); ?></span>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <!-- Costura -->
                                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-3 border border-gray-200 dark:border-slate-600">
                                    <div class="flex items-center space-x-1.5 mb-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <span class="text-xs text-gray-600 dark:text-slate-400 font-medium">Costura</span>
                                    </div>
                                    <p class="text-base font-bold text-gray-900 dark:text-white">R$ <?php echo e(number_format($costuraUnitaria, 2, ',', '.')); ?></p>
                                </div>
                                
                                <!-- Aplicação -->
                                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-3 border border-gray-200 dark:border-slate-600">
                                    <div class="flex items-center space-x-1.5 mb-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <span class="text-xs text-gray-600 dark:text-slate-400 font-medium">Aplicação</span>
                                    </div>
                                    <p class="text-base font-bold text-gray-900 dark:text-white">R$ <?php echo e(number_format($personalizacaoUnitaria, 2, ',', '.')); ?></p>
                                </div>
                                
                                <!-- Total -->
                                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-3 border border-indigo-200 dark:border-indigo-800">
                                    <div class="flex items-center space-x-1.5 mb-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-xs text-indigo-700 dark:text-indigo-300 font-semibold">TOTAL</span>
                                    </div>
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">R$ <?php echo e(number_format($valorPorCamisa, 2, ',', '.')); ?></p>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg px-4 py-2.5 border border-gray-200 dark:border-slate-600">
                                <p class="text-sm text-gray-700 dark:text-slate-300 text-center">
                                    <span class="font-medium"><?php echo e($item->quantity); ?></span> camisas × 
                                    <span class="font-medium">R$ <?php echo e(number_format($valorPorCamisa, 2, ',', '.')); ?></span> = 
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">R$ <?php echo e(number_format($itemTotal, 2, ',', '.')); ?></span>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Resumo Detalhado -->
                        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-gray-200 dark:border-slate-700">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-slate-400">Subtotal costura:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">R$ <?php echo e(number_format($costuraSubtotal, 2, ',', '.')); ?></span>
                                </div>
                                <?php if($personalizacaoSubtotal > 0): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-slate-400">Subtotal personalização:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">R$ <?php echo e(number_format($personalizacaoSubtotal, 2, ',', '.')); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between text-sm pt-2 border-t border-gray-200 dark:border-slate-700">
                                    <span class="text-gray-900 dark:text-white font-semibold">Total do item:</span>
                                    <span class="font-bold text-lg text-indigo-600 dark:text-indigo-400">R$ <?php echo e(number_format($itemTotal, 2, ',', '.')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <!-- ETAPA 4: Pagamento -->
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-5 h-5 rounded-md flex items-center justify-center" class="bg-indigo-100 dark:bg-indigo-900/30">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <h2 class="text-sm font-medium text-gray-900 dark:text-white">Pagamento</h2>
                            </div>

                        <?php if($payment && $payment->count() > 0): ?>
                            <div class="bg-gray-50 dark:bg-slate-800/50 rounded-md p-4">
                        <div class="mb-4">
                                    <span class="text-xs text-gray-600 dark:text-slate-400">Data de Entrada:</span>
                                    <?php
                                        $firstPayment = $payment->first();
                                        $entryDate = $firstPayment && $firstPayment->entry_date ? \Carbon\Carbon::parse($firstPayment->entry_date)->format('d/m/Y') : 'Não informada';
                                    ?>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($entryDate); ?></p>
                        </div>

                        <div class="mb-4">
                                    <span class="text-xs text-gray-600 dark:text-slate-400 block mb-2">Formas de Pagamento:</span>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $payment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paymentItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex justify-between items-center bg-white dark:bg-slate-800 rounded-lg p-3 border border-gray-200 dark:border-slate-700">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white capitalize"><?php echo e($paymentItem->payment_method); ?></span>
                                                <?php if($paymentItem->entry_date): ?>
                                                    <span class="text-xs text-gray-500 dark:text-slate-400"><?php echo e(\Carbon\Carbon::parse($paymentItem->entry_date)->format('d/m/Y')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">R$ <?php echo e(number_format($paymentItem->entry_amount, 2, ',', '.')); ?></span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <?php
                                // Calcular total pago baseado nos payment_methods (fonte única de verdade)
                                // NUNCA usar $p->amount pois esse é o total do pedido, não o valor pago
                                $totalPago = 0;
                                foreach($payment as $p) {
                                    if($p->payment_methods && is_array($p->payment_methods) && count($p->payment_methods) > 0) {
                                        $sumFromMethods = 0;
                                        foreach($p->payment_methods as $method) {
                                            $sumFromMethods += floatval($method['amount'] ?? 0);
                                        }
                                        // Se a soma dos payment_methods for igual ao total do pedido, pode ser um erro
                                        // Nesse caso, usar entry_amount se disponível
                                        if(abs($sumFromMethods - $order->total) < 0.01 && $p->entry_amount > 0) {
                                            $totalPago += floatval($p->entry_amount);
                                        } else {
                                            $totalPago += $sumFromMethods;
                                        }
                                    } else {
                                        // Fallback para pagamentos antigos sem payment_methods
                                        // Usar entry_amount, nunca amount (que é o total do pedido)
                                        $totalPago += floatval($p->entry_amount ?? 0);
                                    }
                                }
                                $restante = $order->total - $totalPago;
                            ?>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-slate-400">Total Pago:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">R$ <?php echo e(number_format($totalPago, 2, ',', '.')); ?></span>
                            </div>
                            <?php if($restante > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-slate-400">Restante:</span>
                                <span class="font-medium text-orange-600 dark:text-orange-400">R$ <?php echo e(number_format($restante, 2, ',', '.')); ?></span>
                            </div>
                            <?php elseif($restante < 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-slate-400">Crédito do Cliente:</span>
                                <span class="font-medium text-green-600 dark:text-green-400">R$ <?php echo e(number_format(abs($restante), 2, ',', '.')); ?></span>
                            </div>
                            <?php else: ?>
                            <div class="flex items-center text-sm" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pago Integralmente
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                        <?php else: ?>
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-md p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-orange-900 dark:text-orange-100">Nenhum pagamento registrado</p>
                                        <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">
                                            Este pedido ainda não possui informações de pagamento. 
                                            Clique em "Voltar para Pagamento" abaixo para adicionar as formas de pagamento.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                </div>

                    </div>
                </div>
            </div>

            <!-- Coluna Lateral - Resumo Financeiro e Ações -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 rounded-lg shadow-sm dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800 sticky top-6">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-md flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/30">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Resumo Financeiro</h2>
                                <p class="text-sm text-gray-600 dark:text-slate-400">Valores do pedido</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-slate-400">Subtotal:</span>
                                <span class="font-medium text-gray-900 dark:text-white">R$ <?php echo e(number_format($order->subtotal, 2, ',', '.')); ?></span>
                        </div>

                        <?php if(!empty($sizeSurcharges)): ?>
                        <div class="border-t pt-2">
                            <p class="text-gray-600 dark:text-slate-400 font-medium mb-2">Acréscimos por Tamanho:</p>
                            <?php $__currentLoopData = $sizeSurcharges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size => $surcharge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between text-xs ml-2">
                                <span class="text-gray-500 dark:text-slate-500"><?php echo e($size); ?>:</span>
                                <span class="text-orange-600 dark:text-orange-400">+R$ <?php echo e(number_format($surcharge, 2, ',', '.')); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-600 dark:text-slate-400">Total Acréscimos:</span>
                                <span class="font-medium text-orange-600 dark:text-orange-400">+R$ <?php echo e(number_format(array_sum($sizeSurcharges), 2, ',', '.')); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($order->delivery_fee > 0): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-slate-400">Taxa de Entrega:</span>
                                <span class="font-medium text-gray-900 dark:text-white">+R$ <?php echo e(number_format($order->delivery_fee, 2, ',', '.')); ?></span>
                        </div>
                        <?php endif; ?>

                            <div class="border-t pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total Final:</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">R$ <?php echo e(number_format($order->total, 2, ',', '.')); ?></span>
                                </div>
                            </div>

                        <?php if($payment->count() > 0): ?>
                        <div class="border-t pt-3 space-y-2">
                                <?php
                                    // Calcular total pago baseado nos payment_methods (fonte única de verdade)
                                    // NUNCA usar $p->amount pois esse é o total do pedido, não o valor pago
                                    $totalPago = 0;
                                    foreach($payment as $p) {
                                        if($p->payment_methods && is_array($p->payment_methods) && count($p->payment_methods) > 0) {
                                            $sumFromMethods = 0;
                                            foreach($p->payment_methods as $method) {
                                                $sumFromMethods += floatval($method['amount'] ?? 0);
                                            }
                                            // Se a soma dos payment_methods for igual ao total do pedido, pode ser um erro
                                            // Nesse caso, usar entry_amount se disponível
                                            if(abs($sumFromMethods - $order->total) < 0.01 && $p->entry_amount > 0) {
                                                $totalPago += floatval($p->entry_amount);
                                            } else {
                                                $totalPago += $sumFromMethods;
                                            }
                                        } else {
                                            // Fallback para pagamentos antigos sem payment_methods
                                            // Usar entry_amount, nunca amount (que é o total do pedido)
                                            $totalPago += floatval($p->entry_amount ?? 0);
                                        }
                                    }
                                    $saldoRestante = $order->total - $totalPago;
                                    $firstPayment = $payment->first();
                                    $entryDateFormatted = $firstPayment && $firstPayment->entry_date ? \Carbon\Carbon::parse($firstPayment->entry_date)->format('d/m/Y') : 'Não informada';
                                ?>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-slate-400">Data de Entrada:</span>
                                    <span class="font-medium text-gray-900 dark:text-white"><?php echo e($entryDateFormatted); ?></span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-slate-400">Valor Pago:</span>
                                    <span class="font-medium" class="text-indigo-600 dark:text-indigo-400 dark:text-indigo-400">R$ <?php echo e(number_format($totalPago, 2, ',', '.')); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-slate-400"><?php echo e($saldoRestante < 0 ? 'Crédito do Cliente:' : 'Saldo Restante:'); ?></span>
                                    <span class="font-bold" style="color: <?php echo e($saldoRestante > 0 ? 'rgb(234 88 12)' : 'rgb(79 70 229)'); ?>">
                                        R$ <?php echo e(number_format(abs($saldoRestante), 2, ',', '.')); ?>

                                    </span>
                                </div>
                        </div>
                        <?php else: ?>
                        <div class="border-t pt-3">
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded p-3 text-sm">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-orange-900 dark:text-orange-100">Nenhum pagamento registrado</p>
                                        <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">Volte para a etapa de pagamento para adicionar uma forma de pagamento.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                        <!-- Status do Pedido -->
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                            <p class="text-sm text-gray-600 dark:text-slate-400 mb-1">Status do Pedido:</p>
                            <p class="font-bold text-gray-900 dark:text-white">RASCUNHO - Aguardando Confirmação</p>
                            <p class="text-xs text-gray-600 dark:text-slate-400 mt-2">
                                Este pedido ainda não está visível no kanban. Confirme abaixo para enviar para produção.
                            </p>
                        </div>

                    <!-- Ações -->
                    <div class="mt-6 space-y-3">
                            <form method="POST" action="<?php echo e(request()->routeIs('orders.edit.*') ? route('orders.edit.finalize') : route('orders.wizard.finalize')); ?>" id="finalize-form" onsubmit="return handleFinalize(this)">
                            <?php echo csrf_field(); ?>
                            
                                <!-- Checkbox para Evento -->
                                <div class="mb-4 p-4 bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg">
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" name="is_event" value="1" 
                                               <?php echo e(old('is_event') ? 'checked' : ''); ?>

                                               class="w-5 h-5 text-indigo-600 bg-gray-100 dark:bg-slate-700 border-gray-300 dark:border-slate-600 rounded focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-2">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Marcar como Pedido de Evento</span>
                                            <p class="text-xs text-gray-600 dark:text-slate-400 mt-1">Pedidos de evento recebem prioridade e destaque especial</p>
                                        </div>
                                    </label>
                                </div>
                                
                            <button type="submit" id="finalize-btn"
                                        class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 transition-all text-sm font-medium shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span id="finalize-text">Confirmar Pedido e Enviar para Produção</span>
                                    <span id="finalize-loading" class="hidden">Finalizando...</span>
                                </button>
                        </form>
                            
                        <a href="<?php echo e(request()->routeIs('orders.edit.*') ? route('orders.edit.payment') : route('orders.wizard.payment')); ?>" 
                               class="w-full flex items-center justify-center px-4 py-3 bg-gray-600 dark:bg-slate-700 text-white rounded-md hover:bg-gray-700 dark:hover:bg-slate-600 focus:outline-none focus:ring-1 focus:ring-gray-500 transition-all text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Voltar para Pagamento
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
        let formSubmitted = false;

        function handleFinalize(form) {
            if (formSubmitted) {
                return false;
            }

            // Mostrar modal de confirmação
            openConfirmModal();
            return false;
        }

        function confirmFinalize() {
            formSubmitted = true;
            
            // Fechar modal
            closeConfirmModal();
            
            // Desabilitar botão e mostrar loading
            const btn = document.getElementById('finalize-btn');
            const text = document.getElementById('finalize-text');
            const loading = document.getElementById('finalize-loading');
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.classList.add('hidden');
            loading.classList.remove('hidden');

            // Enviar o formulário após um pequeno delay para mostrar o loading
            setTimeout(() => {
                document.getElementById('finalize-form').submit();
            }, 500);
        }

        function openConfirmModal() {
            document.getElementById('confirmModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openAlertModal(title, message) {
            document.getElementById('alertModalTitle').textContent = title;
            document.getElementById('alertModalMessage').textContent = message;
            document.getElementById('alertModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAlertModal() {
            document.getElementById('alertModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Verificar eventos ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            
            // Prevenir múltiplos envios
            const finalizeForm = document.getElementById('finalize-form');
            if (finalizeForm) {
                finalizeForm.addEventListener('submit', function(e) {
                    if (formSubmitted) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
            
            // Fechar modais ao clicar fora
            const confirmModal = document.getElementById('confirmModal');
            if (confirmModal) {
                confirmModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeConfirmModal();
                    }
                });
            }
            
            const alertModal = document.getElementById('alertModal');
            if (alertModal) {
                alertModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAlertModal();
                    }
                });
            }
            
            // Fechar modais com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeConfirmModal();
                    closeAlertModal();
                }
            });
        });
</script>
<?php $__env->stopPush(); ?>

<!-- Modal de Confirmação -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-lg max-w-md w-full shadow-xl dark:shadow-2xl dark:shadow-black/20">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmar Pedido</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-slate-300">Tem certeza que deseja confirmar este pedido e enviar para produção?</p>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">Esta ação não poderá ser desfeita após a confirmação.</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex justify-end space-x-3">
                <button type="button" onclick="closeConfirmModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmFinalize()" 
                        class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white text-sm font-medium rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors">
                    Confirmar e Enviar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Alerta -->
    <div id="alertModal" class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-lg max-w-md w-full shadow-xl dark:shadow-2xl dark:shadow-black/20">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800">
                <h3 id="alertModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Atenção</h3>
            </div>
            <div class="px-6 py-4">
                <p id="alertModalMessage" class="text-sm text-gray-600 dark:text-slate-300"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex justify-end">
                <button type="button" onclick="closeAlertModal()" 
                        class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white text-sm font-medium rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors">
                    OK
                </button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/orders/wizard/confirm.blade.php ENDPATH**/ ?>