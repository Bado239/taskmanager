<x-app-layout>
    @php
        $isMaster = $currentMode === 'master';
        $themeBg = $isMaster ? 'bg-purple-50/50' : 'bg-slate-50/50';
        $themeText = $isMaster ? 'text-purple-900' : 'text-slate-900';
        $themeBtn = $isMaster ? 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
        $themeCardHeader = $isMaster ? 'bg-purple-100/60 text-purple-800' : 'bg-slate-100/60 text-slate-800';
        $themeFocus = $isMaster ? 'focus:border-purple-500 focus:ring-purple-500' : 'focus:border-blue-500 focus:ring-blue-500';
        $themeBorder = $isMaster ? 'border-purple-200' : 'border-gray-200';
    @endphp

    <div class="py-8 {{ $themeBg }} min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- En-tête de Cockpit unifié -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-5 rounded-2xl shadow-sm border border-gray-100 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight {{ $themeText }} flex items-center gap-2">
                        {!! $isMaster ? '🎓 Master Academy <span class="text-xs font-semibold px-2 py-1 bg-purple-100 text-purple-700 rounded-full">Préparation Examens</span>' : '💼 Office Cockpit <span class="text-xs font-semibold px-2 py-1 bg-blue-100 text-blue-700 rounded-full">Gestion Projets & Livrables</span>' !!}
                    </h1>
                    <p class="text-xs text-gray-400 mt-1">Gère et pilote tes objectifs quotidiens avec efficacité.</p>
                </div>
                
                <div class="flex bg-gray-100 p-1 rounded-xl w-full sm:w-auto">
                    <a href="{{ route('mode.switch', 'office') }}" 
                       class="flex-1 sm:flex-none text-center px-5 py-2.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ !$isMaster ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-800' }}">
                        💼 Bureau
                    </a>
                    <a href="{{ route('mode.switch', 'master') }}" 
                       class="flex-1 sm:flex-none text-center px-5 py-2.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $isMaster ? 'bg-white shadow text-purple-600' : 'text-gray-500 hover:text-gray-800' }}">
                        🎓 Master
                    </a>
                </div>
            </div>

            <!-- Stats d'examens (Master) -->
            @if($isMaster && $examStats)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-purple-100">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-sm font-bold text-purple-800 flex items-center gap-1.5">🔥 Progression globale de tes révisions de semestre</h2>
                        <span class="text-xs font-extrabold bg-purple-100 text-purple-700 px-2 py-1 rounded">{{ $examStats['total'] }} matière(s) suivie(s)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden mb-2 p-0.5 border border-gray-200/50">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ $examStats['global_progress'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-gray-500">
                        <span>{{ $examStats['global_progress'] }}% de préparation globale</span>
                    </div>
                </div>
            @endif

            <!-- Layout principal : Formulaire de Saisie (Gauche) + Liste des tâches (Droite) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- 1. Colonne de gauche : Formulaire de saisie -->
                <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-6">
                    @if(!$isMaster)
                        <div class="border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">💼 Nouvelle tâche</h3>
                            <p class="text-xs text-gray-400 mt-1">Crée ou assigne un livrable à un projet.</p>
                        </div>

                        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="type" value="office">

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom de la tâche / Livrable</label>
                                <input type="text" name="title" required class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}" placeholder="Ex: Rédaction du rapport...">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lien du document de travail</label>
                                <input type="url" name="document_link" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}" placeholder="Lien Drive, OneDrive, GitHub, etc.">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catégorie</label>
                                <select name="category_id" required class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="border-t border-gray-100 pt-3 mt-3 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Associer à un projet</label>
                                    <select name="project_id" id="project_select" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                        <option value="">-- Aucun projet --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                        <option value="new" class="text-blue-600 font-bold">+ Créer un nouveau projet...</option>
                                    </select>
                                </div>

                                <div id="new_project_field" class="hidden animate-fadeIn">
                                    <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nom du nouveau projet</label>
                                    <input type="text" name="new_project_name" id="new_project_name" class="w-full text-sm border-blue-300 rounded-lg shadow-sm {{ $themeFocus }}" placeholder="Ex: Étude d'impact...">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Étape du projet</label>
                                    <select name="project_step_id" id="step_select" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                        <option value="" data-project="">-- Choisis d'abord un projet --</option>
                                        @foreach($projects as $project)
                                            @foreach($project->steps as $step)
                                                <option value="{{ $step->id }}" data-project="{{ $project->id }}" class="hidden">{{ $step->name }}</option>
                                            @endforeach
                                        @endforeach
                                        <option value="new" id="option_new_step" class="hidden text-blue-600 font-bold">+ Créer une nouvelle étape...</option>
                                    </select>
                                </div>

                                <div id="new_step_field" class="hidden animate-fadeIn">
                                    <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nom de la nouvelle étape</label>
                                    <input type="text" name="new_step_name" id="new_step_name" class="w-full text-sm border-blue-300 rounded-lg shadow-sm {{ $themeFocus }}" placeholder="Ex: Phase d'analyse...">
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-3 mt-3 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Statut Initial</label>
                                    <select name="document_status" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                        <option value="todo">🔴 Phase 1 : Cadrage & Rédaction</option>
                                        <option value="in_progress">🟡 Phase 2 : Revue en cours</option>
                                        <option value="done">🟢 Phase 3 : Validé et Classé</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Priorité</label>
                                        <select name="priority" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                            <option value="high">Haute</option>
                                            <option value="medium" selected>Moyenne</option>
                                            <option value="low">Basse</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Limite livraison</label>
                                        <input type="date" name="date_prevue" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full mt-4 py-3 px-4 rounded-lg font-bold text-xs text-white {{ $themeBtn }} shadow-md transition">
                                Enregistrer l'activité
                            </button>
                        </form>
                    @else
                        <div class="border-b border-gray-100 pb-4 mb-4">
                            <h3 class="text-sm font-extrabold text-purple-800 uppercase tracking-wider">🎓 Planifier un cours</h3>
                            <p class="text-xs text-gray-400 mt-1">Inscris une nouvelle matière à réviser.</p>
                        </div>

                        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="type" value="master">

                            <div>
                                <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Nom de la matière ou leçon</label>
                                <input type="text" name="title" required class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}" placeholder="Ex: Calcul Stochastique">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Supports / Drives (URL)</label>
                                <input type="url" name="document_link" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}" placeholder="Lien vers tes cours PDF, TD...">
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Date de l'examen</label>
                                    <input type="date" name="date_prevue" class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-purple-500 uppercase mb-1">Catégorie</label>
                                    <select name="category_id" required class="w-full text-sm {{ $themeBorder }} rounded-lg shadow-sm {{ $themeFocus }}">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                                <span class="text-[10px] text-purple-600 italic block leading-relaxed">💡 L'ajout d'un document URL te permettra de générer des quiz d'entraînement via notre module IA.</span>
                            </div>

                            <button type="submit" class="w-full py-3 px-4 rounded-lg font-bold text-xs text-white {{ $themeBtn }} shadow-md transition">
                                Ajouter la Matière
                            </button>
                        </form>
                    @endif
                </div>

                <!-- 2. Colonne de droite : Liste des activités du jour (2/3 de l'écran) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-200/50 pb-3 mb-2">
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-gray-500">📅 Activités prévues aujourd'hui</h2>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-bold">{{ $todayTasks->count() }} au programme</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($todayTasks as $task)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                                
                                <div class="px-4 py-3 {{ $themeCardHeader }} flex justify-between items-center border-b border-gray-100">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider">
                                        {{ $task->priority === 'high' ? '🚨 Haute' : ($task->priority === 'medium' ? '⚡ Moyenne' : '🟢 Basse') }}
                                    </span>
                                    @if($task->date_prevue)
                                        <span class="text-[10px] opacity-75 font-bold">🎯 {{ \Carbon\Carbon::parse($task->date_prevue)->format('d M') }}</span>
                                    @endif
                                </div>

                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h4 class="text-sm font-extrabold text-gray-800 mb-2 leading-tight">{{ $task->title }}</h4>

                                        @if(!$isMaster && $task->project)
                                            <div class="mb-3 flex flex-wrap gap-1 items-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                                    📁 {{ $task->project->name }}
                                                </span>
                                                @if($task->step)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                                        📍 {{ $task->step->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="mb-4 bg-slate-50/80 p-3 rounded-xl border border-gray-100">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase block mb-1">
                                                {{ $isMaster ? '📚 Supports & Ressources' : '📁 Livrable & Document' }}
                                            </span>
                                            @if($task->document_link)
                                                <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-blue-600 hover:underline font-bold block mb-2 truncate">
                                                    🔗 Ouvrir le document joint
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic block mb-2">Aucun lien rattaché</span>
                                            @endif

                                            @if(!$isMaster)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-bold uppercase
                                                    {{ $task->document_status === 'done' ? 'bg-green-100 text-green-700' : ($task->document_status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : ($task->document_status === 'todo' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500')) }}">
                                                    {{ $task->document_status === 'done' ? '🟢 Validé' : ($task->document_status === 'in_progress' ? '🟡 En Revue' : ($task->document_status === 'todo' ? '🔴 À traiter' : 'Non spécifié')) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($isMaster)
                                        <form action="{{ route('tasks.updatePrep', $task->id) }}" method="POST" class="space-y-1.5 mt-4 pt-3 border-t border-gray-100">
                                            @csrf
                                            <span class="text-[9px] font-extrabold text-purple-400 uppercase block mb-1">🧠 Suivi de Révision</span>
                                            
                                            @php $prep = $task->examPrep; @endphp
                                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer hover:text-purple-600">
                                                <input type="checkbox" name="course_reviewed" onchange="this.form.submit()" {{ $prep && $prep->course_reviewed ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                <span>📖 Cours relu & acquis</span>
                                            </label>

                                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer hover:text-purple-600">
                                                <input type="checkbox" name="summary_done" onchange="this.form.submit()" {{ $prep && $prep->summary_done ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                <span>📝 Fiche de révision faite</span>
                                            </label>

                                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer hover:text-purple-600">
                                                <input type="checkbox" name="exercises_done" onchange="this.form.submit()" {{ $prep && $prep->exercises_done ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                <span>📐 Exercices validés</span>
                                            </label>

                                            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer hover:text-purple-600">
                                                <input type="checkbox" name="past_papers_done" onchange="this.form.submit()" {{ $prep && $prep->past_papers_done ? 'checked' : '' }} class="rounded text-purple-600 focus:ring-purple-500">
                                                <span>📚 Annales d'examens faites</span>
                                            </label>
                                        </form>

                                        <div class="mt-4 pt-3 border-t border-dashed border-purple-100">
                                            @if($task->document_link)
                                                <a href="#" class="inline-flex items-center justify-center w-full py-2 px-3 rounded-lg text-[10px] font-extrabold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition gap-1">
                                                    📝 Générer un examen blanc IA
                                                </a>
                                            @else
                                                <span class="text-[9px] text-gray-400 italic">Lie tes supports d'examen pour débloquer l'assistant IA.</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="px-5 py-3 bg-slate-50 border-t border-gray-100 flex justify-between items-center">
                                    @if($isMaster)
                                        <a href="{{ route('flashcards.show', $task->id) }}" class="text-xs text-purple-700 hover:text-purple-900 font-extrabold flex items-center gap-1">
                                            🧠 Flashcards IA
                                        </a>
                                    @else
                                        <span class="text-[10px] text-gray-400 font-bold uppercase">📂 Bureau collaboratif</span>
                                    @endif

                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Supprimer cette activité ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold flex items-center gap-1">🗑️ Supprimer</button>
                                    </form>
                                </div>

                            </div>
                        @empty
                            <div class="col-span-full bg-white p-16 text-center rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center">
                                <div class="text-4xl mb-3">🚀</div>
                                <h3 class="text-sm font-extrabold text-gray-700">Aucune activité pour le moment !</h3>
                                <p class="text-xs text-gray-400 mt-1">Crée ta première tâche à l'aide du panneau d'enregistrement pour commencer à suivre ton avancement aujourd'hui.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

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
                
                if (selectedProjectVal === 'new') {
                    newProjectField.classList.remove('hidden');
                    newProjectInput.required = true;
                } else {
                    newProjectField.classList.add('hidden');
                    newProjectInput.required = false;
                    newProjectInput.value = '';
                }

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

                if (selectedProjectVal && selectedProjectVal !== 'new') {
                    optionNewStep.classList.remove('hidden');
                } else if (selectedProjectVal === 'new') {
                    optionNewStep.classList.remove('hidden');
                } else {
                    optionNewStep.classList.add('hidden');
                }

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

            projectSelect.addEventListener('change', updateSteps);
            stepSelect.addEventListener('change', updateStepField);

            updateSteps();
        });
    </script>
    @endif
</x-app-layout>