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





<!-- DOCUMENTS PERSONNELS -->

<div class="bg-white rounded-2xl border shadow-sm p-6">


<h2 class="text-xl font-bold mb-4">
📂 Mes documents personnels
</h2>


<p class="text-gray-500 mb-5">
Ajoutez vos propres supports de cours (PDF, Word ou lien Internet).
</p>



<button 
type="button"
onclick="document.getElementById('documentForm').classList.toggle('hidden')"
class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold">

➕ Ajouter un document

</button>




<!-- FORMULAIRE AJOUT -->

<div id="documentForm" class="hidden mt-5">


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




<div class="border rounded-xl p-4">

<label class="block text-sm text-gray-600 mb-2">

📄 Ajouter un fichier PDF ou Word

</label>


<input type="file"
name="file"
accept=".pdf,.doc,.docx"
class="w-full">

</div>




<div class="border rounded-xl p-4">

<label class="block text-sm text-gray-600 mb-2">

🌐 Ajouter un lien Internet

</label>


<input type="url"
name="url"
placeholder="https://..."
class="w-full border rounded-xl p-3">

</div>




<button
type="submit"
class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-bold">

💾 Enregistrer le document

</button>


</form>


</div>





<!-- LISTE DOCUMENTS -->


@if($task->learningDocuments->count() > 0)


<div class="mt-8">


<h3 class="font-bold mb-3 text-lg">

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



<a href="https://zhlojjivmwuuqhzeqpgg.supabase.co/storage/v1/object/public/ebooks/{{ $document->file_path }}"
target="_blank"
class="text-blue-600 font-bold">

📄 Ouvrir fichier

</a>



@endif





<form method="POST"
action="{{ route('learning-documents.destroy',$document->id) }}"
class="inline">


@csrf

@method('DELETE')



<button
onclick="return confirm('Supprimer ce document ?')"
class="text-red-600 font-bold ml-3">


🗑 Supprimer


</button>


</form>



</td>


</tr>



@endforeach


</tbody>


</table>


</div>


@endif



</div>





<!-- FUTURE IA -->

<div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl border p-6">


<h2 class="text-xl font-bold">

🤖 Assistant IA

</h2>


<p class="text-gray-600 mt-2">

Générez automatiquement un cours complet à partir de vos documents.

</p>


<form method="POST"
action="{{ route('learning.generate',$task->id) }}">

@csrf


<button
class="mt-4 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-bold">

🤖 Générer le cours avec IA

</button>


</form>

@if($task->generatedCourse)

<div class="bg-white border rounded-2xl p-6">


<h2 class="text-xl font-bold mb-4">

📖 Cours généré par IA

</h2>


<div class="prose">

{!! nl2br($task->generatedCourse->content) !!}

</div>


</div>

@endif

</div>



</div>


@endsection