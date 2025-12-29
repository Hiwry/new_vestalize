<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition overflow-hidden">
    <?php if($transaction->order_id): ?>
    <a href="<?php echo e(route('orders.show', $transaction->order_id)); ?>" class="block p-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
        <div class="flex justify-between items-start mb-2">
            <span class="text-xs font-semibold px-2 py-1 rounded <?php echo e($transaction->type === 'entrada' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'); ?>">
                <?php echo e($transaction->type === 'entrada' ? '↑ Entrada' : '↓ Saída'); ?>

            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($transaction->transaction_date->format('d/m H:i')); ?></span>
        </div>
        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1"><?php echo e($transaction->category); ?></p>
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2"><?php echo e($transaction->description); ?></p>
        
        <!-- Meios de Pagamento -->
        <?php
            $paymentMethods = $transaction->payment_methods ?? [];

            // Se vier como string (JSON ou outro), tentar decodificar
            if (!is_array($paymentMethods) && !empty($paymentMethods)) {
                $decoded = json_decode($paymentMethods, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $paymentMethods = $decoded;
                } else {
                    $paymentMethods = [];
                }
            }

            // Fallback: se ainda estiver vazio mas houver payment_method simples
            if ((empty($paymentMethods) || !is_array($paymentMethods)) && $transaction->payment_method) {
                $paymentMethods = [[
                    'method' => $transaction->payment_method,
                    'amount' => $transaction->amount,
                ]];
            }
        ?>
        <?php if(is_array($paymentMethods) && count($paymentMethods) > 0): ?>
        <div class="mb-2 space-y-1">
            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex justify-between text-xs">
                <span class="text-gray-500 dark:text-gray-400 capitalize"><?php echo e(str_replace('_', ' ', $method['method'] ?? $transaction->payment_method)); ?></span>
                <span class="text-gray-600 dark:text-gray-400">R$ <?php echo e(number_format($method['amount'] ?? $transaction->amount, 2, ',', '.')); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        
        <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-700">
            <span class="text-sm font-bold <?php echo e($transaction->type === 'entrada' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?>">
                <?php echo e($transaction->type === 'entrada' ? '+' : '-'); ?> R$ <?php echo e(number_format($transaction->amount, 2, ',', '.')); ?>

            </span>
            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                Pedido #<?php echo e(str_pad($transaction->order_id, 6, '0', STR_PAD_LEFT)); ?>

            </span>
        </div>
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Vendedor: <?php echo e($transaction->user_name ?? 'Sistema'); ?>

        </div>
    </a>
    <div class="px-3 pb-3">
        <a href="<?php echo e(route('cash.edit', $transaction)); ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline" onclick="event.stopPropagation()">Editar</a>
    </div>
    <?php else: ?>
    <div class="p-3">
        <div class="flex justify-between items-start mb-2">
            <span class="text-xs font-semibold px-2 py-1 rounded <?php echo e($transaction->type === 'entrada' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'); ?>">
                <?php echo e($transaction->type === 'entrada' ? '↑ Entrada' : '↓ Saída'); ?>

            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($transaction->transaction_date->format('d/m H:i')); ?></span>
        </div>
        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1"><?php echo e($transaction->category); ?></p>
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2"><?php echo e($transaction->description); ?></p>
        
        <!-- Meios de Pagamento -->
        <?php
            $paymentMethods = $transaction->payment_methods ?? [];

            // Se vier como string (JSON ou outro), tentar decodificar
            if (!is_array($paymentMethods) && !empty($paymentMethods)) {
                $decoded = json_decode($paymentMethods, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $paymentMethods = $decoded;
                } else {
                    $paymentMethods = [];
                }
            }

            // Fallback: se ainda estiver vazio mas houver payment_method simples
            if ((empty($paymentMethods) || !is_array($paymentMethods)) && $transaction->payment_method) {
                $paymentMethods = [[
                    'method' => $transaction->payment_method,
                    'amount' => $transaction->amount,
                ]];
            }
        ?>
        <?php if(is_array($paymentMethods) && count($paymentMethods) > 0): ?>
        <div class="mb-2 space-y-1">
            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex justify-between text-xs">
                <span class="text-gray-500 dark:text-gray-400 capitalize"><?php echo e(str_replace('_', ' ', $method['method'] ?? $transaction->payment_method)); ?></span>
                <span class="text-gray-600 dark:text-gray-400">R$ <?php echo e(number_format($method['amount'] ?? $transaction->amount, 2, ',', '.')); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        
        <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-700">
            <span class="text-sm font-bold <?php echo e($transaction->type === 'entrada' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?>">
                <?php echo e($transaction->type === 'entrada' ? '+' : '-'); ?> R$ <?php echo e(number_format($transaction->amount, 2, ',', '.')); ?>

            </span>
            <a href="<?php echo e(route('cash.edit', $transaction)); ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Editar</a>
        </div>
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Vendedor: <?php echo e($transaction->user_name ?? 'Sistema'); ?>

        </div>
    </div>
    <?php endif; ?>
</div>

<?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/cash/partials/transaction-card.blade.php ENDPATH**/ ?>