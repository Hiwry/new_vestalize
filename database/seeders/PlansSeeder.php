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
     * - personalized     → Módulo de Personalizados
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
     * - catalog          → Catálogo (acesso aos módulos de catálogo)
     * 
     * BRANDING:
     * - branding         → Personalização de Marca (logo, cores, white-label)
     * 
     * ADMINISTRATIVO:
     * - subscription_module → Módulo de Assinatura (visualização na sidebar)
     * - invoices            → Notas Fiscais (NF-e)
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
            // PLANO PERSONALIZE - R$ 99,90/mês
            // Ideal para: operações focadas em personalizados
            // ========================================
            'start' => [
                'name' => 'Personalize',
                'slug' => 'start',
                'price' => 99.90,
                'description' => 'Plano focado em personalizados, com financeiro e kanban específico.',
                'limits' => [
                    'stores' => 1,
                    'users' => 1,
                ],
                'features' => [
                    'personalized',     // Módulo de Personalizados
                    'financial',        // Gestão Financeira
                    'kanban',           // Kanban de Produção (vista personalizados)
                    'subscription_module', // Visualizar assinatura
                ],
            ],

            // ========================================
            // PLANO MÉDIO - R$ 299,90/mês
            // Ideal para: Vendas completas + produção + catálogo
            // ========================================
            'basic' => [
                'name' => 'Plano Medio',
                'slug' => 'basic',
                'price' => 299.90,
                'description' => 'Vendas completas, produção, catálogo e financeiro.',
                'limits' => [
                    'stores' => 1,
                    'users' => 2,
                ],
                'features' => [
                    'orders',           // Gestão de Pedidos
                    'crm',              // CRM / Clientes
                    'pdv',              // Frente de Caixa (PDV)
                    'pdf_quotes',       // Orçamentos em PDF
                    'external_quote',   // Orçamento Online
                    'production',       // Módulo de Produção
                    'financial',        // Gestão Financeira
                    'kanban',           // Kanban de Produção
                    'personalized',     // Módulo de Personalizados
                    'catalog',          // Catálogo
                    'subscription_module', // Visualizar assinatura
                ],
            ],

            // ========================================
            // PLANO PRO EMPRESAS - R$ 499,90/mês
            // Ideal para: Empresas com múltiplas lojas e equipe maior
            // ========================================
            'pro' => [
                'name' => 'Pro Empresas',
                'slug' => 'pro',
                'price' => 499.90,
                'description' => 'Acesso completo a todas as funcionalidades com limites ampliados.',
                'limits' => [
                    'stores' => 4,
                    'users' => 15,
                ],
                'features' => ['*'], // Acesso a TUDO
            ],

            // Removido: plano premium (não faz mais parte da grade atual)
        ];

        foreach ($plans as $key => $data) {
            $plan = \App\Models\Plan::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
            
            // Update existing tenants with legacy 'plan' column
            \App\Models\Tenant::where('plan', $key)->update(['plan_id' => $plan->id]);
        }

        // Remover planos antigos que não fazem mais parte da grade (apenas se não estiverem em uso)
        $allowedSlugs = collect($plans)->pluck('slug')->values()->toArray();
        $unusedPlans = \App\Models\Plan::whereNotIn('slug', $allowedSlugs)->get();
        foreach ($unusedPlans as $unusedPlan) {
            $hasTenants = \App\Models\Tenant::where('plan_id', $unusedPlan->id)->exists();
            if (!$hasTenants) {
                $unusedPlan->delete();
            }
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
