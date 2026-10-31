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
    $currentType = $view === 'dashboard' ? 'office' :$view;
    // Fusion et déduplication des projets et catégories pour les deux modes
    $allProjects =$projectsOffice->concat($projectsMaster)->unique('id');$allCategories = $categoriesOffice->concat($categoriesMaster)->unique('id');

    // Calcul initial des dates et heures par défaut (Échéance J+4, Exécution J+2)
    $today = \Carbon\Carbon::now();
    $defaultDueDate =$today->copy()->addDays(4)->format('Y-m-d');
    $defaultExecutionDate =$today->copy()->addDays(2)->format('Y-m-d');
    $defaultStartTime = '09:00';$defaultEndTime = '11:00';
@endphp

<div class="space-y-3 pb-12">

    <!-- FORMULAIRE DE CRÉATION DE TÂCHE (CACHÉ PAR DÉFAUT) -->
<div id="taskFormContainer"
     class="bg-white p-6 rounded-xl border border-emerald-200 shadow-lg hover:shadow-xl"
     style="display: none;">

    <div class="mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
            @if(isset($editTask))
                ✏️ Modifier la tâche
            @else
                ➕ Enregistrer une tâche
            @endif
            <span class="text-blue-700 font-extrabold">
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

    <form id="taskForm"
        action="{{ route('tasks.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="grid grid-cols-1 md:grid-cols-2 gap-4"

        x-data="{

            currentMode: '{{ $currentType }}',


            selectedProject: '{{ $view === 'master' ? request()->integer('folder') : '' }}',
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
                    String(hours).padStart(2,'0')
                    + ':'
                    + parts[1];

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

                        throw new Error(
                            data.message || 
                            'Erreur lors de la suppression.'
                        );

                    }



                    this.projects = this.projects.filter(project =>
                        String(project.id) !== String(projectId)
                    );



                    if (String(this.selectedProject) === String(projectId)) {

                        this.selectedProject = '';

                    }



                    alert(
                        data.message || 
                        'Projet archivé avec succès.'
                    );



                } catch(error) {


                    alert(
                        error.message || 
                        'Une erreur est survenue.'
                    );


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


                        throw new Error(
                            data.message || 
                            'Erreur lors de la suppression.'
                        );


                    }





                    this.categories = this.categories.filter(category =>
                        String(category.id) !== String(categoryId)
                    );




                    if (String(this.selectedCategory) === String(categoryId)) {


                        this.selectedCategory = '';

                    }





                    alert(
                        data.message || 
                        'Étape supprimée avec succès.'
                    );




                } catch(error) {



                    alert(
                        error.message || 
                        'Une erreur est survenue.'
                    );


                }


            }


        }">


        @csrf


        <input type="hidden" 
            name="_method" 
            id="methodField"
            value="POST">


        <input type="hidden" 
            name="edit_id" 
            id="editTaskId">


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

            @if($view === 'master' && request()->integer('folder'))

                @php
                    $fixedFolder = $projectsMaster->firstWhere(
                        'id',
                        request()->integer('folder')
                    );
                @endphp

                {{-- Nom du dossier affiché et verrouillé --}}
                <input
                    type="text"
                    value="{{ $fixedFolder->title ?? '' }}"
                    readonly
                    class="w-full rounded-lg border border-gray-200 bg-slate-100 px-3 py-2 text-sm font-bold text-slate-700 cursor-not-allowed"
                >

                {{-- Identifiant du dossier envoyé au serveur --}}
                <input
                    type="hidden"
                    name="project_id"
                    value="{{ $fixedFolder->id ?? '' }}"
                >

            @else

                {{-- Sélection normale pour les autres vues --}}
                <div class="flex gap-2">

                    <select
                        name="project_id"
                        x-model="selectedProject"
                        required
                        class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]"
                    >
                        <option value="">-- Sélectionner --</option>

                        <template x-for="project in filteredProjects" :key="project.id">
                            <option
                                :value="project.id"
                                x-text="project.title"
                            ></option>
                        </template>

                        <option value="new">➕ Créer un nouveau...</option>
                    </select>

                </div>

                <div x-show="selectedProject === 'new'" class="mt-2">
                    <input
                        type="text"
                        name="new_project_name"
                        placeholder="Nom du projet ou de la matière..."
                        class="w-full rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm"
                    >
                </div>

            @endif

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
                        <option :value="category.id" x-text="category.name"></option>
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

                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    3. Libellé de la Tâche *
                </label>

                <input type="text"
                    name="title"
                    required
                    value="{{ isset($editTask) ? $editTask->title : '' }}"
                    placeholder="Ex: Résolution de l'exercice d'économétrie"
                    class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">

            </div>
            {{-- LIEN DE TRAVAIL --}}
            <div class="md:col-span-2">

                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    📎 Document / Support de travail
                </label>

                <input
                    type="file"
                    name="document_file"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                    class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]"
                >


                <div id="oldDocument" class="mt-2 text-sm text-blue-600"></div>

                <div id="document_actuel" class="mt-2 text-sm text-blue-600"></div>

                <div class="mt-4">
                    <label class="mb-1 block text-xs font-semibold text-gray-700">
                        🔗 Lien du document / Support en ligne
                    </label>

                    <input
                        type="url"
                        name="document_link"
                        id="document_link"
                        placeholder="https://exemple.com/mon-cours"
                        class="w-full rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-2 text-sm focus:border-[#0052cc] focus:outline-none"
                    >

                    <p class="mt-1 text-xs text-gray-400">
                        Facultatif : Google Drive, YouTube, PDF en ligne, site de cours...
                        Vous pouvez aussi joindre un fichier ci-dessus.
                    </p>
                </div>


                <p class="text-xs text-gray-400 mt-1">
                    Formats acceptés : PDF, Word, Excel, PowerPoint, Images
                </p>

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
            const form = document.getElementById('taskForm');

            if (!formContainer || !form) return;

            const isHidden = window.getComputedStyle(formContainer).display === 'none';

            if (!isHidden) {
                formContainer.style.display = 'none';

                if (btn) {
                    btn.textContent = '+ Nouvelle tâche (Mode {{ ucfirst($view) }})';
                }

                return;
            }

            // Revenir en mode création.
            form.action = @json(route('tasks.store'));

            document.getElementById('methodField').value = 'POST';
            document.getElementById('editTaskId').value = '';

            form.reset();

            // Réinitialiser les champs contrôlés par Alpine.js.
            if (window.Alpine) {
                const data = Alpine.$data(form);

                if (data) {

                    data.currentMode = @json($currentType);

                    data.selectedProject = @json(
                        $view === 'master' && request()->integer('folder')
                            ? (string) request()->integer('folder')
                            : ''
                    );

                    data.selectedCategory = '';

                    data.dueDate = @json($defaultDueDate);
                    data.executionDate = @json($defaultExecutionDate);
                    data.startTime = @json($defaultStartTime);
                    data.endTime = @json($defaultEndTime);
                }
            }

            // Rétablir les valeurs dans les champs HTML.
            const fields = {
                date_prevue: @json($defaultDueDate),
                execution_date: @json($defaultExecutionDate),
                start_time: @json($defaultStartTime),
                end_time: @json($defaultEndTime)
            };

            for (const [name, value] of Object.entries(fields)) {
                const input = form.elements.namedItem(name);

                if (input) {
                    input.value = value;
                }
            }

            const oldDocument = document.getElementById('oldDocument');

            if (oldDocument) {
                oldDocument.replaceChildren();
            }

            const title = formContainer.querySelector('h2');

            if (title) {
                title.textContent = '➕ Enregistrer une tâche en Mode {{ strtoupper($currentType) }}';
            }

            formContainer.style.display = 'block';

            if (btn) {
                btn.textContent = '✕ Fermer';
            }

            formContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    </script>
        
    <!-- ================= VUE : DASHBOARD GLOBAL ================= -->
    @if($view === 'dashboard')
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                📊 Tableau de bord Global
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <a href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'total'])) }}" class="bg-blue-50 hover:bg-blue-100 border border-blue-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'total' ? 'ring-2 ring-[#0052cc]' : '' }}">
                    <div class="text-xs text-blue-600 font-semibold">Total Actives</div>
                    <div class="text-2xl font-bold text-[#0052cc]">{{ $totalTasks }}</div>
                </a>
                <a href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'todo'])) }}" class="bg-red-50 hover:bg-red-100 border border-red-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'todo' ? 'ring-2 ring-red-500' : '' }}">
                    <div class="text-xs text-red-600 font-semibold">À faire</div>
                    <div class="text-2xl font-bold text-red-600">{{ $todoTasks }}</div>
                </a>
                <a href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'doing'])) }}" class="bg-amber-50 hover:bg-amber-100 border border-amber-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'doing' ? 'ring-2 ring-amber-500' : '' }}">
                    <div class="text-xs text-amber-600 font-semibold">En cours</div>
                    <div class="text-2xl font-bold text-amber-600">{{ $doingTasks }}</div>
                </a>
                <a href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'done'])) }}" class="bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'done' ? 'ring-2 ring-emerald-500' : '' }}">
                    <div class="text-xs text-emerald-600 font-semibold">Validées</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $doneTasks }}</div>
                </a>
                <a href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'office_today'])) }}" class="bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'office_today' ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="text-xs text-indigo-600 font-semibold">Office (Aujourd'hui)</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ $officeTodayCount }}</div>
                </a>
                <a href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'master_today'])) }}" class="bg-purple-50 hover:bg-purple-100 border border-purple-100 p-4 rounded-xl text-center transition-all block {{ $indicator === 'master_today' ? 'ring-2 ring-purple-500' : '' }}">
                    <div class="text-xs text-purple-600 font-semibold">Master (Aujourd'hui)</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $masterTodayCount }}</div>
                </a>
                <a href="{{ route('dashboard', array_merge(request()->only('search'), ['view' => 'dashboard', 'indicator' => 'archived'])) }}" class="bg-gray-100 hover:bg-gray-200 border border-gray-200 p-4 rounded-xl text-center transition-all block {{ $indicator === 'archived' ? 'ring-2 ring-gray-600' : '' }}">
                    <div class="text-xs text-gray-600 font-semibold">Archives</div>
                    <div class="text-2xl font-bold text-gray-700">{{ $archivedCount }}</div>
                </a>
            </div>
        </div>

        @if($indicator)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                <div class="p-4 bg-blue-50 border-b border-blue-200 flex items-center justify-between">
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
                        <thead class="bg-blue-100 text-xs uppercase font-semibold text-blue-900 border-b border-blue-200">
                            <tr>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Projet / Matière</th>
                                <th class="px-4 py-3">Libellé de la Tâche</th>
                                <th class="px-4 py-3">
                                    Statut
                                </th>

                                <th class="px-4 py-3">
                                    Progression
                                </th>

                                <th class="px-4 py-3">
                                    Objectif du jour
                                </th>
                                <th class="px-4 py-3">Date d'exécution</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($globalIndicatorTasks as $task)
                                <tr class="hover:bg-blue-50 transition-colors border-b border-blue-100">
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

                                            @php
                                                $link = $task->document_link;

                                                // Transformation chemin Windows en lien local
                                                if(str_starts_with($link, 'C:\\')) {
                                                    $openLink = 'file:///' . str_replace('\\', '/', $link);
                                                } else {
                                                    $openLink = $link;
                                                }

                                                $extension = strtolower(pathinfo($link, PATHINFO_EXTENSION));
                                            @endphp


                                            @if(in_array($extension, ['pdf']))
                                                <a href="{{ $openLink }}"
                                                target="_blank"
                                                class="text-xs text-red-600 underline block mt-0.5">
                                                    📄 Ouvrir PDF
                                                </a>

                                            @elseif(in_array($extension, ['doc','docx']))
                                                <a href="{{ $openLink }}"
                                                target="_blank"
                                                class="text-xs text-blue-700 underline block mt-0.5">
                                                    📝 Ouvrir Word
                                                </a>

                                            @elseif(in_array($extension, ['xls','xlsx']))
                                                <a href="{{ $openLink }}"
                                                target="_blank"
                                                class="text-xs text-green-700 underline block mt-0.5">
                                                    📊 Ouvrir Excel
                                                </a>

                                            @elseif(in_array($extension, ['ppt','pptx']))
                                                <a href="{{ $openLink }}"
                                                target="_blank"
                                                class="text-xs text-orange-600 underline block mt-0.5">
                                                    📽️ Ouvrir PowerPoint
                                                </a>

                                            @else
                                                <a href="{{ $openLink }}"
                                                target="_blank"
                                                class="text-xs text-[#0052cc] underline block mt-0.5">
                                                    🔗 Document / Support
                                                </a>
                                            @endif

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

                                        <button type="button"
                                        onclick="editTask({{ $task->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-semibold">

                                            Modifier

                                        </button>

                                        @if(!$task->is_archived)
                                            <form action="{{ route('tasks.archive', $task->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs text-amber-600 hover:text-amber-800 font-semibold">
                                                    Archiver
                                                </button>
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
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 font-semibold">
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

    <!-- ================= VUE : MODE OFFICE ================= -->

    {{-- ============================================= --}}
    {{-- VUE : MODE OFFICE — DESIGN TABLEAU UNIQUE     --}}
    {{-- ============================================= --}}

    @if($view === 'office')

        {{-- BARRE COMPACTE : FILTRES + NOUVELLE TÂCHE --}}
        <section class="mb-3 rounded-xl border border-blue-100 bg-white px-4 py-2 shadow-sm">

            <div class="flex flex-wrap items-center justify-between gap-3">

                {{-- FILTRES --}}
                <div class="flex flex-wrap items-center gap-2">

                    <a
                        href="{{ route('dashboard', array_merge(request()->only('search'), [
                            'view' => 'office',
                            'filter' => 'all',
                            'status' => 'active'
                        ])) }}"
                        class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-xs font-bold transition {{ $statusFilter === 'active' && $filter === 'all' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-blue-50' }}"
                    >
                        Toutes actives
                    </a>

                    <a
                        href="{{ route('dashboard', array_merge(request()->only('search'), [
                            'view' => 'office',
                            'filter' => 'today',
                            'status' => 'active'
                        ])) }}"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold transition {{ $statusFilter === 'active' && $filter === 'today' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-blue-50' }}"
                    >
                        📅 Aujourd'hui
                    </a>

                    <a
                        href="{{ route('dashboard', array_merge(request()->only('search'), [
                            'view' => 'office',
                            'status' => 'archived'
                        ])) }}"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold transition {{ $statusFilter === 'archived' ? 'border-slate-700 bg-slate-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100' }}"
                    >
                        📁 Archives
                    </a>

                </div>

                {{-- NOUVELLE TÂCHE À DROITE --}}
                <button
                    type="button"
                    onclick="toggleTaskForm()"
                    id="btnToggleForm"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    + Nouvelle tâche (Mode Office)
                </button>

            </div>
        </section>


        {{-- TABLEAU UNIQUE --}}
        <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">

            {{-- TITRE COMPACT --}}
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">

                <div class="flex items-center gap-3">

                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-lg">
                        📋
                    </span>

                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">
                            Toutes les tâches (Mode Office)
                        </h2>

                        <p class="text-xs text-slate-500">
                            {{ $officeTasks->count() }} tâche(s) affichée(s)
                        </p>
                    </div>

                </div>

            </div>

            {{-- STYLE COMPACT, IDENTIQUE AU MODE MASTER --}}
            <style>
                .office-table {
                    font-size: 12px;
                    line-height: 1.4;
                }

                .office-table th {
                    padding: 12px 10px;
                    font-size: 10px;
                    font-weight: 800;
                    white-space: nowrap;
                    letter-spacing: 0.025em;
                }

                .office-table td {
                    padding: 11px 10px;
                    font-size: 12px;
                    vertical-align: middle;
                    white-space: nowrap;
                }

                .office-table td a,
                .office-table td span {
                    font-size: inherit;
                }

                .office-table td .text-xs {
                    font-size: 11px;
                }

                .office-table tbody tr {
                    min-height: 58px;
                }

                .office-table tbody tr:hover {
                    background-color: #f8fbff;
                }
            </style>

            <div class="overflow-x-auto">

                <table class="office-table w-full min-w-[1100px] border-collapse text-left text-xs">

                    <thead class="border-b border-blue-100 bg-blue-50/80">

                        <tr class="font-extrabold uppercase text-slate-600">
                            <th>Projet</th>
                            <th>Étape</th>
                            <th>Tâche</th>
                            <th>Exécution</th>
                            <th>Échéance</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th class="text-right">Actions</th>
                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">



                        @forelse($officeTasks->sortBy(function ($task) {

                            // 0 = tâches non terminées, 1 = tâches terminées
                            $groupe = $task->document_status === 'done' ? '1' : '0';

                            // Date d'exécution : les dates absentes sont placées en dernier
                            $date = !empty($task->execution_date)
                                ? \Carbon\Carbon::parse($task->execution_date)->format('Y-m-d')
                                : '9999-12-31';

                            // Heure de début : les heures absentes sont placées en dernier
                            $heure = !empty($task->heure_debut)
                                ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i:s')
                                : '23:59:59';

                            // Classement : groupe → date → heure → ID
                            return $groupe . '|' . $date . '|' . $heure . '|' . str_pad((string) $task->id, 12, '0', STR_PAD_LEFT);

                        }) as $task)

                            <tr class="transition-colors hover:bg-blue-50/50">

                                {{-- PROJET --}}
                                <td>
                                    <div class="flex items-center gap-2">

                                        <span class="text-base">📁</span>

                                        <span class="font-bold text-slate-800">
                                            {{ $task->project->title ?? $task->project_name ?? 'Général' }}
                                        </span>

                                    </div>
                                </td>

                                {{-- ÉTAPE --}}
                                <td>
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-blue-600">
                                        📌
                                        {{ $task->category->title ?? $task->category->name ?? 'Général' }}
                                    </span>
                                </td>

                                {{-- TÂCHE ET DOCUMENT --}}
                                <td>
                                    <div class="font-bold text-slate-900">
                                        {{ $task->title }}
                                    </div>

                                    @if($task->document_link)

                                        @php
                                            $documentPath = $task->document_link;

                                            if (
                                                !str_starts_with($documentPath, 'http://') &&
                                                !str_starts_with($documentPath, 'https://') &&
                                                !str_starts_with($documentPath, '/') &&
                                                !str_starts_with($documentPath, 'file://')
                                            ) {
                                                $documentPath = asset('storage/' . $documentPath);
                                            }

                                            $extension = strtolower(
                                                pathinfo($task->document_link, PATHINFO_EXTENSION)
                                            );
                                        @endphp

                                        <a
                                            href="{{ $documentPath }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="mt-1 inline-flex items-center gap-1 font-semibold underline underline-offset-2 {{ $extension === 'pdf' ? 'text-red-600' : 'text-blue-600' }}"
                                        >
                                            @if($extension === 'pdf')
                                                📄 Ouvrir PDF
                                            @elseif(in_array($extension, ['doc', 'docx']))
                                                📝 Ouvrir Word
                                            @elseif(in_array($extension, ['xls', 'xlsx']))
                                                📊 Ouvrir Excel
                                            @elseif(in_array($extension, ['ppt', 'pptx']))
                                                📽️ Ouvrir PowerPoint
                                            @else
                                                🔗 Document / Support
                                            @endif
                                        </a>

                                    @endif

                                </td>

                                {{-- DATE D'EXÉCUTION ET HEURES --}}
                                <td class="text-slate-600">

                                    <div class="flex items-center gap-2">
                                        <span>🗓️</span>

                                        <span>
                                            {{ $task->execution_date
                                                ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y')
                                                : 'Non planifié' }}
                                        </span>
                                    </div>

                                    @if($task->heure_debut || $task->heure_fin)

                                        <div class="mt-1 flex items-center gap-2 text-slate-500">

                                            <span>⏱️</span>

                                            <span>
                                                {{ $task->heure_debut
                                                    ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i')
                                                    : '--:--' }}

                                                à

                                                {{ $task->heure_fin
                                                    ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i')
                                                    : '--:--' }}
                                            </span>

                                        </div>

                                    @endif

                                </td>

                                {{-- ÉCHÉANCE --}}
                                <td class="text-slate-600">
                                    {{ $task->date_prevue
                                        ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y')
                                        : '—' }}
                                </td>

                                {{-- STATUT --}}
                                <td>

                                    @if($task->is_archived)

                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 font-bold text-slate-600">
                                            📁 Archivée
                                        </span>

                                    @elseif($task->document_status === 'done')

                                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-700">
                                            ✓ Terminée
                                        </span>

                                    @elseif($task->document_status === 'in_progress')

                                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-700">
                                            ◷ En cours
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 font-bold text-slate-600">
                                            ◷ À faire
                                        </span>

                                    @endif

                                </td>

                                {{-- PRIORITÉ --}}
                                <td>

                                    @if($task->priority === 'high')

                                        <span class="inline-flex rounded-full bg-red-50 px-3 py-1.5 font-bold text-red-600">
                                            ↑ Haute
                                        </span>

                                    @elseif($task->priority === 'low')

                                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-700">
                                            ↓ Basse
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1.5 font-bold text-amber-700">
                                            = Moyenne
                                        </span>

                                    @endif

                                </td>

                                {{-- ACTIONS --}}
                                <td>
                                    <div class="flex items-center justify-end gap-2">

                                        {{-- MODIFIER --}}
                                        <button
                                            type="button"
                                            onclick="editTask({{ $task->id }})"
                                            title="Modifier"
                                            aria-label="Modifier la tâche"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="m16 4 4 4M4 20l4-.8L19 8a2.1 2.1 0 0 0-3-3L5 16l-1 4Z"/>
                                            </svg>
                                        </button>

                                        {{-- ARCHIVER --}}
                                        @if(!$task->is_archived)

                                            <form
                                                action="{{ route('tasks.archive', $task->id) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Archiver cette tâche ?');"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    title="Archiver la tâche"
                                                    aria-label="Archiver la tâche"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400 text-amber-950 shadow-sm transition hover:bg-amber-500"
                                                >
                                                    <svg
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <rect x="3" y="4" width="18" height="4" rx="1"/>
                                                        <path d="M5 8v12h14V8"/>
                                                        <path d="M9 13l3 3 3-3"/>
                                                        <path d="M12 11v5"/>
                                                    </svg>
                                                </button>
                                            </form>

                                        @endif

                                        {{-- SUPPRIMER --}}
                                        <form
                                            action="{{ route('tasks.destroy', $task->id) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Supprimer définitivement cette tâche ?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Supprimer"
                                                aria-label="Supprimer la tâche"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.8"
                                                        d="M4 7h16M10 11v6m4-6v6M6 7l1 14h10l1-14M9 7V4h6v3"/>
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center">

                                    <div class="mb-3 text-3xl">
                                        🔍
                                    </div>

                                    <h3 class="font-bold text-slate-800">
                                        {{ request()->filled('search')
                                            ? 'Aucune tâche trouvée'
                                            : 'Aucune tâche à afficher' }}
                                    </h3>

                                    <p class="mt-2 text-sm text-slate-500">

                                        @if(request()->filled('search'))
                                            Aucune tâche ne correspond à
                                            « {{ request('search') }} »
                                            avec les filtres actuels.
                                        @else
                                            Les tâches du Mode Office apparaîtront ici.
                                        @endif

                                    </p>

                                    @if(request()->filled('search'))

                                        <a
                                            href="{{ route('dashboard', request()->except(['search', 'page'])) }}"
                                            class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700"
                                        >
                                            Effacer la recherche
                                        </a>

                                    @endif

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    @endif

    <!-- ================= VUE : MODE MASTER ================= -->

    @if($view === 'master')

        @php
            // Le dossier sélectionné est transmis dans l'URL.
            $folderId = request()->integer('folder');

            // Vérifier que le dossier appartient bien au Mode Master.
            $selectedMasterFolder = $folderId
                ? $projectsMaster->first(function ($project) use ($folderId) {
                    return (int) $project->id === $folderId;
                })
                : null;


            // Déterminer si l'utilisateur demande une vue globale.
            $masterActivityView = !$selectedMasterFolder
                && $statusFilter !== 'archived'
                && in_array($filter, ['all', 'today'])
                && request()->has('filter');

            // Dossiers visibles selon le filtre Archives.
            $displayMasterFolders = $projectsMaster->filter(function ($project) use ($statusFilter) {
                return $statusFilter === 'archived'
                    ? !$project->is_active
                    : (bool) $project->is_active;
            });

            // Dossiers actifs autorisés pour les vues globales.
            $activeMasterIds = $projectsMaster
                ->filter(fn ($project) => (bool) $project->is_active)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            // Tâches à afficher à l'intérieur d'un dossier
            // ou dans la vue globale des activités.
            $masterFolderTasks = $masterTasks->filter(function ($task) use (
                $selectedMasterFolder,
                $masterActivityView,
                $activeMasterIds,
                $filter
            ) {
                if ($selectedMasterFolder) {
                    return (int) $task->project_id === (int) $selectedMasterFolder->id
                        && !$task->is_archived;
                }

                if (!$masterActivityView || $task->is_archived) {
                    return false;
                }

                if (!in_array((int) $task->project_id, $activeMasterIds, true)) {
                    return false;
                }

                if ($filter === 'today') {
                    return $task->execution_date
                        && \Carbon\Carbon::parse($task->execution_date)->isToday();
                }

                return $task->document_status !== 'done';
            });

        @endphp

        {{-- BARRE DE NAVIGATION EXTÉRIEURE MASTER --}}
        @if(!$selectedMasterFolder)

            <section class="mb-3 rounded-xl border border-blue-100 bg-white px-4 py-2 shadow-sm">

                <div class="flex flex-wrap items-center gap-2">

                    <a
                        href="{{ route('dashboard', ['view' => 'master', 'filter' => 'all', 'status' => 'active']) }}"
                        class="rounded-lg border px-3 py-2 text-xs font-bold transition {{ request()->has('filter') && $statusFilter === 'active' && $filter === 'all' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-blue-50' }}"
                    >
                        Toutes actives
                    </a>

                    <a
                        href="{{ route('dashboard', ['view' => 'master', 'filter' => 'today', 'status' => 'active']) }}"
                        class="rounded-lg border px-3 py-2 text-xs font-semibold {{ $statusFilter === 'active' && $filter === 'today' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-blue-50' }}"
                    >
                        📅 Aujourd'hui
                    </a>

                    <a
                        href="{{ route('schedule.index') }}"
                        class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                    >
                        🗓️ Emploi du temps
                    </a>

                 
                    <a
                        href="{{ route('exams.index') }}"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                    >
                        🎓 Préparation aux examens
                    </a>


                </div>

            </section>


            @if(!$masterActivityView)


            {{-- LISTE PRINCIPALE DES DOSSIERS MASTER --}}
            <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">

                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-xl">
                            📁
                        </span>

                        <div>

                            <h2 class="text-lg font-extrabold text-slate-900">
                                {{ $statusFilter === 'archived'
                                    ? 'Dossiers archivés (Mode Master)'
                                    : 'Mes dossiers (Mode Master)' }}
                            </h2>

                            <p class="text-xs text-slate-500">
                                {{ $displayMasterFolders->count() }} dossier(s)
                            </p>
                        </div>
                    </div>
                
                        @if($statusFilter !== 'archived')
                            <button
                                type="button"
                                onclick="document.getElementById('newMasterFolderForm').classList.toggle('hidden')"
                                class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700"
                            >
                                + Nouveau dossier
                            </button>
                        @endif



                </div>

                {{-- FORMULAIRE DE CRÉATION D'UN DOSSIER MASTER --}}
                @if($statusFilter !== 'archived')

                    <form
                        id="newMasterFolderForm"
                        action="{{ route('projects.store') }}"
                        method="POST"
                        class="hidden border-b border-blue-100 bg-slate-50 px-4 py-4"
                    >
                        @csrf

                        <label class="mb-2 block text-xs font-bold text-slate-700">
                            📁 Nom du nouveau dossier
                        </label>

                        <div class="flex flex-wrap items-center gap-3">

                            <input
                                type="text"
                                name="title"
                                required
                                maxlength="255"
                                placeholder="Ex : Finances publiques"
                                class="min-w-[220px] flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                            >

                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700"
                            >
                                ✓ Créer le dossier
                            </button>

                            <button
                                type="button"
                                onclick="document.getElementById('newMasterFolderForm').classList.add('hidden')"
                                class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100"
                            >
                                Annuler
                            </button>

                        </div>

                    </form>

                @endif

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[700px] text-left text-xs">

                        <thead class="border-b border-blue-100 bg-blue-50/80">
                            <tr class="text-[11px] font-extrabold uppercase text-slate-600">
                                <th class="px-4 py-3">Nom du dossier</th>
                                <th class="px-4 py-3">Date de création</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @forelse($displayMasterFolders as $project)

                                @php
                                    $folderTasks = $masterTasks->filter(function ($task) use ($project) {
                                        return (int) $task->project_id === (int) $project->id;
                                    });

                                    $activeFolderTasks = $folderTasks->filter(function ($task) {
                                        return !$task->is_archived;
                                    });

                                    $finishedCount = $activeFolderTasks->where('document_status', 'done')->count();

                                    $isFinished = $activeFolderTasks->isNotEmpty()
                                        && $finishedCount === $activeFolderTasks->count();


                                    $folderUrl = route('dashboard', [
                                        'view' => 'master',
                                        'folder' => $project->id,
                                        'status' => $statusFilter
                                    ]);
                                @endphp

                                <tr
                                    class="cursor-pointer transition hover:bg-blue-50/60"
                                    onclick="window.location.href = @js($folderUrl)"
                                >

                                    {{-- DOSSIER --}}
                                    <td class="px-4 py-4">
                                        <a
                                            href="{{ $folderUrl }}"
                                            class="inline-flex items-center gap-3 font-bold text-slate-800 hover:text-blue-700"
                                        >
                                            <span class="text-xl">📁</span>

                                            <span>{{ $project->title }}</span>

                                            <span class="text-xs font-normal text-slate-400">
                                                ({{ $activeFolderTasks->count() }} tâches)
                                            </span>
                                        </a>
                                    </td>

                                    {{-- DATE DE CRÉATION --}}
                                    <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                        {{ $project->created_at
                                            ? \Carbon\Carbon::parse($project->created_at)->format('d/m/Y')
                                            : '—' }}
                                    </td>

                                    {{-- STATUT --}}
                                    <td class="px-4 py-4">
                                        @if($activeFolderTasks->isEmpty())

                                            <span class="rounded-full bg-slate-100 px-3 py-1.5 font-bold text-slate-600">
                                                À commencer
                                            </span>

                                        @elseif($isFinished)

                                            <span class="rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-700">
                                                ✓ Terminé
                                            </span>

                                        @else

                                            <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-700">
                                                ◷ En cours
                                            </span>

                                        @endif
                                    </td>

                                    {{-- ACTIONS DU DOSSIER --}}
                                    <td class="px-4 py-4 text-right">

                                        <div class="flex items-center justify-end gap-2">

                                            {{-- OUVRIR --}}
                                            <a
                                                href="{{ $folderUrl }}"
                                                class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 font-semibold text-blue-700 hover:bg-blue-100"
                                            >
                                                Ouvrir
                                                <span class="ml-2">›</span>
                                            </a>

                                            {{-- SUPPRIMER DÉFINITIVEMENT --}}
                                            <form
                                                action="{{ route('projects.destroy', $project->id) }}"
                                                method="POST"
                                                class="inline"
                                                onclick="event.stopPropagation()"
                                                onsubmit="return confirm('Supprimer définitivement ce dossier et TOUTES ses tâches ? Cette action est irréversible.');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    title="Supprimer définitivement le dossier"
                                                    class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 font-semibold text-red-600 transition hover:bg-red-100"
                                                >
                                                    🗑 Supprimer
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-slate-500">
                                        Aucun dossier Master enregistré.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>
                    </table>

                </div>


            </section>

            @endif {{-- Fin de la liste des dossiers --}}
            @endif {{-- Fin de la navigation extérieure --}}

            @if($selectedMasterFolder || $masterActivityView)


            {{-- CONTENU DU DOSSIER OU ACTIVITÉS GLOBALES --}}

            @if($selectedMasterFolder)

            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-blue-100 bg-white px-4 py-2 shadow-sm">

                <div class="flex flex-wrap items-center gap-3">

                    <a
                        href="{{ route('dashboard', ['view' => 'master']) }}"
                        class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 hover:text-blue-900"
                    >
                        ← Retour aux dossiers
                    </a>

                    <span class="text-slate-300">|</span>

                    <span class="inline-flex items-center gap-2 text-sm font-bold text-slate-800">
                        📂 {{ $selectedMasterFolder->title }}
                    </span>

                </div>

                <button
                    type="button"
                    id="btnToggleForm"
                    onclick="toggleTaskForm()"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700"
                >

                + Nouvelle tâche
            </button>

        </div>

        @endif {{-- En-tête visible uniquement dans un dossier --}}

        {{-- ============================================== --}}
        {{-- TABLEAU UNIQUE DES TÂCHES MASTER               --}}
        {{-- ============================================== --}}

        <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">

            {{-- EN-TÊTE DU TABLEAU --}}
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">

                <div class="flex items-center gap-3">

                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-xl">
                        📋
                    </span>

                    <div>


                        <h2 class="text-lg font-extrabold text-slate-900">
                            @if($selectedMasterFolder)
                                Chapitres et tâches — {{ $selectedMasterFolder->title }}
                            @elseif($filter === 'today')
                                📅 Activités prévues aujourd'hui
                            @else
                                📋 Toutes les activités en cours
                            @endif
                        </h2>

                        <p class="text-xs text-slate-500">
                            {{ $masterFolderTasks->count() }} tâche(s) affichée(s)
                        </p>
                    </div>

                </div>

            </div>

            {{-- TABLEAU UNIQUE --}}

            
            <style>
                /* Tableau Master : affichage compact et lisible */

                .master-table {
                    font-size: 12px;
                    line-height: 1.4;
                }

                .master-table th {
                    padding: 12px 10px;
                    font-size: 10px;
                    font-weight: 800;
                    white-space: nowrap;
                    letter-spacing: 0.025em;
                }

                .master-table td {
                    padding: 11px 10px;
                    font-size: 12px;
                    vertical-align: middle;
                    white-space: nowrap;
                }

                .master-table td a,
                .master-table td span {
                    font-size: inherit;
                }

                .master-table td .text-xs {
                    font-size: 11px;
                }

                .master-table td .text-lg {
                    font-size: 15px;
                }

                .master-table tbody tr {
                    height: 58px;
                }

                .master-table tbody tr:hover {
                    background-color: #f8fbff;
                }

                .master-table td .flex {
                    gap: 6px;
                }

                /* Conservation du défilement sur les petits écrans */
                @media (max-width: 1200px) {
                    .master-table {
                        min-width: 1100px;
                    }
                }
            </style>

            <div class="overflow-x-auto">

                <table class="master-table w-full min-w-[1100px] border-collapse text-left text-xs">

                    <thead class="border-b border-blue-100 bg-blue-50/80">

                        <tr class="text-[11px] font-extrabold uppercase tracking-wide text-slate-600">

                            <th class="px-4 py-4">Leçon / Étape</th>
                            <th class="px-4 py-4">Tâche</th>
                            <th class="px-4 py-4">Exécution</th>
                            <th class="px-4 py-4">Échéance</th>
                            <th class="px-4 py-4">Statut</th>
                            <th class="px-4 py-4">Priorité</th>
                            <th class="px-5 py-4 text-right">Actions</th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">



                        @forelse($masterFolderTasks->sortBy(function ($task) {

                            // 0 = tâches non terminées, 1 = tâches terminées
                            $groupe = $task->document_status === 'done' ? '1' : '0';

                            // Date d'exécution : les dates absentes sont placées en dernier
                            $date = !empty($task->execution_date)
                                ? \Carbon\Carbon::parse($task->execution_date)->format('Y-m-d')
                                : '9999-12-31';

                            // Heure de début : les heures absentes sont placées en dernier
                            $heure = !empty($task->heure_debut)
                                ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i:s')
                                : '23:59:59';

                            // Classement : groupe → date → heure → ID
                            return $groupe . '|' . $date . '|' . $heure . '|' . str_pad((string) $task->id, 12, '0', STR_PAD_LEFT);

                        }) as $task)

                            <tr class="transition-colors hover:bg-blue-50/50">

                                {{-- LEÇON / ÉTAPE --}}
                                <td class="px-4 py-4">

                                    <span class="inline-flex items-center gap-2 font-semibold text-blue-600">

                                        <span>📖</span>

                                        {{ $task->category->title ?? $task->category->name ?? 'Général' }}

                                    </span>

                                </td>


                                {{-- LIBELLÉ ET DOCUMENT --}}
                                <td class="max-w-sm px-4 py-4">

                                    @if($masterActivityView)
                                        <div class="mb-1 text-[10px] font-bold text-amber-700">
                                            📁 {{ $task->project->title ?? $task->project_name ?? 'Sans dossier' }}
                                        </div>
                                    @endif

                                    <a
                                        href="{{ route('tasks.learning', $task->id) }}"
                                        class="font-bold leading-5 text-slate-900 hover:text-blue-700"
                                    >
                                        {{ $task->title }}
                                    </a>

                                    @if($task->document_link)

                                        @php
                                            $link = $task->document_link;

                                            if (
                                                !str_starts_with($link, 'http://') &&
                                                !str_starts_with($link, 'https://') &&
                                                !str_starts_with($link, '/') &&
                                                !str_starts_with($link, 'file://')
                                            ) {
                                                $link = asset('storage/' . $link);
                                            }

                                            $extension = strtolower(
                                                pathinfo($task->document_link, PATHINFO_EXTENSION)
                                            );
                                        @endphp

                                        <a
                                            href="{{ $link }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="mt-1 flex items-center gap-1 text-xs font-semibold underline underline-offset-2 {{ $extension === 'pdf' ? 'text-red-600' : 'text-blue-600' }}"
                                        >
                                            @if($extension === 'pdf')
                                                📄 Ouvrir PDF
                                            @elseif(in_array($extension, ['doc', 'docx']))
                                                📝 Ouvrir Word
                                            @elseif(in_array($extension, ['xls', 'xlsx']))
                                                📊 Ouvrir Excel
                                            @elseif(in_array($extension, ['ppt', 'pptx']))
                                                📽️ Ouvrir PowerPoint
                                            @else
                                                🔗 Ouvrir le document
                                            @endif
                                        </a>

                                    @endif

                                </td>

                                {{-- EXÉCUTION --}}
                                <td class="whitespace-nowrap px-4 py-4 text-slate-600">

                                    <div class="flex items-center gap-2">

                                        <span>🗓️</span>

                                        {{ $task->execution_date
                                            ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y')
                                            : 'Non planifié' }}

                                    </div>

                                    @if($task->heure_debut || $task->heure_fin)

                                        <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">

                                            <span>⏱️</span>

                                            {{ $task->heure_debut
                                                ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i')
                                                : '--:--' }}

                                            à

                                            {{ $task->heure_fin
                                                ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i')
                                                : '--:--' }}

                                        </div>

                                    @endif

                                </td>

                                {{-- ÉCHÉANCE --}}
                                <td class="whitespace-nowrap px-4 py-4 text-slate-600">

                                    {{ $task->date_prevue
                                        ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y')
                                        : '—' }}

                                </td>

                                {{-- STATUT --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    @if($task->is_archived)

                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                            📁 Archivée
                                        </span>

                                    @elseif($task->document_status === 'done')

                                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700">
                                            ✓ Terminée
                                        </span>

                                    @elseif($task->document_status === 'in_progress')

                                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                                            ◷ En cours
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                            ◷ À faire
                                        </span>

                                    @endif

                                </td>

                                {{-- PRIORITÉ --}}
                                <td class="whitespace-nowrap px-4 py-4">

                                    @if($task->priority === 'high')

                                        <span class="inline-flex rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600">
                                            ↑ Haute
                                        </span>

                                    @elseif($task->priority === 'low')

                                        <span class="inline-flex rounded-full bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700">
                                            ↓ Basse
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                            = Moyenne
                                        </span>

                                    @endif

                                </td>

                                {{-- ACTIONS --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- MODIFIER --}}
                                        <button
                                            type="button"
                                            onclick="editTask({{ $task->id }})"
                                            title="Modifier"
                                            aria-label="Modifier la tâche"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700"
                                        >
                                            ✎
                                        </button>

                                        {{-- SUPPRIMER --}}
                                        <form
                                            action="{{ route('tasks.destroy', $task->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Supprimer définitivement cette tâche ?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Supprimer la tâche"
                                                aria-label="Supprimer la tâche"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M4 7h16"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                    <path d="M6 7l1 14h10l1-14"/>
                                                    <path d="M9 7V4h6v3"/>
                                                </svg>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-14 text-center">

                                    <div class="mb-3 text-3xl">
                                        🔍
                                    </div>

                                    <h3 class="font-bold text-slate-800">

                                        {{ request()->filled('search')
                                            ? 'Aucune tâche trouvée'
                                            : 'Aucune tâche à afficher' }}

                                    </h3>

                                    <p class="mt-2 text-sm text-slate-500">

                                        @if(request()->filled('search'))

                                            Aucune tâche ne correspond à
                                            « {{ request('search') }} ».

                                        @else

                                            Les tâches du Mode Master apparaîtront ici.

                                        @endif

                                    </p>

                                    @if(request()->filled('search'))

                                        <a
                                            href="{{ route('dashboard', request()->except(['search', 'page'])) }}"
                                            class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700"
                                        >
                                            Effacer la recherche
                                        </a>

                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

    </section>

    @endif {{-- Fin du contenu du dossier --}}

@endif {{-- Fin du Mode Master --}}

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
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm md:col-span-2">

                <div class="flex justify-between items-center mb-4">

                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        📚 Bibliothèque à lire
                    </h3>


                    <button
                        type="button"
                        onclick="toggleBookForm()"
                        id="btnBookForm"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">

                        📚 Ajouter un livre

                    </button>


                </div>


                <!-- FORMULAIRE CACHE -->

                <div id="bookForm"
                    class="hidden bg-gray-50 border rounded-lg p-4 mb-5">


                    <form 
                    action="{{ route('personal-resources.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-3">


                        @csrf


                        <input 
                        type="hidden"
                        name="type"
                        value="book">



                        <input 
                        type="text"
                        name="title"
                        placeholder="Titre du livre"
                        class="w-full border rounded-lg px-3 py-2"
                        required>



                        <input
                        type="text"
                        name="author_or_source"
                        placeholder="Auteur (optionnel)"
                        class="w-full border rounded-lg px-3 py-2">



                        <input
                        type="file"
                        name="pdf_file"
                        accept="application/pdf"
                        class="w-full border rounded-lg px-3 py-2"
                        required>



                        <div class="flex gap-3">


                            <button
                            type="submit"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold">

                            💾 Ajouter

                            </button>


                            <button
                            type="button"
                            onclick="toggleBookForm()"
                            class="bg-gray-400 text-white px-5 py-2 rounded-lg">

                            Annuler

                            </button>


                        </div>


                    </form>


                </div>




                <!-- TABLEAU DES LIVRES -->


                @if($personalResources->where('type','book')->isNotEmpty())


                <div class="overflow-x-auto">


                    <table class="w-full text-sm text-left">


                        <thead class="bg-gray-100">

                            <tr>

                                <th class="px-4 py-3">
                                    #
                                </th>

                                <th class="px-4 py-3">
                                    Titre
                                </th>

                                <th class="px-4 py-3">
                                    Auteur
                                </th>

                                <th class="px-4 py-3">
                                    Statut
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Actions
                                </th>


                            </tr>


                        </thead>



                        <tbody>


                        @foreach($readingBooks as $index=>$book)

                        <tr class="border-b">


                            <td class="px-4 py-3">
                                {{ $index+1 }}
                            </td>



                            <td class="px-4 py-3">


                                <a href="{{ route('personal-resources.show',$book->id) }}"
                                class="text-blue-600 font-semibold hover:underline">

                                {{ $book->title }}

                                </a>


                            </td>



                            <td class="px-4 py-3">

                                {{ $book->author_or_source ?? '-' }}

                            </td>



                            <td class="px-4 py-3">


                                @if($book->reading_status === 'finished')

                                <span class="text-green-600">
                                ✅ Terminé
                                </span>

                                @else

                                <span class="text-blue-600">
                                📖 Lecture
                                </span>

                                @endif


                            </td>

                            <td class="px-4 py-3 text-right">

                            @if($book->reading_status === 'reading')

                            <span class="text-blue-600">
                            📖 En cours
                            </span>

                            @endif

                            </td>

                            <td class="px-4 py-3">

                                <div class="text-xs font-semibold">
                                    📖 {{ $book->progress ?? 0 }} %
                                </div>

                                <div class="bg-gray-200 h-2 rounded mt-1">

                                    <div 
                                    class="bg-blue-600 h-2 rounded"
                                    style="width: {{ $book->progress ?? 0 }}%">
                                    </div>

                                </div>

                                <div class="text-xs text-gray-500 mt-1">

                                    Page :
                                    {{ $book->current_page ?? 1 }}

                                </div>
                                <form method="POST"
                                action="{{ route('personal-resources.update-reading-start',$book->id) }}"
                                class="mt-2">

                                @csrf


                                <div class="flex items-center gap-2">


                                <input
                                type="number"
                                name="start_page"
                                value="{{ $book->current_page ?? 1 }}"
                                class="border rounded px-2 py-1 w-20 text-xs"
                                >


                                <button
                                class="bg-gray-800 text-white px-3 py-1 rounded text-xs">

                                ⚙️ Modifier départ

                                </button>


                                </div>

                                </form>

                                @if(isset($readingProgress[$book->id]))

                                    <div class="mt-2 text-sm">

                                        🎯 Objectif :
                                        {{ $book->readingGoal->daily_pages ?? 10 }}
                                        pages/jour

                                    </div>


                                    <div class="mt-1 font-bold text-sm">

                                        {{ $readingProgress[$book->id]['message'] }}

                                    </div>

                                @endif

                            </td>


                            <td class="px-4 py-3">

                                @php
                                    $goal = \App\Models\ReadingGoal::where('personal_resource_id',$book->id)
                                            ->where('date',date('Y-m-d'))
                                            ->first();
                                @endphp
                            </td>
                        </tr>


                        @endforeach


                        </tbody>


                    </table>
                    <div class="mt-6 text-center">

                    <button
                    onclick="toggleGlobalLibrary()"
                    id="globalLibraryBtn"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold">

                    📚 Afficher la bibliothèque globale

                    </button>

                    </div>
                    <!-- BIBLIOTHÈQUE GLOBALE CACHÉE -->

                    <div id="globalLibrary"
                    class="hidden mt-6 bg-gray-50 border rounded-xl p-5">


                    <h3 class="font-bold text-lg mb-4 text-gray-800">
                    📚 Bibliothèque globale
                    </h3>



                    <table class="w-full text-sm text-left">


                    <thead class="bg-gray-200">

                    <tr>

                    <th class="px-4 py-3">
                    #
                    </th>


                    <th class="px-4 py-3">
                    Titre
                    </th>


                    <th class="px-4 py-3">
                    Auteur
                    </th>


                    <th class="px-4 py-3">
                    État
                    </th>


                    <th class="px-4 py-3 text-right">
                    Action
                    </th>


                    </tr>

                    </thead>



                    <tbody>
                    @foreach(
                    $personalResources
                    ->where('type','book')
                    as $index=>$book
                    )

                    <tr class="border-b">


                    <td class="px-4 py-3">
                    {{ $index+1 }}
                    </td>



                    <td class="px-4 py-3 font-semibold">

                    <a href="{{ route('personal-resources.show',$book->id) }}"
                    class="text-blue-600 hover:underline">

                    {{ $book->title }}

                    </a>

                    </td>




                    <td class="px-4 py-3">

                    {{ $book->author_or_source ?? '-' }}

                    </td>





                    <td class="px-4 py-3">


                    @if($book->reading_status === 'finished')


                    <span class="text-green-600 font-semibold">

                    ✅ Terminé

                    </span>



                    @elseif($book->reading_status === 'reading')


                    <span class="text-blue-600 font-semibold">

                    📖 En lecture

                    </span>



                    @else


                    <span class="text-gray-600 font-semibold">

                    📚 Disponible

                    </span>


                    @endif



                    </td>

                    <td class="px-4 py-3 text-right flex justify-end items-center gap-3">


                    @if($book->reading_status === 'library')


                    <form  
                    action="{{ route('personal-resources.add-reading',$book->id) }}" 
                    method="POST">

                    @csrf

                    <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">

                    ➕ Ajouter à lire

                    </button>

                    </form>


                    @elseif($book->reading_status === 'finished')


                    <span class="text-green-600">
                    ✔ Déjà lu
                    </span>


                    @else


                    <span class="text-blue-600">
                    📖 En cours
                    </span>


                    @endif



                    <form
                    action="{{ route('personal-resources.destroy',$book->id) }}"
                    method="POST"
                    onsubmit="return confirm('Voulez-vous supprimer ce livre ?');">

                    @csrf
                    @method('DELETE')

                    <button
                    type="submit"
                    class="text-red-500 hover:text-red-700 text-lg">

                    🗑

                    </button>

                    </form>


                    </td>


                    </tr>



                    @endforeach



                    </tbody>



                    </table>

                    </div>                    
                    

                @else


                <p class="text-gray-400 text-center py-5">

                Aucun livre ajouté pour le moment.

                </p>


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
<script>

function toggleBookForm(){

    let form = document.getElementById('bookForm');

    let btn = document.getElementById('btnBookForm');


    if(form.classList.contains('hidden')){

        form.classList.remove('hidden');

        btn.innerHTML="✕ Fermer";

    }

    else{

        form.classList.add('hidden');

        btn.innerHTML="📚 Ajouter un livre";

    }

}
function toggleGlobalLibrary(){

    let box = document.getElementById('globalLibrary');

    let btn = document.getElementById('globalLibraryBtn');


    if(box.classList.contains('hidden')){


        box.classList.remove('hidden');

        btn.innerHTML="📕 Masquer bibliothèque globale";


    }else{


        box.classList.add('hidden');

        btn.innerHTML="📚 Afficher la bibliothèque globale";


    }

}

async function editTask(id) {

    try {

        const response = await fetch(`/tasks/${id}/edit-data`);

        if (!response.ok) {
            throw new Error("Erreur chargement tâche");
        }





        const task = await response.json();

        console.log('DONNÉES DE LA TÂCHE :', task);
        console.log('ÉCHÉANCE :', task.date_prevue);
        console.log('DATE EXÉCUTION :', task.execution_date);

        // Convertir les dates Laravel pour les champs HTML type="date"
        function formatDateForInput(value) {
            if (!value) return '';

            const date = String(value);

            // Accepte YYYY-MM-DD et les dates ISO Laravel
            if (/^\d{4}-\d{2}-\d{2}/.test(date)) {
                return date.substring(0, 10);
            }

            // Accepte DD/MM/YYYY
            if (/^\d{2}\/\d{2}\/\d{4}$/.test(date)) {
                const [day, month, year] = date.split('/');
                return `${year}-${month}-${day}`;
            }

            return '';
        }

        // Synchroniser Alpine
        const form = document.querySelector('#taskForm');

        if(form && form._x_dataStack){

            let alpineData = form._x_dataStack[0];

            alpineData.selectedProject = task.project_id ?? '';
            alpineData.selectedCategory = task.category_id ?? '';


            alpineData.dueDate = formatDateForInput(task.date_prevue);
            alpineData.executionDate = formatDateForInput(task.execution_date);

            alpineData.startTime = task.heure_debut 
                ? task.heure_debut.substring(0,5)
                : '';

            alpineData.endTime = task.heure_fin 
                ? task.heure_fin.substring(0,5)
                : '';

        }


        // Ouvrir le formulaire
        const formContainer = document.getElementById('taskFormContainer');

        formContainer.style.display = 'block';

        formContainer.scrollIntoView({
            behavior: 'smooth'
        });



        // Passage en mode modification
        document.getElementById('taskForm').action =
            "{{ url('/tasks') }}/" + id;

        document.getElementById('methodField').value = "PUT";

        document.getElementById('editTaskId').value = id;


        // Récupérer Alpine
        const alpineData = document.querySelector('#taskForm')._x_dataStack[0];



        // TITRE
        document.querySelector('input[name="title"]').value = task.title ?? '';



        // PROJET
        alpineData.selectedProject = String(task.project_id ?? '');


        // CATEGORIE
        alpineData.selectedCategory = String(task.category_id ?? '');



        // DOCUMENT
        // Afficher l'URL existante lorsque le support est un lien Internet.
        const documentLinkInput = document.getElementById('document_link');

        if (documentLinkInput) {
            const currentLink = task.document_link || '';

            documentLinkInput.value =
                /^https?:\/\//i.test(currentLink) ? currentLink : '';
        }

        // Conserver l'affichage de l'ancien document.
        const oldDocument = document.getElementById('oldDocument');

        if(oldDocument){

            oldDocument.innerHTML = task.document_link
                ? `
                📎 Document actuel :
                <a href="${task.document_link}" target="_blank" class="text-blue-600 underline">
                    Ouvrir le document
                </a>
                `
                : "";

        }


        document.querySelector('input[name="date_prevue"]').value =
            formatDateForInput(task.date_prevue);

        document.querySelector('input[name="execution_date"]').value =
            formatDateForInput(task.execution_date);




        // DATE ÉCHÉANCE
        alpineData.dueDate = formatDateForInput(task.date_prevue);

        // DATE D'EXÉCUTION
        alpineData.executionDate = formatDateForInput(task.execution_date);



        // HEURE DEBUT
        alpineData.startTime = task.heure_debut
            ? task.heure_debut.substring(0,5)
            : '';



        // HEURE FIN
        alpineData.endTime = task.heure_fin
            ? task.heure_fin.substring(0,5)
            : '';



        // STATUT
        document.querySelector('select[name="document_status"]').value =
            task.document_status ?? 'in_progress';



        // PRIORITE
        document.querySelector('select[name="priority"]').value =
            task.priority ?? 'medium';



        // Changer le titre du formulaire
        const titre = document.querySelector('#taskFormContainer h2');

        if(titre){
            titre.innerHTML = "✏️ Modifier la tâche";
        }



    } catch(error) {

        console.error(error);

        alert("Impossible de charger la tâche");

    }

}
</script>

</div> {{-- Fin du conteneur principal du dashboard --}}

@endsection
