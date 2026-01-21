@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="self-start md:self-auto">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Orçamentos</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie todos os orçamentos</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <a href="{{ route('budget.quick-create') }}" 
               class="flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-md hover:from-emerald-600 hover:to-teal-700 transition shadow-sm font-medium"
               style="color: #fff !important;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Orçamento Rápido
            </a>
            <a href="{{ route('budget.start') }}" 
               class="flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 rounded-md hover:bg-indigo-700 transition shadow-sm"
               style="color: #fff !important;">
                + Novo Orçamento
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filtros -->
    <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 mb-2 md:p-6" x-data="{ filtersOpen: window.innerWidth >= 768 }">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center md:hidden" @click="filtersOpen = !filtersOpen">
            <h3 class="font-medium text-gray-700 dark:text-gray-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                Filtros
            </h3>
            <button class="text-gray-500 dark:text-gray-400 focus:outline-none">
                <svg class="w-5 h-5 transform transition-transform duration-200" :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>
        
        <div x-show="filtersOpen" x-transition x-cloak class="p-4 md:p-0">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Número, cliente..."
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                            <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>Todos</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirado</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="md:col-span-2 flex flex-col md:flex-row gap-2">
                        <button type="submit" 
                                class="w-full md:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                style="color: white !important;">
                            Filtrar
                        </button>
                        <a href="{{ route('budget.index') }}" 
                           class="w-full md:w-auto px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-center transition">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista Mobile -->
    <div class="space-y-4 md:hidden">
        @php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'approved' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
                'expired' => 'bg-gray-200 text-gray-800',
            ];
            $statusLabels = [
                'pending' => 'Pendente',
                'approved' => 'Aprovado',
                'rejected' => 'Rejeitado',
                'expired' => 'Expirado',
            ];
        @endphp
        @forelse($budgets as $budget)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 relative overflow-hidden">
            <div class="pl-1">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400">#{{ str_pad($budget->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                            @if($budget->is_quick)
                                {{ $budget->contact_name ?? 'Cliente' }}
                                <span class="ml-1 px-1.5 py-0.5 text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded">⚡ Rápido</span>
                            @else
                                {{ $budget->client->name ?? 'Cliente' }}
                            @endif
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Criado em {{ $budget->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right space-y-1">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md whitespace-nowrap {{ $statusColors[$budget->status] ?? 'bg-gray-200 text-gray-800' }}">
                            {{ $statusLabels[$budget->status] ?? ucfirst($budget->status) }}
                        </span>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Validade: {{ $budget->valid_until->format('d/m/Y') }}</div>
                    </div>
                </div>

                <div class="space-y-2 mb-3">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="font-semibold text-gray-900 dark:text-white">R$ {{ number_format($budget->total, 2, ',', '.') }}</span>
                    </div>
                    @if($budget->order_id || $budget->order_number)
                    <div class="flex items-center text-sm text-indigo-600 dark:text-indigo-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6" /></svg>
                        @if($budget->order_id)
                            Pedido #{{ str_pad($budget->order_id, 6, '0', STR_PAD_LEFT) }}
                        @else
                            Pedido #{{ $budget->order_number }}
                        @endif
                    </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('budget.show', $budget->id) }}" class="w-full sm:w-auto text-center py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium rounded-lg">
                        Ver detalhes
                    </a>
                    <a href="{{ route('budget.pdf', $budget->id) }}" class="w-full sm:w-auto text-center py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg">
                        PDF
                    </a>
                    @if($budget->status === 'approved')
                    <a href="{{ route('budget.convert-to-order', $budget->id) }}" class="w-full sm:w-auto text-center py-2 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm font-medium rounded-lg">
                        Converter
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-10 bg-white dark:bg-gray-800 rounded-xl">
            <p class="text-gray-500 dark:text-gray-400">Nenhum orçamento encontrado.</p>
        </div>
        @endforelse
    </div>

    <!-- Tabela Desktop -->
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Validade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $statusColorsDesktop = [
                            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                            'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                            'expired' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                        ];
                        $statusLabelsDesktop = [
                            'pending' => 'Pendente',
                            'approved' => 'Aprovado',
                            'rejected' => 'Rejeitado',
                            'expired' => 'Expirado',
                        ];
                    @endphp
                    @forelse($budgets as $budget)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $budget->budget_number }}
                            @if($budget->order_id)
                            <a href="{{ route('orders.show', $budget->order_id) }}" 
                               class="block text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline mt-1 transition-colors">
                                Pedido: #{{ str_pad($budget->order_id, 6, '0', STR_PAD_LEFT) }}
                            </a>
                            @elseif($budget->order_number)
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Pedido: #{{ $budget->order_number }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            @if($budget->is_quick)
                                {{ $budget->contact_name ?? 'Contato' }}
                                <span class="ml-1 px-1.5 py-0.5 text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded">⚡</span>
                            @else
                                {{ $budget->client->name ?? 'Cliente' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $budget->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $budget->valid_until->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            R$ {{ number_format($budget->total, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColorsDesktop[$budget->status] ?? '' }}">
                                {{ $statusLabelsDesktop[$budget->status] ?? $budget->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                            <a href="{{ route('budget.show', $budget->id) }}" 
                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                Ver
                            </a>
                            <a href="{{ route('budget.pdf', $budget->id) }}" 
                               class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                PDF
                            </a>
                            @if($budget->status === 'approved')
                            <a href="{{ route('budget.convert-to-order', $budget->id) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                Converter
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Nenhum orçamento encontrado.
                            <a href="{{ route('budget.start') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">
                                Criar primeiro orçamento
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    @if($budgets->hasPages())
    <div class="pb-4">
        {{ $budgets->links() }}
    </div>
    @endif
</div>
@endsection
