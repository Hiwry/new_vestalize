<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach($products as $product)
        <div onclick="openModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->base_price }}')" 
             class="group bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 cursor-pointer transition-all hover:scale-[1.02] hover:border-pink-300 dark:hover:border-pink-700">
            
            <div class="mb-3 aspect-square rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            
            <h3 class="font-bold text-gray-900 dark:text-white leading-tight mb-1">{{ $product->name }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 capitalize">{{ $product->type }}</p>
            
            <div class="flex justify-between items-center">
                <span class="font-extrabold text-pink-600 dark:text-pink-400">R$ {{ number_format($product->base_price, 2, ',', '.') }}</span>
                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 group-hover:bg-pink-600 group-hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($products->isEmpty())
    <div class="col-span-12 text-center py-12 text-gray-500">
        <p>Nenhum produto encontrado nesta categoria.</p>
    </div>
@endif
