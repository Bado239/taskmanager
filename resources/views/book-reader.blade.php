<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<title>
Lecture : {{ $book->title }}
</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>


<body class="bg-gray-100 h-screen flex flex-col overflow-hidden">


<!-- HEADER -->

<div class="bg-white shadow-sm p-4 flex justify-between items-center border-b">


<div>

<a href="{{ route('dashboard',['view'=>'personal']) }}"
class="text-blue-600 text-sm hover:underline">

← Retour à l'espace personnel

</a>


<h1 class="text-xl font-bold text-gray-800 mt-1">

📖 {{ $book->title }}

</h1>


@if($book->author_or_source)

<p class="text-sm text-gray-500">

{{ $book->author_or_source }}

</p>

@endif


</div>





<div class="flex items-center gap-3">


<button

onclick="toggleNotes()"

id="toggleBtn"

class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">

📝 Masquer notes

</button>



<span class="px-3 py-1 rounded-full text-xs font-bold

@if($book->status === 'done')
bg-green-100 text-green-700

@elseif($book->status === 'reading')
bg-blue-100 text-blue-700

@else
bg-gray-100 text-gray-700

@endif
">


@if($book->status === 'done')

✅ Terminé

@elseif($book->status === 'reading')

📖 En cours

@else

📚 À lire

@endif


</span>


</div>


</div>





@if(session('success'))

<div class="bg-green-100 text-green-700 text-center py-2 text-sm font-semibold">

{{ session('success') }}

</div>

@endif







<!-- ZONE PRINCIPALE -->

<div class="flex-1 flex overflow-hidden">





<!-- PDF -->

<div

id="pdfZone"

class="w-[70%] flex flex-col bg-gray-800 transition-all duration-300">



@if($book->pdf_path)


<div class="flex-1 flex flex-col bg-gray-800">


    <!-- CONTROLE PDF -->
    <div class="bg-gray-900 text-white p-3 flex justify-center items-center gap-4">

        <button
            onclick="previousPage()"
            class="bg-blue-600 px-4 py-2 rounded">
            ⬅ Précédente
        </button>


        <span>
            Page 
            <span id="pageNumber">
                {{ $book->current_page ?? 1 }}
            </span>
            /
            <span id="totalPages">
                ...
            </span>
        </span>


        <button
            onclick="nextPage()"
            class="bg-blue-600 px-4 py-2 rounded">
            Suivante ➡
        </button>

    </div>


    <!-- LECTEUR -->
    <div
        id="pdfViewer"
        class="flex-1 overflow-auto bg-gray-700 flex justify-center">
    </div>


</div>

<!-- PROGRESSION -->

<div class="bg-white p-3 border-t">


<div class="flex justify-between text-xs mb-2">

<span>

📖 Progression de lecture

</span>


<span id="progressText">

{{ $book->progress ?? 0 }} %

</span>


</div>




<div class="w-full bg-gray-200 rounded-full h-3">


<div

id="progressBar"

class="bg-blue-600 h-3 rounded-full"

style="width: {{ $book->progress ?? 0 }}%">

</div>


</div>



<div class="text-xs text-gray-500 mt-2">

Page actuelle :

<span id="currentPage">

{{ $book->current_page ?? 1 }}

</span>

</div>


</div>



@else


<div class="flex-1 flex items-center justify-center text-white">

Aucun PDF disponible

</div>


@endif


</div>









<!-- NOTES -->

<div

id="notesZone"

class="w-[30%] flex flex-col bg-yellow-50 border-l transition-all duration-300">



<div class="p-3 bg-yellow-100 border-b font-semibold text-yellow-700">

✍️ Mes Notes & Réflexions

</div>





<form

action="{{ route('personal-resources.update',$book->id) }}"

method="POST"

class="flex-1 flex flex-col">


@csrf

@method('PUT')



<input

type="hidden"

name="status"

value="{{ $book->status ?? 'reading' }}">





<textarea

name="notes"

class="flex-1 p-6 resize-none bg-yellow-50 outline-none"

placeholder="Écrivez vos notes...">{{ $book->notes }}</textarea>





<div class="p-3 bg-white border-t flex flex-col gap-2">


<button

type="submit"

class="bg-blue-600 text-white py-2 rounded-lg font-bold">

💾 Sauvegarder notes

</button>



@if($book->status !== 'done')


<button

type="submit"

onclick="document.querySelector('input[name=status]').value='done'"

class="bg-green-600 text-white py-2 rounded-lg font-bold">

✅ Marquer comme lu

</button>


@else


<button

type="submit"

onclick="document.querySelector('input[name=status]').value='reading'"

class="bg-orange-500 text-white py-2 rounded-lg font-bold">

↩️ Relire

</button>


@endif


</div>



</form>



</div>



</div>








<script>


function toggleNotes(){


let notes =
document.getElementById('notesZone');


let pdf =
document.getElementById('pdfZone');


let btn =
document.getElementById('toggleBtn');



if(notes.classList.contains('hidden')){


notes.classList.remove('hidden');

pdf.className =
"w-[70%] flex flex-col bg-gray-800 transition-all duration-300";


btn.innerHTML="📝 Masquer notes";


}

else{


notes.classList.add('hidden');


pdf.className =
"w-full flex flex-col bg-gray-800 transition-all duration-300";


btn.innerHTML="📖 Afficher notes";


}


}






// Sauvegarde progression

function saveProgress(page,percent){



fetch(

"{{ route('personal-resources.progress',$book->id) }}",

{

method:"POST",

headers:{

"Content-Type":"application/json",

"X-CSRF-TOKEN":
"{{ csrf_token() }}"

},

body:JSON.stringify({

current_page:page,

progress:percent

})

}


);


}





</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>


const url = "{{ $book->pdf_path }}";


let pdfDoc = null;

let currentPage = {{ $book->current_page ?? 1 }};

let totalPages = 0;



pdfjsLib.GlobalWorkerOptions.workerSrc =
'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';



pdfjsLib.getDocument(url)
.promise
.then(function(pdf){

    pdfDoc = pdf;

    totalPages = pdf.numPages;


    document.getElementById('totalPages').innerHTML =
    totalPages;


    renderPage(currentPage);


});






function renderPage(num){


pdfDoc.getPage(num)
.then(function(page){


let scale = 1.5;


let viewport =
page.getViewport({
    scale:scale
});



let canvas =
document.createElement('canvas');


let context =
canvas.getContext('2d');



canvas.height =
viewport.height;


canvas.width =
viewport.width;



let viewer =
document.getElementById('pdfViewer');


viewer.innerHTML="";


viewer.appendChild(canvas);



page.render({

canvasContext:context,

viewport:viewport

});



document.getElementById('pageNumber')
.innerHTML=num;



saveProgress(num);



});


}







function nextPage(){


if(currentPage < totalPages){

    currentPage++;

    renderPage(currentPage);

}


}






function previousPage(){


if(currentPage > 1){

    currentPage--;

    renderPage(currentPage);

}


}






function saveProgress(page){


let percent =
Math.round(
(page / totalPages) * 100
);



document.getElementById('progressText').innerHTML =
percent+" %";



document.getElementById('progressBar').style.width =
percent+"%";





fetch(
"{{ route('personal-resources.progress',$book->id) }}",
{

method:"POST",

headers:{

"Content-Type":"application/json",

"X-CSRF-TOKEN":
"{{ csrf_token() }}"

},


body:JSON.stringify({

current_page:page,

progress:percent

})


}

);


}




</script>
</body>

</html>