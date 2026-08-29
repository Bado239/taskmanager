<!-- ================= RESSOURCES RECOMMANDÉES ================= -->

<div class="bg-white rounded-2xl border border-gray-200 shadow-md p-6">


    <!-- TITRE + BOUTON RECHERCHE -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">


        <div>

            <h2 class="text-xl font-bold text-gray-900">
                🌍 Ressources recommandées
            </h2>


            <p class="text-sm text-gray-500 mt-1">
                Retrouvez les meilleurs supports pour ce chapitre.
            </p>

        </div>



        <a href="{{ route('tasks.searchCourses',$task->id) }}"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl text-sm font-bold shadow">

            🔎 Rechercher des cours

        </a>


    </div>





    <!-- LISTE DES COURS -->


    <div class="grid md:grid-cols-2 gap-5">


        @forelse($task->courseResources as $resource)



        <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-2xl p-5 hover:shadow-lg transition">


            <div class="flex items-start gap-3">


                <!-- ICONE -->

                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl">

                    📘

                </div>




                <div class="flex-1">


                    <h3 class="font-bold text-gray-900 leading-tight">

                        {{ $resource->title }}

                    </h3>


                    <p class="text-sm text-gray-500 mt-2">

                        🏫 {{ $resource->source }}

                    </p>


                </div>


            </div>





            <div class="flex items-center justify-between mt-5">


                <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold">

                    {{ strtoupper($resource->type) }}

                </span>




                <a href="{{ $resource->url }}"
                   target="_blank"
                   class="text-sm font-bold text-blue-600 hover:text-blue-800">

                    📖 Ouvrir le cours

                </a>



            </div>



        </div>




        @empty



        <div class="md:col-span-2 text-center py-8 text-gray-400">


            <p class="font-semibold">
                Aucun cours trouvé pour ce chapitre.
            </p>


            <p class="text-sm mt-2">
                Cliquez sur "Rechercher des cours".
            </p>


        </div>



        @endforelse



    </div>


</div>