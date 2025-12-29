@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Pagamento Aprovado
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 dark:bg-green-900 mb-6">
                    <svg class="h-16 w-16 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                
                <h3 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">Pagamento Aprovado!</h3>
                <p class="mb-6 text-gray-600 dark:text-gray-400">
                    Sua assinatura foi ativada com sucesso. Você já pode aproveitar todos os recursos do seu plano.
                </p>
                
                @if($paymentId)
                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ID da Transação: <span class="font-mono font-semibold">{{ $paymentId }}</span>
                    </p>
                </div>
                @endif
                
                <div class="space-y-3">
                    <a href="{{ route('dashboard') }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Ir para o Dashboard
                    </a>
                    
                    <a href="{{ route('subscription.index') }}" class="block w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-3 px-6 rounded-lg transition-colors">
                        Ver Minha Assinatura
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
