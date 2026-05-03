<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TradingAccount extends Model
{
    protected $table = 'trading_accounts';

    protected $fillable = [
        'user_id', 'account_number', 'account_name', 'broker', 'platform',
        'currency', 'decimal_places_fx', 'decimal_places_jpy', 'decimal_places_metal',
        'sync_method', 'api_token', 'is_active', 'last_synced_at',
        'total_trades', 'total_pnl',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_trades' => 'integer',
        'total_pnl' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tradeHistories(): HasMany
    {
        return $this->hasMany(TradeHistory::class);
    }
}
