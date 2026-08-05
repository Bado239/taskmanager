@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    showForm: false, 
    formType: '{{ $view === 'dashboard' ? 'office' : $view }}', 
    selectedProject: '', 
    selectedCategory: '{{ $view === 'office' ? 'CGP' : ($view === 'master' ? 'Master ISEF1' : '') }}',
    dropdownOpen: false,
    setFormType(type) {
        this.formType = type;
        if(type === 'office') {
            this.selectedCategory = 'CGP';
        } else if(type === 'master') {
            this.selectedCategory = 'Master ISEF1';
        }
    }
}">

    <!-- BARRE DE NAVIGATION / BOUTONS DE COMMUTATION DE VUE -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard', ['view' => 'dashboard']) }}" 
               class="px-4 py-2 font-bold text-xs rounded-lg transition-all {{ $view === 'dashboard' ? 'bg-[#0052cc] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('dashboard', ['view' => 'office']) }}" 
               class="px-4 py-2 font-bold text-xs rounded-lg transition-all {{ $view === 'office' ? 'bg-[#0052cc] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                💼 Mode Office
            </a>
            <a href="{{ route('dashboard', ['view' => 'master']) }}" 
               class="px-4 py-2 font-bold text-xs rounded-lg transition-all {{ $view === 'master' ? 'bg-[#0052cc] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                🎓 Mode Master
            </a>
        </div>

        {{-- ACTION BOUTON : MODE DASHBOARD (MENU DÉROULANT) --}}
        @if($view === 'dashboard')
            <div class="relative inline-block text-left">
                <button @click="dropdownOpen = !dropdownOpen" 
                        @click.outside="dropdownOpen = false"
                        type="button" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                    <span>+ Nouvelle tâche</span>
                    <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': dropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="dropdownOpen" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                    
                    <button @click="setFormType('office'); showForm = true; dropdownOpen = false" 
                            class="w-full text-left px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 flex items-center gap-2">
                        <span>💼</span> + Nouvelle tâche Office
                    </button>

                    <div class="border-t border-gray-100 my-1"></div>

                    <button @click="setFormType('master'); showForm = true; dropdownOpen = false" 
                            class="w-full text-left px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 flex items-center gap-2">
                        <span>🎓</span> + Nouvelle tâche Master
                    </button>
                </div>
            </div>
        @else
            {{-- ACTION BOUTON : MODES OFFICE ET MASTER DIRECTS --}}
            <button @click="showForm = !showForm; setFormType('{{ $view }}')" 
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                <span x-text="showForm ? '✕ Fermer' : '+ Nouvelle tâche {{ ucfirst($view) }}'"></span>
            </button>
        @endif
    </div>

    <!-- FORMULAIRE D'AJOUT RAPIDE -->
    <div x-show="showForm" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white p-6 rounded-xl border border-emerald-200 shadow-md">
        <div class="mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
                ➕ Enregistrer une tâche en 
                <span class="text-[#0052cc]" x-text="'Mode ' + formType.charAt(0).toUpperCase() + formType.slice(1)"></span>
            </h2>
            <button @click="showForm = false" class="text-xs text-gray-400 hover:text-gray-600">✕ Fermer</button>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <!-- Champ dynamique (office ou master) -->
            <input type="hidden" name="type" :value="formType">

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nom du Livrable *</label>
                <input type="text" name="title" required placeholder="Ex: Analyse de données"
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Lien de Travail</label>
                <input type="url" name="document_link" placeholder="https://..."
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <!-- CATÉGORIE PRÉ-SÉLECTIONNÉE DYNAMISQUEMENT -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catégorie</label>
                <select name="category_id" x-model="selectedCategory" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="">-- Sélectionner --</option>
                    @foreach($categories as $category)
                        @php $catName = $category->title ?? $category->name; @endphp
                        <option value="{{ $category->id }}" :selected="selectedCategory === '{{ $catName }}'">{{ $catName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- ASSOCIER À UN PROJET + BOUTON SUPPRIMER LE PROJET SELECTIONNÉ -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-gray-700">Associer à un projet</label>
                    <template x-if="selectedProject && selectedProject !== 'new'">
                        <button type="button" 
                                @click="if(confirm('Voulez-vous vraiment supprimer ce projet ?')) { document.getElementById('delete-project-form-' + selectedProject).submit(); }"
                                class="text-[10px] text-red-500 hover:underline font-semibold">
                            🗑️ Supprimer projet
                        </button>
                    </template>
                </div>
                <select name="project_id" x-model="selectedProject" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="">-- Aucun projet --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                    <option value="new">+ Créer un nouveau projet...</option>
                </select>
            </div>

            <div x-show="selectedProject === 'new'" x-cloak>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nouveau projet</label>
                <input type="text" name="new_project_name" placeholder="Nom du projet..."
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">État du Livrable</label>
                <select name="document_status" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="todo">🔴 Phase 1 : Cadrage</option>
                    <option value="in_progress">🟡 Phase 2 : En cours</option>
                    <option value="done">🟢 Phase 3 : Validé</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Urgence</label>
                <select name="priority" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="high">Haute</option>
                    <option value="medium" selected>Moyenne</option>
                    <option value="low">Basse</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Échéance</label>
                <input type="date" name="date_prevue" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <div class="md:col-span-3 pt-2">
                <button type="submit" class="bg-[#0052cc] hover:bg-[#003d99] text-white font-bold text-sm py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                    💾 Valider et Enregistrer la tâche <span x-text="formType.toUpperCase()"></span>
                </button>
            </div>
        </form>

        <!-- FORMS CACHÉS POUR LA SUPPRESSION DE PROJET -->
        @foreach($projects as $project)
            <form id="delete-project-form-{{ $project->id }}" action="{{ route('projects.destroy', $project->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>

    <!-- ========================================== -->
    <!-- VUE 1 : DASHBOARD (INDICATEURS GLOBAUX)     -->
    <!-- ========================================== -->
    @if($view === 'dashboard')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <span>Accueil</span> / <span class="text-gray-600 font-medium">Cockpit d'analyse</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                📊 Bilan Global - Cockpit Livrables 
                <span class="text-xs bg-blue-50 text-[#0052cc] px-2.5 py-0.5 rounded-full font-semibold border border-blue-100">
                    🇸🇳 Dakar, Sénégal
                </span>
            </h1>

            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <span class="font-bold">Accès rapides :</span>
                    <a href="{{ route('dashboard', ['view' => 'office']) }}" class="px-2.5 py-1 rounded bg-gray-100 font-semibold hover:bg-gray-200">💼 Mode Office</a>
                    <a href="{{ route('dashboard', ['view' => 'master']) }}" class="px-2.5 py-1 rounded bg-gray-100 font-semibold hover:bg-gray-200">🎓 Mode Master</a>
                </div>
            </div>
        </div>

        <!-- INDICATEURS GLOBAUX / KPI (CUMUL OFFICE + MASTER) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-[#0052cc] text-white p-5 rounded-xl shadow-sm">
                <span class="text-xs font-semibold uppercase opacity-80">📌 Total tâches (Global)</span>
                <div class="text-3xl font-bold mt-2">{{ $totalTasks }}</div>
            </div>

            <div class="bg-amber-400 text-gray-900 p-5 rounded-xl shadow-sm">
                <span class="text-xs font-semibold uppercase opacity-80">🕒 À faire</span>
                <div class="text-3xl font-bold mt-2">{{ $todoTasks }}</div>
            </div>

            <div class="bg-sky-500 text-white p-5 rounded-xl shadow-sm">
                <span class="text-xs font-semibold uppercase opacity-80">⚙️ En cours</span>
                <div class="text-3xl font-bold mt-2">{{ $doingTasks }}</div>
            </div>

            <div class="bg-emerald-600 text-white p-5 rounded-xl shadow-sm">
                <span class="text-xs font-semibold uppercase opacity-80">✅ Terminées</span>
                <div class="text-3xl font-bold mt-2">{{ $doneTasks }}</div>
            </div>
        </div>

        <!-- SUIVI ET TAUX D'AVANCEMENT GLOBAL -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-2">
                <h2 class="font-bold text-gray-900 text-base">🔥 Tâches activement suivies (Office + Master)</h2>
                <p class="text-xs text-gray-500">Ensemble des tâches en cadrage ou en cours</p>
                <div class="text-3xl font-bold text-gray-900 pt-2">{{ $todoTasks + $doingTasks }}</div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-2">
                <h2 class="font-bold text-gray-900 text-base">📈 Taux d’avancement Global</h2>
                @php
                    $rate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
                @endphp
                <div class="text-3xl font-bold text-emerald-600 pt-2">{{ $rate }}%</div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden mt-2">
                    <div class="bg-emerald-600 h-3 rounded-full transition-all duration-300" style="width: {{ $rate }}%;"></div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- VUE 2 : MODE OFFICE (ACTIVITÉS OFFICE)     -->
    <!-- ========================================== -->
    @if($view === 'office')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <span>Accueil</span> / <span class="text-gray-600 font-medium">Cockpit d'analyse</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                💼 OFFICE Cockpit Livrables 
                <span class="text-xs bg-blue-50 text-[#0052cc] px-2.5 py-0.5 rounded-full font-semibold border border-blue-100">
                    🇸🇳 Dakar, Sénégal
                </span>
            </h1>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        📊 Activités du mode OFFICE
                    </h2>
                    <span class="text-xs text-gray-400 font-medium">{{ $officeTasks->count() }} tâche(s) enregistrée(s)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-[#f8fafc] text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">Rang</th>
                            <th class="px-4 py-3">Détails de l'activité</th>
                            <th class="px-4 py-3">Statut / Catégorie</th>
                            <th class="px-4 py-3">Échéance</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($officeTasks as $index => $task)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-400">#{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-900">{{ $task->title }}</div>
                                    @if($task->document_link)
                                        <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-[#0052cc] underline block mt-0.5">🔗 Document de travail</a>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        Projet: {{ $task->project->title ?? 'Aucun' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 space-y-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="bg-blue-50 text-[#0052cc] text-xs font-semibold px-2.5 py-0.5 rounded-full border border-blue-100">
                                            {{ $task->category->title ?? $task->category->name ?? 'CGP' }}
                                        </span>
                                        @if(($task->document_status ?? $task->status) === 'done')
                                            <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded border border-emerald-200">🟢 Validé</span>
                                        @elseif(($task->document_status ?? $task->status) === 'in_progress')
                                            <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded border border-amber-200">🟡 En cours</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded border border-gray-200">🔴 Cadrage</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded border 
                                            {{ $task->priority === 'high' ? 'bg-red-50 text-red-600 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                            Priorité: {{ ucfirst($task->priority ?? 'Medium') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-xs text-gray-500">
                                    {{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Supprimer ce livrable ?')" class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <div class="font-semibold text-gray-600">Aucune activité enregistrée en Mode Office</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- VUE 3 : MODE MASTER (ACTIVITÉS MASTER)     -->
    <!-- ========================================== -->
    @if($view === 'master')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <span>Accueil</span> / <span class="text-gray-600 font-medium">Cockpit d'analyse</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                🎓 MASTER Cockpit Livrables 
                <span class="text-xs bg-blue-50 text-[#0052cc] px-2.5 py-0.5 rounded-full font-semibold border border-blue-100">
                    🇸🇳 Dakar, Sénégal
                </span>
            </h1>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        📊 Activités du mode MASTER
                    </h2>
                    <span class="text-xs text-gray-400 font-medium">{{ $masterTasks->count() }} tâche(s) enregistrée(s)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-[#f8fafc] text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">Rang</th>
                            <th class="px-4 py-3">Détails de l'activité</th>
                            <th class="px-4 py-3">Statut / Catégorie</th>
                            <th class="px-4 py-3">Échéance</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($masterTasks as $index => $task)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-400">#{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-900">{{ $task->title }}</div>
                                    @if($task->document_link)
                                        <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-[#0052cc] underline block mt-0.5">🔗 Document de travail</a>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        Projet: {{ $task->project->title ?? 'Aucun' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 space-y-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="bg-purple-50 text-purple-700 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-purple-100">
                                            {{ $task->category->title ?? $task->category->name ?? 'Master ISEF1' }}
                                        </span>
                                        @if(($task->document_status ?? $task->status) === 'done')
                                            <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded border border-emerald-200">🟢 Validé</span>
                                        @elseif(($task->document_status ?? $task->status) === 'in_progress')
                                            <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded border border-amber-200">🟡 En cours</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded border border-gray-200">🔴 Cadrage</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded border 
                                            {{ $task->priority === 'high' ? 'bg-red-50 text-red-600 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                            Priorité: {{ ucfirst($task->priority ?? 'Medium') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-xs text-gray-500">
                                    {{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Supprimer ce livrable ?')" class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <div class="font-semibold text-gray-600">Aucune activité enregistrée en Mode Master</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection