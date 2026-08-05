@extends('layouts.app')

@section('content')
@php
    $defaultCatOffice = $categories->firstWhere('title', 'CGP')?->id ?? $categories->firstWhere('name', 'CGP')?->id ?? '';
    $defaultCatMaster = $categories->firstWhere('title', 'Master ISEF1')?->id ?? $categories->firstWhere('name', 'Master ISEF1')?->id ?? '';
    
    // Détermination du libellé et de la valeur par défaut pour le type de tâche
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

        <!-- BOUTON D'OUVERTURE DU FORMULAIRE EN HAUT -->
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

        <form action="{{ route('tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            
            <!-- Si on est sur Dashboard, on met un sélecteur de type. Sinon, un input hidden fixe. -->
            @if($view === 'dashboard')
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Destination de la Tâche *</label>
                <select name="type" required class="w-full bg-blue-50 border border-blue-200 text-[#0052cc] font-bold rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="office">💼 Mode Office</option>
                    <option value="master">🎓 Mode Master</option>
                </select>
            </div>
            @else
                <input type="hidden" name="type" value="{{ $view }}">
            @endif

            {{-- Intitulé du livrable --}}
            <div class="{{ $view === 'dashboard' ? '' : 'md:col-span-1' }}">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nom du Livrable *</label>
                <input type="text" name="title" required placeholder="Ex: Rapport d'analyse"
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            {{-- Lien du document --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Lien de Travail</label>
                <input type="url" name="document_link" placeholder="https://..."
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            {{-- Catégorie --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catégorie</label>
                <select name="category_id" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="">-- Sélectionner --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ ($view === 'office' && $category->id == $defaultCatOffice) || ($view === 'master' && $category->id == $defaultCatMaster) ? 'selected' : '' }}>
                            {{ $category->title ?? $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Gestion dynamique des projets avec Alpine.js --}}
            <div x-data="{ selectedProject: '' }" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Sélection de projet existant --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Associer à un projet existant</label>
                    <div class="flex gap-2">
                        <select name="project_id" x-model="selectedProject" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                            <option value="">-- Aucun projet --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                            <option value="new">➕ Créer un nouveau projet...</option>
                        </select>
                    </div>

                    {{-- Bouton pour supprimer le projet sélectionné s'il y en a un --}}
                    <template x-if="selectedProject && selectedProject !== 'new'">
                        <div class="mt-1.5 flex items-center justify-between bg-red-50 border border-red-100 px-3 py-1 rounded-lg">
                            <span class="text-xs text-red-600 font-medium">Gérer ce projet :</span>
                            <button type="button" 
                                    @click="if(confirm('Voulez-vous vraiment supprimer ce projet ?')) {
                                        fetch('/projects/' + selectedProject, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json'
                                            }
                                        }).then(response => {
                                            if(response.ok) {
                                                window.location.reload();
                                            } else {
                                                alert('Erreur lors de la suppression du projet.');
                                            }
                                        });
                                    }"
                                    class="text-xs font-bold text-red-600 hover:text-red-800 underline">
                                Supprimer de la liste
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Champ pour ajouter un nouveau projet (affiché dynamiquement) --}}
                <div x-show="selectedProject === 'new'" style="display: none;">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ou créer un nouveau projet *</label>
                    <input type="text" name="new_project_name" placeholder="Nom du nouveau projet..."
                           class="w-full bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>
            </div>

            {{-- État initial --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">État du Livrable</label>
                <select name="document_status" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="todo">🔴 Phase 1 : Cadrage</option>
                    <option value="in_progress">🟡 Phase 2 : En cours</option>
                    <option value="done">🟢 Phase 3 : Validé</option>
                </select>
            </div>

            {{-- Priorité --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Urgence</label>
                <select name="priority" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                    <option value="high">Haute</option>
                    <option value="medium" selected>Moyenne</option>
                    <option value="low">Basse</option>
                </select>
            </div>

            {{-- Échéance --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Échéance</label>
                <input type="date" name="date_prevue" 
                       class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
            </div>

            {{-- Soumission --}}
            <div class="md:col-span-3 pt-2">
                <button type="submit" class="bg-[#0052cc] hover:bg-[#003d99] text-white font-bold text-sm py-2.5 px-6 rounded-lg transition-colors shadow-sm">
                    💾 Valider et Enregistrer la tâche
                </button>
            </div>
        </form>
    </div>

    <!-- SCRIPT JAVASCRIPT POUR AFFICHER / MASQUER LE FORMULAIRE -->
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

    <!-- VUE 1 : DASHBOARD -->
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

    <!-- VUE 2 : MODE OFFICE -->
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

    <!-- VUE 3 : MODE MASTER -->
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
                                            <span class="bg-[#e6f4ea] text-[#137333] text-xs font-semibold px-2 py-0.5 rounded border border-emerald-200">🟢 Validé</span>
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