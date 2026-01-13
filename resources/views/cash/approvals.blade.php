@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Aprovações de Caixa</h1>
</div>

<!-- Estatísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Pendentes de Aprovação</div>
        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pendentes'] }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Aprovados</div>
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['aprovados'] }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Total Pendente</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($stats['total_pendente'], 2, ',', '.') }}</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">Ações em Lote</div>
        <button id="select-all-btn" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
            Selecionar Todos
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
    <form method="GET" action="{{ route('cash.approvals.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="pendente" {{ $status == 'pendente' ? 'selected' : '' }}>Pendentes</option>
                    <option value="aprovado" {{ $status == 'aprovado' ? 'selected' : '' }}>Aprovados</option>
                    <option value="todos" {{ $status == 'todos' ? 'selected' : '' }}>Todos</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                <select name="type" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    <option value="todos" {{ $type == 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="pedidos" {{ $type == 'pedidos' ? 'selected' : '' }}>Pedidos</option>
                    <option value="vendas" {{ $type == 'vendas' ? 'selected' : '' }}>Vendas</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Nº pedido, cliente..."
                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Tabela -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pago</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Comprovante</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($orders as $order)
                @php
                    $payment = $order->payment ?? null;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $payment && $payment->cash_approved ? 'bg-green-50 dark:bg-green-900/10' : '' }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($payment && !$payment->cash_approved)
                        <input type="checkbox" 
                               class="order-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                               value="{{ $order->id }}">
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($order->is_pdv)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">
                                Venda
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                Pedido
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $order->client?->name ?? 'Sem cliente' }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            R$ {{ number_format($order->total, 2, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            R$ {{ number_format($payment->entry_amount ?? 0, 2, ',', '.') }}
                        </div>
                        @if($payment && $payment->remaining_amount > 0)
                        <div class="text-xs text-red-600 dark:text-red-400">
                            Restante: R$ {{ number_format($payment->remaining_amount, 2, ',', '.') }}
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($payment)
                            @php
                                $methods = is_array($payment->payment_methods) ? $payment->payment_methods : [];
                            @endphp
                            @if(count($methods) > 1)
                                <div class="text-xs text-gray-600 dark:text-gray-400">Múltiplos</div>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ ucfirst($payment->payment_method ?? $payment->method ?? 'N/A') }}
                                </span>
                            @endif
                        @else
                            <span class="text-xs text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($payment && $payment->receipt_attachment)
                            <a href="{{ route('cash.approvals.view-receipt', $order->id) }}" 
                               target="_blank"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                                Ver Comprovante
                            </a>
                            <button onclick="removeReceipt({{ $order->id }})" 
                                    class="ml-2 text-red-600 dark:text-red-400 hover:underline text-xs">
                                Remover
                            </button>
                        @else
                            <label class="cursor-pointer">
                                <input type="file" 
                                       class="hidden receipt-input" 
                                       data-order-id="{{ $order->id }}"
                                       accept="image/*,application/pdf">
                                <span class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                                    Anexar
                                </span>
                            </label>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($payment && $payment->cash_approved)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                Aprovado
                            </span>
                            @if($payment->approvedBy)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Por: {{ $payment->approvedBy->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $payment->approved_at?->format('d/m/Y H:i') }}
                            </div>
                            @endif
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300">
                                Pendente
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        @if($payment && $payment->cash_approved !== true)
                        <button onclick="approveOrder({{ $order->id }})" 
                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3">
                            Aprovar
                        </button>
                        @endif
                        <a href="{{ route('orders.show', $order->id) }}" 
                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                            Ver Detalhes
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Nenhum pedido/venda encontrado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $orders->links() }}
    </div>
</div>

<!-- Botão de Aprovar Selecionados -->
@if($status == 'pendente' || $status == 'todos')
<div class="mt-4 flex justify-end">
    <button onclick="approveSelected()" 
            id="approve-selected-btn"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            disabled>
        Aprovar Selecionados
    </button>
</div>
@endif

<!-- Modal de Confirmação -->
<div id="confirm-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-4">
                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2" id="modal-title">Confirmar Ação</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400" id="modal-message">
                    Deseja realmente realizar esta ação?
                </p>
            </div>
            <div class="flex items-center px-4 py-3 space-x-3">
                <button id="modal-cancel" 
                        type="button"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancelar
                </button>
                <button id="modal-confirm" 
                        type="button"
                        class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Sucesso/Erro -->
<div id="result-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div id="result-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                <!-- Ícone será inserido via JavaScript -->
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2" id="result-title">Resultado</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400" id="result-message">
                    Operação realizada com sucesso!
                </p>
            </div>
            <div class="flex items-center px-4 py-3">
                <button onclick="closeResultModal()"
                        class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAction = null;
let currentOrderId = null;
let currentOrderIds = null;

// Selecionar todos
document.getElementById('select-all-checkbox')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateApproveButton();
});

// Atualizar botão de aprovar selecionados
function updateApproveButton() {
    const checked = document.querySelectorAll('.order-checkbox:checked');
    const btn = document.getElementById('approve-selected-btn');
    if (btn) {
        btn.disabled = checked.length === 0;
    }
}

document.querySelectorAll('.order-checkbox').forEach(cb => {
    cb.addEventListener('change', updateApproveButton);
});

