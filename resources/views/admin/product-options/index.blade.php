@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Opções de Produtos</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie as opções de personalização dos produtos</p>
    </div>
</div>



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
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors"
                   style="color: white !important;">
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
                            <th class="w-10 px-6 py-3"></th> <!-- Drag Handle -->
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                            <!-- Preço removido para a maioria, mas exibido para tipo_corte, detalhe e gola -->
                            @if(in_array($type, ['tipo_corte', 'detalhe', 'gola']))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Preço</th>
                            @endif
                            @if(in_array($type, ['tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola']))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pai</th>
                            @endif
                            <!-- Ordem removida do header pois é visual (drag) -->
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-options" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($options as $option)
                            <tr data-id="{{ $option->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition group">
                                <td class="px-6 py-4 cursor-move drag-handle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($option->type === 'cor' && $option->color_hex)
                                            <span class="w-6 h-6 rounded-full border border-gray-200 dark:border-gray-600 mr-3 shadow-sm" style="background-color: {{ $option->color_hex }}"></span>
                                        @endif
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $option->name }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 sm:hidden">Ordem: {{ $option->order }}</div>
                                </td>
                                <!-- Preço exibido para tipo_corte, detalhe e gola -->
                                @if(in_array($type, ['tipo_corte', 'detalhe', 'gola']))
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        R$ {{ number_format($option->price, 2, ',', '.') }}
                                    </td>
                                @endif
                                @if(in_array($type, ['tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola']))
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            @if($option->parents->count() > 0)
                                                @foreach($option->parents as $parent)
                                                    <span class="inline-block px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded mr-1 mb-1">
                                                        {{ $parent->name }}
                                                    </span>
                                                @endforeach
                                            @elseif($option->parent)
<span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded mr-1 mb-1">
                                                    {{ $option->parent->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                <!-- Ordem visual removida -->
                                <td class="px-6 py-4 whitespace-nowrap">
@push('styles')
<style>
    .custom-toggle-track {
        height: 1.75rem; /* 28px */
        width: 3.5rem;   /* 56px */
        border-radius: 9999px;
        border-width: 1px;
        border-color: rgba(255, 255, 255, 0.2);
        background-color: rgba(255, 255, 255, 0.1);
        transition: all .3s cubic-bezier(.4,0,.2,1);
        position: relative;
        cursor: pointer;
        box-sizing: border-box;
    }
    
    .custom-toggle-thumb {
        position: absolute;
        top: 2px;
        left: 2px;
        height: calc(1.75rem - 6px); /* 22px */
        width: calc(1.75rem - 6px);  /* 22px */
        border-radius: 9999px;
        background-color: #ffffff;
        transition: all .3s cubic-bezier(.4,0,.2,1);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    input:checked + .custom-toggle-track {
        background-color: #7c3aed; /* --primary provided by user */
        border-color: #7c3aed;
    }

    input:checked + .custom-toggle-track .custom-toggle-thumb {
        transform: translateX(calc(3.5rem - 1.75rem + 2px)); /* Move strictly to the right */
    }
</style>
@endpush

                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="" class="sr-only toggle-status" data-id="{{ $option->id }}" {{ $option->active ? 'checked' : '' }}>
                                        <div class="custom-toggle-track">
                                            <div class="custom-toggle-thumb"></div>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300 status-label w-12">
                                            {{ $option->active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </label>
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
<button type="submit" class="inline-flex items-center text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors" style="background-color: transparent !important; box-shadow: none !important; border: none !important; padding: 0 !important; color: #dc2626 !important;">
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

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Toggle Status
        document.querySelectorAll('.toggle-status').forEach(toggle => {
            toggle.addEventListener('change', async function() {
                const id = this.dataset.id;
                const label = this.closest('label').querySelector('.status-label');
                
                try {
                    const response = await fetch(`/admin/product-options/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        label.textContent = data.active ? 'Ativo' : 'Inativo';
                    } else {
                        // Revert
                        this.checked = !this.checked;
                        alert('Erro ao atualizar status');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    // Revert
                    this.checked = !this.checked;
                }
            });
        });

        // Sortable - Drag and Drop
        var el = document.getElementById('sortable-options');
        var sortable = Sortable.create(el, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function (evt) {
                const rows = document.querySelectorAll('#sortable-options tr');
                const ids = Array.from(rows).map(row => row.dataset.id);
                
                fetch('{{ route("admin.product-options.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                }).then(response => {
                    if (!response.ok) {
                        alert('Erro ao salvar a nova ordem.');
                    }
                });
            },
        });
    </script>
@endsection
