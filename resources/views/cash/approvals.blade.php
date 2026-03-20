@extends('layouts.admin')

@section('content')
<style>
    .approvals-ft .approvals-scroll-top-wrap {
        padding: 10px 12px 0;
        background: inherit;
        border-bottom: 1px solid rgba(107,114,128,0.15);
    }
    .approvals-ft .approvals-scroll-hint {
        display: flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 700;
        color: #9ca3af; margin-bottom: 8px; letter-spacing: .01em;
    }
    .approvals-ft .approvals-scroll-top {
        overflow-x: auto; overflow-y: hidden;
        height: 20px; border-radius: 999px;
        background: rgba(107,114,128,0.15);
        scrollbar-width: auto;
        scrollbar-color: #7c3aed rgba(107,114,128,0.15);
    }
    .approvals-ft .approvals-scroll-top-track { height: 1px; min-width: 100%; }
    .approvals-ft .approvals-scroll-top::-webkit-scrollbar { height: 18px; }
    .approvals-ft .approvals-scroll-top::-webkit-scrollbar-track {
        background: rgba(107,114,128,0.15); border-radius: 999px;
    }
    .approvals-ft .approvals-scroll-top::-webkit-scrollbar-thumb {
        background: #7c3aed; border-radius: 999px;
    }
    .approvals-ft .table-scroll-wrapper {
        overflow-x: auto; overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }
    .approvals-ft .table-scroll-wrapper::-webkit-scrollbar { height: 8px; }
    .approvals-ft .table-scroll-wrapper::-webkit-scrollbar-track { background: rgba(107,114,128,0.1); }
    .approvals-ft .table-scroll-wrapper::-webkit-scrollbar-thumb { background: #7c3aed; border-radius: 999px; }
    .approvals-table { width: 100%; min-width: 860px; border-collapse: collapse; }
    .step-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 7px; border-radius: 9999px;
        font-size: 10px; font-weight: 700; white-space: nowrap; letter-spacing: .02em;
    }
    .approve-btn {
        display: inline-block; padding: 3px 10px;
        border-radius: 6px; font-size: 11px; font-weight: 600;
        white-space: nowrap; cursor: pointer; border: none; transition: opacity .15s;
    }
    .approve-btn:hover { opacity: .85; }
    .approve-btn-entry   { background: #4f46e5; color: #fff; }
    .approve-btn-remaining { background: #16a34a; color: #fff; }
    .approve-btn-single  { background: #16a34a; color: #fff; }
</style>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Aprovacoes de Caixa</h1>
</div>

<!-- Estatisticas -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Pendentes de Aprovacao</div>
        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pendentes'] }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Aprovados</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['aprovados'] }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Total Pendente</div>
        <div class="text-xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($stats['total_pendente'], 2, ',', '.') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Acoes em Lote</div>
        <button id="select-all-btn" class="mt-1 px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
            Selecionar Todos
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4 mb-6">
    <form method="GET" action="{{ route('cash.approvals.index') }}">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                    <option value="pendente" {{ $status == 'pendente' ? 'selected' : '' }}>Pendentes</option>
                    <option value="aprovado" {{ $status == 'aprovado' ? 'selected' : '' }}>Aprovados</option>
                    <option value="todos" {{ $status == 'todos' ? 'selected' : '' }}>Todos</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                <select name="type" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                    <option value="todos" {{ $type == 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="pedidos" {{ $type == 'pedidos' ? 'selected' : '' }}>Pedidos</option>
                    <option value="vendas" {{ $type == 'vendas' ? 'selected' : '' }}>Vendas</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="No pedido, cliente..."
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm font-medium">
                    Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Tabela -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 approvals-ft">

    <!-- Barra de scroll superior -->
    <div class="approvals-scroll-top-wrap" id="approvalsScrollTopWrap">
        <div class="approvals-scroll-hint">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 3a1 1 0 01.707.293l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586L9.293 4.707A1 1 0 0110 3z"/>
            </svg>
            Role para ver mais
        </div>
        <div class="approvals-scroll-top" id="approvalsScrollTopBar">
            <div class="approvals-scroll-top-track" id="approvalsScrollTopTrack"></div>
        </div>
    </div>

    <div class="table-scroll-wrapper" id="approvalsTableWrapper">
        <table class="approvals-table divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-8">
                        <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedido</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vendedor</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pagamento</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Metodo</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Comprovante</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aprovacao</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acoes</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($orders as $order)
                @php
                    $payment        = $order->payment ?? null;
                    $isPedido       = !$order->is_pdv;
                    $hasSplit       = $isPedido && ($payment?->remaining_amount ?? 0) > 0;
                    $entryApproved  = (bool)($payment?->entry_approved);
                    $remainApproved = (bool)($payment?->remaining_approved);
                    $fullyApproved  = (bool)($payment?->cash_approved);
                    $methods = is_array($payment?->payment_methods) ? $payment->payment_methods : [];
                    $methodDates = collect($methods)->pluck('date')->filter()
                        ->map(fn($d) => \Carbon\Carbon::tryParse($d))->filter()
                        ->sortBy(fn($d) => $d->timestamp)->values();
                    $paidDate    = $methodDates->last() ?? $payment?->payment_date ?? $payment?->entry_date;
                    $remainDate  = $payment?->due_date ?? $payment?->entry_date ?? $payment?->payment_date;
                    $receiptList = $payment?->receipt_attachments_list ?? [];
                    if ($fullyApproved)                  $rowBg = 'bg-green-50 dark:bg-green-900/10';
                    elseif ($hasSplit && $entryApproved) $rowBg = 'bg-amber-50 dark:bg-amber-900/10';
                    else                                 $rowBg = '';
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors {{ $rowBg }}">

                    <td class="px-3 py-3">
                        @if($payment && !$fullyApproved)
                            <input type="checkbox" class="order-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="{{ $order->id }}">
                        @endif
                    </td>

                    <td class="px-3 py-3 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="mt-0.5">
                            @if($order->is_pdv)
                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">Venda</span>
                            @else
                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">Pedido</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-3 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-100" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $order->client?->name ?? 'Sem cliente' }}
                        </div>
                    </td>

                    <td class="px-3 py-3">
                        <div class="text-sm text-gray-700 dark:text-gray-300" style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $order->user?->name ?? '-' }}
                        </div>
                    </td>

                    <td class="px-3 py-3 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
                    </td>

                    <td class="px-3 py-3">
                        <div class="whitespace-nowrap">
                            <span class="text-sm text-gray-900 dark:text-gray-100 font-medium">R$ {{ number_format($payment?->entry_amount ?? 0, 2, ',', '.') }}</span>
                            @if($paidDate)
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ $paidDate->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        @if($hasSplit)
                            <div class="whitespace-nowrap mt-0.5">
                                <span class="text-xs font-medium text-red-500 dark:text-red-400">Restante: R$ {{ number_format($payment->remaining_amount, 2, ',', '.') }}</span>
                                @if($remainDate)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ $remainDate->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        @endif
                    </td>

                    <td class="px-3 py-3 whitespace-nowrap">
                        @if($payment)
                            @if(count($methods) > 1)
                                <span class="text-xs text-gray-600 dark:text-gray-400">Multiplos</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ ucfirst($payment->payment_method ?? $payment->method ?? 'N/A') }}
                                </span>
                            @endif
                        @endif
                    </td>

                    <td class="px-3 py-3">
                        <div class="flex flex-col gap-0.5">
                            @if(count($receiptList) > 0)
                                @foreach($receiptList as $idx => $receipt)
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('cash.approvals.view-receipt', $order->id) }}?receipt={{ $idx }}" target="_blank"
                                           class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
                                           style="max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                           title="{{ $receipt['name'] ?? '' }}">
                                            {{ $receipt['name'] ?? 'Comprovante '.($idx+1) }}
                                        </a>
                                        <button onclick="removeReceipt({{ $order->id }}, {{ $idx }})" class="text-red-500 hover:text-red-700 text-xs leading-none flex-shrink-0" title="Remover">x</button>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400">Nenhum</span>
                            @endif
                            <label class="cursor-pointer mt-0.5">
                                <input type="file" class="hidden receipt-input" data-order-id="{{ $order->id }}" accept="image/*,application/pdf" multiple>
                                <span class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ count($receiptList) > 0 ? '+ Anexar' : 'Anexar' }}
                                </span>
                            </label>
                        </div>
                    </td>

                    <td class="px-3 py-3">
                        @if($hasSplit)
                            <div class="flex items-center gap-1 mb-1.5">
                                <span class="text-xs text-gray-400 w-14 shrink-0">Entrada:</span>
                                @if($entryApproved)
                                    <span class="step-badge bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">&#10003; Aprovada</span>
                                    @if($payment->entryApprovedBy)
                                        <span class="text-xs text-gray-400 ml-1">{{ $payment->entry_approved_at?->format('d/m H:i') }}</span>
                                    @endif
                                @else
                                    <span class="step-badge bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">Pendente</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-xs text-gray-400 w-14 shrink-0">Restante:</span>
                                @if($remainApproved)
                                    <span class="step-badge bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">&#10003; Aprovado</span>
                                    @if($payment->remainingApprovedBy)
                                        <span class="text-xs text-gray-400 ml-1">{{ $payment->remaining_approved_at?->format('d/m H:i') }}</span>
                                    @endif
                                @elseif(!$entryApproved)
                                    <span class="step-badge bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Aguardando</span>
                                @else
                                    <span class="step-badge bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">Pendente</span>
                                @endif
                            </div>
                        @elseif($fullyApproved)
                            <span class="step-badge bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">&#10003; Aprovado</span>
                            @if($payment?->approvedBy)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $payment->approvedBy->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->approved_at?->format('d/m/Y H:i') }}</div>
                            @endif
                        @else
                            <span class="step-badge bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">Pendente</span>
                        @endif
                    </td>

                    <td class="px-3 py-3 whitespace-nowrap">
                        <div class="flex flex-col gap-1 items-start">
                            @if($hasSplit)
                                @if(!$entryApproved)
                                    <button onclick="approveEntry({{ $order->id }})" class="approve-btn approve-btn-entry">
                                        Aprovar Entrada
                                    </button>
                                @endif
                                @if($entryApproved && !$remainApproved)
                                    <button onclick="approveRemaining({{ $order->id }})" class="approve-btn approve-btn-remaining">
                                        Aprovar Restante
                                    </button>
                                @endif
                            @elseif($payment && !$fullyApproved)
                                <button onclick="approveOrder({{ $order->id }})" class="approve-btn approve-btn-single">
                                    Aprovar
                                </button>
                            @endif
                            <a href="{{ route('orders.show', $order->id) }}"
                               class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                Ver detalhes
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Nenhum pedido/venda encontrado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $orders->links() }}
    </div>
