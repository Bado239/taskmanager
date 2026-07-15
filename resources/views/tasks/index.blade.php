<x-app-layout>
    @php
        $isMaster = $currentMode === 'master';
        $themeBg = $isMaster ? 'bg-purple-50' : 'bg-slate-50';
        $themeText = $isMaster ? 'text-purple-900' : 'text-slate-900';
        $themeBtn = $isMaster ? 'bg-purple-600 hover:bg-purple-700' : 'bg-blue-600 hover:bg-blue-700';
        $themeCardHeader = $isMaster ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-800';
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
                <h3 class="text-md font-bold text-gray-800 mb-4">➕ Enregistrer une nouvelle activité</h3>
                <form action="{{ route('tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <input type="hidden" name="type" value="{{ $currentMode }}">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom de l'activité ou matière</label>
                        <input type="text" name="title" required class="w-full text-sm border-gray-300 rounded shadow-sm focus:border-blue-500" placeholder="{{ $isMaster ? 'Ex: Calcul Stochastique' : 'Ex: Migration Render du TaskManager' }}">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lien du Document (URL)</label>
                        <input type="url" name="document_link" class="w-full text-sm border-gray-300 rounded shadow-sm focus:border-blue-500" placeholder="Lien Google Drive, Notion, etc.">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Statut du Document / Livrable</label>
                        <select name="document_status" class="w-full text-sm border-gray-300 rounded shadow-sm focus:border-blue-500">
                            <option value="none">Pas de document requis</option>
                            <option value="todo">🔴 À rédiger / À lire</option>
                            <option value="in_progress">🟡 En cours d'analyse / Rédaction</option>
                            <option value="done">🟢 Validé et classé</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Priorité</label>
                        <select name="priority" class="w-full text-sm border-gray-300 rounded shadow-sm focus:border-blue-500">
                            <option value="high">Haute</option>
                            <option value="medium" selected>Moyenne</option>
                            <option value="low">Basse</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date limite / Examen</label>
                        <input type="date" name="date_prevue" class="w-full text-sm border-gray-300 rounded shadow-sm focus:border-blue-500">
                    </div>

                    <button type="submit" class="w-full py-2 px-4 rounded font-bold text-xs text-white {{ $themeBtn }} transition">
                        Enregistrer
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($tasks as $task)
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
                            <h4 class="text-md font-bold text-gray-800 mb-3">{{ $task->title }}</h4>

                            <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">📁 Document & Livrables</span>
                                @if($task->document_link)
                                    <a href="{{ $task->document_link }}" target="_blank" class="text-xs text-blue-600 hover:underline font-semibold block mb-2 overflow-hidden text-ellipsis whitespace-nowrap">
                                        🔗 Ouvrir le document externe
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic block mb-2">Aucun document lié</span>
                                @endif

                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold uppercase
                                    {{ $task->document_status === 'done' ? 'bg-green-100 text-green-700' : ($task->document_status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : ($task->document_status === 'todo' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500')) }}">
                                    {{ $task->document_status === 'done' ? '🟢 Validé / Prêt' : ($task->document_status === 'in_progress' ? '🟡 En rédaction / Lecture' : ($task->document_status === 'todo' ? '🔴 À traiter' : 'Aucun requis')) }}
                                </span>
                            </div>

                            @if($isMaster)
                                <form action="{{ route('tasks.updatePrep', $task->id) }}" method="POST" class="space-y-2 mt-4 pt-3 border-t border-gray-100">
                                    @csrf
                                    <span class="text-[10px] font-bold text-purple-400 uppercase block mb-2">🧠 Progression révision</span>
                                    
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
                            @endif
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                            @if($isMaster)
                                <a href="{{ route('flashcards.show', $task->id) }}" class="text-xs text-purple-700 hover:text-purple-900 font-extrabold flex items-center gap-1">
                                    🧠 Réviser (Flashcards IA)
                                </a>
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
</x-app-layout>