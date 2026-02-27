@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-2">Meus Serviços</h1>
                <p class="text-lg text-gray-500 font-medium">Gerencie o que você oferece aos usuários da Vestalize.</p>
            </div>
            
            <a href="{{ route('marketplace.designers.services.create') }}" class="px-8 py-4 bg-primary hover:bg-primary-hover text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-primary/20 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i> Adicionar Novo Serviço
            </a>
        </div>

        <!-- Designer Stats (Mini) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Total de Serviços</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $services->count() }}</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Serviços Ativos</span>
                <span class="text-2xl font-black text-emerald-500">{{ $services->where('status', 'active')->count() }}</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Em Análise</span>
                <span class="text-2xl font-black text-amber-500">{{ $services->where('status', 'pending')->count() }}</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                 <span class="block text-[8px] font-black uppercase tracking-widest text-gray-400 mb-1">Vendas Totais</span>
                 <span class="text-2xl font-black text-indigo-500">{{ $designer->total_sales }}</span>
            </div>
        </div>

        <!-- Services List -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                            <th class="px-8 py-6">Serviço</th>
                            <th class="px-8 py-6">Categoria</th>
                            <th class="px-8 py-6">Preço</th>
                            <th class="px-8 py-6">Status</th>
                            <th class="px-8 py-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($services as $service)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $service->cover_image }}" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                                    <div class="max-w-[200px]">
                                        <span class="block font-bold text-gray-900 dark:text-white truncate">{{ $service->title }}</span>
                                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Criado em {{ $service->created_at->format('d/m/y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">{{ $service->category_label }}</span>
                            </td>
                            <td class="px-8 py-6 font-black text-gray-900 dark:text-white">
                                {{ $service->price_credits }} <span class="text-[9px] text-gray-400 uppercase">Credits</span>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $colors = ['active' => 'emerald', 'pending' => 'amber', 'inactive' => 'gray', 'rejected' => 'red'];
                                    $color = $colors[$service->status] ?? 'gray';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-{{ $color }}-500/10 text-{{ $color }}-500 border border-{{ $color }}-500/20">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('marketplace.designers.services.edit', $service->id) }}" class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400 hover:text-primary hover:bg-primary/10 transition-all" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('marketplace.designers.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Tem certeza?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-all" title="Excluir">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('marketplace.services.show', $service->id) }}" target="_blank" class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-900 dark:hover:bg-white dark:hover:text-gray-900 transition-all" title="Ver Vitrine">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Você ainda não cadastrou nenhum serviço.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
