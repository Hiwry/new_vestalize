@props(['order'])

@php
// Função auxiliar para formatar valores (definida uma única vez)
if (!function_exists('formatChangeValue')) {
    function formatChangeValue($value, $fieldName = '') {
        if (is_null($value)) {
            return '<span class="text-gray-400 italic">Não informado</span>';
        }
        
        if (is_bool($value)) {
            return $value ? '<span class="text-green-600">Sim</span>' : '<span class="text-red-600">Não</span>';
        }
        
        if (is_array($value)) {
            // Tamanhos
            if (in_array($fieldName, ['sizes', 'tamanhos'])) {
                $formatted = [];
                foreach ($value as $size => $qty) {
                    if ($qty > 0) {
                        $formatted[] = htmlspecialchars($size) . ': ' . htmlspecialchars($qty);
                    }
                }
                return '<span class="font-mono text-xs">' . implode(', ', $formatted) . '</span>';
            }
            
            // Payment methods
            if (str_contains($fieldName, 'payment_methods')) {
                $formatted = [];
                foreach ($value as $method) {
                    if (isset($method['method']) && isset($method['amount'])) {
                        $formatted[] = htmlspecialchars(strtoupper($method['method'])) . ': R$ ' . number_format($method['amount'], 2, ',', '.');
                    }
                }
                return implode(' + ', $formatted);
            }
            
            // Outros arrays
            return '<span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">' . count($value) . ' itens</span>';
        }
        
        // Valores monetários
        if (is_numeric($value) && (str_contains($fieldName, 'price') || str_contains($fieldName, 'amount') || str_contains($fieldName, 'total') || str_contains($fieldName, 'fee') || str_contains($fieldName, 'discount'))) {
            return 'R$ ' . number_format((float)$value, 2, ',', '.');
        }
        
        // Datas
        if (str_contains($fieldName, 'date') && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            try {
                return \Carbon\Carbon::parse($value)->format('d/m/Y');
            } catch (\Exception $e) {
                return $value;
            }
        }
        
        return htmlspecialchars($value);
    }
}

