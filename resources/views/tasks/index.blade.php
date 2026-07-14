<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Focus du Jour') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Messages de succès -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- BLOC DU TABLEAU DES TÂCHES -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">📅 Activités prévues pour aujourd'hui</h3>
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
                                <td class="py-3.5 px-4 font-medium text-gray-900">{{ $task->title }}</td>
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
                                <td class="py-3.5 px-4 font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $task->title }}</span>
                                        
                                        @if($task->document_link)
                                            <a href="{{ $task->document_link }}" target="_blank" 
                                            class="inline-flex items-center text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded hover:bg-blue-100 transition font-semibold"
                                            title="Ouvrir le document">
                                                <i class="fa-solid fa-file-pdf mr-1"></i> Cours / Doc
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>