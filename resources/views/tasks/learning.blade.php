@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">


<!-- HEADER -->

<div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 shadow p-6">

    <h1 class="text-3xl font-bold text-gray-900">
        🎓 {{ $task->title }}
    </h1>

    <p class="text-gray-500 mt-2">
        Espace d'apprentissage intelligent du chapitre
    </p>

</div>





<!-- INFORMATIONS -->

<div class="bg-white rounded-2xl border shadow-sm p-6">


<h2 class="text-xl font-bold mb-4">
📚 Informations du chapitre
</h2>


<div class="space-y-2 text-gray-700">


<p>
Matière :
<strong class="text-blue-600">
{{ $task->project->title ?? 'Finance' }}
</strong>
</p>


<p>
Chapitre :
<strong>
{{ $task->title }}
</strong>
</p>


</div>

</div>







<!-- RESSOURCES WEB -->

<div class="bg-white rounded-2xl border shadow-sm p-6">


<div class="flex justify-between items-center mb-6">


<div>

<h2 class="text-xl font-bold">
🌍 Ressources recommandées
</h2>


<p class="text-sm text-gray-500">
Cours trouvés automatiquement sur le Web
</p>

</div>



<a href="{{ route('tasks.searchCourses',$task->id) }}"
class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl font-bold text-sm">

🔎 Nouvelle recherche

</a>


</div>





<div class="grid md:grid-cols-2 gap-5">


@forelse($task->courseResources as $resource)


<div class="border rounded-2xl p-5 bg-white shadow-sm hover:shadow-lg transition">



<!-- TITRE -->


<div class="flex gap-3">


<div class="bg-blue-600 text-white w-12 h-12 rounded-xl flex items-center justify-center text-xl">

@if($resource->file_type === 'PDF')

📄

@else

🌐

@endif

</div>




<div>

<h3 class="font-bold text-gray-900">

{{ $resource->title }}

</h3>


<p class="text-sm text-gray-600 mt-1">

🏫 {{ $resource->source }}

</p>


</div>


</div>







<!-- BADGES -->


<div class="flex flex-wrap gap-2 mt-5">


<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">

{{ $resource->file_type ?? 'WEB' }}

</span>



@if($resource->is_university)

<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">

🎓 Université

</span>

@endif




<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">

⭐ Score {{ $resource->score }}/100

</span>


</div>







<!-- ACTIONS -->


<div class="mt-5 flex justify-between items-center">


<a href="{{ $resource->url }}"
target="_blank"
class="text-blue-600 font-bold hover:underline">

📖 Ouvrir

</a>




</div>





<!-- EVALUATION -->


<div class="mt-5 border-t pt-4">


<p class="text-xs text-gray-500 mb-2">

Votre évaluation :

</p>


<div class="flex gap-2">


<form method="POST"
action="{{ route('courses.relevance',$resource->id) }}">

@csrf


<button name="value"
value="3"
class="px-3 py-2 bg-green-100 text-green-700 rounded-lg text-xs font-bold">

⭐⭐⭐ Très pertinent

</button>



<button name="value"
value="2"
class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">

⭐⭐ Pertinent

</button>



<button name="value"
value="1"
class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold">

⭐ Faible

</button>


</form>


</div>


</div>




</div>




@empty


<div class="col-span-2 text-center py-10 text-gray-400">

Aucun cours trouvé.

Cliquez sur 🔎 Nouvelle recherche.

</div>


@endforelse



</div>


</div>







<!-- DOCUMENTS PERSONNELS -->


<div class="bg-white rounded-2xl border shadow-sm p-6">


<h2 class="text-xl font-bold mb-4">

📂 Mes documents

</h2>


<p class="text-gray-500">

Ajoutez vos propres supports de cours.

</p>


</div>




</div>


@endsection