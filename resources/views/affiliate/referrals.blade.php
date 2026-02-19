@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Indicações</h1>
            <p class="text-gray-500 dark:text-gray-400">Clientes indicados por você</p>
        </div>
        <a href="{{ route('affiliate.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold">Voltar ao painel</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Plano</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($referrals as $tenant)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $tenant->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $tenant->currentPlan?->name ?? 'Sem plano' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold {{ $tenant->status === 'active' ? 'text-emerald-600' : 'text-gray-500' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $tenant->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Nenhuma indicação encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($referrals->hasPages())
        <div>{{ $referrals->links() }}</div>
    @endif
</div>
@endsection
