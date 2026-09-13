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

@php
$cleanWord = trim($word);
@endphp


@if($cleanWord != '')

<span 
class="word"
data-word="{{ $cleanWord }}">

{{ $word }}

</span>

@endif


@endforeach

</div>


</div>



<div id="popup"
class="fixed right-5 top-20 bg-white shadow-xl rounded-xl p-5 hidden w-80 z-50">


<button 
onclick="document.getElementById('popup').classList.add('hidden')"
class="float-right text-red-600">

✖

</button>

<h3 class="font-bold">
📖 Définition
</h3>


<p id="definition"></p>


</div>




<script>

document.addEventListener("DOMContentLoaded", function(){


document.querySelectorAll('.word')
.forEach(function(element){


element.addEventListener('click', function(){


let word = this.dataset.word;


word = word.replace(
/[.,;:!?()"'«»]/g,
''
);



console.log("Mot sélectionné :", word);



fetch('/dictionary/' + encodeURIComponent(word))


.then(response => response.json())


.then(data => {


document.getElementById('definition').innerHTML =

"<b>" + data.word + "</b><br><br>" +

data.definition;



document
.getElementById('popup')
.classList.remove('hidden');


});


});


});


});

</script>

@endsection