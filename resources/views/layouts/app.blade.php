
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
            background: linear-gradient(180deg, #10294b 0%, #091a33 100%);
        }

        .tm-nav-link {
            transition: background .2s ease, color .2s ease;
        }

        .tm-nav-link:hover {
            background: rgba(255, 255, 255, .09);
        }

        .tm-nav-active {
            background: linear-gradient(100deg, #2563eb, #1748a5);
            border: 1px solid rgba(147, 197, 253, .35);
            box-shadow: 0 5px 14px rgba(0, 0, 0, .12);
        }

        .tm-search:focus-within {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .10);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body
    class="min-h-screen text-slate-800 antialiased"
    x-data="{ sidebarOpen: window.innerWidth >= 768 }"
>

<div class="min-h-screen">

    <!-- BARRE SUPÉRIEURE -->
    <header class="sticky top-0 z-40 flex h-[76px] items-center gap-4 border-b border-slate-200 bg-white px-4 shadow-sm md:px-6">

        <!-- Bouton menu -->
        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            aria-label="Afficher ou masquer le menu"
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-blue-600"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Logo -->
        <a
            href="{{ route('dashboard', ['view' => 'dashboard']) }}"
            class="flex shrink-0 items-center gap-2.5"
        >
            <span class="text-2xl">⚡</span>

            <span class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">
                TaskManager
            </span>

            <span class="rounded-md bg-blue-600 px-2 py-1 text-[10px] font-extrabold tracking-wide text-white">
                PRO
            </span>
        </a>

        <!-- Recherche visuelle -->
        <div class="tm-search ml-2 hidden max-w-2xl flex-1 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 transition md:flex">
            <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                <path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>
            </svg>

            <input
                type="search"
                placeholder="Rechercher une tâche, une matière, un chapitre..."
                aria-label="Rechercher"
                class="w-full border-0 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:ring-0"
            >
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3 sm:gap-5">

            <!-- Notifications visuelles -->
            <button
                type="button"
                aria-label="Notifications"
                class="relative hidden h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 sm:flex"
                title="Notifications"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                        d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
                </svg>
            </button>

            <!-- Profil -->
            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-bold text-slate-800">
                        Mon espace
                    </p>
                    <p class="mt-0.5 flex items-center justify-end gap-1.5 text-xs text-slate-500">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Connecté
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-extrabold text-white shadow-sm ring-4 ring-blue-50">
                    TM
                </div>
            </div>

        </div>
    </header>


    <!-- STRUCTURE PRINCIPALE -->
    <div class="flex min-h-[calc(100vh-76px)]">

        <!-- FOND MOBILE -->
        <div
            x-cloak
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-950/50 md:hidden"
        ></div>

        <!-- MENU LATÉRAL -->
        <aside
            x-cloak
            x-show="sidebarOpen"
            class="tm-sidebar fixed inset-y-0 left-0 z-50 flex w-72 flex-col text-white shadow-2xl md:sticky md:top-[76px] md:z-20 md:h-[calc(100vh-76px)] md:shrink-0 md:shadow-none"
        >

            <!-- Identité du menu -->
            <div class="flex h-[76px] items-center justify-between border-b border-white/10 px-5">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-200">
                        Navigation
                    </p>
                    <p class="mt-1 text-xs text-blue-100/60">
                        Votre espace de travail
                    </p>
                </div>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="rounded-lg p-2 text-blue-200 transition hover:bg-white/10 hover:text-white"
                    aria-label="Masquer le menu"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 6l12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>


            <nav class="flex-1 space-y-7 overflow-y-auto px-3 py-6">

                <!-- Navigation principale -->
                <section>
                    <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-200/60">
                        Menu principal
                    </p>

                    <a
                        href="{{ route('dashboard', ['view' => 'dashboard']) }}"
                        class="tm-nav-link flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold
                        {{ request('view', 'dashboard') === 'dashboard'
                            ? 'tm-nav-active text-white'
                            : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="1.7"/>
                                <rect x="14" y="3" width="7" height="7" rx="1" stroke-width="1.7"/>
                                <rect x="3" y="14" width="7" height="7" rx="1" stroke-width="1.7"/>
                                <rect x="14" y="14" width="7" height="7" rx="1" stroke-width="1.7"/>
                            </svg>
                        </span>

                        <span>Dashboard</span>

                        @if(request('view', 'dashboard') === 'dashboard')
                            <span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>
                </section>


                <!-- Espaces de travail -->
                <section>
                    <p class="mb-3 border-t border-white/10 px-3 pt-5 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-200/60">
                        Espace de travail
                    </p>

                    <!-- Mode Office -->
                    <a
                        href="{{ route('dashboard', ['view' => 'office']) }}"
                        class="tm-nav-link mb-2 flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold
                        {{ request('view') === 'office'
                            ? 'tm-nav-active text-white'
                            : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-400/15 text-lg">
                            💼
                        </span>

                        <span>Mode Office</span>

                        @if(request('view') === 'office')
                            <span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>

                    <!-- Mode Master -->
                    <a
                        href="{{ route('dashboard', ['view' => 'master']) }}"
                        class="tm-nav-link mb-2 flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold
                        {{ request('view') === 'master'
                            ? 'tm-nav-active text-white'
                            : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-400/15 text-lg">
                            🎓
                        </span>

                        <span>Mode Master</span>

                        @if(request('view') === 'master')
                            <span class="ml-auto h-2 w-2 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>

                    <!-- Développement personnel -->
                    <a
                        href="{{ route('dashboard', ['view' => 'personal']) }}"
                        class="tm-nav-link flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-semibold
                        {{ request('view') === 'personal'
                            ? 'tm-nav-active text-white'
                            : 'text-blue-100' }}"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-400/15 text-lg">
                            🌱
                        </span>

                        <span class="leading-5">Développement Personnel</span>

                        @if(request('view') === 'personal')
                            <span class="ml-auto h-2 w-2 shrink-0 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>
                </section>

            </nav>


            <!-- Bloc PRO -->
            <div class="mx-3 mb-4 rounded-2xl border border-blue-400/30 bg-gradient-to-br from-blue-500/15 to-indigo-500/10 p-4">
                <div class="mb-3 flex items-center gap-2">
                    <span class="text-xl">👑</span>
                    <p class="text-sm font-bold text-white">
                        TaskManager PRO
                    </p>
                </div>

                <p class="text-xs leading-5 text-blue-100/80">
                    Organisez vos tâches, suivez vos cours et optimisez votre productivité.
                </p>
            </div>


            <!-- Pied du menu -->
            <div class="border-t border-white/10 px-4 py-4 text-center">
                <p class="text-xs font-semibold text-blue-100/80">
                    TaskManager PRO
                </p>
                <p class="mt-1 text-[10px] text-blue-200/50">
                    © {{ date('Y') }} — Tous droits réservés
                </p>
            </div>

        </aside>


        <!-- CONTENU PRINCIPAL -->
        <main class="min-w-0 flex-1 overflow-x-hidden bg-[#f5f8ff] p-4 sm:p-6 lg:p-8">

            <div class="mx-auto w-full max-w-[1800px]">

                @yield('content')

            </div>

        </main>

    </div>

</div>

</body>
</html>
