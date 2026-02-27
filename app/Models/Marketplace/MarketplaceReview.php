<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceReview extends Model
{
    protected $table = 'marketplace_reviews';

    protected $fillable = [
        'marketplace_order_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment',
        'designer_reply',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    // ─── Helpers ───────────────────────────────────────────────

    public function getStarsHtmlAttribute(): string
    {
        $filled = str_repeat('★', $this->rating);
        $empty  = str_repeat('☆', 5 - $this->rating);
        return $filled . $empty;
    }
}
