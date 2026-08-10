<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Lecture : {{ $book->title }}</title>
    <!-- Tailwind CSS (ajoute ton CDN si pas déjà inclus) -->
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
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 text-center py-2 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulaire Sauvegarde -->
    <form action="{{ route('personal-resources.update', $book->id) }}" method="POST" class="flex-1 flex overflow-hidden">
        @csrf
        @method('PUT')

        <!-- GAUCHE : Zone de Lecture -->
        <div class="w-1/2 flex flex-col border-r border-gray-300 bg-white">
            <div class="p-3 bg-gray-50 border-b text-sm font-semibold text-gray-600 flex justify-between items-center">
                📚 Texte / Contenu
                <span class="text-xs text-gray-400 italic">Colle le texte ici ou lis-le directement</span>
            </div>
            <textarea name="content" class="flex-1 w-full p-6 focus:outline-none resize-none text-gray-800 leading-relaxed" placeholder="Collez le texte du livre ou du chapitre ici...">{{ $book->content }}</textarea>
        </div>

        <!-- DROITE : Zone de Notes -->
        <div class="w-1/2 flex flex-col bg-yellow-50">
            <div class="p-3 bg-yellow-100 border-b text-sm font-semibold text-yellow-700 flex justify-between items-center">
                ✍️ Mes Notes & Réflexions
                <span class="text-xs text-yellow-500 italic">Prenez vos notes ici</span>
            </div>
            <textarea name="notes" class="flex-1 w-full p-6 focus:outline-none resize-none text-gray-800 leading-relaxed bg-yellow-50/50" placeholder="Écrivez vos notes, résumés ou citations ici...">{{ $book->notes }}</textarea>
        </div>

    </div>

    <!-- Footer / Bouton Sauvegarde -->
    <div class="bg-white p-3 border-t shadow-lg text-center">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg transition">
            💾 Sauvegarder la lecture et les notes
        </button>
    </div>
    </form>

</body>
</html>