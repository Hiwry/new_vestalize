<?php $__env->startPush('styles'); ?>
<style>
    [x-cloak] { display: none !important; }
    
    /* Custom Scrollbar for Calendar Events */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Definir componente globalmente para garantir acesso via x-data
    window.kanbanBoard = function(ordersData, startDate) {
        return {
            view: 'kanban', // 'kanban' | 'calendar'
            calendarView: 'month', // 'month' | 'week' | 'day'
            currentDate: startDate ? new Date(startDate + 'T12:00:00') : new Date(),
            events: ordersData,
            
            get currentMonthName() {
                if (this.calendarView === 'day') {
                    return this.currentDate.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' });
                }
                if (this.calendarView === 'week') {
                    const start = new Date(this.currentDate);
                    start.setDate(this.currentDate.getDate() - this.currentDate.getDay());
                    const end = new Date(start);
                    end.setDate(start.getDate() + 6);
                    
                    if (start.getMonth() === end.getMonth()) {
                        return `${start.getDate()} - ${end.getDate()} de ${start.toLocaleString('pt-BR', { month: 'long', year: 'numeric' })}`;
                    } else {
                        return `${start.getDate()} de ${start.toLocaleString('pt-BR', { month: 'short' })} - ${end.getDate()} de ${end.toLocaleString('pt-BR', { month: 'short', year: 'numeric' })}`;
                    }
                }
                return this.currentDate.toLocaleString('pt-BR', { month: 'long', year: 'numeric' });
            },

            get calendarDays() {
                const days = [];
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();
                
                if (this.calendarView === 'day') {
                    days.push({ 
                        date: new Date(this.currentDate), 
                        isCurrentMonth: true, 
                        isToday: this.isToday(this.currentDate) 
                    });
                    return days;
                }

                if (this.calendarView === 'week') {
                    const current = new Date(this.currentDate);
                    const day = current.getDay(); // 0 (Domingo) - 6 (Sábado)
                    const startOfWeek = new Date(current);
                    startOfWeek.setDate(current.getDate() - day);

                    for (let i = 0; i < 7; i++) {
                        const d = new Date(startOfWeek);
                        d.setDate(startOfWeek.getDate() + i);
                        days.push({ 
                            date: d, 
                            isCurrentMonth: d.getMonth() === month, 
                            isToday: this.isToday(d) 
                        });
                    }
                    return days;
                }

                // Month View (Default)
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                
                // Domingo como primeiro dia
                const startDayOfWeek = firstDay.getDay(); 
                
                // Dias do mês anterior
                for (let i = startDayOfWeek; i > 0; i--) {
                    const d = new Date(year, month, 1 - i);
                    days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
                }
                
                // Dias do mês atual
                for (let i = 1; i <= lastDay.getDate(); i++) {
                    const d = new Date(year, month, i);
                    days.push({ date: d, isCurrentMonth: true, isToday: this.isToday(d) });
                }
                
                // Preencher grade (42 células = 6 linhas)
                const remainingCells = 42 - days.length;
                for (let i = 1; i <= remainingCells; i++) {
                    const d = new Date(year, month + 1, i);
                    days.push({ date: d, isCurrentMonth: false, isToday: this.isToday(d) });
                }
                
                return days;
            },

            isToday(date) {
                const today = new Date();
                return date.getDate() === today.getDate() &&
                       date.getMonth() === today.getMonth() &&
                       date.getFullYear() === today.getFullYear();
            },

            getEventsForDay(date) {
                const dateString = date.toISOString().split('T')[0];
                return this.events.filter(event => event.date === dateString);
            },

            prev() {
                if (this.calendarView === 'month') {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                } else if (this.calendarView === 'week') {
                    this.currentDate.setDate(this.currentDate.getDate() - 7);
                    this.currentDate = new Date(this.currentDate); // trigger reactivity
                } else { // day view
                    this.currentDate.setDate(this.currentDate.getDate() - 1);
                    this.currentDate = new Date(this.currentDate);
                }
            },

            next() {
                if (this.calendarView === 'month') {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                } else if (this.calendarView === 'week') {
                    this.currentDate.setDate(this.currentDate.getDate() + 7);
                    this.currentDate = new Date(this.currentDate);
                } else { // day view
                    this.currentDate.setDate(this.currentDate.getDate() + 1);
                    this.currentDate = new Date(this.currentDate);
                }
            },
            
            goToToday() {
                this.currentDate = new Date();
            },

            init() {
                const savedView = localStorage.getItem('kanban_view_mode');
                if (savedView) this.view = savedView;
                this.$watch('view', value => localStorage.setItem('kanban_view_mode', value));
            }
        };
    };
</script>
<?php $__env->stopPush(); ?>