// Função para obter título legível
if (!function_exists('getFieldLabel')) {
    function getFieldLabel($key) {
        $labels = [
            'name' => 'Nome',
            'phone_primary' => 'Telefone Principal',
            'phone_secondary' => 'Telefone Secundário',
            'email' => 'E-mail',
            'cpf_cnpj' => 'CPF/CNPJ',
            'address' => 'Endereço',
            'city' => 'Cidade',
            'state' => 'Estado',
            'zip_code' => 'CEP',
            'category' => 'Categoria',
            'quantity' => 'Quantidade',
            'unit_price' => 'Preço Unitário',
            'total_price' => 'Preço Total',
            'fabric' => 'Tecido',
            'color' => 'Cor',
            'collar' => 'Gola',
            'model' => 'Modelo',
            'detail' => 'Detalhe',
            'print_type' => 'Tipo de Impressão',
            'art_name' => 'Nome da Arte',
            'sizes' => 'Tamanhos',
            'entry_date' => 'Data de Entrada',
            'entry_amount' => 'Valor de Entrada',
            'payment_date' => 'Data de Pagamento',
            'payment_method' => 'Forma de Pagamento',
            'payment_methods' => 'Formas de Pagamento',
            'delivery_fee' => 'Taxa de Entrega',
            'delivery_date' => 'Data de Entrega',
            'subtotal' => 'Subtotal',
            'total' => 'Total',
            'discount' => 'Desconto',
            'seller' => 'Vendedor',
            'contract_type' => 'Tipo de Contrato',
            'is_event' => 'É Evento',
        ];
        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-md flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Histórico de Edições</h3>
                <p class="text-sm text-gray-600 dark:text-slate-400">Registro de todas as alterações realizadas</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if($order->editHistory->count() > 0)
            <div class="space-y-4">
                @foreach($order->editHistory->sortByDesc('created_at') as $history)
                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            @if($history->action === 'finalize') bg-green-100 text-green-600
                            @elseif($history->action === 'client_changes') bg-blue-100 text-blue-600
                            @elseif($history->action === 'item_changes') bg-purple-100 text-purple-600
                            @elseif($history->action === 'payment_changes') bg-yellow-100 text-yellow-600
                            @else bg-gray-100 text-gray-600
                            @endif">
                            @if($history->action === 'finalize')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @elseif($history->action === 'client_changes')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            @elseif($history->action === 'item_changes')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            @elseif($history->action === 'payment_changes')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white break-words">{{ $history->description }}</h4>
                            <time class="text-xs text-gray-500 dark:text-slate-400 whitespace-nowrap flex-shrink-0" datetime="{{ $history->created_at }}">
                                {{ $history->created_at->format('d/m/Y H:i') }}
                            </time>
                        </div>
                        
                        <p class="text-xs text-gray-600 dark:text-slate-400 mt-1 break-words">
                            Por: <span class="font-medium">{{ $history->user_name }}</span>
                        </p>
                        
                        @php
                            // Garantir que changes é um array
                            $changes = $history->changes;
                            if (is_string($changes)) {
                                $changes = json_decode($changes, true) ?? [];
                            } elseif (!is_array($changes)) {
                                $changes = [];
                            }
                        @endphp
                        
                        @if(!empty($changes) && is_array($changes) && count($changes) > 0)
                        <div class="mt-3">
                            <details class="group">
                                <summary class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 cursor-pointer font-medium">
                                    Ver alterações detalhadas
                                    <svg class="w-3 h-3 inline ml-1 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </summary>
                                
                                <div class="mt-3">
                                    @php
                                        // Verificar se há conteúdo para exibir
                                        $hasContent = false;
                                        if (isset($changes['order']) && !empty($changes['order'])) $hasContent = true;
                                        if (isset($changes['client']) && !empty($changes['client'])) $hasContent = true;
                                        if (isset($changes['items']) && !empty($changes['items'])) $hasContent = true;
                                        if (isset($changes['payments']) && !empty($changes['payments'])) $hasContent = true;
                                    @endphp
                                    
                                    @if(!$hasContent)
                                        <div class="text-center py-4">
                                            <p class="text-sm text-gray-500 dark:text-slate-400">Nenhuma alteração detalhada disponível para este registro.</p>
                                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Edições feitas antes da atualização do sistema podem não ter detalhes salvos.</p>
                                        </div>
                                    @else
                                    <div class="space-y-4">
                                    
                                    {{-- Seção: Dados do Pedido --}}
                                    @if(isset($changes['order']) && !empty($changes['order']))
                                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-950/50 dark:to-blue-950/50 rounded-lg p-4 border border-indigo-200 dark:border-indigo-800">
                                            <div class="flex items-center space-x-2 mb-3">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <h5 class="text-sm font-semibold text-indigo-900 dark:text-indigo-300">Dados do Pedido</h5>
                                            </div>
                                            <div class="space-y-2">
                                @foreach($changes['order'] as $field => $change)
                                    @if(is_array($change) && isset($change['old']) && isset($change['new']))
                                        <div class="flex items-start justify-between bg-white dark:bg-slate-800 rounded-md p-2 text-xs gap-2">
                                            <span class="font-medium text-gray-700 dark:text-slate-300 flex-shrink-0">{{ getFieldLabel($field) }}:</span>
                                            <div class="text-right flex-1 min-w-0">
                                                <div class="text-red-600 line-through break-words">{!! formatChangeValue($change['old'], $field) !!}</div>
                                                <div class="text-green-600 font-medium break-words">→ {!! formatChangeValue($change['new'], $field) !!}</div>
                                            </div>
                                        </div>
                                                    @else
                                                        {{-- Fallback para formato antigo --}}
                                                        <div class="bg-white dark:bg-gray-700 rounded-md p-2 text-xs text-gray-600 break-words overflow-hidden">
                                                            <span class="font-medium">{{ getFieldLabel($field) }}:</span>
                                                            <span class="ml-2">{{ is_array($change) ? json_encode($change) : $change }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Seção: Cliente --}}
                                    @if(isset($changes['client']) && !empty($changes['client']))
                                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-950/50 dark:to-pink-950/50 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                                            <div class="flex items-center space-x-2 mb-3">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <h5 class="text-sm font-semibold text-purple-900 dark:text-purple-300">Dados do Cliente</h5>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach($changes['client'] as $field => $change)
                                            @if(is_array($change) && isset($change['old']) && isset($change['new']))
                                                <div class="flex items-start justify-between bg-white dark:bg-gray-700 rounded-md p-2 text-xs gap-2">
                                                    <span class="font-medium text-gray-700 dark:text-gray-400 flex-shrink-0">{{ getFieldLabel($field) }}:</span>
                                                    <div class="text-right flex-1 min-w-0">
                                                        <div class="text-red-600 line-through break-words">{!! formatChangeValue($change['old'], $field) !!}</div>
                                                        <div class="text-green-600 font-medium break-words">→ {!! formatChangeValue($change['new'], $field) !!}</div>
                                                    </div>
                                                </div>
                                                    @else
                                                        {{-- Fallback para formato antigo --}}
                                                        <div class="bg-white dark:bg-slate-800 rounded-md p-2 text-xs text-gray-600 break-words overflow-hidden">
                                                            <span class="font-medium">{{ getFieldLabel($field) }}:</span>
                                                            <span class="ml-2">{{ is_array($change) ? json_encode($change) : $change }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Seção: Itens do Pedido --}}
                                    @if(isset($changes['items']) && !empty($changes['items']))
                                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-950/50 dark:to-amber-950/50 rounded-lg p-4 border border-orange-200 dark:border-orange-800">
                                            <div class="flex items-center space-x-2 mb-3">
                                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                <h5 class="text-sm font-semibold text-orange-900 dark:text-orange-300">Itens do Pedido</h5>
                                            </div>
                                            <div class="space-y-3">
                                                @foreach($changes['items'] as $itemId => $itemChange)
                                                    @if(is_array($itemChange) && isset($itemChange['type']))
                                                        <div class="bg-white dark:bg-gray-700 rounded-md p-3 border border-orange-200">
                                                            @if($itemChange['type'] === 'modified' && isset($itemChange['changes']))
                                                                <div class="flex items-center space-x-2 mb-2">
                                                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">✏️ Modificado</span>
                                                                    <span class="text-xs text-gray-500">Item #{{ $itemId }}</span>
                                                                </div>
                                                                <div class="space-y-2">
                                                                    @foreach($itemChange['changes'] as $field => $change)
                                                                        @if($field !== 'sublimations' && $field !== 'files' && is_array($change) && isset($change['old']) && isset($change['new']))
                                                                        <div class="flex items-start justify-between text-xs">
                                                                            <span class="font-medium text-gray-700 dark:text-slate-300 mr-3">{{ getFieldLabel($field) }}:</span>
                                                                            <div class="text-right flex-1">
                                                                                <div class="text-red-600 line-through">{!! formatChangeValue($change['old'], $field) !!}</div>
                                                                                <div class="text-green-600 font-medium">→ {!! formatChangeValue($change['new'], $field) !!}</div>
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @elseif($itemChange['type'] === 'added' && isset($itemChange['data']))
                                                                <div class="flex items-center space-x-2">
                                                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">✨ Adicionado</span>
                                                                    <span class="text-xs text-gray-700">{{ $itemChange['data']['quantity'] ?? 0 }} un - {{ $itemChange['data']['print_type'] ?? 'N/A' }}</span>
                                                                </div>
                                                            @elseif($itemChange['type'] === 'removed' && isset($itemChange['data']))
                                                                <div class="flex items-center space-x-2">
                                                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded">🗑️ Removido</span>
                                                                    <span class="text-xs text-gray-700">{{ $itemChange['data']['quantity'] ?? 0 }} un - {{ $itemChange['data']['print_type'] ?? 'N/A' }}</span>
                                                </div>
                                                            @endif
                                                </div>
                                            @else
                                                        {{-- Fallback para formato antigo - Visualização organizada --}}
                                                        @if(is_array($itemChange))
                                                            <div class="bg-white dark:bg-gray-700 rounded-md p-3 border border-orange-200">
                                                                <div class="flex items-center space-x-2 mb-2">
                                                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">📦 Item</span>
                                                                    <span class="text-xs text-gray-500">#{{ $itemId }}</span>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                                    @if(isset($itemChange['quantity']))
                                                                        <div class="bg-gray-50 dark:bg-gray-700/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Quantidade:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $itemChange['quantity'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['print_type']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Impressão:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $itemChange['print_type'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['fabric']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Tecido:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $itemChange['fabric'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['color']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Cor:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $itemChange['color'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['collar']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Gola:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $itemChange['collar'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['model']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Modelo:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $itemChange['model'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['detail']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Detalhe:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $itemChange['detail'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['unit_price']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Preço Unit.:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">R$ {{ number_format((float)$itemChange['unit_price'], 2, ',', '.') }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($itemChange['total_price']))
                                                                        <div class="bg-gray-50 dark:bg-slate-800/50 p-2 rounded">
                                                                            <span class="text-gray-600 dark:text-slate-400">Total:</span>
                                                                            <span class="font-medium text-gray-900 dark:text-white ml-1">R$ {{ number_format((float)$itemChange['total_price'], 2, ',', '.') }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @if(isset($itemChange['sizes']) && is_array($itemChange['sizes']))
                                                                    <div class="mt-2 bg-indigo-50 p-2 rounded">
                                                                        <span class="text-xs text-indigo-700 font-medium">Tamanhos: </span>
                                                                        <span class="text-xs text-indigo-900 font-mono">
                                                                            @php
                                                                                $sizesFormatted = [];
                                                                                foreach($itemChange['sizes'] as $size => $qty) {
                                                                                    if($qty > 0) {
                                                                                        $sizesFormatted[] = "$size: $qty";
                                                                                    }
                                                                                }
                                                                                echo implode(', ', $sizesFormatted);
                                                                            @endphp
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                                @if(isset($itemChange['art_name']))
                                                                    <div class="mt-2 bg-purple-50 p-2 rounded text-xs">
                                                                        <span class="text-purple-700 font-medium">🎨 Arte: </span>
                                                                        <span class="text-purple-900">{{ $itemChange['art_name'] }}</span>
                                                                    </div>
                                                                @endif
                                                                
                                                                @if(isset($itemChange['print_type']) && !in_array($itemChange['print_type'], ['', 'N/A', null]))
                                                                    <div class="mt-3 border-t border-orange-200 pt-3">
                                                                        <div class="flex items-center space-x-2 mb-2">
                                                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                                                            </svg>
                                                                            <span class="text-xs font-semibold text-indigo-900">
                                                                                🎨 Personalização: {{ $itemChange['print_type'] }}
                                                                                @php
                                                                                    $subCount = 0;
                                                                                    if(isset($itemChange['sublimations'])) {
                                                                                        if(is_array($itemChange['sublimations'])) {
                                                                                            $subCount = count($itemChange['sublimations']);
                                                                                        } elseif(is_numeric($itemChange['sublimations'])) {
                                                                                            $subCount = (int)$itemChange['sublimations'];
                                                                                        }
                                                                                    }
                                                                                @endphp
                                                                                @if($subCount > 0)
                                                                                    ({{ $subCount }})
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                
                                                                @php
                                                                    $hasSublimationDetails = false;
                                                                    if(isset($itemChange['sublimations']) && is_array($itemChange['sublimations']) && !empty($itemChange['sublimations'])) {
                                                                        foreach($itemChange['sublimations'] as $sub) {
                                                                            if(is_array($sub) && (isset($sub['application_type']) || isset($sub['location_name']) || isset($sub['final_price']))) {
                                                                                $hasSublimationDetails = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                                
                                                                @if($hasSublimationDetails)
                                                                        <div class="space-y-2">
                                                                            @foreach($itemChange['sublimations'] as $subIndex => $sublimation)
                                                                                @if(is_array($sublimation))
                                                                                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 rounded-lg p-3 border border-indigo-200 dark:border-indigo-800">
                                                                                    <div class="flex items-center justify-between mb-2">
                                                                                        <span class="text-xs font-medium text-indigo-900 dark:text-indigo-300">Personalização {{ $subIndex + 1 }}</span>
                                                                                        @if(isset($sublimation['final_price']))
                                                                                        <span class="text-xs font-bold text-indigo-700">R$ {{ number_format((float)$sublimation['final_price'], 2, ',', '.') }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                                                        @if(isset($sublimation['application_type']))
                                                                                            <div class="bg-white p-2 rounded">
                                                                                                <span class="text-gray-600 dark:text-slate-400">Tipo:</span>
                                                                                                <span class="font-medium text-indigo-900 ml-1">{{ $sublimation['application_type'] }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if(isset($sublimation['location_name']))
                                                                                            <div class="bg-white p-2 rounded">
                                                                                                <span class="text-gray-600 dark:text-slate-400">Localização:</span>
                                                                                                <span class="font-medium text-indigo-900 ml-1">{{ $sublimation['location_name'] }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if(isset($sublimation['size_name']))
                                                                                            <div class="bg-white p-2 rounded">
                                                                                                <span class="text-gray-600 dark:text-slate-400">Tamanho:</span>
                                                                                                <span class="font-medium text-indigo-900 ml-1">{{ $sublimation['size_name'] }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if(isset($sublimation['quantity']))
                                                                                            <div class="bg-white p-2 rounded">
                                                                                                <span class="text-gray-600 dark:text-slate-400">Quantidade:</span>
                                                                                                <span class="font-medium text-indigo-900 ml-1">{{ $sublimation['quantity'] }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if(isset($sublimation['color_count']))
                                                                                            <div class="bg-white p-2 rounded">
                                                                                                <span class="text-gray-600 dark:text-slate-400">Cores:</span>
                                                                                                <span class="font-medium text-indigo-900 ml-1">{{ $sublimation['color_count'] }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if(isset($sublimation['unit_price']))
                                                                                            <div class="bg-white p-2 rounded">
                                                                                                <span class="text-gray-600 dark:text-slate-400">Preço Unit.:</span>
                                                                                                <span class="font-medium text-indigo-900 ml-1">R$ {{ number_format((float)$sublimation['unit_price'], 2, ',', '.') }}</span>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    @if(isset($sublimation['files']) && is_array($sublimation['files']) && !empty($sublimation['files']))
                                                                                        <div class="mt-2 bg-white p-2 rounded">
                                                                                            <span class="text-xs text-gray-600">📎 Arquivos: </span>
                                                                                            <span class="text-xs text-gray-900 font-medium">{{ count($sublimation['files']) }} arquivo(s)</span>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                @else
                                                                    {{-- Fallback quando não há sublimations detalhadas --}}
                                                                    <div class="bg-white rounded-lg p-3 border border-indigo-200 text-center">
                                                                        <p class="text-xs text-gray-600">
                                                                            <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 rounded-full mr-2">
                                                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                                </svg>
                                                                            </span>
                                                                            Item configurado com personalização
                                                                        </p>
                                                                        <p class="text-xs text-gray-400 mt-2 italic">Os detalhes completos da personalização aparecerão em edições futuras</p>
                                                                    </div>
                                                                @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="bg-white dark:bg-slate-800 rounded-md p-2 text-xs text-gray-600">
                                                                <span class="font-medium">Item #{{ $itemId }}:</span>
                                                                <span class="ml-2">{{ $itemChange }}</span>
                                                </div>
                                            @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Seção: Pagamentos --}}
                                    @if(isset($changes['payments']) && !empty($changes['payments']))
                                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/50 dark:to-emerald-950/50 rounded-lg p-4 border border-green-200 dark:border-green-800">
                                            <div class="flex items-center space-x-2 mb-3">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                <h5 class="text-sm font-semibold text-green-900 dark:text-green-300">Pagamento</h5>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach($changes['payments'] as $paymentIndex => $paymentChanges)
                                                    @if(is_array($paymentChanges))
                                                        @foreach($paymentChanges as $field => $change)
                                                            @if(is_array($change) && isset($change['old']) && isset($change['new']))
                                                            <div class="flex items-start justify-between bg-white dark:bg-slate-800 rounded-md p-2 text-xs">
                                                                <span class="font-medium text-gray-700 dark:text-slate-300 mr-3">{{ getFieldLabel($field) }}:</span>
                                                                <div class="text-right flex-1">
                                                                    <div class="text-red-600 line-through">{!! formatChangeValue($change['old'], $field) !!}</div>
                                                                    <div class="text-green-600 font-medium">→ {!! formatChangeValue($change['new'], $field) !!}</div>
                                                                </div>
                                                            </div>
                                                            @else
                                                                {{-- Fallback para formato antigo --}}
                                                                <div class="bg-white dark:bg-slate-800 rounded-md p-2 text-xs text-gray-600">
                                                                    <span class="font-medium">{{ getFieldLabel($field) }}:</span>
                                                                    <span class="ml-2">{{ is_array($change) ? json_encode($change) : $change }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{-- Fallback para formato muito antigo --}}
                                                        <div class="bg-white dark:bg-slate-800 rounded-md p-2 text-xs text-gray-600">
                                                            {{ is_array($paymentChanges) ? json_encode($paymentChanges) : $paymentChanges }}
                                    </div>
                                                    @endif
                                    @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    </div>
                                    @endif
                                </div>
                            </details>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-2">Nenhuma edição registrada</p>
                <p class="text-xs text-gray-400 dark:text-slate-500">O histórico de edições aparecerá aqui</p>
            </div>
        @endif
    </div>
</div>
