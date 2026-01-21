@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#7c3aed] text-white stay-white force-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]" style="color: #ffffff !important; -webkit-text-fill-color: #ffffff !important;">1</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Dados do Cliente</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 1 de 4</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-[#7c3aed] dark:text-[#a78bfa]">25%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-2.5 shadow-inner">
                <div class="bg-[#7c3aed] h-2.5 rounded-full transition-all duration-500 ease-out" style="width: 25%"></div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-900/50">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                             <div class="w-8 h-8 bg-[#7c3aed] force-white rounded-lg flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-none border border-[#7c3aed]">
                                <svg class="w-5 h-5 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                             </div>
                             Novo Orçamento
                        </h1>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 pl-10">Informe os dados do cliente para continuar</p>
            </div>

            <div class="p-6">

            <!-- Buscar Cliente Existente -->
            <div class="mb-6" x-data="{ showSearch: false }" @click.away="">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 dark:from-purple-900/20 dark:to-purple-900/10 rounded-xl border-2 border-purple-200 dark:border-purple-800/30 p-5 shadow-sm transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center shadow-md border border-purple-100 dark:border-purple-900/30">
                                <svg class="w-5 h-5 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Buscar Cliente Existente</h3>
                                <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Encontre um cliente já cadastrado no sistema</p>
                            </div>
                        </div>
                        <button @click="showSearch = !showSearch" 
                                class="px-4 py-2 text-sm font-semibold text-[#7c3aed] dark:text-purple-300 bg-white dark:bg-slate-800 rounded-lg transition-all border border-purple-200 dark:border-purple-700 shadow-sm">
                            <span x-show="!showSearch">Mostrar</span>
                            <span x-show="showSearch">Ocultar</span>
                        </button>
                    </div>
                    
                    <div x-show="showSearch" x-transition class="space-y-3 mt-4">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" id="search-client" placeholder="Digite nome, telefone ou CPF..." 
                                       class="w-full pl-4 pr-10 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-[#7c3aed] dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-100 dark:focus:ring-purple-900/20 transition-all text-sm font-medium">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" onclick="window.runClientSearch()" style="color: white !important;" 
                                    class="px-6 py-3 bg-[#7c3aed] text-white stay-white rounded-lg transition-colors text-sm font-semibold shadow-sm">
                                Buscar
                            </button>
                        </div>
                        <div id="search-results" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Toggle Cliente Rápido -->
            <div class="mb-6" x-data="{ quickMode: false }">
                <div class="bg-gradient-to-br from-emerald-50 to-teal-100/50 dark:from-emerald-900/20 dark:to-teal-900/10 rounded-xl border-2 border-emerald-200 dark:border-emerald-800/30 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center shadow-md border border-emerald-100 dark:border-emerald-900/30">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Cliente Rápido</h3>
                                <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Preencha apenas nome e telefone</p>
                            </div>
                        </div>
                        <button type="button" @click="quickMode = !quickMode; if(quickMode) { $dispatch('quick-mode-on') } else { $dispatch('quick-mode-off') }" 
                                :class="quickMode ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            <span :class="quickMode ? 'translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <form method="POST" action="{{ route('budget.client') }}" id="client-form" class="space-y-6" x-data="{ quickMode: false }" @quick-mode-on.window="quickMode = true" @quick-mode-off.window="quickMode = false">
                @csrf
                <input type="hidden" id="client_id" name="client_id" value="{{ session('budget.client.id', '') }}">
                <input type="hidden" name="quick_client" :value="quickMode ? '1' : '0'">

                <!-- Seção: Informações Básicas -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-500/10 rounded-lg flex items-center justify-center ring-2 ring-purple-100 dark:ring-purple-500/20">
                            <svg class="w-5 h-5 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Informações Básicas</h2>
                    </div>

                    <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Nome Completo *</label>
                            <input id="name" name="name" type="text"
                                   value="{{ session('budget.client.name', '') }}"
                                   placeholder="Digite o nome completo do cliente"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-[#7c3aed] dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-100 dark:focus:ring-purple-900/20 transition-all text-sm font-medium">
                            @error('name')
                                <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Seção: Contato -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center ring-2 ring-emerald-100 dark:ring-emerald-500/20">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Informações de Contato</h2>
                    </div>

                    <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone_primary" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Telefone Principal *</label>
                                <input id="phone_primary" name="phone_primary" type="text"
                                       value="{{ session('budget.client.phone_primary', '') }}"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="phone_secondary" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Telefone Secundário</label>
                                <input id="phone_secondary" name="phone_secondary" type="text"
                                       value="{{ session('budget.client.phone_secondary', '') }}"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>

                        <div x-show="!quickMode" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Email</label>
                                <input id="email" name="email" type="email"
                                       value="{{ session('budget.client.email', '') }}"
                                       placeholder="cliente@email.com"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="cpf_cnpj" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">CPF/CNPJ</label>
                                <input id="cpf_cnpj" name="cpf_cnpj" type="text"
                                       value="{{ session('budget.client.cpf_cnpj', '') }}"
                                       placeholder="000.000.000-00"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Endereço -->
                <div class="space-y-3" x-show="!quickMode" x-transition>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center ring-2 ring-blue-100 dark:ring-blue-500/20">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Endereço</h2>
                    </div>

                    <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Endereço Completo</label>
                            <input id="address" name="address" type="text"
                                   value="{{ session('budget.client.address', '') }}"
                                   placeholder="Rua, número, bairro"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Cidade</label>
                                <input id="city" name="city" type="text"
                                       value="{{ session('budget.client.city', '') }}"
                                       placeholder="Nome da cidade"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Estado</label>
                                <select id="state" name="state"
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                                    <option value="">Selecione o estado</option>
                                    @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" {{ session('budget.client.state') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="zip_code" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">CEP</label>
                                <input id="zip_code" name="zip_code" type="text"
                                       value="{{ session('budget.client.zip_code', '') }}"
                                       placeholder="00000-000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Categoria -->
                <div class="space-y-3" x-show="!quickMode" x-transition>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-500/10 rounded-lg flex items-center justify-center ring-2 ring-amber-100 dark:ring-amber-500/20">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Categoria do Cliente</h2>
                    </div>

                    <div class="bg-white dark:bg-slate-900/50 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm">
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Tipo de Cliente</label>
                            <select id="category" name="category"
                                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:focus:ring-amber-900/20 transition-all text-sm font-medium">
                                <option value="">Selecione a categoria do cliente</option>
                                @foreach(['Varejo', 'Atacado', 'Revenda', 'Empresa', 'Particular'] as $cat)
                                    <option value="{{ $cat }}" {{ session('budget.client.category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ route('budget.index') }}" 
                       class="inline-flex items-center px-5 py-2.5 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 border border-gray-200 dark:border-slate-700 rounded-lg transition-all text-sm font-medium shadow-sm hover:shadow">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                    <button type="submit" 
                            style="color: white !important;"
                            class="inline-flex items-center px-8 py-3 bg-[#7c3aed] text-white stay-white font-semibold rounded-xl shadow-sm transition-colors text-sm">
                        Continuar
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
(function() {
    console.log('Budget Client Wizard Script Loaded');
    const clientSearchUrl = "{{ url('/api/clients/search') }}";

    function initClientPage() {
        console.log('Initializing Budget Client Page...');
        const form = document.getElementById('client-form');
        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', function(e) {
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }
                
                const name = document.getElementById('name').value;
                const phone = document.getElementById('phone_primary').value;
                
                if (!name || !phone) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos Obrigatórios',
                        text: 'Por favor, preencha o Nome e o Telefone Principal.',
                        confirmButtonColor: '#7c3aed'
                    });
                    e.preventDefault();
                    return false;
                }
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processando...
                    `;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                }
                
                form.dataset.submitting = 'true';
            });
            form.dataset.listenerAttached = 'true';
        }

        const searchInput = document.getElementById('search-client');
        if (searchInput && !searchInput.dataset.listenerAttached) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    window.runClientSearch();
                }
            });
            searchInput.dataset.listenerAttached = 'true';
        }

        setupMasksAndValidation();
    }

    function runClientSearch() {
        var queryInput = document.getElementById('search-client');
        var resultsDiv = document.getElementById('search-results');
        if (!queryInput || !resultsDiv) return;

        var query = queryInput.value;
        if (query.length < 3) {
            resultsDiv.innerHTML = '<p class="text-sm text-gray-500">Digite ao menos 3 caracteres para buscar</p>';
            return;
        }

        fetch(clientSearchUrl + '?q=' + encodeURIComponent(query))
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<p class="text-sm text-gray-500">Nenhum cliente encontrado</p>';
                    return;
                }

                resultsDiv.innerHTML = data.map(function(client) {
                    var clientJson = JSON.stringify(client).replace(/'/g, "&#39;");
                    return `
                    <div class="p-4 bg-white dark:bg-slate-800 rounded-lg border-2 border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/20 cursor-pointer transition-all group"
                         onclick='window.fillClientData(${clientJson})'>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#7c3aed] to-[#7c3aed] rounded-lg flex items-center justify-center shadow-lg shadow-[#7c3aed]/20 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">${client.name}</div>
                                <div class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">
                                    ${client.phone_primary || ''} ${client.email ? '• ' + client.email : ''}
                                </div>
                            </div>
                            <div class="text-[#7c3aed] dark:text-[#7c3aed] group-hover:translate-x-1 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                `}).join('');
            })
            .catch(function(error) {
                console.error('Erro:', error);
                resultsDiv.innerHTML = '<p class="text-sm text-red-600">Erro ao buscar clientes</p>';
            });
    }
    window.runClientSearch = runClientSearch;

    function fillClientData(client) {
        const safeSetValue = (id, val) => {
            const el = document.getElementById(id);
            if(el) el.value = val || '';
        };

        safeSetValue('client_id', client.id);
        safeSetValue('name', client.name);
        safeSetValue('phone_primary', client.phone_primary);
        safeSetValue('phone_secondary', client.phone_secondary);
        safeSetValue('email', client.email);
        safeSetValue('cpf_cnpj', client.cpf_cnpj);
        safeSetValue('address', client.address);
        safeSetValue('city', client.city);
        safeSetValue('state', client.state);
        safeSetValue('zip_code', client.zip_code);
        safeSetValue('category', client.category);
        
        const resultsDiv = document.getElementById('search-results');
        if(resultsDiv) {
            resultsDiv.innerHTML = 
                '<div class="p-4 bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-900/10 border-2 border-emerald-200 dark:border-emerald-800/30 rounded-xl shadow-sm">' +
                '<div class="flex items-center space-x-3">' +
                '<div class="w-10 h-10 bg-emerald-600 dark:bg-emerald-500 rounded-lg flex items-center justify-center shadow-lg shadow-emerald-600/20">' +
                '<svg class="w-5 h-5 text-white stay-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' +
                '</svg>' +
                '</div>' +
                '<div class="flex-1">' +
                '<p class="text-sm font-bold text-gray-900 dark:text-white">Cliente selecionado com sucesso!</p>' +
                '<p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Você pode editar os dados se necessário antes de continuar.</p>' +
                '</div>' +
                '</div>' +
                '</div>';
        }
    }
    window.fillClientData = fillClientData;

    function setupMasksAndValidation() {
        const patterns = {
            phone: /^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/,
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            cep: /^\d{5}-?\d{3}$/
        };

        const validateField = (fieldId, regex, errorMessage) => {
            const field = document.getElementById(fieldId);
            if(!field) return true;
            
            const value = field.value.trim();
            const isValid = regex.test(value);
            
            field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            field.classList.add('border-gray-200', 'focus:border-[#7c3aed]', 'focus:ring-[#7c3aed]');
            
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) existingError.remove();
            
            if (value && !isValid) {
                field.classList.remove('border-gray-200', 'focus:border-[#7c3aed]', 'focus:ring-[#7c3aed]');
                field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error mt-1 text-xs text-red-600 flex items-center';
                errorDiv.innerHTML = `<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${errorMessage}`;
                field.parentNode.appendChild(errorDiv);
            }
            return isValid;
        };

        const attachBlur = (id, pattern, msg) => {
            const el = document.getElementById(id);
            if(el) {
                el.removeEventListener('blur', el._blurHandler);
                el._blurHandler = () => { if(el.value.trim()) validateField(id, pattern, msg); };
                el.addEventListener('blur', el._blurHandler);
            }
        };

        attachBlur('phone_primary', patterns.phone, 'Formato inválido.');
        attachBlur('phone_secondary', patterns.phone, 'Formato inválido.');
        attachBlur('email', patterns.email, 'Email inválido.');
        attachBlur('zip_code', patterns.cep, 'CEP inválido.');
        
        const applyMask = (id, masker) => {
            const el = document.getElementById(id);
            if(el) {
                el.removeEventListener('input', el._maskerHandler);
                el._maskerHandler = masker;
                el.addEventListener('input', el._maskerHandler);
            }
        };

        const phoneMask = e => {
            let v = e.target.value.replace(/\D/g,'');
            v = v.replace(/^(\d{2})(\d)/g,"($1) $2");
            v = v.replace(/(\d)(\d{4})$/,"$1-$2");
            e.target.value = v.substring(0, 15);
        };

        applyMask('phone_primary', phoneMask);
        applyMask('phone_secondary', phoneMask);
        
        applyMask('zip_code', e => {
            let v = e.target.value.replace(/\D/g,'');
            v = v.replace(/^(\d{5})(\d)/,"$1-$2");
            e.target.value = v.substring(0, 9);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClientPage);
    } else {
        initClientPage();
    }
})();
</script>
@endpush
@endsection
