@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">


    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 shadow-sm p-6">

        <h1 class="text-2xl font-bold text-gray-900">
            🎓 {{ $task->title }}
        </h1>

        <p class="text-gray-500 mt-2">
            Espace d'apprentissage du chapitre
        </p>

    </div>




    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">


        <h2 class="text-lg font-bold text-gray-900 mb-4">
            📚 Informations du chapitre
        </h2>


        <p>
            Matière :
            <strong>
                {{ $task->project->title ?? 'Finance' }}
            </strong>
        </p>


        <p class="mt-3">
            Chapitre :
            {{ $task->title }}
        </p>


    </div>




    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <h2 class="text-lg font-bold mb-4">
            🌍 Ressources recommandées
        </h2>


        <p class="text-gray-500">
            Les meilleurs cours et documents seront ajoutés ici.
        </p>


    </div>




    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <h2 class="text-lg font-bold mb-4">
            📂 Mes documents
        </h2>


        <p class="text-gray-500">
            Vos fichiers personnels seront ajoutés ici.
        </p>


    </div>


</div>


@endsection