@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">1</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Edição de Pedido</span>
                        <p class="text-xs text-gray-500">Etapa 1 de 5 - Dados do Cliente</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600">20%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 20%"></div>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                <div>
                        <h1 class="text-lg font-semibold text-gray-900">Editar Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
                        <p class="text-sm text-gray-600">Cliente: {{ $order->client->name }}</p>
                </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Buscar Cliente Existente -->
                <div class="mb-6" x-data="{ showSearch: false }" @click.away="">
                    <div class="bg-indigo-50 rounded-md border border-indigo-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-indigo-100 rounded-md flex items-center justify-center">
                                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Buscar Cliente Existente</h3>
                                    <p class="text-xs text-gray-600">Encontre um cliente já cadastrado no sistema</p>
                </div>
                            </div>
                            <button @click="showSearch = !showSearch" 
                                    class="px-3 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                <span x-show="!showSearch">Mostrar</span>
                                <span x-show="showSearch">Ocultar</span>
                            </button>
            </div>

                        <div x-show="showSearch" x-transition class="space-y-3">
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="text" id="search-client" placeholder="Digite nome, telefone ou CPF..." 
                                           class="w-full pl-3 pr-10 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        </div>
                        </div>
                                <button type="button" onclick="searchClient()" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                                    Buscar
                                </button>
                        </div>
                            <div id="search-results" class="space-y-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Formulário -->
                <form method="POST" action="{{ route('orders.edit.client') }}" id="client-form" class="space-y-6">
                @csrf
                    <input type="hidden" id="client_id" name="client_id" value="{{ $order->client->id }}">

                    <!-- Seção: Informações Básicas -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Informações Básicas</h2>
                                </div>

                        <div class="bg-gray-50 rounded-md p-4">
                                <div>
                                <label for="name" class="block text-xs font-medium text-gray-700 mb-1">Nome Completo *</label>
                                <input id="name" name="name" type="text"
                                       value="{{ $order->client->name }}"
                                       placeholder="Digite o nome completo do cliente"
                                       class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Informações de Contato</h2>
                            </div>

                        <div class="bg-gray-50 rounded-md p-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="phone_primary" class="block text-xs font-medium text-gray-700 mb-1">Telefone Principal *</label>
                                    <input id="phone_primary" name="phone_primary" type="text"
                                           value="{{ $order->client->phone_primary }}"
                                           placeholder="(00) 00000-0000"
                                           class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                                <div>
                                    <label for="phone_secondary" class="block text-xs font-medium text-gray-700 mb-1">Telefone Secundário</label>
                                    <input id="phone_secondary" name="phone_secondary" type="text"
                                           value="{{ $order->client->phone_secondary }}"
                                           placeholder="(00) 00000-0000"
                                           class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                    </div>
                </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                                    <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                                    <input id="email" name="email" type="email"
                                           value="{{ $order->client->email }}"
                                           placeholder="cliente@email.com"
                                           class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                        <div>
                                    <label for="cpf_cnpj" class="block text-xs font-medium text-gray-700 mb-1">CPF/CNPJ</label>
                                    <input id="cpf_cnpj" name="cpf_cnpj" type="text"
                                           value="{{ $order->client->cpf_cnpj }}"
                                           placeholder="000.000.000-00"
                                           class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                </div>
                        </div>
                    </div>
                </div>

                    <!-- Seção: Endereço -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Endereço</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4 space-y-4">
                            <div>
                                <label for="address" class="block text-xs font-medium text-gray-700 mb-1">Endereço Completo</label>
                                <input id="address" name="address" type="text"
                                       value="{{ $order->client->address }}"
                                       placeholder="Rua, número, bairro"
                                       class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition-all text-sm">
                            </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                                    <label for="city" class="block text-xs font-medium text-gray-700 mb-1">Cidade</label>
                                    <input id="city" name="city" type="text"
                                           value="{{ $order->client->city }}"
                                           placeholder="Nome da cidade"
                                           class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                        <div>
                                    <label for="state" class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                    <select id="state" name="state"
                                            class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                        <option value="">Selecione o estado</option>
                                        <option value="AC" {{ $order->client->state == 'AC' ? 'selected' : '' }}>Acre (AC)</option>
                                        <option value="AL" {{ $order->client->state == 'AL' ? 'selected' : '' }}>Alagoas (AL)</option>
                                        <option value="AP" {{ $order->client->state == 'AP' ? 'selected' : '' }}>Amapá (AP)</option>
                                        <option value="AM" {{ $order->client->state == 'AM' ? 'selected' : '' }}>Amazonas (AM)</option>
                                        <option value="BA" {{ $order->client->state == 'BA' ? 'selected' : '' }}>Bahia (BA)</option>
                                        <option value="CE" {{ $order->client->state == 'CE' ? 'selected' : '' }}>Ceará (CE)</option>
                                        <option value="DF" {{ $order->client->state == 'DF' ? 'selected' : '' }}>Distrito Federal (DF)</option>
                                        <option value="ES" {{ $order->client->state == 'ES' ? 'selected' : '' }}>Espírito Santo (ES)</option>
                                        <option value="GO" {{ $order->client->state == 'GO' ? 'selected' : '' }}>Goiás (GO)</option>
                                        <option value="MA" {{ $order->client->state == 'MA' ? 'selected' : '' }}>Maranhão (MA)</option>
                                        <option value="MT" {{ $order->client->state == 'MT' ? 'selected' : '' }}>Mato Grosso (MT)</option>
                                        <option value="MS" {{ $order->client->state == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul (MS)</option>
                                        <option value="MG" {{ $order->client->state == 'MG' ? 'selected' : '' }}>Minas Gerais (MG)</option>
                                        <option value="PA" {{ $order->client->state == 'PA' ? 'selected' : '' }}>Pará (PA)</option>
                                        <option value="PB" {{ $order->client->state == 'PB' ? 'selected' : '' }}>Paraíba (PB)</option>
                                        <option value="PR" {{ $order->client->state == 'PR' ? 'selected' : '' }}>Paraná (PR)</option>
                                        <option value="PE" {{ $order->client->state == 'PE' ? 'selected' : '' }}>Pernambuco (PE)</option>
                                        <option value="PI" {{ $order->client->state == 'PI' ? 'selected' : '' }}>Piauí (PI)</option>
                                        <option value="RJ" {{ $order->client->state == 'RJ' ? 'selected' : '' }}>Rio de Janeiro (RJ)</option>
                                        <option value="RN" {{ $order->client->state == 'RN' ? 'selected' : '' }}>Rio Grande do Norte (RN)</option>
                                        <option value="RS" {{ $order->client->state == 'RS' ? 'selected' : '' }}>Rio Grande do Sul (RS)</option>
                                        <option value="RO" {{ $order->client->state == 'RO' ? 'selected' : '' }}>Rondônia (RO)</option>
                                        <option value="RR" {{ $order->client->state == 'RR' ? 'selected' : '' }}>Roraima (RR)</option>
                                        <option value="SC" {{ $order->client->state == 'SC' ? 'selected' : '' }}>Santa Catarina (SC)</option>
                                        <option value="SP" {{ $order->client->state == 'SP' ? 'selected' : '' }}>São Paulo (SP)</option>
                                        <option value="SE" {{ $order->client->state == 'SE' ? 'selected' : '' }}>Sergipe (SE)</option>
                                        <option value="TO" {{ $order->client->state == 'TO' ? 'selected' : '' }}>Tocantins (TO)</option>
                                    </select>
                        </div>
                        <div>
                                    <label for="zip_code" class="block text-xs font-medium text-gray-700 mb-1">CEP</label>
                                    <input id="zip_code" name="zip_code" type="text"
                                           value="{{ $order->client->zip_code }}"
                                           placeholder="00000-000"
                                           class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                        </div>
                    </div>
                </div>

                    <!-- Seção: Categoria -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Categoria do Cliente</h2>
                </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <div>
                                <label for="category" class="block text-xs font-medium text-gray-700 mb-1">Tipo de Cliente</label>
                                <select id="category" name="category"
                                        class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 transition-all text-sm">
                                    <option value="">Selecione a categoria do cliente</option>
                                    <option value="Varejo" {{ $order->client->category == 'Varejo' ? 'selected' : '' }}>Varejo</option>
                                    <option value="Atacado" {{ $order->client->category == 'Atacado' ? 'selected' : '' }}>Atacado</option>
                                    <option value="Revenda" {{ $order->client->category == 'Revenda' ? 'selected' : '' }}>Revenda</option>
                                    <option value="Empresa" {{ $order->client->category == 'Empresa' ? 'selected' : '' }}>Empresa</option>
                                    <option value="Particular" {{ $order->client->category == 'Particular' ? 'selected' : '' }}>Particular</option>
                                </select>
                            </div>
                        </div>
                </div>

                <!-- Botões de Ação -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <a href="{{ route('orders.show', $order->id) }}" 
                           class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        Cancelar
                    </a>
                    <button type="submit" 
                                class="flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                            Continuar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                    </button>
                </div>
            </form>
                </div>

        </div>
    </div>

    <script>
        // Debug: capturar erros globais
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });
        const clientSearchUrl = "{{ url('/api/clients/search') }}";

        // Debug: verificar se o formulário está sendo enviado
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('client-form');
            if (form) {
                form.addEventListener('submit', function(e) {
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

            fetch(`${clientSearchUrl}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<p class="text-sm text-gray-500">Nenhum cliente encontrado</p>';
                        return;
                    }

                    resultsDiv.innerHTML = data.map(client => `
                        <div class="p-3 bg-white rounded-md border border-gray-200 hover:border-indigo-400 hover:shadow-sm cursor-pointer transition-all"
                             onclick='fillClientData(${JSON.stringify(client)})'>
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 bg-indigo-100 rounded-md flex items-center justify-center">
                                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                        </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">${client.name}</div>
                                    <div class="text-xs text-gray-600">
                                        ${client.phone_primary || ''} ${client.email ? '• ' + client.email : ''}
                        </div>
                    </div>
                                <div class="text-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                '<div class="p-3 bg-indigo-50 border border-indigo-200 rounded-md">' +
                '<div class="flex items-center space-x-2">' +
                '<svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' +
                '</svg>' +
                '<p class="text-sm font-medium text-indigo-800">Cliente selecionado com sucesso!</p>' +
                '</div>' +
                '<p class="text-xs text-indigo-600 mt-1">Você pode editar os dados se necessário antes de continuar.</p>' +
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
@endsection
