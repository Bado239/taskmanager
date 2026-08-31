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





<!-- RESSOURCES -->

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





<table class="w-full border-collapse">

<thead>

<tr class="bg-gray-100">

<th class="p-3 text-left">
Cours
</th>

<th class="p-3 text-left">
Source
</th>

<th class="p-3 text-left">
Format
</th>

<th class="p-3 text-left">
Action
</th>

</tr>

</thead>


<tbody>


@forelse($task->courseResources as $resource)


<tr class="border-b hover:bg-gray-50">


<td class="p-3 font-bold text-gray-900">

{{ $resource->title }}

</td>



<td class="p-3 text-gray-600">

🏫 {{ $resource->source }}

</td>



<td class="p-3">

@if($resource->file_type === 'PDF')

📄 PDF

@else

🌐 WEB

@endif

</td>



<td class="p-3">


<a href="{{ $resource->url }}"
target="_blank"
class="text-blue-600 font-bold hover:underline">

📖 Ouvrir

</a>


</td>


</tr>



@empty


<tr>

<td colspan="4"
class="p-5 text-center text-gray-400">

Aucun cours trouvé.

<br>

Cliquez sur 🔎 Nouvelle recherche.

</td>

</tr>


@endforelse


</tbody>


</table>

</div>







<!-- DOCUMENTS PERSONNELS -->

<div class="bg-white rounded-2xl border shadow-sm p-6">


<h2 class="text-xl font-bold mb-4">

📂 Mes documents personnels

</h2>


<p class="text-gray-500 mb-5">

Ajoutez vos propres supports de cours (PDF, Word ou lien Internet).

</p>



<form method="POST"
action="{{ route('learning-documents.store') }}"
enctype="multipart/form-data"
class="space-y-4">

@csrf

<input type="hidden"
name="task_id"
value="{{ $task->id }}">



<input type="text"
name="title"
placeholder="Titre du document"
class="w-full border rounded-xl p-3"
required>



<select name="type"
class="w-full border rounded-xl p-3">


<option value="pdf">
📄 PDF / Word
</option>


<option value="link">
🌐 Lien Internet
</option>


</select>




<div>

<label class="text-sm text-gray-600">

Fichier PDF ou Word

</label>


<input type="file"
name="file"
accept=".pdf,.doc,.docx"
class="w-full border rounded-xl p-3">


</div>




<div>

<label class="text-sm text-gray-600">

Lien Internet

</label>


<input type="url"
name="url"
placeholder="https://..."
class="w-full border rounded-xl p-3">


</div>




<button
class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold">

➕ Ajouter le document

</button>



</form>
@if($task->learningDocuments->count() > 0)

<div class="mt-6">

<h3 class="font-bold mb-3">
📚 Documents ajoutés
</h3>


<table class="w-full border-collapse">

<thead>

<tr class="bg-gray-100">

<th class="p-3 text-left">
Document
</th>

<th class="p-3 text-left">
Type
</th>

<th class="p-3 text-left">
Action
</th>

</tr>

</thead>


<tbody>


@foreach($task->learningDocuments as $document)

<tr class="border-b">


<td class="p-3 font-bold">

{{ $document->title }}

</td>



<td class="p-3">

@if($document->type == 'link')

🌐 Lien Internet

@else

📄 PDF / Word

@endif

</td>



<td class="p-3">


@if($document->url)

<a href="{{ $document->url }}"
target="_blank"
class="text-blue-600 font-bold">

🔗 Ouvrir

</a>


@elseif($document->file_path)

<a href="{{ asset('storage/'.$document->file_path) }}"
target="_blank"
class="text-blue-600 font-bold">

📄 Ouvrir fichier

</a>

@endif

</td>


</tr>


@endforeach


</tbody>

</table>


</div>

@endif


</div>



</div>


@endsection