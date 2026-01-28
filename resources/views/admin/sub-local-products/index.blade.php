@extends('layouts.admin')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3 tracking-tight">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl text-white shadow-lg shadow-indigo-200 dark:shadow-none transform rotate-3">
                    <i class="fa-solid fa-gift text-2xl text-white" style="color: #ffffff !important;"></i>
                </div>
                Brindes e Outros
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg font-medium ml-1">
                Gerencie o catálogo de produtos personalizados e brindes.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
            <a href="{{ route('settings.customized-products') }}" class="px-5 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-arrow-left text-indigo-500"></i>
                Voltar
            </a>
            <a href="{{ route('admin.sub-local-products.create') }}" 
               style="color: #ffffff !important;"
               class="px-6 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all duration-300 font-bold text-sm flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-plus text-white" style="color: #ffffff !important;"></i>
                Novo Produto
            </a>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700">
        <header class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list-ul text-indigo-500"></i>
                Catálogo de Produtos
                <span class="ml-2 px-2.5 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-black">
                    {{ $products->count() }}
                </span>
            </h2>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Imagem</th>
                        <th scope="col" class="px-6 py-4 font-bold">Produto</th>
                        <th scope="col" class="px-6 py-4 font-bold">Categoria</th>
                        <th scope="col" class="px-6 py-4 font-bold">Preço Base</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-right font-bold">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="relative w-14 h-14 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm transition-transform group-hover:scale-105">
                                    @if($product->image)
                                        <img 
                                            src="{{ asset('storage/' . $product->image) }}" 
                                            alt="{{ $product->name }}" 
                                            class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%239ca3af%22 stroke-width=%221%22><rect x=%223%22 y=%223%22 width=%2218%22 height=%2218%22 rx=%224%22/><path d=%22M21 15l-5-5L5 21%22/><circle cx=%228.5%22 cy=%228.5%22 r=%221.5%22/></svg>'; this.classList.add('p-3');"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-image text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white text-base">{{ $product->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5 line-clamp-1 truncate max-w-[200px]" title="{{ $product->description }}">{{ $product->description ?? 'Sem descrição' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                    @switch($product->category)
                                        @case('vestuario') <i class="fa-solid fa-shirt mr-1"></i> Vestuário @break
                                        @case('canecas') <i class="fa-solid fa-mug-hot mr-1"></i> Canecas @break
                                        @case('acessorios') <i class="fa-solid fa-hat-wizard mr-1"></i> Acessórios @break
                                        @default <i class="fa-solid fa-box mr-1"></i> Diversos
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-indigo-600 dark:text-indigo-400 font-black text-base">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.sub-local-products.edit', $product->id) }}" 
                                       class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-indigo-500 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:border-indigo-200 transition-all shadow-sm active:scale-90"
                                       title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            onclick="openDeleteModal({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                            class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-red-500 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-200 transition-all shadow-sm active:scale-90"
                                            title="Excluir">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900/50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-300">
                                    <i class="fa-solid fa-box-open text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nenhum produto encontrado</h3>
                                <p class="text-gray-500 dark:text-gray-400 max-w-xs mx-auto">Comece cadastrando seu primeiro produto personalizado no botão "Novo Produto".</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Exclusão -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center border border-gray-100 dark:border-gray-700 transform transition-all scale-100">
        <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6 text-red-500">
             <i class="fa-solid fa-triangle-exclamation text-4xl"></i>
        </div>
        
        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Excluir Produto?</h3>
        <p id="delete-message" class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
            Esta ação removerá permanentemente o produto selecionado.
        </p>
        
        <form id="delete-form" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" @click="closeDeleteModal()" onclick="closeDeleteModal()" class="flex-1 px-4 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancelar
            </button>
            <button type="submit" 
                    style="color: #ffffff !important;"
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 dark:shadow-none transition-colors">
                Sim, excluir
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openDeleteModal(id, name) {
    const modal = document.getElementById('delete-modal');
    const form = document.getElementById('delete-form');
    const message = document.getElementById('delete-message');
    
    form.action = `/admin/sub-local-products/${id}`;
    message.innerHTML = `Tem certeza que deseja excluir o produto <br><strong class="text-gray-900 dark:text-white">"${name}"</strong>?`;
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('div').classList.add('scale-100');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.querySelector('div').classList.remove('scale-100');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Fechar ao clicar fora
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush

@endsection
