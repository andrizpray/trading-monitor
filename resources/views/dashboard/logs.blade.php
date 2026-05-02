@extends('layouts.app')
@section('title', 'Error Logs — Trading Monitor')

@section('content')
<div class="w-full max-w-7xl mx-auto">

    <div class="mb-4 sm:mb-6">
        <h1 class="text-lg sm:text-2xl font-bold text-slate-900">Error Logs</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 sm:mt-1">Log error dari aplikasi Jurnal Trading</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6" x-data="{ open: false }">
        <!-- Search -->
        <div class="relative mb-2.5 sm:mb-0">
            <svg class="absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <form method="GET" action="{{ route('dashboard.logs') }}">
                @if(request('level'))
                <input type="hidden" name="level" value="{{ request('level') }}">
                @endif
                <input type="text" name="search" value="{{ request('search', '') }}"
                       placeholder="Cari error..."
                       class="w-full pl-8 sm:pl-10 pr-3 sm:pr-4 py-2 text-xs sm:text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </form>
        </div>
        <!-- Level filter buttons -->
        <div class="flex gap-1.5 sm:gap-2 flex-wrap">
            <a href="{{ route('dashboard.logs') }}{{ request('search') ? '?search=' . urlencode(request('search')) : '' }}"
               class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-[10px] sm:text-xs font-medium rounded-lg transition {{ !request('level') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All
            </a>
            <a href="{{ route('dashboard.logs', ['level' => 'ERROR']) }}{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}"
               class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-[10px] sm:text-xs font-medium rounded-lg transition {{ request('level') == 'ERROR' ? 'bg-red-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🔴 Error ({{ $errorCount }})
            </a>
            <a href="{{ route('dashboard.logs', ['level' => 'WARNING']) }}{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}"
               class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-[10px] sm:text-xs font-medium rounded-lg transition {{ request('level') == 'WARNING' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🟡 Warning ({{ $warningCount }})
            </a>
            <a href="{{ route('dashboard.logs', ['level' => 'CRITICAL']) }}{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}"
               class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-[10px] sm:text-xs font-medium rounded-lg transition {{ request('level') == 'CRITICAL' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🔴 Critical ({{ $criticalCount }})
            </a>
        </div>
    </div>

    <!-- Log Entries -->
    <div class="space-y-2 sm:space-y-2">
        @forelse($logs as $i => $log)
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 overflow-hidden" x-data="{ expanded: false }">
            <div class="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-4 cursor-pointer hover:bg-slate-50 transition" @click="expanded = !expanded">
                <!-- Level Badge -->
                <span class="shrink-0 mt-0.5 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs font-semibold
                    {{ $log['level'] === 'ERROR' ? 'bg-red-50 text-red-700' : '' }}
                    {{ $log['level'] === 'WARNING' ? 'bg-amber-50 text-amber-700' : '' }}
                    {{ $log['level'] === 'CRITICAL' ? 'bg-red-100 text-red-800' : '' }}
                    {{ !in_array($log['level'], ['ERROR', 'WARNING', 'CRITICAL']) ? 'bg-slate-100 text-slate-600' : '' }}">
                    {{ $log['level'] }}
                </span>
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm text-slate-700 line-clamp-1">{{ Str::limit($log['message'], 100) }}</p>
                    <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">{{ $log['date'] }}</p>
                </div>
                <!-- Expand arrow -->
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400 shrink-0 transition-transform mt-0.5" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <!-- Expanded stack trace -->
            <div x-show="expanded" x-cloak x-transition class="border-t border-slate-100 bg-slate-50 p-2.5 sm:p-4">
                <p class="text-[10px] sm:text-xs font-medium text-slate-500 mb-1.5 sm:mb-2">Full Message:</p>
                <p class="text-xs sm:text-sm text-slate-700 mb-2 sm:mb-3 break-all">{{ $log['message'] }}</p>
                @if($log['stack'])
                <p class="text-[10px] sm:text-xs font-medium text-slate-500 mb-1.5 sm:mb-2">Stack Trace:</p>
                <pre class="text-[10px] sm:text-xs text-slate-500 bg-slate-100 rounded-lg p-2 sm:p-3 overflow-x-auto max-h-40 sm:max-h-60 whitespace-pre-wrap break-all">{{ $log['stack'] }}</pre>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg sm:rounded-xl border border-slate-200 p-8 sm:p-12 text-center">
            <p class="text-slate-400 text-sm sm:text-base">🎉 Tidak ada error ditemukan</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($total > $perPage)
    <div class="flex items-center justify-center gap-1 sm:gap-2 mt-4 sm:mt-6">
        @php $totalPages = ceil($total / $perPage); @endphp
        @for($p = 1; $p <= $totalPages; $p++)
            <a href="{{ route('dashboard.logs', array_merge(request()->only('search', 'level'), ['page' => $p])) }}"
               class="w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center rounded-lg text-xs sm:text-sm font-medium transition
               {{ $page == $p ? 'bg-blue-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                {{ $p }}
            </a>
        @endfor
    </div>
    @endif

    <p class="text-[10px] sm:text-xs text-slate-400 text-center mt-3 sm:mt-4">Menampilkan {{ count($logs) }} dari {{ $total }} entries</p>
</div>
@endsection
