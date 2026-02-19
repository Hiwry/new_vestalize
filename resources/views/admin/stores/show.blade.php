@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $store->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Detalhes da loja</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(Auth::user()->isAdminGeral())
            <a href="{{ route('admin.stores.edit', $store->id) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Editar
            </a>
            @endif
            <a href="{{ route('admin.stores.index') }}" 
               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Voltar
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Informações Gerais -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações Gerais</h2>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Nome</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $store->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tipo</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($store->isMain())
                        Loja Principal
                    @elseif($store->parent)
                        Sub-loja de {{ $store->parent->name }}
                    @else
                        Loja
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $store->active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                    {{ $store->active ? 'Ativa' : 'Inativa' }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Criada em</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $store->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estatísticas</h2>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pedidos</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $store->orders->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Orçamentos</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $store->budgets->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Clientes</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $store->clients->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sub-lojas</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $store->subStores->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Usuários do Sistema (card superior) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Usuários da Loja</h2>
        @if(isset($allUsers) && $allUsers->isNotEmpty())
        <div class="space-y-2">
            @foreach($allUsers as $user)
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
                @if(Auth::user()->isAdminGeral() && $store->users->contains($user->id))
                <form action="{{ route('admin.stores.remove-admin', [$store->id, $user->id]) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Tem certeza que deseja remover este administrador?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs">
                        Remover
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum usuário encontrado</p>
        @endif

        @if(Auth::user()->isAdminGeral())
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <form action="{{ route('admin.stores.assign-admin', $store->id) }}" method="POST">
                @csrf
                <div class="flex flex-col sm:flex-row gap-2">
                    <select name="user_id" 
                            required
                            class="flex-1 min-w-0 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione um usuário...</option>
                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                            @if(!$store->users->contains($user->id))
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm whitespace-nowrap flex-shrink-0">
                        Adicionar
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<!-- Linha abaixo com Vendedores (esquerda) e Administradores (direita) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Vendedores -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Vendedores</h2>
        @if(isset($sellers) && $sellers->isNotEmpty())
        <div class="space-y-2 max-h-72 overflow-auto pr-1">
            @foreach($sellers as $seller)
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $seller->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $seller->email }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum vendedor encontrado</p>
        @endif
    </div>

    <!-- Administradores (abaixo, lado direito) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Administradores</h2>
        @if(isset($admins) && $admins->isNotEmpty())
        <div class="space-y-2">
            @foreach($admins as $user)
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum administrador atribuído</p>
        @endif
    </div>
</div>

<!-- Sub-lojas -->
@if($store->isMain() && $store->subStores->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Sub-lojas</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($store->subStores as $subStore)
        <a href="{{ route('admin.stores.show', $subStore->id) }}" 
           class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $subStore->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $subStore->orders->count() }} pedidos
                    </p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $subStore->active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                    {{ $subStore->active ? 'Ativa' : 'Inativa' }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection

