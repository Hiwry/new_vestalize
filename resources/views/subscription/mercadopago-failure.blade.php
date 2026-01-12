@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Pagamento Não Realizado
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 dark:bg-red-900 mb-6">
                    <svg class="h-16 w-16 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                
                <h3 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">Pagamento Não Realizado</h3>
                <p class="mb-6 text-gray-600 dark:text-gray-400">
                    Houve um problema ao processar seu pagamento. Por favor, tente novamente.
                </p>
                
                <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded text-left">
                    <p class="text-sm text-red-700 dark:text-red-300 mb-2 font-semibold">
                        Possíveis causas:
                    </p>
                    <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-1">
                        <li>Dados de pagamento incorretos</li>
                        <li>Saldo insuficiente</li>
                        <li>Cartão bloqueado ou vencido</li>
                        <li>Cancelamento durante o processo</li>
                    </ul>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('subscription.index') }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Tentar Novamente
                    </a>
                    
                    <a href="{{ route('dashboard') }}" class="block w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-3 px-6 rounded-lg transition-colors">
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
