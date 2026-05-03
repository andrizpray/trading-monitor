@extends('layouts.app')
@section('title', 'Portfolio — Trading Monitor')

@section('content')
<div class="w-full max-w-7xl mx-auto">

    <div class="mb-4 sm:mb-6">
        <h1 class="text-lg sm:text-2xl font-bold text-slate-900">
            <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-cyan-100 text-cyan-700 rounded mr-2 align-middle">CONNECT</span>
            Portfolio & Analytics
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 sm:mt-1">Performa user, pair, dan distribusi trading</p>
    </div>

    <!-- Top 10 P&L Chart -->
    @if($topPnl->count() > 0)
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 mb-4 sm:mb-6">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">🏆 Top 10 by P&L</h3>
        <div class="h-[200px] sm:h-[300px]">
            <canvas id="pnlChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Per-Pair P&L Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-3 sm:mb-4">🏷️ P&L per Pair</h3>
            <div class="space-y-1.5">
                @forelse($pairPnl as $pair)
                <div class="flex items-center justify-between py-1 border-b border-slate-50 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $pair->currency_pair }}</span>
                        <span class="text-[10px] text-slate-400">{{ number_format($pair->trades) }} trades</span>
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

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
            <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-3 sm:mb-4">📊 Quick Stats</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-[10px] sm:text-xs text-slate-400 mb-1">Total P&L All Users</p>
                    @php $totalAllPnl = $users->sum('total_pnl'); @endphp
                    <p class="text-lg sm:text-xl font-bold {{ $totalAllPnl >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $totalAllPnl >= 0 ? '+' : '' }}{{ number_format($totalAllPnl, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] sm:text-xs text-slate-400 mb-1">Best Trader</p>
                    @php $best = $users->first(); @endphp
                    <p class="text-sm font-semibold text-slate-800">{{ $best ? $best->name : '-' }}</p>
                    <p class="text-[10px] text-emerald-600">{{ $best ? '+' . number_format($best->total_pnl, 0, ',', '.') : '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] sm:text-xs text-slate-400 mb-1">Most Active</p>
                    @php $mostActive = $users->sortByDesc('total_trades')->first(); @endphp
                    <p class="text-sm font-semibold text-slate-800">{{ $mostActive ? $mostActive->name : '-' }}</p>
                    <p class="text-[10px] text-slate-500">{{ $mostActive ? number_format($mostActive->total_trades) . ' trades' : '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] sm:text-xs text-slate-400 mb-1">Highest Win Rate</p>
                    @php $bestWr = $users->where('total_trades', '>', 5)->sortByDesc('win_rate')->first(); @endphp
                    <p class="text-sm font-semibold text-slate-800">{{ $bestWr ? $bestWr->name : '-' }}</p>
                    <p class="text-[10px] text-slate-500">{{ $bestWr ? $bestWr->win_rate . '% WR' : '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Table -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-3 sm:mb-4">📋 All Users Portfolio</h3>
        <div class="overflow-x-auto -mx-3 sm:mx-0">
            <div class="min-w-[480px] sm:min-w-0 px-3 sm:px-0">
            <table class="w-full text-xs sm:text-sm">
                <thead>
                    <tr class="text-left text-[10px] sm:text-xs text-slate-500 uppercase tracking-wide border-b-2 border-slate-200">
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4">#</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4">User</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">Accounts</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">P&L</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">Avg P&L</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">Win Rate</th>
                        <th class="pb-2 sm:pb-3 text-right">Trades</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition">
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-slate-400">{{ $i + 1 }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 font-medium text-slate-800 truncate max-w-[80px] sm:max-w-none">{{ $user->name }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right text-slate-600">{{ $user->account_count ?? 0 }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right font-medium whitespace-nowrap {{ $user->total_pnl >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $user->total_pnl >= 0 ? '+' : '' }}{{ number_format($user->total_pnl, 0, ',', '.') }}
                        </td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right whitespace-nowrap {{ ($user->avg_pnl ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ ($user->avg_pnl ?? 0) >= 0 ? '+' : '' }}{{ number_format($user->avg_pnl ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right">
                            <span class="inline-block px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium {{ $user->win_rate >= 60 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->win_rate }}%
                            </span>
                        </td>
                        <td class="py-2 sm:py-3 text-right text-slate-600">{{ number_format($user->total_trades) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-6 sm:py-8 text-center text-slate-400 text-xs sm:text-sm">Belum ada data trading</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';
    Chart.defaults.font.size = window.innerWidth < 640 ? 10 : 12;
    Chart.defaults.color = '#94A3B8';

    @if($topPnl->count() > 0)
    new Chart(document.getElementById('pnlChart'), {
        type: 'bar',
        data: {
            labels: @json($topPnlLabels),
            datasets: [{
                label: 'P&L',
                data: @json($topPnlValues),
                backgroundColor: @json($topPnlColors),
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#F1F5F9' }, border: { display: false } },
                y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: window.innerWidth < 640 ? 10 : 12 } } }
            }
        }
    });
    @endif
});
</script>
@endsection
