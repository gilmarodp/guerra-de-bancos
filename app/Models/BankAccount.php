<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasFactory;

    public const SLUG_UNSAFE = 'unsafe';

    public const SLUG_SAFE = 'safe';

    protected $fillable = [
        'slug',
        'name',
        'is_safe',
        'show_qr',
        'withdrawals_enabled',
        'initial_balance',
        'balance',
        'withdrawal_amount',
    ];

    protected $casts = [
        'is_safe' => 'boolean',
        'show_qr' => 'boolean',
        'withdrawals_enabled' => 'boolean',
    ];

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(BankEntry::class);
    }
}
