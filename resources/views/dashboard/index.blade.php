@extends('layouts.app')
@section('title', 'Dashboard — Trading Monitor')

@section('content')
<div class="w-full max-w-7xl mx-auto">

    <!-- Page header -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-lg sm:text-2xl font-bold text-slate-900">Dashboard Overview</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 sm:mt-1">Ringkasan data trading & server</p>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-4 mb-4 sm:mb-6">

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
                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $winRate }}%"></div>
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

        <!-- Server Health mini -->
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

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">

        <!-- User Growth Chart -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">📈 User Growth (30 hari)</h3>
            <div class="h-[180px] sm:h-[280px]">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Daily Trades Chart -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">📊 Daily Trades (7 hari)</h3>
            <div class="h-[180px] sm:h-[280px]">
                <canvas id="dailyTradesChart"></canvas>
            </div>
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
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: window.innerWidth < 640 ? 0 : 2,
                pointHoverRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
});
</script>
@endsection
