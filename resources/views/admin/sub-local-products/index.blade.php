@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Produtos Sublimação Local</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('admin.sub-local-products.create') }}" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Novo Produto</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
        <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">Catálogo de Produtos ({{ $products->count() }})</h2>
        </header>
        <div class="p-3">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Imagem</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Nome</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Categoria</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Preço</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Status</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Ações</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($products as $product)
                            <tr>
                                <td class="p-2 whitespace-nowrap">
                                    @if($product->image)
                                        <img 
                                            src="{{ asset('storage/' . $product->image) }}" 
                                            alt="{{ $product->name }}" 
                                            class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                                            onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%239ca3af%22 stroke-width=%222%22><rect x=%223%22 y=%223%22 width=%2218%22 height=%2218%22 rx=%222%22/><circle cx=%228.5%22 cy=%228.5%22 r=%221.5%22/><path d=%22M21 15l-5-5L5 21%22/></svg>'; this.classList.add('bg-gray-100', 'dark:bg-gray-700', 'p-2');"
                                        >
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $product->name }}</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        {{ ucfirst($product->category) }}
                                    </span>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left font-bold text-indigo-600">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    @if($product->is_active)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Ativo</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inativo</span>
                                    @endif
                                </td>
                                <td class="p-2 whitespace-nowrap text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.sub-local-products.edit', $product->id) }}" class="text-indigo-500 hover:text-indigo-700">Editar</a>
                                        <form action="{{ route('admin.sub-local-products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-2 py-8 text-center text-gray-500">Nenhum produto cadastrado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
