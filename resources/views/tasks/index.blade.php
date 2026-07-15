<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Focus du Jour') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">📅 Activités prévues pour aujourd'hui</h3>
                <a href="{{ route('tasks.create') }}" class="bg-green-700 hover:bg-green-800 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                    ➕ Ajouter une activité
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="py-3 px-4">Titre</th>
                            <th class="py-3 px-4">Catégorie</th>
                            <th class="py-3 px-4">Projet</th>
                            <th class="py-3 px-4">Début</th>
                            <th class="py-3 px-4">Fin</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($todayTasks as $task)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3.5 px-4 font-medium text-gray-900">
                                    <div class="flex flex-col gap-1">
                                        <span>{{ $task->title }}</span>
                                        
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            @if($task->document_link)
                                                <a href="{{ $task->document_link }}" target="_blank" 
                                                   class="inline-flex items-center text-xs bg-blue-50 text-blue-700 px-2.5 py-1 rounded hover:bg-blue-100 transition font-semibold"
                                                   title="Ouvrir le document externe">
                                                    <i class="fa-solid fa-file-pdf mr-1"></i> Cours / Doc (Lien)
                                                </a>
                                            @endif

                                            @if($task->file_path)
                                                <a href="{{ asset('storage/' . $task->file_path) }}" target="_blank" 
                                                   class="inline-flex items-center text-xs bg-green-50 text-green-700 px-2.5 py-1 rounded hover:bg-green-100 transition font-semibold"
                                                   title="Ouvrir le fichier local">
                                                    <i class="fa-solid fa-folder-open mr-1"></i> Voir le document
                                                </a>
                                            @endif
                                            <!-- 🖼️ BLOC EMPLOI DU TEMPS -->
                                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                                    📅 Mon Emploi du Temps du Semestre
                                                </h3>

                                                @if($currentSchedule)
                                                    <!-- Liseuse d'image -->
                                                    <div class="relative border border-gray-200 rounded-lg overflow-hidden bg-gray-50 max-h-96 flex justify-center items-center group">
                                                        <img src="{{ asset('storage/' . $currentSchedule->file_path) }}" alt="Emploi du temps" class="object-contain max-h-96 w-full transition duration-300 group-hover:opacity-95">
                                                        
                                                        <!-- Bouton plein écran -->
                                                        <div class="absolute bottom-4 right-4">
                                                            <a href="{{ asset('storage/' . $currentSchedule->file_path) }}" target="_blank" class="bg-gray-900 bg-opacity-75 hover:bg-opacity-90 text-white text-xs font-semibold py-2 px-3 rounded-md transition shadow flex items-center gap-1">
                                                                <i class="fa-solid fa-expand"></i> Voir en grand / Imprimer
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Message si aucun emploi du temps n'est configuré -->
                                                    <div class="text-center py-6 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 text-sm">
                                                        📸 Aucun emploi du temps n'a encore été téléversé pour ce semestre.
                                                    </div>
                                                @endif

                                                <!-- Petit formulaire rapide pour mettre à jour l'image en un clic -->
                                                <form action="{{ route('schedule.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                                                    @csrf
                                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Mettre à jour la photo :</label>
                                                    <input type="file" name="schedule_file" accept="image/*,application/pdf" required class="text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-1.5 px-3 rounded text-xs transition shadow-sm">
                                                        💾 Sauvegarder
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-full font-semibold">
                                        {{ $task->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-gray-500">{{ $task->project->title ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-gray-600">
                                    {{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-600">
                                    {{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-600 hover:text-blue-800 p-1">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-400 py-8">🎉 Aucune activité restante pour aujourd'hui !</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>