<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>
Lecture : {{ $book->title }}
</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
.textLayer {

    position:absolute;

    top:0;

    left:0;

    width:100%;

    height:100%;

    overflow:hidden;

    opacity:1;

    z-index:20;

    color:transparent;

}


.textLayer span {

    position:absolute;

    cursor:pointer;

    color:transparent;

    user-select:text;

}


.textLayer span:hover {

    background:rgba(255,255,0,0.4);

}


.textLayer span {

    position:absolute;

    cursor:pointer;

    pointer-events:auto;

}



.textLayer span:hover {

    background:yellow;

    color:black;

}

.textLayer span:hover{

background:rgba(255,255,0,0.4);

}


.night-mode{

    background:#111827 !important;

}


.night-mode #pdfViewer{

    background:#000 !important;

}


</style>

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

<!-- MODES DE LECTURE -->

<div class="bg-gray-900 p-2 flex justify-center gap-2">


<button
onclick="modeNormal()"
class="bg-gray-700 text-white px-3 py-1 rounded">

☀️ Normal

</button>


<button
onclick="modeNuit()"
class="bg-black text-white px-3 py-1 rounded border">

🌙 Nuit

</button>


<button
onclick="modePapier()"
class="bg-yellow-700 text-white px-3 py-1 rounded">

📖 Papier

</button>


<button
onclick="pleinEcran()"
class="bg-blue-600 text-white px-3 py-1 rounded">

⛶ Plein écran

</button>


<button
onclick="changerZoom(-0.1)"
class="bg-gray-700 text-white px-3 py-1 rounded">

🔠 A-

</button>


<button
onclick="changerZoom(0.1)"
class="bg-gray-700 text-white px-3 py-1 rounded">

🔠 A+

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
id="notes"
class="flex-1 p-5 bg-yellow-50 resize-none">

{{ $book->notes }}

</textarea>

<div class="p-3 flex flex-col gap-2">


<button
type="button"
onclick="saveNotes()"
class="bg-blue-600 text-white px-5 py-3 rounded">

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


<div id="definitionBox"

class="hidden fixed right-5 top-20 bg-white shadow-xl rounded-xl p-5 w-80 z-50">


<h3 class="font-bold text-lg">
📖 Définition
</h3>


<p id="definitionText"
class="mt-3 text-gray-700">
</p>


<button
onclick="closeDefinition()"
class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">

Fermer

</button>


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



    // CHARGER LA PAGE ACTUELLE

    loadPage(currentPage);



    // REVENIR A LA DERNIERE PAGE LUE

    setTimeout(()=>{

        let page =
        document.querySelector(
        `canvas[data-page="${currentPage}"]`
        );


        if(page){

            viewer.scrollTo({

                top: page.offsetTop - 20,

                behavior:"smooth"

            });

        }


    },1500);


});







function loadPage(num){


    pdfDoc.getPage(num)

    .then(page=>{


        let viewport = page.getViewport({
            scale: zoom
        });



        let canvas = document.createElement('canvas');


        let pageContainer = document.createElement('div');


        pageContainer.style.position = "relative";

        pageContainer.style.width = viewport.width+"px";

        pageContainer.style.height = viewport.height+"px";

        pageContainer.style.margin = "0 auto 30px auto";

        pageContainer.style.marginBottom = "30px";


        canvas.dataset.page = num;


        canvas.className =
        "mb-8 mx-auto bg-white shadow";



        canvas.width =
        viewport.width;


        canvas.height =
        viewport.height;



        pageContainer.appendChild(canvas);

        viewer.appendChild(pageContainer);



        let ctx =
        canvas.getContext('2d');



        page.render({

            canvasContext: ctx,

            viewport: viewport

        });

        page.getTextContent()
        .then(textContent => {


        let textLayer = document.createElement('div');

        textLayer.className = "textLayer";


        Object.assign(textLayer.style, {

            position:"absolute",

            top:"0",

            left:"0",

            width: viewport.width + "px",

            height: viewport.height + "px",

            pointerEvents:"auto",

            zIndex:"10"

        });


        pdfjsLib.renderTextLayer({

        textContentSource:textContent,

        textContent:textContent,

        container:textLayer,

        viewport:viewport,

        textDivs:[]

        });


       pageContainer.appendChild(textLayer);


    
       
        textLayer.style.width = canvas.width+"px";
        textLayer.style.height = canvas.height+"px";



        });



    });


}


