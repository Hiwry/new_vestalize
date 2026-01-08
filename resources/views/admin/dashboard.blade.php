@extends('layouts.admin')

@section('content')
        <!-- Cabeçalho -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Painel de Administração</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Gerencie todos os aspectos do sistema de pedidos</p>
        </div>

        <!-- Estatísticas Gerais -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Pedidos</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_orders'] }}</p>
                    </div>
                </div>
            </div>

            @if(auth()->user()->tenant_id === null)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                       <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M4 20c0-4 4-6 8-6s8 2 8 6" />
                       </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuários</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_users'] }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->tenant_id === null)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4h4M4 4l5 5M20 8V4h-4m4 0l-5 5M4 16v4h4m-4 0l5-5M20 16v4h-4m4 0l-5-5" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tamanhos de Sublimação</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_sizes'] }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Solicitações Pendentes</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['pending_delivery_requests'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seções de Gerenciamento -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- Gerenciamento de Pedidos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Gerenciamento de Pedidos</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="{{ route('orders.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Lista de Pedidos</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Visualize e gerencie todos os pedidos</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.cancellations.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="relative mr-3">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                @if(isset($stats['pending_cancellations']) && $stats['pending_cancellations'] > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 dark:bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $stats['pending_cancellations'] }}
                                </span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Solicitações de Cancelamento</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Aprove ou rejeite cancelamentos de pedidos</p>
                            </div>
                        </a>

                @if(auth()->user()->tenant_id === null || auth()->user()->tenant->canAccess('kanban'))
                        <a href="{{ route('kanban.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2-2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H9a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Kanban de Produção</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Acompanhe o progresso dos pedidos</p>
                            </div>
                        </a>
                        @endif

                        <a href="{{ route('delivery-requests.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Solicitações de Entrega</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie solicitações de entrega</p>
                                @if($stats['pending_delivery_requests'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        {{ $stats['pending_delivery_requests'] }} pendente(s)
                                    </span>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gerenciamento Financeiro -->
            @if(auth()->user()->tenant_id === null || auth()->user()->tenant->canAccess('financial'))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Gerenciamento Financeiro</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="{{ route('cash.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Caixa</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie transações financeiras</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Configurações do Sistema -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
            <!-- Produtos e Preços -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Produtos e Preços</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="{{ route('admin.product-options.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Opções de Produtos</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Personalização, tecidos, cores, etc.</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.personalization-prices.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Preços de Personalização</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Configure preços para DTF, Serigrafia, Bordado, Sublimação, Emborrachado e Sublimação Total</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Catálogo e PDV -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Catálogo e PDV</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                @if(auth()->user()->tenant_id === null || auth()->user()->tenant->canAccess('pdv'))
                        <a href="{{ route('admin.quick-products.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h12" />
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Criar Produto PDV</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Visualize ou crie produtos específicos para o ponto de venda</p>
                            </div>
                        </a>
                        @endif

                        <a href="{{ route('admin.catalog-items.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Gerenciar Catálogo</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Administre os itens do catálogo de produtos</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sistema e Usuários -->
            @if(auth()->user()->tenant_id === null)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Sistema e Usuários</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                          <svg class="h-6 w-6 text-purple-600 dark:text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" />
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M4 20c0-4 4-6 8-6s8 2 8 6" />
                          </svg>

                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Usuários</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie usuários e permissões</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.company.settings') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Dados da Empresa</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Informações, logo e configurações da empresa</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.edit-requests.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-orange-600 dark:text-orange-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Solicitações de Edição</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Aprove ou rejeite solicitações de edição de pedidos</p>
                                @if(isset($stats['pending_edit_requests']) && $stats['pending_edit_requests'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ $stats['pending_edit_requests'] }} pendente(s)
                                    </span>
                                @endif
                            </div>
                        </a>

                        @if(auth()->user()->tenant_id === null || auth()->user()->tenant->canAccess('subscription_module'))
                        <a href="{{ route('admin.terms-conditions.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Termos e Condições</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie os termos e condições do sistema</p>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Gerenciamento de Lojas -->
        @if(auth()->user()->tenant_id === null)
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Gerenciamento de Lojas</h2>
                        <a href="{{ route('admin.stores.index') }}" 
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                            Ver todas →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total de Lojas</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_stores'] }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sub-lojas</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_sub_stores'] }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ route('admin.stores.index') }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">Gerenciar Lojas</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Crie e gerencie lojas e sub-lojas do sistema</p>
                            </div>
                        </a>
                    </div>

                    @if($stores->isNotEmpty())
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Lojas Cadastradas</h3>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($stores as $store)
                                @if($store->isMain() || !$store->parent_id)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                            </svg>
                                            <div>
                                                <a href="{{ route('admin.stores.show', $store->id) }}" 
                                                   class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                                    {{ $store->name }}
                                                    @if($store->isMain())
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">(Principal)</span>
                                                    @endif
                                                </a>
                                                @if($store->subStores->isNotEmpty())
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $store->subStores->count() }} sub-lojas
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $store->active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                            {{ $store->active ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </div>
                                    
                                    @if($store->subStores->isNotEmpty())
                                    <div class="mt-2 pl-7 space-y-1">
                                        @foreach($store->subStores as $subStore)
                                        <div class="flex items-center justify-between text-xs">
                                            <a href="{{ route('admin.stores.show', $subStore->id) }}" 
                                               class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center">
                                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $subStore->name }}
                                            </a>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $subStore->active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                                {{ $subStore->active ? 'Ativa' : 'Inativa' }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Atividade Recente -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Pedidos Recentes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pedidos Recentes</h2>
                </div>
                <div class="p-6">
                    @forelse($recent_orders as $order)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->client?->name ?? 'Sem cliente cadastrado' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-900 dark:text-gray-100">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Nenhum pedido recente</p>
                    @endforelse
                </div>
            </div>

            <!-- Usuários Recentes -->
            @if(auth()->user()->tenant_id === null)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Usuários Recentes</h2>
                </div>
                <div class="p-6">
                    @forelse($recent_users as $user)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Nenhum usuário recente</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
