<!DOCTYPE html>
<html lang="fr" class="h-full bg-[#f8fafc]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskManager Pro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>


<body class="h-full text-gray-800 antialiased flex flex-col" x-data="{ sidebarOpen:true }">


<div class="min-h-screen flex flex-col w-full">


<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sticky top-0 z-30 shadow-sm">

    <div class="flex items-center gap-3">

        <button @click="sidebarOpen=!sidebarOpen"
            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 border border-gray-200">

            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"/>
            </svg>

        </button>


        <span class="font-extrabold text-lg text-gray-900 tracking-tight flex items-center gap-2">

            ⚡ TaskManager

            <span class="text-xs bg-[#0052cc] text-white px-2 py-0.5 rounded font-bold">
                PRO
            </span>

        </span>


    </div>



    <div class="flex items-center gap-3">

        <span class="text-xs text-gray-500 font-medium hidden sm:inline">
            Connecté
        </span>


        <div class="w-8 h-8 rounded-full bg-[#0052cc] text-white flex items-center justify-center font-bold text-xs">
            TM
        </div>

    </div>


</header>




<div class="flex flex-1 overflow-hidden">



<aside
x-show="sidebarOpen"
x-cloak
class="w-64 bg-gradient-to-b from-[#102f55] to-[#071b36] p-4 flex flex-col justify-between flex-shrink-0 shadow-xl z-20">


<div class="space-y-6">


<!-- TITRE -->

<div class="flex items-center justify-between pb-3 border-b border-white/10">

<span class="text-xs font-bold uppercase tracking-widest text-blue-200">
Navigation
</span>


<button @click="sidebarOpen=false"
class="text-blue-200 hover:text-white text-xs">

✕ Masquer

</button>


</div>





<!-- DASHBOARD -->

<nav>

<a href="{{ route('dashboard',['view'=>'dashboard']) }}"

class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition

{{ request('view','dashboard')==='dashboard'
? 'bg-blue-600/30 text-white border border-blue-400/30'
: 'text-blue-100 hover:bg-white/10' }}">


📊

<span>
Dashboard
</span>


</a>

</nav>







<!-- ESPACE TRAVAIL -->


<div class="space-y-2 pt-4 border-t border-white/10">


<span class="text-xs font-bold uppercase tracking-widest text-blue-200 block mb-3">

Espace de travail

</span>





<a href="{{ route('dashboard',['view'=>'office']) }}"

class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition

{{ request('view')==='office'
? 'bg-blue-600/30 text-white border border-blue-400/30'
: 'text-blue-100 hover:bg-white/10' }}">


<span class="flex items-center gap-2">

💼 Mode Office

</span>



@if(request('view')==='office')

<span class="w-2 h-2 rounded-full bg-emerald-400"></span>

@endif


</a>








<a href="{{ route('dashboard',['view'=>'master']) }}"

class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition

{{ request('view')==='master'
? 'bg-blue-600/40 text-white border border-blue-400/40 shadow-md'
: 'text-blue-100 hover:bg-white/10' }}">


<span class="flex items-center gap-2">

🎓 Mode Master

</span>



@if(request('view')==='master')

<span class="w-2 h-2 rounded-full bg-emerald-400"></span>

@endif


</a>









<a href="{{ route('dashboard',['view'=>'personal']) }}"

class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition

{{ request('view')==='personal'
? 'bg-blue-600/30 text-white border border-blue-400/30'
: 'text-blue-100 hover:bg-white/10' }}">


<span class="flex items-center gap-2">

🌱 Développement Personnel

</span>



@if(request('view')==='personal')

<span class="w-2 h-2 rounded-full bg-emerald-400"></span>

@endif


</a>



</div>


</div>






<!-- FOOTER SIDEBAR -->

<div class="pt-4 border-t border-white/10 text-xs text-blue-200 text-center">

TaskManagerPRO © {{ date('Y') }}

</div>




</aside>







<main class="flex-1 overflow-y-auto p-4 md:p-8 bg-[#f8fafc]">

@yield('content')

</main>




</div>


</div>


</body>

</html>