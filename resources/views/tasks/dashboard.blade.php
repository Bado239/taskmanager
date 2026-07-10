<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- INDICATEURS DE PERFORMANCE -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- 1. Passées / En retard -->
            <a href="{{ route('tasks.dashboard', ['filter' => 'late']) }}" class="block bg-white p-5 rounded-lg shadow-sm border-l-4 border-red-500 hover:translate-y-[-2px] transition duration-200">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Passées / En retard</span>
                <h2 class="text-3xl font-extrabold text-red-600 mt-1">{{ $countLate }}</h2>
            </a>

            <!-- 2. Activités à venir -->
            <a href="{{ route('tasks.dashboard', ['filter' => 'future']) }}" class="block bg-white p-5 rounded-lg shadow-sm border-l-4 border-blue-500 hover:translate-y-[-2px] transition duration-200">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Activités à venir</span>
                <h2 class="text-3xl font-extrabold text-blue-600 mt-1">{{ $countFuture }}</h2>
            </a>

            <!-- 3. Activités sans date -->
            <a href="{{ route('tasks.dashboard', ['filter' => 'nodate']) }}" class="block bg-white p-5 rounded-lg shadow-sm border-l-4 border-gray-500 hover:translate-y-[-2px] transition duration-200">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sans date de planification</span>
                <h2 class="text-3xl font-extrabold text-gray-600 mt-1">{{ $countNoDate }}</h2>
            </a>
        </div>

        <!-- LISTE FILTRÉE DYNAMIQUE -->
        @if($filter)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        @if($filter === 'late') 🔴 Liste des activités passées / en retard
                        @elseif($filter === 'future') 🔵 Liste des activités à venir
                        @elseif($filter === 'nodate') ⚫ Liste des activités de fond (sans date)
                        @endif
                    </h3>
                    <a href="{{ route('tasks.dashboard') }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="py-3 px-4">Titre</th>
                                <th class="py-3 px-4">Catégorie</th>
                                <th class="py-3 px-4">Projet</th>
                                @if($filter !== 'nodate')
                                    <th class="py-3 px-4">Date prévue</th>
                                @endif
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($tasks as $task)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 font-medium text-gray-900">{{ $task->title }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded border border-gray-200">
                                            {{ $task->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-500">{{ $task->project->title ?? '-' }}</td>
                                    @if($filter !== 'nodate')
                                        <td class="py-3.5 px-4 text-gray-600">
                                            {{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '-' }}
                                        </td>
                                    @endif
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
                                    <td colspan="5" class="text-center text-gray-400 py-8">Aucune activité trouvée dans cette catégorie.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 text-blue-700 text-center py-6 px-4 rounded-lg shadow-sm">
                💡 Cliquez sur un des indicateurs ci-dessus pour afficher la liste des activités correspondantes.
            </div>
        @endif
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
</x-app-layout>