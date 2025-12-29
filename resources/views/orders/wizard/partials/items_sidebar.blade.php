<div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800 sticky top-6">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-slate-800">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Itens do Pedido</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ $order->items->count() }} item(s)</p>
    </div>
    
    <div class="p-5 max-h-[600px] overflow-y-auto">
        @if($order->items->count() > 0)
            <div class="space-y-3">
                @foreach($order->items as $index => $item)
                <div class="p-4 bg-gray-50 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-600 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Item {{ $index + 1 }}</span>
                        <div class="flex gap-1">
                            <button type="button" onclick="togglePin({{ $item->id }})" class="p-1 {{ $item->is_pinned ? 'text-yellow-500 hover:text-yellow-600' : 'text-gray-400 hover:text-yellow-500' }} transition-colors" title="{{ $item->is_pinned ? 'Desafixar do topo' : 'Fixar no topo' }}">
                                <svg class="w-4 h-4" fill="{{ $item->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </button>
                            <button type="button" onclick="editItem({{ $item->id }})" class="p-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors" title="Editar item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button type="button" onclick="openDeleteModal({{ $item->id }})" class="p-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Remover item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-1 text-xs text-gray-600 dark:text-slate-400">
                        <div class="flex justify-between"><span>Personalização:</span><span class="font-medium text-gray-900 dark:text-white">{{ $item->print_type }}</span></div>
                        <div class="flex justify-between"><span>Tecido:</span><span class="font-medium text-gray-900 dark:text-white">{{ $item->fabric }}</span></div>
                        <div class="flex justify-between"><span>Cor:</span><span class="font-medium text-gray-900 dark:text-white">{{ $item->color }}</span></div>
                        <div class="flex justify-between"><span>Quantidade:</span><span class="font-medium text-gray-900 dark:text-white">{{ $item->quantity }} pç</span></div>
                        <div class="flex justify-between"><span>Custo Un.:</span><span class="font-medium text-red-600 dark:text-red-400">R$ {{ number_format($item->unit_cost ?? 0, 2, ',', '.') }}</span></div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-slate-700">
                            <span class="font-semibold">Total:</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-4 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">Subtotal:</span>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($order->items->sum('total_price'), 2, ',', '.') }}</span>
                </div>
            </div>

            <form method="POST" action="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" class="mt-4">
                @csrf
                <input type="hidden" name="action" value="finish">
                <button type="submit" class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all text-sm">
                    Finalizar e Prosseguir →
                </button>
            </form>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-slate-400">Nenhum item adicionado</p>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Preencha o formulário para adicionar</p>
            </div>
        @endif
    </div>
</div>
