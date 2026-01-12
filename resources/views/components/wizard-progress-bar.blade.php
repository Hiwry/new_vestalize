{{--
    Wizard Progress Bar Component
    Mostra o progresso do wizard com steps numerados e labels
    
    Uso:
    <x-wizard-progress-bar :steps="['Cliente', 'Itens', 'Personalização', 'Pagamento', 'Confirmação']" :current="2" />
--}}

@props(['steps' => [], 'current' => 1])

<div class="w-full mb-6 px-4 sm:px-0">
    {{-- Mobile: Compact Progress --}}
    <div class="md:hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                Etapa {{ $current }} de {{ count($steps) }}
            </span>
            <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">
                {{ $steps[$current - 1] ?? '' }}
            </span>
        </div>
        
        {{-- Progress Bar --}}
        <div class="relative h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full transition-all duration-500 ease-out"
                 style="width: {{ (($current - 1) / (count($steps) - 1)) * 100 }}%"></div>
        </div>
        
        {{-- Step Indicators (dots) --}}
        <div class="flex justify-between mt-3">
            @foreach($steps as $index => $step)
                @php $stepNumber = $index + 1; @endphp
                <div class="flex flex-col items-center">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                        {{ $current > $stepNumber 
                            ? 'bg-green-500 text-white' 
                            : ($current == $stepNumber 
                                ? 'bg-indigo-600 text-white ring-2 ring-offset-2 ring-indigo-300 dark:ring-offset-gray-800' 
                                : 'bg-gray-200 dark:bg-gray-700 text-gray-500') }}">
                        @if($current > $stepNumber)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Desktop: Full Progress Bar --}}
    <div class="hidden md:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            @foreach($steps as $index => $step)
                @php $stepNumber = $index + 1; @endphp
                
                {{-- Step Circle --}}
                <div class="flex flex-col items-center relative z-10">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                        {{ $current > $stepNumber 
                            ? 'bg-green-500 text-white shadow-lg shadow-green-500/30' 
                            : ($current == $stepNumber 
                                ? 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30 ring-4 ring-indigo-100 dark:ring-indigo-900/50' 
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500') }}">
                        @if($current > $stepNumber)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>
                    
                    {{-- Step Label --}}
                    <span class="mt-2 text-xs font-medium 
                        {{ $current >= $stepNumber 
                            ? 'text-gray-900 dark:text-white' 
                            : 'text-gray-400 dark:text-gray-500' }}">
                        {{ $step }}
                    </span>
                </div>
                
                {{-- Connector Line (não mostrar após último step) --}}
                @if($index < count($steps) - 1)
                    <div class="flex-1 mx-4 h-1 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 relative">
                        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-green-500 to-green-400 rounded-full transition-all duration-500 ease-out"
                             style="width: {{ $current > $stepNumber + 1 ? '100%' : ($current == $stepNumber + 1 ? '50%' : '0%') }}"></div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
