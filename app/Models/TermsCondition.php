<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsCondition extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'content',
        'version',
        'active',
        'personalization_type',
        'fabric_type_id',
        'title',
        'tenant_id'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the fabric type (tecido)
     */
    public function fabricType(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'fabric_type_id');
    }

    /**
     * Get the active terms and conditions (general or specific)
     */
    public static function getActive($personalizationType = null, $fabricTypeId = null)
    {
        $query = self::where('active', true);
        
        // Se não especificar tipo, retorna termos gerais (sem tipo específico)
        if ($personalizationType === null && $fabricTypeId === null) {
            $query->whereNull('personalization_type')
                  ->whereNull('fabric_type_id');
        } else {
            // Buscar termos específicos primeiro
            $specificQuery = clone $query;
            $specificQuery->where(function($q) use ($personalizationType, $fabricTypeId) {
                if ($personalizationType) {
                    $q->where('personalization_type', $personalizationType);
                } else {
                    $q->whereNull('personalization_type');
                }
                
                if ($fabricTypeId) {
                    $q->where('fabric_type_id', $fabricTypeId);
                } else {
                    $q->whereNull('fabric_type_id');
                }
            });
            
            $specific = $specificQuery->latest()->first();
            
            if ($specific) {
                return $specific;
            }
            
            // Fallback: buscar termos gerais se não encontrar específico
            return $query->whereNull('personalization_type')
                        ->whereNull('fabric_type_id')
                        ->latest()
                        ->first();
        }
        
        return $query->latest()->first();
    }

    /**
     * Get all active terms for a specific combination
     */
    public static function getActiveForOrder($order)
    {
        $terms = collect();
        
        $tenantId = $order->tenant_id ?? null;
        
        // Buscar termos gerais
        $generalQuery = self::where('active', true)
            ->whereNull('personalization_type')
            ->whereNull('fabric_type_id');
            
        if ($tenantId) {
            $generalQuery->where(function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)
                  ->orWhereNull('tenant_id');
            });
        }
        
        $general = $generalQuery->latest()->first();
        
        if ($general) {
            $terms->push($general);
        }
        
        // Buscar termos específicos por item
        if ($order->items) {
            foreach ($order->items as $item) {
                // Termos por tipo de personalização
                if ($item->sublimations) {
                    foreach ($item->sublimations as $sub) {
                        if ($sub->application_type) {
                            // Normalizar o tipo de personalização para maiúsculas
                            $personalizationType = strtoupper(trim($sub->application_type));
                            
                            \Log::info('Searching terms for personalization', [
                                'item_id' => $item->id,
                                'application_type' => $sub->application_type,
                                'normalized_type' => $personalizationType
                            ]);
                            
                            // Buscar termos específicos do tipo de personalização (case-insensitive)
                            $pTermQuery = self::where('active', true)
                                ->whereRaw('UPPER(TRIM(personalization_type)) = ?', [$personalizationType])
                                ->whereNull('fabric_type_id');
                            
                            if ($tenantId) {
                                $pTermQuery->where(function($q) use ($tenantId) {
                                    $q->where('tenant_id', $tenantId)
                                      ->orWhereNull('tenant_id');
                                });
                            }
                                
                            $personalizationTerm = $pTermQuery->latest()->first();
                            
                            \Log::info('Term search result', [
                                'found' => $personalizationTerm ? true : false,
                                'term_id' => $personalizationTerm->id ?? null,
                                'term_title' => $personalizationTerm->title ?? null,
                                'term_personalization_type' => $personalizationTerm->personalization_type ?? null
                            ]);
                            
                            if ($personalizationTerm && !$terms->contains('id', $personalizationTerm->id)) {
                                $terms->push($personalizationTerm);
                            }
                        }
                    }
                }
                
                // Termos por tipo de tecido
                if ($item->fabric) {
                    $fabricId = is_numeric($item->fabric) ? $item->fabric : null;
                    if ($fabricId) {
                        $fTermQuery = self::where('active', true)
                            ->whereNull('personalization_type')
                            ->where('fabric_type_id', $fabricId);
                            
                        if ($tenantId) {
                            $fTermQuery->where(function($q) use ($tenantId) {
                                $q->where('tenant_id', $tenantId)
                                  ->orWhereNull('tenant_id');
                            });
                        }
                            
                        $fabricTerm = $fTermQuery->latest()->first();
                        
                        if ($fabricTerm && !$terms->contains('id', $fabricTerm->id)) {
                            $terms->push($fabricTerm);
                        }
                        
                        // Termos combinados (personalização + tecido)
                        if ($item->sublimations) {
                            foreach ($item->sublimations as $sub) {
                                if ($sub->application_type) {
                                    // Normalizar o tipo de personalização para maiúsculas
                                    $personalizationType = strtoupper(trim($sub->application_type));
                                    
                                    // Buscar termos combinados (personalização + tecido) - case-insensitive
                                    $cTermQuery = self::where('active', true)
                                        ->whereRaw('UPPER(TRIM(personalization_type)) = ?', [$personalizationType])
                                        ->where('fabric_type_id', $fabricId);
                                        
                                    if ($tenantId) {
                                        $cTermQuery->where(function($q) use ($tenantId) {
                                            $q->where('tenant_id', $tenantId)
                                              ->orWhereNull('tenant_id');
                                        });
                                    }
                                        
                                    $combinedTerm = $cTermQuery->latest()->first();
                                    
                                    if ($combinedTerm && !$terms->contains('id', $combinedTerm->id)) {
                                        $terms->push($combinedTerm);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $terms;
    }
}