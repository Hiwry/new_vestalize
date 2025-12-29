<?php $__env->startPush('styles'); ?>
<style>
    .card-hover {
        transition: all 0.2s ease;
    }
    .card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .dark .card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Preços de Personalização</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure os preços para cada tipo de personalização</p>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200"><?php echo e(session('success')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200"><?php echo e(session('error')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php $__currentLoopData = $pricesByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $typeData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden card-hover">
                    <!-- Card Header -->
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-md flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate"><?php echo e($typeData['label']); ?></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tipo de Personalização</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5">
                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100"><?php echo e($typeData['sizes']->count()); ?></div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Tamanhos</div>
                            </div>
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100"><?php echo e($typeData['total_ranges']); ?></div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Faixas</div>
                            </div>
                        </div>

                        <!-- Config Badges -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <?php if($typeData['charge_by_color'] ?? false): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 text-xs rounded border border-yellow-200 dark:border-yellow-800">
                                🎨 Cor
                            </span>
                            <?php endif; ?>
                            <?php if(($typeData['discount_2nd'] ?? 0) > 0): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs rounded border border-green-200 dark:border-green-800">
                                🏷️ <?php echo e(number_format($typeData['discount_2nd'], 0)); ?>%
                            </span>
                            <?php endif; ?>
                            <?php if(($typeData['special_options_count'] ?? 0) > 0): ?>
                            <span class="inline-flex items-center px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded border border-purple-200 dark:border-purple-800">
                                ✨ <?php echo e($typeData['special_options_count']); ?>

                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Sizes List -->
                        <?php if($typeData['sizes']->count() > 0): ?>
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tamanhos disponíveis:</p>
                            <div class="flex flex-wrap gap-1.5">
                                <?php $__currentLoopData = $typeData['sizes']->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">
                                    <?php echo e($size->size_name); ?>

                                </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($typeData['sizes']->count() > 6): ?>
                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs rounded">
                                    +<?php echo e($typeData['sizes']->count() - 6); ?>

                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4 mb-4">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum preço configurado</p>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="<?php echo e(route('admin.personalization-prices.edit', $typeKey)); ?>" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2.5 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm font-medium rounded-md transition-colors duration-150">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Preços
                            </a>
                            <?php if(isset($settings[$typeKey])): ?>
                            <a href="<?php echo e(route('admin.personalization-settings.edit', $typeKey)); ?>" 
                               class="inline-flex items-center justify-center px-3 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-150"
                               title="Configurações">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- LocalizaçõÇæes de AplicaÇõÇœ -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Localizações de Aplicação</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gerencie as opções mostradas na personalização</p>
                    </div>
                    <form method="POST" action="<?php echo e(route('admin.personalization-prices.locations.store')); ?>" class="flex items-center gap-2">
                        <?php echo csrf_field(); ?>
                        <input type="text" name="name" placeholder="Ex: Frente, Costa, Manga Esquerda" required
                               class="w-40 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-green-600 dark:bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 dark:hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                    </form>
                </div>
                <div class="p-5 space-y-2 max-h-[420px] overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100"><?php echo e($location->name); ?></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Ordem: <?php echo e($location->order ?? '-'); ?></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="<?php echo e(route('admin.personalization-prices.locations.toggle-pdf', $location)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border <?php echo e($location->show_in_pdf ?? true ? 'border-blue-300 text-blue-700 bg-blue-50 dark:border-blue-700 dark:text-blue-200 dark:bg-blue-900/30' : 'border-gray-300 text-gray-600 bg-white dark:border-gray-600 dark:text-gray-200 dark:bg-gray-700'); ?>"
                                            title="Mostrar no PDF">
                                        📄 <?php echo e($location->show_in_pdf ?? true ? 'PDF' : 'Sem PDF'); ?>

                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('admin.personalization-prices.locations.toggle', $location)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="active" value="<?php echo e($location->active ? 0 : 1); ?>">
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border <?php echo e($location->active ? 'border-green-300 text-green-700 bg-green-50 dark:border-green-700 dark:text-green-200 dark:bg-green-900/30' : 'border-gray-300 text-gray-600 bg-white dark:border-gray-600 dark:text-gray-200 dark:bg-gray-700'); ?>">
                                        <?php echo e($location->active ? 'Ativa' : 'Inativa'); ?>

                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('admin.personalization-prices.locations.destroy', $location)); ?>" id="delete-location-form-<?php echo e($location->id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button" onclick="openDeleteLocationModal('delete-location-form-<?php echo e($location->id); ?>', '<?php echo e(addslashes($location->name)); ?>')" class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Remover">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma localizaÇõÇœ cadastrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de ConfirmaÇõÇœo de ExclusÇœo -->
    <div id="delete-location-modal" class="hidden fixed inset-0 bg-black/60 dark:bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center px-4">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Remover localizaÇõÇœo?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Esta aÇõÇœo nÇœo pode ser desfeita.</p>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-800 dark:text-gray-200">Deseja remover a localizaÇõÇœ <span id="delete-location-name" class="font-semibold text-indigo-600 dark:text-indigo-400"></span>?</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                <button type="button" onclick="closeDeleteLocationModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">Cancelar</button>
                <button type="button" onclick="confirmDeleteLocation()" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Remover</button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let deleteLocationFormId = null;

    function openDeleteLocationModal(formId, name) {
        deleteLocationFormId = formId;
        document.getElementById('delete-location-name').textContent = name;
        document.getElementById('delete-location-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteLocationModal() {
        deleteLocationFormId = null;
        document.getElementById('delete-location-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmDeleteLocation() {
        if (!deleteLocationFormId) return;
        const form = document.getElementById(deleteLocationFormId);
        if (form) {
            form.submit();
        }
        closeDeleteLocationModal();
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/admin/personalization-prices/index.blade.php ENDPATH**/ ?>