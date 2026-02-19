<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'description',
        'features',
        'limits',
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'price' => 'decimal:2',
    ];

    public const AVAILABLE_FEATURES = [
        'orders' => 'Gestão de Pedidos',
        'crm' => 'CRM / Clientes',
        'reports_simple' => 'Relatórios Simples',
        'reports_complete' => 'Relatórios Completos',
        'pdf_quotes' => 'Orçamentos em PDF',
        'production' => 'Módulo de Produção',
        'kanban' => 'Kanban de Produção',
        'personalized' => 'Módulo de Personalizados',
        'pdv' => 'Frente de Caixa (PDV)',
        'stock' => 'Controle de Estoque',
        'bi' => 'Business Intelligence (BI)',
        'external_quote' => 'Orçamento Online (Self-Service)',
        'financial' => 'Gestão Financeira',
        'catalog' => 'Catálogo',
        'invoices' => 'Notas Fiscais (NF-e)',
        'subscription_module' => 'Módulo de Assinatura',
        'branding' => 'Personalização de Marca',
        'sublimation_total' => 'Sublimação Total',
    ];

    public const PUBLIC_SLUGS = [
        'start',
        'basic',
        'pro',
    ];
}
