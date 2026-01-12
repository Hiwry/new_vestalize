<?php

$tenant = \App\Models\Tenant::where('store_code', 'TNFE01')->first();
if (!$tenant) { 
    echo "Tenant não encontrado"; 
    exit; 
}

$config = \App\Models\TenantInvoiceConfig::updateOrCreate(
    ['tenant_id' => $tenant->id],
    [
        'provider' => 'focusnfe',
        'environment' => 'homologacao',
        'api_token' => 'DEMO',
        'razao_social' => 'EMPRESA TESTE LTDA',
        'nome_fantasia' => 'Loja Teste NF-e',
        'cnpj' => '00000000000191',
        'inscricao_estadual' => 'ISENTO',
        'inscricao_municipal' => '12345',
        'regime_tributario' => 1,
        'logradouro' => 'Rua Teste',
        'numero' => '123',
        'complemento' => 'Sala 1',
        'bairro' => 'Centro',
        'cidade' => 'Sao Paulo',
        'uf' => 'SP',
        'cep' => '01310100',
        'codigo_municipio' => '3550308',
        'default_cfop' => '5102',
        'default_ncm' => '61091000',
        'natureza_operacao' => 'VENDA DE MERCADORIA',
        'serie_nfe' => 1,
        'numero_nfe_atual' => 0,
        'is_active' => true,
    ]
);

echo "Configuracao criada com sucesso!\n";
echo "Token: DEMO\n";
echo "Razao Social: " . $config->razao_social . "\n";
echo "CNPJ: " . $config->cnpj . "\n";
