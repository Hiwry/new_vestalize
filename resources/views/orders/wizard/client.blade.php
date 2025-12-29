@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">1</div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Dados do Cliente</span>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Etapa 1 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-slate-400 font-medium">Progresso</div>
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">20%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-slate-800 rounded-full h-2.5 shadow-inner">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-500 dark:to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out shadow-lg shadow-indigo-500/30 dark:shadow-indigo-600/30" style="width: 20%"></div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl dark:shadow-2xl dark:shadow-black/20 border border-gray-200 dark:border-slate-800 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-900/50">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ isset($editData) ? 'Edição de Pedido' : 'Novo Pedido' }}</h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Informe os dados do cliente para continuar</p>
                    </div>
                </div>
            </div>

            <div class="p-6">

            <!-- Buscar Cliente Existente -->
            <div class="mb-6" x-data="{ showSearch: false }" @click.away="">
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100/50 dark:from-indigo-900/20 dark:to-indigo-900/10 rounded-xl border-2 border-indigo-200 dark:border-indigo-800/30 p-5 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-600 dark:bg-indigo-500 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-600/20">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Buscar Cliente Existente</h3>
                                <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Encontre um cliente já cadastrado no sistema</p>
                            </div>
                        </div>
                        <button @click="showSearch = !showSearch" 
                                class="px-4 py-2 text-sm font-semibold text-indigo-700 dark:text-indigo-300 hover:text-indigo-900 dark:hover:text-white bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 rounded-lg transition-all border border-indigo-200 dark:border-indigo-700 shadow-sm">
                            <span x-show="!showSearch">Mostrar</span>
                            <span x-show="showSearch">Ocultar</span>
                        </button>
                    </div>
                    
                    <div x-show="showSearch" x-transition class="space-y-3 mt-4">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" id="search-client" placeholder="Digite nome, telefone ou CPF..." 
                                       class="w-full pl-4 pr-10 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 transition-all text-sm font-medium">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" onclick="searchClient()" 
                                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 dark:from-indigo-500 dark:to-indigo-600 hover:from-indigo-700 hover:to-indigo-800 dark:hover:from-indigo-600 dark:hover:to-indigo-700 text-white rounded-lg transition-all text-sm font-semibold shadow-lg shadow-indigo-500/30 dark:shadow-indigo-600/30 hover:scale-105 transform">
                                Buscar
                            </button>
                        </div>
                        <div id="search-results" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <form method="POST" action="{{ isset($editData) ? route('orders.edit.client') : route('orders.wizard.client') }}" id="client-form" class="space-y-6">
                @csrf
                <input type="hidden" id="client_id" name="client_id" value="{{ isset($editData) ? ($editData['client']['id'] ?? '') : '' }}">

                <!-- Seção: Informações Básicas -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/10 rounded-lg flex items-center justify-center ring-2 ring-indigo-100 dark:ring-indigo-500/20">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Informações Básicas</h2>
                    </div>

                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-800/50 dark:to-slate-800/30 rounded-xl border border-gray-200 dark:border-slate-700/50 p-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Nome Completo *</label>
                            <input id="name" name="name" type="text"
                                   value="{{ isset($editData) ? ($editData['client']['name'] ?? '') : '' }}"
                                   placeholder="Digite o nome completo do cliente"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 transition-all text-sm font-medium">
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

                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/30 dark:from-emerald-900/10 dark:to-emerald-900/5 rounded-xl border border-emerald-200 dark:border-emerald-800/30 p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone_primary" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Telefone Principal *</label>
                                <input id="phone_primary" name="phone_primary" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['phone_primary'] ?? '') : '' }}"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="phone_secondary" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Telefone Secundário</label>
                                <input id="phone_secondary" name="phone_secondary" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['phone_secondary'] ?? '') : '' }}"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Email</label>
                                <input id="email" name="email" type="email"
                                       value="{{ isset($editData) ? ($editData['client']['email'] ?? '') : '' }}"
                                       placeholder="cliente@email.com"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="cpf_cnpj" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">CPF/CNPJ</label>
                                <input id="cpf_cnpj" name="cpf_cnpj" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['cpf_cnpj'] ?? '') : '' }}"
                                       placeholder="000.000.000-00"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Endereço -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center ring-2 ring-blue-100 dark:ring-blue-500/20">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Endereço</h2>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-blue-100/30 dark:from-blue-900/10 dark:to-blue-900/5 rounded-xl border border-blue-200 dark:border-blue-800/30 p-5 space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Endereço Completo</label>
                            <input id="address" name="address" type="text"
                                   value="{{ isset($editData) ? ($editData['client']['address'] ?? '') : '' }}"
                                   placeholder="Rua, número, bairro"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Cidade</label>
                                <input id="city" name="city" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['city'] ?? '') : '' }}"
                                       placeholder="Nome da cidade"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Estado</label>
                                <select id="state" name="state"
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                                    <option value="">Selecione o estado</option>
                                    <option value="AC" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'AC') ? 'selected' : '' }}>Acre (AC)</option>
                                    <option value="AL" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'AL') ? 'selected' : '' }}>Alagoas (AL)</option>
                                    <option value="AP" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'AP') ? 'selected' : '' }}>Amapá (AP)</option>
                                    <option value="AM" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'AM') ? 'selected' : '' }}>Amazonas (AM)</option>
                                    <option value="BA" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'BA') ? 'selected' : '' }}>Bahia (BA)</option>
                                    <option value="CE" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'CE') ? 'selected' : '' }}>Ceará (CE)</option>
                                    <option value="DF" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'DF') ? 'selected' : '' }}>Distrito Federal (DF)</option>
                                    <option value="ES" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'ES') ? 'selected' : '' }}>Espírito Santo (ES)</option>
                                    <option value="GO" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'GO') ? 'selected' : '' }}>Goiás (GO)</option>
                                    <option value="MA" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'MA') ? 'selected' : '' }}>Maranhão (MA)</option>
                                    <option value="MT" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'MT') ? 'selected' : '' }}>Mato Grosso (MT)</option>
                                    <option value="MS" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'MS') ? 'selected' : '' }}>Mato Grosso do Sul (MS)</option>
                                    <option value="MG" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'MG') ? 'selected' : '' }}>Minas Gerais (MG)</option>
                                    <option value="PA" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'PA') ? 'selected' : '' }}>Pará (PA)</option>
                                    <option value="PB" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'PB') ? 'selected' : '' }}>Paraíba (PB)</option>
                                    <option value="PR" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'PR') ? 'selected' : '' }}>Paraná (PR)</option>
                                    <option value="PE" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'PE') ? 'selected' : '' }}>Pernambuco (PE)</option>
                                    <option value="PI" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'PI') ? 'selected' : '' }}>Piauí (PI)</option>
                                    <option value="RJ" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'RJ') ? 'selected' : '' }}>Rio de Janeiro (RJ)</option>
                                    <option value="RN" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'RN') ? 'selected' : '' }}>Rio Grande do Norte (RN)</option>
                                    <option value="RS" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'RS') ? 'selected' : '' }}>Rio Grande do Sul (RS)</option>
                                    <option value="RO" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'RO') ? 'selected' : '' }}>Rondônia (RO)</option>
                                    <option value="RR" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'RR') ? 'selected' : '' }}>Roraima (RR)</option>
                                    <option value="SC" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'SC') ? 'selected' : '' }}>Santa Catarina (SC)</option>
                                    <option value="SP" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'SP') ? 'selected' : '' }}>São Paulo (SP)</option>
                                    <option value="SE" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'SE') ? 'selected' : '' }}>Sergipe (SE)</option>
                                    <option value="TO" {{ (isset($editData) && ($editData['client']['state'] ?? '') == 'TO') ? 'selected' : '' }}>Tocantins (TO)</option>
                                </select>
                            </div>
                            <div>
                                <label for="zip_code" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">CEP</label>
                                <input id="zip_code" name="zip_code" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['zip_code'] ?? '') : '' }}"
                                       placeholder="00000-000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Categoria -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-500/10 rounded-lg flex items-center justify-center ring-2 ring-amber-100 dark:ring-amber-500/20">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Categoria do Cliente</h2>
                    </div>

                    <div class="bg-gradient-to-br from-amber-50 to-amber-100/30 dark:from-amber-900/10 dark:to-amber-900/5 rounded-xl border border-amber-200 dark:border-amber-800/30 p-5">
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Tipo de Cliente</label>
                            <select id="category" name="category"
                                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:focus:ring-amber-900/20 transition-all text-sm font-medium">
                                <option value="">Selecione a categoria do cliente</option>
                                <option value="Varejo" {{ (isset($editData) && ($editData['client']['category'] ?? '') == 'Varejo') ? 'selected' : '' }}>Varejo</option>
                                <option value="Atacado" {{ (isset($editData) && ($editData['client']['category'] ?? '') == 'Atacado') ? 'selected' : '' }}>Atacado</option>
                                <option value="Revenda" {{ (isset($editData) && ($editData['client']['category'] ?? '') == 'Revenda') ? 'selected' : '' }}>Revenda</option>
                                <option value="Empresa" {{ (isset($editData) && ($editData['client']['category'] ?? '') == 'Empresa') ? 'selected' : '' }}>Empresa</option>
                                <option value="Particular" {{ (isset($editData) && ($editData['client']['category'] ?? '') == 'Particular') ? 'selected' : '' }}>Particular</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ isset($editData) ? route('orders.show', $order->id) : '/' }}" 
                       class="inline-flex items-center px-5 py-2.5 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 border border-gray-200 dark:border-slate-700 rounded-lg transition-all text-sm font-medium shadow-sm hover:shadow">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 dark:from-indigo-500 dark:to-indigo-600 hover:from-indigo-700 hover:to-indigo-800 dark:hover:from-indigo-600 dark:hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl shadow-indigo-500/30 dark:shadow-indigo-600/30 transition-all transform hover:scale-105 text-sm"
                            onclick="console.log('Button clicked'); return true;">
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

