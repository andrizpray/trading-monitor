<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trading Monitor')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            200: '#BFDBFE',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F8FAFC; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-slate-200 transition-transform duration-200 lg:static lg:z-auto flex flex-col">

        <!-- Logo -->
        <div class="p-4 border-b border-slate-200">
            <h1 class="text-lg font-bold text-blue-600">📊 Trading Monitor</h1>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-3 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->is('/') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <span>🏠</span> Overview
            </a>
            <a href="{{ route('dashboard.users') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->is('users') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <span>👥</span> Users
            </a>
            <a href="{{ route('dashboard.portfolio') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->is('portfolio') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <span>📈</span> Portfolio
            </a>
            <a href="{{ route('dashboard.server') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->is('server') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <span>🖥️</span> Server
            </a>
            <a href="{{ route('dashboard.logs') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->is('logs') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <span>🚨</span> Error Logs
            </a>
        </nav>

        <!-- User area -->
        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                    {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900 truncate">{{ Auth::guard('admin')->user()->name }}</p>
                    <p class="text-xs text-slate-400">Admin</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         x-transition.opacity
         class="fixed inset-0 z-40 bg-black/30 lg:hidden"></div>

    <!-- Main content -->
    <div class="lg:ml-[260px] flex flex-col min-h-screen">

        <!-- Top bar -->
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200 px-4 py-3 flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex-1"></div>
            <span class="text-sm text-slate-500">{{ now()->format('d M Y, H:i') }}</span>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-4 md:p-6 overflow-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
