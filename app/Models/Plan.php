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
        'kanban' => 'Kanban de Produção',
        'pdv' => 'Frente de Caixa (PDV)',
        'stock' => 'Controle de Estoque',
        'bi' => 'Business Intelligence (BI)',
        'external_quote' => 'Orçamento Online (Self-Service)',
        'financial' => 'Gestão Financeira',
        'subscription_module' => 'Módulo de Assinatura',
        'branding' => 'Personalização de Marca',
        'sublimation_total' => 'Sublimação Total',
    ];
}
