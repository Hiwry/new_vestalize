@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Etapa 1 de 5</span>
                <span class="text-sm text-gray-500">Dados do Cliente</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: 20%"></div>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold">Edição de Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
                    <p class="text-sm text-gray-600 mt-1">Cliente: {{ $editData['client']['name'] ?? 'N/A' }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('orders.show', $order->id) }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        ← Cancelar
                    </a>
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-6">Dados do Cliente</h2>
            
            <form method="POST" action="{{ route('orders.edit-wizard.client') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                        <input type="text" id="name" name="name" value="{{ $editData['client']['name'] ?? '' }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="phone_primary" class="block text-sm font-medium text-gray-700 mb-2">Telefone Principal *</label>
                        <input type="text" id="phone_primary" name="phone_primary" value="{{ $editData['client']['phone_primary'] ?? '' }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ $editData['client']['email'] ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 mb-2">CPF/CNPJ</label>
                        <input type="text" id="cpf_cnpj" name="cpf_cnpj" value="{{ $editData['client']['cpf_cnpj'] ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
                        <input type="text" id="address" name="address" value="{{ $editData['client']['address'] ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Próximo →
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
