@extends('layouts.admin')

@section('content')
<div x-data="{ showClearModal: false }" x-cloak class="max-w-6xl mx-auto px-4 py-6 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notificações</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400">Histórico completo das suas notificações</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-[#7c3aed] bg-white dark:bg-slate-800 border border-[#7c3aed] rounded-lg hover:bg-purple-50 dark:hover:bg-slate-700 transition">
                    Marcar todas como lidas
                </button>
            </form>
            <button type="button" @click="showClearModal = true"
                    class="px-4 py-2 text-sm font-semibold text-white stay-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                Limpar tudo
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 overflow-hidden">
        <div class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse($notifications as $notification)
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-3 {{ !$notification->read ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $notification->title }}</span>
                            @if(!$notification->read)
                                <span class="w-2 h-2 bg-indigo-600 dark:bg-indigo-400 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 dark:text-slate-400 mt-1 break-words">{{ $notification->message }}</p>
                        <div class="text-xs text-gray-500 dark:text-slate-500 mt-2">
                            {{ $notification->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($notification->link)
                            <a href="{{ $notification->link }}"
                               class="px-3 py-1.5 text-xs font-semibold text-[#7c3aed] bg-white dark:bg-slate-800 border border-[#7c3aed] rounded-lg hover:bg-purple-50 dark:hover:bg-slate-700 transition">
                                Abrir
                            </a>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-200 dark:border-red-500/30 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Remover
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500 dark:text-slate-400">
                    Nenhuma notificação encontrada.
                </div>
            @endforelse
        </div>
    </div>

    @if($notifications->hasPages())
        <div class="flex justify-center">
            {{ $notifications->links() }}
        </div>
    @endif
    <div x-show="showClearModal" class="fixed inset-0 z-[70] flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showClearModal = false"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Limpar notificações?</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Essa ação remove todo o histórico.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="showClearModal = false"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white">
                    Cancelar
                </button>
                <form method="POST" action="{{ route('notifications.clear-all') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white stay-white bg-red-600 hover:bg-red-700 rounded-lg">
                        Limpar tudo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
