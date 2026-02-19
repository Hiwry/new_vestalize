@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Criar Nova Loja</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Adicione uma nova loja ou sub-loja ao sistema</p>
</div>

@if($errors->any())
<div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <form action="{{ route('admin.stores.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome da Loja <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Loja Principal (para criar sub-loja)
                </label>
                <select name="parent_id" 
                        id="parent_id"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Nenhuma (Loja Principal)</option>
                    @if($mainStore)
                        <option value="{{ $mainStore->id }}" {{ old('parent_id') == $mainStore->id ? 'selected' : '' }}>
                            {{ $mainStore->name }}
                        </option>
                    @endif
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Deixe em branco para criar uma loja principal. Selecione uma loja principal para criar uma sub-loja.
                </p>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" 
                           name="active" 
                           value="1"
                           {{ old('active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Loja ativa</span>
                </label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.stores.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Criar Loja
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

