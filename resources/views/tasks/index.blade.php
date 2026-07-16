<x-app-layout>
    @php
        $isMaster = $currentMode === 'master';
        $themeBg = 'bg-[#f4f5f8]'; // Le gris signature de Similarweb
        $themeText = $isMaster ? 'text-purple-900' : 'text-[#1e293b]';
        $themeBtn = $isMaster ? 'bg-purple-600 hover:bg-purple-700' : 'bg-[#1862ff] hover:bg-[#004be5]'; // Bleu électrique
        $themeFocus = $isMaster ? 'focus:ring-purple-500' : 'focus:ring-[#1862ff]';
        $themeBorder = 'border-gray-200';
    @endphp

    <div class="min-h-screen {{ $themeBg }} font-sans antialiased flex flex-col lg:flex-row">

        <!-- ─── BARRE LATÉRALE VERTICALE (STYLE SIMILARWEB) ─── -->
        <aside class="w-full lg:w-64 bg-white border-r border-gray-200/80 flex flex-col shrink-0 lg:fixed lg:inset-y-0 lg:left-0 z-20">
            <!-- Header de la Sidebar : Logo & Titre -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <span class="text-2xl">📋</span>
                <div>
                    <h2 class="text-sm font-black tracking-tight text-[#0f172a] uppercase">TaskManager</h2>
                    <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Console v1.0</span>
                </div>
            </div>

            <!-- Liens de Navigation Verticaux -->
            <nav class="flex-1 px-4 py-6 space-y-1.5">
                <a href="{{ route('tasks.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all bg-blue-50 text-[#1862ff] border border-blue-100/50">
                    <span class="text-lg">📅</span>
                    <span>Aujourd'hui</span>
                </a>

                <a href="{{ route('tasks.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:text-[#1862ff] hover:bg-gray-50 transition-all">
                    <span class="text-lg">📊</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('tasks.veille') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:text-[#1862ff] hover:bg-gray-50 transition-all">
                    <span class="text-lg">📡</span>
                    <span>Veille Tech</span>
                </a>

                <a href="{{ route('tasks.create') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:text-[#1862ff] hover:bg-gray-50 transition-all">
                    <span class="text-lg">➕</span>
                    <span>Ajouter une activité</span>
                </a>
            </nav>

            <!-- Pied de la Sidebar : Mode Actif rapide -->
            <div class="p-4 border-t border-gray-100 bg-[#fafbfe]">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 px-2">Mode actif actuel</div>
                <div class="flex bg-gray-100 p-0.5 rounded-lg border border-gray-200">
                    <a href="{{ route('mode.switch', 'office') }}" 
                       class="flex-1 text-center py-1.5 rounded-md text-[10px] font-bold transition-all {{ !$isMaster ? 'bg-white shadow-sm text-[#1862ff]' : 'text-gray-500 hover:text-gray-800' }}">
                        💼 Office
                    </a>
                    <a href="{{ route('mode.switch', 'master') }}" 
                       class="flex-1 text-center py-1.5 rounded-md text-[10px] font-bold transition-all {{ $isMaster ? 'bg-white shadow-sm text-purple-600' : 'text-gray-500 hover:text-gray-800' }}">
                        🎓 Master
                    </a>
                </div>
            </div>
        </aside>

        <!-- ─── ZONE DE CONTENU PRINCIPALE (DÉCALÉE À DROITE SUR GRAND ÉCRAN) ─── -->
        <main class="flex-1 lg:pl-64 min-w-0">
            <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Affichage des erreurs de validation -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-xs space-y-1 shadow-sm">
                        <p class="font-bold">⚠️ Erreur de validation :</p>
                        <ul class="list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Similarweb style Header / Breadcrumb -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm gap-4">
                    <div>
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <span>Home</span> <span class="text-gray-300">/</span> <span class="text-[#1862ff]">Cockpit d'analyse</span>
                        </div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-[#0f172a] flex items-center gap-2">
                            @if($isMaster)
                                🎓 Master Academy <span class="text-xs font-semibold px-2.5 py-0.5 bg-purple-50 text-purple-700 rounded-full border border-purple-100">Révisions</span>
                            @else
                                💼 Office Cockpit <span class="text-xs font-semibold px-2.5 py-0.5 bg-blue-50 text-[#1862ff] rounded-full border border-blue-100">Livrables</span>
                            @endif
                        </h1>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-xl">
                            🇸🇳 Dakar, Sénégal
                        </span>
                    </div>
                </div>

                <!-- Stats d'examens (Similaire aux widgets de trafic Similarweb) -->
                @if($isMaster && $examStats)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm col-span-1 md:col-span-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Progression Globale du Semestre</span>
                            <div class="text-3xl font-black text-purple-800 mt-2 mb-3">{{ $examStats['global_progress'] }}%</div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-500" style="width: {{ $examStats['global_progress'] }}%"></div>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Matières actives</span>
                            <div class="text-3xl font-black text-[#0f172a] mt-2">{{ $examStats['total'] }}</div>
                            <span class="text-[10px] text-green-500 font-bold">100% de suivi</span>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Révisions faites</span>
                            <div class="text-3xl font-black text-[#0f172a] mt-2">{{ $examStats['reviewed'] }}</div>
                            <span class="text-[10px] text-gray-400 italic">Sur l'ensemble</span>
                        </div>
                    </div>
                @endif

                <!-- Layout principal : 2 Colonnes de style Analytique -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    
                    <!-- Panneau de saisie rapide (Style Sidebar d'analyse) -->
                    <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-5">
                        <div class="border-b border-gray-100 pb-3">
                            <h3 class="text-sm font-extrabold text-[#0f172a] uppercase tracking-wider">⚙️ Panneau d'administration</h3>
                            <p class="text-xs text-gray-400 mt-1">Saisie et modélisation des données.</p>
                        </div>

                        @if(!$isMaster)
                            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="type" value="office">

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nom du Livrable</label>
                                    <input type="text" name="title" required class="w-full text-sm border-gray-200 rounded-xl shadow-sm focus:border-[#1862ff] focus:ring-[#1862ff] px-4 py-2.5" placeholder="Ex: Rapport mensuel...">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Lien de Travail</label>
                                    <input type="url" name="document_link" class="w-full text-sm border-gray-200 rounded-xl shadow-sm focus:border-[#1862ff] focus:ring-[#1862ff] px-4 py-2.5" placeholder="Drive, Notion, GitHub...">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Catégorie</label>
                                    <select name="category_id" required class="w-full text-sm border-gray-200 rounded-xl shadow-sm focus:border-[#1862ff] focus:ring-[#1862ff] px-4 py-2.5 bg-white">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="border-t border-gray-100 pt-3 space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Associer à un projet</label>
                                        <select name="project_id" id="project_select" class="w-full text-sm border-gray-200 rounded-xl shadow-sm focus:border-[#1862ff] focus:ring-[#1862ff] px-4 py-2.5 bg-white">
                                            <option value="">-- Aucun projet --</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                            <option value="new" class="text-[#1862ff] font-bold">+ Créer un nouveau projet...</option>
                                        </select>
                                    </div>

                                    <div id="new_project_field" class="hidden">
                                        <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nouveau projet</label>
                                        <input type="text" name="new_project_name" id="new_project_name" class="w-full text-sm border-blue-200 rounded-xl focus:ring-blue-500 px-4 py-2.5" placeholder="Nom du projet...">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Étape liée</label>
                                        <select name="project_step_id" id="step_select" class="w-full text-sm border-gray-200 rounded-xl shadow-sm focus:border-[#1862ff] focus:ring-[#1862ff] px-4 py-2.5 bg-white">
                                            <option value="" data-project="">-- Choisis un projet --</option>
                                            @foreach($projects as $project)
                                                @foreach($project->steps as $step)
                                                    <option value="{{ $step->id }}" data-project="{{ $project->id }}" class="hidden">{{ $step->name }}</option>
                                                @endforeach
                                            @endforeach
                                            <option value="new" id="option_new_step" class="hidden text-[#1862ff] font-bold">+ Créer une étape...</option>
                                        </select>
                                    </div>

                                    <div id="new_step_field" class="hidden">
                                        <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Nouvelle étape</label>
                                        <input type="text" name="new_step_name" id="new_step_name" class="w-full text-sm border-blue-200 rounded-xl focus:ring-blue-500 px-4 py-2.5" placeholder="Nom de l'étape...">
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 pt-3 space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">État du Livrable</label>
                                        <select name="document_status" class="w-full text-sm border-gray-200 rounded-xl shadow-sm focus:border-[#1862ff] focus:ring-[#1862ff] px-4 py-2.5 bg-white">
                                            <option value="todo">🔴 Phase 1 : Cadrage</option>
                                            <option value="in_progress">🟡 Phase 2 : En cours</option>
                                            <option value="done">🟢 Phase 3 : Validé</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Urgence</label>
                                            <select name="priority" class="w-full text-sm border-gray-200 rounded-xl focus:border-[#1862ff] px-3 py-2.5 bg-white">
                                                <option value="high">Haute</option>
                                                <option value="medium" selected>Moyenne</option>
                                                <option value="low">Basse</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Echéance</label>
                                            <input type="date" name="date_prevue" class="w-full text-sm border-gray-200 rounded-xl px-3 py-2">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white {{ $themeBtn }} transition-all shadow-sm">
                                    Enregistrer la donnée
                                </button>
                            </form>
                        @else
                            <!-- Formulaire Master -->
                            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="type" value="master">

                                <div>
                                    <label class="block text-xs font-bold text-purple-600 uppercase tracking-wider mb-1.5">Leçon ou Matière</label>
                                    <input type="text" name="title" required class="w-full text-sm border-gray-200 rounded-xl focus:ring-purple-500 px-4 py-2.5" placeholder="Ex: Calcul Stochastique">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-purple-600 uppercase tracking-wider mb-1.5">Supports / URL</label>
                                    <input type="url" name="document_link" class="w-full text-sm border-gray-200 rounded-xl focus:ring-purple-500 px-4 py-2.5" placeholder="Lien vers tes cours PDF, TD...">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-purple-600 uppercase tracking-wider mb-1.5">Examen</label>
                                        <input type="date" name="date_prevue" class="w-full text-sm border-gray-200 rounded-xl px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-purple-600 uppercase tracking-wider mb-1.5">Catégorie</label>
                                        <select name="category_id" required class="w-full text-sm border-gray-200 rounded-xl px-3 py-2.5 bg-white">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white {{ $themeBtn }} transition-all">
                                    Planifier le cours
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Section de Droite : Leaderboard d'activités (Style Similarweb) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-[#fafbfe]">
                            <h2 class="text-xs font-black uppercase tracking-wider text-gray-400">📊 Top Activités du jour</h2>
                            <span class="text-xs font-bold text-[#1862ff] bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-lg">
                                {{ $todayTasks->count() }} au programme
                            </span>
                        </div>

                        <!-- En-tête du tableau Similarweb -->
                        <div class="grid grid-cols-12 px-6 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                            <div class="col-span-1">Rang</div>
                            <div class="col-span-6 md:col-span-7">Détails de l'activité</div>
                            <div class="col-span-3 md:col-span-2">Statut / Canal</div>
                            <div class="col-span-2 text-right">Actions</div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @php $index = 1; @endphp
                            @forelse($todayTasks as $task)
                                <div class="grid grid-cols-12 px-6 py-4 items-center hover:bg-[#fafbfe] transition-all">
                                    
                                    <!-- Index / Rang (Similarweb-style) -->
                                    <div class="col-span-1">
                                        <span class="text-xs font-extrabold text-gray-300">#{{ $index++ }}</span>
                                    </div>

                                    <!-- Titre & métadonnées -->
                                    <div class="col-span-6 md:col-span-7 space-y-1.5 pr-2">
                                        <h4 class="text-sm font-extrabold text-[#0f172a] leading-snug">{{ $task->title }}</h4>
                                        
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <!-- Badge Priorité -->
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-extrabold border uppercase
                                                {{ $task->priority === 'high' ? 'bg-red-50 text-red-600 border-red-100' : ($task->priority === 'medium' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-green-50 text-green-600 border-green-100') }}">
                                                {{ $task->priority === 'high' ? 'Urgent' : ($task->priority === 'medium' ? 'Moyen' : 'Normal') }}
                                            </span>

                                            @if(!$isMaster && $task->project)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100 truncate max-w-[150px]">
                                                    📁 {{ $task->project->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Support de cours ou document lié -->
                                        @if($task->document_link)
                                            <a href="{{ $task->document_link }}" target="_blank" class="text-[11px] text-[#1862ff] hover:underline inline-flex items-center gap-1 font-bold">
                                                🔗 Ouvrir le document ressource
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Statut & Catégorie -->
                                    <div class="col-span-3 md:col-span-2 space-y-1">
                                        @if(!$isMaster)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                                                {{ $task->document_status === 'done' ? 'bg-green-50 text-green-700 border border-green-100' : ($task->document_status === 'in_progress' ? 'bg-yellow-50 text-yellow-700 border border-yellow-100' : 'bg-red-50 text-red-700 border border-red-100') }}">
                                                {{ $task->document_status === 'done' ? 'Validé' : ($task->document_status === 'in_progress' ? 'En Revue' : 'À traiter') }}
                                            </span>
                                        @else
                                            <!-- Master : Progression en cases à cocher miniatures -->
                                            <form action="{{ route('tasks.updatePrep', $task->id) }}" method="POST" class="flex items-center gap-1">
                                                @csrf
                                                @php $prep = $task->examPrep; @endphp
                                                <input type="checkbox" name="course_reviewed" onchange="this.form.submit()" {{ $prep && $prep->course_reviewed ? 'checked' : '' }} class="w-3.5 h-3.5 rounded text-purple-600 border-gray-200" title="Cours relu">
                                                <input type="checkbox" name="summary_done" onchange="this.form.submit()" {{ $prep && $prep->summary_done ? 'checked' : '' }} class="w-3.5 h-3.5 rounded text-purple-600 border-gray-200" title="Fiche rédigée">
                                                <input type="checkbox" name="exercises_done" onchange="this.form.submit()" {{ $prep && $prep->exercises_done ? 'checked' : '' }} class="w-3.5 h-3.5 rounded text-purple-600 border-gray-200" title="Exercices faits">
                                            </form>
                                        @endif
                                        
                                        <div class="text-[10px] text-gray-400 font-bold uppercase">
                                            {{ $task->category ? $task->category->name : 'Général' }}
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-span-2 text-right flex justify-end items-center gap-3">
                                        @if($isMaster)
                                            <a href="{{ route('flashcards.show', $task->id) }}" class="text-xs text-purple-600 hover:text-purple-800 font-bold" title="Flashcards IA">🧠</a>
                                        @endif
                                        
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Supprimer cette activité ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-bold">🗑️</button>
                                        </form>
                                    </div>

                                </div>
                            @empty
                                <div class="p-16 text-center">
                                    <div class="text-4xl mb-3">📈</div>
                                    <h3 class="text-sm font-extrabold text-gray-700">Aucun trafic d'activités enregistré</h3>
                                    <p class="text-xs text-gray-400 mt-1">Saisis tes tâches quotidiennes sur le panneau latéral pour commencer le suivi.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </main>
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

                options.forEach(opt => {
                    const parentProjId = opt.getAttribute('data-project');
                    if (parentProjId) {
                        if (selectedProjectVal && parentProjId === selectedProjectVal) {
                            opt.classList.remove('hidden');
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