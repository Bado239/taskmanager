@extends('layouts.app')


@section('content')


<div class="max-w-5xl mx-auto p-6">


<h1 class="text-3xl font-bold">
📖 {{ $book->title }}
</h1>


<p class="text-gray-500 mb-6">
{{ $book->author_or_source }}
</p>



<div class="bg-white shadow rounded-xl p-8 leading-loose text-lg">


@php

$words = preg_split('/(\s+)/',$text);

@endphp



@foreach($words as $word)


<span 
class="word cursor-pointer hover:bg-yellow-200"
onclick="definition('{{ trim($word) }}')">

{{ $word }}

</span>


@endforeach


</div>


</div>



<div id="popup"
class="fixed right-5 top-20 bg-white shadow-xl rounded-xl p-5 hidden">


<h3 class="font-bold">
📖 Définition
</h3>


<p id="definition"></p>


</div>




<script>


function definition(word)
{


word = word.replace(/[.,;:!?()]/g,'');



fetch('/dictionary/'+word)

.then(r=>r.json())

.then(data=>{


document
.getElementById('definition')
.innerHTML =
"<b>"+data.word+"</b><br><br>"+
data.definition;



document
.getElementById('popup')
.classList.remove('hidden');


});


}


</script>


@endsection