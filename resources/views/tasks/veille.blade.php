<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Veille Tech & Actualités') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-2">⚡ Dernières actualités Tech</h3>
            <p class="text-sm text-gray-500 mb-6">Consultez les sujets chauds du moment et planifiez une tâche de lecture en un clic.</p>

            <div class="space-y-6 divide-y divide-gray-100">
                @forelse($articles as $article)
                    <div class="pt-6 first:pt-0 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex-1 space-y-1">
                            <span class="text-xs text-green-700 font-semibold bg-green-50 px-2 py-0.5 rounded">TechCrunch</span>
                            <h4 class="text-base font-bold text-gray-900">
                                <a href="{{ $article['link'] }}" target="_blank" class="hover:text-green-700 transition">
                                    {{ $article['title'] }} <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-50 ml-1"></i>
                                </a>
                            </h4>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($article['description'], 180) }}</p>
                            <span class="block text-xs text-gray-400">Publié le {{ $article['date'] }}</span>
                        </div>

                        <!-- BOUTON D'ACTION POUR CRÉER LA TÂCHE -->
                        <div class="shrink-0">
                            <a href="{{ route('tasks.create', ['title' => 'Lire : ' . $article['title']]) }}" class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white font-semibold text-xs py-2 px-3 rounded shadow-sm transition">
                                <i class="fa-solid fa-plus"></i> Créer une tâche
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8">
                        Impossible de charger les actualités pour le moment. Réessayez plus tard !
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>