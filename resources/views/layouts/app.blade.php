<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaskManager PRO')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
        .tm-sidebar { background: linear-gradient(180deg, #10294b 0%, #091a33 100%); }
        .tm-nav-link { transition: background .2s ease, color .2s ease; }
        .tm-nav-link:hover { background: rgba(255,255,255,.09); }
        .tm-nav-active { background: linear-gradient(100deg, #2563eb, #1748a5); border: 1px solid rgba(147,197,253,.35); box-shadow: 0 5px 14px rgba(0,0,0,.12); }
        .tm-search:focus-within { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.10); }
        /* Masquer la barre de défilement du menu sans désactiver le défilement */
        .tm-sidebar nav { scrollbar-width: none; -ms-overflow-style: none; }
        .tm-sidebar nav::-webkit-scrollbar { display: none; width: 0; height: 0; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen text-slate-800 antialiased" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
<div class="min-h-screen">
    <!-- BARRE SUPÉRIEURE -->
    <header class="sticky top-0 z-40 flex h-[76px] items-center gap-3 border-b border-slate-200 bg-white px-3 shadow-sm sm:gap-4 sm:px-4 lg:px-6">
        <button type="button" @click="sidebarOpen = !sidebarOpen" aria-label="Afficher ou masquer le menu" :aria-expanded="sidebarOpen.toString()"
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-blue-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('dashboard', ['view' => 'dashboard']) }}" class="flex shrink-0 items-center gap-2 text-lg font-extrabold tracking-tight text-slate-950 sm:text-xl">
            <span class="text-2xl text-amber-500">⚡</span><span>TaskManager</span><span class="rounded-md bg-blue-600 px-2 py-1 text-[10px] font-extrabold text-white sm:text-xs">PRO</span>
        </a>
        <div class="hidden min-w-0 flex-1 justify-center px-2 md:flex lg:px-4">
            <div class="tm-search flex h-11 w-full max-w-[650px] items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 text-slate-500">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/></svg>
                <span class="truncate text-sm">Rechercher une tâche, une matière, un chapitre...</span>
            </div>
        </div>
        <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-4">
            <span class="hidden text-xs font-medium text-slate-500 sm:inline">Connecté</span>
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-extrabold text-white ring-4 ring-blue-50" aria-label="Profil TaskManager">TM</div>
        </div>
    </header>

    <div class="relative flex min-h-[calc(100vh-76px)]">
        <!-- Fond d'assombrissement mobile -->
        <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 top-[76px] z-30 bg-slate-950/50 md:hidden"></div>

        <!-- MENU LATÉRAL -->
        <aside x-cloak x-show="sidebarOpen"
            class="tm-sidebar fixed bottom-0 left-0 top-[76px] z-40 flex w-72 flex-col text-white shadow-2xl md:sticky md:top-[76px] md:z-20 md:h-[calc(100vh-76px)] md:shrink-0 md:shadow-none">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-5">
                <div><p class="text-xs font-extrabold uppercase tracking-[.16em] text-blue-200">Navigation</p><p class="mt-1 text-xs text-blue-100/60">Votre espace de travail</p></div>
                <button type="button" @click="sidebarOpen = false" class="rounded-lg p-2 text-blue-200 transition hover:bg-white/10 hover:text-white md:hidden" aria-label="Fermer le menu">✕</button>
                <button type="button" @click="sidebarOpen = false" class="hidden rounded-lg p-2 text-blue-200 transition hover:bg-white/10 hover:text-white md:block" aria-label="Masquer le menu">✕</button>
            </div>

            <nav class="flex-1 space-y-7 overflow-y-auto px-3 py-6">
                <section>
                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[.18em] text-blue-200/60">Menu principal</p>
                    <a href="{{ route('dashboard', ['view' => 'dashboard']) }}" class="tm-nav-link flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view', 'dashboard') === 'dashboard' ? 'tm-nav-active text-white' : 'text-blue-100' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10">▦</span><span>Dashboard</span>
                        @if(request('view', 'dashboard') === 'dashboard')<span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>@endif
                    </a>
                </section>
                <section>
                    <p class="mb-3 border-t border-white/10 px-3 pt-5 text-[10px] font-bold uppercase tracking-[.18em] text-blue-200/60">Espace de travail</p>
                    <a href="{{ route('dashboard', ['view' => 'office']) }}" class="tm-nav-link mb-2 flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view') === 'office' ? 'tm-nav-active text-white' : 'text-blue-100' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-400/15">💼</span><span>Mode Office</span>
                        @if(request('view') === 'office')<span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>@endif
                    </a>
                    <a href="{{ route('dashboard', ['view' => 'master']) }}" class="tm-nav-link mb-2 flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view') === 'master' ? 'tm-nav-active text-white' : 'text-blue-100' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-400/15">🎓</span><span>Mode Master</span>
                        @if(request('view') === 'master')<span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>@endif
                    </a>
                    <a href="{{ route('dashboard', ['view' => 'personal']) }}" class="tm-nav-link flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view') === 'personal' ? 'tm-nav-active text-white' : 'text-blue-100' }}">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-400/15">🌱</span><span class="leading-5">Développement Personnel</span>
                        @if(request('view') === 'personal')<span class="ml-auto h-2 w-2 shrink-0 rounded-full bg-emerald-400"></span>@endif
                    </a>
                </section>
            </nav>

            <div class="mx-3 mb-4 rounded-2xl border border-blue-400/30 bg-gradient-to-br from-blue-500/15 to-indigo-500/10 p-4">
                <div class="mb-2 flex items-center gap-2"><span class="text-xl">👑</span><p class="text-sm font-bold text-white">TaskManager PRO</p></div>
                <p class="text-xs leading-5 text-blue-100/80">Organisez vos tâches, suivez vos cours et optimisez votre productivité.</p>
            </div>
            <footer class="border-t border-white/10 px-4 py-4 text-center"><p class="text-xs font-semibold text-blue-100/80">TaskManager PRO</p><p class="mt-1 text-[10px] text-blue-200/50">© {{ date('Y') }} — Tous droits réservés</p></footer>
        </aside>

        <!-- CONTENU -->
        <main class="min-w-0 flex-1 overflow-x-hidden bg-[#f5f8ff] p-3 sm:p-5 lg:p-8">
            <div class="mx-auto w-full max-w-[1800px]">@yield('content')</div>
        </main>
    </div>
</div>
</body>
</html>
