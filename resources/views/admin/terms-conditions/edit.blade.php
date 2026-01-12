@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Termos e Condições</h1>
            <a href="{{ route('admin.terms-conditions.index') }}" 
               class="bg-gray-500 dark:bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors">
                Voltar
            </a>
        </div>

        <form action="{{ route('admin.terms-conditions.update', $termsCondition) }}" method="POST" class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Versão *
                </label>
                <input type="text" 
                       id="version" 
                       name="version" 
                       value="{{ old('version', $termsCondition->version) }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                       placeholder="Ex: 1.0, 2.0, etc."
                       required>
                @error('version')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Título (opcional)
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title', $termsCondition->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                       placeholder="Ex: Termos para DTF em Algodão">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Título que aparecerá quando este termo for exibido
                </p>
                @error('title')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="personalization_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tipo de Personalização (opcional)
                    </label>
                    <select id="personalization_type" 
                            name="personalization_type"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">Termos Gerais (para todos os tipos)</option>
                        @foreach($personalizationTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('personalization_type', $termsCondition->personalization_type) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Deixe em branco para termos gerais, ou selecione um tipo específico
                    </p>
                    @error('personalization_type')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fabric_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tipo de Tecido (opcional)
                    </label>
                    <select id="fabric_type_id" 
                            name="fabric_type_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">Para todos os tecidos</option>
                        @foreach($fabricTypes as $fabric)
                            <option value="{{ $fabric->id }}" {{ old('fabric_type_id', $termsCondition->fabric_type_id) == $fabric->id ? 'selected' : '' }}>
                                {{ $fabric->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Deixe em branco para todos os tecidos, ou selecione um tipo específico
                    </p>
                    @error('fabric_type_id')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Conteúdo *
                </label>
                <textarea id="content" 
                          name="content" 
                          rows="20"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                          placeholder="Digite os termos e condições aqui..."
                          required>{{ old('content', $termsCondition->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="active" 
                           value="1"
                           {{ old('active', $termsCondition->active) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Ativar esta versão (desativará outras versões ativas)
                    </span>
                </label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.terms-conditions.index') }}" 
                   class="bg-gray-500 dark:bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-indigo-600 dark:bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 transition-colors">
                    Atualizar Termos e Condições
                </button>
            </div>
        </form>
@endsection
