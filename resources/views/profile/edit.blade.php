@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-sm" style="color: white !important;">
                <i class="fa-solid fa-user-gear text-lg"></i>
            </div>
            Meu Perfil
        </h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 ml-13">
            Gerencie suas informações de conta, senha e preferências de segurança.
        </p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Coluna Esquerda: Informações Gerais -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Dados do Perfil -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Segurança / Senha -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Coluna Direita: Ações de Conta -->
        <div class="space-y-6">
            <!-- Cards de Informação Extra -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-indigo-500"></i> Segurança
                </h3>
                <div class="space-y-4">
                    <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/50 flex gap-3">
                        <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 mt-1"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-700 dark:text-green-300">Conta Ativa</p>
                            <p class="text-xs text-green-600/80 dark:text-green-400/80 mt-1">Seu acesso está normalizado e seguro.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deletar Conta -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
