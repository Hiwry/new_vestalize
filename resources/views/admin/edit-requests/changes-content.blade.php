<div class="space-y-4">
    <!-- Informações da Solicitação -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Motivo da Solicitação</h4>
        <p class="text-sm text-blue-800 dark:text-blue-200">{{ $editRequest->reason }}</p>
    </div>

    @if($editRequest->admin_notes)
    <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Observações do Administrador</h4>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $editRequest->admin_notes }}</p>
    </div>
    @endif

    @if($editRequest->status === 'completed' && isset($differences) && !empty($differences))
    <!-- Alterações Implementadas (Antes e Depois) -->
    <div class="space-y-3">
        <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">Alterações Implementadas</h4>
        
        @if(isset($differences['order']))
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">📋 Dados do Pedido</h5>
            <div class="space-y-2">
                @foreach($differences['order'] as $key => $change)
                <div class="grid grid-cols-2 gap-4 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $change['field'] }}</p>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded px-2 py-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Antes:</p>
                            <div class="text-sm text-red-700 dark:text-red-300">
                                @if(is_array($change['old']))
                                    <pre class="text-xs overflow-auto max-h-32">{{ json_encode($change['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @elseif(is_numeric($change['old']) && in_array($key, ['subtotal', 'discount', 'delivery_fee', 'total']))
                                    R$ {{ number_format($change['old'], 2, ',', '.') }}
                                @elseif(in_array($key, ['order_date', 'delivery_date', 'entry_date']) && $change['old'])
                                    {{ \Carbon\Carbon::parse($change['old'])->format('d/m/Y') }}
                                @else
                                    {{ $change['old'] ?? '(vazio)' }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 invisible">{{ $change['field'] }}</p>
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded px-2 py-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Depois:</p>
                            <div class="text-sm text-green-700 dark:text-green-300">
                                @if(is_array($change['new']))
                                    <pre class="text-xs overflow-auto max-h-32">{{ json_encode($change['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @elseif(is_numeric($change['new']) && in_array($key, ['subtotal', 'discount', 'delivery_fee', 'total']))
                                    R$ {{ number_format($change['new'], 2, ',', '.') }}
                                @elseif(in_array($key, ['order_date', 'delivery_date', 'entry_date']) && $change['new'])
                                    {{ \Carbon\Carbon::parse($change['new'])->format('d/m/Y') }}
                                @else
                                    {{ $change['new'] ?? '(vazio)' }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(isset($differences['client']))
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">👤 Dados do Cliente</h5>
            <div class="space-y-2">
                @foreach($differences['client'] as $key => $change)
                <div class="grid grid-cols-2 gap-4 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $change['field'] }}</p>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded px-2 py-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Antes:</p>
                            <div class="text-sm text-red-700 dark:text-red-300">
                                @if(is_array($change['old']))
                                    <pre class="text-xs overflow-auto max-h-32">{{ json_encode($change['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    {{ $change['old'] ?? '(vazio)' }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 invisible">{{ $change['field'] }}</p>
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded px-2 py-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Depois:</p>
                            <div class="text-sm text-green-700 dark:text-green-300">
                                @if(is_array($change['new']))
                                    <pre class="text-xs overflow-auto max-h-32">{{ json_encode($change['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    {{ $change['new'] ?? '(vazio)' }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(isset($differences['items']))
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">🎨 Itens do Pedido</h5>
            @foreach($differences['items'] as $itemId => $itemChanges)
            <div class="mb-4 last:mb-0">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Item #{{ $itemId }}</p>
                <div class="space-y-2 ml-4">
                    @foreach($itemChanges as $key => $change)
                    <div class="grid grid-cols-2 gap-4 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $change['field'] }}</p>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded px-2 py-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Antes:</p>
                                <div class="text-sm text-red-700 dark:text-red-300">
                                    @php
                                        $oldValue = $change['old'];
                                        // Formatar tamanhos
                                        if($key === 'sizes' && is_array($oldValue)) {
                                            echo '<div class="space-y-1">';
                                            foreach($oldValue as $size => $qty) {
                                                if($qty > 0) {
                                                    echo '<div class="flex justify-between"><span class="font-medium">' . $size . ':</span><span>' . $qty . ' un</span></div>';
                                                }
                                            }
                                            echo '</div>';
                                        }
                                        // Formatar sublimações
                                        elseif($key === 'sublimations' && is_array($oldValue)) {
                                            echo '<div class="space-y-2">';
                                            foreach($oldValue as $index => $sub) {
                                                echo '<div class="border-l-2 border-red-400 pl-2">';
                                                echo '<p class="font-semibold">Aplicação ' . ($index + 1) . '</p>';
                                                echo '<p class="text-xs">Tipo: ' . ($sub['application_type'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Local: ' . ($sub['location_name'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Tamanho: ' . ($sub['size_name'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Quantidade: ' . ($sub['quantity'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Cores: ' . ($sub['color_details'] ?? $sub['color_count'] . ' cores') . '</p>';
                                                if(!empty($sub['seller_notes'])) {
                                                    echo '<p class="text-xs">Obs: ' . $sub['seller_notes'] . '</p>';
                                                }
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        }
                                        // Formatar arquivos
                                        elseif($key === 'files' && is_array($oldValue)) {
                                            echo '<div class="space-y-1">';
                                            foreach($oldValue as $file) {
                                                echo '<div class="text-xs">📎 ' . ($file['file_name'] ?? 'Arquivo') . '</div>';
                                            }
                                            echo '</div>';
                                        }
                                        // Outros arrays
                                        elseif(is_array($oldValue)) {
                                            echo '<pre class="text-xs overflow-auto max-h-40">' . json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                                        }
                                        // Valores monetários
                                        elseif(is_numeric($oldValue) && in_array($key, ['unit_price', 'total_price'])) {
                                            echo 'R$ ' . number_format($oldValue, 2, ',', '.');
                                        }
                                        // Valores simples
                                        else {
                                            echo $oldValue ?? '(vazio)';
                                        }
                                    @endphp
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 invisible">{{ $change['field'] }}</p>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded px-2 py-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Depois:</p>
                                <div class="text-sm text-green-700 dark:text-green-300">
                                    @php
                                        $newValue = $change['new'];
                                        // Formatar tamanhos
                                        if($key === 'sizes' && is_array($newValue)) {
                                            echo '<div class="space-y-1">';
                                            foreach($newValue as $size => $qty) {
                                                if($qty > 0) {
                                                    echo '<div class="flex justify-between"><span class="font-medium">' . $size . ':</span><span>' . $qty . ' un</span></div>';
                                                }
                                            }
                                            echo '</div>';
                                        }
                                        // Formatar sublimações
                                        elseif($key === 'sublimations' && is_array($newValue)) {
                                            echo '<div class="space-y-2">';
                                            foreach($newValue as $index => $sub) {
                                                echo '<div class="border-l-2 border-green-400 pl-2">';
                                                echo '<p class="font-semibold">Aplicação ' . ($index + 1) . '</p>';
                                                echo '<p class="text-xs">Tipo: ' . ($sub['application_type'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Local: ' . ($sub['location_name'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Tamanho: ' . ($sub['size_name'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Quantidade: ' . ($sub['quantity'] ?? '-') . '</p>';
                                                echo '<p class="text-xs">Cores: ' . ($sub['color_details'] ?? $sub['color_count'] . ' cores') . '</p>';
                                                if(!empty($sub['seller_notes'])) {
                                                    echo '<p class="text-xs">Obs: ' . $sub['seller_notes'] . '</p>';
                                                }
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        }
                                        // Formatar arquivos
                                        elseif($key === 'files' && is_array($newValue)) {
                                            echo '<div class="space-y-1">';
                                            foreach($newValue as $file) {
                                                echo '<div class="text-xs">📎 ' . ($file['file_name'] ?? 'Arquivo') . '</div>';
                                            }
                                            echo '</div>';
                                        }
                                        // Outros arrays
                                        elseif(is_array($newValue)) {
                                            echo '<pre class="text-xs overflow-auto max-h-40">' . json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                                        }
                                        // Valores monetários
                                        elseif(is_numeric($newValue) && in_array($key, ['unit_price', 'total_price'])) {
                                            echo 'R$ ' . number_format($newValue, 2, ',', '.');
                                        }
                                        // Valores simples
                                        else {
                                            echo $newValue ?? '(vazio)';
                                        }
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @elseif($editRequest->status === 'approved' && $editRequest->order_snapshot_before)
    <!-- Solicitação Aprovada mas ainda não implementada -->
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
        <h4 class="font-semibold text-green-900 dark:text-green-100 mb-2">✅ Edição Aprovada</h4>
        <p class="text-sm text-green-800 dark:text-green-200">Esta edição foi aprovada e está aguardando implementação pelo usuário. O estado atual do pedido foi salvo e as alterações poderão ser visualizadas após a implementação.</p>
    </div>
    
    @if($editRequest->changes && count($editRequest->changes) > 0)
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
        <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Alterações Solicitadas:</h5>
        <pre class="text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-3 rounded overflow-auto max-h-60">{{ json_encode($editRequest->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    @endif
    @else
    <!-- Solicitação Pendente -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <h4 class="font-semibold text-yellow-900 dark:text-yellow-100 mb-2">⏳ Aguardando Análise</h4>
        <p class="text-sm text-yellow-800 dark:text-yellow-200">Esta solicitação está pendente de análise. As alterações detalhadas serão visíveis após a aprovação e implementação.</p>
    </div>

    @if($editRequest->changes && count($editRequest->changes) > 0)
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
        <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Alterações Solicitadas:</h5>
        <pre class="text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-3 rounded overflow-auto max-h-60">{{ json_encode($editRequest->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    @endif
    @endif

    <!-- Informações Adicionais -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
        <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Informações</h5>
        <div class="space-y-1 text-sm">
            <p class="text-gray-700 dark:text-gray-300"><strong>Pedido:</strong> #{{ str_pad($editRequest->order_id, 6, '0', STR_PAD_LEFT) }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Solicitado por:</strong> {{ $editRequest->user->name }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Data:</strong> {{ $editRequest->created_at->format('d/m/Y H:i') }}</p>
            @if($editRequest->approvedBy)
            <p class="text-gray-700 dark:text-gray-300"><strong>{{ $editRequest->status === 'approved' ? 'Aprovado' : 'Rejeitado' }} por:</strong> {{ $editRequest->approvedBy->name }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Em:</strong> {{ $editRequest->approved_at->format('d/m/Y H:i') }}</p>
            @endif
            @if($editRequest->completed_at)
            <p class="text-gray-700 dark:text-gray-300"><strong>Implementado em:</strong> {{ $editRequest->completed_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    </div>
</div>

