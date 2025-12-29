

<?php $__env->startSection('content'); ?>
<div class="mb-6 space-y-4">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Dashboard - Admin Geral</h1>
    </div>
    
    <!-- Filtros -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <!-- Filtro de Período -->
        <form method="GET" action="<?php echo e(route('dashboard')); ?>" id="periodFilterForm" class="flex flex-wrap items-center gap-2">
            <?php if(isset($selectedStoreId)): ?>
                <input type="hidden" name="store_id" value="<?php echo e($selectedStoreId); ?>">
            <?php endif; ?>
            <label for="period" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Período:</label>
            <select name="period" id="period" 
                    onchange="this.form.submit()"
                    class="w-full xs:w-auto min-w-[140px] px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="today" <?php echo e(($period ?? 'month') === 'today' ? 'selected' : ''); ?>>Hoje</option>
                <option value="week" <?php echo e(($period ?? 'month') === 'week' ? 'selected' : ''); ?>>Esta Semana</option>
                <option value="month" <?php echo e(($period ?? 'month') === 'month' ? 'selected' : ''); ?>>Este Mês</option>
                <option value="year" <?php echo e(($period ?? 'month') === 'year' ? 'selected' : ''); ?>>Este Ano</option>
                <option value="custom" <?php echo e(($period ?? 'month') === 'custom' ? 'selected' : ''); ?>>Personalizado</option>
            </select>
            
            <?php if(($period ?? 'month') === 'custom'): ?>
            <input type="date" name="start_date" value="<?php echo e($startDate->format('Y-m-d') ?? ''); ?>" 
                   class="w-full xs:w-auto px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
            <input type="date" name="end_date" value="<?php echo e($endDate->format('Y-m-d') ?? ''); ?>" 
                   class="w-full xs:w-auto px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
            <?php endif; ?>
        </form>
        
        <!-- Filtro de Loja -->
        <?php if(isset($stores) && $stores->count() > 0): ?>
        <form method="GET" action="<?php echo e(route('dashboard')); ?>" id="storeFilterForm" class="flex flex-wrap items-center gap-2">
            <?php if(isset($period)): ?>
                <input type="hidden" name="period" value="<?php echo e($period); ?>">
            <?php endif; ?>
            <?php if(isset($startDate) && isset($endDate) && ($period ?? '') === 'custom'): ?>
                <input type="hidden" name="start_date" value="<?php echo e($startDate->format('Y-m-d')); ?>">
                <input type="hidden" name="end_date" value="<?php echo e($endDate->format('Y-m-d')); ?>">
            <?php endif; ?>
            <label for="store_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Loja:</label>
            <select name="store_id" id="store_filter" 
                    onchange="this.form.submit()"
                    class="w-full xs:w-auto min-w-[160px] px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas as Lojas</option>
                <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($store->id); ?>" <?php echo e((isset($selectedStoreId) && $selectedStoreId == $store->id) ? 'selected' : ''); ?>>
                        <?php echo e($store->name); ?><?php if($store->isMain()): ?> (Principal)<?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Cards de Estatísticas Principais -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total de Pedidos -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total de Pedidos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($totalPedidos); ?></p>
                <?php if(isset($variacaoPedidos)): ?>
                <p class="text-xs mt-2 <?php echo e($variacaoPedidos >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e($variacaoPedidos >= 0 ? '+' : ''); ?><?php echo e(number_format($variacaoPedidos, 1)); ?>% vs período anterior
                </p>
                <?php endif; ?>
            </div>
            <div class="bg-indigo-100 dark:bg-indigo-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Faturamento Total -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Faturamento Total</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">R$ <?php echo e(number_format($totalFaturamento, 2, ',', '.')); ?></p>
                <?php if(isset($variacaoFaturamento)): ?>
                <p class="text-xs mt-2 <?php echo e($variacaoFaturamento >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e($variacaoFaturamento >= 0 ? '+' : ''); ?><?php echo e(number_format($variacaoFaturamento, 1)); ?>% vs período anterior
                </p>
                <?php endif; ?>
            </div>
            <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total de Clientes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total de Clientes</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($totalClientes); ?></p>
            </div>
            <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Ticket Médio -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Ticket Médio</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">R$ <?php echo e(number_format($ticketMedio ?? 0, 2, ',', '.')); ?></p>
                <?php if(isset($variacaoTicketMedio)): ?>
                <p class="text-xs mt-2 <?php echo e($variacaoTicketMedio >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e($variacaoTicketMedio >= 0 ? '+' : ''); ?><?php echo e(number_format($variacaoTicketMedio, 1)); ?>% vs período anterior
                </p>
                <?php endif; ?>
            </div>
            <div class="bg-purple-100 dark:bg-purple-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Cards Secundários -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Vendas PDV -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Vendas PDV</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($vendasPDV ?? 0); ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">R$ <?php echo e(number_format($vendasPDVValor ?? 0, 2, ',', '.')); ?></p>
            </div>
            <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pedidos Online -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pedidos Online</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($pedidosOnline ?? 0); ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">R$ <?php echo e(number_format($pedidosOnlineValor ?? 0, 2, ',', '.')); ?></p>
            </div>
            <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pedidos Hoje -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pedidos Hoje</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($pedidosHoje); ?></p>
            </div>
            <div class="bg-orange-100 dark:bg-orange-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pagamentos Pendentes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pagamentos Pendentes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100"><?php echo e($pagamentosPendentes->count()); ?></p>
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Total: R$ <?php echo e(number_format($totalPendente, 2, ',', '.')); ?></p>
            </div>
            <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Pedidos por Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Pedidos por Status</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Faturamento por Loja -->
    <?php if(isset($faturamentoPorLoja) && $faturamentoPorLoja->isNotEmpty()): ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento por Loja</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoLojaChart"></canvas>
        </div>
    </div>
    <?php else: ?>
    <!-- Faturamento Diário -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento Diário (Últimos 30 Dias)</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Gráficos Adicionais -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Faturamento Mensal -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento Mensal (Últimos 12 Meses)</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoMensalChart"></canvas>
        </div>
    </div>

    <!-- Distribuição por Forma de Pagamento -->
    <?php if(isset($distribuicaoPagamento) && $distribuicaoPagamento->isNotEmpty()): ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Formas de Pagamento</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="pagamentoChart"></canvas>
        </div>
    </div>
    <?php else: ?>
    <!-- Faturamento Diário -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Faturamento Diário</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="faturamentoChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Tabelas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top 10 Clientes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top 10 Clientes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $topClientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($cliente->name); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo e($cliente->total_pedidos); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ <?php echo e(number_format($cliente->total_gasto, 2, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum cliente encontrado</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Vendedores -->
    <?php if(isset($topVendedores) && $topVendedores->isNotEmpty()): ?>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top Vendedores</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Vendedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedidos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faturamento</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $__currentLoopData = $topVendedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendedor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($vendedor->name); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo e($vendedor->total_pedidos); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ <?php echo e(number_format($vendedor->total_faturamento, 2, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <!-- Pagamentos Pendentes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pagamentos Pendentes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Restante</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $pagamentosPendentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                            <a href="<?php echo e(route('orders.show', $pagamento->order->id)); ?>" 
                               class="hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline">
                                #<?php echo e(str_pad($pagamento->order->id, 6, '0', STR_PAD_LEFT)); ?>

                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            <?php echo e($pagamento->order->client ? $pagamento->order->client->name : 'Sem cliente'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-orange-600 dark:text-orange-400">R$ <?php echo e(number_format($pagamento->remaining_amount, 2, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum pagamento pendente</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Produtos Mais Vendidos -->
<?php if(isset($produtosMaisVendidos) && $produtosMaisVendidos->isNotEmpty()): ?>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Produtos Mais Vendidos</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Produto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantidade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Faturamento</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php $__currentLoopData = $produtosMaisVendidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo e($produto->print_type ?? 'N/A'); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo e($produto->total_vendido); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ <?php echo e(number_format($produto->total_faturamento, 2, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Pedidos Recentes -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pedidos e Vendas Recentes</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedido/Venda</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php $__empty_1 = true; $__currentLoopData = $pedidosRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($pedido->is_pdv): ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                Venda
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                Pedido
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                        <a href="<?php echo e(route('orders.show', $pedido->id)); ?>" 
                           class="hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline">
                            #<?php echo e(str_pad($pedido->id, 6, '0', STR_PAD_LEFT)); ?>

                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        <?php echo e($pedido->client ? $pedido->client->name : 'Sem cliente'); ?>

                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                              style="background-color: <?php echo e($pedido->status->color); ?>20; color: <?php echo e($pedido->status->color); ?>">
                            <?php echo e($pedido->status->name); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">R$ <?php echo e(number_format($pedido->total, 2, ',', '.')); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo e($pedido->created_at->format('d/m/Y')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum pedido ou venda encontrado</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('page-scripts'); ?>
<script>
(function() {
    'use strict';
    
    // Preparar dados dos gráficos
    <?php
        // Garantir que pedidosPorStatus seja uma collection
        $pedidosPorStatusCollection = is_array($pedidosPorStatus) ? collect($pedidosPorStatus) : $pedidosPorStatus;
        $statusData = $pedidosPorStatusCollection->map(function($item) {
            // Se for objeto, acessar como propriedade; se for array, acessar como array
            $status = is_object($item) ? ($item->status ?? $item['status'] ?? 'Sem Status') : ($item['status'] ?? 'Sem Status');
            $color = is_object($item) ? ($item->color ?? $item['color'] ?? '#9ca3af') : ($item['color'] ?? '#9ca3af');
            $total = is_object($item) ? (int)($item->total ?? $item['total'] ?? 0) : (int)($item['total'] ?? 0);
            
            return [
                'status' => $status,
                'color' => $color,
                'total' => $total
            ];
        })->toArray();
        
        $faturamentoData = $faturamentoDiario->map(function($item) {
            $dia = is_object($item) ? ($item->dia ?? '') : ($item['dia'] ?? '');
            $total = is_object($item) ? (float)($item->total ?? 0) : (float)($item['total'] ?? 0);
            return [
                'dia' => $dia,
                'total' => $total
            ];
        })->toArray();
        
        $faturamentoMensalData = $pedidosPorMes->map(function($item) {
            $mes = is_object($item) ? ($item->mes ?? '') : ($item['mes'] ?? '');
            $faturamento = is_object($item) ? (float)($item->faturamento ?? 0) : (float)($item['faturamento'] ?? 0);
            return [
                'mes' => $mes,
                'total' => $faturamento
            ];
        })->toArray();
        
        $faturamentoLojaData = isset($faturamentoPorLoja) ? $faturamentoPorLoja->map(function($item) {
            return [
                'name' => $item['name'] ?? '',
                'total' => (float)($item['total_faturamento'] ?? 0)
            ];
        })->toArray() : [];
        
        $pagamentoData = isset($distribuicaoPagamento) ? $distribuicaoPagamento->map(function($item) {
            return [
                'method' => $item['method'] ?? '',
                'total' => (float)($item['total'] ?? 0)
            ];
        })->toArray() : [];
    ?>
    
    const dashboardData = {
        statusData: <?php echo json_encode($statusData ?? [], 15, 512) ?>,
        faturamentoData: <?php echo json_encode($faturamentoData ?? [], 15, 512) ?>,
        faturamentoMensalData: <?php echo json_encode($faturamentoMensalData ?? [], 15, 512) ?>,
        faturamentoLojaData: <?php echo json_encode($faturamentoLojaData ?? [], 15, 512) ?>,
        pagamentoData: <?php echo json_encode($pagamentoData ?? [], 15, 512) ?>
    };
    
    // Debug: verificar dados
    console.log('=== DASHBOARD DATA ===');
    console.log('Status Data:', dashboardData.statusData);
    console.log('Faturamento Data:', dashboardData.faturamentoData);
    console.log('Faturamento Mensal Data:', dashboardData.faturamentoMensalData);
    console.log('Faturamento Loja Data:', dashboardData.faturamentoLojaData);
    console.log('Pagamento Data:', dashboardData.pagamentoData);
    console.log('Chart.js disponível:', typeof Chart !== 'undefined');
    
    // Função para inicializar gráficos
    function initCharts() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js não está disponível');
            return false;
        }
        
        console.log('Inicializando gráficos...');
        
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(0, 0, 0, 0.1)';
        const borderColor = isDark ? '#1f2937' : '#ffffff';
        
        // Destruir gráficos existentes se houver
        if (window.statusChart && typeof window.statusChart.destroy === 'function') {
            try { window.statusChart.destroy(); } catch(e) {}
        }
        if (window.faturamentoChart && typeof window.faturamentoChart.destroy === 'function') {
            try { window.faturamentoChart.destroy(); } catch(e) {}
        }
        if (window.faturamentoMensalChart && typeof window.faturamentoMensalChart.destroy === 'function') {
            try { window.faturamentoMensalChart.destroy(); } catch(e) {}
        }
        if (window.faturamentoLojaChart && typeof window.faturamentoLojaChart.destroy === 'function') {
            try { window.faturamentoLojaChart.destroy(); } catch(e) {}
        }
        if (window.pagamentoChart && typeof window.pagamentoChart.destroy === 'function') {
            try { window.pagamentoChart.destroy(); } catch(e) {}
        }
        
        // Gráfico de Status (Pizza)
        const statusCanvas = document.getElementById('statusChart');
        if (!statusCanvas) {
            console.error('Canvas statusChart não encontrado');
            return false;
        }
        
        const statusData = dashboardData.statusData || [];
        console.log('Status Data:', statusData);
        
        // Filtrar apenas dados com total > 0
        const validStatusData = statusData.filter(item => item && item.total > 0);
        
        if (validStatusData.length === 0) {
            console.warn('Sem dados válidos para gráfico de status');
            statusCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
        } else {
        
        const statusLabels = validStatusData.map(item => item.status || 'Sem Status');
        const statusValues = validStatusData.map(item => parseInt(item.total) || 0);
        const statusColors = validStatusData.map(item => item.color || '#9ca3af');
        
        console.log('Criando gráfico de status com:', { labels: statusLabels, values: statusValues, colors: statusColors });
        
        try {
            window.statusChart = new Chart(statusCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusColors,
                        borderWidth: 2,
                        borderColor: borderColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: textColor, padding: 15, font: { size: 12 } }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#374151' : '#ffffff',
                            titleColor: isDark ? '#f9fafb' : '#111827',
                            bodyColor: isDark ? '#e5e7eb' : '#374151',
                            borderColor: isDark ? '#4b5563' : '#e5e7eb',
                            borderWidth: 1
                        }
                    }
                }
            });
            console.log('Gráfico de status criado com sucesso');
        } catch (error) {
            console.error('Erro ao criar gráfico de status:', error);
            statusCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
        }
        }
        
        // Gráfico de Faturamento Diário (Linha)
        const faturamentoCanvas = document.getElementById('faturamentoChart');
        if (!faturamentoCanvas) {
            console.warn('Canvas faturamentoChart não encontrado');
        } else {
            console.log('Criando gráfico de faturamento diário');
            const faturamentoData = dashboardData.faturamentoData || [];
            console.log('Faturamento Diário Data:', faturamentoData);
            
            if (!faturamentoData || faturamentoData.length === 0 || faturamentoData.every(item => !item.total || item.total === 0)) {
                console.warn('Sem dados para gráfico de faturamento diário');
                faturamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            } else {
                const faturamentoLabels = faturamentoData.map(item => {
                    if (!item.dia) return '';
                    try {
                        const parts = String(item.dia).split(/[-/]/);
                        if (parts.length >= 3) {
                            const d = new Date(parts[0], parts[1] - 1, parts[2]);
                            return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                        }
                        return '';
                    } catch (e) {
                        return '';
                    }
                }).filter(label => label !== '');
                const faturamentoValues = faturamentoData.map(item => parseFloat(item.total || 0));
                
                console.log('Faturamento Labels:', faturamentoLabels);
                console.log('Faturamento Values:', faturamentoValues);
                
                try {
                    window.faturamentoChart = new Chart(faturamentoCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: faturamentoLabels.length > 0 ? faturamentoLabels : ['Sem dados'],
                            datasets: [{
                                label: 'Faturamento (R$)',
                                data: faturamentoValues.length > 0 ? faturamentoValues : [0],
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: isDark ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(99, 102, 241)',
                                pointBorderColor: borderColor,
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        callback: function(value) {
                                            return 'R$ ' + value.toLocaleString('pt-BR');
                                        }
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de faturamento diário criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de faturamento diário:', error);
                    faturamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            }
        }
        
        // Gráfico de Faturamento Mensal (Linha)
        const faturamentoMensalCanvas = document.getElementById('faturamentoMensalChart');
        if (!faturamentoMensalCanvas) {
            console.warn('Canvas faturamentoMensalChart não encontrado');
        } else {
            console.log('Criando gráfico de faturamento mensal');
            console.log('Faturamento Mensal Data:', dashboardData.faturamentoMensalData);
            const faturamentoMensalData = dashboardData.faturamentoMensalData || [];
            
            if (!faturamentoMensalData || faturamentoMensalData.length === 0 || faturamentoMensalData.every(item => !item.total || item.total === 0)) {
                console.warn('Sem dados para gráfico de faturamento mensal');
                faturamentoMensalCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            } else {
            const faturamentoMensalLabels = faturamentoMensalData.map(item => {
                if (!item.mes) return '';
                try {
                    const parts = String(item.mes).split('-');
                    if (parts.length >= 2) {
                        const d = new Date(parts[0], parts[1] - 1);
                        return d.toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' });
                    }
                    return '';
                } catch (e) {
                    return '';
                }
                }).filter(label => label !== '');
                const faturamentoMensalValues = faturamentoMensalData.map(item => parseFloat(item.total || 0));
                
                console.log('Faturamento Mensal Labels:', faturamentoMensalLabels);
                console.log('Faturamento Mensal Values:', faturamentoMensalValues);
                
                try {
                    window.faturamentoMensalChart = new Chart(faturamentoMensalCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: faturamentoMensalLabels.length > 0 ? faturamentoMensalLabels : ['Sem dados'],
                            datasets: [{
                                label: 'Faturamento (R$)',
                                data: faturamentoMensalValues.length > 0 ? faturamentoMensalValues : [0],
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: isDark ? 'rgba(34, 197, 94, 0.2)' : 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(34, 197, 94)',
                                pointBorderColor: borderColor,
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        callback: function(value) {
                                            return 'R$ ' + value.toLocaleString('pt-BR');
                                        }
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de faturamento mensal criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de faturamento mensal:', error);
                    faturamentoMensalCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            }
        }
        
        // Gráfico de Faturamento por Loja (Barras)
        const faturamentoLojaCanvas = document.getElementById('faturamentoLojaChart');
        if (!faturamentoLojaCanvas) {
            console.warn('Canvas faturamentoLojaChart não encontrado');
        } else {
            if (dashboardData.faturamentoLojaData && dashboardData.faturamentoLojaData.length > 0 && dashboardData.faturamentoLojaData.some(item => item.total > 0)) {
                const faturamentoLojaLabels = dashboardData.faturamentoLojaData.map(item => item.name || 'Sem nome');
                const faturamentoLojaValues = dashboardData.faturamentoLojaData.map(item => parseFloat(item.total || 0));
                
                console.log('Criando gráfico de faturamento por loja:', { labels: faturamentoLojaLabels, values: faturamentoLojaValues });
                
                try {
                    window.faturamentoLojaChart = new Chart(faturamentoLojaCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: faturamentoLojaLabels,
                            datasets: [{
                                label: 'Faturamento (R$)',
                                data: faturamentoLojaValues,
                                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                                borderColor: 'rgb(99, 102, 241)',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        callback: function(value) {
                                            return 'R$ ' + value.toLocaleString('pt-BR');
                                        }
                                    },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de faturamento por loja criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de faturamento por loja:', error);
                    faturamentoLojaCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            } else {
                console.warn('Faturamento por Loja: sem dados');
                faturamentoLojaCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            }
        }
        
        // Gráfico de Formas de Pagamento (Pizza)
        const pagamentoCanvas = document.getElementById('pagamentoChart');
        if (!pagamentoCanvas) {
            console.warn('Canvas pagamentoChart não encontrado');
        } else {
            console.log('Pagamento Data:', dashboardData.pagamentoData);
            if (dashboardData.pagamentoData && dashboardData.pagamentoData.length > 0 && dashboardData.pagamentoData.some(item => item.total > 0)) {
                const pagamentoLabels = dashboardData.pagamentoData.map(item => {
                    const methodNames = {
                        'dinheiro': 'Dinheiro',
                        'pix': 'PIX',
                        'cartao': 'Cartão',
                        'transferencia': 'Transferência',
                        'boleto': 'Boleto'
                    };
                    return methodNames[item.method] || item.method;
                });
                const pagamentoValues = dashboardData.pagamentoData.map(item => parseFloat(item.total || 0));
                const pagamentoColors = [
                    'rgba(34, 197, 94, 0.8)',   // Verde - PIX
                    'rgba(59, 130, 246, 0.8)',  // Azul - Cartão
                    'rgba(234, 179, 8, 0.8)',   // Amarelo - Dinheiro
                    'rgba(168, 85, 247, 0.8)',  // Roxo - Transferência
                    'rgba(239, 68, 68, 0.8)'    // Vermelho - Boleto
                ];
                
                console.log('Criando gráfico de formas de pagamento:', { labels: pagamentoLabels, values: pagamentoValues });
                
                try {
                    window.pagamentoChart = new Chart(pagamentoCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: pagamentoLabels,
                            datasets: [{
                                data: pagamentoValues,
                                backgroundColor: pagamentoColors.slice(0, pagamentoLabels.length),
                                borderWidth: 2,
                                borderColor: borderColor
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: textColor, padding: 15, font: { size: 12 } }
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#374151' : '#ffffff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#e5e7eb' : '#374151',
                                    borderColor: isDark ? '#4b5563' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                            return context.label + ': R$ ' + context.parsed.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de formas de pagamento criado com sucesso');
                } catch (error) {
                    console.error('Erro ao criar gráfico de formas de pagamento:', error);
                    pagamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Erro: ' + error.message + '</div>';
                }
            } else {
                console.warn('Formas de Pagamento: sem dados');
                pagamentoCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Sem dados para exibir</div>';
            }
        }
        
        console.log('Gráficos inicializados');
        return true;
    }
    
    // Aguardar Chart.js e DOM estarem prontos
    function waitAndInit() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js não está disponível');
            setTimeout(waitAndInit, 100);
            return;
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initCharts, 200);
            });
        } else {
            setTimeout(initCharts, 200);
        }
    }
    
    // Inicializar quando a página carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitAndInit);
    } else {
        waitAndInit();
    }
    
    // Fallback: tentar novamente após um tempo
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (!window.statusChart && typeof Chart !== 'undefined') {
                console.log('Tentando inicializar gráficos novamente...');
                initCharts();
            }
        }, 1000);
    });
})();
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/dashboard/admin-geral.blade.php ENDPATH**/ ?>