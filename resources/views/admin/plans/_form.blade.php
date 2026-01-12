@csrf

<div class="grid grid-cols-1 md:grid-cols-12 gap-8">
    <!-- Basic Info Column -->
    <div class="md:col-span-12 lg:col-span-7 space-y-6">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 backdrop-blur-xl">
            <h3 class="flex items-center text-xl font-bold text-gray-900 dark:text-white mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3 text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                Detalhes do Plano
            </h3>
            
            <div class="space-y-6">
                <!-- Name Input -->
                <div class="relative group">
                    <x-input-label for="name" :value="__('Nome do Plano')" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <input type="text" name="name" id="name" 
                            class="pl-12 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:ring-opacity-50 transition-all duration-200 py-3.5" 
                            placeholder="Ex: Plano Enterprise" 
                            value="{{ old('name', $plan->name ?? '') }}" required autofocus>
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Price Input -->
                <div class="relative group">
                    <x-input-label for="price" :value="__('Preço Mensal')" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold group-focus-within:text-green-500 transition-colors text-lg">R$</span>
                        </div>
                        <input type="number" name="price" id="price" step="0.01" 
                            class="pl-12 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-400 focus:border-green-500 focus:ring-green-500 focus:ring-2 focus:ring-opacity-50 transition-all duration-200 py-3.5 font-mono text-lg" 
                            placeholder="0.00" 
                            value="{{ old('price', $plan->price ?? '') }}" required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">/mês</span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>

                <!-- Description Input -->
                <div>
                    <x-input-label for="description" :value="__('Descrição')" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300" />
                    <textarea id="description" name="description" 
                        class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 focus:ring-2 focus:ring-opacity-50 transition-all duration-200 py-3 px-4 resize-none" 
                        rows="4" 
                        placeholder="Descreva os benefícios principais deste plano...">{{ old('description', $plan->description ?? '') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    <!-- Limits Column -->
    <div class="md:col-span-12 lg:col-span-5 space-y-6">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 h-full">
            <h3 class="flex items-center text-xl font-bold text-gray-900 dark:text-white mb-6">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg mr-3 text-purple-600 dark:text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                Capacidade do Plano
            </h3>

            <div class="space-y-8">
                <!-- Store Limit -->
                <div>
                    <div class="flex justify-between mb-2">
                        <x-input-label for="limits_stores" :value="__('Limite de Lojas')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <span class="text-xs text-purple-600 dark:text-purple-400 font-medium bg-purple-50 dark:bg-purple-900/20 px-2 py-0.5 rounded-full">Multi-loja</span>
                    </div>
                    <div class="relative rounded-xl shadow-sm">
                        <input type="number" name="limits_stores" id="limits_stores" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-2 focus:ring-opacity-50 transition-all duration-200 py-3.5 pr-12" 
                            value="{{ old('limits_stores', $plan->limits['stores'] ?? 1) }}" required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm font-medium">Lojas</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Digite 9999 para ilimitado
                    </p>
                    <x-input-error :messages="$errors->get('limits_stores')" class="mt-2" />
                </div>

                <!-- User Limit -->
                <div>
                    <div class="flex justify-between mb-2">
                        <x-input-label for="limits_users" :value="__('Limite de Usuários')" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <span class="text-xs text-purple-600 dark:text-purple-400 font-medium bg-purple-50 dark:bg-purple-900/20 px-2 py-0.5 rounded-full">Equipe</span>
                    </div>
                    <div class="relative rounded-xl shadow-sm">
                        <input type="number" name="limits_users" id="limits_users" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-400 focus:border-purple-500 focus:ring-purple-500 focus:ring-2 focus:ring-opacity-50 transition-all duration-200 py-3.5 pr-16" 
                            value="{{ old('limits_users', $plan->limits['users'] ?? 1) }}" required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm font-medium">Usuários</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Digite 9999 para ilimitado
                    </p>
                    <x-input-error :messages="$errors->get('limits_users')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Toggles Section -->
<div class="mt-8 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50" 
     x-data="{ 
        allFeatures: {{ in_array('*', old('features', $plan->features ?? [])) ? 'true' : 'false' }}
     }">
    
    <h3 class="flex items-center text-xl font-bold text-gray-900 dark:text-white mb-8">
        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg mr-3 text-green-600 dark:text-green-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        Funcionalidades Habilitadas
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Master Toggle -->
        <div class="relative flex items-center p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer"
             :class="allFeatures ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 hover:border-indigo-300'"
             @click="allFeatures = !allFeatures">
            
            <div class="flex items-center h-6">
                 <div class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                      :class="allFeatures ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600'">
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                          :class="allFeatures ? 'translate-x-5' : 'translate-x-0'"></span>
                 </div>
                 <input type="checkbox" name="features[]" value="*" x-model="allFeatures" class="hidden">
            </div>
            
            <div class="ml-4">
                <span class="block text-sm font-bold transition-colors"
                      :class="allFeatures ? 'text-indigo-800 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'">
                    Acesso Ilimitado
                </span>
                <span class="block text-xs mt-0.5"
                      :class="allFeatures ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'">
                    Habilita todas as funções
                </span>
            </div>
        </div>

        <!-- Individual Features -->
        @foreach($availableFeatures as $key => $label)
            @php
                $checked = in_array($key, old('features', $plan->features ?? []));
            @endphp
            <div class="relative flex items-center p-4 rounded-xl border transition-all duration-200"
                 x-data="{ checked: {{ $checked ? 'true' : 'false' }} }"
                 :class="{ 
                    'opacity-50 pointer-events-none bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-800': allFeatures,
                    'border-green-500 bg-green-50 dark:bg-green-900/10 shadow-sm': checked && !allFeatures,
                    'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600': !checked && !allFeatures
                 }">
                
                <div class="flex items-center h-6" @click="if(!allFeatures) checked = !checked">
                    <div class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                         :class="checked ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="checked ? 'translate-x-5' : 'translate-x-0'"></span>
                    </div>
                    <input type="checkbox" name="features[]" value="{{ $key }}" x-model="checked" class="hidden">
                </div>
                
                <div class="ml-3 cursor-pointer" @click="if(!allFeatures) checked = !checked">
                    <span class="text-sm font-medium select-none"
                          :class="checked ? 'text-green-900 dark:text-green-300' : 'text-gray-700 dark:text-gray-300'">
                        {{ $label }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('features')" class="mt-2" />
</div>

<!-- Actions -->
<div class="flex items-center justify-end mt-8 gap-4 pb-8">
    <a href="{{ route('admin.plans.index') }}" 
       class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all shadow-sm">
        {{ __('Cancelar') }}
    </a>
    <button type="submit" 
            class="inline-flex justify-center items-center px-6 py-2.5 rounded-xl border border-transparent bg-indigo-600 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all transform hover:-translate-y-0.5">
        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ __('Salvar Plano') }}
    </button>
</div>
