@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Editar Loja</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Atualize as informações da loja e configurações da empresa</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ session('success') }}
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.stores.update', $store->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')
    
    <!-- Informações Básicas da Loja -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Informações Básicas
            </h2>
        </div>
        
        <div class="p-6 space-y-6">
            <div>
                <label for="name" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">
                    Nome da Loja <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $store->name) }}"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
            </div>

            @if(!$store->isMain())
            <div>
                <label for="parent_id" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">
                    Loja Principal
                </label>
                <select name="parent_id" 
                        id="parent_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    @if($mainStore)
                        <option value="{{ $mainStore->id }}" {{ old('parent_id', $store->parent_id) == $mainStore->id ? 'selected' : '' }}>
                            {{ $mainStore->name }}
                        </option>
                    @endif
                </select>
            </div>
            @endif

            <div>
                <label class="flex items-center">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" 
                           name="active" 
                           value="1"
                           {{ old('active', $store->active) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Loja ativa</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Logo da Empresa -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Logo da Empresa
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Esta logo será exibida nos orçamentos, pedidos e links de assinatura desta loja</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Preview do Logo Atual -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-slate-400 mb-2 font-medium">Logo Atual</label>
                    <div id="current-logo" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700/30 min-h-[160px]">
                        @if($settings->logo_path && file_exists(public_path($settings->logo_path)))
                            <div class="text-center">
                                <img src="{{ asset($settings->logo_path) }}" alt="Logo da Empresa" class="max-h-32 w-auto mx-auto rounded-md shadow-sm">
                                <button type="button" onclick="deleteLogo()" 
                                        class="mt-3 inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remover Logo
                                </button>
                            </div>
                        @else
                            <div class="text-center text-gray-400 dark:text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Nenhuma logo cadastrada</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upload de Novo Logo -->
                <div>
                    <label class="block text-xs text-gray-600 dark:text-slate-400 mb-2 font-medium">Enviar Nova Logo</label>
                    <div class="relative">
                        <input type="file" 
                               name="logo" 
                               id="logo" 
                               accept="image/*"
                               onchange="previewImage(event)"
                               class="hidden">
                        
                        <label for="logo" 
                               id="drop-zone"
                               class="border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-400 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer bg-white dark:bg-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all min-h-[160px]">
                            <div id="upload-content" class="text-center">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Clique para escolher ou arraste aqui
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    PNG, JPG ou GIF (máx. 2MB)
                                </p>
                            </div>
                            <div id="preview-content" class="hidden text-center">
                                <img id="preview-image" src="" alt="Preview" class="max-h-28 w-auto mx-auto rounded-md shadow-sm mb-2">
                                <p class="text-xs text-gray-600 dark:text-gray-400" id="file-name"></p>
                                <button type="button" 
                                        onclick="clearPreview(event)"
                                        class="mt-2 text-xs text-red-600 dark:text-red-400 hover:underline">
                                    Remover
                                </button>
                            </div>
                        </label>
                    </div>
                    @error('logo')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Informações da Empresa -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Informações da Empresa
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nome da Empresa -->
                <div class="md:col-span-2">
                    <label for="company_name" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Nome da Empresa</label>
                    <input type="text" 
                           name="company_name" 
                           id="company_name" 
                           value="{{ old('company_name', $settings->company_name) }}"
                           placeholder="Nome da empresa"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- CNPJ -->
                <div>
                    <label for="company_cnpj" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">CNPJ</label>
                    <input type="text" 
                           name="company_cnpj" 
                           id="company_cnpj" 
                           value="{{ old('company_cnpj', $settings->company_cnpj) }}"
                           placeholder="00.000.000/0000-00"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- CEP -->
                <div>
                    <label for="company_zip" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">CEP</label>
                    <input type="text" 
                           name="company_zip" 
                           id="company_zip" 
                           value="{{ old('company_zip', $settings->company_zip) }}"
                           placeholder="00000-000"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- Endereço -->
                <div class="md:col-span-2">
                    <label for="company_address" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Endereço</label>
                    <input type="text" 
                           name="company_address" 
                           id="company_address" 
                           value="{{ old('company_address', $settings->company_address) }}"
                           placeholder="Rua, número, complemento"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- Cidade -->
                <div>
                    <label for="company_city" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Cidade</label>
                    <input type="text" 
                           name="company_city" 
                           id="company_city" 
                           value="{{ old('company_city', $settings->company_city) }}"
                           placeholder="Cidade"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- Estado -->
                <div>
                    <label for="company_state" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Estado (UF)</label>
                    <input type="text" 
                           name="company_state" 
                           id="company_state" 
                           value="{{ old('company_state', $settings->company_state) }}"
                           maxlength="2"
                           placeholder="SP"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all uppercase">
                </div>
            </div>
        </div>
    </div>

    <!-- Informações de Contato -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                Informações de Contato
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Telefone -->
                <div>
                    <label for="company_phone" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Telefone</label>
                    <input type="text" 
                           name="company_phone" 
                           id="company_phone" 
                           value="{{ old('company_phone', $settings->company_phone) }}"
                           placeholder="(00) 00000-0000"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- E-mail -->
                <div>
                    <label for="company_email" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">E-mail</label>
                    <input type="email" 
                           name="company_email" 
                           id="company_email" 
                           value="{{ old('company_email', $settings->company_email) }}"
                           placeholder="contato@empresa.com"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- Website -->
                <div class="md:col-span-2">
                    <label for="company_website" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Website</label>
                    <input type="url" 
                           name="company_website" 
                           id="company_website" 
                           value="{{ old('company_website', $settings->company_website) }}"
                           placeholder="https://www.empresa.com"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>
            </div>
        </div>
    </div>

    <!-- Informações Bancárias -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Informações Bancárias
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Dados que podem aparecer nos documentos</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Banco -->
                <div class="md:col-span-2">
                    <label for="bank_name" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Banco</label>
                    <input type="text" 
                           name="bank_name" 
                           id="bank_name" 
                           value="{{ old('bank_name', $settings->bank_name) }}"
                           placeholder="Nome do banco"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- Agência -->
                <div>
                    <label for="bank_agency" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Agência</label>
                    <input type="text" 
                           name="bank_agency" 
                           id="bank_agency" 
                           value="{{ old('bank_agency', $settings->bank_agency) }}"
                           placeholder="0000"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- Conta -->
                <div>
                    <label for="bank_account" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Conta</label>
                    <input type="text" 
                           name="bank_account" 
                           id="bank_account" 
                           value="{{ old('bank_account', $settings->bank_account) }}"
                           placeholder="00000-0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>

                <!-- Chave PIX -->
                <div class="md:col-span-2">
                    <label for="pix_key" class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium">Chave PIX</label>
                    <input type="text" 
                           name="pix_key" 
                           id="pix_key" 
                           value="{{ old('pix_key', $settings->pix_key) }}"
                           placeholder="CPF, e-mail, telefone ou chave aleatória"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                </div>
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="flex flex-col sm:flex-row justify-between gap-3">
        <a href="{{ route('admin.stores.index') }}" 
           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Cancelar
        </a>
        <button type="submit" 
                class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium bg-indigo-600 dark:bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Atualizar Loja e Configurações
        </button>
    </div>
</form>

@push('scripts')
<script>
// Drag & Drop
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('logo');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
        dropZone.classList.add('border-indigo-500', 'dark:border-indigo-400', 'bg-indigo-50', 'dark:bg-indigo-900/20');
    }, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
        dropZone.classList.remove('border-indigo-500', 'dark:border-indigo-400', 'bg-indigo-50', 'dark:bg-indigo-900/20');
    }, false);
});

dropZone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
    previewImage({ target: { files: files } });
}, false);

// Preview da imagem
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('upload-content').classList.add('hidden');
            document.getElementById('preview-content').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

// Limpar preview
function clearPreview(event) {
    event.preventDefault();
    event.stopPropagation();
    document.getElementById('logo').value = '';
    document.getElementById('upload-content').classList.remove('hidden');
    document.getElementById('preview-content').classList.add('hidden');
}

// Deletar logo - Expor globalmente
window.deleteLogo = function() {
    if (!confirm('Tem certeza que deseja remover o logo?')) {
        return;
    }

    fetch('{{ route("admin.stores.deleteLogo", $store->id) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Erro ao remover o logo.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Erro ao remover o logo.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Erro ao remover o logo.');
    });
};

// Manter compatibilidade
function deleteLogo() {
    window.deleteLogo();
}
</script>
@endpush
@endsection
