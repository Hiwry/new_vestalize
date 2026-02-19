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
    public static function getSettings($storeId = null)
    {
        if ($storeId) {
            // Primeiro tenta buscar as configurações da loja específica
            $settings = self::where('store_id', $storeId)->first();
            
            if ($settings) {
                \Log::info("CompanySettings encontradas para loja ID: {$storeId}", [
                    'store_id' => $storeId,
                    'company_name' => $settings->company_name,
                    'logo_path' => $settings->logo_path
                ]);
                return $settings;
            }
            
            // Se não encontrou, tenta buscar da loja principal
            $mainStore = \App\Models\Store::where('is_main', true)->first();
            if ($mainStore && $mainStore->id != $storeId) {
                $mainSettings = self::where('store_id', $mainStore->id)->first();
                if ($mainSettings) {
                    \Log::info("CompanySettings não encontradas para loja ID: {$storeId}, usando loja principal ID: {$mainStore->id}", [
                        'requested_store_id' => $storeId,
                        'main_store_id' => $mainStore->id,
                        'company_name' => $mainSettings->company_name
                    ]);
                    return $mainSettings;
                }
            }
            
            // Se ainda não encontrou, retorna uma instância vazia
            \Log::warning("CompanySettings não encontradas para loja ID: {$storeId} e nem para loja principal", [
                'store_id' => $storeId
            ]);
            return new self(['store_id' => $storeId]);
        }
        
        // Se não foi passado store_id, busca configurações globais ou da loja principal
        $globalSettings = self::whereNull('store_id')->first();
        if ($globalSettings) {
            return $globalSettings;
        }
        
        $mainStore = \App\Models\Store::where('is_main', true)->first();
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

