@if(empty($cart))
    <div class="flex flex-col items-center justify-center h-48 text-gray-400 dark:text-gray-500">
        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="text-sm">Seu carrinho está vazio</p>
    </div>
@else
    @foreach($cart as $item)
    <div class="group bg-white dark:bg-gray-800 rounded-xl p-3 shadow-sm border border-gray-100 dark:border-gray-700 hover:border-pink-500 dark:hover:border-pink-500 transition-colors">
        <div class="flex justify-between items-start mb-2">
            <div class="flex-1 pr-2">
                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm leading-tight">{{ $item['name'] }}</p>
                @if(!empty($item['customization_note']) || !empty($item['client_name']))
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 p-1.5 rounded-lg">
                        @if(!empty($item['client_name']))
                           <p><strong>Nome:</strong> {{ $item['client_name'] }}</p>
                        @endif
                        @if(!empty($item['customization_note']))
                           <p><strong>Obs:</strong> {{ $item['customization_note'] }}</p>
                        @endif
                        @if(!empty($item['addons']))
                           <div class="mt-1 pt-1 border-t border-gray-200 dark:border-gray-600">
                               <p class="font-semibold text-[10px] uppercase text-gray-400">Adicionais:</p>
                               <ul class="space-y-0.5">
                                   @foreach($item['addons'] as $addon)
                                       <li class="flex justify-between">
                                           <span>{{ $addon['name'] }}</span>
                                           <span>+R$ {{ number_format($addon['price'], 2, ',', '.') }}</span>
                                       </li>
                                   @endforeach
                               </ul>
                           </div>
                        @endif
                    </div>
                @endif
            </div>
            <button onclick="removeFromCart('{{ $item['id'] }}')" class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 transition-opacity">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="flex items-end justify-between mt-2">
            <div class="text-xs text-gray-500">
                {{ $item['quantity'] }} x R$ {{ number_format($item['unit_price'], 2, ',', '.') }}
            </div>
            <p class="font-bold text-gray-900 dark:text-white text-sm">
                R$ {{ number_format($item['total_price'], 2, ',', '.') }}
            </p>
        </div>
    </div>
    @endforeach
@endif

<script>
    function removeFromCart(id) {
        fetch('{{ route("personalized.cart.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    }
</script>
