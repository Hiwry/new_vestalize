@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Termos e Condições</h1>
            <a href="{{ route('admin.terms-conditions.create') }}" 
               class="bg-indigo-600 dark:bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                Criar Novo
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Termos Ativos -->
        @if($activeTerms)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">Versão Ativa</h3>
                    <p class="text-green-600 dark:text-green-300">Versão: {{ $activeTerms->version }}</p>
                    <p class="text-green-600 dark:text-green-300">Criado em: {{ $activeTerms->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.terms-conditions.edit', $activeTerms) }}" 
                       class="bg-blue-500 dark:bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 dark:hover:bg-blue-700">
                        Editar
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Lista de Versões -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Todas as Versões</h2>
            </div>
        
        @forelse($terms as $term)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Versão {{ $term->version }}
                        </h3>
                        @if($term->active)
                            <span class="bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 text-xs font-medium px-2.5 py-0.5 rounded">
                                Ativo
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Criado em: {{ $term->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                    </p>
                    @if($term->title)
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">
                            <strong>Título:</strong> {{ $term->title }}
                        </p>
                    @endif
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if($term->personalization_type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-200">
                                Personalização: {{ $term->personalization_type }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                Todos os tipos
                            </span>
                        @endif
                        @if($term->fabricType)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200">
                                Tecido: {{ $term->fabricType->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                Todos os tecidos
                            </span>
                        @endif
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300 max-h-20 overflow-hidden">
                        {!! Str::limit(strip_tags($term->content), 200) !!}
                    </div>
                </div>
                
                <div class="flex space-x-2 ml-4">
                    @if(!$term->active)
                        <form action="{{ route('admin.terms-conditions.activate', $term) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-green-500 dark:bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-600 dark:hover:bg-green-700"
                                    onclick="return confirm('Deseja ativar esta versão?')">
                                Ativar
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.terms-conditions.edit', $term) }}" 
                       class="bg-blue-500 dark:bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-600 dark:hover:bg-blue-700">
                        Editar
                    </a>
                    
                    <form action="{{ route('admin.terms-conditions.destroy', $term) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 dark:bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-600 dark:hover:bg-red-700"
                                onclick="return confirm('Deseja remover esta versão?')">
                            Remover
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
            <p>Nenhum termo e condição encontrado.</p>
            <a href="{{ route('admin.terms-conditions.create') }}" 
               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 mt-2 inline-block">
                Criar o primeiro
            </a>
        </div>
        @endforelse
    </div>
@endsection
