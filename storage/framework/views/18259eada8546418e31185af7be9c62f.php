

<?php $__env->startSection('content'); ?>
<div class="mb-6 space-y-4">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Dashboard Financeiro</h1>
    </div>
    
    <!-- Filtros -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <!-- Filtro de Período -->
        <form method="GET" action="<?php echo e(route('financial.dashboard')); ?>" id="periodFilterForm" class="flex flex-wrap items-center gap-2">
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
            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">Filtrar</button>
            <?php endif; ?>
        </form>
        
        <!-- Filtro de Loja -->
        <?php if(isset($stores) && $stores->count() > 0): ?>
        <form method="GET" action="<?php echo e(route('financial.dashboard')); ?>" id="storeFilterForm" class="flex flex-wrap items-center gap-2">
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

<!-- Cards Principais -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Receita Total -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Receita Total</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">R$ <?php echo e(number_format($totalRevenue, 2, ',', '.')); ?></p>
            </div>
            <div class="bg-indigo-100 dark:bg-indigo-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Custo Total -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Custo Total</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">R$ <?php echo e(number_format($totalCost, 2, ',', '.')); ?></p>
            </div>
            <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Lucro Bruto -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Lucro Bruto</p>
                <p class="text-3xl font-bold <?php echo e($grossProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?>">
                    R$ <?php echo e(number_format($grossProfit, 2, ',', '.')); ?>

                </p>
            </div>
            <div class="bg-green-100 dark:bg-green-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Margem de Lucro -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Margem de Lucro</p>
                <p class="text-3xl font-bold <?php echo e($profitMargin >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400'); ?>">
                    <?php echo e(number_format($profitMargin, 1)); ?>%
                </p>
            </div>
            <div class="bg-blue-100 dark:bg-blue-900/30 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Evolução -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Evolução Financeira (Receita vs Custo vs Lucro)</h2>
    <div style="height: 400px; position: relative;">
        <canvas id="evolutionChart"></canvas>
    </div>
</div>

<!-- Produtos Mais Lucrativos e Relatório por Item -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Top Produtos -->
    <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Top 5 Produtos por Lucro</h2>
        <div class="space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex-1 min-w-0 mr-4">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        <?php echo e($product->print_type ?? 'Produto s/ nome'); ?>

                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Qtd: <?php echo e($product->quantity); ?>

                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600 dark:text-green-400">
                        R$ <?php echo e(number_format($product->profit, 2, ',', '.')); ?>

                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Margem: <?php echo e($product->revenue > 0 ? number_format(($product->profit / $product->revenue) * 100, 1) : 0); ?>%
                    </p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center text-gray-500 py-4">Sem dados para exibir</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Relatório Detalhado -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Relatório Detalhado por Item</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item/Produto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pedido</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qtd</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Preço Un.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Custo Un.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Lucro</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $itemsReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            <?php echo e($item->print_type ?? 'N/A'); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-indigo-600 dark:text-indigo-400">
                            <a href="<?php echo e(route('orders.show', $item->order_code)); ?>" class="hover:underline">
                                #<?php echo e(str_pad($item->order_code, 6, '0', STR_PAD_LEFT)); ?>

                            </a>
                            <div class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($item->order_date)->format('d/m/Y')); ?></div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            <?php echo e($item->quantity); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                            R$ <?php echo e(number_format($item->unit_price, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                            R$ <?php echo e(number_format($item->unit_cost, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold <?php echo e(($item->total_price - $item->total_cost) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?>">
                            R$ <?php echo e(number_format($item->total_price - $item->total_cost, 2, ',', '.')); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Nenhum item encontrado</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            <?php echo e($itemsReport->links()); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('page-scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dados do gráfico
        const dailyData = <?php echo json_encode($dailyData, 15, 512) ?>;
        
        const labels = dailyData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
        });
        
        const revenueData = dailyData.map(item => parseFloat(item.revenue));
        const costData = dailyData.map(item => parseFloat(item.cost));
        const profitData = dailyData.map(item => parseFloat(item.profit));
        
        const ctx = document.getElementById('evolutionChart').getContext('2d');
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#e5e7eb' : '#374151';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.3)' : 'rgba(0, 0, 0, 0.1)';
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Receita',
                        data: revenueData,
                        borderColor: 'rgb(79, 70, 229)', // Indigo
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Lucro',
                        data: profitData,
                        borderColor: 'rgb(16, 185, 129)', // Emerald
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Custo',
                        data: costData,
                        borderColor: 'rgb(239, 68, 68)', // Red
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: textColor }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumSignificantDigits: 3 }).format(value);
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
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/dashboard/financeiro.blade.php ENDPATH**/ ?>