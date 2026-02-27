@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12 flex items-center gap-6">
            <a href="{{ route('admin.marketplace.packages.index') }}" class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-400 hover:text-primary shadow-sm border border-gray-100 dark:border-gray-700 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">
                    {{ isset($package) ? 'Editar Pacote' : 'Novo Pacote' }}
                </h1>
                <p class="text-lg text-gray-500 font-medium">Configure as condições de venda de créditos.</p>
            </div>
        </div>

        <form action="{{ isset($package) ? route('admin.marketplace.packages.update', $package->id) : route('admin.marketplace.packages.store') }}" 
              method="POST" class="space-y-8">
            @csrf
            @if(isset($package)) @method('PUT') @endif

            <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-8 md:p-12 shadow-2xl border border-gray-100 dark:border-gray-700">
                <div class="space-y-8">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Nome do Pacote (Ex: Pacote VIP, Starter...)</label>
                        <input type="text" name="name" value="{{ old('name', $package->name ?? '') }}" required 
                               class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                               placeholder="Ex: 500 Créditos">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Credits -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Quantidade de Créditos</label>
                            <input type="number" name="credits" value="{{ old('credits', $package->credits ?? 0) }}" required min="1"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner">
                        </div>
                        
                        <!-- Is Featured -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Destaque na Vitrine?</label>
                            <label class="relative flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl cursor-pointer border-2 border-transparent has-[:checked]:border-primary transition-all">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $package->is_featured ?? false) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-primary">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Marcar como Recomendado</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-gray-50 dark:border-gray-700">
                        <!-- Regular Price -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Preço Normal (R$)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="price" value="{{ old('price', $package->price ?? 0.00) }}" required 
                                       class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                                       placeholder="0.00">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <span class="text-[10px] font-black uppercase text-gray-400">R$</span>
                                </div>
                            </div>
                        </div>

                        <!-- Subscriber Price -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4 px-2">Preço para Assinantes (R$)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="subscriber_price" value="{{ old('subscriber_price', $package->subscriber_price ?? 0.00) }}" required 
                                       class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-6 py-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary shadow-inner"
                                       placeholder="0.00">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <span class="text-[10px] font-black uppercase text-gray-400">R$</span>
                                </div>
                            </div>
                            <p class="mt-2 text-[10px] text-emerald-500 font-bold px-2">Recomendado: 10% de desconto.</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 py-6 bg-primary hover:bg-primary-hover text-white rounded-[2rem] font-black text-sm uppercase tracking-widest shadow-2xl transition-all active:scale-95">
                    {{ isset($package) ? 'Salvar Alterações' : 'Criar Pacote' }}
                </button>
                <a href="{{ route('admin.marketplace.packages.index') }}" class="flex-1 py-6 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-center rounded-[2rem] font-black text-sm uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Cancelar
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