</div>

@if($status == 'pendente' || $status == 'todos')
<div class="mt-4 flex justify-end">
    <button onclick="approveSelected()" id="approve-selected-btn"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
        Aprovar Selecionados
    </button>
</div>
@endif

<!-- Modal de Confirmacao -->
<div id="confirm-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-4">
                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2" id="modal-title">Confirmar Acao</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400" id="modal-message">Deseja realmente realizar esta acao?</p>
            </div>
            <div class="flex items-center px-4 py-3 space-x-3">
                <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600">
                    Cancelar
                </button>
                <button type="button" onclick="confirmAction()"
                        class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resultado -->
<div id="result-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div id="result-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4"></div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2" id="result-title">Resultado</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400" id="result-message">Operacao realizada com sucesso!</p>
            </div>
            <div class="flex items-center px-4 py-3">
                <button onclick="closeResultModal()"
                        class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var topBar  = document.getElementById('approvalsScrollTopBar');
    var wrapper = document.getElementById('approvalsTableWrapper');
    var track   = document.getElementById('approvalsScrollTopTrack');
    function syncWidth() { if (wrapper && track) track.style.width = wrapper.scrollWidth + 'px'; }
    syncWidth();
    if (typeof ResizeObserver !== 'undefined') new ResizeObserver(syncWidth).observe(wrapper);
    if (topBar && wrapper) {
        topBar.addEventListener('scroll', function() { wrapper.scrollLeft = topBar.scrollLeft; });
        wrapper.addEventListener('scroll', function() { topBar.scrollLeft = wrapper.scrollLeft; });
    }
})();

