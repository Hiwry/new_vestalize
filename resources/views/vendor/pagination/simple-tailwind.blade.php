@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegação de Páginas" class="flex items-center justify-between py-3">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 cursor-default leading-5 rounded-md">
                Anterior
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none transition ease-in-out duration-150">
                Anterior
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none transition ease-in-out duration-150">
                Próximo
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 cursor-default leading-5 rounded-md">
                Próximo
            </span>
        @endif
    </nav>
@endif