// Event listeners para os botões do modal
document.addEventListener('DOMContentLoaded', function() {
    const modalCancel = document.getElementById('modal-cancel');
    const modalConfirm = document.getElementById('modal-confirm');
    
    if (modalCancel) {
        modalCancel.addEventListener('click', closeModal);
    }
    
    if (modalConfirm) {
        modalConfirm.addEventListener('click', confirmAction);
    }
});

// Funções do Modal
function showModal(title, message, action, orderId = null, orderIds = null) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-message').textContent = message;
    currentAction = action;
    currentOrderId = orderId;
    currentOrderIds = orderIds;
    document.getElementById('confirm-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirm-modal').classList.add('hidden');
    currentAction = null;
    currentOrderId = null;
    currentOrderIds = null;
}

function confirmAction() {
    console.log('confirmAction chamado', { currentAction, currentOrderId, currentOrderIds });
    
    // Salvar valores antes de fechar o modal
    const action = currentAction;
    const orderId = currentOrderId;
    const orderIds = currentOrderIds;
    
    closeModal();
    
    // Executar ação após fechar o modal
    if (action === 'approve') {
        console.log('Executando approveOrderRequest com orderId:', orderId);
        approveOrderRequest(orderId);
    } else if (action === 'approve-multiple') {
        console.log('Executando approveMultipleRequest com orderIds:', orderIds);
        approveMultipleRequest(orderIds);
    } else if (action === 'remove-receipt') {
        console.log('Executando removeReceiptRequest com orderId:', orderId);
        removeReceiptRequest(orderId);
    } else {
        console.error('Ação desconhecida:', action);
        showResultModal('error', 'Erro', 'Ação desconhecida');
    }
}

function showResultModal(type, title, message) {
    const iconDiv = document.getElementById('result-icon');
    iconDiv.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4';
    
    if (type === 'success') {
        iconDiv.classList.add('bg-green-100', 'dark:bg-green-900/30');
        iconDiv.innerHTML = `
            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        `;
    } else {
        iconDiv.classList.add('bg-red-100', 'dark:bg-red-900/30');
        iconDiv.innerHTML = `
            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        `;
    }
    
    document.getElementById('result-title').textContent = title;
    document.getElementById('result-message').textContent = message;
    document.getElementById('result-modal').classList.remove('hidden');
}

function closeResultModal() {
    document.getElementById('result-modal').classList.add('hidden');
    location.reload();
}

// Aprovar pedido individual
function approveOrder(orderId) {
    showModal(
        'Aprovar Pagamento',
        'Deseja aprovar este pagamento e dar baixa no pedido?',
        'approve',
        orderId
    );
}

function approveOrderRequest(orderId) {
    if (!orderId) {
        console.error('OrderId não fornecido');
        showResultModal('error', 'Erro', 'ID do pedido não fornecido');
        return;
    }
    
    console.log('Aprovando pedido:', orderId);
    
    fetch(`/cash/approvals/${orderId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        console.log('Resposta recebida:', response.status);
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.error || 'Erro na requisição');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Dados recebidos:', data);
        if (data.success) {
            showResultModal('success', 'Sucesso!', data.message);
        } else {
            showResultModal('error', 'Erro', data.error || 'Erro ao aprovar pagamento');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showResultModal('error', 'Erro', error.message || 'Erro ao aprovar pagamento');
    });
}

// Aprovar selecionados
function approveSelected() {
    const checked = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
    
    if (checked.length === 0) {
        showResultModal('error', 'Atenção', 'Selecione pelo menos um pedido');
        return;
    }

    showModal(
        'Aprovar Múltiplos Pagamentos',
        `Deseja aprovar ${checked.length} pagamento(s) e dar baixa nos pedidos?`,
        'approve-multiple',
        null,
        checked
    );
}

function approveMultipleRequest(orderIds) {
    fetch('/cash/approvals/approve-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ order_ids: orderIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = data.message;
            if (data.errors && data.errors.length > 0) {
                message += '\n\nErros: ' + data.errors.join(', ');
            }
            showResultModal('success', 'Sucesso!', message);
        } else {
            showResultModal('error', 'Erro', data.error || 'Erro ao aprovar pagamentos');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showResultModal('error', 'Erro', 'Erro ao aprovar pagamentos');
    });
}

// Anexar comprovante
document.querySelectorAll('.receipt-input').forEach(input => {
    input.addEventListener('change', function() {
        const orderId = this.dataset.orderId;
        const file = this.files[0];
        
        if (!file) return;

        const formData = new FormData();
        formData.append('receipt', file);

        fetch(`/cash/approvals/${orderId}/attach-receipt`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showResultModal('success', 'Sucesso!', data.message);
            } else {
                showResultModal('error', 'Erro', data.error || 'Erro ao anexar comprovante');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showResultModal('error', 'Erro', 'Erro ao anexar comprovante');
        });
    });
});

// Remover comprovante
function removeReceipt(orderId) {
    showModal(
        'Remover Comprovante',
        'Deseja remover o comprovante deste pagamento?',
        'remove-receipt',
        orderId
    );
}

function removeReceiptRequest(orderId) {
    fetch(`/cash/approvals/${orderId}/remove-receipt`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResultModal('success', 'Sucesso!', data.message);
        } else {
            showResultModal('error', 'Erro', data.error || 'Erro ao remover comprovante');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showResultModal('error', 'Erro', 'Erro ao remover comprovante');
    });
}

// Expor funções globalmente para os handlers onclick inline
window.approveOrder = approveOrder;
window.approveSelected = approveSelected;
window.removeReceipt = removeReceipt;
window.closeResultModal = closeResultModal;

</script>
@endsection

