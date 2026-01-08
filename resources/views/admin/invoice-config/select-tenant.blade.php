@extends('layouts.admin')

@section('title', 'Configuração NF-e - Selecionar Empresa')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Configuração NF-e</h1>
        
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium text-amber-800 dark:text-amber-200">Você está logado como Super Admin</p>
                    <p class="text-sm text-amber-600 dark:text-amber-300">Selecione uma empresa para configurar a NF-e.</p>
                </div>
            </div>
        </div>

        @if($tenants->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="font-medium">Nenhuma empresa cadastrada</p>
                <p class="text-sm mt-1">Cadastre uma empresa primeiro para configurar a NF-e.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($tenants as $tenant)
                    <a href="{{ route('admin.invoice-config.editTenant', $tenant->id) }}" 
                       class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:border-indigo-300 dark:hover:border-indigo-700 transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 dark:text-indigo-400 font-bold text-lg">{{ substr($tenant->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $tenant->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $tenant->email ?? 'Sem e-mail' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300' }}">
                                {{ $tenant->status === 'active' ? 'Ativo' : 'Inativo' }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
