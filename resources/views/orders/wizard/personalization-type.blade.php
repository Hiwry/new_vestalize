@extends('layouts.admin')

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Etapa 2 de 6</span>
                <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Tipo de Personalização</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 33%"></div>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Escolha o Tipo de Personalização</h1>
            <p class="text-gray-600 dark:text-gray-400">Selecione o tipo de serviço para este pedido</p>
        </div>

        <!-- Client Info Card -->
        @if(session('wizard.client'))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ session('wizard.client.name') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ session('wizard.client.phone_primary') ?? 'Sem telefone' }}</p>
                </div>
                <a href="{{ route('orders.wizard.start') }}" class="ml-auto text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    Alterar cliente
                </a>
            </div>
        </div>
        @endif

        <!-- Personalization Types Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Sub. Local -->
            <a href="{{ route('orders.wizard.items', ['type' => 'sub_local']) }}" 
               class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Sublimação Local</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sublimação em áreas específicas da peça (gola, manga, etc.)</p>
                </div>
            </a>

            <!-- Serigrafia -->
            <a href="{{ route('orders.wizard.items', ['type' => 'serigrafia']) }}" 
               class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Serigrafia</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Impressão por tela em uma ou mais cores</p>
                </div>
            </a>

            <!-- DTF -->
            <a href="{{ route('orders.wizard.items', ['type' => 'dtf']) }}" 
               class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">DTF</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Direct to Film - Impressão digital com transfer</p>
                </div>
            </a>

            <!-- Bordado -->
            <a href="{{ route('orders.wizard.items', ['type' => 'bordado']) }}" 
               class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-pink-100 dark:bg-pink-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Bordado</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bordado computadorizado ou manual</p>
                </div>
            </a>

            <!-- Emborrachado -->
            <a href="{{ route('orders.wizard.items', ['type' => 'emborrachado']) }}" 
               class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Emborrachado</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aplicação de etiquetas e detalhes emborrachados</p>
                </div>
            </a>

            <!-- Lisas -->
            <a href="{{ route('orders.wizard.items', ['type' => 'lisas']) }}" 
               class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Lisas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Peças sem personalização (apenas costura)</p>
                </div>
            </a>

            <!-- Sub. Total -->
            <a href="{{ route('orders.wizard.items', ['type' => 'sub_total']) }}" 
               class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-gray-200 dark:border-gray-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all cursor-pointer hover:shadow-lg md:col-span-2 lg:col-span-1">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-100 to-indigo-100 dark:from-cyan-900/30 dark:to-indigo-900/30 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Sublimação Total</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sublimação em toda a peça (full print)</p>
                </div>
            </a>

        </div>

        <!-- Back Button -->
        <div class="mt-10 flex justify-start">
            <a href="{{ route('orders.wizard.start') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Voltar
            </a>
        </div>

    </div>
</div>
@endsection
