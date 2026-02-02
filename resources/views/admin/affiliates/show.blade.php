@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.affiliates.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition">
        ← Voltar para lista
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
@endif

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Coluna Principal -->
    <div class="lg:w-2/3 space-y-6">
        <!-- Informações do Afiliado -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-gray-900/25 sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="h-16 w-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 dark:text-purple-400 font-bold text-2xl">
                                {{ strtoupper(substr($affiliate->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $affiliate->name }}</h1>
                            <p class="text-gray-500 dark:text-gray-400">{{ $affiliate->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.affiliates.edit', $affiliate) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                        Editar
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_referrals'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Indicações</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active_referrals'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ativos</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">R$ {{ number_format($stats['pending_balance'], 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Saldo Pendente</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($stats['total_earnings'], 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Ganho</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimas Comissões -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-gray-900/25 sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Últimas Comissões</h2>
                <a href="{{ route('admin.affiliates.commissions', $affiliate) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    Ver todas →
                </a>
            </div>
            <div class="p-6">
                @if($affiliate->commissions->count() > 0)
                    <div class="space-y-3">
                        @foreach($affiliate->commissions as $commission)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $commission->tenant->name ?? 'Tenant removido' }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $commission->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 dark:text-green-400">
                                        R$ {{ number_format($commission->amount, 2, ',', '.') }}
                                    </p>
                                    @if($commission->status === 'pending')
                                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Pendente</span>
                                    @elseif($commission->status === 'approved')
                                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">Aprovada</span>
                                    @elseif($commission->status === 'paid')
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Paga</span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Cancelada</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">Nenhuma comissão registrada ainda.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Coluna Lateral -->
    <div class="lg:w-1/3 space-y-6">
        <!-- Código do Afiliado -->
        <div class="bg-gradient-to-br from-purple-600 to-indigo-700 overflow-hidden shadow-lg sm:rounded-lg text-white p-6">
            <h3 class="text-lg font-semibold mb-2">Link de Indicação</h3>
            <p class="text-purple-200 text-sm">Compartilhe este link para o cadastro</p>
            <input
                type="text"
                readonly
                value="{{ $referralLink }}"
                class="mt-3 w-full px-3 py-2 rounded bg-white/15 border border-white/20 text-white text-sm"
            >
            <button
                onclick="navigator.clipboard.writeText('{{ $referralLink }}')"
                class="mt-3 w-full bg-white/20 hover:bg-white/30 py-2 px-4 rounded transition text-sm font-medium">
                Copiar link
            </button>
            <p class="text-xs text-purple-200 mt-2">Código: <span class="font-mono font-bold">{{ $affiliate->code }}</span></p>
        </div>

        <!-- Informações -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-gray-900/25 sm:rounded-lg">
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Telefone</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $affiliate->phone ?? 'Não informado' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Taxa de Comissão</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($affiliate->commission_rate, 1) }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    @if($affiliate->status === 'active')
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                            Ativo
                        </span>
                    @else
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            Inativo
                        </span>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cadastrado em</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $affiliate->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Dados Bancários -->
        @if($affiliate->bank_info)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-gray-900/25 sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Dados para Saque</h3>
            </div>
            <div class="p-6 space-y-3">
                @if($affiliate->bank_info['pix'] ?? null)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">PIX</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $affiliate->bank_info['pix'] }}</p>
                    </div>
                @endif
                @if($affiliate->bank_info['bank'] ?? null)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Banco</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $affiliate->bank_info['bank'] }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
