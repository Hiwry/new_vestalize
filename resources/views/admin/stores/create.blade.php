@extends('layouts.admin')

@section('content')
@php
    $tenant = $tenant ?? null;
    $storeCount = $storeCount ?? null;
    $storeLimit = $storeLimit ?? null;
    $remainingStores = $remainingStores ?? null;
    $canCreateStore = $canCreateStore ?? true;
@endphp
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Criar Nova Loja</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">
        @if($tenant && $storeCount !== null && $storeLimit !== null)
            {{ $tenant->name }}: {{ $storeCount }} / {{ $storeLimit }} lojas em uso
        @else
            Adicione uma nova loja ou sub-loja ao sistema
        @endif
    </p>
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

@if($tenant && $storeLimit !== null)
<div class="mb-6 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg px-4 py-3 text-sm text-indigo-800 dark:text-indigo-200">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <span>Tenant atual: <strong>{{ $tenant->name }}</strong></span>
        <span>
            Uso do plano: <strong>{{ $storeCount }}</strong> de <strong>{{ $storeLimit }}</strong> lojas
            @if($remainingStores !== null)
                ({{ $remainingStores }} restante{{ $remainingStores === 1 ? '' : 's' }})
            @endif
        </span>
    </div>
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

            @if($mainStore)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Loja Principal
                    </label>
                    <input type="hidden" name="parent_id" value="{{ old('parent_id', $mainStore->id) }}">
                    <input type="text"
                           value="{{ $mainStore->name }}"
                           disabled
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700/60 text-gray-900 dark:text-gray-100 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        As novas lojas deste tenant sao vinculadas automaticamente a loja principal.
                    </p>
                </div>
            @else
                <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 px-4 py-3 text-sm text-amber-800 dark:text-amber-200">
                    Este tenant ainda nao possui loja principal. A loja criada agora sera definida como principal.
                </div>
            @endif

            <div>
                <input type="hidden" name="active" value="0">
                <label for="active" class="admin-check-label items-start">
                    <input type="checkbox"
                           id="active"
                           name="active"
                           value="1"
                           {{ old('active', true) ? 'checked' : '' }}
                           class="admin-check-input">
                    <span class="admin-check-ui" aria-hidden="true"></span>
                    <span class="admin-check-copy">
                        <span class="admin-check-title">Loja ativa</span>
                        <span class="admin-check-hint">Mantem a loja disponivel para pedidos, estoque e vinculo de usuarios.</span>
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.stores.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        @disabled(!$canCreateStore)
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:cursor-not-allowed disabled:bg-gray-400 disabled:hover:bg-gray-400">
                    Criar Loja
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

