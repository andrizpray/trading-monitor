<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeHistory extends Model
{
    protected $table = 'trade_histories';

    protected $fillable = [
        'user_id', 'trading_account_id', 'ticket', 'open_date', 'close_date',
        'currency_pair', 'trade_type', 'lot_size', 'open_price', 'close_price',
        'stop_loss', 'take_profit', 'swap', 'commission', 'profit_loss',
        'result', 'duration_minutes', 'comment', 'imported_at',
    ];

    protected $casts = [
        'open_date' => 'date',
        'close_date' => 'date',
        'lot_size' => 'decimal:2',
        'open_price' => 'decimal:8',
        'close_price' => 'decimal:8',
        'stop_loss' => 'decimal:8',
        'take_profit' => 'decimal:8',
        'swap' => 'decimal:2',
        'commission' => 'decimal:2',
        'profit_loss' => 'decimal:2',
        'duration_minutes' => 'integer',
        'imported_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tradingAccount(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class);
    }
}
