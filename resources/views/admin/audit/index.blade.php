@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 py-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Auditoria do Sistema</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Rastreie todas as atividades e mudanças realizadas no banco de dados.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.reload()" class="p-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('admin.audit.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Evento</label>
                <select name="event" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 text-sm">
                    <option value="">Todos os Eventos</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                            {{ ucfirst($event) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Usuário</label>
                <select name="user_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 text-sm">
                    <option value="">Todos os Usuários</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Busca Rápida</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="IP, descrição..." 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition shadow-sm">
                    Filtrar
                </button>
                <a href="{{ route('admin.audit.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 py-2 px-4 rounded-lg transition border border-gray-200 dark:border-gray-500">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela de Logs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuário</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Evento</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Origem</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 mr-3 text-xs font-bold">
                                    {{ substr($log->causer->name ?? 'S', 0, 1) }}
                                </div>
                                <div class="text-gray-900 dark:text-gray-200 font-medium">
                                    {{ $log->causer->name ?? 'Sistema' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badgeClass = match($log->event) {
                                    'created' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'updated' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'deleted' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full font-bold uppercase tracking-tight {{ $badgeClass }}">
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 dark:text-gray-300 max-w-xs truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded inline-block">
                                {{ $log->ip_address ?? '0.0.0.0' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button onclick="viewLogDetails({{ $log->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 font-bold uppercase text-xs tracking-wider">
                                Detalhes
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-history text-4xl mb-3 opacity-20"></i>
                                <p>Nenhum registro de auditoria encontrado.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de Detalhes -->
<div id="logDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeLogModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detalhes da Atividade</h3>
                <button onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto" id="modalContent">
                <div class="flex justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewLogDetails(id) {
        const modal = document.getElementById('logDetailModal');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        content.innerHTML = '<div class="flex justify-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div></div>';

        fetch(`{{ url('admin/audit') }}/${id}/details`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = '<div class="text-red-500 p-4">Erro ao carregar detalhes.</div>';
            });
    }

    function closeLogModal() {
        document.getElementById('logDetailModal').classList.add('hidden');
    }
</script>
@endsection
