@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Tipos de Tecido do Estoque</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Defina o preço base por tipo de tecido usado nas peças de tecido e no estoque.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('fabric-pieces.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Peças de Tecido
            </a>
            <a href="{{ route('stock-fabrics.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Novo tipo
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @forelse($groupedFabrics as $parentName => $fabrics)
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $parentName }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $fabrics->count() }} tipo(s) vinculado(s)</p>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Peças vinculadas: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $fabrics->sum('fabric_type_pieces_count') }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50/70 dark:bg-gray-900/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Preço Base</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Peças Vinculadas</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ordem</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($fabrics as $fabric)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $fabric->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">R$ {{ number_format((float) $fabric->price, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $fabric->fabric_type_pieces_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $fabric->order }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $fabric->active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $fabric->active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('stock-fabrics.edit', $fabric->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Editar</a>
                                        <form action="{{ route('stock-fabrics.destroy', $fabric->id) }}" method="POST" onsubmit="return confirm('Deseja excluir este tecido de estoque?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum tipo de tecido cadastrado.</div>
        </div>
    @endforelse
</div>
@endsection