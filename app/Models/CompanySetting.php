<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    /**
     * Relacionamento com Store
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
    protected $fillable = [
        'store_id',
        'company_name',
        'company_address',
        'company_city',
        'company_state',
        'company_zip',
        'company_phone',
        'company_email',
        'company_website',
        'company_cnpj',
        'logo_path',
        'bank_name',
        'bank_agency',
        'bank_account',
        'pix_key',
        'footer_text',
        'terms_conditions',
    ];

    /**
     * Obter as configurações (sempre retorna o primeiro registro ou por store_id)
     */
    public static function getSettings($storeId = null, $tenantId = null)
    {
        if ($storeId) {
            // Primeiro tenta buscar as configurações da loja específica
            $settings = self::where('store_id', $storeId)->first();
            
            if ($settings && !empty($settings->terms_conditions)) {
                return $settings;
            }
            
            // Se não encontrou ou os termos estão vazios, tenta buscar da loja principal DO TENANT
            $mainStoreQuery = \App\Models\Store::where('is_main', true);
            
            if ($tenantId) {
                $mainStoreQuery->where('tenant_id', $tenantId);
            } else if ($storeId) {
                // Tenta inferir o tenant_id da loja fornecida
                $providedStore = \App\Models\Store::find($storeId);
                if ($providedStore) {
                    $mainStoreQuery->where('tenant_id', $providedStore->tenant_id);
                }
            }

            $mainStore = $mainStoreQuery->first();

            if ($mainStore && $mainStore->id != $storeId) {
                $mainSettings = self::where('store_id', $mainStore->id)->first();
                if ($mainSettings) {
                    return $mainSettings;
                }
            }
            
            // Retorna o que encontrou inicialmente ou uma instância vazia
            return $settings ?: new self(['store_id' => $storeId]);
        }
        
        // Se não foi passado store_id, busca configurações globais ou da loja principal
        $globalSettings = self::whereNull('store_id')->first();
        if ($globalSettings) {
            return $globalSettings;
        }
        
        $mainStoreQuery = \App\Models\Store::where('is_main', true);
        if ($tenantId) {
            $mainStoreQuery->where('tenant_id', $tenantId);
        }
        
        $mainStore = $mainStoreQuery->first();
        if ($mainStore) {
            $mainSettings = self::where('store_id', $mainStore->id)->first();
            if ($mainSettings) {
                return $mainSettings;
            }
        }
        
        return new self();
    }

    /**
     * Obter o caminho completo do logo
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path && file_exists(public_path($this->logo_path))) {
            return asset($this->logo_path);
        }
        return null;
    }
}

