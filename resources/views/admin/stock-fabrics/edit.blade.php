@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Editar Tipo de Tecido</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Atualize o preço base e o vínculo com o tecido principal usado no estoque e nas peças.</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('stock-fabrics.update', $fabric->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name', $fabric->name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="parent_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tecido pai</label>
                <select id="parent_id" name="parent_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Selecione...</option>
                    @foreach($parentFabrics as $parentFabric)
                        <option value="{{ $parentFabric->id }}" {{ old('parent_id', $fabric->parent_id) == $parentFabric->id ? 'selected' : '' }}>{{ $parentFabric->name }}</option>
                    @endforeach
                </select>
                @error('parent_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="price" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Preço base</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $fabric->price) }}" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    @error('price')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="order" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ordem</label>
                    <input type="number" id="order" name="order" value="{{ old('order', $fabric->order) }}" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    @error('order')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <label for="active" class="admin-check-label items-start">
                <input type="checkbox" id="active" name="active" value="1" {{ old('active', $fabric->active) ? 'checked' : '' }} class="admin-check-input">
                <span class="admin-check-ui" aria-hidden="true"></span>
                <span class="admin-check-copy">
                    <span class="admin-check-title">Ativo</span>
                    <span class="admin-check-hint">Mantém esse tipo de tecido disponível para uso no estoque e nas peças.</span>
                </span>
            </label>

            <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:bg-gray-900/40 dark:text-gray-300">
                Peças vinculadas: <strong>{{ $fabric->fabricTypePieces()->count() }}</strong>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('stock-fabrics.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancelar</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Atualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection