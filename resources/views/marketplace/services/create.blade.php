@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12 flex items-center gap-6">
            <a href="{{ route('marketplace.my-services.index') }}" class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-500 hover:text-primary shadow-sm border border-gray-100 dark:border-gray-700 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">Novo Serviço</h1>
                <p class="text-lg text-gray-500 font-medium">Cadastre um novo serviço na vitrine do marketplace.</p>
            </div>
        </div>

        <form action="{{ route('marketplace.my-services.store') }}" 
              method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Basic Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <div class="space-y-8">
                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Título do Serviço</label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                               class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                               placeholder="Ex: Criação de Logotipo Profissional">
                        @error('title') <p class="mt-2 text-xs text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Categoria</label>
                            <div class="relative">
                                <select name="category" required class="appearance-none w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner cursor-pointer">
                                    <option value="">Selecione...</option>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Preço (em Créditos)</label>
                            <div class="relative">
                                <input type="number" name="price_credits" value="{{ old('price_credits') }}" required min="1" max="9999"
                                       class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                                       placeholder="0">
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <span class="text-[10px] font-black uppercase text-gray-400">Créditos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Descrição Detalhada</label>
                        <textarea name="description" rows="6" required 
                                  class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-[2rem] px-6 py-5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner leading-relaxed font-bold"
                                  placeholder="Explique o que o cliente recebe...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Configuration Card -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Prazo de Entrega (Dias)</label>
                            <input type="number" name="delivery_days" value="{{ old('delivery_days', 3) }}" required min="1"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Nº de Revisões</label>
                            <input type="number" name="revisions" value="{{ old('revisions', 2) }}" required min="0"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Requisitos para o Cliente</label>
                        <textarea name="requirements" rows="3" 
                                  class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                                  placeholder="O que você precisa para começar?">{{ old('requirements') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Media Card -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-8 px-2">Mídia e Portfólio</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-4 px-2">Imagens (Mínimo 1, Máximo 5)</label>
                        <input type="file" name="images[]" multiple required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        <p class="mt-2 text-[10px] text-gray-400 px-2 font-medium">A primeira imagem será usada como capa.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 py-6 bg-primary hover:bg-primary-hover text-white rounded-[2rem] font-black text-sm uppercase tracking-widest shadow-2xl shadow-primary/20 transition-all active:scale-95">
                    Publicar Serviço
                </button>
                <a href="{{ route('marketplace.my-services.index') }}" class="flex-1 py-6 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-center rounded-[2rem] font-black text-sm uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition-all active:scale-95">
                    Cancelar
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
