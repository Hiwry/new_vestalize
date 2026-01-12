@props([
    'amount' => 0,
    'orderId' => null,
    'showCard' => true,
])

@php
    $pixService = app(\App\Services\PixService::class);
    $txId = $orderId ? 'PED' . str_pad($orderId, 6, '0', STR_PAD_LEFT) : null;
    $pix = $pixService->generate($amount, $txId);
@endphp

@if($showCard)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-md mx-auto">
@endif
    <div class="text-center">
        {{-- Header --}}
        <div class="mb-4">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full mb-2">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pagamento via PIX</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Escaneie o QR Code ou copie o código</p>
        </div>

        {{-- Valor --}}
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
            <span class="text-sm text-green-600 dark:text-green-400">Valor a pagar</span>
            <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $pix['formatted_amount'] }}</p>
        </div>

        {{-- QR Code --}}
        <div class="mb-4 p-4 bg-white rounded-lg inline-block border-2 border-gray-200">
            <img src="{{ $pix['qrcode'] }}" alt="QR Code PIX" class="w-48 h-48 mx-auto">
        </div>

        {{-- Dados do PIX --}}
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
            <p><span class="font-medium">Chave CPF:</span> {{ $pix['pix_key'] }}</p>
            <p><span class="font-medium">Nome:</span> {{ $pix['merchant_name'] }}</p>
            <p><span class="font-medium">Cidade:</span> {{ $pix['merchant_city'] }}</p>
        </div>

        {{-- Copia e Cola --}}
        <div class="mt-4" x-data="{ copied: false }">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                PIX Copia e Cola
            </label>
            <div class="relative">
                <input 
                    type="text" 
                    id="pix-payload-{{ $orderId ?? rand() }}"
                    value="{{ $pix['payload'] }}" 
                    readonly
                    class="w-full px-3 py-2 pr-20 text-xs font-mono bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-green-500"
                >
                <button 
                    type="button"
                    @click="
                        navigator.clipboard.writeText($el.previousElementSibling.value);
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                    class="absolute right-1 top-1 px-3 py-1 text-xs font-medium rounded-md transition-colors"
                    :class="copied ? 'bg-green-500 text-white' : 'bg-green-600 hover:bg-green-700 text-white'"
                >
                    <span x-show="!copied">Copiar</span>
                    <span x-show="copied">✓ Copiado!</span>
                </button>
            </div>
        </div>

        {{-- Instruções --}}
        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-left">
            <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-2">Como pagar:</h4>
            <ol class="text-xs text-yellow-700 dark:text-yellow-400 space-y-1 list-decimal list-inside">
                <li>Abra o app do seu banco</li>
                <li>Escolha pagar com PIX</li>
                <li>Escaneie o QR Code ou cole o código</li>
                <li>Confirme os dados e finalize</li>
            </ol>
        </div>
    </div>
@if($showCard)
</div>
@endif
