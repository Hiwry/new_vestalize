@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Progress Bar -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">1</div>
                <div>
                    <span class="text-base font-medium text-indigo-600 dark:text-indigo-400">Dados do Cliente</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Etapa 1 de 4</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500 dark:text-gray-400">Progresso</div>
                <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">25%</div>
            </div>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
            <div class="bg-indigo-600 dark:bg-indigo-500 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 25%"></div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-md flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Novo Orçamento</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Selecione o cliente para continuar</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('budget.client') }}" x-data="{ 
                selectedClientId: '', 
                useQuickCreate: false,
                clientName: '',
                clientContact: ''
            }">
                @csrf
                <input type="hidden" name="client_id" x-model="selectedClientId">

                <!-- Toggle entre Buscar e Criar Rápido -->
                <div class="mb-4 flex gap-2 border-b border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            @click="useQuickCreate = false"
                            :class="!useQuickCreate ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'"
                            class="px-4 py-2 text-sm font-medium transition">
                        Buscar Cliente
                    </button>
                    <button type="button" 
                            @click="useQuickCreate = true; selectedClientId = ''"
                            :class="useQuickCreate ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'"
                            class="px-4 py-2 text-sm font-medium transition">
                        Criar Rápido
                    </button>
                </div>

                <!-- Buscar Cliente Existente -->
                <div x-show="!useQuickCreate" class="mb-6">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-md border border-indigo-200 dark:border-indigo-800/30 p-4">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Buscar Cliente
                        </label>
                        <input type="text" 
                               id="search-client" 
                               placeholder="Digite nome, telefone ou CPF..." 
                               class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                        
                        <div id="search-results" class="mt-2 max-h-60 overflow-y-auto"></div>
                        
                        <!-- Cliente Selecionado -->
                        <div id="selected-client-indicator" class="mt-3 hidden">
                            <div class="flex items-center text-sm text-green-700 dark:text-green-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Cliente selecionado com sucesso!
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Novo Cliente Completo -->
                    <div class="mt-4">
                        <a href="{{ route('clients.create') }}" 
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                            + Cadastrar Novo Cliente Completo
                        </a>
                    </div>
                </div>

                <!-- Criar Cliente Rápido -->
                <div x-show="useQuickCreate" class="mb-6">
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-md border border-green-200 dark:border-green-800/30 p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Preencha apenas o nome e contato para criar um orçamento rápido. Os demais dados podem ser completados depois.
                        </p>
                        
                        <div class="space-y-4">
                            <!-- Nome -->
                            <div>
                                <label for="client_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nome <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="client_name" 
                                       name="client_name"
                                       x-model="clientName"
                                       x-bind:required="useQuickCreate"
                                       placeholder="Nome do cliente"
                                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                            </div>

                            <!-- Contato -->
                            <div>
                                <label for="client_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Contato (Telefone/WhatsApp) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="client_contact" 
                                       name="client_contact"
                                       x-model="clientContact"
                                       x-bind:required="useQuickCreate"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-500 transition-all text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('budget.index') }}" 
                       class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 text-sm">
                        ← Cancelar
                    </a>
                    <button type="submit" 
                            x-bind:disabled="!useQuickCreate ? !selectedClientId : (!clientName || !clientContact)"
                            x-bind:class="(!useQuickCreate && selectedClientId) || (useQuickCreate && clientName && clientContact) ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="px-6 py-2 text-white rounded-md transition text-sm font-medium">
                        Continuar →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Busca de clientes
    const searchInput = document.getElementById('search-client');
    const searchResults = document.getElementById('search-results');
    let debounceTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimeout);
        const query = this.value;

        // Resetar seleção ao digitar
        const hiddenInput = document.querySelector('[name="client_id"]');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
        
        const form = document.querySelector('form');
        if (form && form.__x) {
            form.__x.$data.selectedClientId = '';
        }
        
        const indicator = document.getElementById('selected-client-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
        }
        
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'dark:bg-indigo-600', 'dark:hover:bg-indigo-700');
            submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
        }

        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        debounceTimeout = setTimeout(() => {
            fetch(`/api/clients/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        searchResults.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-2">Nenhum cliente encontrado</p>';
                        return;
                    }

                    searchResults.innerHTML = data.map(client => `
                        <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded-md border-b border-gray-100 dark:border-gray-700 last:border-0"
                             onclick="selectClient(${client.id}, '${client.name.replace(/'/g, "\\'")}')">
                            <div class="font-medium text-gray-900 dark:text-gray-100">${client.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${client.phone || 'Sem telefone'} • ${client.email || 'Sem email'}</div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Erro ao buscar clientes:', error);
                    searchResults.innerHTML = '<p class="text-sm text-red-500 dark:text-red-400 p-2">Erro ao buscar clientes. Tente novamente.</p>';
                });
        }, 300);
    });

    function selectClient(id, name) {
        // Atualizar o input hidden
        const hiddenInput = document.querySelector('[name="client_id"]');
        if (hiddenInput) {
            hiddenInput.value = id;
            hiddenInput.dispatchEvent(new Event('input'));
        }
        
        // Atualizar o x-model do Alpine.js
        const form = document.querySelector('form');
        if (form && form.__x) {
            form.__x.$data.selectedClientId = id;
        }
        
        // Atualizar campo de busca e limpar resultados
        searchInput.value = name;
        searchResults.innerHTML = '';
        
        // Mostrar indicador de sucesso
        const indicator = document.getElementById('selected-client-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
        }
        
        // Forçar atualização visual do botão
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
            submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'dark:bg-indigo-600', 'dark:hover:bg-indigo-700');
        }
    }
</script>
@endpush
@endsection
