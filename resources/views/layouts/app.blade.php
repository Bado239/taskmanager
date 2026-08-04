<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskManager Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">

<div class="min-h-screen flex">

    <!-- SIDEBAR -->
    <aside id="sidebar-wrapper" class="w-64 bg-slate-900 text-white flex flex-col transition-all duration-300">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h1 class="text-xl font-bold tracking-wide">TaskManager Pro</h1>
            <button id="sidebarToggle" class="text-gray-400 hover:text-white lg:hidden">✕</button>
        </div>

        <nav class="flex-grow p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 transition-colors">
                📊 Dashboard
            </a>
            <a href="{{ route('mode.switch', 'office') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 transition-colors">
                💼 Mode Office
            </a>
            <a href="{{ route('mode.switch', 'master') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-slate-800 transition-colors">
                🎓 Mode Master
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-grow flex flex-col min-w-0">

        <!-- HEADER TOP BAR -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-semibold text-sm">Espace de travail</span>
                <span class="text-gray-300">/</span>
                <span class="text-slate-900 font-semibold text-sm">Cockpit d'analyse</span>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-grow p-8">
            @yield('content')
        </main>

    </div>

</div>

<!-- SIDEBAR TOGGLE SCRIPT -->
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar-wrapper');
        
        if (toggle && sidebar) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }
    });
</script>

</body>
</html>