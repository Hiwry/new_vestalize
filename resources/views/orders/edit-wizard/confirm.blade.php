@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">5</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Confirmação da Edição</span>
                        <p class="text-xs text-gray-500">Etapa 5 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600">100%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 100%"></div>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Confirmação da Edição</h1>
                        <p class="text-sm text-gray-600">Revise todas as alterações antes de finalizar</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Resumo das Alterações -->
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Resumo das Alterações</h2>
                    
                    <div class="space-y-4">
                        <!-- Dados do Cliente -->
                        @if(isset($editData['client']))
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-blue-900">Dados do Cliente</h3>
                            </div>
                            <div class="text-sm text-blue-800">
                                <p><strong>Nome:</strong> {{ $editData['client']['name'] ?? $order->client->name }}</p>
                                <p><strong>Telefone:</strong> {{ $editData['client']['phone_primary'] ?? $order->client->phone_primary }}</p>
                                @if(isset($editData['client']['email']))
                                <p><strong>Email:</strong> {{ $editData['client']['email'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Itens do Pedido -->
                        @if(isset($editData['items']))
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-green-900">Itens do Pedido</h3>
                            </div>
                            <div class="text-sm text-green-800">
                                <p><strong>Total de itens:</strong> {{ count($editData['items']) }}</p>
                                <p><strong>Total de peças:</strong> {{ array_sum(array_column($editData['items'], 'quantity')) }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Personalização -->
                        @if(isset($editData['personalization']))
                        <div class="bg-purple-50 border border-purple-200 rounded-md p-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-purple-900">Personalização</h3>
                            </div>
                            <div class="text-sm text-purple-800">
                                <p><strong>Nome da Arte:</strong> {{ $editData['personalization']['art_name'] ?? 'Não informado' }}</p>
                                @if(isset($editData['personalization']['sublimations']))
                                <p><strong>Aplicações:</strong> {{ count($editData['personalization']['sublimations']) }} aplicação(ões)</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Pagamento -->
                        @if(isset($editData['payment']))
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex items-center space-x-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-yellow-900">Pagamento</h3>
                            </div>
                            <div class="text-sm text-yellow-800">
                                <p><strong>Data de Entrega:</strong> {{ isset($editData['payment']['delivery_date']) ? \Carbon\Carbon::parse($editData['payment']['delivery_date'])->format('d/m/Y') : 'Não informado' }}</p>
                                <p><strong>Subtotal:</strong> R$ {{ number_format($editData['payment']['subtotal'] ?? 0, 2, ',', '.') }}</p>
                                <p><strong>Total:</strong> R$ {{ number_format($editData['payment']['total'] ?? 0, 2, ',', '.') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Formulário de Confirmação -->
                <form method="POST" action="{{ route('orders.edit-wizard.confirm') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Motivo da Edição -->
                    <div class="bg-gray-50 rounded-md p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Motivo da Edição</h3>
                        <textarea name="edit_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm"
                                  placeholder="Explique o motivo das alterações realizadas..."></textarea>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <a href="{{ route('orders.edit-wizard.payment') }}" 
                           class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Voltar
                        </a>
                        <button type="submit" 
                                class="flex items-center px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500 transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Finalizar Edição
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection