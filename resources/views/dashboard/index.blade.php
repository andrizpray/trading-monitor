@extends('layouts.app')
@section('title', 'Dashboard — Trading Monitor')

@section('content')
<div class="w-full max-w-7xl mx-auto">

    <!-- Page header -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-lg sm:text-2xl font-bold text-slate-900">
            <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-cyan-100 text-cyan-700 rounded mr-2 align-middle">CONNECT</span>
            Dashboard Overview
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 sm:mt-1">Monitoring Journal Trading Connect — port 8081</p>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4 mb-4 sm:mb-6">

        <!-- Total Users -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Total Users</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-50 rounded-lg flex items-center justify-center text-sm sm:text-lg shrink-0">👥</div>
            </div>
            <p class="text-[10px] sm:text-xs mt-1.5 sm:mt-2 {{ $userGrowth >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ $userGrowth >= 0 ? '↑' : '↓' }} {{ abs($userGrowth) }}% dari kemarin
            </p>
        </div>

        <!-- Active Today -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 border-l-cyan-500">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Active Today</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($activeToday) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-cyan-50 rounded-lg flex items-center justify-center text-sm sm:text-lg shrink-0">🟢</div>
            </div>
            <p class="text-[10px] sm:text-xs mt-1.5 sm:mt-2 text-slate-400">{{ number_format($newUsersToday) }} user baru</p>
        </div>

        <!-- Total Trades -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 border-l-violet-500">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Total Trades</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($totalTrades) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-violet-50 rounded-lg flex items-center justify-center text-sm sm:text-lg shrink-0">📊</div>
            </div>
            <p class="text-[10px] sm:text-xs mt-1.5 sm:mt-2 {{ $tradeGrowth >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ $tradeGrowth >= 0 ? '↑' : '↓' }} {{ abs($tradeGrowth) }}% dari kemarin
            </p>
        </div>

        <!-- Win Rate -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Win Rate</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ $winRate }}%</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-amber-50 rounded-lg flex items-center justify-center text-sm sm:text-lg shrink-0">📈</div>
            </div>
            <div class="mt-1.5 sm:mt-2 w-full bg-slate-100 rounded-full h-1.5">
                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min($winRate, 100) }}%"></div>
            </div>
        </div>

        <!-- Total P&L -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 {{ $totalPnl >= 0 ? 'border-l-emerald-500' : 'border-l-red-500' }}">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Total P&L</p>
                    <p class="text-xl sm:text-2xl font-bold mt-0.5 sm:mt-1 {{ $totalPnl >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $totalPnl >= 0 ? '+' : '' }}{{ number_format($totalPnl, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 {{ $totalPnl >= 0 ? 'bg-emerald-50' : 'bg-red-50' }} rounded-lg flex items-center justify-center text-sm sm:text-lg shrink-0">
                    {{ $totalPnl >= 0 ? '💰' : '📉' }}
                </div>
            </div>
        </div>

        <!-- Total Lots -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 border-l-pink-500">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Total Lots</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($totalLots, 1) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-pink-50 rounded-lg flex items-center justify-center text-sm sm:text-lg shrink-0">📐</div>
            </div>
        </div>

        <!-- Accounts -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 border-l-teal-500">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Trading Accounts</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($totalAccounts) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-teal-50 rounded-lg flex items-center justify-center text-sm sm:text-lg shrink-0">🏦</div>
            </div>
            @foreach($accountsByBroker->take(3) as $broker)
                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">{{ $broker->broker }}: {{ $broker->count }}</p>
            @endforeach
        </div>

        <!-- Server Health -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 border-l-4 border-l-indigo-500">
            <p class="text-xs sm:text-sm text-slate-500 font-medium mb-1.5 sm:mb-2">🖥️ Server Health</p>
            <div class="space-y-1 sm:space-y-1.5">
                <div class="flex items-center justify-between text-[10px] sm:text-xs">
                    <span class="text-slate-600">CPU</span>
                    <span class="font-medium {{ $cpu > 80 ? 'text-red-500' : ($cpu > 60 ? 'text-amber-500' : 'text-slate-700') }}">{{ $cpu }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1 sm:h-1.5">
                    <div class="h-1 sm:h-1.5 rounded-full {{ $cpu > 80 ? 'bg-red-500' : ($cpu > 60 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ min($cpu, 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] sm:text-xs">
                    <span class="text-slate-600">RAM</span>
                    <span class="font-medium {{ $ram > 80 ? 'text-red-500' : ($ram > 60 ? 'text-amber-500' : 'text-slate-700') }}">{{ $ram }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1 sm:h-1.5">
                    <div class="h-1 sm:h-1.5 rounded-full {{ $ram > 80 ? 'bg-red-500' : ($ram > 60 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ min($ram, 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] sm:text-xs">
                    <span class="text-slate-600">Disk</span>
                    <span class="font-medium {{ $disk > 90 ? 'text-red-500' : ($disk > 60 ? 'text-amber-500' : 'text-slate-700') }}">{{ $disk }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1 sm:h-1.5">
                    <div class="h-1 sm:h-1.5 rounded-full {{ $disk > 90 ? 'bg-red-500' : ($disk > 60 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ min($disk, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
        <!-- User Growth -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">📈 User Growth (30 hari)</h3>
            <div class="h-[180px] sm:h-[280px]">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Daily Trades -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">📊 Daily Trades (7 hari)</h3>
            <div class="h-[180px] sm:h-[280px]">
                <canvas id="dailyTradesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
        <!-- Daily P&L -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">💵 Daily P&L (7 hari)</h3>
            <div class="h-[180px] sm:h-[280px]">
                <canvas id="dailyPnlChart"></canvas>
            </div>
        </div>

        <!-- Top Pairs -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">🏷️ Top Currency Pairs</h3>
            <div class="space-y-2">
                @forelse($topPairs as $pair)
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $pair->currency_pair }}</span>
                        <span class="text-[10px] text-slate-400">{{ number_format($pair->count) }} trades</span>
                    </div>
                    <span class="text-xs font-semibold {{ $pair->pnl >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $pair->pnl >= 0 ? '+' : '' }}{{ number_format($pair->pnl, 0, ',', '.') }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Trades -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-3">🕐 Recent Trades (20 terakhir)</h3>
        <div class="overflow-x-auto -mx-3 sm:mx-0">
            <table class="w-full text-[10px] sm:text-xs min-w-[600px]">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-2 text-slate-500 font-medium">Tanggal</th>
                        <th class="text-left py-2 px-2 text-slate-500 font-medium">User</th>
                        <th class="text-left py-2 px-2 text-slate-500 font-medium">Pair</th>
                        <th class="text-left py-2 px-2 text-slate-500 font-medium">Type</th>
                        <th class="text-left py-2 px-2 text-slate-500 font-medium">Lots</th>
                        <th class="text-right py-2 px-2 text-slate-500 font-medium">P&L</th>
                        <th class="text-center py-2 px-2 text-slate-500 font-medium">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTrades as $trade)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="py-1.5 px-2 text-slate-600">{{ $trade->close_date ? $trade->close_date->format('d M') : '-' }}</td>
                        <td class="py-1.5 px-2 text-slate-700 font-medium truncate max-w-[80px]">{{ $trade->user->name ?? '-' }}</td>
                        <td class="py-1.5 px-2 font-semibold text-slate-700">{{ $trade->currency_pair }}</td>
                        <td class="py-1.5 px-2">
                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold {{ $trade->trade_type === 'buy' || str_starts_with($trade->trade_type, 'buy') ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }}">
                                {{ strtoupper($trade->trade_type) }}
                            </span>
                        </td>
                        <td class="py-1.5 px-2 text-slate-600">{{ number_format($trade->lot_size, 2) }}</td>
                        <td class="py-1.5 px-2 text-right font-semibold {{ $trade->profit_loss >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 0, ',', '.') }}
                        </td>
                        <td class="py-1.5 px-2 text-center">
                            @if($trade->result === 'win')
                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600">WIN</span>
                            @elseif($trade->result === 'loss')
                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600">LOSS</span>
                            @else
                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-50 text-slate-500">{{ strtoupper($trade->result ?? '-') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-6 text-center text-slate-400">Belum ada trade</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';
    Chart.defaults.font.size = window.innerWidth < 640 ? 10 : 12;
    Chart.defaults.color = '#94A3B8';

    @if(count($userGrowthLabels) > 0)
    new Chart(document.getElementById('userGrowthChart'), {
        type: 'line',
        data: {
            labels: @json($userGrowthLabels),
            datasets: [{
                label: 'Total Users',
                data: @json($userGrowthValues),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                fill: true, tension: 0.4, borderWidth: 2,
                pointRadius: window.innerWidth < 640 ? 0 : 2, pointHoverRadius: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: false, grid: { color: '#F1F5F9' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: window.innerWidth < 640 ? 5 : 10 } }
            }
        }
    });
    @else
    new Chart(document.getElementById('userGrowthChart'), {
        type: 'line',
        data: { labels: ['Belum ada data'], datasets: [{ data: [0] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    @endif

    @if(count($dailyTradesLabels) > 0)
    new Chart(document.getElementById('dailyTradesChart'), {
        type: 'bar',
        data: {
            labels: @json($dailyTradesLabels),
            datasets: [{
                label: 'Trades',
                data: @json($dailyTradesValues),
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F1F5F9' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
    @else
    new Chart(document.getElementById('dailyTradesChart'), {
        type: 'bar',
        data: { labels: ['Belum ada data'], datasets: [{ data: [0] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    @endif

    @if(count($dailyPnlLabels) > 0)
    new Chart(document.getElementById('dailyPnlChart'), {
        type: 'bar',
        data: {
            labels: @json($dailyPnlLabels),
            datasets: [{
                label: 'P&L',
                data: @json($dailyPnlValues),
                backgroundColor: @json($dailyPnlColors),
                borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#F1F5F9' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
    @else
    new Chart(document.getElementById('dailyPnlChart'), {
        type: 'bar',
        data: { labels: ['Belum ada data'], datasets: [{ data: [0] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    @endif
});
</script>
@endsection
