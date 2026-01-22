@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Gerenciar Edições</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Aprove ou rejeite solicitações de edição de pedidos</p>
    </div>
</div>

        @if(session('success'))
<div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
<div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
        @endif

<!-- Lista de Solicitações de Edição -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
            <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Pedido
                            </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cliente
                            </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Solicitado por
                            </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Motivo
                            </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Data
                            </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($editRequests as $editRequest)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div>
                                <a href="{{ route('orders.show', $editRequest->order->id) }}" 
                                   class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                    #{{ str_pad($editRequest->order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    R$ {{ number_format($editRequest->order->total, 2, ',', '.') }}
                                </div>
                            </div>
                                </div>
                            </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $editRequest->order->client->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $editRequest->order->client->phone_primary }}</div>
                            </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $editRequest->user->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $editRequest->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                    <td class="px-6 py-4 max-w-xs">
                        <div class="text-sm text-gray-900 dark:text-gray-100 truncate" title="{{ $editRequest->reason }}">
                                    {{ $editRequest->reason }}
                                </div>
                            </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($editRequest->status === 'pending')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                        Pendente
                                    </span>
                                @elseif($editRequest->status === 'approved')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        Aprovado
                                    </span>
                        @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        Rejeitado
                                    </span>
                                @endif
                            </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ $editRequest->created_at->format('d/m/Y') }}
                            </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @if($editRequest->status === 'pending')
                            <div class="flex justify-center space-x-2">
                                <button onclick="viewChanges({{ $editRequest->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </button>
                                        <button onclick="approveEdit({{ $editRequest->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/30 rounded-md hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                            Aprovar
                                        </button>
                                        <button onclick="rejectEdit({{ $editRequest->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 rounded-md hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                            Rejeitar
                                        </button>
                                    </div>
                                @else
                            <div class="flex flex-col items-center space-y-2">
                                <button onclick="viewChanges({{ $editRequest->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </button>
                                        @if($editRequest->approvedBy)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Por: {{ $editRequest->approvedBy->name }}
                                    </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhuma solicitação de edição encontrada.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($editRequests->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $editRequests->links() }}
            </div>
            @endif
    </div>

<!-- Modal de Visualização de Alterações -->
<div id="changes-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/50 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Alterações Solicitadas</h3>
                    <button onclick="closeChangesModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                </div>
            <div id="changes-content" class="px-6 py-4">
                <!-- Conteúdo carregado via AJAX -->
            </div>
            </div>
        </div>
    </div>

    <!-- Modal de Aprovação -->
<div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/50 max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aprovar Edição</h3>
                </div>
                <form id="approve-form" method="POST">
                    @csrf
                    <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Observações (opcional)
                        </label>
                        <textarea name="admin_notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500"
                                  placeholder="Adicione observações sobre a aprovação..."></textarea>
                    </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3 rounded-b-lg">
                        <button type="button" onclick="closeApproveModal()" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition">
                            Aprovar Edição
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Rejeição -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/50 max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Rejeitar Edição</h3>
                </div>
                <form id="reject-form" method="POST">
                    @csrf
                    <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo da Rejeição *
                        </label>
                        <textarea name="admin_notes" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-500"
                                  placeholder="Explique o motivo da rejeição..."></textarea>
                    </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3 rounded-b-lg">
                        <button type="button" onclick="closeRejectModal()" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                            Cancelar
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-red-600 dark:bg-red-600 text-white rounded-md hover:bg-red-700 dark:hover:bg-red-700 transition">
                            Rejeitar Edição
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function viewChanges(editRequestId) {
            fetch(`/producao/edit-requests/${editRequestId}/changes`)
                .then(response => response.json())
                .then(data => {
                document.getElementById('changes-content').innerHTML = data.html;
                        document.getElementById('changes-modal').classList.remove('hidden');
                })
                .catch(error => {
                console.error('Erro ao carregar alterações:', error);
                alert('Erro ao carregar as alterações');
            });
    }

    function closeChangesModal() {
        document.getElementById('changes-modal').classList.add('hidden');
        }

        function approveEdit(editRequestId) {
            document.getElementById('approve-form').action = `/producao/edit-requests/${editRequestId}/approve`;
            document.getElementById('approve-modal').classList.remove('hidden');
        }

        function rejectEdit(editRequestId) {
            document.getElementById('reject-form').action = `/producao/edit-requests/${editRequestId}/reject`;
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }

        // Fechar modais ao clicar fora
        document.getElementById('changes-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeChangesModal();
            }
        });

        document.getElementById('approve-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });

        document.getElementById('reject-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
@endsection
