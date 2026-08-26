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

<div class="bg-white p-4 border-b flex justify-between items-center">


<div>

<a href="{{ route('dashboard',['view'=>'personal']) }}"
class="text-blue-600 text-sm">

← Retour espace personnel

</a>


<h1 class="text-xl font-bold mt-1">

📖 {{ $book->title }}

</h1>


<p class="text-gray-500 text-sm">

{{ $book->author_or_source }}

</p>

</div>



<button

onclick="toggleNotes()"

id="toggleBtn"

class="bg-blue-600 text-white px-4 py-2 rounded">

📝 Masquer notes

</button>


</div>






<div class="flex-1 flex overflow-hidden">


<!-- LECTEUR PDF -->

<div

id="pdfZone"

class="w-[70%] flex flex-col bg-gray-700">

<!-- BARRE DE CONTROLE LECTURE -->

<div class="bg-gray-900 text-white h-14 flex items-center justify-center gap-5 shadow">

    <button
    onclick="previousPage()"
    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
        ⬅
    </button>


    <span class="font-bold">
        <span id="topPage">
            {{ $book->current_page ?? 1 }}
        </span>
        /
        <span id="topTotal">
            ...
        </span>
    </span>


    <button
    onclick="nextPage()"
    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
        ➡
    </button>


    <button
    onclick="zoomOut()"
    class="bg-gray-700 px-3 py-2 rounded">
        −
    </button>


    <span id="zoomText">
        100%
    </span>


    <button
    onclick="zoomIn()"
    class="bg-gray-700 px-3 py-2 rounded">
        +
    </button>


</div>



<div

id="pdfViewer"

class="flex-1 overflow-y-auto p-6">

</div>





<!-- PROGRESSION -->

<div class="bg-white p-4 border-t">


<div class="flex justify-between text-sm">

<span>
📖 Progression
</span>


<span id="progressText">

{{ $book->progress ?? 0 }} %

</span>

</div>



<div class="bg-gray-200 h-3 rounded mt-2">


<div

id="progressBar"

class="bg-blue-600 h-3 rounded"

style="width:{{ $book->progress ?? 0 }}%">

</div>


</div>



<div class="text-xs text-gray-500 mt-2">

Page :

<span id="currentPage">

{{ $book->current_page ?? 1 }}

</span>

/


<span id="totalPages">

...

</span>


</div>



</div>



</div>








<!-- NOTES -->


<div

id="notesZone"

class="w-[30%] bg-yellow-50 flex flex-col border-l">


<div class="p-3 bg-yellow-100 font-bold">

✍️ Notes & Réflexions

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

value="{{ $book->status }}">



<textarea

name="notes"

class="flex-1 p-5 bg-yellow-50 resize-none">

{{ $book->notes }}

</textarea>



<div class="p-3 flex flex-col gap-2">


<button
type="submit"
class="bg-blue-600 text-white py-2 rounded">

💾 Sauvegarder les notes

</button>



@if($book->reading_status !== 'finished')


<button

type="submit"

onclick="
document.querySelector('input[name=status]').value='done'
"

class="bg-green-600 text-white py-2 rounded">

✅ Terminer la lecture

</button>


@else


<button

type="submit"

onclick="
document.querySelector('input[name=status]').value='reading'
"

class="bg-orange-500 text-white py-2 rounded">

↩️ Reprendre la lecture

</button>


@endif


</div>

</form>


</div>



</div>







<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>



<script>


const url = "{{ $book->pdf_path }}";


let pdfDoc = null;

let totalPages = 0;

let currentPage = {{ $book->current_page ?? 1 }};

let lastSavedPage = currentPage;
let zoom = 1.4;


const viewer = document.getElementById('pdfViewer');



pdfjsLib.GlobalWorkerOptions.workerSrc =

"https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";





pdfjsLib.getDocument(url)

.promise

.then(pdf=>{


    pdfDoc = pdf;


    totalPages = pdf.numPages;



    document.getElementById('totalPages').innerHTML =
    totalPages;



    // CHARGER TOUT LE LIVRE

    for(let i=1; i<=totalPages; i++){

        loadPage(i);

    }



    // REVENIR A LA DERNIERE PAGE LUE

    setTimeout(()=>{


        let page =
        document.querySelector(
        `canvas[data-page="${currentPage}"]`
        );


        if(page){

            page.scrollIntoView({
                behavior:"smooth",
                block:"start"
            });

        }


    },3000);



});







function loadPage(num){



    pdfDoc.getPage(num)

    .then(page=>{


        let viewport =
        page.getViewport({
            scale:zoom
        });



        let canvas =
        document.createElement('canvas');



        canvas.dataset.page=num;



        canvas.className =
        "mb-8 mx-auto bg-white shadow";



        canvas.width =
        viewport.width;



        canvas.height =
        viewport.height;



        viewer.appendChild(canvas);



        let ctx =
        canvas.getContext('2d');



        page.render({

            canvasContext:ctx,

            viewport:viewport

        });



    });



}









// DETECTION PAGE VISIBLE


viewer.addEventListener('scroll',()=>{


    let pages =
    document.querySelectorAll('#pdfViewer canvas');



    let visiblePage = 1;



    pages.forEach(canvas=>{


        let rect =
        canvas.getBoundingClientRect();



        let zone =
        viewer.getBoundingClientRect();



        if(
            rect.top <= zone.top + 250 &&
            rect.bottom >= zone.top + 250
        ){


            visiblePage =
            parseInt(canvas.dataset.page);


        }



    });



    updateProgress(visiblePage);



});










function updateProgress(page){



    if(page < 1){

        page = 1;

    }



    if(page > totalPages){

        page = totalPages;

    }



    let percent =
    Math.round(
        (page / totalPages) * 100
    );



    document.getElementById('currentPage').innerHTML =
    page;



    document.getElementById('progressText').innerHTML =
    percent + " %";



    document.getElementById('progressBar').style.width =
    percent + "%";

    document.getElementById('topPage').innerHTML = page;

    document.getElementById('topTotal').innerHTML = totalPages;





    if(page !== lastSavedPage){


        lastSavedPage = page;


        saveProgress(page,percent);


    }



}








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




function nextPage(){

    if(currentPage < totalPages){

        currentPage++;

        let page =
        document.querySelector(
            `canvas[data-page="${currentPage}"]`
        );

        if(page){

            page.scrollIntoView({
                behavior:"smooth",
                block:"start"
            });

        }

    }

}



function previousPage(){

    if(currentPage > 1){

        currentPage--;

        let page =
        document.querySelector(
            `canvas[data-page="${currentPage}"]`
        );

        if(page){

            page.scrollIntoView({
                behavior:"smooth",
                block:"start"
            });

        }

    }

}



function zoomIn(){

    zoom += 0.2;

    document.getElementById('zoomText').innerHTML =
    Math.round((zoom/1.4)*100)+"%";

    reloadPages();

}



function zoomOut(){

    if(zoom > 0.8){

        zoom -= 0.2;

    }


    document.getElementById('zoomText').innerHTML =
    Math.round((zoom/1.4)*100)+"%";


    reloadPages();

}



function reloadPages(){

    viewer.innerHTML="";


    for(let i=1;i<=totalPages;i++){

        loadPage(i);

    }


}




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
    "w-[70%] flex flex-col bg-gray-700";


    btn.innerHTML =
    "📝 Masquer notes";



}

else{


    notes.classList.add('hidden');


    pdf.className =
    "w-full flex flex-col bg-gray-700";


    btn.innerHTML =
    "📖 Afficher notes";


}



}


</script>


</body>

</html>