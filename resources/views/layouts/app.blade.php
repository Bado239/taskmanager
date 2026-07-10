<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                overflow-x: hidden;
            }
            /* Structure de la Sidebar */
            #wrapper {
                display: flex;
                width: 100vw;
                height: 100vh;
            }
            #sidebar-wrapper {
                min-height: 100vh;
                width: 260px;
                margin-left: -260px;
                transition: margin 0.25s ease-out;
                background-color: #198754; /* Vert Sénégal */
                z-index: 1000;
            }
            #sidebar-wrapper .sidebar-heading {
                padding: 1.2rem 1.25rem;
                font-size: 1.2rem;
            }
            /* Toggle de la Sidebar */
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }
            #page-content-wrapper {
                flex: 1;
                width: 100%;
                overflow-y: auto;
            }
            @media (min-width: 768px) {
                #sidebar-wrapper {
                    margin-left: 0;
                }
                #wrapper.toggled #sidebar-wrapper {
                    margin-left: -260px;
                }
            }
            /* Liens de la barre */
            .sidebar-link {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                padding: 14px 20px;
                display: block;
                transition: 0.3s;
                font-weight: 500;
            }
            .sidebar-link:hover, .sidebar-link.active {
                background-color: rgba(255, 255, 255, 0.15);
                color: #fff;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">

        <div id="wrapper">
            
            <!-- 1. BARRE VERTICALE À GAUCHE (SIDEBAR) -->
            <div class="border-end shadow-sm" id="sidebar-wrapper">
                <div class="sidebar-heading text-white fw-bold border-bottom border-light border-opacity-25 d-flex align-items-center">
                    <span>🇸🇳 {{ config('app.name', 'PharmaGarde') }}</span>
                </div>
                <div class="list-group list-group-flush my-3">
                    <a href="{{ route('tasks.index') }}" class="sidebar-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check me-2"></i> Toutes les tâches
                    </a>
                    <a href="{{ route('tasks.create') }}" class="sidebar-link {{ request()->routeIs('tasks.create') ? 'active' : '' }}">
                        <i class="fa-solid fa-plus me-2"></i> Créer une tâche
                    </a>
                    <!-- Tu pourras ajouter les futurs liens pour ton site de pharmacie ici -->
                </div>
            </div>

            <!-- 2. CONTENU DE LA PAGE (À DROITE) -->
            <div id="page-content-wrapper" class="d-flex flex-column">
                
                <!-- Navbar du haut avec bouton Menu & l'ancienne navigation (profil, déconnexion...) -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-2 shadow-sm d-flex justify-content-between">
                    <button class="btn btn-success" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    
                    <div class="d-flex align-items-center">
                        <!-- On garde les infos utilisateur à droite de la navbar -->
                        <span class="text-muted me-3">{{ Auth::user()->name ?? '' }}</span>
                    </div>
                </nav>

                <!-- En-tête dynamique (Page Heading) -->
                @isset($header)
                    <header class="bg-white shadow-sm border-bottom py-3 px-4">
                        <div class="fs-4 fw-medium text-gray-800">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Injection des pages (Page Content) -->
                <main class="flex-grow-1 p-4">
                    {{ $slot }}
                </main>
            </div>

        </div>

        <!-- SCRIPT JAVASCRIPT POUR LE TOGGLE -->
        <script>
            window.addEventListener('DOMContentLoaded', event => {
                const sidebarToggle = document.body.querySelector('#sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', event => {
                        event.preventDefault();
                        document.body.querySelector('#wrapper').classList.toggle('toggled');
                    });
                }
            });
        </script>
    </body>
</html>