document.getElementById('select-all-checkbox')?.addEventListener('change', function () {
    document.querySelectorAll('.order-checkbox').forEach(function(cb) { cb.checked = this.checked; }, this);
    updateApproveButton();
});
document.getElementById('select-all-btn')?.addEventListener('click', function () {
    var all = document.querySelectorAll('.order-checkbox');
    var anyUnchecked = Array.from(all).some(function(cb) { return !cb.checked; });
    all.forEach(function(cb) { cb.checked = anyUnchecked; });
    updateApproveButton();
});
function updateApproveButton() {
    var btn = document.getElementById('approve-selected-btn');
    if (btn) btn.disabled = !document.querySelectorAll('.order-checkbox:checked').length;
}
document.querySelectorAll('.order-checkbox').forEach(function(cb) { cb.addEventListener('change', updateApproveButton); });

window.currentAction = window.currentOrderId = window.currentOrderIds = window.currentReceiptIndex = null;

window.showModal = function (title, message, action, orderId, orderIds, receiptIndex) {
    document.getElementById('modal-title').textContent   = title;
    document.getElementById('modal-message').textContent = message;
    window.currentAction       = action       || null;
    window.currentOrderId      = orderId      || null;
    window.currentOrderIds     = orderIds     || null;
    window.currentReceiptIndex = receiptIndex != null ? receiptIndex : null;
    document.getElementById('confirm-modal').classList.remove('hidden');
};
window.closeModal = function () {
    document.getElementById('confirm-modal').classList.add('hidden');
    window.currentAction = window.currentOrderId = window.currentOrderIds = window.currentReceiptIndex = null;
};
window.confirmAction = function () {
    var action = window.currentAction, orderId = window.currentOrderId,
        orderIds = window.currentOrderIds, receiptIndex = window.currentReceiptIndex;
    window.closeModal();
    if      (action === 'approve')           approveOrderRequest(orderId);
    else if (action === 'approve-entry')     approveEntryRequest(orderId);
    else if (action === 'approve-remaining') approveRemainingRequest(orderId);
    else if (action === 'approve-multiple')  approveMultipleRequest(orderIds);
    else if (action === 'remove-receipt')    removeReceiptRequest(orderId, receiptIndex);
};

