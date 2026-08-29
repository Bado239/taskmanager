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

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">


    <h2 class="text-lg font-bold mb-5">
        🌍 Ressources recommandées
    </h2>


    <div class="space-y-4">


    @forelse($task->courseResources as $resource)


    <div class="flex items-center justify-between p-4 rounded-xl bg-blue-50 border border-blue-100">


    <div>

    <h3 class="font-bold text-gray-900">
        {{ $resource->title }}
    </h3>


    <p class="text-sm text-gray-500">
        {{ $resource->source }}
    </p>


    <span class="text-xs text-blue-600 font-semibold">
        {{ strtoupper($resource->type) }}
    </span>


    </div>

<a href="{{ $resource->url }}"
target="_blank"
class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold">

Voir le cours

</a>


</div>


@empty


<p class="text-gray-500">
Aucune ressource ajoutée pour ce chapitre.
</p>


@endforelse


</div>


</div>

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