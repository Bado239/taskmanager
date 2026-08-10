@extends('layouts.app')

@section('content')
@php
    $currentType = $view === 'dashboard' ? 'office' :$view;
    // Fusion et déduplication des projets et catégories pour les deux modes
    $allProjects =$projectsOffice->concat($projectsMaster)->unique('id');$allCategories = $categoriesOffice->concat($categoriesMaster)->unique('id');

    // Calcul initial des dates et heures par défaut (Échéance J+4, Exécution J+2)
    $today = \Carbon\Carbon::now();
    $defaultDueDate =$today->copy()->addDays(4)->format('Y-m-d');
    $defaultExecutionDate =$today->copy()->addDays(2)->format('Y-m-d');
    $defaultStartTime = '09:00';$defaultEndTime = '11:00';
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
            <a href="{{ route('dashboard', ['view' => 'personal']) }}" 
            class="px-4 py-2 font-bold text-xs rounded-lg transition-all {{ $view === 'personal' ? 'bg-[#0052cc] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                🌱 Perso
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
<div id="taskFormContainer"
     class="bg-white p-6 rounded-xl border border-emerald-200 shadow-md"
     style="display: none;">

    <div class="mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
            ➕ Enregistrer une tâche
            <span class="text-[#0052cc]">
                @if($view === 'dashboard')
                    en Mode Global
                @else
                    en Mode {{ strtoupper($view) }}
                @endif
            </span>
        </h2>

        <button onclick="toggleTaskForm()" type="button"
                class="text-gray-400 hover:text-gray-600 text-xs font-bold">
            ✕ Fermer
        </button>
    </div>

    <form action="{{ route('tasks.store') }}"
        method="POST"
        class="grid grid-cols-1 md:grid-cols-2 gap-4"
        x-data="{
            currentMode: '{{ $currentType }}',
            selectedProject: '',
            selectedCategory: '',
            dueDate: '{{ $defaultDueDate }}',
            executionDate: '{{ $defaultExecutionDate }}',
            startTime: '{{ $defaultStartTime }}',
            endTime: '{{ $defaultEndTime }}',

            projects: @js($allProjects->where('is_active', true)->values()),
            categories: @js($allCategories->values()),

            get filteredProjects() {
                return this.projects.filter(project =>
                    project.type === this.currentMode
                );
            },

            get filteredCategories() {
                return this.categories.filter(category =>
                    category.type === this.currentMode
                );
            },

            updateExecutionDate() {
                if (!this.dueDate) return;
                const date = new Date(this.dueDate);
                date.setDate(date.getDate() - 2);
                this.executionDate = date.toISOString().split('T')[0];
            },

            updateEndTime() {
                if (!this.startTime) return;
                const parts = this.startTime.split(':');
                let hours = parseInt(parts[0], 10) + 2;
                if (hours > 23) hours = 23;
                this.endTime = String(hours).padStart(2, '0') + ':' + parts[1];
            },

            async deleteProject(projectId) {
                if (!confirm('Voulez-vous vraiment archiver ce projet ?')) return;

                try {
                    const response = await fetch(`/projects/${projectId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Erreur lors de la suppression.');
                    }

                    this.projects = this.projects.filter(project => String(project.id) !== String(projectId));

                    if (String(this.selectedProject) === String(projectId)) {
                        this.selectedProject = '';
                    }

                    alert(data.message || 'Projet archivé avec succès.');
                } catch (error) {
                    alert(error.message || 'Une erreur est survenue.');
                }
            },

            async deleteCategory(categoryId) {
                if (!confirm('Voulez-vous vraiment supprimer cette étape ?')) return;

                try {
                    const response = await fetch(`/categories/${categoryId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Erreur lors de la suppression.');
                    }

                    this.categories = this.categories.filter(category => String(category.id) !== String(categoryId));

                    if (String(this.selectedCategory) === String(categoryId)) {
                        this.selectedCategory = '';
                    }

                    alert(data.message || 'Étape supprimée avec succès.');
                } catch (error) {
                    alert(error.message || 'Une erreur est survenue.');
                }
            }
        }">

        @csrf

        {{-- DESTINATION --}}
        @if($view === 'dashboard')
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Destination de la Tâche *
                </label>
                <select x-model="currentMode"
                        required
                        class="w-full bg-blue-50 border border-blue-200 text-[#0052cc] font-bold rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="office">💼 Mode Office</option>
                    <option value="master">🎓 Mode Master</option>
                </select>
                <input type="hidden" name="type" :value="currentMode">
            </div>
        @else
            <input type="hidden" name="type" value="{{ $view }}">
        @endif

        {{-- 1. PROJET / MATIÈRE --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
                1. Projet / Matière *
            </label>
            <div class="flex gap-2">
                <select name="project_id"
                        x-model="selectedProject"
                        required
                        class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="">-- Sélectionner --</option>
                    <template x-for="project in filteredProjects" :key="project.id">
                        <option :value="project.id" x-text="project.title"></option>
                    </template>
                    <option value="new">➕ Créer un nouveau...</option>
                </select>

                <template x-if="selectedProject && selectedProject !== 'new'">
                    <button type="button"
                            @click="deleteProject(selectedProject)"
                            class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-lg text-xs font-bold">
                        🗑️
                    </button>
                </template>
            </div>

            <div x-show="selectedProject === 'new'" class="mt-2">
                <input type="text"
                    name="new_project_name"
                    placeholder="Nom du projet ou de la matière..."
                    class="w-full bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        {{-- 2. CATÉGORIE (Étape / Leçon) --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
                2. Étape / Leçon *
            </label>
            <div class="flex gap-2">
                <select name="category_id"
                        x-model="selectedCategory"
                        required
                        class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="">-- Sélectionner --</option>
                    <template x-for="category in filteredCategories" :key="category.id">
                        <option :value="category.id" x-text="category.title || category.name"></option>
                    </template>
                    <option value="new">➕ Créer une nouvelle...</option>
                </select>

                <template x-if="selectedCategory && selectedCategory !== 'new'">
                    <button type="button"
                            @click="deleteCategory(selectedCategory)"
                            class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-lg text-xs font-bold">
                        🗑️
                    </button>
                </template>
            </div>

            <div x-show="selectedCategory === 'new'" class="mt-2">
                <input type="text"
                    name="new_category_name"
                    placeholder="Nom de l'étape ou de la leçon..."
                    class="w-full bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-sm">
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

            {{-- DATES ET HEURES LIÉES REACTIVEMENT VIA ALPINE.JS --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Échéance</label>
                <input type="date" name="date_prevue" x-model="dueDate" @change="updateExecutionDate()" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Date d'exécution</label>
                <input type="date" name="execution_date" x-model="executionDate" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Heure de début</label>
                <input type="time" name="start_time" x-model="startTime" @change="updateEndTime()" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Heure de fin</label>
                <input type="time" name="end_time" x-model="endTime" 
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
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'total']) }}" class="bg-blue-50 hover:bg-blue-100 border border-blue-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'total' ? 'ring-2 ring-[#0052cc]' : '' }}">
                    <div class="text-xs text-blue-600 font-semibold">Total Actives</div>
                    <div class="text-2xl font-bold text-[#0052cc]">{{ $totalTasks }}</div>
                </a>
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'todo']) }}" class="bg-red-50 hover:bg-red-100 border border-red-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'todo' ? 'ring-2 ring-red-500' : '' }}">
                    <div class="text-xs text-red-600 font-semibold">À faire</div>
                    <div class="text-2xl font-bold text-red-600">{{ $todoTasks }}</div>
                </a>
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'doing']) }}" class="bg-amber-50 hover:bg-amber-100 border border-amber-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'doing' ? 'ring-2 ring-amber-500' : '' }}">
                    <div class="text-xs text-amber-600 font-semibold">En cours</div>
                    <div class="text-2xl font-bold text-amber-600">{{ $doingTasks }}</div>
                </a>
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'done']) }}" class="bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'done' ? 'ring-2 ring-emerald-500' : '' }}">
                    <div class="text-xs text-emerald-600 font-semibold">Validées</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $doneTasks }}</div>
                </a>
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'office_today']) }}" class="bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'office_today' ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="text-xs text-indigo-600 font-semibold">Office (Aujourd'hui)</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ $officeTodayCount }}</div>
                </a>
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'master_today']) }}" class="bg-purple-50 hover:bg-purple-100 border border-purple-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'master_today' ? 'ring-2 ring-purple-500' : '' }}">
                    <div class="text-xs text-purple-600 font-semibold">Master (Aujourd'hui)</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $masterTodayCount }}</div>
                </a>
                <a href="{{ route('dashboard', ['view' => 'dashboard', 'indicator' => 'archived']) }}" class="bg-gray-100 hover:bg-gray-200 border border-gray-200 p-4 rounded-xl text-center transition-all block {{ $indicator === 'archived' ? 'ring-2 ring-gray-600' : '' }}">
                    <div class="text-xs text-gray-600 font-semibold">Archives</div>
                    <div class="text-2xl font-bold text-gray-700">{{ $archivedCount }}</div>
                </a>
            </div>
        </div>

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
                                        {{ $task->project->title ?? $task->project_name ?? 'Général' }}
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
                return $task->project->title ?? $task->project_name ?? 'Sans Projet';
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
                return $task->project->title ?? $task->project_name ?? 'Matière Non Spécifiée';
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

<!-- ================= VUE : DÉVELOPPEMENT PERSONNEL ================= -->
    @if($view === 'personal')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                🌱 Espace Développement Personnel
            </h1>
            <p class="text-sm text-gray-500 mt-2">Maîtrise de la langue française, culture générale et lectures enrichissantes.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Section Livres -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">📚 Bibliothèque à lire</h3>
                
                <!-- Affichage des erreurs de validation -->
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-md">
                        <p class="font-bold text-red-700">⚠️ Oups ! Une erreur est survenue :</p>
                        <ul class="list-disc list-inside text-red-600 text-sm mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Affichage du succès -->
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-r-md text-green-700">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <!-- Formulaire d'ajout de livre avec Upload PDF (CORRIGÉ) -->
                <form action="{{ route('personal-resources.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 space-y-2">
                    @csrf 
                    <input type="hidden" name="type" value="book">
                    
                    <input type="text" name="title" placeholder="Titre du livre" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    <input type="text" name="author_or_source" placeholder="Auteur (optionnel)" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    
                    <!-- Champ Upload PDF -->
                    <input type="file" name="pdf_file" accept="application/pdf" class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                    
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded px-3 py-2 text-sm transition">+ Ajouter le livre (PDF)</button>
                </form>

                <!-- Liste des livres -->
                @if($personalResources->where('type', 'book')->isNotEmpty())
                    <ul class="space-y-3 divide-y divide-gray-100">
                        @foreach($personalResources->where('type', 'book') as $book)
                            <li class="pt-3 flex justify-between items-start">
                                <div>
                                    <a href="{{ route('personal-resources.show', $book->id) }}" class="font-semibold text-blue-700 hover:text-blue-900 text-sm hover:underline">{{ $book->title }}</a>
                                    <p class="text-xs text-gray-500 mt-1">{{ $book->author_or_source }}</p>
                                </div>
                                <form action="{{ route('personal-resources.destroy', $book->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs ml-2">✕</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4 text-gray-400 text-sm italic">Aucun livre ajouté pour le moment.</div>
                @endif
            </div>

            <!-- Section Vocabulaire -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">✍️ Vocabulaire & Français</h3>
                
                <!-- Formulaire d'ajout de mot -->
                <form action="{{ route('personal-resources.store') }}" method="POST" class="mb-4 space-y-2">
                    @csrf
                    <input type="hidden" name="type" value="vocab">
                    <input type="text" name="title" placeholder="Mot ou expression" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-green-500" required>
                    <textarea name="description" placeholder="Définition ou exemple d'utilisation..." rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-green-500"></textarea>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold rounded px-3 py-2 text-sm transition">+ Ajouter un mot</button>
                </form>

                <!-- Liste des mots -->
                @if($personalResources->where('type', 'vocab')->isNotEmpty())
                    <ul class="space-y-3 divide-y divide-gray-100">
                        @foreach($personalResources->where('type', 'vocab') as $word)
                            <li class="pt-3 flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 text-sm">{{ $word->title }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $word->description }}</p>
                                </div>
                                <form action="{{ route('personal-resources.destroy', $word->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs ml-2">✕</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4 text-gray-400 text-sm italic">Ajoute des mots pour enrichir ton expression.</div>
                @endif
            </div>

        </div>
    @endif
    
    {{-- Formulaires de suppression dynamiques pour tous les projets et catégories fusionnés --}}

@foreach($allCategories as $c)
    <form id="delete-category-{{ $c->id }}" action="{{ route('categories.destroy', $c->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

<footer class="mt-20 pb-8 text-center text-sm text-slate-600 font-medium">
    <div class="inline-flex flex-col sm:flex-row items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-slate-100/70 border border-slate-200/80 shadow-sm backdrop-blur-sm">
        <span class="text-slate-700">Laravel Task Manager &copy; {{ date('Y') }}</span>
        <span class="hidden sm:inline text-slate-300">•</span>
        <span>Conçu avec <span class="text-red-500">❤️</span> par <strong class="text-slate-900 font-semibold">Bado</strong></span>
    </div>
</footer>
@endsection