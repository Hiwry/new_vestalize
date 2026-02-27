<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DesignerProfile extends Model
{
    protected $table = 'marketplace_designer_profiles';

    protected $fillable = [
        'user_id',
        'slug',
        'display_name',
        'bio',
        'avatar',
        'specialties',
        'instagram',
        'behance',
        'portfolio_url',
        'status',
        'commission_rate',
        'notify_new_orders',
    ];

    protected $casts = [
        'specialties' => 'array',
        'rating_average' => 'decimal:2',
        'commission_rate' => 'integer',
        'notify_new_orders' => 'boolean',
    ];

    public static array $specialtyLabels = [
        'logo'              => 'Logo & Marca',
        'vetorizacao'       => 'Vetorização',
        'estampa'           => 'Estampa',
        'mockup'            => 'Mockup',
        'identidade_visual' => 'Identidade Visual',
        'outros'            => 'Outros',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(MarketplaceService::class, 'designer_profile_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MarketplaceOrder::class, 'designer_id');
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ─── Helpers ───────────────────────────────────────────────

    public function getSpecialtyLabelsAttribute(): array
    {
        $specialties = $this->specialties ?? [];
        return array_map(fn($s) => self::$specialtyLabels[$s] ?? $s, $specialties);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->display_name) . '&background=6366f1&color=fff&size=128';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Gera slug único a partir do display_name
    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = self::where('slug', 'LIKE', "$slug%")->count();
        return $count > 0 ? "$slug-$count" : $slug;
    }

    // Atualiza média de avaliações
    public function recalculateRating(): void
    {
        $this->rating_average = MarketplaceReview::whereHas('order', function ($q) {
            $q->where('designer_id', $this->id);
        })->avg('rating') ?? 0;

        $this->rating_count = MarketplaceReview::whereHas('order', function ($q) {
            $q->where('designer_id', $this->id);
        })->count();

        $this->saveQuietly();
    }
}
