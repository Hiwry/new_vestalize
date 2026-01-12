@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Editar Venda #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</h1>
        <a href="{{ route('pdv.sales') }}" 
           class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            Voltar
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <form action="{{ route('pdv.sales.update', $sale->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Cliente -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cliente <span class="text-red-500">*</span>
                    </label>
                    <select name="client_id" 
                            required
                            class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all">
                        <option value="">Selecione um cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ $sale->client_id == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} @if($client->phone_primary) - {{ $client->phone_primary }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status (sempre Entregue para vendas do PDV) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <input type="text" 
                           value="Entregue" 
                           disabled
                           class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 bg-gray-100 dark:bg-gray-700/50 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Vendas do PDV sempre ficam com status "Entregue"
                    </p>
                    <input type="hidden" name="status_id" value="{{ $statuses->where('name', 'Entregue')->first()->id ?? $sale->status_id }}">
                </div>

                <!-- Observações -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observações
                    </label>
                    <textarea name="notes" 
                              rows="4"
                              class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all">{{ old('notes', $sale->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informações da Venda (somente leitura) -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações da Venda</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Data/Hora</label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($sale->created_at)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total</label>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                R$ {{ number_format($sale->total, 2, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Itens</label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $sale->total_items ?? 0 }} item(s)
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Vendedor</label>
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $sale->user->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('pdv.sales') }}" 
                   class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

