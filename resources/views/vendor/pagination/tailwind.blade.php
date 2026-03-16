@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegação de Páginas" class="flex items-center justify-between py-3">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 cursor-default leading-5 rounded-md">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none transition ease-in-out duration-150">
                    Anterior
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none transition ease-in-out duration-150">
                    Próximo
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 cursor-default leading-5 rounded-md">
                    Próximo
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-slate-400 leading-5">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $paginator->firstItem() }}</span>
                        a
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    de
                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center p-2 text-sm font-medium text-gray-400 dark:text-slate-600 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 cursor-default rounded-full leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center p-2 text-sm font-medium text-gray-500 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-full leading-5 hover:text-gray-400 focus:z-10 focus:outline-none transition ease-in-out duration-150" aria-label="Anterior">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-400 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 cursor-default rounded-full leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-indigo-600 border border-indigo-600 cursor-default rounded-full leading-5 shadow-lg shadow-indigo-500/30">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-full leading-5 hover:bg-gray-50 dark:hover:bg-slate-700 focus:z-10 focus:outline-none transition ease-in-out duration-150" aria-label="Ir para página {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center p-2 text-sm font-medium text-gray-500 dark:text-slate-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-full leading-5 hover:text-gray-400 focus:z-10 focus:outline-none transition ease-in-out duration-150" aria-label="Próximo">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center p-2 text-sm font-medium text-gray-400 dark:text-slate-600 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 cursor-default rounded-full leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
