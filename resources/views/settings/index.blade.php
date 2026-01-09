@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Configurações</h1>
</div>

<!-- Tabs de Categorias -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex -mb-px space-x-8 px-6" aria-label="Tabs">
            @if(Auth::user()->isAdmin())
            <a href="{{ route('settings.index', ['category' => 'admin']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $category == 'admin' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Administração
            </a>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isProducao())
            @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('kanban'))
            <a href="{{ route('settings.index', ['category' => 'producao']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $category == 'producao' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Produção
            </a>
            @endif
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isEstoque())
            @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('stock'))
            <a href="{{ route('settings.index', ['category' => 'estoque']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $category == 'estoque' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Estoque
            </a>
            @endif
            @endif

            @if(Auth::user()->isAdmin())
            @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('financial'))
            <a href="{{ route('settings.index', ['category' => 'caixa']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $category == 'caixa' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Caixa
            </a>
            @endif
            @endif

            @if(Auth::user()->isVendedor() || Auth::user()->isAdmin())
            <a href="{{ route('settings.index', ['category' => 'vendedor']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $category == 'vendedor' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Vendas
            </a>
            @endif
        </nav>
    </div>

    <!-- Conteúdo por Categoria -->
    <div class="p-6">
        @if($category == 'admin')
            <!-- Categoria: Administração -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ Auth::user()->tenant_id ? route('dashboard') : route('admin.dashboard') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Dashboard Principal</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Visão geral do sistema</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.users.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Usuários</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar usuários</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('settings.company') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Dados da Empresa</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Marca, Termos, CNPJ e mais</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('settings.personalizations') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Personalizações</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Preços, produtos e opções</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.catalog-categories.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Categorias Catálogo</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar categorias</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.catalog-items.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Itens do Catálogo</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar itens</p>
                        </div>
                    </div>
                </a>



                @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('pdf_quotes'))
                <a href="{{ route('admin.terms-conditions.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Termos e Condições</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Contratos e cláusulas</p>
                        </div>
                    </div>
                </a>
                @endif

                @if(Auth::user()->tenant)
                <a href="{{ route('subscription.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Minha Assinatura</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ Auth::user()->tenant->currentPlan->name ?? 'Ver plano' }} - 
                                @if(Auth::user()->tenant->subscription_ends_at)
                                    Válida até {{ Auth::user()->tenant->subscription_ends_at->format('d/m/Y') }}
                                @else
                                    Ativar agora
                                @endif
                            </p>
                        </div>
                    </div>
                </a>
                @endif
            </div>

        @elseif($category == 'producao')
            <!-- Categoria: Produção -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('production.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Produção</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar produção</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('production.dashboard') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Dashboard Produção</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Análises e métricas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('kanban.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Kanban</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Visualizar pedidos</p>
                        </div>
                    </div>
                </a>
            </div>

        @elseif($category == 'estoque')
            <!-- Categoria: Estoque -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('stocks.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Estoque</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar estoque</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('stock-requests.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Solicitações</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Solicitações de estoque</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('stock-history.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Histórico</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Histórico de estoque</p>
                        </div>
                    </div>
                </a>
            </div>

        @elseif($category == 'caixa')
            <!-- Categoria: Caixa -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('cash.approvals.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Aprovações</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aprovar pagamentos</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('cash.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Caixa</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar caixa</p>
                        </div>
                    </div>
                </a>
            </div>

        @elseif($category == 'vendedor')
            <!-- Categoria: Vendas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('pdv'))
                <a href="{{ route('pdv.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">PDV</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Ponto de venda</p>
                        </div>
                    </div>
                </a>

                @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('pdv'))
                <a href="{{ route('pdv.sales') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Vendas</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Lista de vendas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('sales-history.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Histórico de Vendas</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Consultar histórico</p>
                        </div>
                    </div>
                </a>
                @endif
                @endif

                @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('pdf_quotes'))
                <a href="{{ route('budget.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Orçamentos</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar orçamentos</p>
                        </div>
                    </div>
                </a>
                @endif

                <a href="{{ route('catalog.index') }}" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Catálogo</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Visualizar catálogo</p>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
