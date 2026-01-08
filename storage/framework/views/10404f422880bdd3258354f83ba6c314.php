

<?php $__env->startSection('header'); ?>
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        <?php echo e(__('Minha Assinatura')); ?>

    </h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        
        <?php if(!$tenant->trial_ends_at || $tenant->trial_ends_at->isPast()): ?>
            <div class="bg-indigo-900/20 border border-indigo-500/30 rounded-xl p-6 text-white shadow-sm overflow-hidden relative">
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-gift text-indigo-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Experimente o Plano Médio por 7 Dias Grátis!</h3>
                            <p class="text-gray-400 text-sm mt-0.5">Explore todas as ferramentas de produtividade sem compromisso.</p>
                        </div>
                    </div>
                    <?php $medioPlan = $allPlans->where('name', 'Plano Medio')->first() ?? $allPlans->sortByDesc('price')->first(); ?>
                    <?php if($medioPlan && (!$currentPlan || $medioPlan->price > $currentPlan->price)): ?>
                    <form action="<?php echo e(route('subscription.trial', $medioPlan)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition-all shadow-sm whitespace-nowrap">
                            Ativar Teste Agora
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-emerald-500/30 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-qrcode text-2xl text-emerald-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pagamento Instantâneo PIX</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">Pague e libere sua conta na hora</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Selecione um plano abaixo e clique em <span class="font-bold text-emerald-500">"Pagar com PIX"</span> para gerar seu QR Code.</p>

                    <div id="pix-display-area" class="hidden animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-emerald-500/20 mb-4 flex flex-col items-center">
                            <img id="pix-qr-code" src="" alt="QR Code PIX" class="w-48 h-48 mb-4 border-4 border-white dark:border-gray-700 rounded-lg shadow-sm">
                            
                            <a id="pix-ticket-url" href="#" target="_blank" class="text-emerald-600 dark:text-emerald-400 text-xs font-bold hover:underline mb-4 flex items-center">
                                <i class="fa-solid fa-arrow-up-right-from-square mr-1.5"></i> Abrir QR Code em nova aba
                            </a>

                            <div class="w-full">
                                <span class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold block mb-1">Copia e Cola PIX</span>
                                <div class="flex items-center justify-between gap-2 bg-white dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <code class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 break-all line-clamp-2" id="pix-copy-paste"></code>
                                    <button onclick="copyContent('pix-copy-paste')" class="shrink-0 p-2 text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-center">
                <h4 class="font-bold text-gray-900 dark:text-white mb-2">Precisa de Ajuda?</h4>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Contate nosso suporte para ativação ou dúvidas sobre pagamentos.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="https://wa.me/5500000000000" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors">
                        <i class="fa-brands fa-whatsapp mr-2 text-sm"></i> WhatsApp
                    </a>
                    <a href="mailto:contato@vestalize.com" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold rounded-lg transition-colors">
                        <i class="fa-solid fa-envelope mr-2 text-sm"></i> E-mail
                    </a>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Seu Plano Atual</span>
                            <?php if($tenant->status === 'active'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Ativo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <?php echo e(ucfirst($tenant->status)); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-baseline gap-4">
                            <?php
                                $isTrial = $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
                                $remainingTrialDays = $isTrial ? ceil(now()->diffInHours($tenant->trial_ends_at) / 24) : 0;
                            ?>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                                <?php if($isTrial): ?>
                                    Teste por <?php echo e((int)$remainingTrialDays); ?> <?php echo e($remainingTrialDays > 1 ? 'dias' : 'dia'); ?>

                                    <span class="text-lg font-normal text-indigo-500">(<?php echo e($currentPlan?->name); ?>)</span>
                                <?php else: ?>
                                    <?php echo e($currentPlan ? $currentPlan->name : 'Sem Plano'); ?>

                                <?php endif; ?>
                            </h3>
                            <?php if($currentPlan && !$isTrial): ?>
                            <span class="text-lg text-gray-600 dark:text-gray-400">
                                R$ <?php echo e(number_format($currentPlan->price, 2, ',', '.')); ?><span class="text-sm">/mês</span>
                            </span>
                            <?php endif; ?>
                        </div>

                        
                         <?php if($currentPlan && $currentPlan->limits): ?>
                            <div class="mt-6 flex gap-6 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-store text-gray-400"></i>
                                    <span>
                                        <strong><?php echo e($tenant->stores()->count()); ?></strong> / <?php echo e($currentPlan->limits['stores'] ?? 1); ?> Lojas
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users text-gray-400"></i>
                                    <span>
                                        <strong><?php echo e($tenant->users()->count()); ?></strong> / <?php echo e($currentPlan->limits['users'] ?? 2); ?> Usuários
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    
                    <div class="text-left md:text-right">
                        <?php
                            $endDate = $isTrial ? $tenant->trial_ends_at : $tenant->subscription_ends_at;
                        ?>
                        
                        <?php if($endDate): ?>
                            <?php
                                $daysLeft = ceil(now()->diffInHours($endDate, false) / 24);
                                $isExpired = $daysLeft < 0;
                                $isExpiring = $daysLeft >= 0 && $daysLeft <= 7;
                            ?>
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                <?php echo e($isTrial ? 'Expira em' : 'Renovação em'); ?>

                            </p>
                            <p class="text-xl font-semibold <?php echo e($isExpired ? 'text-red-600' : ($isExpiring ? 'text-amber-600' : 'text-gray-900 dark:text-white')); ?>">
                                <?php echo e($endDate->format('d/m/Y')); ?>

                            </p>
                            <p class="text-sm mt-1 <?php echo e($isExpired ? 'text-red-500' : ($isExpiring ? 'text-amber-500' : 'text-gray-400')); ?>">
                                <?php if($isExpired): ?>
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> Expirado há <?php echo e((int)abs($daysLeft)); ?> dias
                                <?php else: ?>
                                    <i class="fa-solid fa-clock mr-1"></i> <?php echo e((int)$daysLeft); ?> <?php echo e((int)$daysLeft > 1 ? 'dias restantes' : 'dia restante'); ?>

                                <?php endif; ?>
                            </p>

                            <?php if(!$isTrial && now()->diffInDays($tenant->subscription_ends_at, false) <= 30): ?>
                                <div class="mt-4">
                                     <form action="<?php echo e(route('subscription.renew')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
                                            <i class="fa-solid fa-rotate mr-2"></i> Renovar Agora
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-500">Assinatura vitalícia ou não definida</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Planos Disponíveis</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $__currentLoopData = $allPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $onTrial = $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
                        $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id && !$onTrial;
                        $isUpgrade = !$currentPlan || $plan->price > $currentPlan->price || $onTrial;
                    ?>
                    
                    <div class="flex flex-col bg-white dark:bg-gray-800 rounded-lg <?php echo e(($currentPlan && $currentPlan->id === $plan->id) ? 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-900' : 'border border-gray-100 dark:border-gray-700'); ?> shadow-sm transition-all hover:shadow-md">
                        <div class="p-6 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg"><?php echo e($plan->name); ?></h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 min-h-[40px]"><?php echo e($plan->description); ?></p>
                                </div>
                                <?php if($currentPlan && $currentPlan->id === $plan->id): ?>
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400">
                                        <?php if($onTrial): ?>
                                            <i class="fa-solid fa-clock text-xs"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-check text-xs"></i>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-6">
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">R$ <?php echo e(number_format($plan->price, 2, ',', '.')); ?></span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">/mês</span>
                            </div>
                            
                            <?php if($plan->features): ?>
                                <ul class="space-y-3 mb-6">
                                    <?php $__currentLoopData = $plan->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                            <?php if($feature === '*'): ?>
                                                 <i class="fa-solid fa-star text-indigo-500 mt-0.5 mr-2.5"></i>
                                                 <span class="font-medium">Acesso Completo</span>
                                            <?php else: ?>
                                                <i class="fa-solid fa-check text-green-500 mt-0.5 mr-2.5"></i>
                                                <?php echo e(\App\Models\Plan::AVAILABLE_FEATURES[$feature] ?? $feature); ?>

                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    
                                    
                                    <?php if($plan->limits): ?>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300 pt-2 border-t border-gray-100 dark:border-gray-700 mt-2">
                                            <i class="fa-solid fa-store text-gray-400 mr-2.5"></i>
                                            <?php echo e($plan->limits['stores'] ?? 1); ?> Lojas
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fa-solid fa-users text-gray-400 mr-2.5"></i>
                                            <?php echo e($plan->limits['users'] ?? 2); ?> Usuários
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <div class="p-6 pt-0 mt-auto">
                            <?php if($isCurrentPlan): ?>
                                <button disabled class="w-full py-2.5 px-4 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/50 cursor-default">
                                    Sua Assinatura Atual
                                </button>
                            <?php elseif($isUpgrade || $onTrial): ?>
                                <div class="space-y-3">
                                    <form action="<?php echo e(route('mercadopago.create-preference', $plan)); ?>" method="POST" id="mp-form-<?php echo e($plan->id); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="inline-flex w-full justify-center py-2.5 px-4 bg-gray-900 hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white rounded-md text-sm font-medium transition-colors shadow-sm">
                                            Pagar com Mercado Pago
                                        </button>
                                    </form>
                                    <button type="button" onclick="generatePix('<?php echo e($plan->id); ?>')" class="inline-flex w-full justify-center py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm font-medium transition-colors shadow-sm">
                                        <i class="fa-solid fa-qrcode mr-2"></i> Pagar com PIX
                                    </button>
                                    <script>
                                    document.getElementById('mp-form-<?php echo e($plan->id); ?>').addEventListener('submit', async (e) => {
                                        e.preventDefault();
                                        const btn = e.target.querySelector('button');
                                        btn.disabled = true;
                                        btn.textContent = 'Carregando...';
                                        
                                        try {
                                            const response = await fetch('<?php echo e(route('mercadopago.create-preference', $plan)); ?>', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                                }
                                            });
                                            const data = await response.json();
                                            if (data.error) {
                                                alert(data.error);
                                                btn.disabled = false;
                                                btn.textContent = 'Pagar com Mercado Pago';
                                            } else {
                                                window.location.href = data.init_point;
                                            }
                                        } catch (error) {
                                            alert('Erro ao processar pagamento');
                                            btn.disabled = false;
                                            btn.textContent = 'Pagar com Mercado Pago';
                                        }
                                    });
                                    </script>
                                    <form action="<?php echo e(route('subscription.trial', $plan)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="w-full py-2.5 px-4 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium transition-colors">
                                           <i class="fa-solid fa-gift mr-1.5 text-indigo-500"></i> Testar 7 Dias
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                 <button disabled class="w-full py-2.5 px-4 border border-gray-200 dark:border-gray-700 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/50 cursor-default">
                                    Plano Inferior
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="text-center pt-8 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Precisa de um plano personalizado? 
                <a href="mailto:suporte@vestalize.com" class="text-indigo-600 hover:text-indigo-500 font-medium inline-flex items-center">
                    Fale conosco <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </a>
            </p>
        </div>
        
    </div>
