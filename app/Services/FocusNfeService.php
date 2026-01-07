<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\TenantInvoiceConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FocusNfeService
{
    private TenantInvoiceConfig $config;
    private string $baseUrl;
    private bool $demoMode = false;

    public function __construct(TenantInvoiceConfig $config)
    {
        $this->config = $config;
        $this->baseUrl = $config->environment === 'producao'
            ? 'https://api.focusnfe.com.br'
            : 'https://homologacao.focusnfe.com.br';
        
        // Ativar modo demo se não houver token configurado
        $this->demoMode = empty($config->api_token) || $config->api_token === 'DEMO';
    }

    /**
     * Verificar se está em modo demo
     */
    public function isDemoMode(): bool
    {
        return $this->demoMode;
    }

    /**
     * Emitir NF-e para um pedido
     */
    public function emitirNfe(Order $order): array
    {
        try {
            // Criar registro da invoice
            $invoice = $this->createInvoiceRecord($order);
            
            // Se modo demo, simular resposta
            if ($this->demoMode) {
                return $this->simulateEmission($invoice);
            }
            
            // Montar payload da NF-e
            $payload = $this->buildNfePayload($order, $invoice);
            
            // Enviar para Focus NFe
            $response = $this->sendToFocusNfe($invoice->ref, $payload);
            
            // Processar resposta
            return $this->processResponse($invoice, $response);
            
        } catch (\Exception $e) {
            Log::error('Erro ao emitir NF-e', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao emitir NF-e: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Simular emissão de NF-e (modo demo)
     */
    private function simulateEmission(Invoice $invoice): array
    {
        // Gerar dados simulados
        $chaveNfe = $this->generateFakeChaveNfe();
        $protocolo = 'DEMO' . str_pad(rand(1, 999999999), 15, '0', STR_PAD_LEFT);
        
        // Atualizar invoice com dados simulados
        $invoice->update([
            'status' => Invoice::STATUS_AUTHORIZED,
            'chave_nfe' => $chaveNfe,
            'protocolo' => $protocolo,
            'data_emissao' => now(),
            'status_sefaz' => '100',
            'motivo_sefaz' => 'Autorizado o uso da NF-e (SIMULAÇÃO)',
            'attempts' => 1,
            'last_attempt_at' => now(),
        ]);

        Log::info('NF-e emitida em modo DEMO', [
            'invoice_id' => $invoice->id,
            'chave_nfe' => $chaveNfe,
        ]);

        return [
            'success' => true,
            'message' => '✅ NF-e emitida com sucesso! (MODO DEMONSTRAÇÃO - Nota não válida fiscalmente)',
            'invoice' => $invoice->fresh(),
            'demo' => true,
        ];
    }

    /**
     * Gerar chave NFe fake para demo
     */
    private function generateFakeChaveNfe(): string
    {
        $uf = '35'; // SP
        $aamm = now()->format('ym');
        $cnpj = str_pad($this->config->cnpj ?? '00000000000000', 14, '0', STR_PAD_LEFT);
        $mod = '55';
        $serie = str_pad($this->config->serie_nfe ?? 1, 3, '0', STR_PAD_LEFT);
        $numero = str_pad($this->config->numero_nfe_atual ?? 1, 9, '0', STR_PAD_LEFT);
        $tpEmis = '1';
        $codigo = str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        
        $chave = $uf . $aamm . $cnpj . $mod . $serie . $numero . $tpEmis . $codigo;
        
        // Adicionar dígito verificador fake
        $dv = rand(0, 9);
        
        return $chave . $dv;
    }

    /**
     * Consultar status de uma NF-e
     */
    public function consultarNfe(Invoice $invoice): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/v2/nfe/{$invoice->ref}");
            
            return $this->processStatusResponse($invoice, $response->json());
            
        } catch (\Exception $e) {
            Log::error('Erro ao consultar NF-e', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cancelar NF-e
     */
    public function cancelarNfe(Invoice $invoice, string $justificativa): array
    {
        if (!$invoice->canBeCancelled()) {
            return [
                'success' => false,
                'message' => 'Esta nota não pode ser cancelada. Prazo de 24h expirado ou nota não autorizada.',
            ];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->delete("{$this->baseUrl}/v2/nfe/{$invoice->ref}", [
                    'justificativa' => $justificativa,
                ]);
            
            $data = $response->json();
            
            if (isset($data['status']) && $data['status'] === 'cancelado') {
                $invoice->update([
                    'status' => Invoice::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                    'cancel_protocol' => $data['protocolo_cancelamento'] ?? null,
                    'cancel_reason' => $justificativa,
                ]);
                
                return ['success' => true, 'message' => 'NF-e cancelada com sucesso.'];
            }
            
            return [
                'success' => false,
                'message' => $data['mensagem'] ?? 'Erro ao cancelar NF-e.',
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar NF-e', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Criar registro da Invoice no banco
     */
    private function createInvoiceRecord(Order $order): Invoice
    {
        $invoice = Invoice::create([
            'tenant_id' => $order->tenant_id,
            'order_id' => $order->id,
            'ref' => Invoice::generateRef($order->tenant_id, $order->id),
            'serie' => $this->config->serie_nfe,
            'numero' => $this->config->getNextNfeNumber(),
            'valor_produtos' => $order->subtotal,
            'valor_frete' => $order->delivery_fee ?? 0,
            'valor_desconto' => $order->discount ?? 0,
            'valor_total' => $order->total,
            'status' => Invoice::STATUS_PROCESSING,
        ]);

        // Criar itens da invoice
        foreach ($order->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'order_item_id' => $item->id,
                'codigo' => $item->id,
                'descricao' => $this->buildDescricao($item),
                'ncm' => $this->config->default_ncm,
                'cfop' => $this->config->default_cfop,
                'unidade' => 'UN',
                'quantidade' => $item->quantity,
                'valor_unitario' => $item->unit_price,
                'valor_total' => $item->total_price,
                'origem' => '0',
                'csosn' => $this->getCsosn(),
            ]);
        }

        // Calcular tributos da Reforma Tributária (IBS/CBS)
        $this->applyTaxReformCalculations($invoice);

        return $invoice->load('items');
    }

    /**
     * Aplicar cálculos da Reforma Tributária (IBS/CBS) - Fase 2026
     */
    private function applyTaxReformCalculations(Invoice $invoice): void
    {
        // 1º de janeiro de 2026: CBS (0,9%) e IBS (0,1%)
        $pCBS = 0.90;
        $pIBS = 0.10;
        
        $totalIbs = 0;
        $totalCbs = 0;
        $totalBc = 0;

        foreach ($invoice->items as $item) {
            $bc = $item->valor_total;
            $ibs = round(($bc * $pIBS) / 100, 2);
            $cbs = round(($bc * $pCBS) / 100, 2);

            $item->update([
                'ibs_cbs_base_calculo' => $bc,
                'ibs_valor' => $ibs,
                'cbs_valor' => $cbs,
                'pIBS' => $pIBS,
                'pCBS' => $pCBS,
                'cst_ibs_cbs' => '01', // Tributado integralmente (Padrão inicial)
            ]);

            $totalBc += $bc;
            $totalIbs += $ibs;
            $totalCbs += $cbs;
        }

        $invoice->update([
            'ibs_cbs_base_calculo' => $totalBc,
            'ibs_valor_total' => $totalIbs,
            'cbs_valor_total' => $totalCbs,
        ]);
    }

    /**
     * Montar payload da NF-e conforme layout Focus NFe
     */
    private function buildNfePayload(Order $order, Invoice $invoice): array
    {
        $client = $order->client;
        
        return [
            'natureza_operacao' => $this->config->natureza_operacao,
            'data_emissao' => now()->format('Y-m-d\TH:i:sP'),
            'tipo_documento' => 1, // 0=Entrada, 1=Saída
            'finalidade_emissao' => 1, // 1=Normal
            'consumidor_final' => 1, // 1=Consumidor final
            'presenca_comprador' => 1, // 1=Presencial
            
            // Emitente (dados do tenant)
            'cnpj_emitente' => $this->config->cnpj,
            'inscricao_estadual_emitente' => $this->config->inscricao_estadual,
            
            // Destinatário (cliente)
            'nome_destinatario' => $client->name,
            'cpf_destinatario' => $this->formatCpfCnpj($client->cpf_cnpj),
            'telefone_destinatario' => preg_replace('/\D/', '', $client->phone_primary ?? ''),
            'logradouro_destinatario' => $client->address ?? 'Não informado',
            'numero_destinatario' => 'S/N',
            'bairro_destinatario' => $client->city ?? 'Não informado',
            'municipio_destinatario' => $client->city ?? 'Não informado',
            'uf_destinatario' => $client->state ?? 'SP',
            'cep_destinatario' => preg_replace('/\D/', '', $client->zip_code ?? '00000000'),
            'indicador_inscricao_estadual_destinatario' => 9, // 9=Não contribuinte
            
            // Valores totais
            'valor_produtos' => number_format($invoice->valor_produtos, 2, '.', ''),
            'valor_frete' => number_format($invoice->valor_frete, 2, '.', ''),
            'valor_desconto' => number_format($invoice->valor_desconto, 2, '.', ''),
            'valor_total' => number_format($invoice->valor_total, 2, '.', ''),
            
            // Itens
            'items' => $this->buildItemsPayload($invoice),
            
            // Reforma Tributária (Apenas totais)
            'ibs_cbs_base_calculo' => number_format($invoice->ibs_cbs_base_calculo, 2, '.', ''),
            'ibs_valor_total' => number_format($invoice->ibs_valor_total, 2, '.', ''),
            'cbs_valor_total' => number_format($invoice->cbs_valor_total, 2, '.', ''),

            // Forma de pagamento
            'formas_pagamento' => [
                [
                    'forma_pagamento' => $this->mapPaymentMethod($order),
                    'valor_pagamento' => number_format($invoice->valor_total, 2, '.', ''),
                ]
            ],
        ];
    }

    /**
     * Montar itens para o payload
     */
    private function buildItemsPayload(Invoice $invoice): array
    {
        $items = [];
        $itemNumber = 1;
        
        foreach ($invoice->items as $item) {
            $items[] = [
                'numero_item' => $itemNumber++,
                'codigo_produto' => (string) $item->codigo,
                'descricao' => $item->descricao,
                'ncm' => $item->ncm,
                'cfop' => $item->cfop,
                'unidade_comercial' => $item->unidade,
                'quantidade_comercial' => number_format($item->quantidade, 4, '.', ''),
                'valor_unitario_comercial' => number_format($item->valor_unitario, 4, '.', ''),
                'valor_bruto' => number_format($item->valor_total, 2, '.', ''),
                'unidade_tributavel' => $item->unidade,
                'quantidade_tributavel' => number_format($item->quantidade, 4, '.', ''),
                'valor_unitario_tributavel' => number_format($item->valor_unitario, 4, '.', ''),
                'origem' => $item->origem,
                'csosn' => $item->csosn,
                'inclui_no_total' => 1,

                // Campos Reforma Tributária
                'ibs_cbs_situacao_tributaria' => $item->cst_ibs_cbs ?? '01',
                'ibs_cbs_base_calculo' => number_format($item->ibs_cbs_base_calculo, 2, '.', ''),
                'ibs_aliquota' => number_format($item->pIBS, 2, '.', ''),
                'ibs_valor' => number_format($item->ibs_valor, 2, '.', ''),
                'cbs_aliquota' => number_format($item->pCBS, 2, '.', ''),
                'cbs_valor' => number_format($item->cbs_valor, 2, '.', ''),
            ];
        }
        
        return $items;
    }

    /**
     * Enviar NF-e para Focus NFe
     */
    private function sendToFocusNfe(string $ref, array $payload): array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/v2/nfe?ref={$ref}", $payload);
        
        return $response->json();
    }

    /**
     * Processar resposta da emissão
     */
    private function processResponse(Invoice $invoice, array $response): array
    {
        $invoice->update([
            'attempts' => $invoice->attempts + 1,
            'last_attempt_at' => now(),
        ]);

        // Status de processamento (ainda aguardando SEFAZ)
        if (isset($response['status']) && $response['status'] === 'processando_autorizacao') {
            $invoice->update(['status' => Invoice::STATUS_PROCESSING]);
            
            return [
                'success' => true,
                'message' => 'NF-e enviada para processamento. Aguarde a autorização.',
                'invoice' => $invoice,
            ];
        }

        // Autorizado
        if (isset($response['status']) && $response['status'] === 'autorizado') {
            $this->saveInvoiceFiles($invoice, $response);
            
            $invoice->update([
                'status' => Invoice::STATUS_AUTHORIZED,
                'chave_nfe' => $response['chave_nfe'] ?? null,
                'protocolo' => $response['protocolo'] ?? null,
                'data_emissao' => now(),
                'status_sefaz' => $response['status_sefaz'] ?? null,
                'motivo_sefaz' => $response['mensagem_sefaz'] ?? null,
            ]);
            
            return [
                'success' => true,
                'message' => 'NF-e autorizada com sucesso!',
                'invoice' => $invoice->fresh(),
            ];
        }

        // Erro ou rejeição
        $invoice->update([
            'status' => Invoice::STATUS_ERROR,
            'status_sefaz' => $response['status_sefaz'] ?? null,
            'motivo_sefaz' => $response['mensagem_sefaz'] ?? $response['mensagem'] ?? 'Erro desconhecido',
            'error_log' => array_merge($invoice->error_log ?? [], [$response]),
        ]);

        return [
            'success' => false,
            'message' => $response['mensagem_sefaz'] ?? $response['mensagem'] ?? 'Erro ao emitir NF-e',
            'invoice' => $invoice,
        ];
    }

    /**
     * Processar resposta de consulta de status
     */
    private function processStatusResponse(Invoice $invoice, array $response): array
    {
        if (isset($response['status'])) {
            switch ($response['status']) {
                case 'autorizado':
                    $this->saveInvoiceFiles($invoice, $response);
                    $invoice->update([
                        'status' => Invoice::STATUS_AUTHORIZED,
                        'chave_nfe' => $response['chave_nfe'] ?? null,
                        'protocolo' => $response['protocolo'] ?? null,
                        'data_emissao' => now(),
                    ]);
                    return ['success' => true, 'status' => 'authorized'];
                    
                case 'cancelado':
                    $invoice->update(['status' => Invoice::STATUS_CANCELLED]);
                    return ['success' => true, 'status' => 'cancelled'];
                    
                case 'erro_autorizacao':
                    $invoice->update([
                        'status' => Invoice::STATUS_DENIED,
                        'motivo_sefaz' => $response['mensagem_sefaz'] ?? null,
                    ]);
                    return ['success' => false, 'status' => 'denied'];
            }
        }
        
        return ['success' => true, 'status' => $response['status'] ?? 'processing'];
    }

    /**
     * Salvar arquivos XML e PDF
     */
    private function saveInvoiceFiles(Invoice $invoice, array $response): void
    {
        $basePath = "invoices/{$invoice->tenant_id}/{$invoice->id}";
        
        // Baixar e salvar XML
        if (!empty($response['caminho_xml_nota_fiscal'])) {
            try {
                $xml = Http::get($response['caminho_xml_nota_fiscal'])->body();
                $xmlPath = "{$basePath}/nfe.xml";
                Storage::disk('public')->put($xmlPath, $xml);
                $invoice->update(['xml_path' => $xmlPath]);
            } catch (\Exception $e) {
                Log::warning('Falha ao salvar XML', ['error' => $e->getMessage()]);
            }
        }
        
        // Baixar e salvar PDF (DANFE)
        if (!empty($response['caminho_danfe'])) {
            try {
                $pdf = Http::get($response['caminho_danfe'])->body();
                $pdfPath = "{$basePath}/danfe.pdf";
                Storage::disk('public')->put($pdfPath, $pdf);
                $invoice->update(['pdf_path' => $pdfPath]);
            } catch (\Exception $e) {
                Log::warning('Falha ao salvar DANFE', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Headers para API Focus NFe
     */
    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->config->api_token . ':'),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Montar descrição do produto
     */
    private function buildDescricao($item): string
    {
        $parts = [];
        
        if ($item->fabric) $parts[] = $item->fabric;
        if ($item->model) $parts[] = $item->model;
        if ($item->art_name) $parts[] = $item->art_name;
        
        $desc = implode(' - ', $parts);
        
        // Limitar a 120 caracteres
        return mb_substr($desc ?: 'Produto', 0, 120);
    }

    /**
     * Formatar CPF/CNPJ
     */
    private function formatCpfCnpj(?string $value): string
    {
        if (empty($value)) return '';
        return preg_replace('/\D/', '', $value);
    }

    /**
     * Obter CSOSN baseado no regime tributário
     */
    private function getCsosn(): string
    {
        // 102 = Tributado pelo Simples Nacional sem permissão de crédito
        // 103 = Isenção de ICMS para faixa de receita bruta
        // 500 = ICMS cobrado anteriormente por ST ou antecipação
        return '102';
    }

    /**
     * Mapear forma de pagamento
     */
    private function mapPaymentMethod(Order $order): string
    {
        $method = $order->payment?->method ?? 'pix';
        
        return match (strtolower($method)) {
            'dinheiro', 'especie' => '01',
            'cheque' => '02',
            'cartao_credito', 'credito' => '03',
            'cartao_debito', 'debito' => '04',
            'pix' => '17',
            'boleto' => '15',
            default => '99', // Outros
        };
    }
}
