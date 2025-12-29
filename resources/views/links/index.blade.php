@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Links Rápidos</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Acesso rápido às principais funcionalidades do sistema</p>
</div>

<!-- Tabs de Categorias -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex -mb-px space-x-8 px-6 overflow-x-auto" aria-label="Tabs">
            <a href="{{ route('links.index', ['category' => 'geral']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition {{ $category == 'geral' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Geral
            </a>

            @if(Auth::user()->isVendedor() || Auth::user()->isAdmin())
            <a href="{{ route('links.index', ['category' => 'vendedor']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition {{ $category == 'vendedor' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Vendas
            </a>
            @endif

            @if(Auth::user()->isAdmin())
            <a href="{{ route('links.index', ['category' => 'estoque']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition {{ $category == 'estoque' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Estoque
            </a>

            <a href="{{ route('links.index', ['category' => 'caixa']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition {{ $category == 'caixa' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Caixa
            </a>
            @endif

            @if(Auth::user()->isProducao() || Auth::user()->isAdmin())
            <a href="{{ route('links.index', ['category' => 'producao']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition {{ $category == 'producao' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Produção
            </a>
            @endif

            @if(Auth::user()->isAdmin())
            <a href="{{ route('links.index', ['category' => 'admin']) }}" 
               class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition {{ $category == 'admin' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                Administração
            </a>
            @endif
        </nav>
    </div>

    <!-- Conteúdo por Categoria -->
    <div class="p-6">
        @if($category == 'geral')
            <!-- Categoria: Geral -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Novo Pedido -->
                <a href="{{ route('orders.wizard.start') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">Novo Pedido</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Criar um novo pedido para cliente</p>
                    </div>
                </a>

                <!-- Pedidos -->
                <a href="{{ route('orders.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg p-4 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-2">Pedidos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Visualizar e gerenciar todos os pedidos</p>
                    </div>
                </a>

                <!-- Clientes -->
                <a href="{{ route('clients.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-lg p-4 group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors mb-2">Clientes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar base de clientes e contatos</p>
                    </div>
                </a>

                <!-- Kanban -->
                <a href="{{ route('kanban.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900/30 rounded-lg p-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-2">Kanban</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Visualizar pedidos em formato Kanban</p>
                    </div>
                </a>

                <!-- Catálogo -->
                <a href="{{ route('catalog.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-teal-100 dark:bg-teal-900/30 rounded-lg p-4 group-hover:bg-teal-200 dark:group-hover:bg-teal-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors mb-2">Catálogo</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Visualizar catálogo de produtos</p>
                    </div>
                </a>
            </div>

        @elseif($category == 'vendedor')
            <!-- Categoria: Vendas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- PDV -->
                <a href="{{ route('pdv.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-pink-500 dark:hover:border-pink-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-pink-100 dark:bg-pink-900/30 rounded-lg p-4 group-hover:bg-pink-200 dark:group-hover:bg-pink-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors mb-2">PDV</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ponto de Venda</p>
                    </div>
                </a>

                <!-- Vendas -->
                <a href="{{ route('pdv.sales') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900/30 rounded-lg p-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-2">Vendas</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lista de vendas realizadas</p>
                    </div>
                </a>

                <!-- Histórico de Vendas -->
                <a href="{{ route('sales-history.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">Histórico de Vendas</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Consultar histórico completo</p>
                    </div>
                </a>

                <!-- Orçamentos -->
                <a href="{{ route('budget.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-yellow-500 dark:hover:border-yellow-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg p-4 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors mb-2">Orçamentos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Criar e gerenciar orçamentos</p>
                    </div>
                </a>
            </div>

        @elseif($category == 'estoque')
            <!-- Categoria: Estoque -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Estoque -->
                <a href="{{ route('stocks.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">Estoque</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar estoque</p>
                    </div>
                </a>

                <!-- Solicitações -->
                <a href="{{ route('stock-requests.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-orange-500 dark:hover:border-orange-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-orange-100 dark:bg-orange-900/30 rounded-lg p-4 group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-2">Solicitações</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Solicitações de estoque</p>
                    </div>
                </a>

                <!-- Histórico de Estoque -->
                <a href="{{ route('stock-history.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg p-4 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-2">Histórico</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Histórico de movimentações</p>
                    </div>
                </a>
            </div>

        @elseif($category == 'caixa')
            <!-- Categoria: Caixa -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Caixa -->
                <a href="{{ route('cash.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-emerald-500 dark:hover:border-emerald-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg p-4 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors mb-2">Caixa</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar movimentações financeiras</p>
                    </div>
                </a>
            </div>

        @elseif($category == 'producao')
            <!-- Categoria: Produção -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Produção -->
                <a href="{{ route('production.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-orange-500 dark:hover:border-orange-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-orange-100 dark:bg-orange-900/30 rounded-lg p-4 group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-2">Produção</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Acompanhar pedidos em produção</p>
                    </div>
                </a>

                <!-- Dashboard Produção -->
                <a href="{{ route('production.dashboard') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-lg p-4 group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors mb-2">Dashboard</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Análises e métricas de produção</p>
                    </div>
                </a>

                <!-- Kanban -->
                <a href="{{ route('kanban.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900/30 rounded-lg p-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-2">Kanban</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Visualizar pedidos em produção</p>
                    </div>
                </a>
            </div>

        @elseif($category == 'admin')
            <!-- Categoria: Administração -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Dashboard Admin -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-gray-500 dark:hover:border-gray-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded-lg p-4 group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors mb-4">
                            <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-gray-600 dark:group-hover:text-gray-400 transition-colors mb-2">Dashboard Admin</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Painel administrativo</p>
                    </div>
                </a>

                <!-- Usuários -->
                <a href="{{ route('admin.users.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg p-4 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-2">Usuários</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar usuários do sistema</p>
                    </div>
                </a>

                <!-- Opções de Produto -->
                <a href="{{ route('admin.product-options.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">Opções de Produto</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tecidos, cores, cortes</p>
                    </div>
                </a>

                <!-- Preços Personalização -->
                <a href="{{ route('admin.personalization-prices.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-lg p-4 group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors mb-2">Preços Personalização</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Configurar preços</p>
                    </div>
                </a>

                <!-- Categorias Catálogo -->
                <a href="{{ route('admin.catalog-categories.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-teal-100 dark:bg-teal-900/30 rounded-lg p-4 group-hover:bg-teal-200 dark:group-hover:bg-teal-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors mb-2">Categorias Catálogo</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar categorias</p>
                    </div>
                </a>

                <!-- Itens do Catálogo -->
                <a href="{{ route('admin.catalog-items.index') }}" 
                   class="group bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-cyan-500 dark:hover:border-cyan-400">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex-shrink-0 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg p-4 group-hover:bg-cyan-200 dark:group-hover:bg-cyan-900/50 transition-colors mb-4">
                            <svg class="w-8 h-8 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors mb-2">Itens do Catálogo</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gerenciar itens</p>
                    </div>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
