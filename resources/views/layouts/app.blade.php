<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Trading Monitor')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', '-apple-system', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#EFF6FF', 100: '#DBEAFE', 200: '#BFDBFE',
                            500: '#3B82F6', 600: '#2563EB', 700: '#1D4ED8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        * { -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 2px; }
        body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-[260px] sm:w-[260px] bg-white border-r border-slate-200 transition-transform duration-200 lg:static lg:z-auto flex flex-col">

        <!-- Logo -->
        <div class="p-4 border-b border-slate-200">
            <h1 class="text-base sm:text-lg font-bold text-blue-600">📊 Trading Monitor</h1>
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
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs sm:text-sm shrink-0">
                    {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900 truncate">{{ Auth::guard('admin')->user()->name }}</p>
                    <p class="text-xs text-slate-400">Admin</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 transition p-1" title="Logout">
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
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200 px-3 sm:px-4 py-2.5 sm:py-3 flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-800 p-0.5 -ml-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex-1"></div>
            <span class="text-xs sm:text-sm text-slate-400">{{ now()->format('d M Y, H:i') }}</span>
        </header>

        <!-- Page content -->
        <main class="flex-1 px-3 py-3 sm:px-4 sm:py-4 md:px-6 md:py-6 overflow-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
