<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutorial_category_id',
        'titulo',
        'descricao',
        'youtube_id',
        'duracao',
        'capa_url',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(TutorialCategory::class, 'tutorial_category_id');
    }

    /**
     * Returns the thumbnail URL — custom cover or YouTube default.
     */
    public function getThumbnailAttribute(): string
    {
        return $this->capa_url ?: "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }
}
