<?php

namespace App\Console\Commands;

use App\Models\JournalEntry;
use App\Models\MonitorSnapshot;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CollectDailySnapshot extends Command
{
    protected $signature = 'snapshot:collect-daily';
    protected $description = 'Collect daily metrics snapshot (user, trading, server)';

    public function handle(): int
    {
        $today = now()->toDateString();

        // User metrics
        $totalUsers = User::count();
        $activeToday = User::whereHas('journalEntries', fn($q) => $q->where('entry_date', '>=', now()->startOfDay()))->count();
        $activeWeek = User::whereHas('journalEntries', fn($q) => $q->where('entry_date', '>=', now()->subDays(7)))->count();
        $activeMonth = User::whereHas('journalEntries', fn($q) => $q->where('entry_date', '>=', now()->subDays(30)))->count();
        $newUsersToday = User::where('created_at', '>=', now()->startOfDay())->count();

        // Trading metrics
        $stats = JournalEntry::selectRaw("
            COUNT(*) as total,
            SUM(profit_loss) as total_pnl,
            AVG(profit_loss) as avg_pnl,
            SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as wins
        ")->first();
        $winRate = $stats->total > 0 ? round(($stats->wins / $stats->total) * 100, 2) : 0;

        // Server metrics from Prometheus
        $cpu = $this->queryPrometheus('100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)');
        $ram = $this->queryPrometheus('(1 - node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100');
        $disk = $this->queryPrometheus('(1 - node_filesystem_avail_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"}) * 100');
        $diskUsedGb = $this->queryPrometheus('(node_filesystem_size_bytes{mountpoint="/"} - node_filesystem_avail_bytes{mountpoint="/"}) / 1073741824');

        // Upsert snapshot
        MonitorSnapshot::updateOrCreate(
            ['snapshot_date' => $today],
            [
                'total_users' => $totalUsers,
                'active_users_today' => $activeToday,
                'active_users_week' => $activeWeek,
                'active_users_month' => $activeMonth,
                'new_users_today' => $newUsersToday,
                'total_entries' => $stats->total ?? 0,
                'total_pnl' => $stats->total_pnl ?? 0,
                'avg_pnl' => $stats->avg_pnl ?? 0,
                'win_rate' => $winRate,
                'cpu_percent' => $cpu,
                'ram_percent' => $ram,
                'disk_percent' => $disk,
                'disk_used_gb' => $diskUsedGb,
            ]
        );

        $this->info("✅ Snapshot collected for {$today}");
        $this->info("   Users: {$totalUsers} (active today: {$activeToday})");
        $this->info("   Trades: {$stats->total} | Win Rate: {$winRate}%");
        $this->info("   CPU: {$cpu}% | RAM: {$ram}% | Disk: {$disk}%");

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
            $this->warn("Prometheus query failed: {$e->getMessage()}");
        }
        return 0;
    }
}
