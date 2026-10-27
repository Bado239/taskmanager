
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaskManager PRO')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
        }

        .tm-sidebar {
            background: linear-gradient(180deg, #10294b, #091a33);
        }

        .tm-sidebar nav {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .tm-sidebar nav::-webkit-scrollbar {
            display: none;
        }

        .tm-nav-link {
            transition: background .2s, color .2s;
        }

        .tm-nav-link:hover {
            background: rgba(255,255,255,.09);
        }

        .tm-nav-active {
            background: linear-gradient(100deg, #2563eb, #1748a5);
            border: 1px solid rgba(147,197,253,.35);
            box-shadow: 0 5px 14px rgba(0,0,0,.12);
        }

        .tm-search:focus-within {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(59,130,246,.10);
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>

<body
    class="min-h-screen text-slate-800 antialiased"
    x-data="{ sidebarOpen: window.innerWidth >= 768 }"
>
<div class="min-h-screen">

    <!-- HEADER -->
    <header class="sticky top-0 z-40 flex h-[76px] items-center gap-3 border-b border-slate-200 bg-white px-3 shadow-sm sm:px-5 lg:gap-5">

        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            :aria-expanded="sidebarOpen.toString()"
            aria-controls="tm-sidebar"
            aria-label="Afficher ou masquer le menu"
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- LOGO -->
        <a
            href="{{ route('dashboard', ['view' => 'dashboard']) }}"
            class="flex shrink-0 items-center gap-2"
        >
            <span class="text-xl">⚡</span>
            <span class="text-base font-extrabold tracking-tight text-slate-900 sm:text-xl">
                TaskManager
            </span>
            <span class="rounded-md bg-blue-600 px-2 py-1 text-[10px] font-extrabold text-white">
                PRO
            </span>
        </a>


            <!-- BARRE DE RECHERCHE -->
            <form
                action="{{ route('dashboard') }}"
                method="GET"
                role="search"
                class="tm-search ml-2 hidden max-w-2xl flex-1 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 transition md:flex"
            >
                <!-- Conserver le mode actuel -->
                <input
                    type="hidden"
                    name="view"
                    value="{{ request('view', 'dashboard') }}"
                >

                <!-- Conserver les filtres actifs -->
                @if(request()->filled('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif

                @if(request()->filled('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <!-- Icône recherche -->
                <svg
                    class="h-5 w-5 shrink-0 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                    <path
                        d="m16 16 4 4"
                        stroke-linecap="round"
                        stroke-width="1.8"
                    />
                </svg>

                <!-- Champ de recherche -->
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher une tâche, une matière, un chapitre..."
                    aria-label="Rechercher une tâche"
                    class="w-full min-w-0 border-0 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400"
                >

                <!-- Bouton rechercher -->
                <button
                    type="submit"
                    class="shrink-0 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                >
                    Rechercher
                </button>
            </form>
            <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                <path d="m16 16 4 4" stroke-linecap="round" stroke-width="1.8"/>
            </svg>
            <input
                type="search"
                aria-label="Recherche bientôt disponible"
                placeholder="Rechercher une tâche, une matière, un chapitre..."
                title="Recherche bientôt disponible"
                readonly
                class="w-full cursor-default border-0 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400"
            >
        </div>

        <!-- PROFIL -->
        <div class="ml-auto flex shrink-0 items-center gap-3 sm:gap-5">
            <span class="hidden items-center justify-center text-slate-400 sm:flex"
                  title="Notifications bientôt disponibles">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                          d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
                </svg>
            </span>

            <div class="hidden text-right sm:block">
                <p class="text-sm font-bold text-slate-800">Mon espace</p>
                <p class="flex items-center justify-end gap-1.5 text-xs text-slate-500">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Connecté
                </p>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-extrabold text-white ring-4 ring-blue-50">
                TM
            </div>
        </div>
    </header>

    <!-- PAGE -->
    <div class="flex min-h-[calc(100vh-76px)]">

        <!-- FOND MOBILE -->
        <div
            x-cloak
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-950/50 md:hidden"
        ></div>

        <!-- SIDEBAR -->
        <aside
            id="tm-sidebar"
            x-cloak
            x-show="sidebarOpen"
            class="tm-sidebar fixed inset-y-0 left-0 z-50 flex w-72 flex-col text-white shadow-2xl md:sticky md:top-[76px] md:z-20 md:h-[calc(100vh-76px)] md:shrink-0 md:shadow-none"
        >
            <div class="flex h-[76px] shrink-0 items-center justify-between border-b border-white/10 px-5">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[.16em] text-blue-200">
                        Navigation
                    </p>
                    <p class="mt-1 text-xs text-blue-100/60">
                        Votre espace de travail
                    </p>
                </div>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    aria-label="Masquer le menu"
                    class="rounded-lg p-2 text-blue-200 hover:bg-white/10 hover:text-white"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 6l12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>

            <!-- NAVIGATION -->
            <nav class="min-h-0 flex-1 space-y-7 overflow-y-auto px-3 py-6"
                 aria-label="Navigation principale">

                <section>
                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[.18em] text-blue-200/60">
                        Menu principal
                    </p>

                    <a
                        href="{{ route('dashboard', ['view' => 'dashboard']) }}"
                        class="tm-nav-link flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view', 'dashboard') === 'dashboard' ? 'tm-nav-active text-white' : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10">
                            📊
                        </span>
                        <span>Dashboard</span>
                        @if(request('view', 'dashboard') === 'dashboard')
                            <span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>
                </section>

                <section>
                    <p class="mb-3 border-t border-white/10 px-3 pt-5 text-[10px] font-bold uppercase tracking-[.18em] text-blue-200/60">
                        Espace de travail
                    </p>

                    <!-- OFFICE -->
                    <a
                        href="{{ route('dashboard', ['view' => 'office']) }}"
                        class="tm-nav-link mb-2 flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view') === 'office' ? 'tm-nav-active text-white' : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-400/15 text-lg">
                            💼
                        </span>
                        <span>Mode Office</span>
                        @if(request('view') === 'office')
                            <span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>

                    <!-- MASTER -->
                    <a
                        href="{{ route('dashboard', ['view' => 'master']) }}"
                        class="tm-nav-link mb-2 flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view') === 'master' ? 'tm-nav-active text-white' : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-400/15 text-lg">
                            🎓
                        </span>
                        <span>Mode Master</span>
                        @if(request('view') === 'master')
                            <span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>

                    <!-- PERSONNEL -->
                    <a
                        href="{{ route('dashboard', ['view' => 'personal']) }}"
                        class="tm-nav-link flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold {{ request('view') === 'personal' ? 'tm-nav-active text-white' : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-400/15 text-lg">
                            🌱
                        </span>
                        <span class="leading-5">Développement Personnel</span>
                        @if(request('view') === 'personal')
                            <span class="ml-auto h-2 w-2 shrink-0 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>
                </section>
            </nav>

            <!-- CARTE PRO -->
            <div class="mx-3 mb-4 shrink-0 rounded-2xl border border-blue-400/30 bg-blue-500/10 p-4">
                <p class="mb-2 text-sm font-bold">👑 TaskManager PRO</p>
                <p class="text-xs leading-5 text-blue-100/80">
                    Organisez vos tâches, suivez vos cours et optimisez votre productivité.
                </p>
            </div>

            <!-- FOOTER -->
            <div class="shrink-0 border-t border-white/10 px-4 py-4 text-center">
                <p class="text-xs font-semibold text-blue-100/80">
                    TaskManager PRO
                </p>
                <p class="mt-1 text-[10px] text-blue-200/50">
                    © {{ date('Y') }} — Tous droits réservés
                </p>
            </div>
        </aside>

        <!-- CONTENU PRINCIPAL -->
        <main
            id="contenu-principal"
            class="min-w-0 flex-1 overflow-x-hidden bg-[#f5f8ff] p-4 sm:p-6 lg:p-8"
        >
            <div class="mx-auto w-full max-w-[1800px]">
                @yield('content')
            </div>
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
