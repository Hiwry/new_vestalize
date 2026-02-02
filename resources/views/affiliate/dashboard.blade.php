@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Meu Painel de Afiliado</h1>
            <p class="text-gray-500 dark:text-gray-400">Acompanhe suas indicações e desempenho</p>
        </div>
        <a href="{{ route('affiliate.referrals') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
            Ver indicados
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-xs uppercase tracking-widest text-gray-400">Indicações</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_referrals'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ativas: {{ $stats['active_referrals'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-xs uppercase tracking-widest text-gray-400">Cliques</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['clicks'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Conversão: {{ number_format($stats['conversion_rate'], 2, ',', '.') }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-xs uppercase tracking-widest text-gray-400">Ganhos</p>
            <p class="text-2xl font-bold text-emerald-600">R$ {{ number_format($stats['total_earnings'], 2, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pendente: R$ {{ number_format($stats['pending_balance'], 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Seu link de indicação</h2>
        <div class="flex flex-col md:flex-row gap-3">
            <input type="text" readonly value="{{ $referralLink }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
            <button
                type="button"
                onclick="navigator.clipboard.writeText('{{ $referralLink }}')"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg font-semibold dark:bg-gray-700">
                Copiar link
            </button>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Código: <span class="font-mono font-bold">{{ $affiliate->code }}</span></p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Últimos indicados</h2>
            <a href="{{ route('affiliate.referrals') }}" class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold">Ver todos</a>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($affiliate->tenants()->latest()->limit(5)->get() as $tenant)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $tenant->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->email }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->currentPlan?->name ?? 'Sem plano' }}</p>
                        <span class="text-xs font-bold {{ $tenant->status === 'active' ? 'text-emerald-600' : 'text-gray-500' }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum indicado ainda.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
