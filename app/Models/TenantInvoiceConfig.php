<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class TenantInvoiceConfig extends Model
{
    protected $fillable = [
        'tenant_id',
        'provider',
        'api_token',
        'environment',
        'certificate_path',
        'certificate_password',
        'certificate_expires_at',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'regime_tributario',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'cep',
        'codigo_municipio',
        'default_cfop',
        'default_ncm',
        'natureza_operacao',
        'serie_nfe',
        'numero_nfe_atual',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'certificate_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
        'regime_tributario' => 'integer',
        'serie_nfe' => 'integer',
        'numero_nfe_atual' => 'integer',
    ];

    /**
     * Campos que devem ser criptografados
     */
    protected $encryptedFields = ['api_token', 'certificate_password'];

    /**
     * Relacionamento com o Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Accessor para decriptar API Token
     */
    public function getApiTokenAttribute($value): ?string
    {
        if (empty($value)) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Mutator para encriptar API Token
     */
    public function setApiTokenAttribute($value): void
    {
        $this->attributes['api_token'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Accessor para decriptar senha do certificado
     */
    public function getCertificatePasswordAttribute($value): ?string
    {
        if (empty($value)) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Mutator para encriptar senha do certificado
     */
    public function setCertificatePasswordAttribute($value): void
    {
        $this->attributes['certificate_password'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Verifica se a configuração está completa
     */
    public function isComplete(): bool
    {
        // Se for modo DEMO, não exige certificado
        if ($this->api_token === 'DEMO') {
            return !empty($this->cnpj) &&
                   !empty($this->razao_social) &&
                   !empty($this->inscricao_estadual);
        }

        return !empty($this->api_token) &&
               !empty($this->certificate_path) &&
               !empty($this->cnpj) &&
               !empty($this->razao_social) &&
               !empty($this->inscricao_estadual);
    }

    /**
     * Verifica se o certificado está válido
     */
    public function isCertificateValid(): bool
    {
        if (empty($this->certificate_expires_at)) {
            return false;
        }
        return $this->certificate_expires_at->isFuture();
    }

    /**
     * Retorna o próximo número de NF-e
     */
    public function getNextNfeNumber(): int
    {
        $next = $this->numero_nfe_atual + 1;
        $this->update(['numero_nfe_atual' => $next]);
        return $next;
    }

    /**
     * Retorna o regime tributário formatado
     */
    public function getRegimeTributarioLabelAttribute(): string
    {
        return match($this->regime_tributario) {
            1 => 'Simples Nacional',
            2 => 'Simples Nacional - Excesso de sublimite',
            3 => 'Regime Normal',
            default => 'Não definido',
        };
    }
}
