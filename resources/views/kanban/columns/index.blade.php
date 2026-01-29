@extends('layouts.admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600/30 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Gerenciar Colunas do Kanban</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Configure as colunas e sua ordem no Kanban de produção</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('kanban.index') }}" 
                   class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 transition">
                    ← Voltar ao Kanban
                </a>
                <a href="{{ route('kanban.columns.create') }}" 
                   class="px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition" style="color: white !important;">
                    + Nova Coluna
                </a>
            </div>
        </div>

        <!-- Lista de Colunas -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Colunas do Kanban</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Arraste para reordenar as colunas</p>
            </div>

            @if($statuses->count() > 0)
            <div id="sortable-columns" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($statuses as $status)
                <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" data-status-id="{{ $status->id }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Ícone de arrastar -->
                            <div class="cursor-move text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                            </div>

                            <!-- Cor e Nome -->
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600" 
                                     style="background-color: {{ $status->color }}"></div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $status->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Posição: {{ $status->position }}</p>
                                </div>
                            </div>

                            <!-- Contador de Pedidos -->
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                    {{ $status->orders_count ?? 0 }} pedido(s)
                                </span>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('kanban.columns.edit', $status) }}" 
                               class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-md hover:bg-blue-200 dark:hover:bg-blue-900/50 transition">
                                Editar
                            </a>
                            
                            @if($status->orders_count > 0)
                            <button onclick="openMoveModal({{ $status->id }}, '{{ $status->name }}', {{ $status->orders_count }})"
                                    class="px-3 py-1 text-sm bg-yellow-500 dark:bg-yellow-600 text-white rounded-md hover:bg-yellow-600 dark:hover:bg-yellow-700 transition" style="color: white !important;">
                                Mover Pedidos
                            </button>
                            @endif

                            <form id="delete-form-{{ $status->id }}" method="POST" action="{{ route('kanban.columns.destroy', $status) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        onclick="openDeleteModal({{ $status->id }}, '{{ $status->name }}')"
                                        class="px-3 py-1 text-sm bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-md hover:bg-red-200 dark:hover:bg-red-900/50 transition
                                               {{ $status->orders_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $status->orders_count > 0 ? 'disabled' : '' }}>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhuma coluna encontrada</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Comece criando sua primeira coluna do Kanban.</p>
                <a href="{{ route('kanban.columns.create') }}" 
                   class="px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition">
                    Criar Primeira Coluna
                </a>
            </div>
            @endif
        </div>

        <!-- Botão Salvar Ordem -->
        @if($statuses->count() > 1)
        <div class="mt-6 text-center">
            <button onclick="saveOrder()" 
                    class="px-6 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition" style="color: white !important;">
                Salvar Ordem das Colunas
            </button>
        </div>
        @endif
    </div>

    <!-- Modal para Mover Pedidos -->
    <div id="moveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/25 max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium dark:text-gray-100">Mover Pedidos</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Mover <span id="moveCount">0</span> pedido(s) da coluna 
                        <strong id="moveFromColumn">-</strong> para:
                    </p>
                    <form id="moveForm" method="POST">
                        @csrf
                        <select name="target_status_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                            <option value="">Selecione a coluna de destino</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end space-x-3">
                    <button onclick="closeMoveModal()" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button onclick="submitMove()" 
                            class="px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition">
                        Mover Pedidos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Confirmar Exclusão -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/25 max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium dark:text-gray-100 italic">Confirmar Exclusão</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 dark:text-gray-300">
                        Tem certeza que deseja excluir a coluna <strong id="deleteColumnName">-</strong>?
                    </p>
                    <p class="text-sm text-red-500 mt-2">
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-end space-x-3">
                    <button onclick="closeDeleteModal()" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button onclick="confirmDelete()" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition font-bold">
                        Sim, Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inicializar Sortable apenas se houver colunas
        document.addEventListener('DOMContentLoaded', function() {
            const sortableContainer = document.getElementById('sortable-columns');
            if (sortableContainer) {
                const sortable = new Sortable(sortableContainer, {
                    animation: 150,
                    ghostClass: 'opacity-50',
                    handle: '.cursor-move'
                });
            }
        });

        // Salvar ordem
        function saveOrder() {
            const statusIds = Array.from(document.querySelectorAll('[data-status-id]'))
                .map(el => parseInt(el.dataset.statusId)); // Converter para inteiro

            if (statusIds.length === 0) {
                alert('Nenhuma coluna encontrada para reordenar.');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('Erro: Token CSRF não encontrado. Recarregue a página e tente novamente.');
                return;
            }

            fetch('{{ route("kanban.columns.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ statuses: statusIds })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Erro ao salvar ordem');
                    }).catch(() => {
                        throw new Error('Erro ao processar resposta do servidor');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Ordem salva com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao salvar ordem: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar ordem das colunas: ' + error.message);
            });
        }

        // Modal de mover pedidos
        let currentStatusId = null;
        let statusToDelete = null;

        function openMoveModal(statusId, statusName, ordersCount) {
            currentStatusId = statusId;
            document.getElementById('moveCount').textContent = ordersCount;
            document.getElementById('moveFromColumn').textContent = statusName;
            document.getElementById('moveForm').action = `{{ url('kanban/columns') }}/${statusId}/move-orders`;
            document.getElementById('moveModal').classList.remove('hidden');
        }

        function closeMoveModal() {
            document.getElementById('moveModal').classList.add('hidden');
            currentStatusId = null;
        }

        function submitMove() {
            const form = document.getElementById('moveForm');
            const targetStatusId = form.target_status_id.value;
            
            if (!targetStatusId) {
                alert('Selecione uma coluna de destino');
                return;
            }

            form.submit();
        }

        // Modal de Deletar
        function openDeleteModal(statusId, statusName) {
            statusToDelete = statusId;
            document.getElementById('deleteColumnName').textContent = statusName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            statusToDelete = null;
        }

        function confirmDelete() {
            if (statusToDelete) {
                document.getElementById('delete-form-' + statusToDelete).submit();
            }
        }

        // Fechar modais ao clicar fora
        window.addEventListener('click', function(e) {
            const moveModal = document.getElementById('moveModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (e.target === moveModal) {
                closeMoveModal();
            }
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
