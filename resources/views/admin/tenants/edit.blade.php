@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="mb-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Assinatura</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Atualize os dados do tenant e seu status de assinatura.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código da Loja</label>
                        <input type="text" value="{{ $tenant->store_code }}" disabled
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700 text-gray-700 dark:text-gray-300 shadow-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de Criação</label>
                        <input type="text" value="{{ $tenant->created_at->format('d/m/Y H:i') }}" disabled
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700 text-gray-700 dark:text-gray-300 shadow-sm cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nome do Cliente / Empresa
                    </label>
                    <input type="text" name="name" id="name" required
                        value="{{ old('name', $tenant->name) }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email do Admin
                    </label>
                    <input type="email" name="email" id="email" required
                        value="{{ old('email', $tenant->email) }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Plano
                        </label>
                        <select name="plan_id" id="plan_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ $tenant->plan_id == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} (R$ {{ number_format($plan->price, 2, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="active" {{ $tenant->status == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="suspended" {{ $tenant->status == 'suspended' ? 'selected' : '' }}>Suspenso</option>
                            <option value="cancelled" {{ $tenant->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="trial_ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fim do Trial
                        </label>
                        <input type="date" name="trial_ends_at" id="trial_ends_at"
                            value="{{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d') : '' }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="subscription_ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Vencimento da Assinatura
                        </label>
                        <input type="date" name="subscription_ends_at" id="subscription_ends_at"
                            value="{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('Y-m-d') : '' }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">Deixe em branco para acesso vitalício/indefinido.</p>

                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Alterar Senha de Acesso</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nova Senha
                            </label>
                            <input type="text" name="password" id="password" placeholder="Deixe em branco para manter a atual"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Ações
                            </label>
                            <div class="flex items-center mt-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <input type="checkbox" name="send_password_email" id="send_password_email" value="1" checked 
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 bg-white dark:bg-gray-800">
                                <label for="send_password_email" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    Enviar nova senha por email para o cliente
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.tenants.index') }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 transition-colors font-semibold text-sm uppercase tracking-wide">
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
