<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * ============================================
     * LISTA COMPLETA DE FEATURES DISPONÍVEIS
     * ============================================
     * 
     * CORE (Básico):
     * - orders           → Gestão de Pedidos (criar, editar, visualizar pedidos)
     * - crm              → CRM / Clientes (cadastro e gestão de clientes)
     * 
     * RELATÓRIOS:
     * - reports_simple   → Relatórios Simples (gráficos básicos, fluxo de pedidos)
     * - reports_complete → Relatórios Completos (BI avançado, análises detalhadas)
     * 
     * FINANCEIRO:
     * - financial        → Gestão Financeira (dashboard financeiro, caixa, faturamento)
     * 
     * PRODUÇÃO:
     * - production       → Módulo de Produção (ordens de produção, dashboard produção)
     * - kanban           → Kanban de Produção (board visual, calendário de entregas)
     * 
     * VENDAS:
     * - pdv              → Frente de Caixa (PDV) (ponto de venda, vendas rápidas)
     * - pdf_quotes       → Orçamentos em PDF (geração de PDFs de orçamento)
     * - external_quote   → Orçamento Online/Self-Service (link público para orçamentos)
     * 
     * ESTOQUE:
     * - stock            → Controle de Estoque (gestão completa de estoque)
     * 
     * CATÁLOGO:
     * - sublimation_total → Sublimação Total (produtos de sublimação total)
     * 
     * BRANDING:
     * - branding         → Personalização de Marca (logo, cores, white-label)
     * 
     * ADMINISTRATIVO:
     * - subscription_module → Módulo de Assinatura (visualização na sidebar)
     * 
     * ESPECIAL:
     * - *                → Acesso Ilimitado (todas as funcionalidades)
     * 
     * ============================================
     */
    public function run(): void
    {
        $plans = [
            // ========================================
            // PLANO START - R$ 100/mês
            // Ideal para: MEI, autônomos, início de operação
            // ========================================
            'start' => [
                'name' => 'Start',
                'slug' => 'start',
                'price' => 100.00,
                'description' => 'Plano essencial para quem está começando. Funcionalidades básicas para gestão de pedidos e clientes.',
                'limits' => [
                    'stores' => 1,
                    'users' => 1,
                ],
                'features' => [
                    'orders',           // Gestão de Pedidos
                    'crm',              // CRM / Clientes
                    'financial',        // Gestão Financeira
                    'branding',         // Personalização de Marca
                    'subscription_module', // Visualizar assinatura
                ],
            ],

            // ========================================
            // PLANO BÁSICO - R$ 200/mês
            // Ideal para: Pequenas empresas, 1-2 funcionários
            // ========================================
            'basic' => [
                'name' => 'Básico',
                'slug' => 'basic',
                'price' => 200.00,
                'description' => 'Plano completo para pequenos negócios com relatórios e organização visual.',
                'limits' => [
                    'stores' => 1,
                    'users' => 3,
                ],
                'features' => [
                    'orders',           // Gestão de Pedidos
                    'crm',              // CRM / Clientes
                    'financial',        // Gestão Financeira
                    'reports_simple',   // Relatórios Simples
                    'kanban',           // Kanban de Produção
                    'production',       // Módulo de Produção
                    'pdf_quotes',       // Orçamentos em PDF
                    'branding',         // Personalização de Marca
                    'subscription_module', // Visualizar assinatura
                ],
            ],

            // ========================================
            // PLANO PRO - R$ 300/mês
            // Ideal para: Empresas em crescimento, equipes médias
            // ========================================
            'pro' => [
                'name' => 'Profissional',
                'slug' => 'pro',
                'price' => 300.00,
                'description' => 'Plano ideal para negócios em crescimento com PDV, estoque e orçamentos online.',
                'limits' => [
                    'stores' => 1,
                    'users' => 5,
                ],
                'features' => [
                    'orders',           // Gestão de Pedidos
                    'crm',              // CRM / Clientes
                    'financial',        // Gestão Financeira
                    'reports_simple',   // Relatórios Simples
                    'reports_complete', // Relatórios Completos
                    'kanban',           // Kanban de Produção
                    'production',       // Módulo de Produção
                    'pdv',              // Frente de Caixa (PDV)
                    'pdf_quotes',       // Orçamentos em PDF
                    'external_quote',   // Orçamento Online
                    'stock',            // Controle de Estoque
                    'branding',         // Personalização de Marca
                    'subscription_module', // Visualizar assinatura
                ],
            ],

            // ========================================
            // PLANO PREMIUM - R$ 500/mês
            // Ideal para: Empresas consolidadas, multi-loja
            // ========================================
            'premium' => [
                'name' => 'Premium',
                'slug' => 'premium',
                'price' => 500.00,
                'description' => 'Acesso total e ilimitado. Multi-loja, usuários ilimitados e todas as funcionalidades.',
                'limits' => [
                    'stores' => 9999,
                    'users' => 9999,
                ],
                'features' => ['*'], // Acesso a TUDO
            ],
        ];

        foreach ($plans as $key => $data) {
            $plan = \App\Models\Plan::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
            
            // Update existing tenants with legacy 'plan' column
            \App\Models\Tenant::where('plan', $key)->update(['plan_id' => $plan->id]);
        }
        
        $this->command->info('✅ Planos criados/atualizados com sucesso!');
        $this->command->table(
            ['Plano', 'Preço', 'Usuários', 'Lojas', 'Features'],
            collect($plans)->map(function($plan) {
                return [
                    $plan['name'],
                    'R$ ' . number_format($plan['price'], 2, ',', '.'),
                    $plan['limits']['users'] >= 9999 ? '∞' : $plan['limits']['users'],
                    $plan['limits']['stores'] >= 9999 ? '∞' : $plan['limits']['stores'],
                    is_array($plan['features']) && in_array('*', $plan['features']) 
                        ? 'TODAS' 
                        : count($plan['features']) . ' features'
                ];
            })->toArray()
        );
    }
}
