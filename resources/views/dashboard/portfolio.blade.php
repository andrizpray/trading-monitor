@extends('layouts.app')
@section('title', 'Portfolio Growth — Trading Monitor')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Portfolio Growth</h1>
        <p class="text-sm text-slate-500 mt-1">Perkembangan portofolio setiap user</p>
    </div>

    <!-- Top 10 Growth Chart -->
    @if($topGrowth->count() > 0)
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">🏆 Top 10 Growth</h3>
        <div style="height: 300px;">
            <canvas id="growthChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Portfolio Table -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">📋 Detail Portfolio</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 uppercase tracking-wide border-b-2 border-slate-200">
                        <th class="pb-3 pr-4">#</th>
                        <th class="pb-3 pr-4">User</th>
                        <th class="pb-3 pr-4 text-right">Modal Awal</th>
                        <th class="pb-3 pr-4 text-right">P&L</th>
                        <th class="pb-3 pr-4 text-right">Growth</th>
                        <th class="pb-3 pr-4 text-right">Win Rate</th>
                        <th class="pb-3 text-right">Trades</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition">
                        <td class="py-3 pr-4 text-slate-400">{{ $i + 1 }}</td>
                        <td class="py-3 pr-4 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="py-3 pr-4 text-right text-slate-600">
                            {{ $user->default_capital ? 'Rp ' . number_format($user->default_capital, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 pr-4 text-right font-medium {{ $user->total_pnl >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $user->total_pnl >= 0 ? '+' : '' }}{{ number_format($user->total_pnl, 0, ',', '.') }}
                        </td>
                        <td class="py-3 pr-4 text-right">
                            @if($user->growth_percent !== null)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->growth_percent >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $user->growth_percent >= 0 ? '+' : '' }}{{ $user->growth_percent }}%
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-right">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $user->win_rate >= 60 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->win_rate }}%
                            </span>
                        </td>
                        <td class="py-3 text-right text-slate-600">{{ number_format($user->total_trades) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-slate-400">Belum ada data trading</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#94A3B8';

    @if($topGrowth->count() > 0)
    new Chart(document.getElementById('growthChart'), {
        type: 'bar',
        data: {
            labels: @json($topGrowth->pluck('name')->values()),
            datasets: [{
                label: 'Growth %',
                data: @json($topGrowth->pluck('growth_percent')->values()),
                backgroundColor: @json($topGrowth->map(fn($u) => $u->growth_percent >= 0 ? 'rgba(16,185,129,0.7)' : 'rgba(239,68,68,0.7)')->values()),
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
                y: { grid: { display: false }, border: { display: false } }
            }
        }
    });
    @endif
});
</script>
@endsection
