@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Item de Catálogo</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Atualize um item exibido apenas no catálogo público.</p>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6">
    <form action="{{ route('admin.catalog-items.update', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria</label>
                <select name="catalog_category_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('catalog_category_id', $item->catalog_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
                <input type="text" name="title" value="{{ old('title', $item->title) }}" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtítulo</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $item->subtitle) }}" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Imagem Atual</label>
                @if($item->image_path)
                    <img src="{{ url('/storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="h-24 w-24 object-cover rounded mb-2">
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Nenhuma imagem cadastrada.</p>
                @endif
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nova Imagem (opcional)</label>
                <input type="file" name="image" class="w-full text-sm text-gray-700 dark:text-gray-300">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se enviar uma nova imagem, a antiga será substituída.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="active" value="1" id="active" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('active', $item->active) ? 'checked' : '' }}>
                <label for="active" class="text-sm text-gray-700 dark:text-gray-300">Ativo no catálogo</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordem</label>
                <input type="number" name="order" value="{{ old('order', $item->order) }}" class="w-full max-w-xs px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Itens com menor ordem aparecem primeiro.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.catalog-items.index') }}" 
               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Salvar
            </button>
        </div>
    </form>
</div>
@endsection
