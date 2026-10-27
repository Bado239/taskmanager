@extends('layouts.app')

@section('content')

{{-- RÉSULTATS DE RECHERCHE --}}
@if(request()->filled('search'))
    <div class="mb-6 flex flex-col gap-4 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-xl">
                🔎
            </div>

            <div>
                <p class="text-sm font-bold text-slate-900">
                    Résultats de recherche
                </p>

                <p class="mt-1 text-sm text-slate-600">
                    Recherche :
                    <span class="font-semibold text-blue-700">
                        {{ request('search') }}
                    </span>
                </p>
            </div>
        </div>

        <a
            href="{{ route('dashboard', request()->except(['search', 'page'])) }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
        >
            ✕ Effacer la recherche
        </a>
    </div>
@endif

@php
    $currentType = $view === 'dashboard' ? 'office' : $view;

    // Fusion des projets et catégories des deux modes.
    $allProjects = $projectsOffice
        ->concat($projectsMaster)
        ->unique('id');

    $allCategories = $categoriesOffice
        ->concat($categoriesMaster)
        ->unique('id');

    // Dates et heures par défaut.
    $today = \Carbon\Carbon::now();

    $defaultDueDate = $today->copy()
        ->addDays(4)
        ->format('Y-m-d');

    $defaultExecutionDate = $today->copy()
        ->addDays(2)
        ->format('Y-m-d');

    $defaultStartTime = '09:00';
    $defaultEndTime = '11:00';
@endphp

