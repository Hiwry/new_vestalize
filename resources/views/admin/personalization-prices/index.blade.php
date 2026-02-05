@extends('layouts.admin')

@push('styles')
<style>
    .card-hover {
        transition: all 0.2s ease;
    }
    .card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .dark .card-hover:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
    }
    
    /* Toggle Switch Glassmorphism */
    .glass-toggle {
        width: 3.5rem;
        height: 1.75rem;
        background-color: rgba(255, 255, 255, 0.1); 
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 9999px;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .dark .glass-toggle {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .glass-toggle::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 1.5rem;
        height: 1.5rem;
        background-color: white;
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .glass-toggle-input:checked + .glass-toggle {
        background-color: #7c3aed; /* --primary */
        border-color: #7c3aed;
    }

    .glass-toggle-input:checked + .glass-toggle::after {
        transform: translateX(1.75rem);
    }
</style>
@endpush

@section('content')
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Preços de Personalização</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure os preços para cada tipo de personalização</p>
                </div>
            </div>
        </div>



        <!-- Cards Grid (Preços) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            @foreach($pricesByType as $typeKey => $typeData)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden card-hover">
                    <!-- Card Header -->
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-md flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $typeData['label'] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tipo de Personalização</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5">
                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $typeData['sizes']->count() }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Tamanhos</div>
                            </div>
                            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $typeData['total_ranges'] }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Faixas</div>
                            </div>
                        </div>

                        <!-- Config Badges -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @if($typeData['charge_by_color'] ?? false)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 text-xs font-semibold rounded-md border border-amber-200 dark:border-amber-800">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3l7 7a6 6 0 11-12 0l5-7z" />
                                        <circle cx="9" cy="12" r="1" />
                                    </svg>
                                    Cor
                                </span>
                            @endif
                            @if(($typeData['discount_2nd'] ?? 0) > 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 text-xs font-semibold rounded-md border border-emerald-200 dark:border-emerald-800">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M5 19L19 5" />
                                        <circle cx="7" cy="7" r="2" />
                                        <circle cx="17" cy="17" r="2" />
                                    </svg>
                                    {{ number_format($typeData['discount_2nd'], 0) }}%
                                </span>
                            @endif
                            @if(($typeData['special_options_count'] ?? 0) > 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-200 text-xs font-semibold rounded-md border border-violet-200 dark:border-violet-800">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9.813 15.904L9 18l-.813-2.096a4.5 4.5 0 00-2.091-2.091L4 13l2.096-.813a4.5 4.5 0 002.091-2.091L9 8l.813 2.096a4.5 4.5 0 002.091 2.091L14 13l-2.096.813a4.5 4.5 0 00-2.091 2.091zM18 7l1.064 2.75L22 11l-2.936 1.25L18 15l-1.064-2.75L14 11l2.936-1.25L18 7z" />
                                    </svg>
                                    {{ $typeData['special_options_count'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Sizes List -->
                        @if($typeData['sizes']->count() > 0)
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Tamanhos disponíveis:</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($typeData['sizes']->take(6) as $size)
                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">
                                    {{ $size->size_name }}
                                </span>
                                @endforeach
                                @if($typeData['sizes']->count() > 6)
                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs rounded">
                                    +{{ $typeData['sizes']->count() - 6 }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4 mb-4">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum preço configurado</p>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('admin.personalization-prices.edit', $typeKey) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2.5 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white stay-white text-sm font-medium rounded-md transition-colors duration-150">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Preços
                            </a>
                            @if(isset($settings[$typeKey]))
                            <a href="{{ route('admin.personalization-settings.edit', $typeKey) }}" 
                               class="inline-flex items-center justify-center px-3 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors duration-150"
                               title="Configurações">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            </div>

            <!-- Localizações de Aplicação -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Localizações de Aplicação</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie as opções de localização (Frente, Costas, etc.)</p>
                    </div>
                    <form method="POST" action="{{ route('admin.personalization-prices.locations.store') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto mt-3 sm:mt-0">
                        @csrf
                        <div class="relative flex-1 sm:flex-none">
                            <input type="text" name="name" placeholder="Nova localização..." required
                                   class="w-full sm:w-64 pl-4 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-400 text-xs hidden sm:block">↵</span>
                            </div>
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 !text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-1.5 !text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Adicionar
                        </button>
                    </form>
                </div>
                
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nome</th>
                                    <th scope="col" class="px-6 py-3 text-center">Ordem</th>
                                    <th scope="col" class="px-6 py-3 text-center">Opções</th>
                                    <th scope="col" class="px-6 py-3 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($locations as $location)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $location->name }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        {{ $location->order ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-6">
                                            <!-- Toggle PDF -->
                                            <div class="flex items-center gap-3">
                                                <form method="POST" action="{{ route('admin.personalization-prices.locations.toggle-pdf', $location) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label class="cursor-pointer">
                                                        <input type="checkbox" onchange="this.form.submit()" class="sr-only glass-toggle-input" {{ $location->show_in_pdf ? 'checked' : '' }}>
                                                        <div class="glass-toggle"></div>
                                                    </label>
                                                </form>
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">PDF</span>
                                            </div>
                                            
                                            <!-- Toggle Ativa -->
                                            <div class="flex items-center gap-3">
                                                <form method="POST" action="{{ route('admin.personalization-prices.locations.toggle', $location) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="active" value="{{ $location->active ? 0 : 1 }}">
                                                    <label class="cursor-pointer">
                                                        <input type="checkbox" onchange="this.form.submit()" class="sr-only glass-toggle-input" {{ $location->active ? 'checked' : '' }}>
                                                        <div class="glass-toggle"></div>
                                                    </label>
                                                </form>
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Ativa</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.personalization-prices.locations.destroy', $location) }}" id="delete-location-form-{{ $location->id }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="openDeleteLocationModal('delete-location-form-{{ $location->id }}', '{{ addslashes($location->name) }}')" 
                                                    class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all opacity-0 group-hover:opacity-100 focus:opacity-100" 
                                                    title="Remover localização">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <p class="font-medium">Nenhuma localização cadastrada</p>
                                            <p class="text-xs mt-1">Adicione novas localizações usando o formulário acima.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="delete-location-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity backdrop-blur-sm"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Remover localização</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Tem certeza que deseja remover a localização <span id="delete-location-name" class="font-medium text-gray-900 dark:text-white"></span>? Esta ação não pode ser desfeita.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="confirmDeleteLocation()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                        Remover
                    </button>
                    <button type="button" onclick="closeDeleteLocationModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let deleteLocationFormId = null;

    function openDeleteLocationModal(formId, name) {
        deleteLocationFormId = formId;
        document.getElementById('delete-location-name').textContent = name;
        document.getElementById('delete-location-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteLocationModal() {
        deleteLocationFormId = null;
        document.getElementById('delete-location-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmDeleteLocation() {
        if (!deleteLocationFormId) return;
        const form = document.getElementById(deleteLocationFormId);
        if (form) {
            form.submit();
        }
        closeDeleteLocationModal();
    }
</script>
@endpush