</div>


<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Histórico de Pagamentos</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Mostrando os últimos 10</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plano</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intent</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $subscriptionPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        <?php echo e(optional($payment->paid_at ?? $payment->created_at)->format('d/m/Y H:i')); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        <?php echo e($payment->plan->name ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        R$ <?php echo e(number_format($payment->amount, 2, ',', '.')); ?> <?php echo e(strtoupper($payment->currency)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            <?php echo e($payment->status === 'succeeded' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'); ?>">
                                            <?php echo e(ucfirst($payment->status)); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        <?php echo e($payment->payment_intent_id ?? '-'); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Nenhum pagamento registrado.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function copyContent(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            notify('Copiado para a área de transferência!', 'success', 2000);
        }).catch(err => {
            notify('Erro ao copiar', 'error');
        });
    }

    async function generatePix(planId) {
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Gerando PIX...';

        try {
            const response = await fetch(`/mercadopago/pix/${planId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            });

            const data = await response.json();

            if (data.error) {
                showToast(data.error, 'error');
            } else {
                document.getElementById('pix-qr-code').src = `data:image/png;base64,${data.qr_code_base64}`;
                document.getElementById('pix-copy-paste').innerText = data.qr_code;
                
                // Ajustar link do ticket (pode ser # no fallback)
                const ticketLink = document.getElementById('pix-ticket-url');
                if (data.ticket_url && data.ticket_url !== '#') {
                    ticketLink.href = data.ticket_url;
                    ticketLink.classList.remove('hidden');
                } else {
                    ticketLink.classList.add('hidden');
                }
                
                document.getElementById('pix-display-area').classList.remove('hidden');
                
                // Se for fallback, mostrar nota especial
                if (data.source === 'pixservice') {
                    showToast('PIX gerado! Após o pagamento, envie o comprovante para ativar seu plano. Chave: ' + data.pix_key, 'warning', 8000);
                } else {
                    showToast('PIX gerado com sucesso! Escaneie o QR Code abaixo.', 'success');
                }
                
                // Rolar até a área do PIX
                document.getElementById('pix-display-area').scrollIntoView({ behavior: 'smooth' });
            }
        } catch (error) {
            console.error('Error generating PIX:', error);
            showToast('Erro ao gerar PIX. Tente novamente mais tarde.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }
    
    // Toast Notification System
    function showToast(message, type = 'info', duration = 5000) {
        const container = document.getElementById('toast-container') || createToastContainer();
        
        const colors = {
            success: 'bg-emerald-600',
            error: 'bg-red-600',
            warning: 'bg-amber-500',
            info: 'bg-blue-600'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-start gap-3 max-w-md transform transition-all duration-300 translate-x-full opacity-0`;
        toast.innerHTML = `
            <i class="fa-solid ${icons[type]} text-lg mt-0.5 shrink-0"></i>
            <span class="text-sm">${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white shrink-0">
                <i class="fa-solid fa-times"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });
        
        // Auto remove
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
        document.body.appendChild(container);
        return container;
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/subscription/index.blade.php ENDPATH**/ ?>