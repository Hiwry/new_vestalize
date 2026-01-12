@extends('layouts.catalog')

@section('content')

<div class="mb-4 sm:mb-6">
    <a href="{{ route('catalog.index') }}" 
       class="inline-flex items-center text-sm sm:text-base text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition px-2 py-1.5 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="font-medium">Voltar ao catálogo</span>
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow dark:shadow-gray-900/25 overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 p-4 sm:p-6">
        <!-- Imagem do Item -->
        <div>
            <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg sm:rounded-xl overflow-hidden flex items-center justify-center">
                @if($item->image_path)
                    <img src="{{ url('/storage/' . $item->image_path) }}" 
                         alt="{{ $item->title }}"
                         class="w-full h-full object-cover"
                         loading="eager"
                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect width=\'400\' height=\'400\' fill=\'%23e5e7eb\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-size=\'16\'%3ESem imagem%3C/text%3E%3C/svg%3E';">
                @else
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                @endif
            </div>
        </div>

        <!-- Detalhes do Item -->
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                {{ $item->title }}
            </h1>

            @if($item->subtitle)
            <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 mb-4">
                {{ $item->subtitle }}
            </p>
            @endif

            @if($item->category)
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                    {{ $item->category->name }}
                </span>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
