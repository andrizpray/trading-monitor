<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class MetricsController extends Controller
{
    public function index()
    {
        $output = [];

        // Total users
        $totalUsers = User::count();
        $output[] = "# HELP trading_monitor_total_users Total registered users";
        $output[] = "# TYPE trading_monitor_total_users gauge";
        $output[] = "trading_monitor_total_users {$totalUsers}";
        $output[] = "";

        // Active users (last 24h)
        $activeUsers = User::whereHas('journalEntries', function ($q) {
            $q->where('entry_date', '>=', now()->subDay());
        })->count();
        $output[] = "# HELP trading_monitor_active_users Users active in last 24h";
        $output[] = "# TYPE trading_monitor_active_users gauge";
        $output[] = "trading_monitor_active_users {$activeUsers}";
        $output[] = "";

        // Total journal entries
        $totalEntries = JournalEntry::count();
        $output[] = "# HELP trading_monitor_total_entries Total journal entries";
        $output[] = "# TYPE trading_monitor_total_entries gauge";
        $output[] = "trading_monitor_total_entries {$totalEntries}";
        $output[] = "";

        // Error count from log file
        $logPath = base_path('../jurnal-trading/storage/logs/laravel.log');
        $errorCount = 0;
        if (file_exists($logPath)) {
            $lines = array_slice(file($logPath), -5000);
            $errorCount = count(array_filter($lines, fn($l) => str_contains($l, 'local.ERROR')));
        }
        $output[] = "# HELP trading_monitor_errors_total Total error log entries (last 5000 lines)";
        $output[] = "# TYPE trading_monitor_errors_total counter";
        $output[] = "trading_monitor_errors_total {$errorCount}";

        return response(implode("\n", $output), 200, ['Content-Type' => 'text/plain']);
    }
}
