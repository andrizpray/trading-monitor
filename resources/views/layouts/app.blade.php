<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trading Monitor')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#22d3ee',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
    </style>
</head>
<body class="bg-slate-900 text-slate-200 min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-800 border-r border-slate-700 transition-transform duration-200 lg:static lg:z-auto">
        <div class="p-4 border-b border-slate-700">
            <h1 class="text-lg font-bold text-brand">📊 Trading Monitor</h1>
        </div>
        <nav class="p-4 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->is('/') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white' }} transition">
                <span>🏠</span> Overview
            </a>
            <a href="{{ route('dashboard.users') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->is('users') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white' }} transition">
                <span>👥</span> Users
            </a>
            <a href="{{ route('dashboard.portfolio') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->is('portfolio') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white' }} transition">
                <span>📈</span> Portfolio
            </a>
            <a href="{{ route('dashboard.server') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->is('server') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white' }} transition">
                <span>🖥️</span> Server
            </a>
            <a href="{{ route('dashboard.logs') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->is('logs') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white' }} transition">
                <span>🚨</span> Error Logs
            </a>
        </nav>
    </aside>

    <!-- Overlay for mobile sidebar -->
    @if (true)
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
    @endif

    <!-- Main content -->
    <div class="lg:ml-64 flex flex-col min-h-screen">
        <!-- Top bar -->
        <header class="sticky top-0 z-30 bg-slate-800/80 backdrop-blur border-b border-slate-700 px-4 py-3 flex items-center justify-between">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-400">{{ Auth::guard('admin')->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="text-sm text-slate-400 hover:text-red-400 transition">Logout</button>
                </form>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 overflow-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
