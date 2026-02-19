@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition">
        ← Voltar para {{ $affiliate->name }}
    </a>
</div>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Comissões de {{ $affiliate->name }}</h1>
</div>

@if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-4">
        <form method="GET" class="flex gap-4 items-center">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="">Todos os status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendentes</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovadas</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagas</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
            </select>
        </form>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        @if($commissions->count() > 0)
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pagamento</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Taxa</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Comissão</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($commissions as $commission)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $commission->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $commission->tenant->name ?? 'Removido' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right">
                                R$ {{ number_format($commission->payment_amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                {{ number_format($commission->rate, 1) }}%
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600 dark:text-green-400 text-right">
                                R$ {{ number_format($commission->amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($commission->status === 'pending')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Pendente</span>
                                @elseif($commission->status === 'approved')
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">Aprovada</span>
                                @elseif($commission->status === 'paid')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Paga</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Cancelada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                @if($commission->status === 'pending')
                                    <form action="{{ route('admin.affiliates.commissions.approve', $commission) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-900 mr-2">Aprovar</button>
                                    </form>
                                    <form action="{{ route('admin.affiliates.commissions.cancel', $commission) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">Cancelar</button>
                                    </form>
                                @elseif($commission->status === 'approved')
                                    <form action="{{ route('admin.affiliates.commissions.pay', $commission) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Marcar Paga</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6">{{ $commissions->links() }}</div>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400 py-8">Nenhuma comissão encontrada.</p>
        @endif
    </div>
</div>
@endsection
