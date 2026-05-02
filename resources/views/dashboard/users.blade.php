@extends('layouts.app')
@section('title', 'Users — Trading Monitor')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">User Analytics</h1>
        <p class="text-sm text-slate-500 mt-1">Registrasi, aktivitas, dan performa user</p>
    </div>

    <!-- Active Users Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 text-center border-t-4 border-t-blue-500">
            <p class="text-sm text-slate-500">DAU</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($dau) }}</p>
            <p class="text-xs text-slate-400 mt-1">Daily Active Users</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 text-center border-t-4 border-t-violet-500">
            <p class="text-sm text-slate-500">WAU</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($wau) }}</p>
            <p class="text-xs text-slate-400 mt-1">Weekly Active Users</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 text-center border-t-4 border-t-cyan-500">
            <p class="text-sm text-slate-500">MAU</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($mau) }}</p>
            <p class="text-xs text-slate-400 mt-1">Monthly Active Users</p>
        </div>
    </div>

    <!-- Registration Chart -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">📈 Registrasi User (30 hari terakhir)</h3>
        <div style="height: 250px;">
            <canvas id="registrationChart"></canvas>
        </div>
    </div>

    <!-- Top Traders -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">🏆 Top Traders</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 uppercase tracking-wide border-b border-slate-200">
                        <th class="pb-3 pr-4">#</th>
                        <th class="pb-3 pr-4">User</th>
                        <th class="pb-3 pr-4 text-right">Trades</th>
                        <th class="pb-3 pr-4 text-right">P&L</th>
                        <th class="pb-3 text-right">Win Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topTraders as $i => $user)
                    <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition">
                        <td class="py-3 pr-4 text-slate-400">{{ $i + 1 }}</td>
                        <td class="py-3 pr-4 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="py-3 pr-4 text-right text-slate-600">{{ number_format($user->total_trades) }}</td>
                        <td class="py-3 pr-4 text-right font-medium {{ $user->total_pnl >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $user->total_pnl >= 0 ? '+' : '' }}{{ number_format($user->total_pnl, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-right">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $user->win_rate >= 60 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->win_rate }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-slate-400">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Users -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">📋 Semua User</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 uppercase tracking-wide border-b-2 border-slate-200">
                        <th class="pb-3 pr-4">Name</th>
                        <th class="pb-3 pr-4">Email</th>
                        <th class="pb-3 pr-4">Bergabung</th>
                        <th class="pb-3 pr-4 text-right">Trades</th>
                        <th class="pb-3 pr-4 text-right">P&L</th>
                        <th class="pb-3 text-right">Terakhir Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition">
                        <td class="py-3 pr-4 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="py-3 pr-4 text-slate-500">{{ $user->email }}</td>
                        <td class="py-3 pr-4 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-3 pr-4 text-right text-slate-600">{{ number_format($user->total_trades) }}</td>
                        <td class="py-3 pr-4 text-right font-medium {{ ($user->total_pnl ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ ($user->total_pnl ?? 0) >= 0 ? '+' : '' }}{{ number_format($user->total_pnl ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-right text-slate-500 text-xs">
                            {{ $user->last_active ? $user->last_active->diffForHumans() : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-slate-400">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="flex items-center justify-center gap-1 mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#94A3B8';

    @if($registrationData->count() > 0)
    new Chart(document.getElementById('registrationChart'), {
        type: 'line',
        data: {
            labels: @json($registrationData->keys()->map(fn($d) => date('d M', strtotime($d)))),
            datasets: [{
                label: 'User Baru',
                data: @json($registrationData->values()),
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139,92,246,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 5,
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
    @endif
});
</script>
@endsection
