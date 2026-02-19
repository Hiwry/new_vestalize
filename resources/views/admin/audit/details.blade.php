<div class="space-y-6">
    <!-- Header info -->
    <div class="grid grid-cols-2 gap-4 bg-indigo-50 dark:bg-indigo-900/10 p-4 rounded-xl">
        <div>
            <span class="block text-xs font-bold text-indigo-400 dark:text-indigo-500 uppercase">Ação</span>
            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ strtoupper($log->event) }}</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-indigo-400 dark:text-indigo-500 uppercase">Horário</span>
            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-indigo-400 dark:text-indigo-500 uppercase">Usuário</span>
            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $log->causer->name ?? 'Sistema' }}</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-indigo-400 dark:text-indigo-500 uppercase">Endereço IP</span>
            <span class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $log->ip_address }}</span>
        </div>
    </div>

    <!-- Description -->
    <div>
        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 border-l-4 border-indigo-500 pl-2">Descrição</h4>
        <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg text-sm text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-800">
            {{ $log->description }}
        </div>
    </div>

    <!-- Metadata (Model Info) -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Modelo Afetado</h4>
            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs text-gray-600 dark:text-gray-400 font-mono">
                {{ class_basename($log->subject_type) }}
            </span>
        </div>
        <div>
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ID do Registro</h4>
            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs text-gray-600 dark:text-gray-400 font-mono">
                #{{ $log->subject_id }}
            </span>
        </div>
    </div>

    <!-- Properties (JSON Changes) -->
    @if($log->properties && count($log->properties) > 0)
    <div>
        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 border-l-4 border-indigo-500 pl-2">Alterações Detalhadas</h4>
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Campo</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Valor Anterior</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Novo Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    @foreach($log->properties as $key => $values)
                        @if(is_array($values) && isset($values['old']))
                        <tr>
                            <td class="px-4 py-2 font-bold text-gray-600 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-900/30">{{ $key }}</td>
                            <td class="px-4 py-2 text-red-600 dark:text-red-400 bg-red-50/20 dark:bg-red-900/10">{{ is_scalar($values['old']) ? $values['old'] : 'Complexo' }}</td>
                            <td class="px-4 py-2 text-green-600 dark:text-green-400 bg-green-50/20 dark:bg-green-900/10">{{ is_scalar($values['new']) ? $values['new'] : 'Complexo' }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="px-4 py-2 font-bold text-gray-600 dark:text-gray-400">{{ $key }}</td>
                            <td colspan="2" class="px-4 py-2 font-mono truncate">{{ json_encode($values) }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- User Agent -->
    <div>
        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">User Agent</h4>
        <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-[10px] text-gray-500 font-mono break-all line-clamp-2">
            {{ $log->user_agent }}
        </div>
    </div>
</div>
