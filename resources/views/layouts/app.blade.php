<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf-token() }}">
        <!-- Balise de validation Google Search Console -->
        <meta name="google-site-verification" content="bufHsOhARrgBcSxf5jl2TSo8QZHds4glU1Om2XAHZ0c" />

        <title>Gestionnaire de tâches</title>
        <!-- Fonts & FontAwesome pour les icônes -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <style>
            body {
                font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
            }
        </style>
        <!-- Scripts (Tailwind est chargé ici) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $currentMode = session('user_mode', 'office');
        $isMaster = $currentMode === 'master';
    @endphp
    <body class="font-sans antialiased bg-[#f4f5f8] text-[#1e293b]">
        <div class="flex h-screen overflow-hidden" id="wrapper">
            
            <!-- ─── 1. BARRE VERTICALE À GAUCHE (SIDEBAR STYLE SIMILARWEB) ─── -->
            <aside id="sidebar-wrapper" class="w-64 bg-white border-r border-gray-200/80 text-[#1e293b] flex flex-col transition-all duration-300 fixed inset-y-0 left-0 z-50 md:static md:translate-x-0 shadow-sm transform -translate-x-full">
                
                <!-- Logo & Titre -->
                <div class="p-5 border-b border-gray-100 flex items-center gap-3 bg-white shrink-0">
                    <span class="text-2xl">📋</span>
                    <div>
                        <h2 class="text-sm font-black tracking-tight text-[#0f172a] uppercase">TaskManager</h2>
                        <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Console v1.0</span>
                    </div>
                </div>
                
                <!-- Liens de Navigation -->
                <nav class="flex-grow px-3 py-4 space-y-1 bg-white overflow-y-auto">
                    <!-- 📅 FEUILLE AUJOURD'HUI -->
                    <a href="{{ route('tasks.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('tasks.index') ? 'bg-blue-50 text-[#1862ff] border border-blue-100/50' : 'text-gray-600 hover:text-[#1862ff] hover:bg-gray-50' }}">
                        <span class="text-lg">📅</span>
                        <span>Aujourd'hui</span>
                    </a>

                    <!-- 📊 FEUILLE DASHBOARD -->
                    <a href="{{ route('tasks.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('tasks.dashboard') ? 'bg-blue-50 text-[#1862ff] border border-blue-100/50' : 'text-gray-600 hover:text-[#1862ff] hover:bg-gray-50' }}">
                        <span class="text-lg">📊</span>
                        <span>Dashboard</span>
                    </a>

                    <!-- 🌐 FEUILLE VEILLE TECH -->
                    <a href="{{ route('tasks.veille') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('tasks.veille') ? 'bg-blue-50 text-[#1862ff] border border-blue-100/50' : 'text-gray-600 hover:text-[#1862ff] hover:bg-gray-50' }}">
                        <span class="text-lg">📡</span>
                        <span>Veille Tech</span>
                    </a>

                    <!-- ➕ FEUILLE AJOUTER UNE TACHE -->
                    <a href="{{ route('tasks.create') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('tasks.create') ? 'bg-blue-50 text-[#1862ff] border border-blue-100/50' : 'text-gray-600 hover:text-[#1862ff] hover:bg-gray-50' }}">
                        <span class="text-lg">➕</span>
                        <span>Ajouter une activité</span>
                    </a>
                </nav>

                <!-- Commutateur de Mode en bas de la Sidebar -->
                <div class="p-4 border-t border-gray-100 bg-[#fafbfe] shrink-0">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 px-2">Mode actif actuel</div>
                    <div class="flex bg-gray-100 p-0.5 rounded-lg border border-gray-200">
                        <a href="{{ route('mode.switch', 'office') }}" 
                           class="flex-1 text-center py-1.5 rounded-md text-[10px] font-bold transition-all {{ !$isMaster ? 'bg-white shadow-sm text-[#1862ff]' : 'text-gray-500 hover:text-gray-800' }}">
                            💼 Office
                        </a>
                        <a href="{{ route('mode.switch', 'master') }}" 
                           class="flex-1 text-center py-1.5 rounded-md text-[10px] font-bold transition-all {{ $isMaster ? 'bg-white shadow-sm text-purple-600' : 'text-gray-500 hover:text-gray-800' }}">
                            🎓 Master
                        </a>
                    </div>
                </div>
            </aside>

            <!-- ─── 2. CONTENU À DROITE (DÉROULANT PROPREMENT) ─── -->
            <div class="flex-grow flex flex-col overflow-y-auto min-w-0">
                
                <!-- En-tête supérieur de contrôle -->
                <header class="bg-white border-b border-gray-200/80 h-16 flex items-center justify-between px-6 shadow-sm shrink-0">
                    <button id="sidebarToggle" class="text-gray-500 hover:text-[#1862ff] focus:outline-none p-2 rounded-xl hover:bg-gray-50">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-xl">
                            🇸🇳 Dakar, Sénégal
                        </span>
                        @if(Auth::check())
                            <span class="text-sm font-bold text-[#0f172a]">{{ Auth::user()->name }}</span>
                        @endif
                    </div>
                </header>

                <!-- Injection dynamique des pages -->
                <main class="flex-grow">
                    {{ $slot }}
                </main>
            </div>

        </div>

        <!-- SCRIPT POUR LE TOGGLE MOBILE ET BUREAU -->
        <script>
            window.addEventListener('DOMContentLoaded', event => {
                const sidebarToggle = document.body.querySelector('#sidebarToggle');
                const sidebar = document.body.querySelector('#sidebar-wrapper');
                
                if (sidebarToggle && sidebar) {
                    sidebarToggle.addEventListener('click', event => {
                        event.preventDefault();
                        
                        if (window.innerWidth >= 768) {
                            sidebar.classList.toggle('md:w-0');
                            sidebar.classList.toggle('md:overflow-hidden');
                            sidebar.classList.toggle('md:border-r-0');
                        } else {
                            sidebar.classList.toggle('-translate-x-full');
                        }
                    });
                }
            });
        </script>
    </body>
</html>