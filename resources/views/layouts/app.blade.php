<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="bufHsOhARrgBcSxf5jl2TSo8QZHds4glU1Om2XAHZ0c" />

    <title>TaskManager — Console v1.0</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $currentMode = session('user_mode', 'office');
    $isMaster = $currentMode === 'master';
@endphp

<body class="font-sans antialiased bg-gradient-to-br from-[#0f172a] via-[#0b1220] to-[#111827] text-slate-200">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR PREMIUM -->
    <aside id="sidebar-wrapper"
        class="w-64 bg-[#0f172a]/90 backdrop-blur-xl border-r border-white/5 text-slate-300 flex flex-col transition-all duration-300 fixed inset-y-0 left-0 z-50 md:static md:translate-x-0 shadow-2xl transform -translate-x-full">

        <!-- LOGO -->
        <div class="p-6 border-b border-white/5 flex items-center gap-3">
            <span class="text-2xl">📋</span>
            <div>
                <h2 class="text-xs font-black tracking-widest text-white uppercase">TaskManager</h2>
                <span class="text-[10px] font-bold text-blue-400 tracking-[0.2em] uppercase">
                    Console v1.0
                </span>
            </div>
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-grow px-4 py-6 space-y-2 overflow-y-auto">

            <a href="{{ route('tasks.index') }}"
               class="group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-300
               {{ request()->routeIs('tasks.index')
               ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20'
               : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <span>📅</span>
                <span>Aujourd'hui</span>
            </a>

            <a href="{{ route('tasks.dashboard') }}"
               class="group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-300
               {{ request()->routeIs('tasks.dashboard')
               ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20'
               : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <span>📊</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('tasks.veille') }}"
               class="group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-300
               {{ request()->routeIs('tasks.veille')
               ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20'
               : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <span>📡</span>
                <span>Veille Tech</span>
            </a>

            <a href="{{ route('tasks.create') }}"
               class="group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-300
               {{ request()->routeIs('tasks.create')
               ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-lg shadow-green-500/20'
               : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <span>➕</span>
                <span>Ajouter une activité</span>
            </a>

        </nav>

        <!-- MODE SWITCH -->
        <div class="p-4 border-t border-white/5">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3">
                Mode actif
            </div>

            <div class="flex bg-white/5 p-1 rounded-lg">

                <a href="{{ route('mode.switch', 'office') }}"
                   class="flex-1 text-center py-2 rounded-md text-xs font-semibold transition-all
                   {{ !$isMaster ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                    💼 Bureau
                </a>

                <a href="{{ route('mode.switch', 'master') }}"
                   class="flex-1 text-center py-2 rounded-md text-xs font-semibold transition-all
                   {{ $isMaster ? 'bg-purple-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                    🎓 Master
                </a>

            </div>
        </div>

    </aside>

    <!-- CONTENT -->
    <div class="flex-grow flex flex-col overflow-y-auto min-w-0">

        <!-- HEADER -->
        <header class="bg-[#0f172a]/80 backdrop-blur-xl border-b border-white/5 h-16 flex items-center justify-between px-8">

            <button id="sidebarToggle"
                class="text-slate-400 hover:text-white focus:outline-none p-2 rounded-lg hover:bg-white/5">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>

            <div class="flex items-center gap-4">
                <span class="text-xs font-semibold text-blue-400 bg-blue-500/10 border border-blue-500/20 px-3 py-1.5 rounded-full">
                    🇸🇳 Dakar, Sénégal
                </span>

                @if(Auth::check())
                    <span class="text-sm font-semibold text-white">
                        {{ Auth::user()->name }}
                    </span>
                @endif
            </div>

        </header>

        <!-- BREADCRUMB -->
        <div class="px-8 py-4 text-sm text-slate-400 border-b border-white/5">
            <span class="hover:text-white transition">Accueil</span>
            <span class="mx-2">/</span>
            <span class="text-white font-semibold">Cockpit d'analyse</span>
        </div>

        <!-- MAIN CONTENT -->
        <main class="flex-grow p-8">
            {{ $slot }}
        </main>

    </div>

</div>

<!-- SIDEBAR TOGGLE SCRIPT -->
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar-wrapper');

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    });
</script>

</body>
</html>