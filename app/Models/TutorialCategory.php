<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'icone',
        'cor',
        'perfil',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function tutorials()
    {
        return $this->hasMany(Tutorial::class)->orderBy('ordem');
    }

    public function activeTutorials()
    {
        return $this->hasMany(Tutorial::class)->where('ativo', true)->orderBy('ordem');
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePerfil($query, $perfil)
    {
        return $query->where('perfil', $perfil);
    }
}
