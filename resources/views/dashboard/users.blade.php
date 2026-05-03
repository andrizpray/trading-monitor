@extends('layouts.app')
@section('title', 'Users — Trading Monitor')

@section('content')
<div class="w-full max-w-7xl mx-auto">

    <div class="mb-4 sm:mb-6">
        <h1 class="text-lg sm:text-2xl font-bold text-slate-900">User Analytics</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 sm:mt-1">Registrasi, aktivitas, dan performa user</p>
    </div>

    <!-- Active Users Cards -->
    <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-4 sm:mb-6">
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 text-center border-t-4 border-t-blue-500">
            <p class="text-[10px] sm:text-sm text-slate-500">DAU</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($dau) }}</p>
            <p class="text-[9px] sm:text-xs text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Daily Active Users</p>
        </div>
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 text-center border-t-4 border-t-violet-500">
            <p class="text-[10px] sm:text-sm text-slate-500">WAU</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($wau) }}</p>
            <p class="text-[9px] sm:text-xs text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Weekly Active Users</p>
        </div>
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 text-center border-t-4 border-t-cyan-500">
            <p class="text-[10px] sm:text-sm text-slate-500">MAU</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-900 mt-0.5 sm:mt-1">{{ number_format($mau) }}</p>
            <p class="text-[9px] sm:text-xs text-slate-400 mt-0.5 sm:mt-1 hidden sm:block">Monthly Active Users</p>
        </div>
    </div>

    <!-- Registration Chart -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 mb-4 sm:mb-6">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">📈 Registrasi User (30 hari)</h3>
        <div class="h-[180px] sm:h-[250px]">
            <canvas id="registrationChart"></canvas>
        </div>
    </div>

    <!-- Top Traders -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 mb-4 sm:mb-6">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-3 sm:mb-4">🏆 Top Traders</h3>
        <div class="overflow-x-auto -mx-3 sm:mx-0">
            <div class="min-w-[360px] sm:min-w-0 px-3 sm:px-0">
            <table class="w-full text-xs sm:text-sm">
                <thead>
                    <tr class="text-left text-[10px] sm:text-xs text-slate-500 uppercase tracking-wide border-b border-slate-200">
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4">#</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4">User</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">Trades</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">P&L</th>
                        <th class="pb-2 sm:pb-3 text-right">Win Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topTraders as $i => $user)
                    <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition">
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-slate-400">{{ $i + 1 }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 font-medium text-slate-800 truncate max-w-[100px] sm:max-w-none">{{ $user->name }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right text-slate-600">{{ number_format($user->total_trades) }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right font-medium {{ $user->total_pnl >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $user->total_pnl >= 0 ? '+' : '' }}{{ number_format($user->total_pnl, 0, ',', '.') }}
                        </td>
                        <td class="py-2 sm:py-3 text-right">
                            <span class="inline-block px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium {{ $user->win_rate >= 60 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->win_rate }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 sm:py-8 text-center text-slate-400 text-xs sm:text-sm">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- All Users -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-3 sm:mb-4">📋 Semua User</h3>
        <div class="overflow-x-auto -mx-3 sm:mx-0">
            <div class="min-w-[520px] sm:min-w-0 px-3 sm:px-0">
            <table class="w-full text-xs sm:text-sm">
                <thead>
                    <tr class="text-left text-[10px] sm:text-xs text-slate-500 uppercase tracking-wide border-b-2 border-slate-200">
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4">Name</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4">Email</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4">Gabung</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">Trades</th>
                        <th class="pb-2 sm:pb-3 pr-3 sm:pr-4 text-right">P&L</th>
                        <th class="pb-2 sm:pb-3 text-right">Accounts</th>
                        <th class="pb-2 sm:pb-3 text-right">Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition">
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 font-medium text-slate-800 truncate max-w-[100px] sm:max-w-none">{{ $user->name }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-slate-500 truncate max-w-[130px] sm:max-w-none">{{ $user->email }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-slate-500 whitespace-nowrap">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right text-slate-600">{{ number_format($user->total_trades) }}</td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right font-medium {{ ($user->total_pnl ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ ($user->total_pnl ?? 0) >= 0 ? '+' : '' }}{{ number_format($user->total_pnl ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-2 sm:py-3 text-right text-slate-500 text-[10px] sm:text-xs whitespace-nowrap">
                            {{ $user->last_active ? \Carbon\Carbon::parse($user->last_active)->diffForHumans() : '-' }}
                        </td>
                        <td class="py-2 sm:py-3 pr-3 sm:pr-4 text-right text-slate-500 text-[10px] sm:text-xs">{{ $user->account_count ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-6 sm:py-8 text-center text-slate-400 text-xs sm:text-sm">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="flex items-center justify-center gap-1 mt-3 sm:mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';
    Chart.defaults.font.size = window.innerWidth < 640 ? 10 : 12;
    Chart.defaults.color = '#94A3B8';

    @if(count($registrationLabels) > 0)
    new Chart(document.getElementById('registrationChart'), {
        type: 'line',
        data: {
            labels: @json($registrationLabels),
            datasets: [{
                label: 'User Baru',
                data: @json($registrationValues),
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139,92,246,0.1)',
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
                y: { beginAtZero: true, grid: { color: '#F1F5F9' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: window.innerWidth < 640 ? 5 : 10 } }
            }
        }
    });
    @endif
});
</script>
@endsection
