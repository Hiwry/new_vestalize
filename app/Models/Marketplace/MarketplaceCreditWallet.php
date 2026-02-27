<?php

namespace App\Models\Marketplace;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceCreditWallet extends Model
{
    protected $table = 'marketplace_credit_wallets';

    protected $fillable = [
        'user_id',
        'balance',
        'total_purchased',
        'total_spent',
    ];

    // ─── Relationships ─────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MarketplaceCreditTransaction::class, 'user_id', 'user_id')
            ->latest();
    }

    // ─── Helpers ───────────────────────────────────────────────

    /**
     * Obtém ou cria a carteira para um usuário
     */
    public static function getOrCreate(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'total_purchased' => 0, 'total_spent' => 0]
        );
    }

    public function hasBalance(int $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Adiciona créditos à carteira e registra transação
     */
    public function credit(int $amount, string $type, string $description, array $meta = []): MarketplaceCreditTransaction
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->total_purchased += $amount;
        $this->save();

        return MarketplaceCreditTransaction::create([
            'user_id'        => $this->user_id,
            'type'           => $type,
            'amount'         => $amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $this->balance,
            'description'    => $description,
            ...$meta,
        ]);
    }

    /**
     * Debita créditos da carteira e registra transação
     * Retorna false se saldo insuficiente
     */
    public function debit(int $amount, string $description, array $meta = []): MarketplaceCreditTransaction|false
    {
        if (!$this->hasBalance($amount)) {
            return false;
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->total_spent += $amount;
        $this->save();

        return MarketplaceCreditTransaction::create([
            'user_id'        => $this->user_id,
            'type'           => 'spend',
            'amount'         => -$amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $this->balance,
            'description'    => $description,
            ...$meta,
        ]);
    }
}
