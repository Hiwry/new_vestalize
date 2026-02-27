@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12 flex items-center gap-6">
            <a href="{{ route('admin.marketplace.tools.index') }}" class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-500 hover:text-primary shadow-sm border border-gray-100 dark:border-gray-700 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">
                    {{ isset($tool) ? 'Editar Ferramenta' : 'Nova Ferramenta' }}
                </h1>
                <p class="text-lg text-gray-500 font-medium">Configure as propriedades do arquivo digital.</p>
            </div>
        </div>

        <form action="{{ isset($tool) ? route('admin.marketplace.tools.update', $tool->id) : route('admin.marketplace.tools.store') }}" 
              method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($tool)) @method('PUT') @endif

            <!-- Basic Info -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <div class="space-y-8">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Título da Ferramenta</label>
                        <input type="text" name="title" value="{{ old('title', $tool->title ?? '') }}" required 
                               class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                               placeholder="Ex: Mockup de Camiseta Cotton Premium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Categoria</label>
                            <select name="category" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner cursor-pointer">
                                @foreach($categories as $value => $label)
                                    <option value="{{ $value }}" {{ old('category', $tool->category ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Preço (Créditos)</label>
                            <input type="number" name="price_credits" value="{{ old('price_credits', $tool->price_credits ?? 10) }}" required min="0"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Descrição</label>
                        <textarea name="description" rows="5" required 
                                  class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-[2rem] px-6 py-5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner font-bold">{{ old('description', $tool->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- File Data -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                 <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-8 px-2">Arquivos e Dados Técnicos</h3>
                 
                 <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Formato Principal (Ex: Ai, ZIP)</label>
                            <input type="text" name="file_format" value="{{ old('file_format', $tool->file_format ?? 'ZIP') }}" required 
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Tamanho do Arquivo (Ex: 15.5 MB)</label>
                            <input type="text" name="file_size" value="{{ old('file_size', $tool->file_size ?? '0 MB') }}" required 
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-4 px-2">Arquivo para Download (.zip, .pdf, .ai)</label>
                        <input type="file" name="download_file" {{ isset($tool) ? '' : 'required' }} class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>

                    <div class="pt-8 border-t border-gray-50 dark:border-gray-700">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Imagem de Capa</label>
                        <input type="file" name="cover_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-gray-100 file:text-gray-500 hover:file:bg-gray-200">
                    </div>
                 </div>
            </div>

            <!-- Status -->
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl border border-gray-100 dark:border-gray-700">
                 <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="status" value="active" {{ (old('status', $tool->status ?? 'active') === 'active') ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-primary">
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Ferramenta Ativa e Visível</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 py-6 bg-primary hover:bg-primary-hover text-white rounded-[2rem] font-black text-sm uppercase tracking-widest shadow-2xl transition-all active:scale-95">
                    {{ isset($tool) ? 'Salvar Alterações' : 'Criar Ferramenta' }}
                </button>
                <a href="{{ route('admin.marketplace.tools.index') }}" class="flex-1 py-6 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-center rounded-[2rem] font-black text-sm uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Cancelar
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
