@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">


    <!-- HEADER -->

    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 shadow p-6">

        <h1 class="text-3xl font-bold text-gray-900">
            🎓 {{ $task->title }}
        </h1>

        <p class="text-gray-500 mt-2">
            Espace d'apprentissage du chapitre
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
                    Les meilleurs supports pour ce chapitre
                </p>

            </div>




            <a href="{{ route('tasks.searchCourses',$task->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl font-bold text-sm">

                🔎 Rechercher

            </a>


        </div>





        <div class="grid md:grid-cols-2 gap-5">


        @forelse($task->courseResources as $resource)


            <div class="border rounded-2xl p-5 bg-blue-50 hover:shadow-lg transition">


                <div class="flex gap-3">


                    <div class="bg-blue-600 text-white w-12 h-12 rounded-xl flex items-center justify-center text-xl">
                        📘
                    </div>



                    <div>

                        <h3 class="font-bold text-gray-900">

                            {{ $resource->title }}

                        </h3>


                        <p class="text-sm text-gray-600 mt-2">

                            🏫 {{ $resource->source }}

                        </p>


                    </div>


                </div>




                <div class="mt-5 flex justify-between items-center">


                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">

                        {{ strtoupper($resource->type) }}

                    </span>



                    <a href="{{ $resource->url }}"
                       target="_blank"
                       class="text-blue-600 font-bold hover:underline">

                        📖 Ouvrir

                    </a>


                </div>


            </div>



        @empty


            <div class="col-span-2 text-center text-gray-400 py-10">

                Aucun cours trouvé.

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