function saveNotes(){

    let notes =
    document.getElementById('notes').value;


    fetch("{{ route('personal-resources.update',$book->id) }}", {

        method:"PUT",

        headers:{

            "Content-Type":"application/json",

            "X-CSRF-TOKEN":
            "{{ csrf_token() }}"

        },

        body:JSON.stringify({

            notes:notes

        })

    })

    .then(()=>{

        alert("✅ Notes sauvegardées");

    })

    .catch(error=>{

        console.log(error);

        alert("Erreur sauvegarde");

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
    currentPage = page;



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


    loadPage(currentPage);

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

// ==========================
// MODES DE LECTURE
// ==========================


function modeNuit(){

    document.body.classList.add('night-mode');

    document.querySelectorAll('#pdfViewer canvas')
    .forEach(canvas=>{

        canvas.style.filter =
        "invert(0.9) hue-rotate(180deg)";

    });

}


function modePapier(){

    document.body.classList.remove('night-mode');


    document.querySelectorAll('#pdfViewer canvas')
    .forEach(canvas=>{

        canvas.style.filter =
        "sepia(25%) brightness(95%) contrast(90%)";

    });

}


function modeNormal(){

    document.querySelectorAll('#pdfViewer canvas')
    .forEach(canvas=>{

        canvas.style.filter="none";

    });


    document.body.classList.remove('night-mode');

}


// ==========================
// PLEIN ECRAN
// ==========================

function pleinEcran(){

    let zone =
    document.getElementById('pdfZone');


    if(!document.fullscreenElement){

        zone.requestFullscreen();

    }else{

        document.exitFullscreen();

    }

}



// ==========================
// ZOOM LECTURE
// ==========================

let zoomLecture = 1.4;


function changerZoom(valeur){

    zoomLecture += valeur;


    if(zoomLecture < 0.8){

        zoomLecture = 0.8;

    }


    if(zoomLecture > 2){

        zoomLecture = 2;

    }


    document.querySelectorAll(
    "#pdfViewer canvas"
    ).forEach(canvas=>{

        canvas.style.transform=
        "scale("+zoomLecture/1.4+")";

        canvas.style.transformOrigin=
        "top center";

    });


}

window.addEventListener("beforeunload",()=>{

    let page = currentPage;

    let percent =
    Math.round((page / totalPages) * 100);


    saveProgress(page,percent);

});


let dictionaryCache = {};


function showDefinition(word)
{


if(dictionaryCache[word])
{

displayDefinition(
word,
dictionaryCache[word]
);

return;

}



fetch('/dictionary/'+word)

.then(response=>response.json())

.then(data=>{


dictionaryCache[word]=data.definition;


displayDefinition(
data.word,
data.definition
);


});


}




function displayDefinition(word,definition)
{

document.getElementById(
"definitionText"
).innerHTML =

"<b>"+word+"</b><br><br>"
+
definition;



document
.getElementById("definitionBox")
.classList.remove("hidden");


}


function closeDefinition()
{

document
.getElementById("definitionBox")
.classList.add("hidden");

}

document.addEventListener(
'click',
function(e){


let span = e.target.closest('.textLayer span, .textLayer div');


if(span){


let word = span.textContent
    .trim();



/*
 Nettoyage complet
*/

word = cleanWord(word);


console.log("Mot envoyé :", word);



if(word.length > 1){

    showDefinition(word);

}


}


});

function cleanWord(word)
{

return word
.normalize("NFD")
.replace(/[\u0300-\u036f]/g,"")
.replace(/[.,;:!?()"'«»]/g,'')
.trim()
.toLowerCase();

}


</script>



</body>

</html>