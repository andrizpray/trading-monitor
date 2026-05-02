<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorSnapshot extends Model
{
    protected $table = 'monitor_snapshots';

    protected $fillable = [
        'snapshot_date',
        'total_users',
        'active_users_today',
        'active_users_week',
        'active_users_month',
        'new_users_today',
        'total_entries',
        'total_pnl',
        'avg_pnl',
        'win_rate',
        'cpu_percent',
        'ram_percent',
        'disk_percent',
        'disk_used_gb',
    ];
}
