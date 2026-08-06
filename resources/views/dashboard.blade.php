@extends('layouts.app')

@section('content')
@php
    $defaultCatOffice = $categories->firstWhere('title', 'CGP')?->id ?? $categories->firstWhere('name', 'CGP')?->id ?? '';
    $defaultCatMaster = $categories->firstWhere('title', 'Master ISEF1')?->id ?? $categories->firstWhere('name', 'Master ISEF1')?->id ?? '';
    
    $currentType = $view === 'dashboard' ? 'office' : $view;
@endphp

<div class="space-y-6">

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
                <select name="project_id" x-model="selectedProject" required class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="">-- Sélectionner --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                    <option value="new">➕ Créer un nouveau...</option>
                </select>

                <div x-show="selectedProject === 'new'" style="display: none;" class="mt-2">
                    <input type="text" name="new_project_name" placeholder="Nom du projet ou de la matière..."
                           class="w-full bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>
            </div>

            {{-- 2. ÉTAPE / LEÇON --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">2. Étape / Leçon *</label>
                <select name="category_id" x-model="selectedCategory" required class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="">-- Sélectionner --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->title ?? $category->name }}
                        </option>
                    @endforeach
                    <option value="new">➕ Créer une nouvelle...</option>
                </select>

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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl text-center">
                    <div class="text-xs text-blue-600 font-semibold">Total Tâches</div>
                    <div class="text-2xl font-bold text-[#0052cc]">{{ $totalTasks }}</div>
                </div>
                <div class="bg-red-50 border border-red-100 p-4 rounded-xl text-center">
                    <div class="text-xs text-red-600 font-semibold">Cadrage / À faire</div>
                    <div class="text-2xl font-bold text-red-600">{{ $todoTasks }}</div>
                </div>
                <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl text-center">
                    <div class="text-xs text-amber-600 font-semibold">En cours</div>
                    <div class="text-2xl font-bold text-amber-600">{{ $doingTasks }}</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-center">
                    <div class="text-xs text-emerald-600 font-semibold">Validées</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $doneTasks }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= VUE : MODE OFFICE ================= -->
    @if($view === 'office')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                💼 OFFICE Cockpit Livrables 
                <span class="text-xs bg-blue-50 text-[#0052cc] px-2.5 py-0.5 rounded-full font-semibold border border-blue-100">
                    🇸🇳 Dakar, Sénégal
                </span>
            </h1>
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
                                        <td class="px-4 py-4 text-right">
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
                <div class="bg-white p-12 text-center rounded-xl border border-gray-200 shadow-sm text-gray-400 font-semibold text-gray-600">
                    Aucune activité enregistrée en Mode Office
                </div>
            @endforelse
        </div>
    @endif

    <!-- ================= VUE : MODE MASTER ================= -->
    @if($view === 'master')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                🎓 MASTER Cockpit - Suivi Académique
            </h1>
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
                                        <td class="px-4 py-4 text-right">
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
@endsection