@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Vendas do PDV</h1>
    <a href="{{ route('pdv.index') }}" 
       class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
        Nova Venda
    </a>
</div>

<!-- Estatísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Total de Vendas</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSales }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Faturamento Total</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Vendas Hoje</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $salesToday }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Faturamento Hoje</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">R$ {{ number_format($revenueToday, 2, ',', '.') }}</div>
    </div>
</div>

@if(isset($vendorStats) && $vendorStats)
<!-- Estatísticas do Vendedor Selecionado -->
<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">Estatísticas do Vendedor</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <div class="text-sm text-blue-700 dark:text-blue-300">Total de Vendas</div>
            <div class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ $vendorStats['total_sales'] }}</div>
        </div>
        <div>
            <div class="text-sm text-blue-700 dark:text-blue-300">Faturamento Total</div>
            <div class="text-xl font-bold text-blue-900 dark:text-blue-100">R$ {{ number_format($vendorStats['total_revenue'], 2, ',', '.') }}</div>
        </div>
        <div>
            <div class="text-sm text-blue-700 dark:text-blue-300">Ticket Médio</div>
            <div class="text-xl font-bold text-blue-900 dark:text-blue-100">R$ {{ number_format($vendorStats['avg_ticket'], 2, ',', '.') }}</div>
        </div>
    </div>
</div>
@endif

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
    <form method="GET" action="{{ route('pdv.sales') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Nº da venda, nome do cliente ou telefone..."
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
            </div>

            @if(Auth::user()->isAdmin() && isset($vendors) && $vendors->count() > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendedor</label>
                <select name="vendor_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    <option value="">Todos</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ $vendorId == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    <option value="">Todos</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}" {{ $status == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Forma de Pagamento</label>
                <select name="payment_method" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    <option value="">Todas</option>
                    <option value="dinheiro" {{ $paymentMethod == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                    <option value="pix" {{ $paymentMethod == 'pix' ? 'selected' : '' }}>PIX</option>
                    <option value="cartao" {{ $paymentMethod == 'cartao' ? 'selected' : '' }}>Cartão</option>
                    <option value="transferencia" {{ $paymentMethod == 'transferencia' ? 'selected' : '' }}>Transferência</option>
                    <option value="boleto" {{ $paymentMethod == 'boleto' ? 'selected' : '' }}>Boleto</option>
                </select>
            </div>
        </div>

        <!-- Filtro de Canceladas -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="show_cancelled" 
                       value="1"
                       {{ $showCancelled ? 'checked' : '' }}
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 dark:focus:ring-indigo-600">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mostrar vendas canceladas</span>
            </label>
        </div>

        <!-- Filtro de Data -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Inicial</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}"
                           class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Final</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}"
                           class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                </div>

                <div class="flex items-end">
                    <div class="w-full space-y-2">
                        <button type="submit" class="w-full px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Filtrar
                        </button>
                        <a href="{{ route('pdv.sales') }}" class="block w-full px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-center transition">
                            Limpar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Lista de Vendas -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Venda</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data/Hora</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Forma de Pagamento</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Itens</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($sales as $sale)
                @php
                    $payment = $sale->payments->first();
                    $paymentMethods = [];
                    if ($payment && $payment->payment_methods) {
                        $paymentMethods = is_array($payment->payment_methods) ? $payment->payment_methods : json_decode($payment->payment_methods, true);
                    }
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $sale->is_cancelled ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $sale->client?->name ?? 'Sem cliente cadastrado' }}
                        </div>
                        @if($sale->client?->phone_primary)
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $sale->client->phone_primary }}
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($sale->is_cancelled)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">
                                Cancelada
                            </span>
                            @if($sale->cancellation_reason)
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400" title="{{ $sale->cancellation_reason }}">
                                    {{ \Illuminate\Support\Str::limit($sale->cancellation_reason, 30) }}
                                </div>
                            @endif
                        @elseif($sale->status)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                  style="background-color: {{ $sale->status->color }}20; color: {{ $sale->status->color }}">
                                {{ $sale->status->name }}
                            </span>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">Sem Status</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($sale->created_at)->setTimezone('America/Sao_Paulo')->format('d/m/Y') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($sale->created_at)->setTimezone('America/Sao_Paulo')->format('H:i') }}
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        @if(is_array($paymentMethods) && count($paymentMethods) > 0)
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                @foreach($paymentMethods as $method)
                                    <div class="capitalize">{{ str_replace('_', ' ', $method['method'] ?? 'N/A') }}</div>
                                @endforeach
                            </div>
                        @elseif($payment)
                            <div class="text-sm text-gray-900 dark:text-gray-100 capitalize">
                                {{ str_replace('_', ' ', $payment->payment_method ?? 'N/A') }}
                            </div>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $sale->total_items ?? 0 }}</span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-right">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            R$ {{ number_format($sale->total, 2, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('orders.show', $sale->id) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300"
                               title="Ver Detalhes">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('pdv.sale-receipt', $sale->id) }}" 
                               target="_blank"
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                               title="Nota de Venda">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            @if(Auth::user()->isAdmin() && !$sale->is_cancelled)
                            <a href="{{ route('pdv.sales.edit', $sale->id) }}" 
                               class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300"
                               title="Editar Venda">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button onclick="openCancelModal({{ $sale->id }})" 
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                    title="Cancelar Venda">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        Nenhuma venda encontrada
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
        {{ $sales->links() }}
    </div>
</div>

<!-- Modal de Cancelamento -->
@if(Auth::user()->isAdmin())
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Cancelar Venda</h3>
                <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="cancelForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Justificativa do Cancelamento <span class="text-red-500">*</span>
                    </label>
                    <textarea name="cancellation_reason" 
                              rows="4"
                              required
                              placeholder="Informe o motivo do cancelamento..."
                              class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-red-500 dark:focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:focus:ring-red-500 transition-all"></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Esta justificativa será registrada para controle e auditoria.
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeCancelModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Confirmar Cancelamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal(saleId) {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    form.action = '{{ url("/pdv/vendas") }}/' + saleId + '/cancelar';
    modal.classList.remove('hidden');
}

function closeCancelModal() {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    form.reset();
    modal.classList.add('hidden');
}

// Fechar modal ao clicar fora
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endif

@if(session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif

@if(session('error'))
<script>
    alert('{{ session('error') }}');
</script>
@endif
@endsection

