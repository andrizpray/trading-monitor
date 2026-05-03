@extends('layouts.app')
@section('title', 'Server Monitoring — Trading Monitor')

@section('content')
<div class="w-full max-w-7xl mx-auto">

    <div class="mb-4 sm:mb-6">
        <h1 class="text-lg sm:text-2xl font-bold text-slate-900">Server Monitoring</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 sm:mt-1">Metrik server real-time dari Prometheus</p>
    </div>

    <!-- Alerts -->
    @if($cpu > 80 || $ram > 80 || $disk > 90)
    <div class="bg-red-50 border border-red-200 rounded-lg sm:rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 flex items-start gap-2 sm:gap-3">
        <span class="text-red-500 text-sm sm:text-lg shrink-0">⚠️</span>
        <div class="text-xs sm:text-sm text-red-700">
            <p class="font-semibold">Peringatan Server!</p>
            <ul class="mt-1 list-disc list-inside space-y-0.5">
                @if($cpu > 80)<li>CPU tinggi: <strong>{{ $cpu }}%</strong></li>@endif
                @if($ram > 80)<li>RAM tinggi: <strong>{{ $ram }}%</strong></li>@endif
                @if($disk > 90)<li>Disk kritis: <strong>{{ $disk }}%</strong></li>@endif
            </ul>
        </div>
    </div>
    @endif

    <!-- Gauge Cards -->
    <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-4 sm:mb-6">

        <!-- CPU -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-2 sm:p-6 text-center">
            <div class="relative w-16 h-16 sm:w-28 sm:h-28 mx-auto mb-1 sm:mb-3">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" stroke="#F1F5F9" stroke-width="8" fill="none"/>
                    <circle cx="50" cy="50" r="42"
                        stroke="{{ $cpu > 80 ? '#EF4444' : ($cpu > 60 ? '#F59E0B' : '#3B82F6') }}"
                        stroke-width="8" fill="none"
                        stroke-linecap="round"
                        stroke-dasharray="{{ 2 * pi() * 42 }}"
                        stroke-dashoffset="{{ 2 * pi() * 42 * (1 - min($cpu, 100) / 100) }}"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm sm:text-2xl font-bold text-slate-900">{{ $cpu }}%</span>
                </div>
            </div>
            <p class="text-[10px] sm:text-sm font-medium text-slate-700">CPU</p>
            <span class="inline-block mt-0.5 sm:mt-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs font-medium {{ $cpu > 80 ? 'bg-red-50 text-red-700' : ($cpu > 60 ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700') }}">
                {{ $cpu > 80 ? '🔴 High' : ($cpu > 60 ? '🟡 Med' : '🟢 OK') }}
            </span>
        </div>

        <!-- RAM -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-2 sm:p-6 text-center">
            <div class="relative w-16 h-16 sm:w-28 sm:h-28 mx-auto mb-1 sm:mb-3">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" stroke="#F1F5F9" stroke-width="8" fill="none"/>
                    <circle cx="50" cy="50" r="42"
                        stroke="{{ $ram > 80 ? '#EF4444' : ($ram > 60 ? '#F59E0B' : '#8B5CF6') }}"
                        stroke-width="8" fill="none"
                        stroke-linecap="round"
                        stroke-dasharray="{{ 2 * pi() * 42 }}"
                        stroke-dashoffset="{{ 2 * pi() * 42 * (1 - min($ram, 100) / 100) }}"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm sm:text-2xl font-bold text-slate-900">{{ $ram }}%</span>
                </div>
            </div>
            <p class="text-[10px] sm:text-sm font-medium text-slate-700">RAM</p>
            <span class="inline-block mt-0.5 sm:mt-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs font-medium {{ $ram > 80 ? 'bg-red-50 text-red-700' : ($ram > 60 ? 'bg-amber-50 text-amber-700' : 'bg-violet-50 text-violet-700') }}">
                {{ $ram > 80 ? '🔴 High' : ($ram > 60 ? '🟡 Med' : '🟢 OK') }}
            </span>
        </div>

        <!-- Disk -->
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-2 sm:p-6 text-center">
            <div class="relative w-16 h-16 sm:w-28 sm:h-28 mx-auto mb-1 sm:mb-3">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" stroke="#F1F5F9" stroke-width="8" fill="none"/>
                    <circle cx="50" cy="50" r="42"
                        stroke="{{ $disk > 90 ? '#EF4444' : ($disk > 60 ? '#F59E0B' : '#06B6D4') }}"
                        stroke-width="8" fill="none"
                        stroke-linecap="round"
                        stroke-dasharray="{{ 2 * pi() * 42 }}"
                        stroke-dashoffset="{{ 2 * pi() * 42 * (1 - min($disk, 100) / 100) }}"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm sm:text-2xl font-bold text-slate-900">{{ $disk }}%</span>
                </div>
            </div>
            <p class="text-[10px] sm:text-sm font-medium text-slate-700">Disk</p>
            <span class="inline-block mt-0.5 sm:mt-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs font-medium {{ $disk > 90 ? 'bg-red-50 text-red-700' : ($disk > 60 ? 'bg-amber-50 text-amber-700' : 'bg-cyan-50 text-cyan-700') }}">
                {{ $disk > 90 ? '🔴 Critical' : ($disk > 60 ? '🟡 Med' : '🟢 OK') }}
            </span>
        </div>
    </div>

    <!-- Disk Detail -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 mb-4 sm:mb-6">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-3">💾 Disk Detail (/)</h3>
        <div class="flex items-center gap-3 sm:gap-4 mb-2">
            <span class="text-xs sm:text-sm text-slate-600">Used: <strong>{{ number_format($diskUsedGb, 1) }} GB</strong></span>
            <span class="text-xs sm:text-sm text-slate-400">/</span>
            <span class="text-xs sm:text-sm text-slate-600">Total: <strong>{{ number_format($diskTotalGb, 1) }} GB</strong></span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2 sm:h-3">
            <div class="h-2 sm:h-3 rounded-full {{ $disk > 90 ? 'bg-red-500' : ($disk > 60 ? 'bg-amber-500' : 'bg-blue-500') }}"
                 style="width: {{ min($disk, 100) }}%"></div>
        </div>
    </div>

    <!-- Server Info -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5 mb-4 sm:mb-6">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-3">🖥️ Server Info</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3">
            <div class="bg-slate-50 rounded-lg p-2 sm:p-3">
                <p class="text-[10px] sm:text-xs text-slate-400">OS</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-700 truncate">{{ $osInfo ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-2 sm:p-3">
                <p class="text-[10px] sm:text-xs text-slate-400">PHP</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-700">{{ $phpVersion ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-2 sm:p-3">
                <p class="text-[10px] sm:text-xs text-slate-400">MySQL</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-700">{{ $mysqlVersion ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-2 sm:p-3">
                <p class="text-[10px] sm:text-xs text-slate-400">Nginx</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-700">{{ $nginxVersion ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-2 sm:p-3">
                <p class="text-[10px] sm:text-xs text-slate-400">Uptime</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-700 truncate">{{ $uptime ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-2 sm:p-3">
                <p class="text-[10px] sm:text-xs text-slate-400">Disk Free</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-700">{{ $diskFree ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="mt-2 sm:mt-3 flex flex-wrap gap-1.5 sm:gap-2">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-cyan-50 text-cyan-700 rounded text-[10px] sm:text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> JTC Connect :8081
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] sm:text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Jurnal Trading :80
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-[10px] sm:text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Monitor :8080
            </span>
        </div>
    </div>

    <!-- Historical Chart -->
    @if($historical->count() > 0)
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-5">
        <h3 class="text-xs sm:text-sm font-semibold text-slate-900 mb-2 sm:mb-4">📈 CPU & RAM (7 hari)</h3>
        <div class="h-[180px] sm:h-[280px]">
            <canvas id="serverHistoryChart"></canvas>
        </div>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Inter, -apple-system, sans-serif';
    Chart.defaults.font.size = window.innerWidth < 640 ? 10 : 12;
    Chart.defaults.color = '#94A3B8';

    @if(count($historicalLabels) > 0)
    new Chart(document.getElementById('serverHistoryChart'), {
        type: 'line',
        data: {
            labels: @json($historicalLabels),
            datasets: [
                {
                    label: 'CPU %',
                    data: @json($historicalCpu),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: false,
                    tension: 0.4,
                    borderWidth: 2,
                },
                {
                    label: 'RAM %',
                    data: @json($historicalRam),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139,92,246,0.1)',
                    fill: false,
                    tension: 0.4,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, pointStyle: 'circle', padding: window.innerWidth < 640 ? 10 : 16, font: { size: window.innerWidth < 640 ? 10 : 12 } } }
            },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: '#F1F5F9' }, border: { display: false }, ticks: { callback: v => v + '%' } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
    @endif
});
</script>
@endsection
