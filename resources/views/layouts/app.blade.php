<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Gestionnaire des taches') }}</title>

        <!-- Fonts & FontAwesome pour les icônes -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <style>
            body {
                font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
            }
        </style>
        <!-- Scripts (Tailwind est chargé ici) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <div class="flex h-screen overflow-hidden" id="wrapper">
            
            <!-- 1. BARRE VERTICALE À GAUCHE (SIDEBAR) -->
            <div id="sidebar-wrapper" class="w-64 bg-green-700 text-white flex flex-col transition-all duration-300 fixed inset-y-0 left-0 z-50 md:static md:translate-x-0 shadow-lg transform -translate-x-full">
                <!-- Titre personnalisé demandé -->
                <div class="p-5 text-lg font-bold border-b border-green-600 flex items-center justify-between tracking-wide">
                    <span>📋 Liste des pages</span>
                </div>
                
                <nav class="flex-1 px-3 py-4 space-y-1">
                    <!-- 📅 FEUILLE AUJOURD'HUI -->
                    <a href="{{ route('tasks.index') }}" class="flex items-center px-4 py-3 rounded-md transition hover:bg-green-600 {{ request()->routeIs('tasks.index') ? 'bg-green-800 font-semibold text-white' : 'text-green-100' }}">
                        <i class="fa-solid fa-calendar-day w-6"></i> Aujourd'hui
                    </a>

                    <!-- 📊 FEUILLE DASHBOARD -->
                    <a href="{{ route('tasks.dashboard') }}" class="flex items-center px-4 py-3 rounded-md transition hover:bg-green-600 {{ request()->routeIs('tasks.dashboard') ? 'bg-green-800 font-semibold text-white' : 'text-green-100' }}">
                        <i class="fa-solid fa-chart-pie w-6"></i> Dashboard
                    </a>

                    <!-- ➕ FEUILLE AJOUTER UNE PAGE -->
                    <a href="{{ route('tasks.create') }}" class="flex items-center px-4 py-3 rounded-md transition hover:bg-green-600 {{ request()->routeIs('tasks.create') ? 'bg-green-800 font-semibold text-white' : 'text-green-100' }}">
                        <i class="fa-solid fa-plus w-6"></i> Ajouter une page
                    </a>
                </nav>
            </div>

            <!-- 2. CONTENU À DROITE -->
            <div class="flex-1 flex flex-col overflow-y-auto min-w-0">
                
                <!-- Navbar du haut -->
                <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm shrink-0">
                    <button id="sidebarToggle" class="text-gray-600 hover:text-green-700 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? '' }}</span>
                    </div>
                </header>

                <!-- Injection dynamique des pages -->
                <main class="flex-grow p-6">
                    {{ $slot }}
                </main>
            </div>

        </div>

        <!-- SCRIPT JAVASCRIPT POUR LE TOGGLE (OUVRIR/FERMER SANS BUG) -->
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
                        } else {
                            sidebar.classList.toggle('-translate-x-full');
                        }
                    });
                }
            });
        </script>
    </body>
</html>