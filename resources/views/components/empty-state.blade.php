@props([
    'title' => 'Nenhum dado encontrado',
    'description' => 'Parece que ainda não há registros por aqui.',
    'icon' => 'folder-open',
    'actionUrl' => null,
    'actionLabel' => 'Criar Novo',
    'secondaryUrl' => null,
    'secondaryLabel' => 'Saiba mais'
])

<div class="flex flex-col items-center justify-center py-16 px-4 text-center bg-white dark:bg-gray-800/50 rounded-2xl border-2 border-dashed border-gray-100 dark:border-gray-700/50">
    <div class="mb-6 relative">
        <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/30 rounded-full blur-2xl scale-150 opacity-50"></div>
        <div class="relative h-24 w-24 bg-indigo-50 dark:bg-indigo-900/40 rounded-full flex items-center justify-center text-indigo-500 dark:text-indigo-400">
            @if($icon === 'folder-open')
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
            @elseif($icon === 'shopping-cart')
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            @elseif($icon === 'users')
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            @elseif($icon === 'search')
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            @else
                <i class="fa-solid fa-{{ $icon }} text-4xl"></i>
            @endif
        </div>
    </div>

    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $title }}</h3>
    <p class="text-gray-500 dark:text-gray-400 max-w-sm mb-8 leading-relaxed">
        {{ $description }}
    </p>

    <div class="flex flex-col sm:flex-row items-center gap-3">
        @if($actionUrl)
            <a href="{{ $actionUrl }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-lg shadow-indigo-200 dark:shadow-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ $actionLabel }}
            </a>
        @endif

        @if($secondaryUrl)
            <a href="{{ $secondaryUrl }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                {{ $secondaryLabel }}
            </a>
        @endif
    </div>
</div>
