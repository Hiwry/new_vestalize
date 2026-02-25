@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Gestão de Assinaturas (Tenants)') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-4 text-sm font-medium">
                <a href="{{ route('admin.affiliates.index') }}" class="text-purple-600 dark:text-purple-400 hover:underline">
                    Gerenciar Afiliados
                </a>
                <a href="{{ route('admin.plans.index') }}" class="text-purple-600 dark:text-purple-400 hover:underline">
                    Gerenciar Planos
                </a>
                <a href="{{ route('admin.tutorials.index') }}" class="text-purple-600 dark:text-purple-400 hover:underline">
                    Gerenciar Tutoriais
                </a>
            </div>
            <a href="{{ route('admin.tenants.create') }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Nova Assinatura
            </a>
        </div>

        <!-- Resumo de Afiliados -->
        @if($affiliates->count() > 0)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Indicações por Afiliado</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($affiliates as $affiliate)
                    <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="block p-4 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-lg hover:shadow-md transition group">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $affiliate->tenants_count }}</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate group-hover:text-purple-600">{{ $affiliate->name }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ $affiliate->code }}</div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente/Empresa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código Loja</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Plano</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Afiliado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vencimento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tenants as $tenant)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                            {{ $tenant->name }}
                                            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="ml-2 text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300" title="Editar">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $tenant->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $tenant->store_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $slug = $tenant->currentPlan ? $tenant->currentPlan->slug : 'basic';
                                            $planColors = [
                                                'start' => 'bg-amber-100 text-amber-800',
                                                'basic' => 'bg-gray-100 text-gray-800',
                                                'pro' => 'bg-blue-100 text-blue-800',
                                                'premium' => 'bg-purple-100 text-purple-800',
                                            ];
                                            $color = $planColors[$slug] ?? 'bg-indigo-100 text-indigo-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                            {{ $tenant->plan_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($tenant->affiliate)
                                            <a href="{{ route('admin.affiliates.show', $tenant->affiliate) }}" class="text-purple-600 dark:text-purple-400 hover:underline">
                                                {{ $tenant->affiliate->name }}
                                            </a>
                                            <span class="text-xs text-gray-400 block">{{ $tenant->affiliate->code }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $isActive = $tenant->isActive();
                                            $isAtrasado = !$isActive && $tenant->status !== 'cancelled';
                                        @endphp
                                        
                                        @if($isAtrasado)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-600 text-white animate-pulse">
                                                Inadimplente
                                            </span>
                                        @elseif($tenant->status === 'active')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($tenant->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                                            <span class="text-blue-500 font-semibold">Trial: {{ $tenant->trial_ends_at->format('d/m/Y') }}</span>
                                        @else
                                            {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d/m/Y') : '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.tenants.edit', $tenant) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all"
                                               title="Editar Cliente">
                                                <i class="fa-solid fa-edit mr-1"></i>
                                                Editar
                                            </a>
                                        
                                            <form action="{{ route('admin.tenants.resend-access', $tenant) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold rounded-lg transition-all"
                                                        onclick="return confirm('Reenviar email de acesso?')">
                                                    <i class="fa-solid fa-paper-plane mr-1 text-[10px]"></i>
                                                    Acesso
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Nenhuma assinatura encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $tenants->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
