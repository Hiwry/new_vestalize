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
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">Editar Serviço</h1>
                <p class="text-lg text-gray-500 font-medium">Atualize as informações do seu serviço.</p>
            </div>
        </div>

        <form action="{{ route('marketplace.my-services.update', $service->id) }}" 
              method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <div class="space-y-8">
                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Título do Serviço</label>
                        <input type="text" name="title" value="{{ old('title', $service->title) }}" required 
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
                                        <option value="{{ $value }}" {{ old('category', $service->category) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('category') <p class="mt-2 text-xs text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Preço (em Créditos)</label>
                            <div class="relative">
                                <input type="number" name="price_credits" value="{{ old('price_credits', $service->price_credits) }}" required min="1" max="9999"
                                       class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                                       placeholder="0">
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <span class="text-[10px] font-black uppercase text-gray-400">Créditos</span>
                                </div>
                            </div>
                            @error('price_credits') <p class="mt-2 text-xs text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Descrição Detalhada</label>
                        <textarea name="description" rows="6" required 
                                  class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-[2rem] px-6 py-5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner leading-relaxed font-bold"
                                  placeholder="Explique o que o cliente recebe...">{{ old('description', $service->description) }}</textarea>
                        @error('description') <p class="mt-2 text-xs text-red-500 font-bold px-2">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Configuration Card -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Prazo de Entrega (Dias)</label>
                            <input type="number" name="delivery_days" value="{{ old('delivery_days', $service->delivery_days) }}" required min="1"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Nº de Revisões</label>
                            <input type="number" name="revisions" value="{{ old('revisions', $service->revisions) }}" required min="0"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner">
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->status === 'active') ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-primary">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Serviço Ativo (Visível na Vitrine)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Media Card -->
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-8 px-2">Mídia e Portfólio</h3>
                
                @if($service->images->count() > 0)
                <div class="grid grid-cols-5 gap-4 mb-8">
                    @foreach($service->images as $img)
                    <div class="relative group aspect-square rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover">
                        @if($img->is_cover)
                             <div class="absolute top-2 right-2 bg-primary text-white text-[8px] font-black uppercase px-2 py-1 rounded-md">Capa</div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="space-y-6">
                    <p class="text-xs text-amber-500 font-bold px-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Adicionar novas imagens substituirá as atuais.</p>
                    <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-gray-100 file:text-gray-500 hover:file:bg-gray-200">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 py-6 bg-primary hover:bg-primary-hover text-white rounded-[2rem] font-black text-sm uppercase tracking-widest shadow-2xl shadow-primary/20 transition-all active:scale-95">
                    Salvar Alterações
                </button>
                <a href="{{ route('marketplace.my-services.index') }}" class="flex-1 py-6 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-center rounded-[2rem] font-black text-sm uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition-all active:scale-95">
                    Cancelar
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
