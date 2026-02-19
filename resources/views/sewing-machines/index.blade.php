@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Máquinas de Costura</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerenciamento de ativos e equipamentos</p>
        </div>
        @if(!Auth::user()->isVendedor())
        <div>
            <a href="{{ route('sewing-machines.create') }}" 
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Máquina
            </a>
        </div>
        @endif
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('sewing-machines.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, código, serial..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loja</label>
                <select name="store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todas</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg text-sm">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Manutenção</option>
                    <option value="broken" {{ request('status') == 'broken' ? 'selected' : '' }}>Quebrada</option>
                    <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Descartada</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">Filtrar</button>
            </div>
        </form>
    </div>

    {{-- Tabela --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Máquina / Descrição</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Loja</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Responsável</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Serial</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Financeiro</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($machines as $machine)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $machine->internal_code ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                <div class="font-medium">{{ $machine->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $machine->brand }} {{ $machine->model }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $machine->store->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $machine->worker_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $machine->serial_number ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                <div>R$ {{ number_format($machine->purchase_price ?? 0, 2, ',', '.') }}</div>
                                @if($machine->invoice_number)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">NF: {{ $machine->invoice_number }}</div>
                                @endif
                                @if($machine->purchase_date)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $machine->purchase_date->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $machine->status_color }}">
                                    {{ $machine->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    @if(!Auth::user()->isVendedor())
                                    <a href="{{ route('sewing-machines.edit', $machine) }}" 
                                       class="p-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 rounded transition"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p>Nenhuma máquina encontrada</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($machines->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $machines->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
