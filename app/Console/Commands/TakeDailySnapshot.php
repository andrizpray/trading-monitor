<?php

namespace App\Console\Commands;

use App\Models\MonitorSnapshot;
use App\Models\TradeHistory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TakeDailySnapshot extends Command
{
    protected $signature = 'monitor:snapshot';
    protected $description = 'Take daily snapshot of trading & server metrics';

    public function handle(): int
    {
        $today = now()->toDateString();

        // Avoid duplicate
        if (MonitorSnapshot::where('snapshot_date', $today)->exists()) {
            $this->info("Snapshot for {$today} already exists. Skipping.");
            return self::SUCCESS;
        }

        // User metrics
        $totalUsers = User::count();
        $activeToday = TradeHistory::where('close_date', '>=', now()->startOfDay())
            ->distinct('user_id')->count('user_id');
        $activeWeek = TradeHistory::where('close_date', '>=', now()->subDays(7))
            ->distinct('user_id')->count('user_id');
        $activeMonth = TradeHistory::where('close_date', '>=', now()->subDays(30))
            ->distinct('user_id')->count('user_id');
        $newUsersToday = User::where('created_at', '>=', now()->startOfDay())->count();

        // Trade metrics
        $totalEntries = TradeHistory::count();
        $stats = TradeHistory::selectRaw("
            SUM(profit_loss) as total_pnl,
            AVG(profit_loss) as avg_pnl,
            SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as wins,
            COUNT(*) as total
        ")->first();
        $totalPnl = $stats->total_pnl ?? 0;
        $avgPnl = $stats->avg_pnl ?? 0;
        $winRate = $stats->total > 0
            ? round(($stats->wins / $stats->total) * 100, 2)
            : 0;

        // Server metrics
        $cpu = $this->queryPrometheus('100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)');
        $ram = $this->queryPrometheus('(1 - node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100');
        $disk = $this->queryPrometheus('(1 - node_filesystem_avail_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"}) * 100');
        $diskUsedGb = $this->queryPrometheus('(node_filesystem_size_bytes{mountpoint="/"} - node_filesystem_avail_bytes{mountpoint="/"}) / 1073741824');

        MonitorSnapshot::create([
            'snapshot_date' => $today,
            'total_users' => $totalUsers,
            'active_users_today' => $activeToday,
            'active_users_week' => $activeWeek,
            'active_users_month' => $activeMonth,
            'new_users_today' => $newUsersToday,
            'total_entries' => $totalEntries,
            'total_pnl' => $totalPnl,
            'avg_pnl' => $avgPnl,
            'win_rate' => $winRate,
            'cpu_percent' => $cpu,
            'ram_percent' => $ram,
            'disk_percent' => $disk,
            'disk_used_gb' => $diskUsedGb,
        ]);

        $this->info("Snapshot for {$today} saved successfully.");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Users', $totalUsers],
                ['Active Today', $activeToday],
                ['Total Trades', $totalEntries],
                ['Win Rate', $winRate . '%'],
                ['Total P&L', number_format($totalPnl, 2)],
                ['CPU', $cpu . '%'],
                ['RAM', $ram . '%'],
                ['Disk', $disk . '%'],
            ]
        );

        return self::SUCCESS;
    }

    private function queryPrometheus(string $query): float
    {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:9090/api/v1/query', [
                'query' => $query,
            ]);
            $data = $response->json();
            if (($data['status'] ?? '') === 'success' && !empty($data['data']['result'])) {
                return round((float) $data['data']['result'][0]['value'][1], 2);
            }
        } catch (\Throwable $e) {
            $this->warn("Prometheus unavailable: {$e->getMessage()}");
        }
        return 0;
    }
}
