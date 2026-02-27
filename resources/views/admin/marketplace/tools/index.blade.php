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
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter mb-1">Gestão de Ferramentas</h1>
                    <p class="text-lg text-gray-500 font-medium">Cadastre e gerencie arquivos digitais para venda.</p>
                </div>
            </div>
            
            <a href="{{ route('admin.marketplace.tools.create') }}" class="px-8 py-4 bg-primary hover:bg-primary-hover text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-primary/20 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2"></i> Nova Ferramenta
            </a>
        </div>

        <!-- Tools Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                            <th class="px-8 py-6">ID / Ferramenta</th>
                            <th class="px-8 py-6">Preço</th>
                            <th class="px-8 py-6">Downloads</th>
                            <th class="px-8 py-6">Status</th>
                            <th class="px-8 py-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @foreach($tools as $tool)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <span class="text-[9px] font-black text-gray-300">#{{ $tool->id }}</span>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $tool->cover_image }}" class="w-10 h-10 rounded-xl object-cover">
                                        <span class="font-bold text-gray-900 dark:text-white truncate max-w-[150px]">{{ $tool->title }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 font-black text-primary">
                                {{ $tool->price_credits }} <span class="text-[9px] text-gray-400">Cr.</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $tool->total_downloads }}</span>
                            </td>
                            <td class="px-8 py-6">
                                @php $color = $tool->status === 'active' ? 'emerald' : 'gray'; @endphp
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-{{ $color }}-500/10 text-{{ $color }}-500 border border-{{ $color }}-500/20">
                                    {{ $tool->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.marketplace.tools.edit', $tool->id) }}" class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400 hover:text-primary transition-all">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.marketplace.tools.destroy', $tool->id) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
