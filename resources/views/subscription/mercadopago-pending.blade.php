@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Pagamento Pendente
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-yellow-100 dark:bg-yellow-900 mb-6">
                    <svg class="h-16 w-16 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                
                <h3 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">Pagamento Pendente</h3>
                <p class="mb-6 text-gray-600 dark:text-gray-400">
                    Seu pagamento está sendo processado. Você receberá um email assim que for confirmado.
                </p>
                
                @if($paymentId)
                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ID da Transação: <span class="font-mono font-semibold">{{ $paymentId }}</span>
                    </p>
                </div>
                @endif
                
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                         Para pagamentos via boleto, pode levar até 2 dias úteis para compensar.
                    </p>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('dashboard') }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Ir para o Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
