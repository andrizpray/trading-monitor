<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';

    protected $fillable = [
        'user_id', 'trade_history_id', 'currency_pair', 'trade_type',
        'profit_loss', 'result', 'analysis', 'lesson_learned',
        'emotion_score', 'market_condition', 'strategy_used',
        'auto_imported', 'screenshot_path', 'tags',
        'template_type', 'plan_setup', 'plan_entry',
        'plan_sl', 'plan_tp', 'plan_reasoning',
    ];

    protected $casts = [
        'profit_loss' => 'decimal:2',
        'emotion_score' => 'integer',
        'auto_imported' => 'boolean',
        'plan_sl' => 'decimal:8',
        'plan_tp' => 'decimal:8',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