<?php $__env->startSection('content'); ?>
<div class="max-w-[1800px] mx-auto">
        <?php if(session('error')): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
            <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <!-- Calendar Data Preparation -->
        <?php
            $calendarData = ($ordersForCalendar ?? collect())->map(function($order) {
                $firstItem = $order->items->first();
                $artName = null;
                if ($firstItem && $firstItem->sublimations) {
                    $firstSublimation = $firstItem->sublimations->first();
                    if ($firstSublimation) $artName = $firstSublimation->art_name;
                }
                $title = $firstItem?->art_name ?? ($order->client->name ?? 'Cliente');
                
                return [
                    'id' => $order->id,
                    'title' => $title,
                    'client' => $order->client?->name ?? 'N/A',
                    'date' => $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : null,
                    'items_count' => $order->items->sum('quantity'),
                    'status_color' => $order->status->color ?? '#ccc',
                ];
            })->values();

            // Default start date from filter or today
            $startDate = $deliveryDateFilter ?? request('delivery_date') ?? null;
        ?>

        <div x-data="kanbanBoard(<?php echo e(Js::from($calendarData)); ?>, '<?php echo e($startDate); ?>')" x-cloak>

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Kanban de Produção</h1>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Total de Pedidos: <strong><?php echo e($ordersByStatus->flatten()->count()); ?></strong>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- View Toggle -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-800 p-1 rounded-lg border border-gray-200 dark:border-gray-700">
                    <button @click="view = 'kanban'" 
                            :class="{ 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm': view === 'kanban', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': view !== 'kanban' }"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Kanban
                    </button>
                    <button @click="view = 'calendar'" 
                            :class="{ 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm': view === 'calendar', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': view !== 'calendar' }"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Calendário
                    </button>
                </div>

                <?php if(Auth::user()->isAdmin()): ?>
                <a href="<?php echo e(route('kanban.columns.index')); ?>" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center space-x-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Gerenciar Colunas</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Barra de Busca e Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
            <form method="GET" action="<?php echo e(route('kanban.index')); ?>" class="space-y-4">
                <!-- Busca -->
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               id="search-input"
                               value="<?php echo e($search ?? ''); ?>"
                               placeholder="Buscar por nº do pedido (ex: 28 ou 000028), nome do cliente, telefone ou nome da arte..."
                               class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 whitespace-nowrap font-semibold transition shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Buscar
                    </button>
                </div>
                
                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Tipo de Personalização
                        </label>
                        <select name="personalization_type" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Todas as Personalizações</option>
                            <?php $__currentLoopData = $personalizationTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('personalization_type') == $key ? 'selected' : ''); ?>>
                                    <?php echo e($name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <?php if($search || request('personalization_type')): ?>
                        <a href="<?php echo e(route('kanban.index')); ?>" 
                           class="w-full px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 whitespace-nowrap transition font-semibold flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Limpar Filtros
                        </a>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Data de entrega
                        </label>
                        <input type="date" name="delivery_date" value="<?php echo e($deliveryDateFilter ?? request('delivery_date')); ?>" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
                
                <?php if($search || request('personalization_type') || ($deliveryDateFilter ?? request('delivery_date'))): ?>
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filtros ativos:</span>
                        <?php if($search): ?>
                        <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 rounded-full text-sm font-medium flex items-center gap-2">
                            Busca: "<?php echo e($search); ?>"
                            <a href="<?php echo e(route('kanban.index', array_merge(request()->except('search'), ['personalization_type' => request('personalization_type')]))); ?>" 
                               class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                        <?php endif; ?>
                        <?php if(request('personalization_type')): ?>
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full text-sm font-medium flex items-center gap-2">
                            Personalização: <?php echo e($personalizationTypes[request('personalization_type')] ?? request('personalization_type')); ?>

                            <a href="<?php echo e(route('kanban.index', array_merge(request()->except('personalization_type'), ['search' => request('search')]))); ?>" 
                               class="hover:text-purple-600 dark:hover:text-purple-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                        <?php endif; ?>
                        <?php if($deliveryDateFilter ?? request('delivery_date')): ?>
                        <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 rounded-full text-sm font-medium flex items-center gap-2">
                            Entrega: <?php echo e(\Carbon\Carbon::parse($deliveryDateFilter ?? request('delivery_date'))->format('d/m/Y')); ?>

                            <a href="<?php echo e(route('kanban.index', array_merge(request()->except('delivery_date'), ['search' => request('search'), 'personalization_type' => request('personalization_type')]))); ?>" 
                               class="hover:text-amber-600 dark:hover:text-amber-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <?php
            $hasFilters = ($deliveryDateFilter ?? request('delivery_date')) || ($personalizationType ?? request('personalization_type')) || ($search ?? request('search'));
        ?>

        <?php if($hasFilters): ?>
        <div x-show="view === 'kanban'" class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Agenda por data de entrega</h3>
                <a href="<?php echo e(route('kanban.index')); ?>" class="text-sm text-indigo-600 font-semibold hover:text-indigo-500">Voltar ao Kanban</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <?php $__empty_1 = true; $__currentLoopData = ($ordersForCalendar ?? collect())->groupBy(fn($o) => optional($o->delivery_date)->format('Y-m-d') ?? 'sem_data'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $isNoDate = $dateKey === 'sem_data'; ?>
                    <div class="border border-gray-200 rounded-lg shadow-sm bg-white">
                        <div class="px-3 py-2 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-800">
                                <?php echo e($isNoDate ? 'Sem data' : \Carbon\Carbon::parse($dateKey)->format('d/m/Y')); ?>

                            </span>
                            <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full"><?php echo e($group->count()); ?></span>
                        </div>
                        <div class="p-3 space-y-3">
                            <?php $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $firstItem = $order->items->first();
                                    $displayName = $firstItem?->art_name ?? ($order->client?->name ?? 'Sem cliente');
                                    $storeName = $order->store?->name ?? 'Loja Principal';
                                ?>
                                <div class="border border-gray-200 rounded-md p-3 bg-white shadow-sm">
                                    <div class="flex items-center justify-between mb-1">
                                        <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100">
                                            #<?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?>

                                        </a>
                                        <?php if($order->priority): ?>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold
                                                <?php if($order->priority === 'alta'): ?> bg-red-100 text-red-800
                                                <?php elseif($order->priority === 'media'): ?> bg-yellow-100 text-yellow-800
                                                <?php else: ?> bg-green-100 text-green-800 <?php endif; ?>">
                                                <?php echo e(ucfirst($order->priority)); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($displayName); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($storeName); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center text-sm text-gray-500 py-6">Nenhum pedido para a data filtrada.</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div x-show="view === 'kanban'" class="kanban-board flex gap-4 overflow-x-auto pb-4" <?php if($hasFilters): ?> style="display:none;" <?php endif; ?>>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 flex-shrink-0 overflow-hidden shadow-sm" style="min-width: 320px; max-width: 320px;">
                    <div class="px-4 py-3 font-semibold flex justify-between items-center text-white" 
                         style="background-color: <?php echo e($status->color); ?>;">
                        <span><?php echo e($status->name); ?></span>
                        <span class="bg-white/20 px-2 py-1 rounded-full text-xs">
                            <?php echo e(($ordersByStatus[$status->id] ?? collect())->count()); ?>

                        </span>
                    </div>
                    <div class="kanban-column p-3 space-y-3 overflow-y-auto bg-gray-50 dark:bg-gray-900/50" style="height: calc(100vh - 300px); max-height: 800px;" data-status-id="<?php echo e($status->id); ?>">
                        <?php $__currentLoopData = ($ordersByStatus[$status->id] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $firstItem = $order->items->first();
                                $coverImage = $order->cover_image_url ?: $firstItem?->cover_image_url;
                                $artName = $firstItem?->art_name;
                                $displayName = $artName ?? ($order->client?->name ?? 'Sem cliente');
                                $storeName = $order->store?->name ?? 'Loja Principal';
                                
                                // Verificar se tem sublimação local (para vendas PDV)
                                $hasSublocal = false;
                                $sublocalInfo = [];
                                if ($order->is_pdv) {
                                    foreach ($order->items as $item) {
                                        if ($item->sublimations) {
                                            foreach ($item->sublimations as $sub) {
                                                if ($sub->location_id || $sub->location_name) {
                                                    $hasSublocal = true;
                                                    $locationName = $sub->location ? $sub->location->name : ($sub->location_name ?? 'Local não informado');
                                                    $sizeName = $sub->size ? $sub->size->name : ($sub->size_name ?? '');
                                                    $sublocalInfo[] = [
                                                        'location' => $locationName,
                                                        'size' => $sizeName,
                                                        'quantity' => $sub->quantity,
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                            ?>
                            <div class="kanban-card <?php echo e($order->is_event ? 'bg-red-50 dark:bg-red-900/20 border-red-400 dark:border-red-500 border-4' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700'); ?> shadow dark:shadow-gray-900/25 rounded-lg overflow-hidden <?php echo e(Auth::user()->isAdmin() ? 'cursor-move' : 'cursor-pointer'); ?> hover:shadow-xl dark:hover:shadow-gray-900/50 transition-all duration-200 border" 
                                 draggable="<?php echo e(Auth::user()->isAdmin() ? 'true' : 'false'); ?>" 
                                 data-order-id="<?php echo e($order->id); ?>"
                                 onclick="event.stopPropagation(); if(typeof openOrderModal === 'function') { openOrderModal(<?php echo e($order->id); ?>); }">
                                
                                <!-- Imagem de Capa -->
                                <?php if($coverImage): ?>
                                <div class="h-48 bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                    <img src="<?php echo e($coverImage); ?>" 
                                         alt="Capa do Pedido" 
                                         class="w-full h-48 object-cover"
                                         style="object-fit: cover; object-position: center;"
                                         onerror="this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>'">
                                </div>
                                <?php else: ?>
                                <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <?php endif; ?>

                                <!-- Conteúdo do Card -->
                                <div class="p-4">
                                    <!-- Número do Pedido e Cliente -->
                                    <div class="mb-3">
                                            <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center space-x-2">
                                                <a href="<?php echo e(route('orders.show', $order->id)); ?>" 
                                                   class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/50 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                                    #<?php echo e(str_pad($order->id, 6, '0', STR_PAD_LEFT)); ?>

                                                </a>
                                                <?php if($order->edit_status === 'requested'): ?>
                                                <span class="text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 px-2 py-1 rounded-full">
                                                    Aguardando Aprovação
                                                </span>
                                                <?php elseif($order->edit_status === 'approved'): ?>
                                                <span class="text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded-full">
                                                    Aprovado
                                                </span>
                                                <?php elseif($order->edit_status === 'rejected'): ?>
                                                <span class="text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-2 py-1 rounded-full">
                                                    Rejeitado
                                                </span>
                                                <?php elseif($order->is_modified): ?>
                                                <span class="text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full">
                                                    Editado
                                                </span>
                                                <?php endif; ?>
                                                
                                                <?php if($order->is_event): ?>
                                                <span class="text-xs font-medium bg-red-500 dark:bg-red-600 text-white px-2 py-1 rounded-full">
                                                    EVENTO
                                                </span>
                                                <?php endif; ?>
                                                
                                                <?php if($order->stock_status): ?>
                                                <?php
                                                    $stockLabel = \App\Services\StockService::getStockStatusLabel($order->stock_status);
                                                ?>
                                                <span class="text-xs font-semibold inline-flex items-center gap-1 px-2.5 py-1 rounded-full border
                                                    <?php if($order->stock_status === 'total'): ?> bg-green-100 text-green-800 border-green-300 dark:bg-green-900/30 dark:text-green-200 dark:border-green-700
                                                    <?php elseif($order->stock_status === 'partial'): ?> bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-200 dark:border-yellow-700
                                                    <?php elseif($order->stock_status === 'none'): ?> bg-red-100 text-red-800 border-red-300 dark:bg-red-900/30 dark:text-red-200 dark:border-red-700
                                                    <?php elseif($order->stock_status === 'reserved'): ?> bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-700
                                                    <?php else: ?> bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 <?php endif; ?>">
                                                    <?php echo e($stockLabel['label']); ?>

                                                </span>
                                                <?php endif; ?>
                                                
                                                <!-- Indicadores de Cancelamento e Edição -->
                                                <?php if($order->has_pending_cancellation): ?>
                                                <span class="text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-2 py-1 rounded-full">
                                                    Cancelamento Pendente
                                                </span>
                                                <?php elseif($order->is_cancelled): ?>
                                                <span class="text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded-full">
                                                    Cancelado
                                                </span>
                                                <?php endif; ?>
                                                
                                                <?php if($order->has_pending_edit): ?>
                                                <span class="text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-2 py-1 rounded-full">
                                                    Edição Pendente
                                                </span>
                                                <?php endif; ?>
                                                
                                                <?php if($order->last_updated_at && $order->last_updated_at > $order->updated_at): ?>
                                                <span class="text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-2 py-1 rounded-full">
                                                    Atualizado
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                <?php echo e($order->items->sum('quantity')); ?> pçs
                                            </span>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm truncate" title="<?php echo e($displayName); ?>">
                                            <?php echo e($displayName); ?>

                                        </h3>
                                        <?php if($storeName): ?>
                                        <div class="flex items-center text-xs text-indigo-700 dark:text-indigo-400 mt-1">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 6l9 4 9-4m-9 4v6" />
                                            </svg>
                                            <span class="truncate" title="<?php echo e($storeName); ?>">
                                                <strong>Loja:</strong> <?php echo e($storeName); ?>

                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Informações do Produto -->
                                    <?php if($firstItem): ?>
                                    <div class="space-y-2 mb-3 text-xs">
                                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                            </svg>
                                            <span class="truncate" title="<?php echo e($firstItem->fabric); ?>">
                                                <strong>Tecido:</strong> <?php echo e($firstItem->fabric); ?>

                                            </span>
                                        </div>

                                        <?php if($firstItem->model): ?>
                                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path>
                                            </svg>
                                            <span class="truncate" title="<?php echo e($firstItem->model); ?>">
                                                <strong>Corte:</strong> <?php echo e($firstItem->model); ?>

                                            </span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Sublimação Local (apenas para vendas PDV) -->
                                        <?php if($order->is_pdv && $hasSublocal && !empty($sublocalInfo)): ?>
                                        <div class="mt-2 pt-2 border-t border-indigo-200 dark:border-indigo-700">
                                            <div class="flex items-center text-indigo-700 dark:text-indigo-400 mb-1">
                                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                                </svg>
                                                <strong class="text-xs">Sublimação Local:</strong>
                                            </div>
                                            <?php $__currentLoopData = $sublocalInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sublocal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="text-xs text-indigo-600 dark:text-indigo-400 ml-5">
                                                • <?php echo e($sublocal['location']); ?>

                                                <?php if($sublocal['size']): ?>
                                                - <?php echo e($sublocal['size']); ?>

                                                <?php endif; ?>
                                                (<?php echo e($sublocal['quantity']); ?>x)
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <?php endif; ?>

                                        <?php if($firstItem->print_type): ?>
                                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                            </svg>
                                            <span class="truncate" title="<?php echo e($firstItem->print_type); ?>">
                                                <strong>Personalização:</strong> <?php echo e($firstItem->print_type); ?>

                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Vendedor e Criador -->
                                    <div class="mb-3 space-y-1">
                                        <?php if($order->seller): ?>
                                        <div class="flex items-center text-xs text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="truncate" title="<?php echo e($order->seller); ?>">
                                                <strong>Vendedor:</strong> <?php echo e($order->seller); ?>

                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Datas -->
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                        <?php if($order->created_at): ?>
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span><strong>Pedido:</strong> <?php echo e(\Carbon\Carbon::parse($order->created_at)->format('d/m/Y')); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php if($order->delivery_date): ?>
                                        <div class="flex items-center text-orange-600 dark:text-orange-400">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span><strong>Entrega:</strong> <?php echo e(\Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y')); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Total -->
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">Total:</span>
                                            <span class="text-sm font-bold text-green-600 dark:text-green-400">
                                                R$ <?php echo e(number_format($order->total, 2, ',', '.')); ?>

                                            </span>
                                        </div>
                                    </div>

                                    <!-- Botões de Ação -->
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Calendar View -->
        <div x-show="view === 'calendar'" 
             class="calendar-container" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100">
             
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-[calc(100vh-250px)] min-h-[600px] p-4">
                
                <!-- Cabeçalho do Calendário -->
                <div class="flex items-center justify-between mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 capitalize" x-text="currentMonthName"></h2>
                        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                            <button @click="prev()" class="p-1 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-colors">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button @click="goToToday()" class="px-3 py-1 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                                Hoje
                            </button>
                            <button @click="next()" class="p-1 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-colors">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- View Switcher -->
                    <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button @click="calendarView = 'month'" 
                                :class="{ 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': calendarView === 'month', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': calendarView !== 'month' }"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-all">
                            Mês
                        </button>
                        <button @click="calendarView = 'week'" 
                                :class="{ 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': calendarView === 'week', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': calendarView !== 'week' }"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-all">
                            Semana
                        </button>
                        <button @click="calendarView = 'day'" 
                                :class="{ 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white': calendarView === 'day', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': calendarView !== 'day' }"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-all">
                            Dia
                        </button>
                    </div>
                </div>

                <!-- Dias da Semana (Header) - Hide on Day view -->
                <div x-show="calendarView !== 'day'" class="grid grid-cols-7 mb-2 border-b border-gray-200 dark:border-gray-700">
                    <template x-for="day in ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB']">
                        <div class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 py-2" x-text="day"></div>
                    </template>
                </div>

                <!-- Grid do Calendário -->
                <div class="grid gap-px bg-gray-200 dark:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden flex-1"
                     :class="{ 'grid-cols-7': calendarView !== 'day', 'grid-cols-1': calendarView === 'day' }">
                    <template x-for="day in calendarDays" :key="day.date.toISOString()">
                        <div class="bg-white dark:bg-gray-800 p-2 transition-colors relative group overflow-hidden flex flex-col"
                             :class="{ 
                                'bg-gray-50 dark:bg-gray-800/50 text-gray-400': !day.isCurrentMonth && calendarView === 'month',
                                'bg-blue-50/30 dark:bg-blue-900/10': day.isToday
                             }">
                             
                            <!-- Data -->
                            <div class="flex items-center justify-between mb-2 flex-shrink-0">
                                <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full"
                                      :class="{ 
                                        'bg-blue-600 text-white': day.isToday,
                                        'text-gray-700 dark:text-gray-300': !day.isToday && (day.isCurrentMonth || calendarView !== 'month'),
                                        'text-gray-400 dark:text-gray-600': !day.isToday && !day.isCurrentMonth && calendarView === 'month'
                                      }" 
                                      x-text="day.date.getDate()">
                                </span>
                                <span x-show="calendarView === 'day'" class="text-sm text-gray-500 dark:text-gray-400 font-medium" x-text="day.date.toLocaleDateString('pt-BR', { weekday: 'long' })"></span>
                            </div>

                            <!-- Eventos -->
                            <div class="space-y-1.5 overflow-y-auto custom-scrollbar flex-1">
                                <template x-for="event in getEventsForDay(day.date)">
                                    <div @click.stop="openOrderModal(event.id)"
                                         class="px-3 py-2 rounded-md cursor-pointer hover:opacity-90 transition-all border-l-4 shadow-sm"
                                         :style="`background-color: ${event.status_color}25; border-left-color: ${event.status_color};`">
                                        <div class="font-bold text-sm truncate leading-tight" 
                                             :style="`color: ${event.status_color}`"
                                             x-text="event.title"></div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 opacity-90" x-text="'#' + event.id.toString().padStart(5, '0')"></span>
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 opacity-90" x-text="event.items_count + ' pçs'"></span>
                                        </div>
                                        <!-- Detalhes extras apenas na visão de Dia -->
                                        <div x-show="$parent.$parent.calendarView === 'day'" class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700/50">
                                            <div class="text-xs text-gray-700 dark:text-gray-300 font-medium truncate" x-text="event.client"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        </div> <!-- Close x-data -->
    </div> <!-- Close max-w -->

    <!-- Modal de Detalhes do Pedido -->
    <div id="order-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-5 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-full max-w-7xl shadow-lg dark:shadow-gray-900/25 rounded-md bg-white dark:bg-gray-800 mb-5">
            <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div id="modal-title" class="flex-1 mr-4">Detalhes do Pedido</div>
                <button onclick="closeOrderModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="modal-content" class="space-y-6">
                <!-- Será preenchido via JavaScript - Layout em 2 colunas -->
            </div>
        </div>
    </div>

    <!-- Modal de Pagamento Adicional -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-lg shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    Registrar Pagamento
                </h3>
                <button onclick="closePaymentModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="payment-form" onsubmit="submitPayment(event)" class="space-y-5">
                <input type="hidden" id="payment-order-id" name="order_id">
                
                <!-- Valor Restante -->
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-600/30">
                    <label class="block text-xs text-orange-700 dark:text-orange-400 font-medium mb-1">Valor Restante</label>
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="remaining-amount">R$ 0,00</div>
                </div>

                <!-- Valor a Pagar -->
                <div class="space-y-2">
                    <label for="payment-amount" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Valor a Pagar *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <input type="number" 
                               id="payment-amount" 
                               name="amount" 
                               step="0.01" 
                               min="0.01"
                               required
                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-green-500 dark:focus:border-green-500 focus:ring-1 focus:ring-green-500 dark:focus:ring-green-500 text-sm"
                               placeholder="0,00">
                    </div>
                </div>

                <!-- Forma de Pagamento -->
                <div class="space-y-2">
                    <label for="payment-method" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Forma de Pagamento *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <select id="payment-method" 
                                name="payment_method" 
                                required
                                class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-green-500 dark:focus:border-green-500 focus:ring-1 focus:ring-green-500 dark:focus:ring-green-500 text-sm">
                            <option value="">Selecione...</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="pix">PIX</option>
                            <option value="cartao">Cartão</option>
                            <option value="transferencia">Transferência</option>
                            <option value="boleto">Boleto</option>
                        </select>
                    </div>
                </div>

                <!-- Data do Pagamento -->
                <div class="space-y-2">
                    <label for="payment-date" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Data do Pagamento *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <input type="date" 
                               id="payment-date" 
                               name="payment_date" 
                               required
                               value="<?php echo e(date('Y-m-d')); ?>"
                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-green-500 dark:focus:border-green-500 focus:ring-1 focus:ring-green-500 dark:focus:ring-green-500 text-sm">
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="closePaymentModal()"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-green-600 dark:bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Registrar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edição de Imagem de Capa -->
    <div id="cover-image-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-lg shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    Editar Imagem de Capa
                </h3>
                <button onclick="closeCoverImageModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="cover-image-form" enctype="multipart/form-data" onsubmit="submitCoverImage(event)" class="space-y-5">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="cover-item-id" name="item_id">
                
                <!-- Informação sobre tamanho recomendado -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-600/30">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-xs text-blue-700 dark:text-blue-400 font-medium">Tamanho Recomendado</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">794 x 1123 pixels (A4 em 96 DPI)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Selecionar Imagem -->
                <div class="space-y-2">
                    <label for="cover-image-input" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">
                        Selecione uma imagem *
                    </label>
                    <input type="file" 
                           id="cover-image-input" 
                           name="cover_image" 
                           accept="image/*" 
                           onchange="previewCoverImage(this)"
                           class="hidden"
                           data-paste-upload="true"
                           data-paste-images-only="true"
                           data-paste-max-size="10"
                           data-paste-extensions="jpg,jpeg,png,gif">
                    <button type="button" 
                            onclick="window.pasteModal.open(document.getElementById('cover-image-input'))"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Adicionar Imagem (Ctrl+V, Arraste ou Clique)
                    </button>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2">
                        💡 Use Ctrl+V para colar da área de transferência!
                    </p>
                </div>
                
                <!-- Preview da Imagem -->
                <div id="cover-preview" class="hidden">
                    <label class="block text-xs text-gray-600 dark:text-gray-300 font-medium mb-2">Visualização</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <img id="cover-preview-img" src="" alt="Preview" class="max-w-full max-h-64 mx-auto object-contain rounded-lg">
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeCoverImageModal()" 
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-indigo-600 dark:bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Salvar Imagem
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Movimentação -->
    <div id="move-confirm-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-md shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    Confirmar Movimentação
                </h3>
                <button onclick="closeMoveConfirmModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Conteúdo -->
            <div class="mb-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Tem certeza que deseja mover este pedido para <strong id="move-confirm-status-name" class="text-indigo-600 dark:text-indigo-400"></strong>?
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Esta ação atualizará o status do pedido no kanban.
                </p>
            </div>
            
            <!-- Botões -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeMoveConfirmModal()" 
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                    Cancelar
                </button>
                <button type="button" onclick="confirmMoveCard()" 
                        class="px-6 py-2.5 bg-indigo-600 dark:bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirmar Movimentação
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitação de Edição -->
    <div id="editRequestModal" class="hidden fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border border-gray-300 dark:border-gray-700 w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-900/30">
                            <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Solicitar Edição</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pedido <span id="modalEditOrderId" class="font-semibold"></span></p>
                        </div>
                    </div>
                    <button onclick="closeEditRequestModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="mt-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Esta solicitação será enviada para aprovação do administrador.
                                </p>
                            </div>
                        </div>
                    </div>

                    <label for="editRequestReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motivo da Edição <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <textarea 
                        id="editRequestReason" 
                        rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Descreva o motivo pelo qual este pedido precisa ser editado..."
                        maxlength="1000"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo de 1000 caracteres</p>
                    <p id="editReasonError" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden">O motivo é obrigatório</p>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        onclick="closeEditRequestModal()" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                        Cancelar
                    </button>
                    <button 
                        onclick="submitEditRequest()" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                        Solicitar Edição
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Solicitação de Antecipação -->
    <div id="delivery-request-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-lg shadow-xl dark:shadow-gray-900/25 rounded-lg bg-white dark:bg-gray-800">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    Solicitar Antecipação
                </h3>
                <button onclick="closeDeliveryRequestModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="delivery-request-form" onsubmit="submitDeliveryRequest(event)" class="space-y-5">
                <input type="hidden" id="delivery-order-id" name="order_id">
                <input type="hidden" id="current-delivery-date" name="current_delivery_date">
                
                <!-- Data de Entrega Atual -->
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-600/30">
                    <label class="block text-xs text-orange-700 dark:text-orange-400 font-medium mb-1">Data de Entrega Atual</label>
                    <div class="text-lg font-bold text-orange-600 dark:text-orange-400" id="current-delivery-display">-</div>
                </div>

                <!-- Nova Data Solicitada -->
                <div class="space-y-2">
                    <label for="requested-delivery-date" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Nova Data Solicitada *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <input type="date" 
                               id="requested-delivery-date" 
                               name="requested_delivery_date" 
                               required
                               class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-500 text-sm">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            A data deve ser anterior à data de entrega atual
                        </p>
                    </div>
                </div>

                <!-- Motivo da Solicitação -->
                <div class="space-y-2">
                    <label for="delivery-reason" class="block text-xs text-gray-600 dark:text-gray-300 font-medium">Motivo da Solicitação *</label>
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-md p-4">
                        <textarea id="delivery-reason" 
                                  name="reason" 
                                  rows="4"
                                  required
                                  maxlength="500"
                                  placeholder="Explique o motivo da antecipação..."
                                  class="w-full px-4 py-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-500 text-sm resize-none"></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-right">Máximo 500 caracteres</p>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="closeDeliveryRequestModal()"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 dark:bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-700 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Enviar Solicitação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variáveis globais para confirmação de movimentação (fora da IIFE para serem acessíveis)
        let pendingMoveOrderId = null;
        let pendingMoveStatusId = null;
        
        // Garantir que o código seja executado após o DOM estar pronto
        (function() {
            // Função para busca com Enter
            window.handleSearchKeypress = function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    // Submeter o formulário pai do input de busca
                    event.target.closest('form').submit();
                }
            };

            // Drag and Drop functionality (apenas para administradores)
            let draggedElement = null;
            let isDragging = false;
            const isAdmin = <?php echo e(Auth::user()->isAdmin() ? 'true' : 'false'); ?>;

            if (isAdmin) {
            document.querySelectorAll('.kanban-card').forEach(card => {
                card.addEventListener('dragstart', function(e) {
                    isDragging = true;
                    draggedElement = this;
                    this.style.opacity = '0.5';
                    this.classList.add('scale-95');
                });

                card.addEventListener('dragend', function(e) {
                    setTimeout(() => { isDragging = false; }, 100);
                    this.style.opacity = '1';
                    this.classList.remove('scale-95');
                });

                // Prevenir click quando estiver arrastando
                card.addEventListener('click', function(e) {
                    if (isDragging) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                });
            });

            document.querySelectorAll('.kanban-column').forEach(column => {
                column.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
                });

                column.addEventListener('dragleave', function(e) {
                    this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
                });

                column.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
                    
                    if (draggedElement) {
                        this.appendChild(draggedElement);
                        
                        const orderId = draggedElement.dataset.orderId;
                        const newStatusId = this.dataset.statusId;
                        
                        // Atualizar status via AJAX
                        updateOrderStatus(orderId, newStatusId);
                    }
                });
            });
        }

        function updateOrderStatus(orderId, statusId) {
            fetch(`/kanban/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status_id: statusId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Status atualizado com sucesso!', 'success');
                } else {
                    showNotification('Erro ao atualizar status', 'error');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao atualizar status', 'error');
                location.reload();
            });
        }

        function openOrderModal(orderId) {
            if (isDragging) return;
            
            // Armazenar orderId no modal para uso posterior
            const orderModal = document.getElementById('order-modal');
            if (orderModal) {
                orderModal.setAttribute('data-current-order-id', orderId);
            }
            
            fetch(`/kanban/order/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    displayOrderDetails(data);
                    orderModal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao carregar detalhes do pedido', 'error');
                });
        }
        
        // Expor funções globalmente
        window.openOrderModal = openOrderModal;
        
        function closeOrderModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }
        
        window.closeOrderModal = closeOrderModal;

        function displayOrderDetails(order) {
            const payment = order.payment;
            // Contar arquivos das personalizações E dos itens
            const totalFiles = order.items.reduce((sum, item) => {
                let itemFilesCount = 0;
                // Contar arquivos das personalizações
                if (item.sublimations) {
                    item.sublimations.forEach(sub => {
                        if (sub.files) {
                            itemFilesCount += sub.files.length;
                        }
                    });
                }
                // Contar arquivos dos itens
                if (item.files) {
                    itemFilesCount += item.files.length;
                }
                return sum + itemFilesCount;
            }, 0);
            
            let html = `
                <!-- Container principal com 2 colunas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- COLUNA ESQUERDA -->
                    <div class="space-y-6">
                        <!-- Ações do Pedido -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 border-2 border-blue-200 dark:border-blue-600/30">
                    <h4 class="font-semibold mb-3 text-blue-900 dark:text-blue-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        Ações
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="openEditRequestModal(${order.id})" 
                                class="px-3 py-1.5 text-sm bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-medium rounded-md transition-all flex items-center shadow">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Pedido
                        </button>
                        <?php if(Auth::user()->isAdmin()): ?>
                        <div class="flex gap-2 flex-1">
                            <select id="move-status-select" 
                                    class="flex-1 px-3 py-1.5 text-sm bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-600/30 rounded-md text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all">
                                <option value="">Selecione a coluna...</option>
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->id); ?>" data-status-id="<?php echo e($status->id); ?>">
                                    <?php echo e($status->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button onclick="moveCardToColumn(${order.id})" 
                                    class="px-4 py-1.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-md transition-all flex items-center shadow">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mover
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                    <!-- Botões de Download -->
                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-600/30">
                    <h4 class="font-semibold mb-3 text-indigo-900 dark:text-indigo-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Downloads
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <a href="/kanban/download-costura/${order.id}" target="_blank"
                           class="flex items-center justify-center px-3 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Costura (A4)
                        </a>
                        <a href="/kanban/download-personalizacao/${order.id}" target="_blank"
                           class="flex items-center justify-center px-3 py-2 text-sm bg-pink-600 text-white rounded-md hover:bg-pink-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Personalização (A4)
                        </a>
                        ${totalFiles > 0 ? `
                        <button onclick="downloadAllFiles(${order.id})"
                                class="flex items-center justify-center px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Arquivos da Arte (${totalFiles})
                        </button>
                        ` : ''}
                    </div>
                </div>

                    <!-- Informações do Cliente -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Cliente
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <div><strong class="text-gray-900 dark:text-gray-100">Nome:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.name}</span></div>
                        <div><strong class="text-gray-900 dark:text-gray-100">Telefone:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.phone_primary || '-'}</span></div>
                        ${order.client.email ? `<div><strong class="text-gray-900 dark:text-gray-100">Email:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.email}</span></div>` : ''}
                        ${order.client.cpf_cnpj ? `<div><strong class="text-gray-900 dark:text-gray-100">CPF/CNPJ:</strong> <span class="text-gray-700 dark:text-gray-300">${order.client.cpf_cnpj}</span></div>` : ''}
                    </div>
                </div>

                    <!-- Vendedor -->
                ${order.seller ? `
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Vendedor
                    </h4>
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <div><strong class="text-gray-900 dark:text-gray-100">Nome:</strong> <span class="text-gray-700 dark:text-gray-300">${order.seller}</span></div>
                    </div>
                </div>
                ` : ''}

                    <!-- Itens do Pedido -->
                <div class="space-y-6">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Itens do Pedido (${order.items.length})
                    </h4>
                    
                    ${order.items.map((item, index) => `
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border-2 border-indigo-200 dark:border-indigo-600/30 p-6">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-indigo-300 dark:border-indigo-600/30">
                            <h5 class="text-xl font-bold text-indigo-900 dark:text-indigo-300">Item ${item.item_number || index + 1}</h5>
                            <span class="text-sm bg-indigo-600 dark:bg-indigo-600 text-white px-3 py-1 rounded-full font-semibold">${item.quantity} peças</span>
                        </div>

                        <!-- Nome da Arte -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4 border-2 border-purple-200 dark:border-purple-600/30">
                            <div class="flex items-center justify-between mb-3">
                                <h6 class="font-semibold text-purple-900 dark:text-purple-300 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                    Nome da Arte
                                </h6>
                                <button onclick="saveArtName(${item.id})" 
                                        id="save-art-name-btn-${item.id}"
                                        class="px-3 py-1 bg-purple-600 text-white text-xs rounded-md hover:bg-purple-700 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Salvar
                                </button>
                            </div>
                            <input type="text" 
                                   id="art-name-input-${item.id}"
                                   value="${item.art_name || ''}"
                                   placeholder="Digite o nome da arte..."
                                   class="w-full px-3 py-2 border border-purple-300 dark:border-purple-600/30 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 rounded-md focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-500 focus:border-purple-500 dark:focus:border-transparent"
                                   onkeypress="if(event.key==='Enter') saveArtName(${item.id})">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-start">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Este nome aparecerá no card do Kanban e nos PDFs
                            </p>
                        </div>

                        <!-- Imagem de Capa -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Imagem de Capa
                                </h6>
                                <button onclick="editItemCoverImage(${item.id})" 
                                        class="px-3 py-1 bg-indigo-600 dark:bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                                    ${item.cover_image ? 'Alterar' : 'Adicionar'}
                                </button>
                            </div>
                            <div class="mb-2 flex items-start bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-600/30 rounded-md p-2">
                                <svg class="w-4 h-4 mr-1.5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-blue-700 dark:text-blue-400"><strong>Tamanho recomendado:</strong> 794 x 1123 pixels (A4 em 96 DPI)</span>
                            </div>
                            ${item.cover_image_url ? `
                                <img src="${item.cover_image_url}" alt="Capa" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 shadow-md bg-gray-50 dark:bg-gray-900/20" style="max-height: 600px; object-fit: contain;" onerror="this.parentElement.innerHTML='<div class=\'text-center text-gray-500 dark:text-gray-400 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/20\'><svg class=\'w-16 h-16 mx-auto mb-3 text-gray-400 dark:text-gray-500\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg><p class=\'text-sm font-medium mb-1 text-gray-700 dark:text-gray-300\'>Imagem não encontrada</p><p class=\'text-xs text-gray-500 dark:text-gray-400\'>O arquivo de imagem não foi encontrado no servidor</p></div>'">
                            ` : `
                                <div class="text-center text-gray-500 dark:text-gray-400 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/20">
                                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Nenhuma imagem de capa</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Adicione uma imagem de 794 x 1123 pixels</p>
                                </div>
                            `}
                        </div>

                        <!-- Detalhes da Costura -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                            <h6 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Costura
                            </h6>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm text-gray-700 dark:text-gray-300">
                                ${order.store ? `<div><strong class="text-gray-900 dark:text-gray-100">Loja:</strong> <span class="text-gray-700 dark:text-gray-300">${order.store.name}</span></div>` : ''}
                                <div><strong class="text-gray-900 dark:text-gray-100">Tecido:</strong> <span class="text-gray-700 dark:text-gray-300">${item.fabric}</span></div>
                                <div><strong class="text-gray-900 dark:text-gray-100">Cor:</strong> <span class="text-gray-700 dark:text-gray-300">${item.color}</span></div>
                                ${item.collar ? `<div><strong class="text-gray-900 dark:text-gray-100">Gola:</strong> <span class="text-gray-700 dark:text-gray-300">${item.collar}</span></div>` : ''}
                                ${item.detail ? `<div><strong class="text-gray-900 dark:text-gray-100">Detalhe:</strong> <span class="text-gray-700 dark:text-gray-300">${item.detail}</span></div>` : ''}
                                ${item.model ? `<div><strong class="text-gray-900 dark:text-gray-100">Tipo de Corte:</strong> <span class="text-gray-700 dark:text-gray-300">${item.model}</span></div>` : ''}
                                <div><strong class="text-gray-900 dark:text-gray-100">Personalização:</strong> <span class="text-gray-700 dark:text-gray-300">${item.print_type}</span></div>
                            </div>
                            
                            <div class="mt-4">
                                <strong class="block mb-2 text-gray-900 dark:text-gray-300">Tamanhos:</strong>
                                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                    ${Object.entries(item.sizes).map(([size, qty]) => 
                                        qty > 0 ? `
                                        <div class="bg-gray-100 dark:bg-gray-700 rounded-md px-2 py-1 text-center border border-gray-200 dark:border-gray-600">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">${size}</span>
                                            <p class="font-bold text-sm text-gray-900 dark:text-gray-200">${qty}</p>
                                        </div>
                                        ` : ''
                                    ).join('')}
                                </div>
                            </div>
                        </div>

                        <!-- Personalização -->
                        ${item.sublimations && item.sublimations.length > 0 ? `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                            <h6 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                Personalização
                            </h6>
                            ${item.art_name ? `<p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong class="text-gray-900 dark:text-gray-100">Nome da Arte:</strong> <span class="text-gray-700 dark:text-gray-300">${item.art_name}</span></p>` : ''}
                            <div class="space-y-2">
                                ${item.sublimations.map(sub => {
                                    const sizeName = sub.size ? sub.size.name : sub.size_name;
                                    const sizeDimensions = sub.size ? sub.size.dimensions : '';
                                    const locationName = sub.location ? sub.location.name : sub.location_name;
                                    const appType = sub.application_type ? sub.application_type.toUpperCase() : 'APLICAÇÃO';
                                    
                                    return `
                                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 rounded-md p-3 text-sm border border-gray-200 dark:border-gray-600">
                                        <div>
                                            <strong class="text-gray-900 dark:text-gray-200">
                                                ${sizeName ? sizeName : appType}${sizeDimensions ? ` (${sizeDimensions})` : ''}
                                            </strong>
                                            ${locationName ? ` <span class="text-gray-600 dark:text-gray-400">- ${locationName}</span>` : ''}
                                            <span class="text-gray-600 dark:text-gray-400"> x${sub.quantity}</span>
                                            ${sub.color_count > 0 ? `<br><span class="text-xs text-gray-500 dark:text-gray-400">${sub.color_count} ${sub.color_count == 1 ? 'Cor' : 'Cores'}${sub.has_neon ? ' + Neon' : ''}</span>` : ''}
                                        </div>
                                        <div class="text-right">
                                            <div class="text-gray-600 dark:text-gray-400 text-xs">R$ ${parseFloat(sub.unit_price).toFixed(2).replace('.', ',')} × ${sub.quantity}</div>
                                            ${sub.discount_percent > 0 ? `<div class="text-xs text-green-600 dark:text-green-400">-${sub.discount_percent}%</div>` : ''}
                                            <div class="font-bold text-gray-900 dark:text-gray-200">R$ ${parseFloat(sub.final_price).toFixed(2).replace('.', ',')}</div>
                                        </div>
                                    </div>
                                `}).join('')}
                            </div>
                        </div>
                        ` : ''}

                        <!-- Arquivos do Item -->
                        ${(item.files && item.files.length > 0) || (item.sublimations && item.sublimations.some(sub => sub.files && sub.files.length > 0)) ? `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mt-4">
                            <h6 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Arquivos da Arte
                            </h6>
                            <div class="space-y-2">
                                ${item.files && item.files.length > 0 ? item.files.map(file => `
                                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-gray-900 dark:text-gray-300">${file.file_name}</span>
                                            ${file.file_name.toLowerCase().endsWith('.cdr') || file.file_name.toLowerCase().endsWith('.cdrx') ? '<span class="ml-2 px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded">Corel</span>' : ''}
                                        </div>
                                    </div>
                                `).join('') : ''}
                                ${item.sublimations ? item.sublimations.map(sub => 
                                    sub.files && sub.files.length > 0 ? sub.files.map(file => `
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-md p-2 text-sm border border-gray-200 dark:border-gray-600">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-gray-900 dark:text-gray-300">${file.file_name}</span>
                                                ${file.file_name.toLowerCase().endsWith('.cdr') || file.file_name.toLowerCase().endsWith('.cdrx') ? '<span class="ml-2 px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded">Corel</span>' : ''}
                                            </div>
                                        </div>
                                    `).join('') : ''
                                ).filter(f => f).join('') : ''}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    `).join('')}
                </div>
                    </div>

                    <!-- COLUNA DIREITA -->
                    <div class="space-y-6">
                        <!-- Data de Entrega e Solicitação de Antecipação -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Entrega
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data de Pedido:</strong></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">${new Date(order.created_at).toLocaleDateString('pt-BR')}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data de Entrega:</strong></span>
                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400">${order.delivery_date ? new Date(order.delivery_date).toLocaleDateString('pt-BR') : 'Não definida'}</span>
                        </div>
                        ${order.pending_delivery_request ? `
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-600/30 rounded-lg p-3">
                            <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-400 mb-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Solicitação Pendente
                            </p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-400">Nova data solicitada: ${order.pending_delivery_request.requested_delivery_date ? new Date(order.pending_delivery_request.requested_delivery_date).toLocaleDateString('pt-BR') : 'N/A'}</p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">Motivo: ${order.pending_delivery_request.reason || 'Não informado'}</p>
                        </div>
                        ` : `
                        <button type="button" 
                                class="delivery-request-btn px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition text-sm font-medium flex items-center"
                                data-order-id="${order.id}"
                                data-delivery-date="${order.delivery_date || ''}">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Solicitar Antecipação
                        </button>
                        `}
                    </div>
                </div>

                        <!-- Pagamento -->
                ${order.payments && order.payments.length > 0 ? `
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Pagamento
                    </h4>
                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        ${order.entry_date ? `<div><strong class="text-gray-900 dark:text-gray-100">Data de Entrada:</strong> <span class="text-gray-700 dark:text-gray-300">${new Date(order.entry_date).toLocaleDateString('pt-BR')}</span></div>` : ''}
                        <div><strong class="text-gray-900 dark:text-gray-100">Formas de Pagamento:</strong></div>
                        ${order.payments.map(payment => `
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3 border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="capitalize font-medium text-gray-900 dark:text-gray-200">${payment.payment_method}</span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100">R$ ${(() => {
                                        // Calcular valor do pagamento baseado em payment_methods
                                        if (payment.payment_methods && Array.isArray(payment.payment_methods)) {
                                            return payment.payment_methods.reduce((sum, m) => sum + parseFloat(m.amount || 0), 0).toFixed(2).replace('.', ',');
                                        }
                                        return parseFloat(payment.entry_amount || 0).toFixed(2).replace('.', ',');
                                    })()}</span>
                                </div>
                                ${payment.payment_date ? `
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    ${new Date(payment.payment_date).toLocaleDateString('pt-BR')}
                                </div>
                                ` : ''}
                            </div>
                        `).join('')}
                        ${(() => {
                            // Calcular total pago baseado nos payment_methods (fonte única de verdade)
                            let totalPago = 0;
                            order.payments.forEach(p => {
                                if (p.payment_methods && Array.isArray(p.payment_methods)) {
                                    p.payment_methods.forEach(method => {
                                        totalPago += parseFloat(method.amount || 0);
                                    });
                                } else {
                                    // Fallback para pagamentos antigos sem payment_methods
                                    totalPago += parseFloat(p.entry_amount || 0);
                                }
                            });
                            const restante = parseFloat(order.total) - totalPago;
                            return `
                        <div class="border-t border-gray-300 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between text-gray-700 dark:text-gray-300 py-1"><span>Total:</span><strong class="text-gray-900 dark:text-gray-100">R$ ${parseFloat(order.total).toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between text-gray-700 dark:text-gray-300 py-1"><span>Pago:</span><strong class="text-green-600 dark:text-green-400">R$ ${totalPago.toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between text-gray-700 dark:text-gray-300 py-1"><span>${restante < 0 ? 'Crédito do Cliente:' : 'Restante:'}</span><strong class="${restante > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400'}">R$ ${Math.abs(restante).toFixed(2).replace('.', ',')}</strong></div>
                        </div>
                        ${restante > 0 ? `
                        <div class="border-t border-gray-300 dark:border-gray-700 pt-3 mt-3">
                            <button onclick="openPaymentModal(${order.id}, ${restante})" 
                                    class="px-4 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition text-sm font-medium flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Registrar Pagamento Restante
                            </button>
                        </div>
                        ` : ''}
                            `;
                        })()}
                    </div>
                </div>
                ` : ''}

                        <!-- Comentários -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Comentários
                    </h4>
                    
                    <!-- Formulário de Novo Comentário -->
                    <div class="mb-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                        <textarea id="comment-text-${order.id}" placeholder="Escreva seu comentário..." 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500 focus:border-indigo-500 dark:focus:border-transparent" 
                                  rows="3"></textarea>
                        <button onclick="addComment(${order.id})" 
                                class="mt-2 px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 text-sm font-medium">
                            Adicionar Comentário
                        </button>
                    </div>

                    <!-- Lista de Comentários -->
                    <div id="comments-list-${order.id}" class="space-y-3">
                        ${order.comments && order.comments.length > 0 ? order.comments.map(comment => `
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-500 dark:border-blue-400">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-semibold text-sm text-blue-900 dark:text-blue-300">${comment.user_name}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">${new Date(comment.created_at).toLocaleString('pt-BR')}</span>
                                </div>
                                <p class="text-sm text-blue-800 dark:text-blue-300">${comment.comment}</p>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhum comentário ainda.</p>'}
                    </div>
                </div>

                        <!-- Log de Atendimento -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 p-4">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Log de Atendimento
                    </h4>
                    <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                        ${order.logs && order.logs.length > 0 ? order.logs.map(log => `
                            <div class="flex items-start space-x-3 text-sm border-l-4 ${
                                log.action === 'status_changed' ? 'border-purple-500 dark:border-purple-400 bg-purple-50 dark:bg-purple-900/20' :
                                log.action === 'comment_added' ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20' :
                                log.action === 'order_edited' ? 'border-orange-500 dark:border-orange-400 bg-orange-50 dark:bg-orange-900/20' :
                                'border-gray-400 dark:border-gray-500 bg-gray-50 dark:bg-gray-700'
                            } rounded-md p-3 border-r border-t border-b border-gray-200 dark:border-gray-600">
                                <div class="flex-shrink-0 mt-0.5">
                                    ${log.action === 'status_changed' ? 
                                        '<svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' :
                                      log.action === 'comment_added' ? 
                                        '<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>' :
                                      log.action === 'order_edited' ? 
                                        '<svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>' :
                                        '<svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
                                    }
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">${log.user_name}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">${new Date(log.created_at).toLocaleString('pt-BR')}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">${log.description}</p>
                                    ${log.action === 'order_edited' && log.changes ? (() => {
                                        let changesHtml = '<div class="mt-2 space-y-1">';
                                        const changes = typeof log.changes === 'string' ? JSON.parse(log.changes) : log.changes;
                                        
                                        // Mapeamento de nomes de campos para português
                                        const fieldLabels = {
                                            'name': 'Nome',
                                            'phone_primary': 'Telefone',
                                            'phone_secondary': 'Telefone 2',
                                            'email': 'E-mail',
                                            'cpf_cnpj': 'CPF/CNPJ',
                                            'address': 'Endereço',
                                            'city': 'Cidade',
                                            'state': 'Estado',
                                            'zip_code': 'CEP',
                                            'category': 'Categoria',
                                            'fabric': 'Tecido',
                                            'color': 'Cor',
                                            'collar': 'Gola',
                                            'model': 'Modelo',
                                            'detail': 'Detalhe',
                                            'print_type': 'Personalização',
                                            'quantity': 'Quantidade',
                                            'unit_price': 'Preço Unitário',
                                            'total_price': 'Preço Total',
                                            'sizes': 'Tamanhos',
                                            'delivery_date': 'Data Entrega',
                                            'entry_date': 'Data Entrada',
                                            'discount': 'Desconto',
                                            'delivery_fee': 'Taxa Entrega',
                                            'total': 'Total',
                                            'notes': 'Observações',
                                            'seller': 'Vendedor',
                                            'art_name': 'Nome da Arte',
                                            'art_notes': 'Notas da Arte',
                                        };
                                        
                                        // Formatar valor para exibição
                                        const formatValue = (val) => {
                                            if (val === null || val === undefined) return '-';
                                            if (typeof val === 'object') return JSON.stringify(val);
                                            if (typeof val === 'number' && val.toString().includes('.')) {
                                                return 'R$ ' + parseFloat(val).toFixed(2).replace('.', ',');
                                            }
                                            return val;
                                        };
                                        
                                        // Cliente
                                        if (changes.client) {
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300">📋 Cliente:</div>';
                                            Object.entries(changes.client).forEach(([field, change]) => {
                                                const label = fieldLabels[field] || field;
                                                changesHtml += '<div class="text-xs ml-2 text-gray-600 dark:text-gray-400">' +
                                                    '<span class="font-medium">' + label + ':</span> ' +
                                                    '<span class="line-through text-red-500 dark:text-red-400">' + formatValue(change.old) + '</span>' +
                                                    ' → <span class="text-green-600 dark:text-green-400">' + formatValue(change.new) + '</span></div>';
                                            });
                                        }
                                        
                                        // Pedido
                                        if (changes.order) {
                                            changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300 mt-1">📦 Pedido:</div>';
                                            Object.entries(changes.order).forEach(([field, change]) => {
                                                const label = fieldLabels[field] || field;
                                                changesHtml += '<div class="text-xs ml-2 text-gray-600 dark:text-gray-400">' +
                                                    '<span class="font-medium">' + label + ':</span> ' +
                                                    '<span class="line-through text-red-500 dark:text-red-400">' + formatValue(change.old) + '</span>' +
                                                    ' → <span class="text-green-600 dark:text-green-400">' + formatValue(change.new) + '</span></div>';
                                            });
                                        }
                                        
                                        // Itens
                                        if (changes.items) {
                                            Object.entries(changes.items).forEach(([itemId, itemChange]) => {
                                                if (itemChange.type === 'modified' && itemChange.changes) {
                                                    changesHtml += '<div class="text-xs font-semibold text-orange-700 dark:text-orange-300 mt-1">👕 Item ' + itemId + ' (modificado):</div>';
                                                    Object.entries(itemChange.changes).forEach(([field, change]) => {
                                                        if (field !== 'sublimations' && field !== 'files') {
                                                            const label = fieldLabels[field] || field;
                                                            changesHtml += '<div class="text-xs ml-2 text-gray-600 dark:text-gray-400">' +
                                                                '<span class="font-medium">' + label + ':</span> ' +
                                                                '<span class="line-through text-red-500 dark:text-red-400">' + formatValue(change.old) + '</span>' +
                                                                ' → <span class="text-green-600 dark:text-green-400">' + formatValue(change.new) + '</span></div>';
                                                        }
                                                    });
                                                } else if (itemChange.type === 'added') {
                                                    changesHtml += '<div class="text-xs font-semibold text-green-700 dark:text-green-300 mt-1">➕ Novo Item ' + itemId + ' adicionado</div>';
                                                } else if (itemChange.type === 'removed') {
                                                    changesHtml += '<div class="text-xs font-semibold text-red-700 dark:text-red-300 mt-1">➖ Item ' + itemId + ' removido</div>';
                                                }
                                            });
                                        }
                                        
                                        changesHtml += '</div>';
                                        return changesHtml;
                                    })() : ''}
                                </div>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhum log de atendimento ainda.</p>'}
                    </div>
                </div>
                    </div>
                </div>
            `;
            
            // Pegar o nome da arte do primeiro item (se existir)
            const firstArtName = order.items.find(item => item.art_name)?.art_name;
            const deliveryDate = order.delivery_date ? new Date(order.delivery_date).toLocaleDateString('pt-BR') : 'Sem data';
            const isEvent = order.is_event;
            
            // Criar título com OS e data à esquerda, nome da arte centralizado (se existir)
            const modalTitle = document.getElementById('modal-title');
            modalTitle.innerHTML = `
                <div class="flex items-start justify-between w-full">
                    <div class="flex flex-col">
                        <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">OS ${String(order.id).padStart(2, '0')}</span>
                        <span class="text-base font-semibold ${isEvent ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400'}">${deliveryDate}${isEvent ? ' - EVENTO' : ''}</span>
                    </div>
                    ${firstArtName ? `
                    <div class="flex-1 flex justify-center">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">${firstArtName}</span>
                    </div>
                    ` : ''}
                    <div class="w-24"></div>
                </div>
            `;
            
            document.getElementById('modal-content').innerHTML = html;
            
            // Configurar o select de status após inserir o HTML
            const statusSelect = document.getElementById('move-status-select');
            if (statusSelect && order.status_id) {
                statusSelect.value = order.status_id;
            }
        }

        function addComment(orderId) {
            const commentText = document.getElementById(`comment-text-${orderId}`).value;

            if (!commentText) {
                showNotification('Por favor, escreva um comentário', 'error');
                return;
            }

            fetch(`/kanban/order/${orderId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    comment: commentText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Comentário adicionado com sucesso!', 'success');
                    // Recarregar detalhes do pedido
                    openOrderModal(orderId);
                } else {
                    showNotification('Erro ao adicionar comentário', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao adicionar comentário', 'error');
            });
        }

        function downloadAllFiles(orderId) {
            window.open(`/kanban/download-files/${orderId}`, '_blank');
        }

        function openPaymentModal(orderId, remainingAmount) {
            document.getElementById('payment-order-id').value = orderId;
            document.getElementById('remaining-amount').textContent = `R$ ${parseFloat(remainingAmount).toFixed(2).replace('.', ',')}`;
            document.getElementById('payment-amount').value = parseFloat(remainingAmount).toFixed(2);
            document.getElementById('payment-amount').max = parseFloat(remainingAmount).toFixed(2);
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
            document.getElementById('payment-form').reset();
        }

        function submitPayment(event) {
            event.preventDefault();
            
            const orderId = document.getElementById('payment-order-id').value;
            const amount = parseFloat(document.getElementById('payment-amount').value);
            const paymentMethod = document.getElementById('payment-method').value;
            const paymentDate = document.getElementById('payment-date').value;

            fetch(`/kanban/order/${orderId}/add-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    amount: amount,
                    payment_method: paymentMethod,
                    payment_date: paymentDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Pagamento registrado com sucesso!', 'success');
                    closePaymentModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao registrar pagamento', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao registrar pagamento', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Fechar modal ao clicar fora
        document.getElementById('order-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });

        document.getElementById('cover-image-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCoverImageModal();
            }
        });

        document.getElementById('payment-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });

        // Event listener para botões de solicitar antecipação usando delegação de eventos
        document.addEventListener('click', function(e) {
            const deliveryBtn = e.target.closest('.delivery-request-btn');
            if (deliveryBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                const orderId = deliveryBtn.getAttribute('data-order-id');
                const deliveryDate = deliveryBtn.getAttribute('data-delivery-date');
                
                if (typeof window.openDeliveryRequestModal === 'function') {
                    window.openDeliveryRequestModal(orderId, deliveryDate);
                } else {
                    console.error('openDeliveryRequestModal não está disponível');
                    showNotification('Erro: Função não disponível. Recarregue a página.', 'error');
                }
            }
        });
        
        document.getElementById('delivery-request-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeliveryRequestModal();
            }
        });

        const editRequestModal = document.getElementById('editRequestModal');
        if (editRequestModal) {
            editRequestModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditRequestModal();
                }
            });
        }

        const moveConfirmModal = document.getElementById('move-confirm-modal');
        if (moveConfirmModal) {
            moveConfirmModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeMoveConfirmModal();
                }
            });
        }

        function openDeliveryRequestModal(orderId, currentDeliveryDate) {
            if (!orderId) {
                console.error('Order ID não fornecido');
                showNotification('Erro: ID do pedido não encontrado', 'error');
                return;
            }
            
            document.getElementById('delivery-order-id').value = orderId;
            
            // Validar e processar data de entrega
            if (!currentDeliveryDate || currentDeliveryDate === 'null' || currentDeliveryDate === '') {
                showNotification('Erro: Data de entrega não definida para este pedido', 'error');
                return;
            }
            
            // Tentar criar objeto Date
            let deliveryDate;
            try {
                deliveryDate = new Date(currentDeliveryDate);
                if (isNaN(deliveryDate.getTime())) {
                    throw new Error('Data inválida');
                }
            } catch (e) {
                console.error('Erro ao processar data:', e, 'Data recebida:', currentDeliveryDate);
                showNotification('Erro: Data de entrega inválida', 'error');
                return;
            }
            
            document.getElementById('current-delivery-date').value = currentDeliveryDate;
            
            // Formatar data para exibição
            document.getElementById('current-delivery-display').textContent = deliveryDate.toLocaleDateString('pt-BR');
            
            // Definir data máxima para o input (um dia antes da data atual)
            const maxDate = new Date(deliveryDate);
            maxDate.setDate(maxDate.getDate() - 1);
            document.getElementById('requested-delivery-date').max = maxDate.toISOString().split('T')[0];
            
            document.getElementById('delivery-request-modal').classList.remove('hidden');
        }

        function closeDeliveryRequestModal() {
            document.getElementById('delivery-request-modal').classList.add('hidden');
            document.getElementById('delivery-request-form').reset();
        }

        // Funções para modal de imagem de capa
        function editItemCoverImage(itemId) {
            document.getElementById('cover-item-id').value = itemId;
            document.getElementById('cover-image-modal').classList.remove('hidden');
        }

        function closeCoverImageModal() {
            document.getElementById('cover-image-modal').classList.add('hidden');
            document.getElementById('cover-image-form').reset();
            document.getElementById('cover-preview').classList.add('hidden');
        }

        function previewCoverImage(input) {
            const preview = document.getElementById('cover-preview');
            const previewImg = document.getElementById('cover-preview-img');
            
            if (input.files && input.files.length > 0) {
                // Se houver múltiplos arquivos, usar o primeiro
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }
        
        // Listener para atualizar preview quando arquivo for adicionado via modal de paste
        document.addEventListener('DOMContentLoaded', function() {
            const coverImageInput = document.getElementById('cover-image-input');
            if (coverImageInput) {
                coverImageInput.addEventListener('change', function() {
                    previewCoverImage(this);
                });
            }
            
            // Abrir modal automaticamente se houver pesquisa com resultado
            <?php if(isset($autoOpenOrderId) && $autoOpenOrderId): ?>
            // Aguardar um pouco para garantir que a página carregou completamente
            setTimeout(function() {
                if (typeof openOrderModal === 'function') {
                    openOrderModal(<?php echo e($autoOpenOrderId); ?>);
                }
            }, 300);
            <?php endif; ?>
        });

        function submitCoverImage(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const itemId = formData.get('item_id');
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="animate-pulse">Enviando...</span>';
            
            fetch(`/api/order-items/${itemId}/cover-image`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Erro ao fazer upload da imagem');
                    });
                }
                return response.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                console.log('Resposta do upload:', data);
                
                if (data.success) {
                    // Verificar informações de debug
                    if (data.debug) {
                        console.log('Debug info:', data.debug);
                        if (!data.debug.file_exists) {
                            console.error('Arquivo não encontrado após upload!');
                            showNotification('Aviso: Arquivo pode não ter sido salvo corretamente', 'warning');
                        }
                        if (!data.debug.symlink_exists) {
                            console.error('Symlink do storage não encontrado!');
                            showNotification('Aviso: Symlink do storage pode não estar configurado', 'warning');
                        }
                    }
                    
                    if (!data.cover_image_url) {
                        console.error('URL da imagem não foi gerada!', data);
                        showNotification('Erro: URL da imagem não foi gerada. Verifique os logs.', 'error');
                        return;
                    }
                    
                    showNotification('Imagem de capa atualizada com sucesso!', 'success');
                    closeCoverImageModal();
                    
                    // Recarregar detalhes do pedido no modal se estiver aberto
                    const orderModal = document.getElementById('order-modal');
                    const currentOrderId = orderModal ? orderModal.getAttribute('data-current-order-id') : null;
                    
                    if (currentOrderId && !orderModal.classList.contains('hidden')) {
                        // Recarregar os detalhes do pedido
                        fetch(`/kanban/order/${currentOrderId}`)
                            .then(response => response.json())
                            .then(orderData => {
                                displayOrderDetails(orderData);
                            })
                            .catch(error => {
                                console.error('Erro ao recarregar detalhes do pedido:', error);
                            });
                    }
                    
                    // Atualizar a imagem no card do kanban
                    if (data.order_id && data.cover_image_url) {
                        console.log('Atualizando card do kanban:', data.order_id, data.cover_image_url);
                        updateKanbanCardImage(data.order_id, data.cover_image_url);
                    } else {
                        console.error('Dados insuficientes para atualizar card:', data);
                    }
                } else {
                    showNotification(data.message || 'Erro ao atualizar imagem de capa', 'error');
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar imagem de capa:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                showNotification('Erro ao atualizar imagem de capa', 'error');
            });
        }

        function updateKanbanCardImage(orderId, imageUrl) {
            console.log('updateKanbanCardImage chamado:', orderId, imageUrl);
            
            // Encontrar o card do pedido no kanban
            const card = document.querySelector(`.kanban-card[data-order-id="${orderId}"]`);
            if (!card) {
                console.warn('Card não encontrado para orderId:', orderId);
                return;
            }
            
            // Encontrar o container da imagem no card
            const imageContainer = card.querySelector('.h-48');
            if (!imageContainer) {
                console.warn('Container de imagem não encontrado no card');
                return;
            }
            
            // Adicionar timestamp para forçar reload da imagem
            const imageUrlWithTimestamp = imageUrl + (imageUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
            
            // Atualizar a imagem
            const img = imageContainer.querySelector('img');
            if (img) {
                console.log('Atualizando imagem existente:', imageUrlWithTimestamp);
                img.src = imageUrlWithTimestamp;
                img.onerror = function() {
                    console.error('Erro ao carregar imagem:', imageUrlWithTimestamp);
                    // Se a imagem falhar ao carregar, mostrar placeholder
                    this.parentElement.innerHTML = '<div class=\'h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';
                };
                img.onload = function() {
                    console.log('Imagem carregada com sucesso:', imageUrlWithTimestamp);
                };
            } else {
                // Se não houver imagem, criar uma nova
                console.log('Criando nova imagem:', imageUrlWithTimestamp);
                imageContainer.innerHTML = `<img src="${imageUrlWithTimestamp}" alt="Capa do Pedido" class="w-full h-48 object-cover" style="object-fit: cover; object-position: center;" onerror="this.parentElement.innerHTML='<div class=\'h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center\'><svg class=\'w-12 h-12 text-white opacity-50\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>'">`;
            }
        }

        function saveArtName(itemId) {
            const input = document.getElementById(`art-name-input-${itemId}`);
            const button = document.getElementById(`save-art-name-btn-${itemId}`);
            const artName = input.value.trim();

            if (!artName) {
                showNotification('Por favor, digite um nome para a arte', 'error');
                return;
            }

            // Mostrar loading
            const originalBtnText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="animate-pulse">Salvando...</span>';

            fetch(`/api/order-items/${itemId}/art-name`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    art_name: artName
                })
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalBtnText;
                
                if (data.success) {
                    showNotification('Nome da arte salvo com sucesso!', 'success');
                    
                    // Recarregar cards do kanban para atualizar o nome no overlay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao salvar nome da arte', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                button.disabled = false;
                button.innerHTML = originalBtnText;
                showNotification('Erro ao salvar nome da arte', 'error');
            });
        }

        function moveCardToColumn(orderId) {
            const selectElement = document.getElementById('move-status-select');
            const newStatusId = selectElement.value;

            if (!newStatusId) {
                showNotification('Por favor, selecione uma coluna', 'error');
                return;
            }

            // Armazenar dados da movimentação pendente
            pendingMoveOrderId = orderId;
            pendingMoveStatusId = newStatusId;
            
            // Mostrar modal de confirmação
            const statusName = selectElement.options[selectElement.selectedIndex].text;
            document.getElementById('move-confirm-status-name').textContent = statusName;
            document.getElementById('move-confirm-modal').classList.remove('hidden');
        }

        function closeMoveConfirmModal() {
            document.getElementById('move-confirm-modal').classList.add('hidden');
            pendingMoveOrderId = null;
            pendingMoveStatusId = null;
        }

        function confirmMoveCard() {
            if (!pendingMoveOrderId || !pendingMoveStatusId) {
                return;
            }

            // Fazer requisição para mover o card
            fetch('/kanban/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: pendingMoveOrderId,
                    status_id: pendingMoveStatusId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Card movido com sucesso!', 'success');
                    
                    // Fechar modais e recarregar página para atualizar o kanban
                    closeMoveConfirmModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    showNotification(data.message || 'Erro ao mover card', 'error');
                    closeMoveConfirmModal();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao mover card', 'error');
                closeMoveConfirmModal();
            });
        }
        
        // Expor função globalmente
        window.moveCardToColumn = moveCardToColumn;

        function submitDeliveryRequest(event) {
            event.preventDefault();
            
            const orderId = document.getElementById('delivery-order-id').value;
            const currentDeliveryDate = document.getElementById('current-delivery-date').value;
            const requestedDeliveryDate = document.getElementById('requested-delivery-date').value;
            const reason = document.getElementById('delivery-reason').value;

            // Validar se a data solicitada é anterior à atual
            if (new Date(requestedDeliveryDate) >= new Date(currentDeliveryDate)) {
                showNotification('A data solicitada deve ser anterior à data de entrega atual', 'error');
                return;
            }

            fetch(`/delivery-requests`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: orderId,
                    current_delivery_date: currentDeliveryDate,
                    requested_delivery_date: requestedDeliveryDate,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Solicitação enviada com sucesso!', 'success');
                    closeDeliveryRequestModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao enviar solicitação', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao enviar solicitação', 'error');
            });
        }
        
        // Variável global para armazenar o ID do pedido sendo editado
        let currentEditOrderId = null;

        // Funções do Modal de Solicitação de Edição
        function openEditRequestModal(orderId) {
            currentEditOrderId = orderId;
            const modal = document.getElementById('editRequestModal');
            if (modal) {
                document.getElementById('modalEditOrderId').textContent = '#' + String(orderId).padStart(6, '0');
                document.getElementById('editRequestReason').value = '';
                document.getElementById('editReasonError').classList.add('hidden');
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeEditRequestModal() {
            const modal = document.getElementById('editRequestModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentEditOrderId = null;
            }
        }

        function submitEditRequest() {
            const reason = document.getElementById('editRequestReason').value.trim();
            const errorElement = document.getElementById('editReasonError');

            if (!reason) {
                errorElement.classList.remove('hidden');
                document.getElementById('editRequestReason').focus();
                return;
            }

            errorElement.classList.add('hidden');

            // Desabilitar botão para evitar duplo clique
            const submitBtn = event.target;
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Enviando...';

            fetch(`/pedidos/${currentEditOrderId}/edit-request`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('Solicitação de edição enviada com sucesso!', 'success');
                    closeEditRequestModal();
                    
                    // Recarregar detalhes do pedido no modal se estiver aberto
                    const orderModal = document.getElementById('order-modal');
                    const currentOrderId = orderModal ? orderModal.getAttribute('data-current-order-id') : null;
                    
                    if (currentOrderId && !orderModal.classList.contains('hidden')) {
                        setTimeout(() => {
                            openOrderModal(parseInt(currentOrderId));
                        }, 500);
                    } else {
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    showNotification(data.message || 'Erro ao enviar solicitação de edição', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Erro detalhado:', error);
                let errorMessage = 'Erro ao enviar solicitação de edição';
                
                if (error.message) {
                    errorMessage = error.message;
                } else if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join(', ');
                }
                
                showNotification(errorMessage, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        }

        // Expor todas as funções necessárias globalmente
        window.addComment = addComment;
        window.downloadAllFiles = downloadAllFiles;
        window.openPaymentModal = openPaymentModal;
        window.closePaymentModal = closePaymentModal;
        window.submitPayment = submitPayment;
        window.submitDeliveryRequest = submitDeliveryRequest;
        window.closeDeliveryRequestModal = closeDeliveryRequestModal;
        window.openDeliveryRequestModal = openDeliveryRequestModal;
        window.showNotification = showNotification;
        window.saveArtName = saveArtName;
        window.editItemCoverImage = editItemCoverImage;
        window.closeCoverImageModal = closeCoverImageModal;
        window.submitCoverImage = submitCoverImage;
        window.closeMoveConfirmModal = closeMoveConfirmModal;
        window.confirmMoveCard = confirmMoveCard;
        window.openEditRequestModal = openEditRequestModal;
        window.closeEditRequestModal = closeEditRequestModal;
        window.submitEditRequest = submitEditRequest;
        })(); // Fim da IIFE
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views/kanban/index.blade.php ENDPATH**/ ?>