@push('scripts')
<script>
        // Debug: capturar erros globais
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });

        // Debug: verificar se o formulário está sendo enviado
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('client-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Prevent submission if already submitting
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        return false;
                    }

                    console.log('Form submit event triggered');
                    console.log('Form action:', form.action);
                    console.log('Form method:', form.method);
                    
                    // Verificar se todos os campos obrigatórios estão preenchidos
                    const name = document.getElementById('name').value;
                    const phone = document.getElementById('phone_primary').value;
                    
                    console.log('Name:', name);
                    console.log('Phone:', phone);
                    
                    if (!name || !phone) {
                        console.error('Campos obrigatórios não preenchidos');
                        e.preventDefault();
                        return false;
                    }
                    
                    console.log('Form validation passed, submitting...');
                    
                    // Disable button and show processing state
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
            } else {
                console.error('Form not found!');
            }
        });

        function searchClient() {
            const query = document.getElementById('search-client').value;
            const resultsDiv = document.getElementById('search-results');
            
            if (query.length < 3) {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500">Digite ao menos 3 caracteres para buscar</p>';
                return;
            }

            fetch(`/api/clients/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<p class="text-sm text-gray-500">Nenhum cliente encontrado</p>';
                        return;
                    }

                    resultsDiv.innerHTML = data.map(client => `
                        <div class="p-4 bg-white dark:bg-slate-800 rounded-lg border-2 border-gray-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-black/20 cursor-pointer transition-all group"
                             onclick='fillClientData(${JSON.stringify(client)})'>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/20 dark:shadow-indigo-600/20 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">${client.name}</div>
                                    <div class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">
                                        ${client.phone_primary || ''} ${client.email ? '• ' + client.email : ''}
                                    </div>
                                </div>
                                <div class="text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    resultsDiv.innerHTML = '<p class="text-sm text-red-600">Erro ao buscar clientes</p>';
                });
        }

        function fillClientData(client) {
            document.getElementById('client_id').value = client.id;
            document.getElementById('name').value = client.name || '';
            document.getElementById('phone_primary').value = client.phone_primary || '';
            document.getElementById('phone_secondary').value = client.phone_secondary || '';
            document.getElementById('email').value = client.email || '';
            document.getElementById('cpf_cnpj').value = client.cpf_cnpj || '';
            document.getElementById('address').value = client.address || '';
            document.getElementById('city').value = client.city || '';
            document.getElementById('state').value = client.state || '';
            document.getElementById('zip_code').value = client.zip_code || '';
            document.getElementById('category').value = client.category || '';
            
            document.getElementById('search-results').innerHTML = 
                '<div class="p-4 bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-900/10 border-2 border-emerald-200 dark:border-emerald-800/30 rounded-xl shadow-sm">' +
                '<div class="flex items-center space-x-3">' +
                '<div class="w-10 h-10 bg-emerald-600 dark:bg-emerald-500 rounded-lg flex items-center justify-center shadow-lg shadow-emerald-600/20">' +
                '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
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

        // Buscar ao pressionar Enter
        document.getElementById('search-client').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchClient();
            }
        });

        // Validação em tempo real com regex
        function validateField(fieldId, regex, errorMessage) {
            const field = document.getElementById(fieldId);
            const value = field.value.trim();
            const isValid = regex.test(value);
            
            // Remove classes de erro existentes
            field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            field.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
            
            // Remove mensagem de erro existente
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            
            if (value && !isValid) {
                field.classList.remove('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
                field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error mt-1 text-xs text-red-600 flex items-center';
                errorDiv.innerHTML = `
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    ${errorMessage}
                `;
                field.parentNode.appendChild(errorDiv);
            }
            
            return isValid;
        }

        // Regex patterns
        const patterns = {
            phone: /^\(\d{2}\)\s\d{4,5}-\d{4}$/,
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            cpf: /^\d{3}\.\d{3}\.\d{3}-\d{2}$/,
            cnpj: /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/,
            cep: /^\d{5}-\d{3}$/
        };

        // Validação de telefone
        document.getElementById('phone_primary').addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                validateField('phone_primary', patterns.phone, 'Formato inválido. Use: (00) 00000-0000');
            }
        });

        document.getElementById('phone_secondary').addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                validateField('phone_secondary', patterns.phone, 'Formato inválido. Use: (00) 00000-0000');
            }
        });

        // Validação de email
        document.getElementById('email').addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                validateField('email', patterns.email, 'Formato de email inválido');
            }
        });

        // Validação de CPF/CNPJ
        document.getElementById('cpf_cnpj').addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                const isValidCpf = patterns.cpf.test(value);
                const isValidCnpj = patterns.cnpj.test(value);
                
                if (!isValidCpf && !isValidCnpj) {
                    validateField('cpf_cnpj', /^$/, 'Formato inválido. Use: 000.000.000-00 ou 00.000.000/0000-00');
                }
            }
        });

        // Validação de CEP
        document.getElementById('zip_code').addEventListener('blur', function() {
            const value = this.value.trim();
            if (value) {
                validateField('zip_code', patterns.cep, 'Formato inválido. Use: 00000-000');
            }
        });

        // Máscaras de entrada
        function applyPhoneMask(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    if (value.length <= 2) {
                        value = value.replace(/(\d{2})/, '($1) ');
                    } else if (value.length <= 7) {
                        value = value.replace(/(\d{2})(\d{4,5})/, '($1) $2');
                    } else {
                        value = value.replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
                    }
                }
                e.target.value = value;
            });
        }

        function applyCpfCnpjMask(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    // CPF
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                } else {
                    // CNPJ
                    value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                }
                e.target.value = value;
            });
        }

        function applyCepMask(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 8) {
                    value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
                }
                e.target.value = value;
            });
        }

        // Aplicar máscaras
        applyPhoneMask(document.getElementById('phone_primary'));
        applyPhoneMask(document.getElementById('phone_secondary'));
        applyCpfCnpjMask(document.getElementById('cpf_cnpj'));
        applyCepMask(document.getElementById('zip_code'));
</script>
@endpush
@endsection