function showResultModal(type, title, message) {
    var icon = document.getElementById('result-icon');
    icon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4';
    if (type === 'success') {
        icon.classList.add('bg-green-100');
        icon.innerHTML = '<svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
    } else {
        icon.classList.add('bg-red-100');
        icon.innerHTML = '<svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
    }
    document.getElementById('result-title').textContent   = title;
    document.getElementById('result-message').textContent = message;
    document.getElementById('result-modal').classList.remove('hidden');
}
function closeResultModal() {
    document.getElementById('result-modal').classList.add('hidden');
    location.reload();
}

function csrf() { return (document.querySelector('meta[name="csrf-token"]') || {}).content; }
function doPost(url, body) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: body ? JSON.stringify(body) : undefined,
    }).then(function(r) { return r.json(); });
}

function approveOrder(orderId) {
    showModal('Aprovar Pagamento', 'Confirmar aprovacao deste pagamento?', 'approve', orderId);
}
function approveEntry(orderId) {
    showModal('Aprovar Entrada', 'Confirmar aprovacao da entrada deste pedido?', 'approve-entry', orderId);
}
function approveRemaining(orderId) {
    showModal('Aprovar Restante', 'Confirmar aprovacao do restante? O pedido sera marcado como totalmente quitado.', 'approve-remaining', orderId);
}
function approveSelected() {
    var ids = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(function(cb) { return cb.value; });
    if (!ids.length) return showResultModal('error', 'Atencao', 'Selecione pelo menos um pedido');
    showModal('Aprovar Multiplos Pagamentos', 'Deseja aprovar ' + ids.length + ' pagamento(s)?', 'approve-multiple', null, ids);
}

