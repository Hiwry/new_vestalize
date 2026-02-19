@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Pedidos do Catálogo</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Gerencie os pedidos recebidos pelo catálogo online</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-indigo-500">
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ (int)($stats->total ?? 0) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Total</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-yellow-600">{{ (int)($stats->pending ?? 0) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Pendentes</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-600">{{ (int)($stats->approved ?? 0) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Aprovados</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-blue-600">R$ {{ number_format($stats->total_revenue ?? 0, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Receita Total</div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, telefone, e-mail ou código..."
               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm flex-1 min-w-[200px]">
        <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
            <option value="">Todos os status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
            <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Convertido</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            Filtrar
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.catalog-orders.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium">
                Limpar
            </a>
        @endif
    </form>
</div>

@if(session('success'))
<div class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
    {{ session('error') }}
</div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Itens</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.catalog-orders.show', $order) }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ $order->order_code }}
                        </a>
                        <div class="text-xs text-gray-400">{{ ucfirst($order->pricing_mode) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer_phone }}</div>
                        @if($order->customer_email)
                            <div class="text-xs text-gray-400">{{ $order->customer_email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $order->total_items }} itens</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $order->formatted_total }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'converted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            ];
                            $paymentStatusColors = [
                                'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                'paid' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'failed' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                                'refunded' => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
                            ];
                            $paymentMethodLabels = [
                                'pix' => 'PIX',
                                'delivery' => 'Na entrega',
                                'cash' => 'Dinheiro',
                                'credit_card' => 'Cartão crédito',
                                'debit_card' => 'Cartão débito',
                                'bank_transfer' => 'Transferência',
                                'link' => 'Link de pagamento',
                                'other' => 'Outro',
                            ];
                            $paymentMethod = $order->payment_method
                                ? ($paymentMethodLabels[$order->payment_method] ?? strtoupper($order->payment_method))
                                : 'N/A';
                        @endphp
                        <div class="flex flex-col gap-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full w-fit {{ $statusColors[$order->status] ?? '' }}">
                                {{ $order->status_label }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full w-fit {{ $paymentStatusColors[$order->payment_status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $order->payment_status_label ?? 'N/A' }}
                            </span>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                {{ $paymentMethod }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.catalog-orders.show', $order) }}"
                               class="px-2 py-1 bg-indigo-600 text-white rounded text-xs font-medium hover:bg-indigo-700">
                                Detalhes
                            </a>
                            {{-- Quick status actions --}}
                            @if($order->status === 'pending')
                                @if(!$requirePaidBeforeOrder || $order->payment_status === 'paid')
                                    <form action="{{ route('admin.catalog-orders.updateStatus', $order) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700" title="Aprovar">
                                            ✓ Aprovar
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 text-xs font-medium">
                                        Aguardando pagamento
                                    </span>
                                @endif
                                <form action="{{ route('admin.catalog-orders.updateStatus', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700" title="Rejeitar"
                                            onclick="return confirm('Tem certeza que deseja rejeitar este pedido?')">
                                        ✗ Rejeitar
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'approved' && (!$requirePaidBeforeOrder || $order->payment_status === 'paid') && !$order->order_id)
                                <form action="{{ route('admin.catalog-orders.convert', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700" title="Converter em Pedido"
                                            onclick="return confirm('Converter este pedido do catálogo em pedido interno?')">
                                        ↗ Converter
                                    </button>
                                </form>
                            @endif

                            @if($order->order_id)
                                <a href="{{ route('orders.show', $order->order_id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs font-medium">
                                    Ver Pedido #{{ $order->order_id }}
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="font-medium">Nenhum pedido do catálogo ainda</p>
                        <p class="text-sm mt-1">Os pedidos feitos pelo catálogo online aparecerão aqui</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
