<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\MonitorSnapshot;
use App\Models\TradeHistory;
use App\Models\TradingAccount;
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
        $activeToday = TradeHistory::where('close_date', '>=', now()->startOfDay())
            ->distinct('user_id')->count('user_id');
        $newUsersToday = User::where('created_at', '>=', now()->startOfDay())->count();

        // Trading metrics (from trade_histories)
        $totalTrades = TradeHistory::count();
        $tradeStats = TradeHistory::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as wins,
            SUM(CASE WHEN result = 'loss' THEN 1 ELSE 0 END) as losses,
            SUM(CASE WHEN result = 'break_even' THEN 1 ELSE 0 END) as be,
            SUM(profit_loss) as total_pnl,
            AVG(profit_loss) as avg_pnl,
            SUM(lot_size) as total_lots
        ")->first();
        $winRate = $tradeStats->total > 0
            ? round((($tradeStats->wins ?? 0) / $tradeStats->total) * 100, 1)
            : 0;
        $totalPnl = $tradeStats->total_pnl ?? 0;
        $totalLots = $tradeStats->total_lots ?? 0;

        // Journal metrics
        $totalJournals = JournalEntry::count();

        // Trading accounts
        $totalAccounts = TradingAccount::where('is_active', true)->count();
        $accountsByBroker = TradingAccount::selectRaw("broker, COUNT(*) as count")
            ->groupBy('broker')->orderByDesc('count')->get();

        // Top pairs
        $topPairs = TradeHistory::selectRaw("currency_pair, COUNT(*) as count, SUM(profit_loss) as pnl")
            ->groupBy('currency_pair')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

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
        $userGrowthLabels = $userGrowthRaw->keys()->map(fn($d) => date('d M', strtotime($d)))->values()->all();
        $userGrowthValues = $userGrowthRaw->values()->all();

        // Daily trades chart (last 7 days)
        $dailyTradesRaw = TradeHistory::selectRaw("DATE(close_date) as date, COUNT(*) as count")
            ->where('close_date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        $dailyTradesLabels = $dailyTradesRaw->keys()->map(fn($d) => date('d M', strtotime($d)))->values()->all();
        $dailyTradesValues = $dailyTradesRaw->values()->all();

        // Daily P&L chart (last 7 days)
        $dailyPnlRaw = TradeHistory::selectRaw("DATE(close_date) as date, SUM(profit_loss) as pnl")
            ->where('close_date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('pnl', 'date');
        $dailyPnlLabels = $dailyPnlRaw->keys()->map(fn($d) => date('d M', strtotime($d)))->values()->all();
        $dailyPnlValues = $dailyPnlRaw->values()->all();

        // Recent trades (last 20)
        $recentTrades = TradeHistory::with('user', 'tradingAccount')
            ->orderByDesc('close_date')
            ->limit(20)
            ->get();

        return view('dashboard.index', compact(
            'totalUsers', 'activeToday', 'newUsersToday', 'totalTrades',
            'winRate', 'totalPnl', 'totalLots', 'totalJournals', 'totalAccounts',
            'userGrowth', 'tradeGrowth',
            'cpu', 'ram', 'disk',
            'userGrowthLabels', 'userGrowthValues',
            'dailyTradesLabels', 'dailyTradesValues',
            'dailyPnlLabels', 'dailyPnlValues',
            'accountsByBroker', 'topPairs', 'recentTrades'
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
        $registrationLabels = $registrationRaw->keys()->map(fn($d) => date('d M', strtotime($d)))->values()->all();
        $registrationValues = $registrationRaw->values()->all();

        // Active users (traded in period)
        $dau = TradeHistory::where('close_date', '>=', now()->startOfDay())
            ->distinct('user_id')->count('user_id');
        $wau = TradeHistory::where('close_date', '>=', now()->subDays(7))
            ->distinct('user_id')->count('user_id');
        $mau = TradeHistory::where('close_date', '>=', now()->subDays(30))
            ->distinct('user_id')->count('user_id');

        // User list with trade stats
        $users = User::withCount('tradeHistories as total_trades')
            ->withSum('tradeHistories as total_pnl', 'profit_loss')
            ->withCount('tradingAccounts as account_count')
            ->orderByDesc('total_pnl')
            ->paginate(20);

        // Top traders
        $topTraders = User::withCount('tradeHistories as total_trades')
            ->withSum('tradeHistories as total_pnl', 'profit_loss')
            ->having('total_trades', '>', 0)
            ->orderByDesc('total_pnl')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $total = $user->tradeHistories()->count();
                $wins = $user->tradeHistories()->where('result', 'win')->count();
                $user->win_rate = $total > 0 ? round(($wins / $total) * 100, 1) : 0;
                return $user;
            });

        return view('dashboard.users', compact(
            'registrationLabels', 'registrationValues',
            'dau', 'wau', 'mau', 'users', 'topTraders'
        ));
    }

    public function portfolio()
    {
        $users = User::withCount('tradeHistories as total_trades')
            ->withSum('tradeHistories as total_pnl', 'profit_loss')
            ->withCount('tradingAccounts as account_count')
            ->having('total_trades', '>', 0)
            ->get()
            ->map(function ($user) {
                $total = $user->tradeHistories()->count();
                $wins = $user->tradeHistories()->where('result', 'win')->count();
                $user->win_rate = $total > 0 ? round(($wins / $total) * 100, 1) : 0;
                $user->avg_pnl = $total > 0
                    ? round($user->tradeHistories()->avg('profit_loss'), 2)
                    : 0;
                return $user;
            })
            ->sortByDesc('total_pnl');

        // Top 10 by P&L
        $topPnl = $users->take(10);
        $topPnlLabels = $topPnl->pluck('name')->values()->all();
        $topPnlValues = $topPnl->pluck('total_pnl')->values()->all();
        $topPnlColors = $topPnl->map(fn($u) => $u->total_pnl >= 0 ? 'rgba(16,185,129,0.7)' : 'rgba(239,68,68,0.7)')->values()->all();

        // Per-pair P&L breakdown
        $pairPnl = TradeHistory::selectRaw("currency_pair, SUM(profit_loss) as pnl, COUNT(*) as trades")
            ->groupBy('currency_pair')
            ->orderByDesc('pnl')
            ->limit(15)
            ->get();

        return view('dashboard.portfolio', compact(
            'users', 'topPnl', 'topPnlLabels', 'topPnlValues', 'topPnlColors', 'pairPnl'
        ));
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

        $historicalLabels = $historical->pluck('snapshot_date')->map(fn($d) => date('d M', strtotime($d)))->all();
        $historicalCpu = $historical->pluck('cpu_percent')->all();
        $historicalRam = $historical->pluck('ram_percent')->all();

        // Quick server info
        $phpVersion = PHP_VERSION;
        $mysqlVersion = $this->getMysqlVersion();
        $nginxVersion = $this->getNginxVersion();
        $osInfo = php_uname('s') . ' ' . php_uname('r');
        $uptime = $this->getUptime();
        $diskFree = $this->getDiskFree();

        return view('dashboard.server', compact(
            'cpu', 'ram', 'disk', 'diskUsedGb', 'diskTotalGb',
            'historical', 'historicalLabels', 'historicalCpu', 'historicalRam',
            'phpVersion', 'mysqlVersion', 'nginxVersion', 'osInfo', 'uptime', 'diskFree'
        ));
    }

    public function logs(Request $request)
    {
        // Monitor both jurnal-trading and journal-trading-connect logs
        $logPaths = [
            '/home/ubuntu/jurnal-trading/storage/logs/laravel.log' => 'Jurnal Trading',
            '/home/ubuntu/journal-trading-connect/storage/logs/laravel.log' => 'JTC Connect',
        ];

        $allEntries = [];
        $search = $request->get('search', '');
        $level = $request->get('level', '');
        $source = $request->get('source', '');

        foreach ($logPaths as $logPath => $sourceName) {
            if ($source && $source !== $sourceName) continue;
            if (!file_exists($logPath)) continue;

            $lines = array_slice(file($logPath), -3000);
            $current = null;

            foreach ($lines as $line) {
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+):/', $line, $m)) {
                    if ($current) {
                        $allEntries[] = $current;
                    }
                    $current = [
                        'date' => $m[1],
                        'level' => strtoupper($m[3]),
                        'message' => trim(str_replace($m[0], '', $line)),
                        'stack' => '',
                        'source' => $sourceName,
                    ];
                } elseif ($current) {
                    $current['stack'] .= $line;
                }
            }
            if ($current) {
                $allEntries[] = $current;
            }
        }

        // Filter
        $filtered = collect($allEntries)->filter(function ($entry) use ($level, $search) {
            if ($level && $entry['level'] !== strtoupper($level)) return false;
            if ($search && stripos($entry['message'] . $entry['stack'], $search) === false) return false;
            return true;
        })->sortByDesc('date')->values()->all();

        // Paginate
        $perPage = 20;
        $page = $request->get('page', 1);
        $total = count($filtered);
        $logs = array_slice($filtered, ($page - 1) * $perPage, $perPage);

        $errorCount = collect($filtered)->where('level', 'ERROR')->count();
        $warningCount = collect($filtered)->where('level', 'WARNING')->count();
        $criticalCount = collect($filtered)->where('level', 'CRITICAL')->count();

        $sources = array_keys($logPaths);

        return view('dashboard.logs', compact(
            'logs', 'total', 'perPage', 'page',
            'errorCount', 'warningCount', 'criticalCount', 'sources'
        ));
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
            // Prometheus might not be running
        }
        return 0;
    }

    private function getMysqlVersion(): string
    {
        try {
            $result = shell_exec("mysql -V 2>/dev/null");
            if ($result && preg_match('/Ver\s+([\d.]+)/', $result, $m)) {
                return $m[1];
            }
        } catch (\Throwable $e) {}
        return 'N/A';
    }

    private function getNginxVersion(): string
    {
        try {
            $result = shell_exec("nginx -v 2>&1");
            if ($result && preg_match('/nginx\/([\d.]+)/', $result, $m)) {
                return $m[1];
            }
        } catch (\Throwable $e) {}
        return 'N/A';
    }

    private function getUptime(): string
    {
        try {
            $uptime = shell_exec("uptime -p 2>/dev/null");
            return $uptime ? trim($uptime) : 'N/A';
        } catch (\Throwable $e) {}
        return 'N/A';
    }

    private function getDiskFree(): string
    {
        try {
            $df = shell_exec("df -h / 2>/dev/null");
            if ($df && preg_match('/\d+\%\s*$/', $df, $m)) {
                return $m[0];
            }
        } catch (\Throwable $e) {}
        return 'N/A';
    }
}
