<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\MonitorSnapshot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // User metrics
        $totalUsers = User::count();
        $activeToday = User::whereHas('journalEntries', function ($q) {
            $q->where('entry_date', '>=', now()->startOfDay());
        })->count();
        $newUsersToday = User::where('created_at', '>=', now()->startOfDay())->count();

        // Trading metrics
        $totalEntries = JournalEntry::where('user_id', Auth::guard('admin')->check() ? null : null)
            ->count();
        $tradeStats = JournalEntry::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as wins,
            SUM(CASE WHEN result = 'loss' THEN 1 ELSE 0 END) as losses,
            SUM(profit_loss) as total_pnl,
            AVG(profit_loss) as avg_pnl
        ")->first();
        $winRate = $tradeStats->total > 0
            ? round(($tradeStats->wins / $tradeStats->total) * 100, 1)
            : 0;
        $totalPnl = $tradeStats->total_pnl ?? 0;
        $totalTrades = $tradeStats->total ?? 0;

        // Yesterday snapshot for growth %
        $yesterdaySnap = MonitorSnapshot::where('snapshot_date', now()->subDay()->toDateString())->first();
        $userGrowth = ($yesterdaySnap && $yesterdaySnap->total_users > 0)
            ? round((($totalUsers - $yesterdaySnap->total_users) / $yesterdaySnap->total_users) * 100, 1)
            : 0;
        $tradeGrowth = ($yesterdaySnap && $yesterdaySnap->total_entries > 0)
            ? round((($totalTrades - $yesterdaySnap->total_entries) / $yesterdaySnap->total_entries) * 100, 1)
            : 0;

        // Server metrics from Prometheus
        $cpu = $this->queryPrometheus('100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)');
        $ram = $this->queryPrometheus('(1 - node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100');
        $disk = $this->queryPrometheus('(1 - node_filesystem_avail_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"}) * 100');

        // User growth chart (last 30 days)
        $userGrowthRaw = MonitorSnapshot::where('snapshot_date', '>=', now()->subDays(30)->toDateString())
            ->orderBy('snapshot_date')
            ->pluck('total_users', 'snapshot_date');
        $userGrowthLabels = $userGrowthRaw->keys()->map(function ($d) { return date('d M', strtotime($d)); })->values()->all();
        $userGrowthValues = $userGrowthRaw->values()->all();

        // Daily trades chart (last 7 days) — from journal_entries
        $dailyTradesRaw = JournalEntry::selectRaw("DATE(entry_date) as date, COUNT(*) as count")
            ->where('entry_date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        $dailyTradesLabels = $dailyTradesRaw->keys()->map(function ($d) { return date('d M', strtotime($d)); })->values()->all();
        $dailyTradesValues = $dailyTradesRaw->values()->all();

        return view('dashboard.index', compact(
            'totalUsers', 'activeToday', 'newUsersToday', 'totalTrades',
            'winRate', 'totalPnl', 'userGrowth', 'tradeGrowth',
            'cpu', 'ram', 'disk',
            'userGrowthLabels', 'userGrowthValues',
            'dailyTradesLabels', 'dailyTradesValues'
        ));
    }

    public function users()
    {
        // Registration chart (last 30 days)
        $registrationRaw = User::selectRaw("DATE(created_at) as date, COUNT(*) as count")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        $registrationLabels = $registrationRaw->keys()->map(function ($d) { return date('d M', strtotime($d)); })->values()->all();
        $registrationValues = $registrationRaw->values()->all();

        // Active users
        $dau = User::whereHas('journalEntries', fn($q) => $q->where('entry_date', '>=', now()->startOfDay()))->count();
        $wau = User::whereHas('journalEntries', fn($q) => $q->where('entry_date', '>=', now()->subDays(7)))->count();
        $mau = User::whereHas('journalEntries', fn($q) => $q->where('entry_date', '>=', now()->subDays(30)))->count();

        // User list with stats
        $users = User::withCount('journalEntries as total_trades')
            ->withSum('journalEntries as total_pnl', 'profit_loss')
            ->withMax('journalEntries as last_active', 'entry_date')
            ->orderByDesc('total_pnl')
            ->paginate(20);

        // Top traders
        $topTraders = User::withCount('journalEntries as total_trades')
            ->withSum('journalEntries as total_pnl', 'profit_loss')
            ->having('total_trades', '>', 0)
            ->orderByDesc('total_pnl')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $total = $user->journalEntries()->count();
                $wins = $user->journalEntries()->where('result', 'win')->count();
                $user->win_rate = $total > 0 ? round(($wins / $total) * 100, 1) : 0;
                return $user;
            });

        return view('dashboard.users', compact('registrationLabels', 'registrationValues', 'dau', 'wau', 'mau', 'users', 'topTraders'));
    }

    public function portfolio()
    {
        $users = User::withCount('journalEntries as total_trades')
            ->withSum('journalEntries as total_pnl', 'profit_loss')
            ->having('total_trades', '>', 0)
            ->get()
            ->map(function ($user) {
                $wins = $user->journalEntries()->where('result', 'win')->count();
                $total = $user->journalEntries()->count();
                $user->win_rate = $total > 0 ? round(($wins / $total) * 100, 1) : 0;
                $user->growth_percent = ($user->default_capital > 0)
                    ? round(($user->total_pnl / $user->default_capital) * 100, 1)
                    : null;
                return $user;
            })
            ->sortByDesc('total_pnl');

        // Top 10 growth
        $topGrowth = $users->filter(fn($u) => $u->growth_percent !== null)
            ->sortByDesc('growth_percent')
            ->take(10);

        // Pre-compute chart data to avoid @json + fn() in Blade
        $growthLabels = $topGrowth->pluck('name')->values()->all();
        $growthValues = $topGrowth->pluck('growth_percent')->values()->all();
        $growthColors = $topGrowth->map(function ($u) {
            return $u->growth_percent >= 0 ? 'rgba(16,185,129,0.7)' : 'rgba(239,68,68,0.7)';
        })->values()->all();

        return view('dashboard.portfolio', compact('users', 'topGrowth', 'growthLabels', 'growthValues', 'growthColors'));
    }

    public function server()
    {
        // Current metrics
        $cpu = $this->queryPrometheus('100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)');
        $ram = $this->queryPrometheus('(1 - node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100');
        $disk = $this->queryPrometheus('(1 - node_filesystem_avail_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"}) * 100');
        $diskUsedGb = $this->queryPrometheus('(node_filesystem_size_bytes{mountpoint="/"} - node_filesystem_avail_bytes{mountpoint="/"}) / 1073741824');
        $diskTotalGb = $this->queryPrometheus('node_filesystem_size_bytes{mountpoint="/"} / 1073741824');

        // Historical data from snapshots (7 days)
        $historical = MonitorSnapshot::where('snapshot_date', '>=', now()->subDays(7)->toDateString())
            ->orderBy('snapshot_date')
            ->get();

        // Pre-compute chart data
        $historicalLabels = $historical->pluck('snapshot_date')->map(function ($d) { return date('d M', strtotime($d)); })->all();
        $historicalCpu = $historical->pluck('cpu_percent')->all();
        $historicalRam = $historical->pluck('ram_percent')->all();

        return view('dashboard.server', compact('cpu', 'ram', 'disk', 'diskUsedGb', 'diskTotalGb', 'historical', 'historicalLabels', 'historicalCpu', 'historicalRam'));
    }

    public function logs(Request $request)
    {
        $logPath = base_path('../jurnal-trading/storage/logs/laravel.log');
        $logs = [];
        $filtered = [];

        if (file_exists($logPath)) {
            // Read last 5000 lines
            $lines = array_slice(file($logPath), -5000);
            $entries = [];
            $current = null;

            foreach ($lines as $line) {
                // Match log header: [2026-05-02 11:13:27] local.ERROR:
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+):/', $line, $m)) {
                    if ($current) {
                        $entries[] = $current;
                    }
                    $current = [
                        'date' => $m[1],
                        'level' => strtoupper($m[3]),
                        'message' => trim(str_replace($m[0], '', $line)),
                        'stack' => '',
                    ];
                } elseif ($current) {
                    $current['stack'] .= $line;
                }
            }
            if ($current) {
                $entries[] = $current;
            }

            // Filter
            $search = $request->get('search', '');
            $level = $request->get('level', '');

            foreach ($entries as $entry) {
                if ($level && $entry['level'] !== strtoupper($level)) continue;
                if ($search && stripos($entry['message'] . $entry['stack'], $search) === false) continue;
                $filtered[] = $entry;
            }
        }

        // Paginate
        $perPage = 20;
        $page = $request->get('page', 1);
        $total = count($filtered);
        $logs = array_slice($filtered, ($page - 1) * $perPage, $perPage);

        // Count by level
        $errorCount = collect($filtered)->where('level', 'ERROR')->count();
        $warningCount = collect($filtered)->where('level', 'WARNING')->count();
        $criticalCount = collect($filtered)->where('level', 'CRITICAL')->count();

        return view('dashboard.logs', compact('logs', 'total', 'perPage', 'page', 'errorCount', 'warningCount', 'criticalCount'));
    }

    private function queryPrometheus(string $query): float
    {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:9090/api/v1/query', [
                'query' => $query,
            ]);
            $data = $response->json();
            if (($data['status'] ?? '') === 'success' && !empty($data['data']['result'])) {
                return round((float) $data['data']['result'][0]['value'][1], 1);
            }
        } catch (\Throwable $e) {
            // Silent fail — Prometheus might not have data yet
        }
        return 0;
    }
}
