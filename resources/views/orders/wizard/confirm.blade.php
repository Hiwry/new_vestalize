@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header Minimalista -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Confirmação do Pedido</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Revise os detalhes antes de confirmar</p>
            </div>
            <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-sm font-medium rounded-full">
                Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
            </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-4">
            <div class="bg-indigo-600 h-1 rounded-full w-full"></div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
        <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- Cliente -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Cliente</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Nome</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $order->client->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Telefone</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $order->client->phone_primary }}</span>
                    </div>
                    @if($order->client->email)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">E-mail</span>
                        <span class="text-gray-900 dark:text-white font-medium truncate block">{{ $order->client->email }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Itens do Pedido</h2>
                
                <div class="space-y-3">
                    @foreach($order->items as $index => $item)
                    @php
                        $isSubLocal = $item->print_type === 'Sublimação Local';
                        $itemSizes = is_array($item->sizes) ? $item->sizes : (is_string($item->sizes) ? json_decode($item->sizes, true) : []);
                        $itemSizes = $itemSizes ?? [];
                        $personalizacaoSubtotal = $item->sublimations->sum('final_price');
                        $itemTotal = ($item->unit_price * $item->quantity) + $personalizacaoSubtotal;
                    @endphp
                    <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">ITEM {{ $index + 1 }}</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">R$ {{ number_format($itemTotal, 2, ',', '.') }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-3">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Processo</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->print_type }}</span>
                            </div>
                            @if($isSubLocal)
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Modelo</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->model }}</span>
                            </div>
                            @else
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Tecido</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->fabric }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Cor</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->color }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">Quantidade</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $item->quantity }} un</span>
                            </div>
                        </div>

                        <!-- Tamanhos -->
                        <div class="flex flex-wrap gap-2">
                            @php
                                $availableSizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'un'];
                            @endphp
                            @foreach($availableSizes as $size)
                                @php
                                    $qty = $itemSizes[$size] ?? $itemSizes[strtolower($size)] ?? $itemSizes[strtoupper($size)] ?? 0;
                                    $qty = (int)$qty;
                                @endphp
                                @if($qty > 0)
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium rounded">
                                    {{ $size }}: {{ $qty }}
                                </span>
                                @endif
                            @endforeach
                        </div>

                        <!-- Personalizações -->
                        @if($item->sublimations->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mb-2">Personalizações:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($item->sublimations as $sub)
                                @php
                                    $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                                    $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APP';
                                @endphp
                                <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-medium rounded">
                                    {{ $locationName }} - R$ {{ number_format($sub->final_price, 2, ',', '.') }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagamento -->
            @if($payment && $payment->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Pagamento</h2>
                <div class="space-y-2">
                    @foreach($payment as $paymentItem)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            @php
                                $method = strtolower($paymentItem->payment_method);
                                $icon = 'fa-money-bill-wave';
                                if(str_contains($method, 'pix')) $icon = 'fa-brands fa-pix';
                                elseif(str_contains($method, 'cartão') || str_contains($method, 'credito')) $icon = 'fa-credit-card';
                            @endphp
                            <i class="fa-solid {{ $icon }} text-gray-400"></i>
                            <span class="text-gray-700 dark:text-gray-300 capitalize">{{ $paymentItem->payment_method }}</span>
                        </div>
                        <span class="text-gray-900 dark:text-white font-medium">R$ {{ number_format($paymentItem->entry_amount, 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                    <span class="text-sm text-amber-700 dark:text-amber-300">Nenhum pagamento registrado</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Checkout (Direita) -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resumo</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                        <span class="text-gray-900 dark:text-white">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    
                    @if($order->delivery_fee > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Entrega</span>
                        <span class="text-gray-900 dark:text-white">R$ {{ number_format($order->delivery_fee, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    @if($order->discount > 0)
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>Desconto</span>
                        <span>- R$ {{ number_format($order->discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between">
                            <span class="text-gray-900 dark:text-white font-semibold">Total</span>
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.finalize') : route('orders.wizard.finalize') }}" id="finalize-form" class="mt-6" enctype="multipart/form-data">
                    @csrf
                    
                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg cursor-pointer mb-4">
                        <input type="checkbox" name="is_event" value="1" {{ old('is_event', ($order->is_event ?? false)) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white block">Prioridade Evento</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Produção acelerada</span>
                        </div>
                    </label>

                    <button type="submit" id="finalize-btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <span id="finalize-text">Confirmar Pedido</span>
                        <span id="finalize-loading" class="hidden">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>Processando...
                        </span>
                    </button>
                    
                    <a href="{{ request()->routeIs('orders.edit.*') ? route('orders.edit.payment') : route('orders.wizard.payment') }}" 
                       class="block text-center text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 mt-3 transition">
                        ← Voltar para Pagamento
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeConfirmModal()"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Confirmar Pedido?</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">O pedido será enviado para produção.</p>
        <div class="flex gap-3">
            <button onclick="closeConfirmModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancelar
            </button>
            <button onclick="confirmFinalize()" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Confirmar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let formSubmitted = false;

document.getElementById('finalize-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!formSubmitted) {
        document.getElementById('confirmModal').classList.remove('hidden');
        document.getElementById('confirmModal').classList.add('flex');
    }
});

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.getElementById('confirmModal').classList.remove('flex');
}

function confirmFinalize() {
    formSubmitted = true;
    closeConfirmModal();
    
    const btn = document.getElementById('finalize-btn');
    const text = document.getElementById('finalize-text');
    const loading = document.getElementById('finalize-loading');
    
    btn.disabled = true;
    btn.classList.add('opacity-75');
    text.classList.add('hidden');
    loading.classList.remove('hidden');
    
    document.getElementById('finalize-form').submit();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeConfirmModal();
});
</script>
@endpush
@endsection
