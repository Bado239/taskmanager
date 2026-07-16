<x-app-layout>
    @php
        $isMaster = $currentMode === 'master';
        $themeBg = $isMaster ? 'bg-purple-50' : 'bg-slate-50';
        $themeText = $isMaster ? 'text-purple-900' : 'text-slate-900';
        $themeBtn = $isMaster ? 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
        $themeCardHeader = $isMaster ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-800';
        $themeFocus = $isMaster ? 'focus:border-purple-500 focus:ring-purple-500' : 'focus:border-blue-500 focus:ring-blue-500';
        $themeBorder = $isMaster ? 'border-purple-300' : 'border-gray-300';
    @endphp

    <div class="py-6 {{ $themeBg }} min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight {{ $themeText }}">
                        {!! $isMaster ? '🎓 Master Academy <span class="text-sm font-normal text-purple-500">(Préparation Examens)</span>' : '💼 Office Cockpit <span class="text-sm font-normal text-slate-500">(Gestion de Projets & Livrables)</span>' !!}
                    </h1>
                </div>
                
                <div class="flex bg-gray-100 p-1 rounded-lg">
                    <a href="{{ route('mode.switch', 'office') }}" 
                       class="px-4 py-2 rounded-md text-xs font-bold transition flex items-center gap-1.5 {{ !$isMaster ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-800' }}">
                        💼 Bureau
                    </a>
                    <a href="{{ route('mode.switch', 'master') }}" 
                       class="px-4 py-2 rounded-md text-xs font-bold transition flex items-center gap-1.5 {{ $isMaster ? 'bg-white shadow text-purple-600' : 'text-gray-500 hover:text-gray-800' }}">
                        🎓 Master
                    </a>
                </div>
            </div>

            @if($isMaster && $examStats)
                <div class="bg-white p-5 rounded-xl shadow-sm border border-purple-100">
                    <h2 class="text-sm font-bold text-purple-800 mb-3">🔥 Progression globale de tes révisions de semestre</h2>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden mb-2">
                        <div class="bg-purple-600 h-4 rounded-full transition-all duration-500" style="width: {{ $examStats['global_progress'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-gray-500">
                        <span>{{ $examStats['global_progress'] }}% de préparation globale</span>
                        <span>{{ $examStats['total'] }} matière(s) suivie(s)</span>
                    </div>
                </div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                @if(!$isMaster)
                    <h3 class="text-md font-bold text-slate-800 mb-4">💼 Créer une nouvelle tâche de projet</h3>
                    <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="office">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom de la tâche / Livrable</label>
                                <input type="text" name="title" required class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}" placeholder="Ex: Rédaction du rapport d'avancement">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lien vers le document de travail</label>
                                <input type="url" name="document_link" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}" placeholder="Lien Drive, OneDrive, GitHub, etc.">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catégorie</label>
                                <select name="category_id" required class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Associer à un projet</label>
                                    <select name="project_id" id="project_select" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                                        <option value="">-- Aucun projet --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                        <option value="new" class="text-blue-600 font-bold">+ Créer un nouveau projet...</option>
                                    </select>
                                </div>
                                <div id="new_project_field" class="hidden">
                                    <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nom du nouveau projet</label>
                                    <input type="text" name="new_project_name" id="new_project_name" class="w-full text-sm border-blue-300 rounded shadow-sm {{ $themeFocus }}" placeholder="Ex: Étude d'impact environnemental">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Étape du projet</label>
                                    <select name="project_step_id" id="step_select" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                                        <option value="" data-project="">-- Choisis d'abord un projet --</option>
                                        @foreach($projects as $project)
                                            @foreach($project->steps as $step)
                                                <option value="{{ $step->id }}" data-project="{{ $project->id }}" class="hidden">{{ $step->name }}</option>
                                            @endforeach
                                        @endforeach
                                        <option value="new" id="option_new_step" class="hidden text-blue-600 font-bold">+ Créer une nouvelle étape...</option>
                                    </select>
                                </div>
                                <div id="new_step_field" class="hidden">
                                    <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nom de la nouvelle étape</label>
                                    <input type="text" name="new_step_name" id="new_step_name" class="w-full text-sm border-blue-300 rounded shadow-sm {{ $themeFocus }}" placeholder="Ex: Phase d'analyse des données">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Statut Initial du Livrable</label>
                                <select name="document_status" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                                    <option value="todo">🔴 Phase 1 : Cadrage & Rédaction (À traiter)</option>
                                    <option value="in_progress">🟡 Phase 2 : Analyse & Revue en cours</option>
                                    <option value="done">🟢 Phase 3 : Validé et Classé</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Priorité</label>
                                <select name="priority" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                                    <option value="high">Haute</option>
                                    <option value="medium" selected>Moyenne</option>
                                    <option value="low">Basse</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date limite de livraison</label>
                                <input type="date" name="date_prevue" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="w-full md:w-auto py-2.5 px-6 rounded font-bold text-xs text-white {{ $themeBtn }} transition">
                                Enregistrer au Bureau
                            </button>
                        </div>
                    </form>
                @else
                    <h3 class="text-md font-bold text-purple-800 mb-4">🎓 Planifier une Matière / Préparation d'Examen</h3>
                    <form action="{{ route('tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf
                        <input type="hidden" name="type" value="master">

                        <div>
                            <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Nom de la matière ou leçon</label>
                            <input type="text" name="title" required class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}" placeholder="Ex: Calcul Stochastique">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Supports de cours / Drives / TD (URL)</label>
                            <input type="url" name="document_link" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}" placeholder="Lien vers tes cours PDF, exercices, etc.">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Date de l'examen</label>
                            <input type="date" name="date_prevue" class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Catégorie</label>
                            <select name="category_id" required class="w-full text-sm {{ $themeBorder }} rounded shadow-sm {{ $themeFocus }}">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2 flex items-center">
                            <span class="text-[11px] text-purple-400 italic">💡 L'ajout d'un document URL te permettra de générer des quiz et examens blancs d'entraînement via notre module IA.</span>
                        </div>

                        <button type="submit" class="w-full py-2 px-4 rounded font-bold text-xs text-white {{ $themeBtn }} transition">
                            Ajouter la Matière
                        </button>
                    </form>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($todayTasks as $task)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
                        
                        <div class="p-4 {{ $themeCardHeader }} flex justify-between items-center">
                            <span class="text-xs font-extrabold uppercase tracking-wider">
                                {{ $task->priority === 'high' ? '🚨 Priorité Haute' : ($task->priority === 'medium' ? '⚡ Priorité Moyenne' : '🟢 Priorité Basse') }}
                            </span>
                            @if($task->date_prevue)
                                <span class="text-xs opacity-75 font-medium">🎯 {{ \Carbon\Carbon::parse($task->date_prevue)->format('d M') }}</span>
                            @endif
                        </div>

                        <div class="p-5 flex-1">
                            <h4 class="text-md font-bold text-gray-800 mb-1">{{ $task->title }}</h4>

                            @if(!$isMaster && $task->project)
                                <div class="mb-3 flex flex-wrap gap-1 items-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                        📁 Projet : {{ $task->project->name }}
                                    </span>
                                    @if($task->step)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                            📍 Étape : {{ $task->step->name }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">
                                    {{ $isMaster ? '📚 Supports & Ressources' : '📁 Livrable & Document' }}
                                </span>
                                @if($task->document_link)
                                    <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-blue-600 hover:underline font-semibold block mb-2 overflow-hidden text-ellipsis whitespace-nowrap">
                                        🔗 Consulter les ressources externes
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic block mb-2">Aucun document joint</span>
                                @endif

                                @if(!$isMaster)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase
                                        {{ $task->document_status === 'done' ? 'bg-green-100 text-green-700' : ($task->document_status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : ($task->document_status === 'todo' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500')) }}">
                                        {{ $task->document_status === 'done' ? '🟢 Validé / Livré' : ($task->document_status === 'in_progress' ? '🟡 Phase de Revue' : ($task->document_status === 'todo' ? '🔴 Phase de Cadrage' : 'Non spécifié')) }}
                                    </span>
                                @endif
                            </div>

                            @if($isMaster)
                                <form action="{{ route('tasks.updatePrep', $task->id) }}" method="POST" class="space-y-2 mt-4 pt-3 border-t border-gray-100">
                                    @csrf
                                    <span class="text-[10px] font-bold text-purple-400 uppercase block mb-2">🧠 Progression de Révision</span>
                                    
                                    @php $prep = $task->examPrep; @endphp
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="course_reviewed" onchange="this.form.submit()" {{ $prep && $prep->course_reviewed ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                        <span>📖 Cours assimilé & relu</span>
                                    </label>

                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="summary_done" onchange="this.form.submit()" {{ $prep && $prep->summary_done ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                        <span>📝 Fiche de révision rédigée</span>
                                    </label>

                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="exercises_done" onchange="this.form.submit()" {{ $prep && $prep->exercises_done ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                        <span>📐 Exercices & TD pratiqués</span>
                                    </label>

                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="past_papers_done" onchange="this.form.submit()" {{ $prep && $prep->past_papers_done ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                        <span>📚 Annales d'examens validées</span>
                                    </label>
                                </form>

                                <div class="mt-4 pt-3 border-t border-dashed border-purple-100">
                                    <span class="text-[10px] font-bold text-purple-500 uppercase block mb-1.5">🎯 Entraînement IA</span>
                                    @if($task->document_link)
                                        <a href="#" class="inline-flex items-center justify-center w-full py-2 px-3 rounded text-[11px] font-extrabold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition gap-1">
                                            📝 Générer des propositions d'examen
                                        </a>
                                    @else
                                        <span class="text-[10px] text-gray-400 italic">Renseigne un lien de cours (Drive/PDF) ci-dessus pour générer des évaluations par IA.</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                            @if($isMaster)
                                <a href="{{ route('flashcards.show', $task->id) }}" class="text-xs text-purple-700 hover:text-purple-900 font-extrabold flex items-center gap-1">
                                    🧠 Réviser (Flashcards IA)
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">Espace Collaboratif</span>
                            @endif

                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Supprimer cette activité ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold">🗑️ Supprimer</button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-xl shadow-sm border border-gray-100">
                        <p class="text-gray-400 text-sm font-medium">Aucune activité enregistrée pour ce mode pour le moment. Commencez par en ajouter une ! 🚀</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    @if(!$isMaster)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const projectSelect = document.getElementById('project_select');
            const stepSelect = document.getElementById('step_select');
            const newProjectField = document.getElementById('new_project_field');
            const newStepField = document.getElementById('new_step_field');
            const newProjectInput = document.getElementById('new_project_name');
            const newStepInput = document.getElementById('new_step_name');
            const optionNewStep = document.getElementById('option_new_step');

            function updateSteps() {
                const selectedProjectVal = projectSelect.value;
                
                // Réinitialise l'affichage du champ "Nouveau projet"
                if (selectedProjectVal === 'new') {
                    newProjectField.classList.remove('hidden');
                    newProjectInput.required = true;
                } else {
                    newProjectField.classList.add('hidden');
                    newProjectInput.required = false;
                    newProjectInput.value = '';
                }

                // Masque toutes les options d'étape initialement
                const options = stepSelect.querySelectorAll('option');
                let visibleStepsCount = 0;

                options.forEach(opt => {
                    const parentProjId = opt.getAttribute('data-project');
                    if (parentProjId) {
                        if (selectedProjectVal && parentProjId === selectedProjectVal) {
                            opt.classList.remove('hidden');
                            visibleStepsCount++;
                        } else {
                            opt.classList.add('hidden');
                        }
                    }
                });

                // Gère l'affichage de l'option d'ajout d'une nouvelle étape
                if (selectedProjectVal && selectedProjectVal !== 'new') {
                    optionNewStep.classList.remove('hidden');
                } else if (selectedProjectVal === 'new') {
                    // Si nouveau projet, on doit pouvoir lui assigner une première étape directement
                    optionNewStep.classList.remove('hidden');
                } else {
                    optionNewStep.classList.add('hidden');
                }

                // Force le select à revenir à l'option vide si la sélection actuelle n'est plus valable
                const currentSelectedOpt = stepSelect.options[stepSelect.selectedIndex];
                if (currentSelectedOpt && currentSelectedOpt.getAttribute('data-project') && currentSelectedOpt.getAttribute('data-project') !== selectedProjectVal) {
                    stepSelect.value = '';
                }

                updateStepField();
            }

            function updateStepField() {
                if (stepSelect.value === 'new') {
                    newStepField.classList.remove('hidden');
                    newStepInput.required = true;
                } else {
                    newStepField.classList.add('hidden');
                    newStepInput.required = false;
                    newStepInput.value = '';
                }
            }

            // Écouteurs d'événements
            projectSelect.addEventListener('change', updateSteps);
            stepSelect.addEventListener('change', updateStepField);

            // Initialisation au chargement
            updateSteps();
        });
    </script>
    @endif
</x-app-layout>