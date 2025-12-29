<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="self-start md:self-auto">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Orçamentos</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie todos os orçamentos</p>
        </div>
        <a href="<?php echo e(route('budget.start')); ?>" 
           class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-center shadow-sm">
            + Novo Orçamento
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="mb-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 mb-2 md:p-6" x-data="{ filtersOpen: window.innerWidth >= 768 }">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center md:hidden" @click="filtersOpen = !filtersOpen">
            <h3 class="font-medium text-gray-700 dark:text-gray-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                Filtros
            </h3>
            <button class="text-gray-500 dark:text-gray-400 focus:outline-none">
                <svg class="w-5 h-5 transform transition-transform duration-200" :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>
        
        <div x-show="filtersOpen" x-transition x-cloak class="p-4 md:p-0">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                        <input type="text" 
                               name="search" 
                               value="<?php echo e(request('search')); ?>"
                               placeholder="Número, cliente..."
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                            <option value="" <?php echo e(request('status') === null || request('status') === '' ? 'selected' : ''); ?>>Todos</option>
                            <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pendente</option>
                            <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Aprovado</option>
                            <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejeitado</option>
                            <option value="expired" <?php echo e(request('status') === 'expired' ? 'selected' : ''); ?>>Expirado</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="md:col-span-2 flex flex-col md:flex-row gap-2">
                        <button type="submit" 
                                class="w-full md:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Filtrar
                        </button>
                        <a href="<?php echo e(route('budget.index')); ?>" 
                           class="w-full md:w-auto px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-center transition">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista Mobile -->
    <div class="space-y-4 md:hidden">
        <?php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'approved' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
                'expired' => 'bg-gray-200 text-gray-800',
            ];
            $statusLabels = [
                'pending' => 'Pendente',
                'approved' => 'Aprovado',
                'rejected' => 'Rejeitado',
                'expired' => 'Expirado',
            ];
        ?>
        <?php $__empty_1 = true; $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 relative overflow-hidden">
            <div class="pl-1">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400">#<?php echo e(str_pad($budget->id, 6, '0', STR_PAD_LEFT)); ?></span>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100"><?php echo e($budget->client->name); ?></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Criado em <?php echo e($budget->created_at->format('d/m/Y')); ?></p>
                    </div>
                    <div class="text-right space-y-1">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md whitespace-nowrap <?php echo e($statusColors[$budget->status] ?? 'bg-gray-200 text-gray-800'); ?>">
                            <?php echo e($statusLabels[$budget->status] ?? ucfirst($budget->status)); ?>

                        </span>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Validade: <?php echo e($budget->valid_until->format('d/m/Y')); ?></div>
                    </div>
                </div>

                <div class="space-y-2 mb-3">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="font-semibold text-gray-900 dark:text-white">R$ <?php echo e(number_format($budget->total, 2, ',', '.')); ?></span>
                    </div>
                    <?php if($budget->order_id || $budget->order_number): ?>
                    <div class="flex items-center text-sm text-indigo-600 dark:text-indigo-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6" /></svg>
                        <?php if($budget->order_id): ?>
                            Pedido #<?php echo e(str_pad($budget->order_id, 6, '0', STR_PAD_LEFT)); ?>

                        <?php else: ?>
                            Pedido #<?php echo e($budget->order_number); ?>

                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <a href="<?php echo e(route('budget.show', $budget->id)); ?>" class="w-full sm:w-auto text-center py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium rounded-lg">
                        Ver detalhes
                    </a>
                    <a href="<?php echo e(route('budget.pdf', $budget->id)); ?>" class="w-full sm:w-auto text-center py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg">
                        PDF
                    </a>
                    <?php if($budget->status === 'approved'): ?>
                    <a href="<?php echo e(route('budget.convert-to-order', $budget->id)); ?>" class="w-full sm:w-auto text-center py-2 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm font-medium rounded-lg">
                        Converter
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-xl">
            <p class="text-gray-500 dark:text-gray-400">Nenhum orçamento encontrado.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tabela Desktop -->
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Validade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php
                        $statusColorsDesktop = [
                            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                            'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                            'expired' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                        ];
                        $statusLabelsDesktop = [
                            'pending' => 'Pendente',
                            'approved' => 'Aprovado',
                            'rejected' => 'Rejeitado',
                            'expired' => 'Expirado',
                        ];
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            <?php echo e($budget->budget_number); ?>

                            <?php if($budget->order_id): ?>
                            <a href="<?php echo e(route('orders.show', $budget->order_id)); ?>" 
                               class="block text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline mt-1 transition-colors">
                                Pedido: #<?php echo e(str_pad($budget->order_id, 6, '0', STR_PAD_LEFT)); ?>

                            </a>
                            <?php elseif($budget->order_number): ?>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Pedido: #<?php echo e($budget->order_number); ?>

                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            <?php echo e($budget->client->name); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <?php echo e($budget->created_at->format('d/m/Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <?php echo e($budget->valid_until->format('d/m/Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            R$ <?php echo e(number_format($budget->total, 2, ',', '.')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($statusColorsDesktop[$budget->status] ?? ''); ?>">
                                <?php echo e($statusLabelsDesktop[$budget->status] ?? $budget->status); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                            <a href="<?php echo e(route('budget.show', $budget->id)); ?>" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                Ver
                            </a>
                            <a href="<?php echo e(route('budget.pdf', $budget->id)); ?>" 
                               class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                PDF
                            </a>
                            <?php if($budget->status === 'approved'): ?>
                            <a href="<?php echo e(route('budget.convert-to-order', $budget->id)); ?>" 
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                Converter
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Nenhum orçamento encontrado.
                            <a href="<?php echo e(route('budget.start')); ?>" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">
                                Criar primeiro orçamento
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    <?php if($budgets->hasPages()): ?>
    <div class="pb-4">
        <?php echo e($budgets->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/budgets/index.blade.php ENDPATH**/ ?>