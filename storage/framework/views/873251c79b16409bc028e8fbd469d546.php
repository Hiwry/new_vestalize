

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Personalização de Marca (White-labeling)
    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form action="<?php echo e(route('settings.branding.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <?php echo csrf_field(); ?>
                    
                    <div>
                        <h3 class="text-lg font-medium">Logotipo da Empresa</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Este logo será exibido no cabeçalho do sistema e nos documentos PDF de pedidos e orçamentos.
                        </p>
                        
                        <div class="mt-4 flex items-center space-x-6">
                            <div class="shrink-0 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg flex items-center justify-center w-32 h-32">
                                <?php if($tenant->logo_path): ?>
                                    <img class="h-24 w-auto object-contain" src="<?php echo e(Storage::url($tenant->logo_path)); ?>" alt="Logo atual">
                                <?php else: ?>
                                    <svg class="h-20 w-20 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <label class="block">
                                <span class="sr-only">Escolher logo</span>
                                <input type="file" name="logo" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300
                                ">
                                <p class="mt-2 text-xs text-gray-500">PNG, JPG ou SVG. Máximo de 2MB.</p>
                            </label>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium">Cor Principal</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Usada em botões e destaques principais.
                            </p>
                            <div class="mt-4 flex items-center space-x-3">
                                <input type="color" name="primary_color" value="<?php echo e($tenant->primary_color ?? '#4f46e5'); ?>" 
                                    class="h-10 w-20 p-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 cursor-pointer">
                                <span class="font-mono text-sm uppercase"><?php echo e($tenant->primary_color ?? '#4f46e5'); ?></span>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium">Cor Secundária</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Usada para gradientes e elementos secundários.
                            </p>
                            <div class="mt-4 flex items-center space-x-3">
                                <input type="color" name="secondary_color" value="<?php echo e($tenant->secondary_color ?? '#7c3aed'); ?>" 
                                    class="h-10 w-20 p-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 cursor-pointer">
                                <span class="font-mono text-sm uppercase"><?php echo e($tenant->secondary_color ?? '#7c3aed'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['type' => 'button','onclick' => 'window.location=\''.e(route('settings.index')).'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','onclick' => 'window.location=\''.e(route('settings.index')).'\'']); ?>
                            Cancelar
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                            Salvar Alterações
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700 dark:text-indigo-300">
                        <strong>Dica:</strong> Em breve essas cores serão aplicadas automaticamente a todo o visual do seu painel e relatórios!
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/settings/branding.blade.php ENDPATH**/ ?>