<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'activité') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- TITRE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre de l'activité</label>
                    <input type="text" name="title" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" value="{{ old('title', $task->title) }}" required>
                </div>

                <!-- CATÉGORIE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- PROJET ASSOCIÉ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Projet associé (Optionnel)</label>
                    <select name="project_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Aucun projet</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- PRIORITÉ -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                    <select name="priority" id="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>🔴 Haute</option>
                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>🟡 Moyenne</option>
                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>🔵 Basse</option>
                    </select>
                </div>

                <!-- DATE PRÉVUE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date prévue (Optionnelle)</label>
                    <input type="date" name="date_prevue" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" value="{{ old('date_prevue', $task->date_prevue ? \Illuminate\Support\Carbon::parse($task->date_prevue)->format('Y-m-d') : '') }}">
                </div>

                <!-- BLOC HEURES -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">De (Heure début)</label>
                        <input type="time" name="heure_debut" id="heure_debut" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" value="{{ old('heure_debut', $task->heure_debut ? \Illuminate\Support\Carbon::parse($task->heure_debut)->format('H:i') : '') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">À (Heure fin)</label>
                        <input type="time" name="heure_fin" id="heure_fin" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" value="{{ old('heure_fin', $task->heure_fin ? \Illuminate\Support\Carbon::parse($task->heure_fin)->format('H:i') : '') }}">
                    </div>
                </div>

                <!-- BOUTONS -->
                <div class="flex gap-4 pt-2">
                    <button type="submit" class="flex-1 bg-green-700 hover:bg-green-800 text-white font-bold py-2.5 px-4 rounded-md shadow transition">
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('tasks.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2.5 px-4 rounded-md shadow transition border border-gray-300">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 🌟 PIED DE PAGE & SIGNATURE BADO 🌟 -->
    <footer class="text-center py-6 border-t border-dashed border-gray-200 mt-auto">
        <div class="flex flex-col items-center justify-center space-y-1">
            <p class="text-sm font-semibold text-gray-500 tracking-wide">
                🚀 <span class="text-gray-800 border-r border-gray-300 pr-2 mr-2">TaskManager</span> 
                Propulsé avec passion par 
                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold ml-1 shadow-sm">
                    ✍️ BADO
                </span>
            </p>
            <span class="text-xs text-gray-400 opacity-75">
                &copy; {{ date('Y') }} &bull; Tous droits réservés &bull; Amélioration continue
            </span>
        </div>
    </footer>

    <!-- SCRIPT AUTOMATIQUE DES +2 HEURES -->
    <script>
        document.getElementById('heure_debut').addEventListener('change', function() {
            const heureDebutVal = this.value;
            if (heureDebutVal) {
                let [heures, minutes] = heureDebutVal.split(':').map(Number);
                heures += 2;
                if (heures >= 24) {
                    heures -= 24;
                }
                const heuresFormattees = String(heures).padStart(2, '0');
                const minutesFormattees = String(minutes).padStart(2, '0');
                document.getElementById('heure_fin').value = `${heuresFormattees}:${minutesFormattees}`;
            }
        });
    </script>
</x-app-layout>