<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SublimationLocation extends Model
{
    protected $fillable = [
        'name',
        'order',
        'active',
        'show_in_pdf',
        'personalization_types',
        'pdf_note',
    ];

    protected $casts = [
        'active' => 'boolean',
        'show_in_pdf' => 'boolean',
        'personalization_types' => 'array',
    ];

    /**
     * Verifica se este local é usado por um tipo de personalização
     */
    public function isUsedByType(string $type): bool
    {
        if (empty($this->personalization_types)) {
            return true; // Se não tem restrição, está disponível para todos
        }

        return in_array($type, $this->personalization_types);
    }

    /**
     * Scope para locais que aparecem no PDF
     */
    public function scopeVisibleInPdf($query)
    {
        return $query->where('show_in_pdf', true);
    }

    /**
     * Scope para locais ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
