@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.marketplace.index') }}" class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-400 hover:text-primary shadow-sm border border-gray-100 dark:border-gray-700 transition-all">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-1">Pacotes de Crédito</h1>
                    <p class="text-lg text-gray-500 font-medium">Gerencie as opções de compra de créditos para os usuários.</p>
                </div>
            </div>
            
            <a href="{{ route('admin.marketplace.packages.create') }}" class="px-8 py-4 bg-primary hover:bg-primary-hover text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-primary/20 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i> Novo Pacote
            </a>
        </div>

        <!-- Packages Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($packages as $package)
            <div class="group bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-xl border border-gray-100 dark:border-gray-700 transition-all hover:-translate-y-2 relative overflow-hidden">
                @if($package->is_featured)
                    <div class="absolute top-0 right-0 bg-primary text-white text-[8px] font-black uppercase tracking-widest px-6 py-2 rounded-bl-3xl">Destaque</div>
                @endif
                
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ $package->name }}</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-primary">{{ $package->credits }}</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Créditos</span>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-sm font-bold">
                        <span class="text-gray-400">Preço Regular:</span>
                        <span class="text-gray-900 dark:text-white">R$ {{ number_format($package->price, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold">
                        <span class="text-gray-400">Preço Assinante:</span>
                        <span class="text-emerald-500">R$ {{ number_format($package->subscriber_price, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.marketplace.packages.edit', $package->id) }}" class="flex-1 py-4 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300 rounded-2xl text-center text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all">
                        Editar
                    </a>
                    <form action="{{ route('admin.marketplace.packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Excluir este pacote?')">
                         @csrf @method('DELETE')
                         <button type="submit" class="w-12 h-12 bg-gray-50 dark:bg-gray-900 text-gray-400 hover:text-red-500 rounded-2xl flex items-center justify-center transition-all">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                         </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
