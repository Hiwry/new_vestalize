@extends('layouts.admin')

@push('scripts')
    <script>
        // Função definida ANTES de qualquer outro script
        function finalizeBudget() {
            const form = document.getElementById('finalize-form');
            const btn = document.getElementById('finalize-btn');
            const btnText = document.getElementById('btn-text');
            
            if (!form) {
                console.error('Formulário não encontrado!');
                alert('Erro: Formulário não encontrado');
                return;
            }
            
            // Desabilitar botão
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            if (btnText) {
                btnText.textContent = 'Processando...';
            }
            
            // Usar FETCH ao invés de form.submit()
            const formData = new FormData(form);
            
            // Garantir que o token CSRF está no FormData
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken && !formData.has('_token')) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html',
                },
                credentials: 'same-origin',
                redirect: 'follow'  // Mudei para 'follow' para permitir redirecionamentos automáticos
            })
            .then(async response => {
                console.log('📡 Resposta recebida:', response.status, response.statusText);
                console.log('📍 URL final:', response.url);
                
                // Se foi redirecionado para a página de listagem (sucesso)
                if (response.url.includes('/orcamento') && !response.url.includes('confirmacao')) {
                    console.log('✅ Sucesso! Já na página de destino');
                    window.location.href = response.url;
                    return;
                }
                
                // Se foi bem sucedido (200-299)
                if (response.ok) {
                    console.log('✅ Sucesso! Redirecionando...');
                    // Verificar se há mensagem de sucesso na resposta
                    const text = await response.text();
                    window.location.href = '/orcamento';
                    return;
                }
                
                // Se houve erro, tentar ler a resposta
                const contentType = response.headers.get('content-type');
                let errorMsg = `Erro ${response.status}: ${response.statusText}`;
                
                try {
                    if (contentType && contentType.includes('application/json')) {
                        const json = await response.json();
                        errorMsg = json.message || json.error || errorMsg;
                        console.error('❌ Erro JSON:', json);
                    } else {
                        const text = await response.text();
                        console.error('❌ Resposta completa:', text);
                        
                        // Tentar extrair mensagem de erro do HTML
                        const match = text.match(/<title>(.*?)<\/title>/i);
                        if (match && match[1] !== 'Orçamentos') {
                            errorMsg = match[1];
                        }
                        
                        // Procurar por erros de validação Laravel
                        const errorMatch = text.match(/<div class="alert[^"]*alert-danger[^"]*"[^>]*>(.*?)<\/div>/is);
                        if (errorMatch) {
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = errorMatch[1];
                            errorMsg = tempDiv.textContent.trim();
                        }
                    }
                } catch (e) {
                    console.error('❌ Erro ao processar resposta:', e);
                }
                
                throw new Error(errorMsg);
            })
            .catch(error => {
                console.error('❌ Erro ao finalizar orçamento:', error);
                
                // Se o erro for de rede, pode ser que tenha funcionado mas não conseguimos verificar
                if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                    console.log('⚠️ Erro de rede, verificando se orçamento foi criado...');
                    // Tentar redirecionar de qualquer forma
                    setTimeout(() => {
                        window.location.href = '/orcamento';
                    }, 2000);
                    return;
                }
                
                alert('Erro ao finalizar orçamento:\n\n' + error.message);
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                if (btnText) {
                    btnText.textContent = 'Finalizar Orçamento';
                }
            });
        }
    </script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">4</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600 dark:text-indigo-400">Confirmação</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Etapa 4 de 4</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">100%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                <div class="bg-indigo-600 dark:bg-indigo-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 100%"></div>
            </div>
        </div>

        <!-- Resumo do Orçamento -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Confirmar Orçamento</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Revise as informações antes de finalizar</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Cliente -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Cliente</h3>
                    @php
                        $client = \App\Models\Client::find(session('budget_data.client_id'));
                    @endphp
                    @if($client)
                    <div class="space-y-2 text-sm">
                        <div><span class="text-gray-600 dark:text-gray-400">Nome:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $client->name }}</span></div>
                        <div><span class="text-gray-600 dark:text-gray-400">Telefone:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $client->phone_primary }}</span></div>
                        @if($client->email)
                        <div><span class="text-gray-600 dark:text-gray-400">Email:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $client->email }}</span></div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Personalizações -->
                @php
                    $customizations = session('budget_customizations', []);
                    $addonIds = collect($customizations)->flatMap(function ($custom) {
                        $addons = $custom['addons'] ?? [];
                        return is_array($addons) ? $addons : [];
                    })->filter(function ($addonId) {
                        return is_numeric($addonId);
                    })->unique()->values();
                    $addonsLookup = $addonIds->isNotEmpty()
                        ? \App\Models\SublimationAddon::whereIn('id', $addonIds)->pluck('name', 'id')->toArray()
                        : [];
                @endphp
                @if(!empty($customizations))
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Personalizações</h3>
                    <div class="space-y-3">
                        @foreach($customizations as $index => $custom)
                        @php
                            $quantity = $custom['quantity'] ?? 0;
                            $unitPrice = $custom['unit_price'] ?? 0;
                            $finalPrice = $custom['final_price'] ?? 0;
                            $colorCount = $custom['color_count'] ?? 1;
                            $persType = $custom['personalization_name'] ?? '';
                        @endphp
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded p-3 border border-indigo-100 dark:border-indigo-800 text-xs">
                            <div class="font-medium text-indigo-700 dark:text-indigo-300 mb-2">{{ $persType ?: 'Personalização ' . ($index + 1) }}</div>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Localização:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $custom['location'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Tamanho:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $custom['size'] ?? '-' }}</span>
                                </div>
                                @if(in_array($persType, ['SERIGRAFIA', 'EMBORRACHADO']))
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Cores:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $colorCount }} {{ $colorCount > 1 ? 'aplicações' : 'aplicação' }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Quantidade:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $quantity }} peças</span>
                                </div>
                            </div>

                            @php
                                $addonNames = [];
                                $customAddons = $custom['addons'] ?? [];
                                if (is_array($customAddons)) {
                                    foreach ($customAddons as $addonId) {
                                        $addonNames[] = $addonsLookup[$addonId] ?? $addonId;
                                    }
                                }
                                if (!empty($custom['regata_discount'])) {
                                    $addonNames[] = 'REGATA (desconto)';
                                }
                            @endphp
                            @if(!empty($addonNames))
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Adicionais:</span>
                                    {{ implode(', ', $addonNames) }}
                                </div>
                            @endif
                            
                            <!-- Breakdown de valores -->
                            <div class="border-t border-indigo-200 dark:border-indigo-700 pt-2 space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Valor unitário:</span>
                                    <span class="text-gray-900 dark:text-gray-100 font-medium">R$ {{ number_format($unitPrice, 2, ',', '.') }}</span>
                                </div>
                                @if(in_array($persType, ['SERIGRAFIA', 'EMBORRACHADO']) && $colorCount > 1)
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Com {{ $colorCount }} cores{{ $colorCount >= 3 ? ' (desconto na 3ª+)' : '' }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between pt-1 border-t border-indigo-100 dark:border-indigo-800">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Total:</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold text-sm">R$ {{ number_format($finalPrice, 2, ',', '.') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 italic">
                                    (R$ {{ number_format($unitPrice, 2, ',', '.') }} × {{ $quantity }} peças)
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Itens do Orçamento -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Itens do Orçamento</h3>
                    @php
                        $items = session('budget_items', []);
                        $subtotal = 0;
                    @endphp
                    <div class="space-y-3">
                        @foreach($items as $index => $item)
                        @php
                            $itemQuantity = $item['quantity'] ?? 0;
                            $itemUnitPrice = $item['unit_price'] ?? 0;
                            $itemTotal = $itemQuantity * $itemUnitPrice;
                            
                            // Buscar personalizações deste item
                            $itemPersonalizations = array_filter($customizations, function($pers) use ($index) {
                                return isset($pers['item_index']) && $pers['item_index'] == $index;
                            });
                            
                            // Calcular total de personalizações deste item
                            $itemPersonalizationsTotal = array_sum(array_column($itemPersonalizations, 'final_price'));
                            
                            // Calcular valor unitário com personalização (personalização / quantidade)
                            $personalizationPerPiece = $itemQuantity > 0 ? ($itemPersonalizationsTotal / $itemQuantity) : 0;
                            $unitPriceWithPersonalization = $itemUnitPrice + $personalizationPerPiece;
                            
                            // Total geral do item (costura + personalização)
                            $itemGrandTotal = $itemTotal + $itemPersonalizationsTotal;
                            
                            $subtotal += $itemTotal;
                        @endphp
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                                        <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ $index + 1 }}</span>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['print_type'] ?? 'Item ' . ($index + 1) }}</h4>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Tecido:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $item['fabric'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Cor:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $item['color'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Modelo:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $item['model'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Gola:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $item['collar'] ?? '-' }}</span>
                                </div>
                                @if(!empty($item['detail']))
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Detalhe:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $item['detail'] }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Quantidade:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 block">{{ $itemQuantity }} peças</span>
                                </div>
                            </div>
                            
                            <!-- Breakdown de Valores -->
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Costura (unitário):</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($itemUnitPrice, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Costura (total):</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($itemTotal, 2, ',', '.') }}</span>
                                    </div>
                                    
                                    @if(count($itemPersonalizations) > 0)
                                    <div class="flex justify-between text-indigo-600 dark:text-indigo-400">
                                        <span>Personalização (unitário):</span>
                                        <span class="font-medium">R$ {{ number_format($personalizationPerPiece, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-indigo-600 dark:text-indigo-400">
                                        <span>Personalização (total):</span>
                                        <span class="font-medium">R$ {{ number_format($itemPersonalizationsTotal, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">Unitário Total:</span>
                                        <span class="font-bold text-green-600 dark:text-green-400">R$ {{ number_format($unitPriceWithPersonalization, 2, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-500">
                                        <span class="font-bold text-gray-900 dark:text-gray-100">TOTAL DO ITEM:</span>
                                        <span class="font-bold text-lg text-indigo-600 dark:text-indigo-400">R$ {{ number_format($itemGrandTotal, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!empty($item['notes']))
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Observações:</span>
                                <p class="text-xs text-gray-900 dark:text-gray-100 mt-1">{{ $item['notes'] }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Observações -->
                <form method="POST" action="{{ route('budget.finalize') }}" id="finalize-form">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Observações Gerais (opcional)</label>
                            <textarea name="observations" 
                                      rows="2" 
                                      placeholder="Observações gerais sobre o orçamento..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                                Observações do Vendedor
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">(Acréscimo GG/EXG, Prazo, Pagamento, etc.)</span>
                            </label>
                            <textarea name="admin_notes" 
                                      rows="4" 
                                      placeholder="Ex: Acréscimo de R$ 2,00 para tamanhos GG e EXG&#10;Prazo de entrega: 15 dias úteis&#10;Pagamento: 50% entrada + 50% na entrega"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Estas informações aparecerão no PDF do orçamento</p>
                        </div>
                    </div>
                </form>

                @php
                    // Adicionar valor das personalizações ao subtotal
                    $customizationsTotal = array_sum(array_column($customizations, 'final_price'));
                    $grandTotal = $subtotal + $customizationsTotal;
                @endphp

                <!-- Total -->
                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-md p-4 border border-indigo-200 dark:border-indigo-800/30">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-700 dark:text-gray-300">Subtotal Itens:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        @if($customizationsTotal > 0)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-700 dark:text-gray-300">Personalizações:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">R$ {{ number_format($customizationsTotal, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="border-t border-indigo-200 dark:border-indigo-800/30 pt-2 flex justify-between items-center">
                            <span class="text-base font-semibold text-gray-900 dark:text-gray-100">Valor Total:</span>
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($grandTotal, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Orçamento válido por 15 dias</p>
                </div>
            </div>

            <!-- Botões -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                <div class="flex justify-between">
                    <a href="{{ route('budget.customization') }}" 
                       class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 text-sm font-medium">
                        ← Voltar
                    </a>
                    <button type="button"
                            onclick="event.stopPropagation(); event.preventDefault(); finalizeBudget();"
                            style="padding: 12px 24px; background: #16a34a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; position: relative; z-index: 99999;">
                        <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span id="btn-text">Finalizar Orçamento</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Página carregada');
        });
</script>
@endpush
@endsection
