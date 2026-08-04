<!DOCTYPE html>
<html lang="fr" x-data="{ sidebarOpen: true }" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskManager Pro</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js pour la gestion du toggle sidebar -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-gray-800 antialiased flex flex-col">

    <div class="min-h-screen flex flex-col w-full">
        
        <!-- BARRE DE NAVIGATION SUPERIEURE (TOPBAR) -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3">
                <!-- Bouton Masquer / Afficher la sidebar -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none border border-gray-200"
                        title="Masquer / Afficher le menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-extrabold text-lg text-gray-900 tracking-tight flex items-center gap-2">
                    ⚡ TaskManager <span class="text-xs bg-[#0052cc] text-white px-2 py-0.5 rounded font-bold">PRO</span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 font-medium hidden sm:inline">Connecté</span>
                <div class="w-8 h-8 rounded-full bg-[#0052cc] text-white flex items-center justify-center font-bold text-xs">
                    TM
                </div>
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            
            <!-- MENU LATÉRAL (SIDEBAR) -->
            <aside x-show="sidebarOpen" 
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="-translate-x-full opacity-0"
                   x-transition:enter-end="translate-x-0 opacity-100"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0 opacity-100"
                   x-transition:leave-end="-translate-x-full opacity-0"
                   class="w-64 bg-white border-r border-gray-200 p-4 flex flex-col justify-between flex-shrink-0 z-20">
                
                <div class="space-y-6">
                    <!-- ENTÊTE SIDEBAR -->
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="text-xs font-bold uppercase text-gray-400 tracking-wider">Navigation</span>
                        <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-600 text-xs flex items-center gap-1">
                            ✕ Masquer
                        </button>
                    </div>

                    <!-- ACCÈS PRINCIPAUX -->
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard', ['view' => 'dashboard']) }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold bg-blue-50 text-[#0052cc]">
                            📊 <span>Dashboard</span>
                        </a>
                    </nav>

                    <!-- SELECTION DU MODE -->
                    <div class="space-y-2 pt-2 border-t border-gray-100">
                        <span class="text-xs font-bold uppercase text-gray-400 tracking-wider block">Espace de travail</span>
                        
                        <a href="{{ route('mode.switch', 'office') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ session('current_mode', 'office') === 'office' ? 'bg-gray-100 font-bold text-gray-900 border border-gray-200' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2">💼 Mode Office</span>
                            @if(session('current_mode', 'office') === 'office')
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            @endif
                        </a>

                        <a href="{{ route('mode.switch', 'master') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ session('current_mode') === 'master' ? 'bg-gray-100 font-bold text-gray-900 border border-gray-200' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2">🎓 Mode Master</span>
                            @if(session('current_mode') === 'master')
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- FOOTER SIDEBAR -->
                <div class="pt-4 border-t border-gray-100 text-xs text-gray-400 text-center">
                    TaskManager Pro v1.0
                </div>
            </aside>

            <!-- CONTENU PRINCIPAL DE LA PAGE -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-[#f8fafc]">
                @yield('content')
            </main>

        </div>
    </div>

</body>
</html>