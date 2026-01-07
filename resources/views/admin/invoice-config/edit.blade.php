@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Configuração de Nota Fiscal</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure sua integração com Focus NFe para emitir NF-e</p>
        </div>
        <div>
            @if($config->is_active)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Ativo
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Pendente
                </span>
            @endif
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.invoice-config.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Credenciais Focus NFe -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Credenciais Focus NFe</h2>
            </header>
            <div class="p-5 space-y-4">
                <!-- Modo Demo -->
                <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Modo Demonstração</p>
                            <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                Para testar sem API real, digite <code class="px-1 py-0.5 bg-amber-100 dark:bg-amber-800 rounded">DEMO</code> no campo Token ou deixe em branco. 
                                As notas serão simuladas localmente e <strong>não terão validade fiscal</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-sm">
                    Para usar em produção: crie sua conta em <a href="https://focusnfe.com.br" target="_blank" class="font-semibold underline">focusnfe.com.br</a> e obtenha seu token de API.
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Token da API</label>
                        <input type="text" name="api_token" value="{{ old('api_token', $config->api_token) }}"
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Cole o token ou digite DEMO para testar">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Ambiente</label>
                        <select name="environment" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="homologacao" {{ $config->environment === 'homologacao' ? 'selected' : '' }}>Homologação (Testes)</option>
                            <option value="producao" {{ $config->environment === 'producao' ? 'selected' : '' }}>Produção</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <button type="button" onclick="testConnection()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        Testar Conexão
                    </button>
                    <span id="connection-status" class="text-sm"></span>
                </div>
            </div>
        </div>

        <!-- Certificado Digital -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Certificado Digital A1</h2>
            </header>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Arquivo (.pfx)</label>
                        <input type="file" name="certificate" accept=".pfx,.p12"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                        @if($config->certificate_path)
                            <p class="mt-2 text-xs text-green-600 dark:text-green-400">✓ Certificado carregado @if($config->certificate_expires_at)- Expira {{ $config->certificate_expires_at->format('d/m/Y') }}@endif</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Senha</label>
                        <input type="password" name="certificate_password" 
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Senha do arquivo">
                    </div>
                </div>
            </div>
        </div>

        <!-- Dados do Emitente -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Dados do Emitente</h2>
            </header>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Razão Social *</label>
                        <input type="text" name="razao_social" value="{{ old('razao_social', $config->razao_social) }}" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Nome Fantasia</label>
                        <input type="text" name="nome_fantasia" value="{{ old('nome_fantasia', $config->nome_fantasia) }}"
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">CNPJ *</label>
                        <input type="text" name="cnpj" value="{{ old('cnpj', $config->cnpj) }}" maxlength="14" required placeholder="Apenas números"
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Inscrição Estadual *</label>
                        <input type="text" name="inscricao_estadual" value="{{ old('inscricao_estadual', $config->inscricao_estadual) }}" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Inscrição Municipal</label>
                        <input type="text" name="inscricao_municipal" value="{{ old('inscricao_municipal', $config->inscricao_municipal) }}"
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Regime Tributário *</label>
                    <select name="regime_tributario" required class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="1" {{ $config->regime_tributario == 1 ? 'selected' : '' }}>Simples Nacional</option>
                        <option value="2" {{ $config->regime_tributario == 2 ? 'selected' : '' }}>Simples Nacional - Excesso de sublimite</option>
                        <option value="3" {{ $config->regime_tributario == 3 ? 'selected' : '' }}>Regime Normal</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Endereço do Emitente</h2>
            </header>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Logradouro *</label>
                        <input type="text" name="logradouro" value="{{ old('logradouro', $config->logradouro) }}" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Número *</label>
                        <input type="text" name="numero" value="{{ old('numero', $config->numero) }}" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Complemento</label>
                        <input type="text" name="complemento" value="{{ old('complemento', $config->complemento) }}"
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Bairro *</label>
                        <input type="text" name="bairro" value="{{ old('bairro', $config->bairro) }}" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">CEP *</label>
                        <input type="text" name="cep" value="{{ old('cep', $config->cep) }}" maxlength="8" required placeholder="Apenas números"
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Cidade *</label>
                        <input type="text" name="cidade" value="{{ old('cidade', $config->cidade) }}" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">UF *</label>
                        <input type="text" name="uf" value="{{ old('uf', $config->uf) }}" maxlength="2" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Cód. IBGE *</label>
                        <input type="text" name="codigo_municipio" value="{{ old('codigo_municipio', $config->codigo_municipio) }}" maxlength="7" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <a href="https://www.ibge.gov.br/explica/codigos-dos-municipios.php" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">Consultar código IBGE</a>
                </p>
            </div>
        </div>

        <!-- Configurações Fiscais -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Configurações Fiscais</h2>
            </header>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">CFOP *</label>
                        <input type="text" name="default_cfop" value="{{ old('default_cfop', $config->default_cfop) }}" maxlength="4" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <p class="text-xs text-gray-400 mt-1">Ex: 5102</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">NCM *</label>
                        <input type="text" name="default_ncm" value="{{ old('default_ncm', $config->default_ncm) }}" maxlength="8" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <p class="text-xs text-gray-400 mt-1">Ex: 61091000</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Série *</label>
                        <input type="number" name="serie_nfe" value="{{ old('serie_nfe', $config->serie_nfe) }}" min="1" required
                               class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Última NF-e</label>
                        <input type="number" value="{{ $config->numero_nfe_atual }}" disabled
                               class="w-full px-3 py-2 rounded border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Natureza da Operação *</label>
                    <input type="text" name="natureza_operacao" value="{{ old('natureza_operacao', $config->natureza_operacao) }}" required
                           class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 rounded shadow">
                Salvar Configurações
            </button>
        </div>
    </form>
</div>

<script>
function testConnection() {
    const s = document.getElementById('connection-status');
    s.innerHTML = '<span class="text-gray-500">Testando...</span>';
    fetch('{{ route("admin.invoice-config.test") }}', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}})
    .then(r=>r.json()).then(d=>{s.innerHTML=d.success?'<span class="text-green-500">'+d.message+'</span>':'<span class="text-red-500">'+d.message+'</span>';})
    .catch(()=>{s.innerHTML='<span class="text-red-500">Erro</span>';});
}
</script>
@endsection
