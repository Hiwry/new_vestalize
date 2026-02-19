@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <a href="{{ route('settings.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Voltar para Configurações
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Configurar Personalizações</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie tipos, produtos e preços de personalização.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Opções de Produto (Base) -->
    <a href="{{ route('admin.product-options.index') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-200">
        <div class="flex items-center space-x-4 mb-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Opções de Produto</h3>
            </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
            Configure as opções base dos produtos, como Tecidos, Cores, Cortes, Tamanhos e Modelos disponíveis.
        </p>
    </a>

    <!-- Preços de Personalização (Geral) -->
    <a href="{{ route('admin.personalization-prices.index') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-200">
        <div class="flex items-center space-x-4 mb-4">
            <div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Tabela de Preços Geral</h3>
            </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
            Defina os preços base para personalizações como Silk, Bordado e Transfer, incluindo faixas por quantidade.
        </p>
    </a>

    <!-- Sublimação Total -->
    @if(Auth::user()->tenant_id === null || Auth::user()->tenant->canAccess('sublimation_total') || Auth::user()->tenant->canAccess('catalog'))
    <a href="{{ route('admin.sublimation-products.index') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-purple-100 dark:border-purple-900/30 hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-md transition-all duration-200">
        <div class="flex items-center space-x-4 mb-4">
            <div class="p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Sublimação Total</h3>
            </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
            Gerencie produtos de Sublimação Total, definindo modelos, preços por tamanho e configurações específicas.
        </p>
    </a>
    @endif

    <!-- Sublimação Local -->
    <a href="{{ route('admin.sub-local-products.index') }}" class="group block p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-blue-100 dark:border-blue-900/30 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all duration-200">
        <div class="flex items-center space-x-4 mb-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Sublimação Local</h3>
            </div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
            Cadastre produtos para Sublimação Local (ex: Canecas, Camisetas Promocionais) com preços fixos ou variados.
        </p>
    </a>

</div>
@endsection
