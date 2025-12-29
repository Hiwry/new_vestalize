

<?php $__env->startPush('styles'); ?>
<style>
        .dark {
            color-scheme: dark;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    <div class="max-w-7xl mx-auto p-6">
        <!-- Alerta de Confirmação Pendente (apenas para pedidos normais, não PDV) -->
        <?php if($order->status && $order->status->name == 'Pendente' && !$order->client_confirmed && !$order->is_pdv): ?>
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/30 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Aguardando Confirmação do Cliente</h3>
                    <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">
                        Este pedido está pendente até que o cliente confirme através do link de compartilhamento.
                        <?php if($order->client_token): ?>
                            <a href="<?php echo e(route('client.order.show', $order->client_token)); ?>" target="_blank" class="underline font-medium">
                                Visualizar link do cliente
                            </a>
                        <?php else: ?>
                            Gere o link de compartilhamento abaixo para enviar ao cliente.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php elseif($order->client_confirmed && !$order->is_pdv): ?>
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-300">Pedido Confirmado pelo Cliente</h3>
                    <p class="text-xs text-green-700 dark:text-green-400">Confirmado em <?php echo e($order->client_confirmed_at->format('d/m/Y H:i')); ?></p>
                </div>
            </div>
        </div>
        <?php elseif($order->is_pdv): ?>
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Venda Realizada no PDV</h3>
                    <p class="text-xs text-blue-700 dark:text-blue-400">Venda finalizada em <?php echo e($order->created_at->format('d/m/Y H:i')); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cabeçalho -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($order->is_pdv ? 'Venda' : 'Pedido'); ?> #<?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?></h1>
                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">Criado em <?php echo e($order->created_at->format('d/m/Y H:i')); ?></p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('orders.index')); ?>" 
                   class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-slate-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
                <a href="<?php echo e(route('kanban.index')); ?>" 
                   class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-slate-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Ver no Kanban
                </a>
                <form method="POST" action="<?php echo e(route('orders.generate-share-link', $order->id)); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" 
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-slate-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        <span>Compartilhar</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status e Datas -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-slate-800"><?php echo e($order->is_pdv ? 'Informações da Venda' : 'Informações do Pedido'); ?></h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Status</span>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-md mt-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 w-fit">
                                <?php echo e($order->status->name); ?>

                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Data de Entrega</span>
                            <span class="text-sm text-gray-900 dark:text-white">
                                <?php if($order->delivery_date): ?>
                                    <?php echo e(\Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y')); ?>

                                <?php else: ?>
                                    Não definida
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if($order->store): ?>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Loja</span>
                            <span class="text-sm text-gray-900 dark:text-white"><?php echo e($order->store->name); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status de Confirmação do Cliente (Apenas para Pedidos) -->
                    <?php if(!$order->is_pdv): ?>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-2">Confirmação do Cliente</p>
                                <?php if($order->client_confirmed): ?>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-indigo-700 dark:text-indigo-400">Confirmado</span>
                                </div>
                                <?php if($order->client_confirmed_at): ?>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                    <?php echo e($order->client_confirmed_at->format('d/m/Y H:i')); ?>

                                </p>
                                <?php endif; ?>
                                <?php else: ?>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-slate-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-600 dark:text-slate-400">Pendente</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($order->client_token): ?>
                            <div class="text-right">
                                <p class="text-xs font-medium text-gray-600 dark:text-slate-400 mb-2">Link de Compartilhamento</p>
                                <div class="flex items-center space-x-2">
                                    <input type="text" 
                                           value="<?php echo e(route('client.order.show', $order->client_token)); ?>" 
                                           readonly 
                                           class="text-xs px-3 py-2 border border-gray-300 dark:border-slate-700 rounded-md bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white w-48 focus:outline-none">
                                    <button onclick="copyToClipboard('<?php echo e(route('client.order.show', $order->client_token)); ?>', this)" 
                                            class="px-3 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white text-xs rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 transition-all shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                                        Copiar
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Cliente -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-slate-800">Cliente</h2>
                    <?php if($order->client): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Nome</span>
                            <span class="text-sm text-gray-900 dark:text-white"><?php echo e($order->client->name); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Telefone</span>
                            <span class="text-sm text-gray-900 dark:text-white"><?php echo e($order->client->phone_primary ?? 'N/A'); ?></span>
                        </div>
                        <?php if($order->client->email): ?>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Email</span>
                            <span class="text-sm text-gray-900 dark:text-white"><?php echo e($order->client->email); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($order->client->cpf_cnpj): ?>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">CPF/CNPJ</span>
                            <span class="text-sm text-gray-900 dark:text-white"><?php echo e($order->client->cpf_cnpj); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-sm text-gray-600 dark:text-slate-400">
                        <p>Venda realizada sem cliente cadastrado</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Itens do Pedido -->
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-3 border-b border-gray-200 dark:border-slate-800">Item <?php echo e($loop->iteration); ?></h2>
                    
                    <!-- Imagem de Capa -->
                    <?php if($item->cover_image || $item->cover_image_url): ?>
                    <div class="mb-4">
                        <?php if($item->cover_image_url): ?>
                        <img src="<?php echo e($item->cover_image_url); ?>" 
                             alt="Capa" 
                             class="max-w-md rounded-lg border border-gray-200 dark:border-slate-700">
                        <?php else: ?>
                        <div class="border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                            <p class="font-medium mb-1">Imagem não encontrada</p>
                            <?php if($item->cover_image): ?>
                            <p class="text-xs"><?php echo e(basename($item->cover_image)); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Detalhes da Costura -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                        <?php if($order->store): ?>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-slate-400">Loja</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($order->store->name); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-slate-400">Tecido</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->fabric); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-slate-400">Cor</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->color); ?></p>
                        </div>
                        <?php if($item->collar): ?>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-slate-400">Gola</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->collar); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if($item->detail): ?>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-slate-400">Detalhe</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->detail); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if($item->model): ?>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-slate-400">Tipo de Corte</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->model); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-xs text-gray-600 dark:text-slate-400">Personalização</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($item->print_type); ?></p>
                        </div>
                    </div>

                    <!-- Tamanhos -->
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Tamanhos:</p>
                        <?php
                            // Garantir que sizes seja um array
                            $sizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) && !empty($item->sizes) ? json_decode($item->sizes, true) : []);
                            $sizes = $sizes ?? [];
                        ?>
                        <?php if(!empty($sizes)): ?>
                        <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                            <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size => $qty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($qty > 0): ?>
                                <div class="bg-gray-100 dark:bg-gray-700/50 rounded px-2 py-1 text-center border border-gray-200 dark:border-gray-700">
                                    <span class="text-xs text-gray-600 dark:text-slate-400"><?php echo e($size); ?></span>
                                    <p class="font-bold text-sm text-gray-900 dark:text-white"><?php echo e($qty); ?></p>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">Tamanhos não especificados</p>
                        <?php endif; ?>
                        <p class="text-sm text-gray-900 dark:text-white mt-2"><strong class="text-gray-900 dark:text-white">Total:</strong> <?php echo e($item->quantity); ?> peças</p>
                    </div>

                    <!-- Personalizações -->
                    <?php if($item->sublimations && $item->sublimations->count() > 0): ?>
                    <div class="border-t border-gray-200 dark:border-slate-800 pt-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">🎨 Personalização:</p>
                        <?php if($item->art_name): ?>
                            <p class="text-sm text-gray-900 dark:text-white mb-2"><strong class="text-gray-900 dark:text-white">Nome da Arte:</strong> <?php echo e($item->art_name); ?></p>
                        <?php endif; ?>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $item->sublimations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded p-3 text-sm">
                                <div>
                                    <?php
                                        $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                                        $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                                        $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                                        $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                                    ?>
                                    
                                    <strong class="text-gray-900 dark:text-white">
                                        <?php if($sizeName): ?>
                                            <?php echo e($sizeName); ?><?php if($sizeDimensions): ?> (<?php echo e($sizeDimensions); ?>)<?php endif; ?>
                                        <?php else: ?>
                                            <?php echo e($appType); ?>

                                        <?php endif; ?>
                                    </strong>
                                    <?php if($locationName): ?> - <?php echo e($locationName); ?><?php endif; ?>
                                    <span class="text-gray-600 dark:text-slate-400">x<?php echo e($sub->quantity); ?></span>
                                    <?php if($sub->color_count > 0): ?>
                                        <br><span class="text-xs text-gray-500 dark:text-slate-500"><?php echo e($sub->color_count); ?> <?php echo e($sub->color_count == 1 ? 'Cor' : 'Cores'); ?><?php echo e($sub->has_neon ? ' + Neon' : ''); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <div class="text-gray-600 dark:text-slate-400">R$ <?php echo e(number_format($sub->unit_price, 2, ',', '.')); ?> × <?php echo e($sub->quantity); ?></div>
                                    <?php if($sub->discount_percent > 0): ?>
                                        <div class="text-xs text-green-600 dark:text-green-400">-<?php echo e($sub->discount_percent); ?>%</div>
                                    <?php endif; ?>
                                    <div class="font-bold text-gray-900 dark:text-white">R$ <?php echo e(number_format($sub->final_price, 2, ',', '.')); ?></div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Arquivos -->
                    <?php if($item->files && $item->files->count() > 0): ?>
                    <div class="border-t border-gray-200 dark:border-slate-800 pt-4 mt-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">📎 Arquivos:</p>
                        <div class="space-y-1">
                            <?php $__currentLoopData = $item->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="text-sm text-indigo-600 dark:text-indigo-400">
                                • <?php echo e($file->file_name); ?>

                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Coluna Lateral -->
            <div class="space-y-6">
                <!-- Gerenciamento de Pagamentos -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pagamentos
                        </h2>
                        <button onclick="togglePaymentForm()" 
                                class="px-3 py-1 text-sm bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 transition-all shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                            Adicionar Pagamento
                        </button>
                    </div>

                    <!-- Resumo Financeiro -->
                    <?php
                        // Calcular total pago baseado apenas na tabela payments (fonte única de verdade)
                        // NUNCA usar $payment->amount pois esse é o total do pedido, não o valor pago
                        $totalPaid = 0;
                        foreach($order->payments as $payment) {
                            if($payment->payment_methods && is_array($payment->payment_methods) && count($payment->payment_methods) > 0) {
                                $sumFromMethods = 0;
                                foreach($payment->payment_methods as $method) {
                                    $sumFromMethods += floatval($method['amount'] ?? 0);
                                }
                                // Se a soma dos payment_methods for igual ao total do pedido, pode ser um erro
                                // Nesse caso, usar entry_amount se disponível
                                if(abs($sumFromMethods - $order->total) < 0.01 && $payment->entry_amount > 0) {
                                    $totalPaid += floatval($payment->entry_amount);
                                } else {
                                    $totalPaid += $sumFromMethods;
                                }
                            } else {
                                // Fallback para pagamentos antigos sem payment_methods
                                // Usar entry_amount, nunca amount (que é o total do pedido)
                                $totalPaid += floatval($payment->entry_amount ?? 0);
                            }
                        }
                        
                        $remaining = $order->total - $totalPaid;
                    ?>
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400"><?php echo e($order->is_pdv ? 'Total da Venda:' : 'Total do Pedido:'); ?></span>
                            <span class="font-bold text-gray-900 dark:text-white">R$ <?php echo e(number_format($order->total, 2, ',', '.')); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Total Pago:</span>
                            <span class="font-bold text-green-600 dark:text-green-400">
                                R$ <?php echo e(number_format($totalPaid, 2, ',', '.')); ?>

                            </span>
                        </div>
                        <div class="flex justify-between text-sm border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span class="text-gray-600 dark:text-gray-400"><?php echo e($remaining < 0 ? 'Crédito do Cliente:' : 'Restante:'); ?></span>
                            <span class="font-bold <?php echo e($remaining > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400'); ?>">
                                R$ <?php echo e(number_format(abs($remaining), 2, ',', '.')); ?>

                            </span>
                        </div>
                    </div>

                    <!-- Lista de Pagamentos -->
                    <?php if($order->payments->count() > 0): ?>
                    <div class="mb-4">
                        <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">Histórico de Pagamentos</h3>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $order->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($payment->payment_methods && is_array($payment->payment_methods)): ?>
                                    <?php $__currentLoopData = $payment->payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-4">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-white">R$ <?php echo e(number_format($method['amount'], 2, ',', '.')); ?></p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e(ucfirst($method['method'])); ?></p>
                                                        <p class="text-sm text-gray-600 dark:text-slate-400"><?php echo e(ucfirst($method['method'])); ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 dark:text-slate-400"><?php echo e(isset($method['date']) ? \Carbon\Carbon::parse($method['date'])->format('d/m/Y') : \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')); ?></p>
                                                        <?php if($payment->notes): ?>
                                                        <p class="text-xs text-gray-500 dark:text-slate-500"><?php echo e($payment->notes); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-1">
                                                <?php if(isset($method['id'])): ?>
                                                <button onclick="editPayment(<?php echo e($payment->id); ?>, '<?php echo e($method['id']); ?>')" 
                                                        class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                    Editar
                                                </button>
                                                <form method="POST" action="<?php echo e(route('orders.payment.delete', $order->id)); ?>" class="inline" 
                                                      onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="payment_id" value="<?php echo e($payment->id); ?>">
                                                    <input type="hidden" name="method_id" value="<?php echo e($method['id']); ?>">
                                                    <button type="submit" 
                                                            class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                <?php endif; ?>
                                                        Remover
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <!-- Fallback para pagamentos antigos sem payment_methods -->
                                <div class="bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4">
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">R$ <?php echo e(number_format($payment->entry_amount, 2, ',', '.')); ?></p>
                                                    <p class="text-sm text-gray-600 dark:text-slate-400"><?php echo e(ucfirst($payment->method)); ?></p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 dark:text-slate-400"><?php echo e(\Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')); ?></p>
                                                    <?php if($payment->notes): ?>
                                                    <p class="text-xs text-gray-500 dark:text-slate-500"><?php echo e($payment->notes); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex space-x-1">
                                            <button onclick="editPayment(<?php echo e($payment->id); ?>)" 
                                                    class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                Editar
                                            </button>
                                            <form method="POST" action="<?php echo e(route('orders.payment.delete', $order->id)); ?>" class="inline" 
                                                  onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <input type="hidden" name="payment_id" value="<?php echo e($payment->id); ?>">
                                                <button type="submit" 
                                                        class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Formulário de Pagamento (Oculto por padrão) -->
                    <div id="paymentForm" class="hidden border-t border-gray-200 dark:border-slate-800 pt-4">
                        <form method="POST" action="<?php echo e(route('orders.payment.add', $order->id)); ?>">
                            <?php echo csrf_field(); ?>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Método de Pagamento</label>
                                    <select name="method" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" required>
                                        <option value="">Selecione...</option>
                                        <option value="pix">PIX</option>
                                        <option value="dinheiro">Dinheiro</option>
                                        <option value="cartao">Cartão</option>
                                        <option value="boleto">Boleto</option>
                                        <option value="transferencia">Transferência</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Valor</label>
                                    <input type="number" 
                                           name="amount" 
                                           step="0.01" 
                                           min="0.01" 
                                           max="<?php echo e($remaining); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Observações</label>
                                <textarea name="notes" 
                                          rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                                          placeholder="Observações sobre o pagamento..."></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" 
                                        onclick="togglePaymentForm()" 
                                        class="px-4 py-2 text-gray-700 dark:text-slate-300 bg-gray-200 dark:bg-slate-700 rounded-md hover:bg-gray-300 dark:hover:bg-slate-600 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 transition-all shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                                    Adicionar Pagamento
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Formulário de Edição de Pagamento (Oculto por padrão) -->
                    <div id="editPaymentForm" class="hidden border-t pt-4">
                        <form method="POST" action="<?php echo e(route('orders.payment.update', $order->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="payment_id" id="edit_payment_id">
                            <input type="hidden" name="method_id" id="edit_method_id">
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Método de Pagamento</label>
                                    <select name="method" id="edit_method" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" required>
                                        <option value="">Selecione...</option>
                                        <option value="pix">PIX</option>
                                        <option value="dinheiro">Dinheiro</option>
                                        <option value="cartao">Cartão</option>
                                        <option value="boleto">Boleto</option>
                                        <option value="transferencia">Transferência</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Valor</label>
                                    <input type="number" 
                                           name="amount" 
                                           id="edit_amount"
                                           step="0.01" 
                                           min="0.01" 
                                           max="<?php echo e($order->total); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Observações</label>
                                <textarea name="notes" 
                                          id="edit_notes"
                                          rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                                          placeholder="Observações sobre o pagamento..."></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" 
                                        onclick="toggleEditPaymentForm()" 
                                        class="px-4 py-2 text-gray-700 dark:text-slate-300 bg-gray-200 dark:bg-slate-700 rounded-md hover:bg-gray-300 dark:hover:bg-slate-600 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 transition-all shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                                    Atualizar Pagamento
                                </button>
                            </div>
                        </form>
                </div>

                <!-- Histórico de Edições -->
                <?php if (isset($component)) { $__componentOriginalde9f8f57077b8281ee1b352c157c3ffc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalde9f8f57077b8281ee1b352c157c3ffc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.edit-history','data' => ['order' => $order]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('edit-history'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalde9f8f57077b8281ee1b352c157c3ffc)): ?>
<?php $attributes = $__attributesOriginalde9f8f57077b8281ee1b352c157c3ffc; ?>
<?php unset($__attributesOriginalde9f8f57077b8281ee1b352c157c3ffc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalde9f8f57077b8281ee1b352c157c3ffc)): ?>
<?php $component = $__componentOriginalde9f8f57077b8281ee1b352c157c3ffc; ?>
<?php unset($__componentOriginalde9f8f57077b8281ee1b352c157c3ffc); ?>
<?php endif; ?>

                <!-- Histórico de Transações -->
                    <?php if($cashTransactions->count() > 0): ?>
                    <div class="border-t pt-4 mt-4">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-white mb-3">Histórico de Transações</h3>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $cashTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 rounded-md">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($transaction->description); ?></span>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo e($transaction->status === 'confirmado' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'); ?>">
                                            <?php echo e(ucfirst($transaction->status)); ?>

                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                        <?php echo e(ucfirst($transaction->payment_method)); ?> • <?php echo e($transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y H:i') : 'Data não informada'); ?>

                                        <?php if($transaction->notes): ?>
                                        <br><?php echo e($transaction->notes); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                        +R$ <?php echo e(number_format($transaction->amount, 2, ',', '.')); ?>

                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Ações de Pagamento -->
                    <?php if($order->payments->count() > 0): ?>
                    <div class="border-t pt-4 mt-4">
                        <?php
                            $totalPaymentMethods = 0;
                            foreach($order->payments as $payment) {
                                if($payment->payment_methods && is_array($payment->payment_methods)) {
                                    $totalPaymentMethods += count($payment->payment_methods);
                                } else {
                                    $totalPaymentMethods += 1;
                                }
                            }
                        ?>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-slate-400">
                                <?php echo e($totalPaymentMethods); ?> <?php echo e($totalPaymentMethods == 1 ? 'pagamento' : 'pagamentos'); ?> registrado<?php echo e($totalPaymentMethods == 1 ? '' : 's'); ?>

                            </span>
                            
                            <div class="text-sm text-gray-600 dark:text-slate-400">
                                Status: <span class="font-medium <?php echo e($remaining <= 0 ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400'); ?>">
                                    <?php echo e($remaining <= 0 ? 'Pago' : 'Pendente'); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                

                <!-- Downloads -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Downloads
                    </h2>
                    <div class="space-y-2">
                        <!-- Nota do Cliente / Nota de Venda -->
                        <a href="<?php echo e($order->is_pdv ? route('pdv.sale-receipt', $order->id) : route('orders.client-receipt', $order->id)); ?>" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 dark:from-blue-500 dark:to-blue-600 text-white text-center rounded-md hover:from-blue-700 hover:to-blue-600 dark:hover:from-blue-600 dark:hover:to-blue-700 text-sm flex items-center justify-center space-x-2 transition-all shadow-lg shadow-blue-500/20 dark:shadow-blue-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span><?php echo e($order->is_pdv ? 'Nota de Venda (PDF)' : 'Nota do Cliente (PDF)'); ?></span>
                        </a>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>
                        
                        <!-- Downloads Internos -->
                        <a href="<?php echo e(url('/kanban/download-costura/' . $order->id)); ?>" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-500 dark:from-purple-500 dark:to-purple-600 text-white text-center rounded-md hover:from-purple-700 hover:to-purple-600 dark:hover:from-purple-600 dark:hover:to-purple-700 text-sm transition-all shadow-lg shadow-purple-500/20 dark:shadow-purple-600/20">
                            Folha Costura (A4)
                        </a>
                        <a href="<?php echo e(url('/kanban/download-personalizacao/' . $order->id)); ?>" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-gradient-to-r from-pink-600 to-pink-500 dark:from-pink-500 dark:to-pink-600 text-white text-center rounded-md hover:from-pink-700 hover:to-pink-600 dark:hover:from-pink-600 dark:hover:to-pink-700 text-sm transition-all shadow-lg shadow-pink-500/20 dark:shadow-pink-600/20">
                            Folha Personalização (A4)
                        </a>
                        <?php if($order->items->first() && $order->items->first()->files->count() > 0): ?>
                        <a href="<?php echo e(url('/kanban/download-files/' . $order->id)); ?>" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-gradient-to-r from-green-600 to-green-500 dark:from-green-500 dark:to-green-600 text-white text-center rounded-md hover:from-green-700 hover:to-green-600 dark:hover:from-green-600 dark:hover:to-green-700 text-sm transition-all shadow-lg shadow-green-500/20 dark:shadow-green-600/20">
                            Arquivos da Arte
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Solicitações de Antecipação -->
                <?php if($order->deliveryRequests && $order->deliveryRequests->count() > 0): ?>
                <div class="bg-white dark:bg-slate-900 rounded-lg shadow dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📅 Solicitações</h2>
                    <?php $__currentLoopData = $order->deliveryRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-3 p-3 rounded <?php echo e($request->status === 'pendente' ? 'bg-yellow-50 dark:bg-yellow-900/20' : ($request->status === 'aprovado' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20')); ?> border <?php echo e($request->status === 'pendente' ? 'border-yellow-200 dark:border-yellow-800/30' : ($request->status === 'aprovado' ? 'border-green-200 dark:border-green-800/30' : 'border-red-200 dark:border-red-800/30')); ?>">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold <?php echo e($request->status === 'pendente' ? 'text-yellow-800 dark:text-yellow-300' : ($request->status === 'aprovado' ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300')); ?>">
                                <?php echo e(ucfirst($request->status)); ?>

                            </span>
                            <span class="text-xs text-gray-500 dark:text-slate-400"><?php echo e($request->created_at->format('d/m/Y')); ?></span>
                        </div>
                        <p class="text-xs text-gray-700 dark:text-slate-300">
                            <strong class="text-gray-900 dark:text-white">Nova data:</strong> <?php echo e($request->requested_delivery_date ? $request->requested_delivery_date->format('d/m/Y') : 'Data não informada'); ?>

                        </p>
                        <p class="text-xs text-gray-600 dark:text-slate-400 mt-1"><?php echo e($request->reason); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function togglePaymentForm() {
            const form = document.getElementById('paymentForm');
            const editForm = document.getElementById('editPaymentForm');
            form.classList.toggle('hidden');
            editForm.classList.add('hidden');
        }

        function toggleEditPaymentForm() {
            const form = document.getElementById('editPaymentForm');
            const addForm = document.getElementById('paymentForm');
            form.classList.toggle('hidden');
            addForm.classList.add('hidden');
        }

        function editPayment(paymentId, methodId = null) {
            // Buscar dados do pagamento via AJAX
            fetch(`/pedidos/<?php echo e($order->id); ?>/pagamento/${paymentId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_payment_id').value = data.id;
                    
                    // Se tiver methodId, buscar dados do método específico
                    if (methodId && data.payment_methods) {
                        const method = data.payment_methods.find(m => m.id == methodId);
                        if (method) {
                            document.getElementById('edit_method_id').value = methodId;
                            document.getElementById('edit_method').value = method.method;
                            document.getElementById('edit_amount').value = method.amount;
                            document.getElementById('edit_notes').value = data.notes || '';
                        }
                    } else {
                        // Fallback para pagamentos antigos
                        document.getElementById('edit_method_id').value = '';
                        document.getElementById('edit_method').value = data.method;
                        document.getElementById('edit_amount').value = data.entry_amount;
                        document.getElementById('edit_notes').value = data.notes || '';
                    }
                    
                    toggleEditPaymentForm();
                })
                .catch(error => {
                    console.error('Erro ao carregar dados do pagamento:', error);
                    showErrorModal('Erro ao carregar dados do pagamento. Por favor, tente novamente.');
                });
        }

        function copyToClipboard(text, buttonElement) {
            // Função para mostrar feedback visual
            function showSuccess(button) {
                if (button) {
                    const originalText = button.textContent;
                    button.textContent = 'Copiado!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-blue-600');
                    
                    setTimeout(function() {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-blue-600');
                    }, 2000);
                }
            }

            // Verificar se a API de clipboard está disponível
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showSuccess(buttonElement);
                }).catch(function(err) {
                    console.error('Erro ao copiar com clipboard API: ', err);
                    fallbackCopyTextToClipboard(text, buttonElement);
                });
            } else {
                // Fallback para navegadores mais antigos
                fallbackCopyTextToClipboard(text, buttonElement);
            }
        }

        // Função fallback para copiar texto
        function fallbackCopyTextToClipboard(text, buttonElement) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            
            // Evitar scroll para o elemento
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showSuccess(buttonElement);
                } else {
                    throw new Error('execCommand falhou');
                }
            } catch (err) {
                console.error('Fallback: Erro ao copiar texto', err);
                showErrorModal('Erro ao copiar o link. Tente selecionar e copiar manualmente.');
            }
            
            document.body.removeChild(textArea);
        }

        function showSuccess(buttonElement) {
            if (buttonElement) {
                const originalText = buttonElement.textContent;
                buttonElement.textContent = 'Copiado!';
                buttonElement.classList.add('bg-green-600');
                buttonElement.classList.remove('bg-blue-600');
                
                setTimeout(function() {
                    buttonElement.textContent = originalText;
                    buttonElement.classList.remove('bg-green-600');
                    buttonElement.classList.add('bg-blue-600');
                }, 2000);
            }
        }

        // Mostrar link de compartilhamento se foi gerado
        <?php if(session('share_url')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const shareUrl = '<?php echo e(session('share_url')); ?>';
            const input = document.querySelector('input[readonly]');
            if (input) {
                input.value = shareUrl;
            }
        });
        <?php endif; ?>

        // Função para toggle das alterações no histórico
        function toggleChanges(editId) {
            const changesDiv = document.getElementById('changes-' + editId);
            const toggleText = document.getElementById('toggle-text-' + editId);
            const toggleIcon = document.getElementById('toggle-icon-' + editId);
            
            if (changesDiv.classList.contains('hidden')) {
                changesDiv.classList.remove('hidden');
                toggleText.textContent = 'Ocultar alterações';
                toggleIcon.style.transform = 'rotate(180deg)';
            } else {
                changesDiv.classList.add('hidden');
                toggleText.textContent = 'Ver alterações';
                toggleIcon.style.transform = 'rotate(0deg)';
            }
        }
        // Modal de Erro Genérico
        function showErrorModal(message) {
            const modal = document.getElementById('error-modal');
            const messageEl = document.getElementById('error-message');
            messageEl.textContent = message;
            modal.classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('error-modal').classList.add('hidden');
        }

        // Funções do Modal de Solicitação de Edição
        function openEditRequestModal() {
            const localModal = document.getElementById('edit-request-modal');

            if (localModal) {
                localModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                return;
            }

            // Se não existir modal local (ex.: detalhes carregados dentro do Kanban),
            // delegar para a função global, se disponível
            if (window.openEditRequestModal && typeof window.openEditRequestModal === 'function') {
                window.openEditRequestModal(<?php echo e($order->id); ?>);
            }
        }

        function closeEditRequestModal() {
            const localModal = document.getElementById('edit-request-modal');

            if (localModal) {
                localModal.classList.add('hidden');
                const reasonInput = document.getElementById('edit-request-reason');
                const errorElement = document.getElementById('edit-request-error');

                if (reasonInput) {
                    reasonInput.value = '';
                }
                if (errorElement) {
                    errorElement.classList.add('hidden');
                }
                document.body.style.overflow = 'auto';
                return;
            }

            // Delegar para função global se não houver modal local
            if (window.closeEditRequestModal && typeof window.closeEditRequestModal === 'function') {
                window.closeEditRequestModal();
            }
        }

        function submitEditRequest() {
            const reasonInput = document.getElementById('edit-request-reason');
            const errorElement = document.getElementById('edit-request-error');

            // Se não estiver no contexto do modal local, delegar para a função global
            if (!reasonInput || !errorElement) {
                if (window.submitEditRequest && typeof window.submitEditRequest === 'function') {
                    window.submitEditRequest();
                }
                return;
            }

            const reason = reasonInput.value.trim();

            if (!reason) {
                errorElement.classList.remove('hidden');
                reasonInput.focus();
                return;
            }

            errorElement.classList.add('hidden');

            fetch(`/pedidos/<?php echo e($order->id); ?>/solicitar-edicao`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditRequestModal();
                    location.reload();
                } else {
                    showErrorModal(data.message || 'Erro ao enviar solicitação de edição');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showErrorModal('Erro ao enviar solicitação de edição');
            });
        }

        // Funções do Modal de Cancelamento - Expor globalmente
        window.openCancellationModal = function(orderId) {
            if (!orderId) {
                orderId = <?php echo e($order->id); ?>;
            }
            currentOrderId = orderId;
            const modal = document.getElementById('cancellationModal');
            if (modal) {
                const modalOrderIdEl = document.getElementById('modalOrderId');
                const cancellationReasonEl = document.getElementById('cancellationReason');
                const reasonErrorEl = document.getElementById('reasonError');
                
                if (modalOrderIdEl) {
                    modalOrderIdEl.textContent = '#' + String(orderId).padStart(6, '0');
                }
                if (cancellationReasonEl) {
                    cancellationReasonEl.value = '';
                }
                if (reasonErrorEl) {
                    reasonErrorEl.classList.add('hidden');
                }
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeCancellationModal = function() {
            const modal = document.getElementById('cancellationModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            currentOrderId = null;
        };

        window.submitCancellation = function() {
            const reason = document.getElementById('cancellationReason')?.value.trim();
            const errorElement = document.getElementById('reasonError');

            if (!reason) {
                if (errorElement) {
                    errorElement.classList.remove('hidden');
                }
                const reasonInput = document.getElementById('cancellationReason');
                if (reasonInput) {
                    reasonInput.focus();
                }
                return;
            }

            if (errorElement) {
                errorElement.classList.add('hidden');
            }

            const submitBtn = document.querySelector('#cancellationModal button[onclick*="submitCancellation"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Enviando...';
            }

            fetch(`/pedidos/${currentOrderId || <?php echo e($order->id); ?>}/cancelar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                const submitBtn = document.querySelector('#cancellationModal button[onclick*="submitCancellation"]');
                
                if (data.success) {
                    window.closeCancellationModal();
                    location.reload();
                } else {
                    alert(data.message || 'Erro ao enviar solicitação de cancelamento');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Solicitar Cancelamento';
                    }
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao enviar solicitação de cancelamento');
                const submitBtn = document.querySelector('#cancellationModal button[onclick*="submitCancellation"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Solicitar Cancelamento';
                }
            });
        };
        
        // Manter compatibilidade com chamadas diretas
        function openCancellationModal(orderId) {
            window.openCancellationModal(orderId);
        }

        function closeCancellationModal() {
            window.closeCancellationModal();
        }

        function submitCancellation() {
            window.submitCancellation();
        }

        let currentOrderId = null;
    </script>

    <!-- Modal de Cancelamento -->
    <div id="cancellationModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border border-gray-300 dark:border-gray-700 w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Solicitar Cancelamento</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pedido <span id="modalOrderId" class="font-semibold"></span></p>
                        </div>
                    </div>
                    <button onclick="closeCancellationModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="mt-4">
                    <label for="cancellationReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motivo do Cancelamento <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <textarea 
                        id="cancellationReason" 
                        rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Descreva o motivo pelo qual este pedido deve ser cancelado..."
                        maxlength="1000"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo de 1000 caracteres</p>
                    <p id="reasonError" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden">O motivo é obrigatório</p>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        onclick="closeCancellationModal()" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                        Cancelar
                    </button>
                    <button 
                        onclick="submitCancellation()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                        Solicitar Cancelamento
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Erro -->
    <div id="error-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow-xl dark:shadow-2xl dark:shadow-black/20 max-w-md w-full mx-4 border border-gray-200 dark:border-slate-800">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Erro</h3>
                    </div>
                </div>
                <p id="error-message" class="text-sm text-gray-600 dark:text-slate-300 mb-6"></p>
                <div class="flex justify-end">
                    <button onclick="closeErrorModal()" 
                            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 transition-all shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitação de Edição -->
    <div id="edit-request-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow-xl dark:shadow-2xl dark:shadow-black/20 max-w-md w-full mx-4 border border-gray-200 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Solicitar Edição do Pedido</h3>
            </div>
            <div class="p-6">
                <label for="edit-request-reason" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                    Motivo da edição: <span class="text-red-600 dark:text-red-400">*</span>
                </label>
                <textarea 
                    id="edit-request-reason"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all"
                    placeholder="Descreva o motivo da solicitação de edição..."
                ></textarea>
                <p id="edit-request-error" class="hidden text-sm text-red-600 dark:text-red-400 mt-2">Por favor, informe o motivo da edição.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50 flex justify-end space-x-3 rounded-b-lg border-t border-gray-200 dark:border-slate-800">
                <button onclick="closeEditRequestModal()" 
                        class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-md hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </button>
                <button onclick="submitEditRequest()" 
                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 text-white rounded-md hover:from-indigo-700 hover:to-indigo-600 dark:hover:from-indigo-600 dark:hover:to-indigo-700 transition-all shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                    Solicitar Edição
                </button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/orders/show.blade.php ENDPATH**/ ?>