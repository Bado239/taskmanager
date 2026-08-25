<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Lecture : {{ $book->title }}</title>

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


        <!-- MODE LECTURE -->

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

        @endif">


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





<!-- ESPACE PRINCIPAL -->

<div class="flex-1 flex overflow-hidden">



    <!-- PDF -->

    <div
        id="pdfZone"
        class="w-[70%] flex flex-col bg-gray-800 transition-all duration-300">


        @if($book->pdf_path)


            <iframe

                src="{{ $book->pdf_path }}"

                class="flex-1 w-full"

                frameborder="0">

            </iframe>


        @else


            <div class="flex-1 flex items-center justify-center text-white italic">

                Aucun fichier PDF trouvé pour ce livre.

            </div>


        @endif


    </div>







    <!-- NOTES -->

    <div

        id="notesZone"

        class="w-[30%] flex flex-col bg-yellow-50 border-l border-gray-300 transition-all duration-300">


        <div class="p-3 bg-yellow-100 border-b text-sm font-semibold text-yellow-700">

            ✍️ Mes Notes & Réflexions

        </div>





        <form

            action="{{ route('personal-resources.update',$book->id) }}"

            method="POST"

            class="flex-1 flex flex-col overflow-hidden">


            @csrf

            @method('PUT')




            <input

                type="hidden"

                name="status"

                value="{{ $book->status ?? 'reading' }}">






            <textarea

                name="notes"

                class="flex-1 w-full p-6 resize-none focus:outline-none text-gray-800 leading-relaxed bg-yellow-50/50"

                placeholder="Écrivez vos notes, résumés ou citations ici...">{{ $book->notes }}</textarea>







            <div class="p-3 bg-white border-t flex flex-col gap-2">


                <button

                    type="submit"

                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg text-sm">


                    💾 Sauvegarder les notes


                </button>





                @if($book->status !== 'done')


                    <button

                        type="submit"

                        onclick="document.querySelector('input[name=status]').value='done'"

                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg text-sm">


                        ✅ Marquer comme lu


                    </button>


                @else


                    <button

                        type="submit"

                        onclick="document.querySelector('input[name=status]').value='reading'"

                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg text-sm">


                        ↩️ Relire ce livre


                    </button>


                @endif



            </div>




        </form>



    </div>



</div>







<script>


function toggleNotes(){


    let notes = document.getElementById('notesZone');

    let pdf = document.getElementById('pdfZone');

    let btn = document.getElementById('toggleBtn');



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


</script>



</body>

</html>