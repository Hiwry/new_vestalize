@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <a href="{{ route('settings.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-2 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Voltar para Configurações
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Dados da Empresa e Personalização</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie as informações da sua empresa, marca e termos de uso.</p>
    </div>
    
    <button type="submit" form="company-settings-form" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors flex items-center justify-center" style="color: white !important;">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        Salvar Alterações
    </button>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg flex items-center">
    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form id="company-settings-form" action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Coluna Esquerda: Dados Principais e Marca -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Marca e Cores -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                    Identidade Visual
                </h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Logo Upload -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-100 dark:border-gray-600">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Logo da Empresa</label>
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="relative w-32 h-32 bg-white dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600 flex items-center justify-center p-4">
                                @if($tenant)
                                    <img src="{{ asset('vestalize.svg') }}" alt="Vestalize" class="w-full h-full object-contain">
                                @else
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                @endif
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Essa logo será utilizada em documentos importantes como <strong>Orçamentos, Pedidos, Notas e Assinaturas</strong> do cliente. Certifique-se de usar uma imagem de alta qualidade com fundo transparente (PNG).
                                </p>
                                <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition cursor-pointer">
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">Formatos aceitos: PNG, JPG ou SVG. Tamanho máximo: 2MB.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados da Empresa -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Informações da Empresa
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2 md:col-span-1">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Fantasia / Razão Social</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings->company_name) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm" placeholder="Nome da Loja">
                    </div>
                    
                    <div class="col-span-2 md:col-span-1">
                        <label for="company_cnpj" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CNPJ</label>
                        <input type="text" name="company_cnpj" id="company_cnpj" value="{{ old('company_cnpj', $settings->company_cnpj) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm" placeholder="00.000.000/0000-00">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label for="company_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail para Contato (Público)</label>
                        <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $settings->company_email) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm" placeholder="contato@suaempresa.com">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label for="company_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone / WhatsApp</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings->company_phone) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm" placeholder="(00) 00000-0000">
                    </div>

                    <div class="col-span-2">
                        <label for="company_website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website / Instagram</label>
                        <input type="url" name="company_website" id="company_website" value="{{ old('company_website', $settings->company_website) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm" placeholder="https://www.instagram.com/sua_loja">
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-200 mb-3">Endereço</h3>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="md:col-span-2">
                            <label for="company_zip" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">CEP</label>
                            <input type="text" name="company_zip" id="company_zip" value="{{ old('company_zip', $settings->company_zip) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm">
                        </div>
                        <div class="md:col-span-4">
                            <label for="company_address" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Endereço Completo</label>
                            <input type="text" name="company_address" id="company_address" value="{{ old('company_address', $settings->company_address) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label for="company_city" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cidade</label>
                            <input type="text" name="company_city" id="company_city" value="{{ old('company_city', $settings->company_city) }}" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm">
                        </div>
                        <div class="md:col-span-1">
                            <label for="company_state" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">UF</label>
                            <input type="text" name="company_state" id="company_state" value="{{ old('company_state', $settings->company_state) }}" maxlength="2" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm uppercase">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Coluna Direita: Termos e Pré-visualização -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Termos e Condições -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Termos e Condições
                </h2>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        O texto abaixo será exibido para o cliente ao acessar o pedido e gerar orçamentos.
                    </p>
                    <textarea name="terms_conditions" id="terms_conditions" rows="15" class="block w-full px-4 py-2.5 border-0 ring-1 ring-gray-300 dark:ring-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm transition-all sm:text-sm p-3 font-mono text-xs" placeholder="Insira aqui os termos de serviço, políticas de troca, prazos de entrega, etc...">{{ old('terms_conditions', $settings->terms_conditions) }}</textarea>
                </div>
            </div>

            <!-- Informação -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400 dark:text-blue-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Onde isso aparece?</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Cabeçalho e rodapé de PDFs (Orçamentos e Comprovantes)</li>
                                <li>Tela de Confirmação de Pedido do Cliente</li>
                                <li>Cabeçalho do Orçamento Online</li>
                                <li>Emails enviados pelo sistema</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
    // Preview de cor em tempo real
    const primaryInput = document.getElementById('primary_color');
    const primaryText = primaryInput.nextElementSibling;
    
    primaryText.addEventListener('input', function() {
        if(/^#[0-9A-F]{6}$/i.test(this.value)) {
            primaryInput.value = this.value;
        }
    });

    const secondaryInput = document.getElementById('secondary_color');
    const secondaryText = secondaryInput.nextElementSibling;

    secondaryText.addEventListener('input', function() {
        if(/^#[0-9A-F]{6}$/i.test(this.value)) {
            secondaryInput.value = this.value;
        }
    });
</script>
@endsection
