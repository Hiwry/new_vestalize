@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition">
        ← Voltar para detalhes
    </a>
</div>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Afiliado</h1>
</div>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm dark:shadow-gray-900/25 sm:rounded-lg">
    <div class="p-6">
        <form method="POST" action="{{ route('admin.affiliates.update', $affiliate) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuário vinculado</label>
                    <select name="user_id" id="user_id"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <option value="">Sem usuário</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $affiliate->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $affiliate->name) }}" required
                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $affiliate->email) }}" required
                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $affiliate->phone) }}"
                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                </div>

                <div>
                    <label for="commission_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Taxa de Comissão (%) *</label>
                    <input type="number" name="commission_rate" id="commission_rate" value="{{ old('commission_rate', $affiliate->commission_rate) }}" 
                        min="0" max="100" step="0.5" required
                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        <option value="active" {{ old('status', $affiliate->status) === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $affiliate->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Dados Bancários</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bank" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banco</label>
                        <input type="text" name="bank_info[bank]" id="bank" value="{{ old('bank_info.bank', $affiliate->bank_info['bank'] ?? '') }}"
                            class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                    <div>
                        <label for="pix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chave PIX</label>
                        <input type="text" name="bank_info[pix]" id="pix" value="{{ old('bank_info.pix', $affiliate->bank_info['pix'] ?? '') }}"
                            class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