function approveOrderRequest(orderId) {
    doPost('/cash/approvals/' + orderId + '/approve')
        .then(function(d) { d.success ? showResultModal('success', 'Sucesso!', d.message) : showResultModal('error', 'Erro', d.error || 'Erro ao aprovar'); })
        .catch(function() { showResultModal('error', 'Erro', 'Erro ao aprovar pagamento'); });
}
function approveEntryRequest(orderId) {
    doPost('/cash/approvals/' + orderId + '/approve-entry')
        .then(function(d) { d.success ? showResultModal('success', 'Entrada Aprovada!', d.message) : showResultModal('error', 'Erro', d.error || 'Erro ao aprovar entrada'); })
        .catch(function() { showResultModal('error', 'Erro', 'Erro ao aprovar entrada'); });
}
function approveRemainingRequest(orderId) {
    doPost('/cash/approvals/' + orderId + '/approve-remaining')
        .then(function(d) { d.success ? showResultModal('success', 'Restante Aprovado!', d.message) : showResultModal('error', 'Erro', d.error || 'Erro ao aprovar restante'); })
        .catch(function() { showResultModal('error', 'Erro', 'Erro ao aprovar restante'); });
}
function approveMultipleRequest(orderIds) {
    doPost('/cash/approvals/approve-multiple', { order_ids: orderIds })
        .then(function(d) { showResultModal(d.success ? 'success' : 'error', d.success ? 'Sucesso!' : 'Erro', d.message); })
        .catch(function() { showResultModal('error', 'Erro', 'Erro ao aprovar pagamentos'); });
}
function removeReceiptRequest(orderId, receiptIndex) {
    doPost('/cash/approvals/' + orderId + '/remove-receipt', { receipt_index: receiptIndex })
        .then(function(d) { d.success ? showResultModal('success', 'Sucesso!', d.message) : showResultModal('error', 'Erro', d.error || 'Erro ao remover'); })
        .catch(function() { showResultModal('error', 'Erro', 'Erro ao remover comprovante'); });
}
function removeReceipt(orderId, receiptIndex) {
    showModal('Remover Comprovante', 'Deseja remover este comprovante?', 'remove-receipt', orderId, null, receiptIndex);
}

document.querySelectorAll('.receipt-input').forEach(function(input) {
    input.addEventListener('change', function () {
        if (!this.files.length) return;
        var orderId = this.dataset.orderId;
        var fd = new FormData();
        Array.from(this.files).forEach(function(f) { fd.append('receipts[]', f); });
        fetch('/cash/approvals/' + orderId + '/attach-receipt', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            body: fd,
        }).then(function(r) { return r.json(); })
          .then(function(d) { d.success ? showResultModal('success', 'Sucesso!', d.message) : showResultModal('error', 'Erro', d.error || 'Erro ao anexar'); })
          .catch(function() { showResultModal('error', 'Erro', 'Erro ao anexar comprovante'); });
        this.value = '';
    });
});

window.approveOrder     = approveOrder;
window.approveEntry     = approveEntry;
window.approveRemaining = approveRemaining;
window.approveSelected  = approveSelected;
window.removeReceipt    = removeReceipt;
window.closeResultModal = closeResultModal;
</script>
@endsection