<div class="space-y-6 pb-12">

    {{-- NAVIGATION --}}
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 p-5 shadow-md">

        <div class="flex flex-wrap items-center gap-2">

            <a
                href="{{ route('dashboard', ['view' => 'dashboard']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition-all {{ $view === 'dashboard' ? 'bg-[#0052cc] text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50' }}"
            >
                📊 Dashboard
            </a>

            <a
                href="{{ route('dashboard', ['view' => 'office']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition-all {{ $view === 'office' ? 'bg-[#0052cc] text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50' }}"
            >
                💼 Mode Office
            </a>

            <a
                href="{{ route('dashboard', ['view' => 'master']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition-all {{ $view === 'master' ? 'bg-[#0052cc] text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50' }}"
            >
                🎓 Mode Master
            </a>

            <a
                href="{{ route('dashboard', ['view' => 'personal']) }}"
                class="rounded-xl px-4 py-2 text-xs font-bold transition-all {{ $view === 'personal' ? 'bg-[#0052cc] text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50' }}"
            >
                🌱 Perso
            </a>

        </div>

        <button
            onclick="toggleTaskForm()"
            id="btnToggleForm"
            type="button"
            class="flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-xs font-bold text-white shadow-md transition-all hover:bg-emerald-700"
        >
            <span>
                + Nouvelle tâche

                @if($view === 'office')
                    (Mode Office)
                @elseif($view === 'master')
                    (Mode Master)
                @else
                    (Global)
                @endif
            </span>
        </button>
    </div>

    {{-- FORMULAIRE DE CRÉATION / MODIFICATION --}}
    <div
        id="taskFormContainer"
        class="rounded-xl border border-emerald-200 bg-white p-6 shadow-lg hover:shadow-xl"
        style="display: none;"
    >

        <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-2">
            <h2 class="flex items-center gap-2 text-base font-bold text-gray-900">
                @if(isset($editTask))
                    ✏️ Modifier la tâche
                @else
                    ➕ Enregistrer une tâche
                @endif

                <span class="font-extrabold text-blue-700">
                    @if($view === 'dashboard')
                        en Mode Global
                    @else
                        en Mode {{ strtoupper($view) }}
                    @endif
                </span>
            </h2>

            <button
                onclick="toggleTaskForm()"
                type="button"
                class="text-xs font-bold text-gray-400 hover:text-gray-600"
            >
                ✕ Fermer
            </button>
        </div>

        <form
            id="taskForm"
            action="{{ route('tasks.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="grid grid-cols-1 gap-4 md:grid-cols-2"

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

                    if (hours > 23) {
                        hours = 23;
                    }

                    this.endTime =
                        String(hours).padStart(2, '0') + ':' + parts[1];
                },

                async deleteProject(projectId) {
                    if (!confirm('Voulez-vous vraiment archiver ce projet ?')) {
                        return;
                    }

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
                            throw new Error(
                                data.message || 'Erreur lors de la suppression.'
                            );
                        }

                        this.projects = this.projects.filter(project =>
                            String(project.id) !== String(projectId)
                        );

                        if (String(this.selectedProject) === String(projectId)) {
                            this.selectedProject = '';
                        }

                        alert(data.message || 'Projet archivé avec succès.');
                    } catch (error) {
                        alert(error.message || 'Une erreur est survenue.');
                    }
                },

                async deleteCategory(categoryId) {
                    if (!confirm('Voulez-vous vraiment supprimer cette étape ?')) {
                        return;
                    }

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
                            throw new Error(
                                data.message || 'Erreur lors de la suppression.'
                            );
                        }

                        this.categories = this.categories.filter(category =>
                            String(category.id) !== String(categoryId)
                        );

                        if (String(this.selectedCategory) === String(categoryId)) {
                            this.selectedCategory = '';
                        }

                        alert(data.message || 'Étape supprimée avec succès.');
                    } catch (error) {
                        alert(error.message || 'Une erreur est survenue.');
                    }
                }
            }"
        >
            @csrf

            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="edit_id" id="editTaskId">

            {{-- DESTINATION --}}
            @if($view === 'dashboard')
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-gray-700">
                        Destination de la tâche *
                    </label>

                    <select
                        x-model="currentMode"
                        required
                        class="w-full rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-bold text-[#0052cc] focus:border-[#0052cc] focus:outline-none"
                    >
                        <option value="office">💼 Mode Office</option>
                        <option value="master">🎓 Mode Master</option>
                    </select>

                    <input type="hidden" name="type" :value="currentMode">
                </div>
            @else
                <input type="hidden" name="type" value="{{ $view }}">
            @endif

            {{-- PROJET / MATIÈRE --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    1. Projet / Matière *
                </label>

                <div class="flex gap-2">
                    <select
                        name="project_id"
                        x-model="selectedProject"
                        required
                        class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                    >
                        <option value="">-- Sélectionner --</option>

                        <template x-for="project in filteredProjects" :key="project.id">
                            <option :value="project.id" x-text="project.title"></option>
                        </template>

                        <option value="new">➕ Créer un nouveau...</option>
                    </select>

                    <template x-if="selectedProject && selectedProject !== 'new'">
                        <button
                            type="button"
                            @click="deleteProject(selectedProject)"
                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100"
                        >
                            🗑️
                        </button>
                    </template>
                </div>

                <div x-show="selectedProject === 'new'" class="mt-2">
                    <input
                        type="text"
                        name="new_project_name"
                        placeholder="Nom du projet ou de la matière..."
                        class="w-full rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm"
                    >
                </div>
            </div>

            {{-- ÉTAPE / LEÇON --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    2. Étape / Leçon *
                </label>

                <div class="flex gap-2">
                    <select
                        name="category_id"
                        x-model="selectedCategory"
                        required
                        class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                    >
                        <option value="">-- Sélectionner --</option>

                        <template x-for="category in filteredCategories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>

                        <option value="new">➕ Créer une nouvelle...</option>
                    </select>

                    <template x-if="selectedCategory && selectedCategory !== 'new'">
                        <button
                            type="button"
                            @click="deleteCategory(selectedCategory)"
                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100"
                        >
                            🗑️
                        </button>
                    </template>
                </div>

                <div x-show="selectedCategory === 'new'" class="mt-2">
                    <input
                        type="text"
                        name="new_category_name"
                        placeholder="Nom de l'étape ou de la leçon..."
                        class="w-full rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm"
                    >
                </div>
            </div>

            {{-- LIBELLÉ DE LA TÂCHE --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    3. Libellé de la tâche *
                </label>

                <input
                    type="text"
                    name="title"
                    required
                    value="{{ isset($editTask) ? $editTask->title : '' }}"
                    placeholder="Ex. : Résolution de l'exercice d'économétrie"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >
            </div>

            {{-- DOCUMENT --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    📎 Document / Support de travail
                </label>

                <input
                    type="file"
                    name="document_file"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >

                <div id="oldDocument" class="mt-2 text-sm text-blue-600"></div>
                <div id="document_actuel" class="mt-2 text-sm text-blue-600"></div>

                <p class="mt-1 text-xs text-gray-400">
                    Formats acceptés : PDF, Word, Excel, PowerPoint et images.
                </p>
            </div>

            {{-- DATES ET HEURES --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    Échéance
                </label>

                <input
                    type="date"
                    name="date_prevue"
                    x-model="dueDate"
                    @change="updateExecutionDate()"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    Date d'exécution
                </label>

                <input
                    type="date"
                    name="execution_date"
                    x-model="executionDate"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    Heure de début
                </label>

                <input
                    type="time"
                    name="start_time"
                    x-model="startTime"
                    @change="updateEndTime()"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    Heure de fin
                </label>

                <input
                    type="time"
                    name="end_time"
                    x-model="endTime"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >
            </div>

            {{-- STATUT --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    État du livrable
                </label>

                <select
                    name="document_status"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >
                    <option value="todo">🔴 Phase 1 : Cadrage</option>
                    <option value="in_progress" selected>🟡 Phase 2 : En cours</option>
                    <option value="done">🟢 Phase 3 : Validé</option>
                </select>
            </div>

            {{-- PRIORITÉ --}}
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-700">
                    Urgence
                </label>

                <select
                    name="priority"
                    class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                >
                    <option value="high">Haute</option>
                    <option value="medium" selected>Moyenne</option>
                    <option value="low">Basse</option>
                </select>
            </div>

            <div class="md:col-span-2 pt-2">
                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#0052cc] px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-[#003d99]"
                >
                    💾 Valider et enregistrer la tâche
                </button>
            </div>

        </form>
    </div>
    
    {{-- ================= SCRIPT : FORMULAIRE DE TÂCHE ================= --}}
    <script>
        function toggleTaskForm() {
            const formContainer = document.getElementById('taskFormContainer');
            const btn = document.getElementById('btnToggleForm');
            const form = document.getElementById('taskForm');

            if (!formContainer || !btn || !form) return;

            if (formContainer.style.display === 'none') {
                // Réinitialiser pour créer une tâche.
                form.action = "{{ route('tasks.store') }}";

                document.getElementById('methodField').value = 'POST';
                document.getElementById('editTaskId').value = '';

                form.reset();

                if (form._x_dataStack) {
                    const alpineData = form._x_dataStack[0];

                    alpineData.selectedProject = '';
                    alpineData.selectedCategory = '';

                    alpineData.dueDate = '{{ $defaultDueDate }}';
                    alpineData.executionDate = '{{ $defaultExecutionDate }}';
                    alpineData.startTime = '{{ $defaultStartTime }}';
                    alpineData.endTime = '{{ $defaultEndTime }}';
                }

                const oldDocument = document.getElementById('oldDocument');

                if (oldDocument) {
                    oldDocument.innerHTML = '';
                }

                formContainer.style.display = 'block';
                btn.innerHTML = '<span>✕ Fermer</span>';

                formContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                formContainer.style.display = 'none';

                btn.innerHTML = '<span>+ Nouvelle tâche</span>';
            }
        }
    </script>

    {{-- ================= DASHBOARD GLOBAL ================= --}}
    @if($view === 'dashboard')

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h1 class="mb-4 flex items-center gap-2 text-xl font-bold text-gray-900">
                📊 Tableau de bord Global
            </h1>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7">

                {{-- TOTAL ACTIVES --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'total'])) }}"
                    class="block rounded-xl border border-blue-100 bg-blue-50 p-4 text-center transition-all hover:bg-blue-100 {{ $indicator === 'total' ? 'ring-2 ring-[#0052cc]' : '' }}"
                >
                    <div class="text-xs font-semibold text-blue-600">
                        Total Actives
                    </div>

                    <div class="text-2xl font-bold text-[#0052cc]">
                        {{ $totalTasks }}
                    </div>
                </a>

                {{-- À FAIRE --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'todo'])) }}"
                    class="block rounded-xl border border-red-100 bg-red-50 p-4 text-center transition-all hover:bg-red-100 {{ $indicator === 'todo' ? 'ring-2 ring-red-500' : '' }}"
                >
                    <div class="text-xs font-semibold text-red-600">
                        À faire
                    </div>

                    <div class="text-2xl font-bold text-red-600">
                        {{ $todoTasks }}
                    </div>
                </a>

                {{-- EN COURS --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'doing'])) }}"
                    class="block rounded-xl border border-amber-100 bg-amber-50 p-4 text-center transition-all hover:bg-amber-100 {{ $indicator === 'doing' ? 'ring-2 ring-amber-500' : '' }}"
                >
                    <div class="text-xs font-semibold text-amber-600">
                        En cours
                    </div>

                    <div class="text-2xl font-bold text-amber-600">
                        {{ $doingTasks }}
                    </div>
                </a>

                {{-- VALIDÉES --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'done'])) }}"
                    class="block rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-center transition-all hover:bg-emerald-100 {{ $indicator === 'done' ? 'ring-2 ring-emerald-500' : '' }}"
                >
                    <div class="text-xs font-semibold text-emerald-600">
                        Validées
                    </div>

                    <div class="text-2xl font-bold text-emerald-600">
                        {{ $doneTasks }}
                    </div>
                </a>

                {{-- OFFICE AUJOURD'HUI --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'office_today'])) }}"
                    class="block rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-center transition-all hover:bg-indigo-100 {{ $indicator === 'office_today' ? 'ring-2 ring-indigo-500' : '' }}"
                >
                    <div class="text-xs font-semibold text-indigo-600">
                        Office (Aujourd'hui)
                    </div>

                    <div class="text-2xl font-bold text-indigo-600">
                        {{ $officeTodayCount }}
                    </div>
                </a>

                {{-- MASTER AUJOURD'HUI --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'master_today'])) }}"
                    class="block rounded-xl border border-purple-100 bg-purple-50 p-4 text-center transition-all hover:bg-purple-100 {{ $indicator === 'master_today' ? 'ring-2 ring-purple-500' : '' }}"
                >
                    <div class="text-xs font-semibold text-purple-600">
                        Master (Aujourd'hui)
                    </div>

                    <div class="text-2xl font-bold text-purple-600">
                        {{ $masterTodayCount }}
                    </div>
                </a>

                {{-- ARCHIVES --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'archived'])) }}"
                    class="block rounded-xl border border-gray-200 bg-gray-100 p-4 text-center transition-all hover:bg-gray-200 {{ $indicator === 'archived' ? 'ring-2 ring-gray-600' : '' }}"
                >
                    <div class="text-xs font-semibold text-gray-600">
                        Archives
                    </div>

                    <div class="text-2xl font-bold text-gray-700">
                        {{ $archivedCount }}
                    </div>
                </a>

            </div>
        </div>

        {{-- ================= RÉSULTATS DES INDICATEURS ================= --}}
        @if($indicator)

            <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-200 bg-blue-50 p-4">

                    <h3 class="flex items-center gap-2 text-base font-bold text-gray-900">
                        📋 Résultats pour l'indicateur :

                        <span class="uppercase text-[#0052cc]">
                            {{ str_replace('_', ' ', $indicator) }}
                        </span>
                    </h3>

                    <a
                        href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard'])) }}"
                        class="rounded-lg bg-gray-200 px-3 py-1 text-xs font-bold text-gray-700 hover:bg-gray-300"
                    >
                        ✕ Fermer la liste
                    </a>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[950px] text-left text-sm text-gray-600">

                        <thead class="border-b border-blue-200 bg-blue-100 text-xs font-semibold uppercase text-blue-900">
                            <tr>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Projet / Matière</th>
                                <th class="px-4 py-3">Libellé de la tâche</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3">Progression</th>
                                <th class="px-4 py-3">Objectif du jour</th>
                                <th class="px-4 py-3">Date d'exécution</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($globalIndicatorTasks as $task)

                                <tr class="border-b border-blue-100 transition-colors hover:bg-blue-50">

                                    {{-- TYPE --}}
                                    <td class="px-4 py-4 text-xs font-bold">
                                        @if($task->type === 'office')
                                            <span class="rounded border border-blue-200 bg-blue-50 px-2 py-1 text-blue-600">
                                                Office
                                            </span>
                                        @else
                                            <span class="rounded border border-purple-200 bg-purple-50 px-2 py-1 text-purple-600">
                                                Master
                                            </span>
                                        @endif
                                    </td>

                                    {{-- PROJET --}}
                                    <td class="px-4 py-4 font-semibold text-gray-800">
                                        {{ $task->project->title ?? $task->project_name ?? 'Général' }}
                                    </td>

                                    {{-- TITRE ET DOCUMENT --}}
                                    <td class="px-4 py-4 font-bold text-gray-900">

                                        {{ $task->title }}

                                        @if($task->document_link)

                                            @php
                                                $link = $task->document_link;

                                                if (str_starts_with($link, 'C:\\')) {
                                                    $openLink = 'file:///' . str_replace('\\', '/', $link);
                                                } elseif (
                                                    !str_starts_with($link, 'http') &&
                                                    !str_starts_with($link, '/storage') &&
                                                    !str_starts_with($link, '/')
                                                ) {
                                                    $openLink = asset('storage/' . $link);
                                                } else {
                                                    $openLink = $link;
                                                }

                                                $extension = strtolower(
                                                    pathinfo($link, PATHINFO_EXTENSION)
                                                );
                                            @endphp

                                            @if($extension === 'pdf')
                                                <a
                                                    href="{{ $openLink }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="mt-1 block text-xs text-red-600 underline"
                                                >
                                                    📄 Ouvrir PDF
                                                </a>

                                            @elseif(in_array($extension, ['doc', 'docx']))
                                                <a
                                                    href="{{ $openLink }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="mt-1 block text-xs text-blue-700 underline"
                                                >
                                                    📝 Ouvrir Word
                                                </a>

                                            @elseif(in_array($extension, ['xls', 'xlsx']))
                                                <a
                                                    href="{{ $openLink }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="mt-1 block text-xs text-green-700 underline"
                                                >
                                                    📊 Ouvrir Excel
                                                </a>

                                            @elseif(in_array($extension, ['ppt', 'pptx']))
                                                <a
                                                    href="{{ $openLink }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="mt-1 block text-xs text-orange-600 underline"
                                                >
                                                    📽️ Ouvrir PowerPoint
                                                </a>

                                            @else
                                                <a
                                                    href="{{ $openLink }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="mt-1 block text-xs text-[#0052cc] underline"
                                                >
                                                    🔗 Document / Support
                                                </a>
                                            @endif

                                        @endif
                                    </td>

                                    {{-- STATUT --}}
                                    <td class="px-4 py-4">

                                        @if($task->is_archived)
                                            <span class="rounded border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">
                                                📁 Archivée
                                            </span>

                                        @elseif($task->document_status === 'done')
                                            <span class="rounded border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                                🟢 Validé
                                            </span>

                                        @elseif($task->document_status === 'in_progress')
                                            <span class="rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                                🟡 En cours
                                            </span>

                                        @else
                                            <span class="rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">
                                                🔴 À faire
                                            </span>
                                        @endif

                                    </td>

                                    {{-- PROGRESSION --}}
                                    <td class="px-4 py-4 text-xs text-gray-600">
                                        @if($task->document_status === 'done')
                                            <span class="font-bold text-emerald-700">100 %</span>
                                        @elseif($task->document_status === 'in_progress')
                                            <span class="font-semibold text-amber-700">En cours</span>
                                        @else
                                            <span class="text-gray-500">0 %</span>
                                        @endif
                                    </td>

                                    {{-- OBJECTIF DU JOUR --}}
                                    <td class="px-4 py-4 text-xs text-gray-600">
                                        {{ $task->daily_goal ?? '—' }}
                                    </td>

                                    {{-- DATE --}}
                                    <td class="px-4 py-4 text-xs text-gray-600">
                                        {{ $task->execution_date ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y') : 'Non planifié' }}
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="px-4 py-4 text-right">

                                        <div class="flex items-center justify-end gap-3">

                                            <button
                                                type="button"
                                                onclick="editTask({{ $task->id }})"
                                                class="text-xs font-semibold text-blue-600 hover:text-blue-800"
                                            >
                                                Modifier
                                            </button>

                                            @if(!$task->is_archived)
                                                <form
                                                    action="{{ route('tasks.archive', $task->id) }}"
                                                    method="POST"
                                                    class="inline"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        onclick="return confirm('Archiver cette tâche ?')"
                                                        class="text-xs font-semibold text-amber-600 hover:text-amber-800"
                                                    >
                                                        Archiver
                                                    </button>
                                                </form>
                                            @endif

                                            <form
                                                action="{{ route('tasks.destroy', $task->id) }}"
                                                method="POST"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Supprimer cette tâche ?')"
                                                    class="text-xs font-semibold text-red-500 hover:text-red-700"
                                                >
                                                    Supprimer
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="8"
                                        class="px-4 py-8 text-center font-semibold text-gray-500"
                                    >
                                        @if(request()->filled('search'))
                                            Aucun résultat pour « {{ request('search') }} » dans cet indicateur.
                                        @else
                                            Aucune tâche trouvée pour cet indicateur.
                                        @endif
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

        @endif

    @endif


    {{-- ================= MODE OFFICE ================= --}}
    @if($view === 'office')

        {{-- EN-TÊTE ET FILTRES --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h1 class="flex items-center gap-2 text-xl font-bold text-gray-900">

                💼 OFFICE Cockpit Livrables

                <span class="rounded-full border border-blue-100 bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-[#0052cc]">
                    🇸🇳 Dakar, Sénégal
                </span>

            </h1>

            <div class="flex flex-wrap items-center gap-2">

                {{-- TOUTES ACTIVES --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'office', 'filter' => 'all', 'status' => 'active'])) }}"
                    class="rounded-lg border px-3 py-1.5 text-xs font-bold {{ $statusFilter === 'active' && $filter === 'all' ? 'border-[#0052cc] bg-[#0052cc] text-white shadow-md' : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100' }}"
                >
                    Toutes Actives
                </a>

                {{-- AUJOURD'HUI --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'office', 'filter' => 'today', 'status' => 'active'])) }}"
                    class="rounded-lg border px-3 py-1.5 text-xs font-bold {{ $statusFilter === 'active' && $filter === 'today' ? 'border-[#0052cc] bg-[#0052cc] text-white' : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100' }}"
                >
                    📅 Aujourd'hui
                </a>

                {{-- ARCHIVES --}}
                <a
                    href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'office', 'status' => 'archived'])) }}"
                    class="rounded-lg border px-3 py-1.5 text-xs font-bold {{ $statusFilter === 'archived' ? 'border-gray-700 bg-gray-700 text-white' : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100' }}"
                >
                    📁 Archives
                </a>

            </div>
        </div>

        @php
            $groupedOfficeTasks = $officeTasks->groupBy(function ($task) {
                return $task->project->title
                    ?? $task->project_name
                    ?? 'Sans Projet';
            });
        @endphp

        <div class="space-y-6">

            @forelse($groupedOfficeTasks as $projectName => $tasksInProject)

                <div class="overflow-hidden rounded-xl border border-blue-200 bg-white shadow-md">

                    {{-- EN-TÊTE PROJET --}}
                    <div class="flex items-center justify-between gap-3 border-b border-blue-200 bg-blue-50 p-4">

                        <h3 class="flex items-center gap-2 text-base font-bold text-gray-900">
                            📁 Projet :

                            <span class="font-extrabold text-blue-700">
                                {{ $projectName }}
                            </span>
                        </h3>

                        <span class="rounded-full bg-blue-200 px-2.5 py-1 text-xs font-bold text-blue-800">
                            {{ $tasksInProject->count() }} tâche(s)
                        </span>

                    </div>

                    {{-- TABLEAU DES TÂCHES --}}
                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[850px] text-left text-sm text-gray-600">

                            <thead class="border-b border-blue-200 bg-blue-100 text-xs font-semibold uppercase text-blue-900">
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

                                    <tr class="border-b border-blue-100 transition-colors hover:bg-blue-50">

                                        {{-- RANG --}}
                                        <td class="px-4 py-4 font-bold text-blue-400">
                                            #{{ $index + 1 }}
                                        </td>

                                        {{-- ÉTAPE ET TÂCHE --}}
                                        <td class="px-4 py-4">

                                            <div class="mb-0.5 text-xs font-semibold text-blue-700">
                                                📌 Étape :
                                                {{ $task->category->title ?? $task->category->name ?? 'Général' }}
                                            </div>

                                            <div class="font-bold text-gray-900">
                                                {{ $task->title }}
                                            </div>

                                            @if($task->document_link)

                                                @php
                                                    $link = $task->document_link;

                                                    if (
                                                        !str_starts_with($link, 'http') &&
                                                        !str_starts_with($link, '/storage') &&
                                                        !str_starts_with($link, '/')
                                                    ) {
                                                        $link = asset('storage/' . $link);
                                                    }
                                                @endphp

                                                <a
                                                    href="{{ $link }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="mt-1 block text-xs text-[#0052cc] underline"
                                                >
                                                    🔗 Document / Support
                                                </a>

                                            @endif

                                        </td>

                                        {{-- STATUT ET PRIORITÉ --}}
                                        <td class="space-y-1 px-4 py-4">

                                            <div>
                                                @if($task->is_archived)
                                                    <span class="rounded border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">
                                                        📁 Archivée
                                                    </span>

                                                @elseif($task->document_status === 'done')
                                                    <span class="rounded border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                                        🟢 Validé
                                                    </span>

                                                @elseif($task->document_status === 'in_progress')
                                                    <span class="rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">
                                                        🟡 En cours
                                                    </span>

                                                @else
                                                    <span class="rounded border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">
                                                        🔴 Cadrage
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                Priorité :
                                                @if($task->priority === 'high')
                                                    <span class="font-semibold text-red-600">Haute</span>
                                                @elseif($task->priority === 'low')
                                                    <span class="font-semibold text-green-600">Basse</span>
                                                @else
                                                    <span class="font-semibold text-amber-600">Moyenne</span>
                                                @endif
                                            </div>

                                        </td>

                                        {{-- PLANIFICATION --}}
                                        <td class="px-4 py-4 text-xs text-gray-600">

                                            <div>
                                                📅
                                                {{ $task->execution_date ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y') : 'Non planifié' }}
                                            </div>

                                            @if($task->heure_debut || $task->heure_fin)

                                                <div class="font-semibold text-gray-500">
                                                    ⏰
                                                    {{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i') : '--:--' }}
                                                    à
                                                    {{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '--:--' }}
                                                </div>

                                            @endif

                                        </td>

                                        {{-- ÉCHÉANCE --}}
                                        <td class="px-4 py-4 text-xs text-gray-500">
                                            {{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '—' }}
                                        </td>

                                        {{-- ACTIONS --}}
                                        <td class="px-4 py-4 text-right">

                                            <div class="flex items-center justify-end gap-3">

                                                <button
                                                    type="button"
                                                    onclick="editTask({{ $task->id }})"
                                                    class="text-xs font-semibold text-blue-600 hover:text-blue-800"
                                                >
                                                    Modifier
                                                </button>

                                                @if(!$task->is_archived)
                                                    <form
                                                        action="{{ route('tasks.archive', $task->id) }}"
                                                        method="POST"
                                                        class="inline"
                                                    >
                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            onclick="return confirm('Archiver cette tâche ?')"
                                                            class="text-xs font-semibold text-amber-600 hover:text-amber-800"
                                                        >
                                                            Archiver
                                                        </button>
                                                    </form>
                                                @endif

                                                <form
                                                    action="{{ route('tasks.destroy', $task->id) }}"
                                                    method="POST"
                                                    class="inline"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        onclick="return confirm('Supprimer ce livrable ?')"
                                                        class="text-xs font-semibold text-red-500 hover:text-red-700"
                                                    >
                                                        Supprimer
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>
                        </table>

                    </div>

                </div>

            @empty

                {{-- AUCUN RÉSULTAT OFFICE --}}
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-12 text-center font-semibold text-blue-700 shadow-sm">

                    @if(request()->filled('search'))
                        Aucune tâche trouvée pour
                        « {{ request('search') }} »
                        avec les filtres actuels.

                        <div class="mt-5">
                            <a
                                href="{{ route('dashboard', request()->except(['search', 'page'])) }}"
                                class="inline-flex rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                            >
                                ✕ Effacer la recherche
                            </a>
                        </div>
                    @else
                        Aucune activité enregistrée en Mode Office
                    @endif

                </div>

            @endforelse

        </div>

    @endif

    {{-- FIN DE LA PARTIE 2 : LE MODE MASTER SUIT --}}

    {{-- ===================================================== --}}
    {{-- VUE : MODE MASTER                                     --}}
    {{-- ===================================================== --}}

    @if($view === 'master')

        {{-- EN-TÊTE MASTER --}}
        <section class="mb-6 overflow-hidden rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 via-yellow-50 to-white shadow-sm">

            <div class="flex flex-col gap-5 p-5 sm:p-6 xl:flex-row xl:items-center xl:justify-between">

                <div class="flex min-w-0 items-center gap-4">

                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-amber-200 bg-white text-3xl shadow-sm">
                        🎓
                    </div>

                    <div class="min-w-0">

                        <p class="mb-1 text-xs font-bold uppercase tracking-[0.16em] text-amber-700">
                            Espace académique
                        </p>

                        <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
                            MASTER Cockpit
                        </h1>

                        <p class="mt-1 text-sm text-slate-600">
                            Suivi académique · Organisez vos matières et vos leçons
                        </p>

                    </div>

                </div>

                {{-- FILTRES MASTER --}}
                <div class="flex flex-wrap items-center gap-2">

                    {{-- TOUTES ACTIVES --}}
                    <a
                        href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'master', 'filter' => 'all', 'status' => 'active'])) }}"
                        class="inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-bold transition {{ $statusFilter === 'active' && $filter === 'all' ? 'border-blue-600 bg-blue-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:bg-blue-50' }}"
                    >
                        Toutes actives
                    </a>

                    {{-- AUJOURD'HUI --}}
                    <a
                        href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'master', 'filter' => 'today', 'status' => 'active'])) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition {{ $statusFilter === 'active' && $filter === 'today' ? 'border-blue-600 bg-blue-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:bg-blue-50' }}"
                    >
                        <span>📅</span>
                        Aujourd'hui
                    </a>

                    {{-- EMPLOI DU TEMPS --}}
                    <a
                        href="{{ route('schedule.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                    >
                        <span>🗓️</span>
                        Emploi du temps
                    </a>

                    {{-- ARCHIVES --}}
                    <a
                        href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'master', 'status' => 'archived'])) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition {{ $statusFilter === 'archived' ? 'border-slate-700 bg-slate-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100' }}"
                    >
                        <span>📁</span>
                        Archives
                    </a>

                </div>

            </div>

        </section>

        {{-- REGROUPEMENT DES TÂCHES PAR MATIÈRE --}}
        @php
            $groupedMasterTasks = $masterTasks->groupBy(function ($task) {
                return $task->project->title
                    ?? $task->project_name
                    ?? 'Matière non spécifiée';
            });
        @endphp

        <div class="space-y-6">

            @forelse($groupedMasterTasks as $matiereName => $tasksInMatiere)

                <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm transition hover:shadow-md">

                    {{-- EN-TÊTE MATIÈRE --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-white px-5 py-4 sm:px-6">

                        <div class="flex min-w-0 items-center gap-3">

                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-xl">
                                📚
                            </span>

                            <div class="min-w-0">

                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Matière
                                </p>

                                <h2 class="truncate text-lg font-extrabold text-slate-900">
                                    {{ $matiereName }}
                                </h2>

                            </div>

                        </div>

                        <span class="inline-flex items-center rounded-full border border-blue-100 bg-blue-100/70 px-3 py-1.5 text-xs font-bold text-blue-800">
                            {{ $tasksInMatiere->count() }}
                            {{ $tasksInMatiere->count() > 1 ? 'tâches' : 'tâche' }}
                        </span>

                    </div>

                    <p class="px-4 pt-3 text-xs text-slate-500 sm:hidden">
                        Faites glisser le tableau horizontalement pour voir toutes les colonnes.
                    </p>

                    {{-- TABLEAU DES TÂCHES MASTER --}}
                    <div class="overflow-x-auto overscroll-x-contain">

                        <table class="w-full min-w-[980px] border-collapse text-left text-sm">

                            <thead>

                                <tr class="border-b border-blue-200 bg-blue-100/80 text-[11px] font-extrabold uppercase tracking-wide text-blue-900">

                                    <th class="px-5 py-4">Rang</th>
                                    <th class="px-5 py-4">Leçon / étape</th>
                                    <th class="px-5 py-4">Libellé de la tâche</th>
                                    <th class="px-5 py-4">Date d'exécution & heures</th>
                                    <th class="px-5 py-4">Échéance</th>
                                    <th class="px-5 py-4">Statut</th>
                                    <th class="px-5 py-4 text-right">Actions</th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @foreach($tasksInMatiere as $index => $task)

                                    <tr class="group transition-colors hover:bg-blue-50/60">

                                        {{-- RANG --}}
                                        <td class="whitespace-nowrap px-5 py-5 font-extrabold text-blue-600">
                                            #{{ $index + 1 }}
                                        </td>

                                        {{-- LEÇON --}}
                                        <td class="px-5 py-5">

                                            <span class="inline-flex items-center gap-2 font-semibold text-blue-700">

                                                <span class="text-base">📖</span>

                                                {{ $task->category->title ?? $task->category->name ?? 'Général' }}

                                            </span>

                                        </td>

                                        {{-- TÂCHE ET DOCUMENT --}}
                                        <td class="max-w-sm px-5 py-5">

                                            <a
                                                href="{{ route('tasks.learning', $task->id) }}"
                                                class="font-bold leading-5 text-slate-900 transition hover:text-blue-700"
                                            >
                                                {{ $task->title }}
                                            </a>

                                            @if($task->document_link)

                                                @php
                                                    $link = $task->document_link;

                                                    if (
                                                        !str_starts_with($link, 'http') &&
                                                        !str_starts_with($link, '/storage') &&
                                                        !str_starts_with($link, '/')
                                                    ) {
                                                        $link = asset('storage/' . $link);
                                                    }

                                                    $extension = strtolower(
                                                        pathinfo($link, PATHINFO_EXTENSION)
                                                    );
                                                @endphp

                                                @if($extension === 'pdf')

                                                    <a
                                                        href="{{ $link }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="mt-1 block text-xs font-semibold text-red-600 underline underline-offset-2 hover:text-red-800"
                                                    >
                                                        📄 Ouvrir PDF
                                                    </a>

                                                @elseif(in_array($extension, ['doc', 'docx']))

                                                    <a
                                                        href="{{ $link }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="mt-1 block text-xs font-semibold text-blue-700 underline underline-offset-2"
                                                    >
                                                        📝 Ouvrir Word
                                                    </a>

                                                @elseif(in_array($extension, ['xls', 'xlsx']))

                                                    <a
                                                        href="{{ $link }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="mt-1 block text-xs font-semibold text-emerald-700 underline underline-offset-2"
                                                    >
                                                        📊 Ouvrir Excel
                                                    </a>

                                                @elseif(in_array($extension, ['ppt', 'pptx']))

                                                    <a
                                                        href="{{ $link }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="mt-1 block text-xs font-semibold text-orange-700 underline underline-offset-2"
                                                    >
                                                        📽️ Ouvrir PowerPoint
                                                    </a>

                                                @else

                                                    <a
                                                        href="{{ $link }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="mt-1 block text-xs font-semibold text-blue-700 underline underline-offset-2"
                                                    >
                                                        🔗 Ouvrir le document
                                                    </a>

                                                @endif

                                            @endif

                                        </td>

                                        {{-- DATE D'EXÉCUTION ET HEURES --}}
                                        <td class="whitespace-nowrap px-5 py-5 text-slate-600">

                                            <div class="flex items-center gap-2">

                                                <span>📅</span>

                                                <span>
                                                    {{ $task->execution_date ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y') : 'Non planifié' }}
                                                </span>

                                            </div>

                                            <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">

                                                <span>⏱️</span>

                                                <span>
                                                    {{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i') : '--:--' }}
                                                    à
                                                    {{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '--:--' }}
                                                </span>

                                            </div>

                                        </td>

                                        {{-- ÉCHÉANCE --}}
                                        <td class="whitespace-nowrap px-5 py-5 text-slate-600">

                                            {{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '—' }}

                                        </td>

                                        {{-- STATUT --}}
                                        <td class="whitespace-nowrap px-5 py-5">

                                            @if($task->is_archived)

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                                    📁 Archivée
                                                </span>

                                            @elseif($task->document_status === 'done')

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800">
                                                    ✓ Terminée
                                                </span>

                                            @elseif($task->document_status === 'in_progress')

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-800">
                                                    ◷ En cours
                                                </span>

                                            @else

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-800">
                                                    ○ À faire
                                                </span>

                                            @endif

                                        </td>

                                        {{-- ACTIONS --}}
                                        <td class="px-5 py-5">

                                            <div class="flex min-w-[132px] items-center justify-end gap-2">

                                                {{-- MODIFIER --}}
                                                <button
                                                    type="button"
                                                    onclick="editTask({{ $task->id }})"
                                                    title="Modifier la tâche"
                                                    aria-label="Modifier la tâche"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                >
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m16 4 4 4M4 20l4-.8L19 8a2.1 2.1 0 0 0-3-3L5 16l-1 4Z"/>
                                                    </svg>
                                                </button>

                                                {{-- ARCHIVER --}}
                                                @if(!$task->is_archived)

                                                    <form
                                                        action="{{ route('tasks.archive', $task->id) }}"
                                                        method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('Archiver cette tâche ? Vous pourrez la retrouver dans les archives.');"
                                                    >
                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            title="Archiver la tâche"
                                                            aria-label="Archiver la tâche"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400 text-amber-950 shadow-sm transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"
                                                        >
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h18M5 7l1 13h12l1-13M9 7V4h6v3M10 11v5m4-5v5"/>
                                                            </svg>
                                                        </button>

                                                    </form>

                                                @endif

                                                {{-- SUPPRIMER --}}
                                                <form
                                                    action="{{ route('tasks.destroy', $task->id) }}"
                                                    method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Supprimer cette tâche ?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        title="Supprimer la tâche"
                                                        aria-label="Supprimer la tâche"
                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-200"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3"/>
                                                        </svg>
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </section>

            @empty

                {{-- AUCUN RÉSULTAT MASTER --}}
                <div class="rounded-2xl border border-dashed border-blue-200 bg-white px-6 py-14 text-center shadow-sm">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                        🔍
                    </div>

                    <h3 class="mt-4 text-base font-bold text-slate-800">
                        {{ request()->filled('search')
                            ? 'Aucune tâche trouvée'
                            : 'Aucune tâche à afficher' }}
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">

                        @if(request()->filled('search'))

                            Aucune tâche ne correspond à
                            « {{ request('search') }} »
                            pour les filtres actuels.

                        @else

                            Les tâches du Mode Master apparaîtront ici
                            une fois ajoutées.

                        @endif

                    </p>

                    @if(request()->filled('search'))

                        <a
                            href="{{ route('dashboard', request()->except(['search', 'page'])) }}"
                            class="mt-5 inline-flex rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            Effacer la recherche
                        </a>

                    @endif

                </div>

            @endforelse

        </div>

    @endif


    {{-- ===================================================== --}}
    {{-- VUE : DÉVELOPPEMENT PERSONNEL                          --}}
    {{-- ===================================================== --}}

    @if($view === 'personal')

        {{-- EN-TÊTE --}}
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h1 class="flex items-center gap-2 text-xl font-bold text-gray-900">
                🌱 Espace Développement Personnel
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Maîtrise de la langue française, culture générale et lectures enrichissantes.
            </p>

        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- ================= BIBLIOTHÈQUE ================= --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm md:col-span-2">

                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

                    <h3 class="flex items-center gap-2 font-bold text-gray-900">
                        📚 Bibliothèque à lire
                    </h3>

                    <button
                        type="button"
                        onclick="toggleBookForm()"
                        id="btnBookForm"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        📚 Ajouter un livre
                    </button>

                </div>

                {{-- FORMULAIRE AJOUT LIVRE --}}
                <div
                    id="bookForm"
                    class="mb-5 hidden rounded-lg border bg-gray-50 p-4"
                >

                    <form
                        action="{{ route('personal-resources.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-3"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="type"
                            value="book"
                        >

                        <input
                            type="text"
                            name="title"
                            placeholder="Titre du livre"
                            class="w-full rounded-lg border px-3 py-2"
                            required
                        >

                        <input
                            type="text"
                            name="author_or_source"
                            placeholder="Auteur (optionnel)"
                            class="w-full rounded-lg border px-3 py-2"
                        >

                        <input
                            type="file"
                            name="pdf_file"
                            accept="application/pdf"
                            class="w-full rounded-lg border px-3 py-2"
                            required
                        >

                        <div class="flex flex-wrap gap-3">

                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700"
                            >
                                💾 Ajouter
                            </button>

                            <button
                                type="button"
                                onclick="toggleBookForm()"
                                class="rounded-lg bg-gray-400 px-5 py-2 text-white hover:bg-gray-500"
                            >
                                Annuler
                            </button>

                        </div>

                    </form>

                </div>

                {{-- TABLEAU DES LIVRES EN COURS DE LECTURE --}}
                @if($readingBooks->isNotEmpty())

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[850px] text-left text-sm">

                            <thead class="bg-gray-100">

                                <tr>
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Titre</th>
                                    <th class="px-4 py-3">Auteur</th>
                                    <th class="px-4 py-3">Statut</th>
                                    <th class="px-4 py-3">Progression</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($readingBooks as $index => $book)

                                    <tr class="border-b border-gray-100">

                                        {{-- RANG --}}
                                        <td class="px-4 py-3">
                                            {{ $index + 1 }}
                                        </td>

                                        {{-- TITRE --}}
                                        <td class="px-4 py-3">

                                            <a
                                                href="{{ route('personal-resources.show', $book->id) }}"
                                                class="font-semibold text-blue-600 hover:underline"
                                            >
                                                {{ $book->title }}
                                            </a>

                                        </td>

                                        {{-- AUTEUR --}}
                                        <td class="px-4 py-3">
                                            {{ $book->author_or_source ?? '—' }}
                                        </td>

                                        {{-- STATUT --}}
                                        <td class="px-4 py-3">

                                            @if($book->reading_status === 'finished')

                                                <span class="font-semibold text-green-600">
                                                    ✅ Terminé
                                                </span>

                                            @elseif($book->reading_status === 'reading')

                                                <span class="font-semibold text-blue-600">
                                                    📖 En lecture
                                                </span>

                                            @else

                                                <span class="font-semibold text-gray-600">
                                                    📚 Disponible
                                                </span>

                                            @endif

                                        </td>

                                        {{-- PROGRESSION --}}
                                        <td class="px-4 py-3">

                                            <div class="text-xs font-semibold">
                                                📖 {{ $book->progress ?? 0 }} %
                                            </div>

                                            <div class="mt-1 h-2 overflow-hidden rounded bg-gray-200">

                                                <div
                                                    class="h-2 rounded bg-blue-600"
                                                    style="width: {{ min(100, max(0, (float) ($book->progress ?? 0))) }}%"
                                                ></div>

                                            </div>

                                            <div class="mt-1 text-xs text-gray-500">
                                                Page : {{ $book->current_page ?? 1 }}
                                            </div>

                                            @if(isset($readingProgress[$book->id]))

                                                <div class="mt-2 text-xs text-gray-600">
                                                    🎯 Objectif :
                                                    {{ $book->readingGoal->daily_pages ?? 10 }}
                                                    pages/jour
                                                </div>

                                                <div class="mt-1 text-xs font-bold text-blue-700">
                                                    {{ $readingProgress[$book->id]['message'] }}
                                                </div>

                                            @endif

                                        </td>

                                        {{-- ACTIONS DE LECTURE --}}
                                        <td class="px-4 py-3 text-right">

                                            <form
                                                method="POST"
                                                action="{{ route('personal-resources.update-reading-start', $book->id) }}"
                                                class="inline-flex flex-wrap items-center justify-end gap-2"
                                            >
                                                @csrf

                                                <input
                                                    type="number"
                                                    name="start_page"
                                                    value="{{ $book->current_page ?? 1 }}"
                                                    min="1"
                                                    class="w-20 rounded border px-2 py-1 text-xs"
                                                >

                                                <button
                                                    type="submit"
                                                    class="rounded bg-gray-800 px-3 py-1 text-xs text-white hover:bg-gray-900"
                                                >
                                                    ⚙️ Modifier départ
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <p class="rounded-lg bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                        Aucun livre en cours de lecture pour le moment.
                    </p>

                @endif

                {{-- BOUTON BIBLIOTHÈQUE GLOBALE --}}
                <div class="mt-6 text-center">

                    <button
                        type="button"
                        onclick="toggleGlobalLibrary()"
                        id="globalLibraryBtn"
                        class="rounded-lg bg-green-600 px-5 py-2 font-semibold text-white hover:bg-green-700"
                    >
                        📚 Afficher la bibliothèque globale
                    </button>

                </div>

                {{-- ================= BIBLIOTHÈQUE GLOBALE ================= --}}
                <div
                    id="globalLibrary"
                    class="mt-6 hidden rounded-xl border bg-gray-50 p-5"
                >

                    <h3 class="mb-4 text-lg font-bold text-gray-800">
                        📚 Bibliothèque globale
                    </h3>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[700px] text-left text-sm">

                            <thead class="bg-gray-200">

                                <tr>
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Titre</th>
                                    <th class="px-4 py-3">Auteur</th>
                                    <th class="px-4 py-3">État</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse($personalResources->where('type', 'book') as $index => $book)

                                    <tr class="border-b border-gray-200">

                                        <td class="px-4 py-3">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td class="px-4 py-3 font-semibold">

                                            <a
                                                href="{{ route('personal-resources.show', $book->id) }}"
                                                class="text-blue-600 hover:underline"
                                            >
                                                {{ $book->title }}
                                            </a>

                                        </td>

                                        <td class="px-4 py-3">
                                            {{ $book->author_or_source ?? '—' }}
                                        </td>

                                        <td class="px-4 py-3">

                                            @if($book->reading_status === 'finished')

                                                <span class="font-semibold text-green-600">
                                                    ✅ Terminé
                                                </span>

                                            @elseif($book->reading_status === 'reading')

                                                <span class="font-semibold text-blue-600">
                                                    📖 En lecture
                                                </span>

                                            @else

                                                <span class="font-semibold text-gray-600">
                                                    📚 Disponible
                                                </span>

                                            @endif

                                        </td>

                                        <td class="px-4 py-3">

                                            <div class="flex items-center justify-end gap-3">

                                                @if($book->reading_status === 'library')

                                                    <form
                                                        action="{{ route('personal-resources.add-reading', $book->id) }}"
                                                        method="POST"
                                                    >
                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700"
                                                        >
                                                            ➕ Ajouter à lire
                                                        </button>

                                                    </form>

                                                @elseif($book->reading_status === 'finished')

                                                    <span class="text-xs font-semibold text-green-600">
                                                        ✔ Déjà lu
                                                    </span>

                                                @else

                                                    <span class="text-xs font-semibold text-blue-600">
                                                        📖 En cours
                                                    </span>

                                                @endif

                                                <form
                                                    action="{{ route('personal-resources.destroy', $book->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Voulez-vous supprimer ce livre ?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        title="Supprimer le livre"
                                                        class="text-lg text-red-500 hover:text-red-700"
                                                    >
                                                        🗑
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            Aucun livre ajouté pour le moment.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- ===================================================== --}}
    {{-- FORMULAIRES DE SUPPRESSION DES CATÉGORIES             --}}
    {{-- ===================================================== --}}

    @foreach($allCategories as $c)

        <form
            id="delete-category-{{ $c->id }}"
            action="{{ route('categories.destroy', $c->id) }}"
            method="POST"
            style="display: none;"
        >
            @csrf
            @method('DELETE')
        </form>

    @endforeach


    {{-- ===================================================== --}}
    {{-- PIED DE PAGE                                          --}}
    {{-- ===================================================== --}}

    <footer class="mt-20 pb-8 text-center text-sm font-medium text-slate-600">

        <div class="inline-flex flex-col items-center justify-center gap-2 rounded-full border border-slate-200/80 bg-slate-100/70 px-5 py-2.5 shadow-sm backdrop-blur-sm sm:flex-row">

            <span class="text-slate-700">
                Laravel Task Manager &copy; {{ date('Y') }}
            </span>

            <span class="hidden text-slate-300 sm:inline">
                •
            </span>

            <span>
                Conçu avec
                <span class="text-red-500">❤️</span>
                par
                <strong class="font-semibold text-slate-900">Bado</strong>
            </span>

        </div>

    </footer>


    {{-- ===================================================== --}}
    {{-- SCRIPTS JAVASCRIPT                                    --}}
    {{-- ===================================================== --}}

    <script>

        /**
         * Afficher ou masquer le formulaire d'ajout de livre.
         */
        function toggleBookForm() {

            const form = document.getElementById('bookForm');
            const btn = document.getElementById('btnBookForm');

            if (!form || !btn) return;

            if (form.classList.contains('hidden')) {

                form.classList.remove('hidden');

                btn.textContent = '✕ Fermer';

            } else {

                form.classList.add('hidden');

                btn.textContent = '📚 Ajouter un livre';

            }
        }


        /**
         * Afficher ou masquer la bibliothèque globale.
         */
        function toggleGlobalLibrary() {

            const box = document.getElementById('globalLibrary');
            const btn = document.getElementById('globalLibraryBtn');

            if (!box || !btn) return;

            if (box.classList.contains('hidden')) {

                box.classList.remove('hidden');

                btn.textContent = '📕 Masquer bibliothèque globale';

            } else {

                box.classList.add('hidden');

                btn.textContent = '📚 Afficher la bibliothèque globale';

            }
        }


        /**
         * Charger une tâche existante dans le formulaire.
         */
        async function editTask(id) {

            try {

                const response = await fetch(
                    `{{ url('/tasks') }}/${id}/edit-data`,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error('Erreur lors du chargement de la tâche.');
                }

                const task = await response.json();

                const form = document.getElementById('taskForm');
                const formContainer = document.getElementById('taskFormContainer');
                const btn = document.getElementById('btnToggleForm');

                if (!form || !formContainer) {
                    throw new Error('Formulaire de tâche introuvable.');
                }

                // Passer le formulaire en mode modification.
                form.action = `{{ url('/tasks') }}/${id}`;

                document.getElementById('methodField').value = 'PUT';
                document.getElementById('editTaskId').value = id;

                // Titre.
                const titleInput = form.querySelector('input[name="title"]');

                if (titleInput) {
                    titleInput.value = task.title ?? '';
                }

                // Synchronisation Alpine.js.
                if (form._x_dataStack && form._x_dataStack.length > 0) {

                    const alpineData = form._x_dataStack[0];

                    // Mode de la tâche.
                    if (task.type) {
                        alpineData.currentMode = task.type;
                    }

                    // Projet et catégorie.
                    alpineData.selectedProject = String(task.project_id ?? '');
                    alpineData.selectedCategory = String(task.category_id ?? '');

                    // Dates.
                    alpineData.dueDate = task.date_prevue
                        ? String(task.date_prevue).substring(0, 10)
                        : '';

                    alpineData.executionDate = task.execution_date
                        ? String(task.execution_date).substring(0, 10)
                        : '';

                    // Heures.
                    alpineData.startTime = task.heure_debut
                        ? String(task.heure_debut).substring(0, 5)
                        : '';

                    alpineData.endTime = task.heure_fin
                        ? String(task.heure_fin).substring(0, 5)
                        : '';

                }

                // Statut du document.
                const statusSelect = form.querySelector(
                    'select[name="document_status"]'
                );

                if (statusSelect) {
                    statusSelect.value = task.document_status ?? 'in_progress';
                }

                // Priorité.
                const prioritySelect = form.querySelector(
                    'select[name="priority"]'
                );

                if (prioritySelect) {
                    prioritySelect.value = task.priority ?? 'medium';
                }

                // Le navigateur ne permet pas de préremplir un input file.
                const documentInput = form.querySelector(
                    'input[name="document_file"]'
                );

                if (documentInput) {
                    documentInput.value = '';
                }

                // Afficher le document existant sous forme de lien.
                const oldDocument = document.getElementById('oldDocument');

                if (oldDocument) {

                    oldDocument.replaceChildren();

                    if (task.document_link) {

                        let link = String(task.document_link);

                        if (
                            !link.startsWith('http://') &&
                            !link.startsWith('https://') &&
                            !link.startsWith('/') &&
                            !link.startsWith('file://')
                        ) {
                            link = `{{ asset('storage') }}/${link}`;
                        }

                        const label = document.createElement('span');
                        label.textContent = '📎 Document actuel : ';

                        const anchor = document.createElement('a');
                        anchor.href = link;
                        anchor.target = '_blank';
                        anchor.rel = 'noopener noreferrer';
                        anchor.className = 'text-blue-600 underline';
                        anchor.textContent = 'Ouvrir le document';

                        oldDocument.append(label, anchor);
                    }
                }

                // Modifier le titre du formulaire.
                const titre = formContainer.querySelector('h2');

                if (titre) {
                    titre.textContent = '✏️ Modifier la tâche';
                }

                // Ouvrir le formulaire.
                formContainer.style.display = 'block';

                if (btn) {
                    btn.innerHTML = '<span>✕ Fermer</span>';
                }

                formContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

            } catch (error) {

                console.error(error);

                alert(
                    error.message ||
                    'Impossible de charger la tâche.'
                );

            }
        }

    </script>

</div>

@endsection
