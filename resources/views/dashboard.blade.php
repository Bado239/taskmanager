@extends('layouts.app')

@section('content')
@php
    $defaultCatOffice = $categories->firstWhere('title', 'CGP')?->id ?? $categories->firstWhere('name', 'CGP')?->id ?? '';
    $defaultCatMaster = $categories->firstWhere('title', 'Master ISEF1')?->id ?? $categories->firstWhere('name', 'Master ISEF1')?->id ?? '';
    
    $currentType = $view === 'dashboard' ? 'office' : $view;
@endphp

<div class="space-y-6 pb-12">

    <!-- BARRE DE NAVIGATION / BOUTON NOUVELLE TÂCHE EN HAUT -->
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

        <button onclick="toggleTaskForm()" id="btnToggleForm" type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition-colors shadow-sm flex items-center gap-2">
            <span>+ Nouvelle tâche 
                @if($view === 'office') (Mode Office)
                @elseif($view === 'master') (Mode Master)
                @else (Global)
                @endif
            </span>
        </button>
    </div>

    <!-- FORMULAIRE DE CRÉATION DE TÂCHE (CACHÉ PAR DÉFAUT) -->
    <div id="taskFormContainer" class="bg-white p-6 rounded-xl border border-emerald-200 shadow-md" style="display: none;">
        
        <div class="mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
                ➕ Enregistrer une tâche 
                <span id="formTitleContext" class="text-[#0052cc]">
                    @if($view === 'dashboard') en Mode Global (Sélectionnez le type ci-dessous)
                    @else en Mode {{ strtoupper($view) }}
                    @endif
                </span>
            </h2>
            <button onclick="toggleTaskForm()" type="button" class="text-gray-400 hover:text-gray-600 text-xs font-bold">✕ Fermer</button>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ selectedProject: '', selectedCategory: '' }">
            @csrf
            
            @if($view === 'dashboard')
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Destination de la Tâche *</label>
                <select name="type" required class="w-full bg-blue-50 border border-blue-200 text-[#0052cc] font-bold rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="office">💼 Mode Office</option>
                    <option value="master">🎓 Mode Master</option>
                </select>
            </div>
            @else
                <input type="hidden" name="type" value="{{ $view }}">
            @endif

            {{-- 1. PROJET / MATIÈRE --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">1. Projet / Matière *</label>
                <div class="flex gap-2">
                    <select name="project_id" x-model="selectedProject" required class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                        <option value="">-- Sélectionner --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                        <option value="new">➕ Créer un nouveau...</option>
                    </select>

                    {{-- Bouton pour supprimer le projet sélectionné --}}
                    <template x-if="selectedProject && selectedProject !== 'new'">
                        <button type="button" @click="if(confirm('Voulez-vous vraiment supprimer ce projet ?')) { document.getElementById('delete-project-' + selectedProject).submit(); }" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-lg text-xs font-bold transition-colors" title="Supprimer ce projet">
                            🗑️
                        </button>
                    </template>
                </div>

                <div x-show="selectedProject === 'new'" style="display: none;" class="mt-2">
                    <input type="text" name="new_project_name" placeholder="Nom du projet ou de la matière..."
                           class="w-full bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>
            </div>

            {{-- 2. ÉTAPE / LEÇON --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">2. Étape / Leçon *</label>
                <div class="flex gap-2">
                    <select name="category_id" x-model="selectedCategory" required class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                        <option value="">-- Sélectionner --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->title ?? $category->name }}
                            </option>
                        @endforeach
                        <option value="new">➕ Créer une nouvelle...</option>
                    </select>

                    {{-- Bouton pour supprimer l'étape sélectionnée --}}
                    <template x-if="selectedCategory && selectedCategory !== 'new'">
                        <button type="button" @click="if(confirm('Voulez-vous vraiment supprimer cette étape ?')) { document.getElementById('delete-category-' + selectedCategory).submit(); }" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-lg text-xs font-bold transition-colors" title="Supprimer cette étape">
                            🗑️
                        </button>
                    </template>
                </div>

                <div x-show="selectedCategory === 'new'" style="display: none;" class="mt-2">
                    <input type="text" name="new_category_name" placeholder="Nom de l'étape ou de la leçon..."
                           class="w-full bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>
            </div>

            {{-- 3. LIBELLÉ DE LA TÂCHE --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1">3. Libellé de la Tâche *</label>
                <input type="text" name="title" required placeholder="Ex: Résolution de l'exercice d'économétrie"
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            {{-- LIEN DE TRAVAIL --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Lien de Travail</label>
                <input type="url" name="document_link" placeholder="https://..."
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            {{-- DATES ET HEURES --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Échéance</label>
                <input type="date" name="date_prevue" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Date d'exécution</label>
                <input type="date" name="execution_date" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Heure de début</label>
                <input type="time" name="start_time" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Heure de fin</label>
                <input type="time" name="end_time" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            {{-- ÉTAT & URGENCE --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">État du Livrable</label>
                <select name="document_status" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="todo">🔴 Phase 1 : Cadrage</option>
                    <option value="in_progress" selected>🟡 Phase 2 : En cours</option>
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

            <div class="md:col-span-2 pt-2">
                <button type="submit" class="bg-[#0052cc] hover:bg-[#003d99] text-white font-bold text-sm py-2.5 px-6 rounded-lg transition-colors shadow-sm w-full">
                    💾 Valider et Enregistrer la tâche
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleTaskForm() {
            const formContainer = document.getElementById('taskFormContainer');
            const btn = document.getElementById('btnToggleForm');
            const currentView = "{{ $view }}";
            let labelText = "+ Nouvelle tâche";
            if (currentView === 'office') labelText += " (Mode Office)";
            else if (currentView === 'master') labelText += " (Mode Master)";

            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                btn.innerHTML = '<span>✕ Fermer</span>';
                formContainer.scrollIntoView({ behavior: 'smooth' });
            } else {
                formContainer.style.display = 'none';
                btn.innerHTML = '<span>' + labelText + '</span>';
            }
        }
    </script>

    <!-- ================= VUE : DASHBOARD GLOBAL ================= -->
    @if($view === 'dashboard')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                📊 Tableau de bord Global
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <!-- Total Actives -->
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'total']) }}" class="bg-blue-50 hover:bg-blue-100 border border-blue-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'total' ? 'ring-2 ring-[#0052cc]' : '' }}">
                    <div class="text-xs text-blue-600 font-semibold">Total Actives</div>
                    <div class="text-2xl font-bold text-[#0052cc]">{{ $totalTasks }}</div>
                </a>
                <!-- À faire -->
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'todo']) }}" class="bg-red-50 hover:bg-red-100 border border-red-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'todo' ? 'ring-2 ring-red-500' : '' }}">
                    <div class="text-xs text-red-600 font-semibold">À faire</div>
                    <div class="text-2xl font-bold text-red-600">{{ $todoTasks }}</div>
                </a>
                <!-- En cours -->
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'doing']) }}" class="bg-amber-50 hover:bg-amber-100 border border-amber-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'doing' ? 'ring-2 ring-amber-500' : '' }}">
                    <div class="text-xs text-amber-600 font-semibold">En cours</div>
                    <div class="text-2xl font-bold text-amber-600">{{ $doingTasks }}</div>
                </a>
                <!-- Validées -->
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'done']) }}" class="bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'done' ? 'ring-2 ring-emerald-500' : '' }}">
                    <div class="text-xs text-emerald-600 font-semibold">Validées</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $doneTasks }}</div>
                </a>
                <!-- Office Aujourd'hui -->
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'office_today']) }}" class="bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'office_today' ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="text-xs text-indigo-600 font-semibold">Office (Aujourd'hui)</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ $officeTodayCount }}</div>
                </a>
                <!-- Master Aujourd'hui -->
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'master_today']) }}" class="bg-purple-50 hover:bg-purple-100 border border-purple-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'master_today' ? 'ring-2 ring-purple-500' : '' }}">
                    <div class="text-xs text-purple-600 font-semibold">Master (Aujourd'hui)</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $masterTodayCount }}</div>
                </a>
                <!-- Archives -->
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'archived']) }}" class="bg-gray-100 hover:bg-gray-200 border border-gray-200 p-4 rounded-xl text-center transition-all block {{ $indicator === 'archived' ? 'ring-2 ring-gray-600' : '' }}">
                    <div class="text-xs text-gray-600 font-semibold">Archives</div>
                    <div class="text-2xl font-bold text-gray-700">{{ $archivedCount }}</div>
                </a>
            </div>
        </div>

        {{-- Affichage de la liste des tâches si un indicateur a été cliqué --}}
        @if($indicator)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                        📋 Résultats pour l'indicateur : 
                        <span class="text-[#0052cc] uppercase">{{ str_replace('_', ' ', $indicator) }}</span>
                    </h3>
                    <a href="{{ route('dashboard', ['view' => 'dashboard']) }}" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-3 py-1 rounded-lg">
                        ✕ Fermer la liste
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-[#f8fafc] text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Projet / Matière</th>
                                <th class="px-4 py-3">Libellé de la Tâche</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3">Date d'exécution</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($globalIndicatorTasks as $task)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 font-bold text-xs">
                                        @if($task->type === 'office')
                                            <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded border border-blue-200">Office</span>
                                        @else
                                            <span class="bg-purple-50 text-purple-600 px-2 py-1 rounded border border-purple-200">Master</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 font-semibold text-gray-800">
                                        {{ $task->project->title ?? 'Général' }}
                                    </td>
                                    <td class="px-4 py-4 font-bold text-gray-900">
                                        {{ $task->title }}
                                        @if($task->document_link)
                                            <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-[#0052cc] underline block mt-0.5">🔗 Document</a>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($task->is_archived)
                                            <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-0.5 rounded border border-gray-200">📁 Archivée</span>
                                        @elseif($task->document_status === 'done')
                                            <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded border border-emerald-200">🟢 Validé</span>
                                        @elseif($task->document_status === 'in_progress')
                                            <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded border border-amber-200">🟡 En cours</span>
                                        @else
                                            <span class="bg-red-50 text-red-700 text-xs font-semibold px-2 py-0.5 rounded border border-red-200">🔴 À faire</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-xs text-gray-600">
                                        {{ $task->execution_date ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y') : 'Non planifié' }}
                                    </td>
                                    <td class="px-4 py-4 text-right space-x-2">
                                        @if(!$task->is_archived)
                                            <form action="{{ route('tasks.archive', $task->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">Archiver</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Supprimer cette tâche ?')" class="text-xs text-red-500 hover:text-red-700 font-semibold">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 font-semibold">
                                        Aucune tâche trouvée pour cet indicateur.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    <!-- ================= VUE : MODE OFFICE ================= -->
    @if($view === 'office')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                💼 OFFICE Cockpit Livrables 
                <span class="text-xs bg-blue-50 text-[#0052cc] px-2.5 py-0.5 rounded-full font-semibold border border-blue-100">
                    🇸🇳 Dakar, Sénégal
                </span>
            </h1>

            <!-- Barres de filtres -->
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard', ['view' => 'office', 'filter' => 'all', 'status' => 'active']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ $statusFilter === 'active' && $filter === 'all' ? 'bg-[#0052cc] text-white border-[#0052cc]' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                   Toutes Actives
                </a>
                <a href="{{ route('dashboard', ['view' => 'office', 'filter' => 'today', 'status' => 'active']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ $statusFilter === 'active' && $filter === 'today' ? 'bg-[#0052cc] text-white border-[#0052cc]' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                   📅 Aujourd'hui
                </a>
                <a href="{{ route('dashboard', ['view' => 'office', 'status' => 'archived']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ $statusFilter === 'archived' ? 'bg-gray-700 text-white border-gray-700' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                   📁 Archives
                </a>
            </div>
        </div>

        @php
            $groupedOfficeTasks = $officeTasks->groupBy(function($task) {
                return $task->project->title ?? 'Sans Projet';
            });
        @endphp

        <div class="space-y-6">
            @forelse($groupedOfficeTasks as $projectName => $tasksInProject)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                            📁 Projet : <span class="text-[#0052cc]">{{ $projectName }}</span>
                        </h3>
                        <span class="text-xs bg-blue-100 text-[#0052cc] font-bold px-2.5 py-1 rounded-full">
                            {{ $tasksInProject->count() }} tâche(s)
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-[#f8fafc] text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Rang</th>
                                    <th class="px-4 py-3">Étape & Libellé</th>
                                    <th class="px-4 py-3">Statut / Priorité</th>
                                    <th class="px-4 py-3">Planification</th>
                                    <th class="px-4 py-3">Échéance</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($tasksInProject as $index => $task)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 font-bold text-gray-400">#{{ $index + 1 }}</td>
                                        <td class="px-4 py-4">
                                            <div class="text-xs font-semibold text-blue-600 mb-0.5">
                                                📌 Étape : {{ $task->category->title ?? $task->category->name ?? 'Général' }}
                                            </div>
                                            <div class="font-bold text-gray-900">{{ $task->title }}</div>
                                            @if($task->document_link)
                                                <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-[#0052cc] underline block mt-0.5">🔗 Document / Support</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 space-y-1">
                                            <div>
                                                @if($task->document_status === 'done')
                                                    <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2 py-0.5 rounded border border-emerald-200">🟢 Validé</span>
                                                @elseif($task->document_status === 'in_progress')
                                                    <span class="bg-amber-50 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded border border-amber-200">🟡 En cours</span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded border border-gray-200">🔴 Cadrage</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-xs text-gray-600">
                                            <div>📅 {{ $task->execution_date ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y') : 'Non planifié' }}</div>
                                            @if($task->heure_debut || $task->heure_fin)
                                                <div class="text-gray-500 font-semibold">⏰ {{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i' ) : '--:--' }} à {{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '--:--' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-gray-500">
                                            {{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-right space-x-2">
                                            @if(!$task->is_archived)
                                                <form action="{{ route('tasks.archive', $task->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">Archiver</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Supprimer ce livrable ?')" class="text-xs text-red-500 hover:text-red-700 font-semibold">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 text-center rounded-xl border border-gray-200 shadow-sm text-gray-600 font-semibold">
                    Aucune activité enregistrée en Mode Office
                </div>
            @endforelse
        </div>
    @endif

    <!-- ================= VUE : MODE MASTER ================= -->
    @if($view === 'master')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                🎓 MASTER Cockpit - Suivi Académique
            </h1>

            <!-- Barres de filtres -->
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard', ['view' => 'master', 'filter' => 'all', 'status' => 'active']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ $statusFilter === 'active' && $filter === 'all' ? 'bg-[#0052cc] text-white border-[#0052cc]' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                   Toutes Actives
                </a>
                <a href="{{ route('dashboard', ['view' => 'master', 'filter' => 'today', 'status' => 'active']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ $statusFilter === 'active' && $filter === 'today' ? 'bg-[#0052cc] text-white border-[#0052cc]' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                   📅 Aujourd'hui
                </a>
                <a href="{{ route('dashboard', ['view' => 'master', 'status' => 'archived']) }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ $statusFilter === 'archived' ? 'bg-gray-700 text-white border-gray-700' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                   📁 Archives
                </a>
            </div>
        </div>

        @php
            $groupedMasterTasks = $masterTasks->groupBy(function($task) {
                return $task->project->title ?? 'Matière Non Spécifiée';
            });
        @endphp

        <div class="space-y-6">
            @forelse($groupedMasterTasks as $matiereName => $tasksInMatiere)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                            📚 Matière : <span class="text-[#0052cc]">{{ $matiereName }}</span>
                        </h3>
                        <span class="text-xs bg-blue-100 text-[#0052cc] font-bold px-2.5 py-1 rounded-full">
                            {{ $tasksInMatiere->count() }} tâche(s)
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-[#f8fafc] text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Rang</th>
                                    <th class="px-4 py-3">Leçon / Étape</th>
                                    <th class="px-4 py-3">Libellé de la Tâche</th>
                                    <th class="px-4 py-3">Date d'exécution & Heures</th>
                                    <th class="px-4 py-3">Échéance</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($tasksInMatiere as $index => $task)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 font-bold text-gray-400">#{{ $index + 1 }}</td>
                                        <td class="px-4 py-4 font-semibold text-blue-600 text-xs">
                                            📖 {{ $task->category->title ?? $task->category->name ?? 'Général' }}
                                        </td>
                                        <td class="px-4 py-4 font-bold text-gray-900">
                                            {{ $task->title }}
                                            @if($task->document_link)
                                                <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-[#0052cc] underline block mt-0.5">🔗 Document / Support</a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-gray-600">
                                            <div>📅 {{ $task->execution_date ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y') : 'Non planifié' }}</div>
                                            @if($task->heure_debut || $task->heure_fin)
                                                <div class="text-gray-500 font-semibold mt-0.5">⏰ {{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i') : '--:--' }} à {{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '--:--' }}</div>
                                            @else
                                                <div class="text-gray-400 mt-0.5">⏰ --:--</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-xs text-gray-500">
                                            {{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-right space-x-2">
                                            @if(!$task->is_archived)
                                                <form action="{{ route('tasks.archive', $task->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">Archiver</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Supprimer cette tâche ?')" class="text-xs text-red-500 hover:text-red-700 font-semibold">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 text-center rounded-xl border border-gray-200 shadow-sm font-semibold text-gray-600">
                    Aucune activité enregistrée en Mode Master
                </div>
            @endforelse
        </div>
    @endif

</div>

{{-- Formulaires cachés pour la suppression classique des projets et catégories --}}
@foreach($projects as $p)
    <form id="delete-project-{{ $p->id }}" action="{{ route('projects.destroy', $p->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@foreach($categories as $c)
    <form id="delete-category-{{ $c->id }}" action="{{ route('categories.destroy', $c->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

{{-- SIGNATURE EN BAS DE CHAQUE PAGE --}}
<div class="mt-12 pt-4 border-t border-gray-200 text-center text-xs text-gray-400 font-medium">
    Bado
</div>

@endsection