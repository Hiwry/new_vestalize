@extends('layouts.admin')

@section('content')
<style>
    .ow-shell {
        --sh-surface-from: #f3f4f8;
        --sh-surface-to: #eceff4;
        --sh-surface-border: #d8dce6;
        --sh-text-primary: #0f172a;
        --sh-text-secondary: #64748b;
        --sh-card-bg: #ffffff;
        --sh-card-border: #dde2ea;
        --sh-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --sh-accent: #7c3aed;
        --sh-accent-strong: #6d28d9;
        
        background: linear-gradient(180deg, var(--sh-surface-from) 0%, var(--sh-surface-to) 100%);
        border: 1px solid var(--sh-surface-border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        color: var(--sh-text-primary);
    }

    .dark .ow-shell {
        --sh-surface-from: #0d1830;
        --sh-surface-to: #0b1322;
        --sh-surface-border: rgba(148, 163, 184, 0.16);
        --sh-text-primary: #e5edf8;
        --sh-text-secondary: #91a4c0;
        --sh-card-bg: #10203a;
        --sh-card-border: rgba(148, 163, 184, 0.12);
        --sh-card-shadow: none;
        --sh-input-bg: #162847;

        background: linear-gradient(180deg, var(--sh-surface-from) 0%, var(--sh-surface-to) 100%) !important;
        box-shadow: none !important;
        border-color: var(--sh-surface-border) !important;
    }


    .dark .ow-card, .dark .ow-progress, .dark .ow-search-panel, .dark .ow-field-panel, .dark .ow-search-result {
        background-color: var(--sh-card-bg) !important;
        box-shadow: none !important;
    }

    .ow-card, .ow-progress, .ow-search-panel, .ow-field-panel, .ow-search-result {
        background: var(--sh-card-bg) !important;
        border: 1px solid var(--sh-card-border) !important;
        border-radius: 16px !important;
        box-shadow: var(--sh-card-shadow) !important;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .ow-card-header {
        background: color-mix(in srgb, var(--sh-card-bg) 96%, var(--sh-accent) 4%) !important;
        border-bottom: 1px solid var(--sh-card-border) !important;
    }

    .ow-step-badge {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--sh-accent);
        color: #fff !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 8px 16px rgba(124, 58, 237, 0.2);
    }

    .ow-shell input:not([type="color"]),
    .ow-shell select,
    .ow-shell textarea {
        background: var(--sh-input-bg, #f8fafc) !important;
        border: 1px solid var(--sh-card-border) !important;
        color: var(--sh-text-primary) !important;
        border-radius: 12px !important;
    }

    .dark.avento-theme .ow-shell input:not([type="color"]),
    .dark.avento-theme .ow-shell select,
    .dark.avento-theme .ow-shell textarea {
        background-color: var(--sh-input-bg) !important;
        background: var(--sh-input-bg) !important;
    }

    .ow-shell .sh-title {
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--sh-text-primary);
    }

    .ow-shell .sh-subtitle {
        margin-top: 3px;
        font-size: 13px;
        font-weight: 600;
        color: var(--sh-text-secondary);
    }

    .ow-progress-fill {
        background: linear-gradient(90deg, var(--sh-accent), #a78bfa);
        box-shadow: 0 0 12px rgba(124, 58, 237, 0.3);
    }

    .ow-actions {
        border-top: 1px solid var(--sh-card-border) !important;
    }

    .ow-btn-ghost {
        background: var(--sh-input-bg, #f8fafc) !important;
        border: 1px solid var(--sh-card-border) !important;
        color: var(--sh-text-secondary) !important;
    }

    .dark.avento-theme .ow-btn-ghost,
    .dark.avento-theme .ow-search-toggle,
    .dark.avento-theme .ow-search-panel div[class*="dark:bg-slate-800"] {
        background-color: var(--sh-input-bg) !important;
        background: var(--sh-input-bg) !important;
    }

    .ow-btn-primary {
        background: linear-gradient(135deg, var(--sh-accent-strong), var(--sh-accent)) !important;
        color: #ffffff !important;
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25);
        transition: transform .18s ease, box-shadow .2s ease, filter .2s ease;
    }

    .ow-search-result {
        background: var(--sh-card-bg);
        border: 1px solid var(--sh-card-border);
        transition: transform .18s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .ow-search-result:hover {
        border-color: color-mix(in srgb, var(--sh-accent) 38%, var(--sh-card-border));
        transform: translateY(-1px);
    }

    .ow-search-result-icon {
        background: linear-gradient(135deg, var(--sh-accent-strong), var(--sh-accent));
    }

    @media (max-width: 760px) {
        .ow-shell { padding: 16px; border-radius: 16px; }
    }

    /* Absolute Zero Shadow Kill - FINAL OVERRIDE */
    html.dark.avento-theme .ow-shell,
    html.dark.avento-theme .ow-shell *,
    html.dark.avento-theme .ow-shell *::before,
    html.dark.avento-theme .ow-shell *::after {
        box-shadow: none !important;
        text-shadow: none !important;
        filter: none !important;
        -webkit-filter: none !important;
        transition: none !important;
    }
</style>
<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="ow-shell">
        <!-- Top Bar (Estilo Sales Hub) -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <span class="ow-step-badge">1</span>
                <div>
                    <h1 class="sh-title">Dados do Cliente</h1>
                    <p class="sh-subtitle">Etapa 1 de 5 • Informe os dados para continuar</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Passo Atual</div>
                <div class="text-2xl font-black text-[#7c3aed]">20%</div>
            </div>
        </div>

        <!-- Progress Widget -->
        <div class="ow-progress p-4 mb-8">
            <div class="w-full bg-gray-100 dark:bg-slate-800/50 rounded-full h-2">
                <div class="ow-progress-fill h-2 rounded-full transition-all duration-700" style="width: 20%"></div>
            </div>
        </div>

        <!-- Main Workspace Card -->
        <div class="ow-card overflow-hidden">
            <!-- Section Header -->
            <div class="px-6 py-5 ow-card-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#7c3aed] rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ isset($editData) ? 'Edição de Pedido' : 'Registro de Pedido' }}
                    </h2>
                </div>
            </div>

            <div class="p-6">

            <!-- Buscar Cliente Existente -->
            <div class="mb-6" x-data="{ showSearch: false, clientSelected: false }" @client-selected.window="clientSelected = true; showSearch = false" @click.away="">
                <div class="rounded-xl border-2 border-purple-200 dark:border-purple-800/30 p-5 transition-all ow-search-panel">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-500/10 rounded-lg flex items-center justify-center border border-purple-100 dark:border-purple-900/30">
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
                                class="px-4 py-2 text-sm font-semibold text-[#7c3aed] dark:text-purple-300 bg-white rounded-lg transition-all border border-purple-200 dark:border-purple-700 ow-search-toggle">
                            <span x-show="!showSearch">Mostrar</span>
                            <span x-show="showSearch">Ocultar</span>
                        </button>
                    </div>
                    
                    <div x-show="showSearch" x-transition class="space-y-3 mt-4">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" id="search-client" placeholder="Digite nome, telefone ou CPF..." 
                                       class="w-full pl-4 pr-10 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-[#7c3aed] dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-100 dark:focus:ring-purple-900/20 transition-all text-sm font-medium">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" onclick="window.runClientSearch()" style="color: white !important;" 
                                    class="px-6 py-3 bg-[#7c3aed] text-white stay-white rounded-lg transition-colors text-sm font-semibold ow-btn-primary">
                                Buscar
                            </button>
                        </div>
                        <div id="search-results" class="space-y-2"></div>
                    </div>

                    <!-- Success Template (Alpine-controlled) - Outside the search container -->
                    <div x-show="clientSelected" x-transition class="mt-4 p-4 bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-900/10 border-2 border-emerald-200 dark:border-emerald-800/30 rounded-xl shadow-sm">
                        <div class="flex items-start gap-3 sm:items-center">
                            <div class="inline-flex w-10 h-10 shrink-0 items-center justify-center rounded-full bg-emerald-600 shadow-lg shadow-emerald-600/20 dark:bg-emerald-500">
                                <svg class="w-4 h-4 text-white stay-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.414 0l-3.5-3.5a1 1 0 011.414-1.42l2.793 2.794 6.793-6.794a1 1 0 011.414 0Z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Cliente selecionado com sucesso!</p>
                                <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">Você pode editar os dados se necessário antes de continuar.</p>
                            </div>
                            <button type="button" @click="clientSelected = false" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
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
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#7c3aed] dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Informações Básicas</h2>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-5 ow-field-panel">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Nome Completo *</label>
                            <input id="name" name="name" type="text"
                                   value="{{ isset($editData) ? ($editData['client']['name'] ?? '') : '' }}"
                                   placeholder="Digite o nome completo do cliente"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-[#7c3aed] dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-100 dark:focus:ring-purple-900/20 transition-all text-sm font-medium">
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
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Informações de Contato</h2>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-5 space-y-4 ow-field-panel">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone_primary" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Telefone Principal *</label>
                                <input id="phone_primary" name="phone_primary" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['phone_primary'] ?? '') : '' }}"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="phone_secondary" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Telefone Secundário</label>
                                <input id="phone_secondary" name="phone_secondary" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['phone_secondary'] ?? '') : '' }}"
                                       placeholder="(00) 00000-0000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Email</label>
                                <input id="email" name="email" type="email"
                                       value="{{ isset($editData) ? ($editData['client']['email'] ?? '') : '' }}"
                                       placeholder="cliente@email.com"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="cpf_cnpj" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">CPF/CNPJ</label>
                                <input id="cpf_cnpj" name="cpf_cnpj" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['cpf_cnpj'] ?? '') : '' }}"
                                       placeholder="000.000.000-00"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Endereço -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Endereço</h2>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-5 space-y-4 ow-field-panel">
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Endereço Completo</label>
                            <input id="address" name="address" type="text"
                                   value="{{ isset($editData) ? ($editData['client']['address'] ?? '') : '' }}"
                                   placeholder="Rua, número, bairro"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Cidade</label>
                                <input id="city" name="city" type="text"
                                       value="{{ isset($editData) ? ($editData['client']['city'] ?? '') : '' }}"
                                       placeholder="Nome da cidade"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Estado</label>
                                <select id="state" name="state"
                                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
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
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção: Categoria -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Categoria do Cliente</h2>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-5 ow-field-panel">
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Tipo de Cliente</label>
                            <select id="category" name="category"
                                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white text-gray-900 dark:text-white focus:border-amber-500 dark:focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:focus:ring-amber-900/20 transition-all text-sm font-medium">
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
                <div class="flex justify-between items-center pt-6 border-t border-gray-100 dark:border-slate-800 ow-actions">
                    <a href="{{ isset($editData) ? route('orders.show', $order->id) : '/' }}" 
                       class="inline-flex items-center px-5 py-2.5 text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-white hover:bg-gray-50 dark:hover:bg-slate-700 border border-gray-200 dark:border-slate-700 rounded-lg transition-all text-sm font-medium ow-btn-ghost">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                    <button type="submit" 
                            style="color: white !important;"
                            class="inline-flex items-center px-8 py-3 bg-[#7c3aed] text-white stay-white font-semibold rounded-xl transition-colors text-sm ow-btn-primary"
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
    </section>
