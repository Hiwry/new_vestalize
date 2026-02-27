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
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-1">Gestão de Serviços</h1>
                    <p class="text-lg text-gray-500 font-medium">Controle de visibilidade e moderação de serviços.</p>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                            <th class="px-8 py-6">ID / Serviço</th>
                            <th class="px-8 py-6">Designer</th>
                            <th class="px-8 py-6">Preço</th>
                            <th class="px-8 py-6">Categoria</th>
                            <th class="px-8 py-6">Status</th>
                            <th class="px-8 py-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @foreach($services as $service)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <span class="text-[9px] font-black text-gray-300">#{{ $service->id }}</span>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $service->cover_image }}" class="w-10 h-10 rounded-xl object-cover">
                                        <span class="font-bold text-gray-900 dark:text-white truncate max-w-[150px]">{{ $service->title }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $service->designer->display_name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 font-black text-primary">
                                {{ $service->price_credits }} <span class="text-[9px] text-gray-400">Cr.</span>
                            </td>
                            <td class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                {{ $service->category_label }}
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-{{ $service->status_color }}-500/10 text-{{ $service->status_color }}-500 border border-{{ $service->status_color }}-500/20">
                                    {{ $service->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.marketplace.services.toggle', $service->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all {{ $service->status === 'active' ? 'bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white' }}" title="{{ $service->status === 'active' ? 'Desativar' : 'Ativar' }}">
                                            <i class="fa-solid fa-{{ $service->status === 'active' ? 'power-off' : 'check' }} text-xs"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('marketplace.services.show', $service->id) }}" target="_blank" class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-900 transition-all">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 bg-gray-50 dark:bg-gray-900/50">
                {{ $services->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
