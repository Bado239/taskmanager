@extends('layouts.app')

@php
use League\CommonMark\CommonMarkConverter;
@endphp

@section('content')

@if(session('success'))

<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-5">
    ✅ {{ session('success') }}
</div>

@endif


@if(session('error'))

<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-5">
    ❌ {{ session('error') }}
</div>

@endif

<div class="max-w-6xl mx-auto space-y-6">


<!-- HEADER MISSION -->

<div class="bg-gradient-to-r from-slate-900 to-blue-900 rounded-2xl shadow-lg p-6 text-white">

    <div class="flex flex-wrap justify-between items-center gap-3 mb-5">

        <span class="bg-blue-500/20 text-blue-200 px-4 py-2 rounded-full text-sm font-bold">
            {{ strtoupper($task->type) }}
        </span>

        <span class="bg-white/10 px-4 py-2 rounded-full text-sm">
            Mission #{{ $task->id }}
        </span>

    </div>

    <p class="text-blue-200 text-sm mb-2">
        PROJET / MATIÈRE
    </p>

    <h2 class="text-lg font-semibold mb-5">
        {{ $task->project->title ?? $task->project_name ?? 'Projet non défini' }}
    </h2>

    <h1 class="text-3xl font-extrabold">
        {{ $task->title }}
    </h1>

    <div class="flex flex-wrap gap-4 mt-5 text-sm text-blue-100">

        <span>📅 {{ $task->execution_date ? \Carbon\Carbon::parse($task->execution_date)->format('d/m/Y') : 'Non programmée' }}</span>

        <span>🕒 {{ $task->heure_debut ?? '--:--' }} - {{ $task->heure_fin ?? '--:--' }}</span>

        <span>⚡ Priorité : {{ ucfirst($task->priority ?? 'Normale') }}</span>

    </div>

</div>

<!-- INSTRUCTIONS -->

<div class="bg-white rounded-2xl border shadow-sm p-6">

    <h2 class="text-xl font-bold text-gray-800 mb-4">
        📋 Détails et instructions
    </h2>

    @if($task->description)

        <div class="text-gray-700 leading-8 whitespace-pre-line">
            {{ $task->description }}
        </div>

    @else

        <p class="text-gray-400 italic">
            Aucune instruction particulière n'a été renseignée pour cette mission.
        </p>

    @endif

    <a href="{{ route('tasks.edit', $task->id) }}"
       class="inline-block mt-5 text-blue-600 font-semibold hover:underline">

        ✏️ Modifier les informations de la mission

    </a>

</div>

<!-- SUIVI DE LA MISSION -->

<div class="bg-white rounded-2xl border shadow-sm p-6">

    <h2 class="text-xl font-bold mb-5">
        🎯 Suivi de ma mission
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

        @php
            $statuses = [
                'todo' => [
                    'label' => 'À faire',
                    'color' => 'gray',
                    'icon' => '📌'
                ],
                'in_progress' => [
                    'label' => 'En cours',
                    'color' => 'amber',
                    'icon' => '🚀'
                ],
                'done' => [
                    'label' => 'Terminée',
                    'color' => 'green',
                    'icon' => '🏆'
                ],
            ];
        @endphp

        @foreach($statuses as $value => $status)

            <form method="POST"
                  action="{{ route('tasks.learning.status', $task->id) }}">

                @csrf
                @method('PATCH')

                <input type="hidden"
                       name="document_status"
                       value="{{ $value }}">

                <button type="submit"
                    class="w-full rounded-xl border-2 p-4 font-bold transition
                    {{ $task->document_status === $value
                        ? 'border-blue-600 bg-blue-50 text-blue-700 shadow-sm'
                        : 'border-gray-200 bg-white text-gray-600 hover:border-blue-300' }}">

                    <span class="block text-2xl mb-2">
                        {{ $status['icon'] }}
                    </span>

                    {{ $status['label'] }}

                    @if($task->document_status === $value)
                        <span class="block text-xs mt-2">
                            ✓ Statut actuel
                        </span>
                    @endif

                </button>

            </form>

        @endforeach

    </div>

</div>


<!-- DOCUMENT PRINCIPAL -->

@if($task->document_link)

<div class="bg-white rounded-2xl border shadow-sm p-6">

    <h2 class="text-xl font-bold mb-4">
        📎 Document principal de la mission
    </h2>

    <a href="{{ $task->document_link }}"
       target="_blank"
       rel="noopener noreferrer"
       class="inline-flex items-center gap-3 bg-blue-50 text-blue-700 p-4 rounded-xl font-semibold hover:bg-blue-100">

        📄 Ouvrir le document associé

        <span>↗</span>

    </a>

</div>

@endif

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



<a href="{{ route('learning-documents.view',$document->id) }}"
class="text-blue-600 font-bold">

📖 Lire le document

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



</div>

<script>

document.addEventListener(
"DOMContentLoaded",
function(){


let menu=document.getElementById(
"courseMenu"
);


let titles=document.querySelectorAll(
"#courseContent h2"
);



titles.forEach(function(title,index){


let id="chapitre-"+index;


title.id=id;


let li=document.createElement("li");


li.innerHTML=
`
<a href="#${id}"
class="hover:underline">

📖 ${title.innerText}

</a>
`;


menu.appendChild(li);


});


});

</script>


@endsection