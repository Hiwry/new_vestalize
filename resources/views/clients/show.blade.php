@extends('layouts.admin')

@section('content')
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $client->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('clients.edit', $client->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition-colors shadow-sm" style="color: white !important;">
                    Editar Cliente
                </a>
                <a href="{{ route('clients.index') }}" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-slate-300 font-bold py-2 px-4 rounded border border-gray-300 dark:border-gray-700 transition-colors">
                    Voltar
                </a>
            </div>
        </div>
            
            <!-- Mensagens -->
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total de Pedidos -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/20 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-md p-3">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total de Pedidos</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Gasto -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/20 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Gasto</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">R$ {{ number_format($stats['total_spent'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Médio -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/20 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900/30 rounded-md p-3">
                                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Ticket Médio</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">R$ {{ number_format($stats['average_order'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saldo Pendente / Crédito do Cliente -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/20 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 {{ $stats['pending_balance'] > 0 ? 'bg-red-100 dark:bg-red-900/30' : ($stats['pending_balance'] < 0 ? 'bg-green-100 dark:bg-green-900/30' : 'bg-gray-100 dark:bg-gray-700/50') }} rounded-md p-3">
                                <svg class="h-6 w-6 {{ $stats['pending_balance'] > 0 ? 'text-red-600 dark:text-red-400' : ($stats['pending_balance'] < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-slate-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-slate-400">
                                    {{ $stats['pending_balance'] < 0 ? 'Crédito do Cliente' : 'Saldo Pendente' }}
                                </p>
                                <p class="text-2xl font-semibold {{ $stats['pending_balance'] > 0 ? 'text-red-600 dark:text-red-400' : ($stats['pending_balance'] < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white') }}">
                                    R$ {{ number_format(abs($stats['pending_balance']), 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Cliente -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/20 sm:rounded-lg border border-gray-200 dark:border-gray-700" style='margin-top: 20px'>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações do Cliente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Nome</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">CPF/CNPJ</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->cpf_cnpj ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Telefone Principal</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->phone_primary }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Telefone Secundário</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->phone_secondary ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->email ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Categoria</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($client->category)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        {{ $client->category }}
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        @if($client->address)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Endereço</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $client->address }}
                                    @if($client->city || $client->state)
                                        <br>{{ $client->city }}{{ $client->state ? ' - ' . $client->state : '' }}
                                        @if($client->zip_code)
                                            - CEP: {{ $client->zip_code }}
                                        @endif
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if($stats['last_order_date'])
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Último Pedido</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stats['last_order_date']->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-slate-400">Cliente desde</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $client->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Pedidos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/20 sm:rounded-lg border border-gray-200 dark:border-gray-700" style='margin-top: 20px'>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Histórico de Pedidos</h3>
                    
                    @if($client->orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Pedido
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Data
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Itens
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Saldo
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($client->orders as $order)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $order->is_cancelled ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium {{ $order->is_cancelled ? 'text-red-600 dark:text-red-400 line-through' : 'text-gray-900 dark:text-white' }}">
                                                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                                </div>
                                                @if($order->is_cancelled)
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        CANCELADO
                                                    </span>
                                                </div>
                                                @elseif($order->delivery_date)
                                                    <div class="text-xs text-gray-500 dark:text-slate-400">
                                                        Entrega: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm {{ $order->is_cancelled ? 'text-gray-500 dark:text-slate-500 opacity-50' : 'text-gray-900 dark:text-white' }}">
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="space-y-1">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full whitespace-nowrap" 
                                                          style="background-color: {{ $order->status->color }}20; color: {{ $order->status->color }}">
                                                        {{ $order->status->name }}
                                                    </span>
                                                    @if($order->has_pending_cancellation)
                                                    <div>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                            ⏳ Pendente
                                                        </span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                                {{ $order->items->sum('quantity') }} peças
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white">
                                                R$ {{ number_format($order->total, 2, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm">
                                                @php
                                                    $paidAmount = $order->payments->sum('amount');
                                                    $balanceDue = $order->total - $paidAmount;
                                                @endphp
                                                <span class="{{ $balanceDue > 0 ? 'text-red-600 dark:text-red-400 font-medium' : 'text-green-600 dark:text-green-400' }}">
                                                    R$ {{ number_format($balanceDue, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('orders.show', $order->id) }}" 
                                                       class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 inline-flex items-center"
                                                       title="Ver Pedido">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('orders.duplicate', $order->id) }}" method="POST" class="inline" 
                                                          onsubmit="return confirm('Deseja duplicar este pedido?')">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 inline-flex items-center"
                                                                title="Duplicar Pedido">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhum pedido encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Este cliente ainda não possui pedidos.</p>
                            <div class="mt-6">
                                <a href="{{ route('orders.wizard.start') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors" style="color: white !important;">
                                    + Novo Pedido
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

