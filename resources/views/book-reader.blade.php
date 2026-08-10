<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Lecture : {{ $book->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex flex-col overflow-hidden">

    <!-- Header -->
    <div class="bg-white shadow-sm p-4 flex justify-between items-center border-b">
        <div>
            <a href="{{ route('dashboard', ['view' => 'personal']) }}" class="text-blue-600 text-sm hover:underline">← Retour à l'espace personnel</a>
            <h1 class="text-xl font-bold text-gray-800 mt-1">📖 {{ $book->title }}</h1>
            @if($book->author_or_source)<p class="text-sm text-gray-500">{{ $book->author_or_source }}</p>@endif
        </div>
        
        <!-- Indicateur de statut (À lire / En cours / Terminé) -->
        <span class="px-3 py-1 rounded-full text-xs font-bold 
            @if($book->status === 'done') bg-green-100 text-green-700 
            @elseif($book->status === 'reading') bg-blue-100 text-blue-700 
            @else bg-gray-100 text-gray-700 @endif">
            @if($book->status === 'done') ✅ Terminé
            @elseif($book->status === 'reading') 📖 En cours
            @else 📚 À lire @endif
        </span>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 text-center py-2 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Ecran Divisé -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- GAUCHE : Lecteur PDF (depuis Supabase) -->
        <div class="w-1/2 flex flex-col border-r border-gray-300 bg-gray-800">
            @if($book->pdf_path)
                <!-- Le lecteur PDF intégré au navigateur chargeant le fichier depuis Supabase -->
                <iframe src="{{ $book->pdf_path }}" class="flex-1 w-full h-full" frameborder="0"></iframe>
            @else
                <div class="flex-1 flex items-center justify-center text-white italic">Aucun fichier PDF trouvé pour ce livre.</div>
            @endif
        </div>

        <!-- DROITE : Zone de Notes & Statut -->
        <div class="w-1/2 flex flex-col bg-yellow-50">
            <div class="p-3 bg-yellow-100 border-b text-sm font-semibold text-yellow-700 flex justify-between items-center">
                ✍️ Mes Notes & Réflexions
            </div>
            
            <form action="{{ route('personal-resources.update', $book->id) }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
                @csrf
                @method('PUT')
                
                <!-- Champ caché pour le statut (mis à jour via les boutons) -->
                <input type="hidden" name="status" value="{{ $book->status ?? 'reading' }}">

                <textarea name="notes" class="flex-1 w-full p-6 focus:outline-none resize-none text-gray-800 leading-relaxed bg-yellow-50/50" placeholder="Écrivez vos notes, résumés ou citations ici...">{{ $book->notes }}</textarea>
                
                <div class="p-3 bg-white border-t flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                        💾 Sauvegarder les notes
                    </button>
                    
                    <!-- Bouton pour marquer comme lu (passe le statut à 'done') -->
                    @if($book->status !== 'done')
                        <button type="submit" onclick="document.querySelector('input[name=status]').value = 'done'" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                            ✅ Marquer comme lu
                        </button>
                    @else
                        <button type="submit" onclick="document.querySelector('input[name=status]').value = 'reading'" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                            ↩️ Relire ce livre
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

</body>
</html>