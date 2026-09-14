<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✏️ {{ __('Modifier l\'activité') }}
        </h2>
    </x-slot>


    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="bg-white shadow-sm sm:rounded-lg p-6">

            <form action="{{ route('tasks.update', $task->id) }}"
                  method="POST"
                  class="space-y-4">

                @csrf
                @method('PUT')


                <!-- TYPE -->
                <input type="hidden"
                       name="type"
                       value="{{ $task->type }}">



                <!-- TITRE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Titre de l'activité
                    </label>

                    <input type="text"
                           name="title"
                           value="{{ old('title',$task->title) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm"
                           required>
                </div>



                <!-- CATEGORIE -->
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catégorie
                    </label>

                    <select name="category_id"
                            class="w-full rounded-md border-gray-300 shadow-sm"
                            required>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}"
                            {{ old('category_id',$task->category_id)==$category->id?'selected':'' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>



                <!-- PROJET -->

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Projet associé
                    </label>


                    <select name="project_id"
                            class="w-full rounded-md border-gray-300 shadow-sm">


                        <option value="">
                            Aucun projet
                        </option>


                        @foreach($projects as $project)

                            <option value="{{ $project->id }}"
                            {{ old('project_id',$task->project_id)==$project->id?'selected':'' }}>

                                {{ $project->title }}

                            </option>


                        @endforeach


                    </select>

                </div>




                <!-- PRIORITE -->

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Priorité
                    </label>


                    <select name="priority"
                            class="w-full rounded-md border-gray-300 shadow-sm">


                        <option value="high"
                        {{ old('priority',$task->priority)=='high'?'selected':'' }}>
                            🔴 Haute
                        </option>


                        <option value="medium"
                        {{ old('priority',$task->priority)=='medium'?'selected':'' }}>
                            🟡 Moyenne
                        </option>


                        <option value="low"
                        {{ old('priority',$task->priority)=='low'?'selected':'' }}>
                            🔵 Basse
                        </option>


                    </select>


                </div>




                <!-- STATUT -->

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        État
                    </label>


                    <select name="document_status"
                            class="w-full rounded-md border-gray-300 shadow-sm">


                        <option value="todo"
                        {{ old('document_status',$task->document_status)=='todo'?'selected':'' }}>
                            🔴 À faire
                        </option>


                        <option value="in_progress"
                        {{ old('document_status',$task->document_status)=='in_progress'?'selected':'' }}>
                            🟡 En cours
                        </option>


                        <option value="done"
                        {{ old('document_status',$task->document_status)=='done'?'selected':'' }}>
                            🟢 Validée
                        </option>


                    </select>


                </div>





                <!-- DOCUMENT -->

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        🔗 Lien document / cours
                    </label>


                    <input type="url"
                           name="document_link"
                           value="{{ old('document_link',$task->document_link) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm"
                           placeholder="https://...">

                </div>





                <!-- DATE -->

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date prévue
                    </label>


                    <input type="date"
                           name="date_prevue"
                           value="{{ old('date_prevue',$task->date_prevue ? \Illuminate\Support\Carbon::parse($task->date_prevue)->format('Y-m-d'): '') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm">

                </div>





                <!-- HORAIRES -->

                <div class="grid grid-cols-2 gap-4">


                    <div>

                        <label class="block text-sm font-medium text-gray-700">
                            Heure début
                        </label>


                        <input type="time"
                               id="heure_debut"
                               name="heure_debut"
                               value="{{ old('heure_debut',$task->heure_debut) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm">


                    </div>



                    <div>

                        <label class="block text-sm font-medium text-gray-700">
                            Heure fin
                        </label>


                        <input type="time"
                               id="heure_fin"
                               name="heure_fin"
                               value="{{ old('heure_fin',$task->heure_fin) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm">


                    </div>


                </div>





                <!-- BOUTONS -->

                <div class="flex gap-4 pt-4">


                    <button type="submit"
                            class="flex-1 bg-green-700 hover:bg-green-800 text-white font-bold py-2 rounded-md">

                        💾 Enregistrer

                    </button>



                    <a href="{{ route('dashboard',['view'=>$task->type]) }}"
                       class="flex-1 text-center bg-gray-100 hover:bg-gray-200 py-2 rounded-md border">

                        Annuler

                    </a>


                </div>


            </form>


        </div>


    </div>



    <script>

        document.getElementById('heure_debut')
        .addEventListener('change',function(){

            let value=this.value;

            if(value){

                let [h,m]=value.split(':').map(Number);

                h+=2;

                if(h>=24){
                    h-=24;
                }

                document.getElementById('heure_fin').value =
                String(h).padStart(2,'0')+':'+String(m).padStart(2,'0');

            }

        });


    </script>


</x-app-layout>