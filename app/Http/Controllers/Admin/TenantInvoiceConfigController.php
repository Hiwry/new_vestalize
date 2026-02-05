<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantInvoiceConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantInvoiceConfigController extends Controller
{
    /**
     * Exibir formulário de configuração
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) nao deve ver dados de outros tenants
        if ($user->tenant_id === null) {
            $tenants = \App\Models\Tenant::orderBy('name')->get();
            return view('admin.invoice-config.select-tenant', compact('tenants'));
        }

        $tenant = $user->tenant;
        
        $config = TenantInvoiceConfig::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'provider' => 'focusnfe',
                'environment' => 'homologacao',
            ]
        );

        return view('admin.invoice-config.edit', compact('config', 'tenant'));
    }

    /**
     * Super Admin: Editar configuração de um tenant específico
     */
    public function editTenant($tenantId)
    {
        if (Auth::user()->tenant_id !== null) {
            abort(403);
        }

        $tenant = \App\Models\Tenant::findOrFail($tenantId);
        $config = TenantInvoiceConfig::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'provider' => 'focusnfe',
                'environment' => 'homologacao',
            ]
        );

        return view('admin.invoice-config.edit', compact('config', 'tenant'));
    }

    /**
     * Salvar configurações
     */
    public function update(Request $request)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return redirect()->back()->with('error', 'Tenant não encontrado.');
        }

        $validated = $request->validate([
            'api_token' => 'nullable|string|max:255',
            'environment' => 'required|in:homologacao,producao',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|size:14',
            'inscricao_estadual' => 'required|string|max:20',
            'inscricao_municipal' => 'nullable|string|max:20',
            'regime_tributario' => 'required|integer|in:1,2,3',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'uf' => 'required|string|size:2',
            'cep' => 'required|string|size:8',
            'codigo_municipio' => 'required|string|max:7',
            'default_cfop' => 'required|string|size:4',
            'default_ncm' => 'required|string|size:8',
            'natureza_operacao' => 'required|string|max:100',
            'serie_nfe' => 'required|integer|min:1',
            'certificate' => 'nullable|file|mimes:pfx,p12|max:5120',
            'certificate_password' => 'nullable|string|max:100',
        ]);

        $config = TenantInvoiceConfig::firstOrCreate(['tenant_id' => $tenant->id]);

        // Processar upload do certificado
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $filename = 'cert_' . $tenant->id . '_' . time() . '.pfx';
            $path = $file->storeAs("certificates/{$tenant->id}", $filename, 'local');
            
            // Remover certificado antigo
            if ($config->certificate_path) {
                Storage::disk('local')->delete($config->certificate_path);
            }
            
            $config->certificate_path = $path;
            
            // Tentar ler data de expiração do certificado
            if ($request->filled('certificate_password')) {
                $expiresAt = $this->getCertificateExpiration(
                    Storage::disk('local')->path($path),
                    $request->input('certificate_password')
                );
                if ($expiresAt) {
                    $config->certificate_expires_at = $expiresAt;
                }
            }
        }

        // Atualizar senha do certificado se fornecida
        if ($request->filled('certificate_password')) {
            $config->certificate_password = $request->input('certificate_password');
        }

        // Atualizar demais campos
        $config->fill([
            'api_token' => $validated['api_token'] ?? $config->api_token,
            'environment' => $validated['environment'],
            'razao_social' => $validated['razao_social'],
            'nome_fantasia' => $validated['nome_fantasia'],
            'cnpj' => preg_replace('/\D/', '', $validated['cnpj']),
            'inscricao_estadual' => $validated['inscricao_estadual'],
            'inscricao_municipal' => $validated['inscricao_municipal'],
            'regime_tributario' => $validated['regime_tributario'],
            'logradouro' => $validated['logradouro'],
            'numero' => $validated['numero'],
            'complemento' => $validated['complemento'],
            'bairro' => $validated['bairro'],
            'cidade' => $validated['cidade'],
            'uf' => strtoupper($validated['uf']),
            'cep' => preg_replace('/\D/', '', $validated['cep']),
            'codigo_municipio' => $validated['codigo_municipio'],
            'default_cfop' => $validated['default_cfop'],
            'default_ncm' => $validated['default_ncm'],
            'natureza_operacao' => $validated['natureza_operacao'],
            'serie_nfe' => $validated['serie_nfe'],
        ]);

        // Verificar se configuração está completa para ativar
        $config->is_active = $config->isComplete();
        $config->save();

        return redirect()->back()->with('success', 'Configurações de NF-e salvas com sucesso!');
    }

    /**
     * Testar conexão com a API
     */
    public function testConnection()
    {
        $tenant = Auth::user()->tenant;
        $config = TenantInvoiceConfig::where('tenant_id', $tenant->id)->first();

        if (!$config || empty($config->api_token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token da API não configurado.',
            ]);
        }

        // Se for modo DEMO, retornar sucesso sem testar API real
        if ($config->api_token === 'DEMO') {
            return response()->json([
                'success' => true,
                'message' => 'Modo DEMO detectado. O sistema simulará a emissão localmente.',
            ]);
        }

        try {
            $baseUrl = $config->environment === 'producao'
                ? 'https://api.focusnfe.com.br'
                : 'https://homologacao.focusnfe.com.br';

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($config->api_token . ':'),
            ])->get("{$baseUrl}/v2/nfe");

            if ($response->successful() || $response->status() === 401) {
                // 401 significa token inválido, qualquer outro sucesso significa conexão OK
                if ($response->status() === 401) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token inválido. Verifique suas credenciais na Focus NFe.',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Conexão com Focus NFe estabelecida com sucesso!',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar: ' . $response->status(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de conexão: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Ler data de expiração do certificado
     */
    private function getCertificateExpiration(string $path, string $password): ?\DateTime
    {
        try {
            $certContent = file_get_contents($path);
            $certs = [];
            
            if (openssl_pkcs12_read($certContent, $certs, $password)) {
                $certInfo = openssl_x509_parse($certs['cert']);
                if ($certInfo && isset($certInfo['validTo_time_t'])) {
                    return (new \DateTime())->setTimestamp($certInfo['validTo_time_t']);
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return null;
    }
}
