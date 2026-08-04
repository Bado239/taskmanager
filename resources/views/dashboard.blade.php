@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- HEADER & COMMUTATEUR DE MODE -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <span>Accueil</span> / <span class="text-gray-600 font-medium">Cockpit d'analyse</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                💼 {{ strtoupper($currentMode) }} Cockpit Livrables 
                <span class="text-xs bg-blue-50 text-[#0052cc] px-2.5 py-0.5 rounded-full font-semibold border border-blue-100">
                    🇸🇳 Dakar, Sénégal
                </span>
            </h1>
        </div>

        <!-- Mode Switcher -->
        <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-lg border border-gray-200 text-xs">
            <span class="text-gray-500 font-medium px-2">Mode actif :</span>
            <a href="{{ route('mode.switch', 'office') }}" 
               class="px-3 py-1.5 font-bold rounded-md transition-all {{ $currentMode === 'office' ? 'bg-[#0052cc] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                💼 Bureau
            </a>
            <a href="{{ route('mode.switch', 'master') }}" 
               class="px-3 py-1.5 font-bold rounded-md transition-all {{ $currentMode === 'master' ? 'bg-[#0052cc] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                🎓 Master
            </a>
        </div>
    </div>

    <!-- KPI PRINCIPAUX -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-[#0052cc] text-white p-5 rounded-xl shadow-sm">
            <span class="text-xs font-semibold uppercase opacity-80">📌 Total tâches</span>
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

    <!-- AVANCEMENT GLOBAL -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-2">
            <h2 class="font-bold text-gray-900 text-base">🔥 Tâches prioritaires activement suivies</h2>
            <p class="text-xs text-gray-500">Ensemble des tâches en cadrage ou en cours de traitement</p>
            <div class="text-3xl font-bold text-gray-900 pt-2">{{ $todoTasks + $doingTasks }}</div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-2">
            <h2 class="font-bold text-gray-900 text-base">📈 Taux d’avancement global</h2>
            @php
                $total = $totalTasks;
                $rate = $total > 0 ? round(($doneTasks / $total) * 100) : 0;
            @endphp
            <div class="text-3xl font-bold text-emerald-600 pt-2">{{ $rate }}%</div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden mt-2">
                <div class="bg-emerald-600 h-3 rounded-full transition-all duration-300" style="width: {{ $rate }}%;"></div>
            </div>
        </div>
    </div>

    <!-- MAIN SECTION: FORMULAIRE + TABLEAU -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- PANNEAU D'ADMINISTRATION : FORMULAIRE DE SAISIE -->
        <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <div>
                <h2 class="font-bold text-gray-900 text-base">⚙️ Panneau d'administration</h2>
                <p class="text-xs text-gray-500">Saisie et modélisation des données.</p>
            </div>

            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="{{ $currentMode }}">

                <!-- Nom du Livrable -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nom du Livrable *</label>
                    <input type="text" name="title" required placeholder="Ex: Rapport d'analyse économétrique"
                           class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>

                <!-- Lien de travail -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lien de Travail</label>
                    <input type="url" name="document_link" placeholder="https://..."
                           class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>

                <!-- Catégorie -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catégorie</label>
                    <select name="category_id" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                        <option value="">-- Sélectionner une catégorie --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title ?? $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Projet -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Associer à un projet</label>
                    <select name="project_id" id="project_id" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                        <option value="">-- Aucun projet --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                        <option value="new">+ Créer un nouveau projet...</option>
                    </select>
                </div>

                <!-- Nouveau projet input -->
                <div id="new_project_div" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nouveau projet</label>
                    <input type="text" name="new_project_name" placeholder="Nom du projet..."
                           class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>

                <!-- État / Phase -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">État du Livrable</label>
                    <select name="document_status" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                        <option value="todo">🔴 Phase 1 : Cadrage</option>
                        <option value="in_progress">🟡 Phase 2 : En cours</option>
                        <option value="done">🟢 Phase 3 : Validé</option>
                    </select>
                </div>

                <!-- Urgence -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Urgence</label>
                    <select name="priority" class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                        <option value="high">Haute</option>
                        <option value="medium" selected>Moyenne</option>
                        <option value="low">Basse</option>
                    </select>
                </div>

                <!-- Échéance -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Échéance</label>
                    <input type="date" name="date_prevue" 
                           class="w-full bg-[#f8fafc] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#0052cc]">
                </div>

                <button type="submit" class="w-full bg-[#0052cc] hover:bg-[#003d99] text-white font-bold text-sm py-2.5 px-4 rounded-lg transition-colors shadow-sm">
                    Enregistrer la donnée
                </button>
            </form>
        </div>

        <!-- TABLEAU DES ACTIVITÉS DU JOUR -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-gray-900 text-base">📊 Top Activités du jour</h2>
                        <span class="text-xs text-gray-400 font-medium">{{ $todayTasks->count() }} au programme</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-[#f8fafc] text-xs uppercase font-semibold text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Rang</th>
                                <th class="px-4 py-3">Détails de l'activité</th>
                                <th class="px-4 py-3">Statut / Canal</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($todayTasks as $index => $task)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 font-bold text-gray-400">#{{ $index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-900">{{ $task->title }}</div>
                                        @if($task->document_link)
                                            <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-[#0052cc] underline">🔗 Document de travail</a>
                                        @endif
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            Projet: {{ $task->project->title ?? 'Aucun' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 space-y-1">
                                        <div>
                                            <span class="bg-blue-50 text-[#0052cc] text-xs font-semibold px-2.5 py-0.5 rounded-full border border-blue-100">
                                                {{ $task->category->title ?? $task->category->name ?? 'Général' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-medium px-2 py-0.5 rounded border 
                                                {{ $task->priority === 'high' ? 'bg-red-50 text-red-600 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                                {{ ucfirst($task->priority ?? 'Normale') }}
                                            </span>
                                        </div>
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
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <div class="text-3xl mb-2">📈</div>
                                        <div class="font-semibold text-gray-600">Aucun trafic d'activités enregistré</div>
                                        <p class="text-xs text-gray-400 mt-1">Saisis tes tâches quotidiennes sur le panneau latéral pour commencer le suivi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('project_id')?.addEventListener('change', function() {
        const div = document.getElementById('new_project_div');
        if (this.value === 'new') {
            div?.classList.remove('hidden');
        } else {
            div?.classList.add('hidden');
        }
    });
</script>
@endsection