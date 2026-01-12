@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Opções de Produtos</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie as opções de personalização dos produtos</p>
    </div>
</div>

        @if(session('success'))
<div class="mb-6 p-4 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
            <!-- Tabs -->
<div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px overflow-x-auto">
                    @foreach($types as $key => $label)
                        <a href="{{ route('admin.product-options.index', ['type' => $key]) }}"
                           class="px-6 py-3 text-sm font-medium border-b-2 whitespace-nowrap {{ $type === $key ? 'border-indigo-600 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Header -->
<div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 dark:border-gray-700">
                <div>
<h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $types[$type] }}</h2>
<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie as opções de {{ strtolower($types[$type]) }}</p>
                </div>
                <a href="{{ route('admin.product-options.create', ['type' => $type]) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
</svg>
                    Nova Opção
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
<thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Preço</th>
                            @if(in_array($type, ['tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola']))
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pai</th>
                            @endif
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ordem</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($options as $option)
<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
<div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $option->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        @if($option->price > 0)
<span class="text-green-600 dark:text-green-400 font-medium">+R$ {{ number_format($option->price, 2, ',', '.') }}</span>
                                        @else
<span class="text-gray-400 dark:text-gray-500">R$ 0,00</span>
                                        @endif
                                    </div>
                                </td>
                                @if(in_array($type, ['tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola']))
                                    <td class="px-6 py-4">
<div class="text-sm text-gray-600 dark:text-gray-300">
                                            @if($option->parents->count() > 0)
                                                @foreach($option->parents as $parent)
<span class="inline-block px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded mr-1 mb-1">
                                                        {{ $parent->name }}
                                                    </span>
                                                @endforeach
                                            @else
<span class="text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
<div class="text-sm text-gray-600 dark:text-gray-300">{{ $option->order }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($option->active)
<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            Ativo
                                        </span>
                                    @else
<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.product-options.edit', $option->id) }}"
                                       class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
</svg>
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.product-options.destroy', $option->id) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja remover esta opção?')">
                                        @csrf
                                        @method('DELETE')
<button type="submit" class="inline-flex items-center text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
</svg>
                                            Excluir
                                        </button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
<td colspan="6" class="px-6 py-12 text-center">
<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
</svg>
<div class="text-sm text-gray-500 dark:text-gray-400 mb-3">Nenhuma opção cadastrada</div>
                                    <a href="{{ route('admin.product-options.create', ['type' => $type]) }}"
                                       class="inline-flex items-center px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
</svg>
                                        Criar primeira opção
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($options->hasPages())
<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $options->appends(['type' => $type])->links() }}
                </div>
            @endif
        </div>
@endsection
