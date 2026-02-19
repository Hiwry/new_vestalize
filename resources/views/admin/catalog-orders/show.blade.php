@extends('layouts.admin')

@section('content')
@php
    $paymentMethodLabels = [
        'pix' => 'PIX',
        'delivery' => 'Na entrega',
        'cash' => 'Dinheiro',
        'credit_card' => 'Cartão de crédito',
        'debit_card' => 'Cartão de débito',
        'bank_transfer' => 'Transferência',
        'link' => 'Link de pagamento',
        'other' => 'Outro',
    ];

    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        'converted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];

    $paymentStatusColors = [
        'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
        'paid' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
        'failed' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
        'refunded' => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300',
    ];

    $currentPaymentMethod = $catalogOrder->payment_method
        ? ($paymentMethodLabels[$catalogOrder->payment_method] ?? strtoupper($catalogOrder->payment_method))
        : 'N/A';

    $stockSeparationColors = [
        'slate' => 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200',
        'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'emerald' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
        'rose' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
    ];

    $stockSeparationTone = $stockSeparation['tone'] ?? 'slate';
@endphp

<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div>
        <a href="{{ route('admin.catalog-orders.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
            ← Voltar para pedidos
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            Pedido {{ $catalogOrder->order_code }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Criado em {{ $catalogOrder->created_at->format('d/m/Y H:i') }}
            @if($catalogOrder->store)
                • Loja: {{ $catalogOrder->store->name }}
            @endif
        </p>
    </div>

    <div class="flex items-center gap-2">
        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$catalogOrder->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
            {{ $catalogOrder->status_label }}
        </span>
        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $paymentStatusColors[$catalogOrder->payment_status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
            {{ $catalogOrder->payment_status_label ?? 'N/A' }}
        </span>
    </div>
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

@if($errors->any())
    <div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <ul class="list-disc ml-5 space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Cliente</h2>
                <div class="space-y-2 text-sm">
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">Nome:</span> {{ $catalogOrder->customer_name }}</div>
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">Telefone:</span> {{ $catalogOrder->customer_phone }}</div>
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">E-mail:</span> {{ $catalogOrder->customer_email ?: 'N/A' }}</div>
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">CPF:</span> {{ $catalogOrder->customer_cpf ?: 'N/A' }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Pagamento Atual</h2>
                <div class="space-y-2 text-sm">
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">Forma:</span> {{ $currentPaymentMethod }}</div>
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">Situação:</span> {{ $catalogOrder->payment_status_label ?? 'N/A' }}</div>
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">Gateway ID:</span> {{ $catalogOrder->payment_gateway_id ?: 'N/A' }}</div>
                    <div><span class="font-semibold text-gray-900 dark:text-gray-100">Pago em:</span> {{ $catalogOrder->payment_data['paid_at'] ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:col-span-2">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Endereço de Entrega</h2>
                @if($catalogOrder->delivery_address)
                    <div class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed">{!! nl2br(e($catalogOrder->delivery_address)) !!}</div>
                @else
                    <div class="text-sm text-gray-500 dark:text-gray-400">Não informado pelo cliente.</div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:col-span-2">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Observações</h2>
                <div class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed whitespace-pre-line">
                    {{ $catalogOrder->notes ?: 'Sem observações do cliente.' }}
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Itens do Pedido</h2>
                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                    {{ $catalogOrder->total_items }} itens • {{ $catalogOrder->formatted_total }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Produto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tam.</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qtd</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit.</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach(($catalogOrder->items ?? []) as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item['title'] ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item['color'] ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item['size'] ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-right">{{ (int) ($item['quantity'] ?? 0) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 text-right">R$ {{ number_format((float) ($item['unit_price'] ?? 0), 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100 text-right">R$ {{ number_format((float) ($item['total'] ?? 0), 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Configurar Pagamento</h2>
            <form action="{{ route('admin.catalog-orders.updatePayment', $catalogOrder) }}" method="POST" class="space-y-4">
                @csrf
                @php
                    $selectedPaymentMethod = old('payment_method', $catalogOrder->payment_method ?: 'delivery');
                    $hasCustomPaymentMethod = $selectedPaymentMethod && !array_key_exists($selectedPaymentMethod, $paymentMethodLabels);
                @endphp
                <div>
                    <label for="payment_method" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Forma de pagamento</label>
                    <select id="payment_method" name="payment_method" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">
                        @if($hasCustomPaymentMethod)
                            <option value="{{ $selectedPaymentMethod }}" selected>{{ strtoupper($selectedPaymentMethod) }} (atual)</option>
                        @endif
                        @foreach($paymentMethodLabels as $value => $label)
                            <option value="{{ $value }}" {{ $selectedPaymentMethod === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="payment_status" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Situação do pagamento</label>
                    <select id="payment_status" name="payment_status" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">
                        <option value="pending" {{ old('payment_status', $catalogOrder->payment_status ?: 'pending') === 'pending' ? 'selected' : '' }}>Aguardando pagamento</option>
                        <option value="paid" {{ old('payment_status', $catalogOrder->payment_status) === 'paid' ? 'selected' : '' }}>Pago</option>
                        <option value="failed" {{ old('payment_status', $catalogOrder->payment_status) === 'failed' ? 'selected' : '' }}>Falhou</option>
                        <option value="refunded" {{ old('payment_status', $catalogOrder->payment_status) === 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                    </select>
                </div>

                <div>
                    <label for="payment_gateway_id" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">ID pagamento (gateway)</label>
                    <input id="payment_gateway_id" name="payment_gateway_id" type="text"
                           value="{{ old('payment_gateway_id', $catalogOrder->payment_gateway_id) }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100"
                           placeholder="Ex.: MP-123456789">
                </div>

                <div>
                    <label for="payment_notes" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Observação do pagamento</label>
                    <textarea id="payment_notes" name="payment_notes" rows="3"
                              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100"
                              placeholder="Ex.: Confirmado no caixa da loja">{{ old('payment_notes', $catalogOrder->payment_data['admin_payment_notes'] ?? '') }}</textarea>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold">
                    Salvar pagamento
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Separação do Estoque</h2>

            <div class="flex items-center justify-between gap-3">
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $stockSeparationColors[$stockSeparationTone] ?? $stockSeparationColors['slate'] }}">
                    {{ $stockSeparation['label'] ?? 'N/A' }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ (int) ($stockSeparation['total_requests'] ?? 0) }} solicitações
                </span>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                {{ $stockSeparation['description'] ?? 'Sem dados de separação.' }}
            </p>

            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div class="rounded-md bg-gray-50 dark:bg-gray-700/50 px-3 py-2">
                    <span class="text-gray-500 dark:text-gray-400">Pendentes</span>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ (int) ($stockSeparation['counts']['pendente'] ?? 0) }}</div>
                </div>
                <div class="rounded-md bg-gray-50 dark:bg-gray-700/50 px-3 py-2">
                    <span class="text-gray-500 dark:text-gray-400">Aprovadas</span>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ (int) (($stockSeparation['counts']['aprovado'] ?? 0) + ($stockSeparation['counts']['em_transferencia'] ?? 0)) }}</div>
                </div>
                <div class="rounded-md bg-gray-50 dark:bg-gray-700/50 px-3 py-2">
                    <span class="text-gray-500 dark:text-gray-400">Concluídas</span>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ (int) ($stockSeparation['counts']['concluido'] ?? 0) }}</div>
                </div>
                <div class="rounded-md bg-gray-50 dark:bg-gray-700/50 px-3 py-2">
                    <span class="text-gray-500 dark:text-gray-400">Rejeitadas</span>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 mt-1">{{ (int) ($stockSeparation['counts']['rejeitado'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Status do Pedido</h2>
            <form action="{{ route('admin.catalog-orders.updateStatus', $catalogOrder) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">
                        <option value="pending" {{ old('status', $catalogOrder->status) === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="approved"
                                {{ old('status', $catalogOrder->status) === 'approved' ? 'selected' : '' }}
                                {{ ($requirePaidBeforeOrder ?? true) && $catalogOrder->payment_status !== 'paid' ? 'disabled' : '' }}>
                            Aprovado
                        </option>
                        <option value="rejected" {{ old('status', $catalogOrder->status) === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="cancelled" {{ old('status', $catalogOrder->status) === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @if(($requirePaidBeforeOrder ?? true) && $catalogOrder->payment_status !== 'paid')
                        <p class="mt-2 text-xs text-amber-600 dark:text-amber-300">
                            O pedido só pode ser aprovado após pagamento confirmado.
                        </p>
                    @endif
                </div>
                <div>
                    <label for="admin_notes" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Observação interna</label>
                    <textarea id="admin_notes" name="admin_notes" rows="3"
                              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100"
                              placeholder="Observações para equipe">{{ old('admin_notes', $catalogOrder->admin_notes) }}</textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800 text-sm font-semibold">
                    Salvar status
                </button>
            </form>

            @if($catalogOrder->status === 'approved' && (!($requirePaidBeforeOrder ?? true) || $catalogOrder->payment_status === 'paid') && !$catalogOrder->order_id)
                <form action="{{ route('admin.catalog-orders.convert', $catalogOrder) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold"
                            onclick="return confirm('Converter este pedido em pedido interno?')">
                        Converter em Pedido Interno
                    </button>
                </form>
            @endif

            @if($catalogOrder->order_id)
                <a href="{{ route('orders.show', $catalogOrder->order_id) }}"
                   class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-lg text-sm font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/50">
                    Ver Pedido Interno #{{ $catalogOrder->order_id }}
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