</div>

@push('page-scripts')
<script>

(function() {
    console.log('Client Wizard Script Loaded');
    const clientSearchUrl = "{{ url('/api/clients/search') }}";

    function resetSubmitState(form) {
        if (!form) return;

        form.removeAttribute('data-submitting');

        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        if (submitBtn.dataset.originalHtml) {
            submitBtn.innerHTML = submitBtn.dataset.originalHtml;
        }

        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }

    function initClientPage() {
        console.log('Initializing Client Page...');
        const form = document.getElementById('client-form');
        if (form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.dataset.originalHtml) {
                submitBtn.dataset.originalHtml = submitBtn.innerHTML.trim();
            }
            resetSubmitState(form);
        }

        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', function(e) {
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }
                
                const name = document.getElementById('name').value;
                const phone = document.getElementById('phone_primary').value;
                
                if (!name || !phone) {
                    console.error('Campos obrigatórios não preenchidos');
                    e.preventDefault();
                    return false;
                }
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    if (!submitBtn.dataset.originalHtml) {
                        submitBtn.dataset.originalHtml = submitBtn.innerHTML.trim();
                    }
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

        // Attach masks and validation
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
                    <div class="p-4 rounded-lg cursor-pointer group ow-search-result"
                         onclick='window.fillClientData(${clientJson})'>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform ow-search-result-icon">
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
                            <div class="group-hover:translate-x-1 transition-transform ow-search-result-arrow" style="color: var(--sh-accent) !important;">
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
            resultsDiv.innerHTML = '';
            window.dispatchEvent(new CustomEvent('client-selected'));
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

    // Expose initialization for AJAX loading
    window._clientInitSetup = function() {
        document.removeEventListener('ajax-content-loaded', initClientPage);
        document.addEventListener('ajax-content-loaded', initClientPage);
    };
    window._clientInitSetup();

    if (!window._ordersClientWizardPageShowBound) {
        window.addEventListener('pageshow', function() {
            resetSubmitState(document.getElementById('client-form'));
        });
        window._ordersClientWizardPageShowBound = true;
    }

    // Also run on DOMContentLoaded for initial load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClientPage);
    } else {
        initClientPage();
    }
})();
</script>
@endpush
@endsection
