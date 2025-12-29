<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-8">
    <div class="mb-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Assinatura</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Atualize os dados do tenant e seu status de assinatura.</p>
    </div>

    <?php if($errors->any()): ?>
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <form method="POST" action="<?php echo e(route('admin.tenants.update', $tenant)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código da Loja</label>
                        <input type="text" value="<?php echo e($tenant->store_code); ?>" disabled
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700 text-gray-700 dark:text-gray-300 shadow-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de Criação</label>
                        <input type="text" value="<?php echo e($tenant->created_at->format('d/m/Y H:i')); ?>" disabled
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700 text-gray-700 dark:text-gray-300 shadow-sm cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nome do Cliente / Empresa
                    </label>
                    <input type="text" name="name" id="name" required
                        value="<?php echo e(old('name', $tenant->name)); ?>"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email do Admin
                    </label>
                    <input type="email" name="email" id="email" required
                        value="<?php echo e(old('email', $tenant->email)); ?>"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Plano
                        </label>
                        <select name="plan_id" id="plan_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($plan->id); ?>" <?php echo e($tenant->plan_id == $plan->id ? 'selected' : ''); ?>>
                                    <?php echo e($plan->name); ?> (R$ <?php echo e(number_format($plan->price, 2, ',', '.')); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="active" <?php echo e($tenant->status == 'active' ? 'selected' : ''); ?>>Ativo</option>
                            <option value="suspended" <?php echo e($tenant->status == 'suspended' ? 'selected' : ''); ?>>Suspenso</option>
                            <option value="cancelled" <?php echo e($tenant->status == 'cancelled' ? 'selected' : ''); ?>>Cancelado</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="subscription_ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Vencimento da Assinatura
                    </label>
                    <input type="date" name="subscription_ends_at" id="subscription_ends_at"
                        value="<?php echo e($tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('Y-m-d') : ''); ?>"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Deixe em branco para acesso vitalício/indefinido.</p>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="<?php echo e(route('admin.tenants.index')); ?>"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-sm uppercase tracking-wide">
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/admin/tenants/edit.blade.php ENDPATH**/ ?>