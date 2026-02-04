@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">Configurações</span>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400">Observações do Orçamento</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Observações do Orçamento Rápido</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Edite as sugestões que aparecem como botões no orçamento rápido.
            </p>
            @if($tenantId)
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                    Escopo: {{ $tenant?->name ?? 'Tenant' }} (ID {{ $tenantId }})
                </p>
            @else
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                    Escopo: Global (aplica para tenants sem configuração própria)
                </p>
            @endif
        </div>
        <a href="{{ route('settings.index', ['category' => 'vendas']) }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Lista de Observações</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Uma por linha</p>
        </div>
        <form method="POST" action="{{ route('admin.budget-settings.update') }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações</label>
                <textarea name="options" rows="8"
                          class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                          placeholder="Digite uma observação por linha...">{{ $optionsText }}</textarea>
                @error('options')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Prévia</p>
                <div class="flex flex-wrap gap-2">
                    @forelse($options as $opt)
                        <span class="px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-200">
                            {{ $opt }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-500 dark:text-gray-400">Nenhuma observação cadastrada.</span>
                    @endforelse
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition-colors">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
